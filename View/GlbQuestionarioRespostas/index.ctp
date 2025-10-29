<div class="glbQuestionarioRespostas index">
    <h2><?php echo __('Atendimentos'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("novoAtendimento.png", array("alt" => "Atendimento", 'url' => array('action' => 'atender'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenuAtendimento.png", array("alt" => "Faixa")); ?>
        </div>
        <tr>
            <th><?php echo $this->Paginator->sort('cd_questionario', "Pesquisa"); ?></th>
            <th><?php echo $this->Paginator->sort('cd_pessoa', "Cliente"); ?></th>
            <th><?php echo $this->Paginator->sort('cd_usu_cad', "Atendente"); ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('hora_inicio', "Inicio"); ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('hora_fim', "Fim"); ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('dt_cad', "Data"); ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('status_atendimento', "Status"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioRespostas as $glbQuestionarioResposta): ?>
            <tr>
                <td><?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['cd_questionario']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['cd_pessoa']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['cd_usu_cad']); ?>&nbsp;</td>
                <td style="text-align: center;"><?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['hora_inicio']); ?>&nbsp;</td>
                <td style="text-align: center;"><?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['hora_fim']); ?>&nbsp;</td>
                <td style="text-align: center;"><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioResposta['GlbQuestionarioResposta']['dt_cad']); ?>&nbsp;</td>
                <td style="text-align: center;"><?php
                    switch ($glbQuestionarioResposta['GlbQuestionarioResposta']['status_atendimento']) {
                        case(0):
                            echo $this->Html->image('iniciado.png', array('alt' => 'Iniciado'));
                            break;
                        case(1):
                            echo $this->Html->image('semcontato.png', array('alt' => 'Sem Contato'));
                            break;
                        case(2):
                            echo $this->Html->image('concluido.png', array('alt' => 'Concluido'));
                            break;
                    }
                    ?>&nbsp;</td>

                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_resposta']))); ?>
                    <?php //echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_resposta']))); ?>
                    <?php // echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_resposta']), array('escape' => false), __('Você tem certeza de que deseja excluir o atendimento cod # %s?', $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_resposta'])); ?>
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
