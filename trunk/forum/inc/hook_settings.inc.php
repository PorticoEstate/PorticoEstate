<?php
	/**************************************************************************\
	* phpGroupWare - Preferences                                               *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_settings.inc.php 8051 2001-10-03 00:03:18Z jengo $ */

	$default_view = array(
		'threads'   => lang('Threaded'),
		'collapsed' => lang('collapsed')
	);
	create_select_box('Default view','default_view',$default_view);
