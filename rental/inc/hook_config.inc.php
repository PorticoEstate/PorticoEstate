<?php
	/**
	* Rental - configuration hook
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage Frontend
	* @category hooks
	* @version $Id: hook_config.inc.php 4237 2009-11-27 23:17:21Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	function create_user_based_on_email_group($config)
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$group_assigned = isset($config['create_user_based_on_email_group']) ? $config['create_user_based_on_email_group'] : '';

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";

		foreach ( $groups as $group => $label)
		{
			$selected = '';
			if ( $group_assigned == $group )
			{
				$selected = ' selected';
			}

			$out .=  <<<HTML
				<option value="{$group}"{$selected}>{$label}</option>
HTML;
		}
		return $out;
	}