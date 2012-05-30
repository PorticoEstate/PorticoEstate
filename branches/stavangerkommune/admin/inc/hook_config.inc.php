<?php
	/**
	* Administration - Configuration hook
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package admin
	* @subpackage hooks
	* @version $Id$
	*/

	/**
	* Encryption algorithm
	*
	* @param array $config
	* @return string HTML selecbox options
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

			while (list ($key, $value) = each ($algos))
			{
				$found = True;
				/* Only show each once - seems this is a problem in some installs */
				if(!in_array($value,$listed))
				{
					if ($config['mcrypt_algo'] == $value)
					{
						$selected = ' selected';
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

			while (list ($key, $value) = each ($modes))
			{
				$found = True;
				/* Only show each once - seems this is a problem in some installs */
				if(!in_array($value,$listed))
				{
					if ($config['mcrypt_mode'] == $value)
					{
						$selected = ' selected';
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
?>
