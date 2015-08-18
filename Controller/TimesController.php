<?php
/*
	TimesController.php
	--
	manages time clock entries

	@extends AppController
*/

class TimesController extends AppController
{
	//var $scaffold;

	public $paginate = array(
		'limit' => 25,
		'order' => array(
			'Time.start_time' => 'asc'
		)
	);

	public function go_index()
	{
		return $this->redirect( array('volunteer' => true, 'controller' => 'times', 'action' => 'index') );
	}

	public function volunteer_index()
	{
		if( $this->request->is('post') )
		{
			if( 
				isset($this->data["Organization"]) 
				&& isset($this->data["Organization"]['organization_id'])
			)
			{
				// redirect to the organization screen
				return $this->redirect(
					array(
						'volunteer' => true,
						'controller' => 'times',
						'action' => 'in',
						'organization',
						$this->data['Organization']['organization_id']
					)
				);
			}
			else if(
				isset($this->data['Time'])
				&& isset($this->data['locus'])
				&& isset($this->data['Time']['token'])
			)
			{
				$redirect = array(
					'volunteer' => true,
					'controller' => 'times',
					'action' => $this->data['locus']
				);
				
				if($this->data['locus'] == "in")
				{
					$redirect[] = 'event';
				}
				
				$redirect[] = $this->data['Time']['token'];
				
				$this->redirect( $redirect );
			}
		}
		$fields = array();
		$conditions = array(
			'Permission.write' => true
		);
		$contain = array('Organization' => array(
			'fields' => array('organization_id', 'name')));
		$orgs = $this->Time->OrganizationTime->Organization->Permission->find('all', compact('fields', 'contain', 'conditions'));
		//debug($orgs);
		
		$organizations = Hash::combine($orgs, '{n}.Organization.organization_id', '{n}.Organization.name');
		
		//$organizations = $this->Time->OrganizationTime->Organization->find('list');
		$this->set(compact('organizations') );
	}
	
	public function coordinator_dashboard()
	{
		$conditions = array(
			'OrganizationTime.organization_id' => $this->_GetUserOrganizationsByPermission('write')
		);
		
		$contain = array(
			'Organization',
			'Time' => array(
				'User',
				'TimeComment' => array('fields' => 
					array('COUNT(time_comment_id) as CommentCount')
				)
			)
		);
		$order = array(
			'OrganizationTime.created' => 'DESC'
		);
		$this->Paginator->settings = compact('conditions', 'contain', 'order');
		
		$times = $this->Paginator->paginate(
			'OrganizationTime'
		);
		
		$this->set( array('times' => $times) );
	}

