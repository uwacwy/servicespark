<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class VerificationsController extends AppController
{
	public function beforeFilter()
	{
		parent::beforeFilter();
		// Allow users to register and logout.
		$this->Auth->allow('email', 'verify');
	}
	
	public function email()
	{
		$this->set('title_for_layout', __("Check Your Email") );
	}
	
	public function verify($token)
	{
		$verification = $this->Verification->findByToken($token);
		
		if( empty($verification) )
		{
			throw new NotFoundException( __("There is nothing here to show you.") );
		}
		elseif( $this->request->is('post') )
		{
			if( $this->request->data['User']['password_l'] == $this->request->data['User']['password_r'] )
			{
				$this->Verification->User->id = $verification['User']['user_id'];
				$this->Verification->User->saveField('verified_email', 1);
				$this->Verification->User->saveField('password', $this->request->data['User']['password_l']);
				
				$this->Verification->delete( $verification['Verification']['verification_id'] );
				
				$this->Session->setFlash( __("Success!  Please login with your username and password."), 'success' );
				return $this->redirect( array('controller' => 'users', 'action' => 'login') );
			}
		}
		
		$this->set( compact('verification') );
		
	}
}