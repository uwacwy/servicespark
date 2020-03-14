<?php
	/*
		volunteer_create.ctp
		--
		Allows a user (volunteer) to create a new organization. User will be elevated to supervisor for this organization.
	*/
?>

<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1>Welcome!</h1>
		<h2>Let's create a new organization.</h2>
		<p>You will automatically be added as a supervisor for this organization.</p>

		<?php
			echo $this->Form->input('name', array('label' => "Organization Name"));
		?>
		<hr>

		<h2> Add Addresses</h2>
		<p>Add any addresses that are associated with this organization.</p>
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

		<?php echo $this->Form->end(array('label' => "Create This Organization", 'class' => 'btn btn-lg btn-primary')); ?>
	</div>
</div>