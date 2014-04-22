<div class="actions supervisor">
	<div class="list-group">
		<?php
			if( isset($time['Event']['Organization']) )
			{
				echo $this->Html->link(
					sprintf(__('View Organization Summary', $time['Event']['Organization']['name'])),
					array('supervisor'=> 'true', 'controller' => 'organizations', 'action' => 'view', $time['Event']['Organization']['organization_id']),
					array('class' => 'list-group-item')
				);
			}
		?>
		<?php echo $this->Html->link(
			sprintf(__('View Event Summary'), $time['Event']['title']),
			array('supervisor' => 'true', 'controller' => 'events', 'action' => 'view', $time['Event']['title']),
			array('class' => 'list-group-item')
		); ?>

	</div>
</div>