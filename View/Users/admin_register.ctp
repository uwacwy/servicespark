<?php
	/*
		Add.ctp
		--
		I have also annotated this form, since it shows the mechanics of GETTING data
	*/
?>
<div class="actions col-md-3">
	<h3><?php echo __('Actions'); ?></h3>
	<ul class="nav nav-stacked nav-pills">

		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>
<div class="users form col-md-9">
<?php
	/*
		The Form component has a lot of handy methods that generate HTML for us.

		This small blob of PHP should echo something like
		<form action="/users/add" id="UserAddForm" method="post" accept-charset="utf-8">

	*/
?>
<?php echo $this->Form->create('User', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	))); ?>
	<fieldset>
		<legend><?php echo __('Add User'); // again, look at the use of the __() method ?></legend>
	<?php

		echo $this->Form->input('username');
		echo "<p>Password will be generated and emailed to the user</p>";
		echo $this->Form->input('email');
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('Skill');


		$address_types = array('physical', 'mailing', 'both');

		$i = 0;
		foreach($address_types as $address_type)
		{
			switch ($address_type)
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

			echo $this->Form->input("Address.$i.type", array('type' => "hidden", 'value' => $address_type));
			foreach( array('address1', 'address2', 'city', 'state', 'zip') as $field)
			{
				echo $this->Form->input("Address.$i.$field");
			}

			$i++;
		}	
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); // this will output the submit button and close the open form tag. ?>
</div>

