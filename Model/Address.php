<?php
App::uses('AppModel', 'Model');
/**
 * Address Model
 *
 * @property Event $Event
 * @property User $User
 */
class Address extends AppModel
{

/**
General Model Behavior and Setup
*/

	public $primaryKey = 'address_id';

	public $displayField = 'address1';

/**
	Associations
*/

	public $hasAndBelongsToMany = array(
		'Event' => array(
			'className' => 'Event',
			'joinTable' => 'addresses_events',
			'foreignKey' => 'address_id',
			'associationForeignKey' => 'event_id'
		),
		'User' => array(
			'className' => 'User',
			'joinTable' => 'addresses_users',
			'foreignKey' => 'address_id',
			'associationForeignKey' => 'user_id'
		),
		'Organization' => array(
			'className' => 'Organization',
			'joinTable' => 'addresses_organizations',
			'foreignKey' => 'address_id',
			'associationForeignKey' => 'organization_id'
		)
	);

}
