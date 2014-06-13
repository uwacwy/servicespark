<?php
//
//	rsvp.ctp
//	--
//	prints the RSVP button
//

if($user_attending) : ?>
	<p>
		<?php echo $this->Html->link(__("Cancel RSVP"),
			array('volunteer' => true, 'controller' => 'events', 'action' => 'cancel_rsvp', $event['Event']['event_id']),
			array('class' => 'btn btn-sm btn-danger')
		); ?>
				<?php
		if ( $rsvp_count == 0 )
			echo __('You\'ll be the first volunteer.');
		elseif( $rsvp_count == 1)
			echo __('You\'re the only one attending so far.');
		else
			echo __('You\'re attending with %s other volunteers.', number_format($rsvp_count - 1) );
		?>
	</p>
<?php else: ?>
	<p>
		<?php
		echo $this->Html->link(__("RSVP"),
			array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $event['Event']['event_id']),
			array('class' => 'btn btn-md btn-success')
		);
		?>
		<?php
		if ( $rsvp_count == 0 )
			echo __('You\'ll be the first volunteer.');
		elseif( $rsvp_count == 1)
			echo __('You\'ll join one other volunteer.');
		else
			echo __('You\'ll join %s other volunteers', number_format($rsvp_count) );
		?>
	</p>
<?php endif; ?>