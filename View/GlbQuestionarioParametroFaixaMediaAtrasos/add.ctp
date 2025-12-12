<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });

        $('form').submit(function() {
            if (parseInt($('#GlbQuestionarioParametroFaixaMediaAtrasoValorFinal').val()) < parseInt($('#GlbQuestionarioParametroFaixaMediaAtrasoValorInicial').val())) {
                $("<div style='text-align: center;'>O valor final deve ser maior que o valor inicial.</div>").dialog();
                return false;
            }

        });
    });
</script>
<div class="glbQuestionarioParametroFaixaMediaAtrasos form">
    <?php echo $this->Form->create('GlbQuestionarioParametroFaixaMediaAtraso'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Filtro Média de Atraso'); ?></legend>
        <?php
        echo $this->Form->input('ds_parametro_faixa_media_atraso', array('label' => 'Descrição:'));
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
