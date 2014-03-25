<?php
	/*
		leave.ctp
		--
		This form allows users to leave associated organizations.
	*/
?>

<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1>Are you no longer affiliated with an organization? </h1>
		<h1><small>Leave any organization you belong to. </small></h1>
		<hr>

		<h2>Organizations</h2>
		<table class="table table-striped">
			<thead>  
          		<tr>  
            		<th> Organization </th>  
          		</tr>  
        	</thead>
        	<?php foreach ($data as $organization):?>
    			<tr>
					<td>
						<input type="checkbox" class="large"/> 
						<?php echo h($organization['Organization']['name']); ?>
					</td>
    			</tr>
			<?php endforeach; ?>
		</table>

		<?php echo $this->Form->end(array('label' => "Leave These Organizations", 'class' => 'btn btn-lg btn-primary')); ?>
	</div>
</div>