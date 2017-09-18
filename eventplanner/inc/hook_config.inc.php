<?php
	/**
	 * Eventplanner - configuration hook
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage eventplanner
	 * @category hooks
	 * @version $Id: hook_config.inc.php 15466 2016-08-15 17:36:10Z sigurdne $
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
	 * Get HTML listbox with categories that are candidates for the dimb responsibility_1
	 *
	 * @param $config
	 * @return string HTML listbox to be placed in a table
	 */
	function default_application_category( $config )
	{
		$cats = CreateObject('phpgwapi.categories', -1, 'eventplanner', '.application');
		$cats->supress_info = true;

		$selected = isset($config['default_application_category']) ? $config['default_application_category'] : null;
		$cat_select = '<option value="">' . lang('none selected') . '</option>' . "\n";
		$cat_select .= $cats->formatted_list(array('selected' => $selected));
		return $cat_select;
	}

	function default_vendor_category( $config )
	{
		$selected = isset($config['default_vendor_category']) ? $config['default_vendor_category'] : null;

		$status_entries = execMethod('eventplanner.bogeneric.get_list', array('type' => 'vendor_category',
			'selected' => $selected));

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		foreach ($status_entries as $status)
		{
			$selected = $status['selected'] ? ' selected = "selected"' : '';

			$out .= <<<HTML
			<option value='{$status['id']}'{$selected}>{$status['name']}</option>
HTML;
		}
		return $out;
	}

	function vendor_receipt_text_editor( $config )
	{
		phpgw::import_class('phpgwapi.jquery');
		if (empty($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'])
			|| $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'] != 'ckeditor')
		{
			return;
		}
		phpgwapi_jquery::init_ckeditor('vendor_receipt_text');
	}

	function customer_receipt_text_editor( $config )
	{
		phpgw::import_class('phpgwapi.jquery');
		if (empty($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'])
			|| $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'] != 'ckeditor')
		{
			return;
		}
		phpgwapi_jquery::init_ckeditor('customer_receipt_text');
	}

	function vendor_canceled_text_editor( $config )
	{
		phpgw::import_class('phpgwapi.jquery');
		if (empty($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'])
			|| $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'] != 'ckeditor')
		{
			return;
		}
		phpgwapi_jquery::init_ckeditor('vendor_canceled_text');
	}

	function customer_canceled_text_editor( $config )
	{
		phpgw::import_class('phpgwapi.jquery');
		if (empty($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'])
			|| $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'] != 'ckeditor')
		{
			return;
		}
		phpgwapi_jquery::init_ckeditor('customer_canceled_text');
	}

