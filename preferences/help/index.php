<?php
	/**
	* Preferences - user manual
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @subpackage manual
	* @version $Id: index.php 15840 2005-04-17 15:14:31Z powerstat $
	*/

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
	
	/**
	 * Include phpgroupware header
	 */
	include('../../header.inc.php');
	
	$appname = 'preferences';
	
	/**
	 * Include application setup
	 */
	include(PHPGW_SERVER_ROOT . '/' . $appname . '/setup/setup.inc.php');
?>
<img src="<?php echo $GLOBALS['phpgw']->common->image($appname,'navbar'); ?>" border="0" />
<p>
<font face="<?php echo $GLOBALS['phpgw_info']['theme']['font']; ?>" size="2">
Version: <b><?php echo $setup_info[$appname]['version']; ?></b></font></p>

<?php 
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
