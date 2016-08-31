<?php
App::uses('AppModel', 'Model');
/**
 * Post Model
 *
 */
class Comment extends AppModel
{
	var $primaryKey = 'comment_id';
	
	var $belongsTo = array(
		'ParentComment' => array(
			'className' => 'Comment',
			'foreignKey' => 'parent_id',
		),
		'User',
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'event_id',
			'counterCache' => true
		)
	);
	
	var $hasMany = array(
		'Mention' => array(
			'dependent' => true
		)
	);
	
	public function afterSave($created, $options = array() )
	{
		preg_match_all('/@([A-Za-z0-9_]{1,50})\b/', $this->data['Comment']['body'], $mentioned_users);
		
		if( !empty($mentioned_users[1]) )
		{
			
			$mentioned_user_ids = $this->User->find('list', array(
				'conditions' => array(
					'User.username' => $mentioned_users[1]
				),
				'contain' => array(),
				'fields' => array('user_id')
			) );
			
			$mentions = array();
			foreach($mentioned_user_ids as $user_id)
			{
				$mentions[] = array(
					'user_id' => $user_id,
					'comment_id' => $this->id
				);
			}
			
			if( !empty($mentions) )
				$this->Mention->saveAll($mentions);
		}
		
		parent::afterSave($options);
		
		
		/*
			Native events do not provide an adequate method for targeting a comment
			- after the comment is saved
			- AND after the comment's mentions are saved
		*/
		$event = new CakeEvent('App.Comment.afterSave.finalized', $this, array(
			'comment_id' => $this->id
		));
		
		$this->getEventManager()->dispatch( $event );
		
		return true;
	}
}
