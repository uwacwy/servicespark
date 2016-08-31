<?php

require APP . 'Vendor/autoload.php';
use EmailReplyParser\Parser\EmailParser;

App::uses('CakeEventListener', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('Email', 'Model');
App::uses('Event', 'Model');
App::uses('Comment', 'Model');
App::uses('ServiceSparkUtility', 'Lib');

class MandrillIncomingListener implements CakeEventListener
{
	public function __construct($token)
	{
		$this->token = $token;
	}
	
	public function beforeFilter()
	{
		$this->Auth->allow('incoming');
	}
	
	public function implementedEvents()
	{
		return array(
			'Webhook.Incoming.'.$this->token => 'incoming',
			'Email.Incoming.comment_reply' => 'comment_reply',
			'Email.Incoming.event_rsvp' => 'event_rsvp'
		);
	}
	
	public function incoming($evt)
	{
		$WebhookController = $evt->subject();
		
		$mandrill_events = json_decode($WebhookController->request->data['mandrill_events'], true);
		
		$Email = new Email();
		
		foreach($mandrill_events as $mandrill_event)
		{
			$to = explode("@", $mandrill_event['msg']['email'] );
			$sender = $mandrill_event['msg']['from_email'];
			
			$conditions = array(
				'Email.guid' => $to[0],
				'Email.expires >= NOW()'
			);
			
			$email = $Email->find('first', compact('conditions'));
			if( $email )
			{
				$event = new CakeEvent(
					'Email.Incoming.'.$email['Email']['event_type'], 
					$this, 
					array(
						'guid' => $email['Email']['guid'],
						'email_event' => $email,
						'message' => $mandrill_event
					)
				);
				CakeEventManager::instance()->dispatch( $event );
			}
		}
	}
	
	public function event_rsvp($evt)
	{
		$rfc_to_ss = array(
			'ACCEPTED' => 'going',
			'TENTATIVE' => 'maybe',
			'DECLINED' => 'not_going'
		);
		
		$mandrill_event = (array) $evt->data['message'];
		$uid = $evt->data['email_event']['Email']['guid'];
		$user_id = $evt->data['email_event']['Email']['user_id'];
		
		$conditions = json_decode($evt->data['email_event']['Email']['event_data'], true);
		$contain = array();
		
		$Event = new Event();
		$event = $Event->find('first', compact('conditions', 'contain') );
		
		// TODO: scrub the message body for a reply, add to attachment array
		
		ServiceSparkUtility::log($mandrill_event['msg']);

		foreach( $mandrill_event['msg']['attachments'] as $attachment )
		{
			$content = $attachment['content'];
			if( $attachment['base64'] )
				$content = base64_decode($attachment['content']);
				
			$content = trim($content);
			
			$this->debug($content);
				
			$lines = explode("\r\n", $content);
			$lines = ServiceSparkUtility::icsLineMerge($lines);
			$content = implode("\r\n", $lines);
			
			if( $ics_event = ServiceSparkUtility::icsGetEventByUID($content, $uid) )
			if( $ics_event != false )
			if( $status = ServiceSparkUtility::icsGetRsvpFromEvent($ics_event) )
			if( $status != false )
			{
				$Event->Rsvp->setForUser($rfc_to_ss[ $status ], $event['Event']['event_id'], $user_id);
			}
			
		}
		
	}
	
	public function comment_reply($evt)
	{
		$conditions = json_decode($evt->data['email_event']['Email']['event_data'], true);
		
		$Comment = new Comment();
		
		$existing = $Comment->find('first', compact('conditions') );
		
		if( ! $existing['Comment'] )
			return;
		
		$parent_id = null;
		if( ! $existing['Comment']['parent_id'] )
			$parent_id = $existing['Comment']['comment_id'];
		
		$event_id = $existing['Comment']['event_id'];
		$user_id = $evt->data['email_event']['Email']['user_id'];
		$body_parsed = (new EmailParser())->parse( $evt->data['message']['msg']['text'] );
		
		$body = $body_parsed->getVisibleText();
		
		$comment = array(
			'event_id' => $event_id,
			'parent_id' => $parent_id,
			'user_id' => $user_id, // from the email_event
			'body' => trim($body)
		);
		
		$Comment->save($comment);
	}
	
	private function debug($that)
	{
		CakeLog::write('debug', print_r($that, true) );
	}
	
	function guid()
	{
	    if (function_exists('com_create_guid') === true)
	    {
	        return trim(com_create_guid(), '{}');
	    }
	
	    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
}