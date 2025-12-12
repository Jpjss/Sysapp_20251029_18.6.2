<?php
echo $this->Html->css('jquery-ui-1.10.3.custom');

echo $this->fetch('meta');
echo $this->fetch('css');
echo $this->fetch('script');
echo $this->Html->css('style-horizontal-ecommerce');
echo $this->Html->script('jquery-1.10.2.min.js');
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery.blockUI.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('slider-horizontal-ecommerce.js');
echo $this->Html->script('jMenu.js');
echo $this->Html->script('jqsimplemenu');
echo $this->Html->script('jquery.maskMoney');
echo $this->Html->script('jquery.filter_input');
?>
<script>
    $(function() {
        var icons = {
            header: "ui-icon-circle-arrow-e",
            activeHeader: "ui-icon-circle-arrow-s"
        };
        $("#accordion").accordion({
            icons: icons,
            heightStyle: "content"
        });
        $("#toggle").button().click(function() {
            if ($("#accordion").accordion("option", "icons")) {
                $("#accordion").accordion("option", "icons", null);
            } else {
                $("#accordion").accordion("option", "icons", icons);
            }
        });
    });
</script>
<script>
    $(document).ready(function() {

        $("#data_criacao").mask("99/99/9999");
        $('#valor_pedido').maskMoney({thousands: '.', decimal: ',', allowZero: false, allowNegative: true, defaultZero: false});
        $("#cd_sequencia_pedido").attr('maxlength', '9').filter_input({regex: '[0-9]'});
        function enviarEatualizar() {
            $.blockUI({
                message: "<img src='<?php echo $this->webroot; ?>img/iconAtualizar.png' /><br><b>Atualizando dados.<b> <br> Por Favor, Aguarde!",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .8,
                    color: '#fff'
                }});
            valor = '';
            $("input[name='selected_sla[]']").each(function() {
                if ($(this).is(':checked')) {
                    valor = valor + ',' + "'" + $(this).val() + "'";
                }
            });
            statusPedido = '';
            $("input[name='status_pedido[]']").each(function() {
                if ($(this).is(':checked')) {
                    statusPedido = statusPedido + ',' + "'" + $(this).val() + "'";
                }
            });
            valorAntiFraude = '';
            $("input[name='anti_fraude[]']").each(function() {
                if ($(this).is(':checked')) {
                    valorAntiFraude = valorAntiFraude + ',' + "'" + $(this).val() + "'";
                }
            });
            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'acompanhamentoEcommerceAjax')); ?>',
                data: {
                    order_id: $("#order_id").val(),
                    status_erp: $("#status_erp").val(),
                    cd_sequencia_pedido: $("#cd_sequencia_pedido").val(),
                    nm_cliente: $("#nm_cliente").val(),
                    valor_pedido: $("#valor_pedido").val(),
                    data_criacao: $("#data_criacao").val(),
                    sequence: $("#sequence").val(),
                    selected_sla: valor,
                    status_pedido: statusPedido,
                    anti_fraude: valorAntiFraude,
                    qtde_produto_vtex: $("#qtde_produto_vtex").val(),
                    tracking_number: $("#tracking_number").val()
                },
                dataType: "html",
                success: function(data) {

                    $("#pedidos").html(data);
                    $.unblockUI();

                },
            });
        }
        $(document).keypress(function(e) {
            if (e.which == 13) {
                enviarEatualizar();
            }
        });
        $(".multiplaEscolha").change(function() {
            enviarEatualizar();
        })
        var refreshId = setInterval(function()
        {
            if ($("#autoRefresh").is(':checked')) {
                enviarEatualizar();
            }
        }, 60000);
        $(".botoesFiltros").click(function() {
            enviarEatualizar();
        });

        $("#dialog").dialog({
            autoOpen: false,
            width: 800,
            show: {
                effect: "clip",
                duration: 300
            },
            hide: {
                effect: "clip",
                duration: 300
            },
            position: {my: "center", at: "center", of: window}

        });
        $("#pedidos tr td").click(function() {
            $.blockUI({
                message: "<b>Por Favor, Aguarde!</b>",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }});

            $.ajax({type: "POST",
                url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'detalharPedido')); ?>',
                data: {
                    numero_ecommerce: $('td:nth-child(2)', $(this).parents('tr')).text(),
                    codigo_ecommerce: $('td:nth-child(1)', $(this).parents('tr')).text()
                },
                dataType: "html",
                success: function(data) {

                    $("#dialog").html(data);
                    $("#dialog").dialog("open");
                    $.unblockUI();

                },
            });
        });
    });

