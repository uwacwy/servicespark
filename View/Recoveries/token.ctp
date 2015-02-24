<?php
	/*
		token.ctp
	*/
?>

<div class="form users recoveries recover">
	<?php echo $this->Form->create('Recovery', $form_defaults); ?>

	<h2>Recovering Password</h2>
	<p>Please type a new password and confirm the password.</p>

		<?php echo $this->Form->input('User.password_l', array('type'=>'password', 'label' => 'New Password') ); ?>
		<?php echo $this->Form->input('User.password_r', array('type'=>'password', 'label' => 'Confirm New Password') ); ?>


	<?php echo $this->Form->end( array( 'label' => 'Recover My Password', 'class' => 'btn btn-primary') ); ?>
</div>