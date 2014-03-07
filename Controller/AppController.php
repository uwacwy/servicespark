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
		//'Html' => array('className' => 'BoostCake.BoostCakeHtml'),
		//'Form' => array('className' => 'BoostCake.BoostCakeForm'),
		//'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
	);

	public $components = array(
		'Session',
		'Auth' => array(
			'loginRedirect' => array(
				'controller' => 'users',
				'action' => 'view'
			),
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
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control'
				)
			)
		);
	}

	public function _CurrentUserIsSuperAdmin()
	{
		App::uses('User', 'Model');
		
		if( AuthComponent::user('user_id') != null )
		{
			$user = new User();

			$user->user_id = $this->Auth->user('id');
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
		return $permission->_UserCanRead( $this->Auth->user('user_id'), $organization_id);
	}

	public function _CurrentUserCanWrite($organization_id)
	{
		App::uses('Permission', 'Model');

		$permission = new Permission();
		return $permission->_UserCanWrite( $this->Auth->user('user_id'), $organization_id);
	}


}
