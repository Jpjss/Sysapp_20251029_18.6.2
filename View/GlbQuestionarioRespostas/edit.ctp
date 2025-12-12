<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });
    });
</script>
<div class="glbQuestionarioRespostas form">
    <?php echo $this->Form->create('GlbQuestionarioResposta'); ?>
    <fieldset>
        <legend><?php echo __('Editar Glb Questionario Resposta'); ?></legend>
        <?php
        echo $this->Form->input('cd_resposta');
        echo $this->Form->input('cd_questionario');
        echo $this->Form->input('cd_pessoa');
        echo $this->Form->input('cd_usu_cad');
        echo $this->Form->input('hora_inicio');
        echo $this->Form->input('hora_fim');
        echo $this->Form->input('status_atendimento');
        echo $this->Form->input('dt_cad');
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 20px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
            <td><?php echo $this->Form->end(__('Submit')); ?></td>
        </tr>
    </table>
</div>
