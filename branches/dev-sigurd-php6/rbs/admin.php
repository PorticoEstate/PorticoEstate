<?php
$phpgw_info["flags"]["currentapp"] = "rbs";	
include "config.inc";
include "functions.inc";
include "connect.inc";
?>



<h2>Administration</h2>

<table border=1>
<tr>
<th><center><b>Areas</b></center></th>
<th><center><b>Rooms <?php if ($area) { echo "in $area_name";}?></b></center></th>
</tr>

<tr>
<td>
<?php
# This cell has the areas
$res = mysql_query("select id, area_name from mrbs_area order by area_name");
echo mysql_error();

if (mysql_num_rows($res) == 0) {
	echo "No Areas";
} else {
	echo "<ul>";
	while ($row = mysql_fetch_row($res)) {
		$area_name_q = urlencode($row[1]);
		echo "<li><a href=".$phpgw->link( "/rbs/admin.php", "area=$row[0]&area_name=$area_name_q").">$row[1]</a>";
		echo " (<a href=".$phpgw->link("/rbs/del.php", "type=area&area=$row[0]").">Delete</a>)</li>";
	}
	echo "</ul>";
}
?>
</td>
<td>
<?php
# This one has the rooms
if ($area) {
	$res = mysql_query("select id, room_name, description, capacity from mrbs_room where area_id=$area order by room_name");
	if (mysql_num_rows($res) == 0) {
		echo "No rooms";
	} else {
		echo "<ul>";
		while ($row = mysql_fetch_row($res)) {
			echo "<li>$row[1] ($row[2], $row[3]) (<a href=".$phpgw->link("/rbs/del.php", "type=room&room=$row[0]").">Delete</a>)";
		}
		echo "</ul>";
	}
} else {
	echo "No area selected";
}

?>

</tr>
<tr>
<td>
<h3>Add Area</h3>
<form action=add.php method=post>
<input type=hidden name=type value=area>
<input type=text name=name><br>
<input type=submit>
</form>
</td>

<td>
<?php if ($area) { ?>
<h3>Add Room</h3>
<form action=add.php method=post>
<input type=hidden name=type value=room>
<input type=hidden name=area value=<?php echo $area; ?>>
Name:        <input type=text name=name><br>
Description: <input type=text name=description><br>
Capacity:    <input type=text name=capacity><br>
<input type=submit>
</form>
<?php } else { echo "&nbsp;"; }?>
</td>
</tr>
</table>

<?php
  $phpgw->common->phpgw_footer();
?>

