	<?php $solution_name = Configure::read('Solution.name'); ?>

<div class="row">
	<div class="stripe-sm" style="background-color: #efefef">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2><?php echo __("Welcome to %s", Configure::read('Solution.name') ); ?></h2>
					<p class="lead"><?php echo __("Let's get you up to speed.", Configure::read('Solution.name')); ?></p>
					<?php if( AuthComponent::user('account_age') < (3 * 24 * 60) ) : ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="stripe-sm">
	<div class="container">
	<div class="row">
	<div class="col-md-12">
		<h3><?php echo __("Add Skills"); ?></h3>
		<p><?php echo __("Skills allow you to tag your profile.  %s will find service opportunities for you based on your profile", $solution_name); ?></p>
		<div class="stat-bar">
			<div class="stat">
				<span class="key"><?php echo __("Skills on %s", $solution_name); ?></span>
				<span class="value"><?php echo $skill_count; ?></span>
			</div>
			<div class="stat">
				<span class="key"><?php echo __("Skills on your profile"); ?></span>
				<span class="value"><?php echo count($user['SkillUser']); ?></span>
			</div>
		</div>
		<p class="lead"><?php echo $this->Html->link(
			__("Add Skills to My Profile"),
			array(
				'controller' => 'users',
				'action' => 'skills'
			)
		); ?>
	</div>
	</div>
	</div>
	</div>
	
	<div class="stripe-sm">
	<div class="container">
	<div class="row">
	<div class="col-md-12">
		<h3><?php echo __("Follow Organizations"); ?></h3>
		<p><?php echo $this->Utility->__p(array(
			array("%s lets you connect with organizations you care about.", $solution_name),
			array("By default, %s will notify you when they post a new community service event.", $solution_name),
			"Follow an organization to stay in the loop."
		)); ?></p>
		
		<div class="stat-bar">
			<div class="stat">
				<span class="key"><?php echo __("Organizations on %s", $solution_name); ?></span>
				<span class="value"><?php echo $organization_count; ?></span>
			</div>
			<div class="stat">
				<span class="key"><?php echo __("You currently follow..."); ?></span>
				<span class="value"><?php echo count($user['Permission']); ?></span>
			</div>
		</div>
		
	</div>
	</div>
	</div>
	</div>
	
	<div class="stripe-sm">
	<div class="container">
	<div class="row">
	<div class="col-md-12">
		<h3><?php echo __("Publish Your Activity"); ?></h3>
		<p><?php echo $this->Utility->__p(array(
			"Publish your activity to an organization that's interested in your activity.",
			"Supervisors and coordinators will be able to see how much community service you've done.",
			"They will not be able to see where you were, or who you were volunteering for."
			)); ?></p>
	</div>
	</div>
	</div>
	</div>
	
	<div class="stripe-sm">
	<div class="container">
	<div class="row">
	<div class="col-md-12">
		<h3><?php echo __("Find Service Opportunities"); ?></h3>
		<p><?php echo $this->Utility->__p(array(
			array("Browse all upcoming events, or see events that %s has picked for you.", $solution_name),
			array("If you can make it to an event, send an RSVP!")
		)); ?></p>
	</div>
	</div>
	</div>
	</div>
</div>

<div class="row">
	<?php debug($user); ?>
</div>