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
			'Organization' => array(
				'fields' => array(),
				'Permission.write = 1' => array(
					'User'
				)
			),
			'Comment' => 'User');

		$event = $this->Event->find('first', compact('conditions', 'contain'));

		foreach( $event['Organization']['Permission'] as $coordinator )
		{
			$recipients[ $coordinator['User']['user_id'] ]['User'] = $coordinator['User'];
			$recipients[ $coordinator['User']['user_id'] ]['coordinating'] = true;
		}

		foreach( $mentioned_users as $mentioned_user )
		{
			if($mentioned_user['User']['email_mentions'])
			{
				$recipients[ $mentioned_user['User']['user_id'] ]['User'] = $mentioned_user['User'];
				$recipients[ $mentioned_user['User']['user_id'] ]['mentioned'] = true;
			}
		}

		foreach($event['Comment'] as $cmt)
		{
			if( 
				strtotime($event['Event']['stop_time']) < time() // email when the event is current
				&& $cmt['User']['email_participation'] // and if the user allows it
			) // event is old; retire participation anymore
			{
				$recipients[ $cmt['User']['user_id'] ]['User'] = $cmt['User'];
				$recipients[ $cmt['User']['user_id'] ]['participating'] = true;
			}
		}

		// do not notify the current user about what they just did.
		unset($recipients[AuthComponent::user('user_id')]);

		// debug($event);
		// debug($recipients);
		// debug($comment);


		App::uses('CakeEmail', 'Network/Email');

		//debug($subject);
		//debug($message);

		//return;

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
			'foreignKey' => 'parent_id'
		),
		'User',
		'Event'
	);
}