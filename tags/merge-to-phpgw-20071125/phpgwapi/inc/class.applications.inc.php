<?php
	/**
	* Applications manager functions
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2001,2002 Mark Peters
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.applications.inc.php,v 1.58 2007/01/07 02:18:41 skwashd Exp $
	*/

	/**
	* Class for managing and installing applications
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class applications
	{
		var $account_id;
		var $data = array();
		var $db;
		var $public_functions = array(
			'list_methods' => True,
			'read'         => True
		);
		var $xmlrpc_methods = array();


		/**
		* Standard constructor for setting $account_id
		*
		* @param integer $account_id Account id
		*/
		function applications($account_id = '')
		{
			$this->db =& $GLOBALS['phpgw']->db;
			$this->account_id = get_account_id($account_id);

			$this->xmlrpc_methods[] = array(
				'name'        => 'read',
				'description' => 'Return a list of applications the current user has access to'
			);
		}

		/**
		* Get available xmlrpc or soap methods
		*
		* @param string|array $_type Type of methods to list: 'xmlrpc' or 'soap'
		* @return array Array touple (might be empty) with the following fields in the keys value array: function, signature, docstring
		* This handles introspection or discovery by the logged in client,
		* in which case the input might be an array.  The server always calls
		* this function to fill the server dispatch map using a string.
		*/
		function list_methods($_type='xmlrpc')
		{
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'read' => array(
							'function'  => 'read',
							'signature' => array(array(xmlrpcStruct)),
							'docstring' => lang('Returns struct of users application access')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
				case 'soap':
					return $this->soap_functions;
				default:
					return array();
			}
		}


		// These are the standard $this->account_id specific functions


		/**
		* Read application repository from ACLs
		*
		* @return array|boolean Array with list of available applications or false
		* @access private
		*/
		function read_repository()
		{
			if (!isset($GLOBALS['phpgw_info']['apps']) ||
				!is_array($GLOBALS['phpgw_info']['apps']))
			{
				$this->read_installed_apps();
			}
			$this->data = Array();
			if ( $this->account_id == False )
			{
				return array();
			}

			$apps = $GLOBALS['phpgw']->acl->get_user_applications($this->account_id);
			foreach ( $GLOBALS['phpgw_info']['apps'] as $app )
			{
				//$check = $GLOBALS['phpgw']->acl->check('run',1,$app[0]);
				$check = isset($apps[$app['name']]) ? $apps[$app['name']] : False;
				if ($check)
				{
					$this->data[$app['name']] = array(
						'title'   => $GLOBALS['phpgw_info']['apps'][$app['name']]['title'],
						'name'    => $app['name'],
						'enabled' => True,
						'status'  => $GLOBALS['phpgw_info']['apps'][$app['name']]['status'],
						'id'      => $GLOBALS['phpgw_info']['apps'][$app['name']]['id']
					);
				} 
			}
			reset($this->data);
			return $this->data;
		}

		/**
		* Determine what applications a user has rights to
		* 
		* @return array List with applications for the user
		*/
		function read()
		{
			if (count($this->data) == 0)
			{
				$this->read_repository();
			}
			reset($this->data);
			return $this->data;
		}

		/**
		* Add an application to a user profile
		*
		* @param string|array $apps Array or string containing application names to add for a user
		* @return array List with applications for the user
		*/	
		function add($apps)
		{
			if(is_array($apps))
			{
				while($app = each($apps))
				{
					$this->data[$app[1]] = array(
						'title'   => $GLOBALS['phpgw_info']['apps'][$app[1]]['title'],
						'name'    => $app[1],
						'enabled' => True,
						'status'  => $GLOBALS['phpgw_info']['apps'][$app[1]]['status'],
						'id'      => $GLOBALS['phpgw_info']['apps'][$app[1]]['id']
					);
				}
			}
			else if (is_string($apps))
			{
				$this->data[$apps] = array(
					'title'   => $GLOBALS['phpgw_info']['apps'][$apps]['title'],
					'name'    => $apps,
					'enabled' => True,
					'status'  => $GLOBALS['phpgw_info']['apps'][$apps]['status'],
					'id'      => $GLOBALS['phpgw_info']['apps'][$apps]['id']
				);
			}
			reset($this->data);
			return $this->data;
		}
		
		/**
		* Delete an application from a user profile
		*
		* @param string $appname Application name
		* @return array List with applications for the user
		*/
		function delete($appname)
		{
			if($this->data[$appname])
			{
				unset($this->data[$appname]);
			}
			reset($this->data);
			return $this->data;
		}
		
		/**
		* Update list of applications for a user
		*
		* @param array $data Update the list of applications
		* @return array List with applications for the user
		*/
		function update_data($data)
		{
			reset($data);
			$this->data = Array();
			$this->data = $data;
			reset($this->data);
			return $this->data;
		}
		
		/**
		* Save the repository to the ACLs
		*
		* @return array List with applications for the user
		*/
		function save_repository()
		{
			$num_rows = $GLOBALS['phpgw']->acl->delete_repository("%%", 'run', $this->account_id);
			reset($this->data);
			while($app = each($this->data))
			{
				if(!$this->is_system_enabled($app[0]))
				{
					continue;
				}
				$GLOBALS['phpgw']->acl->add_repository($app[0],'run',$this->account_id,1);
			}
			reset($this->data);
			return $this->data;
		}


		// These are the non-standard $account_id specific functions


		function app_perms()
		{
			if (count($this->data) == 0)
			{
				$this->read_repository();
			}
			@reset($this->data);
			while (list ($key) = each ($this->data))
			{
				$app[] = $this->data[$key]['name'];
			}
			return $app;
		}

		function read_account_specific()
		{
			if (!is_array($GLOBALS['phpgw_info']['apps']))
			{
				$this->read_installed_apps();
			}
			$app_list = $GLOBALS['phpgw']->acl->get_app_list_for_id('run', 1, $this->account_id);

			if ( !is_array($app_list) || !count($app_list) )
			{
				return $this->data;
			}
			foreach ( $app_list as $app )
			{
				if ($this->is_system_enabled($app))
				{
					$this->data[$app] = array(
						'title'   => $GLOBALS['phpgw_info']['apps'][$app]['title'],
						'name'    => $app,
						'enabled' => true,
						'status'  => $GLOBALS['phpgw_info']['apps'][$app]['status'],
						'id'      => $GLOBALS['phpgw_info']['apps'][$app]['id']
					);
				}
			}
			return $this->data;
		}


		// These are the generic functions. Not specific to $account_id


		/**
		* Populate array with a list of installed apps
		*/
		function read_installed_apps()
		{
			$apps = (array) $this->db->adodb->GetAssoc('select * from phpgw_applications where app_enabled != 0 order by app_order asc');
			foreach($apps as $key => $value)
			{
				$GLOBALS['phpgw_info']['apps'][$value['app_name']] = array
				(
					'title'   => $value['app_name'],
					'name'    => $value['app_name'],
					'enabled' => true,
					'status'  => $value['app_enabled'],
					'id'      => (int) $value['app_id'],
					'order'   => (int) $value['app_order'],
					'version' => $value['app_version']
				);
			}
		}

		/**
		* Test if an application is enabled
		*
		* @param array $appname Names of the applications to test for. When the type is different read_installed_apps() will be used.
		* @return boolean True when the application is available otherwise false
		* @see read_installed_apps()
		*/
		function is_system_enabled($appname)
		{
			if(!is_array($GLOBALS['phpgw_info']['apps']))
			{
				$this->read_installed_apps();
			}
			return isset($GLOBALS['phpgw_info']['apps'][$appname]) && $GLOBALS['phpgw_info']['apps'][$appname]['enabled'];
		}

		function id2name($id)
		{
			@reset($GLOBALS['phpgw_info']['apps']);
			while (list($appname,$app) = each($GLOBALS['phpgw_info']['apps']))
			{
				if(intval($app['id']) == intval($id))
				{
					@reset($GLOBALS['phpgw_info']['apps']);
					return $appname;
				}
			}
			@reset($GLOBALS['phpgw_info']['apps']);
			return '';
		}
		
		function name2id($appname)
		{
			if(is_array($GLOBALS['phpgw_info']['apps'][$appname]))
			{
				return $GLOBALS['phpgw_info']['apps'][$appname]['id'];
			}
			else
			{
				return 0;
			}
		}
	}
?>
