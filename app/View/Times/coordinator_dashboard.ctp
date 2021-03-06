<?php
	$status_class = array(
		'approved' => 'success',
		'pending' => 'warning',
		'rejected' => 'danger',
		'deleted' => 'muted'
	);
?>
<div class="row">
<div class="col-md-12">
	<?php //debug($new); ?>
</div></div>
<div class="row">
	<div class="col-md-12">
		<h2><?php echo __("Time Dashboard"); ?></h2>
		<p><?php echo $this->Utility->__p(array(
			array('The Time Dashboard shows all time logged on %s for your organizations.', Configure::read('Solution.name') )
		)); ?></p>
		<?php if( !empty($times) ): ?>
		
			<div class="_table">

				
				<div class="_thead">
					<div class="_tr">
						<span class="_th"><?php echo __("Volunteer"); ?></span>
						<span class="_th"><?php echo __("Start Time - Stop Time"); ?></span>
						<span class="_th"><?php echo __("Duration"); ?></span>
						<span class="_th"><?php echo __("Comments"); ?></span>
						<span class="_th"><?php echo __("Status"); ?></span>
					</div>
				</div>
				<div class="_tbody">
	
				<?php foreach($times as $time) : ?>
					<a 
						class="_tr _<?php echo $status_class[$time['Time']['status']]; ?>"
						href="<?php echo $this->Html->url([
							'coordinator' => false,
							'controller' => 'times', 
							'action' => 'view', 
							$time['Time']['time_id']
						]); ?>">
						<span class="_td">
							<strong><?php 
								echo h(
									sprintf(
										"%s %s",
										$time['User']['first_name'],
										$time['User']['last_name']
									)
								);
							?></strong>
							<br>
							<?php echo h( $time['Organization']['name'] ); ?>
							
						</span>
						<span class="_td">
							<?php if( empty( $time['OrganizationTime']['memo'] ) ): ?>
								<em class="text-muted"><?php echo __('no memo'); ?></em>
							<?php else: ?>
								<em><?php echo h($time['OrganizationTime']['memo']); ?></em>
							<?php endif; ?>
							<br>
							<?php echo $this->Duration->format(
								$time['Time']['start_time'], 
								$time['Time']['stop_time']
							); ?>
						</span>
						<span class="_td">
							<?php echo __("%.2f hours", $time[0]['duration']); ?>
						</span>
						<span class="_td">
							<i class="glyphicon glyphicon-comment"></i>
							<?php
								$comment_count = $time[0]['comment_count'];
								$phrase = $comment_count == 1
									? "%s comment"
									: "%s comments";									
							echo __($phrase, number_format($comment_count) ) ?>
						</span>
						<span class="_td text-<?php echo $status_class[$time['Time']['status']]; ?>">
							<span class="sr-only">Currently </span><?php echo h(ucfirst($time['Time']['status'])); ?>
						</span>
					</a>
				<?php endforeach; ?>
				</div><!-- ._tbody -->
			</div><!-- ._table -->
			<p>
			<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<ul class="pagination bottom">
		<?php
			echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
			echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		?>
	</ul>
		<?php else: ?>
		<p><em><?php echo __("There is no time submitted to your organizations"); ?></em></p>
		<?php endif; ?>
	</div>
</div>