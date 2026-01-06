<?php
/**
 * DESCOBRIDOR AUTOMÁTICO DE ESTRUTURA DO ERP
 * 
 * Este script analisa o banco de dados do ERP e:
 * - Lista todas as tabelas
 * - Identifica tabelas de vendas/pedidos
 * - Analisa estrutura das tabelas
 * - Propõe mapeamento correto
 * - Gera views de compatibilidade
 * 
 * Data: 05/01/2026
 */

set_time_limit(600);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

class DescobridorEstruturaERP {
    private $db;
    private $resultados = [];
    private $tabelasVendas = [];
    private $tabelasProdutos = [];
    private $mapeamento = [];
    
    public function __construct() {
        echo $this->header();
    }
    
    private function header() {
        return "
╔══════════════════════════════════════════════════════════════════════╗
║                                                                      ║
║     🔍 DESCOBRIDOR AUTOMÁTICO DE ESTRUTURA DO ERP                   ║
║                                                                      ║
║     Sistema: SysApp                                                  ║
║     Data: " . date('d/m/Y H:i:s') . "                                            ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝

";
    }
    
    private function log($mensagem, $nivel = 'INFO') {
        $cores = [
            'INFO' => "\033[0;36m",    // Ciano
            'SUCESSO' => "\033[0;32m", // Verde
            'AVISO' => "\033[0;33m",   // Amarelo
            'ERRO' => "\033[0;31m",    // Vermelho
            'DESTAQUE' => "\033[1;35m" // Magenta bold
        ];
        
        $reset = "\033[0m";
        $cor = $cores[$nivel] ?? $cores['INFO'];
        
        $icones = [
            'INFO' => 'ℹ️',
            'SUCESSO' => '✅',
            'AVISO' => '⚠️',
            'ERRO' => '❌',
            'DESTAQUE' => '🎯'
        ];
        
        $icone = $icones[$nivel] ?? 'ℹ️';
        
        echo "{$cor}[{$nivel}] {$icone} {$mensagem}{$reset}\n";
    }
    
    private function separador() {
        echo "\n" . str_repeat("═", 70) . "\n\n";
    }
    
    public function conectar() {
        $this->log("Conectando ao banco de dados...");
        
        try {
            if (!Session::check('Config.database')) {
                $host = defined('DB_HOST') ? DB_HOST : 'localhost';
                $database = defined('DB_NAME') ? DB_NAME : 'sysapp';
                $user = defined('DB_USER') ? DB_USER : 'postgres';
                $password = defined('DB_PASS') ? DB_PASS : 'postgres';
                $port = defined('DB_PORT') ? DB_PORT : '5432';
            } else {
                $host = Session::read('Config.host');
                $database = Session::read('Config.database');
                $user = Session::read('Config.user');
                $password = Session::read('Config.password');
                $port = Session::read('Config.porta');
            }
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
            $this->db = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            $this->log("Conectado em: {$host}:{$port}/{$database}", 'SUCESSO');
            return true;
        } catch (Exception $e) {
            $this->log("Falha na conexão: " . $e->getMessage(), 'ERRO');
            return false;
        }
    }
    
    public function listarTodasTabelas() {
        $this->separador();
        $this->log("PASSO 1: Listando todas as tabelas do banco", 'DESTAQUE');
        
        try {
            $sql = "SELECT 
                        schemaname,
                        tablename,
                        (SELECT COUNT(*) FROM information_schema.columns 
                         WHERE table_schema = schemaname 
                         AND table_name = tablename) as total_colunas
                    FROM pg_tables 
                    WHERE schemaname = 'public' 
                    ORDER BY tablename";
            
            $tabelas = $this->db->query($sql)->fetchAll();
            
            $this->log("Total de tabelas encontradas: " . count($tabelas), 'SUCESSO');
            
            echo "\nListagem completa:\n";
            echo str_repeat("-", 70) . "\n";
            printf("%-50s %s\n", "TABELA", "COLUNAS");
            echo str_repeat("-", 70) . "\n";
            
            foreach ($tabelas as $tabela) {
                printf("%-50s %d\n", $tabela['tablename'], $tabela['total_colunas']);
            }
            
            $this->resultados['tabelas'] = $tabelas;
            return $tabelas;
        } catch (Exception $e) {
            $this->log("Erro ao listar tabelas: " . $e->getMessage(), 'ERRO');
            return [];
        }
    }
    
