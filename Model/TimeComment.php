<?php
App::uses('AppModel', 'Model');


class TimeComment extends AppModel
{
	var $primaryKey = "time_comment_id";
	
	var $belongsTo = array('Time', 'User');
}