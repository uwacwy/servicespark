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

<<<<<<< HEAD

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'events_skills',
			'foreignKey' => 'event_id',
			'associationForeignKey' => 'skill_id',
=======
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
>>>>>>> Addresses
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

}
