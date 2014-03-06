<?php

/*
	in.ctp
	--
	confirms a clockin
*/

?>
<div class="time form">
	<?php debug($event); ?>
	<?php echo $this->Form->Create('Time'); ?>

		<div class="text-center">
			<h1><small>Clocking in to</small><br><?php echo h($event['Event']['title']); ?></h1>
			<?php
				$event_start = strtotime( $event['Event']['start_time'] );
				$event_stop = strtotime( $event['Event']['stop_time'] );
				$now = time();

				printf('<p>There are currently %u others volunteering at this event.', count($event['Time']) );

				if( $now < $event_start || $now > $event_stop )
				{
					echo '<p>You are attempting to clock in to an event that is not currently scheduled as in progress.  Are you sure?</p>';
				}

				echo $this->Form->input('confirm', array('type' => 'checkbox', 'label' => 'This is correct.  Clock me in.') );
			?>
		</div>

	<?php echo $this->Form->End('Submit'); ?>
</div>