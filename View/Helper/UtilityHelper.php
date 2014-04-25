<?php

App::uses('AppHelper', 'View/Helper');

class UtilityHelper extends AppHelper
{
	public function format_address($address)
	{

	}

	public function format_duration($start, $stop, $sprint)
	{
		
	}

	public function no_wrap($string)
	{
		return str_replace(' ', '&nbsp;', trim($string) );
	}
}