<div class="glbQuestionarioParametroFaixaQuantidadeCompras view">
    <h2><?php echo __('Filtro Quantidade de Compra'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Faixa'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['ds_parametro_fai']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo (int)($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['valor_inicial']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo (int)($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['valor_final']); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
