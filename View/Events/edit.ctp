<div class="events form">
<?php echo $this->Form->create('Event'); ?>
	<fieldset>
		<legend><?php echo __('Edit Event'); ?></legend>
	<?php
		echo $this->Form->input('event_id');
		echo $this->Form->input('title');
		echo $this->Form->input('description');
		echo $this->Form->input('start_time');
		echo $this->Form->input('stop_time');
		echo $this->Form->input('Skill');


		$i = 0;
		foreach($this->request->data['Address'] as $address)
		{
			switch ($address['type'])
			{
				case 'physical':
					echo '<h3>Physical Address</h3>';
					break;
				case 'mailing':
					echo '<h3>Mailing Address</h3>';
					break;
				case 'both':
					echo '<h3>Physical and Mailing Address</h3>';
					break;
			}
			echo $this->Form->input("Address.$i.address_id");
			foreach( array('address1', 'address2', 'city', 'state', 'zip') as $field)
			{
				echo $this->Form->input("Address.$i.$field");
			}

			$i++;
		}

		//debug($this->request->data);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Event.event_id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Event.event_id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Events'), array('action' => 'index')); ?></li>

		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill'), array('controller' => 'skills', 'action' => 'add')); ?> </li>

	</ul>
</div>
