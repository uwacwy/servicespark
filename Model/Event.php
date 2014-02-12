<?php
App::uses('AppModel', 'Model');
/**
 * Event Model
 *
 */
class Event extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'event_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

}
