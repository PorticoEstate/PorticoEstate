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

  /* $Id: default_records.inc.php,v 1.3 2002/04/08 13:43:20 milosch Exp $ */

	//echo "<center>Adding default records for Job Status table...<br>";

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_job_status (status_id, status_name) "
		. "VALUES (1,'None')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_job_status (status_id, status_name) "
		. "VALUES (2,'Quoted')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_job_status (status_id, status_name) "
		. "VALUES (3,'In Process')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_job_status (status_id, status_name) "
		. "VALUES (4,'Complete')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_job_status (status_id, status_name) "
		. "VALUES (5,'Paid in Full')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_job_status (status_id, status_name) "
		. "VALUES (6,'Not Awarded')");

	//echo "Adding default records for Locations table...<br>";

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_locations (location_id, location_name) "
		. "VALUES (1,'Main Site')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_locations (location_id, location_name) "
		. "VALUES (2,'Remote Site')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_locations (location_id, location_name) "
		. "VALUES (3,'Customer Site')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_locations (location_id, location_name) "
		. "VALUES (4,'Home Office')");

	//echo "Adding default records for Work Catagories table...<br>";

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (1,'3D Modeling')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (2,'2D Detailing')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (3,'Project Management')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (4,'Data Management')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (5,'External System Management')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (6,'CAD Manufacturing')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (7,'Checking')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (8,'Engineering Analysis')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (9,'Consulting')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (10,'Misc. Internal Work')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (11,'Internal Development')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (12,'Internal Systems Management')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (13,'Vacation')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_ttrack_wk_cat (work_catagory_id, catagory_desc) "
		. "VALUES (14,'Sick Leave')");

	/*
	echo "<br><br><b>To complete the installation of the Timetrack application,<br>"
		. "you must login to phpgroupware as an admin user, grant yourself<br>"
		. "access to the Timetrack app under User Admin, and then execute the<br>"
		. "'Site Setup' link under the Timetrack Admin Hooks section.<br>"
		. "Further instructions will be provided there.</b></center>";
	*/
?>
