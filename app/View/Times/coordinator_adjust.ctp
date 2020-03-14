<div class="row">
	
	<div class="col-md-12">

		<h2>
			<small>Adjusting Time Entries</small><br>
			<?php echo h($event['Event']['title']); ?> <small><?php echo h( date('F j, Y g:i a', strtotime($event['Event']['start_time'])) ); ?> - <?php echo h( date('g:i a', strtotime($event['Event']['stop_time']) ) ); ?></small>
		</h2>
		<p>The memo column is not saved, but can be used for comparing to paper logs.</p>

		<?php if( count($grouped) > 0 ) : ?>

			<div>
			<?php echo $sortColumn; ?>
			<?php echo $this->Form->create('Time', ['class' => 'form-inline']); ?>

				<?php

					$time_by_user_id = [];

					foreach( $grouped as $time) {
						$user_id = $time['User']['user_id'];
						if( isset($time_by_user_id[$user_id]) ) {
							$time_by_user_id[$user_id][] = $time;
						} else {
							$time_by_user_id[$user_id] = [$time];
						}
					}

					$time_row_idx = 0;

					//debug($time_by_user_id);
					$last_user_id = 0;
				?>
				<div class="_table">
				<div class="_thead">
					<div class="_tr">
					<?php $headers = [
						'User' => [
							'User.first_name' => __('First Name'),
							'User.last_name' => __('Last Name'),
							'User.username' => __('Username')
						],
						'Start Time' => [
							'Time.start_time' => __('Start Time')
						],
						'Stop Time' => [
							'Time.stop_time' => __('Stop Time')
						],
						'Clear Stop Time?' => [],
						'Memo' => []
					];
					function getOpposite($dir) {
						return $dir == 'asc'
							? 'desc'
							: 'asc';
					}
					?>

					<?php foreach($headers as $label => $sorts): ?>
						<div class="_th">
							
							<?php if( count($sorts) > 1 ) : ?>
								<?php echo h($label); ?> (<?php
									$columnLinks = [];
									foreach( $sorts as $column => $columnLabel ){
										$classes = ['sort'];
										if( $sortColumn == $column ) {
											$classes[] = 'sort--active';
											$classes[] = 'sort--'.$sortDirection;
										}
										$columnLinks[] = $this->Html->link($columnLabel, [
											'controller' => 'times',
											'action' => 'adjust',
											$event['Event']['event_id'],
											'?' => [
												'sort' => $column,
												'direction' => $sortColumn == $column
													? getOpposite($sortDirection)
													: 'asc'
											]
											], [
												'class' => implode(' ', $classes)
											]);
									}
									echo implode(', ', $columnLinks);
									?>)
							<?php elseif( count($sorts) == 1 ): ?>
								<?php foreach($sorts as $column => $columnLabel): ?>
									<?php $classes = ['sort'];
										if( $sortColumn == $column ) {
											$classes[] = 'sort--active';
											$classes[] = 'sort--'.$sortDirection;
										}
										
										echo $this->Html->link($columnLabel, [
										'controller' => 'times',
										'action' => 'adjust',
										$event['Event']['event_id'],
										'?' => [
											'sort' => $column,
											'direction' => $sortColumn == $column
												? getOpposite($column)
												: 'asc'
										]
										], ['class' => implode(' ', $classes)]); ?>
								<?php endforeach; ?>
							<?php else: ?>
								<?php echo h($label); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
							
					</div>
				</div>
				<div class="_tbody">
				<?php foreach($time_by_user_id as $user_id => $times): ?>
					<?php foreach($times as $user_row_idx => $time): 
						$hiddenClass = $user_row_idx > 0 ? 'text-very-quiet' : '';	
					?>
					<div class="_tr">
						<div class="_td">
							<span class="<?= $hiddenClass ?>">
								<strong><?php echo h($times[0]['User']['full_name']); ?></strong>
								<span class="<?php echo $user_row_idx > 0 ? '' : 'text-muted' ?>">@<?php echo h($times[0]['User']['username']) ?></span>
							</span>
						</div>
						<div class="_td">
							<?php echo $this->Form->input("Time.$time_row_idx.time_id", [
								'value' => $time['Time']['time_id']
							]); ?>
							<?php echo $this->Form->input("Time.$time_row_idx.start_time", [
								'selected' => strtotime($time['Time']['start_time']),
								'type' => 'datetime',
								'separator' => '',
								'label' => false,
								'class' => 'quiet-select',
								'maxYear' => date('Y')
							]); ?>
						</div>
						<div class="_td">
							<?php echo $this->Form->input("Time.$time_row_idx.stop_time", [
								'selected' => $time['Time']['stop_time'] == null
									? time()
									: strtotime($time['Time']['stop_time']),
								'type' => 'datetime',
								'separator' => '',
								'label' => false,
								'disabled' => $time['Time']['stop_time'] == null,
								'class' => 'time__stop_time quiet-select',
								'maxYear' => date('Y'),
								'interval' => $time['Time']['stop_time'] === null ? 5 : 1
							]); ?>
						</div>
						<div class="_td text-center">
						<?php echo $this->Form->checkbox("Time.$time_row_idx.blank", [
										'checked' => $time['Time']['stop_time'] == null,
										'label' => false,
										'class' => 'time__blank'
									]) ?>
						</div>
						<div class="_td text-center">
							<input type="checkbox" name="" id="">
						</div>
					</div>
					<?php $time_row_idx++ ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
				</div>
				
				</div>


				<h3><?php echo __('Time Adjust') ?></h3>
				
				<?php echo $this->Form->radio('Adjust.mode', [
					'save' => __('Save changes in table.'),
					'event__stop_time' => __('Set blank values to event end.'),
					'now' => __('Set blank values to now.')
				], [
					'legend' => false
				]) ?>
				<?php echo $this->Form->button('Save Adjustments', array(
					'class' => 'btn btn-primary btn-lg',
					'name' => 'data[Adjust][action]',
					'value' => 'fix'
				)); ?>
			<?php echo $this->Form->end(); ?>

			</div>

		<?php else : ?>
			<p><em>There are no recorded time entries for this event.</em></p>
		<?php endif; ?>


	</div>
</div>