<?php
App::uses('AppController', 'Controller');


/**
 * Organizations Controller
 *
 * @property Organization $Organization
 * @property PaginatorComponent $Paginator
 */
class OrganizationsController extends AppController
{

	public function beforeFilter()
	{
		parent::beforeFilter();
		// Allow guest users to open event index and event view
		$this->Auth->allow('go_view', 'go_add', 'go_index');
	}

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
	public $helpers = array('Address', 'PhpExcel');

	public function api_get_organization($organization_id)
	{
		$organization = $this->Organization->find('first', array(
			'conditions' => array(
				'organization_id' => $organization_id
			),
			'contain' => array()
		));

		if (!$organization) {
			throw new \JsonSchema\Exception\ResourceNotFoundException("Organization not found.");
		}

		$this->set('organization', $organization['Organization']);
		$this->set('_serialize', array('organization'));
	}

	public function api_get_organization_events($organization_id) {
		$organization = $this->Organization->find('first', [
			'conditions' => [
				'organization_id' => $organization_id
			],
			'contain' => []
		]);

		if( !$organization ) {
			throw new NotFoundException('Unable to find organization by id ' . $organization_id);
		}

		$fields = $this->_CurrentUserCanWrite($organization_id)
			? [
				'*'
			]
			: [
				'Event.event_id',
				'Event.title',
				'Event.description',
				'Event.start_time',
				'Event.stop_time'
			];

		$events = $this->Organization->Event->find('all', [
			'conditions' => [
				'Event.organization_id' => $organization_id
			],
			'contain' => [

			],
			'fields' => $fields
		]);

		$this->set([
			'events' => $events,
			'_serialize' => ['organization', 'events']
		]);
	}

