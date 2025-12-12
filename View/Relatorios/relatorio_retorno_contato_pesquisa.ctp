<?php

if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório Retorno de Contato / Pesquisa")
            ->setDescription("Respostas por Pesquisa");
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório Retorno de Contato / Pesquisa');
    $hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));
    if (date("I") == 1) {
        $hora = $hora;
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A3:C3');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão: ' . date("d/m/Y") . ' às ' . $hora);

    if ($per_ini_pesquisas == null) {
        $per_ini_pesquisas = date("d/m/Y");
    }
    if ($per_fim_pesquisas == null) {
        $per_fim_pesquisas = date("d/m/Y");
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Período de Envio: ' . $per_ini_envio . ' á ' . $per_fim_envio);

    $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Período de Retorno: ' . $per_ini_retorno . ' á ' . $per_fim_retorno);


    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A6:F6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:F6')->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle('A6:F6')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Código');
    $objPHPExcel->getActiveSheet()->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cliente');
    $objPHPExcel->getActiveSheet()->getStyle("C6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'PL');
    $objPHPExcel->getActiveSheet()->getStyle("D6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', 'Pedido');
    $objPHPExcel->getActiveSheet()->getStyle("E6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E6', 'Valor Pedido');
    $objPHPExcel->getActiveSheet()->getStyle("F6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F6', 'Data da Compra');

    $data_atual = '';
    $descricao_atual = '';
    $totalAll = 0;

    $i = 7;

    foreach ($dadosRelatorio as $value) {
        if ($value[0]['nm_pessoa'] == 0) {
            $totalAll = $totalAll + 1;
        }
        if ($descricao_atual != $value[0]['ds_questionario'] || $data_atual != $value[0]['data_ligacao']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:F$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['ds_questionario'] . '  -  Data de Atendimento: ' . $value[0]['data_ligacao']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $data_atual = $value[0]['data_ligacao'];
            $descricao_atual = $value[0]['ds_questionario'];
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("E$i")->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->getStyle("E$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle("F$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value[0]['nm_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['nr_pl']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value[0]['cd_ped']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$i", $value[0]['valor_vendido']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("F$i", $value[0]['data_compra']);
        $i++;
    }
    $i++;
    $x = $i;
    $i = $i + 2;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode();
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Geral:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $totalAll);



    $objPHPExcel->getActiveSheet()->setTitle('RESPOSTAS POR PESQUISA');
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_respostas_por_pesquisa' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else {
    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);

    $tcpdf->xheadertext = 'Relatório Retorno de Contato / Pesquisa';
    $tcpdf->xfootertext = 'Systec Web+';
    $hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    if ($per_ini_envio == null) {
        $per_ini_envio = '01/01/1990';
    }
    if ($per_fim_envio == null) {
        $per_fim_envio = date("d/m/Y");
    }

    if ($per_ini_retorno == null) {
        $per_ini_retorno = '01/01/1990';
    }
    if ($per_fim_retorno == null) {
        $per_fim_retorno = date("d/m/Y");
    }
    $envio = 'De ' . $per_ini_envio . ' a ' . $per_fim_envio;
    $retorno = 'De ' . $per_ini_retorno . ' a ' . $per_fim_retorno;

    /*
     * Y deve ficar em cima de X
     * HORIZONTAL = X
     * VERTICAL = Y
     */
    $tcpdf->AddPage();

    $tcpdf->SetX(165);
    $tcpdf->SetTextColor(0, 0, 0);
    $tcpdf->Cell(40, 15, "", 1, 1, 'R');

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(10);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(10);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(15);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Período:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(15);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(20);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(20);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(05);
    $tcpdf->Cell(15, 5, "Código", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(20);
    $tcpdf->Cell(90, 5, "Cliente", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(110);
    $tcpdf->Cell(20, 5, "PL", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(130);
    $tcpdf->Cell(20, 5, "Pedido", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(150);
    $tcpdf->Cell(25, 5, "Valor Pedido", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(175);
    $tcpdf->Cell(30, 5, "Data da Compra", 0, 0, 'L', 1);

    $descricao_atual = '';
    $data_atual = '';
    $totalizador_descricao = '';
    $totalizador_data = '';
    $totalAllAniversariantes = '';

    $linhaDados = $linhaCabecalho + 5;
    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    foreach ($dadosRelatorio as $value) {
        @$totalizador_descricao[$value[0]['ds_questionario']] += 1;
        @$totalizador_data[$value[0]['data_ligacao']] += 1;
        if ($value[0]['nm_pessoa'] == 0) {
            $totalAllAniversariantes = $totalAllAniversariantes + 1;
        }
        if ($descricao_atual != $value[0]['ds_questionario'] || $data_atual != $value[0]['data_ligacao']) {
            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(05);
            $tcpdf->Cell(200, 5, $value[0]['ds_questionario'] . '  -  Data de Atendimento: ' . $value[0]['data_ligacao'], 0, 0, 'C', 1);
            $linhaDados = $linhaDados + 5;
            $descricao_atual = $value[0]['ds_questionario'];
            $data_atual = $value[0]['data_ligacao'];
        }

        $descricao_atual = $value[0]['ds_questionario'];
        $data_atual = $value[0]['data_ligacao'];
        if ($tcpdf->GetY() >= 270) {
            $tcpdf->AddPage();
            $tcpdf->SetX(165);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->Cell(40, 15, "", 1, 1, 'R');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

            $linhaCabecalho = 45;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(05);
            $tcpdf->Cell(15, 5, "Código", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(20);
            $tcpdf->Cell(90, 5, "Cliente", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(110);
            $tcpdf->Cell(20, 5, "PL", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(130);
            $tcpdf->Cell(20, 5, "Pedido", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(150);
            $tcpdf->Cell(25, 5, "Valor Pedido", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(175);
            $tcpdf->Cell(30, 5, "Data da Compra", 0, 0, 'L', 1);

            $linhaDados = 50;
        }
        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(05);
        $tcpdf->Cell(15, 5, $value[0]['cd_pessoa'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(20);
        $tcpdf->Cell(90, 5, $value[0]['nm_pessoa'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(110);
        $tcpdf->Cell(20, 5, $value[0]['nr_pl'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(130);
        $tcpdf->Cell(10, 5, $value[0]['cd_ped'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(140);
        $tcpdf->Cell(25, 5, number_format($value[0]['valor_vendido'], 2, ',', '.'), 0, 0, 'R');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(175);
        $tcpdf->Cell(30, 5, $value[0]['data_compra'], 0, 0, 'L');
        $linhaDados = $linhaDados + 5;
    }
    $linhaDados = $linhaDados + 10;
    foreach ($totalizador_descricao as $key => $value) {
        if ($tcpdf->GetY() > 265) {
            $tcpdf->AddPage();
            $tcpdf->SetX(165);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->Cell(40, 15, "", 1, 1, 'R');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

            $linhaDados = 20;
        }
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Atendidos: " . $key . ":  " . $value, 0, 0, 'L');

        $linhaDados = $linhaDados + 5;
    }
    foreach ($totalizador_data as $key => $value) {
        if ($tcpdf->GetY() > 265) {
            $tcpdf->AddPage();
            $tcpdf->SetX(165);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->Cell(40, 15, "", 1, 1, 'R');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

            $linhaDados = 20;
        }
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Data: " . $key . ":  " . $value, 0, 0, 'L');

        $linhaDados = $linhaDados + 5;
    }
    if ($tcpdf->GetY() <= 240) {
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Geral:  " . $totalAllAniversariantes, 0, 0, 'L');
    } else {
        $tcpdf->AddPage();
        $tcpdf->SetX(165);
        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->Cell(40, 15, "", 1, 1, 'R');

        $tcpdf->SetFont($textfont, 'B', 5);
        $tcpdf->SetY(10);
        $tcpdf->SetX(165);
        $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 5);
        $tcpdf->SetY(10);
        $tcpdf->SetX(180);
        $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 5);
        $tcpdf->SetY(15);
        $tcpdf->SetX(165);
        $tcpdf->Cell(30, 5, "Período:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 5);
        $tcpdf->SetY(15);
        $tcpdf->SetX(180);
        $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 5);
        $tcpdf->SetY(20);
        $tcpdf->SetX(165);
        $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 5);
        $tcpdf->SetY(20);
        $tcpdf->SetX(180);
        $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(30);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Geral:  " . $totalAllAniversariantes, 0, 0, 'L');
    }
    ob_end_clean();
    $data_relatorio = date("d_m_Y");
    $tcpdf->Output('relatorio_retorno_contato_pesquisa' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
