<div class="row">
	<div class="col-md-12">
		<h2>Stop Coordinating </h2>
		<blockquote>You can leave any organization as a coordinator.  You will no longer receive notifications or be able to coordinate events.</blockquote>

		<h3>You coordinate for the following organizations.</h3>
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
		<ul class="pagination collapse-top">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
	</div>
</div>