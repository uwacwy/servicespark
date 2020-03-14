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
		<script src="https://www.google.com/recaptcha/api.js?render=6LdVLdwUAAAAAIhBXUb27-ut5DrzJ-MkthI1Xer9"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('6LdVLdwUAAAAAIhBXUb27-ut5DrzJ-MkthI1Xer9', {action: 'register'}).then(function(token) {
       document.getElementById('RegisterToken').value=token;
    });
});
</script>

		<?php echo $this->Form->hidden('Register.token', ['value' => 'noscript']); ?>

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
				<?php echo $this->Form->input('skill', array('class' => 'autocomplete skills form-control', 'data-target' => '#UserSkills', 'placeholder' => __('type a skill and press enter to add it to your skills list...') ) ); ?>
				<div id="UserSkills">
					<span class="autocomplete-wrapper">
						<a class="autocomplete-cancel" href="#">&times;</a>General
						<input type="hidden" name="data[Skill][Skill][]" value="11" />
					</span>
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
