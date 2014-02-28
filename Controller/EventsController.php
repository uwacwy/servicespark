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
		if (!$this->Event->exists($id)) {
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

			debug($this->request->data);
			// create address entry
			foreach($this->request->data['Address'] as $address)
			{
				$this->Event->Address->create();
				$this->Event->Address->save($address);
				// get the address_id for the join table
				$addressIds['Address'][] = $this->Event->Address->id;
			}

			unset($this->request->data['Address']);
			$this->request->data['Address'] = $addressIds;
			debug($this->request->data);

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
		$address = $this->Event->Address->find('all');
		$this->set(compact('address'));
		$skills = $this->Event->Skill->find('list');
		$this->set( compact('skills') );
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
