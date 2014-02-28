<?php
App::uses('AppController', 'Controller');
/**
 * Permissions Controller
 *
 * @property Permission $Permission
 * @property PaginatorComponent $Paginator
 */
class PermissionsController extends AppController {

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
		$this->Permission->recursive = 0;
		$this->set('permissions', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Permission->exists($id)) {
			throw new NotFoundException(__('Invalid permission'));
		}
		$options = array('conditions' => array('Permission.' . $this->Permission->primaryKey => $id));
		$this->set('permission', $this->Permission->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Permission->create();
			if ($this->Permission->save($this->request->data)) {
				$this->Session->setFlash(__('The permission has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The permission could not be saved. Please, try again.'));
			}
		}
		$organizations = $this->Permission->Organization->find('list');
		$users = $this->Permission->User->find('list');
		$this->set(compact('organizations', 'users'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Permission->exists($id)) {
			throw new NotFoundException(__('Invalid permission'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Permission->save($this->request->data)) {
				$this->Session->setFlash(__('The permission has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The permission could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Permission.' . $this->Permission->primaryKey => $id));
			$this->request->data = $this->Permission->find('first', $options);
		}
		$organizations = $this->Permission->Organization->find('list');
		$users = $this->Permission->User->find('list');
		$this->set(compact('organizations', 'users'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Permission->id = $id;
		if (!$this->Permission->exists()) {
			throw new NotFoundException(__('Invalid permission'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Permission->delete()) {
			$this->Session->setFlash(__('The permission has been deleted.'));
		} else {
			$this->Session->setFlash(__('The permission could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
