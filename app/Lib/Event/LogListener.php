<?php
App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');
App::uses('Event', 'Model');
App::uses('Comment', 'Model');

class LogListener implements CakeEventListener
{
    var $token = "";

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function implementedEvents()
    {
        return array(
            "App.Webhook.Post.$this->token" => 'incoming_post'
        );
    }

    public function incoming_post($event)
    {


        if( $event->data['body']['Message'] ) {
            $Message = (array) $event->data['body']['Message'];
            if( $Message ) {
                CakeLog::write(
                    'incoming_email',
                    print_r(array(
                        'is_array' => is_array($Message),
                        'Message' => json_decode($Message[0])
                    ), true)
                );
            }
        }

        $event->result['log'] = array('LogListener');
    }
}