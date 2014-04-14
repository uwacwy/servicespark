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