<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
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

	public $actsAs = array(
		'Containable',
		'Notification.Notifiable' => array(
			'subjects' => array('User', 'Event', 'Skill', 'Organization', 'Time', 'Rsvp')
		)
	);

	public $virtualFields = array(
		'full_name' => 'CONCAT(User.first_name, " ", User.last_name)',
		'account_age' => 'TIMESTAMPDIFF( MINUTE, User.created, Now() )',
		'push_key' => 'CONCAT(User.user_id, User.password)'
	);

	public $validate = array(
		'username' => array(
			'That username is taken.' => array(
				'rule' => 'isUnique'
			),
			'Your username must be at least 3 characters with no spaces.' => array(
				'rule' => '/^[a-zA-Z0-9_\.]{3,}$/i'
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
		'Address' => array(
			'className' => 'Address',
			'joinTable' => 'addresses_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'address_id'
		)
	);

	public $hasMany = array(
		
		'TimeComment' => array(
			'dependent' => true
		),
		'Permission' => array(
			'dependent' => true // when the User is deleted, all Permission entries are deleted
		),
		'Rsvp' => array(
			'dependent' => true
		),
		'Time' => array(
			'dependent' => true // when the User is deleted, all Time entries are deleted
		),
		'Comment' => array(
			'dependent' => true
		),
		'UserMeta' => array(
			'dependent' => true
		),
		'Email' => array(
			'dependent' => true
		),
		'Mention' => array(
			'dependent' => true
		),
		'Verification' => array(
			'dependent' => true
		),
		'SkillUser' => array(
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
	
	public function get_meta( $key, $user_id = null, $type = 'all', $fields = array('value') )
	{
		if($user_id == null)
			$user_id = AuthComponent::user('user_id');
			
		$result = $this->UserMeta->find($type, array(
			'conditions' => array(
				'user_id' => $user_id,
				'key' => $key
			),
			'fields' => 'UserMeta.value',
			'contain' => array()
		));
		
		return Hash::map($result, '{n}.UserMeta.value', 'json_decode' );
	}
	
	public function add_meta($key, $value, $user_id = null)
	{
		if($user_id == null)
			$user_id = AuthComponent::user('user_id');
			
		$value = json_encode($value);
		
		return $this->UserMeta->save( array(
			'user_id' => $user_id,
			'key' => $key,
			'value' => $value
		) );
	}

}
