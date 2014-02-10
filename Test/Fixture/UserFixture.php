<?php
/**
 * UserFixture
 *
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('User', 'Model');

class UserFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'User');

/**
 * Records
 *
 * @var array
 */

	public function init()
	{
		$hasher = new SimplePasswordHasher();

		for($i = 1; $i < 21; $i++)
		{
			$this->records[] = array(
				'user_id' => $i,
				'created' => date('Y-m-d H:i:s'),
				'modified' => date('Y-m-d H:i:s'),
				'username' => sprintf('test_user_%u', $i),
				'password' => $hasher->hash( sprintf('test_password_%u', $i) ),
				'email' => sprintf('test_user_email_%u@testdomain.com', $i),
				'first_name' => sprintf('First_Name_%u', $i),
				'last_name' => sprintf('Last_Name_%u', $i)
			);
		}

		parent::init();
	}
}
