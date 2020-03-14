<?php
App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('Event', 'Model');
App::uses('Comment', 'Model');

class MandrillListener implements CakeEventListener
{
	private $configuration_name = '';
	
	function __construct($_configuration_name)
	{
		$this->configuration_name = $_configuration_name;
	}
	
	public function implementedEvents()
	{
		return array(
			'App.Event.afterSave.created' => 'event_created',
			'App.Event.Delete.Success' => 'event_deleted',
			'App.Skill.afterSave.created' => 'skill_created',
			'App.Comment.afterSave.created' => 'comment_created',
			'App.User.afterSave.created' => 'user_created',
			'App.Recovery.afterSave.created' => 'recovery_created',
			'App.OrganizationTime.afterSave.created' => 'organization_time_created',
			'App.Time.afterSave.modified' => 'time_touched'
		);
	}
	
	/*
		organization_time_created
		--
		Processes notifications to Coordinators about new OrganizationTime
		and related Time
	*/
	public function organization_time_created($cake_event)
	{
		$this->OrganizationTime = $cake_event->subject();
		
		$organization_time = $this->OrganizationTime->find('first', array(
			'conditions' => array('OrganizationTime.organization_time_id' => $cake_event->data['organization_time_id']),
			'contain' => array(
				'Organization' => array('Permission.write = 1' => array('User.email_coordinating = 1')),
				'Time' => array('User')
			)
		));
		
		$organization_time['Time']['view_link'] =
			Router::url( array(
				'volunteer' => false,
				'controller' => 'times', 
				'action' => 'view', 
				$organization_time['Time']['time_id']
			), true);
		
		$global_merge_vars = array(
			'solution_name' => Configure::read('Solution.name'),
			'time' => $this->PrettyTime( $organization_time['Time'], array('User') ),
			'user' => $this->PrettyUser( $organization_time['Time']['User'], array('password')),
			'organization' => $organization_time['Organization']
		);
		
		unset(
			$global_merge_vars['organization']['Permission']
		);
		
		$global_merge_vars = Hash::flatten($global_merge_vars, "_");
		
		$to = Hash::extract($organization_time, 'Organization.Permission.{n}.User.email');
		$recipient_merge_vars = Hash::combine(
			$organization_time, 
			'Organization.Permission.{n}.User.email', 'Organization.Permission.{n}.User');
		
		$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);
		
		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_organization_time')
			->emailFormat('text')
			->to( $to )
			->subject( "[*|solution_name|*] *|organization_name|*: *|time_duration|* hours submitted for approval" )
			->addHeaders( $headers );
			
