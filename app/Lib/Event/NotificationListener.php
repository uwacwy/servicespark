<?php
App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('Event', 'Model');
App::uses('Comment', 'Model');
App::uses('Time', 'Model');
App::uses('Permission', 'Model');

class NotificationListener implements CakeEventListener
{

	public function implementedEvents()
	{
		return array(
			'App.Event.afterSave.created' => 'event_created',
			'App.Entity.deleted' => 'entity_deleted',
			'App.Rsvp.afterSave.created' => 'rsvp_touched',
			'App.Rsvp.afterSave.modified' => 'rsvp_touched',
			'App.OrganizationTime.afterSave.created' => 'time_touched',
			'App.EventTime.afterSave.created' => 'time_touched',
			'App.Time.afterSave.modified' => 'time_touched',
			'App.TimeComment.afterSave.created' => 'time_comment_created'
		);
	}
	
	private function delete_notification($Model, $model, $model_id)
	{
		$query = 'DELETE Notification,
Subject
FROM notification_subjects Subject 
LEFT JOIN notification_notifications Notification ON Subject.notification_id = Notification.id 
WHERE Subject.model =  "%s" 
AND Subject.model_id = %u
AND Notification.read = 0';

		$Model->query( sprintf($query, $model, $model_id) );
	}
	
	private function _debug($that)
	{
		CakeLog::write('debug', print_r($that, true) );
	}
	
	public function time_comment_created($cake_event)
	{
		$TimeComment = $cake_event->subject();
		$Time = $TimeComment->Time;
		$User = $TimeComment->User;
		
		$user_id = $TimeComment->data['TimeComment']['user_id'];
		$time_id = $TimeComment->data['TimeComment']['time_id'];
		
		$this->_debug($time_id);
		
		$time = $Time->find('first', array(
			'conditions' => array(
				'Time.time_id' =>  $time_id
			),
			'contain' => array(
				'OrganizationTime',
				'EventTime' => array('Event')
			)
		));
		
		if( !empty($time['OrganizationTime']) )
			$path = 'OrganizationTime.0.organization_id';
			
		if( !empty($time['EventTime']) )
			$path = 'EventTime.0.Event.organization_id';
			
		$organization_id = Hash::get($time, $path);
		
		$recipients = array_values( $this->_get_coordinators($organization_id) );
		$recipients[] = $time['Time']['user_id'];
			
		foreach($recipients as $permission_id => $recipient_id)
		{
			if( $recipient_id != $user_id ) // do not notify a coordinator that they just submitted time
			{
				$User->notify(
					$recipient_id,
					'time_comment_created',
					array(
						'Time' => $time_id,
						'Organization' => $organization_id,
						'User' => $user_id
					)
				);
			}
		}
	}
	
