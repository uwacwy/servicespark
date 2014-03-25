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
			'rule' => 'numeric',
			'required' => true,
        	'allowEmpty' => false,
        	'message' => 'Time object requires event id.'
		),
		'user_id' => array(
			'rule' => 'numeric',
			'required' => true,
        	'allowEmpty' => false,
        	'message' => 'Time object requires user id.'
		),
		'start_time' => array(
			'rule' => array('date_compare', 'lt', 'stop_time'),
			'required' => true,
        	'allowEmpty' => false,
        	'message' => 'Time object requires start time.'
		),
		'stop_time' => array(
			'rule' => array('date_compare', 'gt', 'start_time'),
			'required' => 'false',
			'allowEmpty' => true,
		)
	);

	public function date_compare($value, $mode, $against)
	{
		switch($mode)
		{
			case 'lt':
			case '<':
				if( !isset($this->data['Time'][$against]) || is_null($this->data['Time'][$against]) ) return true;
				if( strtotime($value) < strtotime($this->data['Time'][$against]) )
					return true;
				break;
			case 'lte':
			case '<=':
				if( !isset($this->data['Time'][$against]) || is_null($this->data['Time'][$against]) ) return true;
				if( strtotime($value) <= strtotime($this->data['Time'][$against]) )
					return true;
				break;
			case 'gt':
			case '>':
				if( is_null($value) ) return true;
				if( strtotime($value) > strtotime($this->data['Time'][$against]) )
					return true;
				break;
			case 'gte':
			case '>=':
				if( is_null($value) ) return true;
				if( strtotime($value) < strtotime($this->data['Time'][$against]) )
					return true;
				break;
			default:
				return false;
		}
		

	}

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
