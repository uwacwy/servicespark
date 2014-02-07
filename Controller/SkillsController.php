<?php
App::uses('AppController', 'Controller');
/**
 * Skills Controller
 *
 */
class SkillsController extends AppController {

/**
*Components
*
* @var array 
**/

public $components = array(
	'Paginator'
	);

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
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Skill->create();
			if ($this->Skill->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			}
		}
	}

/**
* edit method
*
* @throws NotFoundException
* @param string $id
* @return void
*/
	public function edit($id = null) {
		if(! $this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		if ($this->request->is(array('post', 'put'))){
			if(! empty($this->data['Skill']['skill'])) {

			}
			else {

			}
		}
	}
}
