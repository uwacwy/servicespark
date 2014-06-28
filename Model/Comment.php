<?php
App::uses('AppModel', 'Model');
/**
 * Post Model
 *
 */
class Comment extends AppModel
{

	var $primaryKey = 'comment_id';

	public function afterSave($created, $options = array() )
	{
		//debug($this->id);

		$conditions = array('Comment.comment_id' => $this->id);
		$contain = array('User');

		$comment = $this->find('first', compact('conditions', 'contain') );

		preg_match_all('/@([A-Za-z0-9_]{1,15})(?![.A-Za-z])/', $comment['Comment']['body'], $mentioned_users);

		$mentioned_users = $this->User->find('all', array('contain' => array(), 'conditions' => array('User.username' => $mentioned_users[1] ) ) );

		$conditions = array('Event.event_id' => $comment['Comment']['event_id']);
		$contain = array(
			'Rsvp' => array(
				'fields' => array(),
				'User'
			),
			'Organization' => array(
				'fields' => array(),
				'Permission.write = 1' => array(
					'User'
				)
			),
			'Comment' => 'User');

		$event = $this->Event->find('first', compact('conditions', 'contain'));

		//debug($event['Rsvp']);

		foreach( $event['Rsvp'] as $attending )
		{
			if($attending['User']['email_attending'])
			{
				$recipients[ $attending['User']['user_id'] ]['User'] = $attending['User'];
				$recipients[ $attending['User']['user_id'] ]['attending'] = true;
			}
		}

		foreach( $event['Organization']['Permission'] as $coordinating )
		{
			$recipients[ $coordinating['User']['user_id'] ]['User'] = $coordinating['User'];
			$recipients[ $coordinating['User']['user_id'] ]['coordinating'] = true;
		}

		foreach( $mentioned_users as $mentioned )
		{
			if($mentioned['User']['email_mentions'])
			{
				$recipients[ $mentioned['User']['user_id'] ]['User'] = $mentioned['User'];
				$recipients[ $mentioned['User']['user_id'] ]['mentioned'] = true;
			}
		}

		foreach($event['Comment'] as $participating)
		{
			if( 
				strtotime($event['Event']['stop_time']) < time() // email when the event is current
				&& $participating['User']['email_participation'] // and if the user allows it
			) // event is old; retire participation anymore
			{
				$recipients[ $participating['User']['user_id'] ]['User'] = $participating['User'];
				$recipients[ $participating['User']['user_id'] ]['participating'] = true;
			}
		}

		// do not notify the current user about what they just did.
		unset($recipients[AuthComponent::user('user_id')]);

		//debug($event);
		//debug($recipients);
		//debug($comment);
		//debug($subject);
		//debug($message);

		//return;

		App::uses('CakeEmail', 'Network/Email');
		foreach ($recipients as $recipient)
		{
			$subject = __('[%s] New Comment on "%s"', 
				Configure::read('Solution.name'),
				$event['Event']['title']
			);
			if( isset($recipient['mentioned']) )
			{
				$subject = __('[%s] You were mentioned in a comment on "%s"', 
					Configure::read('Solution.name'),
					$event['Event']['title']
				);
			}
			$message = __("Hello, %%s.\n\nA new comment was posted on %s.\n\n".
				"%s said:\n".
				"%s\n\n".
				"You may view the comment at\n<%s>",
				$event['Event']['title'],
				$comment['User']['full_name'],
				$comment['Comment']['body'],
				Router::url( array(
					'go' => true, 
					'controller' => 'events', 
					'action' => 'view', 
					$event['Event']['event_id'], 
					"#" => sprintf('comment-%s', $comment['Comment']['comment_id'])
					),
					true // this returns the full url
				)
			);

			$message .= __("\n\n----\nYou are receiving this email because...");
			if( isset($recipient['attending']) )
				$message .= __("\n- you are attending this event");
			if( isset($recipient['participating']) )
				$message .= __("\n- you have previously left a comment on this event.");
			if( isset($recipient['coordinating']) )
				$message .= __("\n- you are coordinating this event.");
			if( isset($recipient['mentioned']) )
				$message .= __("\n- you were mentioned in this comment.");

			$message .= __("\n\nManage your notification preferences from your profile\n<%s>",
				Router::url('/users/profile', true)
			);

			CakeEmail::deliver(
				$recipient['User']['email'],
				$subject,
				sprintf($message, $recipient['User']['full_name']),
				array(
					'from' => 'servicespark@unitedwayalbanycounty.org'
				)
			);
		}


		return true;
	}
	
	var $belongsTo = array(
		'ParentComment' => array(
			'className' => 'Comment',
			'foreignKey' => 'parent_id',
			'counterCache' => true
		),
		'User',
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'event_id',
			'counterCache' => true
		)
	);
}