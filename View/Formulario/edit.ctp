<div class="glbParmPergs form">
<?php echo $this->Form->create('GlbParmPerg'); ?>
	<fieldset>
		<legend><?php echo __('Editar Pergunta'); ?></legend>
	<?php
		echo $this->Form->input('cd_perg');
		echo $this->Form->input('ds_perg',array("label"=>"Descrição"));
		echo $this->Form->input('tp_perg',array("label"=>"Tipo de pergunta"));
		echo $this->Form->input('obs',array("label"=>"Observação"));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('GlbParmPerg.cd_perg')), null, __('Você tem certeza que deseja excluir # %s?', $this->Form->value('GlbParmPerg.cd_perg'))); ?></li>
		<li><?php echo $this->Html->link(__('List Glb Parm Pergs'), array('action' => 'index')); ?></li>
	</ul>
</div>
