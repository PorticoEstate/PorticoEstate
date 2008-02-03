<?php
	/**
	* Setup - configuration hook
	*
	* cConfiguration hook
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	/**
	* Get selectbox for supported encryption algorithms selectbox
	*
	* @param $config
	* @return string HTML code for encryption algorithm selection
	*/
	function encryptalgo($config)
	{
		if(@function_exists('mcrypt_list_algorithms'))
		{
			$listed = array();
			if(!isset($config['mcrypt_algo']))
			{
				$config['mcrypt_algo'] = 'tripledes';  /* MCRYPT_TRIPLEDES */
			}
			$algos = @mcrypt_list_algorithms();
			$found = False;

			$out = '';
			while(list($key,$value) = each($algos))
			{
				$found = True;
				/* Only show each once - seems this is a problem in some installs */
				if(!in_array($value,$listed))
				{
					if($config['mcrypt_algo'] == $value)
					{
						$selected = ' selected="selected"';
					}
					else
					{
						$selected = '';
					}
					$descr = strtoupper($value);

					$out .= '<option value="' . $value . '"' . $selected . '>' . $descr . '</option>' . "\n";
					$listed[] = $value;
				}
			}
			if(!$found)
			{
				/* Something is wrong with their mcrypt install or php.ini */
				$out = '<option value="">' . lang('no algorithms available') . '</option>' . "\n";;
			}
		}
		else
		{
			$out = '<option value="tripledes">TRIPLEDES</option>' . "\n";;
		}
		return $out;
	}

	/**
	* Get encryption modes selectbox
	*
	* @param $config
	* @return string HTML select box
	*/
	function encryptmode($config)
	{
		if(@function_exists('mcrypt_list_modes'))
		{
			$listed = array();
			if(!isset($config['mcrypt_mode']))
			{
				$config['mcrypt_mode'] = 'cbc'; /* MCRYPT_MODE_CBC */
			}
			$modes = @mcrypt_list_modes();
			$found = False;

			$out = '';
			while(list($key,$value) = each($modes))
			{
				$found = True;
				/* Only show each once - seems this is a problem in some installs */
				if(!in_array($value,$listed))
				{
					if($config['mcrypt_mode'] == $value)
					{
						$selected = ' selected="selected"';
					}
					else
					{
						$selected = '';
					}
					$descr = strtoupper($value);

					$out .= '<option value="' . $value . '"' . $selected . '>' . $descr . '</option>' . "\n";
					$listed[] = $value;
				}
			}
			if(!$found)
			{
				/* Something is wrong with their mcrypt install or php.ini */
				$out = '<option value="" selected>' . lang('no modes available') . '</option>' . "\n";
			}
		}
		else
		{
			$out = '<option value="cbc" selected>CBC</option>' . "\n";
		}
		return $out;
	}


	/**
	* Get HTML selectbox with supported hash algorithms
	*
	* @param $config
	* @return string HTML select box
	*/
	function passwdhashes($config)
	{
		$hashes = array('des', 'md5');
		if ( function_exists('mhash') )
		{
			$hashes[] = 'sha';
		}

		$out = '';
		foreach ( $hashes as $hash )
		{
			$selected = '';
			if ( isset($config['ldap_encryption_type']) && $config['ldap_encryption_type'] == $hash)
			{
				$selected = ' selected="selected"';
			}

			$out .= '<option value="' . $hash . '"' . $selected . '>' . strtoupper($hash) . "</option>\n";
		}
		return $out;
	}
?>
