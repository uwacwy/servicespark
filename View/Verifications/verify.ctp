<div class="row">
	<div class="col-md-12">
		<h2><?php echo __("Create a Password"); ?></h2>
		<p>
			<?php echo __("Your email address has been verified."); ?>
			<?php echo __("Please create your login password"); ?>
		</p>
		
		<div class="actionable">
			<div class="situation">
				<?php echo $this->Form->create('User'); ?>
					<?php echo $this->Form->input('User.password_l', array('label' => __("Password"), 'type' => 'password') ); ?>
					<?php echo $this->Form->input('User.password_r', array('label' => __("Password, again"), 'type' => 'password') ); ?>
				<?php echo $this->Form->end('Save Password and Continue'); ?>
			</div>
		</div>
		
	</div>
</div>