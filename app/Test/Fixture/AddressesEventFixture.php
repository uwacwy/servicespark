<?php
/**
 * AddressesEventFixture
 *
 */
class AddressesEventFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'address_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'fk: address'),
		'event_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'fk: event'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'event_id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'address_id' => 1,
			'event_id' => 1
		),
	);

}