    public function identificarTabelasVendas() {
        $this->separador();
        $this->log("PASSO 2: Identificando tabelas de VENDAS/PEDIDOS", 'DESTAQUE');
        
        $palavrasChave = ['ped', 'vd', 'venda', 'pedido', 'orcamento', 'nf', 'nota', 'fiscal'];
        $candidatas = [];
        
        foreach ($this->resultados['tabelas'] as $tabela) {
            $nome = strtolower($tabela['tablename']);
            
            foreach ($palavrasChave as $palavra) {
                if (strpos($nome, $palavra) !== false) {
                    $candidatas[] = $tabela['tablename'];
                    break;
                }
            }
        }
        
        $this->log("Tabelas candidatas a VENDAS: " . count($candidatas), 'SUCESSO');
        
        foreach ($candidatas as $tabela) {
            echo "  • {$tabela}\n";
        }
        
        // Analisar cada candidata
        foreach ($candidatas as $tabela) {
            $this->analisarTabelaVendas($tabela);
        }
        
        return $candidatas;
    }
    
    private function analisarTabelaVendas($tabela) {
        try {
            $sql = "SELECT 
                        column_name,
                        data_type,
                        is_nullable
                    FROM information_schema.columns 
                    WHERE table_name = :tabela 
                    ORDER BY ordinal_position";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tabela' => $tabela]);
            $colunas = $stmt->fetchAll();
            
            // Contar registros
            $count = $this->db->query("SELECT COUNT(*) FROM {$tabela}")->fetchColumn();
            
            // Procurar campos importantes
            $camposImportantes = [
                'pedido' => ['cd_ped', 'cd_pedido', 'nr_ped', 'num_pedido'],
                'cliente' => ['cd_pessoa', 'cd_cliente', 'id_cliente', 'cliente'],
                'data' => ['dt_ped', 'dt_pedido', 'dt_venda', 'dt_emi', 'data', 'dt_hr_ped'],
                'valor' => ['vl_', 'valor', 'vlr_', 'total'],
                'filial' => ['cd_filial', 'filial'],
                'status' => ['sts_', 'status', 'situacao']
            ];
            
            $camposEncontrados = [];
            foreach ($colunas as $coluna) {
                $nomeColuna = strtolower($coluna['column_name']);
                
                foreach ($camposImportantes as $tipo => $padroes) {
                    foreach ($padroes as $padrao) {
                        if (strpos($nomeColuna, $padrao) !== false) {
                            $camposEncontrados[$tipo][] = $coluna['column_name'];
                        }
                    }
                }
            }
            
            $score = 0;
            foreach ($camposEncontrados as $campos) {
                $score += count($campos);
            }
            
            $this->tabelasVendas[$tabela] = [
                'total_colunas' => count($colunas),
                'total_registros' => $count,
                'campos_encontrados' => $camposEncontrados,
                'score' => $score,
                'colunas' => $colunas
            ];
            
        } catch (Exception $e) {
            $this->log("Erro ao analisar {$tabela}: " . $e->getMessage(), 'AVISO');
        }
    }
    
    public function identificarTabelasProdutos() {
        $this->separador();
        $this->log("PASSO 3: Identificando tabelas de PRODUTOS", 'DESTAQUE');
        
        $palavrasChave = ['prod', 'produto', 'item', 'est_', 'estoque', 'marca'];
        $candidatas = [];
        
        foreach ($this->resultados['tabelas'] as $tabela) {
            $nome = strtolower($tabela['tablename']);
            
            foreach ($palavrasChave as $palavra) {
                if (strpos($nome, $palavra) !== false) {
                    $candidatas[] = $tabela['tablename'];
                    break;
                }
            }
        }
        
        $this->log("Tabelas candidatas a PRODUTOS: " . count($candidatas), 'SUCESSO');
        
        foreach ($candidatas as $tabela) {
            echo "  • {$tabela}\n";
        }
        
        // Analisar cada candidata
        foreach ($candidatas as $tabela) {
            $this->analisarTabelaProdutos($tabela);
        }
        
        return $candidatas;
    }
    
