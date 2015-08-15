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
			'App.Recovery.afterSave.created' => 'recovery_created'
		);
	}
	
	public function user_created($cake_event)
	{
		$this->User = $cake_event->subject();
		$user = $this->User->find('first', array(
			'conditions' => array('User.user_id' => $cake_event->data['user_id']),
			'contain' => false
		));
		
		debug($user);
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
		
		$recipients = $this->_GetRecipients($event_id, false, false, true, false, false, null);
		
		if( empty($recipients) )
		{
			CakeLog::write('info', "MandrillListener::event_created: No recipients to notify..." . $cake_event->data['event_id']);
			return;
		}
		
		$recipient_merge_vars = array();
		foreach($recipients as $user_id => $recipient)
		{				
			$recipient_merge_vars[ $recipient['email'] ] = array(
				'full_name' => $recipient['full_name']
			);
			$to[] = $recipient['email'];
		}
		
		$headers = $this->CreateMandrillMergeVars( $recipient_merge_vars );
		$headers['preserve_recipients'] = false;
		
		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_event')
			->emailFormat('text')
			->viewVars(array(
				'event' => $app_event
			))
			->to( $to )
			->subject( $this->_ApplyMergeVars(
				"[*|solution_name|*] New event for *|full_name|*: *|event_title|*",
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
		$comment_id = $cake_event->data['comment_id'];
		$comment_conditions = array(
			'Comment.comment_id' => $comment_id
		);

		
		$comment = $Comment->find('first', array(
			'conditions' => $comment_conditions, 
			'contain' => array('Event', 'User')
		));
		
		
		preg_match_all('/@([A-Za-z0-9_]{1,15})(?![.A-Za-z])/', $comment['Comment']['body'], $mentioned_users);
		
		$recipients = $this->_GetRecipients($comment['Comment']['event_id'], true, false, true, true, true, $mentioned_users[1]);
		$recipients = $this->_RestrictRecipients($recipients);
		
		unset($recipients[AuthComponent::user('user_id')]);
		
		if( empty($recipients) )
		{
			CakeLog::write('info', "Nobody to notify; ending!");
			return;
		}
		
		$recipient_merge_vars = array();
		foreach($recipients as $user_id => $recipient)
		{	
			$subject = "[*|solution_name|*] New Comment on \"*|event_title|*\"";
			$reasons = array();
				
			if( isset($recipient['context']['attended']) && $recipient['context']['attended'] )
				$reasons[] = __("- You attended this event.");
				
			if( isset($recipient['context']['going']) && $recipient['context']['going'] )
				$reasons[] = __("- You have RSVP'd to this event." );
				
			if( isset($recipient['context']['mentioned']) && $recipient['context']['mentioned'] )
			{
				$reasons[] = __("- You were mentioned in this comment.");
				$subject = "[*|solution_name|*] You were mentioned in a comment on \"*|event_title|*\"";
			}
			
			if( isset($recipient['context']['discussing']) && $recipient['context']['discussing'] )
				$reasons[] = __("- You previously left a comment on this event.");
				
			if( isset($recipient['context']['coordinating']) && $recipient['context']['coordinating'] )
				$reasons[] = __("- You are coordinating this event");
			
			$recipient_merge_vars[ $recipient['email'] ] = array(
				'full_name' => $recipient['full_name'],
				'reasons_block' => implode("\n", $reasons),
				'subject' => $this->_ApplyMergeVars($subject, array(
					'solution_name' => Configure::read('Solution.name'),
					'event_title' => $comment['Event']['title']
				))
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

		if( $email->send() )
			CakeLog::write('info', "Finished sending new comment email notifications");
		else
			CakeLog::write('info', "Send Failed");
		
		
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
}