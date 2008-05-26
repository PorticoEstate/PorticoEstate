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
	 * This is the manual entry for entities
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
<li><b>Entities</b><p/>
Also a metadatabase (separated from location to) defines entities (as components, equipment, reports ..) which links to other entities or locations.<p/>

Entities are organized in class of entitity and entity_category: each entity_category is represented by their own table.<p/>

Example: entity_1 is equipment, entity_1_1 is elevator, and entity_1_2 is fire alarm system<p/>

There is a set of submodules that all is controlled by ACL2 (also: each entity_category emerge as its one submodule when defined)<p/>
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
