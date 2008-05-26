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
	 * This is the manual entry for invoice
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

<ul>
<li><b>Electronic invoice handling</b><p/>
Invoices is imported and handled by the system (according to users role - controlled by ACL2) before exported to accounting system.<p/>

The import filters currently handles cvs ,BBS and XML files (the vendors has to agree on a template)<p/>

The export format is defined by a drop-in filter per target system(currently two systems is supported).<p/>
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
