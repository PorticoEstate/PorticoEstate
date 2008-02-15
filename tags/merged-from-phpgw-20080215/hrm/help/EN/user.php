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

	 * This is the manual entry for user
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
<ul>
<li><b>User information:</b>

<p>some info:</p>

<ol>
 <li>pkt </li>
 <li>pkt </li>
</ol>

<p>All.. </p>


</ul>
</font>
<?php $phpgw->common->phpgw_footer(); ?>
