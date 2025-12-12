<div class="glbQuestionarioGlbQuestionarioPerguntas view">
    <h2><?php echo __('Glb Questionario Glb Questionario Pergunta'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Questionario'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Cd Pergunta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_pergunta']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Prioridade'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['prioridade']); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
