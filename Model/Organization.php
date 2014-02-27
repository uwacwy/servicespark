<?php
App::uses('AppModel', 'Model');
/**
 * Organization Model
 *
 * @property User $User
 */
class Organization extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'organization_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	public $hasMany = array(
		'Permission' => array(
			'dependent' => true // when the Organization is deleted, Permissions are also deleted
		)
	);


}
