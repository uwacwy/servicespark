<div style="text-align: right">
	<?php echo $this->Html->link(__('Back to Events'), array('action' => 'index'), array('class' => 'btn btn-primary')); ?>
</div>

<h2><?php echo __('Event'); ?></h2>

<?php 
	$startTime = new DateTime($event['Event']['start_time']);
	$stopTime = new DateTime($event['Event']['stop_time']);
?>

<div class="row">
	<div class="col-md-12">
		<h1><small><?php echo $event['Organization']['name']; ?></small><br><?php echo h($event['Event']['title']); ?> <small><?php echo $startTime->format('F j, Y, g:i a'); ?> - <?php echo $stopTime->format('g:i a'); ?></small></h1>
		<blockquote><?php echo h($event['Event']['description']); ?></blockquote>

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
			<p><em>This event doesn't have any skills.</em></p>
		<?php endif; ?>


		<?php echo $this->Element('print_addresses', array('addresses' => $event['Address']) ); ?>
	</div>
</div>



