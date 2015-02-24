<?php
App::uses('AppController', 'Controller');

App::uses('CakeEmail', 'Network/Email');
/**
 * Events Controller
 *
 * @property Event $Event
 * @property PaginatorComponent $Paginator
 */
class EventsController extends AppController {

	public function beforeFilter()
	{
		parent::beforeFilter();
	    // Allow guest users to open event index and event view
	    $this->Auth->allow('index','view', 'go_view', 'go_add', 'go_index');
	}

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	public $helpers = array('Comment');


	public function go_view($event_id)
	{
		$event = $this->Event->findByEventId($event_id, array('contain' => array() ) );

		if( $this->_CurrentUserCanWrite( $event['Event']['organization_id']) )
		{
			return $this->redirect( array('coordinator' => true, 'action' => 'view', $event_id) );
		}
		elseif( $this->_CurrentUserCanRead( $event['Event']['organization_id']) )
		{
			return $this->redirect( array('supervisor' => true, 'action' => 'view', $event_id) );
		}
		elseif( $this->Auth->user('user_id') != null )
		{
			return $this->redirect( array('volunteer' => true, 'action' => 'view', $event_id) );
		}
		else
		{
			return $this->redirect( array('volunteer' => false, 'action' => 'view', $event_id) );

		}
	}

	public function go_add()
	{
		$this->redirect( array('coordinator' => true, 'action' => 'add') );
	}

	public function go_index()
	{
		if( $this->_GetUserOrganizationsByPermission('write') != null)
		{
			return $this->redirect( array('coordinator' => true, 'action' => 'index') );
		}
		elseif( $this->_GetUserOrganizationsByPermission('read') != null)
		{
			return $this->redirect( array('supervisor' => true, 'action' => 'index') );
		}
		elseif( $this->_GetUserOrganizationsByPermission('publish') != null)
		{
			return $this->redirect( array('volunteer' => true, 'action' => 'index') );
		}
		else
		{
			return $this->redirect( array('volunteer' => false, 'action' => 'index') );
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Event->id = $id;
		if (!$this->Event->exists()) {
			throw new NotFoundException(__('Invalid event'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Event->delete()) {
			$this->Session->setFlash(__('The event has been deleted.'), 'success');
		} else {
			$this->Session->setFlash(__('The event could not be deleted. Please, try again.'), 'danger');
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
			ADMIN
			URL: localhost/admin/events/...
	*/
	public function admin_delete($id = null)
	{
		if( AuthComponent::user('super_admin')  )
		{
			$this->delete($id);
		}
		else
		{
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'index'));
		}
	}

	public function admin_add($id = null)
	{
		if( AuthComponent::user('super_admin')  )
		{
			if ($this->request->is('post')) 
			{
				if(! $this->Event->validTimes()) {
					return false;
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
						$this->Event->Address->create();
						$this->Event->Address->save($address);
						// get the address_id for the join table
						$address_ids['Address'][] = $this->Event->Address->id;
					}
				}
				unset( $this->request->data['Address'] );

				if( !empty($address_ids) )
					$this->request->data['Address'] = $address_ids;

				$hash = sha1( json_encode($this->request->data['Event']) ); // serializes the event and hashes it

				/*
					by choosing 9 characters from a base 16 hash, there are a total possible
					 68,719,476,736 hashes.  this should be adequate.
				*/
				$this->request->data['Event']['start_token'] = substr($hash, 0, 9); // 9 starting characters
				$this->request->data['Event']['stop_token'] = substr($hash, -9, 9); // 9 ending characters

				// create and save the event
				$this->Event->create();
				if ($this->Event->saveAll($this->request->data)) 
				{
					$this->Session->setFlash(__('The event has been saved.'), 'success');
					//debug($this->request->data);
					//return $this->redirect(array('action' => 'index'));
				} 
				else 
				{
					$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
				}
			}
			
			$organization = $this->Event->Organization->find('list');
			$address = $this->Event->Address->find('all');
			$skills = null;
			$this->set( compact('skills', 'address', 'organization') );

			$this->set('organizations', $this->Event->Organization->find(
	            'list',
	            array(
	                'fields' => array('Organization.name'),
	                'order' => array('Organization.name')
	            )));
		}
		else
		{
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'add'));
		}
	}

