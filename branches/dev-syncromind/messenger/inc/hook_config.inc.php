<?php
/**************************************************************************\
* phpGroupWare - KnowledgeBase                                             *
* http://www.phpgroupware.org                                              *
*                                                                          *
* Copyright (c) 2003-2010 Free Sofware Foundation Inc                      *
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
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups', -1, 'ASC', 'account_lid', '', -1);

		$restrict_to_group = isset($config['restrict_to_group']) && $config['restrict_to_group'] ? $config['restrict_to_group'] : array();
		$out = '';
		foreach ( $groups as $group )
		{
			$checked = in_array($group->id, $restrict_to_group) ? 'checked = "checked"' : '';
			$out .=  <<<HTML
				<tr>
					<td>
						{$group->__toString()}
					</td>
					<td>
						<input type="checkbox" name="newsettings[restrict_to_group][]" value="{$group->id}" {$checked}>
					</td>
				</tr>
HTML;
		}
		return $out;
	}
