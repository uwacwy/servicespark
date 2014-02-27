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

	public function beforeSave($options = array())
	{
		if( $this->Address->exists($user_id) )
		{
			$this->Address->delete($user_id);
		}
		
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
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	

}
