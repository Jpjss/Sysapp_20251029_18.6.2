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
                <td style="font-size: 24px;">Relatório Vendas por Vendedor<br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s') ?></b><br>
                    <b>Per&iacute;odo: <?php echo $data_formatada_inicial ?> a <?php echo $data_formatada_final ?></b></td>

            </tr>
        </table>
        <br>
        <div class="contentRelatorio">
            <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_por_vendedor')"/>
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
                    <td>Vendedor</td>
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
                $totalGeralPS = "";
                $totalGeralVendasTrocas = "";
                $totalGeralTicketM = "";
                $totalGeralVMedioProd = "";

                if ($dadosRelatorio != FALSE) {
                    if (isset($dadosRelatorio)) {
                        $filialAnterior = -1;
                        foreach ($dadosRelatorio as $value) {

                            $totalGeralVlrLanc += $value['vlr_lanc'];
                            $totalGeralItens += $value['itens'];
                            $totalGeralVlrItensTroca += $value['itens_troca'];
                            $totalGeralVlrItensSaldo += $value['itens_saldo'];
                            $totalGeralVendas += $value['vendas'];
                            $totalGeralMetas += $value['metas'];
                            $totalGeralPercentReal += $value['percent_real'];
                            $totalGeralVlrDesconto += $value['vlr_desconto'];
                            $totalGeralVendasTrocas += $value['vendas_trocas'];

                            if ($filialAnterior === $value['nm_fant']) {
                                foreach ($dadosRelatorioFilial as $chave) {
                                    if ($value['nm_fant'] == $chave['total_nm_fant']) {
                                        // escreve dados
                                        // conta total da filial
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo utf8_encode($value['nm_usu']); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['itens'], 0, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['itens_troca'], 0, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['itens_saldo'], 0, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo $value['vendas'] ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['metas'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['vlr_lanc'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['vlr_lanc'] / $chave['total_vlr_lanc'] * 100, 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['percent_real'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['vlr_desconto'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($value['itens'] == '0' || $value['vendas'] == '0') {
                                                    echo '0';
                                                } else {
                                                    echo (number_format($value['itens'] / $value['vendas'], 2, ',', '.'));
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $value['vendas_trocas']; ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['ticket_medio'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($value['vlr_medio_prod'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <?php echo utf8_encode($value['nm_usu']) . '<br>'; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <tr style="background-color:#FFFFFF">
                                    <td> </td>
                                </tr>

                                <?php
                                if ($filialAnterior != $value['nm_fant']) {
                                    foreach ($dadosRelatorioFilial as $chave) {
                                        if ($value['nm_fant'] == $chave['total_nm_fant']) {
                                            ?>
                                            <tr style="background-color:#32C7FF">
                                                <td>
                                                    <?php
                                                    echo utf8_encode($chave['total_nm_fant']) . '<br>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_itens'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_itens_troca'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_itens_saldo'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_vendas'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_metas'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_vlr_lanc'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format(($chave['total_vlr_lanc'] / $totalGeralValorVendido * 100), 2, ',', '.'); ?>
                                                </td>
                                                <?php if (!$chave['total_metas'] == 0 && !$chave['total_metas'] == '0.00') { ?>
                                                    <td>
                                                        <?php echo number_format(($chave['total_vlr_lanc'] / $chave['total_metas'] * 100), 2, ',', '.'); ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td>
                                                        <?php echo "0,00"; ?>
                                                    </td>
                                                <?php } ?>
                                                <td>
                                                    <?php echo number_format($chave['total_vlr_desconto'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_itens'] / $chave['total_vendas'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo $chave['total_vendas_trocas']; ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_vlr_lanc'] / ($chave['total_vendas'] - $chave['total_vendas_trocas']), 2, ',', '.') ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($chave['total_vlr_lanc'] / $chave['total_itens_saldo'], 2, ',', '.') ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?php echo utf8_encode($value['nm_usu']); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['itens'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['itens_troca'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['itens_saldo'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['vendas'] ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['metas'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['vlr_lanc'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['vlr_lanc'] / $chave['total_vlr_lanc'] * 100, 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['percent_real'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['vlr_desconto'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($value['itens'] == '0' || $value['vendas'] == '0') {
                                                        echo '0';
                                                    } else {
                                                        echo (number_format($value['itens'] / $value['vendas'], 2, ',', '.'));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['vendas_trocas'] ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['ticket_medio'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($value['vlr_medio_prod'], 2, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php echo utf8_encode($value['nm_usu']) . '<br>'; ?>
                                                </td>
                                            </tr>
                                            <?php
                                            // zera total da filial
                                            $filialAnterior = $value['nm_fant'];
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                        <tr style="background-color:#BBE42F">
                            <td>Total Geral :</td>
                            <td><?php echo number_format($totalGeralItens, 0, ',', '.'); ?> </td>
                            <td><?php echo number_format($totalGeralVlrItensTroca, 0, ',', '.'); ?> </td>
                            <td><?php echo number_format($totalGeralVlrItensSaldo, 0, ',', '.'); ?> </td>
                            <td><?php echo number_format($totalGeralVendas, 0, ',', '.'); ?></td>
                            <td><?php echo number_format($totalGeralMetas, 2, ',', '.'); ?></td>
                            <td><?php echo number_format($totalGeralVlrLanc, 2, ',', '.'); ?></td>
                            <td><?php echo number_format(($totalGeralVlrLanc / $totalGeralValorVendido * 100), 2, ',', '.'); ?></td>
                            <?php if (!$totalGeralMetas == 0) { ?>
                                <td><?php echo number_format(($totalGeralVlrLanc / $totalGeralMetas * 100), 2, ',', '.'); ?></td>
                            <?php } else { ?>
                                <td><?php echo "0,00"; ?></td>
                            <?php } ?>
                            <td><?php echo number_format($totalGeralVlrDesconto, 2, ',', '.') ?></td>
                            <td><?php echo number_format($totalGeralItens / $totalGeralVendas, 2, ',', '.'); ?></td>
                            <td><?php echo number_format($totalGeralVendasTrocas); ?></td>
                            <td><?php echo number_format($totalGeralVlrLanc / ($totalGeralVendas - $totalGeralVendasTrocas), 2, ',', '.'); ?></td>
                            <td><?php echo number_format($totalGeralVlrLanc / $totalGeralVlrItensSaldo, 2, ',', '.'); ?></td>
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
                <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_por_vendedor')"/>
            </div>
        </div>
        <a class="cd-top" href="#0">Top</a>
    </body>
</div>
