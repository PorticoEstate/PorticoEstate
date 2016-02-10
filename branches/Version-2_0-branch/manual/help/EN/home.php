<?php
	/**
	* Manual - User manual
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package manual
	* @subpackage manual
	* @version $Id$
	*/

	$phpgw_info["flags"] = array("currentapp" => "manual", "enable_utilities_class" => True);

	/**
	 * Include phpgroupware header
	 */
	include('../../../header.inc.php');
?>
<img src="<?php echo $phpgw_info["server"]["app_images"]; ?>/title_home.gif" border="0" alt="" />
<font face="Arial, Helvetica, san-serif" size="2">
<p>When you first log onto the system, this page will be presented to you. 
<p>The navigation bar at the top will show you the system applications you have. 
These applications are represented in two formats
<ul>
  <li>Icons (small pictures, representing the function)</li>
  <li>Icons and text (same as icons, but with added text below each icon)</li>
</ul>
<p>Alerts linked to the calendar or to the trouble ticket/todo list, such as birthday or
priority tasks, will be listed on this page.
<p>When you first login to this system, you may have an alert on this page, stating you need
to change your password. This can be done in preferences.
<p>If you would like other features that are not in your navigation bar, 
but are discussed in these howto pages, please contact the systems admin.
<?php
	$phpgw->common->phpgw_footer();
?>
