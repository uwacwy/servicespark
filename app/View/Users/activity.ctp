<?php
$date_fmt = "F j, Y";
$time_fmt = "g:i a";
$datetime_fmt = $date_fmt . " " . $time_fmt;
$periods = array(
	'month' => __("Past Month"), 
	'year' => __("Past Year"), 
	'ytd' => __("Year-To-Date"), 
	'all' => __('All Activity'), 
	'custom' => __("Custom Range") );


?>

<div class="row">
	<div class="col-md-3">
		<h3><?php echo h($user['User']['full_name']); ?> 
			<?php
				$user_class = "label-default";
				$reputation_sign = "";
				if( $user['User']['reputation'] > 0 )
				{
					$reputation_sign = "+";
					$user_class = "label-success";
				}
				else if( $user['User']['reputation'] < 0 )
				{
					$user_class = "label-danger";
				}
					
				echo sprintf('<span class="label reputation %s" title="%s">%s%s</span>',
					$user_class,
					__("This is your reputation.  It goes up when you volunteer."),
					$reputation_sign,
					number_format($user['User']['reputation'])
				);
			?> <br>
			<small>@<?php echo h($user['User']['username']); ?></small>
			
		</h3>
		<div class="list-group">

			<?php

				if( !isset($period) )
					$period = '';

				foreach ( $periods as $pd => $label)
				{
					
					$sprint = '<a href="%1$s" class="%2$s">%3$s</a>';
					
					echo sprintf($sprint,
						$this->Html->url( array('controller' => 'users', 'action' => 'activity', $pd) ),
						( ($pd == $period) ? 'list-group-item active' : 'list-group-item' ),
						$label
					);
				}
			?>
		</div>
		<p><?php echo $this->Utility->btn_link_icon(
			__("Deleted Time Entries"),
			array(
				'volunteer' => false,
				'controller' => 'times',
				'action' => 'trash'
			),
			'btn btn-danger btn-sm btn-block',
			'glyphicon-trash'); ?> 
			<?php echo $this->Utility->btn_link_icon(
					__('Download as Microsoft Excel'),
					array('controller' => 'users','action' => 'activity',$period,'xlsx', '?' => $_SERVER['QUERY_STRING']),
					'btn btn-success btn-sm btn-block',
					'glyphicon-download-alt'
				); ?></p>
		
		<p><i class="glyphicon glyphicon-hand-right"></i>&nbsp;<?php echo __("Drag to your bookmarks bar for easy access in the future."); ?>
		<div>
			<?php
				echo $this->Utility->btn_link_icon(
					__('%s: %s', Configure::read('Solution.name'), $periods[$period]),
					array('controller' => 'users','action' => 'activity',$period,'?' => $_SERVER['QUERY_STRING']),
					'btn btn-info btn-sm btn-block',
					'glyphicon-bookmark'
				);
			?> 
		</div>

	</div>
	<div class="col-md-9">

		<div class="row">
			<div class="col-md-12">
				
				<h2><?php echo __('Detailed Activity'); ?></h2>
				
					<?php
					if ($period == 'custom') :

						$form_defaults['class'] = "form-inline append-bottom append-top";
						$form_defaults['type'] = "GET";

						echo $this->Form->create('Custom', $form_defaults);
							echo $this->Form->input(
								'Custom.start', array('label' => "Start Date", 'type' => 'date', 'separator' => " " )  );
							echo '<br>';
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
									<th><?php echo __("Description"); ?></th>
									<th><?php echo $this->Paginator->sort('Time.start_time', 'In'); ?></th>
									<th><?php echo $this->Paginator->sort('Time.stop_time', 'Out'); ?></th>
									<th><?php echo $this->Paginator->sort('Time.duration', 'Total'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
								foreach($time_data as $time_entry):
								$row_total = 0;
							?>
								<tr>
									<td>
										
										<?php if( !empty($time_entry['EventTime']) ): ?>
											<strong><?php echo $this->Html->link( 
											$time_entry['EventTime'][0]['Event']['title'], 
											array('volunteer' => false, 'controller' => 'times', 'action' => 'view', $time_entry['Time']['time_id']) );?>&nbsp;</strong>
											<br><?php echo h($time_entry['EventTime'][0]['Event']['description']); ?>&nbsp;
											<br>
												<?php echo $this->Duration->format(
													$time_entry['EventTime'][0]['Event']['start_time'], 
													$time_entry['EventTime'][0]['Event']['stop_time']); ?>
										
										<?php elseif( !empty($time_entry['OrganizationTime']) ): ?>
											<?php $memo = __("<em>No memo provided</em>");
											if( !empty($time_entry['OrganizationTime'][0]['memo']) )
												$memo = h($time_entry['OrganizationTime'][0]['memo']); ?>
											<strong>
												<?php echo $this->Html->link(
													$time_entry['OrganizationTime'][0]['Organization']['name'],
													array('volunteer' => false, 'controller' => 'times', 'action' => 'view', $time_entry['Time']['time_id'])
												); ?>
											</strong>
											<div><?php echo $memo; ?></div>
											<?php if( $time_entry['Time']['status'] == "approved" ): ?>
												<em><?php echo __("this time entry has been approved"); ?></em>
											<?php elseif( $time_entry['Time']['status'] == "pending" ): ?>
												<em><?php echo __("this time entry has not been reviewed yet"); ?></em>
											<?php else: ?>
												<em><?php echo __("this time entry has been rejected"); ?></em>
											<?php endif; ?>
										<?php endif; ?>
										
									</td>
									<td>
										<?php echo $this->Utility->no_wrap( h( date($datetime_fmt, strtotime($time_entry['Time']['start_time']) ) ) ); ?>&nbsp;
									</td>
									<td>
										<?php
											if( $time_entry['Time']['stop_time'] != null )
											{
												echo $this->Utility->no_wrap( h( date($datetime_fmt, strtotime($time_entry['Time']['stop_time']) ) ) );
											}
											else
											{
												echo "<em>missed punch</em>";
											}
										?>&nbsp;
									</td>
									<td>
										<?php
										
											$duration_sprint = __("%s&nbsp;hr");
											
											if( !empty($time_entry['OrganizationTime']) && $time_entry['Time']['status'] != "approved" )
												$duration_sprint = __("<em>%s&nbsp;hr</em>");
									
											if( $time_entry['Time']['stop_time'] != null )
											{
												echo sprintf($duration_sprint,
													number_format( $time_entry['Time']['duration'], 2)
												);
											}
											else
											{
												echo "&mdash;";
											}
										?>&nbsp;
									</td>
								</tr>
							<?php
								if( !empty($time_entry['EventTime']) )
									$duration_total += $time_entry['Time']['duration'];
								elseif( !empty($time_entry['OrganizationTime']) && $time_entry['Time']['status'] == "approved" )
									$duration_total += $time_entry['Time']['duration'];
								endforeach;
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3">
										<?php echo __("Page Total"); ?>
									</th>
									<th>
										<?php echo number_format($duration_total, 2); ?>&nbsp;hr&nbsp;
									</th>
								</tr>
								<tr>
									<th colspan="3">
										<?php echo h($periods[$period]); ?> Total
									</th>
									<th>
										<?php echo number_format($period_total[0][0]['period_total'], 2); ?> hr
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
						<p><em><?php echo __('Missed punches?'); ?></em>
							<?php echo $this->Utility->__p(array(
								'You will need to talk to an event coordinator to fix these.',
								'Visit the event page to find your event coordinator.')); ?></p>

				<?php else : ?>
					<p class="append-top"><em><?php echo __('You have no volunteer activity in the specified time period.'); ?></em></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
