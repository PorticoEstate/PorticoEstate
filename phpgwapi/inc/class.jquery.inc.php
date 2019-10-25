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

			if (preg_match('/MSIE (6|7|8)/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$message = lang('outdated browser: %1', $_SERVER['HTTP_USER_AGENT']);
				phpgwapi_cache::message_set($message, 'error');

				$_jquery_core = 'jquery-1.11.3'; // In case we need IE 6–8 support.
	//			$_jquery_migrate = 'jquery-migrate-1.4.1.min';
			}
			else
			{
				$_jquery_core = 'jquery-3.4.1';
	//			$_jquery_migrate = 'jquery-migrate-3.0.0.min';
			}

			$_jquery_ui = 'jquery-ui-1.12.1';
			$_type = '.min'; // save some download

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$theme = 'redmond';
			}
			else
			{
				$theme = 'redmond';
			}
			$load = array();
			switch ($widget)
			{
				case 'core':
					$load = array(
						"js/{$_jquery_core}{$_type}",
					);
					break;

				case 'datepicker':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"js/{$_jquery_ui}{$_type}",
						"ui/i18n/datepicker-{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui.min.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");
					break;

				case 'datetimepicker':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
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
						"js/{$_jquery_core}{$_type}",
						"validator/jquery.form-validator{$_type}"