	public function admin_edit($id = null)
	{
		if( AuthComponent::user('super_admin')  )
		{
			if (!$this->Event->exists($id)) {
			throw new NotFoundException(__('Invalid event'));
			}
			if ($this->request->is(array('post', 'put'))) 
			{
				if(! $this->Event->validTimes()) {
					return false;
				}

				if($this->request->data['Address'] != null)
				{
					foreach($this->request->data['Address'] as $address)
					{
						$this->Event->Address->save($address);
					}
				}

				if ($this->Event->save($this->request->data)) {
					$this->Session->setFlash(__('The event has been saved.'), 'success');
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
				}
			} else {
				$options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
				$this->request->data = $this->Event->find('first', $options);
			}

			$organization = $this->Event->Organization->find('list');
			$address = $this->Event->Address->find('all');
			$skills = null;
			$this->set( compact('skills', 'address', 'organization') );

			$this->set('organizations', $this->Event->Organization->find(
	            'list',
	            array(
	                'fields' => array('Organization.name'),
	                'order' => array('Organization.name')
	            )));
		}
		else
		{
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'edit', $id));
		}
	}

	public function admin_index($id = null)
	{
		if( !AuthComponent::user('super_admin')  )
		{
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'index'));
		}

		$this->Paginator->settings['limit'] = 15;

		$events = $this->Paginator->paginate();

		//$events = $this->Event->find('all', array('conditions' => $conditions) ) ;
		$this->set( compact('events') );


	}

	public function admin_view($id = null)
	{
		if( !AuthComponent::user('super_admin')  )
		{
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'view', $id));
		}

		$event = $this->Event->find('first', array('conditions' => array('Event.event_id' => $id) ) );

		$conditions = array(
			'Time.event_id' => $id
		);
		$fields = array(
			'Time.*',
			'User.*',
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as OrganizationAllTime',
			'COUNT( Time.time_id ) as TimeEntryCount'
		);
		$group = array(
			'Time.user_id'
		);

		// juxtapose this with the actual Time->find('all', $options) syntax
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'group' => $group,
			'limit' => 15
		);
		$times = $this->Paginator->paginate('Time');

		// versus

