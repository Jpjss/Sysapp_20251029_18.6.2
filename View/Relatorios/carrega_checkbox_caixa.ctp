<?php 

if(isset($listaCaixas)){
	$html = "";
	$html .= '<tr>
                   <td style="width: 13px;"><input type="checkbox" id="marcaTodasCategorias" value="" checked=true onclick="javascript:marca()"/></td>
                   <td>TODAS AS CATEGORIAS</td>
              </tr>';
	foreach($listaCaixas as $value){
		$html .= "<tr>";
			$html .= "<td>";
				$html .= '<input type="checkbox" class="categorias" name="data[Relatorios][caixas][]" value="'.$value["cd_cx"].'" checked=true/>';
			$html .= "</td>";
			$html .= '<td>'.$value["cd_cx"].' - '.utf8_encode($value["ds_cx"]).'</td>';
		$html .= "</tr>";
	}
	echo $html;
}
