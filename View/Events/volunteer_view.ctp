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
	</div>
</div>


<?php
	if( !empty($event['Address']) )
	{
		echo '<div class="row"><div class="col-md-12"><h2>Event Addresses</h2></div></div>';

		foreach( $event['Address'] as $address )
		{
			echo '<div class="row"><div class="col-md-4"><address>';
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

			if( $address['type'] != 'mailing' )
			{
				echo '<div class="col-md-8">';
					echo '<iframe width="100%" height="300" frameborder="0" style="border:0"';
						echo 'src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAVfPLXNv_u-c7k6gQpoPK8c7oDbGHwvNU';
					    echo '&q='.$address['address1'].'+'.$address['city'].'+'.$address['state'].'+'.$address['zip'].'">';
					echo '</iframe>';
				echo '</div>';
			}
			echo '</div><hr>';
		}
	}
?>