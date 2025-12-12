<div class="glbQuestionarioRespostaCpls view">
    <h2><?php echo __('Glb Questionario Resposta Cpl'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Resposta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioRespostaCpl['GlbQuestionarioRespostaCpl']['cd_resposta']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Cd Pergunta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioRespostaCpl['GlbQuestionarioRespostaCpl']['cd_pergunta']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Cd Pergunta Cpl'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioRespostaCpl['GlbQuestionarioRespostaCpl']['cd_pergunta_cpl']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Ds Resposta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioRespostaCpl['GlbQuestionarioRespostaCpl']['ds_resposta']); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
