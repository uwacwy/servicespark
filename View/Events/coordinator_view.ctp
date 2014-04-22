<div class="events view">

	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><strong><?php echo __('Coordinator'); ?></strong></li>
				<li><?php echo $this->Html->link( Configure::read('Solution.name'), '/'); ?></li>
				<li><?php echo $this->Html->link( $event['Organization']['name'], array('coordinator' => true, 'controller' => 'organizations', 'action' => 'view', $event['Organization']['organization_id']) ); ?></li>
				<li><?php echo h( __( $event['Event']['title']) ); ?></li>
			</ol>
		</div>
	</div>



	<?php 
		$startTime = new DateTime($event['Event']['start_time']);
		$stopTime = new DateTime($event['Event']['stop_time']);
	?>


	<div class="row">
		<div class="col-md-12">
			<div class="btn-group pull-right">
				<button class="btn btn-default btn-md dropdown-toggle btn-primary" type="button" data-toggle="dropdown">
					Actions <span class="caret"></span>
					</button>
				<ul class="dropdown-menu">
					<li><?php echo $this->Html->link(__('Edit Event'), array('action' => 'edit', $event['Event']['event_id'])); ?></li>
					<li><?php echo $this->Form->postlink(__('Delete Event'), array('action' => 'delete', $event['Event']['event_id']), null, __('Are you sure you want to delete # %s?', $event['Event']['event_id'])); ?> </li>
					<li><?php echo $this->Html->link(__('List Events'), array('action' => 'index')); ?></li>
					<li><?php echo $this->Html->link(__('New Event'), array('action' => 'add')); ?> </li>
				</ul>
			</div>
			<h1><small><?php echo $event['Organization']['name']; ?></small><br>
				<?php echo h($event['Event']['title']); ?>
				<small><?php echo $this->Duration->format($startTime->format(DateTime::W3C), $stopTime->format(DateTime::W3C) ); ?></small></h1>
			<blockquote><?php echo h($event['Event']['description']); ?></blockquote>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-12">
			<h2>Volunteer Time Tokens</h2>
			<p class="text-muted">Show these time tokens and QR codes to your volunteers to help them track volunteer time.</p>
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
	</div>
	<hr>
	<div class="row">		
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
	
	<div class="row">
		<div class="col-md-12">

			<h2>Volunteer Time Entries</h2>
			<?php if( !empty($times) ) : ?>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
									<th><?php echo $this->Paginator->sort('User.first_name', "First Name"); ?></th>
									<th><?php echo $this->Paginator->sort('User.last_name', "Last Name"); ?></th>
									<th><?php echo $this->Paginator->sort('Time.start_time', "Clock In"); ?></th>
									<th><?php echo $this->Paginator->sort('Time.stop_time', "Clock Out"); ?></th>
									<th class="text-right"><?php echo $this->Paginator->sort('Time.duration', "Duration"); ?></th>
									<th>&nbsp;</th>
							</tr>
						</thead>
						<?php
							$grand_total_time = 0;
							foreach($times as $time) :
								$clock_in = new DateTime($time['Time']['start_time']);
								if($time['Time']['stop_time'] != null)
									$clock_out = new DateTime($time['Time']['stop_time']);
						?>
							<tr>
								<td><?php echo h($time['User']['first_name']); ?></td>
								<td><?php echo h($time['User']['last_name']); ?></td>
								<td><?php echo h($clock_in->format('F j, Y, g:i a')); ?></td>
								<td><?php
									if( isset($clock_out) )
									{
										echo h($clock_out->format('F j, Y, g:i a'));
									}
									else
									{
										echo $this->Html->tag('em', __('missed punch') );
									}
								?></td>
								<td class="text-right"><?php
									echo h( sprintf( __('%s hr'), number_format($time['Time']['duration'], 2, '.', ',') ) );
									$grand_total_time += $time['Time']['duration'];
								?></td>
								<td class="text-right"><?php
									echo $this->Html->link(
										__('Edit Time Entry'),
										array('coordinator' => true, 'controller' => 'times', 'action' => 'edit', $time['Time']['time_id']),
										array('class' => 'btn btn-xs btn-primary')
									);
								?></td>
							</tr>
						<?php
							endforeach;
						?>
						<tfoot>
							<tr>
								<th colspan="4">Page Total</th>
								<th class="text-right"><?php echo h( sprintf( __('%s hr'), number_format($grand_total_time, 2) ) ); ?></td>
								<th>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="4">Event Total</th>
								<th class="text-right"><?php echo h( sprintf( __('%s hr'), number_format($event_total[0][0]['EventTotal'], 2) ) ); ?></th>
								<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>
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