	/*
		/volunteer/in/organization/1234
		/volunteer/in/event/39fx
	*/
	public function volunteer_in($mode = null, $id_or_token = null)
	{
		if($mode == null)
		{
			$this->Session->setFlash( __('%s was unable to determine the clock in mode.', Configure::read("Solution.name") ), 'danger');
			return $this->redirect( $this->_Redirector('volunteer', 'times', 'index') );
		}
		
		if( !in_array($mode, array('organization', 'event') ) )
		{
			$this->Session->setFlash( __('%s was unable to clock you in with the information provided.', Configure::read("Solution.name") ), 'danger');
			return $this->redirect( $this->_Redirector('volunteer', 'times', 'index') );
		}
		
		if( $mode == 'organization' )
		{
			$organization_id = $id_or_token;
			
			$organization_contain = array();
			$organization_conditions = array(
				'organization_id' => $organization_id
			);
			$options = array(
				'contain' => $organization_contain,
				'conditions' => $organization_conditions
			);
			
			$organization = $this->Time->OrganizationTime->Organization->find('first', $options);
			
			if( !empty($organization) )
			{
				if( $this->request->is('post') )
				{
					$memo = "";
					if( isset($this->data['OrganizationTime'][0]['memo']) )
						$memo = $this->data['OrganizationTime'][0]['memo'];
						
					$time = array(
						'Time' => array(
							'user_id' => $this->Auth->user('user_id'),
							'start_time' => $this->data['Time']['start_time'],
							'stop_time' => $this->data['Time']['stop_time'],
							'status' => 'pending'
						),
						'OrganizationTime' => array(
							array(
								'organization_id' => $organization_id,
								'memo' => $memo
							)
						)
					);
					
					if( $this->Time->saveAssociated($time) )
					{
						$this->Session->setFlash( __('Your time for %s has been logged.', $organization['Organization']['name'] ), 'success');
						return $this->redirect( array('volunteer' => true, 'action' => 'index') );
					}
					else
					{
						$this->Session->setFlash( __('There were problems saving your time for %s.', $organization['Organization']['name'] ), 'danger');
					}
				}
				$this->set( compact('organization') );
			}
		}
		
		
		if ( $mode == 'event' )
		{
			$start_token = $id_or_token;
			$event_conditions = array(
				'start_token' => $start_token
			);
			$event_contain = array(
				'Organization' => array(
					'Permission' => array(
						'conditions' => array('write' => true),
						'fields' => array(),
						'User'
					)
				),
				'EventTime' => array(
					'fields' => array(),
					'Time' => array(
						'conditions' => array('user_id' => $this->Auth->user('user_id') )
					)
				)
			);
			$options = array(
				'conditions' => $event_conditions,
				'contain' => $event_contain
			);
			
			$event = $this->Time->EventTime->Event->find('first', $options);
			if( !empty($event) )
			{
				if( $this->request->is('post') )
				{
					$time = array(
						'Time' => array(
							'user_id' => $this->Auth->user('user_id'),
							'status' => 'approved',
							'start_time' => date('Y-m-d H:i:s'),
							'stop_time' => null
						),
						'EventTime' => array(
							array(
								'event_id' => $event['Event']['event_id'])
						)
					);
					
					if( $this->Time->saveAssociated($time) )
					{
						$this->Session->setFlash( __('You have been clocked in to %s.', $event['Event']['title'] ), 'success');
						return $this->redirect( array('volunteer' => true, 'action' => 'index') );
					}
					else
					{
						$this->Session->setFlash( __('There were problems saving your time for %s.', $organization['Event']['title'] ), 'danger');
					}	
				}
				
				$this->set( compact('event') );
			}
			else
			{
				$this->Session->setFlash( __('You provided an invalid time token.  Please talk to your event coordinator for more assistance.', Configure::read("Solution.name") ), 'danger');
				return $this->redirect( $this->_Redirector('volunteer', 'times', 'index') );
			}
		}
	}

	public function volunteer_out($stop_token = null, $time_id = null)
	{
		$event_conditions = array(
			'stop_token' => $stop_token
		);
		$event_contain = array(
			'Organization' => array(
				'Permission' => array(
					'conditions' => array('write' => true),
					'fields' => array(),
					'User'
				)
			),
			'EventTime' => array(
				'fields' => array(),
				'Time' => array(
					'conditions' => array(
						'user_id' => $this->Auth->user('user_id'),
						'stop_time' => null
					)
				)
			)
		);
		$options = array(
			'conditions' => $event_conditions,
			'contain' => $event_contain
		);
		
		$event = $this->Time->EventTime->Event->find('first', $options);
		for($i = 0; $i < count($event['EventTime']); $i++)
		{
			if( empty($event['EventTime'][$i]['Time']) )
			{
				unset($event['EventTime'][$i]);
			}
		}
		$event['EventTime'] = array_values($event['EventTime']);
		
		if( $this->request->is('post') )
		{
			$time = $this->Time->findByTimeId($time_id);
			
			if( isset($time['Time']['time_id']) )
			if( $time['Time']['user_id'] == $this->Auth->user('user_id') )
			{
				$this->Time->id = $time_id;
				if( $this->Time->saveField('stop_time', date("Y-m-d H:i:s")))
				{
					$this->Session->setFlash(
						__("You have successfully clocked out."),
						'success'
					);
				}
			}
			
			return $this->redirect( array('volunteer'=> true, 'controller' => 'times', 'action' => 'index') );
		}
		
		$this->set( compact('event') );
	}
	
