<?php
/**************************************************************************\
* phpGroupWare - KnowledgeBase                                             *
* http://www.phpgroupware.org                                              *
*                                                                          *
* Copyright (c) 2003-2006 Free Sofware Foundation Inc                      *
* Written by Dave Hall skwashd at phpgropware.org                          *
* ------------------------------------------------------------------------ *
*  Started off as a port of phpBrain - http://vrotvrot.com/phpBrain/	   *
*  but quickly became a full rewrite					   *
* ------------------------------------------------------------------------ *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

	/* $Id$ */

	function restrict_to_group($config)
	{
		$str = '';
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups', -1, 'ASC', 'account_lid', '', -1);
		foreach ( $groups as $group )
		{
			$str .= '<option value="' . $group->id . '"' . ($config['restrict_to_group'] == $group->id ? ' selected="selected"' : '' ) .'>'
				. $GLOBALS['phpgw']->common->display_fullname($group->lid, $group->firstname, $group->lastname)
			."</option>\n";
		}
		return $str;
		$sbox->getAccount('newsettings[restrict_to_group]',$config['restrict_to_group'], true, 'groups', 1);
	}
?>
