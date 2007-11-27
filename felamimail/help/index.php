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

	/* $Id: index.php 17818 2006-12-28 11:40:47Z Caeies $ */

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../header.inc.php');
	$appname = 'felamimail';
	include(PHPGW_SERVER_ROOT.'/'.$appname.'/setup/setup.inc.php');
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border="0"><p/>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
Version: <b><?php echo $setup_info[$appname]['version']; ?></b><p/>
This app was based on <a href="http://www.squirrelmail.org" target="_new">Squirrelmail</a>.<br/><p/>
Transformed by <a href="<?php echo $phpgw->link('/index.php', array('menuaction' => 'felamimail.uicompose.compose' , 'send_to' => 'lkneschke@phpgroupware.org')); ?>">Lars Kneschke (knecke)</a><br/>
</font>
<?php $phpgw->common->phpgw_footer(); ?>
