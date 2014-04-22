<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{
	public $actsAs = array('Containable');

	/*
		date_compare
		--
		can be used by Event and Time
	*/
	public function date_compare($value, $mode, $against)
	{
		$against_str = $this->data[$this->name][$against];
		$value_str = reset($value);

		
		if( $against == 'stop_time' && $against_str == null  )
		{
			return true;
		}

		if( $against == 'start_time' && $value_str == null)
		{
			return true;
		}		

		switch($mode)
		{
			case 'lt':
			case '<':
				//debug( sprintf('verifying %s < %s', $value_str, $against_str) );
				if( strtotime($value_str) < strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
			case 'lte':
			case '<=':
				//debug( sprintf('verifying %s <= %s', $value_str, $against_str) );
				if( strtotime($value_str) <= strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
			case 'gt':
			case '>':
				//debug( sprintf('verifying %s > %s', $value_str, $against_str) );
				if( strtotime($value_str) > strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
			case 'gte':
			case '>=':
				//debug( sprintf('verifying %s >= %s', $value_str, $against_str) );
				if( strtotime($value_str) < strtotime($against_str) )
				{
					//debug('returning true');
					return true;
				}
				break;
		}

		//debug('returning false');
		
		return false;

	}
}
