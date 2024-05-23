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
	class phpgwapi_jquery
	{

		/**
		 * @var int $counter the widget id counter
		 */
		private static $counter = 0;

		/**
		 * Load all the dependencies for a jQuery widget
		 *
		 * @param string $widget the name of the widget to load, such as autocomplete
		 *
		 * @return string yahoo namespace for widget - empty string on failure
		 *
		 * @internal this does not render the widget it only includes the header js files
		 */
		public static function load_widget( $widget )
		{
			$migration_test = false;

			if (preg_match("/(Trident\/(\d{2,}|7|8|9)(.*)rv:(\d{2,}))|(MSIE\ (\d{2,}|8|9)(.*)Tablet\ PC)|(Trident\/(\d{2,}|7|8|9))/", $_SERVER["HTTP_USER_AGENT"]))
			{
				$message = lang('outdated browser: %1', $_SERVER['HTTP_USER_AGENT']);
				phpgwapi_cache::message_set($message, 'error');

				$_jquery_core = 'jquery-1.11.3'; // In case we need IE 6–8 support.
			}
			else
			{
				$_jquery_core = 'jquery-3.7.1';
			}

			$_jquery_ui	 = 'jquery-ui-1.13.2';
			$_type		 = '.min'; // save some download

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$theme = 'redmond';
			}
			else
			{
				$theme = 'base';
			}
			$load = array();

			$GLOBALS['phpgw']->js->validate_file('jquery', "js/{$_jquery_core}{$_type}", 'phpgwapi', false, array('combine' => true ));

			switch ($widget)
			{
				case 'ui':
					$load = array(
						"ui/{$_jquery_ui}{$_type}",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui.min.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/theme.css");
					break;
				case 'datepicker':
					$load = array(
		//				"js/{$_jquery_core}{$_type}",
						"ui/{$_jquery_ui}{$_type}",
						"ui/i18n/datepicker-{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui.min.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/theme.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");
					break;

				case 'datetimepicker':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						'datetimepicker' => array(
							"js/jquery.datetimepicker.full{$_type}",
						//					"i18n/DateTimePicker-i18n"
						)
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/datetimepicker/css/jquery.datetimepicker.min.css");
					break;

				case 'validator':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						'form-validator' => array("jquery.form-validator{$_type}")//, "lang/{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}")
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/form-validator/theme-default.css");
					break;

				case 'menu':
				case 'autocomplete':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						"ui/{$_jquery_ui}{$_type}",
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui.min.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/theme.css");

					break;

				case 'tabview':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						//	"tabs/jquery.responsiveTabs",
						"tabs/jquery.responsiveTabs{$_type}",
		//				'common'
					);
					$GLOBALS['phpgw']->js->validate_file('jquery', "common", 'phpgwapi', false, array('combine' => true ));

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/tabs/css/responsive-tabs.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/tabs/css/style.css");

					break;
				case 'mmenu':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						"mmenu/src/js/jquery.mmenu.min.all"
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/mmenu/src/css/jquery.mmenu.all.css");

					break;

				case 'treeview':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						"treeview/jstree{$_type}"
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/treeview/themes/default/style.min.css");

					break;

				case 'jqtree':
					$load = array(
		//				"js/{$_jquery_core}{$_type}",
						"jqTree/tree.jquery",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/jqTree/jqtree.css");
					break;

				case 'numberformat':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						"number-format/jquery.number{$_type}"
					);

					break;
				case 'layout':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
		//				"ui/{$_jquery_ui}{$_type}",
		//				'layout' => array("jquery.layout{$_type}", "plugins/jquery.layout.state")
					);
					$GLOBALS['phpgw']->js->validate_file('jquery', "ui/{$_jquery_ui}{$_type}", 'phpgwapi', true, array('combine' => true ));
					$GLOBALS['phpgw']->js->validate_file('layout', "jquery.layout{$_type}", 'phpgwapi', true, array('combine' => true ));
					$GLOBALS['phpgw']->js->validate_file('layout', "plugins/jquery.layout.state", 'phpgwapi', true, array('combine' => true ));

					break;

				case 'contextMenu':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						'contextMenu' => array("jquery.contextMenu{$_type}")
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/contextMenu/jquery.contextMenu.min.css");
					break;

				case 'chart':
					$load = array
						(
						'chart' => array("chart.umd{$_type}")
					);

					break;

				case 'print':
					$load = array
						(
						"print/jQuery.print.min"
					);

					break;

				case 'file-upload':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						"ui/{$_jquery_ui}{$_type}",
						"file-upload/js/tmpl{$_type}",
						"file-upload/js/jquery.fileupload",
						"file-upload/js/jquery.fileupload-process",
						"file-upload/js/jquery.fileupload-validate",
						"file-upload/js/jquery.fileupload-ui",
						"file-upload/js/jquery.fileupload-jquery-ui",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/file-upload/css/jquery.fileupload.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/file-upload/css/jquery.fileupload-ui.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/file-upload/css/jquery.fileupload-custom.css");
					break;

				case 'file-upload-minimum':
					$load = array
						(
		//				"js/{$_jquery_core}{$_type}",
						"ui/{$_jquery_ui}{$_type}",
						"file-upload/js/jquery.fileupload",
						"file-upload/js/jquery.fileupload-process",
						"file-upload/js/jquery.fileupload-validate",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/file-upload/css/jquery.fileupload.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/file-upload/css/jquery.fileupload-ui.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/file-upload/css/jquery.fileupload-custom.css");

					break;

				case 'bootstrap-multiselect':
					$load = array(
		//				"js/{$_jquery_core}{$_type}",
						'bootstrap-multiselect' => array("js/bootstrap-multiselect.min")
					);

					if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'bootstrap')
					{
						unset($load['bootstrap-multiselect']);//to be inserted last
						$load['popper']					 = array("popper2.min");
						$load['bootstrap5']				 = array("vendor/twbs/bootstrap/dist/js/bootstrap.min");

						$load['bootstrap-multiselect']	 = array("js/bootstrap-multiselect.min");

						$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/css/bootstrap.min.css");

					}

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/bootstrap-multiselect/css/bootstrap-multiselect.min.css");

					break;
				case 'select2':
					$load = array(
		//				"js/{$_jquery_core}{$_type}",
						'select2' => array("js/select2{$_type}", "js/i18n/{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}")
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/select2/css/select2{$_type}.css");

					if (in_array($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'], array('bootstrap','bootstrap2', 'bookingfrontend', 'mobilefrontend')))
					{
//						$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/select2/css/select2-bootstrap4{$_type}.css");
						$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/select2/css/select2-bootstrap-5-theme{$_type}.css");
					}

					break;
				case 'glider':
					$load = array
						(
						'glider' => array("glider{$_type}", 'glider_init')
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/glider/glider{$_type}.css");
					break;
				case 'intl-tel-input':
					$load = array
						(
						'intl-tel-input' => array(
							"js/utils",
							"js/intlTelInput"
						)
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/intl-tel-input/css/intlTelInput.min.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/intl-tel-input/css/isValidNumber.css");

					break;

				case 'daterangepicker':
					$load = array
					(
						'daterangepicker' => array(
							"js/moment.min",
							"js/daterangepicker.min"

						)
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/daterangepicker/css/daterangepicker.css");

					break;

				case 'timepicker':
					$load = array
					(
						'timepicker' => array(
							"js/jquery.timepicker.min"
						)
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/timepicker/css/jquery.timepicker.min.css");

					break;
				default:
					$err = "Unsupported jQuery widget '%1' supplied to phpgwapi_jquery::load_widget()";
					trigger_error(lang($err, $widget), E_USER_WARNING);
					return '';
			}
			foreach ($load as $key => $scripts)
			{

				$package = 'jquery';

				if (!$key == intval($key))
				{
					$package = $key;
				}

				if (!is_array($scripts))
				{
					$scripts = array($scripts);
				}

				foreach ($scripts as $script)
				{
					$test = $GLOBALS['phpgw']->js->validate_file($package, $script);
					if (!$test)
					{
						$err = "Unable to load jQuery script '%1' when attempting to load widget: '%2'";
						trigger_error(lang($err, $script, $widget), E_USER_WARNING);
						return '';
					}
				}
			}
			if ($migration_test)
			{
				//_debug_array($_jquery_migrate);
				$GLOBALS['phpgw']->js->validate_file('jquery', "js/$_jquery_migrate");
			}

			return "phpgroupware.{$widget}" . ++self::$counter;
		}

		public static function formvalidator_generate( $modules = array(), $form_id = 'form', $errorMessagePosition_id = '' )
		{
			// keep track of number of times loaded per pageload
			static $times_loaded = 0;

			self::load_widget('validator');
			$modules_js = '"' . implode(',', $modules) . '"';

			if ($errorMessagePosition_id)
			{
				$errorMessagePosition	 = "$('#{$errorMessagePosition_id}')";
				$scrollToTopOnError		 = 'false';
			}
			else
			{
				$errorMessagePosition	 = "'top'";
				$scrollToTopOnError		 = 'true';
			}

			switch ($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
			{
				case 'no':
				case 'nn':
					$lang	 = 'no';
					break;
				case 'fr':
				case 'de':
				case 'se':
				case 'sv':
				case 'en':
				case 'pt':
					$lang	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
					break;
				default:
					$lang	 = 'en';
					break;
			}

			$js = <<<JS

			$(document).ready(function ()
			{
				$.validate({
					lang: '{$lang}', // (supported languages are fr, de, se, sv, en, pt, no)
					modules : {$modules_js},
					form: '#{$form_id}',
					validateOnBlur : false,
					scrollToTopOnError : false,
					validateHiddenInputs: true,
					errorMessagePosition : {$errorMessagePosition},
					scrollToTopOnError: {$scrollToTopOnError}
				});
			});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
			$times_loaded++;
		}

		/**
		 * Add the events required for tabs to work
		 *
		 * @param array $tabs
		 * @param string $selection active tab
		 * @param string $tab_set indentificator of tabset
		 * @return string HTML definition of the tabs
		 */
		public static function tabview_generate( $tabs, $selection, $tab_set = 'tab-content' )
		{
			self::load_widget('tabview');
			$output		 = <<<HTML
					<ul>
HTML;
			$disabled	 = array();
			$tab_map	 = array();
			$i			 = 0;
			foreach ($tabs as $id => $tab)
			{
				$tab_map[$id] = $i;

				$label		 = $tab['label'];
				$_function	 = '';
				if (isset($tab['function']))
				{
					$_function = " onclick=\"javascript: {$tab['function']};\"";
				}

				//Set disabled tabs
				//if (empty($tab['link']) && empty($tab['function'])) {
				if (!empty($tab['disable']))
				{
					$disabled[] = $i;
				}

				if ($tab['link'] && !preg_match('/(^#)/i', $tab['link']))
				{
					$_function	 = " onclick=\"javascript: window.location = '{$tab['link']}';\"";
					$tab['link'] = "#{$id}";
				}


				$output .= <<<HTML
				<li><a href="{$tab['link']}"{$_function}>{$label}</a></li>
HTML;

				$i++;
			}

			$selected = array_key_exists($selection, $tab_map) ? (int) $tab_map[$selection] : 0;

			$disabled_js = '[' . implode(',', $disabled) . ']';

			$output	 .= <<<HTML
					</ul>
HTML;
			$js		 = <<<JS
		$(document).ready(function ()
		{
			JqueryPortico.render_tabs();
		});

			JqueryPortico.render_tabs = function ()
			{
				$('#{$tab_set}').responsiveTabs({
//					startCollapsed: 'accordion',
					collapsible: 'accordion',
					rotate: false,
					disabled: {$disabled_js},
					collapsible: 'accordion',
					setHash: true,
					activate: function(e, tab) {
						if(tab.disabled == false)
						{
							var selector = tab.selector;
							var active_tab = selector.replace("#", '');
							try
							{
								set_tab(active_tab);

							}
							catch (err)
							{
								console.log(err);
							}
							try
							{
				//				$($.fn.dataTable.tables(true)).DataTable().draw();
								$.fn.dataTable.tables({
									visible: true,
									api: true
								}).columns.adjust();
							}
							catch (err)
							{
								console.log(err);
							}
						}
					}

				});
				if($selected)
				{
					$('#{$tab_set}').responsiveTabs('activate', {$selected});
				}

			};
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
			return $output;
		}

		/**
		 * @param string $target
		 */
		public static function init_ckeditor( $target )
		{
			self::load_widget('core');
			$GLOBALS['phpgw']->js->validate_file('ckeditor', 'ckeditor');
			$GLOBALS['phpgw']->js->validate_file('ckeditor', 'adapters/jquery');
			$userlang = isset($GLOBALS['phpgw_info']['server']['default_lang']) && $GLOBALS['phpgw_info']['server']['default_lang'] ? $GLOBALS['phpgw_info']['server']['default_lang'] : 'en';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']))
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}


			$js = <<<JS

		$( document ).ready( function() {

			CKEDITOR.replace('$target', {
			   language: '$userlang'
			 });

//		$( 'textarea#{$target}' ).ckeditor(
//				{
//					language: '{$userlang}',
//					resize_dir: 'both',
//					extraAllowedContent: [
//						'div(*){*}[*]',
//						'h1(*){*}[*]',
//						'h2(*){*}[*]',
//						'h3(*){*}[*]',
//						'h4(*){*}[*]',
//						'h5(*){*}[*]'
//						].join("; ")
//				}
//			);
		} );
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}

		public static function init_summernote( $target )
		{
			self::load_widget('core');

			switch ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'])
			{
				case 'bootstrap':
					$GLOBALS['phpgw']->js->validate_file('summernote', 'dist/summernote-bs5');
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/summernote/dist/summernote-bs5.css");
					break;
				default:
					$GLOBALS['phpgw']->js->validate_file('summernote', 'dist/summernote-lite');
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/summernote/dist/summernote-lite.css");
					break;
			}

			$userlang = isset($GLOBALS['phpgw_info']['server']['default_lang']) && $GLOBALS['phpgw_info']['server']['default_lang'] ? $GLOBALS['phpgw_info']['server']['default_lang'] : 'en';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']))
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

			switch ($userlang)
			{
				case 'nn':
				case 'no':
					$lang	 = 'nb-NO';
					break;
				default:
					$lang	 = 'nb-NO';
					break;
			}

			$GLOBALS['phpgw']->js->validate_file('summernote', "dist/lang/summernote-{$lang}");


			static $init = false;

			$disableDragAndDrop = '';
			if (empty($GLOBALS['phpgw_info']['flags']['allow_html_image']))
			{
				$disableDragAndDrop = "disableDragAndDrop: true,
				callbacks: {
			    onImageUpload: function (data) {
				 data.pop();
			    }
		     },
";
			}

			$lang_placeholder = lang('write here...');

			$js = '';
			if (!$init)
			{
				$js = <<<JS

				var toolbarOptions = [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ];
JS;
			}

			$js		 .= <<<JS


		$( document ).ready( function() {
			$( 'textarea#{$target}').summernote({
			  lang: '{$lang}', // default: 'en-US'
			  {$disableDragAndDrop}
			  placeholder: '{$lang_placeholder}',
			  height: 250,
			  toolbar: toolbarOptions
			});
		});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
			$init	 = true;
		}

		public static function init_quill( $target )
		{
			/**
			 * https://github.com/tangien/quilljs-textarea
			 */
			self::load_widget('core');
			$GLOBALS['phpgw']->js->validate_file('quill', 'quill.min');
			$GLOBALS['phpgw']->js->validate_file('quill', 'quill-textarea');
			$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/quill/quill.snow.css");

			$userlang = isset($GLOBALS['phpgw_info']['server']['default_lang']) && $GLOBALS['phpgw_info']['server']['default_lang'] ? $GLOBALS['phpgw_info']['server']['default_lang'] : 'en';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']))
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

			static $init = false;

			$js = '';
			if (!$init)
			{
				$js = <<<JS
			var quill = {};
			var toolbarOptions = [
			  ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
			  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
			  [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
			  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
			  [{ 'align': [] }],
			  ['clean']                                         // remove formatting button
			];

JS;
			}


			$js .= <<<JS

		$( document ).ready( function() {

			var editors = quilljs_textarea('textarea#{$target}', {
				modules: {
				   toolbar: toolbarOptions
				 },
			    table: true,
				placeholder: 'Skriv her',
			    theme: 'snow'
			 });

			quill.$target = editors.$target;

		});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);

			$init = true;
		}

		public static function init_multi_upload_file()
		{
			self::load_widget('file-upload');
		}



		public static function init_intl_tel_input( $target )
		{
			$cache_refresh_token = time();
			if (!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
			{
				$cache_refresh_token = $GLOBALS['phpgw_info']['server']['cache_refresh_token'];
			}
			self::load_widget('intl-tel-input');

			$js = <<<JS
			$(document).ready(function ()
			{
				var input{$target} = document.querySelector("#{$target}");

				$('<span id="{$target}error-msg" class="hide"></span>').insertAfter($("#{$target}"));
				$('<span id="{$target}valid-msg" class="hide"> ✓</span>').insertAfter($("#{$target}"));

				{$target}errorMsg = document.querySelector("#{$target}error-msg"),
					{$target}validMsg = document.querySelector("#{$target}valid-msg");

				// here, the index maps to the error code returned from getValidationError - see readme
				var errorMap = [
					"Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"
				];

				// initialise plugin

				var iti = window.intlTelInput(input{$target}, {
					initialCountry: 'no'
					, placeholderNumberType: 'MOBILE',
					utilsScript: "../phpgwapi/js/intl-tel-input/js/utils.js?{$cache_refresh_token}" // just for formatting/placeholders etc
				});

				var {$target}reset = function ()
				{
					input{$target}.classList.remove("error");
					{$target}errorMsg.innerHTML = "";
					{$target}errorMsg.classList.add("hide");
					{$target}validMsg.classList.add("hide");
				};

				// on blur: validate
				input{$target}.addEventListener('blur', function ()
				{
					{$target}reset();
					if (input{$target}.value.trim())
					{
						if (iti.isValidNumber())
						{
							{$target}validMsg.classList.remove("hide");
							input{$target}.setCustomValidity('');
							$("#{$target}").val(iti.getNumber());
						}
						else
						{
							input{$target}.classList.add("error");
							var errorCode = iti.getValidationError();
							input{$target}.setCustomValidity(errorMap[errorCode]);
							{$target}errorMsg.innerHTML = errorMap[errorCode];
							{$target}errorMsg.classList.remove("hide");
						}
					}
				});

				// on keyup / change flag: reset
				input{$target}.addEventListener('change', {$target}reset);
				input{$target}.addEventListener('keyup', {$target}reset);
			});

JS;
			$GLOBALS['phpgw']->js->add_code('', $js);


		}
	}
