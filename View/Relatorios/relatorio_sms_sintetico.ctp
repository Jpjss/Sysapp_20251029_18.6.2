<?php

if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Campanha SMS X Valor SMS SINTÉTICO")
            ->setDescription("Campanha SMS X Valor SMS SINTÉTICO");



    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Campanha SMS X Valor SMS Sintético');

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
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFill()->getStartColor()->setRGB('631212');

    $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Campanha');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Clientes');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Valor Pago');

    $i = 7;
    $totalGeral = 0;
    foreach ($dadosRelatorio as $value) {

        $objPHPExcel->getActiveSheet()->getStyle("A$i:B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_campanha']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value[0]['qtde_clientes']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['vlr_total_sms']);
        $totalGeral += $value[0]['vlr_total_sms'];
        $i++;
    }

    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode('#,##0.00');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Recebido:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $totalGeral);

    $objPHPExcel->getActiveSheet()->setTitle('SMS X Valor SMS SINTÉTICO');


    $objPHPExcel->setActiveSheetIndex(0);


    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_sms_sintetico_' . $data_relatorio . '.xls"');
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
    $tcpdf->xheadertext = 'Relatório de Campanha SMS X Valor SMS SINTÉTICO';
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

    $x = 60;
    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(10 + $x);
    $tcpdf->Cell(20, 5, "Campanha", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(30 + $x);
    $tcpdf->Cell(20, 5, "Clientes", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(50 + $x);
    $tcpdf->Cell(30, 5, "Valor Pago", 0, 0, 'R', 1);

    $totalGeral = 0;

    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    $linhaDados = $linhaDados + 5;
    foreach ($dadosRelatorio as $value) {

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
            $tcpdf->Cell(30, 5, "Período SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(15);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

            $x = 60;
            $linhaCabecalho = 45;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(10 + $x);
            $tcpdf->Cell(20, 5, "Campanha", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(30 + $x);
            $tcpdf->Cell(20, 5, "Clientes", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(50 + $x);
            $tcpdf->Cell(30, 5, "Valor Pago", 0, 0, 'R', 1);


            $linhaDados = $linhaCabecalho + 5;

            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
//            $linhaDados = 50;
        }
        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(20, 5, $value[0]['cd_campanha'], 0, 0, 'C');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(30 + $x);
        $tcpdf->Cell(20, 5, $value[0]['qtde_clientes'], 0, 0, 'C');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, number_format($value[0]['vlr_total_sms'], 2, ',', '.'), 0, 0, 'R');

        $totalGeral += $value[0]['vlr_total_sms'];


        $tcpdf->SetFillColor(219, 219, 234);
        $tcpdf->SetTextColor(0, 0, 0);

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
        $tcpdf->Cell(30, 5, "R$ " . number_format($totalGeral, 2, ',', '.'), 0, 0, 'R');
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
        $tcpdf->Cell(30, 5, "R$ " . number_format($totalGeral, 2, ',', '.'), 0, 0, 'R');
    }


//    ob_start();
    ob_end_clean();

    $data_relatorio = date("d_m_Y");

    $tcpdf->Output('relatorio_sms_sintetico_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
