<?php
App::uses('User', 'Model');
App::uses('Recovery', 'Model');
App::uses('UsersController', 'Controller');
App::uses('RecoveriesController', 'Controller');

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * UsersController Test Case
 *
 */
if (! class_exists('UsersControllerTest') ) :

class UsersControllerTest extends ControllerTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'app.user',
		'app.recovery'
	);

	/**
	 * testLogin method
	 *
	 * @return void
	 */
	public function testLogin()
	{

	}

	/**
	 * testLogout method
	 *
	 * @return void
	 */
	public function testLogout()
	{

	}

	/**
	 * testIndex method
	 *
	 * @return void
	 */
	public function testFixtureSetup()
	{
		$hasher = new SimplePasswordHasher();
		$this->controller = new UsersController();

		$result = $this->testAction('/users/index', array('return' => 'vars'));
		foreach($result['users'] as $user)
		{
			$this->assertEquals( $hasher->hash($user['User']['password']), $user['Recovery']['token']);
		}
		//debug($result);
	}

	/**
	 * testView method
	 *
	 * @return void
	 */
	public function testView()
	{
		$this->controller = new UsersController();

		$i = 1;
		$action = sprintf('/users/view/%u', $i);
		$result = $this->testAction($action, array('return' => 'vars') );
		$this->assertEquals($i, $result['user']['User']['user_id']);
	}

	public function testRegister()
	{
		$this->controller = new UsersController();

		$i = 9999;

		$user = array(
			'User' => array(
				'username' => sprintf('test_user_%u', $i),
				'password_l' => 'Password',
				'password_r' => 'Password',
				'email' => sprintf('test_user_email_%u@testdomain.com', $i),
				'first_name' => sprintf('First_Name_%u', $i),
				'last_name' => sprintf('Last_Name_%u', $i)
			)
		);

		// $result = $this->testAction( '/users/register', array('data' => $user) );
		// debug($result);
	}


	public function testRegisterPasswordMismatch()
	{
		$this->controller = new UsersController();

		$i = time();

		$mismatch = array(
			'User' => array(
				'username' => sprintf('test_user_%u', $i),
				'password_l' => 'Password',
				'password_r' => 'password',
				'email' => sprintf('test_user_email_%u@testdomain.com', $i),
				'first_name' => sprintf('First_Name_%u', $i),
				'last_name' => sprintf('Last_Name_%u', $i)
			)
		);

		$result = $this->testAction('/users/register', array('data' => $mismatch, 'return' => 'vars') );
	}

	/**
	 * testEdit method
	 *
	 * @return void
	 */
	public function testEdit()
	{
	
	}


	/**
	 * testDelete method
	 *
	 * @return void
	 */
	public function testDelete()
	{

	}

}

endif;