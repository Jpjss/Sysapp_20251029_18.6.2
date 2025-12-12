<?php
if (isset($listaUsuarios)) {
    $html = '<select style="min-width:500px" id="UsuariosPadUsu" name="data[Usuarios][box_usu]">';
    $html .= '<option value=""></>';
    foreach ($listaUsuarios as $value) {

        $html .= '<option value="' . $value[0]['cd_usu'] . '">' . strtoupper(utf8_encode($value[0]['nm_usu'])) . '</>';
    }
    $html .= '</select>';
}

echo $html;
?>
