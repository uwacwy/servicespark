<?php
	/*
		index.ctp
		--
		This form allows users to publish their activity to organizations.
	*/
?>

<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1> Feel free to take a look at all the organizations with upcoming events! </h1>
		<hr>
		<div class="table-responsive">
			<table class="table table-striped"> 
				<thead>  
	          		<tr>  
	            		<th> Organization Name </th>  
	          		</tr>  
	        	</thead>
	        		<?php foreach ($organizations as $organization):?>
	        			<tr>
							<td><?php echo h($organization['Organization']['name']); ?></td>
							<td>
								<?php echo $this->Html->link(__('View Events'), array('action' => 'view', $organization['Organization']['organization_id'])); ?>
							</td>
	        			</tr>
					<?php endforeach; ?>
			</table>
		</div>
		<hr>
	</div>
</div>