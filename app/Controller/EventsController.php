<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('MandrillListener', 'Lib/Event');
App::uses('Hash', 'Utilities');

/**
 * Events Controller
 *
 * @property Event $Event
 * @property PaginatorComponent $Paginator
 */
class EventsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    public $helpers = array('Comment');

    public function beforeFilter()
    {
        parent::beforeFilter();
        // Allow guest users to open event index and event view
        $this->Auth->allow('index', 'view', 'go_view', 'go_add', 'go_index');
    }

    public function go_view($event_id)
    {
        $event = $this->Event->findByEventId($event_id, array('contain' => array()));

        if ($this->_CurrentUserCanWrite($event['Event']['organization_id'])) {
            return $this->redirect(array('coordinator' => true, 'action' => 'view', $event_id));
        } elseif ($this->_CurrentUserCanRead($event['Event']['organization_id'])) {
            return $this->redirect(array('supervisor' => true, 'action' => 'view', $event_id));
        } elseif ($this->Auth->user('user_id') != null) {
            return $this->redirect(array('volunteer' => true, 'action' => 'view', $event_id));
        } else {
            return $this->redirect(array('volunteer' => false, 'action' => 'view', $event_id));

        }
    }

    public function go_add()
    {
        $this->redirect(array('coordinator' => true, 'action' => 'add'));
    }

    /**
     * COORDINATOR
     * URL: localhost/coordinator/events/...
     */
    public function coordinator_delete($event_id = null)
    {
        if (!$this->Event->exists($event_id)) {
            $this->Session->setFlash(
                __("There is no event, or you do not have permission to delete it."),
                'danger'
            );
            return $this->redirect(array('action' => 'index'));
        }

        $event = $this->Event->findByEventId($event_id);

        if ($this->_CurrentUserCanWrite($event['Organization']['organization_id'])) {
            $this->getEventManager()->dispatch(new CakeEvent('App.Event.Delete.Before', $this, array('event' => $event)));

            if ($this->Event->delete($event_id)) {
                $this->getEventManager()->dispatch(new CakeEvent('App.Event.Delete.Success', $this, array('event' => $event)));
            } else {
                $this->getEventManager()->dispatch(new CakeEvent('App.Event.Delete.Failed', $this, array('event' => $event)));
            }
        } else {
            $this->Session->setFlash('You do not have permission.', 'danger');
        }

        return $this->redirect(array(
            'coordinator' => true,
            'controller' => 'events',
            'action' => 'dashboard',
        ));
    }

    public function coordinator_add($id = null)
    {
        $user_organizations = $this->_GetUserOrganizationsByPermission('write');

        if (!empty($user_organizations)) // do we have organizations?
        {
            if ($this->request->is('post')) {
                if (!$this->Event->validTimes()) {
                    return false;
                }

                /*
                process relations
                 */
                if (isset($this->request->data['Skill'])) {
                    $skill_ids = $this->_ProcessSkills($this->request->data['Skill'], $this->Event->Skill);
                }

                if (isset($this->request->data['Address'])) {
                    $address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->Event->Address);
                }

                unset($this->request->data['Address'], $this->request->data['Skill']);

                if (!empty($address_ids)) {
                    $this->request->data['Address'] = $address_ids;
                }

                if (!empty($skill_ids)) {
                    $this->request->data['Skill'] = $skill_ids;
                }

                /*
                process the event hashes
                 */
                $hash = sha1(json_encode($this->request->data['Event'])); // serializes the event and hashes it

                /*
                by choosing 9 characters from a base 16 hash, there are a total possible
                68,719,476,736 hashes.  this should be adequate.
                 */
                $complexity = 4;
                $this->request->data['Event']['start_token'] = substr($hash, 0, $complexity); // 9 starting characters
                $this->request->data['Event']['stop_token'] = substr($hash, -$complexity, $complexity); // 9 ending characters

                // create and save the event
                $this->Event->create();
                if ($this->Event->saveAll($this->request->data)) {
                    $this->getEventManager()->dispatch(new CakeEvent('App.Event.Add.Success', $this, array('event_id' => $this->Event->id)));

                    $this->Session->setFlash(
                        __('The event has been saved.'),
                        'success'
                    );
                    return $this->redirect(array(
                        'controller' => 'events',
                        'action' => 'view',
                        $this->Event->id,
                        'coordinator' => true,
                    ));
                } else {
                    $this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
                }
            }

            $address = $this->Event->Address->find('all');
            $skills = null;
            $this->set(compact('skills', 'organization'));
            $this->set('title_for_layout', __('Creating New Event'));

            //debug($user_organizations);

            $this->set('organizations', $this->Event->Organization->find(
                'list',
                array(
                    'conditions' => array(
                        'Organization.organization_id' => $user_organizations,
                    ),
                    'order' => array('Organization.name'),
                )));
        } else {
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
        $this->organization_id = $event['Event']['organization_id'];

        $user_organizations = $this->_GetUserOrganizationsByPermission('write');

        if ($this->_CurrentUserCanWrite($event['Organization']['organization_id'])) {

            if ($this->request->is(array('post', 'put'))) {
                if (!$this->Event->validTimes()) {
                    return false;
                }

                if (isset($this->request->data['Address'])) {
                    $address_ids = $this->_ProcessAddresses($this->request->data['Address'], $this->Event->Address);
                    unset($this->request->data['Address']);
                    $this->request->data['Address'] = $address_ids;
                }

                if (isset($this->request->data['Skill'])) {
                    $skill_ids = $this->_ProcessSkills($this->request->data['Skill'], $this->Event->Skill);
                    unset($this->request->data['Skill']);
                    $this->request->data['Skill'] = $skill_ids;
                }

                $this->getEventManager()->dispatch(new CakeEvent(
                    'App.Event.Edit.Before',
                    $this,
                    array(
                        'event_id' => $this->request->data['Event']['event_id'],
                    )
                ));

                if ($this->Event->save($this->request->data)) {
                    $this->getEventManager()->dispatch(new CakeEvent('App.Event.Edit.Success', $this, array('event_id' => $this->request->data['Event']['event_id'])));

                    $this->Session->setFlash(__('The event has been saved.'), 'success');
                    return $this->redirect(array('action' => 'view', $this->request->data['Event']['event_id']));
                } else {
                    $this->getEventManager()->dispatch(new CakeEvent('App.Event.Edit.Fail', $this, array('event_id' => $this->request->data['Event']['event_id'])));
                    $this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'danger');
                }
            } else {
                $options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
                $this->request->data = $this->Event->find('first', $options);
            }


            $address = $this->Event->Address->find('all');
            $skills = null;
            $this->set(compact('skills', 'address', 'organization'));
            $this->set('title_for_layout', __('Editing Event - ' . $event['Event']['title']));
            //debug($user_organizations);

            $this->set('organizations', $this->Event->Organization->find(
                'list',
                array(
                    'conditions' => array(
                        'Organization.organization_id' => $user_organizations,
                    ),
                    'order' => array('Organization.name'),
                )));
        } else {
            $this->Session->setFlash('You do not have permission.', 'danger');
            return $this->redirect(array('coordinator' => true,
                'controller' => 'events', 'action' => 'index'));
        }
    }

    public function coordinator_dashboard($period = 'current', $render = "default")
    {
        if (!in_array($render, array('default', 'xlsx'))) {
            $render = 'default';
        }

        if (!in_array($period, array('current', 'archive'))) {
            $period = 'current';
        }

        $user_organizations = $this->_GetUserOrganizationsByPermission('write');

        $conditions = array(
            'Event.organization_id' => $user_organizations,
        );
        if ($period == 'archive') {
            $conditions[] = 'Event.stop_time <= Now()';
        }

        if ($period == 'current') {
            $conditions[] = 'Event.stop_time >= Now()';
        }

        $this->Paginator->settings['conditions'] = $conditions;
        $this->Paginator->settings['limit'] = ($render == 'default') ? 10 :
        $this->Event->find('count', compact('conditions'));
        $this->Paginator->settings['order'] = array(
            'Event.start_time' => ($period == "current") ? 'asc' : 'desc',
        );

        $events = $this->Paginator->paginate();
        $this->set('title_for_layout', __('Ongoing/Upcoming Event Dashboard'));
        $this->set(compact('events', 'period'));

        if ($render == 'xlsx') {
            $this->render('/Events/coordinator_dashboard.xlsx');
        }

    }

    public function coordinator_archive($id = null)
    {
        return $this->redirect(array('action' => 'dashboard', 'archive'));
    }

    public function coordinator_report($event_id = null)
    {
        $user_organizations = $this->_GetUserOrganizationsByPermission('read');

        $sql_date_fmt = 'Y-m-d H:i:s';
        $contain = array('Event');

        $event = $this->Event->find('first', array(
            'conditions' => array('Event.event_id' => $event_id),
            'contain' => array('Organization')
        ));

        if (empty($event)) {
            throw new NotFoundException(__('Page Not Found'));
        }

        if (!$this->_CurrentUserCanRead($event['Event']['organization_id'])) {
            $this->Session->setFlash(__('You may not supervise this event'), 'danger');
            return $this->redirect(array('volunteer' => true,
                'controller' => 'events', 'action' => 'view', $event_id));
        }

        $conditions = array(
            'EventTime.event_id' => $event_id,
        );
        $fields = array(
            'EventTime.*',
            'Time.*',
            'User.*',
            'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as OrganizationAllTime',
            'COUNT( Time.time_id ) as TimeEntryCount',
        );
        $group = array(
            'Time.user_id',
        );

        $times = $this->Event->EventTime->find('all', array(
            'conditions' => [
                'EventTime.event_id' => $event_id
            ],
            'contain' => [
                'Time' => [
                    'User'
                ]
            ]
        ));
        $this->set(compact('times', 'event'));
    }

    public function angular()
    {

    }

    public function coordinator_view($event_id = null)
    {
        $user_organizations = $this->_GetUserOrganizationsByPermission('write');

        $contain = array(
            'Organization',
            'Rsvp' => array('User'),
            'Skill',
            'Address',
            'EventTime' => array(
                'Time' => array(
                	'User' => array()
                ),
            ),
        );

        $event = $this->Event->find('first', array(
            'conditions' => array(
                'Event.event_id' => $event_id, 
                'Event.organization_id' => $user_organizations
            ), 
            'contain' => $contain
        ));

        if( !$event ) {
            throw new NotFoundException('Unable to find any events, or you do not have permission to edit.');
        }

        $this->organization_id = $event['Event']['organization_id'];

        // Find neighbors
        $neighbors = $this->Event->find('neighbors', [
            'field' => 'event_id',
            'value' => $event_id,
            'conditions' => [
                'organization_id' => $this->organization_id
            ],
            'contain' => []
        ]);
        $this->set( compact('neighbors') );

        // Find convertable Organization Time
        App::uses('OrganizationTime', 'Model');
        $OrganizationTime = new OrganizationTime();
        $organization_time = $OrganizationTime->find('all', [
            'conditions' => [
                'OrganizationTime.organization_id' => $event['Organization']['organization_id'],
                'Time.status' => ['approved', 'pending'],
                'OR' => [
                    [
                        // Time.start_time <= Event.stop_time
                        // AND
                        // Time.start_time >= Event.start_time
                        'AND' => [
                            sprintf("Time.start_time <= DATE_ADD('%s', INTERVAL 1 HOUR)", $event['Event']['stop_time']),
                            sprintf("Time.start_time >= DATE_ADD('%s', INTERVAL -1 HOUR)", $event['Event']['start_time'])
                        ]
                    ],
                    [
                        // Time.stop_time <= Event.stop_time
                        // AND
                        // Time.stop_time >= Event.start_tim
                        'AND' => [
                            sprintf("Time.stop_time <= DATE_ADD('%s', INTERVAL 1 HOUR)", $event['Event']['stop_time']),
                            sprintf("Time.stop_time >= DATE_ADD('%s', INTERVAL -1 HOUR)", $event['Event']['start_time'])
                        ]
                    ]
                ]
            ],
            'contain' => [
                'Time' => ['User']
            ]
        ]);
        $this->set( compact('organization_time') );
        //debug($organization_time);

        if (empty($event)) {
            throw new NotFoundException(__('Page Not Found'));
        }

        if (!$this->_CurrentUserCanWrite($event['Event']['organization_id'])) {
            return $this->redirect(array('supervisor' => true, 'controller' => 'events', 'action' => 'view', $event_id));
        }

        $conditions = array(
            'EventTime.event_id' => $event_id,
        );
        $fields = array(
            'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as EventTotal',
        );
        $event_total = $this->Event->EventTime->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
        ));
        $this->set(compact('event_total'));

        /*
         * Event Comments
         */
        $comments = $this->Event->Comment->find('threaded', array(
            'conditions' => array('Comment.event_id' => $event_id),
            'contain' => array('User', 'ParentComment' => array('User')),
            'order' => array('Comment.created ASC'),
        ));
        $this->set(compact('comments'));

        /*
         * User RSVP status, if any
         */
        $user_rsvp_status = $this->Event->Rsvp->find('first', array(
            'conditions' => array(
                'Rsvp.user_id' => $this->Auth->user('user_id'),
                'Rsvp.event_id' => $event_id,
            ),
            'contain' => array(),
        ));
        $this->set(compact('user_rsvp_status'));

