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
		$eventOptions = array('conditions' => array('Event.start_time >' => date('Y-m-d H:i:s'),
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
	private function delete($id = null) 
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
				

				if( !empty($this->request->data['Address']) )
				{
					unset( $this->request->data['Address'] );
				}
				

				if( !empty($address_ids) )
				{
					$this->request->data['Address'] = $address_ids;
				}

				if ($this->Organization->save($entry['Organization'])) 
				{
					foreach($entry['Permission'] as $permission) {
						$permissions = array(
							'user_id' => $permission['user_id'],
							'organization_id' => $id,
							'publish' => $permission['publish']	,
							'write' => $permission['write'],
							'read' => $permission['read']				
						);
						$this->Organization->Permission->saveAll($permissions);
					}
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
		}
		else 
		{
			return $this->redirect(array('supervisor' => true,
										 'controller' => 'organizations',
										 'action' => 'view',
										 $id));
		}

		$conditions = array(
			'Permission.organization_id' => $id
		);
		// get a list of user's organizations
		$data = $this->Organization->Permission->find('all', array('conditions' => $conditions));
		$this->set(compact('data'));

		$addresses = $this->Organization->Address->find('all');
		$this->set(compact('addresses'));
	}


	public function coordinator_delete($id = null) 
	{
		if (!$this->Organization->exists($id)) 
		{
			throw new NotFoundException(__('Invalid organization'));
		}

		$this->Organization->delete($id);

		$this->redirect(
			array(
				'volunteer' => false,
				'controller' => 'users',
				'action' => 'activity'
			)
		);
	}


/**
 * supervisor_view
 *
 * @throws ForbiddenException, NotFoundException
 * @param string $id
 * @return void
*/
	public function supervisor_view($period = null) 
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

			$time_data = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'contain' => $contain, 'order' => $order) );
			$this->set( compact('time_data', 'period') );

		}

		// summary all time
		$users = $this->Organization->Permission->find('all');
		debug($users);
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
			'Time.user_id' => $this->Auth->user('user_id'),
			'Time.start_time >=' => date($sql_date_fmt, strtotime('1 month ago') )
		);
		$fields = array(
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as OrganizationPastMonth'
		);
		$summary_past_month = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
		$this->set( compact('summary_past_month') );

		// summary year
		$conditions = array(
			'Time.user_id' => $this->Auth->user('user_id'),
			'Time.start_time >=' => date($sql_date_fmt, strtotime('1 year ago') )
		);
		$fields = array(
			'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) ) as OrganizationPastYear'
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


/**
 * leave
 *
 * @throws ForbiddenException
 * @param string $id
 * @return void
*/
	public function volunteer_leave($organization_id = null) 
	{

		if ($organization_id != null) 
		{
			$conditions = array(
				'Permission.user_id' => $this->Auth->user('user_id'),
				'Permission.organization_id' => $organization_id
			);
			$this->Organization->Permission->deleteAll($conditions);
		}

		$conditions = array(
			'Permission.user_id' => $this->Auth->user('user_id')
		);

		$contain = array(
			// 'Address',
			'Organization',
			// 'Skill',
			// 'Time'
		);

		// get a list of user's organizations
		$data = $this->Organization->Permission->find('all', array('conditions' => $conditions, 
																   'contain' => $contain));
		$this->set(compact('data'));
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
					'organization_id' => $this->request->data['Organization']['Organization'][$i],
					'publish' => true
				);

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

					return $this->redirect(array('action' => 'index'));
				} 
				else 
				{
					$this->Session->setFlash(__('The organization could not be created. Please, try again.'));
				}
			}
		}
		else 
		{
			return $this->redirect(array(
											'volunteer' => false,
											'controller' => $organizations,
											'action' => 'index'
										)
								);
		}
	}
}
