<?php
echo $this->Html->link(
	__("<strong>%s:</strong> %s clocked out.", $data['Event']['title'], $data['User']['full_name'] ),
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