<?php
$header_styles = array(
	'background-color' => '#f5f5f5',
	'border-bottom' => '1px solid #ddd',
	'padding-bottom' => '0',
	'padding-top' => '3em',
	'margin-top' => '-20px',
);

$container_styles = array(
	//'margin-bottom' => '-1px',
	'background-color' => 'rgba(255,255,255,0.8)'
);

$trianglify_class = " trianglify";
if( !empty($event['Address']) ):
	$address = Hash::extract($event, 'Address.0.one_line');
	$google_map = sprintf('size=640x640&scale=2&key=%s&center=%s&zoom=%s&style=element:labels|visibility:off&maptype=satellite',
		Configure::read('Google.maps.api_key'),
		urlencode($address[0]),
		15
	);
	$header_styles['background-image'] = sprintf("URL('https://maps.googleapis.com/maps/api/staticmap?%s')", $google_map);
	$header_styles['background-size'] = 'cover';
	$trianglify_class = "";
endif;
	
?>

<div class="stripe stripe-sm<?php echo $trianglify_class; ?>" style="<?php echo $this->Html->style($header_styles); ?>" data-seed="<?php echo md5( $this->here ); ?>">
	<div class="container" style="<?php echo $this->Html->style($container_styles); ?>">
		<div class="row">
			<div class="col-md-12">
				
				<h2><?php echo h($event['Event']['title']); ?></h2>
				<p class="text-muted"><?php echo $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']); ?></p>
				
				<div class="pull-right">
					<?php if( AuthComponent::user('user_id') ) : ?>
						<?php echo $this->Element('rsvp', array('Event' => $event['Event'], 'Rsvp' => $rsvp)); ?>
					<?php else : ?>
						<strong><?php echo __("Are you going?"); ?></strong>
						<?php echo $this->Html->link(
							__('Sign In'),
							array('volunteer' => false, 'controller' => 'users', 'action' => 'login'),
							array('class' => 'btn btn-success')
						); ?>
						<?php echo __('or'); ?> 
						<?php echo $this->Html->link(
							__('Create an Account'),
							array('volunteer' => false, 'controller' => 'users', 'action' => 'register'),
							array('class' => 'btn btn-primary')
						); ?>
						<?php echo __('to RSVP'); ?>
					<?php endif; ?>
				</div>
				
				<ul class="nav nav-tabs" role="tablist" style="border-bottom: none">
					<li role="presentation" class="active">
						<a href="#overview" aria-controls="home" role="tab" data-toggle="tab"><?php echo __("Overview"); ?></a>
					</li>
						
					<?php if( isset($comment) ) : ?>
						<li role="presentation" class="">
							<a href="#comments" aria-controls="comments" role="tab" data-toggle="tab"><?php echo __("Discussion"); ?></a>
						</li>
					<?php endif; ?>
						
					<?php if( isset($rsvp_going, $rsvp_maybe, $rsvp_not_going) ) : ?>
						<li role="presentation" class="">
							<a href="#rsvp" aria-controls="rsvp" role="tab" data-toggle="tab"><?php echo __("RSVP"); ?></a>
						</li>
					<?php endif; ?>
					
					<?php if ( isset($time) ) : ?>
						<li role="presentation" class="">
							<a href="#time" aria-controls="time" role="tab" data-toggle="tab"><?php echo __("Time"); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
			
		</div>
	</div>
</div>

