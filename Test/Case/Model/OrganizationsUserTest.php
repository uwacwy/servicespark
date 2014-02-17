<?php
App::uses('OrganizationsUser', 'Model');

/**
 * OrganizationsUser Test Case
 *
 */
class OrganizationsUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.organizations_user',
		'app.organization',
		'app.user',
		'app.recovery',
		'app.skill',
		'app.skills_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->OrganizationsUser = ClassRegistry::init('OrganizationsUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->OrganizationsUser);

		parent::tearDown();
	}

}