//		$times = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
		
		$this->set( compact('times', 'event') );
	}


	/**
			COORDINATOR
			URL: localhost/coordinator/events/...
	*/
	public function coordinator_delete($id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		if( $this->_CurrentUserCanWrite($user_organizations) )
		{
			$this->delete($id);
		}
		else
		{
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'index'));
		}
	}

	public function coordinator_add($id = null)
	{
		// $user_organizations = $this->Event->Organization->Permission->find('list',
		// 		array(
		// 			'fields' => array('Permission.organization_id'),
		// 			'conditions' => array(
		// 				'Permission.write' => true
		// 			)
		// 		)
		// 	);

		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		if( !empty($user_organizations) ) // do we have organizations?
		{
			if ($this->request->is('post')) 
			{
				if(! $this->Event->validTimes()) {
					return false;
				}

				/*
					process relations
				*/
				if( isset($this->request->data['Skill']) )
					$skill_ids = $this->_ProcessSkills($this->request->data['Skill'], $this->Event->Skill);
				if( isset($this->request->data['Address']) )
					$address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->Event->Address);
				
				unset( $this->request->data['Address'], $this->request->data['Skill'] );

				if( !empty($address_ids) )
					$this->request->data['Address'] = $address_ids;
				if( !empty($skill_ids) )
					$this->request->data['Skill'] = $skill_ids;

				/*
					process the event hashes
				*/
				$hash = sha1( json_encode($this->request->data['Event']) ); // serializes the event and hashes it

				/*
					by choosing 9 characters from a base 16 hash, there are a total possible
					 68,719,476,736 hashes.  this should be adequate.
				*/
				$complexity = 4;
				$this->request->data['Event']['start_token'] = substr($hash, 0, $complexity); // 9 starting characters
				$this->request->data['Event']['stop_token'] = substr($hash, -$complexity, $complexity); // 9 ending characters

				// create and save the event
				$this->Event->create();
				if ($this->Event->saveAll($this->request->data)) 
				{
					$conditions = array(
						'Event.event_id' => $this->Event->id
					);
					$contain = array('Address', 'Organization' => array('Permission.write = 1' => 'User'), 'Skill' => array('User'));
					$event = $this->Event->find('first', compact('conditions', 'contain') );

					foreach ($event['Skill'] as $skill)
					{
						foreach( $skill['User'] as $user )
						{
							$users[$user['user_id']] = $user;
							$users[$user['user_id']]['Skills'][] = $skill;
						}
					}

					if ( !empty($users) )
					{
						foreach($users as $user)
						{
							$Email = new CakeEmail('mandrill');
							$Email->viewVars( compact('user', 'event') );
							$Email->template('NewEvent')
								->emailFormat('text')
								->to( $user['email'], $user['full_name'] )
								->subject( __('[%s] %s for %s', Configure::read('Solution.name'), $event['Event']['title'], $user['full_name'] ) )
								->send();
						}						
					}

					$this->Session->setFlash(__('The event has been saved.'), 'success');
					return $this->redirect(array('controller' => 'events', 'action' => 'view', $this->Event->id, 'coordinator' => true));
				} 
				else 
				{
					$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
				}
			}
			
			$address = $this->Event->Address->find('all');
			$skills = null;
			$this->set( compact('skills', 'organization') );
			$this->set('title_for_layout', __('Creating New Event') );

			//debug($user_organizations);

			$this->set('organizations', $this->Event->Organization->find(
	            'list',
	            array(
	            	'conditions' => array(
	            		'Organization.organization_id' => $user_organizations
	            	),
	                'order' => array('Organization.name')
	            )));
		}
		else
		{
			//throw new ForbiddenException('You do not have permission...');
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('supervisor' => true,
				'controller' => 'events', 'action' => 'index'));
		}
	}

	public function coordinator_edit($id = null)
	{
		if (!$this->Event->exists($id)) {
			throw new NotFoundException(__('Invalid event'));
		}
		
		$event = $this->Event->findByEventId($id);
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		if( $this->_CurrentUserCanWrite($event['Organization']['organization_id']) )
		{

			if ($this->request->is(array('post', 'put'))) 
			{
				if(! $this->Event->validTimes()) {
					return false;
				}

				if( isset($this->request->data['Address']) )
				{
					$address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->Event->Address);
					unset($this->request->data['Address']);
					$this->request->data['Address'] = $address_ids;
				}

				if( isset($this->request->data['Skill']) )
				{
					$skill_ids = $this->_ProcessSkills($this->request->data['Skill'], $this->Event->Skill);
					unset($this->request->data['Skill']);
					$this->request->data['Skill' ] =  $skill_ids;
				}

				if ($this->Event->save($this->request->data)) {
					$this->Session->setFlash(__('The event has been saved.'), 'success');
					return $this->redirect(array('action' => 'view', $this->request->data['Event']['event_id']));
				} else {
					$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
				}
			} else {
				$options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
				$this->request->data = $this->Event->find('first', $options);
			}

			$address = $this->Event->Address->find('all');
			$skills = null;
			$this->set( compact('skills', 'address', 'organization') );
			$this->set('title_for_layout', __('Editing Event - '. $event['Event']['title']) );
			//debug($user_organizations);

			$this->set('organizations', $this->Event->Organization->find(
	            'list',
	            array(
	            	'conditions' => array(
	            		'Organization.organization_id' => $user_organizations
	            	),
	                'order' => array('Organization.name')
	            )));
		}
		else
		{
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'index'));
		}
	}

	public function coordinator_index($id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		// if( !$this->_CurrentUserCanWrite($user_organizations) )
		// {
		// 	$this->Session->setFlash('You do not have permission.', 'danger');
		// 	return $this->redirect(array('supervisor' => true,
		// 		'controller' => 'events', 'action' => 'index'));
		// }

		$conditions = array(
			'Event.organization_id' => $user_organizations,
			'Event.stop_time >=' => date('Y-m-d H:i:s', time())
		);

		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['limit'] = 15;
		$this->Paginator->settings['order'] = array(
			'Event.start_time' => 'asc'
		);

		$events = $this->Paginator->paginate();
		$this->set('title_for_layout', __('Coordinator Events Dashboard') );
		//$events = $this->Event->find('all', array('conditions' => $conditions) ) ;
		$this->set( compact('events') );
	}

	public function coordinator_archive($id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		// if( !$this->_CurrentUserCanWrite($user_organizations) )
		// {
		// 	$this->Session->setFlash('You do not have permission.', 'danger');
		// 	return $this->redirect(array('supervisor' => true,
		// 		'controller' => 'events', 'action' => 'index'));
		// }

		$conditions = array(
			'Event.organization_id' => $user_organizations,
			'Event.stop_time <= Now()'
		);

		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['limit'] = 15;
		$this->Paginator->settings['order'] = array(
			'Event.stop_time' => 'desc'
		);

		$events = $this->Paginator->paginate();
		$this->set('title_for_layout', __('Event Archive') );
		//$events = $this->Event->find('all', array('conditions' => $conditions) ) ;
		$this->set( compact('events') );
	}

	public function coordinator_report($event_id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('read');

		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		$event = $this->Event->find('first', array('conditions' => array('Event.event_id' => $event_id) ) );

		if( empty($event) )
		{
			throw new NotFoundException( __('Page Not Found') );
		}

		if( !$this->_CurrentUserCanRead($event['Event']['organization_id']) )
		{
			$this->Session->setFlash(__('You may not supervise this event'), 'danger');
			return $this->redirect(array('volunteer' => true,
				'controller' => 'events', 'action' => 'view', $event_id));
		}


		$conditions = array(
			'Time.event_id' => $event_id
		);
		$fields = array(
			'Time.*',
			'User.*',
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as OrganizationAllTime',
			'COUNT( Time.time_id ) as TimeEntryCount'
		);
		$group = array(
			'Time.user_id'
		);
		
		$times = $this->Event->Time->find('all', array(
			'conditions' => $conditions,
			'fields' => $fields,
			'group' => $group
		));
		$this->set( compact('times', 'event') );
	}

	public function coordinator_view($event_id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		// if( !$this->_CurrentUserCanRead($user_organizations) )
		// {
		// 	$this->Session->setFlash('You do not have permission.', 'danger');
		// 	return $this->redirect(array('supervisor' => true,
		// 		'controller' => 'events', 'action' => 'view', $event_id));
		// }

		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Organization', 'Rsvp' => array('User'), 'Skill', 'Address', 'Time');

		// summary all time
		//$users = $this->_GetUsersByOrganization($event_id);

		$event = $this->Event->find('first', array('conditions' => array('Event.event_id' => $event_id), 'contain' => $contain ) );

		if( empty($event) )
		{
			throw new NotFoundException( __('Page Not Found') );
		}

		if( !$this->_CurrentUserCanWrite($event['Event']['organization_id']) )
		{
			return $this->redirect( array('supervisor' => true, 'controller' => 'events', 'action' => 'view', $event_id) );
		}

		$conditions = array(
			'Time.event_id' => $event_id
		);
		$fields = array(
			'Time.*',
			'User.*',
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as OrganizationAllTime',
			'COUNT( Time.time_id ) as TimeEntryCount'
		);
		$group = array(
			'Time.user_id'
		);

		// juxtapose this with the actual Time->find('all', $options) syntax
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'group' => $group,
			'limit' => 15
		);
		$times = $this->Paginator->paginate('Time');

		$conditions = array(
			'Time.event_id' => $event_id
		);
		$fields = array(
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as EventTotal'
		);
		$event_total = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
		$this->set( compact('event_total') );
		$this->set('title_for_layout', __('Viewing Event - '. $event['Event']['title']) );
		// versus

		$c_conditions = array('Comment.event_id' => $event_id);
		$c_contain = array('User', 'ParentComment' => array('User'));
		$c_order = array('Comment.created ASC');
		$comments = $this->Event->Comment->find('threaded', array('conditions' => $c_conditions, 'contain' => $c_contain, 'order' => $c_order) );

		$user_attending = ($this->Event->Rsvp->find('count', array('conditions' => array('Rsvp.user_id' => $this->Auth->user('user_id'), 'Rsvp.event_id' => $event_id)) ) == 1)? true : false;


