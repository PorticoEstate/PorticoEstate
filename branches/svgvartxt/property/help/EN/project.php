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
	 * This is the manual entry for project
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
<li><b>Project management:</b><p/>
A project is a collection of orders/contracts. The project is linked to a location or entity (e.g equipment). Projects is separated in orders/contracts that could be subject to bidding contest amongst vendors. Each order is linked to its parent project and to a vendor - and consists of a series of work-descriptions to perform and / or items to deliver. An order can be defined as simple as a brief description of simple tasks - or as a detailed complex tender document with a full blown deviation auditing system up per record in the contract.<p/>

When calculating an order - one can either choose elements from the prizebook, or add custom entries.<p/>

A typical order can be saved as a template for later re-use.<p/>

Orders is sent by email to vendors - which in return sends the invoice (also on email)<p/>
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
