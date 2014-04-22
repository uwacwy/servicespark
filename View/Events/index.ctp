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
		<p class="visible-sm visible-xs"><span class="glyphicon glyphicon-resize-horizontal"></span> <?php echo __('Scroll side-to-side to see more information'); ?></p>
		<p>Sort by <?php echo $this->Paginator->sort('Event.title', 'Event Title'); ?>, 
			<?php echo $this->Paginator->sort('Organization.name', __('Organization Name') ); ?>, 
			<?php echo $this->Paginator->sort('Event.start_time', __('Event Start') ); ?>, 
			<?php echo $this->Paginator->sort('Event.stop_time', __('Event Stop') ); ?>
		</p>
		<div class="table-responsive">
			<table class="table table-striped table-condensed">
				<?php foreach ($events as $event): ?>

				<tr>
					<td>
						<strong><?php echo $this->Html->link( $event['Event']['title'], array('volunteer' => false, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']) ); ?></strong>
						<br><?php echo h( $event['Event']['description'] ); ?>
						<br><?php echo h( $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']) ); ?>
						<?php if( !empty($event['Skill']) ) : ?>
							<br>
							<?php
								foreach($event['Skill'] as $skill)
								{
									echo $this->Html->tag('span', $skill['skill'], array('class' => 'label label-info', 'title' => __('If you enjoy %s, consider volunteering for this event.', $skill['skill']) ) );
									echo ' ';
								}
							?>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $this->Html->link( $event['Organization']['name'], array('controller' => 'organizations', 'action' => 'view', $event['Organization']['organization_id']), array('class' => 'btn btn-info btn-xs')); ?>
					</td>

				</tr>

				<?php endforeach; ?>
				</ul>
			</table>
		</div>
		<p>
			<?php
				echo $this->Paginator->counter(array(
					'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
			?>
		</p>
		<ul class="pagination bottom">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
	</div>
</div>