	public function comment($time_id)
	{
		//debug($this->data);
		$conditions = array(
			'Time.time_id' => $time_id
		);
		$contain = array(
			'User',
			'EventTime' => array('Event'),
			'OrganizationTime' => array('Organization')
		);
		$time = $this->Time->find('first', compact('conditions', 'contain') );
		
		if( empty($time) )
		{
			throw new NotFoundException( __("You requested a page that does not exist.") );
		}
		
		if( !empty($time['OrganizationTime']) )
		{
			$this->organization_id = Hash::get($time, 'OrganizationTime.0.Organization.organization_id');
		}
		elseif( !empty($time['EventTime']) )
		{
			$this->organization_id = Hash::get($time, 'EventTime.0.Event.organization_id');
		}
		
		if( empty($time) )
			return;
		
		$can_comment = false;
		
		if( $time['Time']['user_id'] == $this->Auth->user('user_id') )
		{
			$can_comment = true;
		}
		elseif( $this->_CurrentUserCanWrite( $this->organization_id ) )
		{
			$can_comment = true;
		}
		
		
		if( $this->request->is('post') && $can_comment )
		{
			$time_comment = array(
				'time_id' => $time_id,
				'user_id' => AuthComponent::user('user_id'),
				'body' => $this->data['TimeComment']['body']
			);
			
			$this->Time->TimeComment->save( $time_comment );
			
			return $this->redirect( array('controller' => 'times', 'action' => 'view', $time_id) );
		}
		
		throw new BadRequestException( __("This request was invalid, so there is nothing to show you right now.") );
	}
	
	public function view($time_id = null)
	{
		if( $this->Time->exists($time_id) )
		{
			$contain = array(
				'User',
				'EventTime' => array('Event' => array('Organization')),
				'OrganizationTime' => array('Organization'),
				'TimeComment' => array('User')
			);
			$conditions = array(
				'Time.time_id' => $time_id
			);
			$time = $this->Time->find('first', compact('contain', 'conditions') );
			
			if( !empty($time['EventTime']) )
			{
				$this->organization_id = $time['EventTime'][0]['Event']['organization_id'];
			}
			
			if( !empty($time['OrganizationTime']) )
			{
				$this->organization_id = $time['OrganizationTime'][0]['Organization']['organization_id'];
			}
			
			if(
				$time['User']['user_id'] == $this->Auth->user('user_id')
				|| $this->_CurrentUserCanWrite($this->organization_id) 
			)
			{
				$title_for_layout = __("Time: %0.2f hour for %s", $time['Time']['duration'], $time['User']['full_name']);
				$this->set( compact('time', 'title_for_layout') );	
			}
			else
			{
				throw new ForbiddenException( __("You do not have permission to view this page") );
			}
		}
		else
			throw new NotFoundException( __("There is nothing to see here. (Error 404)"));
	}
	
	private function _GetTimeState($time_id, $contain = null)
	{
		$rtn = null;
		
		if( $contain == null)
		{
			$contain = array(
				'User',
				'EventTime' => array('Event' => array('Organization')),
				'OrganizationTime' => array('Organization')
			);
		}
		$conditions = array(
			'Time.time_id' => $time_id
		);
			
		if( $this->Time->exists($time_id) )
			$rtn = $this->Time->find('first', compact('contain', 'conditions') );
		else
			return null;
			
		if( !empty($rtn['EventTime']) )
			$this->organization_id = $rtn['EventTime'][0]['Event']['organization_id'];
			
		if( !empty($rtn['OrganizationTime']) )
			$this->organization_id = $rtn['OrganizationTime'][0]['Organization']['organization_id'];
			
		
		$rtn['context'] = array(
			'owner' => $this->Auth->user('user_id') == $rtn['Time']['user_id'],
			'coordinator' => $this->_CurrentUserCanWrite($this->organization_id)
		);
			
		return $rtn;
	}
	
