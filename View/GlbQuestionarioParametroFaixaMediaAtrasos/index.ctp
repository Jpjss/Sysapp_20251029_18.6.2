<div class="glbQuestionarioParametroFaixaValorMedioCompras index">
    <h2><?php echo __('Filtro Média de Atrasos'); ?></h2>
    <?php echo $this->Html->image("novoFiltro.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    <div style="width: 100%; text-align: right;">
        <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
    </div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_faixa_media_atraso', 'Descrição'); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', 'Cadastro'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_inicial'); ?></th>
            <th><?php echo $this->Paginator->sort('valor_final'); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioParametroFaixaMediaAtrasos as $glbQuestionarioParametroFaixaMediaAtraso): ?>
            <tr>
                <td><?php echo h($glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['ds_parametro_faixa_me']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['dt_cad']); ?>&nbsp;</td>
                <td><?php echo $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['valor_inicial']; ?>&nbsp;</td>
                <td><?php echo $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['valor_final']; ?>&nbsp;</td>
    <!--                <td class="actions">
                <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me'])); ?>
                <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me'])); ?>
                <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me']), null, __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['ds_parametro_faixa_me'])); ?>
                </td>-->
                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me']), array('escape' => false), __('Você tem certeza que deseja deletar o filtro: %s?', $glbQuestionarioParametroFaixaMediaAtraso['GlbQuestionarioParametroFaixaMediaAtraso']['ds_parametro_faixa_me'])); ?>
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
