<?php
	/**
	* Setup - configuration hook
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage setup
	* @caegory hooks
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Get a list of possible domains to be used for a session cookies
	 *
	 * @param array $config the current configuration values
	 * @return string HTML snippet with the available domain options
	 */
	function cookie_domain($config)
	{
		$current_domain = '';
		if ( isset($config['cookie_domain']) )
		{
			$current_domain = $config['cookie_domain'];
		}

		$available = array();
		$domain_parts = explode('.', $_SERVER['HTTP_HOST']);

		foreach ( $domain_parts as $cnt => $part )
		{
			$str = '.' . implode('.', $domain_parts);
			$available[$str] = $str;
			unset($domain_parts[$cnt]);
		}

		// give the user a sane default
		$available[''] = lang('request fqdn');

		$available = array_reverse($available, true);

		$out = '';
		foreach ( $available as $key => $domain )
		{
			$sel = '';
			if ( $key == $current_domain )
			{
				$sel = ' selected';
			}
			$out .= "<option value=\"{$key}\"{$sel}>{$domain}</option>\n";
		}
		return $out;
	}

	/**
	 * Get selectbox for supported encryption algorithms selectbox
	 *
	 * @param $config
	 * @return string HTML code for encryption algorithm selection
	 */
	function encryptalgo($config)
	{
		if ( function_exists('mcrypt_list_algorithms') )
		{
			$listed = array();
			if(!isset($config['mcrypt_algo']))
			{
				$config['mcrypt_algo'] = MCRYPT_TRIPLEDES; 
			}
			$algos = mcrypt_list_algorithms();
			$found = False;

			$out = '';
			foreach ( $algos as $algo )
			{
				$found = True;
				/* Only show each once - seems this is a problem in some installs */
				if ( in_array($algo, $listed) )
				{
					continue;
				}

				$selected = '';
				if ( $config['mcrypt_algo'] == $algo )
				{
					$selected = ' selected';
				}

				$descr = strtoupper($algo);

				$out .= "<option value=\"{$algo}\"{$selected}>{$descr}</option>\n";
				$listed[] = $algo;
			}
			if(!$found)
			{
				/* Something is wrong with their mcrypt install or php.ini */
				$out = '<option value="">' . lang('no algorithms available') . '</option>' . "\n";
			}
		}
		else
		{
			$out = '<option value="tripledes">TRIPLEDES</option>' . "\n";
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
		if ( function_exists('mcrypt_list_modes') )
		{
			$listed = array();
			if ( !isset($config['mcrypt_mode']) )
			{
				$config['mcrypt_mode'] = 'cbc'; /* MCRYPT_MODE_CBC */
			}
			$modes = mcrypt_list_modes();
			$found = False;

			$out = '';
			foreach ( $modes as $mode )
			{
				$found = True;
				/* Only show each once - seems this is a problem in some installs */
				if ( in_array($mode, $listed) )
				{
					continue;
				}

				$selected = '';
				if ( $config['mcrypt_mode'] == $mode )
				{
					$selected = ' selected';
				}

				$descr = strtoupper($mode);

				$out .= "<option value=\"{$mode}\"{$selected}>{$descr}</option>\n";
				$listed[] = $mode;
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
		$hashes = array
		(
			'SSHA'	=> lang('Salted SHA1 - strong encryption'),
			'SMD5'	=> lang('Salted MD5'),
			'SHA'	=> lang('SHA1'),
			'MD5'	=> lang('MD5 - Vulnerable to dictionary attack'),
			'CRYPT'	=> lang('Crypt - Weak encryption')
		);

		if ( !isset($config['encryption_type']) )
		{
			$config['encryption_type'] = 'SSHA';
		}
		$enc_type = $config['encryption_type'];

		$out = '';
		foreach ( $hashes as $hash => $label)
		{
			$selected = '';
			if ( $enc_type == $hash)
			{
				$selected = ' selected';
			}

			$out .=  <<<HTML
				<option value="{$hash}"{$selected}>{$label}</option>";

HTML;
		}
		return $out;
	}
	/**
	* Configureable password securitylevel
	*
	* @param $config
	* @return string HTML select box
	*/
	function passwdlevels($config)
	{
		$levels = array
		(
			'8CHAR'	=> lang('at least 8 characters long'),
			'2UPPER'	=> lang('..and at least 2 upper case characters'),
			'2LOW'	=> lang('..and at least 2 lower case characters'),
			'1NUM'	=> lang('..and contain at least 1 number'),
			'NONALPHA'	=> lang('..and at least 1 non alphanumeric character')
		);

		if ( !isset($config['password_level']) )
		{
			$config['password_level'] = 'NONALPHA';
		}
		$enc_type = $config['password_level'];

		$out = '';
		foreach ( $levels as $level => $label)
		{
			$selected = '';
			if ( $enc_type == $level)
			{
				$selected = ' selected';
			}

			$out .=  <<<HTML
				<option value="{$level}"{$selected}>{$label}</option>";

HTML;
		}
		return $out;
	}
