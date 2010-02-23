<?php
	/**
	* Frontend - configuration hook
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

	/**
	* Get HTML selectbox with supported remote authentication
	*
	* @param $config
	* @return string table-rows with text-input for sorting
	*/
	function tab_sorting($config)
	{
		$locations = $GLOBALS['phpgw']->locations->get_locations(false, 'frontend');

		unset($locations['.']);
		unset($locations['admin']);

		$_locations = array();
		foreach ($locations as $location => $name)
		{
			$_locations[] = array
			(
				'location'	=> $location,
				'name'		=> $name,
				'sort'		=> isset($config['tab_sorting'][$name]) ? $config['tab_sorting'][$name] : 99
			);
		}
		
		if(isset($config['tab_sorting']) && $config['tab_sorting'])
		{
			array_multisort($config['tab_sorting'], SORT_ASC, $_locations);
		}

		$out = '';
		foreach ($_locations as $key => $location)
		{
			$name = $location['name'];
			$location_name = $GLOBALS['phpgw']->translation->translate($name, array(), false, 'frontend');
			$value = isset($config['tab_sorting'][$name]) ? $config['tab_sorting'][$name] : '';
			$out .=  <<<HTML
				<tr>
					<td>
						$location_name
					</td>
					<td>
						<input name="newsettings[tab_sorting][$name]" value="{$value}" size='2'>
					</td>
				</tr>
HTML;
		}

		return $out;
	}

