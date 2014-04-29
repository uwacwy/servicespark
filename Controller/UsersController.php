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
		$this->Auth->allow('register', 'login', 'logout', 'check');
	}

	public function go_login()
	{
		$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false, 'coordinator' => false, 'manager' => false));
	}

	public function volunteer_login()
	{
		$this->go_login();
	}

	public function coordinator_login()
	{
		$this->go_login();
	}
	
	public function supervisor_login()
	{
		$this->go_login();
	}

	public function login()
	{
		if ($this->request->is('post'))
		{
	    	if ($this->Auth->login())
			{
				/*
					Refreshing this session data upon login is crucial
				*/
				$this->Session->delete('can_coordinate');
				$this->Session->delete('can_supervise');

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

	        	return $this->redirect( $this->Auth->redirectUrl() );
	    	}
	    	$this->Session->setFlash(__('Invalid username or password, try again'), 'danger');
		}

		$title_for_layout = sprintf( __('Login to %s'), Configure::read('Solution.name') );
		$this->set( compact('title_for_layout') );
	}

/**
 * logout
 * does not have a view.
 * does not need a title_for_layout
 */
	public function logout()
	{
		$this->Session->delete('can_coordinate');
		$this->Session->delete('can_coordinate_exp');
		$this->Session->delete('can_supervise');
		$this->Session->delete('can_supervise_exp');
		
		return $this->redirect( $this->Auth->logout() );
	}
/**
	index methods
*/
	public function go_index()
	{

	}

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

		$this->set('title_for_layout', __('Listing Users') );
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
	 * TEMPORARILY DISABLED!
	 *
	 * @throws NotFoundException
	 * @param string $username
	 * @return void
	 */
	// public function view($username)
	// {
	// 	/*
	// 		cake's magic methods let us use cool methods to find stuff
	// 	*/
	// 	$user_id = $this->User->findByUsername($username);

	// 	debug($user_id);

	// 	$options = array(
	// 		'conditions' => array('User.username'  => $username),
	// 		'contain' => array(
	// 			'Recovery', 
	// 			'Permission' => array('Organization'), // without this containable behavior, cake would have sent the related User back again
	// 			'Skill',
	// 			'Address',
	// 			'Time' => array('Event')
	// 		)
	// 	);

	// 	$user = $this->User->find('first', $options);

	// 	$this->set( compact('user') );
	// }

	public function check()
	{

		header('Content-type: application/json');

		$username = ( isset($this->params->query['username']) )? $this->params->query['username']: '';
		$conditions = array('User.username' => $this->params->query['username']);
		$count = ( $this->User->find('count', array('conditions' => $conditions) ) );

		$valid = true;
		if( $count == 1)
		{
			$valid = false;
		}
		$this->autoRender = false;

		echo json_encode( compact('valid') );

	}