	public function api_get_organization_role($organization_id)
	{
		$organization = $this->Organization->find('first', array(
			'conditions' => array(
				'organization_id' => $organization_id
			),
			'contain' => array()
		));

		if (!$organization) {
			throw new \JsonSchema\Exception\ResourceNotFoundException("Organization not found.");
		}

		$permission = $this->Organization->Permission->find('first', array(
			'conditions' => array(
				'organization_id' => $organization['Organization']['organization_id'],
				'user_id' => $this->Auth->user('user_id')
			),
			'contain' => array()
		));

		$this->set('permission', $permission['Permission']);
		$this->set('_serialize', array('permission'));
	}


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
	public function go_add()
	{
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
	public function go_index($id = null)
	{
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
		if (!$this->Organization->exists($id)) {
			throw new NotFoundException(__('Invalid organization'));
		}

		$organizationOptions = array(
			'conditions' => array('
				Organization.organization_id' => $id));
		$eventOptions = array(
			'conditions' => array(
				'Event.start_time >' => date('Y-m-d H:i:s'),
				'Event.organization_id' => $id
			)
		);
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
		$this->Paginator->settings['contain'] = array(
			'Address',
			'Event' => array(
				'conditions' => array(
					'Event.stop_time >= Now()'
				)
			),
			'Permission'
		);

		$pag_organizations = $this->Paginator->paginate();

		$this->set(compact('pag_organizations'));

		$title_for_layout = sprintf(__('Coordinating Organizations'));
		$this->set(compact('title_for_layout'));
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
		if (!$this->Organization->exists($id)) {
			throw new NotFoundException(__('Invalid organization'));
		}

		// Get the organization to edit.
		$organization = $this->Organization->findByOrganizationId($id);

		$this->set('organization_id', $id);

		//Check user permissions.
		if ($this->_CurrentUserCanWrite($organization['Organization']['organization_id'])) {
			if ($this->request->is(array('post', 'put'))) {
				$entry = $this->request->data;

				// create address entry
				if (!empty($entry['Address'])) {
					$address_ids = $this->_ProcessAddresses($entry['Address'], $this->Organization->Address);
				}


				if (!empty($entry['Address'])) {
					unset($entry['Address']);
				}


				if (!empty($address_ids)) {
					$entry['Address'] = $address_ids;
				}

				/*
					keep this block before the Organization save.
					The database is set to delete 0-0-0 permissions before Organizations are saved.
				*/
				foreach ($entry['Permission'] as $permission) {
					$permissions = array(
						'permission_id' => $permission['permission_id'], // without this, we will get integrity violations
						// 'user_id' => $permission['user_id'],
						// 'organization_id' => $id,
						'publish' => $permission['publish'],
						'write' => $permission['write'],
						'read' => $permission['read']
					);
					$this->Organization->Permission->saveAll($permissions);
				}

				if ($this->Organization->save($entry)) {

					$this->Session->setFlash(__('The organization has been saved.'), 'success');
					return $this->redirect(array('action' => 'coordinator_index'));
				} else {
					$this->Session->setFlash(__('The organization could not be saved. Please, try again.'));
				}
			} else {
				$options = array('conditions' => array('Organization.' . $this->Organization->primaryKey => $id));
				$this->request->data = $this->Organization->find('first', $options);
			}
		} else {
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

		$title_for_layout = sprintf(__('Edit this organization'));
		$this->set(compact('title_for_layout'));
	}

	public function coordinator_delete($organization_id = null)
	{
		if (!$this->Organization->exists($organization_id)) {
			throw new NotFoundException(__('Invalid organization'));
		}

		if ($this->_CurrentUserCanWrite($organization_id)) {
			$this->Organization->delete($organization_id);

			$this->redirect(
				array(
					'volunteer' => false,
					'controller' => 'users',
					'action' => 'activity'
				)
			);
		} else {
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => $organizations,
					'action' => 'index'
				)
			);
		}

		$title_for_layout = sprintf(__('Delete this organization'));
		$this->set(compact('title_for_layout'));
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

		$title_for_layout = sprintf(__('View your supervising organizations'));
		$this->set(compact('title_for_layout'));
	}

	/**
	 * _getDateConditions
	 * --
	 * generates and returns a valid SQL date/time range
	 * */
	private function _getDateConditions($period = null, $model = "Time")
	{
		if ($period == null)
			$period = 'mtd';

		if (!in_array($period, array('custom', 'ytd', 'mtd', 'last_month')))
			$period = 'mtd';

		if ($period == 'custom' && empty($this->request->query['start_date']))
			$period = 'mtd';

		if ($period == 'custom' && empty($this->request->query['stop_date']))
			$period = 'mtd';


		/*
			start_date <= Time.start_time < stop_date
		*/
		switch ($period) {
			case 'ytd':
				$start_date = mktime(0, 0, 0, 1, 1, date('Y'));
				$stop_date = mktime(0, 0, 0, 1, 1, date('Y') + 1);
				break;
			case 'last_month': // between 1st of last month and 1st of this month
				$one_month_ago = strtotime('-1 month');
				$start_date = mktime(0, 0, 0, date('n', $one_month_ago), 1, date('Y', $one_month_ago));
				$stop_date = mktime(0, 0, 0, date('n'), 1, date('Y'));
				break;
			case 'mtd':
				$one_month_future = strtotime('+1 month');
				$start_date = mktime(0, 0, 0, date('n'), 1, date('Y'));
				$stop_date = mktime(0, 0, 0, date('n', $one_month_future), 1, date('Y', $one_month_future));
				break;
			case 'custom':
				{
					$query_start_date = $this->request->query['start_date'];
					$query_stop_date = $this->request->query['stop_date'];
					$start_date = mktime(0, 0, 0,
						$query_start_date['month'],
						$query_start_date['day'],
						$query_start_date['year']
					);
					$stop_date = mktime(0, 0, 0,
						$query_stop_date['month'],
						$query_stop_date['day'],
						$query_stop_date['year']
					);
					$stop_date = $stop_date + (60 * 60 * 24); // less than the next day
					break;
				}
		}

		// date('Y-m-d H:i:s',
		$date = new DateTime();
		return array(
			"$model.start_time >=" => $date->setTimestamp($start_date)->format("Y-m-d H:i:s"),
			"$model.start_time <" => $date->setTimestamp($stop_date)->format("Y-m-d H:i:s")
		);
	}

	/**
	 * supervisor_dashboard
	 *
	 * @throws ForbiddenException, NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function supervisor_dashboard($organization_id = null, $period = null, $format = null)
	{
		if ($this->_CurrentUserCanRead($organization_id)) {
			/*
				This is very specifically the information that volunteers have chosen to share with users;
				This should not include time associated with an organization's events
			*/

			$time_conditions = $this->_getDateConditions($period);
			$time_conditions['Time.status'] = "approved";

			$this->set(compact('period', 'time_conditions'));

			$conditions = array(
				'Organization.organization_id' => $organization_id,
			);
			$contain = array(
				'Permission' => array(
					'conditions' => array(
						'Permission.publish' => true
					),
					'fields' => array(),
					'User' => array(
						'fields' => array('*'),
						'Time' => array(
							'conditions' => $time_conditions,
							'fields' => array(
								'COUNT( Time.time_id ) as Count',
								'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time)/60 ) as Duration'
							)
						)
					)
				)
			);
			$fields = array(
				'Organization.name'
			);

			$time = $this->Organization->find('first', compact('fields', 'conditions', 'contain'));

			$users = $time['Permission'];
			$organization = $time['Organization'];

			$row_counts = Set::combine($users, '{n}.User.user_id', '{n}.User.Time.0.Time.0.Count');
			$row_totals = Set::combine($users, '{n}.User.user_id', '{n}.User.Time.0.Time.0.Duration');
			$duration_total = array_sum($row_totals);
			$count_total = array_sum($row_counts);

			$this->set(compact('organization', 'users', 'row_counts', 'row_totals', 'duration_total', 'count_total'));


		} else {
			return $this->redirect(
				array(
					'go' => true,
					'controller' => 'organizations',
					'action' => 'index'
				)
			);
		}
	}

