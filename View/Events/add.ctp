<div class="events form">
<?php echo $this->Form->create('Event'); ?>
	<fieldset>
		<legend><?php echo __('Add Event'); ?></legend>
	<?php
		//echo $this->Form->input('event_id');
		echo $this->Form->input('title');
		echo $this->Form->input('description');
		echo $this->Form->input('start_time');
		echo $this->Form->input('stop_time');


		//echo $this->Form->input('Address');
		echo $this->Form->input('Address.address_id');
		echo $this->Form->input('Address.mailing_address');
		echo $this->Form->input('Address.mailing_city');
		echo $this->Form->input('Address.mailing_state');
		echo $this->Form->input('Address.mailing_zip');
		echo $this->Form->input('Address.physical_address');
		echo $this->Form->input('Address.physical_city');
		echo $this->Form->input('Address.physical_state');
		echo $this->Form->input('Address.physical_zip');

	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Events'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill'), array('controller' => 'skills', 'action' => 'add')); ?> </li>

	</ul>
</div>
