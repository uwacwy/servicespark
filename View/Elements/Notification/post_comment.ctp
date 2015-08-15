

<a href="<?php echo $this->Html->url( array(
	'go' => true, 
	'controller' => 'events', 
	'action' => 'view', 
	$data['Event']['event_id']
) ); ?>">
	<?php echo sprintf("@%s also commented on %s",
		$data['User']['username'],
		$data['Event']['title']	
	); ?>
</a>