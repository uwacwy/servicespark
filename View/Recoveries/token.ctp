<?php
	/*
		token.ctp
	*/
?>

<div class="users recoveries recover">
	<?php echo $this->Form->create('Recovery'); ?>

		<?php echo $this->Form->input('User.password_l', array('type'=>'password', 'label' => 'New Password') ); ?>
		<?php echo $this->Form->input('User.password_r', array('type'=>'password', 'label' => 'Confirm New Password') ); ?>


	<?php echo $this->Form->end('Recover My Account'); ?>
</div>