<?php

/*
	login.ctp
*/

?>
<div class="row">

	<div class="actions col-md-3">
		<h3><?php echo __('Actions'); ?></h3>
		<ul class="nav nav-stacked">
			<li><?php echo $this->Html->link(__('Create an Account'), array('action' => 'register')); ?></li>
		</ul>
	</div>

	<div class="col-md-9">
		<?php echo $this->Session->flash('auth'); ?>
		<?php echo $this->Form->create( 'User',
			array(
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control'
				)
			)
		); ?>
		<h3>Please enter your username and password</h3>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('password');
		?>
		<?php echo $this->Form->end(__('Login'), array('class' => 'btn btn-primary') ); ?>
	</div>


</div>