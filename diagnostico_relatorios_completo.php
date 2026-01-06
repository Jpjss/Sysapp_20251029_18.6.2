<?php
/**
 * DIAGNÓSTICO COMPLETO DOS RELATÓRIOS
 * 
 * Este script verifica:
 * - Conexão com o banco de dados
 * - Existência das tabelas necessárias
 * - Estrutura das tabelas
 * - Dados disponíveis
 * - Todas as queries do CONSULTAS_RELATORIOS.md
 * - Métodos do Model Relatorio.php
 * 
 * Data: 05/01/2026
 */

// Desabilitar limite de tempo
set_time_limit(300);
ini_set('memory_limit', '512M');

// Configuração de erro detalhado
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Importar configurações
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/models/Relatorio.php';

Session::start();

// Classe de diagnóstico
class DiagnosticoRelatorios {
    private $db;
    private $resultados = [];
    private $erros = [];
    private $avisos = [];
    private $sucessos = [];
    private $totalTestes = 0;
    private $testesPassaram = 0;
    
    public function __construct() {
        $this->addLog('info', 'Iniciando diagnóstico completo dos relatórios...');
    }
    
    private function addLog($tipo, $mensagem, $detalhes = null) {
        $log = [
            'tipo' => $tipo,
            'mensagem' => $mensagem,
            'detalhes' => $detalhes,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        switch($tipo) {
            case 'erro':
                $this->erros[] = $log;
                break;
            case 'aviso':
                $this->avisos[] = $log;
                break;
            case 'sucesso':
                $this->sucessos[] = $log;
                $this->testesPassaram++;
                break;
            default:
                $this->resultados[] = $log;
        }
        
        $this->totalTestes++;
    }
    
    /**
     * TESTE 1: Verificar conexão com banco
     */
    public function testeConexaoBanco() {
        $this->addLog('info', '=== TESTE 1: VERIFICANDO CONEXÃO COM BANCO ===');
        
        try {
            // Verificar se há configuração de banco na sessão
            if (!Session::check('Config.database')) {
                $this->addLog('aviso', 'Nenhuma empresa selecionada na sessão');
                $this->addLog('info', 'Usando configuração do banco PROPASSO...');
                
                // FORÇAR CONEXÃO NO BANCO CORRETO (bd_propasso)
                $host = 'banco.propasso.systec.ftp.sh';
                $database = 'bd_propasso';
                $user = 'admin';
                $password = 'systec2011.';
                $port = '5432';
            } else {
                // Usar configuração da sessão
                $host = Session::read('Config.host');
                $database = Session::read('Config.database');
                $user = Session::read('Config.user');
                $password = Session::read('Config.password');
                $port = Session::read('Config.porta');
            }
            
            $this->addLog('info', "Tentando conectar em: {$host}:{$port}/{$database}");
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
            $this->db = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // Testar a conexão
            $version = $this->db->query('SELECT version()')->fetch();
            $this->addLog('sucesso', 'Conexão com banco estabelecida!', [
                'host' => $host,
                'database' => $database,
                'versao' => $version['version']
            ]);
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Falha ao conectar no banco de dados', [
                'mensagem' => $e->getMessage(),
                'codigo' => $e->getCode()
            ]);
            return false;
        }
    }
    
    /**
     * TESTE 2: Verificar existência das tabelas
     */
    public function testeExistenciaTabelas() {
        $this->addLog('info', '=== TESTE 2: VERIFICANDO EXISTÊNCIA DAS TABELAS ===');
        
        $tabelasNecessarias = [
            'dm_orcamento_vendas_consolidadas' => 'CRÍTICA - Tabela principal de vendas',
            'dm_produto' => 'CRÍTICA - Tabela de produtos e marcas',
            'prc_filial' => 'OPCIONAL - Tabela de filiais',
            'est_produto_cpl_tamanho' => 'OPCIONAL - Tabela de produtos/tamanhos'
        ];
        
        try {
            $sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename";
            $tabelas = $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
            
            $this->addLog('info', 'Total de tabelas encontradas: ' . count($tabelas));
            
            foreach ($tabelasNecessarias as $tabela => $descricao) {
                if (in_array($tabela, $tabelas)) {
                    // Contar registros
                    try {
                        $count = $this->db->query("SELECT COUNT(*) FROM {$tabela}")->fetchColumn();
                        $this->addLog('sucesso', "Tabela '{$tabela}' encontrada", [
                            'descricao' => $descricao,
                            'total_registros' => $count
                        ]);
                    } catch (Exception $e) {
                        $this->addLog('aviso', "Tabela '{$tabela}' existe mas erro ao contar registros", [
                            'erro' => $e->getMessage()
                        ]);
                    }
                } else {
                    if (strpos($descricao, 'CRÍTICA') !== false) {
                        $this->addLog('erro', "Tabela '{$tabela}' NÃO encontrada!", [
                            'descricao' => $descricao
                        ]);
                    } else {
                        $this->addLog('aviso', "Tabela '{$tabela}' não encontrada", [
                            'descricao' => $descricao
                        ]);
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Erro ao verificar tabelas', [
                'mensagem' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * TESTE 3: Verificar estrutura da tabela principal
     */
    public function testeEstruturaTabelaPrincipal() {
        $this->addLog('info', '=== TESTE 3: VERIFICANDO ESTRUTURA DA TABELA PRINCIPAL ===');
        
        $camposEsperados = [
            'cd_pedido' => 'Código do pedido',
            'cd_pessoa' => 'Código do cliente',
            'nm_cliente' => 'Nome do cliente',
            'cd_cpl_tamanho' => 'Código produto/tamanho',
            'dt_emi_pedido' => 'Data/hora emissão',
            'qtde_produto' => 'Quantidade vendida',
            'vl_tot_it' => 'Valor total item',
            'vl_devol_proporcional' => 'Valor devolução'
        ];
        
        try {
            $sql = "SELECT column_name, data_type, is_nullable 
                    FROM information_schema.columns 
                    WHERE table_name = 'dm_orcamento_vendas_consolidadas' 
                    ORDER BY ordinal_position";
            
            $colunas = $this->db->query($sql)->fetchAll();
            
            if (empty($colunas)) {
                $this->addLog('erro', 'Tabela dm_orcamento_vendas_consolidadas não encontrada ou sem colunas');
                return false;
            }
            
            $this->addLog('info', 'Total de colunas na tabela: ' . count($colunas));
            
            $colunasEncontradas = array_column($colunas, 'column_name');
            
            foreach ($camposEsperados as $campo => $descricao) {
                if (in_array($campo, $colunasEncontradas)) {
                    $info = array_filter($colunas, function($c) use ($campo) {
                        return $c['column_name'] === $campo;
                    });
                    $info = reset($info);
                    
                    $this->addLog('sucesso', "Campo '{$campo}' encontrado", [
                        'descricao' => $descricao,
                        'tipo' => $info['data_type'],
                        'permite_null' => $info['is_nullable']
                    ]);
                } else {
                    $this->addLog('erro', "Campo '{$campo}' NÃO encontrado!", [
                        'descricao' => $descricao
                    ]);
                }
            }
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Erro ao verificar estrutura da tabela', [
                'mensagem' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * TESTE 4: Verificar dados disponíveis
     */
    public function testeDadosDisponiveis() {
        $this->addLog('info', '=== TESTE 4: VERIFICANDO DADOS DISPONÍVEIS ===');
        
        try {
            // Total de registros
            $total = $this->db->query("SELECT COUNT(*) FROM dm_orcamento_vendas_consolidadas")->fetchColumn();
            $this->addLog('info', "Total de registros na tabela: {$total}");
            
            if ($total == 0) {
                $this->addLog('erro', 'Tabela dm_orcamento_vendas_consolidadas está VAZIA!', [
                    'impacto' => 'Nenhum relatório retornará dados'
                ]);
                return false;
            }
            
            // Pedidos únicos
            $pedidos = $this->db->query("SELECT COUNT(DISTINCT cd_pedido) FROM dm_orcamento_vendas_consolidadas")->fetchColumn();
            $this->addLog('sucesso', "Total de pedidos únicos: {$pedidos}");
            
            // Clientes únicos
            $clientes = $this->db->query("SELECT COUNT(DISTINCT cd_pessoa) FROM dm_orcamento_vendas_consolidadas")->fetchColumn();
            $this->addLog('sucesso', "Total de clientes únicos: {$clientes}");
            
            // Período dos dados
            $sql = "SELECT 
                        MIN(dt_emi_pedido) as primeira_venda,
                        MAX(dt_emi_pedido) as ultima_venda,
                        MAX(dt_emi_pedido)::date = CURRENT_DATE as tem_venda_hoje
                    FROM dm_orcamento_vendas_consolidadas";
            $periodo = $this->db->query($sql)->fetch();
            
            $this->addLog('info', 'Período dos dados', [
                'primeira_venda' => $periodo['primeira_venda'],
                'ultima_venda' => $periodo['ultima_venda'],
                'tem_venda_hoje' => $periodo['tem_venda_hoje'] ? 'SIM' : 'NÃO'
            ]);
            
            // Verificar valores
            $sql = "SELECT 
                        COUNT(*) as total,
                        COUNT(CASE WHEN vl_tot_it IS NULL THEN 1 END) as vl_tot_null,
                        COUNT(CASE WHEN vl_devol_proporcional IS NULL THEN 1 END) as vl_devol_null,
                        SUM(vl_tot_it - vl_devol_proporcional) as valor_total
                    FROM dm_orcamento_vendas_consolidadas";
            $valores = $this->db->query($sql)->fetch();
            
            if ($valores['vl_tot_null'] > 0 || $valores['vl_devol_null'] > 0) {
                $this->addLog('aviso', 'Existem valores NULL nos campos de valor', $valores);
            } else {
                $this->addLog('sucesso', 'Todos os registros têm valores preenchidos', [
                    'valor_total' => number_format($valores['valor_total'], 2, ',', '.')
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Erro ao verificar dados disponíveis', [
                'mensagem' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * TESTE 5: Testar queries do CONSULTAS_RELATORIOS.md
     */
    public function testeQueriesRelatorios() {
        $this->addLog('info', '=== TESTE 5: TESTANDO QUERIES DO CONSULTAS_RELATORIOS.MD ===');
        
        $queries = [
            'Query #1.1 - Total de Clientes' => [
                'sql' => "SELECT COUNT(DISTINCT cd_pessoa) as total FROM dm_orcamento_vendas_consolidadas",
                'params' => []
            ],
            'Query #1.2 - Total de Vendas' => [
                'sql' => "SELECT COUNT(DISTINCT cd_pedido) as total, 
                         COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total 
                         FROM dm_orcamento_vendas_consolidadas",
                'params' => []
            ],
            'Query #1.3 - Vendas Hoje' => [
                'sql' => "SELECT COUNT(DISTINCT cd_pedido) as total, 
                         COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_hoje 
                         FROM dm_orcamento_vendas_consolidadas 
                         WHERE dt_emi_pedido >= CURRENT_DATE 
                         AND dt_emi_pedido < CURRENT_DATE + INTERVAL '1 day'",
                'params' => []
            ],
            'Query #1.4 - Vendas no Mês' => [
                'sql' => "SELECT COUNT(DISTINCT cd_pedido) as total, 
                         COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_mes 
                         FROM dm_orcamento_vendas_consolidadas 
                         WHERE EXTRACT(MONTH FROM dt_emi_pedido) = EXTRACT(MONTH FROM CURRENT_DATE)
                         AND EXTRACT(YEAR FROM dt_emi_pedido) = EXTRACT(YEAR FROM CURRENT_DATE)",
                'params' => []
            ],
            'Query #2 - Vendas por Período' => [
                'sql' => "SELECT DATE(dt_emi_pedido) as data, 
                         COUNT(DISTINCT cd_pedido) as total,
                         COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                         COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                         FROM dm_orcamento_vendas_consolidadas
                         WHERE DATE(dt_emi_pedido) BETWEEN :dt_inicio AND :dt_fim
                         GROUP BY DATE(dt_emi_pedido)
                         ORDER BY DATE(dt_emi_pedido)
                         LIMIT 10",
                'params' => [
                    ':dt_inicio' => date('Y-m-01'),
                    ':dt_fim' => date('Y-m-d')
                ]
            ],
            'Query #3 - Top Clientes' => [
                'sql' => "SELECT v.cd_pessoa,
                         v.nm_cliente,
                         COUNT(DISTINCT v.cd_pedido) as total_atendimentos,
                         MAX(v.dt_emi_pedido) as ultimo_atendimento,
                         COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                         FROM dm_orcamento_vendas_consolidadas v
                         GROUP BY v.cd_pessoa, v.nm_cliente
                         ORDER BY valor_total DESC
                         LIMIT 5",
                'params' => []
            ],
            'Query #7 - Vendas Detalhadas' => [
                'sql' => "SELECT 
                         DATE(dt_emi_pedido) as data,
                         COUNT(DISTINCT cd_pedido) as total_atendimentos,
                         COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                         COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                         FROM dm_orcamento_vendas_consolidadas
                         WHERE DATE(dt_emi_pedido) >= :dt_inicio::date 
                         AND DATE(dt_emi_pedido) <= :dt_fim::date
                         GROUP BY DATE(dt_emi_pedido)
                         ORDER BY data ASC
                         LIMIT 10",
                'params' => [
                    ':dt_inicio' => date('Y-m-01'),
                    ':dt_fim' => date('Y-m-d')
                ]
            ],
            'Query #8 - Totais do Período' => [
                'sql' => "SELECT 
                         COUNT(DISTINCT cd_pedido) as total_atendimentos,
                         COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                         COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                         FROM dm_orcamento_vendas_consolidadas
                         WHERE DATE(dt_emi_pedido) >= :dt_inicio::date 
                         AND DATE(dt_emi_pedido) <= :dt_fim::date",
                'params' => [
                    ':dt_inicio' => date('Y-m-01'),
                    ':dt_fim' => date('Y-m-d')
                ]
            ]
        ];
        
        foreach ($queries as $nome => $config) {
            try {
                $stmt = $this->db->prepare($config['sql']);
                
                $inicio = microtime(true);
                $stmt->execute($config['params']);
                $tempo = round((microtime(true) - $inicio) * 1000, 2);
                
                $resultado = $stmt->fetchAll();
                $totalLinhas = count($resultado);
                
                $this->addLog('sucesso', "{$nome} - OK", [
                    'linhas_retornadas' => $totalLinhas,
                    'tempo_ms' => $tempo,
                    'preview' => $totalLinhas > 0 ? $resultado[0] : 'Sem dados'
                ]);
            } catch (Exception $e) {
                $this->addLog('erro', "{$nome} - FALHOU", [
                    'mensagem' => $e->getMessage(),
                    'sql' => $config['sql'],
                    'params' => $config['params']
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * TESTE 6: Testar queries de marcas
     */
    public function testeQueriesMarcas() {
        $this->addLog('info', '=== TESTE 6: TESTANDO QUERIES DE MARCAS ===');
        
        try {
            // Verificar se tabela dm_produto existe
            $tabelaExiste = $this->db->query(
                "SELECT COUNT(*) FROM pg_tables WHERE tablename = 'dm_produto'"
            )->fetchColumn();
            
            if ($tabelaExiste == 0) {
                $this->addLog('aviso', 'Tabela dm_produto não existe - queries de marcas não funcionarão');
                return false;
            }
            
            // Query #4 - Marcas mais vendidas
            $sql = "SELECT 
                        dm_produto.cd_marca,
                        dm_produto.ds_marca,
                        COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
                        SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
                        SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
                    FROM dm_produto
                    INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
                        ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
                    WHERE dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL '30 days'
                        AND dm_produto.cd_marca IS NOT NULL
                        AND dm_produto.ds_marca IS NOT NULL
                    GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
                    ORDER BY quantidade_vendida DESC
                    LIMIT 10";
            
            $inicio = microtime(true);
            $marcas = $this->db->query($sql)->fetchAll();
            $tempo = round((microtime(true) - $inicio) * 1000, 2);
            
            if (count($marcas) > 0) {
                $this->addLog('sucesso', 'Query #4 - Marcas Mais Vendidas - OK', [
                    'total_marcas' => count($marcas),
                    'tempo_ms' => $tempo,
                    'top_1' => $marcas[0]
                ]);
            } else {
                $this->addLog('aviso', 'Query #4 retornou 0 marcas', [
                    'possivel_causa' => 'Não há vendas nos últimos 30 dias ou dados de marca não estão preenchidos'
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Erro ao testar queries de marcas', [
                'mensagem' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * TESTE 7: Testar métodos do Model
     */
    public function testeMetodosModel() {
        $this->addLog('info', '=== TESTE 7: TESTANDO MÉTODOS DO MODEL RELATORIO.PHP ===');
        
        try {
            $relatorio = new Relatorio();
            
            // Teste: getEstatisticas()
            try {
                $stats = $relatorio->getEstatisticas();
                if (is_array($stats) && isset($stats['total_clientes'])) {
                    $this->addLog('sucesso', 'Model::getEstatisticas() - OK', $stats);
                } else {
                    $this->addLog('erro', 'Model::getEstatisticas() - Retorno inválido', $stats);
                }
            } catch (Exception $e) {
                $this->addLog('erro', 'Model::getEstatisticas() - ERRO', [
                    'mensagem' => $e->getMessage()
                ]);
            }
            
            // Teste: getAtendimentosPorPeriodo()
            try {
                $periodo = $relatorio->getAtendimentosPorPeriodo(date('Y-m-01'), date('Y-m-d'));
                $this->addLog('sucesso', 'Model::getAtendimentosPorPeriodo() - OK', [
                    'total_dias' => count($periodo)
                ]);
            } catch (Exception $e) {
                $this->addLog('erro', 'Model::getAtendimentosPorPeriodo() - ERRO', [
                    'mensagem' => $e->getMessage()
                ]);
            }
            
            // Teste: getTopClientes()
            try {
                $clientes = $relatorio->getTopClientes(5);
                $this->addLog('sucesso', 'Model::getTopClientes() - OK', [
                    'total_clientes' => count($clientes)
                ]);
            } catch (Exception $e) {
                $this->addLog('erro', 'Model::getTopClientes() - ERRO', [
                    'mensagem' => $e->getMessage()
                ]);
            }
            
            // Teste: getAtendimentosDetalhados()
            try {
                $detalhados = $relatorio->getAtendimentosDetalhados(date('Y-m-01'), date('Y-m-d'));
                $this->addLog('sucesso', 'Model::getAtendimentosDetalhados() - OK', [
                    'total_registros' => count($detalhados)
                ]);
            } catch (Exception $e) {
                $this->addLog('erro', 'Model::getAtendimentosDetalhados() - ERRO', [
                    'mensagem' => $e->getMessage()
                ]);
            }
            
            // Teste: getTotaisAtendimentos()
            try {
                $totais = $relatorio->getTotaisAtendimentos(date('Y-m-01'), date('Y-m-d'));
                if (is_array($totais) && isset($totais['total_atendimentos'])) {
                    $this->addLog('sucesso', 'Model::getTotaisAtendimentos() - OK', $totais);
                } else {
                    $this->addLog('erro', 'Model::getTotaisAtendimentos() - Retorno inválido', $totais);
                }
            } catch (Exception $e) {
                $this->addLog('erro', 'Model::getTotaisAtendimentos() - ERRO', [
                    'mensagem' => $e->getMessage()
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Erro ao testar métodos do Model', [
                'mensagem' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * TESTE 8: Performance e Índices
     */
    public function testePerformance() {
        $this->addLog('info', '=== TESTE 8: VERIFICANDO PERFORMANCE E ÍNDICES ===');
        
        try {
            // Verificar índices na tabela principal
            $sql = "SELECT 
                        indexname, 
                        indexdef 
                    FROM pg_indexes 
                    WHERE tablename = 'dm_orcamento_vendas_consolidadas'";
            $indices = $this->db->query($sql)->fetchAll();
            
            if (count($indices) > 0) {
                $this->addLog('sucesso', 'Índices encontrados na tabela principal', [
                    'total_indices' => count($indices),
                    'indices' => array_column($indices, 'indexname')
                ]);
            } else {
                $this->addLog('aviso', 'Nenhum índice encontrado na tabela principal', [
                    'recomendacao' => 'Criar índices em dt_emi_pedido, cd_pedido, cd_pessoa para melhor performance'
                ]);
            }
            
            // Testar performance de query comum
            $sql = "SELECT COUNT(DISTINCT cd_pedido) FROM dm_orcamento_vendas_consolidadas 
                    WHERE DATE(dt_emi_pedido) >= CURRENT_DATE - INTERVAL '30 days'";
            
            $inicio = microtime(true);
            $this->db->query($sql);
            $tempo = round((microtime(true) - $inicio) * 1000, 2);
            
            if ($tempo < 100) {
                $this->addLog('sucesso', 'Performance EXCELENTE', ['tempo_ms' => $tempo]);
            } elseif ($tempo < 500) {
                $this->addLog('sucesso', 'Performance BOA', ['tempo_ms' => $tempo]);
            } elseif ($tempo < 1000) {
                $this->addLog('aviso', 'Performance REGULAR', ['tempo_ms' => $tempo, 'recomendacao' => 'Considerar criar índices']);
            } else {
                $this->addLog('aviso', 'Performance LENTA', ['tempo_ms' => $tempo, 'recomendacao' => 'URGENTE: Criar índices']);
            }
            
            return true;
        } catch (Exception $e) {
            $this->addLog('erro', 'Erro ao testar performance', [
                'mensagem' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Executar todos os testes
     */
    public function executarTodosTestes() {
        $inicio = microtime(true);
        
        // Teste 1: Conexão
        if (!$this->testeConexaoBanco()) {
            $this->addLog('erro', 'DIAGNÓSTICO INTERROMPIDO: Falha na conexão com banco');
            return $this->gerarRelatorio();
        }
        
        // Teste 2: Tabelas
        $this->testeExistenciaTabelas();
        
        // Teste 3: Estrutura
        $this->testeEstruturaTabelaPrincipal();
        
        // Teste 4: Dados
        $this->testeDadosDisponiveis();
        
        // Teste 5: Queries
        $this->testeQueriesRelatorios();
        
        // Teste 6: Marcas
        $this->testeQueriesMarcas();
        
        // Teste 7: Model
        $this->testeMetodosModel();
        
        // Teste 8: Performance
        $this->testePerformance();
        
        $tempoTotal = round(microtime(true) - $inicio, 2);
        $this->addLog('info', "Diagnóstico concluído em {$tempoTotal} segundos");
        
        return $this->gerarRelatorio();
    }
    
    /**
     * Gerar relatório HTML
     */
    public function gerarRelatorio() {
        $totalErros = count($this->erros);
        $totalAvisos = count($this->avisos);
        $totalSucessos = count($this->sucessos);
        $percentualSucesso = $this->totalTestes > 0 ? round(($this->testesPassaram / $this->totalTestes) * 100, 1) : 0;
        
        // Determinar status geral
        if ($totalErros == 0 && $totalAvisos == 0) {
            $statusGeral = 'EXCELENTE';
            $corStatus = '#10b981';
        } elseif ($totalErros == 0) {
            $statusGeral = 'BOM';
            $corStatus = '#3b82f6';
        } elseif ($totalErros < 5) {
            $statusGeral = 'ATENÇÃO';
            $corStatus = '#f59e0b';
        } else {
            $statusGeral = 'CRÍTICO';
            $corStatus = '#ef4444';
        }
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Diagnóstico Completo dos Relatórios - SysApp</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 20px;
                    min-height: 100vh;
                }
                .container {
                    max-width: 1400px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    font-size: 2.5rem;
                    margin-bottom: 10px;
                }
                .header p {
                    font-size: 1.1rem;
                    opacity: 0.9;
                }
                .resumo {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    padding: 30px;
                    background: #f8fafc;
                    border-bottom: 3px solid #e2e8f0;
                }
                .card-resumo {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                    text-align: center;
                    transition: transform 0.2s;
                }
                .card-resumo:hover {
                    transform: translateY(-5px);
                }
                .card-resumo .numero {
                    font-size: 3rem;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
                .card-resumo .label {
                    font-size: 0.9rem;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .status-geral {
                    margin: 30px;
                    padding: 30px;
                    background: white;
                    border-radius: 10px;
                    border-left: 5px solid <?php echo $corStatus; ?>;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .status-geral h2 {
                    color: <?php echo $corStatus; ?>;
                    font-size: 2rem;
                    margin-bottom: 10px;
                }
                .conteudo {
                    padding: 30px;
                }
                .secao {
                    margin-bottom: 30px;
                }
                .secao-titulo {
                    font-size: 1.5rem;
                    color: #1e293b;
                    margin-bottom: 15px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #e2e8f0;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .log-item {
                    background: white;
                    border-left: 4px solid;
                    padding: 15px;
                    margin-bottom: 10px;
                    border-radius: 5px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .log-item.erro { border-color: #ef4444; background: #fef2f2; }
                .log-item.aviso { border-color: #f59e0b; background: #fffbeb; }
                .log-item.sucesso { border-color: #10b981; background: #f0fdf4; }
                .log-item.info { border-color: #3b82f6; background: #eff6ff; }
                .log-titulo {
                    font-weight: 600;
                    margin-bottom: 8px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .log-detalhes {
                    background: rgba(0,0,0,0.03);
                    padding: 10px;
                    border-radius: 5px;
                    margin-top: 10px;
                    font-size: 0.9rem;
                    font-family: 'Courier New', monospace;
                    max-height: 300px;
                    overflow-y: auto;
                }
                .badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                .badge.erro { background: #fee2e2; color: #991b1b; }
                .badge.aviso { background: #fef3c7; color: #92400e; }
                .badge.sucesso { background: #d1fae5; color: #065f46; }
                .badge.info { background: #dbeafe; color: #1e40af; }
                .progress-bar {
                    width: 100%;
                    height: 30px;
                    background: #e2e8f0;
                    border-radius: 15px;
                    overflow: hidden;
                    margin: 20px 0;
                }
                .progress-fill {
                    height: 100%;
                    background: linear-gradient(90deg, #10b981, #3b82f6);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 600;
                    transition: width 0.5s ease;
                }
                .timestamp {
                    font-size: 0.85rem;
                    color: #94a3b8;
                    margin-top: 5px;
                }
                @media print {
                    body { background: white; padding: 0; }
                    .container { box-shadow: none; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔍 Diagnóstico Completo dos Relatórios</h1>
                    <p>Sistema SysApp - Verificação detalhada de queries e dados</p>
                    <p style="font-size: 0.9rem; margin-top: 10px;">
                        <?php echo date('d/m/Y H:i:s'); ?>
                    </p>
                </div>
                
                <div class="resumo">
                    <div class="card-resumo">
                        <div class="numero" style="color: #10b981;"><?php echo $totalSucessos; ?></div>
                        <div class="label">Sucessos</div>
                    </div>
                    <div class="card-resumo">
                        <div class="numero" style="color: #f59e0b;"><?php echo $totalAvisos; ?></div>
                        <div class="label">Avisos</div>
                    </div>
                    <div class="card-resumo">
                        <div class="numero" style="color: #ef4444;"><?php echo $totalErros; ?></div>
                        <div class="label">Erros</div>
                    </div>
                    <div class="card-resumo">
                        <div class="numero" style="color: #3b82f6;"><?php echo $this->totalTestes; ?></div>
                        <div class="label">Total de Testes</div>
                    </div>
                </div>
                
                <div class="status-geral">
                    <h2>Status Geral: <?php echo $statusGeral; ?></h2>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $percentualSucesso; ?>%;">
                            <?php echo $percentualSucesso; ?>% de sucesso
                        </div>
                    </div>
                    <?php if ($totalErros > 0): ?>
                        <p style="color: #ef4444; margin-top: 10px;">
                            ⚠️ Foram encontrados <strong><?php echo $totalErros; ?> erros críticos</strong> que precisam ser corrigidos.
                        </p>
                    <?php elseif ($totalAvisos > 0): ?>
                        <p style="color: #f59e0b; margin-top: 10px;">
                            ℹ️ Existem <strong><?php echo $totalAvisos; ?> avisos</strong> que merecem atenção.
                        </p>
                    <?php else: ?>
                        <p style="color: #10b981; margin-top: 10px;">
                            ✅ Todos os testes passaram com sucesso! Os relatórios estão funcionais.
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="conteudo">
                    <?php if (count($this->erros) > 0): ?>
                        <div class="secao">
                            <h2 class="secao-titulo">
                                ❌ Erros Encontrados
                            </h2>
                            <?php foreach ($this->erros as $log): ?>
                                <div class="log-item erro">
                                    <div class="log-titulo">
                                        <span class="badge erro">ERRO</span>
                                        <?php echo htmlspecialchars($log['mensagem']); ?>
                                    </div>
                                    <?php if ($log['detalhes']): ?>
                                        <div class="log-detalhes">
                                            <?php echo htmlspecialchars(json_encode($log['detalhes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="timestamp"><?php echo $log['timestamp']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($this->avisos) > 0): ?>
                        <div class="secao">
                            <h2 class="secao-titulo">
                                ⚠️ Avisos
                            </h2>
                            <?php foreach ($this->avisos as $log): ?>
                                <div class="log-item aviso">
                                    <div class="log-titulo">
                                        <span class="badge aviso">AVISO</span>
                                        <?php echo htmlspecialchars($log['mensagem']); ?>
                                    </div>
                                    <?php if ($log['detalhes']): ?>
                                        <div class="log-detalhes">
                                            <?php echo htmlspecialchars(json_encode($log['detalhes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="timestamp"><?php echo $log['timestamp']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="secao">
                        <h2 class="secao-titulo">
                            ✅ Testes Bem-Sucedidos
                        </h2>
                        <?php foreach ($this->sucessos as $log): ?>
                            <div class="log-item sucesso">
                                <div class="log-titulo">
                                    <span class="badge sucesso">OK</span>
                                    <?php echo htmlspecialchars($log['mensagem']); ?>
                                </div>
                                <?php if ($log['detalhes']): ?>
                                    <div class="log-detalhes">
                                        <?php echo htmlspecialchars(json_encode($log['detalhes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="timestamp"><?php echo $log['timestamp']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="secao">
                        <h2 class="secao-titulo">
                            ℹ️ Log Completo
                        </h2>
                        <?php foreach ($this->resultados as $log): ?>
                            <div class="log-item info">
                                <div class="log-titulo">
                                    <span class="badge info">INFO</span>
                                    <?php echo htmlspecialchars($log['mensagem']); ?>
                                </div>
                                <?php if ($log['detalhes']): ?>
                                    <div class="log-detalhes">
                                        <?php echo htmlspecialchars(json_encode($log['detalhes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="timestamp"><?php echo $log['timestamp']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}

// Executar diagnóstico
$diagnostico = new DiagnosticoRelatorios();
echo $diagnostico->executarTodosTestes();
