<?php
	/**************************************************************************\
	* phpGroupWare - Shopping cart                                             *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	// selects the tax from products category
	function select_tax($cat_id)
	{
		$GLOBALS['phpgw']->db->query("select cat_data from phpgw_categories where cat_id='$cat_id'");
		$GLOBALS['phpgw']->db->next_record();
		$data = unserialize($GLOBALS['phpgw']->db->f('cat_data'));
		$tax = $data['tax'];
		$taxpercent = ($tax/100);

		return $taxpercent;
	}
?>
