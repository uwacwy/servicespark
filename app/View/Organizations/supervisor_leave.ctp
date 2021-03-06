<div class="row">
	<div class="col-md-12">
		<h1>Do you want to stop creating and editing events for your organizations? </h1>
		<h1><small>Leave any organization you supervise. </small></h1>
		<hr>

		<h2> Here are your organizations</h2>
		<div class="table-responsive">
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
		<ul class="pagination collapse-top">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
	</div>
</div>