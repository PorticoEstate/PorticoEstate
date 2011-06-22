<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$account_id = phpgw::get_var('account_id', 'int');
	if ( $account_id )
	{
		// delete all mapping to account
		// Using Single Sign-On
		if(isset($GLOBALS['phpgw_info']['server']['mapping']) && ($GLOBALS['phpgw_info']['server']['mapping'] == 'all' || $GLOBALS['phpgw_info']['server']['mapping'] == 'table'))
		{
			$phpgw_map_location = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? $_SERVER['HTTP_SHIB_ORIGIN_SITE'] : 'local';
			$phpgw_map_authtype = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? 'shibboleth':'remoteuser';
			if(!is_object($GLOBALS['phpgw']->mapping))
			{
				$GLOBALS['phpgw']->mapping = CreateObject('phpgwapi.mapping', array('auth_type'=> $phpgw_map_authtype, 'location' => $phpgw_map_location));
			}
			$account = CreateObject('phpgwapi.accounts', $account_id, 'u');
			$data = $account->read();
			$account_lid = $data['account_lid'];
			$GLOBALS['phpgw']->mapping->delete_mapping(array('account_lid' => $account_lid));
		}
	}
