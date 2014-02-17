<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {


	public $primaryKey = 'user_id';
	public $displayField = 'username';
	public $virtualFields = array(
	    'full_name' => 'CONCAT(User.first_name, " ", User.last_name)',
	    'account_age' => 'TIMESTAMPDIFF( MINUTE, User.created, Now() )'
	);

	public $validate = array(
		'username' => array(
			array(
				'rule' => 'alphaNumeric',
				'message' => 'Your username must consist of alphanumeric characters.',
				'required' => true
			),
			array(
				'rule' => 'unique',
				'message' => 'That username is taken.'
			)
		),
		'password' => array(
			'rule' => array('between', 40, 40),
			'message' => 'There was a problem saving your password',
			'required' => true
		),
		'email' => array(
			'rule' => 'email',
			'message' => 'You must input a valid email address',
			'required' => true
		)
	);


	public $actsAs = array('Containable');

	public function beforeSave($options = array())
	{
	    if (isset($this->data[ $this->alias ]['password']))
	    {
	        $hasher = new SimplePasswordHasher();
	        $this->data[ $this->alias ]['password'] = $hasher->hash( $this->data[ $this->alias ]['password'] );
	    }
	    return true;
	}


	public $hasOne = 'Recovery';

	public $hasAndBelongsToMany = array(
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'skills_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'skill_id',
			'unique' => 'keepExisting',
			// 'conditions' => '',
			// 'fields' => '',
			// 'order' => '',
			// 'limit' => '',
			// 'offset' => '',
			// 'finderQuery' => '',
		)
	);

	public $hasMany = array(
		'Permission' => array(
			'dependent' => true // when the User is deleted, all Permission entries are deleted
		)

	);

	

}
