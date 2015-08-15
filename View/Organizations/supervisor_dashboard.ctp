

<div class="row">
	<div class="col-md-3">
		<?php echo $this->Element('date_filter', array('organization' => $organization, 'permission' => 'supervisor')); ?>

	</div>
	<div class="col-md-9">
	


<h1>
	<small><?php echo __("Supervisor Dashboard"); ?></small><br>
	<?php echo h($organization['name']); ?></h1>
<?php
	$sentences = array(
		__("You are viewing summary information for users that publish their information to <strong>%s</strong>.", $organization['name']),
		__("For data about events coordinated by %s, please visit the %s.",
			$organization['name'],
			$this->Html->link(
				__('Coordinator Dashboard'),
				array(
					'coordinator' => true,
					'controller' => 'organizations',
					'action' => 'view',
					$organization['organization_id']
				)
			)
		)
	);
?>
<p class="lead"><?php echo implode($sentences, ' '); ?></p>

<p><?php echo __("Viewing data between %s and %s.",
	date("F j, Y", strtotime($time_conditions['Time.start_time >='])),
	date("F j, Y", strtotime($time_conditions['Time.start_time <']))
); ?></p>

<?php if( !empty($users) ): ?>

	<table class="table">
		<thead>
			<tr>
				<th><?php echo h(__("Name") ); ?></th>
				<th class="text-right"><?php echo h(__("Activity Count") ); ?></th>
				<th class="text-right"><?php echo h(__("Total Duration") ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($users as $user) : ?>
			<tr>
				<td><?php echo h( $user['User']['full_name'] ); ?></td>
				<td class="text-right"><?php echo number_format($row_counts[ $user['User']['user_id'] ], 0); ?></td>
				<td class="text-right"><?php echo number_format( $row_totals[ $user['User']['user_id'] ], 2 ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th><?php echo h(__("%s Totals", $organization['name']) ); ?></th>
				<th class="text-right"><?php echo number_format($count_total, 0); ?></th>
				<th class="text-right"><?php echo number_format($duration_total, 2); ?></th>
			</tr>
		</tfoot>
	</table>

<?php else: ?>
	<p><em><?php echo __("There is no activity."); ?></em></p>
<?php endif; ?>

	</div>
</div>