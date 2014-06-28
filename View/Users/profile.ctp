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
		<?php echo $this->Form->input('user_id'); ?>
		<h1>Editing your profile.  <small>Let's get this up-to-date.</small></h1>
		<p>We need to know a little information before you can start managing your volunteer opportunities.</p>
		<hr>

		<h2>Account Information</h2>
		<p>This is what you'll use to login.</p>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php
						echo $this->Form->input('password_l', array('type' => 'password', 'label' => "Change Password") );
						echo $this->Form->input('password_r', array('type' => 'password', 'label' => "Confirm Changed Password") );
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
				<?php echo $this->Form->input('skill', array('class' => 'autocomplete skills form-control', 'data-target' => '#UserSkills', 'placeholder' => __('type a skill and press enter to add it to your profile...') ) ); ?>
				<div id="UserSkills">
					<?php

						$skill_sprint = '<span class="autocomplete-wrapper"><a class="autocomplete-cancel" href="#">&times</a>%s<input type="hidden" name="data[Skill][Skill][]" value="%u"></span>';
						
						// this is done to keep state in the event of validation errors
						// skills are created whether or not they are valid
						if( isset($relevant_skills) )
						{
							foreach($relevant_skills as $skill_id => $skill)
							{
								echo sprintf(
									$skill_sprint,
									$skill,
									$skill_id
								);
							}
						}

						if( isset($this->request->data['Skill']) )
						{
							foreach($this->request->data['Skill'] as $skill)
							{
								echo sprintf(
									$skill_sprint,
									$skill['skill'],
									$skill['skill_id']
								);
							}
							//debug($this->request->data['Skill']);
						}
					?>
					<?php // TODO: this should fill full of skill elements if the page has a validation error ?>
				</div>
			</div>
		</div>
		<hr>

		<h2>Add Addresses</h2>
		<p>Add addresses to your profile so we can help you find directions to volunteer opportunities.</p>
		<div class="row">
			<div class="col-md-12">
				<p><a href="#" class="add-address btn btn-success" data-target="#UserAddresses"><i class="glyphicon glyphicon-plus"></i> Add An Address</a></p>
				<div id="UserAddresses" class="address-container">
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

		<h2>Email Preferences</h2>
		<p>We will send you emails from time to time.  If you are receiving too many emails, you can unsubscribe here.</p>
		<div class="row">
			<div class="col-md-12">
				<ul class="list-style-none">
					<li><?php echo $this->Form->input('User.email_mentions', array('label' => __("Email me when I'm mentioned in a comment."), 'class' => 'checkbox' ) ); ?></li>
					<li><?php echo $this->Form->input('User.email_participation', array('label' => __("Email me when there is a reply to a conversation I'm involved in."), 'class' => 'checkbox') ); ?></li>
					<li><?php echo $this->Form->input('User.email_attending', array('label' => __("Email me when a comment is posted on an event I am attending. (Events I RSVP to)."), 'class' => 'checkbox') ); ?></li>
				</ul>
			</div>
		</div>

		<h2>Does Everything Look Good?</h2>

		<?php echo $this->Form->end(array('label' => "Save Profile", 'class' => 'btn btn-lg btn-primary')); ?>

	</div>
</div>