//		$times = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
		$this->set('event_id', $event_id);
		$this->set( compact('times', 'event', 'comments', 'user_attending') );
	}

	/**
			supervisor
			URL: localhost/supervisor/events/...
	*/
	public function supervisor_add($id = null)
	{
		return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'add'));
	}

	public function supervisor_edit($id = null)
	{
		return $this->redirect(array('coordinator' => true,
				'controller' => 'events', 'action' => 'edit', $id));
	}

	public function supervisor_index($id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('read');

		if( !$this->_CurrentUserCanRead($user_organizations) )
		{
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('volunteer' => true,
				'controller' => 'events', 'action' => 'index'));
		}

		$conditions = array(
			'Event.organization_id' => $user_organizations
		);

		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['limit'] = 15;
		$this->Paginator->settings['contain'] = array('Organization');
		$this->set('title_for_layout', __('Supervisor Event Dashboard') );

		$events = $this->Paginator->paginate('Event');
		$this->set( compact('events') );
	}

	public function supervisor_report($event_id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('read');

		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		$event = $this->Event->find('first', array('conditions' => array('Event.event_id' => $event_id) ) );

		if( empty($event) )
		{
			throw new NotFoundException( __('Page Not Found') );
		}

		if( !$this->_CurrentUserCanRead($event['Event']['organization_id']) )
		{
			$this->Session->setFlash(__('You may not supervise this event'), 'danger');
			return $this->redirect(array('volunteer' => true,
				'controller' => 'events', 'action' => 'view', $event_id));
		}


		$conditions = array(
			'Time.event_id' => $event_id
		);
		$fields = array(
			'Time.*',
			'User.*',
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as OrganizationAllTime',
			'COUNT( Time.time_id ) as TimeEntryCount'
		);
		$group = array(
			'Time.user_id'
		);
		
		$times = $this->Event->Time->find('all', array(
			'conditions' => $conditions,
			'fields' => $fields,
			'group' => $group
		));
		$this->set( compact('times', 'event') );
	}

	public function supervisor_view($event_id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('read');

		// if( !$this->_CurrentUserCanRead($user_organizations) )
		// {
		// 	$this->Session->setFlash('You do not have permission.', 'danger');
		// 	return $this->redirect(array('supervisor' => true,
		// 		'controller' => 'events', 'action' => 'view', $event_id));
		// }

		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		// summary all time
		//$users = $this->_GetUsersByOrganization($event_id);

		$event = $this->Event->find('first', array('conditions' => array('Event.event_id' => $event_id) ) );

		if( empty($event) )
		{
			throw new NotFoundException( __('Page Not Found') );
		}

		if( !$this->_CurrentUserCanRead($event['Event']['organization_id']) )
		{
			return $this->redirect( array('volunteer' => true, 'controller' => 'events', 'action' => 'view', $event_id) );
		}

		$conditions = array(
			'Time.event_id' => $event_id
		);
		$fields = array(
			'Time.*',
			'User.*',
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as OrganizationAllTime',
			'COUNT( Time.time_id ) as TimeEntryCount'
		);
		$group = array(
			'Time.user_id'
		);

		// juxtapose this with the actual Time->find('all', $options) syntax
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'group' => $group,
			'limit' => 15
		);
		$times = $this->Paginator->paginate('Time');

		$conditions = array(
			'Time.event_id' => $event_id
		);
		$fields = array(
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as EventTotal'
		);
		$event_total = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
		$this->set( compact('event_total') );

		$c_conditions = array('Comment.event_id' => $event_id);
		$c_contain = array('User', 'ParentComment' => array('User'));
		$c_order = array('Comment.created ASC');
		$comments = $this->Event->Comment->find('threaded', array('conditions' => $c_conditions, 'contain' => $c_contain, 'order' => $c_order) );

		$user_attending = ($this->Event->Rsvp->find('count', array('conditions' => array('Rsvp.user_id' => $this->Auth->user('user_id'), 'Rsvp.event_id' => $event_id)) ) == 1)? true : false;


		// versus

