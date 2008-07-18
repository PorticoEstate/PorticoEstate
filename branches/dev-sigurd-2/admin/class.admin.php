<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* This file written by Dan Kuykendall <dan@kuykendall.org>                 *
	* Copyright (C) 2003 Dan Kuykendall                                        *
	* -------------------------------------------------------------------------*
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
	\**************************************************************************/

	/* $Id$ */

	class admin_admin
	{
		function adduser ()
		{
			$args = new safe_args();
			$args->set('username');
			$args->set('passwd', '');
			$args->set('fname', '');
			$args->set('lname', '');
			$args->set('isadmin', False, 'bool');
			$args = $args->get(func_get_args(),__LINE__,__FILE__);

			if(isset($args['username']))
			{
				$account_info['account_lid'] = $args['username'];
				$account_info['account_passwd'] = $args['passwd'];
				$account_info['account_firstname'] = $args['fname'];
				$account_info['account_lastname'] = $args['lname'];
				$account_info['account_type'] = 'u';
				$account_info['account_status'] = 'A';
				$account_info['account_expires'] = mktime (0,0,0,12,31,2005);
				$newid = $GLOBALS['phpgw']->accounts->create($account_info);
				if($newid === False)
				{
					$result['text'] = '';
					return $result;
				}
				$result['text'] = 'Created '.$args['username'].' which has id of '.$newid;

				$acl = createobject('api_acl', array('account_id'=>$newid));
				if($args['username'])
				{
					$acl->add('.',63);
				}
				else
				{
					$acl->add('api.base',1);
					$acl->add('skel.base',1);
					$acl->add('wcm.base',1);
					$acl->add('admin.base',1);
				}
			}
			else
			{
				$result['text'] = 'Enter details';
			}
			$GLOBALS['phpgw']->add_xsl('admin.adduser');
			return $result;
		}	
	}
