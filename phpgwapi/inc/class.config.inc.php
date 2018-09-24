<?php

	/**
	* Application configuration in a centralized location
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage application
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
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Application configuration in a centralized location
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_config
	{
		/**
		 * @var object $db reference to global phpgwapi_db object
		 */
		private $db;

		/**
		 * @var string $module the module the data belongs to
		 */

		protected $module;
		/**
		 * @var array $config_data the configuration data - this is likely to become protected
		 */
		public $config_data = array();

		/**
		 *
		 * @var bool  $global_lock to be used in nested transactions
		 */
		protected $global_lock = false;

		/**
		 * Constructor
		 *
		 * @param string $module the module to store the data for
		 */
		public function __construct($module = '')
		{
			if ( !$module )
			{
				$module = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			$this->db      =& $GLOBALS['phpgw']->db;
			$this->module = $this->db->db_addslashes($module);
		}


		/**
		 * Load the config values for the current module
		 *
		 * @return array the config values
		 */

		public function read()
 		{
			if(!count($this->config_data))
			{
				$this->read_repository();
			}
			return $this->config_data;
		}

		/**
		 * Load the config values for the current module
		 * @todo change to protected
		 *
		 * @return array the config values
		 */
		public function read_repository()
		{
			static $data_cache = array();

			if(!empty($data_cache[$this->module]))
			{
				$this->config_data = $data_cache[$this->module];
				return $this->config_data;
			}

			$this->config_data = array();
			
			$this->db->query("SELECT * FROM phpgw_config WHERE config_app='{$this->module}'",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$test = @unserialize($this->db->f('config_value', true));
				if($test)
				{
					$this->config_data[$this->db->f('config_name')] = $test;
				}
				else
				{
					$this->config_data[$this->db->f('config_name')] = $this->db->f('config_value', true);
				}
			}

			$data_cache[$this->module] = $this->config_data;

			return $this->config_data;
		}

		/**
		 * Store the current configuration
		 */
		public function save_repository()
		{
			$config_data = $this->config_data;

			if ( is_array($config_data) && count($config_data) )
			{
				if ( $this->db->get_transaction() )
				{
					$this->global_lock = true;
				}
				else
				{
					$this->db->transaction_begin();
				}

				$this->delete_repository();
				foreach ( $config_data as $name => $value )
				{
					if(is_array($value))
					{
						$value = serialize($value);
					}
					$name  = $this->db->db_addslashes($name);
					$value = $this->db->db_addslashes($value);
					$query = "INSERT INTO phpgw_config (config_app,config_name,config_value) "
						. "VALUES ('{$this->module}', '{$name}', '{$value}')";
					$this->db->query($query, __LINE__, __FILE__);
				}

				if ( !$this->global_lock )
				{
					$this->db->transaction_commit();
				}
			}
		}

		/**
		 * Delete all configuration data for the selected module
		 */
		public function delete_repository()
		{
			$this->db->query("DELETE FROM phpgw_config WHERE config_app='{$this->module}'",__LINE__,__FILE__);
		}

		/**
		 * Add/update configuration data
		 *
		 * Should call save_repository() to store the new configuration data
		 *
		 * @var string $name the key for the configuration data data
		 * @var mixed $data the data to store
		 */
		public function value($name, $data)
		{
			$this->config_data[$name] = $data;
		}
	}
