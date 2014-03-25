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
	public $helpers = array('Address');


/**
 * index method
 *
 * @return void
 */
	public function index() 
	{
		$organizations = $this->Organization->find('all');
		$this->set(compact('organizations'));
		//debug($organizations);
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
		$eventOptions = array('conditions' => array('Event.start_time >' => date('m/d/Y h:i:s a', time()),
													'Event.' . $this->Organization->primaryKey => $id) );
		$this->set('organization', $this->Organization->find('first', $organizationOptions));
		$events = $this->Organization->Event->find('all', $eventOptions);
		//debug($events);	
		$this->set(compact('events'));
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
					$this->Organization->Address->create();
					$this->Organization->Address->save($address);
					// get the address_id for the join table
					$address_ids['Address'][] = $this->Organization->Address->id;
				}
			}

			unset( $this->request->data['Address'] );

			if( !empty($address_ids) )
				$this->request->data['Address'] = $address_ids;

			$this->Organization->create();

			if ($this->Organization->save($this->request->data))
			{
				$this->Session->setFlash(__('The organization has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The organization could not be saved. Please, try again.'));
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
		if (!$this->Organization->exists($id)) 
		{
			throw new NotFoundException(__('Invalid organization'));
		}

		if ($this->request->is(array('post', 'put'))) 
		{

			foreach ($this->request->data['Address'] as $address) 
			{
				$this->Organization->Address->save($address);
			}

			if ($this->Organization->save($this->request->data)) {
				$this->Session->setFlash(__('The organization has been saved.'));
				//return $this->redirect(array('action' => 'index'));
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

		$address = $this->Organization->Address->find('all');
		$this->set(compact('address'));
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
		$this->Organization->id = $id;
		if (!$this->Organization->exists()) 
		{
			throw new NotFoundException(__('Invalid organization'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Organization->delete()) 
		{
			$this->Session->setFlash(__('The organization has been deleted.'));
		} 
		else 
		{
			$this->Session->setFlash(__('The organization could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
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
		$conditions = array(
			'Permission.user_id' => $this->Auth->user('user_id')
		);
		// get a list of user's organizations
		$organizations = $this->Organization->Permission->find('all', array('conditions' => $conditions));
		$this->set(compact('organizations'));
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
		// if (!$this->Organization->exists($id)) 
		// {
		// 	throw new NotFoundException(__('Invalid organization'));
		// }

		// Get the organization to edit.
		$organization = $this->Organization->findByOrganizationId($id);
	
		// Check user permissions.
		// if($this->_CurrentUserCanWrite($organization['Organization']['organization_id'])) 
		// {
			if ($this->request->is(array('post', 'put'))) 
			{
				$entry = $this->request->data;

				foreach($entry['Address'] as $address)
				{
					// at a minimum, an address should have a line 1, city, state and zip
					if( 
						!empty( $address['address1'] ) && 
						!empty( $address['city'] ) && 
						!empty( $address['state'] ) &&
						!empty( $address['zip'] ) )
					{
						$this->Organization->Address->create();

						if($this->Organization->Address->save($address))
						{
							$this->Session->setFlash(__('The address has been saved.'));
						}
						else 
						{
							$this->Session->setFlash(__('The address has not been saved.'));
						}
						//return;
						// get the address_id for the join table
						$address_ids['Address'][] = $this->Organization->Address->id;
					}
				}

				if( !empty($address_ids) )
				{
					$this->request->data['Address'] = $address_ids;
					debug($address_ids);
				}
					

				if ($this->Organization->save($entry)) 
				{
					$this->Session->setFlash(__('The organization has been saved.'));
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
		//}
		// else 
		// {
		// 	throw new ForbiddenException("You are not allowed to edit this organization.");
		// }

		$conditions = array(
			'Permission.user_id' => $this->Auth->user('user_id')
		);
		// get a list of user's organizations
		$data = $this->Organization->Permission->find('all', array('conditions' => $conditions));
		$this->set(compact('data'));

		$addresses = $this->Organization->Address->find('all');
		$this->set(compact('addresses'));
	}

	public function _find_organization_time($array) 
	{
		debug($array);
	}

/**
 * manager_view
 *
 * @throws ForbiddenException, NotFoundException
 * @param string $id
 * @return void
*/
	public function supervisor_view($id = null) 
	{
		// Get the organization to edit.
		$organization = $this->Organization->findAllByOrganizationId($id);

		// Check user permissions.
		// if($this->_CurrentUserCanRead($organization['Organization']['organization_id'])) 
		// {
			if (!$this->Organization->exists($id)) 
			{
				throw new NotFoundException(__('Invalid organization'));
			}

			$options = array('conditions' => array('Organization.' . $this->Organization->primaryKey => $id));
			$this->set('organization', $this->Organization->find('first', $options));

			$organization = $this->Organization->Event->find('first', $options);

			// Find total time volunteered at the organization
			$organizationTime = array('hours' => 0, 'minutes' => 0);
			foreach ($organization['Time'] as $data) 
			{
				$start = new DateTime($data['start_time']);
				$stop = new DateTime($data['stop_time']);
				
				$time = $start->diff($stop);
				$time->format("%H");
				$organizationTime['hours'] += $time->h;
				$organizationTime['minutes'] += $time->i;
			}

			// Correctly set hours and minutes.
			if($organizationTime['minutes'] >= 60) 
			{
				$hours = intval($organizationTime['minutes'] / 60);
				$minutes = intval($organizationTime['minutes'] % 60);
				$organizationTime['hours'] += $hours;
				$organizationTime['minutes'] = $minutes;
			}

			$this->set('organizationTime', $organizationTime);

			// Create an array with users and total volunteer time.
			$conditions = array(
				'Permission.user_id' => $this->Auth->user('user_id')
			);
			// get a list of user's organizations
			$data = $this->Organization->Permission->find('all', array('conditions' => $conditions));

			foreach($data as $user) {
				debug($user);
				debug($organization['Time']);
				// $userConditions = array(
				// 	'Time.user_id' => $user->primaryKey)
				// $userInfo = $organization['Time']->find('first', array('conditions' => $userConditions));
			}

		// }
		// else 
		// {
			// 	throw new ForbiddenException("You are not allowed to view this organization");
			// }
	}


/**
 * leave
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function volunteer_leave() 
	{
		$conditions = array(
			'Permission.user_id' => $this->Auth->user('user_id')
		);
		// get a list of user's organizations
		$data = $this->Organization->Permission->find('all', array('conditions' => $conditions));
		$this->set(compact('data'));

		if ($this->request->is(array('post', 'put'))) 
		{
			debug($this->request->data);
		}
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
			$i = 0;
			foreach($this->request->data['Organization']['Organization'] as $organization)
			{
				$entry['Permission'] = array(
				'user_id' => $this->Auth->user('user_id'),
				'organization_id' => $this->request->data['Organization']['Organization'][$i]);

				$this->Organization->Permission->create();
				$this->Organization->Permission->save($entry);

				$organization_ids['Organization']['Organization'][] = $this->Organization->id;

				$i++;
			}

			unset($this->request->data['Organization']['Organization']);

			if(!empty($organization_ids))
			{
				$this->request->data['Organization']['Organization'] = $organization_ids;
			}
			return $this->redirect(array('action' => 'index'));
		}

		$organizations = $this->Organization->find('list');
		//debug($organizations);
		$this->set(compact('organizations'));
	}


/**
 * publish
 *
 * @throws NotImplementedException
 * @param string $id
 * @return void
*/
	public function volunteer_create() 
	{
		if ($this->request->is('post'))
		{

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
					$this->Organization->Address->create();
					$this->Organization->Address->save($address);
					// get the address_id for the join table
					$address_ids['Address'][] = $this->Organization->Address->id;
				}
			}

			unset( $this->request->data['Address'] );

			if( !empty($address_ids) )
			{
				$this->request->data['Address'] = $address_ids;
			}
			
			$this->Organization->create();

			if ($this->Organization->save($this->request->data))
			{
				$this->Session->setFlash(__('The organization has been created.'));
				return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The organization could not be created. Please, try again.'));
			}
		}
	}
}
