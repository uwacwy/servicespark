<div class="row">
	<div class="col-md-12">
		<h2><?php echo __("Notifications"); ?></h2>
		
		<?php if( !empty($notifications) ): ?>
			<p><?php echo $this->Html->link(
				__("Mark All As Read"),
				array('controller' => 'users', 'action' => 'clear'),
				array('class' => 'btn btn-primary')
			); ?></p>
			
			<div class="_table">
				<div class="_thead">
					<div class="_tr">
						<span class="_th" id="label_notification"><?php echo __("Notification"); ?></span>
						<span class="_th" id="label_status"><?php echo __("Status"); ?></span>
					</div>
				</div>
				<div class="_tbody">
				<?php foreach($notifications as $notification): ?>
					<?php $notification_class = $notification['Notification']['read'] ? '' : '_primary'; ?>
					<div class="_tr <?php echo $notification_class; ?> wrapper">
						<span class="_td" aria-labelledby="label_notification"><?php echo $this->Notification->display($notification); ?></span>
						<span class="_td" aria-labelledby="label_status"><?php echo $notification['Notification']['read'] ? __("Read") : __("Unread"); ?></span>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
			
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
			<p><em><?php echo __("You don't have any notifications at this time."); ?></em></p>
		<?php endif; ?>
	</div>
</div>