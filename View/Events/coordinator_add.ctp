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
							
						?>
						
						<ul class="nav nav-tabs" role="tablist">
						    <li role="presentation" class="active"><a href="#event-add-description-compose" aria-controls="home" role="tab" data-toggle="tab"><?php echo __("Edit"); ?></a></li>
						    <li role="presentation"><a href="#event-add-description-preview" aria-controls="profile" role="tab" data-toggle="tab"><?php echo __("Preview"); ?></a></li>
  						</ul>
  						
  						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="event-add-description-compose">
								<?php echo $this->Form->input('Event.description', array('type' => 'textarea') ); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="event-add-description-preview"><div class="markdown"></div></div>
						</div>
						
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
				
				<?php echo $this->Element('skill_picker'); ?>
				
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
