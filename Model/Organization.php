<?php
App::uses('AppModel', 'Model');
/**
 * Organization Model
 *
 * @property User $User
 */
class Organization extends AppModel
{

/**
	General Model Behavior and Setup
*/
	public $primaryKey = 'organization_id';

	public $displayField = 'name';

	/*
		required
		-
		name

	*/	
	public $validate = array();

/**
	Associations
*/
	public $hasMany = array(
		'Permission' => array(
			'dependent' => true // when the Organization is deleted, Permissions are also deleted
		),
		'Event' => array(
			'dependent' => true // when Organzation is deleted, events are also deleted
		)
	);

	public $hasAndBelongsToMany = array(
		'Address' => array(
			'className' => 'Address',
			'joinTable' => 'addresses_organizations',
			'foreignKey' => 'organization_id',
			'associationForeignKey' => 'address_id'
		)
	);
}
