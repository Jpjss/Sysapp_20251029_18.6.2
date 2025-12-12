<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
            
        });
    });
</script>
<div class="glbQuestionarioGlbQuestionarioPerguntas form">
    <?php echo $this->Form->create('GlbQuestionarioGlbQuestionarioPergunta'); ?>
    <fieldset>
        <legend><?php echo __('Edit Glb Questionario Glb Questionario Pergunta'); ?></legend>
        <?php
        echo $this->Form->input('cd_questionario');
        echo $this->Form->input('cd_pergunta');
        echo $this->Form->input('prioridade');
        ?>
    </fieldset>
        <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
