<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('CakeTime', 'Utility');

class HourlyShell extends AppShell
{
	public $uses = array('Event', 'User');

	public function main()
	{
		$this->email_config = "mandrill_api_test";
		
		if( Configure::read('debug') == 0)
			$this->email_config = "mandrill_api_prod";
			
		$this->out('Running hourly ServiceSpark jobs... ');
		
		$this->start_time 	= mktime( date("H"), 0,	0 );
		$this->stop_time 	= $this->start_time + (60 * 60);
		
		$this->date_fmt = 'Y-m-d H:i:s';
		
		$this
			->EmailVolunteersOneHourBefore()
			->EmailVolunteers24HoursAfterEvent()
			->EmailVolunteers24HoursBefore()
			->EmailCoordinators25HoursBefore()
			;
		
		$this->out('finished hourly ServiceSpark jobs...');
	}
	
	private function EmailVolunteersOneHourBefore()
	{
		$contain = array();
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', $this->start_time + (60*60) ),
			'Event.start_time <' => date('Y-m-d H:i:s', $this->stop_time + (60*60) )
		);
		
		$events = $this->Event->find('all', compact('conditions', 'contain') );
		
		foreach($events as $event)
		{
			$this->debug( $event['Event']['title'] );
			
			$recipients = $this->GetRecipients( $event['Event']['event_id'], array(
				'going' => true
			) );
			
			$global_merge_vars = $this->GlobalMergeVars();
			$global_merge_vars += $this->EventMergeVars($event['Event']);
			
			$recipient_merge_vars = Hash::combine($recipients, '{n}.User.email', '{n}.User');
			$to = Hash::extract($recipients, '{n}.User.email');
			
			$email = new CakeEmail( $this->email_config );
			$email
				->emailFormat( 'text' )
				->template( 'event_one_hour' )
				->to( $to )
				->subject("[*|solution_name|*] *|event_title|* starts soon")
				->addHeaders( $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars) )
				;
				
