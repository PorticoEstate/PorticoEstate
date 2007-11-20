<?php
	/**
	 * phpGroupWare YUI wrapper class
	 *
	 * @author Dave Hall
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage gui
	 * @version $Id: class.jscalendar.inc.php,v 1.14 2007/01/14 11:10:08 skwashd Exp $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	/**
	 * phpGroupWare YUI wrapper class
	 *
	 * @package phpgwapi
	 * @subpackage gui
	 */

	if ( !isset($GLOBALS['phpgw']->js) && !is_object($GLOBALS['phpgw']->js) )
	{
		$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
	}
	
	class phpgwapi_yui
	{
		/**
		* @var int $counter the widget id counter
		*/
		private static $counter = 0;

		/**
		* Load all the dependencies for a YUI widget
		*
		* @internal this does not render the widget it only includes the header js files
		* @param string $widget the name of the widget to load, such as autocomplete
		* @param bool use the minimised versions of the files
		* @return string yahoo namespace for widget - empty string on failure
		*/
		public static function load_widget($widget, $use_min = true)
		{
			$min = $use_min ? '-min' : '';

			$load = array();
			switch ( $widget )
			{
				case 'animation':
					$load = array('animation');
					break;

				case 'autocomplete':
					$load = array('autocomplete', 'connection');
					break;

				case 'button':
					$load = array('button', 'element');
					break;

				case 'calendar':
					$load = array('calendar');
					break;

				case 'colorpicker':
				case 'colourpicker': // be nice to the speakers of H.M. English :)
					$load = array('colorpicker');
					break;

				case 'container':
					$load = array('container', 'dragdrop');
					break;

				case 'datasource':
					$load = array('datasource', 'connection');
					break;

				case 'datatable':
					$load = array('datatable', 'datasource');
					break;

				case 'dom':
					// do nothing - auto included
					break;

				case 'dragdrop':
					$load = array('dragdrop');
					break;

				case 'editor':
					$load = array('editor', 'menu', 'element', 'button', 'animation', 'dragdrop');
					break;

				case 'element':
					$load = array('element');
					break;

				case 'event':
					// do nothing - auto included
					break;

				// not including history - as it isn't needed - need to handle the not included/used types somewhere

				case 'imageloader':
					$load = array('imageloader');
					break;

				case 'logger':
					$load = array('dragdrop', 'logger');
					break;

				case 'menu':
					$load = array('containercore', 'menu');
					break;

				case 'slider':
					$load = array('dragdrop', 'animation', 'slider');
					break;

				case 'tabview':
					$load = array('element', 'tabview');
					break;

				case 'treeview':
					$load = array('treeview');
					break;

				default:
					trigger_error(lang("Unsupport YUI widget '%1' supplied to phpgwapi_yui::load_widget()", $widget), E_USER_WARNING);
					return '';
			}

			$ok = true;
			$GLOBALS['phpgw']->js->validate_file('yahoo', 'yahoo-dom-event');
			foreach ( $load as $script )
			{
				$test = $GLOBALS['phpgw']->js->validate_file('yahoo', "{$script}{$min}");
				if ( !$test & !$ok )
				{
					trigger_error(lang("Unable to load YUI script '%1' when attempting to load widget: '%2'", "{$script}{$min}", $widget), E_USER_WARNING);
					return '';
				}
			}

			return "phpgroupware.{$widget}" . ++self::$counter;
		}
	}
