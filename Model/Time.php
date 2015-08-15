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
		
		$conditions = array(
			'Time.time_id !=' => $this->id,
			'Time.user_id' => AuthComponent::user('user_id'),
			'Time.stop_time IS NOT NULL',
			'Time.start_time <' => $value,
			'Time.status' => 'approved'
		);
		
		if( $key == "start_time" )
		{
			$conditions['Time.stop_time >'] = $value;
		}
		else
		{
			$conditions['Time.stop_time >='] = $value;
		}
		
		$overlaps = $this->find('all', compact('conditions') );
		
		//$this->validate[$key]['no_overlaps']['message'] = "changed procedurally";
		
		if(count($overlaps) > 0)
		{
			App::uses('HtmlHelper', 'View/Helper');
			$html = new HtmlHelper( new View() );
			$this->validator()->getField($key)->getRule('no_overlaps')->message = 
				sprintf("%s overlaps with %s",
					$key == 'start_time' ? 'Start Time' : 'Stop Time',
					$html->link('Time #' . $overlaps[0]['Time']['time_id'], array('volunteer' => false, 'controller' => 'times', 'action' => 'view', $overlaps[0]['Time']['time_id']) )
				);
			return false;
		}
		else
			return true;
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
