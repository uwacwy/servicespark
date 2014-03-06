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
	*/
	public $validate = array();

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
