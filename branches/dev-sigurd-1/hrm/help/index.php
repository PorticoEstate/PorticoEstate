<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage manual
 	* @version $Id$
	*/

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../header.inc.php');
	$appname = 'hrm';
	include(PHPGW_SERVER_ROOT.'/'.$appname.'/setup/setup.inc.php');
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.png'); ?>" border="0"><p/>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
Version: <b><?php echo $setup_info[$appname]['version']; ?></b><p/>
A web-based Human Resource competence Management system.
</font>
<?php $phpgw->common->phpgw_footer(); ?>
