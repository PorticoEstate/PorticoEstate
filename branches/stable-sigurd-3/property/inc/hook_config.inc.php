<?php
	/**
	* Property - configuration hook
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage property
	* @category hooks
	* @version $Id$
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


	/**
	* Get HTML checkbox with groups that are candidates for simplified tts interface
	*
	* @param $config
	* @return string HTML checkboxes to be placed in a table
	*/
	function fmttssimple_group($config)
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$groups_assigned = isset($config['fmttssimple_group']) ? $config['fmttssimple_group'] : array();
		$out = '';
		foreach ( $groups as $group => $label)
		{
			$checked = '';
			if ( in_array($group, $groups_assigned))
			{
				$checked = ' checked';
			}

			$out .=  <<<HTML
				<tr><td><input type="checkbox" name="newsettings[fmttssimple_group][]" value="{$group}" {$checked}><label>{$label}</label></td></tr>
HTML;
		}
		return $out;
	}

	/**
	* Get HTML checkbox with groups that are candidates for the field finnish date at tts
	*
	* @param $config
	* @return string HTML checkboxes to be placed in a table
	*/
	function fmtts_group_finnish_date($config)
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$groups_assigned = isset($config['fmtts_group_finnish_date']) ? $config['fmtts_group_finnish_date'] : array();
		$out = '';
		foreach ( $groups as $group => $label)
		{
			$checked = '';
			if ( in_array($group, $groups_assigned))
			{
				$checked = ' checked';
			}

			$out .=  <<<HTML
				<tr><td><input type="checkbox" name="newsettings[fmtts_group_finnish_date][]" value="{$group}" {$checked}><label>{$label}</label></td></tr>
HTML;
		}
		return $out;
	}


