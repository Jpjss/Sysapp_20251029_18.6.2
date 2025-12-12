<?php
// Verificar se é para exportar para Excel
if ($tipo_arquivo == 'EXCEL') {
    App::import('Component', 'Funcionalidades');
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("SysApp")
        ->setLastModifiedBy("SysApp")
        ->setTitle("Relatório de Estoque Detalhado")
        ->setDescription("Relatório de Estoque Detalhado por Família/Grupo");

    // Configurar larguras das colunas
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);

    // Título
    $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Relatório de Estoque Detalhado - ' . ucfirst(strtolower($tipo_agrupamento)));
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Data de emissão
    $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Emissão: ' . date('d/m/Y H:i:s') . ' | Data de Referência: ' . $data_formatada);
    $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Cabeçalho
    $linha = 4;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $linha, 'Família/Grupo');
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $linha, 'Custo Estoque (Total)');
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $linha, 'Qtde Estoque (Total)');
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $linha, 'Total SKUs');
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $linha, 'Total Estoque (Em %)');
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $linha, 'Valor Estoque (Em %)');

    $objPHPExcel->getActiveSheet()->getStyle('A' . $linha . ':F' . $linha)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $linha . ':F' . $linha)->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setRGB('4682B4');
    $objPHPExcel->getActiveSheet()->getStyle('A' . $linha . ':F' . $linha)->getFont()->getColor()->setRGB('FFFFFF');

    // Dados
    $linha++;
    if ($dadosRelatorio && count($dadosRelatorio) > 0) {
        foreach ($dadosRelatorio as $item) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $linha, $item[0]['ds_categoria']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $linha, 'R$ ' . number_format($item[0]['custo_total'], 2, ',', '.'));
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $linha, number_format($item[0]['qtde_total'], 0, ',', '.'));
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $linha, number_format($item[0]['total_skus'], 0, ',', '.'));
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $linha, number_format($item[0]['perc_qtde'], 2, ',', '.') . '%');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $linha, number_format($item[0]['perc_valor'], 2, ',', '.') . '%');
            $linha++;
        }

        // Totais
        $linha++;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $linha, 'TOTAL GERAL');
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $linha, 'R$ ' . number_format($total_custo, 2, ',', '.'));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $linha, number_format($total_qtde, 0, ',', '.'));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $linha, number_format($total_skus, 0, ',', '.'));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $linha, '100,00%');
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $linha, '100,00%');

        $objPHPExcel->getActiveSheet()->getStyle('A' . $linha . ':F' . $linha)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $linha . ':F' . $linha)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D3D3D3');
    }

    // Bordas
    $objPHPExcel->getActiveSheet()->getStyle('A4:F' . $linha)->getBorders()->getAllBorders()
        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="estoque_detalhado_' . date('Y-m-d_His') . '.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}
?>

<!-- HTML Version -->
<?php if ($tipo_arquivo == 'HTML'): ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Estoque Detalhado</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        
        #cabecalhoTabela {
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }
        
        #cabecalhoTabela td {
            padding: 10px;
        }
        
        .titulo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .subtitulo {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .contentRelatorio {
            margin-top: 20px;
        }
        
        #tabelaRelatorio {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        #tabelaRelatorio th {
            background-color: #4682B4;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        #tabelaRelatorio td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        #tabelaRelatorio tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        #tabelaRelatorio tr:hover {
            background-color: #f0f8ff;
        }
        
        .categoria-principal {
            font-weight: bold;
            background-color: #e6f2ff !important;
        }
        
        .texto-direita {
            text-align: right;
        }
        
        .texto-centro {
            text-align: center;
        }
        
        .total-geral {
            background-color: #d3d3d3 !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .valor-monetario {
            color: #2e7d32;
            font-weight: 500;
        }
        
        .sem-dados {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
        
        .botoes {
            margin-top: 20px;
            text-align: center;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: #4682B4;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #36648B;
        }
        
        @media print {
            .botoes {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div id="pai">
        <!-- Cabeçalho -->
        <table id="cabecalhoTabela">
            <tr>
                <td class="titulo">
                    Relatório de Estoque Detalhado por <?php echo ucfirst(strtolower($tipo_agrupamento)); ?>
                </td>
            </tr>
            <tr>
                <td class="subtitulo">
                    <strong>Emissão:</strong> <?php echo date('d/m/Y H:i:s'); ?><br>
                    <strong>Data de Referência:</strong> <?php echo $data_formatada; ?>
                </td>
            </tr>
        </table>

        <!-- Conteúdo do Relatório -->
        <div class="contentRelatorio">
            <?php if ($dadosRelatorio && count($dadosRelatorio) > 0): ?>
                <table id="tabelaRelatorio">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Família/Grupo</th>
                            <th style="width: 15%;" class="texto-direita">Custo Estoque (Total)</th>
                            <th style="width: 13%;" class="texto-direita">Qtde Estoque (Total)</th>
                            <th style="width: 10%;" class="texto-centro">Total SKUs</th>
                            <th style="width: 13%;" class="texto-direita">Total Estoque (Em %)</th>
                            <th style="width: 14%;" class="texto-direita">Valor Estoque (Em %)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dadosRelatorio as $item): ?>
                            <tr class="categoria-principal">
                                <td><?php echo $item[0]['ds_categoria']; ?></td>
                                <td class="texto-direita valor-monetario">
                                    R$ <?php echo number_format($item[0]['custo_total'], 2, ',', '.'); ?>
                                </td>
                                <td class="texto-direita">
                                    <?php echo number_format($item[0]['qtde_total'], 0, ',', '.'); ?>
                                </td>
                                <td class="texto-centro">
                                    <?php echo number_format($item[0]['total_skus'], 0, ',', '.'); ?>
                                </td>
                                <td class="texto-direita">
                                    <?php echo number_format($item[0]['perc_qtde'], 2, ',', '.'); ?>%
                                </td>
                                <td class="texto-direita">
                                    <?php echo number_format($item[0]['perc_valor'], 2, ',', '.'); ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Linha de Total -->
                        <tr class="total-geral">
                            <td><strong>TOTAL GERAL</strong></td>
                            <td class="texto-direita valor-monetario">
                                R$ <?php echo number_format($total_custo, 2, ',', '.'); ?>
                            </td>
                            <td class="texto-direita">
                                <?php echo number_format($total_qtde, 0, ',', '.'); ?>
                            </td>
                            <td class="texto-centro">
                                <?php echo number_format($total_skus, 0, ',', '.'); ?>
                            </td>
                            <td class="texto-direita">100,00%</td>
                            <td class="texto-direita">100,00%</td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="sem-dados">
                    <p>📦 Nenhum dado encontrado para os filtros selecionados.</p>
                    <p>Tente ajustar os filtros e gerar o relatório novamente.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Botões -->
        <div class="botoes">
            <button onclick="window.print();" class="btn btn-primary">
                🖨️ Imprimir
            </button>
            <button onclick="window.close();" class="btn btn-primary">
                ✖️ Fechar
            </button>
            <button onclick="window.history.back();" class="btn btn-primary">
                ⬅️ Voltar
            </button>
        </div>
    </div>
</body>
</html>
<?php endif; ?>
