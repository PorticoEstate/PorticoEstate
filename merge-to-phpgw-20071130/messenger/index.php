<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php 7887 2001-09-22 02:22:54Z milosch $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'messenger',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$obj = createobject('messenger.uimessenger');
	$obj->inbox();

	$GLOBALS['phpgw']->common->phpgw_footer();
