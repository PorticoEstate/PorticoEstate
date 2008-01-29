<?php
  /**************************************************************************\
  * phpgwtimetrack - phpGroupWare addon application                          *
  * http://phpgwtimetrack.sourceforge.net                                    *
  * Written by Robert Schader <bobs@product-des.com>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: customers.php 9782 2002-03-18 03:18:05Z rschader $ */

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  $t = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('timetrack'));
  
  $t->set_file("body", "customers.tpl" );
  
  $t->set_block("body", "header", "header");
  $t->set_block("body", "row", "rows");
  $t->set_block("body", "footer", "footer");

  $t->set_var("rows", ""); // Default rows value in case there are no customers.
  
  if (! $start)
     $start = 0;

  if ($order)
      $ordermethod = "order by $order $sort";
   else
      $ordermethod = "order by company_name,industry_type,status asc";

  if (! $sort)
     $sort = "desc";

  if ($query) {
     $querymethod = " where company_name like '%$query%' OR industry_type like '%$query%' OR status "
		        . "like '%$query%' ";
  }

  $GLOBALS['phpgw']->db->query("select count(*) from phpgw_ttrack_customers $querymethod");
  $GLOBALS['phpgw']->db->next_record();

  $total = $GLOBALS['phpgw']->db->f(0);
  //$limit = $GLOBALS['phpgw']->db->limit($start);

  $t->set_var("bg_color",$GLOBALS['phpgw_info']["theme"]["bg_color"]);
  $t->set_var("th_bg",$GLOBALS['phpgw_info']["theme"]["th_bg"]);

  $t->set_var("left_next_matchs",$GLOBALS['phpgw']->nextmatchs->left("/timetrack/customers.php",$start,$total));
  $t->set_var("lang_customer_list",lang("customer list"));
  $t->set_var("right_next_matchs",$GLOBALS['phpgw']->nextmatchs->right("/timetrack/customers.php",$start,$total));

  $t->set_var("lang_company_name",$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"company_name",$order,"/timetrack/customers.php",lang("company name")));
  $t->set_var("lang_industry_type",$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"industry_type",$order,"/timetrack/customers.php",lang("industry type")));
  $t->set_var("lang_status",$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"status",$order,"/timetrack/customers.php",lang("status")));

  $t->set_var("lang_edit",lang("Edit"));
  $t->set_var("lang_delete",lang("Delete"));
  $t->set_var("lang_view",lang("View"));

  $t->parse("header","header");

  $GLOBALS['phpgw']->db->limit_query("select company_id,company_name,industry_type,status "
	. "from phpgw_ttrack_customers $querymethod "
	. "$ordermethod",$start,__LINE__,__FILE__);
				 
				 
  while ($GLOBALS['phpgw']->db->next_record()) {
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
    $t->set_var("tr_color",$tr_color);

    $company_name  = $GLOBALS['phpgw']->db->f("company_name");
    $industry_type = $GLOBALS['phpgw']->db->f("industry_type");
    $status = $GLOBALS['phpgw']->db->f("status");

    if (! $company_name)  $company_name  = '&nbsp;';
    if (! $industry_type) $industry_type = '&nbsp;';
    if (! $status) $status = '&nbsp;';

    $t->set_var("row_company_name",$company_name);
    $t->set_var("row_industry_type",$industry_type);
    $t->set_var("row_status",$status);
    $t->set_var("row_edit",'<a href="'.$GLOBALS['phpgw']->link("/timetrack/editcustomer.php","cid="
				  . $GLOBALS['phpgw']->db->f("company_id")) . '"> ' . lang("Edit") . ' </a>');

    if($GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] || $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"])
    {
      $t->set_var("row_delete",'<a href="' . $GLOBALS['phpgw']->link("/timetrack/deletecustomer.php",'cid='
			 	  . $GLOBALS['phpgw']->db->f("company_id")) . '"> '.lang("Delete").' </a>');
    } else {
      $t->set_var("row_delete",lang("N/A"));
    }

    $t->set_var("row_view",'<a href="' . $GLOBALS['phpgw']->link("/timetrack/viewcustomer.php", "cid="
				 . $GLOBALS['phpgw']->db->f("company_id")) . '"> ' . lang("View") . ' </a>');

	// Build the rows variable
	$t->parse("rows","row",True);

  }

  $t->set_var("actionurl",$GLOBALS['phpgw']->link("/timetrack/newcustomer.php"));
  $t->set_var("lang_add",lang("add"));
  $t->set_var("lang_search",lang("search"));
  $t->set_var("queryurl",$GLOBALS['phpgw']->link("/timetrack/customers.php"));

  $t->parse("footer","footer");

  // Display completed body.  
  $t->pparse("res", "body");

  $GLOBALS['phpgw']->common->phpgw_footer();
