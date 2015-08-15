<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Event'); ?>
		<h2><?php echo __("Create Event"); ?></h2>
		<p class="text-muted"><?php echo $this->Utility->__p(array(
			"Events are a convenient way to draw and coordinate volunteers for community service at a specific time and place.",
			array("%s volunteers can RSVP and comment on events.", Configure::read('Solution.name') ),
			"Events are a powerful way to communicate with volunteers, and make it easy to track time for large groups of volunteers."
		)); ?></p>
		<div class="row">
			<div class="col-md-12">
				<div class="actionable">
					<div class="situation">
						<?php
							echo $this->Form->input('Event.title' );
							echo $this->Form->input('Event.description', array('type' => 'textarea') );
						?>
						<div class="row">
							<div class="col-md-6"><?php
								echo $this->Form->input('Event.start_time',
									array('label' => "Event Start", 'type' => 'datetime' ) ); ?></div>
							<div class="col-md-6"><?php
								echo $this->Form->input('Event.stop_time',
										array('label' => 'Event End', 'type' => 'datetime' ) );?></div>
						</div>
						
						<div class="row">
							<div class="col-md-6"><?php echo $this->Form->input('Event.organization_id' ); ?></div>
							<div class="col-md-6"><?php echo $this->Form->input('Event.rsvp_desired',
								array('label' => __('Desired Volunteers'))); ?>
								</div>
						</div>
						
						
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h2>
					<?php echo __("Event Skills/Tags"); ?>
					<small><?php echo __("Tag your event with skills to help %s find amazing volunteers for your event.", Configure::read('Solution.name') ); ?></small></h2>
					<p><?php echo $this->Utility->__p(array(
						"Begin typing any skills or tags you would like to associate your event with.",
						"If the skill/tag already exists, click it to add it to your event.",
						"If the skill/tag doesn't exist, press return to add it anyway.",
						"Stuck?  Try popular software, types of people skills (children, event coordination), or college degrees."
					)); ?></p>
				<?php echo $this->Form->input('skill', array('class' => 'autocomplete skills form-control', 'data-target' => '#UserSkills') ); ?>
				<div id="UserSkills">
					<?php

						$skill_sprint = '<span class="autocomplete-wrapper"><a class="autocomplete-cancel" href="#">&times</a>%s<input type="hidden" name="data[Skill][Skill][]" value="%u"></span>';

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
				</div>
			</div>
		</div>
		
		<hr>

		<div class="row">
			<div class="col-md-12">
				<h2>
					<?php echo __("Addresses"); ?>
					<small><?php echo __("Add an address to help volunteers find your events."); ?></small></h2>

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

		<?php echo $this->Form->end(array('label' => "Create Event", 'class' => 'btn btn-lg btn-primary')); ?>
		</div>
	</div>
</div>
