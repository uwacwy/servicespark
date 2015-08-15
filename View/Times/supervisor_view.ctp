<div class="row">
	<div class="col-md-3">
		<?php echo $this->Element('supervisor_time_actions', array('time' => $time)); ?>
	</div>
	<div class="col-md-9">
		<h1>
			<small><?php echo h($time['Event']['Organization']['name']); ?></small><br>
			<?php echo h( sprintf( __('Viewing Time Entry %u'), $time['Time']['time_id'] ) ); ?> <small>for <?php echo h($time['User']['full_name']); ?></small>
		</h1>
		<p><strong><?php echo h($time['Event']['title']); ?></strong><br>
			<?php echo h( $this->Duration->format($time['Event']['start_time'], $time['Event']['stop_time']) ); ?><br>
			<?php echo h( $time['Event']['description']); ?></p>
		<div class="table-responsive">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th>Volunteer</th>
						<th>Time In</th>
						<th>Time Out</th>
						<th>Duration</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong><?php echo h($time['User']['full_name']); ?></strong><br><?php echo h( sprintf(__('@%s'), $time['User']['username']) ); ?></td>
						<td><?php echo h( date('F j, Y g:i a', strtotime($time['Time']['start_time']) ) ); ?></td>
						<td><?php
							if( $time['Time']['stop_time'] != null )
							{
								echo h( date('F j, Y g:i a', strtotime($time['Time']['stop_time']) ) );
							}
							else
							{
								echo $this->Html->tag('em', __('missing punch') );
							}
						?></td>
						<td><?php
							if( $time['Time']['stop_time'] != null )
							{
								echo h( sprintf( __('%s hr'), number_format($time['Time']['duration'], 2) ) );
							}
						?></td>

					</tr>
				</tbody>
			</table>
		</div>
		<p class="text-muted"><?php echo h(
			sprintf('%s joined %s %s',
				$time['User']['full_name'], 
				Configure::read('Solution.name'),
				$this->Tm->timeAgoInWords( sprintf('%u minutes ago', $time['User']['account_age']) )
			) 
		); ?></p>
	</div>
</div>