</script>
<!--<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>-->
<script type="text/javascript">

    var animate = {
        'time': 500,
        'randMin': 1000,
        'randMax': 1200
    };

    (function($) {

        function rand(min, max) {
            return Math.floor((Math.random() * (max - min + 1)) + min);
        }

        var defaults = {
            'randMin': 100,
            'randMax': 2000,
            'time': 1000
        };

        $(function() {

            var settings = $.extend(defaults, animate);

            $('a.animate').click(function(e) {
                e.preventDefault();

                var obj = $(this);
                var time = settings.time;

                if (obj.hasClass('rand')) {

                    time = rand(settings.randMin, settings.randMax);

                } else {

                    var result = /time[0-9]+/.exec(obj.attr('class'));
                    if (result)
                        time = parseInt(new String(result).replace('time', ''));
                }

                $('html, body').animate({
                    scrollTop: $(obj.attr('href')).offset().top
                }, time);
            });
        });
    })(jQuery);
</script>
<style>

    /** Tables **/

    table {
        border-right:0;
        clear: both;
        color: #333;
        margin-bottom: 10px;
        width: 100%;
    }
    th {
        border:0;
        border-bottom:2px solid #555;
        text-align: left;
        padding:4px;
    }
    th a {
        display: block;
        padding: 2px 4px;
        text-decoration: none;
    }
    th a.asc:after {
        content: ' ⇣';
    }
    th a.desc:after {
        content: ' ⇡';
    }
    table tr td {
        cursor: pointer;
        padding: 6px;
        text-align: left;
        vertical-align: top;
        border-bottom:1px solid #ddd;
    }
    table tr:nth-child(even) {
        background: #f9f9f9;
    }
    td.actions {
        text-align: center;
        white-space: nowrap;
    }
    table td.actions a {
        margin: 0px 6px;
        padding:2px 5px;
    }
    .container { border:2px solid #ccc; width:100%;
                 margin-left: auto;
                 margin-right: auto;
    }
    .coluna{
        width:100%;
        /*        width:50px;*/
        height:10px;
        font-size: 12px;

    }
    .coluna:hover{
        background-color:#eeeeee;
    }

</style>
<?php echo $this->Html->css('cake.generic'); ?>
<div class="logoSystec">
    <?php echo $this->Html->image('logoContorno.png', array('alt' => 'Systec', 'title' => 'Desenvolvido por:', 'width' => '150px', 'div' => false)); ?>