    private function analisarTabelaProdutos($tabela) {
        try {
            $sql = "SELECT 
                        column_name,
                        data_type,
                        is_nullable
                    FROM information_schema.columns 
                    WHERE table_name = :tabela 
                    ORDER BY ordinal_position";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tabela' => $tabela]);
            $colunas = $stmt->fetchAll();
            
            $count = $this->db->query("SELECT COUNT(*) FROM {$tabela}")->fetchColumn();
            
            $camposImportantes = [
                'produto' => ['cd_produto', 'cd_prod', 'id_produto', 'produto'],
                'marca' => ['cd_marca', 'marca', 'id_marca'],
                'descricao' => ['ds_', 'descricao', 'nome', 'nm_'],
                'tamanho' => ['cd_cpl_tamanho', 'tamanho', 'tam']
            ];
            
            $camposEncontrados = [];
            foreach ($colunas as $coluna) {
                $nomeColuna = strtolower($coluna['column_name']);
                
                foreach ($camposImportantes as $tipo => $padroes) {
                    foreach ($padroes as $padrao) {
                        if (strpos($nomeColuna, $padrao) !== false) {
                            $camposEncontrados[$tipo][] = $coluna['column_name'];
                        }
                    }
                }
            }
            
            $score = 0;
            foreach ($camposEncontrados as $campos) {
                $score += count($campos) * 2; // Peso maior para produtos
            }
            
            $this->tabelasProdutos[$tabela] = [
                'total_colunas' => count($colunas),
                'total_registros' => $count,
                'campos_encontrados' => $camposEncontrados,
                'score' => $score,
                'colunas' => $colunas
            ];
            
        } catch (Exception $e) {
            $this->log("Erro ao analisar {$tabela}: " . $e->getMessage(), 'AVISO');
        }
    }
    
