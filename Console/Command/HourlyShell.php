<?php

App::uses('CakeEmail', 'Network/Email');



class HourlyShell extends AppShell
{

	public $uses = array('Event');

	public function startup()
	{

	}

	public function main()
	{
		$this->out("Beginning hourly jobs");
		$this->EmailVolunteers1HourBefore();
		$this->ensureConnected();
		$this->EmailVolunteers24HoursAfterEvent();
		$this->ensureConnected();
		$this->EmailVolunteers24HoursBefore();
		$this->ensureConnected();
		$this->EmailCoordinators25HoursBefore();
		$this->out("...done");
	}

	public function ensureConnected()
	{
		if( !$this->Event->getDatasource()->isConnected() )
		{
			$this->out("Reconnecting to Database");
			$this->Event->getDatasource()->reconnect();
		}
	}

	public $template_time_block_missed =
<<<MESSAGE
You clocked in at *|time_in|*.
You did not clock out.
MESSAGE;

	public $template_time_block_complete = 
<<<MESSAGE
You clocked in at *|time_in|*.
You clocked out at *|time_out|*.
Total hours: *|total_hours|*
MESSAGE;

	public $template_event_recap =
<<<MESSAGE
Hello *|full_name|*,

Thank you for your recent community service for *|event_name|*!

*|time_block|*

If you have any issues with event time, please contact an event coordinator.

*|event_coordinator_block|*

You can always track your volunteer time using *|solution_name|* at the following link:
<*|profile_link|*>

Thank you for using *|solution_name|*.

----
You are receiving this email because you tracked time using *|solution_name|*.
MESSAGE;

		public $template_footer =
<<<FOOTER

Thank you for using *|solution_name|*.
Too many emails?  You can manage your subscriptions at *|subscription_link|*
FOOTER;

	public $template_volunteer_event_is_tomorrow =
<<<MESSAGE
Hello, *|full_name|*!

The event "*|event_title|*" is tomorrow at *|event_start_time|* until *|event_stop_time|*.

*|next_steps_block|*

To view more information about this event, and communicate with other volunteers, visit the event page:
<*|event_url|*>

*|why_this_email|*
-----
*|footer|*
MESSAGE;

	public $template_volunteer_event_is_tomorrow_next_steps_attending =
<<<MESSAGE
You have already RSVP'd to this event, and we look forward 
to seeing you there!  If you need to cancel your RSVP, or 
if you need to share something with the other volunteers, 
please visit the event page.
MESSAGE;

	public $template_volunteer_event_is_tomorrow_next_steps_not_attending =
<<<MESSAGE
You haven't RSVP'd to this event, and it still needs volunteers.  If you can help, please RSVP.
If you have questions or concerns about the event, leave a comment on the event page.

Click this link to RSVP now:
<*|event_rsvp_link|*>
MESSAGE;

	public $template_volunteer_event_in_1_hour =
<<<MESSAGE
Hello, *|full_name|*!

Don't forget! The event "*|event_title|*" starts shortly at *|event_start_time|* until *|event_stop_time|*.

To view more information about this event, and communicate with other volunteers, visit the event page:
*|event_url|*

You are receiving this email because you have RSVP'd to this event.
-----
MESSAGE;

	public function EmailVolunteers1HourBefore()
    {
		
		$this->out("\tEmailing volunteers 1-2 hours before event...");

    	$contain = array();
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', strtotime("now +1 hours") ),
			'Event.start_time <' => date('Y-m-d H:i:s', strtotime("now +2 hours") )
		);

		$events = $this->Event->find('all', compact('conditions', 'contain') );
		$endl = "\n";
		$endp = $endl . $endl;

