<div class="glbQuestionarioRespostaCpls form">
    <?php echo $this->Form->create('GlbQuestionarioRespostaCpl'); ?>
    <fieldset>
        <legend><?php echo __('Add Glb Questionario Resposta Cpl'); ?></legend>
        <?php
        echo $this->Form->input('cd_resposta');
        echo $this->Form->input('cd_pergunta');
        echo $this->Form->input('cd_pergunta_cpl');
        echo $this->Form->input('ds_resposta');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>
