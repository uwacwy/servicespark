<?php

/*
	in.ctp
	--
	confirms a clockin
*/

?>

<?php if( isset($event) ): ?>

	<?php echo $this->Form->create('Time', $form_defaults);
	$button_class = "btn btn-lg btn-primary"?>
	<h2>
		<small><?php echo __("You are clocking in to"); ?>&hellip;</small><br>
		<?php echo h($event['Event']['title']); ?>
	</h2>
	
	
	
	<p>
		<?php echo __("This event is organized by <strong>%s</strong>.",
			h($event['Organization']['name']) ); ?>  
		<?php echo __("The event is scheduled for <strong>%s</strong>.",
			h( $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']) ) ); ?>
	</p>
	
	<?php if( !empty($event['Organization']['Permission']) ): ?>
	<p><?php echo __("Please contact one of the following event coordinators with any questions about the event."); ?></p>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo __("Name"); ?></th>
					<th><?php echo __("Email Address"); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($event['Organization']['Permission'] as $coordinator) : ?>
					<tr>
						<td><?php echo h($coordinator['User']['full_name']);?></td>
						<td><?php echo sprintf('<a href="mailto:%1$s">%1$s</a>', $coordinator['User']['email']); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>
	
	<?php if( !empty($event['EventTime']) ): ?>
		<?php $button_class = "btn btn-lg btn-warning"; ?>
		<div class="alert alert-warning">
			<h4 class="text-warning"><?php echo __("<strong>Warning!</strong> You have already clocked into %s.", h($event['Event']['title']) ); ?></h4>
			<p><?php echo __("You can still clock in again.  This commonly happens if you clock out and then return to an event."); ?></p>
			<p><?php echo __("You may need to work with an event coordinator to resolve missed punches"); ?></p>
		</div>
	<?php endif; ?>
	
	<?php echo $this->Form->input('Time.confirm', array('type' => 'hidden', 'value' => '1') ); ?>
	
	<?php echo $this->Form->end( array('label' => __("Confirm Clock In"), 'class' => $button_class) ); ?>
	
<?php elseif( isset($organization) ): ?>

	<?php
	$form_defaults['class'] = 'form form-inline';
	echo $this->Form->create('Time', $form_defaults); ?>
	<h2>Log Time</h2>
	<p>
		<?php echo __("You are logging time for <strong>%s</strong>.", $organization['Organization']['name']); ?> 
		<?php
			echo __("If this is incorrect, %s.",
					$this->Html->link(
						__('go back to the time clock'),
						array('volunteer' => true, 'action' => 'index')
				)
			);
	?></p>
	<p><?php echo __("Your time must be approved before it appears on your profile."); ?></p>
	<p><?php echo $this->Form->input('Time.start_time', array(
		'separator' => " ", 
		'error' => array(
			'attributes' => array('escape' => false)
		)
	)); ?></p>
	<p><?php echo $this->Form->input('Time.stop_time', array(
		'separator' => " ", 
		'error' => array(
			'attributes' => array('escape' => false)
		)
	)); ?></p>
	<p><?php echo $this->Form->input('OrganizationTime.0.memo'); ?></p>
	<p class="help-text">
		<?php echo __("Type a brief description of this duration of activity"); ?>
	</p>
	
	<?php echo $this->Form->end( array('label' => __("Log Time"), 'class' => 'btn btn-lg btn-primary') ); ?>

<?php endif; ?>