<?php
if ($tipo_arquivo == 'EXCEL') {
    App::import('Component', 'Funcionalidades');
    App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Systec")
            ->setLastModifiedBy("Systec")
            ->setTitle("Relatório de Atendimentos")
            ->setDescription("Relatório de Atendimentos");



    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório de Atendimentos');

    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));
    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Emissão: ' . date("d/m/Y") . ' às ' . $hora);


    $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Período: ' . 'De ' . $per_ini_vendas . ' a ' . $per_fim_vendas);


    /**
     * Setando cor de fundo e texto das celulas A9 a E9
     */
    $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getFill()->getStartColor()->setRGB('631212');
    $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getFont()->getColor()->setRGB('FFFFFF');
    $objPHPExcel->getActiveSheet()->getStyle("D6:G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Pesquisa');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Cliente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Atendente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', 'Inicio');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E6', 'Fim');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F6', 'Data');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G6', 'Status');

    $i = 7;
    $totalGeral = 0;
    $cd_filial = '';
    $dt_envio_atual = '';

    $iniciado = 0;
    $semContato = 0;
    $concluido = 0;

    foreach ($atendimentos as $value) {
        switch ($value['GlbQuestionarioResposta']['status_atendimento']) {
            case(0):
                $iniciado += 1;
                break;
            case(1):
                $semContato += 1;
                break;
            case(2):
                $concluido += 1;
                break;
        }

        $objPHPExcel->getActiveSheet()->getStyle("A$i:B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("G$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("D$i:G$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $value['Pesquisa']['ds_questionario']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $value['Cliente']['nm_pessoa']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $value['Usuario']['nm_usu']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $value['GlbQuestionarioResposta']['hora_inicio']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$i", $value['GlbQuestionarioResposta']['hora_fim']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("F$i", $funcionalidades->formatarDataAp($value['GlbQuestionarioResposta']['dt_cad']));
        switch ($value['GlbQuestionarioResposta']['status_atendimento']) {
            case 0:
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getFont()->getColor()->setRGB('FFB22A');
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$i", 'Iniciado');
                break;
            case 1:
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getFont()->getColor()->setRGB('E52400');
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$i", 'Sem Contato');
                break;
            case 2:
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getFont()->getColor()->setRGB('9AC810');
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$i", 'Concluído');
                break;
        }
        $i++;
    }

    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Iniciado: ");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $iniciado);
    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Concluído: ");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $concluido);
    $i++;
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", "Total Sem Contato: ");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $semContato);
    $i++;

    $objPHPExcel->getActiveSheet()->setTitle('Relatório de Atendimentos');

    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    $data_relatorio = date("d_m_Y");
    header('Content-Disposition: attachment;filename="relatorio_atendimentos_' . $data_relatorio . '.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} else if ($tipo_arquivo == 'PDF') {
    App::import('Vendor', 'xtcpdf');
    App::import('Component', 'Funcionalidades');

    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $tcpdf = new XTCPDF();
    $textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 

    $tcpdf->SetAuthor("Systec");
    $tcpdf->SetAutoPageBreak(false);
//$tcpdf->setHeaderFont(array($textfont, '', 20));
//$tcpdf->xheadercolor = array(0, 0, 0);
    $tcpdf->xheadertext = 'Relatório de Atendimentos';
    $tcpdf->xfootertext = 'Systec Web+';
    $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));

    if (date("I") == 1) {
        $hora = $hora + "01:00";
    }

    if ($data_in == null) {
        $data_in = '01/01/1990';
    }
    if ($data_fim == null) {
        $data_fim = date("d/m/Y");
    }

    $envio = 'De ' . $data_in . ' a ' . $data_fim;



    /*
     * Y deve ficar em cima de X
     * HORIZONTAL = X
     * VERTICAL = Y
     */
    $tcpdf->AddPage();

//    $tcpdf->SetX(165);
//    $tcpdf->SetTextColor(0, 0, 0);
//    $tcpdf->Cell(40, 15, "", 1, 1, 'R');

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
    $tcpdf->Cell(40, 5, "Atendente", 0, 0, 'L', 1);

//    $tcpdf->SetY($linhaCabecalho);
//    $tcpdf->SetX(130 + $x);
//    $tcpdf->Cell(15, 5, "Início", 0, 0, 'C', 1);
//
//    $tcpdf->SetY($linhaCabecalho);
//    $tcpdf->SetX(145 + $x);
//    $tcpdf->Cell(15, 5, "Fim", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(160 + $x);
    $tcpdf->Cell(15, 5, "Data", 0, 0, 'C', 1);

    $tcpdf->SetY($linhaCabecalho);
    $tcpdf->SetX(175 + $x);
    $tcpdf->Cell(20, 5, "Status", 0, 0, 'C', 1);

    $totalGeral = 0;

    $linhaDados = $linhaCabecalho + 5;

    $tcpdf->SetFillColor(219, 219, 234);
    $tcpdf->SetTextColor(0, 0, 0);


    $filial_atual = '';
    $dt_envio_atual = '';

    foreach ($atendimentos as $value) {
        switch ($value['GlbQuestionarioResposta']['status_atendimento']) {
            case(0):
                $iniciado += 1;
                break;
            case(1):
                $semContato += 1;
                break;
            case(2):
                $concluido += 1;
                break;
        }

//        if ($filial_atual != $value[0]['cd_filial']) {
//            $tcpdf->SetFillColor(219, 219, 234);
//            $tcpdf->SetTextColor(0, 0, 0);
//            $tcpdf->SetY($linhaDados);
//            $tcpdf->SetX(10 + $x);
//            $tcpdf->Cell(70, 5, 'Filial ' . $value[0]['cd_filial'], 0, 0, 'C', 1);
////            $tcpdf->Cell(150, 5, $value[0]['cd_filial'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_hr_envio']), 0, 0, 'C', 1);
//
//            $linhaDados = $linhaDados + 5;
//            $filial_atual = $value[0]['cd_filial'];
//            $dt_envio_atual = $value[0]['dt_hr_envio'];
//        }
//        else if ($dt_envio_atual != $value[0]['dt_hr_envio']) {
//            $tcpdf->SetFillColor(219, 219, 234);
//            $tcpdf->SetTextColor(0, 0, 0);
//            $tcpdf->SetY($linhaDados);
//            $tcpdf->SetX(10 + $x);
//            $tcpdf->Cell(150, 5, $value[0]['nm_campanha'] . ' - Data de envio: ' . $this->Funcionalidades->formatarDataAp($value[0]['dt_hr_envio']), 0, 0, 'C', 1);
//
//            $linhaDados = $linhaDados + 5;
//            $dt_envio_atual = $value[0]['dt_hr_envio'];
//        }
        if ($tcpdf->GetY() >= 270) {
            $tcpdf->AddPage();
//            $tcpdf->SetX(165);
//            $tcpdf->SetTextColor(0, 0, 0);
//            $tcpdf->Cell(40, 15, "", 1, 1, 'R');

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
            $tcpdf->Cell(40, 5, "Atendente", 0, 0, 'L', 1);

//    $tcpdf->SetY($linhaCabecalho);
//    $tcpdf->SetX(130 + $x);
//    $tcpdf->Cell(15, 5, "Início", 0, 0, 'C', 1);
//
//    $tcpdf->SetY($linhaCabecalho);
//    $tcpdf->SetX(145 + $x);
//    $tcpdf->Cell(15, 5, "Fim", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(160 + $x);
            $tcpdf->Cell(15, 5, "Data", 0, 0, 'C', 1);

            $tcpdf->SetY($linhaCabecalho);
            $tcpdf->SetX(175 + $x);
            $tcpdf->Cell(20, 5, "Status", 0, 0, 'C', 1);


            $linhaDados = $linhaCabecalho + 5;

            $tcpdf->SetFillColor(219, 219, 234);
            $tcpdf->SetTextColor(0, 0, 0);
//            $linhaDados = 50;
        }

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(50, 5, $value['Pesquisa']['ds_questionario'], 0, 0, 'L', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(60 + $x);
        $tcpdf->Cell(60, 5, $value['Cliente']['nm_pessoa'], 0, 0, 'L', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(120 + $x);
        $tcpdf->Cell(40, 5, $value['Usuario']['nm_usu'], 0, 0, 'L', 0);
//
//        $tcpdf->SetY($linhaDados);
//        $tcpdf->SetX(130 + $x);
//        $tcpdf->Cell(15, 5, $value['GlbQuestionarioResposta']['hora_inicio'], 0, 0, 'C', 0);
//
//        $tcpdf->SetY($linhaDados);
//        $tcpdf->SetX(145 + $x);
//        $tcpdf->Cell(15, 5, $value['GlbQuestionarioResposta']['hora_fim'], 0, 0, 'C', 0);

        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(160 + $x);
        $tcpdf->Cell(15, 5, $funcionalidades->formatarDataAp($value['GlbQuestionarioResposta']['dt_cad']), 0, 0, 'C', 0);


        $tcpdf->SetY($linhaDados);
        $tcpdf->SetX(175 + $x);

        switch ($value['GlbQuestionarioResposta']['status_atendimento']) {
            case 0:
                $tcpdf->SetTextColor(255, 178, 42);
                $tcpdf->Cell(20, 5, "Iniciado", 0, 0, 'C', 0);
//                $tcpdf->Image(WWW_ROOT.DS.'img/iniciado.png',175, $linhaDados,15);
//                $tcpdf->Image('iniciado.png',175 + $x, $linhaDados, 40, 40, '', '', '', false, 300, '', false, false, 1, false, false, false);

                break;
            case 1:
                $tcpdf->SetTextColor(256, 0, 0);
                $tcpdf->Cell(20, 5, "Sem Contato", 0, 0, 'C', 0);
                break;
            case 2:
                $tcpdf->SetTextColor(154, 200, 16);
                $tcpdf->Cell(20, 5, "Concluído", 0, 0, 'C', 0);
                break;
        }




//        ob_end_clean();
//
//        $data_relatorio = date("d_m_Y");
//
//        $tcpdf->Output('relatorio_atendimentos_' . $data_relatorio . '.pdf', 'I');
//        ob_end_flush();
//        $totalGeral += $value[0]['vlr_total_pedido'];


        $tcpdf->SetFillColor(219, 219, 234);
        $tcpdf->SetTextColor(0, 0, 0);

        $linhaDados = $linhaDados + 5;
    }
    if ($tcpdf->GetY() <= 240) {
        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Iniciado:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $iniciado, 0, 0, 'R');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Sem Contato:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $semContato, 0, 0, 'R');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Concluído:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $concluido, 0, 0, 'R');
    } else {
        $tcpdf->AddPage();
        $linhaDados = $linhaCabecalho + 5;


        $tcpdf->SetFont($textfont, 'B', 5);
        $tcpdf->SetY(10);
        $tcpdf->SetX(165);
        $tcpdf->Cell(30, 5, "Emissão:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 5);
        $tcpdf->SetY(10);
        $tcpdf->SetX(180);
        $tcpdf->Cell(30, 5, date("d/m/Y") . " " . $hora, 0, 0, 'L');


        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Iniciado:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 10);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $iniciado, 0, 0, 'R');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Sem Contato:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 15);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $semContato, 0, 0, 'R');

        $tcpdf->SetFont($textfont, 'B', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(10 + $x);
        $tcpdf->Cell(15, 5, "Total Concluído:", 0, 0, 'L');

        $tcpdf->SetFont($textfont, '', 8);
        $tcpdf->SetY($linhaDados + 20);
        $tcpdf->SetX(50 + $x);
        $tcpdf->Cell(30, 5, $concluido, 0, 0, 'R');
    }


