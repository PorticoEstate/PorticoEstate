<?php
	/**************************************************************************\
	* phpGroupWare Application - phonelog                                      *
	* http://www.phpgroupware.org                                              *
	* Written by Mathieu van Loon <mathieu@playcollective.com>                 *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: header.inc.php 8258 2001-11-13 03:56:36Z milosch $ */

	// header for phonelog application
	// start with status list
	// Edit this as you see fit
	global $phonelog;
	$phonelog["entry_status"][4] = "call back - URGENT";
	$phonelog["entry_status"][3] = "call back";
	$phonelog["entry_status"][2] = "will call back";
	$phonelog["entry_status"][1] = "No further action";
	// Don't use $phonelog["entry_status"][0]
?>
