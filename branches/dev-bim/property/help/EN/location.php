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
	 * This is the manual entry for location
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
<li><b>Location</b><p/>
A metadatabase keeps track of dynamic configurable location hierarchy - which is configurable in both width and depth - that is: one can define as many levels as one like - and each level can also have as many attributes as one would like.<p/>

The location hierarchy is linked with foreign keys with - and querying a certain level generates a query with proper join statements to the top of the hierarchy. The sql-query is stored in a cache table for later use.<p/>
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
