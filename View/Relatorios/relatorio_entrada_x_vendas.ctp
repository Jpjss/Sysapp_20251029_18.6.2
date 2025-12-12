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
                <td style="font-size: 24px;">Relatório Entrada x Vendas<br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
                    <b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>

            </tr>
        </table>
        <br>
        <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/entradas_x_vendas')"/>
        <br>
        <div class="contentRelatorio">
            <table width="50%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
  	<?php 
      $filialAnterior = -1;
       if($dadosRelatorio != FALSE){
          if(isset($dadosRelatorio)){
        	foreach ($dadosRelatorio as $value){
    ?>
    <?php   
       		if($filialAnterior === $value['cd_filial']){
       			foreach($dadosRelatorioFilial as $chave){
       				if($value['cd_filial'] == $chave['total_filial']){
    ?>
                <tr>
                    <td></td>

                    <td><?php echo $value['ds_marca']; ?></td>

                    <!--  Estoque Atual  -->
                    <td><?php echo number_format($value['qtde_estoque_real'],0, ',' , '.'); ?></td>

                    <td><?php echo number_format($value['qtde_entrada'],0, ',' , '.'); ?></td>

                    <!--  %Qtde Estoque  -->
            <?php if($totalQtdeEstoqueReal != 0){?>

                    <!-- total_qtde_estoque_real -->
                    <td><?php echo number_format(($value['qtde_estoque_real']/$totalQtdeEstoqueReal)*100,2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
            <?php }?>

                    <!--  Qtde Venda  -->
                    <td><?php echo number_format($value['qtde_venda'],0, ',' , '.'); ?></td>

                    <!--  %Qtde Venda  -->
            <?php if($totalQtdeVenda != 0){?>
                    <td><?php echo number_format(($value['qtde_venda']/$totalQtdeVenda)*100, 2, ',', '.')?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.')?></td>
            <?php }?>

                    <!--  Vlr Estoque -->
                    <td><?php echo number_format($value['vlr_estoque'],2, ',' , '.'); ?></td>

                    <!--  %Vlr. Estoque  -->
            <?php if($totalVlrEstoque > 0){?>
                    <td><?php echo number_format(($value['vlr_estoque']/$totalVlrEstoque)*100, 2, ',', '.')?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.')?></td>
            <?php }?>

                    <!--  Vlr. Vendido  -->
                    <td><?php echo number_format($value['vlr_venda'],2, ',' , '.'); ?></td>

                    <!--  %Vlr. Venda  -->
            <?php if($totalVlrVenda != 0){?>
                    <td><?php echo number_format(($value['vlr_venda']/$totalVlrVenda)*100 ,2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0 ,2, ',' , '.'); ?></td>
            <?php }?>

                    <!--  Rela��o R$  -->
            <?php if(($value['qtde_venda']*$value['pcusto']) != 0){?>
                    <td><?php echo number_format(($value['vlr_estoque']/($value['qtde_venda']*$value['pcusto'])),2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format($value['vlr_estoque'],2, ',' , '.'); ?></td>
            <?php }?>

                    <!--  Rela��o Estoque  -->
	        <?php if($value['qtde_venda'] != 0){?>    
                    <td><?php echo number_format($value['qtde_estoque_real']/$value['qtde_venda'],2, ',' , '.'); ?></td>
	        <?php }else{?>
                    <td><?php echo number_format($value['qtde_estoque_real'],2, ',' , '.'); ?></td>
	        <?php }?>

                    <!--  Pre�o de Custo  -->
                    <td><?php echo number_format($value['pcusto'],2, ',' , '.'); ?></td>

                    <!--  Pre�o de Venda  -->
                    <td><?php echo number_format($value['pvenda'],2, ',' , '.'); ?></td>

                    <!--  Margem %  -->
            <?php if($value['pvenda'] != 0 && $value['pcusto'] != 0){?>
                    <td><?php echo number_format((($value['pvenda'] - $value['pcusto'])*100/$value['pcusto']),2, ',' , '.'); ?></td>
           	<?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
           	<?php }?>
                </tr>
        <?php 
       				}
        		}
        		$filialAnterior = $value['cd_filial'];
       		}else{
       			if($filialAnterior != $value['cd_filial']){
       				foreach($dadosRelatorioFilial as $chave){
       					if($value['cd_filial'] == $chave['total_filial']){
       	?>
                <tr style="text-align: center; background: #59a6d6; border: 0px;">
                    <td></td>
                    <td>Marca</td>
                    <td>Estoque Atual</td>
                    <td>Qtde Entradas</td>
                    <td>% Qtde Estoque</td>
                    <td>Qtde Venda</td>
                    <td>% Qtde Venda</td>
                    <td>Vlr. Estoque</td>
                    <td>% Vlr. Estoque</td>
                    <td>Vlr. Vendido</td>
                    <td>% Vlr. Venda</td>
                    <td>Rela&ccedil;&atilde;o Est/R$</td>
                    <td>Rela&ccedil;&atilde;o Est/Qtde.</td>
                    <td>Pre&ccedil;o de Custo</td>
                    <td>Pre&ccedil;o de Venda</td>
                    <td>Margem %</td>
                </tr>

                <!-- TOTALIZADORES POR FILIAL-->

                <tr style="text-align: center; background: #32C7FF; border: 0px;">

                    <td><?php echo "Filial ".$chave['total_filial']; ?></td>
                    <td></td>

                    <!--  Estoque Atual Filial -->
                    <td><?php echo number_format($chave['total_qtde_estoque_real'],0, ',' , '.'); ?></td>

                    <!--  Qtde Entradas -->
                    <td><?php echo number_format($chave['total_qtde_entrada'],0, ',' , '.'); ?></td>

                    <!--  %Qtde Estoque Filial -->
            <?php if($chave['totalPorFilialPercentEstoque'] != 0){?>
                    <td><?php echo number_format($chave['totalPorFilialPercentEstoque'], 2, ',', '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
            <?php }?>

                    <!--  Qtde Venda Filial -->
                    <td><?php echo number_format($chave['total_qtde_venda'],0, ',' , '.'); ?></td>

                    <!--  %Qtde Venda Filial  -->
            <?php if($chave['totalPorFilialPercentVenda'] != 0){?>
                    <td><?php echo number_format(($chave['totalPorFilialPercentVenda']), 2, ',', '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
            <?php }?>

                    <!--  Vlr Estoque Filial  -->
                    <td><?php echo number_format($chave['total_vlr_estoque'],2, ',' , '.'); ?></td>

                    <!--  %Vlr. Estoque Filial  -->
            <?php if($chave['totalPorFilialPercentVlrEstoque'] != 0){?>
                    <td><?php echo number_format(($chave['totalPorFilialPercentVlrEstoque']), 2, ',', '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
            <?php }?>

                    <!--  Vlr. Vendido Filial  -->
                    <td><?php echo number_format($chave['total_vlr_venda'],2, ',' , '.'); ?></td>

                    <!--  %Vlr. Venda Filial  -->
			<?php if($chave['totalPorFilialPercentVlrVenda']!= 0){?>
                    <td><?php echo number_format(($chave['totalPorFilialPercentVlrVenda']), 2, ',', '.'); ?></td>
			<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
			<?php }?>

			<?php  if($chave['qtde_p_custo'] != 0){
                            $totalGeralPCusto = $chave['total_p_custo'] / $chave['qtde_p_custo'];
                        }else{
                            $totalGeralPCusto = 0;
                        } ?>

                    <!--  Rela��o Estoque R$ Filial  -->
			<?php if(($chave['total_vlr_venda'] != 0)){?>
                    <td><?php echo number_format(($chave['total_vlr_estoque']/($chave['total_vlr_venda'])), 2, ',', '.'); ?></td>
			<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
			<?php }?>

                    <!--  Rela��o Estoque Filial  -->
			<?php if($chave['total_qtde_venda'] != 0){?>
                    <td><?php echo number_format($chave['total_qtde_estoque_real']/$chave['total_qtde_venda'],2, ',' , '.'); ?></td>
			<?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
			<?php }?>

                    <!--  Pre�o de Custo Filial  -->
			<?php if($chave['qtde_p_custo'] != 0){?>
                    <td><?php echo number_format($chave['total_p_custo']/$chave['qtde_p_custo'], 2, ',', '.') ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.') ?></td>
            <?php }?>

                    <!--  Pre�o de Venda Filial  -->
            <?php if($chave['qtde_p_venda'] != 0){?>
                    <td><?php echo number_format($chave['total_p_venda']/$chave['qtde_p_venda'], 2, ',', '.') ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.') ?></td>
            <?php }?>

                    <!--  Margem Filial  -->
            <?php if(($chave['qtde_p_venda']) != 0 && $chave['qtde_p_custo'] != 0 && ($chave['total_p_custo']/$chave['qtde_p_custo']) != 0){?>
                    <td><?php echo number_format(((($chave['total_p_venda']/$chave['qtde_p_venda']) - ($chave['total_p_custo']/$chave['qtde_p_custo']))*100/($chave['total_p_custo']/$chave['qtde_p_custo'])),2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
            <?php }?>
                </tr>
                <tr>
                    <td></td>
                    <td><?php echo $value['ds_marca']; ?></td>
                    <!--  Estoque Atual  -->
                    <td><?php echo number_format($value['qtde_estoque_real'],0, ',' , '.'); ?></td>

                    <td><?php echo number_format($value['qtde_entrada'],0, ',' , '.'); ?></td>

                    <!--  %Qtde Estoque  -->
            <?php if($totalQtdeEstoqueReal != 0){?>
                    <!-- total_qtde_estoque_real -->
                    <td><?php echo number_format(($value['qtde_estoque_real']/$totalQtdeEstoqueReal)*100,2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
            <?php }?>

                    <!--  Qtde Venda  -->
                    <td><?php echo number_format($value['qtde_venda'],0, ',' , '.'); ?></td>

                    <!--  %Qtde Venda  -->
            <?php if($totalQtdeVenda != 0){?>
                    <td><?php echo number_format(($value['qtde_venda']/$totalQtdeVenda)*100, 2, ',', '.')?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.')?></td>
            <?php }?>

                    <!--  Vlr Estoque -->
                    <td><?php echo number_format($value['vlr_estoque'],2, ',' , '.'); ?></td>

                    <!--  %Vlr. Estoque  -->
            <?php if($totalVlrEstoque > 0){?>
                    <td><?php echo number_format(($value['vlr_estoque']/$totalVlrEstoque)*100, 2, ',', '.')?></td>
            <?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.')?></td>
            <?php }?>

                    <!--  Vlr. Vendido  -->
                    <td><?php echo number_format($value['vlr_venda'],2, ',' , '.'); ?></td>

                    <!--  %Vlr. Venda  -->
            <?php if($totalVlrVenda != 0){?>
                    <td><?php echo number_format(($value['vlr_venda']/$totalVlrVenda)*100 ,2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format(0 ,2, ',' , '.'); ?></td>
            <?php }?>

                    <!--  Rela��o R$  -->
            <?php if(($value['qtde_venda']*$value['pcusto']) != 0){?>
                    <td><?php echo number_format(($value['vlr_estoque']/($value['qtde_venda']*$value['pcusto'])),2, ',' , '.'); ?></td>
            <?php }else{?>
                    <td><?php echo number_format($value['vlr_estoque'],2, ',' , '.'); ?></td>
            <?php }?>

                    <!--  Rela��o Estoque  -->
	        <?php if($value['qtde_venda'] != 0){?>    
                    <td><?php echo number_format($value['qtde_estoque_real']/$value['qtde_venda'],2, ',' , '.'); ?></td>
	        <?php }else{?>
                    <td><?php echo number_format($value['qtde_estoque_real'],2, ',' , '.'); ?></td>
	        <?php }?>

                    <!--  Pre�o de Custo  -->
                    <td><?php echo number_format($value['pcusto'],2, ',' , '.'); ?></td>

                    <!--  Pre�o de Venda  -->
                    <td><?php echo number_format($value['pvenda'],2, ',' , '.'); ?></td>

                    <!--  Margem %  -->
            <?php if($value['pvenda'] != 0 && $value['pcusto'] != 0){?>
                    <td><?php echo number_format((($value['pvenda'] - $value['pcusto'])*100/$value['pcusto']),2, ',' , '.'); ?></td>
           	<?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
           	<?php }?>
                </tr>

       	<?php 
       		$filialAnterior = $value['cd_filial'];
       					}
       				}
       			}
       		}
        }
        ?>

                <tr style="background-color:#BBE42F">
                    <td>Total Geral :</td>
                    <td></td>
                    <td><?php echo number_format($totalQtdeEstoqueReal, 0, ',', '.'); ?></td>
                    <td><?php echo number_format($totalQtdeEntrada, 0, ',', '.'); ?></td>
                    <td><?php echo number_format($totalPercentEstoque, 2, ',', '.'); ?></td>
                    <td><?php echo number_format($totalQtdeVenda, 0, ',', '.'); ?></td>

		   	<?php if($totalQtdeVenda != 0){?>
                    <td><?php echo number_format(($totalQtdeVenda/$totalQtdeVenda)*100, 2, ',', '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
		   	<?php }?>

                    <td><?php echo number_format($totalVlrEstoque, 2, ',', '.'); ?></td>

		   	<?php if($totalVlrEstoque != 0){?>
                    <td><?php echo number_format(($totalVlrEstoque/$totalVlrEstoque)*100, 2, ',', '.'); ?></td>
		   	<?php }else{?>
		   	<?php }?>

                    <td><?php echo number_format($totalVlrVenda, 2, ',', '.'); ?></td>

		   	<?php if($totalVlrVenda != 0){?>
                    <td><?php echo number_format(($totalVlrVenda/$totalVlrVenda)*100, 2, ',', '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
		   	<?php }?>

		   	<?php if($totalQtdeVenda != 0){?>
                    <td><?php echo number_format($totalVlrEstoque/($totalQtdeVenda*($totalPCusto/$countPCustoGeral)), 2, ',', '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
		   	<?php }?>

		   	<?php if($totalQtdeVenda != 0){?>
                    <td><?php echo number_format(($totalQtdeEstoqueReal/$totalQtdeVenda), 2, ',', '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
		   	<?php }?>

		   	<?php if($countPCustoGeral != 0){?>
                    <td><?php echo number_format(($totalPCusto/$countPCustoGeral), 2, ',', '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
		   	<?php }?>

		   	<?php if($countPVendaGeral != 0){?>
                    <td><?php echo number_format(($totalPVenda/$countPVendaGeral), 2, ',', '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0, 2, ',', '.'); ?></td>
		   	<?php }?>

		   	<?php if($countPVendaGeral !=0 && $countPCustoGeral != 0){?>
                    <td><?php echo number_format(((($totalPVenda/$countPVendaGeral) - ($totalPCusto/$countPCustoGeral))*100/($totalPCusto/$countPCustoGeral)),2, ',' , '.'); ?></td>
		   	<?php }else{?>
                    <td><?php echo number_format(0,2, ',' , '.'); ?></td>
		   	<?php }?>
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
            <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/entradas_x_vendas')"/>
        </div>
</div>
<a class="cd-top" href="#0">Top</a>
</body>
