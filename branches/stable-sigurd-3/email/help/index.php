<?php
	/**
	* EMail
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage manual
	* @version $Id$
	*/

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
	
	/**
	* Include phpgroupware header
	*/
	include('../../header.inc.php');
	$appname = 'email';
	
	/**
	* Include emails setup
	*/
	include(PHPGW_SERVER_ROOT.'/'.$appname.'/setup/setup.inc.php');
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.png'); ?>" border="0"><p/>
<font face="<?php echo $GLOBALS['phpgw_info']['theme']['font']; ?>" size="2">
Version: <b><?php echo $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']; ?></b><p/>
</font>
<?php $phpgw->common->phpgw_footer(); ?>
