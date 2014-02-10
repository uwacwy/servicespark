<?php
/**
 * RecoveryFixture
 *
 */

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Recovery', 'Model');

class RecoveryFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	//public $import = array('model' => 'Recovery');

	public $fields = array(
		'user_id' => array('type' => 'integer', 'key' => 'primary'),
		'expiration' => 'datetime',
		'token' => array('type' => 'string', 'length' => 64)
	);

	public function init()
	{
		$hasher = new SimplePasswordHasher();

		for ($i=1; $i < 21; $i++)
		{ 
			$this->records[] = array(
				'user_id' => $i,
				'expiration' => date('Y-m-d H:i:s', strtotime('+3 days')),
				'token' => $hasher->hash( $hasher->hash( sprintf('test_password_%u', $i) ) )
			);
		}

		parent::init();
	}

}
