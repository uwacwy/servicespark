
<div class="events index">
	<div class="pull-right">
		<?php echo $this->Html->link(
			__('Create New Event'), 
			array(
				'coordinator' => true, 
				'controller' => 'events', 
				'action' => 'add'
			),
			array('class' => 'btn btn-primary')
		); ?>
		<?php echo $this->Utility->btn_link_icon(
			__("Download Microsoft Excel"),
			array('coordinator' => true, 'controller' => 'events', $period, 'xlsx'),
			'btn btn-success',
			'glyphicon-download-alt'
		); ?>
	</div>

	<h2><?php echo h( __('Event Dashboard') ); ?></h2>
	<p class="text-muted"><?php echo h( __('These are events for organizations for which you are a coordinator') ); ?></p>

	<div class="btn-toolbar" role="toolbar">
		<div class="btn-group">
			<?php echo $this->Html->link(
				__("View Oncoming and Upcoming Events"),
				array('coordinator' => true, 'controller' => 'events', 'action' => 'dashboard',  'current'),
				array('class' => ($period == 'current') ? 'btn btn-default active' : 'btn btn-default')
			); ?>
			<?php echo $this->Html->link(
				__("View Archived Events"),
				array('coordinator' => true, 'controller' => 'events', 'action' => 'dashboard',  'archive'),
				array('class' => ($period == 'archive') ? 'btn btn-default active' : 'btn btn-default')
			); ?>
		</div>
	</div>
			
		

<?php if( !empty($events) ): ?>

		<div class="_table ">
			<div class="_thead">
			<div class="_tr">
				<div class="_th">
					<?php echo $this->Paginator->sort("Event.title", "Event Title"); ?>
				</div>
				<div class="_th">
					<?php echo $this->Paginator->sort("Event.start_time", "Start Time"); ?> -
					<?php echo $this->Paginator->sort("Event.stop_time", "Stop Time"); ?>
					</div>
				<div class="_th">
					<?php echo $this->Paginator->sort('Event.comment_count', "Comments"); ?>
				</div>
				<div class="_th">
					<i class="glyphicon glyphicon-calendar" title="<?php echo __("RSVP"); ?>"></i> 
					<?php echo $this->Paginator->sort('rsvp_percent', "RSVP Goal Completion"); ?></div>
			</div>
		</div>
		<div class="_tbody">
		<?php foreach ($events as $event): ?>
		<?php
						$current = $event['Event']['rsvp_count'];
						$desired = $event['Event']['rsvp_desired'];
						$pct = $event['Event']['rsvp_percent'];
						if( $pct >= 100)
							$cls = "success";
						if( $pct < 100 )
							$cls = "warning";
							
					?>
			<div class="_tr _<?php echo $cls; ?>">
				<div class="_td">
					<span><?php
						echo $this->Html->link(
							$event['Event']['title'],
							array('action' => 'view', $event['Event']['event_id'])
						); ?>
						
					</span><br>
					<span><?php echo h($event['Event']['description']); ?></span>
				</div>
				<div class="_td">
					<?php echo $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']); ?>
					
				</div>
				<div class="_td">
					<?php echo number_format($event['Event']['comment_count']); ?>
				</div>
				<div class="_td">
					
					<div style="min-width: 125px">
					<div class="progress" title="<?php echo sprintf(
						"%u/%u", 
						$event['Event']['rsvp_count'], 
						$event['Event']['rsvp_desired']
					); ?>" >
						<div 
							class="progress-bar" 
							role="progressbar" 
							aria-valuenow="<?php echo $current; ?>" 
							aria-valuemin="0" 
							aria-valuemax="<?php echo max($current, $desired); ?>" 
							style="width: <?php echo min($pct, 100); ?>%;">
							<?php echo ($pct > 20) 
								? number_format( $pct, 0) . '%' 
								: ''; ?>
						</div>
					</div>
					</div>
				</div>

			</div>

		<?php endforeach; ?>
	</div>
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
	<p><em><?php echo __("There are no events for this view. %s", ($period == 'current') ? $this->Html->link(__('Create One.'), array('coordinator' => true, 'controller' => 'events', 'action' => 'add')) : ""); ?></em></p>
<?php endif; ?>
</div>

