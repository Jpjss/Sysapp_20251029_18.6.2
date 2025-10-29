<script>
    $(document).ready(function() {
        $('#GlbQuestionarioParametroFaixaValorUltCompraValorInicial').maskMoney({thousands: '.', decimal: ',', allowZero: false, allowNegative: true, defaultZero: false});
        $('#GlbQuestionarioParametroFaixaValorUltCompraValorFinal').maskMoney({thousands: '.', decimal: ',', allowZero: false, allowNegative: true, defaultZero: false});

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });

        $('form').submit(function() {
            if (parseInt($('#GlbQuestionarioParametroFaixaValorUltCompraValorFinal').val()) < parseInt($('#GlbQuestionarioParametroFaixaValorUltCompraValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });

    });
</script>
<div class="glbQuestionarioParametroFaixaValorUltCompras form">
    <?php echo $this->Form->create('GlbQuestionarioParametroFaixaValorUltCompra'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Filtro Valor Última Compra'); ?></legend>
        <?php
        echo $this->Form->input('ds_parametro_faixa_valor_ult_compra', array('label' => 'Descrição:'));
        echo $this->Form->input('valor_inicial', array("label"=>"Valor Inicial:", "type"=>"text", "onpaste" => "return false;"));
        echo $this->Form->input('valor_final', array("label"=>"Valor Final:", "type"=>"text", "onpaste" => "return false;"));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
