<div class="organizations view">
<h2><?php echo __('Organization'); ?></h2>
	<dl>
		<dt><?php echo __('Organization Id'); ?></dt>
		<dd>
			<?php echo h($organization['Organization']['organization_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($organization['Organization']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($organization['Organization']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($organization['Organization']['name']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Organization'), array('action' => 'edit', $organization['Organization']['organization_id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Organization'), array('action' => 'delete', $organization['Organization']['organization_id']), null, __('Are you sure you want to delete # %s?', $organization['Organization']['organization_id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Organizations'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Organization'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Permissions'), array('controller' => 'permissions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Permission'), array('controller' => 'permissions', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Permissions'); ?></h3>
	<?php if (!empty($organization['Permission'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Permission Id'); ?></th>
		<th><?php echo __('Organization Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Publish'); ?></th>
		<th><?php echo __('Read'); ?></th>
		<th><?php echo __('Write'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($organization['Permission'] as $permission): ?>
		<tr>
			<td><?php echo $permission['permission_id']; ?></td>
			<td><?php echo $permission['organization_id']; ?></td>
			<td><?php echo $permission['user_id']; ?></td>
			<td><?php echo $permission['publish']; ?></td>
			<td><?php echo $permission['read']; ?></td>
			<td><?php echo $permission['write']; ?></td>
			<td><?php echo $permission['created']; ?></td>
			<td><?php echo $permission['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'permissions', 'action' => 'view', $permission['permission_id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'permissions', 'action' => 'edit', $permission['permission_id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'permissions', 'action' => 'delete', $permission['permission_id']), null, __('Are you sure you want to delete # %s?', $permission['permission_id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Permission'), array('controller' => 'permissions', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
