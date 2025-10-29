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
    	width: 60%;
    	align: center;
    	margin-left: auto;
    	margin-right: auto;
    	border: 1px solid black;
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
	
	.cd-top {
	    background: #35CCFF url("/SysApp/app/webroot/img/cd-top-arrow.svg") no-repeat scroll center 50%;
	    bottom: 100px;
	    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
	    display: inline-block;
	    height: 40px;
	    opacity: 0;
	    overflow: hidden;
	    position: fixed;
	    right: 10px;
	    text-indent: 100%;
	    transition: opacity 0.3s ease 0s, visibility 0s ease 0.3s;
	    visibility: hidden;
	    white-space: nowrap;
	    width: 40px;
	    z-index: 10;
	    -moz-border-radius-bottomright:5px;
		-webkit-border-bottom-right-radius:5px;
		border-bottom-right-radius:5px;
		-moz-border-radius-bottomleft:5px;
		-webkit-border-bottom-left-radius:5px;
		border-bottom-left-radius:5px;
		-moz-border-radius-topright:5px;
		-webkit-border-top-right-radius:5px;
		border-top-right-radius:5px;
		-moz-border-radius-topleft:5px;
		-webkit-border-top-left-radius:5px;
		border-top-left-radius:5px;
	}
	.cd-top.cd-is-visible, .cd-top.cd-fade-out, .no-touch .cd-top:hover {
	    transition: opacity 0.3s ease 0s, visibility 0s ease 0s;
	}
	.cd-top.cd-is-visible {
	    opacity: 1;
	    visibility: visible;
	}
	.cd-top.cd-fade-out {
	    opacity: 0.5;
	}
	.cd-top:hover {
	    background-color: #35CCFF;
	    opacity: 1;
	}
	@media only screen and (min-width: 768px) {
	.cd-top {
	    bottom: 20px;
	    right: 20px;
	}
	}
	@media only screen and (min-width: 1024px) {
	.cd-top {
	    bottom: 30px;
	    height: 40px;
	    right: 30px;
	    width: 40px;
	}
	}
</style>
<meta name="viewport" content="width=min-device-width, initial-scale=0.7, maximum-scale=1, user-scalable=yes">
<script type="text/javascript">
jQuery(document).ready(function($){
	var offset = 300,
		offset_opacity = 1200,
		scroll_top_duration = 700,
		$back_to_top = $('.cd-top');

	$(window).scroll(function(){
		( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
		if( $(this).scrollTop() > offset_opacity ) { 
			$back_to_top.addClass('cd-fade-out');
		}
	});

	$back_to_top.on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, scroll_top_duration
		);
	});

});
</script>
<div id="pai">
<body id="voltarTopo">
<table id="cabecalhoTabela" width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center; border-color:red">
    <tr>
        <td style="font-size: 24px;">Relatório de Or&ccedil;amento por Hora<br></td>
    </tr>
    <tr>
    	<td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
    	<b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>
    	
    </tr>
</table>
<br>
  	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_vendas_hora')"/>
<br>
<div class="contentRelatorio">
  	<table width="50%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
  	<?php 
      $totalGeralQuantidade = 0;
      $totalGeralTotalPedido = 0;
      $filialAnterior = -1;
        
       if($dadosRelatorio != FALSE){
          if(isset($dadosRelatorio)){
        	foreach ($dadosRelatorio as $value){
        		$totalGeralQuantidade += $value['quantidade'];
        		$totalGeralTotalPedido += $value['vlr_total_pedido'];
    ?>
    <?php   
       		if($filialAnterior === $value['cd_filial']){
    ?>
       	
       	<tr>
       		<td><?php echo utf8_encode($value['hora']); ?></td>
       		<td><?php echo $value['quantidade']; ?></td>
       		<td><?php echo number_format($value['vlr_total_pedido'], 2, ',', '.'); ?></td>
       	</tr>
        <?php 
       		}else{
       			if($filialAnterior != $value['cd_filial']){
       				foreach($dadosRelatorioFilial as $chave){
       					if($value['cd_filial'] == $chave['total_cd_filial']){
       	?>
       	<tr style="background-color:#FFFFFF;">
		   <td> </td>
		</tr>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
            <td><?php echo "Loja ".$value['cd_filial']; ?></td>
            <td>Quantidade</td>
            <td>Valor R$</td>
        </tr>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
        	<td></td>
            <td><?php echo $chave['total_quantidade']; ?></td>
            <td><?php echo number_format($chave['total_vlr_total_pedido'], 2, ',', '.'); ?></td>
        </tr>
        <tr>
       		<td><?php echo utf8_encode($value['hora']); ?></td>
       		<td><?php echo $value['quantidade']; ?></td>
       		<td><?php echo number_format($value['vlr_total_pedido'], 2, ',', '.'); ?></td>
       	</tr>

       	<?php 
       		$filialAnterior = $value['cd_filial'];
       					}
       				}
       			}
       		}
        }
        ?>
        <?php 
        	}
        }
        ?>
        <tr style="background-color:#BBE42F">
		   	<td>Total Geral :</td>
		   	<td><?php echo number_format($totalGeralQuantidade, 2, ',', '.');?> </td>
		  	<td><?php echo number_format($totalGeralTotalPedido, 2, ',', '.');?></td>
		</tr>
   </table>
</div>
		<?php 
		if($dadosRelatorio != FALSE){
			if(isset($dadosRelatorio)){
		?>
		<?php 
			}
		}
		?>
        <div>
        	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_vendas_hora')"/>
        </div>
</div>
        	<a class="cd-top" href="#0">Top</a>
</body>
