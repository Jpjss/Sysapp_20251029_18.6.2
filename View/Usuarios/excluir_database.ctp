<?php

if (isset($usuarios)) {
    $html  = '<table class="table-bordered table-hover" id="pesquisa">';
    $html .= '<thead>';
    $html .= '<tr style="background-color:#E4F4FD; height:35px;">';
    $html .= '<th>C&oacute;digo do Usu&aacute;rio</th>';
    $html .= '<th>Nome</th>';
    $html .= '<th>Login Utilizado</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    foreach ($usuarios as $valor) {
    	foreach($valor as $value){
	        $html .= '<tr>';
	        $html .= '<td style="padding-left:2%;">'.$value['cd_usuario']. '</td>';
	        $html .= '<td style="text-align:center; ">'.$value['nome_usuario']. '</td>';
	        $html .= '<td style="text-align:center; ">'.$value['login_usuario']. '</td>';
	        $html .= '<input type="hidden" id="cd_empresa" value="'.$cd_empresa.'"';
	        $html .= '</tr>';
    	}
    }
    $html .= '</tbody>';
    $html .= '</table>';
    
}else{
	echo "Sua busca n&atilde;o retornou nenhum resultado !";
}

echo $html;
