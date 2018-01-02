<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	*  This file written by Joseph Engo <jengo@phpgroupware.org>               *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class bocurrentsessions
	{
		var $ui;
		var $so;
		var $public_functions = array
		(
			'kill' => true
		);

		function total()
		{
			return $GLOBALS['phpgw']->session->total();
		}

		function list_sessions($start, $order, $sort)
		{

			$view_ip = false;
			if ( !$GLOBALS['phpgw']->acl->check('current_sessions_access', phpgwapi_acl::EDIT, 'admin') )
			{
				$view_ip = true;
			}

			$view_action = false;
			if ( !$GLOBALS['phpgw']->acl->check('current_sessions_access', phpgwapi_acl::ADD, 'admin') )
			{
				$view_action = true;
			}

			$values = $GLOBALS['phpgw']->session->list_sessions($start, $sort, $order);
			foreach ( $values as &$value )
			{
				if ( preg_match('/^(.*)#(.*)$/', $value['lid'], $m) )
				{
					$value['lid'] = $m[1];
				}

				if ( !$view_action )
				{
					$value['action'] = ' -- ';
				}

				if ( !$view_ip )
				{
					$value['ip'] = ' -- ';
				}
				
				$value['idle'] = gmdate('G:i:s', time() - $value['dla']);
				$value['logintime'] = $GLOBALS['phpgw']->common->show_date($value['logints']);
			}
			
			return $values;
		}

		function kill()
		{
			if ((isset($_GET['ksession']) && $_GET['ksession']) &&
				($GLOBALS['sessionid'] != $_GET['ksession']) &&
				! $GLOBALS['phpgw']->acl->check('current_sessions_access',8,'admin'))
			{
				$GLOBALS['phpgw']->session->destroy($_GET['ksession']);
			}
			$this->ui = createobject('admin.uicurrentsessions');
			$this->ui->list_sessions();
		}
	}
