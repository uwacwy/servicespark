<?php
	/*
		publish.ctp
		--
		This form allows users to publish their activity to organizations.
	*/
?>

<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1>Are you volunteering on behalf  of an organization? </h1>
		<h2><small>Join an organization here! </small></h2>
		<hr>

		<h2>Organizations</h2>
		<p>Select all the organizations you want to join.<p>
		<?php 
			echo $this->Form->input('Organization');
		?>
		<hr>

		<?php echo $this->Form->end(array('label' => "Join These Organizations", 'class' => 'btn btn-lg btn-primary')); ?>
	</div>
</div>