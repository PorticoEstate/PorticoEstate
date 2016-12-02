<?php
	/**
	* jQuery datepicker wrapper-class
	*
	* @author Sigurd Nes
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	 * @version $Id: class.jqcal.inc.php 15194 2016-05-24 13:10:40Z sigurdne $
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
	class phpgwapi_jqcal2
	{

		public $img_cal;
		public $dateformat;
		private $lang_select_date;
		private $userlang = 'en';

		function __construct()
		{
			phpgwapi_jquery::load_widget('datetimepicker');

			$this->img_cal			 = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$this->dateformat		 = str_ireplace(array('y'), array('Y'), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$this->lang_select_date      = lang('select date');

			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) )
			{
				$this->userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

		}

		function add_listener($name, $type = 'date', $value = '', $config = array())
		{
			switch($type)
			{
				case 'datetime':
					$_type = 'datetime';
					$dateformat = "{$this->dateformat} H:i";
					break;
				case 'time':
					$_type	 = 'time';
					$dateformat = "H:i";
					break;
				default:
					$_type = 'date';
					$dateformat = "{$this->dateformat}";
			}

			$this->_input_modern($name, $_type, $dateformat, $config);
			return "<input id='{$name}' type='text' value='{$value}' size='10' name='{$name}'/>";
		}

		/**
		* Add an event listener to the trigger icon - used for XSLT
		*
		* @access private
		* @param string $name the element ID
		*/
		function _input_modern($id, $type, $dateformat, $config = array())
		{
			$datepicker = $type == 'time' ? 0 : 1;
			$timepicker = $type == 'date' ? 0 : 1;
			$placeholder = str_ireplace(array('Y','m', 'd', 'H', 'i'), array('YYYY', 'MM', 'DD', 'HH', 'mm' ),$dateformat);


			if(empty($config['min_date']))
			{
				$min_date = 0;
			}
			else
			{
				$min_date = "'{$config['min_date']}'";
			}

			if(!empty($config['max_date']))
			{
				$min_date .= ",maxDate:'{$config['max_date']}'";
			}


			$js = <<<JS
			$(document).ready(function()
			{
				$( "#{$id}" ).attr('readonly', false);
				$( "#{$id}" ).attr('placeholder', '{$placeholder}');

				jQuery.datetimepicker.setLocale('{$this->userlang}');
				$( "#{$id}" ).datetimepicker(
				{
					format: '{$dateformat}',
					datepicker:{$datepicker},
					timepicker: {$timepicker},
					step: 15,
					weeks: true,
					dayOfWeekStart:1,
//					mask:true,
					startDate: new Date,
					minDate:{$min_date}
				});
			});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}
	}
