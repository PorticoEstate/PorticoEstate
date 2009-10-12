<?php
#    Resource Booking System (rbs)
#    Originally released as "Meeting Room Booking System", by Sam Mason <smason@mtc.ricardo.com>
#    Ported (okay, portING, not portED) to a phpGroupWare module by:
#      Stephan Beal <stephan@wanderinghorse.net> 
#      (feel free to make a change and add your name here :)
#
#    Disclaimer from Sam's orginal code:
#    You are free to use and distribute this code provided this tiny
#    header is included. No warranties, fitness for purpose, etc. etc.

$phpgw_info["flags"]["currentapp"] = "rbs";

# bbaker - Goal is at some point to get rid of config.inc 
# and use phpGW auth and put other config items in database
include "config.inc"; # this'll include the global phpgw stuff
include "functions.inc";
//include "connect.inc";
include "mincals.inc";

#  include("../inc/config.inc.php");
#  include("../header.inc.php");
#  include_lang("rbs");

load_user_preferences ();

?>

<!-- 
 <HTML>
 <HEAD> -->
 <?php # include "style.inc"?>
  <!-- 
  </HEAD> 
 -->
<BODY>

<STYLE TYPE="text/css">
<!-- 
TH {color:#eeeeee; font-size:10pt; font-family:verdana; background-color:#333333;
border-width:1; border-color:black; vertical-align:top;}
TD.blue {background-color:#F0F0FF;}
TD.red  {background-color:#F0F0F0;}
TD.green {background-color:#DDFFDD;}
TD.I {background-color:#DDFFDD;}
TD.E {background-color:#C0E0FF;}
TD.white {background-color:#FFFFFF;}
 -->
</STYLE>

<?php

#If we dont know the right date then make it up 
if (!$day or !$month or !$year) {
	$day   = date("d");
	$month = date("m");
	$year  = date("Y");
}

# Define the start of day and end of day (default is 7-7)
$am7=mktime($morningstarts,00,0,$month,$day,$year);
$pm7=mktime($eveningends,0,0,$month,$day,$year);

#Let the user know what date they chose
echo "<table><tr><td width=\"100%\">";

echo '<form action="' . $phpgw->link("/rbs/day.php") . '" method="POST">';
	echo "<H2>$lang[bookingsfor]: <FONT SIZE=NORMAL>";
	genDateSelector("", $day, $month, $year);
	echo "<INPUT TYPE=hidden NAME=area VALUE=$area>";
	echo "<INPUT TYPE=submit VALUE=\"" . $lang[goto] . "\">";
echo "</FONT></H2></form><P>";

#Find out which rooms a user wants to see
#If we havent had $area passed in then for now default to area "1" - in the
#  future this will be customisable per user

if (!$area) { $area = 1; };

#Show all avaliable areas
echo "<B><U>$lang[areas]:</B></U><br>";
$sql = "select id, area_name from mrbs_area";
$res = mysql_query($sql);
while ($row = mysql_fetch_row($res)) {
	echo '<a href="' . $phpgw->link("/rbs/day.php","year=$year&month=$month&day=$day&area=$row[0]") . '">';
	if ($row[0] == $area) { echo "<font color=red>";}
	echo "$row[1]</a><br>";
}

echo "</td><td><center>";


echo "</td>";
#Draw the three month calendars
minicals($year, $month, $day, $area);
echo "</center></tr></table>";

#y? are year, month and day of yesterday
#t? are year, month and day of tomorrow

$i= mktime(0,0,0,$month,$day-1,$year);
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);

$i= mktime(0,0,0,$month,$day+1,$year);
$ty = date("Y",$i);
$tm = date("m",$i);
$td = date("d",$i);

#Show colour key
echo "<table border=0><tr><td class=\"I\">$lang[internal]</td><td class=\"E\">$lang[external]</td></tr></table>";

#Show Go to day before and after links
echo '<table width=100%><tr><td><a href="' . $phpgw->link("/rbs/day.php","year=$yy&month=$ym&day=$yd&area=$area") . '">&lt;&lt; ' . $lang[daybefore] . '</a></td>
      <td align=center><a href="' . $phpgw->link("/rbs/day.php","area=$area") . '">' . $lang[gototoday] . '</a></td>
      <td align=right><a href="' .  $phpgw->link("/rbs/day.php","year=$ty&month=$tm&day=$td&area=$area") . '">' . $lang[dayafter] . ' &gt;&gt;</a></td></tr></table>';


#We want to build an array containing all the data we want to show
#and then spit it out. 

#In PHP we dont need to define an array before using it

#Get all appointments for today in the area that we care about
$sql = "select create_by, mrbs_room.id, start_time, end_time, type, name, mrbs_entry.description, mrbs_entry.id, mrbs_entry.type

from mrbs_entry left join mrbs_room on mrbs_entry.room_id = mrbs_room.id

where area_id = $area 
      and (start_time between $am7 and $pm7
		or end_time between $am7 and $pm7
      or $am7 between start_time and end_time)
";

$res = mysql_query($sql);
echo mysql_error();

while ($row = mysql_fetch_row($res)) {
	#Each row weve got here is an appointment. add the details to the array
	#Loop from the start of the booking to the end, adding to the array
	#Row[1] = Room
	#row[2] = start time
	#row[3] = end time
	#row[5] = short description
	#row[7] = id of this booking
	#row[8] = type (internal/external)
	
	# $today is a map of the screen that will be displayed
	# It looks like:
	#     $today[Room Name][Time][id]
	#                            [color]
	#                            [description]
	
	for($t = $row[2]; ($t < $row[3]) || ($t == $row[2]); $t = $t + $resolution)
	{
		$today[$row[1]][$t][id]     = $row[7];
		$today[$row[1]][$t][color]  = $row[8];
	}
	
	# show the name of the booker in the first segment that the
	# booking happens in, or at the start of the day if it started
	# before today
	$today[$row[1]][$row[2]][data] = $row[5];
	
	if($row[2] < $am7)
		$today[$row[1]][$am7][data] = $row[5];
}

#We need to know what all the rooms area called, so we can show them all
#pull the data from the db and store it. Convienently we can print the room 
#headings and capacities at the same time

$sql = "select room_name, capacity, id from mrbs_room where area_id=$area";
$res = mysql_query($sql);

# It might be that there are no rooms defined for this area.
# If there are none then show an error and dont bother doing anything
# else

if (mysql_num_rows($res) == 0) {
	echo "<h1>No rooms defined for this area</h1>";
} else {
	#This is where we start displaying stuff
	echo "<table cellspacing=0 border=1 width=100%>";
	echo "<tr><th>$lang[time]</th>";

	while ($row = mysql_fetch_row($res)) {
		echo "<th align=top>$row[0] ($row[1])</th>";
		$rooms[] = $row[2];
	}
	echo "</tr>\n";
	
	$REQUEST_URI = basename($REQUEST_URI);
	
	#This is the main bit of the display
	#We loop through unixtime and then the rooms we just got

	for ($t=$am7; $t<=$pm7; $t=$t+$resolution) {
		$nw = date("Y-m-d H:i",$t+$resolution);
		echo "<tr>";

		#Show the time linked to the URL for highlighting that time
		echo '<td width="1%" class="red"><a href="' . $phpgw->link($REQUEST_URI,"timetohighlight=$t") . '">' . date("H:i",$t) . '</a></td>';

		#Loop through the list of rooms we have for this area
		while (list($key, $room) = each($rooms)) {
			$id    = $today[$room][$t][id];
			$descr = htmlspecialchars($today[$room][$t][data]);
			$color = $today[$room][$t][color];
			
			# $c is the colour of the cell that the browser sees. White normally, 
			# red if were hightlighting that line and a nice attractive green if the room is booked.
			# We tell if its booked by $id having something in it
			if ($id) {
				$c = $color;
			} else {
				if ($t == $timetohighlight) {
					$c = "red";
				} else {
					$c = "white";
				}
			}
			echo "<td class=\"$c\">";
			#If the room isnt booked then allow it to be booked
			if (!$id)
			{
				$hour = date("H",$t); $minute  = date("i",$t);

				echo '<center><a href="' .$phpgw->link("/rbs/edit_entry.php","room=$room&hour=$hour&minute=$minute&year=$year&month=$month&day=$day") . '"><img src="templates/default/images/new.gif" border="0"></a></center>'; 			}
			else
			{
				#if it is booked then show 
				echo '<a href="' . $phpgw->link("/rbs/view_entry.php","id=$id") . '">' . $descr . '&nbsp;</a>';
			}

			echo "</td>\n";
		}
		echo "</tr>\n";
		reset($rooms);
	}
}
echo "</table>";


#include "trailer.inc"; 

#phpinfo();

$phpgw->common->phpgw_footer();

echo "</BODY>";
echo "</HTML>";
?>

