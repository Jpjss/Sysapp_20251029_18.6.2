<?php
/**
 * Controller de Relatórios
 */

require_once BASE_PATH . '/models/Relatorio.php';
require_once BASE_PATH . '/models/Usuario.php';

class RelatoriosController extends Controller {
    private $Relatorio;
    private $Usuario;
    
    public function __construct() {
        parent::__construct();
        $this->Relatorio = new Relatorio();
        $this->Usuario = new Usuario();
    }
    
    /**
     * Dashboard principal
     */
    public function index() {
        $this->requireAuth();
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            $this->redirect('relatorios/empresa');
        }
        
        $stats = $this->Relatorio->getEstatisticas();
        $topClientes = $this->Relatorio->getTopClientes(5);
        
        // Atendimentos dos últimos 7 dias
        $dt_inicio = date('Y-m-d', strtotime('-7 days'));
        $dt_fim = date('Y-m-d');
        $atendimentosPeriodo = $this->Relatorio->getAtendimentosPorPeriodo($dt_inicio, $dt_fim);
        
        $this->set([
            'stats' => $stats,
            'topClientes' => $topClientes,
            'atendimentosPeriodo' => $atendimentosPeriodo
        ]);
        
        $this->render();
    }
    
    /**
     * API: Retorna estatísticas em JSON (para atualização automática)
     */
    public function getEstatisticasJson() {
        $this->requireAuth();
        $this->layout = false;
        
        header('Content-Type: application/json');
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            echo json_encode(['error' => 'Nenhuma empresa selecionada']);
            exit;
        }
        
        $stats = $this->Relatorio->getEstatisticas();
        $topClientes = $this->Relatorio->getTopClientes(5);
        
        // Atendimentos dos últimos 7 dias
        $dt_inicio = date('Y-m-d', strtotime('-7 days'));
        $dt_fim = date('Y-m-d');
        $atendimentosPeriodo = $this->Relatorio->getAtendimentosPorPeriodo($dt_inicio, $dt_fim);
        
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'stats' => $stats,
            'topClientes' => $topClientes,
            'atendimentosPeriodo' => $atendimentosPeriodo
        ]);
        exit;
    }
    
    /**
     * Seleção de empresa (quando usuário tem múltiplas empresas)
     */
    public function empresa() {
        $this->requireAuth();
        
        $empresas = Session::read('Dados.database');
        
        if (empty($empresas)) {
            // Se não tem empresas, mostra erro e faz logout
            Session::setFlash('Seu usuário não possui empresas configuradas. Entre em contato com o administrador.', 'error');
            $this->redirect('usuarios/logout');
            return;
        }
        
        if ($this->isPost()) {
            $cd_empresa = (int)$_POST['cd_empresa'];
            
            // Busca dados da empresa selecionada
            $empresaSelecionada = null;
            foreach ($empresas as $emp) {
                if ($emp['cd_empresa'] == $cd_empresa) {
                    $empresaSelecionada = $emp;
                    break;
                }
            }
            
            if ($empresaSelecionada) {
                // Configura empresa na sessão
                Session::write('Config.database', $empresaSelecionada['nome_banco']);
                Session::write('Config.databasename', $empresaSelecionada['nome_banco']);
                Session::write('Config.host', $empresaSelecionada['hostname_banco']);
                Session::write('Config.user', $empresaSelecionada['usuario_banco']);
                Session::write('Config.password', Security::decrypt($empresaSelecionada['senha_banco']));
                Session::write('Config.porta', $empresaSelecionada['porta_banco']);
                Session::write('Config.empresa', $empresaSelecionada['nome_empresa']);
                
                // Reconecta ao banco da empresa
                $result = $this->db->connect(
                    $empresaSelecionada['hostname_banco'],
                    $empresaSelecionada['nome_banco'],
                    $empresaSelecionada['usuario_banco'],
                    Security::decrypt($empresaSelecionada['senha_banco']),
                    $empresaSelecionada['porta_banco']
                );
                
                $this->redirect('relatorios/index');
            }
        }
        
        $this->set(['empresas' => $empresas]);
        $this->render();
    }
    
    /**
     * Relatório de atendimentos
     */
    public function atendimentos() {
        $this->requireAuth();
        
        $dt_inicio = $_GET['dt_inicio'] ?? date('Y-m-01');
        $dt_fim = $_GET['dt_fim'] ?? date('Y-m-d');
        
        $atendimentos = $this->Relatorio->getAtendimentosPorPeriodo($dt_inicio, $dt_fim);
        $atendimentosUsuario = $this->Relatorio->getAtendimentosPorUsuario($dt_inicio, $dt_fim);
        
        $this->set([
            'atendimentos' => $atendimentos,
            'atendimentosUsuario' => $atendimentosUsuario,
            'dt_inicio' => $dt_inicio,
            'dt_fim' => $dt_fim
        ]);
        
        $this->render();
    }
    
    /**
     * Relatório simplificado
     */
    public function simplificado() {
        $this->requireAuth();
        
        $filtros = [
            'dt_inicio' => $_GET['dt_inicio'] ?? '',
            'dt_fim' => $_GET['dt_fim'] ?? '',
            'cd_usuario' => $_GET['cd_usuario'] ?? ''
        ];
        
        $dados = $this->Relatorio->getRelatorioSimplificado($filtros);
        
        // Busca usuários para filtro
        $usuarios = $this->db->fetchAll("SELECT cd_usuario, nome_usuario FROM vw_login ORDER BY nome_usuario");
        
        $this->set([
            'dados' => $dados,
            'filtros' => $filtros,
            'usuarios' => $usuarios
        ]);
        
        $this->render();
    }
    
    /**
     * Exportar relatório para CSV
     */
    public function exportar() {
        $this->requireAuth();
        $this->layout = false;
        
        $tipo = $_GET['tipo'] ?? 'atendimentos';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_' . $tipo . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        if ($tipo === 'atendimentos') {
            $dt_inicio = $_GET['dt_inicio'] ?? date('Y-m-01');
            $dt_fim = $_GET['dt_fim'] ?? date('Y-m-d');
            
            $dados = $this->Relatorio->getAtendimentosPorPeriodo($dt_inicio, $dt_fim);
            
            // Cabeçalho
            fputcsv($output, ['Data', 'Total Atendimentos', 'Clientes Únicos'], ';');
            
            // Dados
            foreach ($dados as $row) {
                fputcsv($output, [
                    date('d/m/Y', strtotime($row['data'])),
                    $row['total'],
                    $row['clientes_unicos']
                ], ';');
            }
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Relatório de Estoque Detalhado por Família/Grupo
     */
    public function estoque_detalhado() {
        $this->requireAuth();
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            $this->redirect('relatorios/empresa');
            return;
        }
        
        // Se for POST, processa o relatório
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Data de referência
            $dt_referencia = $_POST['dt_referencia'] ?? date('d/m/Y');
            $dt_referencia_bd = $this->formatarDataBd($dt_referencia);
            
            // Filiais selecionadas
            $filiais = $_POST['filiais'] ?? [];
            $cd_filial = implode(',', array_map('intval', $filiais));
            
            if (empty($cd_filial)) {
                Session::setFlash('Selecione ao menos uma filial', 'error');
                $this->redirect('relatorios/estoque_detalhado');
                return;
            }
            
            // Tipo de agrupamento
            $tipo_agrupamento = $_POST['tipo_agrupamento'] ?? 'FAMILIA';
            
            // Ordenação
            $ordenacao = $_POST['ordenacao'] ?? 'VALOR_DESC';
            
            // Filtro de estoque zerado
            $exibir_estoque_zerado = isset($_POST['exibir_estoque_zerado']);
            
            // Tipo de arquivo
            $tipo_arquivo = $_POST['tipo_arquivo'] ?? 'HTML';
            
            // Parâmetros para o model
            $parametros = [
                'dt_referencia' => $dt_referencia_bd,
                'cd_filial' => $cd_filial,
                'tipo_agrupamento' => $tipo_agrupamento,
                'ordenacao' => $ordenacao,
                'exibir_estoque_zerado' => $exibir_estoque_zerado
            ];
            
            // Buscar dados
            $dadosRelatorio = $this->Relatorio->estoque_detalhado($parametros);
            
            // Calcular totais
            $total_custo = 0;
            $total_qtde = 0;
            $total_skus = 0;
            
            if ($dadosRelatorio) {
                foreach ($dadosRelatorio as $linha) {
                    $total_custo += floatval($linha['custo_total']);
                    $total_qtde += floatval($linha['qtde_total']);
                    $total_skus += intval($linha['total_skus']);
                }
            }
            
            // Se for Excel, gera e faz download
            if ($tipo_arquivo === 'EXCEL') {
                $this->gerarExcelEstoqueDetalhado($dadosRelatorio, $dt_referencia, $tipo_agrupamento, $total_custo, $total_qtde, $total_skus);
                return;
            }
            
            // HTML - renderiza view
            $this->set([
                'dadosRelatorio' => $dadosRelatorio,
                'data_formatada' => $dt_referencia,
                'tipo_agrupamento' => $tipo_agrupamento,
                'ordenacao' => $ordenacao,
                'tipo_arquivo' => $tipo_arquivo,
                'total_custo' => $total_custo,
                'total_qtde' => $total_qtde,
                'total_skus' => $total_skus,
                'exibir_estoque_zerado' => $exibir_estoque_zerado
            ]);
            
            $this->layout = 'relatorio';
            $this->render('estoque_detalhado_resultado');
            return;
        }
        
        // GET - exibe formulário de filtros
        $filiais = $this->Relatorio->getFiliais();
        
        $this->set([
            'filiais' => $filiais
        ]);
        
        $this->render('estoque_detalhado');
    }
    
    /**
     * Gera arquivo Excel do relatório de estoque detalhado
     */
    private function gerarExcelEstoqueDetalhado($dados, $data, $tipo, $total_custo, $total_qtde, $total_skus) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="estoque_detalhado_' . date('Y-m-d_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Título
        fputcsv($output, ['Relatório de Estoque Detalhado - ' . ucfirst(strtolower($tipo))], ';');
        fputcsv($output, ['Emissão: ' . date('d/m/Y H:i:s') . ' | Data de Referência: ' . $data], ';');
        fputcsv($output, [''], ';'); // Linha vazia
        
        // Cabeçalho
        fputcsv($output, [
            'Família/Grupo',
            'Custo Estoque (Total)',
            'Qtde Estoque (Total)',
            'Total SKUs',
            'Total Estoque (Em %)',
            'Valor Estoque (Em %)'
        ], ';');
        
        // Dados
        if ($dados) {
            foreach ($dados as $item) {
                fputcsv($output, [
                    $item['ds_categoria'],
                    'R$ ' . number_format($item['custo_total'], 2, ',', '.'),
                    number_format($item['qtde_total'], 0, ',', '.'),
                    number_format($item['total_skus'], 0, ',', '.'),
                    number_format($item['perc_qtde'], 2, ',', '.') . '%',
                    number_format($item['perc_valor'], 2, ',', '.') . '%'
                ], ';');
            }
            
            // Total
            fputcsv($output, [
                'TOTAL GERAL',
                'R$ ' . number_format($total_custo, 2, ',', '.'),
                number_format($total_qtde, 0, ',', '.'),
                number_format($total_skus, 0, ',', '.'),
                '100,00%',
                '100,00%'
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Formata data de dd/mm/yyyy para yyyy-mm-dd
     */
    private function formatarDataBd($data) {
        if (empty($data)) return date('Y-m-d');
        
        $partes = explode('/', $data);
        if (count($partes) === 3) {
            return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
        }
        
        return date('Y-m-d');
    }
}
