<?php

if ($tipo_arquivo == 'EXCEL') {
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Inadimplência Cargo/Ano")
            ->setDescription("Inadimplência Cargo/Ano");

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório Inadimplência Cargo/Ano');

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
    $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Ano');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cargo');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Valor Total');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', 'Valor em Aberto');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E6', '% de Inadimplência');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F6', 'Qtde Total');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G6', 'Qtde em Aberto');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H6', '% Qtde em Aberto');

    $ano_atual = @$dadosRelatorio[0][0]['ano'];
    $valor_aberto_anual = @$dados[0][0]['valor_em_aberto'];
    $totalAbertoAno = 0;
    $totalQtdeAbertoAno = 0;
    $i = 7;
    foreach ($dadosRelatorio as $value) {

        if ($ano_atual != $value[0]['ano']) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:H$i");
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", 'Ano: ' . $ano_atual . '  -  Valor em Aberto: R$  ' . $totalAbertoAno . '  -  Qtde em Aberto: ' . $totalQtdeAbertoAno);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->getFill()->getStartColor()->setRGB('DBDBEA');
            $ano_atual = $value[0]['ano'];
            $totalAbertoAno = 0;
            $totalQtdeAbertoAno = 0;
            $valor_aberto_anual = $value[0]['valor_aberto_anual'];
            $i++;
        }

        $totalAbertoAno = $totalAbertoAno + $value[0]['valor_em_aberto'];
        $totalQtdeAbertoAno = $totalQtdeAbertoAno + $value[0]['quantidade_em_aberto'];

        $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("E$i:G$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value[0]['ano']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", utf8_encode($value[0]['ds_cargo']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value[0]['valor_total']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value[0]['valor_em_aberto']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$i", $value[0]['percentual_inadimplencia']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("F$i", $value[0]['quantidade_total']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$i", $value[0]['quantidade_em_aberto']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("H$i", $value[0]['percentual_qtde_em_aberto']);


        $i++;
    }
    $ano_atual != $value[0]['ano'];
    $objPHPExcel->getActiveSheet()->mergeCells("A$i:H$i");
    $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", 'Ano: ' . $ano_atual . '  -  Valor em Aberto: R$  ' . $totalAbertoAno . '  -  Qtde em Aberto: ' . $totalQtdeAbertoAno);
    $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->getFill()->getStartColor()->setRGB('DBDBEA');
    $ano_atual = $value[0]['ano'];
    $valor_aberto_anual = $value[0]['valor_aberto_anual'];
    $i++;

    $x = $i;
    $i = $i + 2;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode('#,##0.00');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Valor Total em Aberto:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", "=SUM(D7:D$x)");

    $i++;

    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getNumberFormat()->setFormatCode('#,0');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Qtde Total em Aberto:");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", "=SUM(G7:G$x)");

    $objPHPExcel->getActiveSheet()->setTitle('Relatório de Inadimplência');
    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_inadimplencia_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    $objPHPExcel->getActiveSheet()->setTitle('INADIMPLÊNCIA CARGO-ANO');
    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_inadimplencia_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else {
    App::import('Vendor', 'xtcpdf');
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);

    $tcpdf->xheadertext = 'Relatório de Inadimplência Cargo/Ano';
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
    $tcpdf->Cell(10, 5, "Ano", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(15);
    $tcpdf->Cell(55, 5, "Cargo", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(70);
    $tcpdf->Cell(20, 5, "Valor Total", 0, 0, 'R', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(90);
    $tcpdf->Cell(20, 5, "Valor Aberto", 0, 0, 'R', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(110);
    $tcpdf->Cell(30, 5, "% Inadimplência", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(140);
    $tcpdf->Cell(20, 5, "Qtde Total", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(160);
    $tcpdf->Cell(25, 5, "Qtde Aberto", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(185);
    $tcpdf->Cell(20, 5, "% Qtde Aberto", 0, 0, 'C', 1);
    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    $ano_atual_pdf = @$dadosRelatorio[0][0]['ano'];
    $totalAbertoAno = @$dados[0][0]['valor_em_aberto'];
    $totalQtdeAbertoAno = @$dados[0][0]['quantidade_em_aberto'];
    $totalAberto = 0;
    $totalQtdeAberto = 0;
    $totalAll = 0;
    $totalAllQtde = 0;



    foreach ($dadosRelatorio as $value) {
        if ($ano_atual_pdf != $value[0]['ano']) {
            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
            $tcpdf->SetY($linhaDados);
            $tcpdf->SetX(05 + $x);
            $tcpdf->Cell(200, 5, ' Ano: ' . $ano_atual_pdf . '  -  Valor em Aberto: R$  ' . number_format($totalAbertoAno, 2, ',', '.') . '  -  Qtde em Aberto: ' . $totalQtdeAbertoAno, 0, 0, 'C', 1);
            $totalAll = $totalAll + $totalAbertoAno;
            $totalAllQtde = $totalAllQtde + $totalQtdeAbertoAno;
            $linhaDados = $linhaDados + 5;
            $ano_atual_pdf = $value[0]['ano'];
            $totalAbertoAno = 0;
            $totalQtdeAbertoAno = 0;
        }
        $totalAbertoAno = $totalAbertoAno + $value[0]['valor_em_aberto'];
        $totalQtdeAbertoAno = $totalQtdeAbertoAno + $value[0]['quantidade_em_aberto'];
        $ano_atual_pdf = $value[0]['ano'];

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
            $tcpdf->Cell(10, 5, "Ano", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(15);
            $tcpdf->Cell(55, 5, "Cargo", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(70);
            $tcpdf->Cell(20, 5, "Valor Total", 0, 0, 'R', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(90);
            $tcpdf->Cell(20, 5, "Valor Aberto", 0, 0, 'R', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(110);
            $tcpdf->Cell(30, 5, "% Inadimplência", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(140);
            $tcpdf->Cell(20, 5, "Qtde Total", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(160);
            $tcpdf->Cell(25, 5, "Qtde Aberto", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(185);
            $tcpdf->Cell(20, 5, "% Qtde Aberto", 0, 0, 'C', 1);


            $linhaDados = 50;
        }
        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(05);
        $tcpdf->Cell(10, 5, $value[0]['ano'], 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(15);
        $tcpdf->Cell(55, 5, utf8_encode($value[0]['ds_cargo']), 0, 0, 'L');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(70);
        $tcpdf->Cell(20, 5, number_format($value[0]['valor_total'], 2, ',', '.'), 0, 0, 'R');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(90);
        $tcpdf->Cell(20, 5, number_format($value[0]['valor_em_aberto'], 2, ',', '.'), 0, 0, 'R');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(110);
        $tcpdf->Cell(30, 5, number_format($value[0]['percentual_inadimplencia'], 2, ',', '.'), 0, 0, 'C');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(140);
        $tcpdf->Cell(20, 5, $value[0]['quantidade_total'], 0, 0, 'R');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(160);
        $tcpdf->Cell(20, 5, $value[0]['quantidade_em_aberto'], 0, 0, 'R');

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(180);
        $tcpdf->Cell(20, 5, number_format($value[0]['percentual_qtde_em_aberto'], 2, ',', '.'), 0, 0, 'R');
        $linhaDados = $linhaDados + 5;
    }

    $ano_atual_pdf != $value[0]['ano'];
    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);
    $tcpdf->SetY($linhaDados);
    $tcpdf->SetX(05 + $x);
    $tcpdf->Cell(200, 5, ' Ano: ' . $ano_atual_pdf . '  -  Valor em Aberto: R$  ' . number_format($totalAbertoAno, 2, ',', '.') . '  -  Qtde em Aberto: ' . $totalQtdeAbertoAno, 0, 0, 'C', 1);
    $linhaDados = $linhaDados + 5;
    $ano_atual_pdf = $value [0]['ano'];

    $totalAll = $totalAll + $totalAbertoAno;

    $totalAllQtde = $totalAllQtde + $totalQtdeAbertoAno;


    if ($tcpdf->GetY() <= 240) {


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 30);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Valor Total em Aberto:", 0, 0, 'L');
        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 30);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, number_format($totalAll, 2, ',', '.'), 0, 0, 'L');

        $linhaDados = $linhaDados + 5;


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 35);
        $tcpdf->SetX(10);
        $tcpdf->Cell(15, 5, "Qtde Total em Aberto:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 35);
        $tcpdf->SetX(45);
        $tcpdf->Cell(15, 5, $totalAllQtde, 0, 0, 'L');
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
        $tcpdf->Cell(15, 5, "Valor Total em Aberto:  " . $totalAll, 0, 0, 'L');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY(35);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Qtde Total em Aberto:" . $totalAllQtde, 0, 0, 'L');
    }
    ob_end_clean();
    $data_relatorio = date("d_m_Y");
    $tcpdf->Output('relatorio_descricao_atendimento_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