	public function volunteer_edit($time_id)
	{
		$state = $this->_GetTimeState($time_id);
		
		if( $state == null )
			throw new NotFoundException( __("The requested page does not exist.") );
			
		if( empty($state['OrganizationTime']) )
			throw new ForbiddenException( __("You cannot modify time entries from events you attended.") );
			
		if( $state['Time']['user_id'] != $this->Auth->user('user_id') )
			throw new ForbiddenException( __("You may not modify this time entry as its owner.") );
			
		if( $this->request->is('post') )
		{
			$save = $this->data['Time'];
			
			$save['time_id'] = $time_id;
			$save['status'] = 'pending';
			$save['user_id'] = $this->Auth->user('user_id');
			
			if( $this->Time->save($save) )
			{
				return $this->redirect( array('volunteer' => false, 'action' => 'view', $time_id) );
			}
		}
			
		$this->data = array(
			'Time' => $state['Time'],
			'OrganizationTime' => $state['OrganizationTime']
		);
		
		
		
		
	}
	
	public function edit($time_id = null)
	{
		$state = $this->_GetTimeState($time_id);
		
		if( $state == null )
			throw new NotFoundException( __("The requested page does not exist.") );
		
		
		if( $state['context']['owner'] )
		{
			return $this->redirect( array('volunteer' => true, 'action' => 'edit', $time_id) );
		}
		
		if( $state['context']['coordinator'] )
		{
			return $this->redirect( array('coordinator' => true, 'action' => 'edit', $time_id) );
		}
		
		debug( $state );
		
		throw new NotFoundException( __("The requested page does not exist") );
		
	}
	
	public function delete($time_id)
	{
		$contain = array(
			'User',
			'EventTime' => array('Event' => array('Organization')),
			'OrganizationTime' => array('Organization')
		);
		$conditions = array(
			'Time.time_id' => $time_id
		);
		$time = $this->Time->find('first', compact('contain', 'conditions') );
		
		if( !empty($time['EventTime']) )
		{
			$this->organization_id = $time['EventTime'][0]['Event']['organization_id'];
		}
		
		if( !empty($time['OrganizationTime']) )
		{
			$this->organization_id = $time['OrganizationTime'][0]['Organization']['organization_id'];
		}
		
		if( $time['User']['user_id'] == $this->Auth->user('user_id') )
		{
			$this->Time->id = $time_id;
			if( $this->Time->saveField('status', 'deleted') )
			{
				$this->Session->setFlash( _("This time entry was marked as deleted"), 'toast' );
			}
			else
			{
				$this->Session->setFlash( _("There was a problem deleting your time entry"), 'danger' );
			}
			
			return $this->redirect( array('controller' => 'users', 'action' => 'activity') );
		}
		else
		{
			throw new ForbiddenException(__("You do not have permission to delete this time entry") );
		}
	}
	
	public function undelete($time_id)
	{
		$contain = array(
			'User',
			'EventTime' => array('Event' => array('Organization')),
			'OrganizationTime' => array('Organization')
		);
		$conditions = array(
			'Time.time_id' => $time_id,
			'Time.status' => 'deleted'
		);
		$time = $this->Time->find('first', compact('contain', 'conditions') );
		
		$status = 'deleted';
		if( !empty($time['EventTime']) )
		{
			$status = 'approved';
		}
		
		if( !empty($time['OrganizationTime']) )
		{
			$status = 'pending';
		}
		
		if( $time['User']['user_id'] == $this->Auth->user('user_id') )
		{
			$this->Time->id = $time_id;
			if( $this->Time->saveField('status', $status) )
			{
				$this->Session->setFlash( _("This time entry has been undeleted."), 'toast' );
			}
			else
			{
				$this->Session->setFlash( _("This time entry couldn't be undeleted."), 'danger' );
			}
			
			return $this->redirect( array('controller' => 'times', 'action' => 'trash') );
		}
		else
		{
			throw new ForbiddenException(__("You do not have permission to undelete this time entry") );
		}
	}
	
