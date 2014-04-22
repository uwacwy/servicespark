<?php
/*
	AddressHelper
	--
	generates display controls to make dealing with addresses in forms easy
*/

App::uses('AppHelper', 'View/Helper');

class DurationHelper extends AppHelper
{
	public function format($start_str = null, $stop_str = null, $date_time_fmt = "F j, Y g:i a", $date_fmt = "F j, Y", $time_fmt = "g:i a")
	{

		if( !isset($start_str, $stop_str) )
		{
			return "start or stop was null";
		}


		$start = new DateTime($start_str);
		$stop = new DateTime($stop_str);


		if( $start->format('Y-m-d') == $stop->format('Y-m-d') )
		{
			return sprintf('%s - %s', $start->format($date_time_fmt), $stop->format($time_fmt) );
		}
		else
		{
			return sprintf('%s - %s', $start->format($date_time_fmt), $stop->format($date_time_fmt) );
		}

	}
}