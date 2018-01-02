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
	* @version $Id: hook_config.inc.php 6710 2010-12-27 15:07:01Z sigurdne $
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
	 * Get HTML listbox with workorder reopen status that are to be set when invoice is processed
	 *
	 * @param $config
	 * @return string HTML checkboxes to be placed in a table
	 */
	function reopen_status( $config )
	{
		$status_entries = execMethod('helpdesk.botts.get_status_list');

		$status_assigned = isset($config['reopen_status']) ? $config['reopen_status'] : array();

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		foreach ($status_entries as $status)
		{
			$selected = '';
			if ($status_assigned == $status['id'])
			{
				$selected = ' selected = "selected"';
			}

			$out .= <<<HTML
			<option value='{$status['id']}'{$selected}>{$status['name']}</option>
HTML;
		}

		return $out;
	}


	/**
	 * Get HTML checkbox with groups that are candidates for simplified tts interface
	 *
	 * @param $config
	 * @return string HTML checkboxes to be placed in a table
	 */
	function fmtts_assign_group_candidates( $config )
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$groups_assigned = isset($config['fmtts_assign_group_candidates']) ? $config['fmtts_assign_group_candidates'] : array();
		$out = '';
		foreach ($groups as $group => $label)
		{
			$checked = '';
			if (in_array($group, $groups_assigned))
			{
				$checked = ' checked';
			}

			$out .= <<<HTML
			<tr><td><input type="checkbox" name="newsettings[fmtts_assign_group_candidates][]" value="{$group}" {$checked}><label>{$label}</label></td></tr>
HTML;
		}
		return $out;
	}

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
	 * Get HTML selectbox with user groups - which group to add autocreated users
	 *
	 * @param $config
	 * @return string options for selectbox
	 */
	function autocreate_default_group( $config )
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$group_assigned = isset($config['autocreate_default_group']) ? $config['autocreate_default_group'] : '';

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";

		foreach ($groups as $group => $label)
		{
			$selected = '';
			if ($group_assigned == $group)
			{
				$selected = ' selected';
			}

			$out .= <<<HTML
				<option value="{$group}"{$selected}>{$label}</option>
HTML;
		}
		return $out;
	}
