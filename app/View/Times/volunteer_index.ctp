<?php
	// volunteer_index
	// disambiguates checkins
?>
<div class="row">
	<div class="col-md-12">
		<h2>Time Clock <small><?php echo __("You can use %s to log volunteer time", Configure::read("Solution.name")); ?></small></h2>
		<p>Your volunteer coordinator may have given you a time token. If you are unsure whether the token is for clocking in or clocking out, please ask your volunteer coordinator.</p>
		
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="time-token-heading">
						<h4 class="panel-title">
						<a class="block" data-toggle="collapse" data-parent="#accordion" href="#time-token" aria-expanded="true" aria-controls="time-token">
							<?php echo __("I have a time token."); ?>
						</a>
						</h4>
					</div>
					<div id="time-token" class="panel-collapse collapse" role="tabpanel" aria-labelledby="time-token-heading">
						<div class="panel-body">
						<?php echo $this->Form->create('Time', $form_defaults); ?>
							<?php echo $this->Form->input('Time.token', array('label' => 'Time Token') ); ?>
							<?php echo $this->Form->button('Clock In', array('class' => 'btn btn-success', 'name' => 'locus', 'value' => 'in')); ?>
							<?php echo $this->Form->button('Clock Out', array('class' => 'btn btn-primary', 'name' => 'locus', 'value' => 'out')); ?>
						<?php echo $this->Form->end(null); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="organization-heading">
						<h4 class="panel-title">
						<a class="block" data-toggle="collapse" data-parent="#accordion" href="#organization" aria-expanded="false" aria-controls="organizaton">
							<?php echo __("I am logging volunteer time for an organization."); ?>
						</a>
						</h4>
					</div>
					<div id="organization" class="panel-collapse collapse" role="tabpanel" aria-labelledby="organization-heading">
						<div class="panel-body">
						<?php echo $this->Form->create('Organization', $form_defaults); ?>
							<?php echo $this->Form->input('organization_id', array('label'=> __("Select An Organization To Begin"), 'options' => $organizations)); ?>
						<?php echo $this->Form->end(array('label' => __("Next"), 'class' => 'btn btn-primary')); ?>
						</div>
					</div>
			</div>
	</div>

</div>