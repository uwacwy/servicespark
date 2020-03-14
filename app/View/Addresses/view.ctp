<div class="addresses view">
<h2><?php echo __('Address'); ?></h2>
	<dl>
		<dt><?php echo __('Address Id'); ?></dt>
		<dd>
			<?php echo h($address['Address']['address_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mailing Address'); ?></dt>
		<dd>
			<?php echo h($address['Address']['mailing_address']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mailing City'); ?></dt>
		<dd>
			<?php echo h($address['Address']['mailing_city']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mailing State'); ?></dt>
		<dd>
			<?php echo h($address['Address']['mailing_state']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mailing Zip'); ?></dt>
		<dd>
			<?php echo h($address['Address']['mailing_zip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Physical Address'); ?></dt>
		<dd>
			<?php echo h($address['Address']['physical_address']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Physical City'); ?></dt>
		<dd>
			<?php echo h($address['Address']['physical_city']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Physical State'); ?></dt>
		<dd>
			<?php echo h($address['Address']['physical_state']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Physical Zip'); ?></dt>
		<dd>
			<?php echo h($address['Address']['physical_zip']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Address'), array('action' => 'edit', $address['Address']['address_id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Address'), array('action' => 'delete', $address['Address']['address_id']), null, __('Are you sure you want to delete # %s?', $address['Address']['address_id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Addresses'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Address'), array('action' => 'add')); ?> </li>
	</ul>
</div>
