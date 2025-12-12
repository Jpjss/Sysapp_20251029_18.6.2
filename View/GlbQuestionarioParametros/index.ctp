<div class="glbQuestionarioRespostas index">
    <h2><?php echo __('Parâmetros'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("novoParametro.png", array("alt" => "Parametro", 'url' => array('action' => 'parametros'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
        </div>
        <tr>
            <th><?php echo $this->Paginator->sort('ds_parametro_questionario', "Descrição"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_cad', "Data"); ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('sts_parametro_cobranca', "Status"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbQuestionarioParametro as $parametro):?>
            <tr>
                <td><?php echo h($parametro['GlbQuestionarioParametro']['ds_parametro_questionario']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($parametro['GlbQuestionarioParametro']['dt_cad']); ?>&nbsp;</td>
                <td style="text-align: center;">
                    <?php
                    switch ($parametro['GlbQuestionarioParametro']['sts_parametro_cobranca']) {
                        case(0):
                            echo $this->Html->image('inativo.png', array('alt' => 'Inativo'));
                            break;
                        case(1):
                            echo $this->Html->image('ativo.png', array('alt' => 'Ativo'));
                            break;
                        case(2):
                            echo $this->Html->image('cancelado.png', array('alt' => 'Cancelado'));
                            break;
                    }
                    ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $parametro['GlbQuestionarioParametro']['cd_parametro_questionario']))); ?>
                    <?php 
                    if($parametro['GlbQuestionarioParametro']['tipo_questionario'] == 0){
                        echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $parametro['GlbQuestionarioParametro']['cd_parametro_questionario']))); 
                    }else{
                        echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'editAniversariantes', $parametro['GlbQuestionarioParametro']['cd_parametro_questionario']))); 
                        
                    }
                    ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $parametro['GlbQuestionarioParametro']['cd_emp'], $parametro['GlbQuestionarioParametro']['cd_parametro_questionario']), array('escape' => false), __('Você tem certeza de que deseja excluir o parametro: %s?', $parametro['GlbQuestionarioParametro']['ds_parametro_questionario'])); ?>
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
