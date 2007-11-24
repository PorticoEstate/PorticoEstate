<?php
	/**************************************************************************\
	* phpGroupWare - Account Administration                                    *
	* http://www.phpgroupware.org                                              *
	* Written by coreteam <phpgroupware-developers@gnu.org>                    *
	* -----------------------------------------------------                    *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: class.boaccounts.inc.php,v 1.58 2007/01/24 17:33:38 Caeies Exp $ */

	//FIXME define constants for rights so we can fuck all these magic numbers
	
	class boaccounts
	{
		var $so;
		var $public_functions = array
		(
			'add_group'          => true,
			'delete_group'       => true,
			'delete_user'        => true,
			'edit_group'         => true,
			'save_user'          => true,
			'set_group_managers' => true
		);

		var $xml_functions = array();

		var $soap_functions = array
		(
			'add_user'	=> array
			(
				'in'	=> array('int','struct'),
				'out'	=> array()
			)
		);

		function boaccounts()
		{
			$this->so = createObject('admin.soaccounts');
		}

		function DONTlist_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'rpc_add_user' => array(
							'function'  => 'rpc_add_user',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Add a new account.')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		function check_rights($action, $access = 'group_access')
		{
			switch($action)
			{
				case 'view':	$right = '8'; break;
				case 'add':		$right = '4'; break;
				case 'edit':	$right = '16'; break;
				case 'delete':	$right = '32'; break;
				case 'search':	$right = '2'; break;
			}

			if (!$GLOBALS['phpgw']->acl->check($access,$right,'admin'))
			{
				return True;
			}
			return False;
		}

		function edit_group($values)
		{
			if ($GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_EDIT,'admin'))
			{
				$error[] = lang('no permission to create groups');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			$old_group = CreateObject('phpgwapi.accounts', $values['account_id'], 'g');
			$old_group->read_repository();			
			$old_group->member($old_group->account_id);
			
			$new_group = CreateObject('phpgwapi.accounts', $values['account_id'], 'g');
			$new_group->read_repository();
			$new_group->firstname = $values['account_name'];

			//TODO Move to transactions?
			$GLOBALS['phpgw']->db->lock(array('phpgw_accounts',
											  'phpgw_preferences',
											  'phpgw_config',
											  'phpgw_applications',
											  'phpgw_hooks',
											  'phpgw_sessions',
											  'phpgw_acl',
											  'phpgw_app_sessions',
											  'phpgw_lang' // why lang? no idea, ask sigurd :)
											 ));

			$id = $values['account_id'];
			if ( $id == 0 ) // add new group?
			{
			  	$new_group_values = array
			  						(
										'account_type'	=> 'g',
										'account_lid'	=> $values['account_name'],
										'passwd'		=> '',
										'firstname'		=> $values['account_name'],
										'lastname'		=> lang('group'),
										'status'		=> 'A',
										'expires'		=> -1
									);
				$id = $new_group->create($new_group_values, false);
				//echo "bo::edit_group id == {$id}";
			}
			else //edit group
			{
				$new_group->save_repository();
			}
			$GLOBALS['phpgw']->db->unlock();															 

			// get all new applications for this group
			$apps = CreateObject('phpgwapi.applications', $values['account_id']);
			$old_apps = array_keys($apps->read()); 	 
			foreach($values['account_apps'] as $key => $value)
			{
				if(!in_array($key, $old_apps))
				{
					$new_apps[] = $key;
				}
			}
			$this->set_module_permissions($new_group->account_id, $values['account_apps']);

			// members handling
			// Add new members to group
			$acl = CreateObject('phpgwapi.acl', $values['account_id']);
			$old_group_list = $old_group->get_members();
			for($i = 0; $i < count($values['account_user']); $i++)
			{
				$is_new = true;
				for($j = 0; $j < count($old_group_list); $j++)
				{
					if($values['account_user'][$i] == $old_group_list[$j])
					{
						$old_group_list[$j] = false;
						$is_new = false;
						break;
					}
				}
				if($is_new)
				{
					$acl->add_repository('phpgw_group', $new_group->account_id, $values['account_user'][$i],1);
					$this->refresh_session_data($values['account_user'][$i]);
					
					// The following sets any default preferences needed for new applications..
					// This is smart enough to know if previous preferences were selected, use them.
					$docommit = false;
					if(count($new_apps))
					{
						$GLOBALS['pref'] =& CreateObject('phpgwapi.preferences', $values['account_user'][$i]);
						$t = $GLOBALS['pref']->read_repository();
						foreach ( $new_apps as $app_name)
						{
							if($app_name == 'admin') //another workaround :-(
							{
								$app_name == 'common';
							}
							
							if ( !$t[$app_name] )
							{
								$GLOBALS['phpgw']->hooks->single('add_def_pref', $app_name);
								$docommit = true;
							}
						}
					}
					if ($docommit)
					{
						$GLOBALS['pref']->save_repository();
					}
				}
			}
			// Remove members from group
			foreach($old_group_list as $key => $value)
			{
				if($value)
				{
					$acl->delete_repository('phpgw_group',$new_group->account_id, $value);
					$this->refresh_session_data($values['account_user'][$i]);
				}
			}
			
			//Add the group manager
			$acl->add_repository('phpgw_group', $new_group->account_id, $values['group_manager'], PHPGW_ACL_GROUP_MANAGERS | 1);

			// Things that have to change because of new group name
			// FIXME this needs to be changed to work with all VFS backends
			if($old_group->account_lid != $new_group->account_lid)
			{
				$basedir = $GLOBALS['phpgw_info']['server']['files_dir'] . SEP . 'groups' . SEP;
				@rename($basedir . $old_group->account_lid, $basedir . $new_group->account_lid);
			}			
			return $id;
		}

		/**
		* Saves a new user (account) or update an existing one
		*
		* @param array $values Account details
		* @return null No return value
		*/
		function save_user($values)
		{
			if (is_array($values))
			{
				if(isset($values['expires_never']) && $values['expires_never'])
				{
					$values['expires'] = $values['account_expires'] = -1;
				}
				else
				{
					$values['expires'] = $values['account_expires'] = mktime(2,0,0,$values['account_expires_month'],$values['account_expires_day'],$values['account_expires_year']);
				}

				$userData = array
				(
					'account_type'			=> 'u',
					'account_lid'			=> $values['account_lid'],
					'account_firstname'		=> $values['account_firstname'],
					'account_lastname'		=> $values['account_lastname'],
					'passwd'				=> $values['account_passwd'], //TODO see if this still needed
					'account_passwd'		=> $values['account_passwd'],
					'status'				=> $values['account_status'] ? 'A' : '',
					'old_loginid'			=> $values['old_loginid'] ? rawurldecode(phpgw::get_var('old_loginid', 'string', 'GET')) : '',
					'account_id'			=> $values['account_id'],
					'account_passwd_2'		=> $values['account_passwd_2'],
					'groups'				=> $values['account_groups'],
					'account_permissions'	=> $values['account_permissions'],
					'homedirectory'			=> isset($values['homedirectory']) ? $values['homedirectory'] : '',
					'loginshell'			=> isset($values['loginshell']) ? $values['loginshell'] : '',
					'account_expires_month'	=> $values['account_expires_month'],
					'account_expires_day'	=> $values['account_expires_day'],
					'account_expires_year'	=> $values['account_expires_year'],
					'account_expires_never'	=> $values['expires'],
					'expires'				=> $values['expires'],
					'quota'					=> $values['quota']
					/* 'file_space' => $_POST['account_file_space_number'] . "-" . $_POST['account_file_space_type'] */
				);

				if ($values['account_id']) //user exists
				{
					$userData['account_id'] = $values['account_id'];
					$this->so->update_user($userData);

					if ($userData['passwd'])
					{
						$auth = CreateObject('phpgwapi.auth');
						$auth->change_password($old_passwd,$userData['passwd'],$userData['account_id']);
						$GLOBALS['hook_values']['account_id'] = $userData['account_id'];
						$GLOBALS['hook_values']['old_passwd'] = $old_passwd;
						$GLOBALS['hook_values']['new_passwd'] = $userData['account_passwd'];
						$GLOBALS['phpgw']->hooks->process('changepassword');
					}
					
					$GLOBALS['phpgw']->session->delete_cache(intval($userData['account_id']));
					/* check if would create a menu
					// if we do, we can't return to the users list, because
					// there are also some other plugins
					if (!ExecMethod('admin.uimenuclass.createHTMLCode','edit_user'))
					{
					}*/
				}
				else //new user
				{
					$userData['account_id'] = $this->so->add_user($userData);
					$GLOBALS['hook_values']['account_lid'] = $userData['account_lid'];
					$GLOBALS['hook_values']['account_id']	 = $userData['account_id'];
					$GLOBALS['hook_values']['new_passwd']	 = $userData['passwd'];
					$GLOBALS['phpgw']->hooks->process('addaccount');
				}
				$this->set_module_permissions($userData['account_id'], $userData['account_permissions']);
				$this->set_groups2account($userData['account_id'], $userData['groups']);			
			}
		}

		function set_group_managers()
		{
			if($GLOBALS['phpgw']->acl->check('group_access',16,'admin') || phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups'));
				$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
				exit;
			}
			else if( phpgw::get_var('submit', 'bool', 'POST') )
			{
				$acl = CreateObject('phpgwapi.acl',intval($_POST['account_id']));
				
				$users = $GLOBALS['phpgw']->accounts->member($_POST['account_id']);
				@reset($users);
				while($managers && list($key,$user) = each($users))
				{
					$acl->add_repository('phpgw_group', phpgw::get_var('account_id', 'int', 'POST'), $user['account_id'],1);
				}
				$managers = phpgw::get_var('managers', 'int', 'POST');
				@reset($managers);
				while($managers && list($key,$manager) = each($managers))
				{
					$acl->add_repository('phpgw_group', phpgw::get_var('account_id', 'int', 'POST'), $manager,(1 + PHPGW_ACL_GROUP_MANAGERS));
				}
			}
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups'));
			$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
			exit;
		}

		function validate_group($values)
		{
			$group = CreateObject('phpgwapi.accounts',$values['account_id'],'g');
			$group->read_repository();

			if ( $values['account_id'] == 0 && $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_ADD,'admin'))
			{
				$error[] = lang('no permission to add groups');
			}

			if(!$values['account_name'])
			{
				$error[] = lang('You must enter a group name.');
			}

			if($values['account_name'] != $group->lid)
			{
				if ($group->exists($values['account_name']))
				{
					$error[] = lang('Sorry, that group name has already been taken.');
				}
			}

		/*
			if (preg_match ("/\D/", $account_file_space_number))
			{
				$error[] = lang ('File space must be an integer');
			}
		*/
			if(isset($error) && is_array($error))
			{
				return $error;
			}
		}

		/* checks if the userdata are valid
		 returns FALSE if the data are correct
		 otherwise the error array
		*/
		function validate_user($values)
		{
			$error = array();
			if ( !(isset($values['account_id']) && $values['account_id']) && $GLOBALS['phpgw']->acl->check('account_access',4,'admin'))
			{
				$error[] = lang('no permission to add users');
			}

			/*
			if ($GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap' && ! $values['allow_long_loginids'])
			{
				if (strlen($values['account_lid']) > 8) 
				{
					$error[] = lang('The loginid can not be more then 8 characters');
				}
			}
			*/

			if (!$values['account_lid'])
			{
				$error[] = lang('You must enter a loginid');
			}

			if ($values['old_loginid'] != $values['account_lid']) 
			{
				if ($GLOBALS['phpgw']->accounts->exists($values['account_lid']))
				{
					$error[] = lang('That loginid has already been taken');
				}
			}

			if ($values['account_passwd'] || $values['account_passwd_2']) 
			{
				if ($values['account_passwd'] != $values['account_passwd_2']) 
				{
					$error[] = lang('The two passwords are not the same');
				}

			/*	$temp_msgbox_data = $GLOBALS['phpgw_info']['flags']['msgbox_data'];
				unset($GLOBALS['phpgw_info']['flags']['msgbox_data']);
				if(!sanitize($_userData['account_passwd'],'password'))
				{
					reset($GLOBALS['phpgw_info']['flags']['msgbox_data']);
					while(list($key) = each($GLOBALS['phpgw_info']['flags']['msgbox_data']))
					{
						$error[$totalerrors] = lang($key);
						$totalerrors++;
					}
				}
				$GLOBALS['phpgw_info']['flags']['msgbox_data'] = $temp_msgbox_data;
				unset($temp_msgbox_data); */
			}

			if (!count($values['account_permissions']) && !count($values['account_groups'])) 
			{
				$error[] = lang('You must add at least 1 permission or group to this account');
			}

			if ( !$values['expires_never'] 
				|| ($values['account_expires_month'] && $values['account_expires_day'] && $values['account_expires_year']) )
			{
				if (! checkdate($values['account_expires_month'],$values['account_expires_day'],$values['account_expires_year']))
				{
					$error[] = lang('You have entered an invalid expiration date');
				}
			}

		/*
			$check_account_file_space = explode ('-', $_userData['file_space']);
			if (preg_match ("/\D/", $check_account_file_space[0]))
			{
				$error[$totalerrors] = lang ('File space must be an integer');
				$totalerrors++;
			}
		*/

			if (is_array($error) && count($error) != 0)
			{
				return $error;
			}
		}


		function delete_group($account_id)
		{
			if ($GLOBALS['phpgw']->acl->check('group_access',32,'admin'))
			{
				return False;
			}

			$GLOBALS['phpgw']->db->lock(array
				(
					'phpgw_accounts',
					'phpgw_acl',
					'phpgw_sessions' // should be in direct in the session class!?
				)
			);

			$old_group_list = $GLOBALS['phpgw']->acl->get_ids_for_location($account_id,1,'phpgw_group');

			@reset($old_group_list);
			while($old_group_list && $id = each($old_group_list))
			{
				$GLOBALS['phpgw']->acl->delete_repository('phpgw_group',$account_id,intval($id[1]));
				$GLOBALS['phpgw']->session->delete_cache(intval($id[1]));
			}

			$GLOBALS['phpgw']->acl->delete_repository('%%','run',$account_id);

			@rmdir($GLOBALS['phpgw_info']['server']['files_dir'].SEP.'groups'.SEP.$GLOBALS['phpgw']->accounts->id2name($account_id));

			$GLOBALS['phpgw']->accounts->delete($account_id);
			$GLOBALS['phpgw']->db->unlock();
		}

		function delete_user($id, $newowner)
		{
			if($GLOBALS['phpgw']->acl->check('account_access',32,'admin'))
			{
				ExecMethod('admin.uiaccounts.list_users');
				return False;
			}
			
			$account_id = get_account_id( (int) $id );
			$GLOBALS['hook_values']['account_id'] = $account_id;

			$db = clone($GLOBALS['phpgw']->db);
			$db->query('SELECT app_name,app_order FROM phpgw_applications WHERE app_enabled != 0 ORDER BY app_order',__LINE__,__FILE__);
			if($db->num_rows())
			{
				while($db->next_record())
				{
					$appname = $db->f('app_name');

					if($appname != 'admin' && $appname != 'preferences')
					{
						$GLOBALS['phpgw']->hooks->single('deleteaccount', $appname);
					}
				}
			}

			$GLOBALS['phpgw']->hooks->single('deleteaccount','preferences');
			$GLOBALS['phpgw']->hooks->single('deleteaccount','admin');

			$GLOBALS['phpgw']->hooks->process('deleteaccount');

			//<??[+_+]??
			$basedir = $GLOBALS['phpgw_info']['server']['files_dir'] . SEP . 'users' . SEP;
			$lid = $GLOBALS['phpgw']->accounts->id2name($account_id);
			if (! @rmdir($basedir . $lid))
			{
				$cd = 34;
			}
			else
			{
				$cd = 29;
			}
			//<??[+_+]??
			return $this->so->delete_user($account_id);
		}

		function load_group_users($account_id)
		{
			$temp_user = $GLOBALS['phpgw']->acl->get_ids_for_location($account_id,1,'phpgw_group');
			if(!$temp_user)
			{
				return Array();
			}
			else
			{
				$group_user = $temp_user;
			}
			$account_user = Array();
			while (list($key,$user) = each($group_user))
			{
				$account_user[$user] = ' selected';
			}
			@reset($account_user);
			return $account_user;
		}
		
		/**
		 * Get the user ID of the managers of the addressbook
		 * 
		 * @return array addressmaster ids
		 */
		function get_addressmaster_ids()
		{
			return $GLOBALS['phpgw']->acl->get_ids_for_location('addressmaster',7,'addressbook');
		}
		

		function load_group_apps($account_id)
		{
			$account_id = (int) $account_id;
			$account_apps = array();
			if($account_id)
			{
				$apps = CreateObject('phpgwapi.applications', $account_id);
				$group_apps = $apps->read_account_specific();

				foreach ( $group_apps as $app )
				{
					$account_apps[$app['name']] = True;
				}
			}
			return $account_apps;
		}

		/**
		 * Get the group manager/s for a group
		 * 
		 * @param int $account_id the group for which managers are sought
		 * @return array the manager/s
		 */
		function load_group_managers($account_id)
		{
			$temp_user = $GLOBALS['phpgw']->acl->get_ids_for_location($account_id,PHPGW_ACL_GROUP_MANAGERS,'phpgw_group');
			if(!$temp_user)
			{
				return Array();
			}
			else
			{
				$group_user = $temp_user;
			}
			$account_user = Array();
			while (list($key,$user) = each($group_user))
			{
				$account_user[$user] = ' selected';
			}
			@reset($account_user);
			return $account_user;
		}

		function rpc_add_user($data)
		{
			exit;

			if (!$errors = $this->validate_user($data))
			{
				$result = $this->so->add_user($data);
			}
			else
			{
				$result = $errors;
			}
			return $result;
		}
		
		function set_module_permissions($id, $modules)
		{
			$id = (int) $id;

			if($id && is_array($modules) )
			{
				$apps = CreateObject('phpgwapi.applications', $id);
				$apps->data = array(); //remove all existing rights
				foreach ( $modules as $app_name => $app_status ) 
				{
					if ( $app_status )
					{
						$apps->add($app_name);
					}
				}
				$apps->save_repository();
			}
		}
		
		function set_groups2account($id, $groups)
		{
			$account = CreateObject('phpgwapi.accounts', $id, 'u');
			$allGroups = $account->get_list('groups');
			if ( is_array($groups) )
			{
				foreach ( $groups as $group )
				{
					$newGroups[$group] = $group;
				}
			}
			else
			{
				$groups = array();
			}

			$acl = CreateObject('phpgwapi.acl',$id);
			while (list($key,$groupData) = each($allGroups)) 
			{
				if (in_array($groupData['account_id'], $groups)) 
				{
					$acl->add_repository('phpgw_group',$groupData['account_id'], $id, 1);
				}
				else
				{
					$acl->delete_repository('phpgw_group',$groupData['account_id'],$id);
				}
			}
		}
	
		function refresh_session_data($id)
		{
			// If the user is logged in, it will force a refresh of the session_info
			
			// This can't work - just imaging session data in php4
			// $GLOBALS['phpgw']->db->query("update phpgw_sessions set session_action='' "
			// ."where session_lid='" . $GLOBALS['phpgw']->accounts->id2name($id)
			// . '@' . $GLOBALS['phpgw_info']['user']['domain'] . "'",__LINE__,__FILE__);
			
			$GLOBALS['phpgw']->session->delete_cache($id);
		}
	}
?>
