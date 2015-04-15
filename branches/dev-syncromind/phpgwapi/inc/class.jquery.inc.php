<?php

/**
 * phpGroupWare jQuery wrapper class
 *
 * @author Sigurd Nes
 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package phpgroupware
 * @subpackage phpgwapi
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
 * phpGroupWare jQuery wrapper class
 *
 * @package phpgroupware
 * @subpackage phpgwapi
 * @category gui
 */
class phpgwapi_jquery {

	/**
	 * @var int $counter the widget id counter
	 */
	private static $counter = 0;

	/**
	 * Load all the dependencies for a YUI widget
	 *
	 * @param string $widget the name of the widget to load, such as autocomplete
	 *
	 * @return string yahoo namespace for widget - empty string on failure
	 *
	 * @internal this does not render the widget it only includes the header js files
	 */
	public static function load_widget($widget) {
		$_type = '.min'; // save some download

		$load = array();
		switch ($widget) {
			case 'core':
				$load = array
					(
					//"js/jquery-1.11.1{$_type}",
					"js/jquery-2.1.1{$_type}",
//						"js/jquery-migrate-1.2.1"
				);
				break;

			case 'datepicker':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
					"js/jquery-ui-1.11.1{$_type}",
					"development-bundle/ui/i18n/jquery.ui.datepicker-{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}",
//						"js/jquery-migrate-1.2.1"
				);
				break;

			case 'validator':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
					"validator/jquery.form-validator{$_type}"
				);
				$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/validator/css/main.css");
				break;
			
