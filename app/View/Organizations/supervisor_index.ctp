<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1><small>Supervisor</small><br>Organizations </h1>
		<p>You can supervise users for these organizations.</p>
		<?php if ( count($pag_organizations) > 0) : ?>
		<div class="table-responsive">
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
								<?php echo $this->Html->link(__('Dashboard'),
									array(
										'supervisor' => true,
										'controller' => 'organizations',
										'action' => 'dashboard',
										$organization['Organization']['organization_id']
									),
									array('class' => 'btn btn-xs btn-success')
								); ?>
								<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $organization['Organization']['organization_id']), array('class' => 'btn btn-xs btn-primary')); ?>
								<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $organization['Organization']['organization_id']), array('class' => 'btn btn-xs btn-danger'), __('Are you sure you want to delete # %s?', $organization['Organization']['organization_id'])); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<ul class="pagination collapse-top">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
	<?php else: ?>
		<p><em>You are not a supervisor for any organizations.</em></p>
	<?php endif; ?>
	</div>
</div>