</div>
<div id="menuslider">
    <div id="navimenu">
        <div>
            <?php // echo $this->Html->image('logoContorno.png', array('alt' => 'Savan', 'width' => '200px')); ?>
            <br>
            <div class="autoRefresh">
                <input type="checkbox" name="autoRefresh" checked="true" id="autoRefresh" /> Recarregar automaticamente
            </div>
            <h2 style="font-size: 14px; font-weight: bold; color: #BE2524; padding-top: 2px;">Filtros</h2>
            <div id="accordion">
                <h3>Pedido Ecommerce</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="order_id" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Código Interno</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" maxlength="9" id="cd_sequencia_pedido" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Nº Interno</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" maxlength="9" id="sequence" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Nome Cliente</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="nm_cliente" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Data Pedido</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="data_criacao" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Qtde Produtos</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="qtde_produto_vtex" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Valor</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="valor_pedido" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Status Pedido</h3>
                <div>
                    <ul style="list-style-type:none;">
                        <li style="text-align: left; margin: 0 -20px;"><input type="checkbox" class="multiplaEscolha" name="status_pedido[]" value="VTEX: payment-pending"><span>Payment-pending (<?php echo @$statusPedido['VTEX: payment-pending']; ?>)</span></li>
                        <li style="text-align: left; margin: 0 -20px;"><input type="checkbox" class="multiplaEscolha" name="status_pedido[]" value="VTEX:"><span>VTEX (<?php echo @$statusPedido['VTEX: ']; ?>)</span></li>
                        <li style="text-align: left; margin: 0 -20px;"><input type="checkbox" class="multiplaEscolha" name="status_pedido[]" value="VTEX: canceled"><span>Canceled (<?php echo @$statusPedido['VTEX: canceled']; ?>)</span></li>
                        <li style="text-align: left; margin: 0 -20px;"><input type="checkbox" class="multiplaEscolha" name="status_pedido[]" value="VTEX: handling"><span>Handling (<?php echo @$statusPedido['VTEX: handling']; ?>)</span></li>
                        <li style="text-align: left; margin: 0 -20px;"><input type="checkbox" class="multiplaEscolha" name="status_pedido[]" value="VTEX: start-handling"><span>Start-handling (<?php echo @$statusPedido['VTEX: start-handling']; ?>)</span></li>
                        <li style="text-align: left; margin: 0 -20px;"><input type="checkbox" class="multiplaEscolha" name="status_pedido[]" value="VTEX: ready-for-handling"><span>Ready-for-handling (<?php echo @$statusPedido['VTEX: ready-for-handling']; ?>)</span></li>
                    </ul>
                </div>
                <h3>Status ERP</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="status_erp" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Anti-Fraude</h3>
                <div>
                    <ul style="list-style-type:none;">
                        <li style="text-align: left;"><input type="checkbox" name="anti_fraude[]" class="multiplaEscolha" value="0"><span>Aguardando</span></li>
                        <li style="text-align: left;"><input type="checkbox" name="anti_fraude[]" class="multiplaEscolha" value="1"><span>Aprovado</span></li>
                        <li style="text-align: left;"><input type="checkbox" name="anti_fraude[]" class="multiplaEscolha" value="2"><span>Recusado</span></li>
                    </ul>
                </div>
                <h3>Nº Rastreamento</h3>
                <div>
                    <div style="float: left;"><input class="camposFiltros" type="text" id="tracking_number" /></div>
                    <div style="float: left;">&nbsp;<input class="botoesFiltros" type="button" value="ok" /></div>
                </div>
                <h3>Tipo Envio</h3>
                <div>
                    <ul style="list-style-type:none;">
                        <li style="text-align: left;"><input type="checkbox" name="selected_sla[]" class="multiplaEscolha" value="e-sedex"><span>e-sedex</span></li>
                        <li style="text-align: left;"><input type="checkbox" name="selected_sla[]" class="multiplaEscolha" value="Sedex"><span>Sedex</span></li>
                        <li style="text-align: left;"><input type="checkbox" name="selected_sla[]" class="multiplaEscolha" value="PAC"><span>PAC</span></li>
                        <li style="text-align: left;"><input type="checkbox" name="selected_sla[]" class="multiplaEscolha" value="24 horas"><span>24 Horas</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="hidemenu">Selecionar Filtros</div>
    <br>
    <br>
    <br>
</div>





