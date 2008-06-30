<?php
  /**************************************************************************\
  * phpGroupWare application (NNTP)                                          *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$phpgw_flags = Array(
		'currentapp'			=>	'nntp',
		'enable_vfs_class'	=>	True,
		'noheader'				=>	True,
		'nonavbar'				=>	True
	);

	
	$phpgw_info['flags'] = $phpgw_flags;

	include('../header.inc.php');

	$vfs_params = Array(
		'string'	=> urldecode($GLOBALS['HTTP_GET_VARS']['file']),
		'relatives'	=> array(RELATIVE_USER_APP)
	);		
	if ($GLOBALS['phpgw']->vfs->file_exists($vfs_params))
	{
		Header('Content-length: '.$GLOBALS['phpgw']->vfs->get_size($vfs_params));
		Header('Content-type: '.$GLOBALS['phpgw']->vfs->file_type($vfs_params));
		Header('Content-disposition: attachment; filename="'.$vfs_params['string'].'"');
		echo $GLOBALS['phpgw']->vfs->read($vfs_params);
		flush();
		$GLOBALS['phpgw']->vfs->rm($vfs_params);
	}
?>
