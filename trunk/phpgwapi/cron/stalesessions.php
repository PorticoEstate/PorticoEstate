<?php
	/**
	* Timed Asynchron Services
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage cron
	* @version $Id$
	* @internal stalesession.php - to use instead of stalesession.pl
	* @internal may be invoked via cron with "php stalesession.php"
	*/

  // config start
  $purgedelay = "3600";  // define allowed idle time before deletion in seconds
  $purgetime  = time() - $purgedelay;
  $db_user    = $_SERVER['argv'][1];
  $db_pwd     = "my_pass";
  $db_server  = "localhost";
  $db_db      = "phpGroupWare";
  // config end - do not edit after here unless you really know what you do!
  
  // establish link:
  $link = mysql_connect("$db_server","$db_user","$db_pwd");
  mysql_query("use $db_db", $link);

  // delete old (timed out) sessions
  $query = sprintf("delete from phpgw_sessions where session_dla <= '$purgetime'");
  $res = mysql_query($query, $link);
?>
