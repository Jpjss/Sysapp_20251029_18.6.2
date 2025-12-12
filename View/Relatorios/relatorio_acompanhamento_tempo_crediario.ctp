<?php
if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Acompanhamento de Tempo Crediário")
            ->setDescription("Acompanhamento de Tempo Crediário");
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório de Acompanhamento de Tempo Crediário');
    $hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));
    if (date("I") == 1) {
        $hora = $hora;
    }
    $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão: ' . date("d/m/Y") . ' às ' . $hora);

    if ($per_ini_pesquisas == null) {
        $per_ini_pesquisas = date("d/m/Y");
    }
    if ($per_fim_pesquisas == null) {
        $per_fim_pesquisas = date("d/m/Y");
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Período de Pesquisa: ' . $per_ini_pesquisas . ' á ' . $per_fim_pesquisas);
    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Código');
    $objPHPExcel->getActiveSheet()->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cliente');
    $objPHPExcel->getActiveSheet()->getStyle("C6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Tempo Espera');
    $objPHPExcel->getActiveSheet()->getStyle("D6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', 'Tempo Lançamento');
    $i = 7;
    $filial_atual = @$dadosRelatorio[0][0]['cd_filial'];
    $data_atual = @$dadosRelatorio[0][0]['data_pedido'];
    $totalEsperaData = @$dadosRelatorio[0][0]['tempo_espera'];
    $totalLancamentoData = @$dadosRelatorio[0][0]['tempo_lancamento'];
    $segundos_espera = 0;
    $totalAllSegundosEspera = 0;
    $segundos_lancamento = 0;
    $totalAllSegundosLancamento = 0;
    $totalAllAtendidos = 0;


    foreach ($dadosRelatorio as $value) {
        if ($value[0]['nm_pessoa'] == 0) {
            $totalAllAtendidos = $totalAllAtendidos + 1;
        }
        if ($data_atual != $value[0]['data_pedido'] || $filial_atual != $value[0]['cd_filial']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $returnDateType = PHPExcel_Calculation_Functions::getReturnDateType($totalEspera);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", ' Filial: ' . $filial_atual . '  -  Data: ' . $data_atual . ' -  Tempo Espera: ' . $hours_espera . ':' . $minutes_espera . ':' . $seconds_espera . ' -  Tempo Lançamento: ' . $hours_lancamento . ':' . $minutes_lancamento . ':' . $seconds_lancamento);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $totalEspera = 0;
            $totalLancamento = 0;
            $filial_atual = $value[0]['cd_filial'];
            $data_atual = $value[0]['data_pedido'];
            $totalEsperaData = $value[0]['tempo_espera'];
            $totalLancamentoData = $value[0]['tempo_lancamento'];
            $totalAllSegundosEspera = $totalAllSegundosEspera + $segundos_espera;
            $totalAllSegundosLancamento = $totalAllSegundosLancamento + $segundos_lancamento;
            $segundos_espera = 0;
            $segundos_lancamento = 0;
            $i++;
        }

        $totalEspera = $totalEspera + $value[0]['tempo_espera'];
        $totalLancamento = $totalLancamento + $value[0]['tempo_lancamento'];

        $segundos_espera = $segundos_espera + $value[0]['segundos_espera'];
        $seconds_espera = $segundos_espera;
        $hours_espera = floor($seconds_espera / 3600);
        $seconds_espera -= $hours_espera * 3600;
        $minutes_espera = floor($seconds_espera / 60);
        $seconds_espera -= $minutes_espera * 60;

        $segundos_lancamento = $segundos_lancamento + $value[0]['segundos_lancamento'];
        $seconds_lancamento = $segundos_lancamento;
        $hours_lancamento = floor($seconds_lancamento / 3600);
        $seconds_lancamento -= $hours_lancamento * 3600;
        $minutes_lancamento = floor($seconds_lancamento / 60);
        $seconds_lancamento -= $minutes_lancamento * 60;


        $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value[0]['nm_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['tempo_espera']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value[0]['tempo_lancamento']);

        $i++;
    }
    $data_atual != $value[0]['data_pedido'] || $filial_atual != $value[0]['cd_filial'];
    $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
    $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", ' Filial: ' . $filial_atual . '  -  Data: ' . $data_atual . ' -  Tempo Espera: ' . $hours_espera . ':' . $minutes_espera . ':' . $seconds_espera . ' -  Tempo Lançamento: ' . $hours_lancamento . ':' . $minutes_lancamento . ':' . $seconds_lancamento);
    $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->getStartColor()->setRGB('DBDBEA');
    $filial_atual = $value[0]['cd_filial'];
    $data_atual = $value[0]['data_pedido'];

    $totalAllSegundosEspera = $totalAllSegundosEspera + $segundos_espera;
    $seconds_espera = $totalAllSegundosEspera;
    $hours_espera = floor($seconds_espera / 3600);
    $seconds_espera -= $hours_espera * 3600;
    $minutes_espera = floor($seconds_espera / 60);
    $seconds_espera -= $minutes_espera * 60;

    $i++;
    $x = $i;
    $i = $i + 2;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode();
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Tempo Total de Espera:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $hours_espera . ':' . $minutes_espera . ':' . $seconds_espera);
    $i++;

    $totalAllSegundosLancamento = $totalAllSegundosLancamento + $segundos_lancamento;
    $seconds_lancamento = $totalAllSegundosLancamento;
    $hours_lancamento = floor($seconds_lancamento / 3600);
    $seconds_lancamento -= $hours_lancamento * 3600;
    $minutes_lancamento = floor($seconds_lancamento / 60);
    $seconds_lancamento -= $minutes_lancamento * 60;


    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode();
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Tempo Total de Lançamento:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $hours_lancamento . ':' . $minutes_lancamento . ':' . $seconds_lancamento);
    $i++;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode();
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total de Atendimentos:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $totalAllAtendidos);


    $objPHPExcel->getActiveSheet()->setTitle('ACOMPANHAMENTO TEMPO CREDIÁRIO');
    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_acompanhamento_tempo_crediario_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    $objPHPExcel->getActiveSheet()->setTitle('ACOMPANHAMENTO TEMPO CREDIÁRIO');
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_acompanhamento_tempo_crediario_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else if ($tipo_arquivo == 'PDF') {
    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);

    $tcpdf->xheadertext = 'Relatório de Acompanhamento de Tempo Crediário';
    $tcpdf->xfootertext = 'Systec Web+';
    $hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    if ($per_ini_pesquisas == null) {
        $per_ini_pesquisas = '01/01/1990';
    }
    if ($per_fim_pesquisas == null) {
        $per_fim_pesquisas = date("d/m/Y");
    }

    $envio = $per_ini_pesquisas . ' á ' . $per_fim_pesquisas;

    $tcpdf->AddPage();
    $tcpdf->SetX(165);
    $tcpdf->SetTextColor(0, 0, 0);
    $tcpdf->Cell(40, 15, "", 1, 1, 'R');

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(12);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(12);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(17);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Período: ", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(17);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

    /* $x = 60; */ $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(05);
    $tcpdf->Cell(15, 5, "Código", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(20);
    $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(90);
    $tcpdf->Cell(55, 5, "Tempo Espera", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(145);
    $tcpdf->Cell(60, 5, "Tempo Lançamento", 0, 0, 'C', 1);
    $linhaDados = $linhaCabecalho + 5;
    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    $filial_atual = ''/* @$dadosRelatorio[0][0]['cd_filial'] */;
    $data_atual = ''/* @$dadosRelatorio[0][0]['data_pedido'] */;
    $totalEsperaData = @$dadosRelatorio [0] [0]['tempo_espera'];
    $totalLancamentoData = @$dadosRelatorio[0][0]['tempo_lancamento'];
    $segundos_espera = 0;
    $totalAllSegundosEspera = 0;
    $segundos_lancamento = 0;
    $totalAllSegundosLancamento = 0;
    $totalAllAtendidos = 0;
    $totalizadorEspera = '';
    $totalizadorLancamento = '';
    $totalizadorPessoaFilial = '';
    foreach ($dadosRelatorio as $value) {

        @$totalizadorEspera[$value[0]['cd_filial']] += $value[0]['segundos_espera'];
        @$totalizadorLancamento[$value[0]['cd_filial']] += $value[0]['segundos_lancamento'];
        @$totalizadorPessoaFilial[$value[0]['cd_filial']] += 1;

        if ($value[0]['nm_pessoa'] == 0) {
            $totalAllAtendidos = $totalAllAtendidos + 1;
        }
        if ($data_atual != $value[0]['data_pedido'] || $filial_atual != $value[0]['cd_filial']) {
            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(05);
            $tcpdf->Cell(200, 5, ' Filial: ' . $value[0]['cd_filial'] . '  -  Data: ' . $value[0]['data_pedido'], 0, 0, 'C', 1);
            $totalAllSegundosEspera = $totalAllSegundosEspera + $segundos_espera;
            $totalAllSegundosLancamento = $totalAllSegundosLancamento + $segundos_lancamento;
            $linhaDados = $linhaDados + 5;
            $filial_atual = $value[0]['cd_filial'];
            $data_atual = $value[0]['data_pedido'];
            $segundos_espera = 0;
            $segundos_lancamento = 0;
        }
        $totalEsperaData = $totalEsperaData + $value[0] ['tempo_espera'];
        $totalLancamentoData = $totalLancamentoData + $value[0]['tempo_lancamento'];
        $filial_atual = $value [0]['cd_filial'];
        $data_atual = $value[0]['data_pedido'];

        $segundos_espera = $segundos_espera + $value[0]['segundos_espera'];
        $seconds_espera = $segundos_espera;
        $hours_espera = floor($seconds_espera / 3600);
        $seconds_espera -= $hours_espera * 3600;
        $minutes_espera = floor($seconds_espera / 60);
        $seconds_espera -= $minutes_espera * 60;

        $segundos_lancamento = $segundos_lancamento + $value[0]['segundos_lancamento'];
        $seconds_lancamento = $segundos_lancamento;
        $hours_lancamento = floor($seconds_lancamento / 3600);
        $seconds_lancamento -= $hours_lancamento * 3600;
        $minutes_lancamento = floor($seconds_lancamento / 60);
        $seconds_lancamento -= $minutes_lancamento * 60;


        if ($tcpdf->GetY() >= 270) {
            $tcpdf->AddPage();
            $tcpdf->SetX(165);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->Cell(40, 15, "", 1, 1, 'R');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(12);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(12);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(17);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período: ", 0, 0, 'L');
            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(17);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');
            $linhaCabecalho = 45;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(05);
            $tcpdf->Cell(15, 5, "Código", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(20);
            $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(90);
            $tcpdf->Cell(55, 5, "Tempo Espera", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(145);
            $tcpdf->Cell(60, 5, "Tempo Lançamento", 0, 0, 'C', 1);
            $linhaDados = $linhaCabecalho + 5;
        }
        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(05);
        $tcpdf->Cell(15, 5, $value[0]['cd_pessoa'], 0, 0, 'R');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(20);
        $tcpdf->Cell(70, 5, $value[0]['nm_pessoa'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(90);
        $tcpdf->Cell(55, 5, $value[0]['tempo_espera'], 0, 0, 'C');
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(145);
        $tcpdf->Cell(60, 5, $value[0] ['tempo_lancamento'], 0, 0, 'C');
        $linhaDados = $linhaDados + 5;
    }
    $linhaDados = $linhaDados + 10;
    foreach ($totalizadorEspera as $key => $value) {
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

            $linhaDados = 20;
        }


        $totalizadorSegundosEspera = $value;
        $seconds_espera = $totalizadorSegundosEspera;
        $hours_espera = floor($seconds_espera / 3600);
        $seconds_espera -= $hours_espera * 3600;
        $minutes_espera = floor($seconds_espera / 60);
        $seconds_espera -= $minutes_espera * 60;


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Tempo Espera Filial " . $key . " : ", 0, 0, 'L');


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(60 + $x);
        $tcpdf->Cell(15, 5, $hours_espera . ":" . $minutes_espera . ":" . $seconds_espera, 0, 0, 'L');




        $linhaDados = $linhaDados + 5;
    }
    foreach ($totalizadorLancamento as $key => $value) {
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

            $linhaDados = 20;
        }


        $totalizadorSegundosLancamento = $value;
        $seconds_lancamento = $totalizadorSegundosLancamento;
        $hours_lancamento = floor($seconds_lancamento / 3600);
        $seconds_lancamento -= $hours_lancamento * 3600;
        $minutes_lancamento = floor($seconds_lancamento / 60);
        $seconds_lancamento -= $minutes_lancamento * 60;


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Tempo Lançamento Filial " . $key . " : ", 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(60 + $x);
        $tcpdf->Cell(15, 5, $hours_lancamento . ":" . $minutes_lancamento . ":" . $seconds_lancamento, 0, 0, 'L');




        $linhaDados = $linhaDados + 5;
    }

    foreach ($totalizadorPessoaFilial as $key => $value) {
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

            $linhaDados = 20;
        }
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Atendimentos Filial " . $key . " :  ", 0, 0, 'L');


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(60 + $x);
        $tcpdf->Cell(15, 5, $value, 0, 0, 'L');
        $linhaDados = $linhaDados + 5;
    }




    $totalAllSegundosEspera = $totalAllSegundosEspera + $segundos_espera;
    $seconds_espera = $totalAllSegundosEspera;
    $hours_espera = floor($seconds_espera / 3600);
    $seconds_espera -= $hours_espera * 3600;
    $minutes_espera = floor($seconds_espera / 60);
    $seconds_espera -= $minutes_espera * 60;


    $totalAllSegundosLancamento = $totalAllSegundosLancamento + $segundos_lancamento;
    $seconds_lancamento = $totalAllSegundosLancamento;
    $hours_lancamento = floor($seconds_lancamento / 3600);
    $seconds_lancamento -= $hours_lancamento * 3600;
    $minutes_lancamento = floor($seconds_lancamento / 60);
    $seconds_lancamento -= $minutes_lancamento * 60;

    if ($tcpdf->GetY() <= 240) {
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Tempo Total de Espera :", 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(60);
        $tcpdf->Cell(15, 5, $hours_espera . ':' . $minutes_espera . ':' . $seconds_espera, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Tempo Total de Lançamento :", 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(60);
        $tcpdf->Cell(15, 5, $hours_lancamento . ':' . $minutes_lancamento . ':' . $seconds_lancamento, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total de Atendimentos :", 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(60);
        $tcpdf->Cell(15, 5, $totalAllAtendidos, 0, 0, 'L');
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

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(30);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Tempo Total de Espera :", 0, 0, 'L');


        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY(30);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $hours_espera . ':' . $minutes_espera . ':' . $seconds_espera, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(35);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Tempo Total de Lançamento :", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY(35);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $hours_lancamento . ':' . $minutes_lancamento . ':' . $seconds_lancamento, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(40);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total de Atendimentos :", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY(40);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(15, 5, $totalAllAtendidos, 0, 0, 'L');
    }
    ob_end_clean();
    $data_relatorio = date("d_m_Y");
    $tcpdf->Output('relatorio_acompanhamento_tempo_crediario_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
} else if ($tipo_arquivo == 'GRAFICO') {
    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $dadosFilial = array();
    foreach ($dadosRelatorio as $value) {
        @$dadosFilial[$value[0]['cd_filial']]['filial'] = $value[0]['cd_filial'];
        @$dadosFilial[$value[0]['cd_filial']]['tempo_espera_horas'] = $dadosFilial[$value[0]['cd_filial']]['tempo_espera_horas'] + $value[0]['tempo_espera_horas'];
        @$dadosFilial[$value[0]['cd_filial']]['tempo_lancamento_horas'] = $dadosFilial[$value[0]['cd_filial']]['tempo_lancamento_horas'] + $value[0]['tempo_lancamento_horas'];
    }
    sort($dadosFilial);
    foreach ($dadosFilial as $value) {
        $dadosIndexados[] = $value;
    }
    $cores = array('436EEE', '0000EE', '1874CD', '36648B', '00688B', '6CA6CD', '4A708B', '607B8B', '00868B', '008B8B', '528B8B', '008B45', '008B00', '8B8B7A', '828282', 'CFCFCF', '4F4F4F', '008B8B', '4876FF', '104E8B', '00688B', '9FB6CD', '00B2EE', '4F94CD');
    //$cores = array('0000FF', 'A52A2A', 'FFD700', '8A2BE2', '008B45', '008B8B', 'FF7F00', 'B03060', '7B68EE', '6B8E23', 'CD5C5C', 'FF69B4', '8B8989', '104E8B', '00868B', '8B0000', '8B008B', '9ACD32', 'FFA07A', '5D478B', '000080', 'FFDAB9', '6B8E23', '5D478B');
    ?>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <?php
    if ($per_ini_pesquisas == null) {
        $per_ini_pesquisas = date("d/m/Y");
    }
    if ($per_fim_pesquisas == null) {
        $per_fim_pesquisas = date("d/m/Y");
    }
    ?>
    <!--!-->
    <div style="width: 100%; text-align: center; font-size: 18px;"><b>Relatório de Acompanhamento de Tempo Crediário</b><br>Período de <?php echo $per_ini_pesquisas . ' a ' . $per_fim_pesquisas; ?></div>
    <br><br>
    <div style="width: 100%; text-align: center; font-size: 18px;"><b>Tempo de Espera por Filial</b></div>
    <?php
    $n = 0;
    for ($i = 0; $n < count($dadosIndexados); $i++) {
        ?>
        <script type="text/javascript">
            google.load('visualization', '1.0', {'packages': ['corechart']});
            google.setOnLoadCallback(drawChart);
            function drawChart() {

                var data = google.visualization.arrayToDataTable([
                    ['', 'Tempo de Espera / Hora', {role: 'style'}],
        <?php for ($j = 0; $j < 20; $j++) { ?>
                        ['<?php echo "Filial " . $dadosIndexados[$n]['filial']; ?>', <?php echo $dadosIndexados[$n]['tempo_espera_horas']; ?>, 'color: <?php echo $cores[$j]; ?>    '],
            <?php
            $n++;
            if ($n >= count($dadosIndexados)) {
                break;
            }
        }
        ?>
                ]);
                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                    {calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation"},
                    2]);
                var options = {
                    width: 1200,
                    height: 450,
                    title: '',
                    legend: {position: "none"},
                    bar: {groupWidth: "25%"},
                    hAxis: {title: '', titleTextStyle: {color: 'red'}}
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('chart_col_espera<?php echo $i; ?>'));
                chart.draw(view, options);
            }
        </script>
        <div id="chart_col_espera<?php echo $i; ?>" style="width: 900px; height: 500px;"></div>
        <!--!-->

        <div style="width: 100%; text-align: center; font-size: 18px;"><b>Tempo de Lançamento por Filial</b></div>
        <?php
        $n = 0;
        for ($i = 0; $n < count($dadosIndexados); $i++) {
            ?>
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages': ['corechart']});
                google.setOnLoadCallback(drawChart);
                function drawChart() {

                    var data = google.visualization.arrayToDataTable([
                        ['Year', 'Tempo de Lançamento / Hora', {role: 'style'}],
            <?php for ($j = 0; $j < 20; $j++) { ?>
                            ['<?php echo "Filial " . $dadosIndexados[$n]['filial']; ?>', <?php echo $dadosIndexados[$n]['tempo_lancamento_horas']; ?>, 'color: <?php echo $cores[$j]; ?>    '],
                <?php
                $n++;
                if ($n >= count($dadosIndexados)) {
                    break;
                }
            }
            ?>
                    ]);
                    var view = new google.visualization.DataView(data);
                    view.setColumns([0, 1,
                        {calc: "stringify",
                            sourceColumn: 1,
                            type: "string",
                            role: "annotation"},
                        2]);
                    var options = {
                        width: 1200,
                        height: 450,
                        title: '',
                        legend: {position: "none"},
                        bar: {groupWidth: "25%"},
                        hAxis: {title: '', titleTextStyle: {color: 'red'}}
                    };

                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_col_lancamento<?php echo $i; ?>'));
                    chart.draw(view, options);
                }
            </script>
            <div id="chart_col_lancamento<?php echo $i; ?>" style="width: 900px; height: 500px;"></div>
            <?php
        }
    }
}
?>
