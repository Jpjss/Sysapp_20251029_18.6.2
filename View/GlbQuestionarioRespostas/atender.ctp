<div class="glbQuestionarios index">
    <h2><?php echo __('Pesquisas para Atendimento'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('tipo_questionario', "Tipo"); ?></th>
            <th><?php echo $this->Paginator->sort('ds_questionario', "Descrição"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_vigencia_ini', "Inicio"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_vigencia_fim', "Fim"); ?></th>
            <th><?php echo $this->Paginator->sort('obs', "Observação"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarios as $glbQuestionario): ?>
            <tr>
                <td><?php
                echo $this->Html->image($this->Funcionalidades->imagemPesquisa($glbQuestionario['VwQuestionario']['tipo_questionario']));
                    ?>&nbsp;</td>
                <td><?php echo h($glbQuestionario['VwQuestionario']['questionario']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionario['VwQuestionario']['dt_vigencia_ini']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionario['VwQuestionario']['dt_vigencia_fim']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionario['VwQuestionario']['observacao_questionario']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('Atender'), array('controller' => 'GlbQuestionarioRespostas', 'action' => 'add', $glbQuestionario['VwQuestionario']['cd_questionario'], $glbQuestionario['VwQuestionario']['tipo_questionario'])); ?>
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
