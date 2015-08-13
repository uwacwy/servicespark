<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		// Allow users to register and logout.
		$this->Auth->allow('display');
	}

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

		if($title_for_layout == "Home")
		{
			// gather appropriate statistics!
			App::uses('Time', 'Model');
			App::uses('EventTime', 'Model');
			App::uses('User', 'Model');
			App::uses('Event', 'Model');

			$user = new User();
			//$user->unbindModel( array('hasOne' => array('Recovery') ), false );

			$time = new Time();
			//$time->unbindModel( array('belongsTo' => array('User') ), false );

			$event = new Event( );
			
			$event_time = new EventTime();
			//$event->unbindModel(array('hasAndBelongsToMany' => array('Address', 'Skill'), 'hasMany' => array('Time'), 'belongsTo' => array('Organization') ), false);

			// TODO: unbind models so these counts are faster
			$event->Behaviors->load('Containable');
			$user->Behaviors->load('Containable');
			$time->Behaviors->load('Containable');
			$event_time->Behaviors->load('Containable');
			
			$currently_volunteering = $event_time->find('count', array(
				'contain' => array(
					'Event',
					'Time'
				),
				'conditions' => array(
					'Event.stop_time <= DATE_ADD(Now(), interval 2 hour)',
					'Time.stop_time IS NULL')
			));



			$registered_volunteers = $user->find('count');

			$sql_date_time = "Y-m-d H:i:s";

			$conditions = array(
				'Time.start_time >=' => date( $sql_date_time, mktime(0, 0, 0, 1, 1) ),
				'Time.stop_time <' => date( $sql_date_time, mktime(0, 0, 0, 1, 1, date('Y')+1 ) ),
				'Time.status' => 'approved'
			);
			$fields = array(
				'SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as PeriodTotal'
			);
			$users_ytd = $time->find('all', array('conditions' => $conditions, 'fields' => $fields) );
			
			$conditions = array();
			$contain = array(
				'Time' => array(
					'conditions' => array(
						'Time.status' => 'approved'
					),
					'fields' => array(
						''
					),
					'order' => array('UserTotal' => 'desc')
					
				));
			$limit = 10;
			
			$users_top = $user->query("SELECT User.first_name, User.last_name, SUM( TIMESTAMPDIFF(MINUTE, Time.start_time, Time.stop_time) )/60 as UserTotal FROM
			`users` User
			LEFT JOIN `times` Time ON User.user_id = Time.user_id
			WHERE Time.status = 'approved'
			AND YEAR(Time.start_time) = YEAR( NOW() )
			GROUP BY User.first_name, User.last_name
			ORDER BY UserTotal DESC
			LIMIT 10");
			
			
			

			// $conditions = array(
			// 	'Time.start_time >=' => date( $sql_date_time, mktime(0, 0, 0, date('n'), 1) ),
			// 	'Time.stop_time <=' => date( $sql_date_time, mktime(23, 59, 59, date('n')+1, 0) )
			// );
			// $users_month = $time->find('all', array('conditions' => $conditions, 'fields' => $fields) );

			$conditions = array(
				'Event.stop_time > Now()'
			);
			$upcoming_events = $event->find('count', array('conditions' => $conditions) );

			$this->set( compact('currently_volunteering', 'registered_volunteers', 'users_ytd', 'upcoming_events', 'users_top') );
			$this->set('render_container', false);
		}

		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
}
