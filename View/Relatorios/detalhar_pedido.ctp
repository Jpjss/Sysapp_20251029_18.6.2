<script>
    $('#gravarCodigo').on('click', function() {
        $.ajax({
            type: "POST",
            url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'gravarNumero')); ?>',
            data: {
                tipo: 'rastreamento',
                codigo: $("#codigo").val(),
                codigo_interno: $("#codigo_interno").val()
            },
            dataType: "html",
            success: function(data) {
                alert(data);
                $("#dialog").dialog("close");
                $.unblockUI();
            },
        });
    });
    $('#gravarAntiFraude').on('click', function() {
        $.ajax({
            type: "POST",
            url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'gravarNumero')); ?>',
            data: {
                tipo: 'anti_fraude',
                codigo_interno: $("#codigo_interno").val(),
                anti_fraude: $("#anti_fraude").val(),
            },
            dataType: "html",
            success: function(data) {
                alert(data);
                $("#dialog").dialog("close");
                $.unblockUI();
            },
        });
    });
</script>
<table style="font-size: 10px;">
    <tr style="background-color: #FFF;">
        <td colspan="5"><b>Pedido Ecommerce: <?php echo $codigo_pedido; ?></b></td>
    </tr>
    <tr style="background-color: #ccc; font-size: 13px; font-weight: bold;">
        <td>CPL</td>
        <td>CPL Tam.</td>
        <td>Produto</td>
        <td>Filial ERP Origem</td>
        <td>Status</td>

    </tr>
    <?php foreach ($dadosRelatorio as $value) { ?>
        <tr style="font-weight: bold;">
            <td><?php echo $value[0]["cd_cpl"]; ?></td>
            <td><?php echo $value[0]["cd_cpl_tamanho"]; ?></td>
            <td><?php echo $value[0]["descricao_produto"]; ?></td>
            <td><?php echo $value[0]["nm_fant"]; ?></td>
            <td><?php echo $value[0]["status"]; ?></td>

        </tr>
    <?php } ?>
    <tr>
        <td colspan="3">
            <input style="width: 150px; height: 5px;" type="text" id="codigo" maxlength="100" />
            <input type="hidden" id="codigo_interno" value="<?php echo $numero_ecommerce; ?>" />
            <input style="width: 150px; background-color: #BE2524; color: white; border-width: thin;
                   border-color: black; text-align: center; border-top-right-radius: 10px;
                   border-top-left-radius: 10px;
                   border-bottom-right-radius: 10px;
                   border-bottom-left-radius: 10px;" type="button" id="gravarCodigo" value="Gravar NR Rastreamento" />

        </td>
        <td colspan="2">
            <select id="anti_fraude">
                <option selected>Selecione</option>
                <option value="0">Aguardando</option>
                <option value="1">Aprovado</option>
                <option value="2">Recusado</option>
            </select>
            <input style="width: 110px; background-color: #BE2524; color: white; border-width: thin;
                   border-color: black; text-align: center; border-top-right-radius: 10px;
                   border-top-left-radius: 10px;
                   border-bottom-right-radius: 10px;
                   border-bottom-left-radius: 10px;" type="button" id="gravarAntiFraude" value="Gravar Anti-Fraude" />

        </td>
    </tr>
</table>
