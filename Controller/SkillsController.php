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
	
	public function api_popular()
	{
		$order_by =
			ServiceSparkUtility::ValueOrDefault($this->request->query['order_by'], 'event_count');
		$order_direction =
			ServiceSparkUtility::ValueOrDefault($this->request->query['order_direction'], 'DESC');
		$count =
			ServiceSparkUtility::ValueOrDefault($this->request->query['count'], 5);
		$exclude_user_skills =
			ServiceSparkUtility::ValueOrDefault($this->request->query['exclude_user_skills'], 'false');
		$excluded_skill_ids =
			ServiceSparkUtility::ValueOrDefault($this->request->query['excluded_skill_ids'], "");
			
		$exclude_user_skills = ($exclude_user_skills == 'true') ? true : false;
		$order_direction = $order_direction == 'ASC' ? 'ASC' : 'DESC';
			
		if( !in_array($order_by, array('skill', 'event_count', 'user_count', 'created', 'modified') ) )
			throw new CakeException(__("This is an invalid request") );
		
		if( !is_numeric($count) )
			$count = 5;
			
		$limit = min($count, 25);
		
		$conditions = array(
			'Skill.hidden' => 0
		);
		$fields = array(
			'Skill.skill'
		);
		
		$contain = array();
		$order = array( 'Skill.'.$order_by => $order_direction);
		
		if( $excluded_skill_ids != "" )
			$excluded_skill_ids = explode(',', $excluded_skill_ids);
		else
			$excluded_skill_ids = array();
			
		if( $exclude_user_skills )
		{
			ServiceSparkUtility::log('excluding user skills');
			// user_skill_id => skill_id
			$user_skill_ids = $this->Skill->SkillUser->find('list', array(
				'conditions' => array('SkillUser.user_id' => $this->Auth->user('user_id')),
				'fields' => array('SkillUser.skill_id')
			));
			
			//ServiceSparkUtility::log( array_values($user_skill_ids) );
			
			$excluded_skill_ids = array_merge( array_values($excluded_skill_ids), array_values($user_skill_ids) );
			
			//ServiceSparkUtility::log($excluded_skill_ids);
		}
		
		if( !empty($excluded_skill_ids) )
		{
			$conditions['NOT'] = array('Skill.skill_id' => $excluded_skill_ids);
		}
		
		//ServiceSparkUtility::log($conditions);
		
		$query = compact('conditions', 'order', 'fields', 'limit');
		
		//ServiceSparkUtility::log($query);
		
			
		$skills = $this->Skill->find('list', $query);
		
		$this->response->body( json_encode($skills) );
		$this->response->type('json');
		
		return $this->response;
		
	}


}
