Hello, *|full_name|* 

A new service opportunity has been created that you might be interested in!

<?php echo $event['Event']['title']; ?> by <?php echo $event['Organization']['name']; ?> 
<?php echo $event['Event']['description']; ?> 
<?php echo $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']); ?> 

<?php if( !empty($event['Address']) )
{
	foreach ($event['Address'] as $address)
	{
		if($address['type'] != 'mailing')
		{
			echo $address['address1']."\n";
			echo !empty($address['address2'])? $address['address2']."\n" : '';
			echo sprintf('%s, %s %s', $address['city'], $address['state'], $address['zip']);
			echo "\n"; 
		}
	}
}
?>
RSVP to this event by clicking
<<?php echo $this->Html->url( array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $event['Event']['event_id']), true); ?>>

You can learn more about this event at
<<?php echo $this->Html->url( array('volunteer' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']), true ); ?>> 

If you have any questions about this event, please contact an event coordinator:
<?php
	if( !empty($event['Organization']['Permission']) )
	{
		foreach($event['Organization']['Permission'] as $coordinator)
		{
			echo sprintf("%s <%s>\n", $coordinator['User']['full_name'], $coordinator['User']['email'] );
		}
	}
?>