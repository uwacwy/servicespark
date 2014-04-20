<div class="row">
	<div class="col-md-12">
		<h1>Do you want to stop supervising an organization? </h1>
		<h1><small>Leave any organization you supervise. </small></h1>
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
						<?php echo $this->Html->link('Stop Coordinating', 
							array( $organization['Organization']['organization_id']), 
							array(
								'class' => 'btn btn-danger btn-sm'), 
								'Are you sure you want to stop publishing to this organization?'
							); 
						?>
					</td>
    			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>