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

	/**
	* Get HTML listbox with categories that are candidates for the dimb responsibility_1
	*
	* @param $config
	* @return string HTML listbox to be placed in a table
	*/
	function dimb_cat_1($config)
	{
		$cats	= CreateObject('phpgwapi.categories', -1,  'property', '.invoice.dimb');
		$cats->supress_info = true;

		$selected = isset($config['dimb_responsible_1']) ? $config['dimb_responsible_1'] : '';
		$cat_select = '<option value="">' . lang('none selected') . '</option>' . "\n";
		$cat_select	.= $cats->formatted_list(array('selected' => $selected));
		return $cat_select;
	}

	/**
	* Get HTML listbox with categories that are candidates for the dimb responsibility_2
	*
	* @param $config
	* @return string HTML listbox to be placed in a table
	*/
	function dimb_cat_2($config)
	{
		$cats	= CreateObject('phpgwapi.categories', -1,  'property', '.invoice.dimb');
		$cats->supress_info = true;

		$selected = isset($config['dimb_responsible_2']) ? $config['dimb_responsible_2'] : '';
		$cat_select = '<option value="">' . lang('none selected') . '</option>' . "\n";
		$cat_select	.= $cats->formatted_list(array('selected' => $selected));
		return $cat_select;
	}

	/**
	* Get HTML listbox with workorder status that are to be set when invoice is processed
	*
	* @param $config
	* @return string HTML checkboxes to be placed in a table
	*/
	function workorder_closed_status($config)
	{
		$status_entries = execMethod('property.soworkorder.select_status_list');

		$status_assigned = isset($config['workorder_closed_status']) ? $config['workorder_closed_status'] : array();

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		foreach ( $status_entries as $status)
		{
			$selected = '';
			if ( $status_assigned == $status['id'])
			{
				$selected = 'selected =  "selected"';
			}

			$out .=  <<<HTML
			<option value='{$status['id']}'{$selected}>{$status['name']}</option>
HTML;
		}

		return $out;
	}

	/**
	* Get HTML listbox with workorder reopen status that are to be set when invoice is processed
	*
	* @param $config
	* @return string HTML checkboxes to be placed in a table
	*/
	function workorder_reopen_status($config)
	{
		$status_entries = execMethod('property.soworkorder.select_status_list');

		$status_assigned = isset($config['workorder_reopen_status']) ? $config['workorder_reopen_status'] : array();

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		foreach ( $status_entries as $status)
		{
			$selected = '';
			if ( $status_assigned == $status['id'])
			{
				$selected = 'selected =  "selected"';
			}

			$out .=  <<<HTML
			<option value='{$status['id']}'{$selected}>{$status['name']}</option>
HTML;
		}

		return $out;
	}


	/**
	* Get HTML checkbox with location levels that should be listed in lists
	*
	* @param $config
	* @return string HTML checkboxes to be placed in a table
	*/
	function list_location_level($config)
	{
		$location_types = execMethod('property.soadmin_location.select_location_type');

		$level_assigned = isset($config['list_location_level']) ? $config['list_location_level'] : array();
		$out = '';
		foreach ( $location_types as $dummy => $level)
		{
			$checked = '';
			if ( in_array($level['id'], $level_assigned))
			{
				$checked = ' checked';
			}

			$out .=  <<<HTML
			<tr><td><input type="checkbox" name="newsettings[list_location_level][]" value="{$level['id']}" {$checked}><label>{$level['name']}</label></td></tr>
HTML;
		}
		return $out;
	}


	/**
	* Get HTML options with location levels that should be listed in a listbox
	*
	* @param $config
	* @return string HTML options to be placed in a select
	*/

	function list_location_level_otions($config)
	{
		$location_types = execMethod('property.soadmin_location.select_location_type');

		$level_assigned = isset($config['request_location_level']) ? $config['request_location_level'] : 0;
		$out = '';
		foreach ( $location_types as $dummy => $level)
		{
			$selected = '';
			if ( ($level['id'] == $level_assigned))
			{
				$selected = ' selected';
			}
			$out .=  <<<HTML
			<option value="{$level['id']}" {$selected}><label>{$level['name']}</label></option>
HTML;
		}
		return $out;
	}

	/**
	* Get HTML checkbox with filter buildingpart
	*
	* @param $config
	* @return string HTML listbox to be placed in a table
	*/
	function filter_buildingpart($config)
	{
		$filters = array
		(
			1 => 'Filter 1',
			2 => 'Filter 2',
			3 => 'Filter 3',
//			4 => 'Filter 4'
		);
		
		$locations = array
		(
			'.project' => $GLOBALS['phpgw']->translation->translate('project', array(), false, 'property'),
			'.b_account' => $GLOBALS['phpgw']->translation->translate('accounting', array(), false, 'property'),
			'.project.request' => $GLOBALS['phpgw']->translation->translate('request', array(), false, 'property'),
		);

		$filter_assigned = isset($config['filter_buildingpart']) ? $config['filter_buildingpart'] : array();

		$out = '';
		foreach ( $filters as $filter_key => $filter_name)
		{
			$out .=  <<<HTML
			<tr><td><label>{$filter_name}</label></td><td><select name="newsettings[filter_buildingpart][{$filter_key}]">
HTML;
			$out .= '<option value="">' . lang('none selected') . '</option>' . "\n";
			foreach ( $locations as $key => $name)
			{
				$selected = '';
				if ( isset($filter_assigned[$filter_key]) && $filter_assigned[$filter_key] == $key)
				{
					$selected = 'selected =  "selected"';
				}

				$out .=  <<<HTML
				<option value='{$key}'{$selected}>{$name}</option>
HTML;
			}

			$out .=  <<<HTML
			</select></td></tr>
HTML;
		}
		return $out;
	}
