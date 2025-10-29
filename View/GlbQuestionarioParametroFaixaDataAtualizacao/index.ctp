<div class="glbQuestionarioParametroFaixaDataAtualizacao index">
    <h2><?php echo __('Filtro Data de Atualização'); ?></h2>
    <?php echo $this->Html->image("novoFiltro.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    <div style="width: 100%; text-align: right;">
        <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    </div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_faixa_data_atualizacao', 'Descrição'); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', 'Cadastro'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_inicial'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_final'); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioParametroFaixaDataAtualizacoes as $glbQuestionarioParametroFaixaDataAtualizacao): ?>
            <tr>
                <td><?php echo h($glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['ds_parametro_faix']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['dt_cad']); ?>&nbsp;</td>
                <td><?php echo $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['valor_inicial']; ?>&nbsp;</td>
                <td><?php echo $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['valor_final']; ?>&nbsp;</td>
<!--                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix']), null, __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['ds_parametro_faix'])); ?>
                </td>-->
                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['cd_parametro_faix']), array('escape' => false), __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaDataAtualizacao['GlbQuestionarioParametroFaixaDataAtualizacao']['ds_parametro_faix'])); ?>
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
