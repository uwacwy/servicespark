<?php
/**
 * AddressFixture
 *
 */
class AddressFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'address_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'mailing_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 120, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'mailing_city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'mailing_state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'mailing_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'physical_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 120, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'physical_city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'physical_state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'physical_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'address_id', 'unique' => 1)
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
			'address_id' => 1,
			'mailing_address' => 'Lorem ipsum dolor sit amet',
			'mailing_city' => 'Lorem ipsum dolor sit amet',
			'mailing_state' => '',
			'mailing_zip' => 'Lorem ipsum do',
			'physical_address' => 'Lorem ipsum dolor sit amet',
			'physical_city' => 'Lorem ipsum dolor sit amet',
			'physical_state' => '',
			'physical_zip' => 'Lorem ipsum do'
		),
	);

}
