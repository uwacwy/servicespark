<div class="row">
	<div class="col-md-3">
		<h3>Welcome to <?php echo h( Configure::read('Solution.name') ); ?></h3>
		<?php if( AuthComponent::user('user_id') ): ?>
			<div class="list-group">
				<?php echo $this->Html->link(__("Recommended Events"), array('volunteer' => true, 'controller' => 'events', 'action' => 'matches'), array('class' => 'list-group-item')); ?>
			</div>
		<?php else: ?>
			
			<div class="list-group">
				<?php echo $this->Html->link(__("Login to %s", Configure::read('Solution.name') ), array('volunteer' => true, 'controller' => 'events', 'action' => 'matches'), array('class' => 'list-group-item')); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="col-md-9">
		<h2><?php echo __('Upcoming Events'); ?></h2>
		<p>You're viewing upcoming service opportunities.  Learn more about an opportunity by clicking its title.</p>
<?php
/*
		<table cellpadding="0" cellspacing="0" class="table table-striped">
		<tr>
				<th><?php echo $this->Paginator->sort('title'); ?></th> 
				<th><?php echo $this->Paginator->sort('organization'); ?></th> 
				<th><?php echo $this->Paginator->sort('start_time'); ?></th>
				<th><?php echo $this->Paginator->sort('stop_time'); ?></th>
				<th class="actions">&nbsp;</th>
		</tr>
*/
?>
		<ul class="media-list">
		<?php foreach ($events as $event): ?>
		<li class="media">
			<?php if( isset($event['Image']) ) : ?>

			<?php endif; ?>
			<div class="media-body">
				<h4 class="media-heading">
					<?php echo $this->Html->link( $event['Event']['title'], array('volunteer' => false, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']) ); ?>
				</h4>
				<p>
					<?php echo h( $event['Event']['description'] ); ?><br>
					<?php echo h( $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']) ); ?>
					<?php 

					if( !empty($event['Skill']) ) : ?>
					<br>
					<small>Recommended skills: </small>
					<?php
						foreach($event['Skill'] as $skill)
						{
							echo $this->Html->tag('span', $skill['Skill'], array('class' => 'label label-primary') );
							echo ' ';
						}
					endif; ?>
				</p>
				<hr>

			</div>
		</li>
<?php
/*
		<tr>
			<td><?php echo h($event['Event']['title']); ?>&nbsp;</td>
			<td><?php echo h($event['Organization']['name']); ?>&nbsp;</td>

			<td> <?php $startTime = new DateTime($event['Event']['start_time']);
				echo $startTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

			<td> <?php $stopTime = new DateTime($event['Event']['stop_time']);
				echo $stopTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

			<td class="actions">
				<?php

				echo $this->Html->link(
					__('View Event'),
					array('action' => 'view', $event['Event']['event_id']),
					array('class' => 'btn btn-primary btn-xs')
				); ?>
			</td>
		</tr>
*/
?>
	<?php endforeach; ?>
</ul>
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
	</div>
</div>
