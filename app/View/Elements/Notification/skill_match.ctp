<?php
echo $this->Html->link(
	__("<strong>%s</strong> needs volunteers like you", $data['Event']['title']),
	array(
		'volunteer' => true,
		'controller' => 'events',
		'action' => 'view',
		$data['Event']['event_id']
	),
	array(
		'escape' => false,
		'class' => 'notification',
		'data-api' => Router::url( array('api' => true, 'controller' => 'users', 'action' => 'clear', $data['Notification']['id']) )
	)
); ?>