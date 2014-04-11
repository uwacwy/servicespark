<div class="btn-group pull-right">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		Other Views <span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">

		<li><?php echo $this->Html->link('Best', array('go' => true, 'controller' => 'events', 'action' => 'index') ); ?></li>
		<li><?php echo $this->Html->link('Coordinator', array('coordinator' => true, 'controller' => 'events', 'action' => 'index') ); ?></li>
		<li><?php echo $this->Html->link('Supervisor', array('supervisor' => true, 'controller' => 'events', 'action' => 'index') ); ?></li>
		<li><?php echo $this->Html->link('Volunteer', array('volunteer' => true, 'controller' => 'events', 'action' => 'index') ); ?></li>
		<li class="divider"></li>
		<li><?php echo $this->Html->link('Help', array('coordinator' => false, 'controller' => 'pages', 'action' => 'display', 'help_roles') ); ?></li>
	</ul>
</div>

<div class="events index">
	<h2><?php echo __('Events'); ?></h2>
	
	<div style="text-align: right">
		<?php echo $this->Html->link(__('Create Event'), array('action' => 'add'), array('class' => 'btn btn-primary')); ?>
	</div>
	
	</br>

	<table cellpadding="0" cellspacing="0" class="table table-striped">
	<tr>
			<th><?php echo $this->Paginator->sort('title'); ?></th>
			<th><?php echo $this->Paginator->sort('Organization.name'); ?></th> 
			<th><?php echo $this->Paginator->sort('start_time'); ?></th>
			<th><?php echo $this->Paginator->sort('stop_time'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($events as $event): ?>
	<tr>
		<td><?php echo h($event['Event']['title']); ?>&nbsp;</td>
		<td><?php echo h($event['Organization']['name']); ?>&nbsp;</td>

		<td> <?php $startTime = new DateTime($event['Event']['start_time']);
			echo $startTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

		<td> <?php $stopTime = new DateTime($event['Event']['stop_time']);
			echo $stopTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $event['Event']['event_id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $event['Event']['event_id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $event['Event']['event_id']), null, __('Are you sure you want to delete # %s?', $event['Event']['event_id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo "  ";
		echo $this->Paginator->numbers(array('separator' => ' '));
		echo "  ";
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

