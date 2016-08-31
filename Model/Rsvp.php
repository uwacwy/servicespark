<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class Rsvp extends AppModel
{
	var $primaryKey = 'rsvp_id';

	public $belongsTo = array(
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'event_id',
			'counterCache' => true
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
	
	public function setForUser($status, $event_id, $user_id = null)
	{
		if( $user_id == null )
		{
			$user_id = AuthComponent::user('user_id');	
		}
		
		if( !in_array($status, array('going', 'maybe', 'not_going') ) )
			return false;
		
		// find existing rsvp
		$conditions = array(
			'Rsvp.event_id' => $event_id,
			'Rsvp.user_id' => $user_id
		);
		$contain = array('Event');
		$existing = $this->find('first', compact('conditions', 'contain') );
		
		if( empty($existing) )
		{
			$rsvp = array(
				'status' => $status,
				'event_id' => $event_id,
				'user_id' => $user_id
			);
			
			if( $this->save( $rsvp ) )
			{
				CakeLog::write('info', 'user ' . $user_id . ' is ' . $status . ' to event ' . $event_id );
				return true;
			}
		}
		else
		{
			$existing['Rsvp']['status'] = $status;
			if( $this->save($existing['Rsvp']) )
			//if( true )
			{
				CakeLog::write('info', 'updated rsvp: user ' . $user_id . ' is ' . $status . ' to event ' . $event_id );
				return true;
			}
		}
		
		return false;
		
		CakeLog::write('info', 'end Rsvp->setForUser()');
	}

}

?>