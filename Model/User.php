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
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'That username is taken.'
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

	public $hasOne = 'Recovery';

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
