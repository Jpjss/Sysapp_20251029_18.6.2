<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');

echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>
<style>
    body{
    	font-size: 12px;
    }
    #pai{
		align: center;
    }
    #cabecalhoEmissao {
    	text-align: right;
    	padding-right: 150px;
    }
    #cabecalhoPeriodo{
    	text-align: right;
    	padding-right: 150px;
    }
    #cabecalhoTabela{
    	width: 100%;
    }
    .contentRelatorio{
    	width: 100%;
    	align: center;
    	margin-left: auto;
    	margin-right: auto;
    }
    .geral{
    	width: 60%;
    	align: center;
    	margin-left: auto;
    	margin-right: auto;
    }
    #voltar{
    	width: 10%;
    	margin-left: auto;
    	margin-right: auto;
    }
    .voltarTopo {
	    background: none repeat scroll 0 0 #000000 !important;
	    bottom: 20px !important;
	    color: #FFFFFF;
	    text-align: center;
	    display: block;
	    font-size: 10px;
	    font-weight: bold;
	    height: 20px;
	    position: fixed;
	    right: 10px;
	    text-transform: uppercase;
	    width: 50px;
	}
</style>
<script type="text/javascript">
var $j = jQuery.noConflict();
$j(document).ready(function() {
	$j(".voltarTopo").hide();
		$j(function () {
				$j(window).scroll(function () {
					if ($j(this).scrollTop() > 300) {
						$j('.voltarTopo').fadeIn();
					} else {
						$j('.voltarTopo').fadeOut();
					}
				});
			$j('.voltarTopo').click(function() {
				$j('body,html').animate({scrollTop:0},600);
			}); 
		});
});
</script>
<div id="pai">
<body id="voltarTopo">
<table id="cabecalhoTabela" width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center; border-color:red">
    <tr>
        <td style="font-size: 24px;">Relatório Comparativo de Vendas<br></td>
    </tr>
    <tr>
    	<td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b></td>
    	
    </tr>
</table>
<br>
  	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/comparativo_vendas')"/>
<br>
<div class="contentRelatorio">
  	<table width="50%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
  	<?php 
       if($dadosRelatorio != FALSE){
    ?>
    <tr style="text-align: center; background: #59a6d6; border: 0px;">
    	<td>Filial</td>
    	<td style="text-align:center;">Per&iacute;odo 1 <br/> <?php echo $data_formatada_inicial; ?> at&eacute; <?php echo $data_formatada_final; ?></td>
    	<td style="text-align:center;">Per&iacute;odo 2 <br/> <?php echo $data_formatada_inicial_2; ?> at&eacute; <?php echo $data_formatada_final_2; ?></td>
    	<td>Diferen&ccedil;a %</td>
    </tr>
    <?php foreach($dadosRelatorio as $value){?>
    <tr style="text-align:center;">
    	<td><?php echo utf8_encode($value['nm_fant']); ?></td>
    	<td style="text-align:center;"><?php echo "R$ ".number_format($value['total_venda_1'], 2, ',', '.'); ?></td>
	  	<td style="text-align:center;"><?php echo "R$ ".number_format($value['total_venda_2'], 2, ',', '.'); ?></td>
	  	<td><?php echo number_format($value['percent_diferenca'], 2, ',', '.'); ?></td>
    </tr>
    <?php    	
    	  }
    ?>
    <tr style="background-color:#BBE42F">
	   	<td>Total Geral :</td>
	   	<td style="text-align:center;"><?php echo "R$ ".number_format($totalGeralPeriodo1, 2, ',', '.');?></td>
	   	<td style="text-align:center;"><?php echo "R$ ".number_format($totalGeralPeriodo2, 2, ',', '.');?></td>
	<?php if($totalGeralPeriodo1 != 0){?>
	   	<td><?php echo number_format((($totalGeralPeriodo2/$totalGeralPeriodo1) -1) * 100, 2, ',', '.');?></td>
	<?php }else{?>
		<td><?php echo number_format(0, 2, ',', '.');?></td>
	<?php }?>
	</tr>
    <?php 
       }else{
        	echo "<tr><td><h2>Sua busca n&atilde;o retornou resultados !</h2></td></tr>";
        }
    ?>
   </table>
</div>
        <div>
        	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/comparativo_vendas')"/>
        </div>
</div>
        	<input type="button" class="voltarTopo" onclick="$j('html,body').animate({scrollTop: $j('#voltarTopo').offset().top}, 1000);" value="^" >
</body>
