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
			$q = strtolower( $this->params->query['q'] );
			$conditions = array(
				'LOWER(Skill.skill) LIKE' => "%$q%",
				'Skill.hidden' => false
			);
			$skills = $this->Skill->find('list', array('conditions' => $conditions) );			
		}
		$this->set('query', $q);
		$this->set('skills', $skills);
		$this->set('_serialize', array('query', 'skills') );
	}


}
