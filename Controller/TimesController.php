<?php
/*
	TimesController.php
	--
	manages time clock entries
*/

class TimesController extends AppController
{
	//var $scaffold;

	public function volunteer_in($token = null)
	{
		// find the event_id
		$event = $this->Time->Event->findByStartToken($token);

		if( empty($event) )
		{
			throw new NotImplementedException('Event does not exist, and this case has not been handled.');
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
			if( $this->request->data['Time']['confirm'] == '1' )
			{
				$entry['Time'] = array(
					'user_id' => $this->Auth->user('user_id'),
					'event_id' => $event['Event']['event_id'],
					'start_time' => date('Y-m-d H:i:s')
				);

				if($this->Time->save($entry) )
				{
					$this->Session->setFlash('You have been clocked in');
					return $this->redirect( array('controller' => 'events', 'action' => 'view', $event['Event']['event_id']) );
				}
			}
		}

		$this->set( compact('event') );

		

		// create a time entry for now with $this->Auth->user('user_id')
	}

	public function volunteer_out($token = null)
	{
		$event = $this->Time->Event->findByStopToken($token);

		if( empty($event) )
		{
			throw new NotImplementedException('Event does not exist, and this case has not been handled.');
		}

		$existing = $this->Time->find('count', array('conditions' =>
				array(
					'Time.user_id' => $this->Auth->user('user_id'),
					'Time.event_id' => $event['Event']['event_id'],
					'not' => array('Time.start_time' => null)
				)
			)
		);

		if( $existing != 1 )
		{
			throw new NotFoundException('You are unable to clock out using this token');
			return $this->redirect( array('controller' => 'events', 'action' => 'index') );
		}

		debug($existing);

		$this->set( compact('event') );
	}

	public function coordinator_edit($time_id)
	{
		// fetch time id

		// verify that the current user can read/write this organization's time entries

		// post block
			// verify start time < stop time
			// save
			// redirect to coordinator/event/edit/:event_id

		// set data for view

		throw new NotImplementedException('this method exists but has not been implemented');
	}
	
	public function coordinator_delete($time_id)
	{
		// fetch time id
		$time = $this->Time->findAllById($time_id);

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
