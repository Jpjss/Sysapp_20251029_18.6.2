<div class="glbQuestionarios index">
    <h2><?php echo __('Pesquisas'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("novaPesquisa.png", array("alt" => "Adicionar questionario", 'url' => array('action' => 'add'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenuPesquisa.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
        </div>
        <tr>
            <th><?php echo $this->Paginator->sort('tipo_questionario', "Tipo"); ?></th>
            <th><?php echo $this->Paginator->sort('ds_questionario', "Descrição"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_vigencia_ini', "Inicio"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_vigencia_fim', "Fim"); ?></th>
            <th><?php echo $this->Paginator->sort('obs', "Observação"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarios as $glbQuestionario):
            ?>
            <tr>
                <td><?php
                    echo $this->Html->image($this->Funcionalidades->imagemPesquisa($glbQuestionario['GlbQuestionario']['tipo_questionario']));
                    ?>&nbsp;</td>
                <td><div title="<?php echo $glbQuestionario['GlbQuestionario']['ds_questionario']; ?>"><?php echo substr($glbQuestionario['GlbQuestionario']['ds_questionario'], 0, 30); ?></div>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionario['GlbQuestionario']['dt_vigencia_ini']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionario['GlbQuestionario']['dt_vigencia_fim']); ?>&nbsp;</td>
                <td><div title="<?php echo $glbQuestionario['GlbQuestionario']['obs']; ?>"><?php echo substr($glbQuestionario['GlbQuestionario']['obs'], 0, 60); ?></div>&nbsp;</td>
    <!--                    <td class="actions">
                <?php echo $this->Html->link(__('Adicionar Perguntas'), array('controller' => 'GlbQuestionarioGlbQuestionarioPerguntas', 'action' => 'add', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
                <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
                <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
                <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionario['GlbQuestionario']['cd_questionario']), null, __('Tem certeza de que deseja excluir o questionario: %s?', $glbQuestionario['GlbQuestionario']['ds_questionario'])); ?>
                </td>-->
                <td style="min-width: 130px;">
                    <?php echo $this->Html->image("novaPerguntaIcon.png", array("alt" => "Visualizar", 'url' => array('controller' => 'GlbQuestionarioGlbQuestionarioPerguntas', 'action' => 'add', $glbQuestionario['GlbQuestionario']['cd_questionario']))); ?>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionario['GlbQuestionario']['cd_questionario']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionario['GlbQuestionario']['cd_questionario']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionario['GlbQuestionario']['cd_questionario']), array('escape' => false), __('Tem certeza de que deseja excluir o questionario: %s?', $glbQuestionario['GlbQuestionario']['ds_questionario'])); ?>
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
