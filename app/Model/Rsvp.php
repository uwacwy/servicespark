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
			'counterCache' => [
				'rsvp_count'		=> ['Rsvp.status' => 'going'],
				'rsvp_maybe'		=> ['Rsvp.status' => 'maybe'],
				'rsvp_not_going'	=> ['Rsvp.status' => 'not_going']
			]
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);

}

?>