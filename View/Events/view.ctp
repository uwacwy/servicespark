<div class="events view">
	<div style="text-align: right">
		<?php echo $this->Html->link(__('Back to Events'), array('action' => 'index'), array('class' => 'btn btn-primary')); ?>
	</div>
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
		<?php
			if($event['Address'] != [])
			{
				echo "<h2>Event Addresses</h2>";

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
					echo $address['address1'] . ' <br>';
					if($address['address2'] != null)
					{ 
						echo $address['address1'] . ' <br>';
					}
					echo $address['city'] . ', ' . $address['state'] . '  ' . $address['zip'];
					echo '</address></div>';
				}
				echo "<br>";
			}

		?>
	</div>