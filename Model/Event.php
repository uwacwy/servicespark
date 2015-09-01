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
	public $actsAs = array('Containable');
	public $virtualFields = array(
		'rsvp_percent' => '(Event.rsvp_count / Event.rsvp_desired)*100'
	);

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
		'rsvp_desired' => array(
			'rule' => 'naturalNumber',
			'message' => 'You must specify how many volunteers your event requires.',
			'required' => true
		),
		'start_time' => array(
			'lt_stop_time' => array(
				'rule' => array('date_compare', 'lt', 'stop_time'),
				'message' => 'Start Time must be before the Stop Time',
				'required' => true
			)
		),
		'stop_time' => array(
			'gt_start_time' => array(
				'rule' => array('date_compare', 'gt', 'start_time'),
				'message' => 'Stop Time must be after the Start Time',
				'required' => true
			)
		)
	);
	
	function dateToCal($timestamp)
	{
		return date('Ymd\THis\Z', $timestamp);
	}
	
	function escapeString($string)
	{
		return preg_replace('/([\,;])/','\\\$1', $string);
	}
	
	/*
		get_ics
		--
		returns a valid ics string, with time zones managed
		@param event_id identifies the 
	*/
	public function get_ics($event_id)
	{
		$conditions = array(
			'Event.event_id' => $event_id
		);
		$contain = array('Address', 'Organization');
		
		$event = $this->find('first', $event_id);
		
		// Event should be at $event['Event']
		
		$start_time_utc = CakeTime::convert( (new DateTime($event['Event']['start_time']))->getTimestamp(), 'UTC');
		$stop_time_utc = CakeTime::convert((new DateTime($event['Event']['stop_time']))->getTimestamp(), 'UTC');
		$now = CakeTime::convert( time(), 'UTC');
		$event_url = Router::url(array(
			'go' => true,
			'controller' => 'events',
			'action' => 'view',
			$event['Event']['event_id']
		), true);
		
		$address_one_liners = array( __("no addresses specified") );
		if( !empty($event['Address']) )
		{
			$address_one_liners = Hash::extract($event, 'Address.{n}.one_line');
		}
		
		$description = sprintf("%s\\n\\n%s", $event['Event']['description'], $event_url);
		
		$lines = array(
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//uwacwy/servicespark//EN',
			'CALSCALE:GREGORIAN',
			'BEGIN:VEVENT',
			sprintf('DTEND:%s', $this->dateToCal($stop_time_utc)),
			sprintf('UID:%s', hash('sha256', $event_url) ), // this can be used to automate cancellations
			sprintf('DTSTAMP:%s', $this->dateToCal($now) ),
			sprintf('LOCATION:%s', $this->escapeString( implode(' or ', $address_one_liners) ) ),
			sprintf('DESCRIPTION:%s', $this->escapeString($description) ), // event description
			sprintf('ORGANIZER;CN=%s\;MAILTO:%s', 
				$this->escapeString($event['Organization']['name']), 
				$this->escapeString(sprintf("organization-%s@reply.servicespark.org", $event['Organization']['organization_id']) ) ),
			sprintf('URL;VALUE=URI:%s', $this->escapeString( $event_url ) ),
			sprintf('SUMMARY:%s', $this->escapeString($event['Event']['title'])), // event title
			sprintf('DTSTART:%s', $this->dateToCal($start_time_utc)),
			'END:VEVENT',
			'END:VCALENDAR'
		);
		
		return implode("\n", $lines); // join the lines
	}


		/*
	 *
	 * Validates time data. Returns false if stop time <= start time.
	 * Used in the create and edit functions.
	 *
	*/
		//CURRENTLY NOT WORKING... Set to always return true
	public function validTimes() {
		/*if($this->request->data['Event']['stop_time'] <= $this->request->data['Event']['start_time']) {
				$this->Session->setFlash( __('The end time of the event must be after the start time.') );
				unset(
					$this->request->data['Event']['stop_time'], 
					$this->request->data['Event']['start_time']
				); // this will blank the fields

				return false;
		}
		else {
			return true;
		}*/
		return true;
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
		'EventTime' => array(
			'dependent' => true, // when an event is deleted, related time will be dleted
		),
		'Rsvp' => array(
			'dependent' => true,
		),
		'Comment' => array(
			'dependent' => true,
		)

	);

	public $belongsTo = array(
		'Organization' => array(
			'className' => 'Organization',
			'foreignKey' => 'organization_id'
		)
	);

}
