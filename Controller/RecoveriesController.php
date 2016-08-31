<?php

/*
	Recovery Controller
*/


App::uses('CakeEmail', 'Network/Email');

class RecoveriesController extends AppController
{
	public function beforeFilter()
	{
	    parent::beforeFilter();
	    // Allow users to register and logout.
	    $this->Auth->allow( array('token', 'user') );
	}

	public function user()
	{
		if( $this->Auth->user('user_id') )
		{
			$this->Auth->logout();
		}

		if( $this->request->is('post') )
		{
			if( isset($this->request->data['User']['username']) )
			{
				$user = $this->Recovery->User->find('first',
					array('conditions' => array('User.username' => $this->request->data['User']['username']))
				);

				/*
					Delete Existing Recoveries if they exist
				*/
				if( $this->Recovery->exists($user['User']['user_id']) )
				{
					$this->Recovery->delete($user['User']['user_id']);
				}

				if( isset($user['User']['email']) )
				{
					/*
						hash the existing password a lot of times

						give the user hash n
						but save the hash n+1

						if the user gives us hash n, we hash it and we find hash n+1, we have
						protected our database from prying eyes

						should our database be compromised, it would still be difficult to attack these passwords
					*/

					/*
						Specify the required hashing time in seconds
					*/
					$n_time = 2; // 2 seconds
					$exp_duration = "3 days";

					$hasher = new SimplePasswordHasher();
					$hash = $user['User']['password'];
					$start_t = time();

					for($i = 0; (time() - $start_t) < $n_time; $i++)
					{
						$hash = $hasher->hash($hash);
					}

					$Email = ServiceSparkUtility::GetEmailProvider('production');

					/* send variables to the email generator */
					$token = $hash;
					$expiration = $exp_duration;
					$n_plus_one = $hasher->hash($hash);
					$Email->viewVars( compact('user', 'token', 'expiration', 'n_plus_one') );


					/* send the email */
					$Email->template('PasswordRecovery')
						->emailFormat('text')
						->to( $user['User']['email'], $user['User']['full_name'] )
						->from( 'volunteer@unitedwayalbanycounty.org' )
						->subject( sprintf('Password Recovery for %s', $user['User']['email']) )
						->send();

					$user['Recovery']['user_id'] = $user['User']['user_id'];
					$user['Recovery']['expiration'] = date('Y-m-d H:i:s', strtotime( sprintf('+%s', $exp_duration) ) );
					$user['Recovery']['token'] = $hasher->hash($hash); // here we are saving n+1 = hash(n)

					unset($user['User']);

					if( $this->Recovery->save($user) )
					{
						$this->Session->setFlash( 'An email has been sent to the owner of this account with instructions to complete password recovery.' );
						$this->redirect( array( 'controller' => 'users', 'action' => 'login' ) );
					}
				}
			}
		}
	}

	public function token($token = null)
	{
		// debug($token);

		// hash nth token to get n+1th token
		$hasher = new SimplePasswordHasher();
		$n_plus_one = $hasher->hash($token);
		
		// find n+1th token in database
		$attempts = $this->Recovery->find( 'first', array( 'conditions' => array('token' => $n_plus_one, 'expiration >= Now()') ) );

		if( empty($attempts) ) // a password recovery attempt should yield exactly ONE row
		{
			$this->Session->setFlash('Your password recovery token is invalid.  It may be expired.');
			$this->redirect( array('controller' => 'recoveries', 'action' => 'user') );
		}

		// if there is sent data, check new passwords for equality
		if( $this->request->is('post') && !is_null($attempts['User']['user_id']) )
		{
			if( $this->request->data['User']['password_l'] == $this->request->data['User']['password_r'] )
			{
				$this->Recovery->User->id = $attempts['User']['user_id'];

				if( $this->Recovery->User->saveField('password', $this->request->data['User']['password_l']) )
				{
					$this->Session->setFlash('Your password has been successfully changed!  Please login.');
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
				$this->Session->setFlash('Your passwords didn\'t match.  Please try again.');
			}
		}


	}

}