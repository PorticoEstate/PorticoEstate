<?php
	/**
	* Project Manager - User manual
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @subpackage help
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/help/index.php,v $
	*/

	$phpgw_flags = array('currentapp' => 'manual');

	$phpgw_info['flags'] = $phpgw_flags;
	include('../../header.inc.php');
	$appname = 'projects';
	include(PHPGW_SERVER_ROOT . '/' . $appname . '/setup/setup.inc.php');
?>

<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border="0"><p/>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
Version: <b><?php echo $setup_info[$appname]['version']; ?></b>
</font>
<?php $phpgw->common->phpgw_footer(); ?>
