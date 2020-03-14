<?php
	/*
		PasswordRecovery.ctp
		--
		Email template for the password recovery

		@param $user
		@param $token
		@param $expiration
	*/
?>Hello!

Someone (hopefully you) is trying to reset your password at <?php echo Configure::read('Solution.name'); ?>.

If you initiated this attempt and would like to reset your password click this link.
< <?php echo Router::url( array('controller' => 'recoveries', 'action' => 'token', $token), true); ?> >

If you did not create this password recovery request, please ignore this email.  If you have reason to believe that an adversary is attempting to compromise your account security, login immediately with your existing password to cancel your password request.

This password recovery attempt will expire on <?php echo date( 'F j, Y g:i a', strtotime( sprintf("+%s", $expiration) ) ); ?>.