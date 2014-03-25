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
        	<?php foreach ($data as $organization):?>
    			<tr>
    				<td><?php echo h($organization['User']['first_name'] . ' ' . $organization['User']['last_name']); ?></td>
					<td><input type="checkbox" class="large"/> </td>
					<td><input type="checkbox" class="large"/> </td>
					<td><input type="checkbox" class="large"/> </td>
    			</tr>
			<?php endforeach; ?>
		</table>

		<h2>Does Everything Look Good?</h2>
		<?php echo $this->Form->end(array('label' => "Update this organization", 'class' => 'btn btn-lg btn-primary')); ?>
	</div>
</div>