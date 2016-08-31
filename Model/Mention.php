<?php
App::uses('AppModel', 'Model');

class Mention extends AppModel
{
	var $primaryKey = 'mention_id';
	
	var $belongsTo = array('User', 'Comment');
}