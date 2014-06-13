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

}

?>