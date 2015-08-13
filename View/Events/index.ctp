<div class="row">
	<div class="col-md-3">
		<h3>Welcome to <?php echo h( Configure::read('Solution.name') ); ?></h3>
		<?php if( AuthComponent::user('user_id') ): ?>
			<div class="list-group">
				<?php echo $this->Html->link(__("Recommended Events"), array('volunteer' => true, 'controller' => 'events', 'action' => 'recommended'), array('class' => 'list-group-item')); ?>
			</div>
		<?php else: ?>
			
			<div class="list-group">
				<?php echo $this->Html->link(__("Login to %s", Configure::read('Solution.name') ), array('volunteer' => true, 'controller' => 'events', 'action' => 'matches'), array('class' => 'list-group-item')); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="col-md-9">
		<h2><?php echo __('Upcoming Events'); ?></h2>
		<p>You're viewing ongoing and upcoming service opportunities.  Learn more about an opportunity by clicking its title.</p>

	<?php if( !empty($events) ) : ?>
		<?php foreach($events as $event) : ?>
			<?php echo $this->Element('event_card', array('event' => $event)); ?>
		<?php endforeach; ?>
	<?php else: ?>
		<p><em><?php echo __("There are no ongoing or upcoming events at the moment."); ?></em></p>
	<?php endif; ?>
		<ul class="pagination bottom">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
	</div>
</div>
