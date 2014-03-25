<?php
App::uses('AppModel', 'Model');
/**
 * Event Model
 *
 * @property Skill $Skill
 */
class Event extends AppModel
{

/**
	General Model Behavior and Setup
*/
	public $primaryKey = 'event_id';
	public $displayField = 'title';
	public $actsAs = array('Containable');

	/*
		all fields required
		start time < stop time
		Time content validation is handled in the controller, similar to UsersController.
	*/
	public $validate = array(
		'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You must have an event title.'
			)
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You must add an event description.'
			)
		),
		'start_time' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You must enter a start time for an event.'
			)
		),
		'stop_time' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You must enter an end time for an event.'
			)
		)
	);


		/*
	 *
	 * Validates time data. Returns false if stop time <= start time.
	 * Used in the create and edit functions.
	 *
	*/
		//CURRENTLY NOT WORKING... Set to always return true
	public function validTimes() {
		/*if($this->request->data['Event']['stop_time'] <= $this->request->data['Event']['start_time']) {
				$this->Session->setFlash( __('The end time of the event must be after the start time.') );
				unset(
					$this->request->data['Event']['stop_time'], 
					$this->request->data['Event']['start_time']
				); // this will blank the fields

				return false;
		}
		else {
			return true;
		}*/
		return true;
	}

/**
	Associations
*/

	public $hasAndBelongsToMany = array(
		'Address' => array(
			'className' => 'Address',
			'joinTable' => 'addresses_events',
			'foreignKey' => 'event_id',
			'associationForeignKey' => 'address_id'
		),
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'events_skills',
			'foreignKey' => 'event_id',
			'associationForeignKey' => 'skill_id'
		)
	);

	public $hasMany = array(
		'Time' => array(
			'dependent' => true, // when an event is deleted, related time will be dleted
		)

	);

	public $belongsTo = array(
		'Organization' => array(
			'className' => 'Organization',
			'foreignKey' => 'organization_id'
		)
	);

}
