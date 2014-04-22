<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="<?php echo $this->Html->url('/'); ?>"><span class="glyphicon glyphicon-home"></span><span class="sr-only"><?php echo Configure::read('Solution.name'); ?></span></a></li>
			<li><strong><?php echo h( __('Supervisor') ); ?></strong></li>
			<li><?php echo h( __('Events you supervise') ); ?></li>
		</ol> 
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h2>
			<small><?php echo h( __('Supervise') ); ?></small><br>
			<?php echo __('Events'); ?>
		</h2>
			
		<?php if( !empty($events) ) : ?>
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped">
					<tr>
						<th><?php echo $this->Paginator->sort('title'); ?></th>
						<th><?php echo $this->Paginator->sort('organization'); ?></th> 
						<th><?php echo $this->Paginator->sort('start_time'); ?></th>
						<th><?php echo $this->Paginator->sort('stop_time'); ?></th>
						<th class="actions"><?php echo __('Actions'); ?></th>
					</tr>
					<?php foreach ($events as $event): ?>
					<tr>
						<td><?php echo h($event['Event']['title']); ?>&nbsp;</td>
						<td><?php echo h($event['Organization']['name']); ?>&nbsp;</td>

						<td> <?php $startTime = new DateTime($event['Event']['start_time']);
							echo $startTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

						<td> <?php $stopTime = new DateTime($event['Event']['stop_time']);
							echo $stopTime->format('F j, Y, g:i a'); ?>&nbsp;</td>

						<td class="actions">
							<?php echo $this->Html->link(__('View'), array('action' => 'view', $event['Event']['event_id'])); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<p>
				<?php
					echo $this->Paginator->counter(array(
					'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
					));
				?>	
			</p>
			<ul class="pagination bottom">
				<?php
					echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
					echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
					echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				?>
			</ul>
		<?php else: ?>
			<p><em>You are not supervising for any organizations.  Before you can supervise events, you need to be added to an organization as a supervisor.</em></p>
		<?php endif; ?>
	</div>
</div>