//    ob_start();
    ob_end_clean();

    $data_relatorio = date("d_m_Y");

    $tcpdf->Output('relatorio_atendimentos_' . $data_relatorio . '.pdf', 'I');
    ob_end_flush();
} else if ($tipo_arquivo == 'GRAFICO') {
    App::import('Component', 'Funcionalidades');
    $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());
    $iniciado = 0;
    $semContato = 0;
    $concluido = 0;

    foreach ($atendimentos as $value) {
        switch ($value['GlbQuestionarioResposta']['status_atendimento']) {
            case(0):
                $iniciado += 1;
                break;
            case(1):
                $semContato += 1;
                break;
            case(2):
                $concluido += 1;
                break;
        }
        $usuario[$value['GlbQuestionarioResposta']['cd_usu_cad']]['total'] = @$usuario[$value['GlbQuestionarioResposta']['cd_usu_cad']]['total'] + 1;
        $usuario[$value['GlbQuestionarioResposta']['cd_usu_cad']]['nome'] = $value['Usuario']['nm_usu'];
    }
    $cores = array('436EEE', '0000EE', '1874CD', '36648B', '00688B', '6CA6CD', '4A708B', '607B8B', '00868B', '008B8B', '528B8B', '008B45', '008B00', '8B8B7A', '828282', 'CFCFCF', '4F4F4F', '008B8B', '4876FF', '104E8B', '00688B', '9FB6CD', '00B2EE', '4F94CD');
