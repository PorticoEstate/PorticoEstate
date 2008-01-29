<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: class.boaclmanager.inc.php 17795 2006-12-28 01:35:35Z skwashd $ */

	class boaclmanager
	{
		var $public_functions = array
		(
			'submit' => True
		);

		function boaclmanager()
		{
			//$this->ui = createobject('admin.uiaclmanager');
		}

		function submit()
		{
			if ($GLOBALS['cancel'])
			{
				//$this->ui->list_apps();
				return False;
			}

			$location = base64_decode($GLOBALS['location']);

			$total_rights = 0;
			while (is_array($GLOBALS['acl_rights']) && list(,$rights) = each($GLOBALS['acl_rights']))
			{
				$total_rights += $rights;
			}

			$GLOBALS['phpgw']->acl->add_repository($GLOBALS['acl_app'], $location, $GLOBALS['account_id'], $total_rights);

			//$this->ui->list_apps();
		}

		function get_addressmaster_ids()
		{
			return $GLOBALS['phpgw']->acl->get_ids_for_location('addressmaster',7,'addressbook');
		}

		function list_addressmasters()
		{
			$admins = $this->get_addressmaster_ids();
			//_debug_array($admins);

			$data = array();
			for ( $i = count($admins) - 1; $i >= 0; --$i)
			{
				$acc_name = $GLOBALS['phpgw']->accounts->get_account_data($admins[$i]);

				if ( isset($admins[$i]) )
				{
					$data[] = array
					(
						'account_id'	=> $admins[$i],
						'lid'			=> $acc_name[$admins[$i]]['lid'],
						'firstname'		=> $acc_name[$admins[$i]]['firstname'],
						'lastname'		=> $acc_name[$admins[$i]]['lastname']
					);
				}
			}

 			$this->total = count($data);
			return $data;
		}

		function check_values($users = 0, $groups = 0)
		{
			$errors = array();
			if ( !count($users) && !count($groups) )
			{
				$errors[] = lang('please choose at least one addressmaster');
			}
			if ( count($errors) )
			{
				return $errors;
			}
		}

		function edit_addressmasters($master,$group = 0)
		{
			$GLOBALS['phpgw']->acl->delete_repository('addressbook','addressmaster',False);

			for($i=0;$i<count($master);$i++)
			{
				$GLOBALS['phpgw']->acl->add_repository('addressbook', 'addressmaster',$master[$i],7);
			}

			for($i=0;$i<count($group);$i++)
			{
				$GLOBALS['phpgw']->acl->add_repository('addressbook', 'addressmaster',$group[$i],7);
			}
		}
	}
?>
