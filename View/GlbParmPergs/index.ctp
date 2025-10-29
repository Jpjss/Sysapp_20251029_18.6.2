<div class="glbParmPergs index">
	<h2><?php echo __('Glb Parm Pergs'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('cd_perg'); ?></th>
			<th><?php echo $this->Paginator->sort('ds_perg'); ?></th>
			<th><?php echo $this->Paginator->sort('tp_perg'); ?></th>
			<th><?php echo $this->Paginator->sort('obs'); ?></th>
			<th><?php echo $this->Paginator->sort('cd_usu_cad'); ?></th>
			<th><?php echo $this->Paginator->sort('dt_cad'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($glbParmPergs as $glbParmPerg): ?>
	<tr>
		<td><?php echo h($glbParmPerg['GlbParmPerg']['cd_perg']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPerg['GlbParmPerg']['ds_perg']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPerg['GlbParmPerg']['tp_perg']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPerg['GlbParmPerg']['obs']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPerg['GlbParmPerg']['cd_usu_cad']); ?>&nbsp;</td>
		<td><?php echo h($glbParmPerg['GlbParmPerg']['dt_cad']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbParmPerg['GlbParmPerg']['cd_perg']), null, __('Are you sure you want to delete # %s?', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
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
		<li><?php echo $this->Html->link(__('New Glb Parm Perg'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Perg Cpls'), array('controller' => 'glb_parm_perg_cpls', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg Cpl'), array('controller' => 'glb_parm_perg_cpls', 'action' => 'add')); ?> </li>
	</ul>
</div>
