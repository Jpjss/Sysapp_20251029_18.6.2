<div class="glbQuestionarioPerguntas index">
    <h2><?php echo __('Perguntas'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("novaPergunta.png", array("alt" => "Adicionar pergunta", 'url' => array('action' => 'add'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
        </div>
        <tr>
            <th><?php echo $this->Paginator->sort('ds_pergunta', "Descrição"); ?></th>
            <th><?php echo $this->Paginator->sort('tp_pergunta', "Tipo"); ?></th>
            <th><?php echo $this->Paginator->sort('tp_pergunta', " "); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioPerguntas as $glbQuestionarioPergunta): ?>
            <tr>
                <td><?php echo (substr($glbQuestionarioPergunta['GlbQuestionarioPergunta']['ds_pergunta'], 0, 110));
            ?>&nbsp;</td>
                <td><?php
                    switch ($glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta']) {
                        case(0):
                            echo "Opções";
                            break;
                        case(1):
                            echo "Dissertativa";
                            break;
                        case(2):
                            echo "Pontuação";
                            break;
                        case(3):
                            echo "Sugestão";
                            break;
                    };
                    ?>&nbsp;</td>
                <td class="actions">
                    <?php
                    if ($glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta'] != 1 && $glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta'] != 3) {
                        echo $this->Html->link(__('Adicionar Resposta'), array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'add', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta'], $glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta']));
                        echo $this->Html->link(__('Visualizar Respostas'), array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'listaResposta', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta'], $glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta']));
                    } else {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    ?>
                </td>
                <td style="min-width: 100px;">
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta']), array('escape' => false), __('Tem certeza de que deseja excluir a pergunta:  %s', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['ds_pergunta'])); ?>
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
