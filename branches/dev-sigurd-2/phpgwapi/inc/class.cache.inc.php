<?php
	/**
	* phpGroupWare caching system
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 3 or later
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id: class.acl.inc.php 775 2008-02-24 23:18:32Z dave $
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* phpGroupWare caching system
	*
	* Simple data caching system with common ways to store/retreive data
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category caching
	*/

	class phpgwapi_cache
	{
		/**
		 * Clear stored data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return bool was the data deleted?
		 */
		protected static function _file_clear($key)
		{
			$fn = self::_gen_filename($key);
			if ( is_file($fn) && is_writable($fn) )
			{
				return unlink($fn);
			}
			return true;
		}

		/**
		 * Retreive data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return mixed the data from shared memory
		 */
		protected static function _file_get($key)
		{
			$fn = self::_gen_filename($key);
			if ( is_readable($fn) )
			{
				return file_get_contents($fn);
			}
			return null;
		}

		/**
		 * Store data in shared memory
		 *
		 * @param string $key the data identifier
		 * @param mixed $value the data to store
		 * @return bool was the data stored in shared memory
		 */
		protected static function _file_set($key, $value)
		{
			$fn = self::_gen_filename($key);
			return !!file_put_contents($fn, $value, LOCK_EX);
		}

		/**
		 * Generate the key for the data to be stored/retreived
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return string a unique hash for the data
		 */
		protected static function _gen_key($module, $id)
		{
			return sha1("{$GLOBALS['phpgw_info']['server']['install_id']}::{$module}::{$id}");
		}

		/**
		 * Generate a filename for storing cached data
		 *
		 * @param string $key the data identifier
		 * @return string the filename for be used for caching data
		 */
		protected static function _gen_filename($key)
		{
			return "{$GLOBALS['phpgw_info']['server']['temp_dir']}/phpgw_cache_{$key}";
		}

		/**
		 * Clear stored data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return bool was the data deleted?
		 */
		protected static function _shm_clear($key)
		{
			return $GLOBALS['phpgw']->shm->delete_key($key);
		}

		/**
		 * Retreive data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return mixed the data from shared memory
		 */
		protected static function _shm_get($key)
		{
			return $GLOBALS['phpgw']->shm->get_value($key);
		}

		/**
		 * Store data in shared memory
		 *
		 * @param string $key the data identifier
		 * @param mixed $value the data to store
		 * @return bool was the data stored in shared memory
		 */
		protected static function _shm_set($key, $value)
		{
			return $GLOBALS['phpgw']->shm->store_value($key, $value);
		}

		/**
		 * Prepares a value for storage - all values must  be run through here before caching
		 *
		 * @param mixed the value to store
		 * @return value to store as a string
		 */
		protected static function _value_prepare($value)
		{
			return $GLOBALS['phpgw']->crypto->encrypt(serialize($value));
		}

		/**
		 * Returns a value is a usable form - all values must be run through here before returning to the user
		 *
		 * @param string $str the string to process
		 * @return mixed the unserialized string
		 */
		protected static function _value_return($str)
		{
			if ( is_null($str) )
			{
				return null;
			}

			// crypto class unserializes the data for us
			return $GLOBALS['phpgw']->crypto->decrypt($str);
		}

		/**
		 * Clear a value from the session cache
		 *
		 * @param string $module the module to store the data
		 * @param string $id the identifier for the data
		 */
		public static function session_clear($module, $id)
		{
			$key = self::_gen_key($module, $id);
			if ( isset($_SESSION['phpgw_cache'][$key]) )
			{
				unset($_SESSION['phpgw_cache'][$key]);
			}
			// we don't really care if it is already not set
			return true;
		}

		/**
		 * Retreive data from session cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return mixed the data from session cache
		 */
		public static function session_get($module, $id)
		{
			$key = self::_gen_key($module, $id);
			if ( isset($_SESSION['phpgw_cache'][$key]) )
			{
				return self::_value_return($_SESSION['phpgw_cache'][$key]);
			}
			return null;
		}

		/**
		 * Store data in the session cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store
		 * @return bool was the data stored in the session cache?
		 */
		public static function session_set($module, $id, $data)
		{
			$key = self::_gen_key($module, $id);
			$_SESSION['phpgw_cache'][$key] = self::_value_prepare($data);
			return true;
		}

		/**
		 * Clear data stored in the system wide cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return bool was the data deleted?
		 */
		public static function system_clear($module, $id)
		{
			$key = self::_gen_key($module, $id);

			if ( $GLOBALS['phpgw']->shm->is_enabled() )
			{
				return self::_shm_clear($key);
			}
			return self::_file_clear($key);
		}

		/**
		 * Retreive data from system wide cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return mixed the data from system wide cache
		 */
		public static function system_get($module, $id)
		{
			$key = self::_gen_key($module, $id);

			if ( $GLOBALS['phpgw']->shm->is_enabled() )
			{
				$value = self::_shm_get($key);
			}
			else
			{
				$value = self::_file_get($key);
			}
			return self::_value_return($value);
		}

		/**
		 * Store data in the system wide cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store
		 * @return bool was the data stored in the system wide cache?
		 */
		public static function system_set($module, $id, $value)
		{
			$key = self::_gen_key($module, $id);
			$value = self::_value_prepare($value);

			if ( $GLOBALS['phpgw']->shm->is_enabled() )
			{
				return self::_shm_set($key, $value);
			}
			return self::_file_set($key, $value);
		}

		/**
		 * Clear the data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id the data is stored for
		 * @return bool was the data deleted?
		 */
		public static function user_clear($module, $id, $uid)
		{
			$key = $GLOBALS['phpgw']->db->db_addslashes(self::_gen_key($module, $id));
			$uid = (int) $uid;

			$sql = "DELETE FROM phpgw_cache_user WHERE item_key = '{$key}'";

			// this is a bit of a hack, but we need some way of clearing cache values of all users - i am open to suggestions
			if ( $uid <> -1 )
			{
				$sql .= " AND user_id = {$uid}";
			}
			return !!$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
		}

		/**
		 * Retreive data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id to the data is stored for
		 * @return mixed the data from user cache
		 */
		public static function user_get($module, $id, $uid)
		{
			$key = $GLOBALS['phpgw']->db->db_addslashes(self::_gen_key($module, $id));
			$uid = (int) $uid;

			$ret = null;
			
			$sql = "SELECT cache_data FROM phpgw_cache_user WHERE user_id = {$uid} AND item_key = '{$key}'";
			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			if ( $GLOBALS['phpgw']->db->next_record() )
			{
				$ret = $GLOBALS['phpgw']->db->f('cache_data');
				if(function_exists('gzcompress'))
				{
					$ret =  gzuncompress(base64_decode($ret));
				}
				else
				{
					$ret = stripslashes($ret);
				}
				$ret = self::_value_return($ret);
			}
			return $ret;
		}

		/**
		 * Store data in the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store in user cache
		 * @param int $uid the user id to store the data for
		 * @return bool was the data stored in the user cache?
		 */
		public static function user_set($module, $id, $value, $uid)
		{
			$key = $GLOBALS['phpgw']->db->db_addslashes(self::_gen_key($module, $id));
			$uid = (int) $uid;
			$value = self::_value_prepare($value);
			if(function_exists('gzcompress'))
			{
				$value =  base64_encode(gzcompress($value, 9));
			}
			else
			{
				$value = $GLOBALS['phpgw']->db->db_addslashes($value);
			}

			$now = time();

			$GLOBALS['phpgw']->db->query("SELECT user_id FROM phpgw_cache_user WHERE item_key = '{$key}' AND user_id = {$uid}", __LINE__, __FILE__);
			if ( $GLOBALS['phpgw']->db->next_record() )
			{
				$sql = 'UPDATE phpgw_cache_user'
					. " SET cache_data = '{$value}', lastmodts = {$now}"
					. " WHERE item_key = '{$key}' AND user_id = {$uid}";
			}
			else
			{
				$sql = "INSERT INTO phpgw_cache_user (item_key, user_id, cache_data, lastmodts) VALUES('{$key}', {$uid}, '{$value}', $now)";
			}

			return !!$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
		}
	}
