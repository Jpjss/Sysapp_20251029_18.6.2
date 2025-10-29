<div class="glbQuestionarioParametroFaixaValorMedioCompras view">
    <h2><?php echo __('Filtro Média de Atraso'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Filtro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['ds_parametro_faixa_me']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['valor_inicial']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['valor_final']; ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
