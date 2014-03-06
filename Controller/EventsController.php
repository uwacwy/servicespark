<?php
App::uses('AppController', 'Controller');
/**
 * Events Controller
 *
 * @property Event $Event
 * @property PaginatorComponent $Paginator
 */
class EventsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Event->recursive = 0;
		$this->set('events', $this->Paginator->paginate());
	}




/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {

		if (!$this->Event->exists($id))
		{
			throw new NotFoundException(__('Invalid event'));
		}

		
		$options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
		$this->set('event', $this->Event->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		if ($this->request->is('post')) 
		{
			if(! $this->Event->validTimes()) {
				return false;
			}
			// create address entry
			foreach($this->request->data['Address'] as $address)
			{
				// at a minimum, an address should have a line 1, city, state and zip
				if( 
					!empty( $address['address1'] ) && 
					!empty( $address['city'] ) && 
					!empty( $address['state'] ) &&
					!empty( $address['zip'] ) )
				{
					$this->Event->Address->create();
					$this->Event->Address->save($address);
					// get the address_id for the join table
					$address_ids['Address'][] = $this->Event->Address->id;
				}
			}
			unset( $this->request->data['Address'] );

			if( !empty($address_ids) )
				$this->request->data['Address'] = $address_ids;

			$hash = sha1( json_encode($this->request->data['Event']) ); // serializes the event and hashes it

			/*
				by choosing 9 characters from a base 16 hash, there are a total possible
				 68,719,476,736 hashes.  this should be adequate.
			*/
			$this->request->data['Event']['start_token'] = substr($hash, 0, 9); // 9 starting characters
			$this->request->data['Event']['stop_token'] = substr($hash, -9, 9); // 9 ending characters

			// create and save the event
			$this->Event->create();
			if ($this->Event->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The event has been saved.'));
				//debug($this->request->data);
				//return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The event could not be saved. Please, try again.'));
			}
		}
		$organization = $this->Event->Organization->find('list');
		$address = $this->Event->Address->find('all');
		$skills = null;
		$this->set( compact('skills', 'address', 'organization') );
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Event->exists($id)) {
			throw new NotFoundException(__('Invalid event'));
		}
		if ($this->request->is(array('post', 'put'))) 
		{
			if(! $this->Event->validTimes()) {
				return false;
			}

			foreach($this->request->data['Address'] as $address)
			{
				$this->Event->Address->save($address);
			}

			if ($this->Event->save($this->request->data)) {
				$this->Session->setFlash(__('The event has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The event could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
			$this->request->data = $this->Event->find('first', $options);
		}

		$address = $this->Event->Address->find('all');
		$this->set(compact('address'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Event->id = $id;
		if (!$this->Event->exists()) {
			throw new NotFoundException(__('Invalid event'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Event->delete()) {
			$this->Session->setFlash(__('The event has been deleted.'));
		} else {
			$this->Session->setFlash(__('The event could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}




}
