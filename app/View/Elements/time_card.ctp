<div class="actionable">
	<div class="situation">
	
		<?php
			if( !empty($time['OrganizationTime']) ):
				$organization = $time['OrganizationTime'][0]['Organization'];
		?>
		<div class="organization-time">
			<h4><?php echo __("Time for <strong>%s</strong>", $organization['name']); ?></h4>
		</div>
		<?php endif; ?>
		
		<?php
			if( !empty($time['EventTime']) ):
				$event = $time['EventTime'][0]['Event']
		?>
		<div class="event-time">
			<h4><?php echo __("Time at <strong>%s</strong>", $event['title']); ?></h4>
			<p><?php echo h($event['description']); ?></p>
		</div>
		<?php endif; ?>
		

		<div class="stat-bar">
			
			<?php if( $time['Time']['duration'] != null ): ?>
				<div class="stat">
					<span class="key"><?php echo __("Duration"); ?></span>
					<span class="value"><?php echo $this->Duration->format($time['Time']['start_time'], $time['Time']['stop_time']); ?></span>
				</div>
				<div class="stat">
					<span class="key"><?php echo __("Total"); ?></span>
					<span class="value"><?php echo __("%s hours", number_format($time['Time']['duration'], 2)); ?></span>
				</div>
			<?php endif; ?>
			
			<div class="stat">
				<span class="key"><?php echo __("Deleted"); ?></span>
				<span class="value"><?php echo $this->Time->timeAgoInWords($time['Time']['modified']); ?></span>
			</div>
		</div>
	</div>
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(
				__("Recover"),
				array('controller' => 'times', 'action' => 'undelete', $time['Time']['time_id'])
				); ?>
		</ul>
	</div>
</div>