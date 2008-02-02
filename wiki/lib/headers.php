<?php
// $Id$

// Generate headers saying not to assume this page won't change.
// The page's last-edited time stamp is currently not used.  This caused
//   problems with certain browser settings; especially with pages containing
//   category lists, like RecentChanges.  Although the page itself didn't
//   change, the category list did.
function gen_headers($timestamp)
{
//  $time = mktime(substr($timestamp, 8, 2),  substr($timestamp, 10, 2),
//                 substr($timestamp, 12, 2), substr($timestamp, 4, 2),
//                 substr($timestamp, 6, 2),  substr($timestamp, 0, 4));
//  $mod = gmdate("D, d M Y H:i:s", $time);
  $now = gmdate("D, d M Y H:i:s");

  #header("Expires: $now GMT");
  #header("Last-Modified: $now GMT");
//  header("Cache-Control: no-cache, must-revalidate");
//  header("Pragma: no-cache");
}
?>
