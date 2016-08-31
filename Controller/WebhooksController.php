<?php
App::uses('AppController', 'Controller');

class WebhooksController extends AppController
{
	var $uses = false;
	
	public function beforeFilter()
	{
		$this->Auth->allow('incoming');
	}
	
	public function incoming($token = null)
	{
		if($token != null)
		{
			$event = new CakeEvent("Webhook.Incoming.$token", $this, null);
			$this->getEventManager()->dispatch($event);
		}
		
		$this->response->type('application/json');
		$this->response->body('');
		return $this->response;
	}
}