<?php
	/**
	* @package booking
	* @subpackage utilities
	* @version $Id: functions.inc.php 9188 2012-04-19 20:13:58Z sigurdne $
	*/

	/**
	 * Cherry pick selected values into a new array
	 * 
	 * @param array $array    input array
	 * @param array $keys     array of keys to pick
	 *
	 * @return array containg values from $array for the keys in $keys.
	 */
	function extract_values($array, $keys, $options = array())
	{
		static $default_options = array(
			'prefix' => '',
			'suffix' => '', 
			'preserve_prefix' => false,
			'preserve_suffix' => false
		);
		
		$options = array_merge($default_options, $options);
		
		$result = array();
		foreach($keys as $write_key)
		{
			$array_key = $options['prefix'].$write_key.$options['suffix'];
			if(isset($array[$array_key])) {
				$result[($options['preserve_prefix'] ? $options['prefix'] : '').$write_key.($options['preserve_suffix'] ? $options['suffix'] : '')] = $array[$array_key];
			}
		}
		return $result;
	}
	
	function array_set_default(&$array, $key, $value)
	{
		if(!isset($array[$key])) $array[$key] = $value;
	}


	/**
	 * Reformat an ISO timestamp into norwegian format
	 * 
	 * @param string $date    date
	 *
	 * @return string containg timestamp in norwegian format
	 */
	function pretty_timestamp($date)
	{
		if (empty($date)) return "";
		
		if(is_array($date) && is_object($date[0]) && $date[0] instanceof DOMNode)
		{
			$date = $date[0]->nodeValue;
		}
		preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}))?/', $date, $match);

		$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		if($match[4]) 
		{
			$dateformat .= ' H:i';
			$timestamp = mktime($match[5], $match[6], 0, $match[2], $match[3], $match[1]);
		}
		else
		{
			$timestamp = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
		}
		$text = date($dateformat,$timestamp);
			
		return $text;
	}


