<script>
    jQuery(function($) {
        $("#GlbQuestionarioParametroFaixaQuantidadeCompraValorInicial").attr('maxlength', '14').filter_input({regex: '[0-9]'});
        $("#GlbQuestionarioParametroFaixaQuantidadeCompraValorFinal").attr('maxlength', '14').filter_input({regex: '[0-9]'});

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });

        $('form').submit(function() {

            if (parseInt($('#GlbQuestionarioParametroFaixaQuantidadeCompraValorFinal').val()) < parseInt($('#GlbQuestionarioParametroFaixaQuantidadeCompraValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });
    });
</script>
<div class="glbQuestionarioParametroFaixaQuantidadeCompras form">
    <?php echo $this->Form->create('GlbQuestionarioParametroFaixaQuantidadeCompra'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Filtro Quantidade de Compra'); ?></legend>
        <?php
        echo $this->Form->input('ds_parametro_faixa_quantidade_compra', array('label' => 'Descrição:'));
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
