<?php

//App::import('Vendor', 'utilidades');
if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("ENVIO DE SMS")
            ->setDescription("Relatório de Envio de SMS");



    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);


    $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório de Envio de SMS');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão:');

    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', date("d/m/Y") . ' às ' . $hora);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Filial:');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Tipo de SMS:');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Dt. Envio SMS:');

    if ($tipo_sms == "T") {
        $tipoSms = "Clientes Atrasados e Clientes Negativados";
    } else if ($tipo_sms == 0) {
        $tipoSms = "Clientes Atrasados";
    } else {
        $tipoSms = "Clientes Negativados";
    }
    
    
    if ($per_ini_envio == null) {
        $per_ini_envio = '01/01/1990';
    }
    if ($per_fim_envio == null) {
        $per_fim_envio = date("d/m/Y");
    }
    
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', $tipoSms);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'De ' . $per_ini_envio . ' a ' . $per_fim_envio);


    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A9:D9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A9:D9')->getFill()->getStartColor()->setRGB('631212');

    $objPHPExcel->getActiveSheet()->getStyle('A9:D9')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A9:D9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A9', 'Ficha');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Cliente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', 'Telefone');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D9', 'Contrato');
    $i = 11;
    $total = @count($clientes);
    $totalAtrasados = 0;
    $totalNegativados = 0;

    $filial_atual = @$clientes[0][0]['cd_filial'];
    $dt_envio_atual = @$clientes[0][0]['dt_envio'];
    $tipo_atraso = @$clientes[0][0]['tipo_atraso'];

    $objPHPExcel->getActiveSheet()->mergeCells("A10:D10");
    $objPHPExcel->getActiveSheet()->getStyle("A10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A10", @$clientes[0][0]['nm_filial'] . ' - Data de envio: ' . $dt_envio_atual . ' - ' . @$clientes[0][0]['ds_tipo_atraso']);
    $objPHPExcel->getActiveSheet()->getStyle("A10:D10")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("A10:D10")->getFill()->getStartColor()->setRGB('DBDBEA');

    foreach ($clientes as $value) {

        if ($value[0]['tipo_atraso'] == 0) {
            $totalAtrasados = $totalAtrasados + 1;
        } else {
            $totalNegativados = $totalNegativados + 1;
        }

        if ($filial_atual != $value[0]['cd_filial']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", utf8_encode($value[0]['nm_filial']) . ' - Data de envio: ' . $value[0]['dt_envio'] . ' - ' . $value[0]['ds_tipo_atraso']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $i++;
            $filial_atual = $value[0]['cd_filial'];
            $dt_envio_atual = $value[0]['dt_envio'];
            $tipo_atraso = $value[0]['tipo_atraso'];
        } else if ($dt_envio_atual != $value[0]['dt_envio']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", utf8_encode($value[0]['nm_filial']) . ' - Data de envio: ' . $value[0]['dt_envio'] . ' - ' . $value[0]['ds_tipo_atraso']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $i++;
            $dt_envio_atual = $value[0]['dt_envio'];
        } else if ($tipo_atraso != $value[0]['tipo_atraso']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:D$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", utf8_encode($value[0]['nm_filial']) . ' - Data de envio: ' . $value[0]['dt_envio'] . ' - ' . $value[0]['ds_tipo_atraso']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $i++;
            $tipo_atraso = $value[0]['tipo_atraso'];
        }
        $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value[0]['nm_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['telefone']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value[0]['cd_ped']);
        $i++;
    }
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', utf8_encode($nomeFiliais));


    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Atrasados: $totalAtrasados");

    $i++;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Negativados: $totalNegativados");


    $i++;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total: $total");


    $objPHPExcel->getActiveSheet()->setTitle('Relatório Envio de SMS');


    $objPHPExcel->setActiveSheetIndex(0);


    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_envio_sms_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else {

    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);
//$tcpdf->setHeaderFont(array($textfont, '', 20));
//$tcpdf->xheadercolor = array(0, 0, 0);
    $tcpdf->xheadertext = 'Relatório de Envio de SMS';
    $tcpdf->xfootertext = 'Systec Web+';

    /*
     * Y deve ficar em cima de X
     * HORIZONTAL = X
     * VERTICAL = Y
     */
    $tcpdf->AddPage();
    $total = count($clientes);
    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    if ($tipo_sms == "T") {
        $tipoSms = "Clientes Atrasados e Clientes Negativados";
    } else if ($tipo_sms == 0) {
        $tipoSms = "Clientes Atrasados";
    } else {
        $tipoSms = "Clientes Negativados";
    }

    if ($per_ini_envio == null) {
        $per_ini_envio = '01/01/1990';
    }
    if ($per_fim_envio == null) {
        $per_fim_envio = date("d/m/Y");
    }

    $tcpdf->SetFont($textfont, 'B', 10);
    $tcpdf->SetY(20);
    $tcpdf->SetX(10);
    $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 10);
    $tcpdf->SetY(20);
    $tcpdf->SetX(40);
    $tcpdf->Cell(30, 5, date('d/m/Y') . " às " . $hora, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 10);
    $tcpdf->SetY(25);
    $tcpdf->SetX(10);
    $tcpdf->Cell(30, 5, "Filial:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 6);
    $tcpdf->SetY(25);
    $tcpdf->SetX(40);
    $tcpdf->MultiCell(150, 5, utf8_encode($nomeFiliais), 0, 'L');
//    $tcpdf->MultiCelll(60, 5, utf8_encode($nomeFiliais), 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 10);
    $tcpdf->SetY(35);
    $tcpdf->SetX(10);
    $tcpdf->Cell(30, 5, "Tipo de SMS:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 10);
    $tcpdf->SetY(35);
    $tcpdf->SetX(40);
    $tcpdf->Cell(30, 5, $tipoSms, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 10);
    $tcpdf->SetY(40);
    $tcpdf->SetX(10);
    $tcpdf->Cell(30, 5, "Dt Envio SMS:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 10);
    $tcpdf->SetY(40);
    $tcpdf->SetX(40);
    $tcpdf->Cell(30, 5, $per_ini_envio . ' a ' . $per_fim_envio, 0, 0, 'L');

    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(10);
    $tcpdf->Cell(15, 5, "Ficha", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(25);
    $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(95);
    $tcpdf->Cell(30, 5, "Telefone", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(125);
    $tcpdf->Cell(30, 5, "Contrato", 0, 0, 'L', 1);

    $totalAtrasados = 0;
    $totalNegativados = 0;

    $filial_atual = $clientes[0][0]['cd_filial'];
    $dt_envio_atual = $clientes[0][0]['dt_envio'];
    $tipo_atraso = $clientes[0][0]['tipo_atraso'];

    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    $tcpdf->SetY($linhaDados);
    $tcpdf->SetX(10);
    $tcpdf->Cell(145, 5, utf8_encode($clientes[0][0]['nm_filial']) . ' - Data de envio: ' . $clientes[0][0]['dt_envio'] . ' - ' . $clientes[0][0]['ds_tipo_atraso'], 0, 0, 'C', 1);

    $linhaDados += 5;
    foreach ($clientes as $value) {


        if ($value[0]['tipo_atraso'] == 0) {
            $totalAtrasados = $totalAtrasados + 1;
        } else {
            $totalNegativados = $totalNegativados + 1;
        }

        if ($filial_atual != $value[0]['cd_filial']) {

            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(10);
            $tcpdf->Cell(145, 5, utf8_encode($value[0]['nm_filial']) . ' - Data de envio: ' . $value[0]['dt_envio'] . ' - ' . $value[0]['ds_tipo_atraso'], 0, 0, 'C', 1);

            $linhaDados = $linhaDados + 5; // DBDBEA
            $filial_atual = $value[0]['cd_filial'];
            $dt_envio_atual = $value[0]['dt_envio'];
            $tipo_atraso = $value[0]['tipo_atraso'];
        } else if ($dt_envio_atual != $value[0]['dt_envio']) {
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(10);
            $tcpdf->Cell(145, 5, utf8_encode($value[0]['nm_filial']) . ' - Data de envio: ' . $value[0]['dt_envio'] . ' - ' . $value[0]['ds_tipo_atraso'], 0, 0, 'C', 1);

            $linhaDados = $linhaDados + 5; // DBDBEA
            $dt_envio_atual = $value[0]['dt_envio'];
        } else if ($tipo_atraso != $value[0]['tipo_atraso']) {
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(10);
            $tcpdf->Cell(145, 5, utf8_encode($value[0]['nm_filial']) . ' - Data de envio: ' . $value[0]['dt_envio'] . ' - ' . $value[0]['ds_tipo_atraso'], 0, 0, 'C', 1);

            $linhaDados = $linhaDados + 5;
            $tipo_atraso = $value[0]['tipo_atraso'];
        }


        if ($tcpdf->GetY() >= 270) {
            $tcpdf->AddPage();
            $tcpdf->SetFont($textfont, 'B', 10);
            $tcpdf->SetY(20);
            $tcpdf->SetX(10);
            $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 10);
            $tcpdf->SetY(20);
            $tcpdf->SetX(40);
            $tcpdf->Cell(30, 5, date('d/m/Y') . " às " . $hora, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 10);
            $tcpdf->SetY(25);
            $tcpdf->SetX(10);
            $tcpdf->Cell(30, 5, "Filial:", 0, 0, 'L');
            
            $tcpdf->SetFont($textfont, '', 6);
            $tcpdf->SetY(25);
            $tcpdf->SetX(40);
            $tcpdf->MultiCell(150, 5, utf8_encode($nomeFiliais), 0, 'L');


            $tcpdf->SetFont($textfont, 'B', 10);
            $tcpdf->SetY(35);
            $tcpdf->SetX(10);
            $tcpdf->Cell(30, 5, "Tipo de SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 10);
            $tcpdf->SetY(35);
            $tcpdf->SetX(40);
            $tcpdf->Cell(30, 5, $tipoSms, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 10);
            $tcpdf->SetY(40);
            $tcpdf->SetX(10);
            $tcpdf->Cell(30, 5, "Dt Envio SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 10);
            $tcpdf->SetY(40);
            $tcpdf->SetX(40);
            $tcpdf->Cell(30, 5, $per_ini_envio . ' a ' . $per_fim_envio, 0, 0, 'L');

            $linhaCabecalho = 45;
            $linhaDados = $linhaCabecalho + 5;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(10);
            $tcpdf->Cell(15, 5, "Ficha", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(25);
            $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(95);
            $tcpdf->Cell(30, 5, "Telefone", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(125);
            $tcpdf->Cell(30, 5, "Contrato", 0, 0, 'L', 1);
        }
        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, $value[0]['cd_pessoa'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(25);
        $tcpdf->Cell(70, 5, utf8_encode($value[0]['nm_pessoa']), 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(95);
        $tcpdf->Cell(30, 5, $value[0]['telefone'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(125);
        $tcpdf->Cell(30, 5, $value[0]['cd_ped'], 0, 0, 'L');

        $linhaDados = $linhaDados + 5;
    }


    if ($tcpdf->GetY() <= 240) {
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Atraso:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $totalAtrasados, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Negativado:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $totalNegativados, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 25);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Geral:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 25);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $total, 0, 0, 'L');
    } else {

        $tcpdf->AddPage();

        $tcpdf->SetFont($textfont, 'B', 10);
        $tcpdf->SetY(20);
        $tcpdf->SetX(10);
        $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 10);
        $tcpdf->SetY(20);
        $tcpdf->SetX(40);
        $tcpdf->Cell(30, 5, date('d/m/Y') . " às " . $hora, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 10);
        $tcpdf->SetY(25);
        $tcpdf->SetX(10);
        $tcpdf->Cell(30, 5, "Filial:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 6);
        $tcpdf->SetY(25);
        $tcpdf->SetX(40);
        $tcpdf->MultiCell(150, 5, utf8_encode($nomeFiliais), 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 10);
        $tcpdf->SetY(35);
        $tcpdf->SetX(10);
        $tcpdf->Cell(30, 5, "Tipo de SMS:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 10);
        $tcpdf->SetY(35);
        $tcpdf->SetX(40);
        $tcpdf->Cell(30, 5, $tipoSms, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 10);
        $tcpdf->SetY(40);
        $tcpdf->SetX(10);
        $tcpdf->Cell(30, 5, "Dt Envio SMS:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 10);
        $tcpdf->SetY(40);
        $tcpdf->SetX(40);
        $tcpdf->Cell(30, 5, $per_ini_envio . ' a ' . $per_fim_envio, 0, 0, 'L');

        $linhaDados = 40;


        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, "455", 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Atraso:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $totalAtrasados, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Negativado:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $totalNegativados, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 25);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Total Geral:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 25);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $total, 0, 0, 'L');
    }


//    ob_start();
    ob_end_clean();
    
    $data_relatorio = date("d_m_Y");
    $tcpdf->Output('relatorio_envio_sms_'.$data_relatorio.'.pdf', 'I');
    ob_end_flush();
}
?>
