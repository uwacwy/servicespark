<div class="actionable">
	<div class="situation">
	
		<h4><?php echo $this->Html->link(
			$event['Event']['title'], 
			array('go' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id'])
		); ?></h4>
		<p><?php echo h($event['Event']['description']); ?></p>
		
		<div class="stat-bar">
		
			<div class="stat">
				<span class="key"><?php echo __('Event Time'); ?></span>
				<span class="value"><?php echo $this->Duration->format(
					$event['Event']['start_time'],
					$event['Event']['stop_time']
				); ?></span>
			</div>
			
			<div class="stat">
				<span class="key"><?php echo __('Organization'); ?></span>
				<span class="value"><?php echo $this->Html->link(
					$event['Organization']['name'],
					array('controller' => 'organizations',
						'action' => 'view',
						$event['Organization']['organization_id']
					)); ?></span>
			</div>
			
			<?php if( isset($event['Skill']) && !empty($event['Skill']) ): ?>
			<div class="stat">
				<span class="key"><?php echo __("Skills"); ?> <i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="<?php echo __("Add skills to your profile to get better event recommendations"); ?>"></i></span>
				<span class="value"><?php
					echo implode(', ', Hash::extract($event['Skill'], '{n}.skill'));
				?></span>
			</div>
			
			<?php endif; ?>
		</div>
	</div>
	<div class="actions">
		<ul>
			<?php
				$status_class = "rsvp--unknown";
				$user_rsvp_status = array(
					'Rsvp' => null
				);
				if( !empty($event['Rsvp']) ) {
					$user_rsvp_status['Rsvp'] = $event['Rsvp'][0];
				}
			?>
			<?php if( AuthComponent::user('user_id') != null ): ?>
			<li >
				<?php echo $this->Element('rsvp', compact('rsvp_count', 'user_rsvp_status', 'event') ); ?>
			</li>
			<?php endif; ?>
			
			<li>
				<strong>View Event:</strong>
				<?php echo $this->Html->link(
					$event['Event']['title'],
					array('go' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id'])
				); ?>
				
			</li>
		</ul>
	</div>
</div>