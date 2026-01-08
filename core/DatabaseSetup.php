<?php
/**
 * DatabaseSetup - Configuração automática de novos bancos de dados de clientes
 * 
 * Este helper configura automaticamente:
 * - Interfaces do SysApp
 * - Tabelas necessárias
 * - Permissões básicas
 * - Estruturas de relatórios
 */

class DatabaseSetup {
    
    /**
     * Configura automaticamente um novo banco de dados cliente
     * 
     * @param array $dbConfig ['host', 'database', 'user', 'password', 'port']
     * @param int $cd_empresa ID da empresa no SysApp
     * @return array ['success' => bool, 'message' => string, 'log' => array]
     */
    public static function setupNewDatabase($dbConfig, $cd_empresa) {
        $log = [];
        $errors = [];
        
        try {
            // Conecta ao banco do cliente
            $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
            $conn = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $log[] = "✅ Conectado ao banco {$dbConfig['database']}";
            
            // 1. Criar/Verificar tabela de interfaces
            $log[] = "--- Configurando Interfaces do SysApp ---";
            $interfacesResult = self::setupInterfaces($conn);
            $log = array_merge($log, $interfacesResult['log']);
            
            // 2. Verificar/Criar views necessárias para relatórios
            $log[] = "--- Configurando Views de Relatórios ---";
            $viewsResult = self::setupReportViews($conn);
            $log = array_merge($log, $viewsResult['log']);
            
            // 3. Verificar estrutura de tabelas principais
            $log[] = "--- Verificando Estrutura do Banco ---";
            $structureResult = self::verifyDatabaseStructure($conn);
            $log = array_merge($log, $structureResult['log']);
            
            // 4. Configurar índices para performance
            $log[] = "--- Otimizando Performance ---";
            $indexResult = self::setupPerformanceIndexes($conn);
            $log = array_merge($log, $indexResult['log']);
            
            return [
                'success' => true,
                'message' => 'Banco de dados configurado com sucesso!',
                'log' => $log
            ];
            
        } catch (PDOException $e) {
            $errors[] = "Erro de conexão: " . $e->getMessage();
            return [
                'success' => false,
                'message' => 'Erro ao configurar banco de dados',
                'log' => $log,
                'errors' => $errors
            ];
        } catch (Exception $e) {
            $errors[] = "Erro geral: " . $e->getMessage();
            return [
                'success' => false,
                'message' => 'Erro ao configurar banco de dados',
                'log' => $log,
                'errors' => $errors
            ];
        }
    }
    
    /**
     * Configura tabela de interfaces do SysApp
     */
    private static function setupInterfaces($conn) {
        $log = [];
        
        try {
            // Verifica se tabela existe
            $checkTable = "SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name = 'sysapp_interfaces'
            )";
            
            $stmt = $conn->query($checkTable);
            $exists = $stmt->fetchColumn();
            
            if (!$exists || $exists === 'f') {
                // Cria tabela
                $createTable = "
                    CREATE TABLE sysapp_interfaces (
                        cd_interface INTEGER PRIMARY KEY,
                        nm_interface VARCHAR(100) NOT NULL,
                        ds_interface TEXT,
                        fg_ativo CHAR(1) DEFAULT 'S',
                        dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
                
                $conn->exec($createTable);
                $log[] = "  ✅ Tabela sysapp_interfaces criada";
            } else {
                $log[] = "  ℹ️ Tabela sysapp_interfaces já existe";
            }
            
            // Insere/atualiza interfaces padrão
            $interfaces = [
                ['cd' => 1, 'nome' => 'Dashboard', 'desc' => 'Dashboard principal com estatísticas'],
                ['cd' => 2, 'nome' => 'Relatórios', 'desc' => 'Acesso a relatórios e análises'],
                ['cd' => 3, 'nome' => 'Clientes', 'desc' => 'Gerenciamento de clientes'],
                ['cd' => 4, 'nome' => 'Questionários', 'desc' => 'Questionários e atendimentos'],
                ['cd' => 5, 'nome' => 'Usuários', 'desc' => 'Gerenciamento de usuários'],
                ['cd' => 6, 'nome' => 'Configurações', 'desc' => 'Configurações do sistema']
            ];
            
            $insertCount = 0;
            foreach ($interfaces as $interface) {
                $sql = "INSERT INTO sysapp_interfaces (cd_interface, nm_interface, ds_interface) 
                        VALUES (:cd, :nome, :desc)
                        ON CONFLICT (cd_interface) DO UPDATE 
                        SET nm_interface = EXCLUDED.nm_interface,
                            ds_interface = EXCLUDED.ds_interface";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':cd' => $interface['cd'],
                    ':nome' => $interface['nome'],
                    ':desc' => $interface['desc']
                ]);
                $insertCount++;
            }
            
