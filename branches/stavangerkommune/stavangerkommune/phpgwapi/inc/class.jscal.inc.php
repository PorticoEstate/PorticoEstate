<?php
	/**
	* jsCalendar wrapper-class
	*
	* @author Dave Hall
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* Import the YUI class
	*/
	phpgw::import_class('phpgwapi.yui');

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
			$date_format =& $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(!$date)
			{
				$date = $GLOBALS['phpgw']->common->show_date(time(),$date_format);
			}
			
			if ( !$title )
			{
				$title = 'Select a date';
			}
			$title = lang($title);
			$datels = self::_get_translated_dates();
			$date_pos = self::_get_date_pos($date_format);

			$range_sep = '-';
			switch ( substr($date_format, 1, 1) )
			{
				case '-':
					$range_sep = '--';
					$delim = '-';

				case '.':
					$delim = '.';
					break;

				case '/':
				default:
					$delim = '/';
					break;
			}
			

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/calendar/assets/skins/sam/calendar.css');
			$date_selected = $date;//date(str_replace('M', 'm', $date_format), $date);

			$namespace = phpgwapi_yui::load_widget('calendar');
			$code = <<<JS
			YAHOO.namespace('{$namespace}.{$id}');

			YAHOO.$namespace.$id.init = function()
			{
				YAHOO.$namespace.$id = new YAHOO.widget.Calendar(
					'{$id}-cal',
					'{$id}-container',
					{
						navigator:true, 
						title:'{$title}',
						close:true,
						start_weekday:1, 
						LOCALE_WEEKDAYS:"short"
					}
				);

				YAHOO.$namespace.$id.cfg.setProperty('DATE_FIELD_DELIMITER', '.');

				YAHOO.$namespace.$id.cfg.setProperty('MDY_DAY_POSITION', {$date_pos['d']});
				YAHOO.$namespace.$id.cfg.setProperty('MDY_MONTH_POSITION', {$date_pos['m']});
				YAHOO.$namespace.$id.cfg.setProperty('MDY_YEAR_POSITION', {$date_pos['y']});

				YAHOO.$namespace.$id.cfg.setProperty('MD_DAY_POSITION', {$date_pos['d']});
				YAHOO.$namespace.$id.cfg.setProperty('MD_MONTH_POSITION', {$date_pos['m']});

				YAHOO.$namespace.$id.cfg.setProperty('MONTHS_SHORT',   ['{$datels['months'][1]}', '{$datels['months'][2]}', '{$datels['months'][3]}', '{$datels['months'][4]}', '{$datels['months'][5]}', '{$datels['months'][6]}', '{$datels['months'][7]}', '{$datels['months'][8]}', '{$datels['months'][9]}', '{$datels['months'][10]}', '{$datels['months'][11]}', '{$datels['months'][12]}']);
				YAHOO.$namespace.$id.cfg.setProperty('MONTHS_LONG',    ['{$datels['monthl'][1]}', '{$datels['monthl'][2]}', '{$datels['monthl'][3]}', '{$datels['monthl'][4]}', '{$datels['monthl'][5]}', '{$datels['monthl'][6]}', '{$datels['monthl'][7]}', '{$datels['monthl'][8]}', '{$datels['monthl'][9]}', '{$datels['monthl'][10]}', '{$datels['monthl'][11]}', '{$datels['monthl'][12]}']);
				YAHOO.$namespace.$id.cfg.setProperty('WEEKDAYS_1CHAR', ['{$datels['day1'][7]}', '{$datels['day1'][1]}', '{$datels['day1'][2]}', '{$datels['day1'][3]}', '{$datels['day1'][4]}', '{$datels['day1'][5]}', '{$datels['day1'][6]}']);
				YAHOO.$namespace.$id.cfg.setProperty('WEEKDAYS_SHORT', ['{$datels['days'][7]}', '{$datels['days'][1]}', '{$datels['days'][2]}', '{$datels['days'][3]}', '{$datels['days'][4]}', '{$datels['days'][5]}', '{$datels['days'][6]}']);
				YAHOO.$namespace.$id.cfg.setProperty('WEEKDAYS_MEDIUM',['{$datels['daym'][7]}', '{$datels['daym'][1]}', '{$datels['daym'][2]}', '{$datels['daym'][3]}', '{$datels['daym'][4]}', '{$datels['daym'][5]}', '{$datels['daym'][6]}']);
				YAHOO.$namespace.$id.cfg.setProperty('WEEKDAYS_LONG',  ['{$datels['dayl'][7]}', '{$datels['dayl'][1]}', '{$datels['dayl'][2]}', '{$datels['dayl'][3]}', '{$datels['dayl'][4]}', '{$datels['dayl'][5]}', '{$datels['dayl'][6]}']);

				YAHOO.$namespace.$id.select($date_selected);
				YAHOO.$namespace.$id.render();
				YAHOO.util.Event.addListener('{$id}-trigger', 'click', YAHOO.$namespace.$id.show, YAHOO.$namespace.$id, true);
			}

			YAHOO.util.Event.onDOMReady(YAHOO.$namespace.$id.init);

