<?php
  $phpgw_info["flags"] = array("currentapp" => "rbs", "noheader" => True, "nonavbar" => True);
  include("../header.inc.php");
# bad hack to get around bad nested includes
# It needs to come out ASAP when ACL stuff gets working
$auth[type] = "ip";

# $phpgw_info["flags"]["currentapp"] = "rbs";
# include "config.inc";
# include "functions.inc";
# include "connect.inc";
include "mrbs_auth.inc";
include "mrbs_sql.inc";

if(getAuthorised(getUserName(), getUserPassword()) && ($info = mrbsGetEntryInfo($id)))
{
	$day   = strftime("%d", $info[start_time]);
	$month = strftime("%m", $info[start_time]);
	$year  = strftime("%Y", $info[start_time]);
	$area  = mrbsGetRoomArea($info[room_id]);
	
	if(mrbsDelEntry(getUserName(), $id, $series, 1))
	{
		$phpgw->redirect($phpgw->link('/rbs/day.php','day='.$day.'&month='.$month.'&year='.$year.'&area='.$area));
		exit;
	}
}

// If you got this far then we got an access denied.
	$phpgw->redirect($phpgw->link('/rbs/failed.php'));
?>

