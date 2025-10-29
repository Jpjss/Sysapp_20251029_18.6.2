<?php
if ($tipo_arquivo == 'EXCEL') {
    App::import('Component', 'Funcionalidades');
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Sugestões")
            ->setDescription("Relatório de Sugestões");

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório de Sugestões');

    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));
    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão: ' . date("d/m/Y") . ' às ' . $hora);

    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle('A6:C6')->getFont()->getColor()->setRGB('FFFFFF');
    $objPHPExcel->getActiveSheet()->getStyle("D6:C6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Pesquisa');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cliente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Sugestão');

    $i = 7;

    foreach ($atendimentos as $value) {

        $objPHPExcel->getActiveSheet()->getStyle("A$i:B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("G$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("D$i:G$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value['Pesquisa']['ds_questionario']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value['Cliente']['nm_pessoa']);
        if ($value['RespostaCpl']['ds_resposta'] != null) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value['RespostaCpl']['ds_resposta']);
        } else {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", "Nenhuma sugestão");
        }
        $i++;
    }

    $objPHPExcel->getActiveSheet()->setTitle('Relatório de Sugestões');

    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_sugestoes_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else if ($tipo_arquivo == 'PDF') {
    App::import('Vendor', 'xtcpdf');
    App::import('Component', 'Funcionalidades');

    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $tcpdf = new XTCPDF();
    $textfont = 'freesans';

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);

    $tcpdf->xheadertext = 'Relatório de Sugestões';
    $tcpdf->xfootertext = 'Systec Web+';

    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    /*
     * Y deve ficar em cima de X
     * VERTICAL = Y
     * HORIZONTAL = X
     */
    $tcpdf->AddPage();

    $tcpdf->SetFont($textfont, 'B', 5);
    $tcpdf->SetY(10);
    $tcpdf->SetX(165);
    $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

    $tcpdf->SetFont($textfont, '', 5);
    $tcpdf->SetY(10);
    $tcpdf->SetX(180);
    $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

    $x = 0;
    $linhaCabecalho = 45;
    $tcpdf->SetFillColor(153, 000, 000);
    $tcpdf->SetTextColor(255, 255, 255);
    $tcpdf->SetFont($textfont, '', 8);
    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(10 + $x);
    $tcpdf->Cell(50, 5, "Pesquisa", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(60 + $x);
    $tcpdf->Cell(60, 5, "Cliente", 0, 0, 'L', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(120 + $x);
    $tcpdf->Cell(75, 5, "Sugestão", 0, 0, 'L', 1);

    $totalGeral = 0;

    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);

    foreach ($atendimentos as $value) {

        if ($tcpdf->GetY() >= 270) {
            $tcpdf->AddPage();

            $tcpdf->SetFont($textfont, 'B', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(165);
            $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

            $tcpdf->SetFont($textfont, '', 5);
            $tcpdf->SetY(10);
            $tcpdf->SetX(180);
            $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');

            $linhaCabecalho = 45;
            $tcpdf->SetFillColor(153, 000, 000);
            $tcpdf->SetTextColor(255, 255, 255);
            $tcpdf->SetFont($textfont, '', 8);
            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(10 + $x);
            $tcpdf->Cell(50, 5, "Pesquisa", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(60 + $x);
            $tcpdf->Cell(60, 5, "Cliente", 0, 0, 'L', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(120 + $x);
            $tcpdf->Cell(75, 5, "Sugestão", 0, 0, 'L', 1);

            $linhaDados = $linhaCabecalho + 5;

            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
        }

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(50, 5, $value['Pesquisa']['ds_questionario'], 0, 0, 'L', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(60 + $x);
        $tcpdf->Cell(60, 5, $value['Cliente']['nm_pessoa'], 0, 0, 'L', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(120 + $x);

        if ($value['RespostaCpl']['ds_resposta'] != null) {
            $tcpdf->Cell(40, 5, $value['RespostaCpl']['ds_resposta'], 0, 0, 'L', 0);
        } else {
            $tcpdf->Cell(40, 5, "Nenhuma sugestão", 0, 0, 'L', 0);
        }

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(175 + $x);

        $tcpdf->SetFillColor(219, 219, 234);
        $tcpdf->SetTextColor(0, 0, 0);

        $linhaDados = $linhaDados + 5;
    }

    ob_end_clean();

    $data_relatorio = date("d_m_Y");

    $tcpdf->Output('relatorio_sugestoes_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
}
?>
