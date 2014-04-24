<?php

/*
	in.ctp
	--
	confirms a clockin
*/

?>
<div class="row">
	
	<div class="col-md-3">
		<?php echo $this->element('volunteer_time_actions'); ?>
	</div>
	<div class="col-md-9">
		<?php echo $this->Form->Create('Time'); ?>

			<div>
				<h1>
					<small>Clocking out</small><br>
					<?php echo h($event['Event']['title']); ?> <small><?php echo h($event['Organization']['name']); ?>
				</h1>
				<?php
					$event_start = strtotime( $event['Event']['start_time'] );
					$event_stop = strtotime( $event['Event']['stop_time'] );
					$now = time();

					printf('<p>There are currently %u others volunteering at this event.', count($event['Time']) );

					if( $now < $event_start || $now > $event_stop )
					{
						echo '<p>You are attempting to clock out of an event that is not currently scheduled as in progress.  Are you sure?</p>';
					}

					echo $this->Form->input('confirm', array('type' => 'hidden', 'value' => true) );
				?>
			</div>

		<?php echo $this->Form->End( array('label' => __('Clock Out'), 'class' => 'btn btn-success btn-lg' ) ); ?>
</div>
</div>