<?php

App::uses('AppHelper', 'View/Helper');

class UtilityHelper extends AppHelper
{

    public $helpers = array('Html');

	public function format_address($address)
	{

	}

	public function format_duration($start, $stop, $sprint)
	{
		
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

}