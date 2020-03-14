<div class="events view">

	<?php
	$startTime = new DateTime($event['Event']['start_time']);
	$stopTime = new DateTime($event['Event']['stop_time']);
	?>

	<div class="row">
		<div class="col-md-6">
			<?php 
				if( $neighbors['prev'] ): 
					echo "&laquo; ";
					echo $this->Html->link(
						$neighbors['prev']['Event']['title'],
						[
							'coordinator' => true,
							'controller' => 'events',
							'action' => 'view',
							$neighbors['prev']['Event']['event_id']
						]
					);
				else:
					echo $this->Html->tag(
						'em',
						__('This is the first event')
					);
				endif;
			?>
		</div>
		<div class="col-md-6 text-right">
			<?php 
				if( $neighbors['next'] ): 
					echo $this->Html->link(
						$neighbors['next']['Event']['title'],
						[
							'coordinator' => true,
							'controller' => 'events',
							'action' => 'view',
							$neighbors['next']['Event']['event_id']
						]
					);
					echo " &raquo;";
				else:
					echo $this->Html->tag(
						'em',
						__('This is the last event')
					);
				endif;
			?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<header>
				<div class="row">
					<div class="col-md-9"></div>
				</div>
				<h1>
					<small><?php echo $event['Organization']['name']; ?></small>
					<br>
					<?php echo h($event['Event']['title']); ?>
					<br>
					<small><?php echo $this->Duration->format($startTime->format(DateTime::W3C), $stopTime->format(DateTime::W3C)); ?></small>
				</h1>
				<div class="rsvp">
					<div class="append-bottom">
						<?php
						$rsvp_count = $event['Event']['rsvp_count'];
						echo $this->Element('rsvp', compact('rsvp_count', 'user_rsvp_status', 'event'));
						?>
					</div>

				</div>
			</header>
			<div>

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist" style="margin-bottom:20px;">
					<li role="presentation" class="active">
						<a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a>
					</li>

					<li role="presentation">
						<a href="#rsvp" aria-controls="rsvp" role="tab" data-toggle="tab">Attendees</a>
					</li>

					<li role="presentation">
						<a href="#time" aria-controls="time" role="tab" data-toggle="tab">Time Data</a>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="details">
						<div class="row">
						<div class="col-md-3 col-md-push-9">
								<div class="event__actions">
									<div class="list-group">
										<?php
										echo $this->Html->link(
											__('Edit Event'),
											array('coordinator' => true, 'controller' => 'events', 'action' => 'edit', $event['Event']['event_id']),
											array('class' => 'list-group-item')
										);
										echo $this->Html->link(
											__('Adjust Time Entries'),
											array('coordinator' => true, 'controller' => 'times', 'action' => 'adjust', $event['Event']['event_id']),
											array('class' => 'list-group-item')
										);
										?>
									</div>
									<p>
										<?php echo $this->Form->postlink(
											__('Delete Event'),
											array('coordinator' => true, 'action' => 'delete', $event['Event']['event_id']),
											array('class' => 'btn btn-block btn-danger'),
											__('Are you sure you want to delete %s?  This will delete all associated time entry data.', $event['Event']['title'])
										); ?>
									</p>
								</div>
								<hr>
								<div class="event__tokens">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6 ">
											<h4>Clock In</h4>
											<p><code><?= h($event['Event']['start_token']) ?></code></p>
											<p>
												<?php echo sprintf(
													'<img src="//chart.googleapis.com/chart?cht=qr&chs=100x100&chl=%s&chld=H|0">',
													urlencode($this->Html->Url(array('controller' => 'times', 'action' => 'in', 'volunteer' => true, $event['Event']['start_token']), true))
												); ?>
											</p>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-6 text-right">
											<h4>Clock Out</h4>
											<p><code><?= h($event['Event']['stop_token']) ?></code></p>
											<p>
												<?php echo sprintf(
													'<img src="//chart.googleapis.com/chart?cht=qr&chs=100x100&chl=%s&chld=H|0">',
													urlencode($this->Html->Url(array('controller' => 'times', 'action' => 'out', 'volunteer' => true, $event['Event']['stop_token']), true))
												); ?>
											</p>
										</div>
									</div>
								</div>
								<hr>
								<div class="event__skills">
									<h3>Skills</h3>
									<?php if (!empty($event['Skill'])) : ?>
										<div class="_table">
											<div class="_tbody">
												<?php foreach ($event['Skill'] as $skill) : ?>
													<div class="_tr">
														<div class="_td">
															<?php echo h($skill['skill']); ?>
														</div>
													</div>
												<?php endforeach; ?>
											</div>
										</div>
									<?php else : ?>
										<p><em>This event doesn't have any skills. Add skills to help skilled volunteers discover
												your event.</em></p>
									<?php endif; ?>
								</div>
								<hr class="visible-sm visible-xs">
							</div> <!-- /.col-md-3 -->
							<div class="col-md-9 col-md-pull-3">

								<div class="event__start-time">
									<div class="media">
											<div class="media-left media-middle">
												<i class="glyphicon glyphicon-time" style="font-size: 36px;"></i>
											</div>
											<div class="media-body">
												<h3 style="margin: 0;">
													<?php
													$start = $event['Event']['start_time'];
													$stop = $event['Event']['stop_time'];
													?>
													<?php if (
														$this->Time->isPast($start)
														&& $this->Time->isFuture($stop)
													) : ?>
														<?php echo __('Happening Now'); ?>
													<?php elseif ($this->Time->isPast($stop)) : ?>
														<?php if ($this->Time->isToday($stop)) : ?>
															<?php echo __('Today'); ?>
														<?php else : ?>
															<?php echo $this->Time->timeAgoInWords($stop); ?>
														<?php endif; ?>
													<?php elseif ($this->Time->isTomorrow($start)) : ?>
														<?php echo __('Tomorrow'); ?>
													<?php elseif ($this->Time->isToday($start)) : ?>
														<?php echo __('Today'); ?>
													<?php else : ?>
														<?php echo $startTime->format('F j, Y') ?>
													<?php endif; ?>
													<br>
													<small class="text-muted">
														<?php echo $startTime->format('g:i A'); ?>
														&ndash;
														<?php echo $stopTime->format('g:i A'); ?>
													</small>
												</h3>
											</div>
										</div>
								</div>
										<hr>
								<div class="event__who">
								<div class="media">
											<div class="media-left media-middle">
												<i class="glyphicon glyphicon-user" style="font-size: 36px;"></i>
											</div>
											<div class="media-body">
												<h3 style="margin: 0">
													<?php if ($this->Time->isFuture($stop)) : ?>
														<?php echo sprintf(
															__("%s are going"),
															$event['Event']['rsvp_count']
														) ?>
														<br>
														<small class="text-muted">
															<?php echo sprintf(
																__("%s needed. %s can't make it. %s are interested"),
																number_format($event['Event']['rsvp_desired'], 0),
																number_format($event['Event']['rsvp_not_going'], 0),
																number_format($event['Event']['rsvp_maybe'], 0)
															) ?>
														</small>

													<?php elseif ($this->Time->isPast($stop)) : ?>
														<?php echo sprintf(
															__("%s said they were going"),
															$event['Event']['rsvp_count']
														) ?>
														<br>
														<small class="text-muted">
															<?php echo sprintf(
																__("%s couldn't make it. %s were interested"),
																number_format($event['Event']['rsvp_not_going'], 0),
																number_format($event['Event']['rsvp_maybe'], 0)
															) ?>
														</small>
													<?php endif; ?>

												</h3>
											</div>
										</div>
								</div>
										
										

								<hr>

								<div class="event__description">
									<div class="media">
										<div class="media-left">
											<i class="glyphicon glyphicon-info-sign" style="font-size: 36px;"></i>
										</div>
										<div class="media-body">
											<h3 style="margin-top:5px;">Description</h3>
											<?php echo $this->Text->autoParagraph(
												$this->Text->autoLink($event['Event']['description'])
											); ?>
										</div>
									</div>

								</div>

								<hr>

								<div class="event__addresses">
									<div class="media">
										<div class="media-left">
											<i class="glyphicon glyphicon-map-marker" style="font-size: 36px;"></i>
										</div>
										<div class="media-body">
											<h3 style="margin-top: 5px;">Addresses</h3>
											<?php echo $this->Element('print_addresses', array('addresses' => $event['Address'])); ?>
										</div>
									</div>
								</div>

								<hr>

								<div class="event__comments">
									<div class="media">
										<div class="media-left">
											<div class="glyphicon glyphicon-comment" style="font-size:36px;"></div>
										</div>
										<div class="media-body">
											<h3 style="margin-top: 5px;"><?php echo __('Comments') ?></h3>
											<?php if (!empty($comments)) : ?>
												<?php echo $this->Comment->formatComments($comments, $event['Event']['event_id']); ?>
											<?php else : ?>
												<p><em><?= __("There are no event comments at this time."); ?></em></p>
											<?php endif; ?>

											<h4><?= __("Leave a comment") ?></h4>
											<?php echo $this->Comment->commentForm($event['Event']['event_id'], null, 'leave a comment...'); ?>

										</div>
									</div>
								</div>
							</div>
							
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="rsvp">
						<div class="progress">
							<?php
							$current = $event['Event']['rsvp_count'];
							$desired = $event['Event']['rsvp_desired'];
							$pct = $event['Event']['rsvp_percent'];
							?>
							<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $current; ?>" aria-valuemin="0" aria-valuemax="<?php echo max($current, $desired); ?>" style="width: <?php echo min($pct, 100); ?>%;">
								<?php echo number_format($pct, 0); ?>%
							</div>

						</div>
						<p>Currently <strong><?php echo number_format($current, 0); ?></strong> of
							<?php echo number_format($desired, 0); ?> volunteer goal.
							<?php
							if ($event['Event']['rsvp_not_going'] > 0) echo ($event['Event']['rsvp_not_going'] == 1) ?
								__("%u person is not going.", $event['Event']['rsvp_not_going']) :
								__("%s people are not going", $event['Event']['rsvp_not_going']); ?></p>
						<div class="rsvps">
							<?php if (!empty($event['Rsvp'])) : ?>
								<div class="_table">
									<div class="_thead">
										<div class="_tr">
											<div class="_th">
												<?= __("Name") ?>
											</div>
											<div class="_th">
												<?= __("Status") ?>
											</div>
										</div>
									</div>
									<div class="_tbody">
										<?php foreach ($event['Rsvp'] as $rsvp) :
											$enum_to_words = array(
												'going' => "Going",
												'maybe' => "Interested",
												'not_going' => "Not Going"
											);
											$enum_to_class = array(
												'going' => "_success",
												'maybe' => '_warning',
												'not_going' => '_danger'
											)
										?>

											<div class="_tr <?= $enum_to_class[$rsvp['status']] ?>">
												<div class="_td">
													<?= h($rsvp['User']['full_name']); ?>
													<span class="text-muted">
														@<?php echo sprintf(
																'<a href="#" class="append-username text-muted" title="Click to add the username to the comment field. Ctrl/Cmd+Click to quickly add many usernames.">%s</a>',
																$rsvp['User']['username']
															); ?>
													</span>
												</div>
												<div class="_td">
													<?= h($enum_to_words[$rsvp['status']]) ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php else : ?>
								<p>
									<?= __("There are no RSVP's for this event.") ?>
								</p>
							<?php endif; ?>
						</div><!-- /.rsvps -->
						</h3>
					</div>

					<div role="tabpanel" class="tab-pane" id="time">
						<h3>Volunteer Time Entries</h3>
						<?php
						echo $this->Utility->btn_link_icon(
							__('Download as Microsoft Excel'),
							array('coordinator' => true, 'controller' => 'events', 'action' => 'report', $event_id),
							'btn btn-success btn-sm',
							'glyphicon-download-alt'
						);
						?>
						<?php if (!empty($event['EventTime'])) : ?>
							<div class="">
								<div class="_table">
									<div class="_thead">
										<div class="_tr">
											<div class="_th"><?php echo __('Full Name') ?></div>
											<div class="_th"><?php echo __('Time In'); ?></div>
											<div class="_th"><?php echo __('Time Out'); ?></div>
											<div class="_th text-right"><?php echo __('Total'); ?></div>
											<div class="_th">
												<div class="sr-only">Actions</div>
											</div>
										</div>
									</div>
									<div class="_tbody">
									<?php
									$grand_total_time = 0;
									foreach ($event['EventTime'] as $time) :
										$clock_in = new DateTime($time['Time']['start_time']);
										$clock_out = $time['Time']['stop_time'] != null
											?new DateTime($time['Time']['stop_time'])
											: null;
									?>
										<div class="_tr <?php echo $clock_out === null ? "_warning" : ""; ?>">
											<div class="_td">
												<?php echo h($time['Time']['User']['full_name']); ?>
											</div>
											<div class="_td">
												<?php echo h($clock_in->format('F j, Y g:i a')); ?>
											</div>
											<div class="_td">
												<?php
													if ($time['Time']['stop_time'] != null) {
														echo h($clock_out->format('F j, Y g:i a'));
													} else {
														echo $this->Html->tag('em', __('missed punch'));
													}
												?>
											</div>
											<div class="_td text-right">
												<?php
													if ($time['Time']['stop_time'] != null) {
														echo number_format($time['Time']['duration'], 2) . "&nbsp;hr";
													} else {
														echo "&mdash;";
													}
													$grand_total_time += $time['Time']['duration'];
												?>
											</div>
											<div class="_td text-right">
												<?php
													echo $this->Html->link(
														__('Edit'),
														array('coordinator' => true, 'controller' => 'times', 'action' => 'edit', $time['Time']['time_id']),
														array('class' => '')
													);
												?>
											</div>
										</div>
										
									<?php
									endforeach;
									?>
									</div>
									<div class="_thead">
										<div class="_tr">
											<div class="_th"></div>
											<div class="_th"></div>
											<div class="_th"></div>
											<div class="_th text-right">
												<?php echo h(sprintf(__('%s hr'), number_format($event_total[0][0]['EventTotal'], 2))); ?>
											</div>
										
										</div>
									</div>
								</div>
								
							</div>

						<?php else : ?>
							<p><em>there is no time-punch data for this event.</em></p>
						<?php endif; ?>

						<?php if (!empty($organization_time)) : ?>
							<h3>Convertable Times</h3>
							<p>These times were submitted to <strong><?php echo h($event['Organization']['name']) ?></strong> and can be attached
								to this event.</p>
							<div class="_table">
								<div class="_tbody">
									<?php foreach ($organization_time as $idx => $ot) : ?>
										<div class="_tr">
											<div class="_td">
												<?php echo h($ot['Time']['User']['full_name']); ?>
											</div>
											<div class="_td">
												<?php echo $this->Duration->format(
													$ot['Time']['start_time'],
													$ot['Time']['stop_time']
												); ?>
											</div>
											<div class="_td">
												<?php echo $this->Html->link(__('Convert'), [
													'coordinator' => true,
													'controller' => 'times',
													'action' => 'convert',
													$ot['Time']['time_id'],
													$event_id,
													'?' => [
														'redirect' => $this->here
													]
												]) ?></div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>