		foreach($events as $event)
		{
			$this->out( sprintf("\t\t%s...", $event['Event']['title']) );

			$recipients = $this->_GetRecipients(
				$event['Event']['event_id'],
				false, // coordinator
				true, // rsvp
				false, // skills
				true // attending
			);

			$global_merge_vars = array(
				'event_title' => $event['Event']['title'],
				'event_start_time' => date("g:i A", strtotime($event['Event']['start_time']) ),
				'event_stop_time' => date("g:i A", strtotime($event['Event']['stop_time']) ),
				'event_url' => Router::url( array('go' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']), true ),
				'event_rsvp_link' => Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $event['Event']['event_id']), true ),
				'solution_name' => Configure::read("Solution.name"),
				'subscription_link' => Router::url( array('volunteer' => 'true', 'controller' => 'users', 'action' => 'profile'), true )
			);

			$to = array();
			$recipient_merge_vars = array();
			foreach($recipients as $user_id => $recipient)
			{				
				$recipient_merge_vars[ $recipient['email'] ] = array(
					'full_name' => $recipient['full_name']
				);

				$this->out("\t\t\t...adding recipient " . $recipient['email']);
				$to[] = $recipient['email'];
			}

			$email = new CakeEmail( 'mandrill_api' );
			$email
				->to( $to )
				->subject("[*|solution_name|*] *|event_title|* starts soon!")
				->addHeaders( $this->CreateMandrillMergeVars($global_merge_vars, $recipient_merge_vars) );

			if( $email->send(
				$this->template_volunteer_event_in_1_hour
				. $this->template_footer
			) )
				$this->out("\t\t...sent");
			else
				$this->out("\t\t...error sending");


		} // end foreach event