	public function trash()
	{
		$this->Paginator->settings['conditions'] = array(
			'Time.user_id' => $this->Auth->user('user_id'),
			'Time.status' => 'deleted'
		);
		$this->Paginator->settings['contain'] = array(
			'User',
			'EventTime' => array(
				'Event' => 'Organization'
			),
			'OrganizationTime' => array('Organization')
		);
		
		$deleted_times = $this->Paginator->paginate('Time');
		
		$this->set( compact('deleted_times') );	
	}
	
	
	
	/*
		api_approve
		--
		A lightweight request to approve a time entry.  Use the declarative syntax to call these on click
		<a 
			class="api-trigger" 
			data-target="#time-3" 
			data-on-success="collapse" 
			data-api="/servicespark/api/times/status/3/pending/1" 
			href="/servicespark/coordinator/times/approve/3/1"
		>Approve</a>
	*/
	public function api_status($time_id = null, $status = null, $confirm = null)
	{
		if(
			$this->request->is('post', 'put') 
			&& $confirm == 1 
			&& $this->Time->exists($time_id)
			&& in_array($status, array('approved', 'rejected', 'pending') )
			&& ($time = $this->Time->findByTimeId($time_id) )
			&& !empty($time['OrganizationTime'])
			&& ($this->organization_id = $time['OrganizationTime'][0]['organization_id'])
			&& $this->_CurrentUserCanWrite($this->organization_id)
		)
		{
			$this->Time->id = $time_id;
			if( $this->Time->saveField('status', $status) )
			{
				if( isset($this->request->data['TimeComment']['body']) )
				{
					$comment = array(
						'time_id' => $time_id,
						'user_id' => $this->Auth->user('user_id'),
						'body' => $this->request->data['TimeComment']['body']
					);
					$this->Time->TimeComment->save($comment);
				}
				$response = array(
					'approved' => true,
					'message' => __("Time entry %s is %s", $this->Time->id, $status)
				);	
			}
		}
		else
		{
			$response = array(
				'approved' => false,
				'message' => __("There was a problem with your request")
			);
		}

		$this->autoRender = false;
		$this->response->body( json_encode( compact('response') ) );
		$this->response->type('json');
		
		return $this->response;
	}
	
	public function coordinator_status($action = null, $time_id = null, $confirm = null)
	{
		
		if( $this->_status($time_id, $action, true) )
		{
			$this->Session->setFlash( __("%s time: succeeded", ucfirst($action)), 'toast' );
			return $this->redirect( array('coordinator' => false, 'controller' => 'times', 'action' => 'view', $time_id));
		}
		
		throw new ForbiddenException( __("There is nothing for you to see here") );
	}
	
	public function coordinator_approve($time_id = null, $confirm = null)
	{
		if(
			$this->request->is('post') 
			&& $confirm == 1 
			&& $this->Time->exists($time_id)
			&& ($time = $this->Time->findByTimeId($time_id) )
			&& ($this->organization_id = $time['OrganizationTime'][0]['organization_id'])
			&& $this->_CurrentUserCanWrite($this->organization_id)
		)
		{
			$this->Time->id = $time['Time']['time_id'];
			if( $this->Time->saveField('approved', true) )
			{
				$this->Session->setFlash( __('You approved time for %s.',
						$time['User']['full_name']
					),
					'toast'
				);
				return $this->redirect( array('coordinator' => true, 'controller' => 'times', 'action' => 'approve') );
			}
		}
		// make the permission model available to the Paginator
		
		if( $time_id != null )
		{
			$time = $this->Time->findByTimeId($time_id);
			
			if( !empty($time['EventTime']) )
			{
				$this->Session->setFlash( __('This time was recorded at an event and does not require your approval.'), 'warning');
				return $this->redirect( array('coordinator' => true, 'controller' => 'times', 'action' => 'approve') );
			}
			
			if( $time['Time']['status'] == "approved" )
			{
				$this->Session->setFlash( __('This time has already been approved'), 'success' );
				return $this->redirect( array('coordinator' => true, 'controller' => 'times', 'action' => 'approve') );
			}
			
			$this->set( compact('time') );

		}
		else
		{
			$user_write_organizations = $this->_GetUserOrganizationsByPermission('write');
			
			$conditions = array(
				'OrganizationTime.organization_id' => $user_write_organizations,
				'Time.status' => 'pending'
			);
			
			$this->Paginator->settings['contain'] = array('Organization', 'Time' => array('User'));
			$this->Paginator->settings['conditions'] = $conditions;
			$this->Paginator->settings['order'] = array('Time.status' => 'asc');
			$times = $this->Paginator->paginate('OrganizationTime');
			
			//debug($organizations);
			
			$this->set( compact('times') );
		}

	}
	
