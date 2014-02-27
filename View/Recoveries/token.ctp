<?php
	/*
		token.ctp
	*/
?>

<div class="form users recoveries recover">
	<?php echo $this->Form->create('Recovery'); ?>

	<h2>Recovering Password</h2>
	<p>Please type a new password and confirm the password.</p>

		<?php echo $this->Form->input('User.password_l', array('type'=>'password', 'label' => 'New Password') ); ?>
		<?php echo $this->Form->input('User.password_r', array('type'=>'password', 'label' => 'Confirm New Password') ); ?>


	<?php echo $this->Form->end('Recover My Account'); ?>
</div>