<div class="glbQuestionarioParametroDataUltCompras view">
    <h2><?php echo __('Filtro Data Última Compra'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Faixa'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['ds_parametro_data_ult_co']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['valor_inicial']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['valor_final']); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
