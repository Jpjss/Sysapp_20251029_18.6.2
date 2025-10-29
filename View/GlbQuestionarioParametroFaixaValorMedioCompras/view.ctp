<div class="glbQuestionarioParametroFaixaValorMedioCompras view">
    <h2><?php echo __('Filtro Valor Médio de Compra'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Faixa'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['cd_parametro_fai']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['ds_parametro_fai']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->moeda($glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['valor_inicial'], array('negative' => '-')); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->moeda($glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['valor_final'], array('negative' => '-')); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaValorMedioCompra['GlbQuestionarioParametroFaixaValorMedioCompra']['cd_parametro_fai'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
