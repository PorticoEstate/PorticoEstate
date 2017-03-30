<?php
	/**
	 * Handles encrypting strings based on various encryption schemes
	 * @author Joseph Engo <jengo@phpgroupware.org>
	 * @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	 * @package phpgwapi
	 * @subpackage network
	 * @version $Id: class.crypto.inc.php 13891 2015-09-14 19:31:31Z sigurdne $
	 */


	/**
	 * Handles encrypting strings based on various encryption schemes
	 *
	 * @package phpgwapi
	 * @subpackage network
	 */
	class phpgwapi_crypto extends phpgwapi_crypto_
	{

		function __construct( $vars = '' )
		{
			parent::__construct($vars);
		}

		function init( $vars )
		{
			/* _debug_array(mcrypt_list_algorithms()); */
			$key = $vars[0];
			$iv = $vars[1];

			if (($GLOBALS['phpgw_info']['server']['mcrypt_enabled'] || $GLOBALS['phpgw_info']['server']['enable_crypto'] == 'mcrypt') && extension_loaded('mcrypt') && !$this->enabled)
			{
				$this->algo = MCRYPT_TRIPLEDES;
				$this->mode = MCRYPT_MODE_CBC;

				if (isset($GLOBALS['phpgw_info']['server']['mcrypt_algo']))
				{
					$this->algo = $GLOBALS['phpgw_info']['server']['mcrypt_algo'];
				}
				if (isset($GLOBALS['phpgw_info']['server']['mcrypt_mode']))
				{
					$this->mode = $GLOBALS['phpgw_info']['server']['mcrypt_mode'];
				}

				if ($this->debug)
				{
					echo '<br>crypto: algorithm=' . $this->algo;
					echo '<br>crypto: mode     =' . $this->mode;
				}

				$this->enabled = True;
				/* Start up mcrypt */
				$this->td = mcrypt_module_open($this->algo, '', $this->mode, '');

				$ivsize = mcrypt_enc_get_iv_size($this->td);
				$keysize = mcrypt_enc_get_key_size($this->td);

				/* Hack IV to be the correct size */
				$x = strlen($iv);
				for ($i = 0; $i < $ivsize; $i++)
				{
					$this->iv .= $iv[$i % $x];
				}

				/* Hack Key to be the correct size */
				$x = strlen($key);

				for ($i = 0; $i < $keysize; $i++)
				{
					$this->key .= $key[$i % $x];
				}
			}
			/* If mcrypt isn't loaded, key and iv are not needed. */
		}

		function cleanup()
		{
			if ($this->enabled && $this->td)
			{
				@mcrypt_generic_deinit($this->td);
			}
		}

		function hex2bin( $data )
		{
			$len = strlen($data);
			return pack('H' . $len, $data);
		}

		function encrypt( $data, $bypass = false )
		{
			$_obj = false;
			if ($this->debug)
			{
				echo '<br>' . time() . ' crypto->encrypt() unencrypted data: ---->>>>' . $data . "\n";
			}

			if ($data === '' || is_null($data))
			{
				// no point in encrypting an empty string
				return $data;
			}

			if (is_array($data) || is_object($data))
			{
				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->encrypt() found an "' . gettype($data) . '".  Serializing...' . "\n";
				}
				$data = serialize($data);
				$_obj = true;
			}
			else
			{
				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->encrypt() found "' . gettype($data) . '". No serialization...' . "\n";
				}
				//FIXME - Strings are not decrypted correctly
				$data = serialize($data);
				$_obj = true;
			}

			/* Disable all encryption if the admin didn't set it up */
			if ($this->enabled && !$bypass)
			{
				if ($_obj)
				{
					if ($this->debug)
					{
						echo '<br>' . time() . ' crypto->encrypt() adding slashes' . "\n";
					}
					$data = addslashes($data);
				}

				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->encrypt() data: ---->>>>' . $data;
				}

				mcrypt_generic_init($this->td, $this->key, $this->iv);

				$encrypteddata = mcrypt_generic($this->td, $data);
				$encrypteddata = bin2hex($encrypteddata);

				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->encrypt() crypted data: ---->>>>' . $encrypteddata;
				}
				return $encrypteddata;
			}
			else
			{
				/* No mcrypt == insecure ! */
				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->encrypt() crypted data: ---->>>>' . $data;
				}
				return $data;
			}
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

			/* Disable all encryption if the admin didn't set it up */
			if ($this->enabled && !$bypass)
			{
				$data = $this->hex2bin($encrypteddata);
				mcrypt_generic_init($this->td, $this->key, $this->iv);
				$data = mdecrypt_generic($this->td, $data);

				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->decrypt() decrypted data: ---->>>>' . $data;
				}
				$test = stripslashes($data);
				if ($test)
				{
					if ($this->debug)
					{
						echo '<br>' . time() . ' crypto->decrypt() stripping slashes' . "\n";
					}
					$data = $test;
				}
				unset($test);

				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->decrypt() data: ---->>>>' . $data . "\n";
				}
			}
			else
			{
				/* No mcrypt == insecure ! */
				$data = $encrypteddata;
			}

			$newdata = @unserialize($data);
			if ($newdata || is_array($newdata)) // Check for empty array
			{
				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->decrypt() found serialized "' . gettype($newdata) . '".  Unserializing...' . "\n";
					echo '<br>' . time() . ' crypto->decrypt() returning: ';
					_debug_array($newdata);
				}
				return $newdata;
			}
			else
			{
				if ($this->debug)
				{
					echo '<br>' . time() . ' crypto->decrypt() found UNserialized "' . gettype($data) . '".  No unserialization...' . "\n";
					echo '<br>' . time() . ' crypto->decrypt() returning: ' . $data;
				}
				return $data;
			}
		}
	}
	// class crypto
