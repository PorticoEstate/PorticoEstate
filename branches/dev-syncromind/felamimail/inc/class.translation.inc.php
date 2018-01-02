<?php
/**
 * eGroupWare API - Translations
 * 
 * @link http://www.egroupware.org
 * @author Joseph Engo <jengo@phpgroupware.org>
 * @author Dan Kuykendall <seek3r@phpgroupware.org>
 * Copyright (C) 2000, 2001 Joseph Engo
 * @license http://opensource.org/licenses/lgpl-license.php LGPL - GNU Lesser General Public License
 * @package api
 * @version $Id$
 */

// define the maximal length of a message_id, all message_ids have to be unique
// in this length, our column is varchar 128

// Define prefix for langfiles (historically 'phpgw_')

/**
 * eGroupWare API - Translations
 */

class felamimail_translation
{

	/**
	 * Constructor, sets up a copy of the db-object, gets the system-charset and tries to load the mbstring extension
	 */
	function __construct($warnings = False)
	{
		if (extension_loaded('mbstring') || @dl(PHP_SHLIB_PREFIX.'mbstring.'.PHP_SHLIB_SUFFIX)) {
			$this->mbstring = true;
			if(!empty($this->system_charset)) {
				ini_set('mbstring.internal_encoding',$this->system_charset);
			}
			if (ini_get('mbstring.func_overload') < 7) {
				if ($warnings) {
					echo "<p>Warning: Please set <b>mbstring.func_overload = 7</b> in your php.ini for useing <b>$this->system_charset</b> as your charset !!!</p>\n";
				}
			}
		} else {
			if ($warnings) {
				echo "<p>Warning: Please get and/or enable the <b>mbstring extension</b> in your php.ini for useing <b>$this->system_charset</b> as your charset, we are defaulting to <b>iconv</b> for now !!!</p>\n";
			}
		}
	}


	/**
	 * converts a string $data from charset $from to charset $to
	 *
	 * @param string/array $data string(s) to convert
	 * @param string/boolean $from charset $data is in or False if it should be detected
	 * @param string/boolean $to charset to convert to or False for the system-charset the converted string
	 * @return string/array converted string(s) from $data
	 */
	function convert($data,$from=False,$to=False)
	{
		if (is_array($data))
		{
			foreach($data as $key => $str)
			{
				$ret[$key] = $this->convert($str,$from,$to);
			}
			return $ret;
		}

		if ($from)
		{
			$from = strtolower($from);
		}
		if ($to)
		{
			$to = strtolower($to);
		}

		if (!$from)
		{
			$from = $this->mbstring ? strtolower(mb_detect_encoding($data)) : 'iso-8859-1';
			if($from == 'ascii')
			{
				$from = 'iso-8859-1';
			}
			//echo "<p>autodetected charset of '$data' = '$from'</p>\n";
		}
		/*
			 php does not seem to support gb2312
			 but seems to be able to decode it as EUC-CN
		*/
		switch($from)
		{
			case 'gb2312':
			case 'gb18030':
				$from = 'EUC-CN';
				break;
			case 'us-ascii':
			case 'macroman':
			case 'iso8859-1':
			case 'windows-1258':
			case 'windows-1252':
				$from = 'iso-8859-1';
				break;
			case 'windows-1250':
				$from = 'iso-8859-2';
				break;
		}
		if (!$to)
		{
			$to = 'utf-8';
		}
		if ($from == $to || !$from || !$to || !$data)
		{
			return $data;
		}
		if ($from == 'iso-8859-1' && $to == 'utf-8')
		{
			return utf8_encode($data);
		}
		if ($to == 'iso-8859-1' && $from == 'utf-8')
		{
			return utf8_decode($data);
		}
		if ($this->mbstring && mb_convert_encoding($data,$to,$from)!="")
		{
			return @mb_convert_encoding($data,$to,$from);
		}
		if(function_exists('iconv'))
		{
			// iconv can not convert from/to utf7-imap
			if ($to == 'utf7-imap' && function_exists(imap_utf7_encode)) 
			{
				$convertedData = iconv($from, 'iso-8859-1', $data);
				$convertedData = imap_utf7_encode($convertedData);
				
				return $convertedData;
			}

			if ($from == 'utf7-imap' && function_exists(imap_utf7_decode)) 
			{
				$convertedData = imap_utf7_decode($data);
				$convertedData = iconv('iso-8859-1', $to, $convertedData);

				return $convertedData;
			}

			// the following is to workaround patch #962307
			// if using EUC-CN, for iconv it strickly follow GB2312 and fail 
			// in an email on the first Traditional/Japanese/Korean character, 
			// but in reality when people send mails in GB2312, UMA mostly use 
			// extended GB13000/GB18030 which allow T/Jap/Korean characters.
			if($from=='EUC-CN') 
			{
				$from='gb18030';
			}

			if (($convertedData = iconv($from,$to,$data))) 
			{
				return $convertedData;
			}
		}
		#die("<p>Can't convert from charset '$from' to '$to' without the <b>mbstring extension</b> !!!</p>");

		// this is not good, not convert did succed
		return $data;
	}
}
