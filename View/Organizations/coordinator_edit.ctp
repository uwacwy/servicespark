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
		<p>Add or remove member privileges for this organization.</p>
		<table class="table table-striped"></h2>
			<thead>  
          		<tr>  
            		<th>Member Name</th>
            		<th>Volunteer</th>
            		<th>Coordinator</th>
            		<th>Supervisor</th> 
          		</tr>  
        	</thead>
        	<?php
        		$i=0; 
        		foreach ($data as $organization):
        	?>
    			<tr>
    				<td>
    					<?php echo $this->Form->input(
    						"Permission.$i.user_id",
    						array('value' => $organization['User']['user_id'], 'type' => 'hidden')
    					); ?>
    					<?php echo h( $organization['User']['full_name'] ); ?>
    				</td>
					<td>
						<?php 
							echo $this->Form->input("Permission.$i.publish",
								array(
									'checked' => $organization['Permission']['publish'],
									'type' => 'checkbox',
									'class' => 'checkbox'
								)
							); 
						?>
					</td>
					<td>
						<?php
							echo $this->Form->input("Permission.$i.write", 
								array(
									'checked' => $organization['Permission']['write'],
									'type' => 'checkbox', 
									'class' => 'checkbox'
								)
							);
						?>
					</td>
					<td>
						<?php
							echo $this->Form->input("Permission.$i.read", 
								array(
									'checked' => $organization['Permission']['read'],
									'type' => 'checkbox', 
									'class' => 'checkbox'
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

		<h2>Does Everything Look Good?</h2>
		<div>
			<?php echo $this->Form->button('Update this organization',
									array(
											'class' => 'btn btn-primary',
											'type' => 'submit'
										)
								); 
			?>

			<?php
				echo $this->Html->link('Delete this Organzation', 
									array(
											'coordinator' => true,
											'controller' => 'organizations',
											'action' => 'delete',
											$organization['Organization']['organization_id']
										), 
										array(
											'class' => 'btn btn-danger'
										),
										'Are you sure you want to permanently delete this organization?'
									); 
			?>
		</div>
		<?php echo $this->Form->end(null); ?> 
	</div>
</div>