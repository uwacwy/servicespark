<div class="organizations index">
	<h2><?php echo __('Organizations'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('organization_id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($organizations as $organization): ?>
	<tr>
		<td><?php echo h($organization['Organization']['organization_id']); ?>&nbsp;</td>
		<td><?php echo h($organization['Organization']['created']); ?>&nbsp;</td>
		<td><?php echo h($organization['Organization']['modified']); ?>&nbsp;</td>
		<td><?php echo h($organization['Organization']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $organization['Organization']['organization_id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $organization['Organization']['organization_id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $organization['Organization']['organization_id']), null, __('Are you sure you want to delete # %s?', $organization['Organization']['organization_id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Organization'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Permissions'), array('controller' => 'permissions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Permission'), array('controller' => 'permissions', 'action' => 'add')); ?> </li>
	</ul>
</div>
