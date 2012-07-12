<?php
	/**
	* Todo
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage manual
	* @version $Id$
	*/

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	
	/**
	 * Include phpgroupware header
	 */
	include('../../header.inc.php');
	$appname = 'todo';
	
	/**
	 * Include todo's setup
	 */
	include(PHPGW_SERVER_ROOT.'/'.$appname.'/setup/setup.inc.php');
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border="0"><p/>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
Version: <b><?php echo $setup_info[$appname]['version']; ?></b>
</font>
