<?php
	/**
	 * Frontend - configuration hook
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2015 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage Frontend
	 * @category hooks
	 * @version $Id: hook_config.inc.php 11377 2013-10-18 08:25:54Z sigurdne $
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
	 * Get HTML checkbox with categories that are candidates for frontend documents cat
	 *
	 * @param $config
	 * @return string options for selectbox
	 */
	function document_cat( $config )
	{
		$cats = CreateObject('phpgwapi.categories', -1, 'property', '.document');
		$cats->supress_info = true;
		$values = $cats->return_sorted_array(0, false, '', '', '', $globals = true, '', $use_acl = false);
		$tts_frontend_cat_selected = isset($config['document_cat']) ? $config['document_cat'] : array();
		$out = '';
		foreach ($values as $entry)
		{
			$checked = '';
			if (in_array($entry['id'], $tts_frontend_cat_selected))
			{
				$checked = ' checked';
			}
			$out .= <<<HTML
				<tr><td><input type="checkbox" name="newsettings[document_cat][]" value="{$entry['id']}" {$checked}><label>{$entry['name']}</label></td></tr>
HTML;
		}
		return $out;
	}
