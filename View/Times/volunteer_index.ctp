<?php
	// volunteer_index
	// disambiguates checkins
?>
<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Time', $form_defaults); ?>
		<h1>Time Punch <small>Clock in or out of an event.</small></h1>
		<p>Your volunteer coordinator may have given you a time token.  If you are unsure whether the token is for clocking in or clocking out, please ask your volunteer coordinator.</p>

		<?php echo $this->Form->input('Time.token', array('label' => 'Time Token') ); ?>
		<?php echo $this->Form->button('Clock In', array('class' => 'btn btn-success', 'name' => 'locus', 'value' => 'in')); ?>
		<?php echo $this->Form->button('Clock Out', array('class' => 'btn btn-primary', 'name' => 'locus', 'value' => 'out')); ?>

		<?php echo $this->Form->end(null); ?>
	</div>

</div>