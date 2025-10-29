<div class="glbQuestionarioRespostas index">
    <h2><?php echo __('Pesquisa &hArr; Parâmetros'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("relacionarPesquisaParametro.png", array("alt" => "Pesquisa / Parâmetro", 'url' => array('action' => 'add'))); ?>&nbsp;&nbsp;
        <?php echo $this->Html->image("relacionarPesquisaParametroAniversariantes.png", array("alt" => "Pesquisa / Parâmetro", 'url' => array('action' => 'add_aniversariantes'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
        </div>
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_questionario', "Pesquisa"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', "Parâmetro"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioGlbQuestionarioParametro as $value): ?>
            <tr>
                <td><?php echo h($value['gQuestionario']['ds_questionario']); ?>&nbsp;</td>
                <td><?php echo h($value['gQparametro']['ds_parametro_questionario']); ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $value['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $value['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $value['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario']), array('escape' => false), __('Você tem certeza de que deseja excluir essa relação: %s?', "Pesquisa: " . $value['gQuestionario']['ds_questionario'] . ". Parâmetro: " . $value['gQparametro']['ds_parametro_questionario'])); ?>
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
