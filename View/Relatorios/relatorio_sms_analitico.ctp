<?php

if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Campanha SMS X Valor SMS ANALÍTICO")
            ->setDescription("Campanha SMS X Valor SMS ANALÍTICO");



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
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Campanha SMS X Valor SMS Analítico');

    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));
    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão: ' . date("d/m/Y") . ' às ' . $hora);

//
//
    if ($per_ini_envio == null) {
        $per_ini_envio = '01/01/1990';
    }
    if ($per_fim_envio == null) {
        $per_fim_envio = date("d/m/Y");
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Período de Retorno: ' . 'De ' . $per_ini_envio . ' a ' . $per_fim_envio);


    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getFill()->getStartColor()->setRGB('631212');

    $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Código');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cliente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Telefone');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', 'Valor Pago');

    $i = 7;
    $totalGeral = 0;
    $campanha_atual = '';
    $dt_envio_atual = '';
    $totalizador = '';
    foreach ($dadosRelatorio as $value) {
        @$totalizador[$value[0]['nm_campanha']] += $value[0]['valor_sms'];
        @$totalizador[$value[0]['dt_hr_envio']] += $value[0]['valor_sms'];
        if ($campanha_atual != $value[0]['nm_campanha']) {

            $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", utf8_encode($value[0]['nm_campanha']) . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_hr_envio']));

            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getFill()->getStartColor()->setRGB('DBDBEA');

            $campanha_atual = $value[0]['nm_campanha'];
            $dt_envio_atual = $value[0]['dt_hr_envio'];

            $i++;
        } else if ($dt_envio_atual != $value[0]['dt_hr_envio']) {

            $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", utf8_encode($value[0]['nm_campanha']) . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_hr_envio']));

            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getFill()->getStartColor()->setRGB('DBDBEA');

            $dt_envio_atual = $value[0]['dt_hr_envio'];

            $i++;
        }

        $objPHPExcel->getActiveSheet()->getStyle("A$i:B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle("D$i")->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", utf8_encode($value[0]['nm_pessoa']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $this->Funcionalidades->formatarTelefone($value[0]['telefone']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value[0]['valor_sms']);
        $totalGeral += $value[0]['valor_sms'];
        $i++;
    }

    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("D$i")->getNumberFormat()->setFormatCode('#,##0.00');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Recebido:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $totalGeral);

    $objPHPExcel->getActiveSheet()->setTitle('SMS X Valor SMS ANALÍTICO');


    $objPHPExcel->setActiveSheetIndex(0);


    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_sms_analitico_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else {
    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);
//$tcpdf->setHeaderFont(array($textfont, '', 20));
//$tcpdf->xheadercolor = array(0, 0, 0);
    $tcpdf->xheadertext = 'Relatório de Campanha SMS X Valor SMS Analítico';
    $tcpdf->xfootertext = 'Systec Web+';
    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    if ($per_ini_envio == null) {
        $per_ini_envio = '01/01/1990';
    }
    if ($per_fim_envio == null) {
        $per_fim_envio = date("d/m/Y");
    }

    $envio = 'De ' . $per_ini_envio . ' a ' . $per_fim_envio;



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
    $tcpdf->Cell(30, 5, "Período SMS:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(15);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

    $x = 20;
    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(10 + $x);
    $tcpdf->Cell(20, 5, "Código", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(30 + $x);
    $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(100 + $x);
    $tcpdf->Cell(30, 5, "Telefone", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(130 + $x);
    $tcpdf->Cell(30, 5, "Valor", 0, 0, 'C', 1);

    $totalGeral = 0;

    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);


    $campanha_atual = '';
    $dt_envio_atual = '';
    $totalizador = '';
    foreach ($dadosRelatorio as $value) {
        @$totalizador[$value[0]['nm_campanha']] += $value[0]['valor_sms'];
        @$totalizador[$value[0]['dt_hr_envio']] += $value[0]['valor_sms'];
        if ($tcpdf->GetY() >= 265) {
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
            $tcpdf->Cell(30, 5, "Período SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

            $linhaCabecalho = 45;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(10 + $x);
            $tcpdf->Cell(20, 5, "Código", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(30 + $x);
            $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(100 + $x);
            $tcpdf->Cell(30, 5, "Telefone", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(130 + $x);
            $tcpdf->Cell(30, 5, "Valor", 0, 0, 'C', 1);

            $linhaDados = $linhaCabecalho + 5;

            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
        }
        if ($campanha_atual != $value[0]['nm_campanha']) {
            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(10 + $x);
            $tcpdf->Cell(150, 5, utf8_encode($value[0]['nm_campanha']) . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_hr_envio']), 0, 0, 'C', 1);

            $linhaDados = $linhaDados + 5;
            $campanha_atual = $value[0]['nm_campanha'];
            $dt_envio_atual = $value[0]['dt_hr_envio'];
        } else if ($dt_envio_atual != $value[0]['dt_hr_envio']) {
            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(10 + $x);
            $tcpdf->Cell(150, 5, utf8_encode($value[0]['nm_campanha']) . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_hr_envio']), 0, 0, 'C', 1);

            $linhaDados = $linhaDados + 5;
            $dt_envio_atual = $value[0]['dt_hr_envio'];
        }


        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(20, 5, $value[0]['cd_pessoa'], 0, 0, 'C', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(30 + $x);
        $tcpdf->Cell(70, 5, utf8_encode($value[0]['nm_pessoa']), 0, 0, 'L', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(100 + $x);
        $tcpdf->Cell(30, 5, $this->Funcionalidades->formatarTelefone($value[0]['telefone']), 0, 0, 'C', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(130 + $x);
        $tcpdf->Cell(30, 5, $this->Formatacao->moeda($value[0]['valor_sms']), 0, 0, 'C', 0);


        $totalGeral += $value[0]['valor_sms'];


        $tcpdf->SetFillColor(219, 219, 234);
        $tcpdf->SetTextColor(0, 0, 0);

        $linhaDados = $linhaDados + 5;
    }
    $linhaDados = $linhaDados + 10;
    foreach ($totalizador as $key => $value) {
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
            $tcpdf->Cell(30, 5, "Período SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');
            
            $linhaDados = 20;
        }
        
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total ".$this->Funcionalidades->formatarDataAp($key).":", 0, 0, 'L');


        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 5);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, "R$ " . number_format($value, 2, ',', '.'), 0, 0, 'L');
        
        $linhaDados = $linhaDados + 5;
    }
    if ($tcpdf->GetY() <= 240) {

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Geral:", 0, 0, 'L');


        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, "R$ " . number_format($totalGeral, 2, ',', '.'), 0, 0, 'L');
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
        $tcpdf->Cell(30, 5, "Período SMS:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 5);
        $tcpdf->SetY(15);
        $tcpdf->SetX(180);
        $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(30);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Geral:", 0, 0, 'L');


        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY(30);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, "R$ " . number_format($totalGeral, 2, ',', '.'), 0, 0, 'L');
    }


//    ob_start();
    ob_end_clean();

    $data_relatorio = date("d_m_Y");

    $tcpdf->Output('relatorio_sms_analitico_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
