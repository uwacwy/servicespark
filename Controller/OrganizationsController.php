<?php
App::uses('AppController', 'Controller');
  

/**
 * Organizations Controller
 *
 * @property Organization $Organization
 * @property PaginatorComponent $Paginator
 */
class OrganizationsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	public $helpers = array('Address', 'PhpExcel');


/**
 * go_leave method
 *
 * @return void
 */
	public function go_leave($id = null)
	{
		return $this->redirect(
			array(
					'coordinator' => true,
					'controller' => 'organizations',
					'action' => 'leave',
					$id)
		);
	}


/**
 * go_join method
 *
 * @return void
 */
	public function go_join($id = null)
	{
		return $this->redirect(
			array(
					'volunteer' => true,
					'controller' => 'organizations',
					'action' => 'join',
					$id)
		);
	}


/**
 * go_add method
 *
 * @return void
 */
	public function go_add() {
		return $this->redirect(
			array(
				'volunteer' => true,
				'controller' => 'organizations',
				'action' => 'add'
			)
		);
	}


/**
 * go_index method
 *
 * @return void
 */
	public function go_index($id = null) {
		return $this->redirect(
			array(
					'volunteer' => false,
					'controller' => 'organizations',
					'action' => 'index',
					$id)
		);
	}


/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function index() 
	{
		$this->Paginator->settings['contain'] = array();

		$organizations = $this->Paginator->paginate();

		$this->set(compact('organizations'));
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
		if (!$this->Organization->exists($id)) 
		{
			throw new NotFoundException(__('Invalid organization'));
		}
		$organizationOptions = array('conditions' => array('Organization.' . $this->Organization->primaryKey => $id));
		$eventOptions = array('conditions' => array('Event.start_time >' => date('Y-m-d H:i:s'),
													'Event.' . $this->Organization->primaryKey => $id) );
		$this->set('organization', $this->Organization->find('first', $organizationOptions));
		$events = $this->Organization->Event->find('all', $eventOptions);
		
		$this->set(compact('events'));
	}


/**
 * coordinator_index
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function coordinator_index() 
	{
		$user_co_organizations = $this->_GetUserOrganizationsByPermission('write');

		$this->Paginator->settings['conditions'] = array(
			'Organization.organization_id' => $user_co_organizations
		);
		$this->Paginator->settings['contain'] = array();

		$pag_organizations = $this->Paginator->paginate();

		$this->set(compact('pag_organizations'));
	}


/**
 * coordinator_edit
 *
 * @throws NotImplementedException
 * @param string $id
 * @return void
*/
	public function coordinator_edit($id = null) 
	{
		if (!$this->Organization->exists($id)) 
		{
			throw new NotFoundException(__('Invalid organization'));
		}

		// Get the organization to edit.
		$organization = $this->Organization->findByOrganizationId($id);
	
		//Check user permissions.
		if($this->_CurrentUserCanWrite($organization['Organization']['organization_id'])) 
		{
			if ($this->request->is(array('post', 'put'))) 
			{
				$entry = $this->request->data;

				// create address entry
				if( !empty($entry['Address'])) 
				{
					$address_ids = $this->_ProcessAddresses($entry['Address'], $this->Organization->Address);
				}
				

				if( !empty($entry['Address']) )
				{
					unset( $entry['Address'] );
				}
				

				if( !empty($address_ids) )
				{
					$entry['Address'] = $address_ids;
				}

				/*
					keep this block before the Organization save.
					The database is set to delete 0-0-0 permissions before Organizations are saved.
				*/
				foreach($entry['Permission'] as $permission)
				{
					$permissions = array(
						'permission_id' => $permission['permission_id'], // without this, we will get integrity violations
						// 'user_id' => $permission['user_id'],
						// 'organization_id' => $id,
						'publish' => $permission['publish']	,
						'write' => $permission['write'],
						'read' => $permission['read']				
					);
					$this->Organization->Permission->saveAll($permissions);
				}

				if ( $this->Organization->save($entry) ) 
				{
					
					$this->Session->setFlash(__('The organization has been saved.'), 'success');
					return $this->redirect(array('action' => 'coordinator_index'));
				} 
				else 
				{
					$this->Session->setFlash(__('The organization could not be saved. Please, try again.'));
				}
			} 
			else 
			{
				$options = array('conditions' => array('Organization.' . $this->Organization->primaryKey => $id));
				$this->request->data = $this->Organization->find('first', $options);
			}
		}
		else 
		{
			return $this->redirect(
				array(
						'supervisor' => true,
						'controller' => 'organizations',
						'action' => 'view',
						$id)
			);
		}

		$conditions = array(
			'Permission.organization_id' => $id
		);
		// get a list of user's organizations

		$contain = array('User');

		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 10,
			'contain' => $contain
		);

		$data = $this->Paginator->paginate('Permission');

		$this->set(compact('data'));

		$addresses = $this->Organization->Address->find('all');
		$this->set(compact('addresses'));
	}


	public function coordinator_delete($organization_id = null) 
	{
		if (!$this->Organization->exists($organization_id)) 
		{
			throw new NotFoundException(__('Invalid organization'));
		}

		if( $this->_CurrentUserCanWrite($organization_id) )
		{
			$this->Organization->delete($organization_id);

			$this->redirect(
				array(
					'volunteer' => false,
					'controller' => 'users',
					'action' => 'activity'
				)
			);
		}
		else
		{
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => $organizations,
					'action' => 'index'
				)
			);
		}
	}


