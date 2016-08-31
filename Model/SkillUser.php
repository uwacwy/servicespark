<?php
App::uses('AppModel', 'Model');
App::uses('Auth', 'Component');

class SkillUser extends AppModel
{
	var $useTable = "skills_users";
	var $primaryKey = "skill_user_id";
	
	var $belongsTo = array(
		'Skill' => array(
			'counterCache' => 'user_count'
		),
		'User'
	);
}