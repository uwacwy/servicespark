<?php
echo $this->Html->link(
	__("<strong>%s</strong> commented on a time entry", $data['User']['full_name']),
	array(
		'volunteer' => false,
		'controller' => 'times',
		'action' => 'view',
		$data['Time']['time_id']
	),
	array(
		'escape' => false,
		'class' => 'notification',
		'data-api' => Router::url( array('api' => true, 'controller' => 'users', 'action' => 'clear', $data['Notification']['id']) )
	)
); ?>