	public function coordinator_dashboard($organization_id = null, $period = null, $format = null)
	{
		if ($this->_CurrentUserCanWrite($organization_id)) {
			$event_conditions = $this->_getDateConditions($period, 'Event');

			$conditions['Organization.organization_id'] = $organization_id;
			$contain = array();
			$organization = $this->Organization->find('first', compact('conditions', 'contain'));

			$db = $this->Organization->getDataSource();
			$results = $db->fetchAll(
				"SELECT Event.event_id, Event.title, Event.start_time, Event.stop_time, Event.start_token, Event.stop_token, SUM( TIMESTAMPDIFF(
MINUTE , Time.start_time, Time.stop_time ) /60 ) AS Duration, COUNT( DISTINCT (
Time.user_id
) ) AS Volunteers, COUNT( Time.time_id ) AS Engagements
FROM `events` Event
LEFT JOIN events_times EventTime ON Event.event_id = EventTime.event_id
LEFT JOIN times Time ON EventTime.time_id = Time.time_id
WHERE Event.organization_id = :organization_id
AND Event.start_time >= :start_date
AND Event.start_time < :stop_date
GROUP BY Event.event_id, Event.title, Event.start_time, Event.stop_time, Event.start_token, Event.stop_token",
				array(
					'organization_id' => $organization_id,
					'start_date' => $event_conditions['Event.start_time >='],
					'stop_date' => $event_conditions['Event.start_time <']
				));


