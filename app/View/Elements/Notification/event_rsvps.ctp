<?php
echo $this->Html->link(
	__("%s volunteers are attending <strong>%s</strong>",
		number_format($data['Event']['rsvp_count']),
		$data['Event']['title']
	),
	array(
		'coordinator' => true,
		'controller' => 'events',
		'action' => 'view',
		$data['Event']['event_id']
	),
	array(
		'escape' => false,
		'class' => 'notification',
		'data-api' => Router::url( array(
			'api' => true, 
			'controller' => 'users', 
			'action' => 'clear', 
			$data['Notification']['id']) )
	)
); ?>