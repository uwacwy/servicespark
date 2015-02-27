<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><?php echo $this->Html->link( Configure::read('Solution.name'), '/'); ?></li>
			<li><?php echo h( __('Coordinator') ); ?></li>
			<li><?php echo h( __('Event Archive') ); ?></li>
		</ol>
	</div>
</div>



<div class="events index">
	<div class="pull-right">
		<?php echo $this->Html->link( __('Create New Event'), array('coordinator' => true, 'controller' => 'events', 'action' => 'add'), array('class' => 'btn btn-primary')); ?>
	</div>

	<h2><?php echo h( __('Event Archive') ); ?></h2>
	<p class="text-muted"><?php echo h( __('These events took place in the past.') ); ?></p>

<?php if( !empty($events) ): ?>
	<div class="table-responsive">

		<table cellpadding="0" cellspacing="0" class="table table-striped forum">
			<thead>
			<tr>
				<th>
					<h3><?php echo __("Ongoing and Upcoming Events"); ?></h3>
					<p>
						<?php
							$sorts = array(
								'Event.title' => array(
									'label' => __("Event Title"),
									'desc' => __('Z to A'),
									'asc' => __('A to Z')
								),
								'Event.rsvp_percent' => array(
									'label' => __("RSVP Completion"),
									'desc' => __("highest to lowest"),
									'asc' => __("lowest to highest")
								),
								'Event.start_time' => array(
									'label' => __("Event Start Time"),
									'desc' => __("latest to earliest"),
									'asc' => __("earliest to latest")
								),
								"Event.stop_time" => array(
									'label' => __("Event Stop Time"),
									'desc' => __("latest to earliest"),
									'asc' => __("earliest to latest")
								),
								"Organization.name" => array(
									'label' => __("Organization"),
									'desc' => __('Z to A'),
									'asc' => __('A to Z')
								),
								"Event.missed_punches" => array(
									'label' => __("Missed Punch Count"),
									'desc' => __("most to least"),
									'asc' => __("least to most")
								),
								'Event.comment_count' => array(
									'label' => __("Comment Count"),
									'asc' => __("least to most"),
									'desc' => __("most to least")
								)
							);
						?>
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<?php
									$sort_word = ($this->Paginator->sortDir() == "asc")? __("ascending") : __("descending");
									if( array_key_exists($this->Paginator->sortKey(), $sorts) )
									{
										echo __('<small>SORTED BY</small> <strong>%s</strong> <small class="%s quiet">%s</small>',
											$sorts[ $this->Paginator->sortKey() ]['label'],
											$this->Paginator->sortDir(),
											strtoupper($sorts[ $this->Paginator->sortKey() ][ $this->Paginator->sortDir() ])
										);
									}
									else
										echo __("Sort");
								?>
								<?php  ?>
								<span class="caret"></span>
							</button>
							<?php

								echo '<ul class="dropdown-menu" role="menu">';
								foreach($sorts as $key => $label)
								{
									echo "<li>";
									echo $this->Paginator->sort($key, $label['label']);
									echo "</li>";
								}
								echo '</ul>';
							?>
						</div>
					</p>
				</th>
				<th class="cell-stat text-center hidden-xs hidden-sm">
					<i class="glyphicon glyphicon-time" title="<?php echo __("Missed Punches"); ?>"></i></th>
				<th class="cell-stat text-center hidden-xs hidden-sm">
					<i class="glyphicon glyphicon-comment" title="<?php echo __("Comments"); ?>"></i></th>
				<th class="cell-stat cell-progress text-center hidden-xs hidden-sm">
					<i class="glyphicon glyphicon-calendar" title="<?php echo __("RSVP"); ?>"></i></th>
				<th class="cell-stat text-center hidden-xs hidden-sm">
					<?php echo __("Details"); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($events as $event): ?>
			<tr>
				<td>
					<h4><?php
						echo $this->Html->link(
							$event['Event']['title'],
							array('action' => 'view', $event['Event']['event_id'])
						); ?><br>
						<small><?php echo h($event['Event']['description']); ?></small>
					</h4>
				</td>
				<td class="text-center hidden-xs hidden-sm">
					<?php echo number_format($event['Event']['missed_punches']); ?>
				</td> 
				<td class="text-center hidden-xs hidden-sm">
					<?php echo number_format($event['Event']['comment_count']); ?>
				</td>
				<td class="text-center hidden-xs hidden-sm">
					<?php
						$current = $event['Event']['rsvp_count'];
						$desired = $event['Event']['rsvp_desired'];
						$pct = $event['Event']['rsvp_percent'];
					?>
					<div class="progress" title="<?php echo number_format($pct, 0); ?>%" >
						<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $current; ?>" aria-valuemin="0" aria-valuemax="<?php echo max($current, $desired); ?>" style="width: <?php echo min($pct, 100); ?>%;">
							<?php echo ($pct > 20) ? number_format( $pct, 0) . '%' : ''; ?>
						</div>
					</div>
				</td>
				<td class="hidden-xs hidden-sm">
					by <?php echo $this->Html->link(
						$event['Organization']['name'],
						array('coordinator' => true, 'controller' => 'organizations', 'action' => 'view', $event['Organization']['organization_id'])
					); ?><br>
					<?php 
						echo $this->Utility->no_wrap($this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time'] )); ?>
					<div>
						<?php
							echo $this->Html->link(__('Edit'),
								array('action' => 'edit', $event['Event']['event_id']),
								array('class' => 'text-primary ')
							);
							echo ' ';						
							echo $this->Form->postLink(__('Delete'),
								array('action' => 'delete', $event['Event']['event_id']),
								array('class' => 'text-danger '),
								__('Are you sure you want to delete event #%s?  This will delete all Time Entry data and cannot be recovered.', $event['Event']['event_id'])
							);
						?>
					</div>
				</td>
			</tr>

		<?php endforeach; ?>
	</tbody>
		</table>
	</div>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<ul class="pagination bottom">
		<?php
			echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
			echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		?>
	</ul>
<?php else: ?>
	<p><em><?php echo __("You are not currently coordinating any events. %s", $this->Html->link(__('Create One.'), array('coordinator' => true, 'controller' => 'events', 'action' => 'add'))); ?></em></p>
<?php endif; ?>
</div>