		if( $email->send() )
		{
			$this->log('- Sent new organization time notifications to coordinators');
		}
	}
	
	private function PrettyEvent($event, $unsets = array() )
	{
		$event['start_time'] = date('F j, Y g:i a', strtotime($event['start_time']) );
		$event['stop_time'] = date('F j, Y g:i a', strtotime($event['stop_time']) );
		
		foreach($unsets as $unset)
			unset($event[$unset]);
			
		return $event;
	}
	
	private function PrettyTime($time, $unsets = array() )
	{
		$time['start_time'] = date('F j, Y g:i a', strtotime($time['start_time']) );
		$time['stop_time'] = date('F j, Y g:i a', strtotime($time['stop_time']) );
		$time['duration'] = number_format($time['duration'], 2);
		$time['view_link'] = Router::url( array(
			'volunteer' => false,
			'controller' => 'times', 
			'action' => 'view', 
			$time['time_id']
		), true);
		
		foreach($unsets as $unset)
			unset($time[$unset]);
		
		return $time;
	}
	
	private function PrettyOrganization($organization, $unsets = array() )
	{
		foreach($unsets as $unset)
			unset($organization[$unset]);
		
		return $organization;
	}
	
	public function time_touched($cake_event)
	{
		$Model = $cake_event->subject();
		
		if( $Model->name == "Time" )
		{
			$Time = $Model;
			$time_id = $Time->data['Time']['time_id'];
		}
		else
		{
			$Time = $Model->Time;
			$time_id = $Model->data[ $Model->name ]['time_id'];
		}
		
		$User = $Time->User;
		
		$time = $Time->find('first', array(
			'conditions' => array(
				'Time.time_id' =>  $time_id
			),
			'contain' => array(
				'User',
				'OrganizationTime' => array(
					'Organization' => array(
						'Permission.write = 1' => array(
							'User.email_coordinating = 1'
						)
					)
				)
			)
		));

		// organization time created (pending)
		if( !empty($time['OrganizationTime']) )
		{
			if( $time['Time']['status'] == 'pending' )
			{
				$template = 'coordinator_time_modified';
				$subject = 'Please approve *|time_duration|* hours for *|organization_name|*';
				$to = Hash::extract($time, 'OrganizationTime.{n}.Organization.Permission.{n}.User.email');
				
				$coordinators = Hash::extract($time, 'OrganizationTime.{n}.Organization.Permission.{n}.User');
				
				foreach($coordinators as $key => $coordinator)
				{
					if( empty($coordinator) )
						unset($coordinators[$key]);
				}
				
				$recipient_merge_vars = Hash::combine( $coordinators, '{n}.email', '{n}');
					
			}
			else
			{
				$template = 'volunteer_time_modified';
				$subject = 'Your time at *|organization_name|* has been *|time_status|*';
				// email the user
				$to = array( $time['User']['email'] => $time['User']['email'] );
				$recipient_merge_vars = array(
					$time['User']['email'] => $time['User']
				);
			}
			
			$global_merge_vars = array(
				'solution_name' => Configure::read('Solution.name'),
				'organization' => $this->PrettyOrganization($time['OrganizationTime'][0]['Organization'], array('Permission')),
				'time' => $this->PrettyTime($time['Time']),
				'user' => $this->PrettyUser($time['User'], array('password', 'super_admin'))
			);
			
			$global_merge_vars = Hash::flatten($global_merge_vars, '_');
			
			$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);
			
			$email = new CakeEmail( $this->configuration_name );
			$email
				->template($template)
				->emailFormat('text')
				->to( $to )
				->subject( "[*|solution_name|*] " . $subject )
				->addHeaders( $headers );
				
			if( $email->send() )
			{
				$this->log('- Sent modified org time emails');
			}
		}
	}
	
	private function PrettyUser($user, $unsets = array() )
	{
		
		foreach($unsets as $unset)
			unset($user[$unset]);
		
		return $user;
	}
	
	public function user_created($cake_event)
	{
		$this->User = $cake_event->subject();
		$user = $this->User->find('first', array(
			'conditions' => array('User.user_id' => $cake_event->data['user_id']),
			'contain' => false
		));
		
		$Email = new CakeEmail( $this->configuration_name );
		$Email->template('new_user')
			->viewVars( compact('user') )
			->emailFormat('text')
			->to( $user['User']['email'], $user['User']['full_name'] )
			->subject( __('[%1$s] Welcome to %1$s, %2$s!', Configure::read('Solution.name'), $user['User']['first_name']) )
			->send();
	}
	
	public function event_created($cake_event)
	{
		CakeLog::write('info', "Started MandrillListener->event_created for event " . $cake_event->data['event_id']);
		
		$model_event = new Event();
		
		$event_id = $cake_event->data['event_id'];
		
		$event_conditions = array(
			'Event.event_id' => $event_id,
		);
		$event_contain = array(
			'Organization' => array(
				'Permission.write = 1' => array('User')
			)
		);
		$app_event = $model_event->find('first', array(
			'contain' => $event_contain, 
			'conditions' => $event_conditions
		) );
		
		$ics = $model_event->get_ics($event_id);
		
		//$recipients = $this->_GetRecipients($event_id, false, false, true, false, false, null);
		
		$recipients = $this->GetRecipients($event_id, array(
			'skills' => true
		));
		
		if( empty($recipients) )
		{
			CakeLog::write('info', "MandrillListener::event_created: No recipients to notify..." . $cake_event->data['event_id']);
			return;
		}
		
		$global_merge_vars = array(
			'solution_name' => Configure::read('Solution.name'),
			'event' => $this->PrettyEvent($app_event['Event']),
			'event_rsvp_going_url' => Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', $app_event['Event']['event_id']), true ),
			'event_url' => Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'view', $app_event['Event']['event_id']), true ),
			'organization' => $this->PrettyOrganization($app_event['Organization'], array('Permission')),
		);
		$global_merge_vars = Hash::flatten($global_merge_vars, "_");
		$recipient_merge_vars = Hash::combine($recipients, '{n}.User.email', '{n}.User');
		$to = Hash::extract($recipients, '{n}.User.email');
		
		$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars );
		