	public function time_touched($cake_event)
	{
		$Model = $cake_event->subject();
		
		if( $Model->name == "Time" )
		{
			$Time = $Model;
			$time_id = $Time->data['Time']['time_id'];
		}
		else
		{
			$Time = $Model->Time;
			$time_id = $Model->data[ $Model->name ]['time_id'];
		}
		
		$User = $Time->User;
		
		$time = $Time->find('first', array(
			'conditions' => array(
				'Time.time_id' =>  $time_id
			),
			'contain' => array(
				'OrganizationTime',
				'EventTime' => array('Event')
			)
		));
		
		$this->delete_notification($cake_event->subject(), 'Time', $time_id );
		
		if( $time['Time']['status'] == 'pending' )
		if( !empty($time['OrganizationTime']) )
		{
			
			$organization_id = $time['OrganizationTime'][0]['organization_id'];
			
			$coordinators = $this->_get_coordinators($organization_id);
			
			foreach($coordinators as $permission_id => $user_id)
			{
				if( $user_id != $time['Time']['user_id'] ) // do not notify a coordinator that they just submitted time
				{
					$User->notify(
						$user_id,
						'time_pending',
						array(
							'Time' => $time_id,
							'Organization' => $organization_id,
							'User' => $time['Time']['user_id']
						)
					);
				}
			}
		}
		
		if( $time['Time']['status'] == 'approved')
		if( !empty($time['OrganizationTime']) )
		{
			$User->notify(
				$time['Time']['user_id'],
				'time_approved',
				array(
					'Time' => $time_id,
					'Organization' => $time['OrganizationTime'][0]['organization_id'],
							'User' => $time['Time']['user_id']
				)
			);
		}
		
		if( $time['Time']['status'] == 'rejected')
		if( !empty($time['OrganizationTime']) )
		{
			$User->notify(
				$time['Time']['user_id'],
				'time_rejected',
				array(
					'Time' => $time_id,
					'Organization' => $time['OrganizationTime'][0]['organization_id']
				)
			);
		}
		
		// announce event time events
		if( $time['Time']['status'] == 'approved' )
		if( !empty($time['EventTime']) )
		{
			
			$notification_slug = ($time['Time']['stop_time'] == null) ? 'time_clocked_in' : 'time_clocked_out';
			// notify coordinators that so and so clocked out
			$coordinators = $this->_get_coordinators( $time['EventTime'][0]['Event']['organization_id'] );
			
			$this->_debug($notification_slug);
			
			foreach($coordinators as $permission_id => $user_id)
			{
				if( $user_id != $time['Time']['user_id'] ) // do not notify a coordinator that they just submitted time
				{
					$User->notify(
						$user_id,
						$notification_slug,
						array(
							'Time' => $time_id,
							'Event' => $time['EventTime'][0]['Event']['event_id'],
							'User' => $time['Time']['user_id']
						)
					);	
				}
			}
		}
	}
	
	public function entity_deleted($cake_event)
	{
		// clean up notifications
		$Event = $cake_event->subject();
		
		if( !in_array($cake_event->data['model'], array("Time", "Event", "Organization") ) )
			return;
		
		$this->delete_notification($Event, $cake_event->data['model'], $cake_event->data['id'] );
	}
	
	public function event_created($cake_event)
	{
		$Event = $cake_event->subject();
		$event_id = $Event->id;
		
		$conditions = array(
			'Event.event_id' => $event_id
		);
		$contain = array('Skill' => array('User'));
		
		$event = $Event->find('first', compact('conditions', 'contain') );
		
		$users = array_unique( Hash::extract($event, 'Skill.{n}.User.{n}.user_id') );
		
		foreach($users as $user_id)
		{
			$Event->Skill->User->notify(
				$user_id,
				'skill_match',
				array(
					'Event' => $event_id
				)
			);
		}
	}
	
	public function rsvp_touched($cake_event)
	{
		$Rsvp = $cake_event->subject();
		$data = $Rsvp->data;
		
		// if rsvp_count > 0
			// create "event_has_attendees"
		// else
			// delete "event_has_attendees"
		
		$contain = array(
			'Event'
		);
		$conditions = array(
			'Rsvp.rsvp_id' => $Rsvp->id
		);
		$rsvp = $Rsvp->find('first', compact('contain', 'conditions'));
		
		$coordinators = $this->_get_coordinators($rsvp['Event']['organization_id']);
		
		if( $rsvp['Event']['rsvp_count'] > 0)
		{
			foreach($coordinators as $coordinator_user_id)
			{
				$query = 'DELETE Notification,
Subject
FROM notification_subjects Subject 
LEFT JOIN notification_notifications Notification ON Subject.notification_id = Notification.id 
WHERE Subject.model =  "%s" 
AND Subject.model_id = %u
AND Notification.type = "%s"
AND Notification.user_id = %u';

				$Rsvp->query( sprintf($query, 'Event', $rsvp['Event']['event_id'], 'event_rsvps', $coordinator_user_id) );

				if( $rsvp['Rsvp']['user_id'] != $coordinator_user_id) // do not notify the user that they're going
				{
					$Rsvp->User->notify(
						$coordinator_user_id,
						'event_rsvps',
						array(
							'Event' => $rsvp['Event']['event_id']
						)
					);
				}
			}
		}
		
	}
	

	
	private function _get_coordinators($organization_id)
	{
		$Permission = new Permission();
		
		return $Permission->find('list', array('fields' => array('user_id'), 'conditions' => array(
			'Permission.write' => true,
			'Permission.organization_id' => $organization_id
		)));
	}
	
}