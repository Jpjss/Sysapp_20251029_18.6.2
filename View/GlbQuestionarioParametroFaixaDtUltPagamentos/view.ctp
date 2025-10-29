<div class="glbQuestionarioParametroFaixaValorMedioCompras view">
    <h2><?php echo __('Filtro Data Último Pagamento'); ?></h2>
    <dl>
        <dt><?php echo __('Cd Filtro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['ds_parametro_faixa']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Inicial'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['valor_inicial']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Final'); ?></dt>
        <dd>
            <?php echo $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['valor_final']; ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
