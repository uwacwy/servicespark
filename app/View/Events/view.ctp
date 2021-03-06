<div class="row">
	<div class="col-md-3">
		<h3>Viewing Event</h3>
		<?php echo $this->Html->link(__('Back to Events'), array('action' => 'index'), array('class' => 'btn btn-primary btn-md btn-block')); ?>
		<?php echo $this->Html->link( __('Register for %s', Configure::read('Solution.name')), array('controller' => 'users', 'action' => 'register'), array('class' => 'btn btn-default btn-md btn-block') ); ?>
	</div>
	<div class="col-md-9">
		<h1>
			<small><?php echo $event['Organization']['name']; ?></small><br><?php echo h($event['Event']['title']); ?>
			<small><?php echo $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']); ?></small>
		</h1>
		<blockquote><?php echo h($event['Event']['description']); ?></blockquote>
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

	</div>
</div>
