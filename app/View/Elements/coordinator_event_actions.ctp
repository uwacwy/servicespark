<div class="coordinator time">
	<h3>Manage Event</h3>
	<div class="list-group">
		<?php echo $this->Html->link(__("Edit Event"), array('coordinator' => true, 'controller' => 'events', 'action' => 'edit', $event_id), array('class' => 'list-group-item')); ?>
		<?php echo $this->Html->link( __("View Event"), array('coordinator' => true, 'controller' => 'events', 'action' => 'view', $event_id), array('class' => 'list-group-item')); ?>
	</div>
</div>