			$this->set(compact('results', 'organization', 'event_conditions'));

		} else {
			return $this->redirect(
				array(
					'go' => true,
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
		if ($this->_CurrentUserCanRead($organization_id)) {
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
				'Time.user_id' => $users
			);
			$fields = array(
				'User.*',
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as UserSumTime',
				'COUNT( Time.time_id ) as UserNumberEvents'
			);
			$group = array(
				'User.user_id'
			);

			$userHours = $this->Organization->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group));
			$this->set(compact('userHours', 'events'));
		} else {
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
		if ($this->Auth->user('user_id')) {
			if ($organization_id != null) {
				$conditions = array(
					'Permission.user_id' => $this->Auth->user('user_id'),
					'Permission.organization_id' => $organization_id
				);

				if ($this->Organization->Permission->deleteAll($conditions)) {
					$this->Session->setFlash(__('You have successfully left this organization.'), 'success');
					$this->redirect(
						array(
							'volunteer' => false,
							'controller' => 'users',
							'action' => 'activity'
						)
					);
				} else {
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

			$title_for_layout = sprintf(__('Leave this organization'));
			$this->set(compact('title_for_layout'));
		} else {
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
		if ($organization_id != null) {
			if (!$this->Organization->exists($organization_id)) {
				// get out of here
				$this->Session->setFlash(__('This request was invalid.'), 'danger');
				return $this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			if (!$this->_CurrentUserCanPublish($organization_id)) {
				// get out of here
				$this->Session->setFlash(__('current user cannot publish'), 'danger');
				return $this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			$user_id = $this->Auth->user('user_id');
			$permission = $this->Organization->Permission->findByUserIdAndOrganizationId($user_id, $organization_id); // yes, this will work

			if (empty($permission)) {
				// permissions didn't exist.  redirect gracefully
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');

				// redirect here so the other stuff isn't attempted
				return $this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			$this->Organization->Permission->id = $permission['Permission']['permission_id'];

			if ($this->Organization->Permission->saveField('publish', false)) {
				// redirect
				$this->Session->setFlash(__('You are no longer publishing activity to this organization.'), 'success');
			} else {
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
		$this->set(compact('data'));

		$title_for_layout = sprintf(__('Leave this organization'));
		$this->set(compact('title_for_layout'));
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
		if ($organization_id != null) {
			if (!$this->Organization->exists($organization_id)) {
				// get out of here
				$this->Session->setFlash(__('This request was invalid.'), 'danger');
				return $this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			if (!$this->_CurrentUserCanWrite($organization_id)) {
				// get out of here
				$this->Session->setFlash(__('current user cannot read'), 'danger');
				return $this->redirect(
					array(
						'supervisor' => true,
						'controller' => 'organizations',
						'action' => 'leave'
					)
				);
			}

			$user_id = $this->Auth->user('user_id');
			$permission = $this->Organization->Permission->findByUserIdAndOrganizationId($user_id, $organization_id); // yes, this will work

			if (empty($permission)) {
				// permissions didn't exist.  redirect gracefully
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');

				// redirect here so the other stuff isn't attempted
				return $this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			$this->Organization->Permission->id = $permission['Permission']['permission_id'];

			if ($this->Organization->Permission->saveField('write', false)) {
				// redirect
				$this->Session->setFlash(__('You are no longer able to coordinate for this organization.'), 'success');
			} else {
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
		$this->set(compact('data'));

		$title_for_layout = sprintf(__('Leave this organization'));
		$this->set(compact('title_for_layout'));

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
		if ($organization_id != null) {
			if (!$this->Organization->exists($organization_id)) {
				// get out of here
				$this->Session->setFlash(__('This request was invalid.'), 'danger');
				return $this->redirect(
					array(
						'volunteer' => false,
						'controller' => 'users',
						'action' => 'activity'
					)
				);
			}

			if (!$this->_CurrentUserCanRead($organization_id)) {
				// get out of here
				$this->Session->setFlash(__('current user cannot read'), 'danger');
				return $this->redirect(
					array(
						'volunteer' => true,
						'controller' => 'users',
						'action' => 'leave'
					)
				);
			}

			$user_id = $this->Auth->user('user_id');
			$permission = $this->Organization->Permission->findByUserIdAndOrganizationId($user_id, $organization_id); // yes, this will work

			if (empty($permission)) {
				// permissions didn't exist.  redirect gracefully
				$this->Session->setFlash(__('We are unable to process your request at this time.'), 'danger');

				// redirect here so the other stuff isn't attempted
				$this->redirect('/');
			}

			$this->Organization->Permission->id = $permission['Permission']['permission_id'];

			if ($this->Organization->Permission->saveField('read', false)) {
				// redirect
				$this->Session->setFlash(__('You are no longer able to supervise activity for this organization.'), 'success');
			} else {
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
		$this->set(compact('data'));

		$title_for_layout = sprintf(__('Leave this organization'));
		$this->set(compact('title_for_layout'));
	}

	private function _organization_join($organization_id)
	{
		$success = false;
		$message = __("There was a problem joining this organization");

		if (!$this->Organization->exists($organization_id))
			return compact('success', 'message');

		$conditions = array(
			'Permission.organization_id' => $organization_id,
			'Permission.user_id' => $this->Auth->user('user_id')
		);
		$contain = array('Organization');

		$permission = $this->Organization->Permission->find('first', compact('conditions', 'compact'));

		/*
			Check for existing permission row; update if exists
		*/
		if (!empty($permission)) {
			$save = $permission['Permission'];
			$save['publish'] = true;

			if ($this->Organization->Permission->save($save)) {
				$success = true;
				$message = __("You are now publishing your activity to %s.", $permission['Organization']['name']);
			}
		} else {
			$save = array(
				'organization_id' => $organization_id,
				'user_id' => $this->Auth->user('user_id'),
				'publish' => 1,
				'read' => 0,
				'write' => 0
			);
			if ($this->Organization->Permission->save($save)) {
				$this->Organization->id = $organization_id;
				$success = true;
				$message = __("You are now publishing your activity to %s", $this->Organization->field('name'));
			} else {
				$message = __("Problem *saving* new permission entry");
				CakeLog::write('debug', print_r($this->Organization->Permission->validationErrors, true));
			}
		}

		return array(
			'success' => $success,
			'message' => $message
		);
	}

	public function api_join($organization_id)
	{
		$this->response->type('json');
		$this->response->body(json_encode(array('result' => $this->_organization_join($organization_id))));

		return $this->response;
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

		if ($this->request->is(array('post', 'put'))) {
			foreach ($this->request->data['Organization'] as $organization) {
				//debug($organization);

				if ($organization['publish'] == '1') {

					$entry['Permission'] = array(
						'user_id' => $this->Auth->user('user_id'),
						'organization_id' => $organization['organization_id'],
						'publish' => true
					);
					$this->Organization->Permission->create();
					$this->Organization->Permission->save($entry);
				}
			}

			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => 'users',
					'action' => 'activity'
				)
			);
		}

		$conditions = array();

		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 10,
			'contain' => array(
				'Event' => array(
					'conditions' => array(
						'Event.stop_time >= Now()'
					)
				),
				'Permission' => array(
					'conditions' => array(
						'Permission.user_id' => $this->Auth->user('user_id'),
						'Permission.publish' => true
					)
				)
			)
		);

		$organizations = $this->Paginator->paginate();
		$this->set(compact('organizations'));

		$title_for_layout = sprintf(__('Join an organization'));
		$this->set(compact('title_for_layout'));
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
		if ($this->Auth->user('user_id')) {
			$title_for_layout = sprintf(__('Create an organization'));
			$this->set(compact('title_for_layout'));
			if ($this->request->is('post')) {
				// create address entry
				$address_ids = isset($this->request->data['Address']) ? $this->_ProcessAddresses($this->request->data['Address'], $this->Organization->Address) : null;

				unset($this->request->data['Address']);

				if (!empty($address_ids)) {
					$this->request->data['Address'] = $address_ids;
				}

				$this->Organization->create();

				if ($this->Organization->save($this->request->data)) {
					$this->Session->setFlash(__('The organization has been created.'), 'success');

					$conditions['Permission'] = array(
						'user_id' => $this->Auth->user('user_id'),
						'organization_id' => $this->Organization->id,
						'read' => true
					);

					$this->Organization->Permission->save($conditions);

					return $this->redirect(
						array(
							'volunteer' => false,
							'controller' => 'users',
							'action' => 'activity'
						)
					);
				} else {
					$this->Session->setFlash(__('The organization could not be created. Please, try again.'));
				}
			}
		} else {
			return $this->redirect(
				array(
					'volunteer' => false,
					'controller' => $organizations,
					'action' => 'index'
				)
			);
		}
	}

	public function volunteer_time($organization_id)
	{
		if ($this->Organization->exists($organization_id)) {
			debug("this is a valid organization");
		} else {
			return $this->redirect(
				array(
					'controller' => 'times',
					'action' => 'index'
				)
			);
		}
	}
}
