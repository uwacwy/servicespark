<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array(
		'Paginator'
	);


	public function beforeFilter()
	{
		parent::beforeFilter();
		// Allow users to register and logout.
		$this->Auth->allow('register', 'login', 'logout');
	}

	public function login()
	{
		if ($this->request->is('post'))
		{
	    	if ($this->Auth->login())
			{
				/*
					delete recoveries
					--
					if a user successfully logs in, they should have any password
					recovery attempts deleted.  Users are told in the password recovery
					email that they can simply login to cancel any outstanding recovery
					requests.
				*/
				if( $this->User->Recovery->exists( $this->Auth->user('user_id') ) )
				{
					$this->User->Recovery->delete( $this->Auth->user('user_id') );
				}	

	        	$this->redirect($this->Auth->redirect());
	    	}
	    	$this->Session->setFlash(__('Invalid username or password, try again'));
		}
	}

	public function logout()
	{
		return $this->redirect( $this->Auth->logout() );
	}
/**
	index methods
*/

	public function admin_index()
	{
		/*
			this fall-through pattern will make it easy to get users to the appropriate level of permissions
		*/
		if( $this->_CurrentUserIsSuperAdmin() )
		{
			$this->User->recursive = 0;
			$this->set('users', $this->Paginator->paginate());
		}
		else
		{
			return $this->redirect( array('controller' => 'user', 'action' => 'index', 'prefix' => 'coordinator') );
		}
	}

	public function coordinator_index()
	{

	}

	public function supervisor_index()
	{

	}

	public function volunteer_index()
	{

	}

	public function index()
	{

	}

/**
	view methods
*/
	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $username
	 * @return void
	 */
	public function view($username)
	{
		/*
			cake's magic methods let us use cool methods to find stuff
		*/
		$user_id = $this->User->findByUsername($username);

		debug($user_id);

		$options = array(
			'conditions' => array('User.username'  => $username),
			'contain' => array(
				'Recovery', 
				'Permission' => array('Organization'), // without this containable behavior, cake would have sent the related User back again
				'Skill',
				'Address',
				'Time' => array('Event')
			)
		);

		$user = $this->User->find('first', $options);

		$this->set( compact('user') );
	}


/**
	register methods
*/

	public function admin_register()
	{
		if( $this->_CurrentUserIsSuperAdmin() )
		{

		}
		else
		{
			return $this->redirect( array('controller' => 'user', 'action' => 'register', 'prefix' => 'coordinator') );
		}
	}


	/**
	 * register method
	 *
	 * @return boolean true on success; false on failure
	 *
	 * @uses $this->request->data
	 * @uses User.password
	 * @sends void
	 * 
	 * This controller does not set any variables for the view.
	 *
	 */
	public function register()
	{
		unset( $this->request->data['User']['password'] );

		if ($this->request->is('post'))
		{
			/*
				Process the password.
				--
				hashing is handled by the model
				--
				if password_l and password_r match
					set User.password
				else
					blank password_l and password_r and return false so the form is displayed again
			*/
			if( $this->request->data['User']['password_l'] != $this->request->data['User']['password_r'] )
			{
				$this->Session->setFlash( __('The passwords did not match.  Please try again.') );
				unset(
					$this->request->data['User']['password_l'], 
					$this->request->data['User']['password_r']
				); // this will blank the fields

				return false; // stops remaining processing
			}
			else
			{
				$this->request->data['User']['password'] = $this->request->data['User']['password_l'];
			}


			// create address entry
			foreach($this->request->data['Address'] as $address)
			{
				// at a minimum, an address should have a line 1, city, state and zip
				if( 
					!empty( $address['address1'] ) && 
					!empty( $address['city'] ) && 
					!empty( $address['state'] ) &&
					!empty( $address['zip'] ) )
				{
					$this->User->Address->create();
					$this->User->Address->save($address);
					// get the address_id for the join table
					$address_ids['Address'][] = $this->User->Address->id;
				}
			}

			unset( $this->request->data['Address'] );
			$this->request->data['Address'] = $address_ids;



			$this->User->create();

			if ( $this->User->save($this->request->data) )
			{
				$this->Session->setFlash( __('This account has been created.  Login with your username and password.') );
				$this->redirect(array('action' => 'login'));

			}
			else
			{
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}


	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null)
	{
		unset( $this->request->data['User']['password'] );

		if ( ! $this->_CurrentUserIsSuperAdmin() )
		{
			// only super administrators can edit other users
			$id = $this->Auth->user('user_id');
		}
		elseif ( $id != $this->Auth->user('user_id') )
		{
			throw new ForbiddenException('You do not have permission to modify other users');
		}

		if (!$this->User->exists($id))
		{
			throw new NotFoundException( __('Invalid user') );
		} 

		if ($this->request->is( array('post', 'put') ) )
		{
			if( $this->request->data['User']['password_l'] != "")
			{
				if( $this->request->data['User']['password_l'] != $this->request->data['User']['password_r'] )
				{
					$this->Session->setFlash( __('The passwords did not match.  They were not changed.') );
					unset(
						$this->request->data['User']['password_l'], 
						$this->request->data['User']['password_r'] ); // this will blank the fields
				}
				else
				{
					$this->request->data['User']['password'] = $this->request->data['User']['password_l'];
				}
			}
			
			// create address entry
			foreach($this->request->data['Address'] as $address)
			{
				// at a minimum, an address should have a line 1, city, state and zip
				if( 
					!empty( $address['address1'] ) && 
					!empty( $address['city'] ) && 
					!empty( $address['state'] ) &&
					!empty( $address['zip'] ) )
				{
					$this->User->Address->create();
					$this->User->Address->save($address);
					// get the address_id for the join table
					$address_ids['Address'][] = $this->User->Address->id;
				}
			}

			unset( $this->request->data['Address'] );

			if( !empty($address_ids) )
				$this->request->data['Address'] = $address_ids;
			
			if ( $this->User->save($this->request->data) )
			{
				$this->Session->setFlash( __('The user has been saved.') );
				debug($this->request->data);
				//$this->redirect( array('action' => 'index') );
			}
			else
			{
				$this->Session->setFlash( __('The user could not be saved. Please, try again.') );

			}
		}
		
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->request->data = $this->User->find('first', $options);
		$this->set(compact('addresses'));
	}


	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists())
		{
				throw new NotFoundException( __('Invalid user') );

		}

		$this->request->onlyAllow('post', 'delete');

		if ($this->User->delete())
		{
			$this->Session->setFlash(__('The user has been deleted.'));

			/*
				In order to preserve state, the user will be logged out if they delete their account
			*/
			if( $this->Auth->user('user_id') == $id )
			{
				$this->Session->setFlash(__('Your account was deleted and you have been logged out.'));
    			$this->redirect( $this->Auth->logout() );
    		}
		}
		else
		{
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect( array('action' => 'index') );
	}
}
