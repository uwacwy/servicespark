<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1><small>Coordinator</small><br>Organizations </h1>
		<p>You can coordinate events for these organizations.</p>
		<?php if ( count($pag_organizations) > 1) : ?>
		<table class="table table-striped"> 
			<thead>  
          		<tr>  
            		<th><?php echo $this->Paginator->sort('Organization.name', 'Organization'); ?></th>
            		<th>Actions</th>
          		</tr>  
        	</thead>
        	<tbody>
        		<?php foreach ($pag_organizations as $organization): ?>
					<tr>
						<td><?php echo h($organization['Organization']['name']); ?>&nbsp;</td>
						<td>
							<?php echo $this->Html->link(__('View Your Events'),
								array(
									'coordinator' => false,
									'controller' => 'organizations',
									'action' => 'view',
									$organization['Organization']['organization_id']
								)
							); ?>
							<?php echo $this->Html->link(__('Edit Your Events'), array('action' => 'edit', $organization['Organization']['organization_id'])); ?>
							<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $organization['Organization']['organization_id']), null, __('Are you sure you want to delete # %s?', $organization['Organization']['organization_id'])); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<p><em>You are not a coordinator for any organizations.</em></p>
	<?php endif; ?>
	</div>
</div>