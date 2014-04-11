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
			throw new NotFoundException('Invalid token provided.  Contact your event coordinator for more assistance.');
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
			$this->Session->setFlash('You have already clocked into this event.  Your event coordinator can adjust time punches for you.');
			return $this->redirect( array('controller' => 'events', 'action' => 'view', $event['Event']['event_id']) );
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
					$this->Session->setFlash('You have been clocked in');
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

		// find the event_id using Cake's magic methods
		$event = $this->Time->Event->find('first', array('conditions' => $conditions, 'contain' => $contain));

		if( empty($event) )
		{
			throw new NotImplementedException('Invalid token was provided.  Contact your event coordinator for more assistance.');
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
			throw new NotFoundException('You are unable to clock out using this token.  Contact your event coordinator for more assistance.');
			return $this->redirect( array('controller' => 'events', 'action' => 'index') );
		}

		if( $this->request->is('post') )
		{
			$this->Time->id = $existing['Time']['time_id'];
			$this->Time->saveField('stop_time', date('Y-m-d H:i:s') );
			$this->redirect( array('controller' => 'events', 'action'));
		}

		$this->set( compact('event') );
	}

	public function coordinator_edit($time_id)
	{
		if( !$this->Time->exists($time_id) )
		{
			throw new NotFoundException('Invalid time entry specified');
		}

		// fetch time id
		$time = $this->Time->findByTimeId($time_id);

		// verify that the current user can read/write this organization's time entries
		if( !$this->_CurrentUserCanWrite($time['Event']['organization_id']) )
		{

			throw new ForbiddenException('You do not have permission to edit this organization\'s time entries');
		}

		// This block will execute when data is posted in the request
		if( $this->request->is( array('post', 'put') ) )
		{

			if( $this->request->data['Time']['blank'] )
			{
				unset( $this->request->data['Time']['stop_time'], $this->request->data['Time']['blank'] );
				$this->request->data['Time']['stop_time'] = "";
			}

			$save['Time'] = $this->request->data['Time'];

			debug($save);

			if( $this->Time->save($save) )
			{
				$this->Session->setFlash( sprintf(__('Time entry %1$u was successfully updated.'), $this->Time->id), 'success');
				return $this->redirect( array('coordinator' => true, 'controller' => 'event', 'action' => 'view', $time['Event']['event_id']) );
			}
		}

		$this->request->data = $time;
		$this->set( compact('time') );
		
	}
	
	public function coordinator_delete($time_id)
	{
		if( !$this->Time->exists($time_id) )
		{
			throw new NotFoundException('Invalid time entry specified');
		}

		// fetch time id
		$time = $this->Time->findAllByTimeId($time_id);

		// verify that the current user can read/write this organization's time entries
		if( $this->_CurrentUserCanWrite($time['Time']['organization_id']) )
		{

		}
		else
		{
			throw new ForbiddenException('You are not allowed to edit this organization\'s time entries');
		}

		// post block
			// check a confirm variable
			// redirect to coordinator/event/edit/:event_id
		if( $this->request->is('post') )
		{
			if( $this->Time->delete($time_id) )
			{
				return $this->rediirect( array('coordinator' => true, 'controller' => 'event', 'action' => 'view', $time['Event']['event_id']) );
			}
		}

		// set data for view

		throw new NotImplementedException('this method exists but has not been implemented');
	}
	
	public function coordinator_adjust( $event_id )
	{
		$conditions = array(
			'Event.event_id' => $event_id
		);

		$contain = array('Organization');
		$event = $this->Time->Event->find( 'first', array('conditions' => $conditions, 'contain' => $contain) );

		// verify that the current user can read/write this organization's time entries
		if( $this->_CurrentUserCanWrite($event['Organization']['organization_id']) )
		{

		}
		else
		{
			throw new ForbiddenException('You are not allowed to edit this organization\'s time entries');
		}


		if($this->request->is('post') )
		{
			$new = array(
				'Time.stop_time' => sprintf("'%s'", date('Y-m-d H:i:s', strtotime($event['Event']['stop_time']) ) ) // this is disgusting syntax
			);

			$where = array(
				'Event.event_id' => $event_id
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

		$this->set( compact('event', 'times') );

		//throw new NotImplementedException('this method exists but has not been implemented');
	}


}
