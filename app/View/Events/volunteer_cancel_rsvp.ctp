<?php
	$cancel_penalty = Configure::read('Solution.reputation.cancel_rsvp');
	$cancel_penalty_abs = abs($cancel_penalty);
	$what_if_reputation = $user['User']['reputation'] - $cancel_penalty_abs;
?>

	<h1>
		<small><?php echo h($event['Event']['title']); ?></small><br>
		<?php echo __('Cancel RSVP'); ?></h1>
	<p class="large"><?php echo __("You are cancelling an RSVP to an event that is starting soon."); ?></p>
	
	<h3><?php
		echo sprintf(
			__("This will cost you %s reputation points."),
			number_format($cancel_penalty_abs)
		);
	?></h3>
	<p><?php
		echo sprintf(
			__("You currently have <strong>%s</strong> Reputation Points.  If you cancel, you will have <strong>%s</strong> Reputation Points."),
			number_format($user['User']['reputation']),
			number_format($what_if_reputation)
		);
	?></p>
	
	<h3><?php echo __("The event's coordinators will be notified."); ?></h3>
	<p><?php echo __("Event coordinators are counting on you for a successful event."); ?></p>
	
	<hr>
	<div class="row">
		<div class="col-md-6">
			<p>
				<?php echo __("I've changed my mind..."); ?><br>
				<?php echo $this->Form->postLink(
					__("Back to %s", $event['Event']['title']),
					array('go' => 'true', 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']),
					array('class' => 'btn btn-primary btn-large')
				); ?>
			</p>
		</div>
		<div class="col-md-6">
			<p>
				<?php echo __("I understand the above"); ?><br>
				<?php echo $this->Form->postLink(
					__("Cancel my RSVP to %s", $event['Event']['title']),
					array('volunteer' => 'true', 'controller' => 'events', 'action' => 'cancel_rsvp', $event['Event']['event_id']),
					array('class' => 'btn btn-danger')
				); ?>
			</p>
		</div>
	</div>