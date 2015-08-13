
<?php

$help_text = array("You can make changes to this time entry.");

$can_edit = false;
if( $permission_context['coordinator'] )
{
	$can_edit = false;
}
	
if( $this->data['Time']['user_id'] == AuthComponent::user('user_id') )
if( !empty($this->data['OrganizationTime']) )
{
	
	$can_edit = true;
	$help_text[] = "If you make changes to this time entry, it must be approved by a coordinator before you can see it on your profile.";
}
?>

<?php if( $can_edit ) : ?>

	<?php echo $this->Form->create('Time', $form_defaults); ?>
	
		<?php echo $this->Form->input('start_time'); ?>
		<?php echo $this->Form->input('stop_time'); ?>
		
		
		<?php echo $this->Utility->__p( $help_text ); ?>
		
	<?php echo $this->Form->end( array('label' => "Save Changes") ); ?>

<?php else : ?>

	<?php $this->Utility->__p(array(
		"You cannot directly make changes to this time entry.  You can only approve or reject a volunteer's changes."
	)); ?>	

<?php endif; ?>