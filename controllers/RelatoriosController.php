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
     * Listagem de relatórios disponíveis
     */
    public function lista() {
        $this->requireAuth();
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            $this->redirect('relatorios/empresa');
            return;
        }
        
        $this->render();
    }

    /**
     * Dashboard principal
     */
    public function index() {
        $this->requireAuth();
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            $this->redirect('relatorios/empresa');
            return;
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
                return;
            }
        }
        
        $this->set(['empresas' => $empresas]);
        $this->render();
    }
    
    /**
     * Relatório de Atendimentos (versão completa)
     */
    public function atendimentos() {
        $this->requireAuth();
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            $this->redirect('relatorios/empresa');
            return;
        }
        
        $dt_inicio = $_GET['dt_inicio'] ?? date('Y-m-01');
        $dt_fim = $_GET['dt_fim'] ?? date('Y-m-d');
        
        // Buscar dados do relatório
        $atendimentos = $this->Relatorio->getAtendimentosDetalhados($dt_inicio, $dt_fim);
        $totais = $this->Relatorio->getTotaisAtendimentos($dt_inicio, $dt_fim);
        
        $this->set([
            'atendimentos' => $atendimentos,
            'totais' => $totais,
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
     * Exportar Relatório de Atendimentos em PDF
     */
    public function exportarPDF() {
        $this->requireAuth();
        $this->layout = false;
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            die('Erro: Nenhuma empresa selecionada');
        }
        
        $dt_inicio = $_GET['dt_inicio'] ?? date('Y-m-01');
        $dt_fim = $_GET['dt_fim'] ?? date('Y-m-d');
        
        // Buscar dados
        $atendimentos = $this->Relatorio->getAtendimentosDetalhados($dt_inicio, $dt_fim);
        $totais = $this->Relatorio->getTotaisAtendimentos($dt_inicio, $dt_fim);
        $empresa = Session::read('Config.empresa');
        
        // Verificar se tem biblioteca TCPDF
        if (!class_exists('TCPDF')) {
            // Implementação simples com HTML/CSS para impressão
            $this->gerarPDFSimples($atendimentos, $totais, $dt_inicio, $dt_fim, $empresa);
            return;
        }
        
        // Gerar PDF com TCPDF (implementar quando biblioteca estiver disponível)
        $this->gerarPDFComTCPDF($atendimentos, $totais, $dt_inicio, $dt_fim, $empresa);
    }
    
    /**
     * Exportar Relatório de Atendimentos em Excel
     */
    public function exportarExcel() {
        $this->requireAuth();
        $this->layout = false;
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            die('Erro: Nenhuma empresa selecionada');
        }
        
        $dt_inicio = $_GET['dt_inicio'] ?? date('Y-m-01');
        $dt_fim = $_GET['dt_fim'] ?? date('Y-m-d');
        
        // Buscar dados
        $atendimentos = $this->Relatorio->getAtendimentosDetalhados($dt_inicio, $dt_fim);
        $totais = $this->Relatorio->getTotaisAtendimentos($dt_inicio, $dt_fim);
        $empresa = Session::read('Config.empresa');
        
        // Verificar se tem PhpSpreadsheet
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            // Exportar como CSV melhorado
            $this->exportarCSV($atendimentos, $totais, $dt_inicio, $dt_fim);
            return;
        }
        
        // Gerar Excel com PhpSpreadsheet
        $this->gerarExcelComPhpSpreadsheet($atendimentos, $totais, $dt_inicio, $dt_fim, $empresa);
    }
    
    /**
     * Gerar PDF simples usando HTML/CSS para impressão
     */
    private function gerarPDFSimples($atendimentos, $totais, $dt_inicio, $dt_fim, $empresa) {
        header('Content-Type: text/html; charset=utf-8');
        
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Atendimentos</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
        }
        .header .empresa {
            color: #64748b;
            font-size: 14px;
        }
        .periodo {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table thead {
            background: #667eea;
            color: white;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        table tfoot {
            background: #f1f5f9;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
        .btn-print {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Salvar PDF</button>
    </div>
    
    <div class="header">
        <h1>Relatório de Atendimentos</h1>
        <div class="empresa">' . htmlspecialchars($empresa) . '</div>
    </div>
    
    <div class="periodo">
        <strong>Período:</strong> ' . date('d/m/Y', strtotime($dt_inicio)) . ' até ' . date('d/m/Y', strtotime($dt_fim)) . '<br>
        <strong>Emitido em:</strong> ' . date('d/m/Y H:i:s') . '
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th class="text-right">Total de Atendimentos</th>
                <th class="text-right">Clientes Únicos</th>
                <th class="text-right">Tempo Total</th>
                <th class="text-right">Valor Total (R$)</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($atendimentos as $item) {
            echo '<tr>
                <td>' . date('d/m/Y', strtotime($item['data'])) . '</td>
                <td class="text-right">' . number_format($item['total_atendimentos'], 0, ',', '.') . '</td>
                <td class="text-right">' . number_format($item['clientes_unicos'], 0, ',', '.') . '</td>
                <td class="text-right">' . $item['tempo_total_formatado'] . '</td>
                <td class="text-right">R$ ' . number_format($item['valor_total'], 2, ',', '.') . '</td>
            </tr>';
        }
        
        echo '</tbody>
        <tfoot>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>' . number_format($totais['total_atendimentos'], 0, ',', '.') . '</strong></td>
                <td class="text-right"><strong>' . number_format($totais['clientes_unicos'], 0, ',', '.') . '</strong></td>
                <td class="text-right"><strong>' . $totais['tempo_total_formatado'] . '</strong></td>
                <td class="text-right"><strong>R$ ' . number_format($totais['valor_total'], 2, ',', '.') . '</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        SysApp - Sistema de Gestão | Relatório gerado automaticamente
    </div>
</body>
</html>';
        exit;
    }
    
    /**
     * Exportar como CSV melhorado
     */
    private function exportarCSV($atendimentos, $totais, $dt_inicio, $dt_fim) {
        $filename = 'relatorio_atendimentos_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho do relatório
        fputcsv($output, ['RELATÓRIO DE ATENDIMENTOS'], ';');
        fputcsv($output, ['Período:', date('d/m/Y', strtotime($dt_inicio)) . ' até ' . date('d/m/Y', strtotime($dt_fim))], ';');
        fputcsv($output, ['Emitido em:', date('d/m/Y H:i:s')], ';');
        fputcsv($output, [''], ';'); // Linha em branco
        
        // Cabeçalho da tabela
        fputcsv($output, [
            'Data',
            'Total de Atendimentos',
            'Clientes Únicos',
            'Tempo Total',
            'Valor Total (R$)'
        ], ';');
        
        // Dados
        foreach ($atendimentos as $item) {
            fputcsv($output, [
                date('d/m/Y', strtotime($item['data'])),
                $item['total_atendimentos'],
                $item['clientes_unicos'],
                $item['tempo_total_formatado'],
                number_format($item['valor_total'], 2, ',', '.')
            ], ';');
        }
        
        // Total
        fputcsv($output, [
            'TOTAL',
            $totais['total_atendimentos'],
            $totais['clientes_unicos'],
            $totais['tempo_total_formatado'],
            number_format($totais['valor_total'], 2, ',', '.')
        ], ';');
        
        fclose($output);
        exit;
    }
    
    /**
     * Relatório Entrada x Vendas
     */
    public function entrada_vendas() {
        $this->requireAuth();
        
        // Verifica se tem empresa configurada
        if (!Session::check('Config.database')) {
            $this->redirect('relatorios/empresa');
            return;
        }
        
        // Busca lista de filiais
        $filiais = $this->Relatorio->getFiliais();
        
        $dados = null;
        $totais = null;
        $periodoInfo = null;
        
        // Se foi submetido o formulário
        if ($this->isPost() && isset($_POST['submit']) && $_POST['submit'] === 'visualizar') {
            // Coleta filtros
            $filtros = [
                'venda_dt_inicio' => $_POST['venda_dt_inicio'] ?? date('Y-m-01'),
                'venda_dt_fim' => $_POST['venda_dt_fim'] ?? date('Y-m-d'),
                'entrada_dt_inicio' => $_POST['entrada_dt_inicio'] ?? date('Y-m-01'),
                'entrada_dt_fim' => $_POST['entrada_dt_fim'] ?? date('Y-m-d'),
                'filiais' => $_POST['filiais'] ?? ['todas'],
                'est_positivo' => isset($_POST['est_positivo']),
                'est_zerado' => isset($_POST['est_zerado']),
                'est_negativo' => isset($_POST['est_negativo'])
            ];
            
            // Se marcou "Todas as filiais"
            if (isset($_POST['todas_filiais'])) {
                $filtros['filiais'] = ['todas'];
            }
            
            // Busca dados
            $resultado = $this->Relatorio->getEntradaVendas($filtros);
            $dados = $resultado['dados'];
            $totais = $resultado['totais'];
            
            // Informações do período para exibir no cabeçalho
            $periodoInfo = [
                'vendas' => date('d/m/Y', strtotime($filtros['venda_dt_inicio'])) . ' a ' . date('d/m/Y', strtotime($filtros['venda_dt_fim'])),
                'entradas' => date('d/m/Y', strtotime($filtros['entrada_dt_inicio'])) . ' a ' . date('d/m/Y', strtotime($filtros['entrada_dt_fim']))
            ];
        }
        
        $this->set([
            'filiais' => $filiais,
            'dados' => $dados,
            'totais' => $totais,
            'periodoInfo' => $periodoInfo
        ]);
        
        $this->render();
    }
    
    /**
     * Exportar Entrada x Vendas para PDF
     */
    public function exportarEntradaVendasPDF() {
        $this->requireAuth();
        $this->layout = false;
        
        // Coleta filtros da URL
        $filtros = [
            'venda_dt_inicio' => $_GET['venda_dt_inicio'] ?? date('Y-m-01'),
            'venda_dt_fim' => $_GET['venda_dt_fim'] ?? date('Y-m-d'),
            'entrada_dt_inicio' => $_GET['entrada_dt_inicio'] ?? date('Y-m-01'),
            'entrada_dt_fim' => $_GET['entrada_dt_fim'] ?? date('Y-m-d'),
            'filiais' => isset($_GET['filiais']) ? (is_array($_GET['filiais']) ? $_GET['filiais'] : [$_GET['filiais']]) : ['todas'],
            'est_positivo' => isset($_GET['est_positivo']),
            'est_zerado' => isset($_GET['est_zerado']),
            'est_negativo' => isset($_GET['est_negativo'])
        ];
        
        // Busca dados
        $resultado = $this->Relatorio->getEntradaVendas($filtros);
        $dados = $resultado['dados'];
        $totais = $resultado['totais'];
        
        // Informações do período
        $periodoInfo = [
            'vendas' => date('d/m/Y', strtotime($filtros['venda_dt_inicio'])) . ' a ' . date('d/m/Y', strtotime($filtros['venda_dt_fim'])),
            'entradas' => date('d/m/Y', strtotime($filtros['entrada_dt_inicio'])) . ' a ' . date('d/m/Y', strtotime($filtros['entrada_dt_fim']))
        ];
        
        // Tenta usar TCPDF, senão usa HTML otimizado para impressão
        if (class_exists('TCPDF')) {
            $this->gerarPDFEntradaVendasTCPDF($dados, $totais, $periodoInfo);
        } else {
            $this->gerarPDFEntradaVendasSimples($dados, $totais, $periodoInfo);
        }
    }
    
    /**
     * Gera PDF simples usando HTML (para impressão)
     */
    private function gerarPDFEntradaVendasSimples($dados, $totais, $periodoInfo) {
        header('Content-Type: text/html; charset=utf-8');
        
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório Entrada x Vendas</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        h1 { text-align: center; color: #333; font-size: 16pt; margin-bottom: 5px; }
        .info { text-align: center; color: #666; font-size: 8pt; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        th { background: #667eea; color: white; padding: 6px 4px; text-align: left; font-weight: bold; }
        td { padding: 5px 4px; border-bottom: 1px solid #ddd; }
        th.right, td.right { text-align: right; }
        .filial-header { background: #f1f5f9; font-weight: bold; }
        .subtotal { background: #f8fafc; font-weight: bold; }
        tfoot td { background: #f8fafc; font-weight: bold; border-top: 2px solid #333; padding: 8px 4px; }
        .negative { color: #ef4444; }
        .positive { color: #10b981; }
    </style>
</head>
<body>';
        
        echo '<h1>Relatório Entrada x Vendas</h1>';
        echo '<div class="info">';
        echo 'Período Vendas: ' . $periodoInfo['vendas'] . ' | ';
        echo 'Período Entradas: ' . $periodoInfo['entradas'] . '<br>';
        echo 'Emitido em: ' . date('d/m/Y H:i:s');
        echo '</div>';
        
        echo '<table>
            <thead>
                <tr>
                    <th>Marca</th>
                    <th class="right">Est. Atual</th>
                    <th class="right">Qtde Entr.</th>
                    <th class="right">%Qtde Est.</th>
                    <th class="right">Qtde Vend.</th>
                    <th class="right">%Qtde Vend.</th>
                    <th class="right">Val. Est. (R$)</th>
                    <th class="right">%Val. Est.</th>
                    <th class="right">Val. Vend. (R$)</th>
                    <th class="right">%Val. Vend.</th>
                    <th class="right">Rel. E/R$</th>
                    <th class="right">Rel. E/Qtde</th>
                    <th class="right">P. Custo</th>
                    <th class="right">P. Venda</th>
                    <th class="right">Margem %</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($dados as $filial => $marcas) {
            echo '<tr class="filial-header"><td colspan="15"><strong>Filial: ' . htmlspecialchars($filial) . '</strong></td></tr>';
            
            foreach ($marcas['itens'] as $marca) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($marca['marca']) . '</td>';
                echo '<td class="right">' . number_format($marca['estoque_atual'], 0, ',', '.') . '</td>';
                echo '<td class="right">' . number_format($marca['qtde_entradas'], 0, ',', '.') . '</td>';
                echo '<td class="right">' . number_format($marca['perc_qtde_estoque'], 2, ',', '.') . '%</td>';
                echo '<td class="right">' . number_format($marca['qtde_vendida'], 0, ',', '.') . '</td>';
                echo '<td class="right">' . number_format($marca['perc_qtde_venda'], 2, ',', '.') . '%</td>';
                echo '<td class="right">R$ ' . number_format($marca['valor_estoque'], 2, ',', '.') . '</td>';
                echo '<td class="right">' . number_format($marca['perc_valor_estoque'], 2, ',', '.') . '%</td>';
                echo '<td class="right">R$ ' . number_format($marca['valor_vendido'], 2, ',', '.') . '</td>';
                echo '<td class="right">' . number_format($marca['perc_valor_venda'], 2, ',', '.') . '%</td>';
                echo '<td class="right">' . number_format($marca['rel_estoque_valor'], 2, ',', '.') . '</td>';
                echo '<td class="right">' . number_format($marca['rel_estoque_qtde'], 2, ',', '.') . '</td>';
                echo '<td class="right">R$ ' . number_format($marca['preco_custo'], 2, ',', '.') . '</td>';
                echo '<td class="right">R$ ' . number_format($marca['preco_venda'], 2, ',', '.') . '</td>';
                echo '<td class="right ' . ($marca['margem'] < 0 ? 'negative' : 'positive') . '">' . number_format($marca['margem'], 2, ',', '.') . '%</td>';
                echo '</tr>';
            }
            
            echo '<tr class="subtotal">';
            echo '<td><strong>Subtotal ' . htmlspecialchars($filial) . '</strong></td>';
            echo '<td class="right"><strong>' . number_format($marcas['subtotal']['estoque_atual'], 0, ',', '.') . '</strong></td>';
            echo '<td class="right"><strong>' . number_format($marcas['subtotal']['qtde_entradas'], 0, ',', '.') . '</strong></td>';
            echo '<td class="right"><strong>100,00%</strong></td>';
            echo '<td class="right"><strong>' . number_format($marcas['subtotal']['qtde_vendida'], 0, ',', '.') . '</strong></td>';
            echo '<td class="right"><strong>100,00%</strong></td>';
            echo '<td class="right"><strong>R$ ' . number_format($marcas['subtotal']['valor_estoque'], 2, ',', '.') . '</strong></td>';
            echo '<td class="right"><strong>100,00%</strong></td>';
            echo '<td class="right"><strong>R$ ' . number_format($marcas['subtotal']['valor_vendido'], 2, ',', '.') . '</strong></td>';
            echo '<td class="right"><strong>100,00%</strong></td>';
            echo '<td class="right"><strong>-</strong></td>';
            echo '<td class="right"><strong>-</strong></td>';
            echo '<td class="right"><strong>-</strong></td>';
            echo '<td class="right"><strong>-</strong></td>';
            echo '<td class="right"><strong>' . number_format($marcas['subtotal']['margem'], 2, ',', '.') . '%</strong></td>';
            echo '</tr>';
        }
        
        echo '</tbody>
            <tfoot>
                <tr>
                    <td><strong>TOTAL GERAL</strong></td>
                    <td class="right"><strong>' . number_format($totais['estoque_atual'], 0, ',', '.') . '</strong></td>
                    <td class="right"><strong>' . number_format($totais['qtde_entradas'], 0, ',', '.') . '</strong></td>
                    <td class="right"><strong>100,00%</strong></td>
                    <td class="right"><strong>' . number_format($totais['qtde_vendida'], 0, ',', '.') . '</strong></td>
                    <td class="right"><strong>100,00%</strong></td>
                    <td class="right"><strong>R$ ' . number_format($totais['valor_estoque'], 2, ',', '.') . '</strong></td>
                    <td class="right"><strong>100,00%</strong></td>
                    <td class="right"><strong>R$ ' . number_format($totais['valor_vendido'], 2, ',', '.') . '</strong></td>
                    <td class="right"><strong>100,00%</strong></td>
                    <td class="right"><strong>-</strong></td>
                    <td class="right"><strong>-</strong></td>
                    <td class="right"><strong>-</strong></td>
                    <td class="right"><strong>-</strong></td>
                    <td class="right"><strong>' . number_format($totais['margem'], 2, ',', '.') . '%</strong></td>
                </tr>
            </tfoot>
        </table>';
        
        echo '<script>window.print();</script>';
        echo '</body></html>';
        exit;
    }
    
    /**
     * Exportar Entrada x Vendas para Excel (CSV)
     */
    public function exportarEntradaVendasExcel() {
        $this->requireAuth();
        $this->layout = false;
        
        // Coleta filtros da URL
        $filtros = [
            'venda_dt_inicio' => $_GET['venda_dt_inicio'] ?? date('Y-m-01'),
            'venda_dt_fim' => $_GET['venda_dt_fim'] ?? date('Y-m-d'),
            'entrada_dt_inicio' => $_GET['entrada_dt_inicio'] ?? date('Y-m-01'),
            'entrada_dt_fim' => $_GET['entrada_dt_fim'] ?? date('Y-m-d'),
            'filiais' => isset($_GET['filiais']) ? (is_array($_GET['filiais']) ? $_GET['filiais'] : [$_GET['filiais']]) : ['todas'],
            'est_positivo' => isset($_GET['est_positivo']),
            'est_zerado' => isset($_GET['est_zerado']),
            'est_negativo' => isset($_GET['est_negativo'])
        ];
        
        // Busca dados
        $resultado = $this->Relatorio->getEntradaVendas($filtros);
        $this->exportarEntradaVendasCSV($resultado['dados'], $resultado['totais'], $filtros);
    }
    
    /**
     * Gera arquivo CSV do relatório Entrada x Vendas
     */
    private function exportarEntradaVendasCSV($dados, $totais, $filtros) {
        // Headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="entrada_vendas_' . date('Ymd_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho do relatório
        fputcsv($output, ['Relatório Entrada x Vendas'], ';');
        fputcsv($output, [''], ';'); // Linha em branco
        fputcsv($output, ['Período Vendas:', date('d/m/Y', strtotime($filtros['venda_dt_inicio'])) . ' a ' . date('d/m/Y', strtotime($filtros['venda_dt_fim']))], ';');
        fputcsv($output, ['Período Entradas:', date('d/m/Y', strtotime($filtros['entrada_dt_inicio'])) . ' a ' . date('d/m/Y', strtotime($filtros['entrada_dt_fim']))], ';');
        fputcsv($output, ['Emitido em:', date('d/m/Y H:i:s')], ';');
        fputcsv($output, [''], ';'); // Linha em branco
        
        // Cabeçalho da tabela
        fputcsv($output, [
            'Filial',
            'Marca',
            'Estoque Atual',
            'Qtde Entradas',
            '% Qtde Estoque',
            'Qtde Vendida',
            '% Qtde Venda',
            'Valor Estoque (R$)',
            '% Valor Estoque',
            'Valor Vendido (R$)',
            '% Valor Venda',
            'Relação Est./R$',
            'Relação Est./Qtde',
            'Preço de Custo',
            'Preço de Venda',
            'Margem (%)'
        ], ';');
        
        // Dados
        foreach ($dados as $filial => $marcas) {
            foreach ($marcas['itens'] as $marca) {
                fputcsv($output, [
                    $filial,
                    $marca['marca'],
                    $marca['estoque_atual'],
                    $marca['qtde_entradas'],
                    number_format($marca['perc_qtde_estoque'], 2, ',', '.'),
                    $marca['qtde_vendida'],
                    number_format($marca['perc_qtde_venda'], 2, ',', '.'),
                    number_format($marca['valor_estoque'], 2, ',', '.'),
                    number_format($marca['perc_valor_estoque'], 2, ',', '.'),
                    number_format($marca['valor_vendido'], 2, ',', '.'),
                    number_format($marca['perc_valor_venda'], 2, ',', '.'),
                    number_format($marca['rel_estoque_valor'], 2, ',', '.'),
                    number_format($marca['rel_estoque_qtde'], 2, ',', '.'),
                    number_format($marca['preco_custo'], 2, ',', '.'),
                    number_format($marca['preco_venda'], 2, ',', '.'),
                    number_format($marca['margem'], 2, ',', '.')
                ], ';');
            }
            
            // Subtotal da filial
            fputcsv($output, [
                'SUBTOTAL ' . $filial,
                '',
                $marcas['subtotal']['estoque_atual'],
                $marcas['subtotal']['qtde_entradas'],
                '100,00',
                $marcas['subtotal']['qtde_vendida'],
                '100,00',
                number_format($marcas['subtotal']['valor_estoque'], 2, ',', '.'),
                '100,00',
                number_format($marcas['subtotal']['valor_vendido'], 2, ',', '.'),
                '100,00',
                '-',
                '-',
                '-',
                '-',
                number_format($marcas['subtotal']['margem'], 2, ',', '.')
            ], ';');
        }
        
        // Total geral
        fputcsv($output, [
            'TOTAL GERAL',
            '',
            $totais['estoque_atual'],
            $totais['qtde_entradas'],
            '100,00',
            $totais['qtde_vendida'],
            '100,00',
            number_format($totais['valor_estoque'], 2, ',', '.'),
            '100,00',
            number_format($totais['valor_vendido'], 2, ',', '.'),
            '100,00',
            '-',
            '-',
            '-',
            '-',
            number_format($totais['margem'], 2, ',', '.')
        ], ';');
        
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
