<div class="glbQuestionarioParametroFaixaValorUltCompras view">
    <h2><?php echo __('Filtro Valor Última Compra'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Faixa'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['cd_parametro_faixa']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['ds_parametro_faixa']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário Cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data Cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->moeda($glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['valor_inicial'], array('negative' => '-')); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->moeda($glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['valor_final'], array('negative' => '-')); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaValorUltCompra['GlbQuestionarioParametroFaixaValorUltCompra']['cd_parametro_faixa'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