/**
 * supervisor_index
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function supervisor_index() 
	{
		$user_co_organizations = $this->_GetUserOrganizationsByPermission('read');

		$this->Paginator->settings['conditions'] = array(
			'Organization.organization_id' => $user_co_organizations
		);
		$this->Paginator->settings['contain'] = array();

		$pag_organizations = $this->Paginator->paginate();

		$this->set(compact('pag_organizations'));
	}


/**
 * supervisor_view
 *
 * @throws ForbiddenException, NotFoundException
 * @param string $id
 * @return void
*/
	public function supervisor_view($organization_id = null) 
	{
		if( $this->_CurrentUserCanRead( $organization_id ) || $this->_CurrentUserCanWrite( $organization_id ) )
		{
			$this->set('organization_id', $organization_id);

			$sql_date_fmt = 'Y-m-d H:i:s';

			$users = $this->_GetUsersByOrganization($organization_id);
			
			$event_ids = $this->Organization->Event->find(
				'list', 
				array(
					'conditions' => array('Event.organization_id' => $organization_id),
					'fields' => array('Event.event_id')
				)
			);

			$events = $this->Organization->Event->find('all', array('conditions' => array('Event.event_id' => $event_ids)));

			$conditions = array(
				'Time.event_id' => $event_ids
			);
			$fields = array(
				'User.*',
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as UserSumTime',
				'COUNT( Time.time_id ) as UserNumberEvents'
			);
			$group = array(
				'User.user_id'
			);

			$userHours = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
			$this->set(compact('userHours', 'events'));

	 		// summary all time
			$users = $this->_GetUsersByOrganization($organization_id);

	 		$conditions = array(
	 			'Time.user_id' => $this->Auth->user('user_id')
	 		);
	 		$fields = array(
	 			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as OrganizationAllTime'
	 		);
			$summary_all_time = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
	 		$this->set( compact('summary_all_time') );


			// summary month
	  		$conditions = array(
	 			'Time.start_time >=' => date($sql_date_fmt, strtotime('1 month ago') ),
				'Time.user_id' => $users
	  		);
	  		$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as OrganizationPastMonth'
			);
			$summary_past_month = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_past_month') );


			// summary year
			$conditions = array(
				'Time.user_id' => $users,
				'Time.start_time >=' => date($sql_date_fmt, strtotime('1 year ago') )
				
	  		);
			$fields = array(
				'Time.*',
				'User.*',
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as OrganizationPastYear',
				'COUNT( Time.time_id ) as TimeEntryCount'
			);
			$group = array(
				'Time.user_id'
	  		);
			$summary_past_year = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_past_year') );


			// year-to-date
			$conditions = array(
				'Time.user_id' => $this->Auth->user('user_id'),
				'Time.start_time >=' => date($sql_date_fmt, mktime(0,0,0,1,1, date('Y') ) )
			);
			$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as OrganizationYTD'
			);
			$summary_ytd = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			$this->set( compact('summary_ytd') );
		}
		else
		{
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => 'organizations',
					'action' => 'index'
				)
			);
		}
	}


