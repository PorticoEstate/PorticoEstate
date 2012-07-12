<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage manual
 	* @version $Id$
	*/

	/**
	 * This is the manual entry for property
	 */

	$phpgw_flags = Array(
		'currentapp'	=> 'manual',
		'admin_header'	=> True,
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$appname = 'property';
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border=0>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
A web-based Facilities Management application. Consist of a set of sub-modules.
<ul>
<li><b>Location</b><br/>
<li><b>Entities</b><br/>
<li><b>Project management</b><br/>
<li><b>Deviation / requirement</b><br/>
<li><b>Electronic invoice handling </b><br/>
<li><b>Vendor agreements</b><br/>
<li><b>Document register/Drawing register</b><br/>
<li><b>Helpdesk</b><br/>
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>

