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
	public $actsAs = array('Containable');
	public $virtualFields = array(
		'duration' => 'TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time)/60'
	);

	public $validate = array(
		'start_time' => array(
			'rule' => array('date_compare', 'lt', 'stop_time'),
			'required' => false,
			'allowEmpty' => false,
			'message' => 'Start time must be less than stop time.'
		),
		'stop_time' => array(
			'rule' => array('date_compare', 'gt', 'start_time'),
			'required' => false,
			'message' => 'Stop time must be greater than start time.',
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
			'foreignKey' => 'user_id',
			'counterCache' => array(
				'missed_punches' => array('Time.stop_time' => null)
			)
		)
	);
}