//        $times = $this->Event->Time->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group) );
        $this->set('event_id', $event_id);
        $this->set(compact('times', 'event'));
        $this->set('title_for_layout', __('Coordinator: %s - %d%%', $event['Event']['title'], $event['Event']['rsvp_percent']));
    }

    public function volunteer_index($id = null)
    {
        $this->Paginator->settings['limit'] = 15;
        $this->Paginator->settings['contain'] = array('Organization');
        $this->Paginator->settings['conditions'] = array(
            'Event.stop_time > NOW()',
        );
        $this->Paginator->settings['order'] = array(
            'Event.start_time ASC',
        );

        $events = $this->Paginator->paginate('Event');
        $this->set(compact('events'));
        $this->set('title_for_layout', __('Upcoming Events'));
    }

    // GET /events/upcoming
    public function api_get_upcoming_events()
    {
        // TODO: write events api
    }

    // GET /events/recommended
    // GET /events/archive
    // GET /events/supervising
    // GET /events/coordinating

    public function api_get_event($event_id)
    {
        $event = $this->Event->find('first', array(
            'conditions' => array(
                'Event.event_id' => $event_id,
            ),
            'contain' => array(),
        ));

        if (!$this->_CurrentUserCanWrite($event['Event']['organization_id'])) {
            throw new ForbiddenException('Current user is unable to view this resource');
        }

        $this->set('event', $event['Event']);

        $this->set('_serialize', array('event'));
    }

    public function api_get_event_addresses($event_id)
    {
        $event = $this->Event->find('first', array(
            'fields' => array(),
            'conditions' => array(
                'Event.event_id' => $event_id,
            ),
            'contain' => array('Address'),
        ));

        if ($event) {
            $this->set('addresses', $event['Address']);
        }

        $this->set('_serialize', array('addresses'));
    }

    public function api_get_event_skills($event_id)
    {
        $event = $this->Event->find('first', array(
            'fields' => array(),
            'conditions' => array(
                'Event.event_id' => $event_id,
            ),
            'contain' => array('Skill'),
        ));

        if ($event) {
            $this->set('skills', $event['Skill']);
        }

        $this->set('_serialize', array('skills'));
    }

    public function api_get_event_rsvps($event_id)
    {
        $event = $this->Event->findByEventId($event_id, array('contain' => array()));

        if (!$this->_CurrentUserCanWrite($event['Event']['organization_id'])) {
            throw new ForbiddenException('Current user is unable to view this resource');
        }

        $rsvps = $this->Event->Rsvp->find('all', array(
            'conditions' => array(
                'Rsvp.event_id' => $event_id,
            ),
            'contain' => array('User' => array('fields' => array(
                'full_name',
                'username',
            ))),
        ));
        $this->set(compact('rsvps'));

        $this->set('_serialize', array('rsvps'));

    }

    public function api_get_event_times($event_id)
    {
        $event = $this->Event->findByEventId($event_id, array('contain' => array()));

        if (!$this->_CurrentUserCanWrite($event['Event']['organization_id'])) {
            throw new ForbiddenException('Current user is unable to view this resource');
        }

        $times = $this->Event->EventTime->find('all', array(
            'conditions' => array(
                'EventTime.event_id' => $event_id,
            ),
            'contain' => array('Time' => array('User' => array('fields' => array('full_name', 'username')))),
        ));
        $this->set(array('times' => Hash::extract($times, "{n}.Time")));

        $this->set('_serialize', array('times'));

    }

    public function api_get_event_comments($event_id)
    {
        $comments = $this->Event->Comment->find('threaded', array(
            'conditions' => array(
                'Comment.event_id' => $event_id,
            ),
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'full_name',
                        'username',
                    ),
                ),
            ),
        ));

        $this->set(compact('comments'));
        $this->set('_serialize', array('comments'));
    }

    public function api_post_event_comment($event_id)
    {

        $comment = (array) $this->request->input('json_decode');
        $comment['user_id'] = $this->Auth->user('user_id');

        if ($this->Event->Comment->save($comment)) {
            $resp = $this->Event->Comment->find('first', array(
                'conditions' => array(
                    'Comment.comment_id' => $this->Event->Comment->id,
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('full_name', 'username'),
                    ),
                ),
            ));
            $resp['children'] = array();

            $this->set('comment', $resp);
        }

        $this->set('_serialize', array('comment'));
    }

    public function api_get_my_rsvp($event_id)
    {
        $rsvp = $this->Event->Rsvp->find('first', array(
            'conditions' => array(
                'Rsvp.event_id' => $event_id,
                'Rsvp.user_id' => $this->Auth->user('user_id'),
            ),
            'contain' => array(),
        ));

        if ($rsvp) {
            $this->set('rsvp', $rsvp['Rsvp']);
        }

        $this->set('_serialize', array('rsvp'));
    }

    // PATCH /api/events/:event_id/rsvps/me

    public function api_update_my_rsvp($event_id)
    {
        $rsvp = $this->Event->Rsvp->find('first', array(
            'conditions' => array(
                'Rsvp.event_id' => $event_id,
                'Rsvp.user_id' => $this->Auth->user('user_id'),
            ),
            'contain' => array(),
        ));

        $incoming = (array) $this->request->input('json_decode')->rsvp;

        if ($rsvp) { // Updating
            unset($rsvp['Rsvp']['created'], $rsvp['Rsvp']['modified']);
        } else { // Inserting
            $rsvp = array(
                'Rsvp' => array(
                    'event_id' => $event_id,
                    'user_id' => $this->Auth->user('user_id'),
                ),
            );
        }

        $rsvp = array_merge($rsvp['Rsvp'], $incoming);

        if ($this->Event->Rsvp->save($rsvp)) {
            $updated = $this->Event->Rsvp->findByRsvpId($this->Event->Rsvp->id, array(
                'contain' => array(),
            ));
            $this->set('rsvp', $updated['Rsvp']);
            $this->set('_serialize', array('rsvp'));
        } else {
            throw new InternalErrorException(__('Unable to save RSVP'));
        }
    }

    public function api_rsvp($event_id, $mode = "going", $force = false)
    {
        $this->response->body(json_encode(array('response' => $this->_set_rsvp($event_id, $mode, $force))));
        $this->response->type('json');

        return $this->response;
    }

    private function _set_rsvp($event_id, $mode = "going", $force = false)
    {
        if (!in_array($mode, array('going', 'not_going'))) {
            $mode = 'going';
        }

        $success = false;

        if ($this->Event->exists($event_id)) {
            $event = $this->Event->findByEventId($event_id);
            $conditions = array(
                'Rsvp.user_id' => $this->Auth->user('user_id'),
                'Rsvp.event_id' => $event_id,
            );
            $contain = array('Event');
            $rsvp = $this->Event->Rsvp->find('first', compact('conditions', 'contain'));

            if (empty($rsvp)) {
                $create = array(
                    'user_id' => $this->Auth->user('user_id'),
                    'event_id' => $event_id,
                    'status' => $mode,
                );
                if ($this->Event->Rsvp->save($create)) {
                    $success = true;
                    $message = __("You are %s to %s",
                        ($mode == 'going') ? 'going' : 'not going',
                        $event['Event']['title']
                    );
                }
            } else {
                $rsvp['Rsvp']['status'] = $mode;

                if ($this->Event->Rsvp->save($rsvp['Rsvp'])) {
                    $success = true;
                    $message = __("You are %s to %s.",
                        ($mode == 'going') ? 'now going' : 'no longer going',
                        $rsvp['Event']['title']
                    );
                }
            }

        }

        return array(
            'success' => $success,
            'message' => $message,
        );

    }

    /**
     * Returns
     * /api/events/recommended
     */
    public function api_recommended()
    {
        $conditions = array(
            'User.user_id' => $this->Auth->user('user_id'),
        );
        $contain = array(
            'Skill',
        );
        $user_skills = $this->Event->Skill->User->find('first', compact('conditions', 'contain'));

        $skill_ids = Hash::extract($user_skills, 'Skill.{n}.skill_id');

        $conditions = array(
            'Skill.skill_id' => $skill_ids,
        );
        $contain = array(
            'Event' => array(
                'conditions' => array(
                    'Event.stop_time >= Now()',
                ),
                'order' => array(
                    'Event.start_time' => 'asc',
                ),
                'Organization',
                'Rsvp' => array(
                    'conditions' => array(
                        'Rsvp.user_id' => $this->Auth->user('user_id'),
                    ),
                ),
            ),
        );

        $user_events = $this->Event->Skill->find('all', compact('conditions', 'contain'));

        $this->set('recommended', $user_events);
        $this->set('_serialize', array('recommended'));

    }

    public function volunteer_recommended()
    {
        $conditions = array(
            'User.user_id' => $this->Auth->user('user_id'),
        );
        $contain = array(
            'Skill',
        );
        $user_skills = $this->Event->Skill->User->find('first', compact('conditions', 'contain'));

        $skill_ids = Hash::extract($user_skills, 'Skill.{n}.skill_id');

        $conditions = array(
            'Skill.skill_id' => $skill_ids,
        );
        $contain = array(
            'Event' => array(
                'conditions' => array(
                    'Event.stop_time >= Now()',
                ),
                'order' => array(
                    'Event.start_time' => 'asc',
                ),
                'Organization',
                'Rsvp' => array(
                    'conditions' => array(
                        'Rsvp.user_id' => $this->Auth->user('user_id'),
                    ),
                ),
            ),
        );

        $user_events = $this->Event->Skill->find('all', compact('conditions', 'contain'));

        $this->set(compact('user_events'));

        $this->set('title_for_layout', __('Recommended Events'));
    }

    public function volunteer_rsvp($event_id)
    {

        $response = $this->_set_rsvp($event_id, 'going', false);

        $this->Session->setFlash($response['message'], 'toast');

        return $this->redirect(array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id));
    }

    public function volunteer_cancel_rsvp($event_id)
    {
        $response = $this->_set_rsvp($event_id, 'not_going', false);
        $this->Session->setFlash($response['message'], 'toast');
        return $this->redirect(array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id));

    }

    public function volunteer_comment($event_id)
    {
        if ($this->request->is('post')) {
            $save['Comment']['event_id'] = $event_id;
            $save['Comment']['parent_id'] = (isset($this->request->data['Comment']['parent_id'])) ? $this->request->data['Comment']['parent_id'] : null;
            $save['Comment']['user_id'] = $this->Auth->user('user_id');
            $save['Comment']['body'] = $this->request->data['Comment']['body'];

            if ($this->Event->Comment->save($save)) {
                return $this->redirect(array('go' => true, 'controller' => 'events', 'action' => 'view', $event_id, '#' => sprintf('comment-%s', $this->Event->Comment->id)));
            } else {
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
        $this->organization_id = $event['Event']['organization_id'];

        $c_conditions = array('Comment.event_id' => $event_id);
        $c_contain = array('User', 'ParentComment' => array('User'));
        $c_order = array('Comment.created ASC');

        $user_rsvp_status = $this->Event->Rsvp->find('first', array(
            'conditions' => array(
                'Rsvp.user_id' => $this->Auth->user('user_id'),
                'Rsvp.event_id' => $event_id,
            ),
            'contain' => array(),
        ));

        $comments = $this->Event->Comment->find('threaded', array(
            'conditions' => $c_conditions,
            'contain' => $c_contain,
            'order' => $c_order));

        $this->set(compact('event', 'comments', 'user_rsvp_status'));
        $this->set('title_for_layout', __('Viewing Event - ' . $event['Event']['title']));
    }

    public function index($id = null)
    {
        $this->Paginator->settings['conditions'] = array(
            'Event.stop_time > NOW()',
        );
        $this->Paginator->settings['order'] = array(
            'Event.start_time ASC',
        );
        if ($this->Auth->user('user_id') != null) {
            $contain = array(
                'Organization',
                'Skill',
                'Rsvp' => array(
                    'conditions' => array(
                        'Rsvp.user_id' => $this->Auth->user('user_id'),
                    ),
                ),
            );
            $this->Paginator->settings['contain'] = $contain;
        }
        $this->set('events', $this->Paginator->paginate());
        $this->set('title_for_layout', __('Upcoming Events'));
    }

    public function view($id = null)
    {
        // if (!$this->Event->exists($id)){
        //     throw new NotFoundException(__('Invalid event'));
        // }

        $options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
        $event = $this->Event->find('first', $options);
        $this->request->data = $event;
        $this->set(compact('event'));
        $this->set('title_for_layout', __('Viewing Event - ' . $event['Event']['title']));
    }
}
