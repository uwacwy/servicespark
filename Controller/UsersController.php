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
			$entry = $this->request->data;
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
			if( $entry['User']['password_l'] != $entry['User']['password_r'] )
			{
				$this->Session->setFlash( __('The passwords did not match.  Please try again.') );
				unset(
					$entry['User']['password_l'], 
					$entry['User']['password_r']
				); // this will blank the fields

				return false; // stops remaining processing
			}
			else
			{
				$entry['User']['password'] = $entry['User']['password_l'];
			}

			$address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->User->Address);

			unset( $entry['Address'] );
			$entry['Address'] = $address_ids;

			return;

			$this->User->create();

			if ( $this->User->save($entry) )
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
	 * profile method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function profile($id = null)
	{
		unset( $this->request->data['User']['password'] );

		$id = $this->Auth->user('user_id');

		if( $id == null )
		{
			throw new ForbiddenException( __('Please login before attempting to edit your profile.') );
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

			/*
				abstracted!
				--
				many methods use the skill and address parsers;  this will make them easier to maintain
			*/
			if( isset($this->request->data['Skill']) )
				$skill_ids = $this->_ProcessSkills($this->request->data['Skill'], $this->User->Skill);
			if( isset($this->request->data['Address']) )
				$address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->User->Address);
			
			unset( $this->request->data['Address'], $this->request->data['Skill'] );

			if( !empty($address_ids) )
				$this->request->data['Address'] = $address_ids;
			if( !empty($skill_ids) )
				$this->request->data['Skill'] = $skill_ids;

			
			
			if ( $this->User->save($this->request->data) )
			{
				$this->Session->setFlash( __('The user has been saved.') );
				//debug($this->request->data);
				$this->redirect( array('controller' => 'pages', 'action' => 'index', 'admin' => false, 'coordinator' => false, 'volunteer' => false) );
			}
			else
			{
				$this->Session->setFlash( __('The user could not be saved. Please, try again.') );
				
			}

			// returning the entire dictionary of skills is clunky
			if( isset($skill_ids) )
			{
				$conditions = array('Skill.skill_id' => $skill_ids['Skill']);
				$relevant_skills = $this->User->Skill->find('list', array('conditions' => $conditions) );
				$this->set( compact('relevant_skills') );
			}
		}


		$options = array('conditions' => array('User.user_id' => $id));
		$this->request->data = $this->User->find('first', $options);
		$this->set(compact('addresses'));
	}

	/**
	 * activity method
	 *
	 * @throws 
	 * @return void
	 */
	public function activity($period = null)
	{
		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		if( $period != null)
		{
			$order = array(
				'Event.stop_time DESC'
			);
			$conditions['Time.user_id'] = $this->Auth->user('user_id');

			switch($period)
			{
				case 'month':
					$conditions['Time.start_time >='] = date($sql_date_fmt, strtotime('1 month ago') );
					break;
				case 'year':
					$conditions['Time.start_time >='] = date($sql_date_fmt, strtotime('1 year ago') );
					break;
				case 'ytd':
					$conditions['Time.start_time >='] = date($sql_date_fmt, mktime(0,0,0,1,1, date('Y') ) );
					break;
				case 'custom':
					break;
			}

			$time_data = $this->User->Time->find('all', array('conditions' => $conditions, 'contain' => $contain, 'order' => $order) );
			$this->set( compact('time_data', 'period') );

		}


			// summary all time
			$conditions = array(
				'Time.user_id' => $this->Auth->user('user_id')
			);
			$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as UserAllTime'
			);
			$summary_all_time = $this->User->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_all_time') );

			// summary month
			$conditions = array(
				'Time.user_id' => $this->Auth->user('user_id'),
				'Time.start_time >=' => date($sql_date_fmt, strtotime('1 month ago') )
			);
			$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as UserPastMonth'
			);
			$summary_past_month = $this->User->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_past_month') );

			// summary year
			$conditions = array(
				'Time.user_id' => $this->Auth->user('user_id'),
				'Time.start_time >=' => date($sql_date_fmt, strtotime('1 year ago') )
			);
			$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as UserPastYear'
			);
			$summary_past_year = $this->User->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_past_year') );

			// year-to-date
			$conditions = array(
				'Time.user_id' => $this->Auth->user('user_id'),
				'Time.start_time >=' => date($sql_date_fmt, mktime(0,0,0,1,1, date('Y') ) )
			);
			$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as UserYTD'
			);
			$summary_ytd = $this->User->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_ytd') );



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
