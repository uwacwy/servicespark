<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><?php echo $this->Html->link( Configure::read('Solution.name'), '/' ); ?></li>
			<li><?php echo $this->Html->link( $event['Organization']['name'], array('controller' => 'organizations', 'action' => 'view', $event['Organization']['organization_id']) ); ?></li>
			<li><?php echo $this->Html->link( $event['Event']['title'], array('controller' => 'events', 'action' => 'view', $event['Event']['event_id']) ); ?></li>
			<li><?php echo sprintf( __('Editing times for %s'), $event['Event']['title'] ); ?></li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<?php echo $this->Element('coordinator_event_actions', array('event_id' => $event['Event']['event_id']) ); ?>
		<?php echo $this->Element('coordinator_time_actions', array('event_id' => $event['Event']['event_id']) ); ?>
	</div>	
	<div class="col-md-9">

		<h2>
			<small>Adjusting Time Entries</small><br>
			<?php echo h($event['Event']['title']); ?> <small><?php echo h( date('F j, Y g:i a', strtotime($event['Event']['start_time'])) ); ?> - <?php echo h( date('g:i a', strtotime($event['Event']['stop_time']) ) ); ?></small>
		</h2>

		<?php if( count($times) > 0 ) : ?>

			<?php
				$form_defaults['onSubmit'] = sprintf('return confirm("%s");', __('Are you sure you want to fix all time punches for this event?  This will adjust ALL time punches--not just the ones on this page.') );
				echo $this->Form->create('Time', $form_defaults); ?>

			<ul class="pagination bottom">
				<?php
					echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
					echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
					echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				?>
			</ul>

			<table class="table table-striped">
				<thead>
					<tr>
						<th><?php echo $this->Paginator->sort('User.last_name', "Last, First"); ?></th>
						<th><?php echo $this->Paginator->sort('Time.start_time', 'Time In'); ?></th>
						<th><?php echo $this->Paginator->sort('Time.stop_time', 'Time Out'); ?></th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($times as $time) : ?>
						<tr>
							<td><?php echo h( sprintf( __('%1$s, %2$s'), $time['User']['last_name'], $time['User']['first_name']) ); ?></td>
							<td>
								<?php echo h( date('F j, Y g:i a', strtotime($time['Time']['start_time']) ) ); ?>
							</td>
							<td>
								<?php
									if( $time['Time']['stop_time'] )
									{
										echo h( date('F j, Y g:i a', strtotime($time['Time']['stop_time']) ) );
									}
									else
									{
										echo sprintf('<em>%s</em>', __('missed punch') );
									}
								?>
							</td>
							<td>
								<?php echo $this->Html->link( __('Edit Entry'), array('action' => 'edit', $time['Time']['time_id']), array('class' => 'btn btn-xs btn-primary') ); ?>
							</td>

						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>

			<ul class="pagination">
				<?php
					echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
					echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
					echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				?>
			</ul>

			<?php echo $this->Form->end( array('label' => 'Fix Missed Punches', 'class' => 'btn btn-danger btn-lg') ); ?>

		<?php else : ?>
			<p><em>There are no recorded time entries for this event.</em></p>
		<?php endif; ?>


	</div>
</div>