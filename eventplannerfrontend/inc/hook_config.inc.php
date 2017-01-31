<?php
	/**
	 * Bookingfrontend - configuration hook
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage Bookingfrontend
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

	/**
	 * Get HTML selectbox with supported remote authentication
	 *
	 * @param $config
	 * @return string HTML select box
	 */
	function authentication( $config )
	{
		$selected = $config['authentication_method'];

		$dirname = PHPGW_SERVER_ROOT . "/eventplannerfrontend/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}";
		$dirname = PHPGW_SERVER_ROOT . "/eventplannerfrontend/inc/custom/default";

		$find = array('/_/', '/\.php$/');
		$replace = array(' ', '');

		$file_list = array();
		$dir = new DirectoryIterator($dirname);
		if (is_object($dir))
		{
			foreach ($dir as $file)
			{

				if ($file->isDot() || !$file->isFile() || !$file->isReadable())
				{
					continue;
				}

				$file_list[] = array
					(
					'id' => (string)$file,
					'name' => preg_replace($find, $replace, $file),
					'selected' => $file == $selected ? 'selected' : ''
				);
			}
		}

		$lang_select = lang('select method');
		$out = <<<HTML
				<option value="">{$lang_select}</option>";
HTML;

		foreach ($file_list as $file)
		{
			$out .= <<<HTML
				<option value="{$file['id']}"{$file['selected']}>{$file['name']}</option>";
HTML;
		}
		return $out;
	}
