<?php
	/*
		Add.ctp
		--
		I have also annotated this form, since it shows the mechanics of GETTING data
	*/
?>

<div class="row">
	<div class="col-md-12">
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
						echo $this->Form->input('password_l', array('type' => 'password', 'label' => "Password") );
						echo $this->Form->input('password_r', array('type' => 'password', 'label' => "Confirm Password") );
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
		<hr>

		<h2>Add Skills</h2>
		<p>If we know what you're good at, we can connect you to volunteer opportunities that interest you and challenge you.</p>
		<div class="row">
			<div class="col-md-12">
				<?php echo $this->Form->input('skill', array('class' => 'autocomplete skills form-control', 'data-target' => '#UserSkills') ); ?>
				<div id="UserSkills">
					<?php 
						if( isset($this->request->data['Skill']) )
						{
							for( $i = 0; $i < count($this->request->data['Skill']); $i++ )
							{
								echo $this->element('skill', array('i' => $i) );
							}
						}
					?>
				</div>
			</div>
		</div>
		<hr>

		<h2>Add Addresses</h2>
		<p>Add addresses to your profile so we can help you find directions to volunteer opportunities.</p>
		<div class="row">
			<div class="col-md-12">
				<p><a href="#" class="add-address btn btn-success" data-target="#UserAddresses"><i class="glyphicon glyphicon-plus"></i> Add An Address</a></p>
				<div id="UserAddresses">
					<?php
						// this form will be reshown to users if there was a validation error;
						// as a result, we need to render html elements that were generated
						// by javascript dom manipulation before the page was submitted
						if( isset($this->request->data['Address']) )
						{
							for($i = 0; $i< count($this->request->data['Address']); $i++)
							{
								echo $this->element('address', array("i" => $i) );
							}
						}
					?>
				</div>
			</div>
		</div>
		<hr>

		<h2>Does Everything Look Good?</h2>
		<p>This is the moment of truth.</p>

		<?php echo $this->Form->end(array('label' => "Create My Account", 'class' => 'btn btn-lg btn-primary')); ?>

	</div>
</div>
