<?php
App::uses('AppModel', 'Model');
App::uses('Auth', 'Component');

class Permission extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */

	public $primaryKey = 'permission_id';

	public $actsAs = array('Containable');


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Organization' => array(
			'className' => 'Organization',
			'foreignKey' => 'organization_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/*
		This will delete existing records of permission
		-> this is a kludge since CakePHP does not have comprehensive support for COMPOUND primary keys
	*/
	public function beforeSave($options)
	{
		// if user-organization pair exists
		$conditions = array(
			'Permission.user_id' => $this->data[ $this->alias ]['user_id'],
			'Permission.organization_id' => $this->data[ $this->alias ]['organization_id']
		);
		$existing = $this->field('permission_id', $conditions);

		if($existing)
		{
			$this->delete($existing);
		}

		return true;
	}


	public function _UserCanPublish($user_id, $organization_id)
	{
		return $this->_GetPermissionByUserAndOrganization('publish', $user_id, $organization_id);
	}

	public function _UserCanRead($user_id, $organization_id)
	{
		return $this->_GetPermissionByUserAndOrganization('read', $user_id, $organization_id);
	}

	public function _UserCanWrite($user_id, $organization_id)
	{
		return $this->_GetPermissionByUserAndOrganization('write', $user_id, $organization_id);
	}

	private function _GetPermissionByUserAndOrganization($permission, $user_id, $organization_id)
	{
		$conditions = array(
			'Permission.user_id' => $user_id,
			'Permission.organization_id' => $organization_id
		);

		return $this->field($permission, $conditions); // returns false when a record doesn't exist.
	}
}
