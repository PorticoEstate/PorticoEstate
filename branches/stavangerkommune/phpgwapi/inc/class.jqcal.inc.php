<?php
	/**
	* jQuery datepicker wrapper-class
	*
	* @author Sigurd Nes
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: class.jqcal.inc.php 10127 2012-10-07 17:06:01Z sigurdne $
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

			$theme = 'ui-lightness';
			$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui-1.8.19.custom.css");
			$this->img_cal = $GLOBALS['phpgw']->common->image('phpgwapi','cal');
			$this->dateformat = str_ireplace(array('d', 'm', 'y'), array('dd', 'mm', 'yy'),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$this->lang_select_date      = lang('select date');

		}

		function add_listener($name, $type = 'date')
		{
			switch($type)
			{
				case 'datetime':
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");	
					$GLOBALS['phpgw']->js->validate_file('jquery', 'js/jquery-ui-timepicker-addon');
					$_type = 'datetime';
					break;
				default:
					$_type = 'date';
			}

			$this->_input_modern($name, $_type);
		}

		/**
		* Add an event listener to the trigger icon - used for XSLT
		*
		* @access private
		* @param string $name the element ID
		*/
		function _input_modern($id, $type)
		{
			$js = <<<JS
			$(function() {
				$( "#{$id}" ).{$type}picker({ 
					dateFormat: '{$this->dateformat}',
					showWeek: true,
					changeMonth: true,
					changeYear: true,
					showOn: "button",
					showButtonPanel:true,
					buttonImage: "{$this->img_cal}",
					buttonText: "{$this->lang_select_date}",
					buttonImageOnly: true
				});
			    $('#ui-datepicker-div').draggable();
			});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}
	}
