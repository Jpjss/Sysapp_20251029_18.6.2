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
    jQuery(document).ready(function ($) {
        var offset = 300,
                offset_opacity = 1200,
                scroll_top_duration = 700,
                $back_to_top = $('.cd-top');

        $(window).scroll(function () {
            ($(this).scrollTop() > offset) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
            if ($(this).scrollTop() > offset_opacity) {
                $back_to_top.addClass('cd-fade-out');
            }
        });

        $back_to_top.on('click', function (event) {
            event.preventDefault();
            $('body,html').animate({
                scrollTop: 0,
            }, scroll_top_duration
                    );
        });

    });
</script>

<div id="pai">
    <body id="voltarTopo">
        <table id="cabecalhoTabela" width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center; border-color:red">
            <tr>
                <td style="font-size: 24px;">Relatório Contas a Pagar Filial/Data -  <?php echo $this->Session->read('Conexao.Ativa'); ?><br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
                    <b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>

            </tr>
        </table>
        <br>
        <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_financeira_pagar')"/>
        <br>
        <div class="contentRelatorio">
            <table width="50%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
  	<?php 
      $filialAnterior = -1;
      $dataAnterior = -1;
      $i = 1;
      $valorGeral = 0;
      $descDescProvGeral = 0;
      $jurosMultaCustasGeral = 0;
      $pagtoGeral = 0;
      $saldoGeral = 0;
      
       if($dadosRelatorio != FALSE){
          if(isset($dadosRelatorio)){
        	foreach ($dadosRelatorio as $key => $value){
        		$valorGeral += $value['dup_vlr'];
        		$descDescProvGeral += $value['dup_vlr_desc_provi'];
        		$jurosMultaCustasGeral += ($value['dup_vlr_juros']+$value['dup_vlr_multa']+$value['dup_vlr_custas']);
        		$pagtoGeral += $value['dup_vlr_pagto'];
        		$saldoGeral += $value['dup_vlr_saldo'];
    ?>
    <?php   
       		if($filialAnterior === $value['cd_filial']){
       			foreach($dadosRelatorioFilial as $chave){
       				if($value['cd_filial'] == $chave['cd_filial']){
    ?>
					    <?php 
					    if($dataAnterior != $value['dup_dt_vencto']){
					    	
					    ?>  
                <tr style="background: #FFFFFF; height: 20px;">
                    <td colspan="12"></td>
                </tr>
                
                <tr style="background: #CEC3BD; ">
                    <td colspan="6" >
                        Data de Vencimento => <?php echo date('d/m/Y', strtotime($value['dup_dt_vencto'])); ?>
                    </td>

                <?php 
                    foreach ($dadosRelatorioTotalData as $valueData){	

                    if ($valueData['codigo_filial'] == $value['cd_filial']) { 

                    if ($valueData['data_vencimento'] == $value['dup_dt_vencto']) { 

                    ?>
                    <td><?php echo "R$".number_format($valueData['dup_vlr_pdata'], 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format(($valueData['dup_vlr_desc_pdata']+$valueData['dup_vlr_desc_provi_pdata']), 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format(($valueData['dup_vlr_juros_pdata']+$valueData['dup_vlr_multa_pdata']+$valueData['dup_vlr_custas_pdata']), 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($valueData['dup_vlr_pagto_pdata'], 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($valueData['dup_vlr_saldo_pdata'], 2, ',', '.'); ?></td>
                </tr>

                 <?php 
                                    }
                                }
                            }
                 ?>

                <tr style="text-align: center; background: #59a6d6; border: 0px;">
                    <td>N&uacute;mero</td>
                    <td>Parc</td>
                    <td>T&iacute;tulo</td>
                    <td>Fornecedor</td>
                    <td>Dt. Emiss&atilde;o</td>
                    <td>Dt. Pagto</td>
                    <td>Valor</td>
                    <td>Desc.+ Desc.Prov.</td>
                    <td>Juros + Multa + Custas</td>
                    <td>Pagto</td>
                    <td>Saldo</td>
                </tr>
					    <?php 
					    }
					    ?>
                <tr>
                    <td><?php echo $value['dup_numero']; ?></td>
                    <td><?php echo $value['parc']; ?></td>
                    <td><?php echo utf8_encode($value['numero_titulo']); ?></td>
                    <td><?php echo utf8_encode($value['cod_nome_forn']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($value['dup_dt_emis'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($value['dup_dt_pagto'])); ?></td>
                    <td><?php echo number_format($value['dup_vlr'],2, ',', '.'); ?></td>
                    <td><?php echo number_format(($value['dup_vlr_desc'] + $value['dup_vlr_desc_provi']),2, ',', '.'); ?></td>
                    <td><?php echo number_format(($value['dup_vlr_juros'] + $value['dup_vlr_multa'] + $value['dup_vlr_custas']),2, ',', '.'); ?></td>
                    <td><?php echo number_format($value['dup_vlr_pagto'],2, ',', '.'); ?></td>
                    <td><?php echo number_format($value['dup_vlr_saldo'], 2, ',', '.'); ?></td>
                </tr>

        <?php $dataAnterior = $value['dup_dt_vencto']; //atribui a data que acabamos de usar
        
        			}//if($value['cd_filial'] == $chave['cd_filial'])
       			} //foreach($dadosRelatorioFilial as $chave)
                        
       		}else{
       			if($filialAnterior != $value['cd_filial']){
       				foreach($dadosRelatorioFilial as $chave){
       					if($value['cd_filial'] == $chave['cd_filial']){
       						 
       	?>
       	<?php if($i != 1){?>
                <tr style="background: #FFFFFF; height: 50px;">
                    <td colspan="12"></td>
                </tr>
       	<?php }
       		$i++;
       	?>
                <!-- Inicio Dados Filial -->
                <tr style="text-align: center; background: #32C7FF; border: 0px;">
                    <td colspan="6" > Filial : <?php echo $value['nm_fant']; ?></td>
                    <td><?php echo "R$".number_format($chave['dup_vlr'], 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format(($chave['dup_vlr_desc']+$chave['dup_vlr_desc_provi']), 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format(($chave['dup_vlr_juros']+$chave['dup_vlr_multa']+$chave['dup_vlr_custas']), 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($chave['dup_vlr_pagto'], 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($chave['dup_vlr_saldo'], 2, ',', '.'); ?></td>
                </tr>

                <!-- Fim Dados Filial -->
                <tr style="background: #CEC3BD; ">
                    <td colspan="6" >
                        Data de Vencimento => <?php echo date('d/m/Y', strtotime($value['dup_dt_vencto'])); ?>
                    </td>

                 <?php 
    
            foreach ($dadosRelatorioTotalData as $valueData){	
                   
                    if ($valueData['codigo_filial'] == $value['cd_filial']) { 
                        
                         if ($valueData['data_vencimento'] == $value['dup_dt_vencto']) { 
                                                 
                    ?>
                    <td><?php echo "R$".number_format($valueData['dup_vlr_pdata'], 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format(($valueData['dup_vlr_desc_pdata']+$valueData['dup_vlr_desc_provi_pdata']), 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format(($valueData['dup_vlr_juros_pdata']+$valueData['dup_vlr_multa_pdata']+$valueData['dup_vlr_custas_pdata']), 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($valueData['dup_vlr_pagto_pdata'], 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($valueData['dup_vlr_saldo_pdata'], 2, ',', '.'); ?></td>
                </tr>

                 <?php 
                                    }
                                }
                            }
                 ?>

                </tr>

                <tr style="text-align: center; background: #59a6d6; border: 0px;">
                    <td>N&uacute;mero</td>
                    <td>Parc</td>
                    <td>T&iacute;tulo</td>
                    <td>Fornecedor</td>
                    <td>Dt. Emiss&atilde;o</td>
                    <td>Dt. Pagto</td>
                    <td>Valor</td>
                    <td>Desc.+ Desc.Prov.</td>
                    <td>Juros + Multa + Custas</td>
                    <td>Pagto</td>
                    <td>Saldo</td>
                </tr>
                <tr>
                    <td><?php echo $value['dup_numero']; ?></td>
                    <td><?php echo $value['parc']; ?></td>
                    <td><?php echo utf8_encode($value['numero_titulo']); ?></td>
                    <td><?php echo utf8_encode($value['cod_nome_forn']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($value['dup_dt_emis'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($value['dup_dt_pagto'])); ?></td>
                    <td><?php echo number_format($value['dup_vlr'],2, ',', '.'); ?></td>
                    <td><?php echo number_format(($value['dup_vlr_desc'] + $value['dup_vlr_desc_provi']),2, ',', '.'); ?></td>
                    <td><?php echo number_format(($value['dup_vlr_juros'] + $value['dup_vlr_multa'] + $value['dup_vlr_custas']),2, ',', '.'); ?></td>
                    <td><?php echo number_format($value['dup_vlr_pagto'],2, ',', '.'); ?></td>
                    <td><?php echo number_format($value['dup_vlr_saldo'], 2, ',', '.'); ?></td>
                </tr>
       	<?php 
       		$filialAnterior = $value['cd_filial'];
       		$dataAnterior = $value['dup_dt_vencto'];
       		 
       					}
       				}
       			}
       		}
        }
        ?>

                <tr style="background-color:#BBE42F">
                    <td> TOTAL </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?php echo "R$".number_format($valorGeral, 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($descDescProvGeral, 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($jurosMultaCustasGeral, 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($pagtoGeral, 2, ',', '.'); ?></td>
                    <td><?php echo "R$".number_format($saldoGeral, 2, ',', '.'); ?></td>
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
            <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_financeira_pagar')"/>
        </div>
</div>
<a class="cd-top" href="#0">Top</a>
</body>
