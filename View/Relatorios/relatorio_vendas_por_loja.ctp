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
        align: right; 
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
    }
    #voltar{
        width: 10%;
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
                <td style="font-size: 24px;">Relatório Vendas por Loja<br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
                    <b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>

            </tr>
        </table>
        <br>
        <div class="contentRelatorio">
            <div>
                <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_por_loja')"/>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
                <tr style="text-align: center; background: #59a6d6; border: 0px;">
                    <td></td>
                    <td>Pecas</td>
                    <td>Devol.</td>
                    <td>Saldo</td>
                    <td>Qtd. Vd.</td>
                    <td>Meta R$</td>
                    <td>V. Vendido R$</td>
                    <td>% Part.</td>
                    <td>% Real</td>
                    <td>Desc. R$</td>
                    <td>P/S</td>
                    <td>Trocas</td>
                    <td>Ticket M.</td>
                    <td>V. Medio Prod.</td>
                    <td>Filial</td>
                </tr>
        <?php 
        $totalGeralVlrLanc = "";
        $totalGeralItens = "";
        $totalGeralVlrItensTroca = "";
        $totalGeralVlrItensSaldo = "";
        $totalGeralVendas = "";
        $totalGeralMetas = "";
        $totalGeralPercentReal = "";
        $totalGeralVlrDesconto = "";
        $totalGeralVendasTrocas = "";
        $totalGeralTicketM = "";
        $totalGeralVMedioProd = "";
        
        if($dadosRelatorioFilial != FALSE){
        	if(isset($dadosRelatorioFilial)){
			        foreach($dadosRelatorioFilial as $chave){ 
			        	$totalGeralVlrLanc += $chave['vlr_lanc'];
			        	$totalGeralItens += $chave['itens'];
			        	$totalGeralVlrItensTroca += $chave['itens_troca'];
			        	$totalGeralVlrItensSaldo += $chave['itens_saldo'];
			        	$totalGeralVendas += $chave['vendas'];
			        	$totalGeralMetas += $chave['metas'];
			        	$totalGeralPercentReal += $chave['percent_real'];
			        	$totalGeralVlrDesconto += $chave['vlr_desconto'];
			        	$totalGeralVendasTrocas += $chave['vendas_trocas'];
			        	$totalGeralTicketM += $chave['ticket_medio'];
			        	$totalGeralVMedioProd += $chave['vlr_medio_prod'];
			        	?>
                <tr>
                    <td>
			        		<?php 
			        			echo utf8_encode($chave['nm_fant']).'<br>';
			        		?>
                    </td>
                    <td>
			        		<?php echo number_format($chave['itens'],0,',','.'); ?>
                    </td>
                    <td>
			        		<?php echo $chave['itens_troca']; ?>
                    </td>
                    <td>
			        		<?php echo number_format($chave['itens_saldo'],0,',','.'); ?>
                    </td>
                    <td>
			        		<?php echo number_format($chave['vendas'],0,',','.'); ?>
                    </td>
                    <td>
			        		<?php echo number_format($chave['metas'], 2, ',', '.'); ?>
                    </td>
                    <td>
			        		<?php echo number_format($chave['vlr_lanc'], 2, ',', '.'); ?>
                    </td>

		<?php if(!$totalGeralValorVendido == 0){?>
                    <td><?php echo number_format(($chave['vlr_lanc']/$totalGeralValorVendido * 100),2,',','.'); ?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?>

                    <td><?php echo number_format($chave['percent_real'],2,',','.'); ?></td>
                    <td><?php echo number_format($chave['vlr_desconto'], 2, ',', '.'); ?></td>

                <?php if(!($chave['vendas'] == 0)){?>
                    <td><?php echo (number_format($chave['itens'] / $chave['vendas'],2,',','.')); ?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?> 

                    <td><?php echo $chave['vendas_trocas']; ?></td>
                    <td><?php echo number_format($chave['ticket_medio'],2,',','.') ?></td>
                    <td><?php echo number_format($chave['vlr_medio_prod'],2,',','.') ?></td>
                    <td><?php echo utf8_encode($chave['nm_fant']).'<br>'?></td>
                </tr>
			        <?php 
			        }
			        ?>

                <tr style="background-color:#BBE42F">
                    <td>Total Geral :</td>
                    <td><?php echo number_format($totalGeralItens,0,',','.');?> </td>
                    <td><?php echo number_format($totalGeralVlrItensTroca,0,',','.');?> </td>
                    <td><?php echo number_format($totalGeralVlrItensSaldo,0,',','.');?> </td>
                    <td><?php echo number_format($totalGeralVendas,0,',','.');?></td>
                    <td><?php echo number_format($totalGeralMetas, 2, ',', '.');?></td>
                    <td><?php echo number_format($totalGeralVlrLanc, 2, ',', '.');?></td>

                <?php if(!$totalGeralValorVendido == 0){?>
                    <td><?php echo number_format(($totalGeralVlrLanc/$totalGeralValorVendido * 100),2,',','.');?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?>

                <?php if(!$totalGeralMetas == 0){?>
                    <td><?php echo number_format(($totalGeralVlrLanc/$totalGeralMetas * 100),2,',','.');?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?>

                    <td><?php echo number_format($totalGeralVlrDesconto, 2, ',', '.')?></td>

                <?php if(!$totalGeralVendas == 0){?>
                    <td><?php echo number_format($totalGeralItens/$totalGeralVendas, 2, ',' ,'.');?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?>

                    <td><?php echo number_format($totalGeralVendasTrocas); ?></td>

                <?php if(!($totalGeralVendas - $totalGeralVendasTrocas) == 0){?>
                    <td><?php echo number_format($totalGeralVlrLanc/($totalGeralVendas - $totalGeralVendasTrocas), 2, ',', '.');?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?>

                <?php if(!$totalGeralVlrItensSaldo == 0){?>
                    <td><?php echo number_format($totalGeralVlrLanc/$totalGeralVlrItensSaldo, 2, ',' ,'.');?></td>
        	<?php }else{?>
                    <td><?php echo "0,00";?></td>
        	<?php }?>

                    <td></td>
                </tr>
        <?php 
        	}else{
        		echo "<h2>Sua busca n&atilde;o retornou resultados !</h2>";
        	}
        }else{
        	echo "<tr><td><h2>Sua busca n&atilde;o retornou resultados !</h2></td></tr>";
        }
        ?>



            </table>
            <div>
                <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_por_loja')"/>
            </div>
        </div>
        <a class="cd-top" href="#0">Top</a>
    </body>
</div>