//		$headers['attachments'] = array(
//			array(
//				'type' => 'text/calendar',
//				'name' => 'event.ics',
//				'content' => base64_encode($ics)
//			)
//		);
		
		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_event')
			->emailFormat('text')
			->to( $to )
			->subject( $this->_ApplyMergeVars(
				"[*|solution_name|*] *|event_title|*",
				array(
					'solution_name' => Configure::read('Solution.name'),
					'event_title' => $app_event['Event']['title']
				)
			))
			->addHeaders( $headers );

		if( $email->send() )
			CakeLog::write('info', "Send Succeeded");
		else
			CakeLog::write('info', "Send Failed");
			
		CakeLog::write('info', "Finished MandrillListener::event_created for event " . $cake_event->data['event_id']);

		

	}
	
	public function skill_created($cake_event)
	{
		
	}
	
	public function event_deleted($cake_event)
	{
		
	}
	
	public function comment_created($cake_event)
	{
		CakeLog::write('info', "Sending new comment email notifications.");

		$Comment = new Comment();
		$User = $Comment->User;
		$comment_id = $cake_event->data['comment_id'];
		$comment_conditions = array(
			'Comment.comment_id' => $comment_id
		);

		
		$comment = $Comment->find('first', array(
			'conditions' => $comment_conditions, 
			'contain' => array('Event', 'User')
		));
		
		
		preg_match_all('/@([A-Za-z0-9_]{1,15})(?![.A-Za-z])/', $comment['Comment']['body'], $mentioned_users);
		
		$recipients = $this->_GetRecipients(
			$comment['Comment']['event_id'], 
			true, //coordinators
			false, //skills
			false, //going
			false, //attended
			true, //discussing
			$mentioned_users[1]);
		$recipients = $this->_RestrictRecipients($recipients);
		
		unset($recipients[ $comment['Comment']['user_id'] ]);
		
		if( empty($recipients) )
		{
			CakeLog::write('info', "Nobody to notify; ending!");
			return;
		}
		
		$recipient_merge_vars = array();
		$guids = array();
		
		foreach($recipients as $user_id => $recipient)
		{	
			$subject = "RE: [*|solution_name|*] *|event_title|*";
			$reasons = array();
			
			$guid = $this->guid();
				
			if( isset($recipient['context']['attended']) && $recipient['context']['attended'] )
				$reasons[] = __("- You attended this event.");
				
			if( isset($recipient['context']['going']) && $recipient['context']['going'] )
				$reasons[] = __("- You have RSVP'd to this event." );
				
			if( isset($recipient['context']['mentioned']) && $recipient['context']['mentioned'] )
			{
				$reasons[] = __("- You were mentioned in this comment.");
			}
			
			if( isset($recipient['context']['discussing']) && $recipient['context']['discussing'] )
				$reasons[] = __("- You previously left a comment on this event.");
				
			if( isset($recipient['context']['coordinating']) && $recipient['context']['coordinating'] )
				$reasons[] = __("- You are coordinating this event");
			
			$recipient_merge_vars[ $recipient['email'] ] = array(
				'full_name' => $recipient['full_name'],
				'reasons_block' => implode("\n", $reasons),
				'reply_email' => __("%s@reply.servicespark.org", $guid),
				'subject' => $this->_ApplyMergeVars($subject, array(
					'solution_name' => Configure::read('Solution.name'),
					'event_title' => $comment['Event']['title']
				))
			);
			$guids[] = array(
				'user_id' => $user_id,
				'guid' => $guid,
				'event_type' => 'comment_reply',
				'event_data' => json_encode(array(
					'Comment.comment_id' => $comment_id
				)),
				'expires' => $User->getDataSource()->expression('DATE_ADD(now(), INTERVAL 1 DAY)')
			);
			$to[] = $recipient['email'];
			
			
		}
		
		$headers = $this->CreateMandrillMergeVars( $recipient_merge_vars );
		$headers['preserve_recipients'] = false;
		
		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_comment')
			->emailFormat('text')
			->viewVars(array(
				'comment' => $comment
			))
			->to( $to )
			->subject( "*|subject|*" )
			->addHeaders( $headers );
		
		$this->log($guids);
		//$User->Email->saveAll($guids);

		if( $email->send() )
			CakeLog::write('info', "Finished sending new comment email notifications");
		else
			CakeLog::write('info', "Send Failed");
			
		
		
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
	
	
	
	private function CreateMandrillMergeVars($recipient_merge_vars, $rcpt_key = "rcpt")
	{
		$result = array(
			'merge_vars' => array()
		);

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
	
	private function _ApplyMergeVars($string, $vars)
	{
		foreach($vars as $token => $value)
		{
			$string = str_replace("*|".$token."|*", $value, $string);
		}
		return $string;
	}
	
	private function _RestrictRecipients(&$recipients)
	{
		foreach( $recipients as $user_id => &$recipient )
		{
			if( !$recipient['User']['email_skills'] )
				unset( $recipient['context']['skills'] );
				
			if( !$recipient['User']['email_attended'] )
				unset( $recipient['context']['attended'] );
				
			if( !$recipient['User']['email_going'] )
				unset( $recipient['context']['going'] );
				
			if( !$recipient['User']['email_mentions'] )
				unset( $recipient['context']['mentioned'] );
			
			if( !$recipient['User']['email_discussing'] )
				unset( $recipient['context']['discussing'] );
				
			if( !$recipient['User']['email_coordinating'] )
				unset( $recipient['context']['coordinating'] );
				
			if( empty($recipient['context']) )
				unset($recipients[ $user_id ]);
		}
		
		return $recipients;
	}
	
	private function _GetRecipients($event_id, $coordinating, $skills, $going, $attended, $discussing, $mentioned_users = null)
	{
		$model_event = new Event();
		$User = new User();
		$recipients = array();
		if( $model_event->exists($event_id) )
		{
			$conditions = array(
				'Event.event_id' => $event_id
			);

			if($coordinating)
				$contain['Organization']['Permission.write = 1'] = array('User');

			if($going)
				$contain['Rsvp'] = array('User');

			if($skills)
				$contain['Skill'] = array('User');

			if($attended)
				$contain['EventTime']['Time'] = array('User');
				
			if($discussing)
				$contain['Comment'] = array('User');


			$event = $model_event->find('first', compact('conditions', 'contain') );

			if( !empty($event['Organization']['Permission']) )
			{
				foreach($event['Organization']['Permission'] as $permission)
				{
					$user = $permission['User'];
					$recipients[ $user['user_id'] ]['User'] = $user;
					$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
					$recipients[ $user['user_id'] ]['email'] = $user['email'];
					$recipients[ $user['user_id'] ]['context']['coordinating'] = true;

				}
			}

			if( !empty($event['Rsvp']) )
			{
				foreach($event['Rsvp'] as $rsvp)
				{
					$user = $rsvp['User'];
					$recipients[ $user['user_id'] ]['User'] = $user;
					$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
					$recipients[ $user['user_id'] ]['email'] = $user['email'];
					$recipients[ $user['user_id'] ]['context']['going'] = true;
				}
			}
			
			if( !empty($event['Comment']) )
			{
				foreach($event['Comment'] as $comment)
				{
					$user = $comment['User'];
					$recipients[ $user['user_id'] ]['User'] = $user;
					$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
					$recipients[ $user['user_id'] ]['email'] = $user['email'];
					$recipients[ $user['user_id'] ]['context']['discussing'] = true;
				}
			}

			// if( !empty($event['EventTime']['Time']) )
			// {
			// 	foreach($event['EventTime'] as $time)
			// 	{
			// 		$user = $time['User'];
					
			// 		$recipients[ $user['user_id'] ]['User'] = $user;
			// 		$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
			// 		$recipients[ $user['user_id'] ]['email'] = $user['email'];
			// 		$recipients[ $user['user_id'] ]['Time'] = $time;
			// 		unset( $recipients[ $user['user_id'] ]['Time']['User'] );
			// 		$recipients[ $user['user_id'] ]['context']['attended'] = true;
			// 	}
			// }

			if( !empty($event['Skill']) )
			{
				foreach($event['Skill'] as $skill)
				{
					foreach($skill['User'] as $user)
					{
						$recipients[ $user['user_id'] ]['User'] = $user;
						$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
						$recipients[ $user['user_id'] ]['email'] = $user['email'];
						$recipients[ $user['user_id'] ]['context']['skills'] = true;
						$recipients[ $user['user_id'] ]['Skill'][ $skill['skill_id'] ] = $skill['skill'];
					}
				}
			}
		}
		
		if( $mentioned_users )
		{
			$mentions = $User->find('all', array('contain' => array(), 'conditions' => array('User.username' => $mentioned_users ) ) );
			foreach( $mentions as $mention )
			{
				$user = $mention['User'];
				$recipients[ $user['user_id'] ]['User'] = $user;
				$recipients[ $user['user_id'] ]['full_name'] = $user['full_name'];
				$recipients[ $user['user_id'] ]['email'] = $user['email'];
				$recipients[ $user['user_id'] ]['context']['mentioned'] = true;
			}
		}

		return $recipients;
	}
	
	private function _sendEmail($template, $subject, $recipients, $global_merge_vars, $recipient_merge_vars)
	{
		$global_merge_vars_default = array(
			'solution_name' => Configure::read('Solution.name'),
			'user_profile_link' => Router::url(
				array('volunteer' => false, 'controller'=> 'users', 'action' => 'profile'), true)
		);
		$global_merge_vars = array_merge($global_merge_vars_default, $global_merge_vars);
		
		$email = new CakeEmail( 'mandrill_api' );
		$email
			->to( $recipients )
			->subject("[*|solution_name|*] ". $subject)
			->addHeaders( $this->_CreateMandrillMergeVars($global_merge_vars, $recipient_merge_vars) )
			->send($template);
	}
	
	private function GetRecipients($event_id, $restrict = array() )
	{
		if( !isset($this->Event) )
			$this->Event = new Event();
			
		$default = array(
			'attended' => false,
			'going' => false,
			'coordinating' => false,
			'discussing' => false,
			'skills' => false,
			'mentioned' => false
		);
		
		$loci = Hash::merge($default, $restrict);
		
		$contain = array();
		
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
			$contain['Comment']['User'] =  array(
				'conditions' => array(
					'User.email_discussing' => true
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
			
		if( $loci['mentioned'] )
			$contain['Comment']['Mention'] = array(
				'User' => array(
					'conditions' => array(
						'User.email_mentions' => true
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
			'coordinating' => Hash::extract($result, 'Organization.Permission.{n}.User'),
			'mentioned' => Hash::extract($result, 'Comment.{n}.Mention.User')
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
				if( !empty($user) )
				{
					$user_id = $user['user_id'];
					
					$flipped_result[ $user_id ]['User'] = $user;
					$flipped_result[ $user_id ]['Skill'][] = $skill;
					$flipped_result[ $user_id ][$reason_key]['skills'] = true;
				}
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
		
		foreach( array('going', 'discussing', 'coordinating', 'mentioned') as $locus )
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
	
	private function deprecated_CreateMandrillMergeVars($global_merge_vars, $recipient_merge_vars, $rcpt_key = "rcpt")
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
	
	function guid()
	{
	    if (function_exists('com_create_guid') === true)
	    {
	        return trim(com_create_guid(), '{}');
	    }
	
	    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	
	private function log($that, $severity = "debug")
	{
		if( is_array($that) )
			$that = print_r($that, true);
		
		
		CakeLog::write( $severity, $that );
	}
}
