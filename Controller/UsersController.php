<?php
App::uses('AppController', 'Controller');
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


/**
 * login method
 */

public function beforeFilter()
{
    	//parent::beforeFilter();
    // Allow users to register and logout.
    $this->Auth->allow('add', 'logout', 'recover');
}

public function login()
{
	if ($this->request->is('post'))
	{
    	if ($this->Auth->login())
		{
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

		/*
			This is how we send data to the View.  Right now, our paginator is handling the actual querying, so there isn't much to see.

			the $this->set() method takes two parameters.
			@param string $name
				specifies the name of the variable that will be accessible in the view.  For example this 'users' will create a variable in the view called $users
			@param array $data
				this is the value of your variable.  In this case, this is data straight from our database.
		*/
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
 * add method
 *
 * @return void
 */
	public function add()
	{
		if ($this->request->is('post'))
		{
			if( $this->request->data['User']['password'] != $this->request->data['User']['password_confirmation'] )
			{
				$this->Session->setFlash( __('The passwords did not match.  Please try again.') );
				unset(
					$this->request->data['User']['password'], 
					$this->request->data['User']['password_confirmation'] ); // this will blank the fields

				return false; // stops remaining processing
			}

			$this->request->data['Skill'][0]['skill'] = "CakePHP Programming";


			$this->User->create();
			if ($this->User->saveAll($this->request->data))
			{
					$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
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

			if( $this->request->data['User']['password'] != "")
			{
				if( $this->request->data['User']['password'] != $this->request->data['User']['password_confirmation'] )
				{
					$this->Session->setFlash( __('The passwords did not match.  They were not changed.') );
					unset(
						$this->request->data['User']['password'], 
						$this->request->data['User']['password_confirmation'] ); // this will blank the fields
				}
			}

			if( !empty($this->data['User']['skills_csv']) )
			{
				$skills = split(',', $this->data['User']['skills_csv']);

				foreach ($skills as $skill)
				{
					$skill_meta = $this->get_skills($skill);

					debug($skill_meta);

					if( empty($skill_meta) )
					{
						$saveSkills[] = array(
							'User' => array('user_id' => $id),
							'Skill' => array('skill' => trim($skill) )
						);
					}
					else
					{

					}
					
				}
			}

			debug($saveSkills);

			return;

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

	public function recover($token = null)
	{
		if( $this->request->is( array('post') ) )
		{
			if( isset($this->request->data['User']['username']) )
			{
				$user = $this->User->find('first', 
					array('conditions' => array('User.username' => $this->request->data['User']['username']) ));

				debug($user);

				if( isset($user['Recovery']) )
				{
					debug('deleted existing recovery');
					$this->User->Recovery->delete($user['Recovery']['user_id']);
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

					$nhashes = 9999;

					$hasher = new SimplePasswordHasher();
					$hash = $user['User']['password'];

					for($i = 0; $i < $nhashes; $i++)
					{
						$hash = $hasher->hash($hash);
					}

					debug('email user before hash(n) is computed');

					$user['Recovery']['expiration'] = date('Y-m-d H:i:s', strtotime('+3 days'));
					$user['Recovery']['token'] = $hasher->hash($hash); // here we are saving n+1 = hash(n)

					debug($user);

					unset($user['User']['password']); // don't change the password yet

					$this->User->saveAll($user);
				}
			}
		}

		if( $this->request->is( array('get') ) )
		{
			debug('we are validating a token');
		}
	}

	/*
		get_skills
		--
		searches for related skills
	*/
	public function get_skills($skill)
	{
		return $this->User->Skill->find('list',
			array('conditions' => 
				array('skill' => trim($skill) )
			)
		);
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
		}
		else
		{
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
