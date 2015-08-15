<?php 

$organization = Hash::get($this->data, "OrganizationTime.0.Organization");

?>

<div class="row">
	<div class="col-md-3">
		&nbsp;
	</div>
	<div class="col-md-9">
		<h2><?php echo __("Editing My Time Entry"); ?></h2>
		<div class="actionable">
			<div class="situation">
				<?php echo $this->Form->create('Time', array('type' => 'post')); ?>
					<p>
						<?php echo __("You are editing time for <strong>%s</strong>.", $organization['name'] ); ?>
					</p>
					
					<p>
						<?php echo __("This time entry is currently %s.", $this->data['Time']['status']); ?>  
						<?php echo __("If you edit it, it will be marked as pending."); ?>
					</p>
				
					<?php echo $this->Form->input('start_time'); ?>
					<?php echo $this->Form->input('stop_time'); ?>
				
				<?php echo $this->Form->end( array('label' => 'Save Changes', 'class'=>'btn btn-primary') ); ?>
			</div>
			<div class="actions">
				<ul>
					<li><?php echo $this->Html->link(__("Cancel Edit"),
						array('volunteer' => false, 'action' => 'view', $this->data['Time']['time_id'])
					); ?></li>
					<li><?php echo $this->Html->link(
						__("Delete This Time Entry"),
						array('volunteer' => false, 'controller' => 'times', 'action' => 'delete', $this->data['Time']['time_id']),
						array('class' => 'text-danger confirm')
					); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>