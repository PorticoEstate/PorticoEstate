<?php
	/**
	* jQuery datepicker wrapper-class
	*
	* @author Sigurd Nes
	* @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	 * @version $Id$
	*/
	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

	/**
	* jQuery datepicker wrapper-class
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_jqcal
	{

		public $img_cal;
		public $dateformat;
		private $lang_select_date;

		function __construct()
		{
			phpgwapi_jquery::load_widget('datepicker');

			$this->img_cal			 = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$this->dateformat		 = str_ireplace(array('d', 'm', 'y'), array('dd', 'mm', 'yy'), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$this->lang_select_date      = lang('select date');
		}

		function add_listener($name, $type = 'date', $value = '', $config = array())
		{
			switch($type)
			{
				case 'datetime':
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");	
					$GLOBALS['phpgw']->js->validate_file('jquery', 'js/jquery-ui-timepicker-addon.min');
					$_type = 'datetime';
					break;
				case 'time':
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");
					$GLOBALS['phpgw']->js->validate_file('jquery', 'js/jquery-ui-timepicker-addon.min');
					$_type	 = 'time';
					break;
				default:
					$_type = 'date';
			}

			$this->_input_modern($name, $_type, $config);
			return "<input id='{$name}' type='text' value='{$value}' size='10' name='{$name}'/>";
		}

		/**
		* Add an event listener to the trigger icon - used for XSLT
		*
		* @access private
		* @param string $name the element ID
		*/
		function _input_modern($id, $type, $config = array())
		{
			$_i18n = new stdClass();
			$_i18n->monthsShort 	=
				[
				  lang('Jan'),
				  lang('Feb'),
				  lang('Mar'),
				  lang('April'),
				  lang('May'),
				  lang('Jun'),
				  lang('Jul'),
				  lang('Aug'),
				  lang('Sep'),
				  lang('Oct'),
				  lang('Nov'),
				  lang('Dec')
				];

			$i18n = json_encode($_i18n);

			$date_range_arr = array();
			$date_range = '';

			if(!empty($config['min_date']))
			{
				$date_range_arr[] = "minDate:new Date('{$config['min_date']}')";
			}

			if(!empty($config['max_date']))
			{
				$date_range_arr[] = "maxDate:new Date('{$config['max_date']}')";
			}

			if(!empty($config['no_button']))
			{
				$show_button = "";
			}
			else
			{
				$show_button  = <<<JS
					,showOn: "button",
					buttonImage: "{$this->img_cal}",
					buttonText: "{$this->lang_select_date}",
					buttonImageOnly: true
JS;
			}


			if($date_range_arr)
			{
				$date_range = ',' . implode(',', $date_range_arr);
			}
			$dateformat_materializecss		 = str_ireplace(array('yy'), array('yyyy'), $this->dateformat);
			$js = <<<JS
			$(function() {
				$( "#{$id}" ).{$type}picker({ 
					dateFormat: '{$this->dateformat}',
					format: '{$dateformat_materializecss}', // materializecss
					showClearBtn : true,// materializecss
					showWeek: true,
					changeMonth: true,
					changeYear: true,
					i18n: {$i18n},
					showButtonPanel:true
					{$show_button}
		//			showOn: "button",
		//			buttonImage: "{$this->img_cal}",
		//			buttonText: "{$this->lang_select_date}",
		//			buttonImageOnly: true
					{$date_range}
					//new Date(2018, 1 -1, 1),//Date(year, month, day, hours, minutes, seconds, milliseconds)
					//new Date(2018, 12 -1, 31)
				}).keyup(function(e) {
					if(e.keyCode == 8 || e.keyCode == 46) {
						$.datepicker._clearDate(this);
					}
				});
			    $('#ui-datepicker-div').draggable();
			});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}
	}
