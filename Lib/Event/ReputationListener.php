<?php

App::uses('CakeEventListener', 'Event');

App::uses('Event', 'Model');
App::uses('Rsvp', 'Model');
App::uses('User', 'Model');

class ReputationListener implements CakeEventListener
{
	private $clock_in_points = 100;
	private $clock_in_rsvp_bonus = 75;
	
	function __construct()
	{
		
	}
	
	function implementedEvents()
	{
		return array(
			'App.EventTime.afterSave.created' => 'user_clock_in'
		);
	}
	
	public function user_clock_in($cake_event)
	{
		$User = new User();
		$EventTime = $cake_event->subject();
		
		$event_id = $EventTime->data['EventTime']['event_id'];
		
		$contain = array();
		
		$clock_in_value = array(
			'event' => 'user_clock_in',
			'event_id' => $event_id,
			'points' => $this->clock_in_points
		);
		
		$conditions = array(
			'user_id' => AuthComponent::user('user_id'),
			'value' => json_encode($clock_in_value)
		);
		
		$existing_clock_in_counts = $User->UserMeta->find('count', compact('conditions', 'contain') );
		$user_rsvp_counts = $User->Rsvp->find('count', array(
			'conditions' => array(
				'Rsvp.user_id' => AuthComponent::user('user_id'),
				'Rsvp.event_id' => $event_id,
				'Rsvp.status' => 'going'
			),
			'contain' => array()
		) );
		
		CakeLog::write('info', __("Existing clock in reputation awards: %u", $existing_clock_in_counts) );
		CakeLog::write('info', __("User RSVP's at event: %u", $user_rsvp_counts) );
		
		if(  $existing_clock_in_counts == 0 )
		{
			CakeLog::write('info', 'Awarding reputation points for first clock in at event.');
			$User->add_meta('reputation', $clock_in_value);
		}
			
		//return;
		
		if( $existing_clock_in_counts == 0 && $user_rsvp_counts == 1 )
		{
			CakeLog::write('info', 'Awarding reputation points for first clock in with RSVP at event');
			$User->add_meta('reputation', array(
				'event' => 'user_clock_in_rsvp_bonus',
				'event_id' => $event_id,
				'points' => $this->clock_in_rsvp_bonus
			));
		}
	}
}