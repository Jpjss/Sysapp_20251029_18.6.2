<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });
    });
</script>
<div class="glbQuestionarioRespostaCpls form">
    <?php echo $this->Form->create('GlbQuestionarioRespostaCpl'); ?>
    <fieldset>
        <legend><?php echo __('Edit Glb Questionario Resposta Cpl'); ?></legend>
        <?php
        echo $this->Form->input('cd_resposta');
        echo $this->Form->input('cd_pergunta');
        echo $this->Form->input('cd_pergunta_cpl');
        echo $this->Form->input('ds_resposta');
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
