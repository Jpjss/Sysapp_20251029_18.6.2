<?php

if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("SMS COBRANÇA")
            ->setDescription("Relatório de cobrança SMS");



    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório de Retorno de Cobrança por envio de SMS');

    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));
    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão: ' . date("d/m/Y") . ' às ' . $hora);


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

    if ($per_ini_retorno == null) {
        $per_ini_retorno = '01/01/1990';
    }
    if ($per_fim_retorno == null) {
        $per_fim_retorno = date("d/m/Y");
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Tipo de SMS: ' . $tipoSms);
    $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Dt. Envio SMS: ' . 'De ' . $per_ini_envio . ' a ' . $per_fim_envio);
    $objPHPExcel->getActiveSheet()->mergeCells('A6:D6');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Período de Retorno: ' . 'De ' . $per_ini_retorno . ' a ' . $per_fim_retorno);


    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getFill()->getStartColor()->setRGB('631212');

    $objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//    $objPHPExcel->getActiveSheet()->getStyle('A10:E10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//    $objPHPExcel->getActiveSheet()->getStyle('A11:E11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//    $objPHPExcel->getActiveSheet()->getStyle('A12:E12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8', 'Código');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', 'Cliente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', 'Contrato');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', 'Valor Pago');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E8', 'Data Pagamento');
//    $objPHPExcel->getActiveSheet()->getStyle('D10:D12')->getNumberFormat()->setFormatCode('#,##0.00');
    $totalAtrasados = 0;
    $totalNegativados = 0;
    $filial_atual = @$clientes[0][0]['cd_filial'];
    $dt_envio_atual = @$clientes[0][0]['dt_envio'];
    $tipo_atraso = @$clientes[0][0]['tipo_atraso'];
    $totalFiliais = array();
    $i = 9;
    foreach ($clientes as $value) {
        if ($value[0]['tipo_atraso'] == 0) {
            $totalAtrasados = $totalAtrasados + 1;
        } else {
            $totalNegativados = $totalNegativados + 1;
        }

        if ($filial_atual != $value[0]['cd_filial']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:E$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['nm_filial'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_envio_sms']) . ' - ' . $value[0]['ds_tipo_atraso']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $i++;
            $filial_atual = $value[0]['cd_filial'];
            $dt_envio_atual = $value[0]['dt_envio_sms'];
            $tipo_atraso = $value[0]['tipo_atraso'];
        } else if ($dt_envio_atual != $value[0]['dt_envio_sms']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:E$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['nm_filial'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_envio_sms']) . ' - ' . $value[0]['ds_tipo_atraso']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $i++;
            $dt_envio_atual = $value[0]['dt_envio_sms'];
        } else if ($tipo_atraso != $value[0]['tipo_atraso']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:E$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['nm_filial'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_envio_sms']) . ' - ' . $value[0]['ds_tipo_atraso']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $i++;
            $tipo_atraso = $value[0]['tipo_atraso'];
        }

        @$totalFiliais[$value[0]['cd_filial']] = $totalFiliais[$value[0]['cd_filial']] + 1;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle("D$i")->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value[0]['nm_fant']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['nr_contrato']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value[0]['vlr_pgto']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$i", $this->Funcionalidades->formatarDataAp($value[0]['dt_pagamento']));
        $i++;
    }

    //Exibindo Totais finais
    $x = $i;
    $i = $i + 2;

    $objPHPExcel->getActiveSheet()->getStyle("B$i:C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Geral:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", count($clientes));
    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i:C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Atrasados:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $totalAtrasados);
    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i:C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Negativados:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $totalNegativados);
    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i:C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode('#,##0.00');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Recebido:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", "=SUM(D10:D$x)");

    $objPHPExcel->getActiveSheet()->setTitle('Relatório Retorno Cobrança SMS');


    $objPHPExcel->setActiveSheetIndex(0);


    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_cobranca_sms_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else {
    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);

    $tcpdf->xheadertext = 'Relatório de Retorno de Cobrança por Envio de SMS';
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

    if ($per_ini_retorno == null) {
        $per_ini_retorno = '01/01/1990';
    }
    if ($per_fim_retorno == null) {
        $per_fim_retorno = date("d/m/Y");
    }
    $envio = 'De ' . $per_ini_envio . ' a ' . $per_fim_envio;
    $retorno = 'De ' . $per_ini_retorno . ' a ' . $per_fim_retorno;

    if ($tipo_sms == "T") {
        $tipoSms = "Clientes Atrasados e Clientes Negativados";
    } else if ($tipo_sms == 0) {
        $tipoSms = "Clientes Atrasados";
    } else {
        $tipoSms = "Clientes Negativados";
    }
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

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(20);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(20);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 8);
    $tcpdf->SetY(35);
    $tcpdf->SetX(10);
    $tcpdf->Cell(30, 5, "Tipo de SMS:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY(35);
    $tcpdf->SetX(30);
    $tcpdf->Cell(30, 5, $tipoSms, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 8);
    $tcpdf->SetY(40);
    $tcpdf->SetX(10);
    $tcpdf->Cell(30, 5, "Dt Envio SMS:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY(40);
    $tcpdf->SetX(30);
    $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(10);
    $tcpdf->Cell(15, 5, "Código", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(25);
    $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(95);
    $tcpdf->Cell(30, 5, "Contrato", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(115);
    $tcpdf->Cell(30, 5, "Valor Pago", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(145);
    $tcpdf->Cell(30, 5, "Data Pagamento", 0, 0, 'L', 1);

    $totalRecebido = 0;
    $totalAtrasados = 0;
    $totalNegativados = 0;

    $filial_atual = $clientes[0][0]['cd_filial'];
    $dt_envio_atual = $clientes[0][0]['dt_envio_sms'];
    $tipo_atraso = $clientes[0][0]['tipo_atraso'];

    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    $tcpdf->SetY($linhaDados);
    $tcpdf->SetX(10);
    $tcpdf->Cell(165, 5, $clientes[0][0]['nm_filial'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($clientes[0][0]['dt_envio_sms']) . ' - ' . $clientes[0][0]['ds_tipo_atraso'], 0, 0, 'C', 1);

    $linhaDados = $linhaDados + 5;
    foreach ($clientes as $value) {

        if ($value[0]['tipo_atraso'] == 0) {
            $totalAtrasados = $totalAtrasados + 1;
        } else {
            $totalNegativados = $totalNegativados + 1;
        }
        $totalRecebido = $totalRecebido + $value[0]['vlr_pgto'];

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

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(20);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 8);
            $tcpdf->SetY(35);
            $tcpdf->SetX(10);
            $tcpdf->Cell(30, 5, "Tipo de SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY(35);
            $tcpdf->SetX(30);
            $tcpdf->Cell(30, 5, $tipo, 0, 0, 'L');

            $tcpdf->SetFont($textfont, 'B', 8);
            $tcpdf->SetY(40);
            $tcpdf->SetX(10);
            $tcpdf->Cell(30, 5, "Dt Envio SMS:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY(40);
            $tcpdf->SetX(30);
            $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

            $linhaCabecalho = 45;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(10);
            $tcpdf->Cell(15, 5, "Código", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(25);
            $tcpdf->Cell(70, 5, "Cliente", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(95);
            $tcpdf->Cell(30, 5, "Contrato", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(115);
            $tcpdf->Cell(30, 5, "Valor Pago", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(145);
            $tcpdf->Cell(30, 5, "Data Pagamento:", 0, 0, 'L', 1);
            $linhaDados = 50;
        }

        if ($filial_atual != $value[0]['cd_filial'] || $tipo_atraso != $value[0]['tipo_atraso'] || $dt_envio_atual != $value[0]['dt_envio_sms']) {

            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(10);
            $tcpdf->Cell(165, 5, $value[0]['nm_filial'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_envio_sms']) . ' - ' . $value[0]['ds_tipo_atraso'], 0, 0, 'C', 1);

            $linhaDados = $linhaDados + 5;
            $filial_atual = $value[0]['cd_filial'];
            $dt_envio_atual = $value[0]['dt_envio_sms'];
            $tipo_atraso = $value[0]['tipo_atraso'];
        }

        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, $value[0]['cd_pessoa'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(25);
        $tcpdf->Cell(70, 5, $value[0]['nm_fant'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(95);
        $tcpdf->Cell(30, 5, $value[0]['nr_contrato'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(115);
        $tcpdf->Cell(30, 5, number_format($value[0]['vlr_pgto'], 2, ',', '.'), 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(145);
        $tcpdf->Cell(30, 5, $this->Funcionalidades->formatarDataAp($value[0]['dt_pagamento']), 0, 0, 'L');
        $linhaDados = $linhaDados + 5;
    }
    if ($tcpdf->GetY() > 240) {
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

        $tcpdf->SetFont($textfont, 'B', 5);
        $tcpdf->SetY(20);
        $tcpdf->SetX(165);
        $tcpdf->Cell(30, 5, "Período Retorno:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 5);
        $tcpdf->SetY(20);
        $tcpdf->SetX(180);
        $tcpdf->Cell(30, 5, $retorno, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(35);
        $tcpdf->SetX(10);
        $tcpdf->Cell(30, 5, "Tipo de SMS:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY(35);
        $tcpdf->SetX(30);
        $tcpdf->Cell(30, 5, $tipoSms, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(40);
        $tcpdf->SetX(10);
        $tcpdf->Cell(30, 5, "Dt Envio SMS:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY(40);
        $tcpdf->SetX(30);
        $tcpdf->Cell(30, 5, $envio, 0, 0, 'L');

        $linhaDados = 40;
    }

    $tcpdf->SetFont($textfont, 'B', 8);
    $tcpdf->SetY($linhaDados + 10);
    $tcpdf->SetX(10);
    $tcpdf->Cell(15, 5, "Total Geral:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaDados + 10);
    $tcpdf->SetX(45);
    $tcpdf->Cell(15, 5, count($clientes), 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 8);
    $tcpdf->SetY($linhaDados + 15);
    $tcpdf->SetX(10);
    $tcpdf->Cell(15, 5, "Total Atrasados:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaDados + 15);
    $tcpdf->SetX(45);
    $tcpdf->Cell(15, 5, $totalAtrasados, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 8);
    $tcpdf->SetY($linhaDados + 20);
    $tcpdf->SetX(10);
    $tcpdf->Cell(15, 5, "Total Negativados:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaDados + 20);
    $tcpdf->SetX(45);
    $tcpdf->Cell(15, 5, $totalNegativados, 0, 0, 'L');

    $tcpdf->SetFont($textfont, 'B', 8);
    $tcpdf->SetY($linhaDados + 25);
    $tcpdf->SetX(10);
    $tcpdf->Cell(15, 5, "Total Recebido:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaDados + 25);
    $tcpdf->SetX(45);
    $tcpdf->Cell(15, 5, "R$ " . number_format($totalRecebido, 2, ',', '.'), 0, 0, 'L');

    ob_end_clean();

    $data_relatorio = date("d_m_Y");

    $tcpdf->Output('relatorio_cobranca_sms_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
