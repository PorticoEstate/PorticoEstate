<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id$ */

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('available')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('no longer available')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('back order')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('unknown')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('other')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('sold')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_inv_statuslist (status_name) VALUES ('archive')");
?>
