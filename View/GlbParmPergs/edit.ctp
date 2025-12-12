<div class="glbParmPergs form">
<?php echo $this->Form->create('GlbParmPerg'); ?>
	<fieldset>
		<legend><?php echo __('Edit Glb Parm Perg'); ?></legend>
	<?php
		echo $this->Form->input('cd_perg');
		echo $this->Form->input('ds_perg');
		echo $this->Form->input('tp_perg');
		echo $this->Form->input('obs');
		echo $this->Form->input('cd_usu_cad');
		echo $this->Form->input('dt_cad');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('GlbParmPerg.cd_perg')), null, __('Are you sure you want to delete # %s?', $this->Form->value('GlbParmPerg.cd_perg'))); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Pergs'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Perg Cpls'), array('controller' => 'glb_parm_perg_cpls', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Glb Parm Perg Cpl'), array('controller' => 'glb_parm_perg_cpls', 'action' => 'add')); ?> </li>
	</ul>
</div>
