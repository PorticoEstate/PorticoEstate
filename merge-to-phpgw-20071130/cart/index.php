<?php
  /**************************************************************************\
  * phpGroupWare - Shopping cart                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Bettina Gille [ceb@phpgroupware.org]                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: index.php 9733 2002-03-15 17:21:31Z ceb $ */
  
	$GLOBALS['phpgw_info']['flags'] = array('currentapp' => 'cart');

	include('../header.inc.php');

	echo '<p><center><img src="' . $GLOBALS['phpgw']->common->image($GLOBALS['phpgw_info']['flags']['currentapp'],'logo') . '">';
	echo '</center>';

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
