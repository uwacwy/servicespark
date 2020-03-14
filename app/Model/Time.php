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
	public $virtualFields = array(
		'duration' => 'TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time)/60'
	);

	public $validate = array(
		'start_time' => array(
			'start_time_lt_stop_time' => array(
				'rule' => array('date_compare', 'lt', 'stop_time'),
				'required' => false,
				'allowEmpty' => false,
				'message' => 'Start time must be less than stop time.'
			),
			'no_overlaps' => array(
				'rule' => array('user_time_unique'),
				'required' => false,
				'allowEmpty' => false,
				'message' => 'Start time overlaps with another of your time entries.'
			)
		),
		'stop_time' => array(
			'stop_time_gt_start_time' => array(
				'rule' => array('date_compare', 'gt', 'start_time'),
				'required' => false,
				'message' => 'Stop time must be greater than start time.',
				'allowEmpty' => true,
			),
			'no_overlaps' => array(
				'rule' => array('user_time_unique'),
				'required' => false,
				'allowEmpty' => true,
				'message' => 'Stop time overlaps with another of your time entries.'
			)
		)
	);
	
	public function user_time_unique($value)
	{
		$key = array_keys($value);
		$key = $key[0];
		$value = array_values($value);
		$value = $value[0];

		// Unable to validate based on stop time
		// if there is an overlap, the 'start_time' validator will fail
		if( $key === 'stop_time' && $value === null ) {
			return true;
		}
		
		$conditions = array(
			'Time.user_id' => $this->data['Time']['user_id'],
			'Time.stop_time IS NOT NULL',
			'Time.start_time <' => $value,
			'Time.stop_time >' => $value,
			'Time.status' => 'approved'
		);

		if( isset($this->id) ) {
			$conditions['Time.time_id <>'] = $this->id;
		}
		
		$overlaps = $this->find('all', compact('conditions') );

		return count($overlaps) === 0;
			
	}
	
	public $hasMany = array("EventTime", "OrganizationTime", "TimeComment");

/**
	Associations
*/
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'counterCache' => array(
				'missed_punches' => array('Time.stop_time' => null)
			)
		)
	);
}
