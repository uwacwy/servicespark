<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1>Are you looking to join an organization? </h1>
		<blockquote>Here are all the organizations you don't already belong to.</blockquote>

		<h2>Organizations</h2>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<th>Organization</th>
					<th>Join</th>
				</thead>
				<?php
	        		$i=0; 
	        		foreach ($organizations as $organization):
	        	?>
	    			<tr>
	    				<td>
	    					<?php echo $this->Form->input(
	    						"Organization.$i.organization_id",
	    						array('value' => $organization['Organization']['organization_id'], 'type' => 'hidden')
	    					); ?>
	    					<strong><?php echo h( $organization['Organization']['name'] ); ?></strong><br>
	    				</td>
						<td>
							<?php 
								echo $this->Form->input("Organization.$i.publish",
									array(
										'type' => 'checkbox',
										'class' => 'checkbox-inline',
										'label' => false
									)
								); 
							?>
						</td>
	    			</tr>
				<?php
					$i++;
				 	endforeach;
				?>
			</table>
		</div>
		<ul class="pagination collapse-top">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
		<?php echo $this->Form->end(array('label' => "Join These Organizations", 'class' => 'btn btn-lg btn-primary')); ?>
	</div>	
</div>