<?php
  /*************************************************************************\
  * phpGroupWare Setup - Inventory                                          *
  * http://www.phpgroupware.org                                             *
  * --------------------------------------------                            *
  * This program is free software; you can redistribute it and/or modify it *
  * under the terms of the GNU General Public License as published by the   *
  * Free Software Foundation; either version 2 of the License, or (at your  *
  * option) any later version.                                              *
  \*************************************************************************/
  /* $Id$ */

	$setup_info['inv']['name']      = 'inv';
	$setup_info['inv']['version']   = '0.8.5.001';
	$setup_info['inv']['app_order'] = 15;
	$setup_info['inv']['enable']    = 1;
	$setup_info['inv']['app_group']	= 'other';

	$setup_info['inv']['author'][]  = array
	(
		'name'	=> 'Joseph Engo',
		'email'	=> 'jengo@phpgroupware.org'
	);

	$setup_info['inv']['author'][]	= array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	$setup_info['inv']['maintainer'] = array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	$setup_info['inv']['license']  = 'GPL';
	$setup_info['inv']['description'] = 'inventory application';

	$setup_info['inv']['tables'] = array
	(
		'phpgw_inv_products',
		'phpgw_inv_statuslist',
		'phpgw_inv_orders',
		'phpgw_inv_orderpos',
		'phpgw_inv_delivery',
		'phpgw_inv_deliverypos',
		'phpgw_inv_invoice',
		'phpgw_inv_invoicepos',
		'phpgw_inv_stockrooms'
	);

/* The hooks this app includes, needed for hooks registration */

	$setup_info['inv']['hooks'] = array
	(
		'preferences',
		'manual',
		'add_def_pref'
	);

/* Dependencies for this app to work */

	$setup_info['inv']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['inv']['depends'][] = array(
		 'appname' => 'preferences',
		 'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['inv']['depends'][] = array(
		 'appname' => 'addressbook',
		 'versions' => Array('0.9.17', '0.9.18')
	);
?>