JS;

			$GLOBALS['phpgw']->js->add_code($namespace, $code);

			if ( isset($GLOBALS['phpgw_info']['flags']['xslt_app'])
				&& $GLOBALS['phpgw_info']['flags']['xslt_app'])
			{
				return self::input_html($id, $namespace, $date, $format, $title);
			}
			return '';
		}

		private static function input_html($id, $namespace, $date, $format, $title)
		{
			$html = <<<HTML
			<div id="{$id}-container" class="calendar_container">

HTML;

			$date_str =  $date;//date(str_replace('M', 'm', $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']), $date);

			switch ( $format )
			{
				case 'select':
					$posies = array_flip(self::get_date_pos());
					foreach ( $posies as $pos )
					{
						switch ( $pos )
						{
							case 'd':
								$html .= phpgwapi_sbox::getDays($name, $selected = null);
						}
					}

				case 'input':
				default:
					$html .= <<<HTML
					<input type="text" id="$id" name="$id" value="$date_str" onchange="updateCalFromSelect('{$namespace}', '{$id}');">

HTML;

			}

			$img = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$alt = lang('date selector trigger');


			$html .= <<<HTML
				<img src="$img" alt="$alt" title="$title">
			</div>

HTML;

			return $html;
		}

		function add_listener($name)
		{
			$this->_input_modern($name);
		}

		/**
		 * @author ralfbecker
		 * converts the date-string back to an array with year, month, day and a timestamp
		*
		 * @param $datestr content of the inputfield generated by jscalendar::input()
		 * @param $raw key of the timestamp-field in the returned array or False of no timestamp
		 * @param $day,$month,$year keys for the array, eg. to set mday instead of day
		 */
		public static function input2date($datestr,$raw='raw',$day='day',$month='month',$year='year')
		{
			if ($datestr === '')
			{
				return False;
			}
			$fields = split('[./-]',$datestr);
			foreach(split('[./-]',$this->dateformat) as $n => $field)
			{
				$date[$field] = intval($fields[$n]);
				if($field == 'M')
				{
					for($i=1; $i <=12; $i++)
					{
						if(date('M',mktime(0,0,0,$i,1,2000)) == $fields[$n])
						{
							$date['m'] = $i;
						}
					}
				}
			}
			$ret = array(
				$year  => $date['Y'],
				$month => $date['m'],
				$day   => $date['d']
			);
			if ($raw)
			{
				$ret[$raw] = mktime(12,0,0,$date['m'],$date['d'],$date['Y']);
			}
			//echo "<p>jscalendar::input2date('$datestr','$raw',$day','$month','$year') = "; print_r($ret); echo "</p>\n";

			return $ret;
		}

		/**
		* Get the positions of the components of the date
		*/
		private static function get_date_pos()
		{
			static $positions = null;
			if ( !is_null($positions) )
			{
				return $positions;
			}

			$positions = array();

			$parts = preg_split('/\/-\./', $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			foreach ( $parts as $cnt => $part )
			{
				switch ( $part )
				{
					case 'd':
					case 'j':
						$positions['d'] = $cnt + 1;
						break;

					case 'f':
					case 'm':
					case 'M':
					case 'n':
						$positions['m'] = $cnt + 1;
						break;

					case 'y':
					case 'Y':
						$positions['y'] = $cnt + 1;
						break;
				}
			}
			return $positions;
		}

		private static function _get_translated_dates()
		{
			static $datels = null;
			if ( is_null($datels) )
			{
				$datels = array
				(
					'months'	=> array
					(
						'1'	=> lang('Jan'),
						'2'	=> lang('Feb'),
						'3'	=> lang('Mar'),
						'4'	=> lang('Apr'),
						'5'	=> lang('May'),
						'6'	=> lang('Jun'),
						'7'	=> lang('Jul'),
						'8'	=> lang('Aug'),
						'9'	=> lang('Sep'),
						'10'	=> lang('Oct'),
						'11'	=> lang('Nov'),
						'12'	=> lang('Dec')
					),
					'monthl'	=> array
					(
						'1'	=> lang('January'),
						'2'	=> lang('Febuary'),
						'3'	=> lang('March'),
						'4'	=> lang('April'),
						'5'	=> lang('May'),
						'6'	=> lang('June'),
						'7'	=> lang('July'),
						'8'	=> lang('August'),
						'9'	=> lang('September'),
						'10'	=> lang('October'),
						'11'	=> lang('November'),
						'12'	=> lang('December')
					),
					'days'	=> array
					(
						'1'	=> lang('Su'),
						'2'	=> lang('Mo'),
						'3'	=> lang('Tu'),
						'4'	=> lang('We'),
						'5'	=> lang('Th'),
						'6'	=> lang('Fr'),
						'7'	=> lang('Sa')
					),
					'daym'	=> array
					(
						'1'	=> lang('Sun'),
						'2'	=> lang('Mon'),
						'3'	=> lang('Tue'),
						'4'	=> lang('Wed'),
						'5'	=> lang('Thu'),
						'6'	=> lang('Fri'),
						'7'	=> lang('Sat')
					),
					'dayl'	=> array
					(
						'1'	=> lang('Sunday'),
						'2'	=> lang('Monday'),
						'3'	=> lang('Tuesday'),
						'4'	=> lang('Wednesday'),
						'5'	=> lang('Thursday'),
						'6'	=> lang('Friday'),
						'7'	=> lang('Saturday')
					)
				);

				foreach ( $datels['dayl'] as $day => $native )
				{
					$datels['day1'][$day] = substr($native, 0, 1);
				}
			}
			return $datels;
		}


		private static function _get_date_pos($dateformat)
		{
			$dateformat = strtolower($dateformat);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'y';
			$dlarr[strpos($dateformat,'m')] = 'm';
			$dlarr[strpos($dateformat,'d')] = 'd';
			ksort($dlarr);
			$i = 1;
			$date_pos = array();
			foreach ($dlarr as $entry)
			{
				$date_pos[$entry] = $i;
				$i++;
			}
			return $date_pos;
		}

		/**
		* Add an event listener to the trigger icon - used for XSLT
		*
		* @access private
		* @param string $name the element ID
		*/
		function _input_modern($id)
		{
			$GLOBALS['phpgw']->js->add_event('load', "Calendar.setup({inputField : '$id', button : '{$id}-trigger'});");
		}

	}
