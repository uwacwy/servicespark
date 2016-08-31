<?php
/**
 * Defines App-wide utilities
 */
 App::uses('Hash', 'Utility');
 App::uses('CakeTime', 'Utility');
 App::uses('Router', 'Routing');
 
 /**
  * Utility class defines common utiltiies that can be called statically
  */
class ServiceSparkUtility
{
	
	public static function GetEmailProvider($override = null)
	{
		if( $override != null )
		{
			return new CakeEmail($override);
		}
		else if( Configure::read('debug') > 0 )
		{
			return new CakeEmail('development');	
		}
		else
		{
			return new CakeEmail('production');
		}
	}
	
	/**
	 * Hashes in a loop for a specified amount of $milliseconds
	 * 
	 * @param int $milliseconds a time in milliseconds to hash for
	 * @param string $algo the hashing algorithm to use
	 */
	 public static function HashFor($milliseconds, $algo = 'sha256')
	 {
	 	$start_time = time();
		$hash = Security::hash($start_time, $algo);
		
		while( (time() - $start_time) < $milliseconds )
		{
			$hash = Security::hash( $hash, $algo, time() );
		}
		
		return $hash;
	 }
	 
	 /**
	  * Hashes an input a specified time
	  * 
	  * @param object $input an object to hash
	  * @param int $times a number of times to hash
	  * @param string $algo the hashing algorithm to use
	  */
	 public static function Hash($input, $times = 1, $algo = 'sha256')
	 {
	 	if( $times < 1 || !is_numeric($times))
	 		$times = 1;
	 		
	 	$result = $input;
	 		
	 	for($i = 0; $i < $times; $i++)
	 	{
	 		$result = Security::hash($result, $algo);
	 	}
	 	
	 	return $result;
	 }
	 
