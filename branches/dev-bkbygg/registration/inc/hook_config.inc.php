<?php
	/**
	 * Property - configuration hook
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage property
	 * @category hooks
	 * @version $Id: hook_config.inc.php 8281 2011-12-13 09:24:03Z sigurdne $
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
	 * Get HTML listbox with workorder status that are to be set when invoice is processed
	 *
	 * @param $config
	 * @return string HTML checkboxes to be placed in a table
	 */
	function default_group( $config )
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');

		$_selected = isset($config['default_group_id']) ? $config['default_group_id'] : '';

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		foreach ($groups as $group => $label)
		{
			$selected = '';
			if ($_selected == $group)
			{
				$selected = 'selected =  "selected"';
			}

			$out .= <<<HTML
			<option value='{$group}'{$selected}>{$label}</option>
HTML;
		}

		return $out;
	}
