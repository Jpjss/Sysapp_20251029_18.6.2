<script>
    jQuery(function($) {
        $("#GlbQuestionarioParametroDataUltCompraValorInicial").filter_input({regex: '[0-9]'});
        $("#GlbQuestionarioParametroDataUltCompraValorFinal").filter_input({regex: '[0-9]'});

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });

        $('form').submit(function() {
            if (parseInt($('#GlbQuestionarioParametroDataUltCompraValorFinal').val()) < parseInt($('#GlbQuestionarioParametroDataUltCompraValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });
    });
</script>
<div class="glbQuestionarioParametroDataUltCompras form">
    <?php echo $this->Form->create('GlbQuestionarioParametroDataUltCompra'); ?>
    <fieldset>
        <legend><?php echo __('Editar Filtro Data Última Compra'); ?></legend>
        <?php
        echo $this->Form->input('cd_parametro_data_ult_compra', array('value' => $this->data['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co']));
        echo $this->Form->input('ds_parametro_data_ult_compra', array('label' => 'Descrição:', 'value' => $this->data['GlbQuestionarioParametroDataUltCompra']['ds_parametro_data_ult_co']));
        echo $this->Form->input('valor_inicial', array("label"=>"Valor Inicial:","type"=>"text", "onpaste" => "return false;"));
        echo $this->Form->input('valor_final', array("label"=>"Valor Final:","type"=>"text", "onpaste" => "return false;"));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
