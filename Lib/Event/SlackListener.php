<?php

App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('Event', 'Model');
App::uses('Rsvp', 'Model');
App::uses('User', 'Model');
App::uses('Comment', 'Model');

class SlackListener implements CakeEventListener
{
	private $webhook_url = '';
	
	function __construct($_webhook_url)
	{
		$this->webhook_url = $_webhook_url;
	}
	
	public function implementedEvents()
	{
		return array(
			'App.User.afterSave.created' => 'user_created',
			'App.Event.afterSave.created' => 'event_created',
			'App.Rsvp.afterSave.created' => 'rsvp_created',
			'App.RSVP.Delete.Success' => 'rsvp_cancelled',
			'App.Skill.afterSave.created' => 'skill_created',
			'App.Comment.afterSave.created' => 'comment_created'
		);
	}
	
	public function user_created($cake_event)
	{
		$User = $cake_event->subject();
		$user_id = $cake_event->data['user_id'];
		$user = $User->find('first', array('conditions' => array('User.user_id' => $user_id) ) );
		
		$response = array(
			'text' => __("%s (%s) has signed up for %s.", $user['User']['full_name'], $user['User']['username'], Configure::read('Solution.name') ),
			'attachments' => array()
		);
		
		$this->slack_advanced($response);
		

	}
	
	public function comment_created($cake_event)
	{
		$Comment = new Comment();
		
		$comment_id = $cake_event->data['comment_id']; // hooks default afterSave event
		
		if( !$Comment->exists($comment_id) )
			return;
		
		$comment_conditions = array('Comment.comment_id' => $comment_id);
		$comment_contain = array(
			'Event',
			'User',
			'ParentComment' => array('User')
		);
		
		$comment = $Comment->find('first', array(
			'conditions' => $comment_conditions,
			'contain' => $comment_contain
		));
		
		$response = array(
			'text' => __("A new comment has been posted on %s", Configure::read('Solution.name') ),
			'attachments' => array()
		);
		
		$comment_attachment = array(
			'fallback' => __("%s posted a new comment on %s", $comment['User']['full_name'], $comment['Event']['title']),
			'author_name' => __("%s on", $comment['User']['full_name']),
			'author_icon' => Router::url( array(
				'go' => false,
				'controller' => 'users',
				'action' => 'avatar',
				$comment['User']['username'],
				40
			), true),
			'title' => __("%s", $comment['Event']['title']),
			'title_link' => Router::url( array(
				'go' => true,
				'controller' => 'events', 
				'action' => 'view', 
				$comment['Event']['event_id'], 
				'#' => 'comment-'.$comment['Comment']['comment_id']
			), true ),
			'text' => $comment['Comment']['body']
		);
		
		if( $comment['ParentComment']['comment_id'] )
		{
			$comment_attachment['fields'][] = array(
				'title' => __("In reply to %s, who said...", $comment['ParentComment']['User']['full_name']),
				'value' => $comment['ParentComment']['body']
			);
		}
		
		$response['attachments'][] = $comment_attachment;
		
		$this->slack_advanced($response);
		
	}
	
	public function event_created($cake_event)
	{
		$event_id = $cake_event->data['event_id'];
		$Event = new Event();
		
		$event = $Event->findByEventId($event_id);
		
		$response = array(
			'text' => __('A new event has been created'),
			'attachments' => array(
				array(
					'title' => $event['Event']['title'],
					'title_link' => Router::url( array(
						'controller' => 'events',
						'action' => 'view',
						'go' => true,
						$event['Event']['event_id']
					) , true),
					'text' => $event['Event']['description'],
					'fallback' => __('%s has been created by %s', $event['Event']['title'], $event['Organization']['name']),
					'fields' => array(
						array('title' => 'Start Date', 'value' => $event['Event']['start_time'], 'short' => true),
						array('title' => 'Stop Date', 'value' => $event['Event']['stop_time'], 'short' => true),
						array('title' => 'RSVP Goal', 'value' => $event['Event']['rsvp_desired'], 'short' => true),
						array('title' => 'Clock-In Token', 'value' => $event['Event']['start_token'], 'short' => true),
						array('title' => 'Clock-Out Token', 'value' => $event['Event']['stop_token'], 'short' => true),
					)
				) // end attachment
			)
		);
		
		$this->slack_advanced(
			$response
		);
	}
	
	public function rsvp_created($cake_event)
	{
		$rsvp_id = $cake_event->data['rsvp_id'];
		
		$Rsvp = new Rsvp();
		
		$rsvp = $Rsvp->findByRsvpId($rsvp_id);
		
		$this->slack(
			__("%s is attending %s", $rsvp['User']['full_name'], $rsvp['Event']['title'] )
		);
	}
	
	public function rsvp_cancelled($cake_event)
	{
		$User = new User();
		$Event = new Event;
		$user = $User->findByUserId( $cake_event->data['user_id'] );
		$event = $Event->findByEventId( $cake_event->data['event_id'] );
		
		$this->slack(
			__("%s is no longer attending %s", $user['User']['full_name'], $event['Event']['title'] )
		);
	}
	

	
	public function skill_created($cake_event)
	{
		$msg = __("A new skill, %s, was created.", $cake_event->data['modelData']['Skill']['skill']);
		$this->slack( $msg );
	}
	
	private function slack($message)
	{
		$this->slack_advanced(array(
			'text' => $message,
		));
	}
	
	private function slack_advanced($payload)
	{
		$data = "payload=" . json_encode($payload);
	
		// You can get your webhook endpoint from your Slack settings
		$ch = curl_init( $this->webhook_url );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
	}

}