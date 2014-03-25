<div class="events view">
<h2><?php echo __('Event'); ?></h2>
	 
<!-- 	<table class="table table-bordered table-striped">
		<tr>
		  <td><strong><?php echo __('Title'); ?></strong></td>
		  <td><?php echo h($event['Event']['title']); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Organization'); ?></strong></td>
		  <td><?php echo h($event['Organization']['name']); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Description'); ?></strong></td>
		  <td><?php echo h($event['Event']['description']); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Start Time'); ?></strong></td>
		  <td><?php $startTime = new DateTime($event['Event']['start_time']);
			echo $startTime->format('F j, Y, g:i a'); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Stop Time'); ?></strong></td>
		  <td><?php $stopTime = new DateTime($event['Event']['stop_time']);
			echo $stopTime->format('F j, Y, g:i a'); ?></td>
		</tr>
	</table>  -->
<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Event'); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php
						echo $this->Form->input('title', array('class' => 'form-control', 'disabled' => 'disabled') );
						echo $this->Form->input('description', array('type' => 'textarea', 'class' => 'form-control', 'disabled' => 'disabled') );
						echo $this->Form->input('start_time', array('disabled' => 'disabled'));
						echo $this->Form->input('stop_time', array('disabled' => 'disabled'));
						echo $this->Form->input('Organization.name', array('class' => 'form-control', 'disabled' => 'disabled', 'label' => 'Organization') );
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php echo $this->Address->printAddress($this->request->data['Address']); ?>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Return to Events List'), array('action' => 'index')); ?> </li>
	</ul>
</div>