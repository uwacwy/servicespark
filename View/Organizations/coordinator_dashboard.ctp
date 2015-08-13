<div class="row">
	<div class="col-md-3">
	<?php echo $this->Element('date_filter', array('organization' => $organization['Organization'], 'permission' => 'coordinator')); ?>
	</div>
	<div class="col-md-9">
		<h1>
			<small><?php echo __("Coordinator Dashboard"); ?></small><br>
			<?php echo h($organization['Organization']['name']); ?></h1>
		<?php
			$sentences = array(
				__("You are viewing summary information for events coordinated by <strong>%s</strong>.", $organization['Organization']['name']),
				__("For data about volunteers publishing to %s, please visit the %s.",
					$organization['Organization']['name'],
					$this->Html->link(
						__('Supervisor Dashboard'),
						array(
							'supervisor' => true,
							'controller' => 'organizations',
							'action' => 'view',
							$organization['Organization']['organization_id']
						)
					)
				)
			);
		?>
		<p class="lead"><?php echo implode($sentences, ' '); ?></p>
		
		<p><?php echo __("Viewing data between %s and %s.",
			date("F j, Y", strtotime($event_conditions['Event.start_time >='])),
			date("F j, Y", strtotime($event_conditions['Event.start_time <']))
		); ?></p>
		
		<?php if( !empty($results) ): ?>
			<table class="table">
				<thead>
					<tr>
						<th><?php echo __("Event Title"); ?></th>
						<th class="text-right"><?php echo __("Total Engagements"); ?></th>
						<th class="text-right"><?php echo __("Total Volunteers"); ?></th>
						<th class="text-right"><?php echo __("Total Hours"); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($results as $result) : ?>
					<tr>
						<td><?php echo $this->Html->link(
							$result['Event']['title'],
							array(
								'coordinator' => true,
								'controller' => 'events',
								'action' => 'view',
								$result['Event']['event_id']
							)
						); ?><br>
						<?php echo $this->Duration->format($result['Event']['start_time'], $result['Event']['stop_time']); ?></td>
						
						<td class="text-right"><?php echo number_format($result[0]['Engagements'], 0); ?></td>
						<td class="text-right"><?php echo number_format($result[0]['Volunteers'], 0); ?></td>
						<td class="text-right"><?php echo number_format($result[0]['Duration'], 2); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			
			</table>
		<?php else: ?>
			<p><?php echo __("There is no data for the conditions you specified"); ?></p>
		<?php endif; ?>
	</div>
</div>