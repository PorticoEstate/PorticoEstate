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

	 * This is the manual entry for agreements
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
<li><b>Vendor agreements:</b>

<p>There is two types of vendor agreements:</p>

<ol>
 <li class=MsoNormal>Service agreement - which is actions to perform at
     locations or equipment </li>
 <li class=MsoNormal>Pricebook pr type of work or item to deliver. </li>
</ol>

<p>All elements in the agreements is subjet to price indexing (with history) over the agreement period. </p>

<p>The agreements supervisor is alerted by a user-configurable alarm (email
triggered by async) when it is time to evaluate the agreement for termination
or renewal.</p>

</ul>
</font>
<?php $phpgw->common->phpgw_footer(); ?>
