<?php
{
  $img = "/" . $appname . "/images/" . $appname .".gif";
  if (file_exists($phpgw_info["server"]["server_root"].$img)) {
    $img = $phpgw_info["server"]["webserver_url"].$img;
  } else {
    $img = "/" . $appname . "/images/navbar.gif";
    if (file_exists($phpgw_info["server"]["server_root"].$img)) {
      $img=$phpgw_info["server"]["webserver_url"].$img;
    } else {
    $img = "";
    }
  }
  section_start("Rescource Booking System",$img);

  $pg = $phpgw->link("/rbs/admin.php");
  echo "<A href=".$pg.">".lang("Edit Areas and Rooms")."</A><br>";
#  $pg = $phpgw->link("/rbs/preferences.php","editDefault=1");
#  echo "<A href=".$pg.">".lang("Edit headlines shown by default")."</A>";

  section_end(); 
}
?>
