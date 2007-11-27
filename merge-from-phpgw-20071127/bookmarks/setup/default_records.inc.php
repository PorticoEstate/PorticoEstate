<?php
	/**
	* Bookmarks setup
	* @author jengo
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @subpackage setup
	* @version $Id: default_records.inc.php 15878 2005-04-28 18:59:46Z powerstat $
	*/

	$oProc->query("select count(*) from phpgw_config where config_app='bookmarks'",__LINE__,__FILE__);
	$oProc->next_record();

	if (! $oProc->f(0))
	{
		$oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('bookmarks','mail_footer',"
			. "'\n\n--\nThis was sent from phpGroupWare\nhttp://www.phpgroupware.org\n')");
	}
?>
