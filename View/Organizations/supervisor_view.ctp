<?php
$periods = array('month' => "Past Month", 'year' => 'Past Year', 'ytd' => "Year-To-Date", 'custom' => "Custom Range");
?>
<h1>Activity</h1>

<h2>Overview</h2>
<div class="row">
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_all_time[0][0]['OrganizationAllTime']/60, 2) ); ?></span>
			hours volunteered all-time
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_past_month[0][0]['OrganizationPastMonth']/60, 2) ); ?></span>
			hours volunteered in past month
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_past_year[0][0]['OrganizationPastYear']/60, 2) ); ?></span>
			hours volunteered in past year
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_ytd[0][0]['OrganizationYTD']/60, 2) ); ?></span>
			hours volunteered year-to-date
		</div>
	</div>
</div>

<ul class="nav nav-tabs">
	<?php
	if( !isset($period) )
		$period = '';

		foreach ( $periods as $pd => $label)
		{
			echo sprintf('<li class="%s">%s</li>',
				( ($pd == $period) ? 'active' : '' ),
				$this->Html->link($label, array('controller' => 'Organizations', 'action' => 'supervisor_view', $pd) )
			);
		}
	?>
</ul>

<?php

$date_fmt = "F j, Y";
$time_fmt = "g:i a";
$datetime_fmt = $date_fmt . " " . $time_fmt;

if( !empty($time_data) && $period ):
	$duration_total = 0;
?>
<h2><?php echo h($periods[$period]); ?></h2>

<?php if ($period == 'custom') : ?>

	<?php echo $this->Form->input('Custom.start', array('label' => "Start Date", 'type' => 'date') ); ?>
	<?php echo $this->Form->input('Custom.stop', array('label' => "Stop Date", 'type' => 'date') ); ?>

<?php endif; ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Event</th>
				<th>In</th>
				<th>Out</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			foreach($time_data as $time_entry):
			$row_total = 0;
		?>
			<tr>
				<td>
					<strong><?php echo h($time_entry['Event']['title']); ?>&nbsp;</strong>
					<br><?php echo h($time_entry['Event']['description']); ?>&nbsp;
					<br>
						<?php echo h( date($datetime_fmt, strtotime($time_entry['Event']['start_time']) ) ); ?> - 
						<?php echo h( date($time_fmt, strtotime($time_entry['Event']['stop_time']) ) ); ?>
				</td>
				<td>
					<?php echo h( date($datetime_fmt, strtotime($time_entry['Time']['start_time']) ) ); ?>&nbsp;
				</td>
				<td>
					<?php
						if( $time_entry['Time']['stop_time'] != null )
						{
							echo h( date($datetime_fmt, strtotime($time_entry['Time']['stop_time']) ) );
						}
						else
						{
							echo "<em>missed punch</em>";
						}
					?>
					&nbsp;
				</td>
				<td>
					<?php
						if( $time_entry['Time']['stop_time'] != null )
						{
							$start_ts = strtotime($time_entry['Time']['start_time']);
							$stop_ts = strtotime($time_entry['Time']['stop_time']);
							$row_total = ($stop_ts - $start_ts)/3600;
							echo number_format( $row_total, 2) . " hr";
						}
						else
						{
							echo "&mdash;";
						}
					?>
					&nbsp;
				</td>
			</tr>
		<?php
			$duration_total += $row_total;
			endforeach;
		?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="3">
					<?php echo h($periods[$period]); ?> Total
				</th>
				<th>
					<?php echo number_format($duration_total, 2); ?> hr&nbsp;
				</th>
			</tr>
		</tfoot>
	</table>
<?php else : ?>
	<em>You have no volunteer activity in the specified time period.</em>
<?php endif; ?>