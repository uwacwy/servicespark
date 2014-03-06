<?php
App::uses('AppModel', 'Model');
/**
 * Time Model
 *
 * @property Event $Event
 * @property User $User
 */
class Time extends AppModel
{

/**
	General Model Behavior and Setup
*/
	public $primaryKey = 'time_id';

	/*
		event_id required fk
		user_id required fk
		start_time required
		stop_time NULLABLE
		created
		modified
	*/
	public $validate = array(
		'event_id' => array(
			'rule' => 'event_id',
			'required' => true,
        	'allowEmpty' => false,
        	'message' => 'Time object requires event id.'
		),
		'user_id' => array(
			'rule' => 'user_id',
			'required' => true,
        	'allowEmpty' => false,
        	'message' => 'Time object requires user id.'
		),
		'start_time' => array(
			'rule' => 'start_time',
			'required' => true,
        	'allowEmpty' => false,
        	'message' => 'Time object requires start time.'
		),
		'stop_time' => array(
			'rule' => 'stop_time',
			'required' => 'false',
			'allowEmpty' => true,
		)
	);

/**
	Associations
*/
	public $belongsTo = array(
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'event_id'
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
}