	/**
	 * Generates a GUID
	 * 
	 * @link http://php.net/manual/en/function.com-create-guid.php#99425 From PHP documentation
	 * @return string Hex-formatted GUID string without enclosing curly braces
	 */
	public static function guid()
	{
	    if (function_exists('com_create_guid') === true)
	    {
	        return trim(com_create_guid(), '{}');
	    }
	
	    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	
	/**
	 * Writes information to the cake loggins system.  Automatically detects and formats arrays using {@link print_r()}
	 * 
	 * @param object $that An object to commit to the log.
	 * @param string $severity A CakeLog severity to
	 * @return void
	 */
	public static function log($that, $severity = "info")
	{
		$callers=debug_backtrace();
		$func = $callers[1]['function'];

		if( is_array($that) || is_object($that) )
			$that = print_r($that, true);
		
		CakeLog::write( $severity, $func . " ". $that );
	}
	
	/**
	 * Returns a value, or a default value.  Standardizes null checking
	 * 
	 * @param mixed $var a possibly null/empty value
	 * @param mixed $default a value to return when $var is null/empty
	 * @return If $var has a value, returns $var, otherwise returns $default
	 */
	public static function ValueOrDefault(&$var, $default = null)
	{
	    return (!isset($var) && empty($var)) ? $default : $var;
	}
	
	/**
	 * Builds an ICS file that can be emailed.  If a $user is provided, a guid will be generated that can be stored to reference incoming emails
	 * 
	 * @param array $event An array containing Event and Organization and Address arrays
	 * @param array $user A user that is the intended recipient for the ICS
	 * @return array Contains 'content' (the generated ICS) and 'guid' which can be used to uniquely reference this ICS
	 * @link https://gist.github.com/jakebellacera/635416 Based on jakebellacera's GitHub Gist
	 * 
	 */
	public static function ics($event, $user = null, $domain = null )
	{
		if($domain == null)
			$domain = gethostname();
			
		$rtn = array(
			'guid' => self::guid(),
			'content' => ""
		);
		
		$solution_name = Configure::read('Solution.name');
		
		$event_url = Router::url(array( 'go' => true, 'controller' => 'events', 'action' => 'view', $event['Event']['event_id'] ), true);

		$title = $event['Event']['title'];
		$description = str_replace("\r\n", "\\n", $event["Event"]['description']);
		
		$organization_name = $event['Organization']['name'];
		
		// Event should be at $event['Event']
		$start_time_utc = CakeTime::convert( CakeTime::fromString($event['Event']['start_time']), 'UTC');
		$stop_time_utc = CakeTime::convert( CakeTime::fromString($event['Event']['stop_time']), 'UTC');
		
		$now = CakeTime::convert( time(), 'UTC');
		
		$address_one_liners = array( __("no addresses specified") );
		if( !empty($event['Address']) )
		{
			$address_one_liners = Hash::extract($event, 'Address.{n}.one_line');
		}
		
		$lines = array(
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//uwacwy/servicespark//EN'
		);
		
		if( $user )
			$lines[] = 'METHOD:REQUEST';
		
		$lines[] = 'BEGIN:VEVENT';
		
		if( $user )
			$lines[] = sprintf('ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=%s;MAILTO:%s', $user['User']['full_name'], $user['User']['email']);
		
		$lines = Hash::merge($lines, array(
			sprintf('UID:%s', 			$rtn['guid'] ),
			sprintf('SUMMARY:%s', 		self::escapeIcs( $title ) ),
			sprintf('DESCRIPTION:%s', 	self::escapeIcs($description) ),
			sprintf('LOCATION:%s',		self::escapeIcs( implode(' or ', $address_one_liners) ) ),
			sprintf('ORGANIZER;CN=%s:MAILTO:%s',
				self::escapeIcs( sprintf("%s via %s", $organization_name, $solution_name ) ),
				self::escapeIcs( sprintf("%s@%s", $rtn['guid'], $domain) ) 
			),
			sprintf('URL;VALUE=URI:%s', self::escapeIcs( $event_url ) ),
			sprintf('DTSTART:%s', 		self::escapeIcsDate($start_time_utc)),
			sprintf('DTEND:%s', 		self::escapeIcsDate($stop_time_utc)),
			sprintf('DTSTAMP:%s', 		self::escapeIcsDate($now) ),
			'SEQUENCE:0',
			'END:VEVENT',
			'END:VCALENDAR'
		));
		
		$lines = array_map('self::icsLineSplit', $lines);
		
		$rtn['content'] = implode("\r\n", $lines);
		
		return $rtn;
	}
	
	public static function escapeIcsDate($timestamp)
	{
		return date('Ymd\THis\Z', $timestamp);
	}
	
	public static function escapeIcs($string)
	{
		return preg_replace('/([\,;])/','\\\$1', $string);
	}
	
	public static function icsLineSplit($string)
	{
		return trim( chunk_split( $string, 74, "\r\n ") );
	}
	
	public static function icsLineMerge($lines)
	{
		$result = array();
		$append_to = 0;
		
		foreach( $lines as $i => $line )
		{
			$first = substr($line, 0, 1);
			
			if( substr($line, 0 , 1) === " " )
				$result[ $append_to - 1 ] .= substr($line, 1);
			else
			{
				$result[ $append_to ] = $line;
				$append_to++;
			}
			
		}
		
		return $result;
	}
	
	public static function icsGetEventByUID($ics, $uid)
	{
		$uid = strtoupper($uid);

		$pattern = '/^BEGIN:VCALENDAR[\S\s]*METHOD:REPLY[\S\s]*(BEGIN:VEVENT[\S\s]*UID:'.$uid.'[\S\s]*END:VEVENT)[\S\s]*END:VCALENDAR$/';
		
		if( preg_match($pattern, $ics, $matches) == 1)
		{
			return $matches[1];
		}
		
		return false;
	}
	
	public static function icsGetRsvpFromEvent($ics)
	{
		$pattern = '/^ATTENDEE;.*PARTSTAT=(DECLINED|ACCEPTED|TENTATIVE).*$/m';
		
		if( preg_match($pattern, $ics, $matches) == 1)
		{
			return $matches[1];
		}
		
		return false;
	}
}