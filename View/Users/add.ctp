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
		echo $this->Form->input('username'); // you can echo a form input.  You can pass all kinds of arguments to the Form::input() function to customize the function
		echo $this->Form->input('password');
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
