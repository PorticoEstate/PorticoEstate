<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	*            Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: functions.inc.php 5985 2001-06-17 02:15:04Z bettina $ */
  
// This is loaded by default

	function bla()
	{
		global $status_list, $phpgw;

		$phpgw->db->query("select * from phpgw_inv_statuslist");
		while ($phpgw->db->next_record()) 
		{
			$status_list[$phpgw->db->f('status_id')] = lang($phpgw->db->f('status_name'));
		}
	}

	function select_dist_list($dist) 
	{
		global $phpgw, $phpgw_info, $d;

		if (!$account_id) 
		{ 
			$account_id = $phpgw_info['user']['account_id']; 
		}

		$cols = array('org_name' => 'org_name');

		$entries = $d->read($start,$offset,$cols,$query,'tid=n',$sort,$order,$account_id);
		for ($i=0;$i<count($entries);$i++) 
		{
			$html_dist_list .= '<option value="' . $entries[$i]['id'] . '"';
			if ($entries[$i]['id']==$dist)
			{
				$html_dist_list .= ' selected';
			}
			$html_dist_list .= '>'	
							. $entries[$i]['org_name'] . '</option>';
		}
		return $html_dist_list;
	}

// This will return the HTML for <select> boxs for product status

	function select_status_list($selected_item = '') 
	{
		global $status_list;
		$sl = $status_list;		// For some reason, PHP has a problem with each() and global vars

		$html_selected[$selected_item] = " selected";
		while ($status = each($sl)) 
		{
			$html_status_list .= '<option value="' . $status[0] . '"' . $html_selected[$status[0]] . '>'
								. $status[1] . '</option>';
		}
		return $html_status_list;
	}

// You can have product id numbers in hex or decimal                                                                                             

	$productid_type = "hex";

	function add_leading_zero($num) 
	{
		global $productid_type;

		if ($productid_type == "hex") 
		{
			$num = hexdec($num);
			$num++;
			$num = dechex($num);
		}
		else
		{
			$num++; 
		}

		if (strlen($num) == 4)
			$return = $num;
		if (strlen($num) == 3)
			$return = "0$num";
		if (strlen($num) == 2)
			$return = "00$num";
		if (strlen($num) == 1)
			$return = "000$num";
		if (strlen($num) == 0)
			$return = "0001";

		return strtoupper($return);
	}

	function create_productid($cat_id) 
	{
		global $phpgw;

		$phpgw->db->query("select cat_data from phpgw_categories where cat_id='$cat_id'");
		$phpgw->db->next_record();

		$data = unserialize($phpgw->db->f('cat_data'));                                                                                                                            
		$number = $data['number'];

		$phpgw->db->query("select max(id) from phpgw_inv_products where id like ('$number%')");                                                                                           
		$phpgw->db->next_record();
		$sub = strlen($number);
		$max = add_leading_zero(substr($phpgw->db->f(0),$sub));
		return $number . $max;
	}

// selects the tax from products category    

	function select_tax($cat_id) 
	{
		global $phpgw; 

		$phpgw->db->query("select cat_data from phpgw_categories where cat_id='$cat_id'");
		$phpgw->db->next_record();
		$data = unserialize($phpgw->db->f('cat_data'));
		$tax = $data['tax'];
		$taxpercent = ($tax/100);

		return $taxpercent;                                                                                                                                                               
	}

	$year = $phpgw->common->show_date(time(),'Y');

	function create_orderid($year) 
	{
		global $phpgw,$year;

		$prefix = 'O-' . $year . '-';

		$phpgw->db->query("select max(num) from phpgw_inv_orders where num like ('$prefix%')");
		$phpgw->db->next_record();
		$max = add_leading_zero(substr($phpgw->db->f(0),7));
		return $prefix . $max;
	}

	function create_invoiceid($year)  
	{
		global $phpgw,$year;

		$prefix = 'I-' . $year . '-';

		$phpgw->db->query("select max(num) from phpgw_inv_invoice where num like ('$prefix%')");
		$phpgw->db->next_record();
		$max = add_leading_zero(substr($phpgw->db->f(0),7));
		return $prefix . $max;
	}

	function create_deliveryid($year)  
	{
		global $phpgw,$year;

		$prefix = 'D-' . $year . '-';

		$phpgw->db->query("select max(num) from phpgw_inv_delivery where num like ('$prefix%')");
		$phpgw->db->next_record();
		$max = add_leading_zero(substr($phpgw->db->f(0),7));
		return $prefix . $max;
	}

	bla();
?>
