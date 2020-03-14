<?php
App::uses('AppModel', 'Model');
/**
 * Recovery Model
 *
 * @property User $User
 */
class Recovery extends AppModel
{

/**
	General Model Behavior and Setup
*/
	public $primaryKey = 'user_id';

	public $displayField = 'token';

	/*
		expiration: date in future
	*/
	public $validate = array(
		'future' => array(
            'rule' => array('checkFutureDate'),
            'message' => 'The deadline must be not be in the past'
        )
	);


	public function checkFutureDate($check) {
	    $value = array_values($check);
    	return CakeTime::fromString($value['0']) >= CakeTime::fromString(date('Y-m-d'));
	}

/**
	Associations
*/
	/*
		This model is restricted to returning valid recoveries only
		--
		this might prevent malicious behavior in the future unless explicitly overridden
	*/
	public $hasOne = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => array(
				'Recovery.expiration >= Now()'
			)
		)
	);
}
