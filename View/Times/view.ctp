<?php 
/*
	view.ctp
	--
	the first of the unified views
*/
$organization = null;
if( !empty($time['OrganizationTime']) )
	$organization = $time['OrganizationTime'][0]['Organization'];
	
if( !empty($time['EventTime']) )
	$organization = $time['EventTime'][0]['Event']['Organization'];
	
$event = null;
if( !empty($time['EventTime']) )
	$event = $time['EventTime'][0]['Event'];

$permission_context['owner'] = ($time['Time']['user_id'] == AuthComponent::user('user_id') );

?>
<div class="row">
	<div class="col-md-3">
		<?php if( $event != null ) : ?>
			<h3><?php echo __('Explore This Event'); ?></h3>
			<div class="list-group">
				<?php echo $this->Html->link(
					__("View %s", $event['title']),
					array(
						'volunteer' => true,
						'controller' => 'events',
						'action' => 'view',
						$event['event_id']
					),
					array('class' => 'list-group-item')
				); ?>
			</div>
		<?php endif; ?>
		
		<?php if( $organization != null ) : ?>
			<h3><?php echo __("Explore This Organization"); ?></h3>
			<div class="list-group">
				<?php echo $this->Html->link(
					__('View %s', $organization['name']),
					array(
						'volunteer' => false,
						'controller' => 'organizations',
						'action' => 'view',
						$organization['organization_id']
					),
					array('class' => 'list-group-item')
				); ?>
				<?php echo $this->Html->link(
					__('Log Time', $organization['name']),
					array(
						'volunteer' => true,
						'controller' => 'times',
						'action' => 'in',
						'organization',
						$organization['organization_id']
					),
					array('class' => 'list-group-item')
				); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="col-md-9">
		<h2><?php echo __('Time Detail'); ?></h2>
		<div class="actionable">
			<div class="situation">
				<?php
					$stats = array(
						"Start Time &ndash; Stop Time" => $this->Duration->format($time['Time']['start_time'], $time['Time']['stop_time']),
						"Duration" => __('%s hours', number_format($time['Time']['duration'], 2) ),
						"Logged" => $this->Tm->timeAgoInWords($time['Time']['created']),
						
					);
					
					if( !empty($time['OrganizationTime']) )
					{
						switch( $time['Time']['status'] )
						{
							case 'approved':
								$stats['Status'] =  $this->Html->tag('span', __("Approved"), array('class' => 'text-success') );
								break;
							case 'pending':
								$stats['Status'] =  $this->Html->tag('span', __("Pending"), array('class' => 'text-warning') );
								break;
							case 'rejected':
								$stats['Status'] =  $this->Html->tag('span', __("Rejected"), array('class' => 'text-danger') );
								break;
							case 'deleted':
								$stats['Status'] = $this->Html->tag('span', __("Deleted"), array('class' => 'text-danger') );
								break;
						}
					}
				?>
					<h4><?php echo h($time['User']['full_name']); ?></h4>
					<div class="stat-bar">
						<?php foreach( $stats as $key => $value): ?>
							<div class="stat">
								<span class="key"><?php echo __($key); ?></span>
								<span class="value"><?php echo $value; ?></span>
							</div>
						<?php endforeach; ?>
					</div>
			
				<?php if( !empty($time['OrganizationTime']) ) : ?>
				
					<blockquote>
					<?php if( !empty($time['OrganizationTime'][0]['memo']) ) : ?>
						<?php echo h($time['OrganizationTime'][0]['memo']); ?>
					<?php else: ?>
						<em class="text-muted"><?php echo __('no memo'); ?></em>
					<?php endif; ?>
					</blockquote>
					
					
				<?php elseif( !empty($event) ) : ?>

					
					<?php echo $this->Element('event_card', array('event' => array(
						'Event' => $event,
						'Organization' => $organization) )); ?>
					
				<?php endif; ?>
				
			</div>
			<div class="actions">
				<ul>
				
				<?php if( $permission_context['owner'] ): ?>
					
					<li>
						<?php echo $this->Html->link(
							__("Delete This Time Entry"),
							array('controller' => 'times', 'action' => 'delete', $time['Time']['time_id']),
							array('class' => 'text-danger confirm')
						); ?>
					</li>
					
				<?php endif; ?>
				
				<?php if( !empty($organization) ) : ?>
				
					<?php if( $permission_context['owner'] ): ?>
						<li><?php echo $this->Html->link(
								__("Edit"),
								array('controller' => 'times', 'action' => 'edit', $time['Time']['time_id'])
							); ?></li>
					<?php endif; ?>
				
					<li><?php echo $this->Html->link(
						__("View %s", $organization['name']),
						array('controller' => 'organizations', 'action' => 'view', $organization['organization_id'])
						); ?></li>
					
					<?php if( !isset($event) && $permission_context['coordinator'] ): ?>	
					<?php
						$options = array(
							'rejected' => array(
								'label' => "Reject",
								'action' => 'reject',
								'class' => 'danger'
							),
							'approved' => array(
								'label' => "Approve",
								'action' => 'approve',
								'class' => 'success'
							)
						);
						
						unset( $options[ $time['Time']['status'] ] );
						
						foreach( $options as $option ): ?>
						<li><?php echo $this->Html->link(
								$option['label'],
								array('coordinator' => true, 'controller' => 'times', 'action' => 'status', $option['action'], $time['Time']['time_id'] ),
								array('class' => 'text-'.$option['class'])
							); ?></li>
						<?php endforeach; ?>
					<?php endif; ?>
						
				<?php endif; ?>
				
				
				</ul>
			</div>
		</div>
		
		<?php if( $permission_context['owner'] || $permission_context['coordinator'] ) : ?>
		
		<h4><?php echo __("Comments"); ?></h4>
				
				<?php if( !empty( $time['TimeComment']) ): ?>
					<ol class="comments">
					<?php foreach( $time['TimeComment'] as $time_comment ): ?>
						<li class="comment" id="time-comment-<?php echo $time_comment['time_comment_id']; ?>">
							<div class="comment-meta">
							<?php echo $this->Html->tag('img', null, array(
									'src' => Router::url( array(
										'controller' => 'users',
										'action' => 'avatar',
										$time_comment['User']['username']
										) ),
									'alt' => __("Avatar for ", $time_comment['User']['full_name']),
									'class' => 'user-gravatar user-avatar'
									)); ?>
								<div class="comment-author">
									<?php echo h($time_comment['User']['full_name']); ?>
									<span class="at-username"><?php echo sprintf("@%s", $time_comment['User']['username']); ?></span>
								</div>
								<div class="comment-date">
									<?php echo $this->Tm->timeAgoInWords( $time_comment['created'] ); ?>
								</div>
							</div>
								
							<div class="comment-body"><?php echo h($time_comment['body']); ?></div>
						</li>
					<?php endforeach; ?>
					</ol>
				<?php else: ?>
					<p><em><?php echo __("There are no comments on this time entry."); ?></em></p>
				<?php endif; ?>
		
		<?php 
					$time_comment_form = $form_defaults;
					$time_comment_form['url'] = array('volunteer' => false, 'controller' => 'times', 'action' => 'comment', $time['Time']['time_id']);
				echo $this->Form->create('TimeComment', $time_comment_form); ?>
				
				<?php echo $this->Form->input('time_id', array('value' => $time['Time']['time_id'], 'type' => 'hidden') ); ?>
				<?php echo $this->Form->input('body', array(
					'rows' => 1, 
					'class' => 'reply-body form-control', 
					'placeholder' => __("leave a comment..."),
					'label' => __("Leave a Comment")
					) ); ?>
				
				<?php echo $this->Form->end( array('label' => __("Submit Comment"), 'class' => 'btn btn-primary') ); ?>
				<hr class="space">
				<p><?php echo __("Only %s and  %s coordinators can see these comments.", $time['User']['full_name'], $organization['name']); ?></p>
				
		<?php endif; ?>
	</div>
</div>