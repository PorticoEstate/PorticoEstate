<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$c = createobject('phpgwapi.config','registration');
	$c->read();

	if($c->config_data['activate_account'] == 'pending_approval')
	{
		$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_dla <= '"
		. (time() - 7200) . "' AND reg_info IS NULL",__LINE__,__FILE__);	
	}
	else
	{
		$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_dla <= '"
		. (time() - 7200) . "'",__LINE__,__FILE__);
	}
