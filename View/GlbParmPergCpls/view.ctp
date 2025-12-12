<div class="glbParmPergCpls view">
<h2><?php echo __('Glb Parm Perg Cpl'); ?></h2>
	<dl>
		<dt><?php echo __('Cd Perg'); ?></dt>
		<dd>
			<?php echo h($glbParmPergCpl['GlbParmPergCpl']['cd_perg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cd Perg Cpl'); ?></dt>
		<dd>
			<?php echo h($glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ds Perg Cpl'); ?></dt>
		<dd>
			<?php echo h($glbParmPergCpl['GlbParmPergCpl']['ds_perg_cpl']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cd Usu Cad'); ?></dt>
		<dd>
			<?php echo h($glbParmPergCpl['GlbParmPergCpl']['cd_usu_cad']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Dt Cad'); ?></dt>
		<dd>
			<?php echo h($glbParmPergCpl['GlbParmPergCpl']['dt_cad']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Glb Parm Perg Cpl'), array('action' => 'edit', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Glb Parm Perg Cpl'), array('action' => 'delete', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl']), null, __('Are you sure you want to delete # %s?', $glbParmPergCpl['GlbParmPergCpl']['cd_perg_cpl'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Glb Parm Perg Cpls'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg Cpl'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Glb Parm Pergs'), array('controller' => 'glb_parm_pergs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg'), array('controller' => 'glb_parm_pergs', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Glb Parm Pergs'); ?></h3>
	<?php if (!empty($glbParmPergCpl['GlbParmPerg'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Cd Perg'); ?></th>
		<th><?php echo __('Ds Perg'); ?></th>
		<th><?php echo __('Tp Perg'); ?></th>
		<th><?php echo __('Obs'); ?></th>
		<th><?php echo __('Cd Usu Cad'); ?></th>
		<th><?php echo __('Dt Cad'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($glbParmPergCpl['GlbParmPerg'] as $glbParmPerg): ?>
		<tr>
			<td><?php echo $glbParmPerg['cd_perg']; ?></td>
			<td><?php echo $glbParmPerg['ds_perg']; ?></td>
			<td><?php echo $glbParmPerg['tp_perg']; ?></td>
			<td><?php echo $glbParmPerg['obs']; ?></td>
			<td><?php echo $glbParmPerg['cd_usu_cad']; ?></td>
			<td><?php echo $glbParmPerg['dt_cad']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'glb_parm_pergs', 'action' => 'view', $glbParmPerg['cd_perg'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'glb_parm_pergs', 'action' => 'edit', $glbParmPerg['cd_perg'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'glb_parm_pergs', 'action' => 'delete', $glbParmPerg['cd_perg']), null, __('Are you sure you want to delete # %s?', $glbParmPerg['cd_perg'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Glb Parm Perg'), array('controller' => 'glb_parm_pergs', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
