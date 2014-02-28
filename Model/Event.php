<?php
App::uses('AppModel', 'Model');
/**
 * Event Model
 *
 * @property Skill $Skill
 */
class Event extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'event_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';


	/*public function beforeSave($options = array())
	{
		if( $this->Address->exists($event_id) )
		{
			$this->Address->delete($event_id);
		}
		return true;
	}*/

	public $hasAndBelongsToMany = array(
		'Address' => array(
			'className' => 'Address',
			'joinTable' => 'addresses_events',
			'foreignKey' => 'event_id',
			'associationForeignKey' => 'address_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'events_skills',
			'foreignKey' => 'event_id',
			'associationForeignKey' => 'skill_id',
			'unique' => 'keepExisting'
		)
	);

	public $hasMany = array(
		'Time' => array(
			'dependent' => true // when an event is deleted, related time will be dleted
			'foreign_key' => 'event_id',
			'associationForeignKey' => 'skill_id',
		)

	);

}
