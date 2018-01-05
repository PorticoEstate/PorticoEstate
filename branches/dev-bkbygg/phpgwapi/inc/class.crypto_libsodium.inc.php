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
			$key = $vars[0];

			if ($GLOBALS['phpgw_info']['server']['enable_crypto'] == 'libsodium' && extension_loaded('sodium') && !$this->enabled)
			{
				$this->enabled = true;

				$keysize = SODIUM_CRYPTO_SECRETBOX_KEYBYTES;
				/* Hack Key to be the correct size */
				$x = strlen($key);

				for ($i = 0; $i < $keysize; $i++)
				{
					$this->key .= $key[$i % $x];
				}
			}
		}

		function cleanup()
		{
			if ($this->enabled)
			{
				sodium_memzero($this->key);
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
				$encrypteddata = $this->safeEncrypt( $data, $this->key );
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
				$data = $this->safeDecrypt( $encrypteddata, $this->key );

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

		/**
		 * Encrypt a message
		 *
		 * @param string $message - message to encrypt
		 * @param string $key - encryption key
		 * @return string
		 */
		function safeEncrypt( $message, $key )
		{
			$nonce = random_bytes(
				SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
			);

			$cipher = base64_encode(
				$nonce .
				sodium_crypto_secretbox(
					$message, $nonce, $key
				)
			);
			sodium_memzero($message);
			sodium_memzero($key);
			return $cipher;
		}

		/**
		 * Decrypt a message
		 *
		 * @param string $encrypted - message encrypted with safeEncrypt()
		 * @param string $key - encryption key
		 * @return string
		 */
		function safeDecrypt( $encrypted, $key )
		{
			$decoded = base64_decode($encrypted);
			if ($decoded === false)
			{
				return false;
//				throw new \Exception('Scream bloody murder, the encoding failed');
			}
			if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES))
			{
				return $encrypted;
//				throw new \Exception('Scream bloody murder, the message was truncated');
			}
			$nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
			$ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

			$plain = sodium_crypto_secretbox_open(
				$ciphertext, $nonce, $key
			);
			if ($plain === false)
			{
//				throw new \Exception('Scream bloody murder, the message was tampered with in transit');
			}
			sodium_memzero($ciphertext);
			sodium_memzero($key);
			return $plain;
		}
	}
