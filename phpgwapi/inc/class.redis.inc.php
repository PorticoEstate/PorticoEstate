<?php
/**
	* phpGroupWare caching system
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2022 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 2 or later
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
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


	/**
	* Shared memory handler class
	*/
	class phpgwapi_redis
	{
		private $redis;
		private static $error_connect;
		private static $is_connected;
		
		/**
		* Constructor
		*/
		function __construct()
		{
			
			if(!$this->redis && !$this->error_connect && $this->is_enabled())
			{
				$this->connect();
			}
		}

		public function get_is_connected()
		{
			return $this->is_connected;
		}
		
		private function log_this($msg, $line)
		{
			$GLOBALS['phpgw']->log->error(array(
				'text'	=> 'data som feiler for phpgwapi_redis::connect(). Error: %1',
				'p1'	=> $msg,
				'line'	=> $line,
				'file'	=> __FILE__
			));

		}

		private function connect()
		{
			//Connecting to Redis server on localhost 
			$this->redis = new Redis();
			$host = 'redis';// docker...
//			$host = '127.0.0.1';// local
			$host = $GLOBALS['phpgw_info']['server']['redis_host'];
			$port = 6379;

			if(!$host)
			{
				$msg = 'Redis host not configured';
				phpgwapi_cache::message_set($msg, 'error');
				$this->error_connect = true;
				$this->log_this($msg, __LINE__);
				return;
			}
			
			try
			{
				$this->redis->connect($host, $port);
				$ping = $this->redis->ping();
				$this->error_connect = empty($ping);
				$this->is_connected = !!$ping;
			}
			catch (Exception $e)
			{
				$msg = $e->getMessage();
				phpgwapi_cache::message_set($msg, 'error');
				$this->error_connect = true;

				$this->log_this($msg, __LINE__);
			}
		}
 

		/**
		* Get a value from memory
		*
		* @todo document me properly
		*/
		function get_value($key)
		{
			return $this->redis->get($key); 
		}

		/**
		* Store a value in memory
		*
		* @todo document me properly
		*/
		function store_value($key, $value)
		{
			return $this->redis->set($key, $value); 
		}


	
		/**
		* Delete an entry from the cache
		*
		* @param int $key the entry to delete from the cache
		*/
		function delete_key($key)
		{
			return $this->redis->delete(array($key));
		}

		/**
		* Clear all values from the cache?
		*
		* @todo document me properly
		*/
		function clear_cache()
		{
			return $this->redis->delete($this->redis->keys('*'));
		}


		/**
		* Delete stale entries from the cache
		*/
	
	
		/**
		* Check if redis is enabled
		*
		* @return bool is it enabled?
		*/
		function is_enabled()
		{
			/**
			 * cache results within session
			 */
			static $enabled = false;
			static $checked = false;

			if($checked)
			{
				return $enabled;
			}

			if ( isset($GLOBALS['phpgw_info']['server']['redis_enable']) && $GLOBALS['phpgw_info']['server']['redis_enable'] )
			{
				$checked = true;
				$enabled = extension_loaded('redis');
				return $enabled;
			}

			return false;
		}
	}
