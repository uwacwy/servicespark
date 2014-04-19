<?php

/*
	login.ctp
*/

?>
<div class="row">

	<div class="actions col-md-3">
		<h3><?php echo h( __( Configure::read('Solution.name') ) ); ?></h3>
		<div class="list-group">
			<?php
				echo $this->Html->link( __('Create an Account'), array('action' => 'register'), array('class' => 'list-group-item') );
				echo $this->Html->link( __('Forgot Password'), array('controller' => 'recoveries', 'action' => 'user'), array('class' => 'list-group-item') );
			?>
		</div>
	</div>

	<div class="col-md-9">
		<?php echo $this->Session->flash('auth'); ?>
		<?php echo $this->Form->create( 'User', $form_defaults); ?>
		<h3>Please enter your username and password</h3>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('password');
		?>
		<?php echo $this->Form->end( array('label' => __('Login to ServiceSpark'), 'class' => 'btn btn-primary btn-lg') ); ?>
	</div>


</div>