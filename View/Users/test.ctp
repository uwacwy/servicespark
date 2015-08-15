<?php $notifications = ClassRegistry::init('User')->getUnreadNotification( AuthComponent::user('user_id') ); ?>

<ul>
<?php foreach ($notifications as $notification): ?>
	<li><?php echo $this->Notification->display($notification); ?></li>
<?php endforeach ?>
</ul>
<?php echo $this->Html->link(
	__("Clear Notifications"),
	array('controller' => 'users', 'action' => 'clear')
); ?>