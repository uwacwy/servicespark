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
		<div>
		<?php echo $this->Form->Create('Time', $form_defaults); ?>

			<h1>
				<small>You are clocking in to...</small><br>
				<?php echo h($event['Event']['title']); ?>
				<small><?php echo h($event['Organization']['name']); ?></small>
			</h1>

			<p><?php echo h($event['Event']['description']); ?>&nbsp;</p>

			<table class="table table-bordered table-condensed">
				<tr>
					<td>
						<strong>start time</strong><br>
						<?php echo sprintf('%s<br>', date('l M j, Y', strtotime($event['Event']['start_time'])) ); ?>
						<?php echo sprintf('%s', date('g:i a', strtotime($event['Event']['start_time'])) ); ?>
					</td>
					<td>
						<strong>stop time</strong><br>
						<?php echo sprintf('%s<br>', date('l M j, Y', strtotime($event['Event']['stop_time'])) ); ?>
						<?php echo sprintf('%s', date('g:i a', strtotime($event['Event']['stop_time'])) ); ?>
					</td>
					<?php
						if( count($event['Address']) > 0)
						{
							foreach($event['Address'] as $address)
							{
								$rtn = "";
								$rtn .= sprintf('<strong>%s</strong><br>', $address['type']);
								$rtn .= sprintf('%s<br>', $address['address1'] );
								if( !empty($address['address2']) )
								{
									$rtn .= sprintf('%s<br>', $address['address2'] );
								}
								$rtn .= sprintf('%s, %s %s', $address['city'], $address['state'], $address['zip'] );

								echo sprintf('<td>%s</td>', $rtn);
							}
						}
					?>
					<td>
						<strong>checked in</strong><br>
						<?php echo count($event['Time']); ?>
					</td>
				</tr>
			</table>
			
			<?php
				$event_start = strtotime( $event['Event']['start_time'] );
				$event_stop = strtotime( $event['Event']['stop_time'] );
				$now = time();

				if( $now < $event_start || $now > $event_stop )
				{
					echo '<p class="text-warning">You are attempting to clock in to an event that is not currently scheduled as in progress.  Are you sure?</p>';
				}

				echo $this->Form->input('Time.confirm', array('value' => true, 'type' => 'hidden') );

			?>

		<?php echo $this->Form->End( array('label' => 'Clock Me In', 'class' => 'btn btn-success btn-lg') ); ?>

		<h3>Problems?</h3>
			<p>You can talk to an event coordinator to resolve most issues.</p>

			<?php
				if( !empty($event['Organization']['Permission']) )
				{
					echo '<table class="table table-striped">';
					echo '<tr><th>Name</th><th>Phone</th></tr>';
					foreach ($event['Organization']['Permission'] as $coordinator)
					{
						echo sprintf('<tr><td>%s</td><td>%s</td></tr>', $coordinator['User']['full_name'], 'phone?');
					}
					echo '</table>';
				}
			?>
		</div>
	</div>
</div>