<?php
	/**
	* Applications manager functions
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2001,2002 Mark Peters
	* @copyright Copyright (C) 2003 - 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Class for managing and installing applications
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_applications
	{
		private $account_id;
		private $data = array();
		private $db;
		public $public_functions = array
		(
			'list_methods' => True,
			'read'         => True
		);

		/**
		* Standard constructor for setting $account_id
		*
		* @param integer $account_id Account id
		*/
		public function __construct($account_id = '')
		{
			$this->db =& $GLOBALS['phpgw']->db;
			$this->set_account_id($account_id);
		}

		/**
		* Set the user's id
		*/
		public function set_account_id($account_id)
		{
			$this->account_id = get_account_id($account_id);
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
		function list_methods($_type = 'xmlrpc')
		{
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array
					(
						'read' => array
						(
							'function'  => 'read',
							'signature' => array(array(xmlrpcStruct)),
							'docstring' => lang('Returns struct of users application access')
						),
						'list_methods' => array
						(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct, xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
				/* SOAP disabled - no instance variable
				case 'soap':
					return $this->soap_functions;
				*/
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
					$this->data[$app['name']] = array
					(
						'title'   => lang($app['name']),
						'name'    => $app['name'],
						'enabled' => True,
						'status'  => $GLOBALS['phpgw_info']['apps'][$app['name']]['status'],
						'id'      => $GLOBALS['phpgw_info']['apps'][$app['name']]['id']
					);
				} 
			}
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
				foreach ( $apps as $app )
				{
					$this->data[$app[1]] = array
					(
						'title'   => lang($app[1]),
						'name'    => $app[1],
						'enabled' => true,
						'status'  => $GLOBALS['phpgw_info']['apps'][$app[1]]['status'],
						'id'      => $GLOBALS['phpgw_info']['apps'][$app[1]]['id']
					);
				}
			}
			else if (is_string($apps))
			{
				$this->data[$apps] = array
				(
					'title'   => lang($apps),
					'name'    => $apps,
					'enabled' => true,
					'status'  => $GLOBALS['phpgw_info']['apps'][$apps]['status'],
					'id'      => $GLOBALS['phpgw_info']['apps'][$apps]['id']
				);
			}
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
			$this->data = $data;
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

			if ( !is_array($this->data) || !count($this->data) )
			{
				return array();
			}

			foreach ( $this->data as $app )
			{
				if ( !$this->is_system_enabled($app) )
				{
					continue;
				}
				$GLOBALS['phpgw']->acl->add_repository($app, 'run', $this->account_id, 1);
			}
			return $this->data;
		}


		// These are the non-standard $account_id specific functions


		function app_perms()
		{
			if (count($this->data) == 0)
			{
				$this->read_repository();
			}
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
					$this->data[$app] = array
					(
						'title'   => lang($app),
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
					'name'    => $value['app_name'],
					'title'   => lang($value['app_name']),
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
			$id = (int) $id;
			foreach ( $GLOBALS['phpgw_info']['apps'] as $appname => $app )
			{
				if( $app['id'] == $id )
				{
					return $appname;
				}
			}
			return '';
		}
		
		function name2id($appname)
		{
			if ( is_array($GLOBALS['phpgw_info']['apps'][$appname]) )
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
