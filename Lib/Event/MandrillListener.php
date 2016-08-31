<?php
/**
 * Defines MandrillListener class and imports several files
 */
App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('Event', 'Model');
App::uses('Comment', 'Model');
App::uses('ServiceSparkUtility', 'Lib');

/**
 * Defines an implementation of the Mandrill API for sending emails based on ServiceSpark events
 * 
 * @author Brad Kovach
 * @package uwacwy/servicespark
 * 
 */
class MandrillListener implements CakeEventListener
{
	/**
	 * The name of the current CakeEmail configuration to use for sending.  Can be used to swap between test and prod configs
	 */
	private $configuration_name = '';
	
	/**
	 * The default fields to retrieve when selecting users from the database.  Pulling desired fields reduces DB wear and tear,
	 * and also makes Mandrill API calls lighter and faster.
	 */
	private $user_fields = array('user_id', 'username', 'email', 'first_name', 'full_name');
	
	/**
	 * Creates a new MandrillListener object
	 * 
	 * @param string $configuration_name a string to specify which email configuration from Config/email.php should be used
	 */
	function __construct($_configuration_name)
	{
		$this->configuration_name = $_configuration_name;
	}
	
	/**
	 * Returns an array that specifies which events this handler is responding to
	 * 
	 * @return void
	 * @overrides CakeEventListener::implementedEvents
	 */
	public function implementedEvents()
	{
		return array(
			'App.Comment.afterSave.finalized' => 'comment_created',
			'App.Event.Add.Success' => 'event_created', // dependent entities are created
			'App.Event.Delete.Success' => 'event_deleted',
			'App.OrganizationTime.afterSave.created' => 'organization_time_created',
			'App.Recovery.afterSave.created' => 'recovery_created',
			'App.Skill.afterSave.created' => 'skill_created',
			'App.Time.afterSave.modified' => 'time_touched',
			'App.Verification.afterSave.created' => 'verification_created',
			//'App.User.afterSave.created' => 'user_created',
		);
	}
	
	/**
	 * Emails a new user when a new account requires verification
	 * 
	 * @param CakeEvent $cake_event event containing information about new verification event
	 */
	public function verification_created($cake_event)
	{
		ServiceSparkUtility::log("Verification Created: sending verification email");
		$Verification = $cake_event->subject();
		$verification = $Verification->data;
		
		$verification = $Verification->findByVerificationId( $Verification->data['Verification']['verification_id'] );
		
		$to = $verification['User']['email'];
		
		$global_merge_vars = Hash::flatten( array(
			'solution_name' => Configure::read('Solution.name')
		), "_");
		
		$recipient_merge_vars[ $to ] = Hash::flatten( array(
			'verification' => $verification['Verification'],
			'user' => $verification['User'],
			'verification_link' => Router::url( array(
				'controller' => 'verifications',
				'action' => 'verify',
				$verification['Verification']['token']), true)
		), "_");
		
		$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);
		
		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_verification')
			->emailFormat('text')
			->to( $to )
			->subject( "[*|solution_name|*] Please verify your new account" )
			->addHeaders( $headers );
			