<div class="stripe stripe-sm">
<div class="container">
	<div class="row">
		<div class="col-md-9">
		
			<div class="tab-content">
		
				<div id="overview" role="tabpanel" class="tab-pane active" >
					<?php
						$ts_start_time = CakeTime::fromString($event['Event']['start_time']);
						$ts_stop_time = CakeTime::fromString($event['Event']['stop_time']);
						$ts_now = CakeTime::fromString('now');
						
						if( $ts_now <= $ts_start_time )
						{
							$start_string = __("Starts in %s", CakeTime::timeAgoInWords($ts_start_time) );
							$attendee_string = __("%s %s attending",
								number_format($rsvp_going_count),
								$this->Utility->IsAre($rsvp_going_count)
							);
						}
						
						if( $ts_start_time < $ts_now )
						{
							$start_string = __("Started %s", CakeTime::timeAgoInWords($ts_start_time) );
							$attendee_string = __("%s %s volunteering. %s %s expected to attend.",
								number_format($time_count), 
								$this->Utility->IsAre($time_count),
								number_format($rsvp_going_count),
								$this->Utility->IsAre($rsvp_going_count)
							);
						}
						
						if( $ts_stop_time <= $ts_now )
						{
							$start_string = __("Ended %s", CakeTime::timeAgoInWords($ts_stop_time) );
							$attendee_string = __("%s attended.", number_format($time_count) );
						}
					?>
					<div class="actionable">
						<div class="situation">
							<p>
								<i class="glyphicon glyphicon-bullhorn"></i> 
								<?php echo __("Organized by %s", $this->Html->link(
									$event['Organization']['name'],
									array(
										'controller' => 'organizations',
										'action' => 'view',
										$event['Organization']['organization_id']
									)
								)); ?>
							</p>
							<p>
								<i class="glyphicon glyphicon-time"></i> 
								<?php echo h($start_string); ?>
							</p>
							<p>
								<i class="glyphicon glyphicon-user"></i> 
								<?php echo h($attendee_string); ?>
							</p>
						</div>
					</div>
					<div class="markdown"><?php echo $this->Utility->markdown($event['Event']['description']); ?></div>
				</div>
				
				<?php if( isset($comment) ) : ?>
				<div id="comments" role="tabpanel" class="tab-pane">
					<?php echo $this->Comment->formatComments($comment, $event['Event']['event_id']); ?>
					
					<h4><?php echo __("Leave a comment..."); ?></h4>
					<?php echo $this->Comment->commentForm($event['Event']['event_id'], null, 'leave a comment...'); ?>
				</div>
				<?php endif; ?>
				
				<?php if( isset($rsvp_going, $rsvp_maybe, $rsvp_not_going) ) : ?>
				<div id="rsvp" role="tabpanel" class="tab-pane">
					<h3><?php echo __("Going"); ?></h3>
					<?php if( !empty($rsvp_going) ): ?>
						<?php echo $this->Element('rsvp_list', array('rsvps' => $rsvp_going) ); ?>
					<?php else: ?>
						<p><em><?php echo __("Nobody plans on attending this event."); ?></em></p>
					<?php endif; ?>
					
					<h3><?php echo __("Maybe"); ?></h3>
					<?php if( !empty($rsvp_maybe) ): ?>
						<?php echo $this->Element('rsvp_list', array('rsvps' => $rsvp_maybe) ); ?>
					<?php else: ?>
						<p><em><?php echo __("Nobody has said they might go."); ?></em></p>
					<?php endif; ?>
					
					<h3><?php echo __("Not Going"); ?></h3>
					<?php if( !empty($rsvp_not_going) ): ?>
						<?php echo $this->Element('rsvp_list', array('rsvps' => $rsvp_not_going) ); ?>
					<?php else: ?>
						<p><em><?php echo __("Nobody has said they aren't going."); ?></em></p>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				
				<?php if( isset($time) ) :?>
				<div id="time" role="tablpanel" class="tab-pane">
				
					<h3><?php echo __("Time Tokens"); ?></h3>
					<div class="row">
						<div class="col-md-6 text-center">
								<?php echo sprintf(
									'<img src="//chart.googleapis.com/chart?cht=qr&chs=150x150&chl=%s&chld=H|0">',
									urlencode( $this->Html->Url(array('controller' => 'times', 'action' => 'in', 'volunteer' => true, $event['Event']['start_token']), true ) )
								); ?>
								<h4>In Token</h4>
								<code class="in-token"><?php echo h($event['Event']['start_token']); ?></code>
						</div>
						<div class="col-md-6 text-center">
								<?php echo sprintf(
									'<img src="//chart.googleapis.com/chart?cht=qr&chs=150x150&chl=%s&chld=H|0">',
									urlencode( $this->Html->Url(array('controller' => 'times', 'action' => 'out', 'volunteer' => true, $event['Event']['stop_token']), true ) )
								); ?>
								<h4>Out Token</h4>
								<code class="out-token"><?php echo h($event['Event']['stop_token']); ?></code>
						</div>
					</div>
				
					<h3><?php echo __("Time Entry"); ?></h3>
					<?php if( !empty($time) ): ?>
						<?php echo $this->Element('event_time', array('times' => $time) ); ?>
					<?php else: ?>
						<p><em><?php echo __("There has been no time recorded at this event"); ?></em></p>
					<?php endif; ?>
					
				</div>
				<?php endif; ?>
			
			</div>
			
		</div>
		<div class="col-md-3">
			<?php if( isset($permission_context) && $permission_context['coordinator'] ): ?>
				<div class="row">
					<div class="col-md-6">
						<h4><?php echo __("Clock-In"); ?></h4>
						<p><code><?php echo h($event['Event']['start_token']); ?></code></p>
					</div>
					<div class="col-md-6 text-right">
						<h4><?php echo __("Clock-Out"); ?></h4>
						<p><code><?php echo h($event['Event']['stop_token']); ?></code></p>
					</div>
				</div>
				<p><?php echo $this->Html->link(
					__("Edit Event"),
					array('coordinator' => true, 'controller' => 'events', 'action' => 'edit', $event['Event']['event_id']),
					array('class' => 'btn btn-block btn-success')
				); ?>
				<?php echo $this->Html->link(
					__("Cancel Event"),
					array('coordinator' => true, 'controller' => 'events', 'action' => 'delete', $event['Event']['event_id']),
					array('class' => 'btn btn-block btn-danger')
				); ?></p>
			<?php endif; ?>
			
			<h3><?php echo __("Share"); ?></h3>
			<?php $url = Router::url( array('controller' => 'events', 'action' => 'view', $event['Event']['event_id']), true); ?>
			<ul class="inline">
				<li>
					<?php echo $this->Html->image('facebook.png',
					array(
						'alt' => 'Share on Facebook',
						'url' => __('https://facebook.com/sharer.php?u=%s', urlencode( $url ) ),
						'width' => 29,
						'height' => 29
					)); ?>
				</li>
				<li>
					<?php
						$tweet = __('Check out "%s" hosted by %s on %s',
							$event['Event']['title'],
							$event['Organization']['name'],
							Configure::read('Solution.name')
						);
					echo $this->Html->image('twitter.png',
					array(
						'alt' => 'Share on Twitter',
						'target' => '_blank',
						'url' => __('https://twitter.com/share?text=%s&url=%s', urlencode($tweet), urlencode($url) ) ,
						'width' => 29,
						'height' => 29
					)); ?>
				</li>
			</ul>
			
			<div class="form-group">
			<label for="<?php echo sprintf("event-%s-share-link", $event['Event']['event_id']); ?>"><?php echo __("Share Link"); ?></label>
			<div class="input-group">
				<div class="input-group-addon"><i class="glyphicon glyphicon-share"></i></div>
				<input type="text" class="form-control" id="<?php echo sprintf("event-%s-share-link", $event['Event']['event_id']); ?>" value="<?php echo htmlentities($url); ?>" onfocus="this.select()" onmouseup="return false"></input>
			</div>
			</div>

			
			<h3><?php echo __("Skills"); ?></h3>
			<?php if( !empty($event['EventSkill']) ): ?>
				<div class="list-group">
					<?php foreach($event['EventSkill'] as $skill): ?>
					<?php echo $this->Html->link(
						$skill['Skill']['skill'],
						array('controller' => 'skills', 'action' => 'view', $skill['Skill']['skill_id']),
						array('class' => 'list-group-item')
					); ?>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<p class="text-danger"><?php echo __("There are no skills attached to this event."); ?></p>
			<?php endif; ?>
			
			<h3><?php echo __("Maps"); ?></h3>
			<?php if( !empty($event['Address']) ): ?>
				<?php echo $this->Element('maps', array('addresses' => $event['Address']) ); ?>
			<?php else: ?>
				<p><em><?php echo __("There are no addresses associated with this event"); ?></em></p>
			<?php endif; ?>
			
			
			
		</div>
	</div>
</div>
</div>