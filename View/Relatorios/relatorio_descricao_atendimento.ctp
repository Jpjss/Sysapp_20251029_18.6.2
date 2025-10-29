<?php

if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Descrição de Atendimento")
            ->setDescription("Descrição de Atendimento");


    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório Descrição de Atendimento');
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

    $objPHPExcel->getActiveSheet()->mergeCells('A4:C4');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Período de Pesquisa: ' . $per_ini_pesquisas . ' á ' . $per_fim_pesquisas);
    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Código');
    $objPHPExcel->getActiveSheet()->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cliente');
    $objPHPExcel->getActiveSheet()->getStyle("C6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Descrição');
    $data_atual = '';
    $descricao_atual = '';
    $i = 7;
    foreach ($dadosRelatorio as $value) {
        if ($data_atual != $value[0]['data_cadastro']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:C$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['ds_questionario'] . '  -  Data: ' . $value[0]['data_cadastro']);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:C$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:C$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $data_atual = $value[0]['data_cadastro'];
            $descricao_atual = $value[0]['ds_questionario'];
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['cd_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value[0]['nm_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['ds_historico']);
        $i++;
    }

    $objPHPExcel->getActiveSheet()->setTitle('DESCRIÇÃO ATENDIMENTO');
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_descricao_atendimento_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else {
    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);

    $tcpdf->xheadertext = 'Relatório de Descrição de Atendimento';
    $tcpdf->xfootertext = 'Systec Web+';
    $hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    if ($per_ini_envio == null) {
        $per_ini_envio = '01/01/1990';
    }
    if ($per_ini_pesquisas == null) {
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

    /* $x = 60; */
    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(05);
    $tcpdf->Cell(15, 5, "Código", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(20);
    $tcpdf->Cell(80, 5, "Cliente", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(100);
    $tcpdf->Cell(105, 5, "Descrição", 0, 0, 'L', 1);
    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    $data_atual = '';
    $descricao_atual = '';

    foreach ($dadosRelatorio as $value) {
        if ($descricao_atual != $value[0]['ds_questionario'] || $data_atual != $value[0]['data_cadastro']) {
            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(05);
            $tcpdf->Cell(200, 5, $value[0]['ds_questionario'] . '  -  Data: ' . $value[0]['data_cadastro'], 0, 0, 'C', 1);
            $linhaDados = $linhaDados + 5;
            $descricao_atual = $value[0]['ds_questionario'];
            $data_atual = $value[0]['data_cadastro'];
        }
        $descricao_atual = $value[0]['ds_questionario'];
        $data_atual = $value[0]['data_cadastro'];

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
            $tcpdf->Cell(80, 5, "Cliente", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(100);
            $tcpdf->Cell(105, 5, "Descrição", 0, 0, 'L', 1);
            $linhaDados = 50;
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
        $tcpdf->SetX(100);
        $tcpdf->Cell(115, 5, $value[0]['ds_historico'], 0, 0, 'L');
        $linhaDados = $linhaDados + 5;
    }
    ob_end_clean();
    $data_relatorio = date("d_m_Y");
    $tcpdf->Output('relatorio_descricao_atendimento_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
