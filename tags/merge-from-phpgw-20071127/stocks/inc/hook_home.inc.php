<?php
	/**************************************************************************\
	* phpGroupWare - Stock Quotes                                              *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: hook_home.inc.php 17040 2006-08-30 17:02:38Z skwashd $ */

	if ( isset($GLOBALS['phpgw_info']['user']['apps']['stocks'])
		&& $GLOBALS['phpgw_info']['user']['apps']['stocks'] 
		&& isset($GLOBALS['phpgw_info']['user']['preferences']['stocks']['mainscreen'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['stocks']['mainscreen'] == 'enabled')
	{
		$app_id	= $GLOBALS['phpgw']->applications->name2id('stocks');
		$GLOBALS['portal_order'][] = $app_id;

		$GLOBALS['phpgw']->portalbox->set_params(array('app_name'	=> 'stocks',
														'app_id'	=> $app_id,
														'title'		=> lang('Stocks')));
		$stocks = CreateObject('stocks.uistock');
		$GLOBALS['phpgw']->portalbox->xdraw($stocks->return_quotes());
	}
?>
