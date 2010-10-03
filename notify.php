<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => True,
		'nonavbar' => True,
		'disable_Template_class' => True,
		'currentapp' => 'notifywindow'
	);
	
	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');
?>
<html>
<head>
	<meta http-equiv="Refresh" content="300" />
	<title>Notify Window</title>
</head>
<body bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['bg_color']; ?>" alink="blue" vlink="blue" link="blue">
<table>
	<tr><td><a href="<?php echo $GLOBALS['phpgw']->link('/notify.php'); ?>">Check Now</a></td></tr>
<?php
	$GLOBALS['phpgw']->hooks->process('notifywindow');
?>
</table>
</body>
</html>
