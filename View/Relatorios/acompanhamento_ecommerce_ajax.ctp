<script>
    $(document).ready(function() {
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

            $.ajax({
                type: "POST",
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
