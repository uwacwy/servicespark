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
		//'Form' => array('className' => 'BoostCake.BoostCakeForm'),
		//'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
	);

	public $components = array(
		'Session',
		'Auth' => array(
			'loginRedirect' => '/users/activity',
			'logoutRedirect' => array(
				'controller' => 'pages',
				'action' => 'display',
				'home'
			)
		)
	);

	public function beforeFilter()
	{
		$this->set('super_admin', $this->_CurrentUserIsSuperAdmin() );
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
	 * ProcessAddresses
	 * 
	 * cutting back on code duplication by handling this outside of each individual controller
	 * @param addresses contains an array of addresses
	 * @param model 	rather than redefine or reinstantiate the Address model every time this is called, pass one that is already attached to the controller
	 * @return CakePHP-shaped array of address id's
	 */
	public function _ProcessAddresses($addresses, $model)
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
			$user->id = $this->Auth->user('id');
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

		$permission = new Permission();
		return $permission->_UserCanPublish( $this->Auth->user('user_id'), $organization_id);
	}

	public function _CurrentUserCanRead($organization_id)
	{
		App::uses('Permission', 'Model');

		$permission = new Permission();
		return $permission->_UserCanRead( $this->Auth->user('user_id'), $organization_id) || $this->_CurrentUserIsSuperAdmin();
	}

	public function _CurrentUserCanWrite($organization_id)
	{
		App::uses('Permission', 'Model');

		$permission = new Permission();
		return $permission->_UserCanWrite( $this->Auth->user('user_id'), $organization_id) || $this->_CurrentUserIsSuperAdmin();
	}


}
