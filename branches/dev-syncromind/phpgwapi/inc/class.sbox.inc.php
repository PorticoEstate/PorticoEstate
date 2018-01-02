<?php
	/**
	* Class for creating predefines select boxes
	* @author Marc Logemann <loge@phpgroupware.org>
	* @copyright Copyright (C) 2000,2001 Dan Kuykendall,Marc Logemann
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('phpgwapi.country');

	/**
	* Class for creating predefines select boxes
	* 
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_sbox
	{
		/**
		* Get a hour drop down list
		*
		* @param string $name the name (and html id) for the element
		* @param int $selected the current selection
		* @return string html select element populated with hours
		*/
		public static function hour_formated_text($name, $selected = null)
		{
			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			for ( $i = 0; $i < 24; ++$i )
			{
				$slctd =  $i == $selected ? ' selected' : '';
				$formatted = phpgwapi_datetime::formattime($i);
				$html .= <<<HTML
				<option value="{$i}" {$slctd}>{$formatted}</option>

HTML;
			}
			$html .= <<<HTML
</select>

HTML;
			return $html;
		}

		/**
		* Get an hour drop download - unformatted
		*
		* @param string $name the name (and html id) for the element
		* @param int $selected the current selection
		* @return string html select element populated with hours
		*/
		public static function hour_text($name, $selected = null)
		{
			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			for ( $i = 1; $i < 13; ++$i )
			{
				$slctd =  $i == $selected ? ' selected' : '';
				$html .= <<<HTML
				<option value="{$i}" {$slctd}>{$i}</option>

HTML;
			}
			$html .= <<<HTML
</select>

HTML;
			return $html;
		}

		/**
		* Generate a list of minutes
		*
		* @param string $name the name and id of the html element
		* @param int $selected the currently selected minute
		* @param int $interval the interval between options
		* return string html select element populated with minutes
		*/
		public static function sec_minute_text($name, $selected = null, $interval = 1)
		{
			$html = <<<HTML
			<select name="{$name}">

HTML;
			for ( $i = 0; $i <= 61; $i =+ $interval )
			{
				$slctd =  $i == $selected ? ' selected' : '';
				$val = sprintf('%02d', $i);
				$html .= <<<HTML
				<option value="{$val}" {$slctd}>{$val}</option>

HTML;
			}
			$html .= <<<HTML
</select>

HTML;
			return $html;
		}

		/**
		* Generate AM/PM select list
		*
		* @param string $name the name and id of the html element
		* @param string $select the selected option am|pm
		* @return html select element populated with AM/PM options
		*/
		public static function ap_text($name, $selected = 'am')
		{
			$lang_am = lang('am');
			$lang_pm = lang('pm');
			$selected = strtolower($selected);
			$sel = array
			(
				'am'	=> $selected == 'am' ? ' selected':'',
				'pm'	=> $selected == 'pm' ? ' selected':''
			);
			return <<<HTML
				<select name="{$name}" id="{$name}">
					<option value="am"{$sel['am']}>{$lang_am}</option>
					<option value="pm"{$sel['pm']}}>{$lang_pm}</option>
				</select>

