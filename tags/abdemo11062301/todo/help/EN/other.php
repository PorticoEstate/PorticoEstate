<?php
	/**
	* Todo - User manual
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage manual
	* @version $Id$
	*/

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	
	/**
	 * Include phpgroupware header
	 */
	include('../../../header.inc.php');
?>
<img src="<?php echo $phpgw->common->image('todo','navbar.gif'); ?>" border="0">
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
A searchable todo list for keeping a quick note of things todo.<p/>
<ul><li><b>Search:</b><br/>
Enter a keyword for the task you are looking for, click on the search button.</li><p/>
<li><b>Filter:</b><br/>
Todo items can be listed in two ways:
<dd>Show all = all tasks for all groups you are a member of.</dd>
<dd>Only yours = only your own tasks.</dd></li></ul></font>
