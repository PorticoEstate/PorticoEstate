<?php
  /**************************************************************************\
  * phpGroupWare - User manual                                               *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
?>

<img src="<?php echo $phpgw->common->image('todo','navbar.gif'); ?>" border="0">
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
FeLaMiMail is a webbased imap client, based on the functions of php4. You need to 
have imap extentions enabled in your php4 module, to use the nice piece of software.<p/>
<ul>
<li>
	<b>Features:</b>
	<br/>
	<p/>
	<i>Sieve support:</i>
	<br/>
	FeLaMiMail supports managing sieve scripts on a sieve server.<p/>
	<i>SSL support:</i>
	<br/>
	FeLaMiMail is able to read emails over a encrypted SSL session.<p/>
<li>
	<b>Why FeLaMiMail?</b>
	<br/>
	The name is based on the names of my 3 daughters(Ferike, Lara, Mia-Lena).
</li>
<p/>
<li><b>Filter:</b><br/>
<dd>Todo items can be listed in two ways:</dd>
<dd>Show all = all tasks for all groups you are a member of.</dd><br/>
<dd>Only yours = only your own tasks.</dd></li></ul></font>

<?php $phpgw->common->phpgw_footer(); ?>
