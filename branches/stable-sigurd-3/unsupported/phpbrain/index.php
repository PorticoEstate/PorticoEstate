<?php
	/**************************************************************************\
	* eGroupWare - KnowledgeBase                                             *
	* http://www.egroupware.org                                              *
	* Written by Dave Hall [skwashd AT phpgroupware.org]                       *
	* ------------------------------------------------------------------------ *
	* Started off as a port of phpBrain - http://vrotvrot.com/phpBrain/        *
	*  but quickly became a full rewrite.                                      *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id$ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'phpbrain',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$obj = CreateObject('phpbrain.uikb');
	$obj->index();

	$GLOBALS['phpgw']->common->phpgw_exit(true);
?>
