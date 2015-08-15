<?php
App::uses('AppModel', 'Model');
/**
 * Time Model
 *
 * @property Event $Event
 * @property User $User
 */
class EventTime extends AppModel
{
	var $primaryKey = "event_time_id";
	var $useTable = "events_times";
	
	var $belongsTo = array('Event', 
		'Time' => array(
			'className' => 'Time'));
}