<?php
	/**
	* YUI - Calendar wrapper-class
	*
	* @author Sigurd Nes
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
	phpgw::import_class('phpgwapi.datetime');
	/**
	* jsCalendar wrapper-class
	*
	* @package phpgwapi
	* @subpackage gui
	*/

	class phpgwapi_yuical
	{

		protected $fields = array();

		function __construct()
		{
			$GLOBALS['phpgw']->js->validate_file( 'core', 'formatdate', 'phpgwapi' );
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/calendar/assets/skins/sam/calendar.css');
			$namespace = phpgwapi_yui::load_widget('calendar');
			$this->init($namespace);
		}
		
		protected function init($namespace)
		{
			if ( !$title )
			{
				$title = 'Select a date';
			}
			$title = lang($title);

			$date_format =& $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$datels = self::_get_translated_dates();

			$code = <<<JS
			// CALENDAR LOGIC

			function onClickOnInput(event)
			{
				this.align();
				this.show();
			}

			function closeCalender(event)
			{
				YAHOO.util.Event.stopEvent(event);
				this.hide();
			}

			function clearCalendar(event)
			{
				this.clear();
				document.getElementById(this.inputFieldID).value = '';
				document.getElementById(this.hiddenField).value = '';
			}

			function initCalendar(inputFieldID, divContainerID, calendarBodyId, calendarTitle, closeButton,clearButton,hiddenField,noPostOnSelect)
			{
				var overlay = new YAHOO.widget.Dialog(
					divContainerID, 
					{	visible: false,
						close: true
					}
				);	
						
				var cal = new YAHOO.widget.Calendar(
					"calendar",
					calendarBodyId,
					{ 	
						navigator:true, 
						title: '{$title}',
			//			close:true,
						start_weekday:1, 
						LOCALE_WEEKDAYS:"short"
					}
				);
	
				cal.cfg.setProperty('MONTHS_LONG',['{$datels['monthl'][1]}', '{$datels['monthl'][2]}', '{$datels['monthl'][3]}', '{$datels['monthl'][4]}', '{$datels['monthl'][5]}', '{$datels['monthl'][6]}', '{$datels['monthl'][7]}', '{$datels['monthl'][8]}', '{$datels['monthl'][9]}', '{$datels['monthl'][10]}', '{$datels['monthl'][11]}', '{$datels['monthl'][12]}']);
				cal.cfg.setProperty('WEEKDAYS_SHORT', ['{$datels['days'][7]}', '{$datels['days'][1]}', '{$datels['days'][2]}', '{$datels['days'][3]}', '{$datels['days'][4]}', '{$datels['days'][5]}', '{$datels['days'][6]}']);
				cal.cfg.setProperty('MONTHS_SHORT',   ['{$datels['months'][1]}', '{$datels['months'][2]}', '{$datels['months'][3]}', '{$datels['months'][4]}', '{$datels['months'][5]}', '{$datels['months'][6]}', '{$datels['months'][7]}', '{$datels['months'][8]}', '{$datels['months'][9]}', '{$datels['months'][10]}', '{$datels['months'][11]}', '{$datels['months'][12]}']);
				cal.cfg.setProperty('WEEKDAYS_1CHAR', ['{$datels['day1'][7]}', '{$datels['day1'][1]}', '{$datels['day1'][2]}', '{$datels['day1'][3]}', '{$datels['day1'][4]}', '{$datels['day1'][5]}', '{$datels['day1'][6]}']);
				cal.cfg.setProperty('WEEKDAYS_MEDIUM',['{$datels['daym'][7]}', '{$datels['daym'][1]}', '{$datels['daym'][2]}', '{$datels['daym'][3]}', '{$datels['daym'][4]}', '{$datels['daym'][5]}', '{$datels['daym'][6]}']);
				cal.cfg.setProperty('WEEKDAYS_LONG',  ['{$datels['dayl'][7]}', '{$datels['dayl'][1]}', '{$datels['dayl'][2]}', '{$datels['dayl'][3]}', '{$datels['dayl'][4]}', '{$datels['dayl'][5]}', '{$datels['dayl'][6]}']);

				cal.render();
			
				cal.selectEvent.subscribe(onCalendarSelect,[inputFieldID,overlay,hiddenField,noPostOnSelect],false);
				cal.inputFieldID = inputFieldID;
				cal.hiddenField = hiddenField;
				
				YAHOO.util.Event.addListener(closeButton,'click',closeCalender,overlay,true);
				YAHOO.util.Event.addListener(clearButton,'click',clearCalendar,cal,true);
				YAHOO.util.Event.addListener(inputFieldID,'click',onClickOnInput,overlay,true);
			
				return cal;
			}

			function onCalendarSelect(type,args,array){
				var firstDate = args[0][0];
				var month = firstDate[1] + "";
				var day = firstDate[2] + "";
				var year = firstDate[0] + "";
				var date = month + "/" + day + "/" + year;
				var hiddenDateField = document.getElementById(array[2]);
				if(hiddenDateField != null)
				{
					if(month < 10)
					{
						month = '0' + month;
					}
					if(day < 10)
					{
						day = '0' + day;
					}
					hiddenDateField.value = year + '-' + month + '-' + day;
				}
				document.getElementById(array[0]).value = formatDate('{$date_format}',Math.round(Date.parse(date)/1000));
				array[1].hide();
				if (array[3] != undefined && !array[3]) {
					document.getElementById('ctrl_search_button').click();
				}	
			}
			
			/**
			 * Update the selected calendar date with a date from an input field
			 * Input field value must be of the format YYYY-MM-DD
			 */
			function updateCalFromInput(cal, inputId) {
				var txtDate1 = document.getElementById(inputId);
			
				if (txtDate1.value != "") {
			
					var date_elements = txtDate1.value.split('-');
					var year = date_elements[0];
					var month = date_elements[1];
					var day = date_elements[2];
					
					cal.select(month + "/" + day + "/" + year);
					var selectedDates = cal.getSelectedDates();
					if (selectedDates.length > 0) {
						var firstDate = selectedDates[0];
						cal.cfg.setProperty("pagedate", (firstDate.getMonth()+1) + "/" + firstDate.getFullYear());
						cal.render();
					}
				}
			}
JS;
			$GLOBALS['phpgw']->js->add_code($namespace, $code);
		}

		public function add_listener($name, $date = '', $title = '')
		{
			$this->fields[] = $name;
			$date2 = '';
			if($date)
			{
	 			$date2 = phpgwapi_datetime::convertDate($date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],'Y-m-d');
			}
				
			$title = $title ? $title : $name;
			$img = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$alt = lang('date selector trigger');

			$lang_close = lang('close');
			$lang_clear = lang('clear');
			$html .= <<<HTML
				<input type="text" name="{$name}" id="{$name}" size="10" value="$value" readonly="true" />
			<!--	<img src="$img" alt="$alt" id="{$name}_img" title="$name"> -->
				<input type="hidden" name="{$name}_hidden" id="{$name}_hidden" value="{$date2}"/>
				<div id="calendar{$name}">
					<div id="calendar{$name}_body"></div>
					<div class="calheader">
				-		<button id="calendar{$name}CloseButton">{$lang_close}</button>
						<button id="calendar{$name}ClearButton">{$lang_clear}</button>
					</div>
				</div>
HTML;
			return $html;
		}

		/**
		* Used for generating the list date fields to be included in the head of a page
		*
		* NOTE: This method should only be called by the template class.
		*
		* @returns string the js needed for interacting with the yui-calendar
		*/

		public function get_script()
		{
			//Initiate calendar for changing status date
			if($this->fields)
			{
				$title = 'Select a date';
				$code = <<<JS
				YAHOO.util.Event.onDOMReady(
				function()
				{
JS;
				foreach ($this->fields as $field)
				{
					$code .= <<<JS

					cal_{$field} = initCalendar(
						'{$field}', 
						'calendar{$field}', 
						'calendar{$field}_body', 
						'{$title}', 
						'calendar{$field}CloseButton',
						'calendar{$field}ClearButton',
						'{$field}_hidden',
						true
					);
					updateCalFromInput(cal_{$field}, '{$field}_hidden');
JS;
			}

			$code .= <<<JS
				}
			);
JS;
			}
			return $code;
		}



		protected static function _get_translated_dates()
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
	}
