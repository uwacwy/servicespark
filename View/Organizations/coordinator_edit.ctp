<?php
	/*
		coordinator_edit.ctp
	*/
?>

<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1> Modify your organization.</h1>
		<hr>

		<h2> Organization Name </h2>
		<?php
			echo $this->Form->input('organization_id');
			echo $this->Form->input('name');
		?>
		<hr>

		<h2>Addresses</h2>
		<p>Modify any existing addresses or add a new one to the organization.</p>
		<div class="row">
			<div class="col-md-12">
				<p><a href="#" class="add-address btn btn-success" data-target="#UserAddresses"><i class="glyphicon glyphicon-plus"></i> Add An Address</a></p>
				<div id="UserAddresses">
					<?php
						if(isset($this->request->data['Address']))
						{
							for($i = 0; $i < count($this->request->data['Address']); $i++) 
							{
								echo $this->element('address', array('i' => $i));
							}
						}
						?>
				</div>
			</div>
		</div>
		<hr>

		<h2>Members</h2>
		<p>Modify member privileges for this organization.</p>
		<p>Are you looking to add a member to your organization?  They need to <em>join</em> your organization before you can manage their permissions.</p>
		<div class="table-responsive">
			<table class="table table-condensed"></h2>
				<thead>  
	          		<tr>  
	            		<th>Member</th>
	            		<th class="text-center">Volunteer</th>
	            		<th class="text-center">Supervisor</th>
	            		<th class="text-center">Coordinator</th> 
	          		</tr>  
	        	</thead>
	        	<?php
	        		$i=0; 
	        		foreach ($data as $organization):
	        	?>
	    			<tr>
	    				<td>
	    					<?php echo $this->Form->input(
	    						"Permission.$i.permission_id",
	    						array('value' => $organization['Permission']['permission_id'], 'type' => 'hidden')
	    					); ?>
	    					<strong><?php echo h( $organization['User']['full_name'] ); ?></strong><br>
	    					<?php echo h( sprintf( __('@%s'), $organization['User']['username'] ) ); ?>
	    				</td>
						<td class="text-center success">
							<?php 
								echo $this->Form->input("Permission.$i.publish",
									array(
										'checked' => $organization['Permission']['publish'],
										'type' => 'checkbox',
										'class' => 'checkbox-inline',
										'label' => false
									)
								); 
							?>
						</td>
						<td class="text-center warning">
							<?php
								echo $this->Form->input("Permission.$i.read", 
									array(
										'checked' => $organization['Permission']['read'],
										'type' => 'checkbox', 
										'class' => 'checkbox-inline',
										'label' => false
									)
								);
							?>
						</td>
						<td class="text-center danger">
							<?php
								echo $this->Form->input("Permission.$i.write", 
									array(
										'checked' => $organization['Permission']['write'],
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

		<div>
			<?php echo $this->Form->button('Update this organization',
									array(
											'class' => 'btn btn-primary',
											'type' => 'submit'
										)
								); 
			?>

			<?php
				echo $this->Html->link('Delete this Organization', 
									array(
											'coordinator' => true,
											'controller' => 'organizations',
											'action' => 'delete',
											$organization['Organization']['organization_id']
										), 
										array(
											'class' => 'btn btn-link'
										),
										'Are you sure you want to permanently delete this organization?  ALL permissions, events, and time entries will also be deleted.'
									); 
			?>
		</div>
		<?php echo $this->Form->end(null); ?> 
	</div>
</div>