//    $cores = array('0000FF', 'A52A2A', 'FFD700', '8A2BE2', '008B45', '008B8B', 'FF7F00', 'B03060', '7B68EE', '6B8E23', 'CD5C5C', 'FF69B4', '8B8989', '104E8B', '00868B', '8B0000', '8B008B', '9ACD32', 'FFA07A', '5D478B', '000080', 'FFDAB9', '6B8E23', '5D478B');
    ?>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>

    <?php
    if (empty($data_in)) {
        $data_in = '01/01/1900';
    }
    if (empty($data_fim)) {
        $data_fim = date('d/m/Y');
    }
    ?>
    <div style="width: 100%; text-align: center; font-size: 18px;"><b>Relatório de Atendimentos</b><br>Período de <?php echo $data_in . ' a ' . $data_fim; ?></div>
    <br><br>
    <div style="width: 100%; text-align: center; font-size: 18px;"><b>Por Status</b></div>

    <script type="text/javascript">
        google.load("visualization", "1", {packages: ["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Sem Contato', <?php echo $semContato; ?>],
                ['Concluído', <?php echo $concluido; ?>],
                ['Iniciado', <?php echo $iniciado; ?>]
            ]);

            var options = {'title': '',
                is3D: true,
                colors: ['#E52400', '#9AC810', '#FFB22A'],
                'width': 700,
                'height': 400};

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }
    </script>
    <div id="piechart_3d" style="left: 30%; max-width: 500px;"></div>
    <div style="width: 100%; text-align: center; font-size: 18px;"><b>Por Atendente</b></div>

    <script type="text/javascript">
        google.load('visualization', '1.0', {'packages': ['corechart']});
        google.setOnLoadCallback(drawChart);
        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Element', 'Atendimento(s)', {role: 'style'}, {role: 'annotation'}],
    <?php
    $cor = 0;
    foreach ($usuario as $key => $valor) {
        $valor['nome'] = explode(' ', $valor['nome']);
        $valor['nome'] = $valor['nome'][0];
        ?>
                    ['<?php echo $valor['nome']; ?>', <?php echo $valor['total']; ?>, 'color: <?php echo $cores[$cor]; ?>',<?php echo $valor['total']; ?>],
        <?php
        $cor++;
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
                hAxis: {title: '', titleTextStyle: {color: 'red'}}
            };
            var chart = new google.visualization.ColumnChart(document.getElementById('chart_col_atendente'));
            chart.draw(view, options);


        }
    </script>
    <div id="chart_col_atendente" style="width: 900px; height: 500px;"></div>

    <div style="width: 100%; text-align: center; font-size: 18px;"><b>Por Perguntas</b></div>
    <?php
    foreach ($respostasGrafico as $cod_perg => $value) {
        foreach ($value as $key => $valor) {
            $cor = 0;
            ?>
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages': ['corechart']});
                google.setOnLoadCallback(drawChart);
                function drawChart() {

                    var data = google.visualization.arrayToDataTable([
                        ['Element', 'Atendimento(s)', {role: 'style'}, {role: 'annotation'}],
                        <?php foreach($valor as $resposta => $total) { ?>
                        ['<?php echo $resposta; ?>', <?php echo $total; ?>, 'color: <?php echo $cores[$cor]; ?>',<?php echo $total; ?>],
                        <?php
                            $cor++;
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
                        title: '<?php echo trim($key); ?>',
                        legend: {position: "none"},
                        hAxis: {title: '', titleTextStyle: {color: 'red'}}
                    };
                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_col_perguntas<?php echo $cod_perg; ?>'));
                    chart.draw(view, options);


                }
            </script>
            <div id = "chart_col_perguntas<?php echo $cod_perg; ?>" style = "width: 900px; height: 500px;" > </div>

            <?php
        }
    }
}
?>
