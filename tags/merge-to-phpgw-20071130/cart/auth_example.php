<?php
  /**************************************************************************\
  * phpGroupWare - Shopping cart                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Bettina gille [ceb@phpgroupware.org]                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: auth_example.php 9801 2002-03-20 13:03:59Z milosch $ */
  
	$GLOBALS['phpgw_info']['flags'] = array('currentapp' => 'cart');

	include('../header.inc.php');

	$GLOBALS['phpgw_info']['server']['cart_payment_type'] = 'authorizenet';
	$test = CreateObject('cart.payment');
	$test->transact();
	$test->authorize();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
