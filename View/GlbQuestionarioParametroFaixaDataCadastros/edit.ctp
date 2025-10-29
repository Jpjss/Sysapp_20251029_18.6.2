<script>
    $(document).ready(function() {
        
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
            
        });
        
        $('form').submit(function() {
            if (parseInt($('#GlbQuestionarioParametroFaixaDataCadastroValorFinal').val()) < parseInt($('#GlbQuestionarioParametroFaixaDataCadastroValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });
    });
</script>
<div class="glbQuestionarioParametroFaixaDataCadastros form">
    <?php echo $this->Form->create('GlbQuestionarioParametroFaixaDataCadastro'); ?>
    <fieldset>
        <legend><?php echo __('Editar Filtro Data de Cadastro'); ?></legend>
        <?php
        echo $this->Form->input('cd_parametro_faixa_data_cadastro', array('value' => $this->data['GlbQuestionarioParametroFaixaDataCadastro']['cd_parametro_faixa_d']));
        echo $this->Form->input('ds_parametro_faixa_data_cadastro', array("label"=>"Descrição:", 'value' => $this->data['GlbQuestionarioParametroFaixaDataCadastro']['ds_parametro_faixa_d']));
        echo $this->Form->input('valor_inicial', array("label"=>"Valor Inicial:","type"=>"text", "onpaste" => "return false;", 'value' => $this->data['GlbQuestionarioParametroFaixaDataCadastro']['valor_inicial']));
        echo $this->Form->input('valor_final', array("label"=>"Valor Final:","type"=>"text", "onpaste" => "return false;", 'value' => $this->data['GlbQuestionarioParametroFaixaDataCadastro']['valor_final']));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
