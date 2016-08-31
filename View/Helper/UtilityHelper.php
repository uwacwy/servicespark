<?php

require APP . 'Vendor/autoload.php';
App::uses('AppHelper', 'View/Helper');

use \Michelf\Markdown;

class UtilityHelper extends AppHelper
{

    public $helpers = array('Html');

	public function format_address($address)
	{

	}

	public function format_duration($start, $stop, $sprint)
	{
		
	}
	
	public function IsAre($number)
	{
		if( $number == 1 )
			return "is";
		
		return "are";
	}

	public function no_wrap($string)
	{
		return str_replace(' ', '&nbsp;', trim($string) );
	}
	
	public function markdown($string)
	{
		return Markdown::defaultTransform($string);
	}
	
	public function blurb($string, $chars = 140, $after_truncate = "&hellip;")
	{
		$length = strlen($string);
		
		$shortened = substr($string, 0, min($length, $chars) );
		
		$formatted = h( trim( strip_tags( html_entity_decode( $this->markdown($shortened) ) ) ) );
		
		if( $length > $chars )
			return $formatted . $after_truncate;
		else
			return $formatted;
	}

	public function btn_link_icon($text, $location_array, $link_class, $icon)
	{
		return sprintf('<a href="%1$s" class="%2$s" title="%3$s"><span class="glyphicon %4$s"></span> %5$s</a>',
			$this->Html->url( $location_array ), // link url
			$link_class, // button classes
			$text, // title
			$icon, // glyphicon
			$text // button text
		);
	}
	
	public function __p($sentences, $glue = " ")
	{
		foreach($sentences as $sentence)
		{
			
			
			if( is_array($sentence) )
			{
				$result[] = call_user_func(
					'__',
					$sentence[0],
					array_slice($sentence, 1)
				);
			}
			else
			{
				$result[] = __($sentence);
			}
		}
		return implode($result, $glue);
	}

}