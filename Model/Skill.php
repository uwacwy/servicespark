<?php
App::uses('AppModel', 'Model');
/**
 * Skill Model
 *
 * @property User $User
 * @property Event $Event
 */
class Skill extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'skill_id';
	public $displayField = 'skill';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
			'joinTable' => 'skills_users',
			'foreignKey' => 'skill_id',
			'associationForeignKey' => 'user_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Event' => array(
			'className' => 'Event',
			'joinTable' => 'events_skills',
			'foreignKey' => 'skill_id',
			'associationForeignKey' => 'event_id',
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