/**
 * supervisor_report
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function supervisor_report($organization_id) 
	{
		if( $this->_CurrentUserCanRead( $organization_id ) )
		{
			$sql_date_fmt = 'Y-m-d H:i:s';

			$users = $this->_GetUsersByOrganization($organization_id);
			
			$event_ids = $this->Organization->Event->find(
				'list', 
				array(
					'conditions' => array('Event.organization_id' => $organization_id),
					'fields' => array('Event.event_id')
				)
			);

			$events = $this->Organization->Event->find('all', array('conditions' => array('Event.event_id' => $event_ids)));

			$conditions = array(
				'Time.event_id' => $event_ids
			);
			$fields = array(
				'User.*',
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as UserSumTime',
				'COUNT( Time.time_id ) as UserNumberEvents'
			);
			$group = array(
				'User.user_id'
			);

			$userHours = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
			$this->set(compact('userHours', 'events'));
		}
		else
		{
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => 'organizations',
					'action' => 'index'
				)
			);
		}
	}


/**
 * leave
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function leave($organization_id = null) 
	{
		if($this->Auth->user('user_id') ) 
		{
			if ($organization_id != null) 
			{
				$conditions = array(
					'Permission.user_id' => $this->Auth->user('user_id'),
					'Permission.organization_id' => $organization_id
				);
				
				if($this->Organization->Permission->deleteAll($conditions)) 
				{
					$this->Session->setFlash(__('You have successfully left this organization.'), 'success');
					$this->redirect(
						array(
							'volunteer' => false,
							'controller' => 'users',
							'action' => 'activity'
						)
					 );
				}
				else 
				{
					$this->Session->setFlash(__('Something went wrong. You cannot leave this organization.'), 'danger');
				}
			}

			$conditions = array(
				'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('all')
			);

			$this->Paginator->settings = array(
				'conditions' => $conditions,
				'limit' => 10,
				'contain' => array()
			);

			$data = $this->Paginator->paginate();
			$this->set(compact('data'));
		}
		else
		{
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => $organizations,
					'action' => 'index'
				)
			);
		}
	}


/**
 * volunteer_leave
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function volunteer_leave($organization_id = null) 
	{
		if( $organization_id != null)
		{
			if ( !$this->Organization->exists($organization_id) )
			{
				// get out of here
				$this->Session->setFlash( __('This request was invalid.'), 'danger');
				return 	$this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			if( !$this->_CurrentUserCanPublish($organization_id) )
			{
				// get out of here
				$this->Session->setFlash( __('current user cannot publish'), 'danger');
				return 	$this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			$user_id = $this->Auth->user('user_id');
			$permission = $this->Organization->Permission->findByUserIdAndOrganizationId($user_id, $organization_id); // yes, this will work

			if( empty($permission) )
			{
				// permissions didn't exist.  redirect gracefully
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');

				// redirect here so the other stuff isn't attempted
				return 	$this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			$this->Organization->Permission->id = $permission['Permission']['permission_id'];

			if( $this->Organization->Permission->saveField('publish', false) )
			{
				// redirect
				$this->Session->setFlash(__('You are no longer publishing activity to this organization.'), 'success');
			}
			else
			{
				// didn't save
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');
			}
		}

		$conditions = array(
			'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('publish')
		);

		$fields = array(
			'Organization.*',
		);

		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 10,
			'contain' => array()
		);

		$data = $this->Paginator->paginate();
		$this->set( compact('data') );
	}


/**
 * coordinator_leave
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function coordinator_leave($organization_id = null) 
	{
		if( $organization_id != null)
		{
			if ( !$this->Organization->exists($organization_id) )
			{
				// get out of here
				$this->Session->setFlash( __('This request was invalid.'), 'danger');
				return 	$this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			if( !$this->_CurrentUserCanWrite($organization_id) )
			{
				// get out of here
				$this->Session->setFlash( __('current user cannot read'), 'danger');
				return 	$this->redirect(
					array(
						'supervisor' => true,
						'controller' => 'organizations',
						'action' => 'leave'
					)
				);
			}

			$user_id = $this->Auth->user('user_id');
			$permission = $this->Organization->Permission->findByUserIdAndOrganizationId($user_id, $organization_id); // yes, this will work

			if( empty($permission) )
			{
				// permissions didn't exist.  redirect gracefully
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');

				// redirect here so the other stuff isn't attempted
				return 	$this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			$this->Organization->Permission->id = $permission['Permission']['permission_id'];

			if( $this->Organization->Permission->saveField('write', false) )
			{
				// redirect
				$this->Session->setFlash(__('You are no longer able to coordinate for this organization.'), 'success');
			}
			else
			{
				// didn't save
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');
			}
		}

		$conditions = array(
			'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('write')
		);

		$fields = array(
			'Organization.*',
		);

		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 10,
			'contain' => array()
		);

		$data = $this->Paginator->paginate();
		$this->set( compact('data') );

	}


/**
 * volunteer_leave
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function supervisor_leave($organization_id = null) 
	{
		if( $organization_id != null)
		{
			if ( !$this->Organization->exists($organization_id) )
			{
				// get out of here
				$this->Session->setFlash( __('This request was invalid.'), 'danger');
				return 	$this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			if( !$this->_CurrentUserCanRead($organization_id) )
			{
				// get out of here
				$this->Session->setFlash( __('current user cannot read'), 'danger');
				return 	$this->redirect(
					array(
						'volunteer' => true,
						'controller' => 'users',
						'action' => 'leave'
					)
				);
			}

			$user_id = $this->Auth->user('user_id');
			$permission = $this->Organization->Permission->findByUserIdAndOrganizationId($user_id, $organization_id); // yes, this will work

			if( empty($permission) )
			{
				// permissions didn't exist.  redirect gracefully
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');

				// redirect here so the other stuff isn't attempted
				$this->redirect('/');
			}

			$this->Organization->Permission->id = $permission['Permission']['permission_id'];

			if( $this->Organization->Permission->saveField('read', false) )
			{
				// redirect
				$this->Session->setFlash(__('You are no longer able to supervise activity for this organization.'), 'success');
			}
			else
			{
				// didn't save
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');
			}
		}

		$conditions = array(
			'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('read')
		);

		$fields = array(
			'Organization.*',
		);

		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 10,
			'contain' => array()
		);

		$data = $this->Paginator->paginate();
		$this->set( compact('data') );
	}


/**
 * publish
 *
 * @throws NotImplementedException
 * @param string $id
 * @return void
*/
	public function volunteer_join() 
	{

		if ($this->request->is(array('post', 'put'))) 
		{	
			foreach($this->request->data['Organization'] as $organization)
			{
				//debug($organization);

				if($organization['publish'] == '1') {
					
					$entry['Permission'] = array(
						'user_id' => $this->Auth->user('user_id'),
						'organization_id' => $organization['organization_id'],
						'publish' => true
					);
					$this->Organization->Permission->create();
					$this->Organization->Permission->save($entry);
					debug($entry);
				}
			}

			return 	$this->redirect(
				array(
					'volunteer' => false,
					'controller' => 'users',
					'action' => 'activity'
				)
			);
		}

		$conditions = array(
			'NOT' => array(
				'Organization.organization_id' => $this->_GetUserOrganizationsByPermission('all')
			)
		);

		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 10,
			'contain' => array()
		);

		$organizations = $this->Paginator->paginate();
		$this->set(compact('organizations'));
	}


/**
 * publish
 *
 * @throws NotImplementedException
 * @param string $id
 * @return void
*/
	public function volunteer_add() 
	{
		if($this->Auth->user('user_id') ) 
		{
			if ($this->request->is('post'))
			{
				// create address entry
				$address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->Organization->Address);

				unset( $this->request->data['Address'] );

				if( !empty($address_ids) )
				{
					$this->request->data['Address'] = $address_ids;
				}
				
				$this->Organization->create();

				if ($this->Organization->save($this->request->data))
				{
					$this->Session->setFlash(__('The organization has been created.'));

					$conditions['Permission'] = array( 
						'user_id' => $this->Auth->user('user_id'),
						'organization_id' => $this->Organization->id,
						'write' => true
					);

					$this->Organization->Permission->save($conditions);

					return 	$this->redirect(
						array(
							'volunteer' => false,
							'controller' => 'users',
							'action' => 'activity'
						)
					);
				} 
				else 
				{
					$this->Session->setFlash(__('The organization could not be created. Please, try again.'));
				}
			}
		}
		else 
		{
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => $organizations,
					'action' => 'index'
				)
			);
		}
	}
}
