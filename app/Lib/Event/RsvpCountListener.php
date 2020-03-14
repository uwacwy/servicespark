<?php

App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('Event', 'Model');
App::uses('Rsvp', 'Model');
App::uses('User', 'Model');
App::uses('Comment', 'Model');

class RsvpCountListener implements CakeEventListener
{
	public function implementedEvents()
	{
		return array(
			'App.Rsvp.afterSave.created' => array(
                'callable' => 'rsvp_touched',
                'priority' => 1
            ),
			'App.Rsvp.afterSave.modified' => array(
                'callable' => 'rsvp_touched',
                'priority' => 1
            )
		);
	}
	
	public function rsvp_touched($cake_event)
	{
		
		$Rsvp = $cake_event->subject();
		
		$data = $cake_event->data['modelData'];
		
		$event_id = $data['Rsvp']['event_id'];
		
		// find the event
		$going = $Rsvp->find('count', array('conditions' => array(
			'Rsvp.status' => 'going',
			'Rsvp.event_id' => $event_id
			)));
			
		$Event = $Rsvp->Event;
		
		$Event->id = $event_id;
		$Event->saveField('rsvp_count', $going);
		
		
		//CakeLog::write('info', print_r($going, true) );
	}
}