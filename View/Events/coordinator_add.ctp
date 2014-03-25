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
				<div class="well">
					<?php echo $this->Address->addBlock(); ?>
				</div>
			</div>
		</div>

		<?php echo $this->Form->end(array('label' => "Create Event", 'class' => 'btn btn-lg btn-primary')); ?>
		</div>
	</div>
</div>
