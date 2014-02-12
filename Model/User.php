<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'username';

	public $virtualFields = array(
	    'full_name' => 'CONCAT(User.first_name, " ", User.last_name)'
	);

	public function beforeSave($options = array())
	{
	    if (isset($this->data[ $this->alias ]['password']))
	    {
	    	debug('initiating beforeSave password hash');
	    	debug(sprintf('incoming password was %s', $this->data[ $this->alias ]['password']) );
	        $hasher = new SimplePasswordHasher();
	        $this->data[ $this->alias ]['password'] = $hasher->hash( $this->data[ $this->alias ]['password'] );
	        debug(sprintf('outgoing password was %s', $this->data[ $this->alias ]['password']) );
	    }
	    return true;
	}

	public $hasOne = 'Recovery';


}
