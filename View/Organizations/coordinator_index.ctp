<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1> Organizations </h1>
		<p>These are the organizations you are affiliated with.</p>
		<hr>	
		<table class="table table-striped"> 
			<thead>  
          		<tr>  
            		<th> Name </th>  
          		</tr>  
        	</thead>
        		<?php foreach ($organizations as $organization): ?>
					<tr>
						<td><?php echo h($organization['Organization']['name']); ?>&nbsp;</td>
						<td>
							<?php echo $this->Html->link(__('View Your Events'), array('action' => 'view', $organization['Organization']['organization_id'])); ?>
							<?php echo $this->Html->link(__('Edit Your Events'), array('action' => 'edit', $organization['Organization']['organization_id'])); ?>
							<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $organization['Organization']['organization_id']), null, __('Are you sure you want to delete # %s?', $organization['Organization']['organization_id'])); ?>
						</td>
					</tr>
				<?php endforeach; ?>
		</table>
	</div>
</div>