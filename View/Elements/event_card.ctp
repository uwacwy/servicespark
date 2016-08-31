<div class="actionable">
	<div class="situation">
	
		<h4><?php echo $this->Html->link(
			$event['Event']['title'], 
			array('volunteer' => false, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id'])
		); ?></h4>
		<div class="markdown"><?php echo $this->Utility->blurb( $event['Event']['description'], 280 ); ?></div>
		
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
			<?php if( AuthComponent::user('user_id') != null ): ?>
			<li class="<?php echo (!empty($event['Rsvp']) && $event['Rsvp'][0]['status'] == "going") ? 'off' : 'on'; ?>">
			<?php
				echo $this->Html->link('RSVP',
					array(
						'volunteer' => true,
						'controller' => 'events',
						'action' => 'rsvp',
						$event['Event']['event_id'],
						'going'
					),
					array(
						'class' => 'api-trigger text-primary when-on',
						'data-api' => Router::url( array(
							'api' => true, 
							'controller' => 'Events', 
							'action' => 'rsvp', 
							$event['Event']['event_id'],
							'going'
						)),
						'data-on-success' => 'toggle_parent_class',
						'data-toggle-class' => 'on off'
					)
				)
			?>
			<?php
				echo $this->Html->link('Cancel RSVP',
					array(
						'volunteer' => true,
						'controller' => 'events',
						'action' => 'rsvp',
						$event['Event']['event_id'],
						'not_going'
					),
					array(
						'class' => 'api-trigger text-danger when-off',
						'data-api' => Router::url( array(
							'api' => true, 
							'controller' => 'Events', 
							'action' => 'rsvp', 
							$event['Event']['event_id'],
							'not_going'
						)),
						'data-on-success' => 'toggle_parent_class',
						'data-toggle-class' => 'on off'
					)
				)
			?>
			</li>
			<?php endif; ?>
			
			<li>
				<?php echo $this->Html->link(
					__("View %s", $event['Event']['title']),
					array('controller' => 'events', 'action' => 'view', $event['Event']['event_id'])
				); ?>
				
			</li>
		</ul>
	</div>
</div>