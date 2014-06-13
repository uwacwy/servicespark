<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel
{

/**
	General Model Behavior and Setup
*/
	
	public $primaryKey = 'user_id';

	public $displayField = 'username';

	public $actsAs = array('Containable');

	public $virtualFields = array(
		'full_name' => 'CONCAT(User.first_name, " ", User.last_name)',
		'account_age' => 'TIMESTAMPDIFF( MINUTE, User.created, Now() )'
	);

	public $validate = array(
		'username' => array(
			'That username is taken.' => array(
				'rule' => 'isUnique'
			),
			'Your username must be at least 3 characters with no spaces.' => array(
				'rule' => '/^[a-zA-Z0-9_]{3,}$/i'
			)
		),
		'email' => array(
			'rule' => 'email',
			'message' => 'You must input a valid email address',
			'required' => true
		)
	);

/**
	Associations
*/

	public $hasOne = array('Recovery');

	public $hasAndBelongsToMany = array(
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'skills_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'skill_id'
		),		
		'Address' => array(
			'className' => 'Address',
			'joinTable' => 'addresses_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'address_id'
		)
	);

	public $hasMany = array(
		'Permission' => array(
			'dependent' => true // when the User is deleted, all Permission entries are deleted
		),
		'Time' => array(
			'dependent' => true // when the User is deleted, all Time entries are deleted
		),
		'Comment' => array(
			'dependent' => true
		)

	);

/**
	Overridden Methods
 */

	public function beforeSave($options = array())
	{
		
		if ( isset($this->data[ $this->alias ]['password']) )
		{
			$hasher = new SimplePasswordHasher();
			$this->data[ $this->alias ]['password'] = $hasher->hash( $this->data[ $this->alias ]['password'] );
		}
		return true;
	}

	

}
