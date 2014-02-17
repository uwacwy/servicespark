<?php
	/* PasswordRecovery.ctp */
?>
Hello!

Someone has generated a password recovery attempt for you at [Product Name].

If you initiated this attempt and would like to reset your password click this link.
<?php echo $token; ?>

If you did not create this password recovery request, please ignore this email.  If you have reason to believe that an adversary is attempting to 

This password recovery attempt expires on <?php date( 'F j, Y g:i a', strtotime( sprintf("+%s", $expiration) ) ); ?>.

Thank you for using [Product Name].