<div class="glbParmPergs index">
    <h2><?php echo __('Perguntas'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('ds_perg',"Pergunta"); ?></th>
            <th><?php echo $this->Paginator->sort('tp_perg',"Tipo"); ?></th>
            <th><?php echo $this->Paginator->sort('obs',"Observação"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbParmPergs as $glbParmPerg): ?>
            <tr>
                <td><?php echo h($glbParmPerg['GlbParmPerg']['ds_perg']); ?>&nbsp;</td>
                <td><?php
                switch ($glbParmPerg['GlbParmPerg']['tp_perg']){
                    case(0):
                        echo "SIM/NÃO";
                        break;
                    case(1):
                        echo "Dissertativa";
                        break;
                    case(2):
                        echo "Pontuação";
                        break;
                }
                
                
                ?>&nbsp;</td>
                <td><?php echo h($glbParmPerg['GlbParmPerg']['obs']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbParmPerg['GlbParmPerg']['cd_perg']), null, __('Tem certeza que deseja excluir # %s?', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Página {:page} de {:pages}, mostrando {:current} registros de um total de {:count} , começando em {:start}, terminando em {:end}')
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
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('Nova Pergunta'), array('action' => 'add')); ?></li>
        <li><?php echo $this->Html->link(__('Formulario Perguntas'), array('controller'=>'formulario','action' => 'formulario')); ?></li>
    </ul>
</div>
