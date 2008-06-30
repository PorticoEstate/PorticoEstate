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
<img src="<?php echo $phpgw->common->image('tts','navbar.gif'); ?>" border="0">
<font face="<?php echo $font; ?>" size="2"><p/>
The system functionality can be used to allocate, track and audit tasks of 
groups or members of a specific group.
<ul><li><b>Create:</b><br/>
To create a ticket, click on "New Ticket", you will then be presented with the form to add
the details as above..<p/>
At the top of the screen there are two clickable areas<br/>
<font color="blue">[New ticket | View all tickets]</font>
Clicking on the View all tickets, will change to read<br/>
<font color="blue">[New ticket | View only open tickets]</font>and only the open tickets
in the system will be displayed.<p/>
<table width="80%">
<td bgcolor="#ccddeb" width=50% valign="top">
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
