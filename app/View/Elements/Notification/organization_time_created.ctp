<?php echo $this->Html->link(
	__("<strong>%s</strong> logged %01.1f hours for <strong>%s</strong>.",
		$data['User']['full_name'],
		$data['Time']['duration'],
		$data['Organization']['name']),
	array('coordinator' => true, 'controller' => 'times', 'action' => 'approve'),
	array(
		'escape' => false,
		'class' => 'notification',
		'data-api' => Router::url( array('api' => true, 'controller' => 'users', 'action' => 'clear', $data['Notification']['id']) )
	)
); ?>