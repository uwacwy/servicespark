<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	/*
		App-Wide Components
		Auth => used for username/password authentication
	*/
	public $helpers = array(
		'Js',
		'Html' => array('className' => 'BoostCake.BoostCakeHtml'),
		'Tm' => array('className' => 'Time'),
		'Duration',
		'Utility',
		'PhpExcel',
		'Form' => array('className' => 'BoostCake.BoostCakeForm'),
		//'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
	);

	public $components = array(
		'Paginator',
		'Session',
		'Auth' => array(
			'loginRedirect' => '/users/activity',
			'logoutRedirect' => '/',
			'flashElement' => 'danger'
		)
	);

	public function beforeFilter()
	{
		$this->set('super_admin', $this->_CurrentUserIsSuperAdmin() );

		$can_supervise = $can_coordinate = false;

		if( $this->Session->check('can_coordinate_exp') && $this->Session->read('can_coordinate_exp') <= time() )
		{
			$this->Session->delete('can_coordinate_exp');
			$this->Session->delete('can_coordinate');
		}

		if( $this->Session->check('can_supervise_exp') && $this->Session->read('can_supervise_exp') <= time() )
		{
			$this->Session->delete('can_supervise_exp');
			$this->Session->delete('can_supervise');
		}

		if( !$this->Session->check('can_coordinate') )
		{
			if( count($this->_GetUserOrganizationsByPermission('write')) > 0 )
				$this->Session->write('can_coordinate', true );
			$this->Session->write('can_coordinate_exp', strtotime('+30 seconds') );
		}

		if( !$this->Session->check('can_supervise') )
		{
			if( count($this->_GetUserOrganizationsByPermission('read')) > 0 )
				$this->Session->write('can_supervise', true );
			$this->Session->write('can_supervise_exp', strtotime('+30 seconds') );
		}

		$this->set('form_defaults', array(
				'class' => 'form',
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control'
				)
			)
		);
	}

	/**
	 * Redirector
	 * 
	 * convenience method to cut down on too-verbose url arrays
	 *
	 **/
	public function _Redirector($prefix, $controller, $action, $id = null)
	{
		$redirect = array(
			$prefix => true,
			'controller' => $controller,
			'action' => $action,
			$id
		);

		if($prefix == null)
		{
			unset( $redirect[$prefix] );
		}

		if($id == null)
		{
			unset( $redirect[$id] );
		}

		return $redirect;
	}

	/**
	 * GetUserOrganizationsByPermission
	 *
	 * @param permission
	 * @param user_id	can be null; null will attempt to get
	 * @return $permission_id => $organization_id indexed pairs
	 */
	public function _GetUserOrganizationsByPermission($permission, $user_id = null)
	{
		if( !$user_id )
		{
			$user_id = AuthComponent::user('user_id');
		}

		$conditions['Permission.user_id'] = $user_id;

		// switch block keeps things nice and tidy
		switch($permission)
		{
			case 'publish':
				$conditions['Permission.publish'] = true;
				break;
			case 'read':
				$conditions['Permission.read'] = true;
				break;
			case 'write':
				$conditions['Permission.write'] = true;
				break;
			case 'all':
				break;
		}
		
		App::uses('Permission', 'Model');
		$permission = new Permission();

		$permissions = $permission->find('list', array('conditions' => $conditions, 'fields' => array('Permission.organization_id') ) );

		return $permissions;
	}


	/**
	 * GetUsersByOrganization
	 *
	 * @param org_id
	 * @return $user_id for organization.
	**/
	public function _GetUsersByOrganization($org_id) 
	{
		App::uses('Permission', 'Model');
		$permission = new Permission();

		$conditions = array(
			'organization_id' => $org_id,
			'publish' => true
		);

		return $permission->find('list', array('conditions' => $conditions, 'fields' => array('Permission.user_id')));
	}


	/**
	 * GetOrganizationEvents
	 *
	 * @param org_id
	 * @return $event_id for organization.
	**/
	public function _GetOrganizationEvents($org_id)
	{
		App::uses('Event', 'Model');
		$event = new Event();

		$conditions = array(
			'organization_id' => $org_id
		);

		return $event->find('list', array('conditions' => $conditions, 'fields' => array('Event.event_id')));
	}


	/**
	 * GetSkillsByEventID
	 *
	 * @param event_id
	 * @return list $skill_id => $skill pairs
	 */
	public function _GetSkillsByEventID($event_id)
	{
		App::uses('Skills', 'Model');
		$skill = new Skill();

		return $skill->find('list', array('conditions' => array('Event.id' => $event_id) ) );

		throw new NotImplementedException('The stub for GetSkillsByEventID is created, but the function has not been implemented yet');
	}

	/**
	 * ProcessAddresses
	 * 
	 * cutting back on code duplication by handling this outside of each individual controller
	 * @param addresses contains an array of addresses
	 * @param model 	rather than redefine or reinstantiate the Address model every time this is called, pass one that is already attached to the controller
	 * @return CakePHP-shaped array of address id's
	 */
	public function _ProcessAddresses($addresses, $model)
	{
		if($addresses != null) 
		{
			foreach($addresses as $address)
			{
				// at a minimum, an address should have a line 1, city, state and zip
				if( 
					!empty( $address['address1'] ) && 
					!empty( $address['city'] ) && 
					!empty( $address['state'] ) &&
					!empty( $address['zip'] ) )
				{
					$model->create();
					$model->save($address);
					// get the address_id for the join table
					$address_ids['Address'][] = $model->id;
				}
			}

			return $address_ids;
		}
		else
		{
			return null;
		}

	}

	/**
	 * ProcessSkills
	 *
	 * processes skills and returns an array of skill id's that can be used for whatever
	 *
	 * @param skills 	an array of skills
	 * @param model 	a pre-instantiated skill model so we don't have to do that inside the function
	 */
	public function _ProcessSkills($skills, $model)
	{
		if( isset($skills['New']) )
		{
			foreach($skills['New'] as $new_skill)
			{
				$save['Skill']['skill'] = $new_skill;
				$model->create();
				$model->save( $save );
				$skill_ids['Skill'][] = $model->id;
			}
		}

		if( isset($skills['Skill']) )
		{
			foreach($skills['Skill'] as $existing)
			{
				$skill_ids['Skill'][] = $existing;
			}
		}

		return $skill_ids;
	}

	public function _CurrentUserIsSuperAdmin()
	{
		App::uses('User', 'Model');

		if( AuthComponent::user('super_admin') === false)
		{
			return false;
		}
		else if ( AuthComponent::user('user_id') != null )
		{
			// always recheck the database if our session says we are a super admin
			$user = new User();
			$user->id = $this->Auth->user('user_id');
			return $user->field('super_admin');
		}
		else
		{
			return false;
		}
	}

	public function _CurrentUserCanPublish($organization_id)
	{
		App::uses('Permission', 'Model');

		if( $this->_CurrentUserIsSuperAdmin() )
		{
			return true;
		}

		$permission = new Permission();
		return $permission->_UserCanPublish( $this->Auth->user('user_id'), $organization_id);
	}

	public function _CurrentUserCanRead($organization_id)
	{
		App::uses('Permission', 'Model');

		if( $this->_CurrentUserIsSuperAdmin() )
		{
			return true;
		}

		$permission = new Permission();
		return $permission->_UserCanRead( $this->Auth->user('user_id'), $organization_id) || $this->_CurrentUserCanWrite($organization_id) || $this->_CurrentUserIsSuperAdmin();
	}

	public function _CurrentUserCanWrite($organization_id)
	{
		App::uses('Permission', 'Model');

		if( $this->_CurrentUserIsSuperAdmin() )
		{
			return true;
		}

		$permission = new Permission();
		return $permission->_UserCanWrite( $this->Auth->user('user_id'), $organization_id) || $this->_CurrentUserIsSuperAdmin();
	}


}
