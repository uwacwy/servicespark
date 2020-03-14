<?php
echo $this->Html->link(
	__("<strong>%s</strong> clocked in to <strong>%s</strong>.", $data['User']['full_name'], $data['Event']['title']),
	array(
		'coordinator' => false,
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