		if( $email->send() )
			ServiceSparkUtility::log("Verification Created: email send succeeded");
		else
			ServiceSparkUtility::log("Verification Created: email send failed");

	}
	
	/**
	 * Emails coordinators when time is submitted to an organization.
	 * 
	 * @author Brad Kovach
	 * @param CakeEvent $cake_event An event that contains information about the 
	*/
	public function organization_time_created($cake_event)
	{
		$this->OrganizationTime = $cake_event->subject();
		
		$organization_time = $this->OrganizationTime->find('first', array(
			'conditions' => array('OrganizationTime.organization_time_id' => $cake_event->data['organization_time_id']),
			'contain' => array(
				'Organization' => array('Permission.write = 1' => array('User.email_coordinating = 1')),
				'Time' => array('User')
			)
		));
		
		$organization_time['Time']['view_link'] =
			Router::url( array(
				'volunteer' => false,
				'controller' => 'times', 
				'action' => 'view', 
				$organization_time['Time']['time_id']
			), true);
		
		$global_merge_vars = array(
			'solution_name' => Configure::read('Solution.name'),
			'time' => $this->PrettyTime( $organization_time['Time'], array('User') ),
			'user' => $this->PrettyUser( $organization_time['Time']['User'], array('password')),
			'organization' => $organization_time['Organization']
		);
		
		unset(
			$global_merge_vars['organization']['Permission']
		);
		
		$global_merge_vars = Hash::flatten($global_merge_vars, "_");
		
		$to = Hash::extract($organization_time, 'Organization.Permission.{n}.User.email');
		$recipient_merge_vars = Hash::combine(
			$organization_time, 
			'Organization.Permission.{n}.User.email', 'Organization.Permission.{n}.User');
		
		$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);
		
		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_organization_time')
			->emailFormat('text')
			->to( $to )
			->subject( "[*|solution_name|*] *|organization_name|*: *|time_duration|* hours submitted for approval" )
			->addHeaders( $headers );
			
		if( $email->send() )
		{
			ServiceSparkUtility::log('- Sent new organization time notifications to coordinators');
		}
	}
	
	/**
	 * Event handler for the creation of a new user
	 * 
	 * @author Brad Kovach <bradkovach@github.com>
	 * @param CakeEvent $cake_event a CakePHP event containing information about the new user entity
	 * @return void
	 * 
	 */
	public function user_created($cake_event)
	{
		$this->User = $cake_event->subject();
		$user = $this->User->find('first', array(
			'conditions' => array('User.user_id' => $cake_event->data['user_id']),
			'contain' => false
		));
		
		debug($user);
		$Email = new CakeEmail( $this->configuration_name );
		$Email->template('new_user')
			->viewVars( compact('user') )
			->emailFormat('text')
			->to( $user['User']['email'], $user['User']['full_name'] )
			->subject( __('[%1$s] Welcome to %1$s, %2$s!', Configure::read('Solution.name'), $user['User']['first_name']) )
			->send();
	}
	
	/**
	 * Event handler for the creation of a new event_created
	 * 
	 * @author Brad Kovach <bradkovach@github.com>
	 * @param CakeEvent $cake_event contains 
	 * @return void
	 */
	public function event_created($cake_event)
	{
		if( !isset($this->Event) )
			$this->Event = new Event();
			
		if( !isset($this->User) )
			$this->User = new User();
			
		$event_id = $cake_event->data['event_id'];
			
		CakeLog::write('info', "Mandrill: Sending event-created notifications (event " . $event_id . ")");
		
		$event_conditions = array(
			'Event.event_id' => $event_id,
		);
		$event_contain = array(
			'Organization' => array(
				'Permission.write = 1' => array('User')
			)
		);
		$event = $this->Event->find('first', array(
			'contain' => $event_contain, 
			'conditions' => $event_conditions
		) );
		
		$recipients = $this->GetRecipients($event_id, array(
			'skills' => true
		));
		
		if( empty($recipients) )
		{
			CakeLog::write('info', "MandrillListener::event_created: No recipients to notify..." . $cake_event->data['event_id']);
			return;
		}
		
		$global_merge_vars = array(
			'solution_name' => Configure::read('Solution.name'),
			'event' => $this->PrettyEvent($event['Event']),
			'event_rsvp_going_url' => Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'rsvp', 'going', $event_id), true ),
			'event_url' => Router::url( array('volunteer' => true, 'controller' => 'events', 'action' => 'view', $event_id), true ),
			'organization' => $this->PrettyOrganization($event['Organization'], array('Permission')),
		);
		$global_merge_vars = Hash::flatten($global_merge_vars, "_");
		$recipient_merge_vars = Hash::combine($recipients, '{n}.User.email', '{n}.User');
		$to = Hash::extract($recipients, '{n}.User.email');
		
		// foreach recipient
		$emails = array();
		foreach( $recipients as $to => $recipient )
		{
			unset($recipient_merge_vars);
			// set merge vars
			$recipient_merge_vars[ $recipient['User']['email'] ] = $recipient['User'];
			
			// generate reply ICS file
			// build_ics returns array( 'save' => array(), 'content' => "ics string");
			$ics = ServiceSparkUtility::ics($event, $recipient, 'reply.servicespark.org');
			$emails[] = array(
				'guid' => $ics['guid'],
				'user_id' => $recipient['User']['user_id'],
				'event_type' => 'event_rsvp',
				'event_data' => json_encode(array(
					'Event.event_id' => $event_id
				)),
				'expires' => $event['Event']['stop_time']
			);
			
			// send email with ICS
			
			$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);
			
			$headers['attachments'] = array(
				array(
					'type' => 'text/calendar; charset=UTF-8; method=REQUEST',
					'name' => 'invite.ics',
					'content' => base64_encode( $ics['content'] )
				)
			);
			
			$email = new CakeEmail( $this->configuration_name );
			$email
				->template('new_event')
				->emailFormat('text')
				->to( $recipient['User']['email'] )
				->subject("[*|solution_name|*] New event for *|full_name|*: *|event_title|*")
				->addHeaders( $headers );
	
			if( $email->send() )
				CakeLog::write('info', "Send Succeeded");
			else
				CakeLog::write('info', "Send Failed");
			
		}
		
		if( !empty($emails) )
			$this->User->Email->saveAll($emails);

		CakeLog::write('info', "Finished MandrillListener::event_created for event " . $cake_event->data['event_id']);
	}
	
	/**
	 * Event handler for the creation of a new skill
	 * 
	 * @author Brad Kovach
	 * @param CakeEvent $cake_event event object describing the creation of a skill
	 * @return void
	 */
	public function skill_created($cake_event)
	{
		
	}
	
	/**
	 * Event handler for deleting an event
	 * 
	 * @author Brad Kovach
	 * @param CakeEvent $cake_event describes the deleted event
	 * @return void
	 */
	public function event_deleted($cake_event)
	{
		
	}
	
	/**
	 * Event handler for new event comments
	 * 
	 * @author Brad Kovach
	 * @param CakeEvent $cake_event describes the new comment entity
	 * @return void
	 */
	public function comment_created($cake_event)
	{
		
		$Comment = new Comment();
		$User = $Comment->User;
		$comment_id = $cake_event->data['comment_id'];
		
		CakeLog::write('info', "Mandrill: New Comment (comment ".$comment_id.")");
		
		$comment_conditions = array(
			'Comment.comment_id' => $comment_id
		);
		
		$comment = $Comment->find('first', array(
			'conditions' => array('Comment.comment_id' => $comment_id), 
			'contain' => array('Event', 'User' => array('fields' => $this->user_fields) )
		));
		
		$restrict = array(
			'going' => true,
			'mentions' => true,
			'discussing' => true,
			'coordinating' => true
		);
		
		if( CakeTime::isPast($comment['Event']['stop_time']) )
		{
			$restrict = array(
				'coordinating' => true,
				'mentions' => true
			);
		}
		
		$recipients = $this->GetRecipients(
			$comment['Event']['event_id'],
			$restrict,
			$comment_id
		);
		
		// unset the sending user, regardless of involvement
		unset( $recipients[ $comment['Comment']['user_id'] ] );
		
		if( empty($recipients) )
		{
			return;
		}
		
		$to = Hash::extract($recipients, '{n}.User.email');
		
		$recipients = Hash::map($recipients, '{n}', array($this, 'ExplainEmail') );
		
		$global_merge_vars = Hash::flatten(array(
			'commenter' => $this->PrettyUser($comment['User']),
			'solution_name' => Configure::read('Solution.name'),
			'event' => $this->PrettyEvent($comment['Event']),
			'comment' => $comment['Comment'],
			'comment_url' => Router::url( array(
				'volunteer' => true,
				'controller' => 'events',
				'action' => 'view',
				$comment['Event']['event_id'],
				'#' => 'comment-'.$comment['Comment']['comment_id']
			), true)
		), '_');
		
		$recipient_merge_vars = Hash::combine($recipients, '{n}.User.email', '{n}.User');
		$guids = array();
		foreach($recipient_merge_vars as $email => $user)
		{
			$guid = ServiceSparkUtility::guid();
			
			$recipient_merge_vars[ $email ]['reply_email'] = __("%s@reply.servicespark.org", $guid);
			
			$guids[] = array(
				'user_id' => $user['user_id'],
				'guid' => $guid,
				'event_type' => 'comment_reply',
				'event_data' => json_encode(array(
					'Comment.comment_id' => $comment_id
				)),
				'expires' => $User->getDataSource()->expression('DATE_ADD(now(), INTERVAL 1 DAY)')
			);
		}
		
		$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);

		$email = new CakeEmail( $this->configuration_name );
		$email
			->template('new_comment')
			->emailFormat('text')
			->to( $to )
			->subject( "[*|solution_name|*] *|subject|* \"*|event_title|*\"" )
			->addHeaders( $headers );
		
		if( !empty($guids) )
			$User->Email->saveAll($guids);

		if( $email->send() )
			CakeLog::write('info', "Mandrill: End New Comment (comment ".$comment_id.")");
		else
			CakeLog::write('info', "Mandrill: New Comment SEND FAILED (comment ".$comment_id.")");
	}
	
	/**
	 * Sends appropriate emails when a time event has been touched
	 * 
	 * @param CakeEvent $cake_event CakePHP event describing the nature of the time modification
	 * @author Brad Kovach
	 * @return void
	 */
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
				'User',
				'OrganizationTime' => array(
					'Organization' => array(
						'Permission.write = 1' => array(
							'User.email_coordinating = 1'
						)
					)
				)
			)
		));

		// organization time created (pending)
		if( !empty($time['OrganizationTime']) )
		{
			if( $time['Time']['status'] == 'pending' )
			{
				$template = 'coordinator_time_modified';
				$subject = 'Please approve *|time_duration|* hours for *|organization_name|*';
				$to = Hash::extract($time, 'OrganizationTime.{n}.Organization.Permission.{n}.User.email');
				
				$coordinators = Hash::extract($time, 'OrganizationTime.{n}.Organization.Permission.{n}.User');
				
				foreach($coordinators as $key => $coordinator)
				{
					if( empty($coordinator) )
						unset($coordinators[$key]);
				}
				
				$recipient_merge_vars = Hash::combine( $coordinators, '{n}.email', '{n}');
					
			}
			else
			{
				$template = 'volunteer_time_modified';
				$subject = 'Your time at *|organization_name|* has been *|time_status|*';
				// email the user
				$to = array( $time['User']['email'] => $time['User']['email'] );
				$recipient_merge_vars = array(
					$time['User']['email'] => $time['User']
				);
			}
			
			$global_merge_vars = array(
				'solution_name' => Configure::read('Solution.name'),
				'organization' => $this->PrettyOrganization($time['OrganizationTime'][0]['Organization'], array('Permission')),
				'time' => $this->PrettyTime($time['Time']),
				'user' => $this->PrettyUser($time['User'], array('password', 'super_admin'))
			);
			
			$global_merge_vars = Hash::flatten($global_merge_vars, '_');
			
			$headers = $this->CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars);
			
			$email = new CakeEmail( $this->configuration_name );
			$email
				->template($template)
				->emailFormat('text')
				->to( $to )
				->subject( "[*|solution_name|*] " . $subject )
				->addHeaders( $headers );
				
			if( $email->send() )
			{
				ServiceSparkUtility::log('- Sent modified org time emails');
			}
		}
	}
	
	/**
	 * Generates explanations for why a recipient is getting emailed
	 * 
	 * @param User $recipient An array containing 'User'
	 * @returns array Modified User object with added "reasons_block" key
	 */
	public function ExplainEmail($recipient)
	{
		$recipient['User']['subject'] = 'New comment on';
		$reasons = array();
		
		if( isset($recipient['Locus']['attended']) && $recipient['Locus']['attended'] )
			$reasons[] = __("- You attended this event.");
			
		if( isset($recipient['Locus']['going']) && $recipient['Locus']['going'] )
			$reasons[] = __("- You have RSVP'd to this event." );
			
		if( isset($recipient['Locus']['mentioned']) && $recipient['Locus']['mentioned'] )
		{
			$reasons[] = __("- You were mentioned in this comment.");
			$recipient['User']['subject'] = "You were mentioned in a comment on";
		}
		
		if( isset($recipient['Locus']['discussing']) && $recipient['Locus']['discussing'] )
			$reasons[] = __("- You previously left a comment on this event.");
			
		if( isset($recipient['Locus']['coordinating']) && $recipient['Locus']['coordinating'] )
			$reasons[] = __("- You are coordinating this event");
			
		$recipient['User']['reasons_block'] = implode("\n", $reasons);
		
		return $recipient;
 	}
	
	/**
	 * Prepares an Organization object for presentation
	 * 
	 * @param Organization $organization The organization that will be formatted for presentation
	 * @author Brad Kovach
	 * @param array $unsets An array of field names to be unset
	 * @return Organization with formatted fields
	 * 
	 */
	private function PrettyOrganization($organization, $unsets = array() )
	{
		foreach($unsets as $unset)
			unset($organization[$unset]);
		
		return $organization;
	}
	
	/**
	 * Prepares an Event object for presentation. Properly formats Time elements
	 * 
	 * @author Brad Kovach
	 * @param Event $event The event that will be formatted for presentation
	 * @param array $unsets An array of field names that will be unset before return
	 * @return Event with formatted fields
	 *
	 */
	private function PrettyEvent($event, $unsets = array() )
	{
		$event['start_time'] = date('F j, Y g:i a', CakeTime::fromString($event['start_time']) );
		$event['stop_time'] = date('F j, Y g:i a', CakeTime::fromString($event['stop_time']) );
		
		foreach($unsets as $unset)
			unset($event[$unset]);
			
		return $event;
	}
	
	/**
	 * Prepares a Time object for presentation. Properly formats start time and stop times,
	 * rounds numbers and injects permalinks for use in templates
	 * 
	 * @author Brad Kovach
	 * @param Time $time A time entry that will be formatted for presentation
	 * @param array $unsets An array of field names that will be unset before return
	 * @return Time Properly formatted Time object with fields removed as appropriate.
	 */
	private function PrettyTime($time, $unsets = array() )
	{
		$time['start_time'] = date('F j, Y g:i a', CakeTime::fromString($time['start_time']) );
		$time['stop_time'] = date('F j, Y g:i a', CakeTime::fromString($time['stop_time']) );
		$time['duration'] = number_format($time['duration'], 2);
		$time['view_link'] = Router::url( array(
			'volunteer' => false,
			'controller' => 'times', 
			'action' => 'view', 
			$time['time_id']
		), true);
		
		foreach($unsets as $unset)
			unset($time[$unset]);
		
		return $time;
	}
	
	/**
	 * Prepares a User object for presentation.
	 * 
	 * @author Brad Kovach
	 * @param User $user The User to format.
	 * @param array $unsets array of fields to unset.  Removes password by $global_merge_vars_default
	 * @return User properly formatted User
	 */
	private function PrettyUser($user, $unsets = array('password') )
	{
		foreach($unsets as $unset)
			unset($user[$unset]);
		
		return $user;
	}
	
	/**
	 * Creates a cohesive Mandrill Header object suitable for merged sends
	 * 
	 * @author Brad Kovach
	 * @param array $global_merge_vars Array of Key-Value variables that appear in all messages
	 * @param array $recipient_merge_vars Array of arrays of key-value variables to 
	 * 	be customized on a per recipient basis.
	 * @param string $rcpt_key Can be used to alter the resulting array.
	 * 
	 * @return array Mandrill headers appropriate for sending.
	 */
	private function CreateMandrillHeaders($global_merge_vars, $recipient_merge_vars, $rcpt_key = "rcpt")
	{
		$result = array(
			'preserve_recipients' => false,
			'global_merge_vars' => array(),
			'merge_vars' => array()
		);

		foreach($global_merge_vars as $name => $content)
		{
			$result['global_merge_vars'][] = array(
				'name' => $name,
				'content' => $content
			);
		}

		foreach($recipient_merge_vars as $email => $merge_vars)
		{
			$mandrill_vars = array();
			foreach($merge_vars as $name => $content)
			{
				$mandrill_vars[] = array(
					'name' => $name,
					'content' => $content
				);
			}

			$result['merge_vars'][] = array(
				$rcpt_key => $email,
				'vars' => $mandrill_vars
			);

		}

		return $result;
	}

	/**
	 * Performs Mandrill/Mail Chimp Merge Var find and replace on $string with tokens and values in $vars
	 * 
	 * @author Brad Kovach
	 * @deprecated
	 * @param string $string A string to format
	 * @param array $vars {
	 * 		Key-Value find-replace data
	 * 
	 *		@type string $token A needle to find in $string
	 *		@type string $value A string that will be dropped in as $token's replacement
	 * }
	 */
	private function _ApplyMergeVars($string, $vars)
	{
		foreach($vars as $token => $value)
		{
			$string = str_replace("*|".$token."|*", $value, $string);
		}
		
		return $string;
	}
	
	/**
	 * Gets a list of users to email based on $event_id
	 * 
	 * @author Brad Kovach
	 * @param $event_id the ID of the event concerned
	 * @param $restrict an array specifying what users should be gathered, based on simple criteria
	 * @param $comment_id (optional) a comment ID to fetch mentions information for.  Only necessary when $restrict['mentioned'] is true
	 * @return array An array of recipients keyed by $user_id that have opted in to receive email based on $restrict
	 * 
	 * @uses MandrillListener::$user_fields which helps reduce the amount of data brought back from DB
	 * @uses MandrillListener::$Event if an Event model already exists, recycle it.
	*/
	private function GetRecipients($event_id, $restrict = array(), $comment_id = null )
	{
		if( !isset($this->Event) )
			$this->Event = new Event();
			
		$default = array(
			'attended' => false,
			'going' => false,
			'coordinating' => false,
			'discussing' => false,
			'skills' => false,
			'mentions' => true
		);
		
		$loci = Hash::merge($default, $restrict);
		
		$contain = array();
		
		if( $loci['attended'] )
			$contain['EventTime'] = array(
				'Time' => array(
					'User' => array(
						'conditions' => array(
							'User.email_attended' => true
						),
						'fields' => $this->user_fields
					)
				)
			);
		
		if( $loci['going'] )
			$contain['Rsvp'] = array(
				'conditions' => array(
					'Rsvp.status' => 'going'
				),
				'User' => array(
					'conditions' => array(
						'User.email_going' => true
					),
					'fields' => $this->user_fields
				)
			);
			
		if( $loci['coordinating'] )
			$contain['Organization'] = array(
				'Permission' => array(
					'conditions' => array(
						'Permission.write' => true
					),
					'User' => array(
						'conditions' => array(
							'User.email_coordinating' => true
						),
						'fields' => $this->user_fields
					)
				)
			);
			
		if( $loci['discussing'] )
			$contain['Comment']['User'] =  array(
				'conditions' => array(
					'User.email_discussing' => true
				),
				'fields' => $this->user_fields
			);
			

		if( $loci['skills'] )
			$contain['EventSkill'] = array(
				'Skill' => array(
					'SkillUser' => array(
						'User' => array(
							'conditions' => array(
								'User.email_skills' => true
							),
							'fields' => $this->user_fields
						)
					)
				)
			);
			
		if( $loci['mentions'] )
		{
			$mentions = $this->Event->Comment->Mention->find('all', array(
				'conditions' => array(
					'Mention.comment_id' => $comment_id
				),
				'contain' => array(
					'User' => array(
						'conditions' => array(
							'User.email_mentions'
						),
						'fields' => $this->user_fields
					)
				)
			));
		}
		
		$conditions = array(
			'Event.event_id' => $event_id
		);
		$result = $this->Event->find('first', compact('conditions', 'contain'));
		
		$skills = Hash::extract($result, 'EventSkill.{n}.Skill');
		$skills = Hash::combine($skills, '{n}.skill', '{n}.SkillUser');
		$skills = Hash::filter( $skills, function($in){
			if( !empty($in) )
				return true;
			else
				return false;
		});
		
		/*
			This is a rudimentary extraction of users.  This includes duplicates.
		*/
		$recipients = array(
			'skills' => $skills,
			'attended' => Hash::extract($result, 'EventTime.{n}.Time'),
			'going' => Hash::extract($result, 'Rsvp.{n}.User'),
			'discussing' => Hash::extract($result, 'Comment.{n}.User'),
			'coordinating' => Hash::extract($result, 'Organization.Permission.{n}.User'),
			'mentions' => Hash::extract($mentions, '{n}.User')
		);
		
		
		/*
			The array needs to be flipped, so the primary dimension is User
			The secondary dimension is the locus of control
		*/
		$flipped_result = array();
		
		$reason_key = 'Locus';
		
		foreach( $recipients['skills'] as $skill => $users )
		{
			foreach($users as $user)
			{
				if( !empty($user) )
				{
					$user_id = $user['User']['user_id'];
					
					$flipped_result[ $user_id ]['User'] = $user['User'];
					$flipped_result[ $user_id ]['Skill'][] = $skill;
					$flipped_result[ $user_id ][$reason_key]['skills'] = true;
				}
			}
		}
		
		foreach( $recipients['attended'] as $time )
		{
			if( !empty($time) )
			{
				$user_id = $time['User']['user_id'];
				$flipped_result[ $user_id ]['User'] = $time['User'];
				unset($time['User']);
				
				$flipped_result[ $user_id ]['Time'][] = $time;
				$flipped_result[ $user_id ][$reason_key]['attended'] = true;
			}
		}
		
		foreach( array('going', 'discussing', 'coordinating', 'mentions') as $locus )
		{
			if( is_array($recipients[$locus]) )
			foreach( $recipients[$locus] as $user )
			{
				if( !empty($user) )
				{
					$flipped_result[ $user['user_id'] ]['User'] = $user;
					$flipped_result[ $user['user_id'] ][$reason_key][$locus] = true;
				}

			}
		}
		
		/*
			Empty users must be removed.
			This happens when a user would have been selected, but they prefer not to be emailed for any reason
		*/
		foreach( $flipped_result as $key => $data )
		{
			if(  empty($data['User']['user_id']) || !isset($data['User']['user_id']) )
				unset($flipped_result[$key]);
		}
		
		return $flipped_result;
		
	}

}