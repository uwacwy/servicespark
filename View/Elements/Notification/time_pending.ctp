<?php
echo $this->Html->link(
	__("<strong>%s:</strong> %s hours submitted for approval.", $data['Organization']['name'], number_format($data['Time']['duration'],2) ),
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