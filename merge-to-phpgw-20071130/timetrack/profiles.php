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

  /* $Id: profiles.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/18/2001 (api calls for accounts and contacts)

  // This page currently lists all users in the db, should either list them
  // directly from the profiles table -or- if the profile doen't exist, change
  // Edit button to "Add" for that entry. Of course, the best method would be
  // to have a hook put into groupware so that profiles are always created
  // when an account is created, but I also could have this page scan for
  // profiles to create, which it could just create blank entries for (just login name). 

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  // Borrowed this function from the admin/accounts.php page
  function account_total($query)
  {
	global $phpgw;
	if ($query) {
		$querymethod = " AND (account_firstname LIKE '%$query%' OR account_lastname LIKE "
			. "'%$query%' OR account_lid LIKE '%$query%') ";
	}
	$GLOBALS['phpgw']->db->query("SELECT COUNT(*) FROM phpgw_accounts "
		. "WHERE account_type='u'".$querymethod,__LINE__,__FILE__);
	$GLOBALS['phpgw']->db->next_record();
	return $GLOBALS['phpgw']->db->f(0);
  }

  $t = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('timetrack'));

  $t->set_file("body", "profiles.tpl" );
  
  $t->set_block("body", "header", "header");
  $t->set_block("body", "row", "rows");
  $t->set_block("body", "footer", "footer");

  $t->set_var("rows", ""); // Default rows value in case there are no "accounts".
  
  if (! $start)
     $start = 0;

  if ($order)
      $ordermethod = "order by $order $sort";
   else
      $ordermethod = "order by account_firstname,account_lastname asc";

  if (! $sort)
     $sort = "desc";

  // Need to get the number of all "accounts" first.
  $total = account_total($query);
  //$limit = $GLOBALS['phpgw']->db->limit($start);

  $t->set_var("bg_color",$GLOBALS['phpgw_info']["theme"]["bg_color"]);
  $t->set_var("th_bg",$GLOBALS['phpgw_info']["theme"]["th_bg"]);

  $t->set_var("left_next_matchs",
	$GLOBALS['phpgw']->nextmatchs->left("/timetrack/profiles.php",$start,$total));
  $t->set_var("lang_profile_list",lang("Employee Profiles"));
  $t->set_var("right_next_matchs",
	$GLOBALS['phpgw']->nextmatchs->right("/timetrack/profiles.php",$start,$total));

  $t->set_var("lang_loginid",
	$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,
		"account_lid",$order,"/timetrack/profiles.php",lang("loginid")));
  $t->set_var("lang_firstname",
	$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,
		"account_firstname",$order,"/timetrack/profiles.php",lang("first name")));
  $t->set_var("lang_lastname",
	$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,
		"account_lastname",$order,"/timetrack/profiles.php",lang("last name")));

  $t->set_var("lang_edit",lang("Edit"));
  $t->set_var("lang_view",lang("View"));
  $t->set_var("lang_status",lang("Status"));

  $t->parse("header","header");


  // Need to add extra args to this yet:
  $names = $GLOBALS['phpgw']->accounts->get_list('accounts',$start,$sort, $order, $query, $total);
  for ($i=0; $i<count($names); $i++) {
	$uid = $names[$i]['account_id'];
	$uname = $names[$i]['account_lid'];
	$firstname = $names[$i]['account_firstname'];
	$lastname = $names[$i]['account_lastname'];

	$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
	$t->set_var("tr_color",$tr_color);

	if (! $lastname)  $lastname  = '&nbsp;';
	if (! $firstname) $firstname = '&nbsp;';

	// Check if a profile exists or not:
	$GLOBALS['phpgw']->db->query("SELECT * from phpgw_ttrack_emplyprof WHERE id=$uid");
	$proftest = $GLOBALS['phpgw']->db->num_rows();
	switch($proftest) {
		case 0:	// No profile exists, create a default one
		  $GLOBALS['phpgw']->db->query("INSERT into phpgw_ttrack_emplyprof "
			. "(id,lid,location_id) VALUES ($uid,'$uname',1)");

    		  $t->set_var("row_loginid","<font color=\"FF0000\">$uname</font>");
  		  $t->set_var("row_firstname","<font color=\"FF0000\">$firstname</font>");
  		  $t->set_var("row_lastname","<font color=\"FF0000\">$lastname</font>");
  		  $t->set_var("row_edit",'<a href="' 
		   . $GLOBALS['phpgw']->link("/timetrack/editprofile.php","id=$uid")
				  . '">' . lang("Edit") . '</a>');
  		  $t->set_var("row_view",'<a href="' 
		   . $GLOBALS['phpgw']->link("/timetrack/viewprofile.php","id=$uid")
			 	  . '">'.lang("View") . '</a>');
  		  $t->set_var("row_status","<font color=\"FF0000\">New</font>");
		  // Build the rows variable
		  $t->parse("rows","row",True);

		  /*echo "<tr bgcolor=$tr_color>"
			. "<td width=20%><font color=\"FF0000\">$uname</font></td>"
			. "<td><font color=\"FF0000\">$firstname</font></td>"
			. "<td><font color=\"FF0000\">$lastname</font></td>"
		  	. "<td width=7%><a href=\""
		  	. $GLOBALS['phpgw']->link("/timetrack/editprofile.php","id=$uid")
		  	. "\"> " . lang("Edit") . " </a></td>";
		  echo  "<td width=7%><a href=\""
		  	. $GLOBALS['phpgw']->link("/timetrack/viewprofile.php","id=$uid")
		  	. "\"> " . lang("View") . " </a> </td>";
		  echo "<td width=10%><center><font color=\"ff0000\">New</font></center></td></tr>";*/

		  $profiles_created = True;
		  break;
		case 1:	// Profile exists, just do regular editing
    		  $t->set_var("row_loginid",$uname);
  		  $t->set_var("row_firstname",$firstname);
  		  $t->set_var("row_lastname",$lastname);
  		  $t->set_var("row_edit",'<a href="' 
		   . $GLOBALS['phpgw']->link("/timetrack/editprofile.php","id=$uid")
				  . '">' . lang("Edit") . '</a>');
  		  $t->set_var("row_view",'<a href="' 
		   . $GLOBALS['phpgw']->link("/timetrack/viewprofile.php","id=$uid")
			 	  . '">'.lang("View") . '</a>');
  		  $t->set_var("row_status",'&nbsp;');
		  // Build the rows variable
		  $t->parse("rows","row",True);
		  break;
	}

  }

  // There is no longer any need for the add button and posting to newprofile.php now that
  // profiles will be created automatically when this page is run.

  $t->set_var("lang_search",lang("search"));
  $t->set_var("queryurl",$GLOBALS['phpgw']->link("/timetrack/profiles.php"));

  if ($profiles_created == True) {
	$t->set_var("notice_profiles_created",
		"<font color=\"FF0000\">Highlighted Names have had default profiles created<br>"
		. "automatically. Please edit them as soon as possible.<br>");
  } else {
	$t->set_var("notice_profiles_created","");
  }

  $t->parse("footer","footer");

  // Display completed body.  
  $t->pparse("res", "body");

  $GLOBALS['phpgw']->common->phpgw_footer();

?>
