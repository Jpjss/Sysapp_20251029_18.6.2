<?php

if (isset($filiais)) {
    $html = '<table id="tabela" border="1">';
    $html .= '<tr>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td style="width: 13px;"><input type="checkbox" id="marcarTodasFiliais" value="" /></td>';
    $html .= '<td>TODAS</td>';
    $html .= '</tr>';
    foreach ($filiais as $value) {
        $html .= '<tr>';
        $html .= '<td style="width: 13px;"><input type="checkbox" ' . $checked . ' class="todasFiliais" name="data[Relatorios][filiais][]" value="' . $value['filial']['cd_filial'] . '" /></td>';
        $html .= '<td>' . utf8_encode($value['filial']['nm_fant']) . '<td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
}
echo $html;
?>
