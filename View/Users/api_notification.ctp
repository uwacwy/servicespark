<?php $notifications = ClassRegistry::init('User')->getUnreadNotification(AuthComponent::user('user_id')); ?>
<li class="dropdown notification-dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <?php echo __('Notifications') ?>
        <?php if (!empty($notifications)): ?>
            <span class="badge unread-notifications">
                <?php echo count($notifications); ?>
            </span>
        <?php endif ?>
    </a>
    <ul class="dropdown-menu">
    <?php if( !empty($notifications) ) : ?>
        <?php foreach ($notifications as $notification): ?>
            <li><?php echo $this->Notification->display($notification); ?></li>
        <?php endforeach ?>
     <?php else: ?>
     	<li><?php echo $this->Html->link(
     		__("You have no unread notifications at this time"),
     		array('controller' => 'users', 'action' => 'notifications')
     		); ?></li>
     <?php endif; ?>
        <li class="divider"></li>
        <li class="text-center"><?= $this->Html->link(__('Display all'),
        	array('controller' => 'users', 'action' => 'notifications', 'volunteer' => false)); ?></li>
    </ul>
</li>