
<div class="address">
	<?php
		if(isset($this->request->data['Address'][$i]['address_id']))
		{	
			echo $this->Form->input('Address.$i.address_id');
		}
	?>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->Form->input("Address.$i.type", array('options' =>
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
				<?php echo $this->Form->input("Address.$i.address1"); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
				<?php echo $this->Form->input("Address.$i.address2"); ?>
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
<hr>
</div>
