<?php
$date_fmt = "F j, Y";
$time_fmt = "g:i a";
$datetime_fmt = $date_fmt . " " . $time_fmt;
$periods = array('month' => __("Past Month"), 'year' => __("Past Year"), 'ytd' => __("Year-To-Date"), 'custom' => __("Custom Range") );
?>

<h1>My Volunteer Activity</h1>
<p class="text-muted">This space is for you to keep track of your service.  Here, you can create reports and manage organizations you are connected with.</p>

<h2><?php echo __('Overview'); ?></h2>
<p class="text-muted">Here are the highlights of your time using <?php echo h(Configure::read('Solution.name')); ?></p>

<div class="row">

	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_past_month[0][0]['UserPastMonth']/60, 2) ); ?></span>
			hours volunteered in past month
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_ytd[0][0]['UserYTD']/60, 2) ); ?></span>
			hours volunteered year-to-date
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_past_year[0][0]['UserPastYear']/60, 2) ); ?></span>
			hours volunteered in past year
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat"><?php echo h( number_format($summary_all_time[0][0]['UserAllTime']/60, 2) ); ?></span>
			hours volunteered all-time
		</div>
	</div>
</div>

<hr>

<div class="row">
	<div class="col-md-12">
		<h2>Detailed Activity</h2>
		<p class="text-muted"><span class="glyphicon glyphicon-bookmark"></span> Filter your activity based on specified dates.  You can bookmark reports for easy access in the future.</p>

			<ul class="nav nav-tabs">
				<?php
				if( !isset($period) )
					$period = '';

					foreach ( $periods as $pd => $label)
					{
						echo sprintf('<li class="%s">%s</li>',
							( ($pd == $period) ? 'active' : '' ),
							$this->Html->link($label, array('controller' => 'users', 'action' => 'activity', $pd) )
						);
					}
				?>
			</ul>

			<?php
			if ($period == 'custom') :

				$form_defaults['class'] = "form-inline append-bottom append-top";
				$form_defaults['type'] = "GET";

				echo $this->Form->create('Custom', $form_defaults);
					echo $this->Form->input(
						'Custom.start', array('label' => "Start Date", 'type' => 'date', 'separator' => " " )  ); 
					echo $this->Form->input(
						'Custom.stop', array('label' => "Stop Date", 'type' => 'date', 'separator' => " ")  );
					echo $this->Form->button(
						'Filter Activity', array('class' => 'btn btn-primary') );
				echo $this->Form->end( null ); 

			endif; 

			if( !empty($time_data) && $period ):
				$duration_total = 0;
			?>
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo $this->Paginator->sort('Event.title', 'Event'); ?> (more sorts: <?php echo $this->Paginator->sort('Event.start_time', __('start') ); ?>, <?php echo $this->Paginator->sort('Event.stop_time', __('stop') ); ?>) </th>
							<th><?php echo $this->Paginator->sort('Time.start_time', 'In'); ?></th>
							<th><?php echo $this->Paginator->sort('Time.stop_time', 'Out'); ?></th>
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
								<strong><?php echo $this->Html->link( $time_entry['Event']['title'], array('volunteer' => true, 'controller' => 'events', 'action' => 'view', $time_entry['Event']['event_id']) );?>&nbsp;</strong>
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
									// TODO: calculate this with SQL.
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
								Page Total
							</th>
							<th>
								<?php echo number_format($duration_total, 2); ?> hr&nbsp;
							</th>
						</tr>
						<tr>
							<th colspan="3">
								<?php echo h($periods[$period]); ?> Total
							</th>
							<th>
								<?php echo number_format($period_total[0][0]['PeriodTotal'], 2); ?> hr
							</th>
					</tfoot>
				</table>
			</div>
				<ul class="pagination collapse-top">
					<?php
						echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
						echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
						echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
					?>
				</ul>
				<p><em>Missed punches?</em>  You will need to talk to an event coordinator to fix these.  Visit the event page to find your event coordinator.</p>

		<?php else : ?>
			<p class="append-top"><em>You have no volunteer activity in the specified time period.</em></p>
		<?php endif; ?>
	</div>
</div>

<hr>

<div class="row">
	<div class="col-md-12">
		<h2>My Organizations</h2>
		<p class="text-muted">You are connected to these organizations</p>
	</div>
	<div class="col-md-4">
		<h3>Publishing to...</h3>
		<?php if( !empty($publishing) ) : ?>
		<p>Your volunteering activity may be viewed coordinators and supervisors of these organizations.</p>
		<table class="table">
			<thead>
				<tr>
					<th>Organization</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($publishing as $org) : ?>
				<tr>
					<td><?php echo h($org['Organization']['name']); ?></td>
					<td><?php 
						echo $this->Html->link(
							__('Leave'),
							array('volunteer' => true, 'controller' => 'organizations', 'action' => 'leave', $org['Organization']['organization_id']),
							array('class' => 'btn btn-danger btn-xs'),
							__('Are you sure you want to leave this organization?')
						);
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
		<p><em>You are not publishing activity to any organizations</em></p>
		<?php endif; ?>
	</div>
	<div class="col-md-4">
		<h3>Supervising</h3>
		<?php if( !empty($supervising) ) : ?>
		<p>You can view event and time date for the following organizations.</p>
		<table class="table">
			<thead>
				<tr>
					<th>Organization</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($supervising as $org) : ?>
				<tr>
					<td><?php echo h($org['Organization']['name']); ?></td>
					<td><?php
							echo $this->Html->link(
								__('Supervise'),
								array('supervisor' => true, 'controller' => 'organizations', 'action' => 'view', $org['Organization']['organization_id']),
								array('class' => 'btn btn-primary btn-xs')
							);
							echo " ";
							echo $this->Html->link(
								__('Leave'),
								array('supervisor' => true, 'controller' => 'organizations', 'action' => 'leave',  $org['Organization']['organization_id']),
								array('class' => 'btn btn-danger btn-xs'),
								__('Are you sure you want to stop publishing your activity to this organization?')
							); 
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
		<p><em>You are not supervising any organizations</em></p>
		<?php endif; ?>
	</div>
	<div class="col-md-4">
		<h3>Coordinating</h3>
		<?php if( !empty($coordinating) ) : ?>
		<p>You can create, manage, and supervise events for these organizations..</p>
		<table class="table">
			<thead>
				<tr>
					<th>Organization</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($coordinating as $org) : ?>
				<tr>
					<td><?php echo h($org['Organization']['name']); ?></td>
					<td><?php 
						echo $this->Html->link(
							__('Coordinate'),
							array('coordinator' => true, 'controller' => 'organizations', 'action' => 'view', $org['Organization']['organization_id']),
							array('class' => 'btn btn-primary btn-xs')
						);
						echo " ";echo $this->Html->link(
							__('Leave'),
							array('coordinator' => true, 'controller' => 'organizations', 'action' => 'leave', $org['Organization']['organization_id']),
							array('class' => 'btn btn-danger btn-xs'),
							__('Are you sure you want to leave this organization?')
						);
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
		<p><em>You are not coordinating any organizations</em></p>
		<?php endif; ?>
	</div>
</div>