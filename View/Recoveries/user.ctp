<?php
	/*
		user.ctp
		--
		recovery view to initiate user password recovery
	*/

?>

<div class=" form recovery user">
<?php echo $this->Form->create('Recovery'); ?>
<h2>Password Recovery</h2>
<p>Oh, no!  You forgot your password and you can't login to your account?  Type your username into this form and we'll do our best to help you regain access to your account.</p>
<?php
	echo $this->Form->input('User.username');
?>

<?php echo $this->Form->end('Recover My Password'); ?>
</div>