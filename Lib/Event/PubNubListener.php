<?php
/**
 * Defines MandrillListener class and imports several files
 */
App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('ServiceSparkUtility', 'Lib');
App::uses('User', 'Model');


require APP . "Vendor/autoload.php";
use Pubnub\Pubnub;

/**
 * Defines an implementation of the Mandrill API for sending emails based on ServiceSpark events
 * 
 * @author Brad Kovach
 * @package uwacwy/servicespark
 * 
 */
class PubNubListener implements CakeEventListener
{
	public function implementedEvents()
	{
		return array(
			'App.Notification.afterSave.created' => 'new_notification',
			'App.Event.afterSave.created' => 'new_event'
		);	
	}
	
	public function new_event($cake_event)
	{
		ServiceSparkUtility::log('new event created');
	}
	
	public function new_notification($cake_event)
	{
		$pubnub = new Pubnub( Configure::read('pubnub.keys.publish'), Configure::read('pubnub.keys.subscribe') );
		
		$Notification = $cake_event->subject();
		$User = new User();
		
		$user_id = $Notification->data['Notification']['user_id'];
		
		$user = $User->find('first', array(
			'contain' => array(),
			'conditions' => array(
				'User.user_id' => $user_id
			)
		));
		
		$channel = ServiceSparkUtility::Hash(
			$user['User']['push_key'],
			5
		);
		
		$result = $pubnub->publish($channel, array(
			'model' => 'Notification',
			'action' => 'created',
			'notification_id' => $Notification->id
		));
	}
}