<?php
App::uses('AppModel', 'Model');
/**
 * Address Model
 *
 * @property Event $Event
 * @property User $User
 */
class Address extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'address_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'mailing_address';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Event' => array(
			'className' => 'Event',
			'joinTable' => 'addresses_events',
			'foreignKey' => 'address_id',
			'associationForeignKey' => 'event_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'User' => array(
			'className' => 'User',
			'joinTable' => 'addresses_users',
			'foreignKey' => 'address_id',
			'associationForeignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Organization' => array(
			'className' => 'Organization',
			'joinTable' => 'addresses_organizations',
			'foreignKey' => 'address_id',
			'associationForeignKey' => 'organization_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

}
