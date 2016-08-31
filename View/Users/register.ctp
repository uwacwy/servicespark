<?php
	/*
		Add.ctp
		--
		I have also annotated this form, since it shows the mechanics of GETTING data
	*/
?>

<div class="row">
	<div class="col-md-3">
		<h3>Welcome to ServiceSpark</h3>
		<div class="list-group">
			<?php
				echo $this->Html->link( __('Login'), array('action' => 'register'), array('class' => 'list-group-item') );
				echo $this->Html->link( __('Forgot Password'), array('controller' => 'recoveries', 'action' => 'user'), array('class' => 'list-group-item') );
			?>
		</div>
	</div>
	<div class="col-md-9">
		<?php echo $this->Form->create('User', $form_defaults); ?>
		<h1>Welcome!  <small>Let's create your volunteer account.</small></h1>
		<p>We need to know a little information before you can start managing your volunteer opportunities.</p>
		<hr>

		<h2>Account Information</h2>
		<p>This is what you'll use to login.</p>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php
						echo $this->Form->input('username', array('class' => 'form-control username') );
					?>
				</div>
			</div>
		</div>
		<hr>

		<h2>More About You</h2>
		<p>When you communicate with others, we'll show them your name.</p>
		<div class="row">
			<div class="col-md-4">
				<?php echo $this->Form->input('first_name'); ?>
			</div>
			<div class="col-md-4">
				<?php echo $this->Form->input('last_name'); ?>
			</div>
			<div class="col-md-4">
				<?php echo $this->Form->input('email'); ?>
			</div>
		</div>

		<?php echo $this->Form->end(array('label' => "Create My Account", 'class' => 'btn btn-lg btn-primary')); ?>

	</div>
</div>
