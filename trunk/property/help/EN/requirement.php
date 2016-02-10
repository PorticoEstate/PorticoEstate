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
	 * This is the manual entry for requirements
	 */
	$phpgw_flags = Array(
		'currentapp'	=> 'manual',
		'admin_header'	=> True,
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$appname = 'property';
?>
<img src="<?php echo $phpgw->common->image($appname, 'navbar.gif');?>" border=0>
<font face="<?php echo $phpgw_info['theme']['font'];?>" size="2"><p/>

<ul>
	<li><b>Deviation / requirement</b><p/>
		Maintenance need that can wait is recorded in the deviation submodule - and categorized within six themes by scores within severity, probability and consequence.<p/>

		These themes (safety, aesthetics, etc) is subject to weighting to produce a overall score to help prioritizing among requirements<p/>
</ul></font>
<?php $phpgw->common->phpgw_footer();?>
