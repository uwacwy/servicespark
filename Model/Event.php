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
		Custom validation function
	*/
	function validateData($field=array(), $compare_field=null) {
        foreach( $field as $key => $value ){ 
            $v1 = $value; 
            $v2 = $this->data[$this->name][ $compare_field ];                  
            if($v1 !== $v2) { 
                return FALSE; 
            } else { 
                continue; 
            } 
        } 
        return TRUE; 
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

}