			if( $email->send() )
			{
				$this->out("- Sent one-hour reminders for " . $event['Event']['title']);
			}
			
		}
		
		return $this;
	}
	
	private function EmailVolunteers24HoursAfterEvent()
	{
		$contain = array(
			'EventTime' => 'Time'
		);
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', $this->start_time - (60*60*24) ),
			'Event.start_time <' => date('Y-m-d H:i:s', $this->stop_time - (60*60*24) )
		);
		
		$events = $this->Event->find('all', compact('conditions', 'contain') );
		
		foreach($events as $event)
		{
			$global_merge_vars = $this->GlobalMergeVars();
			$global_merge_vars += $this->EventMergeVars($event['Event']);
			
			$recipients = $this->GetRecipients( $event['Event']['event_id'], array(
				'attended' => true
			) );
			
			$to = Hash::extract($recipients, '{n}.User.email');
			$recipient_merge_vars = Hash::combine($recipients, '{n}.User.email', '{n}.User');

			// replace with functional approach
			// $recipient_merge_vars = Hash::map($recipients, '{n}', array($this, 'ReasonBlocks'));
			// $recipient_merge_vars = Hash::combine($recipient_merge_vars, '{n}.User.email', '{n}.User');
			foreach($recipients as $recipient)
			{
				$time_blocks = Hash::map($recipient, 'Time.{n}', array($this, 'TimeBlock') );
				$recipient_merge_vars[ $recipient['User']['email'] ]['user_time_block'] = implode("\n", $time_blocks);
			}			
			
			$email = new CakeEmail( $this->email_config );
			$email
				->emailFormat( 'text' )
				->template( 'event_volunteer_recap' )
				->to( $to )
				->subject("[*|solution_name|*] *|event_title|*: Your community service recap.")
				->addHeaders( $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars) )
				;
			
			if( $email->send() )
			{
				$this->out("- Sent event volunteer recaps for " . $event['Event']['title']);
			}

		}
		
		return $this;
	}
	
	private function EmailVolunteers24HoursBefore()
	{
		$contain = array(
			'Skill',
			'Organization'
		);
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', $this->start_time + (60*60*24) ),
			'Event.start_time <' => date('Y-m-d H:i:s', $this->stop_time + (60*60*24) )
		);
		
		$events = $this->Event->find('all', compact('conditions', 'contain') );
		
		foreach($events as $event)
		{
			// global
			$global_merge_vars = $this->GlobalMergeVars();
			$global_merge_vars += $this->EventMergeVars($event['Event']);
			$global_merge_vars['organization_name'] = $event['Organization']['name'];
			
			// per user
			$recipients = $this->GetRecipients($event['Event']['event_id'], array(
				'going' => true,
				'skills' => true
			));
			
			$to = Hash::extract($recipients, '{n}.User.email');

			$recipient_merge_vars = Hash::map($recipients, '{n}', array($this, 'ReasonBlocks'));
			$recipient_merge_vars = Hash::combine($recipient_merge_vars, '{n}.User.email', '{n}.User');
			
			$email = new CakeEmail( $this->email_config );
			$email
				->viewVars(array('event' => $event))
				->emailFormat( 'text' )
				->template( 'volunteer_event_24_hours' )
				->to( $to )
				->subject("[*|solution_name|*] *|event_title|* is tomorrow.")
				->addHeaders( $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars) )
				;
			
			if( $email->send() )
			{
				$this->out("- Sent event reminders for " . $event['Event']['title']);
			}
			
		}
		
		return $this;
	}
	
	private function EmailCoordinators25HoursBefore()
	{
		
		$contain = array(
			'Skill'
		);
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', $this->start_time + (60*60*25) ),
			'Event.start_time <' => date('Y-m-d H:i:s', $this->stop_time + (60*60*25) )
		);
		
		$events = $this->Event->find('all', compact('conditions', 'contain') );
		
		foreach($events as $event)
		{
			$global_merge_vars = $this->GlobalMergeVars();
			$global_merge_vars += $this->EventMergeVars($event['Event']);
			
			$recipients = $this->GetRecipients($event['Event']['event_id'], array(
				'coordinating' => true
			));
			
			$skills = Hash::extract($event, 'Skill.{n}.skill');
			
			$global_merge_vars['skills_summary'] = count($skills) > 0
				? __("The following skills are attached: %s", implode(", ", $skills) )
				: __("The event has no attached skills.");
				
			$to = Hash::extract($recipients, '{n}.User.email');
			$recipient_merge_vars = Hash::combine($recipients, '{n}.User.email', '{n}.User');

			$email = new CakeEmail( $this->email_config );
			$email
				->emailFormat( 'text' )
				->template( 'coordinator_event_25_hours' )
				->to( $to )
				->subject("[*|solution_name|*] *|event_title|* is tomorrow.  (*|event_rsvp_percent|*% RSVP, *|event_comment_count|* comments)")
				->addHeaders( $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars) )
				;
			
			if( $email->send() )
			{
				$this->out("- Sent event forecasts for " . $event['Event']['title']);
			}
		}
		
		return $this;
	}
	
	public function EventMergeVars($event)
	{
		$event['start_time'] = date('F j, Y g:i a', strtotime($event['start_time']));
		$event['stop_time'] = date('F j, Y g:i a', strtotime($event['stop_time']));
		$event['rsvp_percent'] = number_format($event['rsvp_percent'], 0);
		
		$result = $this->flatten( $event + array(
			'url' => Router::url( array('go' => true, 'controller' => 'events', 'action' => 'view', $event['event_id']), true ),
			'coordinator_url' => Router::url( array('coordinator' => true, 'controller' => 'events', 'action' => 'view', $event['event_id']), true ),
			'going_link' => Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $event['event_id']), true )
		), 'event_');
		
		return $result;
	}
	
	public function flatten($array, $prefix = "")
	{
		$result = array();
		foreach($array as $key => $value)
		{
			$result[ $prefix . $key ] = $value;
		}
		return $result;
	}
	
	public function GlobalMergeVars()
	{
		return array(
			'solution_name' => Configure::read("Solution.name"),
			'subscription_link' => Router::url( array('volunteer' => 'true', 'controller' => 'users', 'action' => 'profile'), true ),
			'activity_link' => Router::url( array('volunteer' => false, 'controller' => 'users', 'action' => 'activity'), true)
		);
	}
	
	public function TimeBlock($time)
	{
		$time_url = Router::url( array(
					'controller' => 'times', 
					'action' => 'view', 
					$time['time_id']
				),
				true
			);
		if($time['duration'])
			return __("* %s - %s: %s hours - %s",
				CakeTime::niceShort($time['start_time']),
				CakeTime::niceShort($time['stop_time']),
				number_format($time['duration'], 2),
				$time_url
			);
			
		return __("* %s: You clocked in, but you didn't clock out. - %s",
			CakeTime::niceShort($time['start_time']),
			$time_url
		);
	}
	
	public function ReasonBlocks($input)
	{
		$reasons = array();
		
		if( isset($input['Locus']['skills']) )
			$reasons[] = __("- Your profile includes the following skills: %s", implode(", ", $input['Skill']));
		
		if( isset($input['Locus']['going']) )
			$reasons[] = __("- Your RSVP for this event is 'going'");
		
		if( isset($input['Locus']['attended']) )
			$reasons[] = __("- You attended this event");
		
		if( isset($input['Locus']['coordinating']) )
			$reasons[] = __("- You are coordinating this event");
		
		if( isset($input['Locus']['discussing']) )
			$reasons[] = __("- You are discussing this event in the comment section.");
			
		$input['User']['why_this_email'] = implode("\n", $reasons);
		
		return $input;
	}
	
	
	private function ApplyMergeVars($string, $vars)
	{
		foreach($vars as $token => $value)
		{
			$string = str_replace("*|".$token."|*", $value, $string);
		}
		return $string;
	}
	
	private function CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars, $rcpt_key = "rcpt")
	{
		$result = array(
			'global_merge_vars' => array(),
			'merge_vars' => array()
		);

		foreach($global_merge_vars as $name => $content)
		{
			$result['global_merge_vars'][] = array(
				'name' => $name,
				'content' => $content
			);
		}

		//$this->out( print_r($recipient_merge_vars, false));

		foreach($recipient_merge_vars as $email => $merge_vars)
		{
			$mandrill_vars = array();
			foreach($merge_vars as $name => $content)
			{
				$mandrill_vars[] = array(
					'name' => $name,
					'content' => $content
				);
			}

			$result['merge_vars'][] = array(
				$rcpt_key => $email,
				'vars' => $mandrill_vars
			);

		}
		
		$result['preserve_recipients'] = false;

		return $result;

	}
	
	private function GetRecipients($event_id, $restrict = array() )
	{
		$default = array(
			'attended' => false,
			'going' => false,
			'coordinating' => false,
			'discussing' => false,
			'skills' => false
		);
		
		$loci = Hash::merge($default, $restrict);
		
		$contain = array();

		$not_going = $this->Event->Rsvp->find('list', array(
			'fields' => array('user_id'),
			'conditions' => array(
				'Rsvp.status' => 'not_going'
			)
		));
		
		if( $loci['attended'] )
			$contain['EventTime'] = array(
				'Time' => array(
					'User' => array(
						'conditions' => array(
							'User.email_attended' => true
						)
					)
				)
			);
		
		if( $loci['going'] )
			$contain['Rsvp'] = array(
				'conditions' => array(
					'Rsvp.status' => 'going'
				),
				'User' => array(
					'conditions' => array(
						'User.email_going' => true
					)
				)
			);
			
		if( $loci['coordinating'] )
			$contain['Organization'] = array(
				'Permission' => array(
					'conditions' => array(
						'Permission.write' => true
					),
					'User' => array(
						'conditions' => array(
							'User.email_coordinating' => true
						)
					)
				)
			);
			
		if( $loci['discussing'] )
			$contain['Comment'] = array(
				'User' => array(
					'conditions' => array(
						'User.email_discussing' => true
					)
				)
			);
			
		if( $loci['skills'] )
			$contain['Skill'] = array(
				'User' => array(
					'conditions' => array(
						'User.email_skills' => true
					)
				)
			);
		
		$conditions = array(
			'Event.event_id' => $event_id
		);
		$result = $this->Event->find('first', compact('conditions', 'contain'));
		
		
		/*
			This is a rudimentary extraction of users.  This includes duplicates.
		*/
		$recipients = array(
			'skills' => Hash::combine($result, 'Skill.{n}.skill', 'Skill.{n}.User'),
			'attended' => Hash::extract($result, 'EventTime.{n}.Time'),
			'going' => Hash::extract($result, 'Rsvp.{n}.User'),
			'discussing' => Hash::extract($result, 'Comment.{n}.User'),
			'coordinating' => Hash::extract($result, 'Organization.Permission.{n}.User')
		);
		
		/*
			The array needs to be flipped, so the primary dimension is User
			The secondary dimension is the locus of control
		*/
		$flipped_result = array();
		
		$reason_key = 'Locus';
		
		foreach( $recipients['skills'] as $skill => $users )
		{
			foreach($users as $user)
			{
				$user_id = $user['user_id'];
				
				$flipped_result[ $user_id ]['User'] = $user;
				$flipped_result[ $user_id ]['Skill'][] = $skill;
				$flipped_result[ $user_id ][$reason_key]['skills'] = true;
			}
		}
		
		foreach( $recipients['attended'] as $time )
		{
			if( !empty($time) )
			{
				$user_id = $time['User']['user_id'];
				$flipped_result[ $user_id ]['User'] = $time['User'];
				unset($time['User']);
				
				$flipped_result[ $user_id ]['Time'][] = $time;
				$flipped_result[ $user_id ][$reason_key]['attended'] = true;
			}
		}
		
		foreach( array('going', 'discussing', 'coordinating') as $locus )
		{
			if( is_array($recipients[$locus]) )
			foreach( $recipients[$locus] as $user )
			{
				if( !empty($user) )
				{
					$flipped_result[ $user['user_id'] ]['User'] = $user;
					$flipped_result[ $user['user_id'] ][$reason_key][$locus] = true;
				}

			}
		}
		
		return $flipped_result;
		
	}
	
	public function unsetIfEmpty($array)
	{
		if( !empty($array) )
			return $array;
	}
	
	private function debug($that)
	{
		$this->out( print_r($that, true) );
	}
	
}