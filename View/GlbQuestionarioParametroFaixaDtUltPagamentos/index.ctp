<div class="glbQuestionarioParametroFaixaDtUltPagamentos index">
    <h2><?php echo __('Filtro Data Últimos Pagamentos'); ?></h2>
    <?php echo $this->Html->image("novoFiltro.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    <div style="width: 100%; text-align: right;">
        <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    </div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_faixa_dt_ult_pagamento', 'Descrição'); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', 'Cadastro'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_inicial'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_final'); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioParametroFaixaDtUltPagamentos as $glbQuestionarioParametroFaixaDtUltPagamento): ?>
            <tr>
                <td><?php echo h($glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['ds_parametro_faixa']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['dt_cad']); ?>&nbsp;</td>
                <td><?php echo $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['valor_inicial']; ?>&nbsp;</td>
                <td><?php echo $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['valor_final']; ?>&nbsp;</td>
    <!--                <td class="actions">
                <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa'])); ?>
                <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa'])); ?>
                <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa']), null, __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['ds_parametro_faixa'])); ?>
                </td>-->
                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa']), array('escape' => false), __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaDtUltPagamento['GlbQuestionarioParametroFaixaDtUltPagamento']['ds_parametro_faixa'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Página {:page} de {:pages}, mostrando {:current} registros de um total de {:count}, iniciando em {:start}, finalizando em {:end}')
        ));
        ?>	</p>
    <div class="paging">
        <?php
        echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
        ?>
    </div>
</div>
