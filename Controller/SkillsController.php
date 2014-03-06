<?php
App::uses('AppController', 'Controller');
/**
 * Skills Controller
 *
 * @property Skill $Skill
 * @property PaginatorComponent $Paginator
 */
class SkillsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'RequestHandler');

	public function beforeFilter()
	{
		parent::beforeFilter();
		// Allow users to register and logout.
		$this->Auth->allow('search');
	}

	public function search()
	{
		$q = null;
		if( isset( $this->params->query['q'] ) )
		{
			$q = $this->params->query['q'];
			$conditions = array('Skill.skill LIKE' => "%$q%" );
			$skills = $this->Skill->find('list', array('conditions' => $conditions) );			
		}
		$this->set('query', $q);
		$this->set('skills', $skills);
		$this->set('_serialize', array('query', 'skills') );
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->Skill->recursive = 0;
		$this->set('skills', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $id));
		$this->set('skill', $this->Skill->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Skill->create();

			if ($this->Skill->saveAll($this->request->data)) {
				return $this->flash(__('The skill has been saved.'), array('action' => 'index'));
			}
		}

		$users = $this->Skill->User->find('list');
		$this->set(compact('users'));

		$events = $this->Skill->Event->find('list');
		$this->set(compact('events'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid skill'));
		}

		if ($this->request->is(array('post', 'put'))) {
			if ($this->Skill->save($this->request->data)) {
				return $this->flash(__('The skill has been saved.'), array('action' => 'index'));
			}
		} 
		else {
			$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $id));
			$this->request->data = $this->Skill->find('first', $options);
		}

		$users = $this->Skill->User->find('list');
		$this->set(compact('users'));

		$events = $this->Skill->Event->find('list');
		$this->set(compact('events'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Skill->id = $id;

		if (!$this->Skill->exists()) {
			throw new NotFoundException(__('Invalid skill'));
		}

		$this->request->onlyAllow('post', 'delete');

		if ($this->Skill->delete()) {
			return $this->flash(__('The skill has been deleted.'), array('action' => 'index'));
		} 
		else {
			return $this->flash(__('The skill could not be deleted. Please, try again.'), array('action' => 'index'));
		}
	}
}
