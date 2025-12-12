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
    	border: 1px solid black;
    }
    .geral{
    	width: 100%;
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
        <td style="font-size: 24px;">Relatório Fluxo de Caixa -  <?php echo $this->Session->read('Conexao.Ativa'); ?><br></td>
    </tr>
    <tr>
    	<td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
    	<b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>
    	
    </tr>
</table>
<br>
  	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_caixa')"/>
<br>
<div class="contentRelatorio">
  	<table width="50%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
  	<?php 
      $filialAnterior = -1;
      $dataAnterior = -1;
      $dataAnteriorConta = -1;
      $totalEntradasCaixa = 0;
      $totalDocsCaixa = 0;
      $totalRetiradaCaixa = 0;
      $movCaixa = 0;
      $i = 1;
      
       if($dadosRelatorio != FALSE){
          if(isset($dadosRelatorio)){
        	foreach ($dadosRelatorio as $key => $value){
    ?>
    		<?php if($filialAnterior != $value['cd_filial']){?>
    
			    <?php if($i != 1){?>
				       	<tr style="background: #FFFFFF; height: 50px;">
				       		<td></td>
				       	</tr>
				       	<?php }
				       		$i++;
				       	?>
				       	<!-- Inicio Dados Filial -->
				        <tr style="text-align: center; background: #32C7FF; border: 0px;">
							<td> Filial : <?php echo $value['cd_filial']; ?></td>
							<td></td>
							<td></td>
							<td></td>
				        </tr>
				        <!-- Fim Dados Filial -->
				        
				        <!-- Data da Opera��o -->
				       	<tr style="background: #CEC3BD; ">
				       		<td><b>
				       			Data Opera&ccedil;&atilde;o => <?php echo date('d/m/Y', strtotime($value['dt_mov'])); ?></b>
				       		</td>
				       		<td></td>
							<td></td>
							<td></td>
				       	</tr>
				       	<!-- Fim Data da Opera��o -->
				       	
				       	<!-- Caixa -->
				       	<tr style="background: #CEC3BD; ">
				       		<td><b>
				       			Caixa => <?php echo $value['cd_cx']; ?></b>
				       		</td>
				       		<td></td>
							<td></td>
							<td></td>
				       	</tr>
				       	<!-- Fim Caixa -->
				       	
				       	<!-- Saldo Caixa e Total Abertura -->
				        <tr style="text-align: center; background: #59a6d6; border: 0px;">
				        	<td><b>Saldo do Caixa : <?php echo number_format($value['saldo_caixa'], 2, ',', '.'); ?></b></td>
				            <td><b>Total Abertura : <?php echo number_format($value['vlr_abertura'], 2, ',', '.'); ?></b></td>
				            <td></td>
				            <td></td>
				        </tr>
				        <!-- Fim Saldo Caixa e Total Abertura -->
				        
				        <!-- Opera&ccedil;&atilde;o que Movimentaram o Caixa -->
				        <tr>
				        	<td><b>Opera&ccedil;&otilde;es que Movimentaram o Caixa</b></td>
							<td></td>
							<td></td>
							<td></td>
				        </tr>
				        <tr>
							<td><b>Opera&ccedil;&atilde;o</b></td>
							<td></td>
							<td><b>Valor</b></td>
							<td></td>
				       	</tr>
				        <!-- Opera&ccedil;&atilde;o que Movimentaram o Caixa -->
				        
    				<?php if($value['tp_movimento'] != -1){?>
    					<!-- Conte�do -->
				        <tr>
							<td><?php if($value['mov_caixa'] == 1 && $value['tp_transacao'] == 1){ 	echo utf8_encode($value['ds_movimento']."(-)"); }else{ echo utf8_encode($value['ds_movimento']."(+)"); } ?></td>
				        	<td></td>
							<td><?php echo number_format($value['vlr_mov'], 2, ',', '.'); ?></td>
							<td></td>
				       	</tr>
				        <!--  -->
				  <?php }?>
    		<?php 
	       		  $dataAnterior = $value['dt_mov'];
    			  $filialAnterior = $value['cd_filial'];
    			  }else{?>
    			  
    			  <?php if($dataAnterior != $value['dt_mov']){?>
    			  		<!-- Data da Opera��o -->
				       	<tr style="background: #CEC3BD; ">
				       		<td><b>
				       			Data Opera&ccedil;&atilde;o => <?php echo date('d/m/Y', strtotime($value['dt_mov'])); ?></b>
				       		</td>
				       		<td></td>
							<td></td>
							<td></td>
				       	</tr>
				       	<!-- Fim Data da Opera��o -->
				       	
				       	<!-- Caixa -->
				       	<tr style="background: #CEC3BD; ">
				       		<td><b>
				       			Caixa => <?php echo $value['cd_cx']; ?></b>
				       		</td>
				       		<td></td>
							<td></td>
							<td></td>
				       	</tr>
				       	<!-- Fim Caixa -->
				       	
				       	<!-- Saldo Caixa e Total Abertura -->
				        <tr style="text-align: center; background: #59a6d6; border: 0px;">
				        	<td><b>Saldo do Caixa : <?php echo number_format($value['saldo_caixa'], 2, ',', '.'); ?></b></td>
				            <td><b>Total Abertura : <?php echo number_format($value['vlr_abertura'], 2, ',', '.'); ?></b></td>
				            <td></td>
				            <td></td>
				        </tr>
				        <!-- Fim Saldo Caixa e Total Abertura -->
				        
				        <!-- Opera&ccedil;&atilde;o que Movimentaram o Caixa -->
				        <tr>
				        	<td><b>Opera&ccedil;&otilde;es que Movimentaram o Caixa</b></td>
							<td></td>
							<td></td>
							<td></td>
				        </tr>
				        <tr>
							<td><b>Opera&ccedil;&atilde;o</b></td>
							<td></td>
							<td><b>Valor</b></td>
							<td></td>
				       	</tr>
				        <!-- Opera&ccedil;&atilde;o que Movimentaram o Caixa -->
    			  	
    				<?php if($value['tp_movimento'] != -1){?>
    					<!-- Conte�do -->
				        <tr>
							<td><?php if($value['mov_caixa'] == 1 && $value['tp_transacao'] == 1){ 	echo utf8_encode($value['ds_movimento']."(-)"); }else{ echo utf8_encode($value['ds_movimento']."(+)"); } ?></td>
				        	<td></td>
							<td><?php echo number_format($value['vlr_mov'], 2, ',', '.'); ?></td>
							<td></td>
				       	</tr>
				        <!--  -->
				  <?php }?>
				        
				  <?php }else{?>
				        
				        <?php if($movCaixa  == $value['mov_caixa']){?>
				        
				        <!-- Saldo Caixa/Banco Fechamento Dif Movimento -->
				        <tr style="text-align: center; background: #59a6d6; border: 0px;">
				        	<td><b>Saldo do Caixa : <?php echo number_format((($totalEntradasCaixa + $totalRetiradaCaixa) + $value['saldo_caixa'] + $value['saldo_banco']), 2, ',', '.'); ?></b></td>
				            <td><b>Saldo Banco : <?php echo number_format($value['saldo_banco'], 2, ',', '.'); ?></b></td>
				            <td><b>Fechamento : <?php echo number_format($value['vlr_fechamento'] , 2, ',', '.'); ?></b></td>
				            <td><b>Dif. Movimento : <?php echo number_format(($value['vlr_fechamento'] - (($totalEntradasCaixa + $totalRetiradaCaixa) + $value['saldo_caixa'] + $value['saldo_banco'])) , 2, ',', '.');?></b></td>
				        </tr>
				        <!-- Saldo Caixa/Banco Fechamento Dif Movimento -->
				        
				        <!-- Movimento de confer&ecirc;ncia de Documentos -->
				        <tr>
				        	<td><b>Movimento de confer&ecirc;ncia de Documentos</b></td>
							<td></td>
							<td></td>
							<td></td>
				        </tr>
				        <tr>
							<td><b>Opera&ccedil;&atilde;o</b></td>
							<td></td>
							<td><b>Valor</b></td>
							<td>Qtde Doc</td>
				       	</tr>
				        <!-- Movimento de confer&ecirc;ncia de Documentos -->
				        
				        <!-- Conte�do -->
				        <tr>
							<td><?php if($value['mov_caixa'] == 1 && $value['tp_transacao'] == 1){ 	echo utf8_encode($value['ds_movimento']."(-)"); }else{ echo utf8_encode($value['ds_movimento']."(+)"); } ?></td>
							<td></td>
							<td><?php echo number_format($value['vlr_mov'], 2, ',', '.'); ?></td>
							<td><?php echo number_format($value['qtde_itens'], 0, ',', '.'); ?></td>
				       	</tr>
				       	<tr style="text-align: center; background: #59a6d6; border: 0px;">
				       		<td><b>Qtde Baixas: <?php echo number_format($value['qtde_baixa'], 2, ',', '.'); ?></b></td>
				       		<td><b>Troco Acumulado: <?php echo number_format($value['troco_acumulado'], 2, ',', '.'); ?></b></td>
				       		<td><b>Total Documento: <?php echo number_format(($totalDocsCaixa += $value['vlr_mov']), 2, ',', '.'); ?></b></td>
				       		<td></td>
				       	</tr>
				        <!--  -->
				        <?php if($i != 1){?>
				       	<tr style="background: #FFFFFF; height: 50px;">
				       		<td></td>
				       	</tr>
				       	<?php }
				       		$i++;
				       	?>
				        <?php 
				        $totalEntradasCaixa = 0;
				        $totalDocsCaixa = 0;
				        $totalRetiradaCaixa = 0;
				        ?>
				        
				        <?php }else{?>
		    				<?php if($value['tp_movimento'] != -1){?>
		    					<!-- Conte�do -->
						        <tr>
									<td><?php if($value['mov_caixa'] == 1 && $value['tp_transacao'] == 1){ 	echo utf8_encode($value['ds_movimento']."(-)"); }else{ echo utf8_encode($value['ds_movimento']."(+)"); } ?></td>
						        	<td></td>
									<td><?php echo number_format($value['vlr_mov'], 2, ',', '.'); ?></td>
									<td></td>
						       	</tr>
						        <!--  -->
								<?php         
									//Total de Entrada Caixa
							    	if($value['mov_caixa'] == 1 && $value['tp_transacao'] == 0){
							    		$totalEntradasCaixa += $value['vlr_mov'];
							    		 
							    		//Documentos Pag. a Vista
							    		if($value['mov_caixa'] == 1 && $value['totaliza_venda'] == 1){
							    			$totalDocsCaixa += $value['vlr_mov'];
							    		}
							    		//Fim Documentos Pag. a Vista
							    		 
							    	}
							    	//Fim Total de Entrada Caixa
							    	
							    	//Retirada de Caixa
							    	if($value['mov_caixa'] == 1 && $value['tp_transacao'] == 1){
							    		$totalRetiradaCaixa += ($value['vlr_mov'])* -1;
							    		
							    	}
							    	//Fim Retirada de Caixa
							    	
							    	//Documentos em Geral
							    	if($value['mov_caixa'] == 0 && $value['totaliza_venda'] == 1){
							    		$totalDocsCaixa += $value['vlr_mov'];
							    	}
							    	//Fim Documentos em Geral
								?>        
						  <?php }?>
				        <?php }?>
				  <?php }?>
    		<?php 
    			  $dataAnterior = $value['dt_mov'];
    			  $filialAnterior = $value['cd_filial'];
    			  }?>
    		
    <?php 	}?>

		<?php 
        }
        }else{
        	echo "<tr><td><h2>Sua busca n&atilde;o retornou resultados !</h2></td></tr>";
        }
        ?>
   </table>
</div>
        <div>
        	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_caixa')"/>
        </div>
</div>
        	<a class="cd-top" href="#0">Top</a>
</body>
