<?php
echo $this->Html->link(
	__("Your time entry from <strong>%s</strong> was rejected.", $this->Duration->format($data['Time']['start_time'], $data['Time']['stop_time']) ),
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