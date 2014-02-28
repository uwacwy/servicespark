<div class="organizations form">
<?php echo $this->Form->create('Organization'); ?>
	<fieldset>
		<legend><?php echo __('Edit Organization'); ?></legend>
	<?php
		echo $this->Form->input('organization_id');
		echo $this->Form->input('name');


		$i = 0;
		foreach($this->request->data['Address'] as $address)
		{
			echo $this->Form->input("Address.$i.address_id");
			foreach( array('mailing_address', 'mailing_city', 'mailing_state', 'mailing_zip','physical_address', 'physical_city', 'physical_state', 'physical_zip') as $field)
			{
				echo $this->Form->input("Address.$i.$field");
			}

			$i++;
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Organization.organization_id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Organization.organization_id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Organizations'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Permissions'), array('controller' => 'permissions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Permission'), array('controller' => 'permissions', 'action' => 'add')); ?> </li>
	</ul>
</div>