    public function analisarMelhoresCandidatas() {
        $this->separador();
        $this->log("PASSO 4: Analisando MELHORES CANDIDATAS", 'DESTAQUE');
        
        // Ordenar por score
        uasort($this->tabelasVendas, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        uasort($this->tabelasProdutos, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        echo "\n📊 RANKING - TABELAS DE VENDAS:\n";
        echo str_repeat("-", 70) . "\n";
        printf("%-40s %8s %12s %s\n", "TABELA", "SCORE", "REGISTROS", "CAMPOS CHAVE");
        echo str_repeat("-", 70) . "\n";
        
        foreach (array_slice($this->tabelasVendas, 0, 5, true) as $tabela => $info) {
            $campos = [];
            foreach ($info['campos_encontrados'] as $tipo => $lista) {
                if (!empty($lista)) {
                    $campos[] = $tipo;
                }
            }
            printf("%-40s %8d %12s %s\n", 
                $tabela, 
                $info['score'], 
                number_format($info['total_registros']),
                implode(', ', $campos)
            );
        }
        
        echo "\n📦 RANKING - TABELAS DE PRODUTOS:\n";
        echo str_repeat("-", 70) . "\n";
        printf("%-40s %8s %12s %s\n", "TABELA", "SCORE", "REGISTROS", "CAMPOS CHAVE");
        echo str_repeat("-", 70) . "\n";
        
        foreach (array_slice($this->tabelasProdutos, 0, 5, true) as $tabela => $info) {
            $campos = [];
            foreach ($info['campos_encontrados'] as $tipo => $lista) {
                if (!empty($lista)) {
                    $campos[] = $tipo;
                }
            }
            printf("%-40s %8d %12s %s\n", 
                $tabela, 
                $info['score'], 
                number_format($info['total_registros']),
                implode(', ', $campos)
            );
        }
    }
    
    public function gerarMapeamento() {
        $this->separador();
        $this->log("PASSO 5: Gerando MAPEAMENTO AUTOMÁTICO", 'DESTAQUE');
        
        // Pegar a melhor candidata para vendas
        $melhorVendas = array_key_first($this->tabelasVendas);
        $infoVendas = $this->tabelasVendas[$melhorVendas] ?? null;
        
        // Pegar a melhor candidata para produtos
        $melhorProdutos = array_key_first($this->tabelasProdutos);
        $infoProdutos = $this->tabelasProdutos[$melhorProdutos] ?? null;
        
        if (!$melhorVendas || !$infoVendas) {
            $this->log("Não foi possível identificar tabela de vendas!", 'ERRO');
            return false;
        }
        
        $this->log("✅ Tabela de vendas identificada: {$melhorVendas}", 'SUCESSO');
        if ($melhorProdutos) {
            $this->log("✅ Tabela de produtos identificada: {$melhorProdutos}", 'SUCESSO');
        }
        
        // Mapear campos
        $this->mapeamento = [
            'tabela_vendas' => $melhorVendas,
            'tabela_produtos' => $melhorProdutos,
            'campos_vendas' => $infoVendas['campos_encontrados'],
            'campos_produtos' => $infoProdutos['campos_encontrados'] ?? []
        ];
        
        return true;
    }
    
    public function gerarScriptViews() {
        $this->separador();
        $this->log("PASSO 6: Gerando SCRIPT DE VIEWS", 'DESTAQUE');
        
        if (empty($this->mapeamento)) {
            $this->log("Mapeamento não disponível!", 'ERRO');
            return false;
        }
        
        $tabelaVendas = $this->mapeamento['tabela_vendas'];
        $tabelaProdutos = $this->mapeamento['tabela_produtos'] ?? null;
        $camposVendas = $this->mapeamento['campos_vendas'];
        
        // Identificar campos específicos
        $cdPedido = $camposVendas['pedido'][0] ?? 'cd_ped';
        $cdPessoa = $camposVendas['cliente'][0] ?? 'cd_pessoa';
        $dtPedido = $camposVendas['data'][0] ?? 'dt_hr_ped';
        $cdFilial = $camposVendas['filial'][0] ?? 'cd_filial';
        
        // Gerar SQL
        $sql = "-- =============================================\n";
        $sql .= "-- SCRIPT DE COMPATIBILIDADE - VIEWS DO ERP\n";
        $sql .= "-- Gerado automaticamente em: " . date('d/m/Y H:i:s') . "\n";
        $sql .= "-- =============================================\n\n";
        
        $sql .= "-- View: dm_orcamento_vendas_consolidadas\n";
        $sql .= "-- Mapeia a tabela real '{$tabelaVendas}' para o formato esperado\n\n";
        
        $sql .= "CREATE OR REPLACE VIEW dm_orcamento_vendas_consolidadas AS\n";
        $sql .= "SELECT \n";
        $sql .= "    {$cdPedido} as cd_pedido,\n";
        $sql .= "    {$cdPessoa} as cd_pessoa,\n";
        $sql .= "    {$cdFilial} as cd_filial,\n";
        $sql .= "    {$dtPedido} as dt_emi_pedido,\n";
        $sql .= "    -- Adicione outros campos conforme necessário\n";
        $sql .= "    NULL as nm_cliente,\n";
        $sql .= "    NULL as cd_cpl_tamanho,\n";
        $sql .= "    NULL as qtde_produto,\n";
        $sql .= "    NULL as vl_tot_it,\n";
        $sql .= "    0 as vl_devol_proporcional\n";
        $sql .= "FROM {$tabelaVendas};\n\n";
        
        if ($tabelaProdutos) {
            $sql .= "-- View: dm_produto\n";
            $sql .= "-- Mapeia a tabela real '{$tabelaProdutos}' para o formato esperado\n\n";
            $sql .= "CREATE OR REPLACE VIEW dm_produto AS\n";
            $sql .= "SELECT *\n";
            $sql .= "FROM {$tabelaProdutos};\n\n";
        }
        
        // Salvar em arquivo
        $arquivo = __DIR__ . '/criar_views_compatibilidade.sql';
        file_put_contents($arquivo, $sql);
        
        $this->log("Script SQL gerado: criar_views_compatibilidade.sql", 'SUCESSO');
        
        echo "\n" . str_repeat("=", 70) . "\n";
        echo $sql;
        echo str_repeat("=", 70) . "\n";
        
        return $arquivo;
    }
    
    public function gerarRelatorioCompleto() {
        $this->separador();
        $this->log("Gerando RELATÓRIO COMPLETO...", 'DESTAQUE');
        
        $relatorio = [
            'data_analise' => date('Y-m-d H:i:s'),
            'total_tabelas' => count($this->resultados['tabelas']),
            'tabelas_vendas_analisadas' => count($this->tabelasVendas),
            'tabelas_produtos_analisadas' => count($this->tabelasProdutos),
            'mapeamento' => $this->mapeamento,
            'detalhes_vendas' => $this->tabelasVendas,
            'detalhes_produtos' => $this->tabelasProdutos
        ];
        
        $arquivo = __DIR__ . '/analise_estrutura_erp.json';
        file_put_contents($arquivo, json_encode($relatorio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->log("Relatório JSON gerado: analise_estrutura_erp.json", 'SUCESSO');
        
        return $arquivo;
    }
    
    public function executar() {
        if (!$this->conectar()) {
            return false;
        }
        
        $this->listarTodasTabelas();
        $this->identificarTabelasVendas();
        $this->identificarTabelasProdutos();
        $this->analisarMelhoresCandidatas();
        $this->gerarMapeamento();
        $this->gerarScriptViews();
        $this->gerarRelatorioCompleto();
        
        $this->separador();
        $this->log("✨ ANÁLISE CONCLUÍDA COM SUCESSO!", 'SUCESSO');
        $this->log("Próximos passos:", 'DESTAQUE');
        echo "  1. Revisar o arquivo 'criar_views_compatibilidade.sql'\n";
        echo "  2. Ajustar os campos conforme necessário\n";
        echo "  3. Executar o script no banco de dados\n";
        echo "  4. Executar novamente o diagnóstico_relatorios_completo.php\n\n";
        
        return true;
    }
}

// Executar
$descobridor = new DescobridorEstruturaERP();
$descobridor->executar();
