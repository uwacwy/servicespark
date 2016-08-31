<?php
App::uses('AppModel', 'Model');

class Verification extends AppModel
{
	var $primaryKey = 'verification_id';
	
	var $belongsTo = array('User');
}