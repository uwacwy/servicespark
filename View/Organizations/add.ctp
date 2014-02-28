<div class="organizations form">
<?php echo $this->Form->create('Organization'); ?>
	<fieldset>
		<legend><?php echo __('Add Organization'); ?></legend>
	<?php
		echo $this->Form->input('name');

		$fields = array('mailing_address', 'mailing_city', 'mailing_state', 'mailing_zip', 
		'physical_address', 'physical_city', 'physical_state', 'physical_zip');

		if(! empty($this->request->data['Address'])) {
			$i = 0;
			foreach ($this->request->data['Address'] as $address) {

				foreach($fields as $field) {
					echo $this->Form->input("Address.$field");
				}

				$i++;
			}
		} else {
			foreach($fields as $field) {
				echo $this->Form->input("Address.$field");
			}
		}

	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Organizations'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Permissions'), array('controller' => 'permissions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Permission'), array('controller' => 'permissions', 'action' => 'add')); ?> </li>
	</ul>
</div>
