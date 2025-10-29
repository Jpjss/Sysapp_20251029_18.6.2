<div class="glbQuestionarioParametroFaixaValorMedioCompras view">
    <h2><?php echo __('Filtro Data de Atualização'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Filtro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['ds_parametro_faix']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['valor_inicial']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['valor_final']; ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
