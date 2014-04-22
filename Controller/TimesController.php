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
		return $this->redirect('volunteer' => true, 'controller' => 'times', 'action' => 'index');
	}

	public function volunteer_index()
	{
		if( $this->request->is('post') )
		{
			$this->redirect(
				array(
					'volunteer' => true, 
					'controller' => 'times', 
					'action' => $this->request->data['locus'], 
					$this->request->data['Time']['token']
				)
			);
		}
	}

	public function volunteer_in($token = null)
	{
		$conditions = array(
			'Event.start_token' => $token
		);

		$contain = array(
			'Address',
			'Organization' => array(
				'Permission.write = 1' => array(
					'User'
				)
			),
			'Time' => array('conditions' => array('Time.stop_time' => NULL) ) // should show people actually at event
		);

		// find the event_id using Cake's magic methods
		$event = $this->Time->Event->find('first', array('conditions' => $conditions, 'contain' => $contain));

		if( empty($event) )
		{
			$this->Session->setFlash( __('Invalid token was provided.  Contact your event coordinator for more assistance.'), 'danger');
			return $this->redirect( $this->_Redirector('volunteer', 'times', 'index') );
		}

		$existing = $this->Time->find('count', array('conditions' =>
				array(
					'Time.user_id' => $this->Auth->user('user_id'),
					'Time.event_id' => $event['Event']['event_id']
				)
			)
		);

		if($existing > 0)
		{
			$this->Session->setFlash( __('You have already clocked into this event.  Your event coordinator can adjust time punches for you.'), 'warning');
			return $this->redirect( $this->_Redirector('go', 'events', 'view', $event['Event']['event_id']) );
		}

		

		if ($this->request->is('post'))
		{
			if( $this->request->data['Time']['confirm'] )
			{
				$entry['Time'] = array(
					'user_id' => $this->Auth->user('user_id'),
					'event_id' => $event['Event']['event_id'],
					'start_time' => date('Y-m-d H:i:s')
				);

				if( $this->Time->save($entry) )
				{
					$this->Session->setFlash( __('You have been clocked in'), 'success');
					return $this->redirect( array('controller' => 'events', 'action' => 'view', $event['Event']['event_id']) );
				}
			}
		}

		$this->set( compact('event') );

	}

	public function volunteer_out($token = null)
	{
		$conditions = array(
			'Event.stop_token' => $token
		);

		$contain = array(
			'Address',
			'Organization' => array(
				'Permission.write = 1' => array(
					'User'
				)
			),
			'Skill',
			'Time'
		);

		$event = $this->Time->Event->find('first', array('conditions' => $conditions, 'contain' => $contain));

		if( empty($event) )
		{
			$this->Session->setFlash( __('Invalid token was provided.  Contact your event coordinator for more assistance.'), 'danger');
			return $this->redirect( $this->_Redirector('volunteer', 'times', 'index') );
		}

		$existing = $this->Time->find('first',
			array('conditions' =>
				array(
					'Time.user_id' => $this->Auth->user('user_id'),
					'Time.event_id' => $event['Event']['event_id'],
					'not' => array('Time.start_time' => null)
				)
			)
		);

		//debug($existing);

		if( $existing['Time']['stop_time'] != null )
		{
			//throw new NotFoundException('You are unable to clock out using this token.  Contact your event coordinator for more assistance.');
			
			$this->Session->setFlash( __('You are unable to clock out using this token.  Contact your event coordinator for more assistance.'), 'warning' );
			return $this->redirect( array('controller' => 'events', 'action' => 'index') );
		}

		if( $this->request->is('post') )
		{
			$this->Time->id = $existing['Time']['time_id'];
			$this->Time->saveField('stop_time', date('Y-m-d H:i:s') );

			$this->Session->setFlash( __('You have been clocked out of this event'), 'success');
			$this->redirect( $this->_Redirector('volunteer', 'events', 'view', $event['Event']['event_id']) );
		}

		$this->set( compact('event') );
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
		$contain = array( 'Event' => array('Organization'), 'User');
		$time = $this->Time->find('first', array('conditions' => $conditions, 'contain' => $contain) );

		// verify that the current user can read/write this organization's time entries
		if( !$this->_CurrentUserCanWrite($time['Event']['organization_id']) )
		{

			$this->Session->setFlash( __('You do not have permission to edit this organization\'s time entries'), 'warning');
			return $this->redirect( $this->_Redirector('go', 'organizations', 'view', $time['Event']['organization_id']) );
		}

		$title_for_layout = sprintf( __('Editing Time Entry %2$u &ndash; %1$s'), $time['Event']['title'], $time['Time']['time_id']);

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

			//debug($save);

			$this->Time->clear();

			if( $this->Time->save($save) )
			{
				$this->Session->setFlash( sprintf(__('Time entry %1$u was successfully updated.'), $this->Time->id), 'success');
				return $this->redirect( $this->_Redirector('coordinator', 'events', 'view', $time['Event']['event_id']) );
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
		$event = $this->Time->Event->find( 'first', array('conditions' => $conditions, 'contain' => $contain) );

		// verify that the current user can read/write this organization's time entries
		if( !$this->_CurrentUserCanWrite($event['Organization']['organization_id']) )
		{
			$this->Session->setFlash( __("You are not allowed to edit this organization's time entries"), 'danger');
			return $this->redirect( $this->_Redirector('go', 'organizations', 'view', $time['Time']['organization_id']) );
		}

		$title_for_layout = sprintf( __('Adjusting Time Entries for %s'), $event['Event']['title']);


		if($this->request->is('post') )
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

		$this->Paginator->settings['conditions'] = array('Time.event_id' => $event_id);
		$this->Paginator->settings['contain'] = array('User');
		$times = $this->Paginator->paginate();

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
