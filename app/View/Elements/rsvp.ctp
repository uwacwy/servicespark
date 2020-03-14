<?php
/**
 * Prints the RSVP buttons.
 * 
 */



?>
<!--
	<?php print_r($user_rsvp_status); ?>
-->
<form class="radio-bar rsvp" 
	data-model="rsvp"
	data-url="<?php echo $this->Html->url( __('/api/events/%d/rsvps/me', $event['Event']['event_id']), true ); ?>"
	data-method="PATCH">
	<label class="radio-bar__label rsvp__label--going ">
		<input 
			type="radio" 
			name="status" 
			value="going" 
			id="<?php echo __('event_%d__going', $event['Event']['event_id']); ?>" 
			class="radio-bar__radio"
		 <?php echo isset($user_rsvp_status['Rsvp']) && $user_rsvp_status['Rsvp']['status'] == 'going' ? 'checked="checked"' : ''; ?> />
		Going
	</label>
    <label class="radio-bar__label rsvp__label--maybe ">
        <input
                type="radio"
                name="status"
                value="maybe"
                id="<?php echo __('event_%d__maybe', $event['Event']['event_id']); ?>"
                class="radio-bar__radio"
            <?php echo isset($user_rsvp_status['Rsvp']) && $user_rsvp_status['Rsvp']['status'] == 'maybe' ? 'checked="checked"' : ''; ?> />
        Interested
    </label>
	<label class="radio-bar__label rsvp__label--not-going">
		<input 
			type="radio" 
			name="status" 
			value="not_going" 
			id="<?php echo __('event_%d__going', $event['Event']['event_id']); ?>" 
			class="radio-bar__radio"
		 <?php echo isset($user_rsvp_status['Rsvp']) && $user_rsvp_status['Rsvp']['status'] == 'not_going' ? 'checked="checked"' : ''; ?> />
		Not Going
	</label>
</form>