/**
	register methods
*/

	public function go_register()
	{
		$this->redirect( array('go' => false, 'action' => 'register') );
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
		//unset( $this->request->data['User']['password'] );


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
				$this->Session->setFlash( __('The passwords did not match.  Please try again.'), 'danger' );
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

			$address_ids = $skill_ids = null;

			if( isset( $this->request->data['Address']) )
				$address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->User->Address);
			
			if( isset($this->request->data['Skill']) )
				$skill_ids = $this->_ProcessSkills($this->request->data['Skill'], $this->User->Skill);

			unset( $entry['Address'], $entry['Skill'] );

			$entry['Address'] = $address_ids;
			$entry['Skill'] = $skill_ids;

			//return;

			$this->User->create();

			if ( $this->User->save($entry) )
			{
				$user = $this->User->find('first', array('conditions' => array('User.user_id' => $this->User->id) ) );
				$Email = new CakeEmail();
				$Email->viewVars( compact('entry') );
				$Email->template('NewUser')
						->emailFormat('text')
						->to( $user['User']['email'], __('%s %s', $user['User']['first_name'], $user['User']['last_name']) )
						->from( 'volunteer@unitedwayalbanycounty.org' )
						->subject( __('[%1$s] Welcome to %1$s, %2$s!', Configure::read('Solution.name'), $user['User']['first_name']) )
						->send();

				$this->Session->setFlash( __('This account has been created.  Login with your username and password.'), 'success' );
				return $this->redirect( array('controller' => 'users', 'action' => 'login') );

			}
			else
			{
				$this->Session->setFlash( __('The user could not be saved. Please, try again.'), 'danger');
			}
		}

		$this->set( 'title_for_layout', __('Create An Account') );

	}


	public function go_profile()
	{
		return $this->redirect( array('go' => false, 'action' => 'profile') );
	}

	public function coordinator_profile() { return $this->go_profile(); }
	public function supervisor_profile() { return $this->go_profile(); }
	public function volunteer_profile() { return $this->go_profile(); }
	public function admin_profile() { return $this->go_profile(); }


	/**
	 * profile method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function profile()
	{
		unset( $this->request->data['User']['password'], $this->request->data['User']['username'] );

		$user_id = $this->Auth->user('user_id');


		if ( !$this->User->exists($user_id) )
		{
			throw new NotFoundException( __('Invalid user') );
		} 

		if ($this->request->is( array('post', 'put') ) )
		{
			if( $this->request->data['User']['password_l'] != "")
			{
				if( $this->request->data['User']['password_l'] != $this->request->data['User']['password_r'] )
				{
					$this->Session->setFlash( __('The passwords did not match.  They were not changed.'), 'warning' );
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
			$skill_ids = $address_ids = null;

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
				$this->Session->setFlash( __('Your profile has been updated.'), 'success' );
				$this->redirect( $this->_Redirector(null, 'users', 'activity') );
			}
			else
			{
				$this->Session->setFlash( __('The user could not be saved. Please, try again.'), 'danger' );
				
			}

			// returning the entire dictionary of skills is clunky
			if( isset($skill_ids) )
			{
				$conditions = array('Skill.skill_id' => $skill_ids['Skill']);
				$relevant_skills = $this->User->Skill->find('list', array('conditions' => $conditions) );
				$this->set( compact('relevant_skills') );
			}
		} // end if post|delete


		$options = array('conditions' => array('User.user_id' => $user_id));
		$this->request->data = $this->User->find('first', $options);
		$this->set( 'title_for_layout', __('Editing My Profile') );
		$this->set( compact('addresses') ); // unneeded?
	}

	public function go_activity()
	{
		return $this->redirect( array('go' => false, 'action' => 'activity') );
	}

	public function coordinator_activity() { return $this->go_activity(); }
	public function supervisor_activity() { return $this->go_activity(); }
	public function volunteer_activity() { return $this->go_activity(); }
	public function admin_activity() { return $this->go_activity(); }


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

		if( !in_array($period, array('month', 'year', 'ytd', 'all', 'custom') ) )
			$period = "month";

		$order = array(
			'Event.stop_time DESC'
		);
		$conditions['Time.user_id'] = $this->Auth->user('user_id');

		switch($period)
		{
			case 'month':
				$sub_title = "Past Month";
				$conditions['Time.start_time >='] = date($sql_date_fmt, strtotime('1 month ago') );
				break;
			case 'year':
				$sub_title = "Past Year";
				$conditions['Time.start_time >='] = date($sql_date_fmt, strtotime('1 year ago') );
				break;
			case 'ytd':
				$sub_title = "Year-To-Date";
				$conditions['Time.start_time >='] = date($sql_date_fmt, mktime(0,0,0,1,1, date('Y') ) );
				break;
			case 'all':
				$sub_title = "All Activity";
				break;
			case 'custom':
				$sub_title = "Custom Report";
				if( isset($this->request->query['start']) && isset($this->request->query['stop']) )
				{
					$start = $this->request->query['start'];
					$stop = $this->request->query['stop'];
					$date_start = mktime(0, 0, 0, $start['month'], $start['day'], $start['year']);
					$date_stop = mktime(0, 0, 0, $stop['month'], $stop['day'], $stop['year']);
					$conditions['Time.start_time >='] = date($sql_date_fmt, $date_start);
					$conditions['Time.stop_time <='] = date($sql_date_fmt, $date_stop);

					$sub_title = sprintf("%s - %s", date('F j, Y', $date_start), date('F j, Y', $date_stop) );

					$this->request->data['Custom']['start'] = $start;
					$this->request->data['Custom']['stop'] = $stop;

					
				}
				break;
		}

		$this->Paginator->settings['limit'] = 10;
		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['contain'] = 'Event';

		$time_data = $this->Paginator->paginate('Time');

		$fields = array(
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as PeriodTotal'
		);
		$period_total = $this->User->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
		$this->set( compact('period', 'time_data', 'period_total') );
	

		// connected organizations
		$contain = array('Permission.permission_id');
		$publish_conditions = array(
			'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('publish')
		);

		$publishing = $this->User->Permission->Organization->find('all', array('conditions' => $publish_conditions, 'contain' => array() ) );

		$supervise_conditions = array(
			'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('read')
		);
		$supervising = $this->User->Permission->Organization->find('all', array('conditions' => $supervise_conditions, 'contain' => array() ) );

		$coordinate_conditions = array(
			'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('write')
		);
		$coordinating = $this->User->Permission->Organization->find('all', array('conditions' => $coordinate_conditions, 'contain' => array() ) );
		$this->set( compact('publishing', 'supervising', 'coordinating') );


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

		$title_for_layout = sprintf( "%s &ndash; %s", __('My Service Activity'), $sub_title);
		$this->set( compact('title_for_layout') );



	}

	/*
		exports the user's time data to an Excel spreadsheet
	*/
	public function report($period = null)
	{
		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event' => array('Organization') );

		$this->layout = null;

		if( !in_array($period, array('month', 'year', 'ytd', 'custom', 'all') ) )
			$period = "month";

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
			case 'all':
				break;
			case 'custom':
				if( isset($this->request->query['start']) && isset($this->request->query['stop']) )
				{
					$start = $this->request->query['start'];
					$stop = $this->request->query['stop'];
					$date_start = mktime(0, 0, 0, $start['month'], $start['day'], $start['year']);
					$date_stop = mktime(0, 0, 0, $stop['month'], $stop['day'], $stop['year']);
					$conditions['Time.start_time >='] = date($sql_date_fmt, $date_start);
					$conditions['Time.stop_time <='] = date($sql_date_fmt, $date_stop);

					$sub_title = sprintf("%s - %s", date('F j, Y', $date_start), date('F j, Y', $date_stop) );					
				}
				break;
		}

		$time_data = $this->User->Time->find('all', array('conditions' => $conditions, 'contain' => $contain, 'order' => $order) );

		$this->set( compact('time_data', 'period') );


	}


	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function volunteer_delete($user_id = null)
	{
		$user_id = $this->Auth->user('user_id');

		if (!$this->User->exists($user_id))
		{
				throw new NotFoundException( __('Invalid user') );

		}

		$this->request->onlyAllow('post', 'delete');

		if ( $this->User->delete($user_id) )
		{
			$this->Session->setFlash(__('The user has been deleted.'));

			/*
				In order to preserve state, the user will be logged out if they delete their account
			*/
			if( $this->Auth->user('user_id') == $id )
			{
				$this->Session->setFlash( __('Your account was deleted and you have been logged out.'), 'success' );
    			$this->redirect( $this->Auth->logout() );
    		}
		}
		else
		{
			$this->Session->setFlash( __('The user could not be deleted. Please, try again.'), 'danger' );
		}
		return $this->redirect( array('action' => 'index') );
	}
}
