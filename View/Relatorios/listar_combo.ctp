<?php

if (isset($listaFamilia)) {
    $html = '<select style="min-width:500px" id="GlbRelatorioFamilia" name="data[GlbRelatorio][familia]">';
    $html .= '<option value=""></>';
    foreach ($listaFamilia as $value) {

        $html .= '<option value="' . $value['EstProdutoFamilia']['cd_categoria'] . '-' . $value['EstProdutoFamilia']['cd_familia'] . '">' . strtoupper(utf8_encode($value['EstProdutoFamilia']['ds_familia'])) . '</>';
    }
    $html .= '</select>';
}

if (isset($listaGrupo)) {
    $html = '<select id="GlbRelatorioGrupo" name="data[GlbRelatorio][categoria]">';
    $html .= '<option value=""></>';
    foreach ($listaGrupo as $key => $value) {

        $html .= '<option value="'.$key.'">' . strtoupper(utf8_encode($value)) . '</>';
    }
    $html .= '</select>';
}

echo $html;
?>
