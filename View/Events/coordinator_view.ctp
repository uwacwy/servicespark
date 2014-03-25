<div class="events view">
<h2><?php echo __('Event'); ?></h2>
	<!-- <table class="table table-bordered table-striped">
		<tr>
		  <td><strong><?php echo __('Title'); ?></strong></td>
		  <td><?php echo h($event['Event']['title']); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Organization'); ?></strong></td>
		  <td><?php echo h($event['Organization']['name']); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Description'); ?></strong></td>
		  <td><?php echo h($event['Event']['description']); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Start Time'); ?></strong></td>
		  <td><?php $startTime = new DateTime($event['Event']['start_time']);
			echo $startTime->format('F j, Y, g:i a'); ?></td>
		</tr>
		<tr>
		  <td><strong><?php echo __('Stop Time'); ?></strong></td>
		  <td><?php $stopTime = new DateTime($event['Event']['stop_time']);
			echo $stopTime->format('F j, Y, g:i a'); ?></td>
		</tr>
	</table>  -->

	<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Event'); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php
						echo $this->Form->input('title', array('class' => 'form-control', 'disabled' => 'disabled') );
						echo $this->Form->input('description', array('type' => 'textarea', 'class' => 'form-control', 'disabled' => 'disabled') );
						echo $this->Form->input('start_time', array('disabled' => 'disabled'));
						echo $this->Form->input('stop_time', array('disabled' => 'disabled'));
						echo $this->Form->input('Organization.name', array('class' => 'form-control', 'disabled' => 'disabled', 'label' => 'Organization') );
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="well">
					<?php echo $this->Address->printAddress($this->request->data['Address']); ?>
				</div>
			</div>
		</div>
	</div>
	</div>

</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Event'), array('action' => 'edit', $event['Event']['event_id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Event'), array('action' => 'delete', $event['Event']['event_id']), null, __('Are you sure you want to delete # %s?', $event['Event']['event_id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Events'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Event'), array('action' => 'add')); ?> </li>
	</ul>
</div>

<!-- <div class="related">
	<h3><?php echo __('Related Events'); ?></h3>
	<?php if (!empty($event['Event'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Event Id'); ?></th>
		<th><?php echo __('Title'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Start Time'); ?></th>
		<th><?php echo __('Stop Time'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($event['Event'] as $event): ?>
		<tr>
			<td><?php echo $event['event_id']; ?></td>
			<td><?php echo $event['title']; ?></td>
			<td><?php echo $event['description']; ?></td>
			<td><?php echo $event['start_time']; ?></td>
			<td><?php echo $event['stop_time']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'events', 'action' => 'view', $event['event_id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'events', 'action' => 'edit', $event['event_id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'events', 'action' => 'delete', $event['event_id']), null, __('Are you sure you want to delete # %s?', $event['event_id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Event'), array('controller' => 'events', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div> -->
