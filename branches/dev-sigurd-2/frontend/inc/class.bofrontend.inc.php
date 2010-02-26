<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
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
	 * Frontend
	 *
	 * @package Frontend
	 */

	class frontend_bofrontend
	{
		public function __construct()
		{

		}

		public static function get_sections()
		{
			$locations = $GLOBALS['phpgw']->locations->get_locations();

			unset($locations['.']);
			unset($locations['admin']);

			$config	= CreateObject('phpgwapi.config','frontend');
			$config->read();

			$_locations = array();
			foreach ($locations as $location => $name)
			{
				$_locations[] = array
				(
					'location'	=> $location,
					'name'		=> $name,
					'sort'		=> isset($config->config_data['tab_sorting'][$name]) ? $config->config_data['tab_sorting'][$name] : 99
				);
			}
		
			if(isset($config->config_data['tab_sorting']) && $config->config_data['tab_sorting'])
			{
				array_multisort($config->config_data['tab_sorting'], SORT_ASC, $_locations);
			}

			return $_locations;
		}
	}
