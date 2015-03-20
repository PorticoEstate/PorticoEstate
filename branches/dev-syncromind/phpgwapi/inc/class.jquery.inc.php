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

	public static function formvalidator_generate($modules = array()) {
		self::load_widget('validator');
		$modules_js = '"' . implode(',', $modules) . '"';!
		$div = "'".'#error-message-wrapper'."'";
		$messages = '$'.'('.$div.')';
		
		$js = <<<JS
                            
			$(document).ready(function () 
			{
				            $.validate({
                                modules : $modules_js,
								errorMessagePosition : $messages,
                                form: '#form',
								validateOnBlur : false,
								scrollToTopOnError : false,
								errorMessagePosition : 'top'
                            });
			});
JS;
		$GLOBALS['phpgw']->js->add_code('', $js);
		return $output;
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
		$selected = in_array($selection, $tab_map) ? $tab_map[$selection] : 0;

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

}