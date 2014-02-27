<?php
App::uses('AddressesEvent', 'Model');

/**
 * AddressesEvent Test Case
 *
 */
class AddressesEventTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.addresses_event'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AddressesEvent = ClassRegistry::init('AddressesEvent');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AddressesEvent);

		parent::tearDown();
	}

}
