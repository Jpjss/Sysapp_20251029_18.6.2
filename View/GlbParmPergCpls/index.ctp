<div class="glbParmPergCpls index">
	<h2><?php echo __('Glb Parm Perg Cpls'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('cd_perg'); ?></th>
			<th><?php echo $this->Paginator->sort('cd_perg_cpl'); ?></th>
			<th><?php echo $this->Paginator->sort('ds_perg_cpl'); ?></th>
			<th><?php echo $this->Paginator->sort('cd_usu_cad'); ?></th>
			<th><?php echo $this->Paginator->sort('dt_cad'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($glbParmPergCpls as $glbParmPergCpl): ?>
	<tr>
		<td><?php echo h($glbParmPergCpl['GlbParmPergCpl']['cd_perg']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPergCpl['GlbParmPergCpl']['ds_perg_cpl']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPergCpl['GlbParmPergCpl']['cd_usu_cad']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPergCpl['GlbParmPergCpl']['dt_cad']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl']), null, __('Are you sure you want to delete # %s?', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl'])); ?>
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
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg Cpl'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Pergs'), array('controller' => 'glb_parm_pergs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg'), array('controller' => 'glb_parm_pergs', 'action' => 'add')); ?> </li>
	</ul>
</div>
