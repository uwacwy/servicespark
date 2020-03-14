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

	public $validate = array(
		'body' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Comment body cannot be blank.',
				'required' => true
			)
		)
	);
}
