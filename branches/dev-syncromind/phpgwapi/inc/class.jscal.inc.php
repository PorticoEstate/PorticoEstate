<?php
	/**
	* jsCalendar wrapper-class (replaced by jquery)
	*
	* @author Dave Hall
	* @author Sigurd Nes
	* @copyright Copyright (C) 2003,2004,2016 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* jsCalendar wrapper-class
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_jscal
	{
		public static function input($id, $date = '', $format = 'input', $title = null)
		{
			if ( !$title )
			{
				$title = 'Select a date';
			}
			$title = lang($title);

			$this->add_listener($id);
			
			return self::input_html($id, $date, $title);

		}

		private static function input_html($id, $date, $title)
		{
			$html .= <<<HTML
					<input type="text" id="{$id}" name="{$id}" value="{$date}" title="{$title}"/>

HTML;
			return $html;
		}

		function add_listener($name)
		{
			$GLOBALS['phpgw']->jqcal->add_listener($name);
		}

	}
