 <div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Event'); ?>
		<h2>Create Event</h2>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php
						echo $this->Form->input('title', array('class' => 'form-control') );
						echo $this->Form->input('description', array('type' => 'textarea', 'class' => 'form-control') );
						echo $this->Form->input('start_time');
						echo $this->Form->input('stop_time');
						echo $this->Form->input('organization_id', array('class' => 'form-control') );
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h2>Skills <small>Skills will help your event be shown to volunteers that can help.</small></h2>
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
					<?php // TODO: this should fill full of skill elements if the page has a validation error ?>
				</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<h2>Addresses <small>Addresses will help volunteers navigate to your events.</small></h2>

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

		<?php echo $this->Form->end(array('label' => "Create Event", 'class' => 'btn btn-lg btn-primary')); ?>
		</div>
	</div>
</div>
