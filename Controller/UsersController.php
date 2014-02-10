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
		// Allow users to register and logout.
		$this->Auth->allow('register', 'login', 'logout');

		/*
			Since we always process the passwords before we save it, unset
		*/
		unset( $this->request->data['User']['password'] );
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

	        	return $this->redirect($this->Auth->redirect());
	    	}
	    	$this->Session->setFlash(__('Invalid username or password, try again'));
		}
	}

	public function logout()
	{
		return $this->redirect($this->Auth->logout());
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index()
	{
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null)
	{
		if ( !$this->User->exists($id) )
		{
				throw new NotFoundException(__('Invalid user'));
		}
		
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

	/**
	 * register method
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->request->is('post'))
		{
			if( $this->request->data['User']['password_l'] != $this->request->data['User']['password_r'] )
			{
				$this->Session->setFlash( __('The passwords did not match.  Please try again.') );
				unset(
					$this->request->data['User']['password_l'], 
					$this->request->data['User']['password_r'] ); // this will blank the fields

				$this->set('test_password_match', false);

				return false; // stops remaining processing
			}
			else
			{
				$this->set('test_password_match', true);
				$this->request->data['User']['password'] = $this->request->data['User']['password_l'];
			}


			$this->User->create();
			if ($this->User->save($this->request->data))
			{
				$this->Session->setFlash(__('This account has been created.'));
				return $this->redirect(array('action' => 'login'));
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
		if (!$this->User->exists($id))
		{
			throw new NotFoundException(__('Invalid user'));
		}

		if ($this->request->is(array('post', 'put')))
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

			if ($this->User->save($this->request->data)  && $this->User->saveAll($saveSkills) )
			{
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		else
		{
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
			unset(
				$this->request->data['User']['password']
			);
		}
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
				throw new NotFoundException(__('Invalid user'));
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
    			return $this->redirect($this->Auth->logout());
    		}
		}
		else
		{
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
