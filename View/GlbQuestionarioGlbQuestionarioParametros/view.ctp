<div class="glbQuestionarioRespostas view">
    <h2><?php echo __('Pesquisa &hArr; Parâmetro'); ?></h2>
    <dl>
        <dt><?php echo __('Pesquisa'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioGlbQuestionarioParametro['gQuestionario']['ds_questionario']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Parâmetro'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioGlbQuestionarioParametro['gQparametro']['ds_parametro_questionario']; ?>
            &nbsp;
        </dd>

    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioGlbQuestionarioParametro['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
