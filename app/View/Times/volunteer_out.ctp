<h2>
	<small><?php echo __("Clocking out of event"); ?>&hellip;</small><br>
	<?php echo h($event['Event']['title']); ?>
</h2>
<?php if( count($event['EventTime']) == 1 ): ?>
	<p>
		<?php echo __("You are clocking out of this <strong>%s</strong> which is scheduled for %s.",
			$event['Event']['title'],
			$this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time'])
		); ?>
	</p>
	<?php
			echo $this->Form->postButton(
				__("Clock Out"),
				array(
					'volunteer' => true, 
					'controller' => 'times', 
					'action' => 'out', 
					$event['Event']['stop_token'], 
					$event['EventTime'][0]['Time']['time_id']
				),
				array('class' => 'btn btn-primary')
			); ?>
<?php elseif( count($event['EventTime']) > 1): ?>
	
	<p>
		<?php echo __("Many open time punches were found.  Please select which time punch you are clocking out of."); ?>  
		<?php echo __("It is likely that you will need to contact an event coordinator to adjust your time punches."); ?>
	</p>
	<ul>
	<?php foreach( $event['EventTime'] as $punch ): ?>
		<?php if( empty($punch['Time']) ) continue; ?>
		<li>
			<?php
			echo $this->Form->postButton(
				$this->Time->format($punch['Time']['start_time'], "%A, %B %e, %Y %l:%M %p"),
				array('volunteer' => true, 'controller' => 'times', 'action' => 'out', $event['Event']['stop_token'], $punch['Time']['time_id']),
				array('class' => 'btn btn-link')
			); ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
