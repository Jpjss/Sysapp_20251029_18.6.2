<div class="glbQuestionarios index">
    <h2><?php echo __('Controle de Permissões'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("novaPermissao.png", array("alt" => "Adicionar Permissão a Usuário", 'url' => array('action' => 'addPermissoes'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
        </div>
        <tr>
            <th><?php echo @$this->Paginator->sort('nm_usu', "Usuário"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($usuarios as $usuario):
            ?>
            <tr>
                <td><?php echo $usuario[0]['nm_usu'];?></td>
                <td style="min-width: 130px;">
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'viewPermissoes', $usuario[0]['cd_usu']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'editPermissoes', $usuario[0]['cd_usu']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'deletePermissoes', $usuario[0]['cd_usu']), array('escape' => false), __('Tem certeza de que deseja excluir as permissões de: %s?', $usuario[0]['nm_usu'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<!--    <p>
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
    </div>-->
</div>
