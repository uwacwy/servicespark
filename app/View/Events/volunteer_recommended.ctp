<div class="row">
	<div class="col-md-3">
		<?php echo $this->Element('improve_profile'); ?>
	</div>
	<div class="col-md-9">
		<h1><?php echo __("Recommended Events"); ?></h1>
		<p><?php echo __("%s has selected these events for you based on your %s.",
			Configure::read('Solution.name'),
			$this->Html->link(
				__("Volunteer Profile"),
				array(
					'controller' => 'users',
					'action' => 'profile'
				)
			)
		); ?>
		
		
		
		<?php foreach($user_events as $skill): ?>
			<?php if( !empty($skill['Event']) ): ?>
			
				<h3><?php echo h($skill['Skill']['skill']); ?></h3>
			
				<?php foreach($skill['Event'] as $event): ?>
					<?php echo $this->Element('event_card', array('event' => array(
						'Event' => $event,
						'Organization' => $event['Organization'],
						'Rsvp' => $event['Rsvp']
					))); ?>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
			
		<?php endforeach; ?>
	</div>
</div>