	private function _status($time_id = null, $status = null)
	{
		if($this->Time->exists($time_id)
			&& ($time = $this->Time->findByTimeId($time_id) )
			&& ($this->organization_id = $time['OrganizationTime'][0]['organization_id'])
			&& $this->_CurrentUserCanWrite($this->organization_id)
			&& in_array($status, array('approve', 'reject'))
		)
		{
			$verb_status = array(
				'approve' => 'approved',
				'reject' => 'rejected'
			);
			$this->Time->id = $time_id;
			return $this->Time->saveField('status', $verb_status[$status]);
		}
		
		return null;
	}

	public function coordinator_edit($time_id)
	{
		if( !$this->Time->exists($time_id) )
		{
			$this->Session->setFlash( __('An invalid time entry was specified.'), 'warning');
			return $this->redirect( $this->_Redirector('coordinator', 'events', 'index') );
		}

		// fetch time id
		$conditions = array('Time.time_id' => $time_id);
		$contain = array(
			'EventTime' => array(
				'Event' => array('Organization')
			),
			'OrganizationTime' => array(
				'Organization'
			),
			'User',
			'TimeComment'
		);
		$time = $this->Time->find('first', array('conditions' => $conditions, 'contain' => $contain) );
		
		if( empty($time['EventTime']) )
		{
			throw new NotFoundException( __("You cannot edit a time entry unless it is for events you coordinate") );
		}

		// verify that the current user can read/write this organization's time entries
		if( !$this->_CurrentUserCanWrite($time['EventTime'][0]['Event']['organization_id']) )
		{

			$this->Session->setFlash( __('You do not have permission to edit this organization\'s time entries'), 'warning');
			return $this->redirect( $this->_Redirector('go', 'organizations', 'view', $time['Event']['organization_id']) );
		}

		$title_for_layout = sprintf( __('Editing Time Entry %2$u &ndash; %1$s'), $time['EventTime'][0]['Event']['title'], $time['Time']['time_id']);

		// This block will execute when data is posted in the request
		if( $this->request->is( array('post', 'put') ) )
		{

			if( $this->request->data['Time']['blank'] )
			{
				unset( $this->request->data['Time']['stop_time'] );
				$this->request->data['Time']['stop_time'] = "";
			}

			unset($this->request->data['Time']['blank']);

			$save['Time'] = $this->request->data['Time'];
			
			if( trim($this->request->data['TimeComment']['body']) != "" )
			{
				$save['TimeComment'][] = array(
					'user_id' => $this->Auth->user('user_id'),
					'body' => $this->request->data['TimeComment']['body']
				);
			}


			$this->Time->clear();

			if( $this->Time->saveAssociated($save) )
			{
				$this->Session->setFlash( sprintf(__('Time entry %1$u was successfully updated.'), $this->Time->id), 'success');
				
				return $this->redirect( $this->_Redirector('coordinator', 'events', 'view', $time['EventTime'][0]['Event']['event_id']) );
			}
			else
			{
				$this->Session->setFlash( __('There were problems saving your time entry edit.'), 'danger');
			}
		}

		$this->request->data = $time;
		$this->set( compact('time', 'title_for_layout') );
		
	}
	