		$this->out("\t...done with volunteer reminders");
    }

	public function EmailVolunteers24HoursAfterEvent()
	{
		$this->out("\tEmailing volunteers event recap");

    	$contain = array(
			'Organization' => array(
				'Permission.write = 1' => array('User')
			),
			'Time' => array('User')
		);
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', strtotime("now -25 hours") ),
			'Event.start_time <' => date('Y-m-d H:i:s', strtotime("now -24 hours") )
		);

		$events = $this->Event->find('all', compact('conditions', 'contain') );
		$endl = "\n";
		$endp = $endl . $endl;

		$time_block_template_missed = $this->template_time_block_missed;
		$time_block_template_complete = $this->template_time_block_complete;
		$message_template = $this->template_event_recap;

		foreach($events as $event)
		{
			$this->out("\t\tProcessing " . $event['Event']['title']);

			//$this->out( print_r($event, false) );

			$event_coordinator_block = "";
			foreach( $event['Organization']['Permission'] as $permission )
			{
				$user = $permission['User'];
				$merge_vars = array(
					'full_name' => $user['full_name'],
					'email' => $user['email']
				);
				$event_coordinator_block .= $this->ApplyMergeVars("*|full_name|* <*|email|*>", $merge_vars). $endl;
			}
			$event_coordinator_block = trim($event_coordinator_block);
			
			

			foreach( $event['Time'] as $time )
			{
				$email = new CakeEmail('mandrill');

				$time_merge_vars = array(
					'time_in' => $time['start_time'],
					'time_out' => $time['stop_time'],
					'total_hours' => number_format($time['duration'], 2)
				);
				$time_block = ($time_merge_vars['time_out'] == null) ?
					$this->ApplyMergeVars($time_block_template_missed, $time_merge_vars) : 
					$this->ApplyMergeVars($time_block_template_complete, $time_merge_vars);

				$merge_vars = array(
					'solution_name' => Configure::read('Solution.name'),
					'event_name' => $event['Event']['title'],
					"full_name" => $time['User']['full_name'],
					"time_block" => $time_block,
					"event_coordinator_block" => $event_coordinator_block,
					"profile_link" => Router::url(
						array('volunteer' => true, 'controller' => 'users', 'action' => 'activity'),
						true
					)
				);
				
				$email
					->to( $time['User']['email'], $time['User']['full_name'] )
					->from ('servicespark@unitedwayalbanycounty.org', Configure::read("Solution.name")  )
					->subject( sprintf("[%s] Event recap for \"%s\"", Configure::read("Solution.name"), $event['Event']['title'] ) );
					

				if( $email->send( $this->ApplyMergeVars($message_template, $merge_vars) ) )
				//if( true )
				{
					$this->out("\t\t\t...sent to " . $time['User']['email'] );
				}
			}

			$this->out("\t\t... finished " . $event['Event']['title']);
		}
		$this->out("\t... finished emailing volunteers event recaps");
	}

	function ApplyMergeVars($string, $vars)
	{
		foreach($vars as $token => $value)
		{
			$string = str_replace("*|".$token."|*", $value, $string);
		}
		return $string;
	}

	public function EmailVolunteers24HoursBefore()
    {
		
		$this->out("\tEmailing volunteers 24 hours before event...");

    	$contain = array();
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', strtotime("now +24 hours") ),
			'Event.start_time <' => date('Y-m-d H:i:s', strtotime("now +25 hours") )
		);

		$events = $this->Event->find('all', compact('conditions', 'contain') );
		$endl = "\n";
		$endp = $endl . $endl;

		foreach($events as $event)
		{
			$this->out( sprintf("\t\t\tEmailing %s reminders to volunteers...", $event['Event']['title']) );


			$use_skills = false;
			if( $event['Event']['rsvp_percent'] < 100)
				$use_skills = true;

			$recipients = $this->_GetRecipients(
				$event['Event']['event_id'],
				false, // coordinator
				true, // rsvp
				$use_skills, // skills
				true // attending
			);

			$event_title = $event['Event']['title'];
			$event_start_time = date("g:i A", strtotime($event['Event']['start_time']) );
			$event_stop_time = date("g:i A", strtotime($event['Event']['stop_time']) );
			$event_rsvp_link = Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $event['Event']['event_id']), true );
			$event_url = Router::url( array('go' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']), true );

			foreach($recipients as $user_id => $recipient)
			{				
				$email = new CakeEmail('mandrill');

				$email
					->from( 'servicespark@unitedwayalbanycounty.org', Configure::read("Solution.name") )
					->subject( sprintf("[%s] \"%s\" is tomorrow", Configure::read("Solution.name"), $event['Event']['title']) )
					->to($recipient['email'], $recipient['full_name']);
				
				if ( isset($recipient['context']['skills']) && $recipient['context']['skills'] )
				{
					$why_this_email_block = sprintf("You are receiving this message because you have the following skills on your %s profile: ", Configure::read('Solution.name') ) . $endl;
					$why_this_email_block .= implode(', ', $recipient['Skill']);
					$next_steps_block = $this->template_volunteer_event_is_tomorrow_next_steps_not_attending;
				}
				
				if( isset($recipient['context']['rsvp']) && $recipient['context']['rsvp'] == true )
				{
					$why_this_email_block = "You are receiving this message because you are attending this event.";
					$next_steps_block = $this->template_volunteer_event_is_tomorrow_next_steps_attending;
				}

				//$footer = $this->ApplyMergeVars($this->template_footer, array('solution_name' => Configure::read("Solution.name")));

				$message_merge_vars = array(
					'full_name' => $recipient['full_name'],
					'event_title' => $event_title,
					'event_start_time' => $event_start_time,
					'event_stop_time' => $event_stop_time,
					'event_url' => $event_url,
					'next_steps_block' => $next_steps_block,
					'why_this_email' => $why_this_email_block,
					'event_rsvp_link' => $event_rsvp_link,
					'footer' => $this->template_footer,
					'solution_name' => Configure::read("Solution.name"),
					'subscription_link' => Router::url( array('volunteer' => 'true', 'controller' => 'users', 'action' => 'profile'), true )
				);

				$message = $this->ApplyMergeVars($this->template_volunteer_event_is_tomorrow, $message_merge_vars);



				if( $email->send($message) )
				{
					$this->out("\t\t\t...sent to " . $recipient['email']);
				}

			} // end foreach recipient

		} // end foreach event

		$this->out("\t...done with volunteer reminders");
    }

	public function EmailCoordinators25HoursBefore()
	{
		$this->out("\tSending event status updates to coordinators");

		$contain = array(
			'Organization' => array(
				'Permission.write = 1' => array('User')
			),
			'Rsvp' => array('User'),
			'Skill' => array('User')
		);
		$conditions = array(
			'Event.start_time >=' => date('Y-m-d H:i:s', strtotime("now +25 hours") ),
			'Event.start_time <' => date('Y-m-d H:i:s', strtotime("now +26 hours") )
		);

		$events = $this->Event->find('all', compact('conditions', 'contain') );
		foreach($events as $event)
		{
			$this->out( sprintf("\t\t\tEmailing %s reminders to coordinators...", $event['Event']['title']) );

			$email = new CakeEmail('mandrill');

			$email
				->domain('www.unitedwayalbanycounty.org')
				->emailFormat('text')
				->from( 'servicespark@unitedwayalbanycounty.org', Configure::read('Solution.name') )
				->subject( sprintf("[%s] \"%s\" is tomorrow", Configure::read('Solution.name'), $event['Event']['title']) );

			foreach($event['Rsvp'] as $rsvp)
			{
				$coming[] = $rsvp['User']['user_id'];
			}

			$coordinators = array();
			foreach($event['Organization']['Permission'] as $coordinator)
			{
				$email->addTo($coordinator['User']['email'], $coordinator['User']['full_name']);
				// $coordinators[] = array(
				// 	'full_name' => $coordinator['User']['full_name'],
				// 	'email' => $coordinator['User']['email']
				// );
			}

			// construct coordinator email

			$endl = "\n";
			$endp = $endl . $endl;
			$message = sprintf("The event \"%s\" is coming up. The following people have submitted an RSVP:", $event['Event']['title']) . $endl . $endl;

			foreach($event['Rsvp'] as $rsvp)
			{
				$coming[] = $rsvp['User']['user_id'];
				$message .= sprintf("%s <%s>", $rsvp['User']['full_name'], $rsvp['User']['email']) . $endl;
			}

			$message .= $endl;

			if($event['Event']['rsvp_percent'] < 100)
			{
				$message .= sprintf("Your RSVP goal has not been met. ");
				if( !empty($event['Skill']) )
				{
					$message .= sprintf("If the goal isn't met in one hour, %s will send a reminder email to volunteers with the following skills:",
						Configure::read('Solution.name')
					);
					$message .= $endp;
					foreach($event['Skill'] as $skill)
					{
						$message .= $skill['skill'] . $endl;
					}
				}
				else
				{
					$message .= "Your event currently does not have any skills attached." . $endl;
				}
				$message .=  $endl . "Please adjust the event's skills in order to maximize event attendance." . $endp;
			}

			$message .= "You can coordinate the event and communicate with volunteers at:" . $endl;
			$message .= "<" . Router::url( array('coordinator' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id']), true) . ">" . $endp;

			$message .= "Please reply-all to this message to discuss the event privately with other coordinators." .$endl;

			$message .= "-----" . $endl . "You are receiving this message because you are coordinating this event.";

			if( $email->send($message) )
			{
				$this->out("\t\t\t...done");
			}
			
		}
		$this->out("\t...done sending status updates to coordinators");
	}

	private function CreateMandrillMergeVars($global_merge_vars, $recipient_merge_vars, $rcpt_key = "rcpt")
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

		return $result;

	}

	private function _GetRecipients($event_id, $coordinators, $rsvp, $skills, $attended, $force = false)
	{
		$recipients = array();
		if( !$this->Event->exists($event_id) )
		{
			$this->out("<warning>There is no event with event_id $event_id.</warning>");
		}
		else
		{

			$conditions = array(
				'Event.event_id' => $event_id
			);

			if($coordinators)
				$contain['Organization']['Permission.write = 1'] = array('User');

			if($rsvp)
				$contain['Rsvp'] = array('User');

			if($skills)
				$contain['Skill'] = array('User');

			if($attended)
				$contain['Time'] = array('User');


			$event = $this->Event->find('first', compact('conditions', 'contain') );

			if( !empty($event['Organization']['Permission']) )
			{
				foreach($event['Organization']['Permission'] as $permission)
				{
					$user = $permission['User'];
					$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
					$recipients[ $user['user_id'] ]['email'] = $user['email'];
					$recipients[ $user['user_id'] ]['context']['coordinator'] = true;

				}
			}

			if( !empty($event['Rsvp']) )
			{
				foreach($event['Rsvp'] as $rsvp)
				{
					$user = $rsvp['User'];
					$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
					$recipients[ $user['user_id'] ]['email'] = $user['email'];
					$recipients[ $user['user_id'] ]['context']['rsvp'] = true;
				}
			}

			if( !empty($event['Time']) )
			{
				foreach($event['Time'] as $time)
				{
					$user = $time['User'];
					$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
					$recipients[ $user['user_id'] ]['email'] = $user['email'];
					$recipients[ $user['user_id'] ]['Time'] = $time;
					unset( $recipients[ $user['user_id'] ]['Time']['User'] );
					$recipients[ $user['user_id'] ]['context']['attended'] = true;
				}
			}

			if( !empty($event['Skill']) )
			{
				foreach($event['Skill'] as $skill)
				{
					foreach($skill['User'] as $user)
					{
						$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
						$recipients[ $user['user_id'] ]['email'] = $user['email'];
						$recipients[ $user['user_id'] ]['context']['skills'] = true;
						$recipients[ $user['user_id'] ]['Skill'][ $skill['skill_id'] ] = $skill['skill'];
					}
				}
			}
		}

		return $recipients;
	}
}
