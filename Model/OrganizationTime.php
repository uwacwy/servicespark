<?php
App::uses('AppModel', 'Model');
/**
 * Time Model
 *
 * @property Event $Event
 * @property User $User
 */
class OrganizationTime extends AppModel
{
	var $primaryKey = "organization_time_id";
	var $useTable = "organizations_times";
	
	var $belongsTo = array('Organization', 'Time');
}