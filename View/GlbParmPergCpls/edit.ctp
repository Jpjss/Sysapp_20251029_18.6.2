<div class="glbParmPergCpls form">
<?php echo $this->Form->create('GlbParmPergCpl'); ?>
	<fieldset>
		<legend><?php echo __('Edit Glb Parm Perg Cpl'); ?></legend>
	<?php
		echo $this->Form->input('cd_perg');
		echo $this->Form->input('cd_perg_cpl');
		echo $this->Form->input('ds_perg_cpl');
		echo $this->Form->input('cd_usu_cad');
		echo $this->Form->input('dt_cad');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('GlbParmPergCpl.cd_perg_cpl')), null, __('Are you sure you want to delete # %s?', $this->Form->value('GlbParmPergCpl.cd_perg_cpl'))); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Perg Cpls'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Pergs'), array('controller' => 'glb_parm_pergs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg'), array('controller' => 'glb_parm_pergs', 'action' => 'add')); ?> </li>
	</ul>
</div>
