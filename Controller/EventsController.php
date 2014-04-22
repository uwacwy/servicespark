<?php
App::uses('AppController', 'Controller');
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
	    $this->Auth->allow('index','view');
	}

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');


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
		elseif( $this->_CurrentUserCanWrite( $event['Event']['organization_id']) )
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
	// public function delete($id = null) {
	// 	$this->Event->id = $id;
	// 	if (!$this->Event->exists()) {
	// 		throw new NotFoundException(__('Invalid event'));
	// 	}
	// 	$this->request->onlyAllow('post', 'delete');
	// 	if ($this->Event->delete()) {
	// 		$this->Session->setFlash(__('The event has been deleted.'), 'success');
	// 	} else {
	// 		$this->Session->setFlash(__('The event could not be deleted. Please, try again.'), 'danger');
	// 	}
	// 	return $this->redirect(array('action' => 'index'));
	// }


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
				'controller' => 'events', 'action' => 'delete'));
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
		$events = $this->Event->findByEventId($id);
		if( $this->_CurrentUserCanWrite($events['Event']['organization_id']) )
		{
			$this->delete($id);
		}
		else
		{
			//throw new ForbiddenException('You do not have permission...');
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

		if( $this->_CurrentUserCanWrite($user_organizations) )
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
				$this->request->data['Event']['start_token'] = substr($hash, 0, 9); // 9 starting characters
				$this->request->data['Event']['stop_token'] = substr($hash, -9, 9); // 9 ending characters

				// create and save the event
				$this->Event->create();
				if ($this->Event->saveAll($this->request->data)) 
				{
					$this->Session->setFlash(__('The event has been saved.'), 'success');
					//debug($this->request->data);
					return $this->redirect(array('controller' => 'events', 'action' => 'view', $this->Event->id, 'coordinator' => true));
				} 
				else 
				{
					$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
				}
			}
			
			$address = $this->Event->Address->find('all');
			$skills = null;
			$this->set( compact('skills', 'address', 'organization') );

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
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');
		if( $this->_CurrentUserCanWrite($user_organizations) )
		{
			if (!$this->Event->exists($id)) {
				throw new NotFoundException(__('Invalid event'));
			}
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

		if( !$this->_CurrentUserCanWrite($user_organizations) )
		{
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('supervisor' => true,
				'controller' => 'events', 'action' => 'index'));
		}

		$conditions = array(
			'Event.organization_id' => $user_organizations
		);

		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['limit'] = 15;

		$events = $this->Paginator->paginate();

		//$events = $this->Event->find('all', array('conditions' => $conditions) ) ;
		$this->set( compact('events') );
	}

	public function coordinator_view($id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('write');

		if( !$this->_CurrentUserCanRead($user_organizations) )
		{
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('supervisor' => true,
				'controller' => 'events', 'action' => 'view', $id));
		}

		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		// summary all time
		//$users = $this->_GetUsersByOrganization($id);

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

		$conditions = array(
			'Time.event_id' => $id
		);
		$fields = array(
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as EventTotal'
		);
		$event_total = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
		$this->set( compact('event_total') );

		// versus

//		$times = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
		
		$this->set( compact('times', 'event') );
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

		$events = $this->Paginator->paginate('Event');
		$this->set( compact('events') );
	}

	public function supervisor_view($id = null)
	{
		$user_organizations = $this->_GetUserOrganizationsByPermission('read');

		if( !$this->_CurrentUserCanRead($user_organizations) )
		{
			$this->Session->setFlash('You do not have permission.', 'danger');
			return $this->redirect(array('volunteer' => true,
				'controller' => 'events', 'action' => 'view', $id));
		}

		$sql_date_fmt = 'Y-m-d H:i:s';
		$contain = array('Event');

		// summary all time
		//$users = $this->_GetUsersByOrganization($id);

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
		// $user_organizations = $this->_GetUserOrganizationsByPermission('publish');

		// if( !$this->_CurrentUserCanPublish($user_organizations) )
		// {
		// 	$this->Session->setFlash('You do not have permission.', 'danger');
		// 	return $this->redirect('../../events/');
		// }

		// $conditions = array(
		// 	'Event.organization_id' => $user_organizations
		// );

		//$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['limit'] = 15;
		$this->Paginator->settings['contain'] = array('Organization');

		$events = $this->Paginator->paginate('Event');
		$this->set( compact('events') );
	}

	public function volunteer_view($event_id = null)
	{
		$event = $this->Event->findByEventId($event_id);

		// if( empty($event) )
		// {
		// 	throw new NotFoundException(__('Event does not exist') );
		// }
		// if( !$this->_CurrentUserCanPublish($event['Event']['organization_id']) )
		// {
		// 	$this->Session->setFlash('You do not have permission.','danger');
		// 	return $this->redirect('../../events/view/'.$event_id);
		// }

		$this->set( compact('event') );
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

	}


}
