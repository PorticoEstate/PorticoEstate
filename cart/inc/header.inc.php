<?php
	/**************************************************************************\
	* phpGroupWare - Shopping cart                                             *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *  
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$GLOBALS['phpgw']->template->set_file(array('cart_header' => 'header.tpl'));
	$GLOBALS['phpgw']->template->set_var('lang_cart',lang('Shopping cart'));
	$GLOBALS['phpgw']->template->set_var('bg_color',$phpgw_info['theme']['th_bg']);

	$GLOBALS['phpgw']->template->pparse('out','cart_header');
?>
