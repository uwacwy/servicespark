<?php
	/*
		PasswordRecovery.ctp
		--
		Email template for the password recovery

		@param $user
		@param $token
		@param $expiration
	*/
?>
Hello, <?php echo $user['User']['first_name']; ?>!

Someone has generated a password recovery attempt for you at <?php echo Configure::read('Solution.name'); ?>.

If you initiated this attempt and would like to reset your password click this link.
<<?php echo $this->Html->url( array('controller' => 'recoveries', 'action' => 'token', $token), true); ?>>

If you did not create this password recovery request, please ignore this email.  If you have reason to believe that an adversary is attempting to compromise your account security, login immediately and contact support.

This password recovery attempt will expire on <?php echo date( 'F j, Y g:i a', strtotime( sprintf("+%s", $expiration) ) ); ?>.

Thank you for using <?php echo Configure::read('Solution.name'); ?>.