<div class="addresses form">
<?php echo $this->Form->create('Address'); ?>
	<fieldset>
		<legend><?php echo __('Edit Address'); ?></legend>
	<?php
		echo $this->Form->input('address_id');
		echo $this->Form->input('mailing_address');
		echo $this->Form->input('mailing_city');
		echo $this->Form->input('mailing_state');
		echo $this->Form->input('mailing_zip');
		echo $this->Form->input('physical_address');
		echo $this->Form->input('physical_city');
		echo $this->Form->input('physical_state');
		echo $this->Form->input('physical_zip');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Address.address_id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Address.address_id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Addresses'), array('action' => 'index')); ?></li>
	</ul>
</div>
