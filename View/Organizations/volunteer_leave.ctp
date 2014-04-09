<?php
	/*
		leave.ctp
		--
		This form allows users to leave associated organizations.
	*/
?>

<div class="row">
	<div class="col-md-12">
		<h1>Are you no longer affiliated with an organization? </h1>
		<h1><small>Leave any organization you belong to. </small></h1>
		<hr>

		<h2> Here are your organizations</h2>
		<table class="table table-striped">
			<thead>
				<th>Organization</th>
				<th>Action</th>
			</thead>
        	<?php foreach ($data as $organization):?>
    			<tr>
					<td>
						
						<?php 
							echo h( $organization['Organization']['name'] ); 
						?>
					</td>
					<td>
<?php echo $this->Html->link('Leave this Organzation', array( $organization['Organization']['organization_id']), array('class' => 'btn btn-danger btn-sm'), 'Are you sure you want to leave this organization?'); ?>
					</td>
    			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>