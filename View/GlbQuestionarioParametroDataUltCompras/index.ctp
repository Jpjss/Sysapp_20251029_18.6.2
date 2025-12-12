<div class="glbQuestionarioParametroDataUltCompras index">
    <h2><?php echo __('Filtro Data Última Compra'); ?></h2>
    <?php echo $this->Html->image("novoFiltro.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    <div style="width: 100%; text-align: right;">
        <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    </div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_data_ult_compra', 'Descrição'); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', 'Cadastro'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_inicial'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_final'); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioParametroDataUltCompras as $glbQuestionarioParametroDataUltCompra): ?>
            <tr>
                <td><?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['ds_parametro_data_ult_co']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['dt_cad']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['valor_inicial']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['valor_final']); ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co']), array('escape' => false), __('Você tem certeza que deseja excluir o filtro: %s?', $glbQuestionarioParametroDataUltCompra['GlbQuestionarioParametroDataUltCompra']['ds_parametro_data_ult_co'])); ?>
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
