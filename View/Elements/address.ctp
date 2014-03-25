<div class="address">
	<?php
		// this adds compatibility for add screens as well as edit screens
		if( isset($this->request->data['Address'][$i]['address_id']) )
		{
			echo $this->Form->input("Address.$i.address_id");
		}
	?>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->Form->input("Address.$i.type", array(
				'label' => 'Address Type',
				'options' => 
					array(
						'both' => 'Physical and Mailing', 
						'mailing' => 'Mailing Address',
						'physical' => 'Physical Address'
					)
				)
			); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->Form->input("Address.$i.address1", array('label' => 'Address Line 1')); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->Form->input("Address.$i.address2", array('label' => 'Address Line 2')); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?php echo $this->Form->input("Address.$i.city"); ?>
		</div>
		<div class="col-md-3">
			<?php echo $this->Form->input("Address.$i.state"); ?>
		</div>
		<div class="col-md-3">
			<?php echo $this->Form->input("Address.$i.zip"); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2">
			<a href="#" class="btn btn-danger remove-address">Remove this address</a>
		</div>
	</div>
	<hr>
</div>