//		$times = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
		$this->set('event_id', $event_id);
		$this->set( compact('times', 'event', 'comments', 'user_attending') );
		$this->set('title_for_layout', __('Viewing Event - '. $event['Event']['title']) );
	}

	/**
			VOLUNTEER
			URL: localhost/volunteer/events/...
	*/
	public function volunteer_add($id = null)
	{
		return $this->redirect(array('coordinator' => true,
			'controller' => 'events', 'action' => 'add'));
	}

	public function volunteer_edit($id = null)
	{
		return $this->redirect(array('coordinator' => true,
			'controller' => 'events', 'action' => 'edit', $id));
	}

	public function volunteer_index($id = null)
	{
		$this->Paginator->settings['limit'] = 15;
		$this->Paginator->settings['contain'] = array('Organization');
		$this->Paginator->settings['conditions'] = array(
			'Event.stop_time > NOW()'
		);
		$this->Paginator->settings['order'] = array(
			'Event.start_time ASC'
		);

		$events = $this->Paginator->paginate('Event');
		$this->set( compact('events') );
		$this->set('title_for_layout', __('Upcoming Events') );
	}

	public function volunteer_rsvp($event_id)
	{
		//$this->autoRender = false;

		$user_id = $this->Auth->user('user_id');

		$conditions = array('Rsvp.user_id' => $user_id, 'Rsvp.event_id' => $event_id);
		$contain = array();

		if( $this->Event->Rsvp->find('count', compact('conditions', 'contain') ) < 1)
		{
			$this->Event->Rsvp->save( array('Rsvp' => array('user_id' => $user_id, 'event_id' => $event_id) ) );
		}

		return $this->redirect( array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id) );
	}

	public function volunteer_cancel_rsvp($event_id)
	{
		
        App::uses('CakeTime', 'Utility');
		
		$user_id = $this->Auth->user('user_id');
		
		$conditions = array('Rsvp.user_id' => $user_id, 'Rsvp.event_id' => $event_id);
	
			$rsvp = $this->Event->Rsvp->find('first', compact('conditions') );
			
		if( !$rsvp )
			return $this->redirect( array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id) );
		
		$conditions = array(
			'Event.event_id' => $event_id
		);
		$contain = array();
		
		$event = $this->Event->find('first', compact('conditions', 'contain') );

		
		if(
			CakeTime::isToday($event['Event']['start_time'])
			&& CakeTime::isFuture($event['Event']['start_time'])
		)
		{
			$user_conditions = array(
				'User.user_id' => $this->Auth->user('user_id')
			);
			$user_contain = array();
			$user = $this->Event->Organization->Permission->User->find('first', array(
				'conditions' => $user_conditions,
				'contain' => $user_contain
			));
			// set event
			
			$this->set(compact('event', 'user') );
			if( $this->request->is('post') )
			{
				// cancel the RSVP
				$this->Event->Rsvp->delete($rsvp['Rsvp']['rsvp_id']);
				
				// remove reputation
				$user['User']['reputation'] += Configure::read('Solution.reputation.cancel_rsvp');
				
				$this->Event->Rsvp->User->id = $user['User']['user_id'];
				if($this->Event->Rsvp->User->saveField('reputation', $user['User']['reputation']) )
				{
					$saves['User'] = true;
				}
				
				
				// notify coordinators
				$coordinator_conditions = array(
					'Permission.write' => true,
					'Permission.organization_id' => $event['Event']['organization_id'],
					
				);
				$coordinator_contain = array('User');
				$coordinators = $this->Event->Organization->Permission->find('all', array(
					'conditions' => $coordinator_conditions,
					'contain' => $coordinator_contain
				));
				
				$template = <<<TEMPLATE
Hello, *|full_name|*

*|user_full_name|* (@*|user_username|*) is no longer coming to *|event_title|*.

At the moment, *|event_title|* has *|event_rsvp_count|*/*|event_rsvp_desired|* of the RSVP goal met.

You are receiving this email because you are coordinating this event.
--
Thank you for using *|solution_name|*
Manage email preferences at *|user_profile_link|*
TEMPLATE;
				$global_merge_vars = array(
					'user_full_name' => $user['User']['full_name'],
					'user_username' => $user['User']['username'],
					'event_link' => Router::url(
						array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id),
						true)
				);
				
				foreach($event['Event'] as $key => $value)
				{
					$global_merge_vars['event_'.$key] = $value;
				}
				
				$recipient_merge_vars = array();
				
				$to = array();
				
				foreach($coordinators as $coordinator)
				{
					$recipient_merge_vars[ $coordinator['User']['email'] ] = array(
						'full_name' => $coordinator['User']['full_name'],
					);
					$to[ $coordinator['User']['email'] ] =  $coordinator['User']['full_name'];
				}
				
				debug($to);
				
				$this->_sendEmail(
					$template,
					"*|user_full_name|* is no longer attending *|event_title|*",
					$to, 
					$global_merge_vars, 
					$recipient_merge_vars
				);
				
				$this->Session->setFlash(__("You are no longer attending %s.", $event['Event']['title']), 'danger');
				return $this->redirect( array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id) );
				
				// email coordinators
			}
		}
		else
		{
			
	
			//debug($rsvp);
	
			if( isset($rsvp['Rsvp']['rsvp_id']) )
				$this->Event->Rsvp->delete($rsvp['Rsvp']['rsvp_id']);
	
			$this->redirect( array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id) );
		}

	}

	public function volunteer_comment($event_id)
	{
		if( $this->request->is('post') )
		{
			$save['Comment']['event_id'] = $event_id;
			$save['Comment']['parent_id'] = ( isset($this->request->data['Comment']['parent_id']) )? $this->request->data['Comment']['parent_id'] : null;
			$save['Comment']['user_id'] = $this->Auth->user('user_id');
			$save['Comment']['body'] = $this->request->data['Comment']['body'];

			if( $this->Event->Comment->save($save) )
			{
				return $this->redirect( array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id, '#' => sprintf('comment-%s', $this->Event->Comment->id) ) );
			}
			else
			{
				debug('Comment did not save');
			}
		}
	}

	public function volunteer_delete_comment($comment_id)
	{
		$conditions = array('Comment.user_id' => $this->Auth->user('user_id'), 'Comment.comment_id' => $comment_id);
		$this->Event->Comment->deleteAll($conditions);
		return $this->redirect($this->referer());
	}

	public function volunteer_view($event_id = null)
	{
		$event = $this->Event->findByEventId($event_id);
		$c_conditions = array('Comment.event_id' => $event_id);
		$c_contain = array('User', 'ParentComment' => array('User'));
		$c_order = array('Comment.created ASC');
		$user_attending = ($this->Event->Rsvp->find('count', array('conditions' => array('Rsvp.user_id' => $this->Auth->user('user_id'), 'Rsvp.event_id' => $event_id)) ) == 1)? true : false;
		$comments = $this->Event->Comment->find('threaded', array('conditions' => $c_conditions, 'contain' => $c_contain, 'order' => $c_order) );
		$this->set( compact('event', 'comments', 'user_attending') );
		$this->set('title_for_layout', __('Viewing Event - '. $event['Event']['title']) );
	}

	/**
			GUEST
			URL: localhost/events/...
	*/
	public function add($id = null)
	{
		$this->redirect('index');
	}

	public function edit($id = null)
	{
		$this->redirect('index'); 
	}

	public function index($id = null)
	{
		$this->Paginator->settings['conditions'] = array(
			'Event.stop_time > NOW()'
		);
		$this->Paginator->settings['order'] = array(
			'Event.start_time ASC'
		);
		$this->set('events', $this->Paginator->paginate());	
		$this->set('title_for_layout', __('Upcoming Events') );
	}

	public function view($id = null)
	{
		// if (!$this->Event->exists($id)){
		// 	throw new NotFoundException(__('Invalid event'));
		// }

		$options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
		$event = $this->Event->find('first', $options);
		$this->request->data = $event;
		$this->set( compact('event') );
		$this->set('title_for_layout', __('Viewing Event - '. $event['Event']['title']) );

	}


}
