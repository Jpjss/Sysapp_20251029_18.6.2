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
     * Seleção de empresa (quando usuário tem múltiplas empresas)
     */
    public function empresa() {
        $this->requireAuth();
        
        $empresas = Session::read('Dados.database');
        
        if (empty($empresas)) {
            // Se não tem empresas na sessão, redireciona para o dashboard
            $this->redirect('relatorios/index');
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
                error_log("=== SELEÇÃO DE EMPRESA ===");
                error_log("Empresa: " . $empresaSelecionada['nome_empresa']);
                error_log("Banco: " . $empresaSelecionada['nome_banco']);
                error_log("Host: " . $empresaSelecionada['hostname_banco']);
                error_log("User: " . $empresaSelecionada['usuario_banco']);
                error_log("Porta: " . $empresaSelecionada['porta_banco']);
                
                // Configura empresa na sessão
                Session::write('Config.database', $empresaSelecionada['nome_banco']);
                Session::write('Config.databasename', $empresaSelecionada['nome_banco']);
                Session::write('Config.host', $empresaSelecionada['hostname_banco']);
                Session::write('Config.user', $empresaSelecionada['usuario_banco']);
                Session::write('Config.password', Security::decrypt($empresaSelecionada['senha_banco']));
                Session::write('Config.porta', $empresaSelecionada['porta_banco']);
                Session::write('Config.empresa', $empresaSelecionada['nome_empresa']);
                
                error_log("Configurações salvas na sessão");
                
                // Reconecta ao banco da empresa
                error_log("Reconectando ao banco da empresa...");
                $result = $this->db->connect(
                    $empresaSelecionada['hostname_banco'],
                    $empresaSelecionada['nome_banco'],
                    $empresaSelecionada['usuario_banco'],
                    Security::decrypt($empresaSelecionada['senha_banco']),
                    $empresaSelecionada['porta_banco']
                );
                
                error_log("Resultado da reconexão: " . ($result ? "SUCESSO" : "FALHA"));
                
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
}
