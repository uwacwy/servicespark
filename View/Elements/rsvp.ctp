<?php
//
//	rsvp.ctp
//	--
//	prints the RSVP button
//

$status = "no_response";
if( !empty($rsvp) )
	$status = $rsvp['Rsvp']['status'];

?>
<div class="btn-group" role="group" aria-label="<?php echo __("Change your RSVP"); ?>">
	<?php
	echo $this->Html->link("Going",
		array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', 'going', $event['Event']['event_id']),
		array('class' => 'btn btn-md btn-success ' . ($status == "going" ? "active" : "") )
	);?>
	<?php echo $this->Html->link("Maybe",
		array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', 'maybe', $event['Event']['event_id']),
		array('class' => 'btn btn-md btn-warning ' . ($status == "maybe" ? "active" : "") )
	); ?>
	<?php
	echo $this->Html->link(__( "Can't Go" ),
		array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', 'not_going', $event['Event']['event_id']),
		array('class' => 'btn btn-md btn-danger ' . ($status == "not_going" ? "active" : "") )
	);
	?>
</div>