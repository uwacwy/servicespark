<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		debug($this->request->data);

		echo $this->Form->input('user_id');
		echo $this->Form->input('username');
		echo $this->Form->input('password_l', array('type' => 'password', 'label' => "New Password") );
		echo $this->Form->input('password_r', array('type' => 'password', 'label' => "Confirm Password") );
		echo $this->Form->input('email');
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('Skill');

		$fields = array('mailing_address', 'mailing_city', 'mailing_state', 'mailing_zip', 
			'physical_address', 'physical_city', 'physical_state', 'physical_zip');

		if(! empty($this->request->data['Address'])) {
			$i = 0;
			foreach ($this->request->data['Address'] as $address) {
				echo $this->Form->input("Address.$i.address_id");

				foreach($fields as $field) {
					echo $this->Form->input("Address.$i.$field");
				}

				$i++;
			}
		} else {
			foreach($fields as $field) {
				echo $this->Form->input("Address.0.$field");
			}
		}


	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('User.user_id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('User.user_id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>
