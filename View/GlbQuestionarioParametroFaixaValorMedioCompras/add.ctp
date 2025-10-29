<script>
    $(document).ready(function() {
//        $('#GlbQuestionarioParametroFaixaValorMedioCompraValorInicial').attr( 'maxlength', '7' ).maskMoney({allowZero:false, allowNegative:true, defaultZero:false});
        $('#GlbQuestionarioParametroFaixaValorMedioCompraValorInicial').maskMoney({thousands: '.', decimal: ',', allowZero: false, allowNegative: true, defaultZero: false});
        $('#GlbQuestionarioParametroFaixaValorMedioCompraValorFinal').maskMoney({thousands: '.', decimal: ',', allowZero: false, allowNegative: true, defaultZero: false});

        
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });

        $('form').submit(function() {
            if (parseInt($('#GlbQuestionarioParametroFaixaValorMedioCompraValorFinal').val()) < parseInt($('#GlbQuestionarioParametroFaixaValorMedioCompraValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });
    });
</script>
<div class="glbQuestionarioParametroFaixaValorMedioCompras form">
    <?php echo $this->Form->create('GlbQuestionarioParametroFaixaValorMedioCompra'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Filtro Valor Médio de Compra'); ?></legend>
        <?php
        echo $this->Form->input('ds_parametro_faixa_valor_medio_compra', array('label' => 'Descrição:'));
        echo $this->Form->input('valor_inicial', array("label"=>"Valor Inicial:", "type"=>"text", "onpaste" => "return false;"));
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
