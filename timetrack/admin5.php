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

  /* $Id: admin5.php 12053 2003-03-19 05:16:00Z skwashd $ */
  /* This one will most likely be eliminated or changed in order
   * to use acl's instead, which might just be assignable on a user's
   * main account editing page.
   */
 
  
  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

    // --------------------------------------------------------------------------------
    // From original "setup_groups.inc.php"
    // Setup the manage_jobs acl
    // 
    echo "Creating TTrack_Managers group and manage_jobs ACL's...<br>";
	$n_group = "TTrack_Managers";
	$account_expires = -1;
	if ($GLOBALS['phpgw']->accounts->exists($n_group))
	{
		echo 'Warning: TTrack_Managers group already exists<br>';
	} else {
		$group = CreateObject('phpgwapi.accounts',$mgrgrpid);
		$account_info = array(
			'account_type'      => 'g',
			'account_lid'       => $n_group,
			'account_passwd'    => '',
			'account_firstname' => $n_group,
			'account_lastname'  => 'Group',
			'account_status'    => 'A',
			'account_expires'   => $account_expires
		);
		$group->create($account_info);
		$mgrgrpid = $GLOBALS['phpgw']->accounts->name2id($n_group);

		$acl = CreateObject('phpgwapi.acl',$mgrgrpid);
		$acl->read_repository();
		$acl->add("timetrack", "manage_jobs", 1);
		$acl->save_repository();
	}

   	// Setup the payroll acl
   	echo "Creating TTrack_Payroll group and payroll ACL's...<br>";
	$n_group = "TTrack_Payroll";
	if ($GLOBALS['phpgw']->accounts->exists($n_group))
	{
		echo 'Warning: TTrack_Payroll group already exists<br>';
	} else {
		$group = CreateObject('phpgwapi.accounts',$paygrpid);
		$account_info = array(
			'account_type'      => 'g',
			'account_lid'       => $n_group,
			'account_passwd'    => '',
			'account_firstname' => $n_group,
			'account_lastname'  => 'Group',
			'account_status'    => 'A',
			'account_expires'   => $account_expires
		);
		$group->create($account_info);
		$paygrpid = $GLOBALS['phpgw']->accounts->name2id($n_group);

		$acl = CreateObject('phpgwapi.acl',$paygrpid);
		$acl->read_repository();
		$acl->add("timetrack", "manage_payroll", 1);
		$acl->save_repository();
	}

    // --------------------------------------------------------------------------------



    // --------------------------------------------------------------------------------
    // From original "setup_users.inc.php"
    echo "Setting up all users for basic Timetrack access...<br>";

	$accounts = CreateObject('phpgwapi.accounts',$group_id);
	$account_list = $accounts->get_list('accounts');
	$account_num = count($account_list);

	while (list($key,$entry) = each($account_list))
	{
	    $acl = CreateObject('phpgwapi.acl',$entry['account_id']);
          echo 'Processing User ';
          echo $GLOBALS['phpgw']->common->display_fullname(
	    		$entry['account_lid'],
	    		$entry['account_firstname'],
	    		$entry['account_lastname']);
          echo '...<br>';
		$acl->read_repository();
		// check if timetrack "run" acl already exists first for user before adding it
		// The going ticket for this seems to be just to delete the existing acl first:
		$acl->delete("timetrack", "run", 1);
		$acl->add("timetrack", "run", 1);
          echo "Timetrack run acl assigned<br>";
		$acl->save_repository();
          $id = $entry['account_id'];
          $lid = $entry['account_lid'];
          // To save processing time, I decided to move the add profiles code here
          // I suppose eventually this code should be put in class functions.
          // set default location_id to #1 for all profiles
	    // Be sure to check if this entry is already present first.
          $GLOBALS['phpgw']->db->query("SELECT * from phpgw_ttrack_emplyprof WHERE id='$id'");
          if($GLOBALS['phpgw']->db->num_rows() == 0)
	    {
           $sql = "INSERT into phpgw_ttrack_emplyprof " 
             . "(id, lid, location_id) VALUES ($id, '$lid', 1)";
           echo "Employee Profile added.<br>";
           //echo "sql = $sql";
           $GLOBALS['phpgw']->db->query($sql);
	    }
	}

    // --------------------------------------------------------------------------------


    // --------------------------------------------------------------------------------
    // From original contacts_to_customers.inc.php file
    // Since we are going all out on a fresh install, let's go ahead and add customer records for
    // all org_name entries in phpgw_addressbook. Use query option to only pick one of each name
    // Only perform this if # of records for Customers table is zero
    $GLOBALS['phpgw']->db->query("SELECT company_name from phpgw_ttrack_customers");
    if($GLOBALS['phpgw']->db->num_rows() == 0)
    {
     echo 'Adding empty Customer records for all Company Names found in Contacts...<br>';
     $contacts = CreateObject('phpgwapi.contacts');
     $qfields = array(
	 'id' => 'id',
	 'org_name' => 'org_name'
	);
     $start = 0;
     $offset = 0;
     $query = "";
     $filter = 'tid=n';
     $sort = "asc";
     $order = "org_name";
     $entries = $contacts->read($start,$offset,$qfields,$query,$filter,$sort,$order);
     $entry_num = count($entries);
     echo "Number of org_names found is $entry_num<br>";
   
     for ($i=0; $i<$entry_num; $i++)
     {
       $cname = $entries[$i]['org_name'];
       // test before implementing
       echo "Found Company Name $cname <br>";
       // Check to see if $cname is already in the db before trying to add it
       $GLOBALS['phpgw']->db->query("SELECT company_name from phpgw_ttrack_customers "
	. "WHERE company_name='".$GLOBALS['phpgw']->db->db_addslashes($cname)."'",__LINE__,__FILE__);
       if($GLOBALS['phpgw']->db->num_rows() > 0)
       {
           echo "$cname already in Customers table, skipping...<br>";
       } else {
           $GLOBALS['phpgw']->db->query("INSERT into phpgw_ttrack_customers "
               . "(company_name) " .
		"VALUES ('".$GLOBALS['phpgw']->db->db_addslashes($cname)."')",__LINE__,__FILE__);
           echo "$cname added to Customers List<br>";
       }
     }

     // Now finish by setting all customers to "active" status.
     echo "Setting all Customers to active status...<br>";
     $GLOBALS['phpgw']->db->query("UPDATE phpgw_ttrack_customers SET active = 'Y'");
    } // End "if"

    echo "Timetrack application should now be ready to use!<br>"
       . "All that remains now is to add the appropriate users<br>"
       . "to the 'TTrack_Managers' and 'TTrack_Payroll' groups<br>"
	 . "and to customize your site's Locations, Job Status and<br>"
       . "Work Catagory Tables. Notice for PostgreSQL database users:<br>"
       . "You May need to set your database datestyle to ISO by executing<br>"
       . "'SET DATESTYLE = ISO' on your database. Note that this style IS<br>"
       . "supposed to be the default according to the docs. Finally, not all<br>"
       . "current SQL statements have been tested to be compatible with PostgreSQL,<br>"
       . "so please report any problems to the Timetrack maintainer.";


    // --------------------------------------------------------------------------------



  $GLOBALS['phpgw']->common->phpgw_footer();
