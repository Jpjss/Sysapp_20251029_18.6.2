<div class="glbParmPergs view">
<h2><?php echo __('Glb Parm Perg'); ?></h2>
	<dl>
		<dt><?php echo __('Cd Perg'); ?></dt>
		<dd>
			<?php echo h($glbParmPerg['GlbParmPerg']['cd_perg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ds Perg'); ?></dt>
		<dd>
			<?php echo h($glbParmPerg['GlbParmPerg']['ds_perg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tp Perg'); ?></dt>
		<dd>
			<?php echo h($glbParmPerg['GlbParmPerg']['tp_perg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Obs'); ?></dt>
		<dd>
			<?php echo h($glbParmPerg['GlbParmPerg']['obs']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cd Usu Cad'); ?></dt>
		<dd>
			<?php echo h($glbParmPerg['GlbParmPerg']['cd_usu_cad']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Dt Cad'); ?></dt>
		<dd>
			<?php echo h($glbParmPerg['GlbParmPerg']['dt_cad']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Glb Parm Perg'), array('action' => 'edit', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Glb Parm Perg'), array('action' => 'delete', $glbParmPerg['GlbParmPerg']['cd_perg']), null, __('Are you sure you want to delete # %s?', $glbParmPerg['GlbParmPerg']['cd_perg'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Glb Parm Pergs'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Glb Parm Perg Cpls'), array('controller' => 'glb_parm_perg_cpls', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg Cpl'), array('controller' => 'glb_parm_perg_cpls', 'action' => 'add')); ?> </li>
	</ul>
</div>
