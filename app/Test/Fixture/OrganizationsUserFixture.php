<?php
/**
 * OrganizationsUserFixture
 *
 */
class OrganizationsUserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'organization_user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'organization_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'publish' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'read' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'write' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'organization_user_id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_bin', 'engine' => 'MyISAM')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'organization_user_id' => 1,
			'organization_id' => 1,
			'user_id' => 1,
			'publish' => 1,
			'read' => 1,
			'write' => 1
		),
	);

}
