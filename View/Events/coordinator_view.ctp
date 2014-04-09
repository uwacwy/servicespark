<div class="events view">
<h2><?php echo __('Event'); ?></h2>
	<?php $startTime = new DateTime($event['Event']['start_time']);
		$stopTime = new DateTime($event['Event']['stop_time']);
		?>


	<div class="row">
		<div class="col-md-12">
			<h1><small><?php echo $event['Organization']['name']; ?></small><br><?php echo h($event['Event']['title']); ?> <small><?php echo $startTime->format('F j, Y, g:i a'); ?> - <?php echo $stopTime->format('g:i a'); ?></small></h1>
			<blockquote><?php echo h($event['Event']['description']); ?></blockquote>
		</div>
	</div>
	<hr>
	<div class="row">
		<h2>Volunteer Checkin and Checkout</h2>
		<div class="col-md-6">
			<div class="well text-center">
				<?php echo sprintf(
					'<img src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=%s&chld=H|0">',
					urlencode( $this->Html->Url(array('controller' => 'times', 'action' => 'in', 'volunteer' => true, $event['Event']['start_token']), true ) )
				); ?>
				<h3>In Token</h3>
				<?php echo h($event['Event']['start_token']); ?>
			</div>
		</div>
		<div class="col-md-6">
			<div class="well text-center">
				<?php echo sprintf(
					'<img src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=%s&chld=H|0">',
					urlencode( $this->Html->Url(array('controller' => 'times', 'action' => 'out', 'volunteer' => true, $event['Event']['stop_token']), true ) )
				); ?>
				<h3>Out Token</h3>
				<?php echo h($event['Event']['stop_token']); ?>
			</div>
		</div>
	</div>
	<hr>
	<div class="row">
		<h2>Event Addresses</h2>
		<?php
			foreach( $event['Address'] as $address )
			{
				echo '<div class="col-md-12"><address>';
				switch($address['type'])
				{
					case 'physical':
						echo '<h4>Physical Address</h4>';
						break;
					case 'mailing':
						echo '<h4>Mailing Address</h4>';
						break;
					case 'both':
						echo '<h4>Physical and Mailing Address</h4>';
						break;
				}
				echo sprintf('%s<br>%s<br>%s, %s %s',
					$address['address1'],
					$address['address2'],
					$address['city'],
					$address['state'],
					$address['zip']
				);
				echo '</address></div>';
			}
		?>
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
