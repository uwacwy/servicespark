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

<<<<<<< HEAD
	public $validate = array(
		'address1' => array(
			'required' => 'true'
		),
		'city' => array(
			'required' => 'true'
		),
		'state' => array(
			'required' => 'true'
		),
		'zip' => array(
			'required' => 'true',
			'allowEmpty' => 'false'
=======
	/*
		address1, city, state, zip, type required.
	*/
	public $validate = array(
		'address1' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This line required.'
			)
		),
		'city' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This line required.'
			)
		),
		'state' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This line required.'
			)
		),
		'zip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This line required.'
			),
			'postal' => array(
				'rule' => array('postal', null, 'us'),
				'message' => 'Only letters and numbers are allowed for an address.'
			)
		),
		'type' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This line required.'
			)
>>>>>>> Model-Validation
		)
	);

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
