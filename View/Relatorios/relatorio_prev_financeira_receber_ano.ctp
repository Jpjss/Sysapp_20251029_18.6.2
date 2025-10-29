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
        <td style="font-size: 24px;">Relatório Previs&atilde;o Financeira a Receber Por Ano -  <?php echo $this->Session->read('Conexao.Ativa'); ?><br></td>
    </tr>
    <tr>
    	<td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
    	<b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>
    	
    </tr>
</table>

<br>
  	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_finaceira_receber')"/>
<br>

<div class="contentRelatorio" >

	<table width="50%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
	
		<?php 
		
			//variaveis para verificar as posicoes
			$anoAnterior=-1;
			$filialAnterior=-1;
			$i = 1;
			
			//variaveis totalizadoras
			$valorTotFilialVencto =0;
			$valorTotFilialPago=0;
			$valorTotFilialVlrAberto=0;
			$valorTotFilialQtde=0;
			
			$valorTotGeralVencto=0;
			$valorTotGeralPago=0;
			$valorTotGeralVlrAberto=0;
			$valorTotGeralQtde=0;
			
			//key dos dados do relatorio
			//chave dados dos totais por filial
			
			
			if($dadosRelatorio!=false){
				if(isset($dadosRelatorio)){
					foreach ($dadosRelatorio as $key => $value){
						
						$valorTotGeralPago += $value['valor_pago_principal'];
						$valorTotGeralQtde += $value['quantidade'];
						$valorTotGeralVencto += $value['valor_total'];
						$valorTotGeralVlrAberto += $value['valor_devedor'];
				
		
		?>
		<?php 
			
		if($anoAnterior ===$key['ano']){
				foreach($dadosRelatorioFilial as $chave){
					if($value['ano'] == $chave['ano']){
						
			
		?>
		
						<tr style="background: #FFFFFF; height: 20px;">
							<td colspan="12"></td>
						</tr>
								
						
						<!-- colunas do relatorio -->
							<tr style="text-align: center; background: #59a6d6; border: 0px;">
							   	<td>Ano</td>
							    <td>Valor Total Vencimento</td>
							    <td>Valor Pago</td>
							    <td>Valor em Aberto</td>
							    <td>% Inadipl&ecirc;ia</td>
							    <td>Quantidade</td>
							</tr>
							
						<!-- recebendo os fields -->
				
							<tr>
								<td><?php echo $value['ano']; ?></td>
								<td><?php echo number_format($value['valor_total'],2, ',', '.'); ?></td>
								<td><?php echo number_format($value['valor_pago_principal'], 2, ',', '.'); ?></td>
								<td><?php echo number_format($value['valor_devedor'], 2, ',', '.'); ?></td>
								<td><?php echo number_format($value['percentual_inadimplencia'], 2, ',', '.'); ?></td>
								<td><?php echo $value['quantidade']; ?></td>
			       			</tr>
			       			
			       						
			
		<?php 
		
					}
				}
			}else{
				
		?>
		
		<?php if($i != 1){ ?>
					       	<tr style="background: #FFFFFF; height: 50px;">
			       				<td colspan="12"></td>
			       			</tr>
		<?php }
			$i++;
		?>
		
		
				       	<!-- Totais Por ano -->
				        <tr style="text-align: center; background: #32C7FF; border: 0px;">
							<td> Total Ano : <?php echo $value['ano']; ?></td>
							<td><?php echo number_format($value['valor_total'],2, ',', '.'); ?></td>
							<td><?php echo number_format($value['valor_pago_principal'], 2, ',', '.'); ?></td>
							<td><?php echo number_format($value['valor_devedor'], 2, ',', '.'); ?></td>
							<td></td>
							<td><?php echo $value['quantidade']; ?></td>
				        </tr>
		
	     				<!-- colunas do relatorio segunda remessa-->
							<tr style="text-align: center; background: #59a6d6; border: 0px;">
							   	<td>Ano</td>
							    <td>Valor Total Vencimento</td>
							    <td>Valor Pago</td>
							    <td>Valor em Aberto</td>
							    <td>% Inadipl&ecirc;ia</td>
							    <td>Quantidade</td>
							</tr>
							
						<!-- recebendo os fields -->		
							<tr>
								<td><?php echo $value['ano']; ?></td>
								<td><?php echo number_format($value['valor_total'],2, ',', '.'); ?></td>
								<td><?php echo number_format($value['valor_pago_principal'], 2, ',', '.'); ?></td>
								<td><?php echo number_format($value['valor_devedor'], 2, ',', '.'); ?></td>
								<td><?php echo number_format($value['percentual_inadimplencia'], 2, ',', '.'); ?></td>
								<td><?php echo $value['quantidade']; ?></td>
			       			</tr>
		
		
		<?php 
				$anoAnterior = $value['ano'];
			}
		}
		
		?>
		
		
		    <tr style="background-color:#BBE42F">
			<td> TOTAL </td>
			<td><?php echo number_format($value['valor_total'],2, ',', '.'); ?></td>
			<td><?php echo number_format($value['valor_pago_principal'], 2, ',', '.'); ?></td>
			<td><?php echo number_format($value['valor_devedor'], 2, ',', '.'); ?></td>
			<td></td>
			<td><?php echo $value['quantidade']; ?></td>
		</tr>
		<?php 
        }
        }else{
        	echo "<tr><td><h2>Sua busca n&atilde;o retornou resultados !</h2></td></tr>";
        }
        ?>
		
		
	</table>
</div>
    <div>
        	<input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_finaceira_receber')"/>
    </div>
</div>
    <a class="cd-top" href="#0">Top</a>
</body>