            $log[] = "  ✅ {$insertCount} interfaces configuradas";
            
            return ['success' => true, 'log' => $log];
            
        } catch (Exception $e) {
            $log[] = "  ❌ Erro ao configurar interfaces: " . $e->getMessage();
            return ['success' => false, 'log' => $log];
        }
    }
    
    /**
     * Cria views necessárias para relatórios
     */
    private static function setupReportViews($conn) {
        $log = [];
        
        try {
            // View para login (compatibilidade)
            $sql = "CREATE OR REPLACE VIEW vw_login AS
                    SELECT 
                        cd_usuario,
                        nm_usuario as nome_usuario,
                        ds_login as login_usuario,
                        ds_senha as senha_usuario,
                        ds_email as email_usuario,
                        fg_ativo
                    FROM sysapp_config_user
                    WHERE fg_ativo = 'S'";
            
            $conn->exec($sql);
            $log[] = "  ✅ View vw_login criada/atualizada";
            
            // View para dados de clientes simplificada
            $checkGlbPessoa = "SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_name = 'glb_pessoa'
            )";
            
            $stmt = $conn->query($checkGlbPessoa);
            $glbPessoaExists = $stmt->fetchColumn();
            
            if ($glbPessoaExists === 't' || $glbPessoaExists === true) {
                // Detecta campos disponíveis
                $fields = self::detectGlbPessoaFields($conn);
                
                $nomeField = $fields['nome'];
                $cpfCnpjField = $fields['cpf_cnpj'];
                
                $sql = "CREATE OR REPLACE VIEW vw_clientes_simples AS
                        SELECT 
                            cd_pessoa,
                            {$nomeField} as nome_cliente,
                            {$cpfCnpjField} as cpf_cnpj,
                            CASE WHEN LENGTH({$cpfCnpjField}) > 11 THEN 'J' ELSE 'F' END as tipo_pessoa
                        FROM glb_pessoa
                        WHERE fg_ativo = 'S'";
                
                $conn->exec($sql);
                $log[] = "  ✅ View vw_clientes_simples criada/atualizada";
            } else {
                $log[] = "  ⚠️ Tabela glb_pessoa não encontrada - View vw_clientes_simples não criada";
            }
            
            return ['success' => true, 'log' => $log];
            
        } catch (Exception $e) {
            $log[] = "  ⚠️ Erro ao criar views: " . $e->getMessage();
            return ['success' => false, 'log' => $log];
        }
    }
    
    /**
     * Detecta campos disponíveis na tabela glb_pessoa
     */
    private static function detectGlbPessoaFields($conn) {
        $fields = [
            'nome' => 'nm_pessoa',
            'cpf_cnpj' => 'COALESCE(cpf_cgc, nr_cpf_cnpj, \'\')'
        ];
        
        try {
            // Verifica campos de nome
            $sql = "SELECT column_name FROM information_schema.columns 
                    WHERE table_name = 'glb_pessoa' 
                    AND column_name IN ('nm_fant', 'nm_fantasia', 'nm_pessoa')
                    LIMIT 1";
            $stmt = $conn->query($sql);
            $nomeField = $stmt->fetchColumn();
            if ($nomeField) {
                $fields['nome'] = $nomeField;
            }
            
            // Verifica campos de CPF/CNPJ
            $sql = "SELECT column_name FROM information_schema.columns 
                    WHERE table_name = 'glb_pessoa' 
                    AND column_name IN ('cpf_cgc', 'nr_cpf_cnpj', 'cpf_cnpj')
                    LIMIT 1";
            $stmt = $conn->query($sql);
            $cpfField = $stmt->fetchColumn();
            if ($cpfField) {
                $fields['cpf_cnpj'] = $cpfField;
            }
            
        } catch (Exception $e) {
            // Mantém valores padrão
        }
        
        return $fields;
    }
    
    /**
     * Verifica estrutura principal do banco
     */
    private static function verifyDatabaseStructure($conn) {
        $log = [];
        
        $tabelasEssenciais = [
            'glb_pessoa' => 'Cadastro de pessoas/clientes',
            'dm_produto' => 'Cadastro de produtos',
            'dm_orcamento_vendas_consolidadas' => 'Vendas consolidadas'
        ];
        
        foreach ($tabelasEssenciais as $tabela => $descricao) {
            try {
                $sql = "SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_name = '$tabela'
                )";
                
                $stmt = $conn->query($sql);
                $exists = $stmt->fetchColumn();
                
                if ($exists === 't' || $exists === true) {
                    // Conta registros
                    $countSql = "SELECT COUNT(*) FROM $tabela";
                    $stmt = $conn->query($countSql);
                    $count = $stmt->fetchColumn();
                    
                    $log[] = "  ✅ {$descricao} ({$tabela}): " . number_format($count) . " registros";
                } else {
                    $log[] = "  ⚠️ {$descricao} ({$tabela}): NÃO ENCONTRADA";
                }
                
            } catch (Exception $e) {
                $log[] = "  ⚠️ {$descricao} ({$tabela}): Erro ao verificar";
            }
        }
        
        return ['success' => true, 'log' => $log];
    }
    
    /**
     * Cria índices para melhorar performance
     */
    private static function setupPerformanceIndexes($conn) {
        $log = [];
        
        $indexes = [
            [
                'table' => 'dm_orcamento_vendas_consolidadas',
                'name' => 'idx_vendas_data',
                'columns' => 'dt_emi_pedido',
                'desc' => 'Índice para consultas por data'
            ],
            [
                'table' => 'dm_orcamento_vendas_consolidadas',
                'name' => 'idx_vendas_cliente',
                'columns' => 'cd_pessoa',
                'desc' => 'Índice para consultas por cliente'
            ],
            [
                'table' => 'dm_produto',
                'name' => 'idx_produto_marca',
                'columns' => 'cd_marca',
                'desc' => 'Índice para relatórios por marca'
            ]
        ];
        
        foreach ($indexes as $index) {
            try {
                // Verifica se tabela existe
                $checkTable = "SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_name = '{$index['table']}'
                )";
                $stmt = $conn->query($checkTable);
                $tableExists = $stmt->fetchColumn();
                
                if ($tableExists === 't' || $tableExists === true) {
                    // Cria índice se não existir
                    $sql = "CREATE INDEX IF NOT EXISTS {$index['name']} 
                            ON {$index['table']} ({$index['columns']})";
                    
                    $conn->exec($sql);
                    $log[] = "  ✅ {$index['desc']}";
                }
                
            } catch (Exception $e) {
                // Índice pode já existir ou tabela não existe
                $log[] = "  ℹ️ {$index['desc']}: " . (strpos($e->getMessage(), 'does not exist') !== false ? 'Tabela não existe' : 'Já existe ou erro');
            }
        }
        
        return ['success' => true, 'log' => $log];
    }
    
    /**
     * Aplica correções em todas as empresas existentes
     */
    public static function applyToAllExistingDatabases() {
        $log = [];
        $errors = [];
        
        try {
            // Busca todas as empresas ativas
            $db = Database::getInstance();
            $empresas = $db->fetchAll("
                SELECT cd_empresa, nm_empresa, hostname_banco, nome_banco, 
                       usuario_banco, porta_banco
                FROM sysapp_config_empresas 
                WHERE fg_ativo = 'S'
                ORDER BY cd_empresa
            ");
            
            if (empty($empresas)) {
                return [
                    'success' => false,
                    'message' => 'Nenhuma empresa encontrada',
                    'log' => ['⚠️ Nenhuma empresa ativa no sistema']
                ];
            }
            
            $log[] = "📊 Encontradas " . count($empresas) . " empresas ativas";
            $log[] = "";
            
            foreach ($empresas as $empresa) {
                $log[] = "=== Processando: {$empresa['nm_empresa']} (ID: {$empresa['cd_empresa']}) ===";
                
                try {
                    // Descriptografa senha
                    $senha = Security::decrypt($empresa['senha_banco'] ?? '');
                    
                    $dbConfig = [
                        'host' => $empresa['hostname_banco'],
                        'database' => $empresa['nome_banco'],
                        'user' => $empresa['usuario_banco'],
                        'password' => $senha,
                        'port' => $empresa['porta_banco']
                    ];
                    
                    $result = self::setupNewDatabase($dbConfig, $empresa['cd_empresa']);
                    
                    if ($result['success']) {
                        $log = array_merge($log, $result['log']);
                        $log[] = "✅ {$empresa['nm_empresa']}: Configurado com sucesso!";
                    } else {
                        $log = array_merge($log, $result['log']);
                        if (isset($result['errors'])) {
                            $errors = array_merge($errors, $result['errors']);
                        }
                        $log[] = "⚠️ {$empresa['nm_empresa']}: Concluído com avisos";
                    }
                    
                } catch (Exception $e) {
                    $log[] = "❌ {$empresa['nm_empresa']}: Erro - " . $e->getMessage();
                    $errors[] = "{$empresa['nm_empresa']}: " . $e->getMessage();
                }
                
                $log[] = "";
            }
            
            $log[] = "=== PROCESSAMENTO CONCLUÍDO ===";
            $log[] = "Total de empresas processadas: " . count($empresas);
            
            return [
                'success' => empty($errors),
                'message' => empty($errors) ? 'Todas as empresas configuradas!' : 'Algumas empresas com avisos',
                'log' => $log,
                'errors' => $errors
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao processar empresas',
                'log' => $log,
                'errors' => [$e->getMessage()]
            ];
        }
    }
}