			case 'menu':
			case 'autocomplete':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
					"js/jquery-ui-1.11.1{$_type}",
//						"js/jquery-migrate-1.2.1"
				);

				$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-1.11.1{$_type}.css");

				break;

			case 'tabview':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
				//	"tabs/jquery.responsiveTabs{$_type}",
					"tabs/jquery.responsiveTabs",
					'common'
				);

				$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/tabs/css/responsive-tabs.css");
				$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/tabs/css/style.css");

				break;
			case 'mmenu':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
					"mmenu/src/js/jquery.mmenu.min.all"
				);

				$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/mmenu/src/css/jquery.mmenu.all.css");

				break;

			case 'treeview':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
					"treeview/jstree{$_type}"
				);

				$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/treeview/themes/default/style.min.css");

				break;
			
			case 'numberformat':
				$load = array
					(
					"js/jquery-2.1.1{$_type}",
					"number-format/jquery.number{$_type}"
				);

				break;
			
			default:
				$err = "Unsupported YUI widget '%1' supplied to phpgwapi_yui::load_widget()";
				trigger_error(lang($err, $widget), E_USER_WARNING);
				return '';
		}

		foreach ($load as $script)
		{
			$test = $GLOBALS['phpgw']->js->validate_file('jquery', $script);

			if (!$test)
			{
				$err = "Unable to load jQuery script '%1' when attempting to load widget: '%2'";
				trigger_error(lang($err, $script, $widget), E_USER_WARNING);
				return '';
			}
		}
		return "phpgroupware.{$widget}" . ++self::$counter;
	}

	public static function formvalidator_generate($modules = array(), $form_id = 'form', $errorMessagePosition_id = '')
	{
		// keep track of number of times loaded per pageload
		static $times_loaded = 0;

		self::load_widget('validator');
		$modules_js = '"' . implode(',', $modules) . '"';

		if($errorMessagePosition_id)
		{
			$errorMessagePosition = "$('#{$errorMessagePosition_id}')";
		}
		else
		{
			$errorMessagePosition = "'top'";
		}

		$translation = '';
		if(!$times_loaded)//first time only
		{
			//TODO: add translations
			$translation = <<<JS

			var validateLanguage = {
				 errorTitle : 'Form submission failed!',
				 requiredFields : 'You have not answered all required fields',
				 badTime : 'You have not given a correct time',
				 badEmail : 'You have not given a correct e-mail address',
				 badTelephone : 'You have not given a correct phone number',
				 badSecurityAnswer : 'You have not given a correct answer to the security question',
				 badDate : 'You have not given a correct date',
				 lengthBadStart : 'You must give an answer between ',
				 lengthBadEnd : ' characters',
				 lengthTooLongStart : 'You have given an answer longer than ',
				 lengthTooShortStart : 'You have given an answer shorter than ',
				 notConfirmed : 'Values could not be confirmed',
				 badDomain : 'Incorrect domain value',
				 badUrl : 'The answer you gave was not a correct URL',
				 badCustomVal : 'You gave an incorrect answer',
				 badInt : 'The answer you gave was not a correct number',
				 badSecurityNumber : 'Your social security number was incorrect',
				 badUKVatAnswer : 'Incorrect UK VAT Number',
				 badStrength : 'The password isn\'t strong enough',
				 badNumberOfSelectedOptionsStart : 'You have to choose at least ',
				 badNumberOfSelectedOptionsEnd : ' answers',
				 badAlphaNumeric : 'The answer you gave must contain only alphanumeric characters ',
				 badAlphaNumericExtra: ' and ',
				 wrongFileSize : 'The file you are trying to upload is too large',
				 wrongFileType : 'The file you are trying to upload is of wrong type',
				 groupCheckedRangeStart : 'Please choose between ',
				 groupCheckedTooFewStart : 'Please choose at least ',
				 groupCheckedTooManyStart : 'Please choose a maximum of ',
				 groupCheckedEnd : ' item(s)'
			   };
JS;
		}

		$js = <<<JS
			{$translation}
			$(document).ready(function () 
			{
				$.validate({
					modules : {$modules_js},
					form: '#{$form_id}',
					validateOnBlur : false,
					scrollToTopOnError : false,
					errorMessagePosition : {$errorMessagePosition},
					language : validateLanguage
				});
			});
JS;
		$GLOBALS['phpgw']->js->add_code('', $js);
		$times_loaded ++;
	}

	/**
	 * Add the events required for tabs to work
	 *
	 * @param array $tabs
	 * @param string $selection active tab
	 * @param string $tab_set indentificator of tabset
	 * @return string HTML definition of the tabs
	 */
	public static function tabview_generate($tabs, $selection, $tab_set = 'tab-content')
	{
		self::load_widget('tabview');
		$output = <<<HTML
					<ul>
HTML;
		$disabled = array();
		$tab_map = array();
		$i = 0;
		foreach ($tabs as $id => $tab) {
			$tab_map[$id] = $i;

			$label = $tab['label'];
			$_function = '';
			if (isset($tab['function'])) {
				$_function = " onclick=\"javascript: {$tab['function']};\"";
			}

			//Set disabled tabs
			//if (empty($tab['link']) && empty($tab['function'])) {
			if ($tab['disable'] == 1) {
				$disabled[] = $i;
			}

			if($tab['link'] && !preg_match('/(^#)/i', $tab['link']))
			{
				  $_function =  " onclick=\"javascript: window.location = '{$tab['link']}';\"";
				  $tab['link'] = "#{$id}";
			}
			
			
			$output .= <<<HTML
				<li><a href="{$tab['link']}"{$_function}>{$label}</a></li>
HTML;

			$i++;
		}
		$selected = in_array($selection, $tab_map) ? (int)$tab_map[$selection] : 0;

		$disabled_js = '[' . implode(',', $disabled) . ']';

		$output .= <<<HTML
					</ul>
HTML;
		$js = <<<JS
		$(document).ready(function ()
		{
			if(typeof(JqueryPortico.inlineTablesDefined) == 'undefined' || JqueryPortico.inlineTablesDefined == 0)
			{
				JqueryPortico.render_tabs();
			}
		});

			JqueryPortico.render_tabs = function ()
			{
				$('#{$tab_set}').responsiveTabs({
					startCollapsed: 'accordion',
					collapsible: 'accordion',
					rotate: false,
					disabled: {$disabled_js},
					startCollapsed: 'accordion',
					collapsible: 'accordion',
					setHash: true,
					activate: function(e, tab) {
						$('.info').html('Tab <strong>' + tab.id + '</strong> activated!');
					}

				});

				$('#{$tab_set}').responsiveTabs('activate', {$selected});

			};
JS;
		$GLOBALS['phpgw']->js->add_code('', $js);
		return $output;
	}

	public static function init_ckeditor($target)
	{
		self::load_widget('core');
		$GLOBALS['phpgw']->js->validate_file('ckeditor', 'ckeditor');
		$GLOBALS['phpgw']->js->validate_file('ckeditor', 'adapters/jquery');
		$userlang = isset($GLOBALS['phpgw_info']['server']['default_lang']) && $GLOBALS['phpgw_info']['server']['default_lang']? $GLOBALS['phpgw_info']['server']['default_lang'] : 'en';
		if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) )
		{
			$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
		}


		$js = <<<JS

		$( document ).ready( function() {
			$( 'textarea#{$target}' ).ckeditor(
				{
					uiColor: '#9AB8F3',
					language: '{$userlang}'
				}
			);
		} );
JS;
		$GLOBALS['phpgw']->js->add_code('', $js);

	}

}