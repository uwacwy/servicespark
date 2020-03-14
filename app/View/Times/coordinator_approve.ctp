<div class="row">
	<div class="col-md-9">
		<h2><?php echo __("Unapproved Time"); ?></h2>
		<p>
			<?php echo __('Volunteers have recorded the following time entries for your organization.'); ?>  
			<?php echo __('By approving these time punches, volunteers will have time counted on their profiles.'); ?>
		</p>
		<div class="cloth"
			data-api-source="<?php echo $this->Html->url( array('api' => true, 'controller' => 'times', 'action' => 'approve') ); ?>"
			data-api-empty="<?php echo __('There are no unapproved time entries for you to approve.'); ?>"
			data-api-mustache="<?php echo $this->Html->url( array('api' => true, 'controller' => 'times', 'action' => 'mustache') ); ?>">
		<?php if( !empty($times) ): ?>
			<?php foreach($times as $time): ?>
			<div class="actionable" id="time-<?php echo $time['Time']['time_id']; ?>">
			
				<div class="situation">
					<p>
					<?php echo sprintf("<strong>%s</strong> logged <strong>%02.1f hours</strong> for <strong>%s</strong>.",
						$time['Time']['User']['full_name'],
						$time['Time']['duration'],
						$time['Organization']['name']
					); ?>
					<br>
					<?php echo $this->Duration->format($time['Time']['start_time'], $time['Time']['stop_time']); ?>
					
					</p>
					<blockquote>
					<?php if( !empty($time['OrganizationTime']['memo']) ) : ?>
						<?php echo h($time['OrganizationTime']['memo']); ?>
					<?php else: ?>
						<em class="text-muted"><?php echo __('no memo'); ?></em>
					<?php endif; ?>
					</blockquote>
				</div>
				
				<div class="actions">
					<ul>
						<li><?php echo $this->Html->link(
								__("Approve"),
								array('action' => 'approve', $time['Time']['time_id'], true),
								array(
									'class' => 'api-trigger times-approve',
									'data-api' => Router::url( array('api' => true, 'controller' => 'times', 'action' => 'status',  $time['Time']['time_id'], 'approved', true)),
									'data-on-success' => 'collapse',
									'data-target' => sprintf('#time-%s', $time['Time']['time_id'])
								)
						); ?>
						</li>
						<li><?php echo $this->Html->link(
								__("Reject"),
								array('action' => 'reject', $time['Time']['time_id'], true),
								array(
									'class' => 'api-trigger-time-reject',
									'data-api' => Router::url( array('api' => true, 'controller' => 'times', 'action' => 'status',  $time['Time']['time_id'], 'rejected', true)),
									'data-on-success' => 'collapse',
									'data-target' => sprintf('#time-%s', $time['Time']['time_id']),
									'data-prompt' => __("Reject Reason")
								)
						); ?>
						</li>
					</ul>
				</div>

			</div>
			<?php endforeach; ?>
			<ul class="pagination">
				<?php
					echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
					echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
					echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				?>
			</ul>
		<?php else: ?>
			<em><?php echo __('There are no unapproved time entries for you to approve.'); ?></em>
		<?php endif; ?>
		</div>
	</div>
	<div class="col-md-3">&nbsp;</div>
</div>