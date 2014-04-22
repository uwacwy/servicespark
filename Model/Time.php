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

	/*
		event_id required fk
		user_id required fk
		start_time required
		stop_time NULLABLE
		created
		modified
	*/


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

	public function date_compare($value, $mode, $against)
	{
		$against_str = $this->data[$this->name][$against];
		$value_str = reset($value);

		
		if( $against == 'stop_time' && $against_str == null  )
		{
			return true;
		}

		if( $against == 'start_time' && $value_str == null)
		{
			return true;
		}		

		switch($mode)
		{
			case 'lt':
			case '<':
				//debug( sprintf('verifying %s < %s', $value_str, $against_str) );
				if( strtotime($value_str) < strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
			case 'lte':
			case '<=':
				//debug( sprintf('verifying %s <= %s', $value_str, $against_str) );
				if( strtotime($value_str) <= strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
			case 'gt':
			case '>':
				//debug( sprintf('verifying %s > %s', $value_str, $against_str) );
				if( strtotime($value_str) > strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
			case 'gte':
			case '>=':
				//debug( sprintf('verifying %s >= %s', $value_str, $against_str) );
				if( strtotime($value_str) < strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
		}

		//debug('returning false');
		
		return false;

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
