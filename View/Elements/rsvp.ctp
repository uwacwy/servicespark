<?php
//
//	rsvp.ctp
//	--
//	prints the RSVP button
//

// pick verbiage and study

$going = array(
	"RSVP",
	"I'm going!",
	"I'll be there!",
	"I'll go!",
	"Going"
);
?>
<p>
	<?php
	echo $this->Html->link("I'm Going",
		array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $event['Event']['event_id']),
		array('class' => 'btn btn-md btn-success ' . ($user_attending ? "active" : "") )
	);?>
	<?php
	echo $this->Html->link(__( "I'm Not Going" ),
		array('volunteer' => true, 'controller' => 'events', 'action' => 'cancel_rsvp', $event['Event']['event_id']),
		array('class' => 'btn btn-md btn-danger ' . ($user_attending ? "" : "active") )
	);
	?>
</p>