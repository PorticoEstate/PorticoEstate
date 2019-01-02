<?php
	/**
	 * Handles encrypting strings based on various encryption schemes
	 * @author Joseph Engo <jengo@phpgroupware.org>
	 * @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	 * @package phpgwapi
	 * @subpackage network
	 * @version $Id$
	 */
/*
	if(!empty($GLOBALS['phpgw_info']['server']['enable_crypto']))
	{
		if( $GLOBALS['phpgw_info']['server']['enable_crypto'] == 'libsodium' )
		{
			require_once PHPGW_API_INC . '/class.crypto_libsodium.inc.php';
		}
		else if ($GLOBALS['phpgw_info']['server']['enable_crypto'] == 'mcrypt' || !empty($GLOBALS['phpgw_info']['server']['mcrypt_enabled']))
		{
			require_once PHPGW_API_INC . '/class.crypto_mcrypt.inc.php';
		}
	}

	if (!class_exists("phpgwapi_crypto"))
	{
		class phpgwapi_crypto extends phpgwapi_crypto_
		{
		}
	}
*/

	if(!empty($GLOBALS['phpgw_info']['server']['mcrypt_enabled']) || $GLOBALS['phpgw_info']['server']['enable_crypto'] == 'mcrypt' )
	{
		require_once PHPGW_API_INC . '/class.crypto_mcrypt.inc.php';
	}
	else if( $GLOBALS['phpgw_info']['server']['enable_crypto'] == 'libsodium' )
	{
		require_once PHPGW_API_INC . '/class.crypto_libsodium.inc.php';
	}
	else
	{
		//Fall back
		class phpgwapi_crypto extends phpgwapi_crypto_
		{
		}
	}

	/**
	 * Handles encrypting strings based on various encryption schemes
	 *
	 * @package phpgwapi
	 * @subpackage network
	 */
	class phpgwapi_crypto_
	{
		var $enabled = false;
		var $debug = false;
		var $algo;
		var $mode;
		var $td; /* Handle for mcrypt */
		var $iv = '';
		var $key = '';

		function __construct( $vars = '' )
		{
			if (is_array($vars))
			{
				$this->init($vars);
			}
			register_shutdown_function(array(&$this, 'cleanup'));
		}

		function init( $vars )
		{
		}

		function cleanup()
		{
		}

		function hex2bin( $data )
		{
			$len = strlen($data);
			return pack('H' . $len, $data);
		}

		function encrypt( $data, $bypass = false )
		{

			if ($data === '' || is_null($data))
			{
				// no point in encrypting an empty string
				return $data;
			}

			return serialize($data);
		}

		function decrypt( $encrypteddata, $bypass = false )
		{
			if ($this->debug)
			{
				echo '<br>' . time() . ' crypto->decrypt() crypted data: ---->>>>' . $encrypteddata;
			}

			if ($encrypteddata === '' || is_null($encrypteddata))
			{
				// an empty string is always a usless empty string
				return $encrypteddata;
			}

			$data = $encrypteddata;
			

			$newdata = @unserialize($data);
			if ($newdata || is_array($newdata)) // Check for empty array
			{
				return $newdata;
			}
			else
			{
				return $data;
			}
		}
	}