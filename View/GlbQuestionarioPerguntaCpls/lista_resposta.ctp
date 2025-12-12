<?php
echo $this->Html->script('jquery-ui.js');
?>
  <script>
  $(function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  });
  </script>
 
  <div class="glbQuestionarioPerguntaCpls index">
    <h2><?php echo __('Respostas'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <tr class="actions">
            <?php
            echo $this->Html->image("novaResposta.png", array("alt" => "Adicionar Resposta",'url' => array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'add', $this->params["pass"][0],$this->params["pass"][1])));
            echo "&nbsp;";
            echo $this->Html->image("ordenarResposta.png", array("alt" => "Ordenar Prioridades",'url' => array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'ordenar', $this->params["pass"][0],$this->params["pass"][1])));
            
            ?>
        </tr>
        <tr>
            <th><?php echo $this->Paginator->sort('prioridade'); ?></th>
            <th><?php echo $this->Paginator->sort('ds_pergunta_cpl', "Resposta"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', "Data de cadastro"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioPerguntaCpls as $glbQuestionarioPerguntaCpl): ?>
            <tr>
                <td><?php echo h($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['prioridade']); ?>&nbsp;</td>
                <td><?php echo ($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['dt_cad']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['id'],$this->params["pass"][0],$this->params["pass"][1])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['id'],$this->params["pass"][0],$this->params["pass"][1])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['id'], $this->params["pass"][0], $this->params['pass'][1]), null, __('Você deseja realmente deletar essa resposta # %s?', $glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['id'])); ?>
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
    <div class="actions">
        <?php echo $this->Html->link(__('Voltar'), array('controller' => 'GlbQuestionarioPerguntas', 'action' => 'index')); ?>
    </div>
</div>
