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
	public $validate = array(
		'skill' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You must enter a skill.'
				),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This skill already exists.'
			)
		)
	);

/**
	Associations
*/
	var $hasMany = array('EventSkill', 'SkillUser');

}
