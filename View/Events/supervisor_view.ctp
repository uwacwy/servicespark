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

<div class="row">
	<div class="col-md-12">	
		<?php
			if( !empty($event['Address']) )
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
</div>

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
