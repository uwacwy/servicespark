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
		$this->Auth->allow('register', 'login', 'logout', 'check', 'avatar');
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
				
				ServiceSparkUtility::log( $this->Auth->user() );
				
				$this->Session->write('push_channel', ServiceSparkUtility::Hash(
					$this->Auth->user('push_key'),
					5
				));
				
				$this->User->add_meta('login', array(
					'ip_address' => $_SERVER['REMOTE_ADDR']
				));

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
				
				$dest = $this->Auth->redirectUrl();
				if( $this->Auth->user('account_age') < (3 * 24 * 60) )
					$dest = array('controller' => 'users', 'action' => 'welcome');

	        	return $this->redirect( $dest );
	    	}
	    	$this->Session->setFlash(__('Invalid username or password, try again'), 'danger');
		}

		$title_for_layout = sprintf( __('Login to %s'), Configure::read('Solution.name') );
		$this->set( compact('title_for_layout') );
	}
	
	public function welcome()
	{
		$user_id = $this->Auth->user('user_id');
		
		$conditions = array(
			'User.user_id' => $user_id
		);
		$contain = array(
			'SkillUser' => array('Skill'),
			'Permission.follow=1' => array('Organization')
		);
		
		$skill_count = $this->User->SkillUser->Skill->find('count');
		$organization_count = $this->User->Permission->Organization->find('count');
		
		$user = $this->User->find('first', compact('conditions', 'contain') );
		
		$this->set('user', $user);
		$this->set('skill_count', $skill_count);
		$this->set('organization_count', $organization_count);
		
		$this->set('render_container', false);

	}
	
	/**
	 * Attaches a skill to a user's profile
	 * 
	 */
	public function api_skill_attach()
	{
		$this->response->type('json');
		$this->response->statusCode(200);
		$this->response->body("");
		if( $this->request->is('post') )
		{
			$user_id = $this->Auth->user('user_id');
			$skill_id = $this->request->data['skill_id'];
			
			$conditions = array(
				'SkillUser.user_id' => $user_id,
				'SkillUser.skill_id' => $skill_id
			);
			$attached_count = $this->User->SkillUser->find('count', array('conditions' => $conditions) );
			
			if( $attached_count == 0 )
			{
				$skill_user = array(
					'skill_id' => $skill_id,
					'user_id' => $user_id
				);
				if( $this->User->SkillUser->save($skill_user) )
				{
					$this->response->statusCode(201);
					$this->response->body( json_encode(array(
						'attached' => true,
						'created' => true,
						'modified' => false,
						'skill_user_id' => $this->User->SkillUser->id
					)));
				}
			}
			else
			{
				$this->response->body( json_encode(array(
					'attached' => true,
					'created' => false,
					'modified' => false
				)));
			}
		}
		return $this->response;
	}
	
	public function skills()
	{
		
	}
	
	public function api_skills($count = 5, $exclude = null)
	{
		$user_id = $this->Auth->user('user_id');
		
		if( !$user_id )
			throw new ForbiddenException('This endpoint requires authentication.');
			
		if( !is_numeric($count) )
			$count = 5;
		
		$exploded_exclude = array();
		if( !is_null($exclude) )
			$exploded_exclude = explode(",", $exclude);
			
		// returns $skill_user_id => $skill_id
		$existing_skill_ids = $this->User->SkillUser->find('list', array(
			'fields' => array(
				'skill_id'
			),
			'conditions' => array(
				'SkillUser.user_id' => $user_id
			)
		));
		
		$ignore = array_merge($existing_skill_ids, $exploded_exclude);
		
		$conditions = array('hidden' => 0);
		
		if( !empty($ignore) )
			$conditions['skill_id NOT IN'] = $ignore;
		
		$recommended_skills = $this->User->SkillUser->Skill->find('list', array(
			'fields' => array('skill'),
			'conditions' => $conditions,
			'contain' => array(),
			'limit' => $count
		));
		
		//debug( json_encode($recommended_skills) );
		
		$this->response->type('json');
		$this->response->body( json_encode($recommended_skills) );
		
		return $this->response;
	}
	
	public function api_notification()
	{
		$this->layout = 'ajax';
		$notifications = $this->User->getUnreadNotification( AuthComponent::user('user_id') );
		
		$this->set( compact('notifications') );

		
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


	
	public function notifications()
	{
		$conditions = array(
			'Notification.user_id' => $this->Auth->user('user_id')
		);
		
		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['order'] = array(
			'Notification.created' => 'desc'
		);
		
		$notifications = $this->Paginator->paginate('Notification');
		
		$this->set( compact('notifications') );
	}

	public function check()
	{

		header('Content-type: application/json');

		$username = ( isset($this->params->query['username']) )? $this->params->query['username']: '';

		$validate['username'] = $username;

		$this->User->set($validate);

		if( $this->User->validates( array('fieldList' => array('username') ) ) )
		{
			$valid = true;
		}
		else
		{
			$valid = $this->User->validationErrors['username'];
		}

		$this->autoRender = false;

		echo json_encode( compact('valid') );

	}
	
	public function api_clear($notification_id = null)
	{
		if( $notification_id == null )
		{
			$this->User->Notification->markAllAsRead( $this->Auth->user('user_id') );
		}
		else
		{
			$this->User->Notification->markAsRead($notification_id, $this->Auth->user('user_id') );
		}
		
		$this->response->body( json_encode( array('notification_id' => $notification_id, 'read' => true)) );
		$this->response->type('json');
		
		return $this->response;
	}
	
	public function clear()
	{
		$this->User->Notification->markAllAsRead( $this->Auth->user('user_id') );
		
		$this->Session->setFlash( __('All notifications have been marked as read'), 'success');
		return $this->redirect( array('controller' => 'users', 'action' => 'notifications') );
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
		
		if( $this->request->is('post') )
		{
			$data = $this->request->data;
			$user = array(
				'username' => $data['User']['username'],
				'password' => $this->hash_for(1, 'sha1'),
				'first_name' => $data['User']['first_name'],
				'last_name' => $data['User']['last_name'],
				'email' => $data['User']['email']
			);
			
			$verification = array(
				'token' => $this->hash_for(1, 'sha256'),
				'expires' => $this->User->getDataSource()->expression('DATE_ADD( NOW(), INTERVAL 1 DAY)')
			);
			
			$save = array(
				'User' => $user,
				'Verification' => array(
					$verification
				)
			);
			
			if( $this->User->saveAssociated($save) )
			{
				return $this->redirect( array(
					'controller' => 'verifications',
					'action' => 'email'
				));
			}
		}
		
		return;

	}


	public function go_profile()
	{
		return $this->redirect( array('go' => false, 'action' => 'profile') );
	}

	public function coordinator_profile() { return $this->go_profile(); }
	public function supervisor_profile() { return $this->go_profile(); }
	public function volunteer_profile() { return $this->go_profile(); }
	public function admin_profile() { return $this->go_profile(); }
	
	public function avatar($username, $size = 40)
	{
		$user = $this->User->findByUsername($username);
		
		return $this->redirect(
			__('https://www.gravatar.com/avatar/%s?s=%d', md5($user['User']['email']), $size)
		);
	}


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
	public function activity($period = null, $render = "default")
	{
		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		if( !in_array($period, array('month', 'year', 'ytd', 'all', 'custom') ) )
			$period = "month";

		$order = array(
			'Event.stop_time DESC'
		);
		$conditions['Time.status !='] = "deleted";
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

		$this->Paginator->settings['limit'] = ($render == 'default')? 10 : 
			$this->User->Time->find('count', compact('conditions'));
		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['contain'] = array(
			'OrganizationTime' => array('Organization'),
			'EventTime' => array('Event' => array('Organization'))
		);

		$time_data = $this->Paginator->paginate('Time');

		$period_total = $this->User->getDataSource()->fetchAll("
			    SELECT
				SUM(TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time)/60) as period_total
				FROM `times` Time
				WHERE Time.user_id = :user_id
				AND Time.start_time >= :start_date
				AND Time.stop_time <= :stop_date",
			array(
				'user_id' => $this->Auth->user('user_id'),
				'start_date' => isset($conditions['Time.start_time >='])? $conditions['Time.start_time >='] : '',
				'stop_date' => isset($conditions['Time.stop_time <='])? $conditions['Time.stop_time <='] : "NOW()"
			)
		);
		$this->set( compact('period', 'time_data', 'period_total') );

		$user = $this->User->find('first', array(
			'conditions' => array('User.user_id' => $this->Auth->user('user_id') ),
			'contain' => array()
		));
		$this->set( compact('user') );

		$title_for_layout = sprintf( "%s &ndash; %s", __('My Service Activity'), $sub_title);
		$this->set( compact('title_for_layout') );
		
		if($render == "xlsx")
			$this->render('/Users/activity.xlsx');


	}
	
	public function organizations()
	{
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
	}

	/*
		exports the user's time data to an Excel spreadsheet
	*/
	public function report($period = null)
	{
		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array(
			'EventTime' => array(
				'Event' => array('Organization') ) );

		$this->layout = null;

		if( !in_array($period, array('month', 'year', 'ytd', 'custom', 'all') ) )
			$period = "month";

		$order = array(
			'Time.stop_time DESC'
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
		
		$this->render('/Users/activity.xlsx');


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
