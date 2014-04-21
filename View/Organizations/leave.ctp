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
		<p class="text-muted">Leave any organization you belong to.</p>

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
						<?php echo $this->Html->link('Leave this Organzation', 
							array( $organization['Organization']['organization_id']), 
							array('class' => 'btn btn-danger btn-sm'), 'Are you sure you want to leave this organization?'); 
						?>
					</td>
    			</tr>
			<?php endforeach; ?>
		</table>
		
		<ul class="pagination bottom">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
	</div>
</div>