<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><?php echo $this->Html->link( Configure::read('Solution.name'), '/'); ?></li>
			<li><?php echo h( __('Coordinator') ); ?></li>
			<li><?php echo h( __('Events I Can Coordinate') ); ?></li>
		</ol>
	</div>
</div>



<div class="events index">
	<div class="pull-right">
		<?php echo $this->Html->link( __('Create New Event'), array('coordinator' => true, 'controller' => 'events', 'action' => 'add'), array('class' => 'btn btn-primary')); ?>
	</div>

	<h2><?php echo h( __('Event Dashboard') ); ?></h2>
	<p class="text-muted"><?php echo h( __('These are events for organizations for which you are a coordinator') ); ?></p>

<?php if( !empty($events) ): ?>
	<div class="table-responsive">
		<table cellpadding="0" cellspacing="0" class="table table-striped">
		<tr>
				<th><?php echo $this->Paginator->sort('title', __('Event Title') ); ?></th>
				<th><?php echo $this->Paginator->sort('Organization.name', __('Organization') ); ?></th> 
				<th><?php echo $this->Paginator->sort('start_time', __('Start Time') ); ?></th>
				<th><?php echo $this->Paginator->sort('stop_time', __('Stop Time') ); ?></th>
				<th><?php echo $this->Paginator->sort('rsvp_percent', __('RSVP Progress') ); ?></th>
				<th class="actions text-right">&nbsp;</th>
		</tr>
		<?php foreach ($events as $event): ?>
			<tr>
				<td><?php echo h($event['Event']['title']); ?>&nbsp;</td>
				<td><?php echo h($event['Organization']['name']); ?>&nbsp;</td>

				<td> <?php $startTime = new DateTime($event['Event']['start_time']);
					echo $startTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

				<td> <?php $stopTime = new DateTime($event['Event']['stop_time']);
					echo $stopTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

				<td>
				
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

				<td class="actions text-right">
					<?php
						echo $this->Html->link(__('View'),
							array('action' => 'view', $event['Event']['event_id']),
							array('class' => 'btn btn-success btn-xs')
						);
						echo ' ';
						echo $this->Html->link(__('Edit'),
							array('action' => 'edit', $event['Event']['event_id']),
							array('class' => 'btn btn-primary btn-xs')
						);
						echo ' ';						
						echo $this->Form->postLink(__('Delete'),
							array('action' => 'delete', $event['Event']['event_id']),
							array('class' => 'btn btn-danger btn-xs'),
							__('Are you sure you want to delete event #%s?  This will delete all Time Entry data and cannot be recovered.', $event['Event']['event_id'])
						);
					?>
				</td>
			</tr>
		<?php endforeach; ?>
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
	<p><em><?php echo __("You have no organizations you can coordinate for."); ?></em></p>
<?php endif; ?>
</div>