	public function coordinator_delete($time_id)
	{
		if( !$this->Time->exists($time_id) )
		{
			$this->Session->setFlash( __('An invalid time entry was specified.'), 'warning');
			return $this->redirect( $this->_Redirector('coordinator', 'events', 'index') );
		}

		// fetch time id
		$time = $this->Time->findAllByTimeId($time_id);

		// verify that the current user can read/write this organization's time entries
		if( !$this->_CurrentUserCanWrite($time['Time']['organization_id']) )
		{
			$this->Session->setFlash( __("You are not allowed to edit this organization's time entries"), 'danger');
			return $this->redirect( $this->_Redirector('go', 'organizations', 'view', $time['Time']['organization_id']) );
		}

		$title_for_layout = sprintf( __('Deleting Time Entry %u'), $time_id);
		// post block
			// check a confirm variable
			// redirect to coordinator/event/edit/:event_id
		if( $this->request->is('post') )
		{
			if( $this->Time->delete($time_id) )
			{
				$this->Session->setFlash( sprintf(__("Time entry %u has been deleted."), $time_id), 'success');
				return $this->rediirect( array('coordinator' => true, 'controller' => 'times', 'action' => 'view', $time['Event']['event_id']) );
			}
		}

		$this->set( compact('title_for_layout') );
	}
	
	public function coordinator_adjust( $event_id )
	{
		$conditions = array(
			'Event.event_id' => $event_id
		);

		$contain = array('Organization');
		$event = $this->Time->EventTime->Event->find( 'first', array('conditions' => $conditions) );

		// verify that the current user can read/write this organization's time entries
		if( !$this->_CurrentUserCanWrite($event['Organization']['organization_id']) )
		{
			$this->Session->setFlash( __("You are not allowed to edit this organization's time entries"), 'danger');
			return $this->redirect( $this->_Redirector('go', 'organizations', 'view', $time['Time']['organization_id']) );
		}

		$title_for_layout = sprintf( __('Adjusting Time Entries for %s'), $event['Event']['title']);

		if( $this->request->is('post') )
		{
			$new = array(
				'Time.stop_time' => sprintf("'%s'", date('Y-m-d H:i:s', strtotime($event['Event']['stop_time']) ) ) // this is disgusting syntax
			);

			$where = array(
				'Event.event_id' => $event_id,
				'Time.stop_time IS NULL'
			);

			if( $this->Time->updateAll( $new, $where ) )
			{
				$this->Session->setFlash( __('Time entries for this event have been adjusted.'), 'success');
			}
			else
			{
				$this->Session->setFlash( __('Something went wrong with automatic time entry adjustment.  Please contact a site administrator'), 'danger');
			}
		}

		$this->Paginator->settings['conditions'] = array('EventTime.event_id' => $event_id);
		$this->Paginator->settings['contain'] = array(
			'Time' => array('User')
		);
		// 	'EventTime' => array(
		// 		'Time' => array('User')
		// 	)
		// );
		$times = $this->Paginator->paginate('EventTime');
		
		//debug($times);
		
		
		$this->set( compact('event', 'times', 'title_for_layout') );

		//throw new NotImplementedException('this method exists but has not been implemented');
	}

	public function supervisor_view( $time_id )
	{
		$conditions = array('Time.time_id' => $time_id);
		$contain = array('Event' => array('Organization'), 'User');

		$time = $this->Time->find('first', array('conditions' => $conditions, 'contain' => $contain) );

		if( !$this->_CurrentUserCanRead($time['Event']['Organization']['organization_id']) )
		{
			$this->Session->setFlash( __('You do not have permission to view information for this organization.'), 'danger');
			return $this->redirect( array('supervisor' => false, 'controller' => 'users', 'action' => 'activity') );
		}

		$title_for_layout = sprintf( __('Viewing Time Entry %u &laquo; %s &laquo; %s'), $time['Time']['time_id'], $time['Event']['title'], $time['Event']['Organization']['name']);

		$this->set( compact('time', 'title_for_layout') );
	}


}