<div style="float: right; width: 97%;">
    <br>

    <?php echo $this->Form->create('Relatorios'); ?>

    <input name="data[Relatorios][per_inicio]" id="per_inicio" value="<?php echo $per_inicio; ?>" type="hidden">
    <input name="data[Relatorios][per_fim]" id="per_fim" value="<?php echo $per_fim; ?>" type="hidden">
    <input style="width: 240px;" name="data[Relatorios][numero_ecommerce]" value="<?php echo $numEcommerce; ?>" id="numero_ecommerce" type="hidden">
    <?php
    echo $this->Form->end();
    ?>
    <div style="margin-left: auto; margin-right: auto;">
        <br><?php echo $this->Html->image('logoRedondoSavan.png', array('alt' => 'Savan', 'width' => '200px')); ?><br>Integração Ecommerce<br><br>
        <!--<br><?php echo $this->Html->image('logoSavan.jpg', array('alt' => 'Savan', 'width' => '200px')); ?>&nbsp;&nbsp;&nbsp;<?php echo $this->Html->image('logoContorno.png', array('alt' => 'Savan', 'width' => '200px')); ?><br><span style="color: #004871; font-size: 25px; font-weight: bold;">Integração Ecommerce</span><br>-->
        <div>
            <table style="width: 700px; position: relative; margin-left: auto; margin-right: auto;">
                <tr class="coluna">
                    <td style="text-align: center; font-weight: bold;">SKU Pendente</td>
                    <td style="text-align: center; font-weight: bold;">SKU Pendente Estoque</td>
                    <td style="text-align: center; font-weight: bold;">SKU Pendente Preço</td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center;"><?php echo $skuPendente[0][0]['qtde_sku_pendente']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteEstoque[0][0]['qtde_sku_pendente_estoque']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendentePreco[0][0]['qtde_sku_pendente_preco']; ?></td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center; font-weight: bold;">SKU Sincronizado Hoje</td>
                    <td style="text-align: center; font-weight: bold;">1ª Sincronização Hoje</td>
                    <td style="text-align: center; font-weight: bold;">Última Sincronização Hoje</td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizado[0][0]['qtde_sku_sincronizado_hoje']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizado[0][0]['primeira_sincronizacao_hoje']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizado[0][0]['ultima_sincronizacao_hoje']; ?></td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center; font-weight: bold;">SKU Sincronizado Preço Hoje</td>
                    <td style="text-align: center; font-weight: bold;">1ª Sincronização Preço Hoje</td>
                    <td style="text-align: center; font-weight: bold;">Última Sincronização Preço Hoje</td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizadoPreco[0][0]['qtde_sku_sincronizado_preco_hoje']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizadoPreco[0][0]['primeira_sincronizacao_preco_hoje']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizadoPreco[0][0]['ultima_sincronizacao_preco_hoje']; ?></td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center; font-weight: bold;">SKU Sincronizado Estoque Hoje</td>
                    <td style="text-align: center; font-weight: bold;">1ª Sincronização Estoque Hoje</td>
                    <td style="text-align: center; font-weight: bold;">Última Sincronização Estoque Hoje</td>
                </tr>
                <tr class="coluna">
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizadoEstoque[0][0]['qtde_sku_sincronizado_estoque_hoje']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizadoEstoque[0][0]['primeira_sincronizacao_estoque_hoje']; ?></td>
                    <td style="text-align: center;"><?php echo $skuPendenteSincronizadoEstoque[0][0]['ultima_sincronizacao_estoque_hoje']; ?></td>
                </tr>

            </table>   
        </div>
        <?php
        if ($per_inicio == '') {
            $per_inicio = '01/01/2013';
        }
        if ($per_fim == '') {
            $per_fim = date("d/m/Y");
        }
        echo 'De ' . $per_inicio . ' até ' . $per_fim;
        ?><br><br>

    </div>
    <?php
    if (!empty($dadosRelatorioCancelados)) {
        ?>
        <div class="pedidosCancelados">
            <a href="#home" class="animate rand">Existem pedidos cancelados</a>
        </div>
        <?php
    }
    if (!empty($dadosRelatorioBaixa)) {
        ?>
        <br>
        <div class="pedidosBaixa">
            <a href="#tentativaBaixa" class="animate rand">Existem pedidos c/ tentativa baixa</a>
        </div>
    <?php } ?>
    <br>
    <div id="pedidos" class="container">
        <table style="width: 100%" id="pedidos">
            <tr style="background-color: #ccc; font-size: 13px; font-weight: bold;">
                <td>Pedido Ecommerce</td>
                <td>Código Interno</td>
                <td>Nº Interno</td>
                <td>Nome Cliente</td>
                <td>Data Pedido</td>
                <td>Qtde Produtos</td>
                <td>Valor</td>
                <td>Status Pedido</td>
                <td>Status ERP</td>
                <td>Anti-Fraude</td>
                <td>Nº Rastreamento</td>
                <td>Tipo de Envio</td>
            </tr>
            <?php foreach ($dadosRelatorio as $value) { ?>
                <tr class="coluna">
                    <td><?php echo $value[0]['order_id']; ?></td>
                    <td><?php echo $value[0]['cd_sequencia_pedido']; ?></td>
                    <td><?php echo $value[0]['sequence']; ?></td>
                    <td><?php echo ucwords(strtolower($value[0]['nm_cliente'])); ?></td>
                    <td><?php echo $this->Formatacao->data($value[0]['data_criacao']); ?></td>
                    <td><?php echo $value[0]['qtde_produto_vtex']; ?></td>
                    <td><?php echo $this->Formatacao->moeda($value[0]['valor_pedido']); ?></td>
                    <td><?php echo $value[0]['status_pedido']; ?></td>
                    <td><?php echo $value[0]['status_erp']; ?></td>
                    <td>
                        <?php
                        switch ($value[0]['anti_fraude']) {
                            case(0):
                                echo 'Aguardando';
                                break;
                            case(1):
                                echo 'Aprovado';
                                break;
                            case(2):
                                echo 'Recusado';
                                break;
                        }
                        ?>
                    </td>
                    <td><?php echo $value[0]['tracking_number']; ?></td>
                    <td><?php echo $value[0]['selected_sla']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div id="dialog" style="background-color: #ccc;" title="Dados do Pedido"></div>
    <br>

    <?php
    if (!empty($dadosRelatorioBaixa)) {
        ?>
        <div id="tentativaBaixa" style="margin-left: auto; margin-right: auto; color: #BE2524; font-size: 22px;">Produtos Com Tentativa de Baixa</div>
        <div id="cancelados" class="container">
            <table id="pedidosTentativaBaixa">
                <tr style="background-color: #ccc; font-size: 13px; font-weight: bold;">
                    <td>Pedido Ecommerce</td>
                    <td>Data Criação</td>
                    <td>CPL Tamanho</td>
                    <td>Mensagem</td>
                    <td>Data Última Tentativa</td>
                </tr>
                <?php foreach ($dadosRelatorioBaixa as $value) { ?>
                    <tr class="coluna">
                        <td><?php echo $value[0]['order_id']; ?></td>
                        <td><?php echo $this->Formatacao->dataHora($value[0]['dt_cad']); ?></td>
                        <td><?php echo $value[0]['cd_cpl_tamanho']; ?></td>
                        <td><?php echo $value[0]['msg']; ?></td>
                        <td><?php echo $this->Formatacao->data($value[0]['dt_ult_tentativa']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>
    <br>
    <?php
    if (!empty($dadosRelatorioCancelados)) {
        ?>
        <div id="home" style="margin-left: auto; margin-right: auto; color: #BE2524; font-size: 22px;">Produtos Cancelados</div>
        <div id="cancelados" class="container">
            <table id="pedidosCancelados">

                <tr style="background-color: #ccc; font-size: 13px; font-weight: bold;">
                    <td>Pedido Ecommerce</td>
                    <td>Data Criação</td>
                    <td>Valor</td>
                    <td>Nº Nota</td>
                    <td>Data Emissão</td>
                    <td>Valor Nota</td>
                    <td>Cancelamento</td>
                    <td>Usuário</td>
                    <td>Obs</td>
                </tr>
                <?php foreach ($dadosRelatorioCancelados as $value) { ?>
                    <tr class="coluna">
                        <td><?php echo $value[0]['order_id']; ?></td>
                        <td><?php echo $this->Formatacao->dataHora($value[0]['creation_date']); ?></td>
                        <td><?php echo $this->Formatacao->moeda($value[0]['value']); ?></td>
                        <td><?php echo $value[0]['nota_numero']; ?></td>
                        <td><?php echo $this->Formatacao->data($value[0]['dt_emissao']); ?></td>
                        <td><?php echo $this->Formatacao->moeda($value[0]['vlr_nota']); ?></td>
                        <td><?php echo $this->Formatacao->data($value[0]['dt_cancelamento']); ?></td>
                        <td><?php echo $value[0]['cd_usu_cancelou']; ?></td>
                        <td><?php echo $value[0]['obs_cancelamento']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>
</div>
