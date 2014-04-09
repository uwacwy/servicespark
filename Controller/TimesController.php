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
		// fetch time id
		$time = $this->Time->findByTimeId($time_id);

		//debug($time);

		// verify that the current user can read/write this organization's time entries
		if( $this->_CurrentUserCanWrite($time['Event']['organization_id']) )
		{
			// edit views use request data, rather than set data when they prepopulating
			$this->request->data = $time;
			$this->set( compact('time') );
		}
		else
		{
			throw new ForbiddenException('You do not have permission to edit this organization\'s time entries');
		}

		// This block will execute when data is posted in the request
		if( $this->request->is('post') )
		{
			if( $this->request->data['Time']['stop_time']['null'] )
			{
				$this->request->data['Time']['stop_time'] = null;
			}

			$save['Time'] = $this->request->data['Time'];

			if($this->Time->save($save) )
			{
				$this->redirect( array('coordinator' => true, 'controller' => 'event', 'action' => 'view', $time['Event']['event_id']) );
			}
		}

		// throw new NotImplementedException('this method exists but has not been implemented');
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

		}

		// set data for view

		throw new NotImplementedException('this method exists but has not been implemented');
	}
	
	public function coordinator_adjust($adjustment, $event_id)
	{
		// verify adjustment is a valid thing to do (in|out|all)

		// switch on adjustment
			// in: find all entries with NULL clock in time
			// out: find all entries with NULL clock out time
			// both:

		// saveAll

		// set data for view

		throw new NotImplementedException('this method exists but has not been implemented');
	}


}
