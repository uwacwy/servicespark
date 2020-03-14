<?php

App::uses('AppController', 'Controller');
App::uses('JsonView', 'View');

App::uses('CakeEvent', 'Event');

/**
 * Class WebhooksController
 *
 * Receives incoming webhooks and dispatches them as events.
 *
 * @extends AppController
 */
class WebhooksController extends AppController
{

    public function beforeFilter()
    {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('api_get', 'api_post');
    }

    /**
     * @param $token string A token to match against the registered webhook event listeners
     */
    public function api_get($token) {
        $this->set('request', $this->request);
        $this->set('_serialize', array('req') );
    }

    public function api_post($token) {
        $event = new CakeEvent("App.Webhook.Post.$token", $this, array(
            'body' => (array) $this->request->input('json_decode')
        ));

        $this->getEventManager()->dispatch($event);

        $this->set('result', $event->result);
        $this->set('_serialize', array('result') );
    }

    public function api_put($token) {

    }

    public function api_patch($token) {

    }

    public function api_delete($token) {

    }
}