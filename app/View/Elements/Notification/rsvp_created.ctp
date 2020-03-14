<?php
/*
	rsvp_created
*/
?>
<?php

if($data['Rsvp']['status'] == 'going')
{
	$sprint = '<strong>%s</strong> is going to <strong>%s</strong>';
}

if($data['Rsvp']['status'] == 'not_going')
{
	$sprint = '<strong>%s</strong> is not going to <strong>%s</strong>';
}

echo $this->Html->link(
	__($sprint,
		$data['User']['full_name'],
		$data['Event']['title']),
	array(
		'coordinator' => true,
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