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
}
