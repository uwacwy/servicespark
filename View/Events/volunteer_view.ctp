<div class="events view">

	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><strong><?php echo __('Volunteer'); ?></strong></li>
				<li><?php echo $this->Html->link( Configure::read('Solution.name'), '/'); ?></li>
				<li><?php echo $this->Html->link( $event['Organization']['name'], array('volunteer' => true, 'controller' => 'organizations', 'action' => 'view', $event['Organization']['organization_id']) ); ?></li>
				<li><?php echo h( __( $event['Event']['title']) ); ?></li>
			</ol>
		</div>
	</div>



	<?php 
		$startTime = new DateTime($event['Event']['start_time']);
		$stopTime = new DateTime($event['Event']['stop_time']);
	?>


	<div class="row">
		<div class="col-md-3">
			<h3>Viewing Event</h3>
			<div class="list-group">
				<?php echo $this->Html->link(__('List Events'), array('go' => true, 'action' => 'index'), array('class' => 'list-group-item') ); ?>
			</div>
		</div>
		<div class="col-md-9">

			<h1><small><?php echo $event['Organization']['name']; ?></small><br>
				<?php echo h($event['Event']['title']); ?>
				<small><?php echo $this->Duration->format($startTime->format(DateTime::W3C), $stopTime->format(DateTime::W3C) ); ?></small></h1>
			<blockquote><?php echo h($event['Event']['description']); ?></blockquote>

			<?php
				$rsvp_count = count($event['Rsvp']);
				echo $this->Element('rsvp', compact('rsvp_count', 'user_attending') );
			?>

			<div class="progress">
				<?php
					$current = $event['Event']['rsvp_count'];
					$desired = $event['Event']['rsvp_desired'];
					$pct = $event['Event']['rsvp_percent'];
				?>
				<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $current; ?>" aria-valuemin="0" aria-valuemax="<?php echo max($current, $desired); ?>" style="width: <?php echo min($pct, 100); ?>%;">
					<?php echo number_format( $pct, 0); ?>%
				</div>
			
			</div>
			
			<p>Currently <strong><?php echo number_format($current, 0); ?></strong> of
                                <?php echo number_format($desired, 0); ?> volunteer goal.</p>
			
			<hr>
			
			<?php echo $this->Element('print_addresses', array('addresses' => $event['Address']) ); ?>
			
			<hr>

			<h3>Skills</h3>
			<?php if (!empty($event['Skill']) ) : ?>
				
				<p class="lead">
					<?php foreach ($event['Skill'] as $skill) : ?>
						<?php
							echo $this->Html->tag('span', $skill['skill'], array('class' => 'label label-info', 'title' => __('If you enjoy %s, consider volunteering for this event.', $skill['skill']) ) );
							echo ' ';
						?>
					<?php endforeach; ?>
				</p>
			<?php else: ?>
				<p><em>This event doesn't have any skills. </em></p>
			<?php endif; ?>

			<hr>

			<h3>Comments</h3>
			<?php if ( !empty($comments) ) : ?>
				<?php echo $this->Comment->formatComments($comments, $event['Event']['event_id']); ?>
			<?php else : ?>
				<p><em>There are no event comments at this time.</em></p>
			<?php endif; ?>
			<h4>Leave a Comment</h4>
			<?php echo $this->Comment->commentForm($event['Event']['event_id'], null, 'leave a comment...'); ?>


		</div>
	</div>