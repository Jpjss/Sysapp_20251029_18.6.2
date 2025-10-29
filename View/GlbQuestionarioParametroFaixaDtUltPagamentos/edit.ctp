<script>
    $(document).ready(function() {
        
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
            
        });
        
        $('form').submit(function() {
            if (parseInt($('#GlbQuestionarioParametroFaixaDtUltPagamentoValorFinal').val()) < parseInt($('#GlbQuestionarioParametroFaixaDtUltPagamentoValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });
    });
</script>
<div class="glbQuestionarioParametroFaixaDtUltPagamentos form">
    <?php echo $this->Form->create('GlbQuestionarioParametroFaixaDtUltPagamento'); ?>
    <fieldset>
        <legend><?php echo __('Editar Filtro Data Último Pagamento'); ?></legend>
        <?php
        echo $this->Form->input('cd_parametro_faixa_dt_ult_pagamento', array('value' => $this->data['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa']));
        echo $this->Form->input('ds_parametro_faixa_dt_ult_pagamento', array("label"=>"Descrição:", 'value' => $this->data['GlbQuestionarioParametroFaixaDtUltPagamento']['ds_parametro_faixa']));
        echo $this->Form->input('valor_inicial', array("label"=>"Valor Inicial:","type"=>"text", "onpaste" => "return false;", 'value' => $this->data['GlbQuestionarioParametroFaixaDtUltPagamento']['valor_inicial']));
        echo $this->Form->input('valor_final', array("label"=>"Valor Final:","type"=>"text", "onpaste" => "return false;", 'value' => $this->data['GlbQuestionarioParametroFaixaDtUltPagamento']['valor_final']));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
