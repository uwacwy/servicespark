<div class="actions coordinator">
	<h3>Manage Time</h3>
	<div class="list-group">
		<?php echo $this->Html->link(__("Adjust Time Entries"), array('coordinator' => true, 'controller' => 'times', 'action' => 'adjust', $event_id), array('class' => 'list-group-item')); ?>
	</div>
</div>