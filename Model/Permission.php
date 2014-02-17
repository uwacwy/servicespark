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

		return $this->field($permission, $conditions);
	}
}
