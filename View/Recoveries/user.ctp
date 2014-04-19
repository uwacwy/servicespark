<?php
	/*
		user.ctp
		--
		recovery view to initiate user password recovery
	*/

?>

<div class="row">
	<div class="col-md-3">
		<h3><?php echo h( __( Configure::read('Solution.name') ) ); ?> <?php echo h( __('Account') ); ?></h3>
		<div class="list-group">
			<?php
				echo $this->Html->link( __('Create an Account'), array('controller' => 'users', 'action' => 'register'), array('class' => 'list-group-item') );
				echo $this->Html->link( __('Login'), array('controller' => 'users', 'action' => 'login'), array('class' => 'list-group-item') );
			?>
		</div>
	</div>
	<div class="col-md-9">
		<h2>Password Recovery</h2>
		<p class="text-muted">Type your username into this form and we'll do our best to help you regain access to your account.</p>
		<?php
			echo $this->Form->create('Recovery', $form_defaults);
				echo $this->Form->input('User.username');
			echo $this->Form->end( array( 'label' => 'Recover My Password', 'class' => 'btn btn-primary') );
		?>
	</div>
</div>