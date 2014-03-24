<p><?php
	echo $this->Html->link('Back to Event',
		array('volunteer' => false, 'controller' => 'event', 'action' => 'view', $event['Event']['event_id'] ),
		array('class' => 'btn btn-block btn-success')
	);
?></p>