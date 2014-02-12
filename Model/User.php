<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {


	public $primaryKey = 'user_id';
	public $displayField = 'username';
	public $hasOne = 'Recovery';
	public $virtualFields = array(
	    'full_name' => 'CONCAT(User.first_name, " ", User.last_name)'
	);

	public function beforeSave($options = array())
	{
	    if (isset($this->data[ $this->alias ]['password']))
	    {
	        $hasher = new SimplePasswordHasher();
	        $this->data[ $this->alias ]['password'] = $hasher->hash( $this->data[ $this->alias ]['password'] );
	    }
	    return true;
	}


	

}
