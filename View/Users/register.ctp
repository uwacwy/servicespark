<?php
	/*
		Add.ctp
		--
		I have also annotated this form, since it shows the mechanics of GETTING data
	*/
?>
<div class="users form">
<?php
	/*
		The Form component has a lot of handy methods that generate HTML for us.

		This small blob of PHP should echo something like
		<form action="/users/add" id="UserAddForm" method="post" accept-charset="utf-8">

	*/
?>
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Add User'); // again, look at the use of the __() method ?></legend>
	<?php

		echo $this->Form->input('username');
		echo $this->Form->input('password_l', array('type' => 'password', 'label' => "Password") );
		echo $this->Form->input('password_r', array('type' => 'password', 'label' => "Confirm Password") );
		echo $this->Form->input('email');
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('Skill');

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
<?php echo $this->Form->end(__('Submit')); // this will output the submit button and close the open form tag. ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>
