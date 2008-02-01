<?php
	/**
	* Application configuration in a centralized location
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Application configuration in a centralized location
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class config
	{
		var $db;
		var $appname;
		var $config_data;

		function config($appname = '')
		{
			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			$this->db      =& $GLOBALS['phpgw']->db;
			$this->appname = $this->db->db_addslashes($appname);
		}

		function read_repository()
		{
			$this->config_data = array();
			
			$this->db->query("SELECT * FROM phpgw_config WHERE config_app='{$this->appname}'",__LINE__,__FILE__);
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
		}

		function save_repository()
		{
			$config_data = $this->config_data;

			if ( is_array($config_data) && count($config_data) )
			{
				$this->db->lock(array('phpgw_config','phpgw_app_sessions'));
				$this->delete_repository();
				if($this->appname == 'phpgwapi')
				{
					$this->db->query('DELETE FROM phpgw_app_sessions'
									. " WHERE sessionid = '0' and loginid = '0' and app = '{$this->appname}' AND location = 'config'",
									__LINE__,__FILE__);
				}
				foreach ( $config_data as $name => $value )
				{
					if(is_array($value))
					{
						$value = serialize($value);
					}
					$name  = $this->db->db_addslashes($name);
					$value = $this->db->db_addslashes($value);
					$query = "INSERT INTO phpgw_config (config_app,config_name,config_value) "
						. "VALUES ('{$this->appname}', '{$name}', '{$value}')";
					$this->db->query($query, __LINE__, __FILE__);
				}
				$this->db->unlock();
			}
		}

		function delete_repository()
		{
			$this->db->query("DELETE FROM phpgw_config WHERE config_app='{$this->appname}'",__LINE__,__FILE__);
		}

		function value($variable_name,$variable_data)
		{
			$this->config_data[$variable_name] = $variable_data;
		}
	}
?>