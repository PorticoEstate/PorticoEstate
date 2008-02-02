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

	/**
	 * This is the manual entry for hrm
	 */

	$phpgw_flags = Array(
		'currentapp'	=> 'manual',
		'admin_header'	=> True,
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$appname = 'hrm';
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.png'); ?>" border=0>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
A web-based Human Resource competence Management system. Consist of a set of sub-modules.
<ul>
<li><b>User</b><br/>
<li><b>User-traing</b><br/>
<li><b>Job</b><br/>
<li><b>Job-requirement</b><br/>
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>

