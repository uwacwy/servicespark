<div class="row">
	<div class="col-md-12">
		<h1>Do you want to stop supervising an organization?</h1>
		<p class="text-muted lead">Leave any organization you supervise.</p>
		<hr>

		<h2>Organizations You Can Supervise</h2>
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
						<?php echo $this->Html->link('Stop Supervising', 
							array( $organization['Organization']['organization_id']), 
							array(
								'class' => 'btn btn-danger btn-sm'), 
								'Are you sure you want stop supervising this organization?'
							); 
						?>
					</td>
    			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>