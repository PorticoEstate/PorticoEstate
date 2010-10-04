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
<ul><li><b>Add:</b><br/>
Click on the add button, you will be presented with the following fields:<p/>
<i>Blank text box:</i><br/>
Enter you todo in this box, (e.g. don't forget to feed the cat:)<p/>
<i>Urgency:</i><br/>
Set the urgency of the task, Low,Normal,High<p/>
<i>Completed:</i><br/>
Drop down menu, percentage of task completion.<p/>
<i>Date due:</i><br/>
Format: Drop down menu for month, type in day and year,<br/>
(e.g. January 01 2000) or simply a number to represent how<br/>
many days from start date the task needs to be completed by.<p/>
<i>Access type:</i><br/>
Access can be restricted to, private, group readable (that is the members of the
same groups as you are in) will be able to see the todo.<br/>
Todo items made globally readable, will allow all users to the system will be able to read the todo.<p/>
<i>Which Groups:</i><br/>
Drop down menu of groups you are a member of, select one or more.</li><p/></ul></font>
