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
                <td style="font-size: 24px;">Relatório de Controle de Vendas<br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s') ?></b><br>
                    <b>Per&iacute;odo: <?php echo $data_formatada_inicial ?> a <?php echo $data_formatada_final ?></b>
            </tr>
        </table>
        <br>
        <div class="contentRelatorio">
            <input type="button" class="btn btn-primary" id="voltar" value="Voltar" style="margin-bottom:5px" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/controle_vendas')"/>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
                <tr style="text-align: center; background: #59a6d6; border: 0px;">
                    <td>Codigo</td>
                    <td>Descrição</td>
                    <td>Qtde. Estoque</td>
                    <td>Qtde. Vendida</td>
                    <td>Vlr. Vitrine</td>
                    <td>Vlr. Venda</td>
                    <td>Vlr. vendido</td>
                </tr>

                <?php
                if ($dadosRelatorioFilial != FALSE) {
                    
                    if (isset($dadosRelatorioFilial)) {

                        $filialAnterior = "";
                        $nomeVendedorAnterior = "";
                        $intContador = 0;
                        $intContadorVendedor = 0;

                        $QtdeTotalEstoqueGeral = 0;
                        $QtdeTotalVendidaGeral = 0;
                        $TotalValorVitrineGeral = 0;
                        $TotalValorVendaGeral = 0;
                        $TotalValorVendidoGeral = 0;

                        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

                        foreach ($dadosRelatorioFilial as $value) { ?>

               
                        <?php if ($filialAnterior != $value['nm_fant']) { ?> 

                            <tr style="background-color:#FFFFFF">   
                                <td> </td>
                            <tr style="background-color:#32C7FF">
                                <td><?php echo utf8_encode($value['nm_fant']) .'<br>'; ?></td>
                                <td></td>
                                <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["QtdeTotalEstoque"], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["QtdeTotalVendida"], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["TotalValorVitrine"], 2, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["TotalValorVenda"], 2, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["TotalValorVendido"], 2, ',', '.'); ?></td>
                            </tr>

                            <?php $filialAnterior = $value['nm_fant']; ?>
                            <?php $QtdeTotalEstoqueGeral += $arrayQtdeTotalProduto[$intContador]["QtdeTotalEstoque"] ?>
                            <?php $QtdeTotalVendidaGeral += $arrayQtdeTotalProduto[$intContador]["QtdeTotalVendida"] ?>
                            <?php $TotalValorVitrineGeral += $arrayQtdeTotalProduto[$intContador]["TotalValorVitrine"] ?>
                            <?php $TotalValorVendaGeral += $arrayQtdeTotalProduto[$intContador]["TotalValorVenda"] ?>
                            <?php $TotalValorVendidoGeral += $arrayQtdeTotalProduto[$intContador]["TotalValorVendido"] ?>
                            <?php $intContador++; ?>
                        <?php } ?>   
                                    
                        <?php if ($nomeVendedorAnterior != $value['nm_usu']) { ?> 
                            
                            <tr style="background-color:#FFFFFF">   
                                <td> </td>
                            <tr style="background-color:#32C7FF">
                                <td><?php echo 'VENDEDOR: ' . utf8_encode($value['nm_usu']) . '<br>'; ?></td>
                                <td></td>
                                <td><?php echo number_format($arrayQtdeTotalProdutoVendedor[$intContadorVendedor]["QtdeTotalEstoqueVendedor"], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProdutoVendedor[$intContadorVendedor]["QtdeTotalVendidaVendedor"], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProdutoVendedor[$intContadorVendedor]["TotalValorVitrineVendedor"], 2, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProdutoVendedor[$intContadorVendedor]["TotalValorVendaVendedor"], 2, ',', '.'); ?></td>
                                <td><?php echo number_format($arrayQtdeTotalProdutoVendedor[$intContadorVendedor]["TotalValorVendidoVendedor"], 2, ',', '.'); ?></td>
                            </tr>
                            
                            <?php $nomeVendedorAnterior = $value['nm_usu']; ?>
                            <?php $intContadorVendedor++; ?>
                            
                        <?php } ?>

                <tr>
                    <td> <?php echo utf8_encode($value['cd_cpl_tamanho']); ?> </td>
                    <td> <?php echo utf8_encode($value['ds_prod_z']); ?> </td>
                    <td> <?php echo number_format($value['qtde_estoque'], 0, ',', '.'); ?> </td>
                    <td> <?php echo number_format($value['qtde_vendida'], 0, ',', '.'); ?> </td>
                    <td> <?php echo number_format($value['vlr_prazo'], 2, ',', '.'); ?> </td>
                    <td> <?php echo number_format($value['vlr_venda'], 2, ',', '.'); ?> </td>
                    <td> <?php echo number_format($value['vlr_vendido'], 2, ',', '.'); ?> </td>
                </tr>

                            <?php } ?>
                            <?php } ?>

                <tr style = "background-color:#BBE42F">
                    <td>Total Geral :</td>
                    <td></td>
                    <td><?php echo number_format($QtdeTotalEstoqueGeral, 0, ',', '.'); ?> </td>
                    <td><?php echo number_format($QtdeTotalVendidaGeral, 0, ',', '.'); ?> </td>
                    <td><?php echo number_format($TotalValorVitrineGeral, 2, ',', '.'); ?> </td>
                    <td><?php echo number_format($TotalValorVendaGeral, 2, ',', '.'); ?> </td>
                    <td><?php echo number_format($TotalValorVendidoGeral, 2, ',', '.'); ?> </td>
                </tr> 

                        <?php
                   
                } else {
                    echo "<tr><td><h2>Sua busca n&atilde;o retornou resultados !</h2></td></tr>";
                }
                ?>
            </table>
            <div>
                <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/controle_vendas')"/>
            </div>
        </div>
        <a class="cd-top" href="#0">Top</a>
    </body>
</div>
