<?php
App::uses('AppModel', 'Model');

class UserMeta extends AppModel
{
	var $primaryKey = "user_meta_id";
	var $useTable = "users_meta";
	
	var $belongsTo = array('User');
}