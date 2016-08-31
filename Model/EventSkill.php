<?php
App::uses('AppModel', 'Model');
App::uses('Auth', 'Component');

class EventSkill extends AppModel
{
	var $useTable = "events_skills";
	var $primaryKey = "event_skill_id";
	
	var $belongsTo = array(
		'Skill' => array(
			'counterCache' => 'event_count'
		),
		'Event'
	);
}