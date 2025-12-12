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
                <td style="font-size: 24px;">Relatório de Pedido de Compras<br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s') ?></b><br>
                    <?php
                    if ($optTipoPeriodo == 'FATURAMENTO') {
                        ?>
                        <b>Per&iacute;odo Prev. Fat.: <?php echo $data_formatada_inicial ?> a <?php echo $data_formatada_final ?></b>
                        <?php
                    } else {
                        ?>
                        <b>Per&iacute;odo Data Entrada: <?php echo $data_formatada_inicial ?> a <?php echo $data_formatada_final ?></b>
                        <?php
                    }
                    ?>
            </tr>
        </table>
        <br>
        <div class="contentRelatorio">
            <input type="button" class="btn btn-primary" id="voltar" value="Voltar" style="margin-bottom:5px" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/pedido_compras')"/>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
                <tr style="text-align: center; background: #59a6d6; border: 0px;">
                    <td>Marca</td>
                    <td>Linha</td>
                    <td>Qtde. Prod.</td>
                    <td>Qtde. Itens.</td>
                    <td>Valor</td>
                    <td>Saldo</td>
                    <?php
                    if ($optTipoPeriodo == 'FATURAMENTO') {
                        ?>
                        <td>Prev. Fat.</td>
                        <?php
                    } else {
                        ?>
                        <td>Data Entrada</td>
                        <?php
                    }
                    ?>
                    <td>MarkUp%</td>
                    <td>Marca</td>
                </tr>

                <?php
                if ($dadosRelatorioFilial != FALSE) {
                    if (isset($dadosRelatorioFilial)) {

                        $filialAnterior = "";
                        $intContador = 0;

                        $TotalQtdeProdutoGeral = 0;
                        $TotalQtdeItensGeral = 0;
                        $TotalValorGeral = 0;
                        $TotalSaldoGeral = 0;
                        $TotalMarkupGeral = 0;

                        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

                        foreach ($dadosRelatorioFilial as $value) {
                            ?>

                            <?php
                            if ($filialAnterior != $value['nm_fant']) {
                                ?> 

                                <tr style="background-color:#FFFFFF">   
                                    <td> </td>

                                <tr style="background-color:#32C7FF">
                                    <td><?php echo utf8_encode($value['nm_fant']) . '<br>'; ?></td>
                                    <td></td>
                                    <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["QtdeTotalProduto"], 0, ',', '.'); ?></td>
                                    <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["QtdeTotalItens"], 0, ',', '.'); ?></td>
                                    <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["TotalValor"], 2, ',', '.'); ?></td>
                                    <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["TotalSaldo"], 2, ',', '.'); ?></td>
                                    <td></td>
                                    <td><?php echo number_format($arrayQtdeTotalProduto[$intContador]["TotalMarkup"], 2, ',', '.'); ?></td>
                                    <td></td>
                                </tr>

                                <?php $filialAnterior = $value['nm_fant']; ?>
                                <?php $TotalQtdeProdutoGeral += $arrayQtdeTotalProduto[$intContador]["QtdeTotalProduto"] ?>
                                <?php $TotalQtdeItensGeral += $arrayQtdeTotalProduto[$intContador]["QtdeTotalItens"] ?>
                                <?php $TotalValorGeral += $arrayQtdeTotalProduto[$intContador]["TotalValor"] ?>
                                <?php $TotalSaldoGeral += $arrayQtdeTotalProduto[$intContador]["TotalSaldo"] ?>
                                <?php $TotalMarkupGeral += $arrayQtdeTotalProduto[$intContador]["TotalMarkup"] ?>
                                <?php $intContador++; ?>

                            <?php } ?> 

                            <tr>
                                <td>
                                    <?php echo utf8_encode($value['ds_marca']); ?>
                                </td>
                                <td>
                                    <?php echo utf8_encode($value['ds_linha']); ?>
                                </td>
                                <td>
                                    <?php echo number_format($value['qtde_pro_z'], 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php echo number_format($value['qtde_produto_itens'], 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php echo number_format($value['vlr_tot_produt_itens'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php echo number_format($value['qtde_sald_itens_restante'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($optTipoPeriodo == 'FATURAMENTO') {
                                        echo $funcionalidades->formatarDataAp($value['dt_prev_entrega']);
                                    } else {
                                        echo $funcionalidades->formatarDataAp($value['dt_entrada_nf']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo number_format($value['markup'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php echo utf8_encode($value['ds_marca']); ?>
                                </td>
                            </tr>

                            <?php
                        }
                        ?>

                        <?php
                        if ($intContador == 0) {
                            $TotalMarkupGeral = 0;
                        } else {
                            $TotalMarkupGeral = $TotalMarkupGeral / $intContador;
                        }
                        ?>

                        <tr style = "background-color:#BBE42F">
                            <td>Total Geral :</td>
                            <td></td>
                            <td><?php echo number_format($TotalQtdeProdutoGeral, 0, ',', '.'); ?> </td>
                            <td><?php echo number_format($TotalQtdeItensGeral, 0, ',', '.'); ?> </td>
                            <td><?php echo number_format($TotalValorGeral, 2, ',', '.'); ?> </td>
                            <td><?php echo number_format($TotalSaldoGeral, 2, ',', '.'); ?> </td>
                            <td></td>
                            <td><?php echo number_format($TotalMarkupGeral, 2, ',', '.'); ?> </td>
                            <td></td>
                        </tr> 

                        <?php
                    }
                } else {
                    echo "<tr><td><h2>Sua busca n&atilde;o retornou resultados !</h2></td></tr>";
                }
                ?>
            </table>
            <div>
                <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/pedido_compras')"/>
            </div>
        </div>
        <a class="cd-top" href="#0">Top</a>
    </body>
</div>
