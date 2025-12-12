<div class="glbQuestionarioParametroFaixaQuantidadeCompras index">
    <h2><?php echo __('Filtro Quantidade de Compras'); ?></h2>
    <?php echo $this->Html->image("novoFiltro.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    <div style="width: 100%; text-align: right;">
        <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    </div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_faixa_quantidade_compra', 'Descrição'); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', 'Cadastro'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_inicial', 'Inicial'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_final', 'Final'); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioParametroFaixaQuantidadeCompras as $glbQuestionarioParametroFaixaQuantidadeCompra): ?>
            <tr>
                <td><?php echo h($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['ds_parametro_fai']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['dt_cad']); ?>&nbsp;</td>
                <td><?php echo (int) $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['valor_inicial']; ?>&nbsp;</td>
                <td><?php echo (int) $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['valor_final']; ?>&nbsp;</td>
    <!--                <td class="actions">
                <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai'])); ?>
                <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai'])); ?>
                <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai']), null, __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['ds_parametro_fai'])); ?>
                </td>-->

                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['cd_parametro_fai']), array('escape' => false), __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaQuantidadeCompra['GlbQuestionarioParametroFaixaQuantidadeCompra']['ds_parametro_fai'])); ?>
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
