<?php
	/**
	* Trouble Ticket System - User manual
	*
	* @copyright Copyright (C) 2001,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
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
	$font = $phpgw_info['theme']['font'];
?>
<img src="<?php echo $phpgw->common->image('preferences','navbar.gif'); ?>" border="0">
<font face="<?php echo $font; ?>" size="2"><p/>
The system functionality can be used to allocate, track and audit tasks of 
groups or members of a specific group.
<ul><li><b>Edit:</b><br/>
To edit a ticket, click on the number of the ticket, make changes to the relevant files,
enter additional text, then click ok.<p/></li>
<li><b>Close:</b><br/>
To close a ticket, click on the number of the ticket, then click on the close button at the bottom
of the page.. <b>NB:</b>It is a good idea to always add a comment when you close ticket, so that other
members of the groups, can determine the outcome and result.<p/></li>
At the top of the screen there are two clickable areas<br/>
<font color="blue">[New ticket | View all tickets]</font>
Clicking on the View all tickets, will change to read<br/>
<font color="blue">[New ticket | View only open tickets]</font>and only the open tickets
in the system will be displayed.<p/>
To view the details of the ticket, click on the ticket number.<p/>
The ticket will be opened, you can see the following information: 
<table width="80%">
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
Last name:<br/>
ID:<br/>
Assigned from:<br/>
Open date:<br/>
Closed date: (if applicable)<br/>
Priority:<br/>
Group:<br/>
Assigned to:<br/>
Subject:<br/>
Details:<br/>
Additional notes:<br/>
Update:OK:Close buttons:</td></table></li></ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
