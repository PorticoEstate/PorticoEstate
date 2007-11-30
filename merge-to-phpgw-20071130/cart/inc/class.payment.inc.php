<?php
  /**************************************************************************\
  * phpGroupWare - phpgw echo test                                           *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: class.payment.inc.php 17119 2006-09-09 14:39:14Z skwashd $ */

	$payment = '';
	if ( !isset($GLOBALS['phpgw_info']['server']['cart_payment_type'])
		|| empty($GLOBALS['phpgw_info']['server']['cart_payment_type']) )
	{
		$payment = 'echo';
	}
	else
	{
		switch ( $GLOBALS['phpgw_info']['server']['cart_payment_type'] )
		{
			case 'authorizenet':
				$payment = 'authoriznet';
				break;
			case 'pfp':
				$payment = 'pfp';
				break;
			case 'echo':
			default:
				$payment = 'echo';
		}
	}
	include(PHPGW_APP_INC . "/class.payment_{$payment}.inc.php");
	include(PHPGW_APP_INC . '/class.payment_shared.inc.php');
?>
