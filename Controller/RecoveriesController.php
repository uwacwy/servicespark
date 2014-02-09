<?php

/*
	Recovery Controller
*/

class RecoveriesController extends AppController
{
	public function beforeFilter()
	{
	    	//parent::beforeFilter();
	    // Allow users to register and logout.
	    $this->Auth->allow('token');
	}

	public function token($token = null)
	{
		// debug($token);

		// hash nth token to get n+1th token
		$hasher = new SimplePasswordHasher();
		$n_plus_one = $hasher->hash($token);

		// find n+1th token in database
		$attempts = $this->Recovery->find( 'first', array( 'conditions' => array('token' => $n_plus_one) ) );

		// debug($attempts);

		// set the controller

		// if there is sent data, check new passwords for equality
		if( $this->request->is('post') && $attempts['Recovery']['token'] != null )
		{
			// we were sent recovery information
			//debug('we were sent post information');
			//debug($this->request->data);
			if( $this->request->data['User']['password_l'] == $this->request->data['User']['password_r'] )
			{
				$this->Recovery->User->id = $attempts['Recovery']['user_id'];

				// debug('Saving changes');

				if( $this->Recovery->User->saveField('password', $this->request->data['User']['password_l']) )
				{
					// debug('password changed');
					$this->Session->setFlash('Your password has been set.  Please login.', 'good');
					$this->Recovery->delete( $attempts['Recovery']['user_id'] ); // delete attempt from database
					$this->redirect( array('controller' => 'users', 'action' => 'login') ); // redirect toward login screen
				}
				else 
				{
					// debug('password not changed');
					$this->Session->setFlash('Password recovery failed.  Your password has not been changed.');
				}

			}
			else
			{
				$this->Session->setFlash('Your passwords didn\'t match.  Please try again.', 'bad');
			}
		}
	}

}