HTML;
		}

		/**
		* Generate a set of form controls for selecting a time
		*
		* @param string $hour_name the name and id of the hour select element
		* @param int $hour_selected the currently selected hour value
		* @param string $min_name the name and id of the minute select element
		* @param int $min_selected the currently selected minute value
		* @param string $sec_name the name and id of the seconds select element
		* @param int $sec_selected the currently selected second value
		* @param string $ap_name the name and id of the ap/pm select element
		* @param int $ap_selected the currently selected am/pm value
		*/
		public static function full_time($hour_name, $hour_selected, $min_name, $min_selected, $sec_name, $sec_selected, $ap_name, $ap_selected)
		{
			// FIXME: This needs to be changed to support users' time format preferences
			return self::hour_text($hour_name, $hour_selected)
				. self::sec_minute_text($min_name, $min_selected)
				. self::sec_minute_text($sec_name, $sec_selected)
				. self::ap_text($ap_name, $ap_selected);
		}

		/**
		* Get a list of days
		*
		* @param string $name the name and id of the select element
		* @param int $selected the currently selected day
		* @return string select list populated with weekdays
		*/
		public static function getWeekdays($name, $selected = null)
		{
			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			foreach ( phpgwapi_datetime::get_dow_fullnames() as $id => $dow )
			{
				$slctd = $id == $selected ? ' selected' : '';
				$html .= <<<HTML
				<option value="{$id}"{$slctd}>$dow</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		* Get a month select box
		*
		* @param string $name the name and id of the html select element
		* @param int $selected the currently selected month
		* @return string html select element populated with a list of months
		*/
		public static function getMonthText($name, $selected = null)
		{
			if ( empty($selected) )
			{
				$selected = date('n');
			}

			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			foreach ( phpgwapi_datetime::get_month_fullnames() as $id => $month )
			{
				$slctd = $id == $selected ? ' selected="selected"' : '';
				$html .= <<<HTML
				<option value="{$id}"{$slctd}>$month</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		* Get a drop down list for the days of the month
		*
		* @param string $name name and id of html element
		* @param int $selected the currently selected day of the month
		* @return string select element populated with days of the month
		*/
		public static function getDays($name, $selected = null)
		{
			if ( empty($selected) )
			{
				$selected = date('j');
			}

			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			for ( $i=1; $i <= 31; ++$i )
			{
				$slctd = $i == $selected ? ' selected="selected"' : '';
				$html .= <<<HTML
				<option value="{$i}"{$slctd}>{$i}</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		* Get a dropdown list of years
		*
		* @param string $name the name and id of the html select element
		* @param int $selected the currently selected year
		* @param int $start_year the year to start (default: current year - 5)
		* @param int $end_year the last year in the list (default: current year + 10)
		* @return string html select element populated with year options
		*/
		public static function getYears($name, $selected = null, $start_year = null, $end_year = null)
		{
			if ( empty($selected) )
			{
				$selected = date('Y');
			}

			if (!$start_year)
			{
				$start_year = date('Y') - 5;
			}
			if ($selected && $start_year > $selected)
			{
				$start_year = $selected;
			}

			if (!$end_year)
			{
				$end_year = date('Y') + 10;
			}

			if ($selected && $end_year < $selected)
			{
				$end_year = $selected;
			}

			$lang_year = lang('year');

			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			for ( $i = $start_year; $i <= $end_year; ++$i )
			{
				$slctd = $i == $selected ? ' selected="selected"' : '';
				$html .= <<<HTML
					<option value="$i"{$slctd}>{$i}</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;

			return $html;
		}

		/**
		* Get a dropdown list of percentages
		*
		* @param string $name the name and id of the html element
		* @param string $selected the currently selected option
		* @param int $interval, the spacing between options
		* @return string the html select element populated with percentage options
		*/
		public static function getPercentage($name, $selected = null, $interval = 10)
		{
			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;

			for ( $i = 0; $i <= 100; $i += $interval)
			{
				$slctd = $i == $selected ? ' selected' : '';
				$html .= <<<HTML
				<option value="{$i}"{$slctd}>$i</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		* Get a drop down list of priorities
		*
		* @param string $name the name and id of the html element
		* @param int $selected the currently selected option
		* @return string the html element populated with priority options
		*/
		public static function getPriority($name, $selected = 2)
		{
			$priorities = array('', lang('low'), lang('normal'), lang('high') );

			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;
			foreach ( $priorities as $id => $priority )
			{
				$slctd = $i == $selected ? ' selected' : '';
				$html .= <<<HTML
				<option value="{$id}"{$slctd}>$priority</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		* Get a drop down list of access level options
		*
		* @param string $name the name and id of the html element
		* @param string $selected the currently selected option
		* @return string the html element populated with priority options
		*/
		public static function getAccessList($name, $selected = 'private')
		{
			$options = array
			(
				'private'	=> lang('Private'),
				'public'	=> lang('Global public'),
				'group'		=> lang('Group public')
			);

			if ( preg_match('/,/', $selected) )
			{
				$selected = 'group';
			}

			$html = <<<HTML
			<select name="{$name}" id="{$name}">

HTML;

			foreach ( $options as $id => $option )
			{
				$slctd = $id == $selected ? ' selected' : '';
				$html .= <<<HTML
				<option value="{$id}"{$slctd}>$option</option>
HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		* Create a select box filled with countries
		*
		* @param string $selected the currently selected country
		* @param string $name the name of the select box element in the form, used for both the id and name attributes
		* @return string the html for a select box form element
		*/
		public static function country_select($selected, $name = null)
		{
			if ( is_null($name) )
			{
				$name = 'country';
			}
			$selected = trim($selected);

			$slctd = $selected ? '' : ' selected';
			$lang_select1 = lang('Select One');
			$select = <<<HTML
				<select name="{$name}">
					<option value="  "{$selected}>{$lang_select1}</option>

HTML;
			foreach ( phpgwapi_country::get_translated_list() as $code => $country )
			{
				$slctd = $code == $selected ? ' selected' : '' ;
				$select .= <<<HTML
					<option value="{$code}"{$slctd}>{$country}</option>

HTML;
			}
			$select .= <<<HTML
				</select>
HTML;
			return $select;
		}

		/*
		 * Create a generic select list
		 *
		 * @param string $name string with name of the submitted var which holds the key of the selected item form array
		 * @param array $selected key(s) of already selected item(s) from $options, eg. '1' or '1,2' or array with keys
		 * @param array	$options items to populate the <options>
		 * @param bool $no_lang by default all values are translated by calls to lang(), setting this to true disbales it
		 * @param string $attribs additional html attributed to be applied to <select>
		 * @param int $multiple the height of the <select>, if greater than 1, set to 1 to just enable multiple selections
		 * @return string the populated html select element
		 */
		public static function getArrayItem($name, $selected, $options = array(), $no_lang = false, $attribs = '', $multiple = 0 )
		{
			// should be in class common.sbox
			if ( !is_array($options) || !count($options) )
			{
				$options = array('no', 'yes');
			}

			$multiple = (int) $multiple;
			if ( $multiple )
			{
				$attribs .= " multiple ";
				if ( $multiple > 1 )
				{
					$attribs .= "size=\"{$multiple}\"";
				}

				if (substr($name,-2) != '[]')
				{
					$id = $name;
					$name .= '[]';
				}
				else
				{
					$id = substr($name, 0, -2);
				}
			}
			$html = <<<HTML
			<select name="$name" id="$id" $attribs>

HTML;

			$check = array();

			if (!is_array($selected))
			{
				$check[$selected] = true;	
			}
			else
			{
				foreach ($selected as $sel)
				{
					$check[$sel] = true;
				}
			}

			foreach ( $options as $value => $option )
			{
				$check2 = isset( $check[$value] ) ? ' selected' : '';
				$option = $no_lang ? $option : lang($option);

				$html .= <<<HTML
					<option value="{$value}"{$check2}>{$option}</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
		}

		/**
		*
		*/
		public static function get_date($n_year,$n_month,$n_day,$date,$options='')
		{
			if ( is_array($date) && count($date) == 3 )
			{
				list($year,$month,$day) = $date;
			}
			elseif (!$date)
			{
				$day = $month = $year = 0;
			}
			else
			{
				$day = date('d', $date);
				$month = date('m', $date);
				$year = date('Y', $date);
			}
			return phpgw_datetime::dateformatorder
			(
				self::getYears($n_year,$year),
				self::getMonthText($n_month,$month),
				self::getDays($n_day,$day)
			);
		}
	}
