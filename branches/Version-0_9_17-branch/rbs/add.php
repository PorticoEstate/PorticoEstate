<?php
  $phpgw_info["flags"] = array("currentapp" => "rbs", "noheader" => True, "nonavbar" => True);
  include("../header.inc.php");

# $phpgw_info["flags"]["currentapp"] = "rbs";
# include "config.inc";
# include "functions.inc";
# include "connect.inc";

# This file is for adding new areas/rooms

# we need to do different things depending on if its a room
# or an area

if ($type == "area") {
	$area_name_q = addslashes($name);
	$sql = "insert into mrbs_area (area_name) values ('$area_name_q')";
	mysql_query($sql);
	echo mysql_error();
}

if ($type == "room") {
	$room_name_q = addslashes($name);
	$description_q = addslashes($description);
	$sql = "insert into mrbs_room (room_name, area_id, description, capacity)
	        values
			  ('$room_name_q',$area, '$description_q',$capacity)";
	mysql_query($sql);
	echo mysql_error();
}
	
# header is kicking my butt right now, so I'm settling for a link to click on
# if anyone can make the header stuff work, pass me a clue!
header("Location: admin.php?area=$area");
# echo "<a href=".$phpgw->link($HTTP_REFERER).">$lang[returnprev]</a>"; 
#   $phpgw->common->phpgw_footer();
?>
