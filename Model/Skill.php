<?php
App::uses('AppModel', 'Model');
/**
 * Skill Model
 *
 * @property User $User
 * @property Event $Event
 */
class Skill extends AppModel
{

/**
	General Model Behavior and Setup
*/

	public $primaryKey = 'skill_id';

	public $displayField = 'skill';

	/*
		skill not empty, not null
	*/
	public $validate = array();

/**
	Associations
*/
	public $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
			'joinTable' => 'skills_users',
			'foreignKey' => 'skill_id',
			'associationForeignKey' => 'user_id'
		),
		'Event' => array(
			'className' => 'Event',
			'joinTable' => 'events_skills',
			'foreignKey' => 'skill_id',
			'associationForeignKey' => 'event_id'
		)
	);

}
