<div class="glbQuestionarioPerguntaCpls view">
    <h2><?php echo __('Resposta'); ?></h2>
    <dl>
        <dt><?php echo __('Cod Pergunta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['cd_pergunta']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Resposta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Prioridade'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['prioridade']); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['id'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'listaResposta', $this->params['pass'][1], $this->params['pass'][2])); ?>
    </div>
</div>