//					"validator/jquery.form-validator"
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/validator/theme-default.css");
					break;

				case 'menu':
				case 'autocomplete':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"js/{$_jquery_ui}{$_type}",
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui.min.css");

					break;

				case 'tabview':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						//	"tabs/jquery.responsiveTabs{$_type}",
						"tabs/jquery.responsiveTabs{$_type}",
						'common'
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/tabs/css/responsive-tabs.css");
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/tabs/css/style.css");

					break;
				case 'mmenu':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"mmenu/src/js/jquery.mmenu.min.all"
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/mmenu/src/css/jquery.mmenu.all.css");

					break;

				case 'treeview':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"treeview/jstree{$_type}"
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/treeview/themes/default/style.min.css");

					break;

				case 'jqtree':
					$load = array(
						"js/{$_jquery_core}{$_type}",
						"jqTree/tree.jquery",
					);
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/jqTree/jqtree.css");
					break;

				case 'numberformat':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"number-format/jquery.number{$_type}"
					);

					break;
				case 'layout':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"js/{$_jquery_ui}{$_type}",
						'layout' => array("jquery.layout{$_type}", "plugins/jquery.layout.state")
					);
					break;

				case 'contextMenu':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						'contextMenu' => array("jquery.contextMenu{$_type}")
					);
						$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/contextMenu/jquery.contextMenu.min.css");
					break;
				
				case 'chart':
					$load = array
						(
						'chart' => array("Chart{$_type}")
					);

					break;
				
				case 'print':
					$load = array
						(
						"print/jQuery.print"
					);

					break;
				
				case 'file-upload':
					$load = array
						(
						"js/{$_jquery_core}{$_type}",
						"js/{$_jquery_ui}{$_type}",
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

				case 'bootstrap-multiselect':
					$load = array(
						"js/{$_jquery_core}{$_type}",
						'bootstrap-multiselect' => array("js/bootstrap-multiselect")
					);

					if($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'bootstrap' )
					{
						unset($load['bootstrap-multiselect']);//to be inserted last
						$load['popper'] = array("popper{$_type}");
						$load['bootstrap'] = array("js/bootstrap{$_type}");
						$load['bootstrap-multiselect'] = array("js/bootstrap-multiselect");

						$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/bootstrap/css/bootstrap.min.css");
					}

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/bootstrap-multiselect/css/bootstrap-multiselect.css");

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
			if($migration_test)
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
				$errorMessagePosition = "$('#{$errorMessagePosition_id}')";
				$scrollToTopOnError = 'false';
			}
			else
			{
				$errorMessagePosition = "'top'";
				$scrollToTopOnError = 'true';
			}

			switch ($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
			{
				case 'no':
				case 'nn':
					$lang = 'no';
					break;
				case 'fr':
				case 'de':
				case 'se':
				case 'sv':
				case 'en':
				case 'pt':
					$lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
					break;
				default:
					$lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
					break;
			}

			$translation = '';
			if (!$times_loaded)//first time only
			{
				//TODO: use translations from the package
				if ($lang == 'no')
				{
					$translation = <<<JS

				var validateLanguage = {
					errorTitle: 'innsending av skjema mislyktes!',
					requiredField: 'Dette er et obligatorisk felt',
					requiredFields: 'Du har ikke svart på alle obligatoriske felter',
					badTime: 'Du har ikke angitt en gyldig tid',
					badEmail: 'Du har ikke angitt en gyldig e-postadresse',
					badTelephone: 'Du har ikke angitt et gyldig telefonnummer',
					badSecurityAnswer: 'Du har ikke gitt korrekt svar på sikkerhetsspørsmålet',
					badDate: 'Du har ikke gitt en gyldig dato',
					lengthBadStart: 'Inputverdien må være mellom ',
					lengthBadEnd: ' karakterer',
					lengthTooLongStart: 'Inputverdien er lengre enn ',
					lengthTooShortStart: 'Inputverdien er kortere enn ',
					notConfirmed: 'Inputverdiene kunne ikke bekreftes',
					badDomain: 'Feilaktig domene verdi',
					badUrl: 'Inputverdiene er ikke et riktig nettadresse',
					badCustomVal: 'Inputverdien er feil',
					andSpaces: ' og mellomrom ',
					badInt: 'Du har ikke angitt et tall',
					badSecurityNumber: 'Personnummeret validerer ikke',
					badUKVatAnswer: 'Feil britisk moms-kode',
					badStrength: 'Passordet er ikke sterk nok',
					badNumberOfSelectedOptionsStart: 'Du må velge minst ',
					badNumberOfSelectedOptionsEnd: ' svar',
					badAlphaNumeric: 'Inputverdiene kan bare inneholde alfanumeriske tegn ',
					badAlphaNumericExtra: ' og ',
					wrongFileSize: 'Filen du prøver å laste opp er for stor (max %s)',
					wrongFileType: 'Bare filer av type %s er mulig',
					groupCheckedRangeStart: 'Vennligst velg mellom ',
					groupCheckedTooFewStart: 'Vennligst velg minst ',
					groupCheckedTooManyStart: 'Vennligst velg maksimum ',
					groupCheckedEnd: ' alternativ',
					badCreditCard: 'Kredittkortnummeret er ikke gyldig',
					badCVV: 'CVV-nummer ikke var gyldig',
					wrongFileDim : 'Feil bildedimensjoner,',
					imageTooTall : 'bildet kan ikke være høyere enn',
					imageTooWide : 'bildet kan ikke være bredere enn',
					imageTooSmall : 'bildet var for liten',
					min : 'minimum',
					max : 'maximum',
					imageRatioNotAccepted : 'Bildeforholdet kan ikke aksepteres',
					badBrazilTelephoneAnswer: 'Telefonnummeret er ugyldig',
					badBrazilCEPAnswer: 'CEP er ugyldig',
					badBrazilCPFAnswer: 'CPF er ugyldig'
				   };
JS;
				}
				else
				{

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
			}

			$js = <<<JS

			{$translation}

			$(document).ready(function () 
			{
				$.validate({
					lang: '{$lang}', // (supported languages are fr, de, se, sv, en, pt, no)
					modules : {$modules_js},
					form: '#{$form_id}',
					validateOnBlur : false,
					scrollToTopOnError : false,
			//		validateHiddenInputs: true,
					errorMessagePosition : {$errorMessagePosition},
					scrollToTopOnError: {$scrollToTopOnError}
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
		public static function tabview_generate( $tabs, $selection, $tab_set = 'tab-content' )
		{
			self::load_widget('tabview');
			$output = <<<HTML
					<ul>
HTML;
			$disabled = array();
			$tab_map = array();
			$i = 0;
			foreach ($tabs as $id => $tab)
			{
				$tab_map[$id] = $i;

				$label = $tab['label'];
				$_function = '';
				if (isset($tab['function']))
				{
					$_function = " onclick=\"javascript: {$tab['function']};\"";
				}

				//Set disabled tabs
				//if (empty($tab['link']) && empty($tab['function'])) {
				if ($tab['disable'] == 1)
				{
					$disabled[] = $i;
				}

				if ($tab['link'] && !preg_match('/(^#)/i', $tab['link']))
				{
					$_function = " onclick=\"javascript: window.location = '{$tab['link']}';\"";
					$tab['link'] = "#{$id}";
				}


				$output .= <<<HTML
				<li><a href="{$tab['link']}"{$_function}>{$label}</a></li>
HTML;

				$i++;
			}

			$selected = array_key_exists($selection, $tab_map) ? (int)$tab_map[$selection] : 0;

			$disabled_js = '[' . implode(',', $disabled) . ']';

			$output .= <<<HTML
					</ul>
HTML;
			$js = <<<JS
		$(document).ready(function ()
		{
			JqueryPortico.render_tabs();
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
								//nothing
							}
						}
					}

				});
				$('#{$tab_set}').responsiveTabs('activate', {$selected});

			};
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
			return $output;
		}

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
			$( 'textarea#{$target}' ).ckeditor(
				{
					uiColor: '#9AB8F3',
					language: '{$userlang}',
					resize_dir: 'both',
					extraAllowedContent: [
						'div(*){*}[*]',
						'h1(*){*}[*]',
						'h2(*){*}[*]',
						'h3(*){*}[*]',
						'h4(*){*}[*]',
						'h5(*){*}[*]'
						].join("; ")
				}
			);
		} );
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}
		
		public static function init_multi_upload_file()
		{
			self::load_widget('file-upload');
		}
		
		/*
		public static function form_file_upload_generate( $action )
		{
			self::load_widget('file-upload');
			$output = <<<HTML
			<form id="fileupload" action="{$action}" method="POST" enctype="multipart/form-data">
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="fileupload-buttonbar">
					<div class="fileupload-buttons">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<span class="fileinput-button pure-button">
							<span>Add files...</span>
							<input type="file" id="files" name="files[]" multiple>
						</span>
						<button type="submit" class="start pure-button">Start upload</button>
						<button type="reset" class="cancel pure-button">Cancel upload</button>
						<button type="button" class="delete pure-button">Delete</button>
						<input type="checkbox" class="toggle">
						<!-- The global file processing state -->
						<span class="fileupload-process"></span>
					</div>
					<div class="fileupload-progress fade" style="display:none">
						<!-- The global progress bar -->
						<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
						<!-- The extended global progress state -->
						<div class="progress-extended">&nbsp;</div>
					</div>
				</div>
				<!-- The table listing the files available for upload/download -->
				<div style="position: relative; overflow: auto; max-height: 50vh; width: 100%;">					
					<div class="presentation files" style="display: inline-table;"></div>
				</div>
			
			</form>

			<!-- The template to display files available for upload -->
			<script id="template-upload" type="text/x-tmpl">
			{% for (var i=0, file; file=o.files[i]; i++) { %}
				<div class="template-upload fade table-row">
					<div class="table-cell">
						<div class="name">{%=file.name%}</div>
						<div class="error"></div>
					</div>
					<div class="table-cell">
						<div class="size">Processing...</div>
					</div>
					<div class="table-cell">
						<div class="progress" style="width: 100px;"></div>
					</div>
					<div class="table-cell">
						{% if (!i && !o.options.autoUpload) { %}
							<button class="start pure-button" disabled>Start</button>
						{% } %}
						{% if (!i) { %}
							<button class="cancel pure-button">Cancel</button>
						{% } %}
					</div>
				</div>
			{% } %}
			</script>
			<!-- The template to display files available for download -->
			<script id="template-download" type="text/x-tmpl">
			{% for (var i=0, file; file=o.files[i]; i++) { %}
				<div class="template-download fade table-row">
					<div class="table-cell">						
						<div class="name">
							<!--<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>-->
							{%=file.name%}							
						</div>
						{% if (file.error) { %} <div class="error">Error: {%=file.error%} </div>{% } %}
					</div>
					<div class="table-cell">
						<div class="size">{%=o.formatFileSize(file.size)%}</div>
					</div>
					<div class="table-cell">
						<button class="delete pure-button" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>Delete</button>
						<input type="checkbox" name="delete" value="1" class="toggle">
					</div>
				</div>
			{% } %}
			</script>
HTML;
			
			$js = <<<JS
					
		$(function () {
			'use strict';
					
			// Initialize the jQuery File Upload widget:
			$('#fileupload').fileupload({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: '{$action}',
				limitConcurrentUploads: 4,
				//acceptFileTypes: /(\.|\/)(png|pdf)$/i
			});
				
			// Enable iframe cross-domain access via redirect option:
			$('#fileupload').fileupload(
				'option',
				'redirect',
				window.location.href.replace(
					/\/[^\/]*$/,
					'/cors/result.html?%s'
				)
			);
				
			// Load existing files:
			$('#fileupload').addClass('fileupload-processing');
			$.ajax({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: $('#fileupload').fileupload('option', 'url'),
				dataType: 'json',
				context: $('#fileupload')[0]
			}).always(function () {
				$(this).removeClass('fileupload-processing');
			}).done(function (result) {
				$(this).fileupload('option', 'done')
					.call(this, $.Event('done'), {result: result});
			});

		});
JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
			
			return $output;
		}*/
		
	}