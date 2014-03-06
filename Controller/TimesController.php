<?php
/*
	TimesController.php
	--
	manages time clock entries
*/

class TimesController extends AppController
{
	//var $scaffold;

	public function in($token = null)
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

	public function out($token = null)
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
}
