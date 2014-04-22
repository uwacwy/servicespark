<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="<?php echo $this->Html->url('/'); ?>"><span class="glyphicon glyphicon-home"></span><span class="sr-only"><?php echo Configure::read('Solution.name'); ?></span></a></li>
			<li><strong><?php echo h( __('Supervisor') ); ?></strong></li>
			<li><?php echo $this->Html->link(
				$event['Organization']['name'],
				array('supervisor' => true, 'controller' => 'organizations', 'action' => 'view', $event['Organization']['organization_id'])
			); ?></li>
			<li><?php
				echo h( sprintf( __('Viewing event %s'), $event['Event']['title']) );
			?></li>
		</ol> 
	</div>
</div>

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

<div class="row">
	<div class="col-md-12">
		<h3>Volunteer Report</h3>

		<?php if( !empty($times) ) : ?>
			<div class="table-responsive">
			<table cellpadding="0" cellspacing="0" class="table table-striped">
			<tr>
					<th><?php echo $this->Paginator->sort('User.first_name', "First Name"); ?></th>
					<th><?php echo $this->Paginator->sort('User.last_name', "Last Name"); ?></th>
					<th><?php echo $this->Paginator->sort('Time.start_time', "Clock In"); ?></th>
					<th><?php echo $this->Paginator->sort('Time.stop_time', "Clock Out"); ?></th>
					<th>Total Time</th>
			</tr>
			<?php
				$grand_total_time = 0;
				foreach($times as $time)
				{
					echo "<tr>";
					echo "<td>" . $time['User']['first_name'] . "</td>";
					echo "<td>" . $time['User']['last_name'] . "</td>";
					$clock_in = new DateTime($time['Time']['start_time']);
					echo "<td>" . $clock_in->format('F j, Y, g:i a') . "</td>";
					if($time['Time']['stop_time'] != null)
					{
						$clock_out = new DateTime($time['Time']['stop_time']);
						echo "<td>" . $clock_out->format('F j, Y, g:i a') . "</td>";
					}else{
						echo "<td><em>missed punch</em></td>";
					}

					$total_time = $time[0]['OrganizationAllTime'];
					$hours = floor($total_time);
					$minutes = round(60*($total_time-$hours));
					echo "<td>" . $hours . ' Hour(s) ' . $minutes . " Minute(s)</td>";
					$grand_total_time += $total_time;
					echo "</tr>";
				}
				echo "</table> </div>";
				$hours = floor($grand_total_time);
				$minutes = round(60*($grand_total_time-$hours));
				echo '<h5 align="right">Total Event Time: ' . $hours . ' Hour(s) ' . $minutes . ' Minute(s)</h5>';
			?>
			
			<ul class="pagination bottom">
				<?php
					echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
					echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
					echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				?>
			</ul>
			
		<?php else: ?>
			<p><em>there is no time-punch data for this event.</em></p>
		<?php endif; ?>
	</div>
</div>