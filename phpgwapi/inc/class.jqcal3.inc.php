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
	class phpgwapi_jqcal3
	{

		public $img_cal;
		public $dateformat;
		private $lang_select_date;
		private $userlang = 'en';

		function __construct()
		{
			phpgwapi_jquery::load_widget('datepicker_tui');

			$this->img_cal			 = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$this->dateformat		 = str_ireplace(array('y'), array('Y'), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$this->lang_select_date      = lang('select date');

			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) )
			{
				$this->userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

		}

		function add_listener($name, $type = 'date', $value = 0, $config = array())
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
			if(ctype_digit($value) && $value)
			{
				$start_value = date('Y/m/d H:i', $value);
			}
			else
			{
				$start_value = $dateformat == 'H:i' ? $value : '';
			}
			$this->_input_modern($name, $_type, $dateformat, $config, $start_value);
			return "<input id='{$name}' type='text' value='{$value}' size='10' name='{$name}'/>";
		}

		/**
		* Add an event listener to the trigger icon - used for XSLT
		*
		* @access private
		* @param string $name the element ID
		*/
		function _input_modern($id, $type, $dateformat, $config = array(), $start_value = '')
		{
			$format = str_ireplace(array('Y','m', 'd', 'H', 'i'), array('yyyy', 'MM', 'dd', 'HH', 'mm' ),$dateformat);

			$js = <<<JS
			$(document).ready(function()
			{
				$( '<div id="{$id}-container" style="margin-top: -1px;"></div>' ).insertAfter( "#{$id}" );
				$( "<span class=\"tui-ico-date\"></span>" ).insertAfter( "#{$id}" );

				

				var datepicker = new tui.DatePicker('#{$id}-container', {
						  date: new Date(),
						  input: {
							  element: '#{$id}',
							  format: '{$format}'
						  },
						  showAlways: false,
						  
					  });
			});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}
	}
