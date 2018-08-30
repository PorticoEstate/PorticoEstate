<?php
	/**
	 * phpGroupWare
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package phpgwapi
	 * @subpackage utilities
	 * @version $Id: class.uicommon.inc.php 11988 2014-05-23 13:26:30Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.jquery');

	abstract class phpgwapi_uicommon_jquery
	{

		const UI_SESSION_FLASH = 'flash_msgs';

		protected
			$filesArray,
			$ui_session_key,
			$flash_msgs;
		public $dateFormat;
		public $type_of_user;

		//	public $flash_msgs;

		public function __construct( $currentapp = '', $yui = '' )
		{

			$yui = isset($yui) && $yui == 'yui3' ? 'yui3' : 'yahoo';
			$currentapp = $currentapp ? $currentapp : $GLOBALS['phpgw_info']['flags']['currentapp'];


			$this->tmpl_search_path = array();
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $currentapp . '/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $currentapp . '/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);

			if ($yui == 'yui3')
			{
				self::add_javascript('phpgwapi', 'yui3', 'yui/yui-min.js');
				self::add_javascript('phpgwapi', $yui, 'common.js');
			}

			$this->url_prefix = str_replace('_', '.', get_class($this));

			$this->dateFormat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->locations = & $GLOBALS['phpgw']->locations;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($currentapp);

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('contextMenu');
			self::add_javascript('phpgwapi', "jquery", 'common.js');

			self::add_javascript('phpgwapi', 'DataTables', 'DataTables/js/jquery.dataTables.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'Responsive/js/dataTables.responsive.js');
			//Buttons
			self::add_javascript('phpgwapi', 'DataTables', 'Buttons/js/dataTables.buttons.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'Buttons/js/buttons.colVis.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'Buttons/js/buttons.flash.js');
			self::add_javascript('phpgwapi', 'DataTables', 'Buttons/js/buttons.html5.js');
			self::add_javascript('phpgwapi', 'jszip', 'jszip.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'Select/js/dataTables.select.min.js');
//			self::add_javascript('phpgwapi', 'DataTables', 'ColReorder/js/dataTables.ColReorder.min.js');

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');


			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/media/css/jquery.dataTables.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/Responsive/css/dataTables.responsive.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/Buttons/css/buttons.dataTables.css');

			//pop up script
			self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

			if (phpgw::get_var('nonavbar'))
			{
				//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				//	$GLOBALS['phpgw_info']['flags']['headonly']=true;
			}
		}

		private function get_ui_session_key()
		{
			return $this->ui_session_key;
		}

		protected function restore_flash_msgs()
		{
			if (($flash_msgs = $this->session_get(self::UI_SESSION_FLASH)))
			{
				if (is_array($flash_msgs))
				{
					$this->flash_msgs = $flash_msgs;
					$this->session_set(self::UI_SESSION_FLASH, array());
					return true;
				}
			}

			$this->flash_msgs = array();
			return false;
		}

		protected function store_flash_msgs()
		{
			return $this->session_set(self::UI_SESSION_FLASH, $this->flash_msgs);
		}

		protected function reset_flash_msgs()
		{
			$this->flash_msgs = array();
			$this->store_flash_msgs();
		}

		protected function session_set( $key, $data )
		{
			return phpgwapi_cache::session_set($this->get_ui_session_key(), $key, $data);
		}

		protected function session_get( $key )
		{
			return phpgwapi_cache::session_get($this->get_ui_session_key(), $key);
		}

		/**
		 * Provides a private session cache setter per ui class.
		 */
		protected function ui_session_set( $key, $data )
		{
			return $this->session_set(get_class($this) . '_' . $key, $data);
		}

		/**
		 * Provides a private session cache getter per ui class .
		 */
		protected function ui_session_get( $key )
		{
			return $this->session_get(get_class($this) . '_' . $key);
		}

		protected function generate_secret( $length = 10 )
		{
			return substr(base64_encode(sprintf("%010d", mt_rand())), 0, $length);
		}

		public function add_js_event( $event, $js )
		{
			$GLOBALS['phpgw']->js->add_event($event, $js);
		}

		public function add_js_load_event( $js )
		{
			$this->add_js_event('load', $js);
		}

		function get_link_base()
		{
			$base = '/index.php';

			switch ($GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				case 'bookingfrontend':
					$base = '/bookingfrontend/';
					break;
				case 'activitycalendarfrontend':
					$base = '/activitycalendarfrontend/';
					break;
				case 'eventplannerfrontend':
					$base = '/eventplannerfrontend/';
					break;
				default:
					$base = '/index.php';
					break;
			}
			return $base;
		}

		public function link( $data )
		{
			$base = self::get_link_base();
			return $GLOBALS['phpgw']->link($base, $data);
		}

		public function redirect( $link_data )
		{
			$base = self::get_link_base();
			$GLOBALS['phpgw']->redirect_link($base, $link_data);
		}

		public function flash( $msg, $type = 'success' )
		{
			$this->flash_msgs[$msg] = $type == 'success';
		}

		public function flash_form_errors( $errors )
		{
			foreach ($errors as $field => $msg)
			{
				$this->flash_msgs[$msg] = false;
			}
		}

		public static function message_set( $messages = array() )
		{
			if (isset($messages['error']) && is_array($messages['error']))
			{
				foreach ($messages['error'] as $key => $entry)
				{
					phpgwapi_cache::message_set($entry['msg'], 'error');
				}
				unset($entry);
			}
			if (isset($messages['message']) && is_array($messages['message']))
			{
				foreach ($messages['message'] as $key => $entry)
				{
					phpgwapi_cache::message_set($entry['msg'], 'message');
				}
			}
		}

		public function add_stylesheet( $path )
		{
			$GLOBALS['phpgw']->css->add_external_file($path);
		}


		/**
		 *
		 * @param type $app
		 * @param type $pkg will always look within template set, then fallback to $pkg
		 * @param type $name name of the javascript file to include
		 * @return type
		 */

		public function add_javascript( $app, $pkg, $name )
		{
			return $GLOBALS['phpgw']->js->validate_file($pkg, str_replace('.js', '', $name), $app);
		}

		public function set_active_menu( $item )
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $item;
		}

		/**
		 * A more flexible version of xslttemplate.add_file
		 */
		public function add_template_file( $tmpl )
		{
			if (is_array($tmpl))
			{
				foreach ($tmpl as $t)
				{
					$this->add_template_file($t);
				}
				return;
			}
			foreach (array_reverse($this->tmpl_search_path) as $path)
			{
				$filename = $path . '/' . $tmpl . '.xsl';
				if (file_exists($filename))
				{
					$GLOBALS['phpgw']->xslttpl->xslfiles[$tmpl] = $filename;
					return;
				}
			}
			echo "Template $tmpl not found in search path: ";
			print_r($this->tmpl_search_path);
			die;
		}

		public function render_template( $output )
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			if ($this->flash_msgs)
			{
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				foreach ($msgbox_data as & $message)
				{
					echo "<div class='{$message['msgbox_class']}'>";
					echo $message['msgbox_text'];
					echo '</div>';
				}
			}
			echo htmlspecialchars_decode($output);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		/**
		 * Creates an array of translated strings.
		 */
		function lang_array()
		{
			$keys = func_get_args();
			foreach ($keys as &$key)
			{
				$key = lang($key);
			}
			return $keys;
		}

		public function add_jquery_translation( &$data )
		{
			$this->add_template_file('jquery_phpgw_i18n');
			$previous = lang('prev');
			$next = lang('next');
			$first = lang('first');
			$last = lang('last');
			$showing_items = lang('showing items');
			$of = lang('of');
			$to = lang('to');
			$shows_from = lang('shows from');
			$of_total = lang('of total');
			$sort_asc = lang(': activate to sort column ascending');
			$sort_desc = lang(': activate to sort column descending');

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$rows_per_page = 10;
			}
			$lengthmenu = array();
			for ($i = 1; $i < 4; $i++)
			{
				$lengthmenu[0][] = $i * $rows_per_page;
				$lengthmenu[1][] = $i * $rows_per_page;
			}

			if (isset($data['datatable']['allrows']) && $data['datatable']['allrows'])
			{
				$lengthmenu[0][] = -1;
				$lengthmenu[1][] = lang('all');
			}
			$data['jquery_phpgw_i18n'] = array(
				'datatable' => array(
					'emptyTable' => json_encode(lang("No data available in table")),
					'info' => json_encode(lang("Showing _START_ to _END_ of _TOTAL_ entries")),
					'infoEmpty' => json_encode(lang("Showing 0 to 0 of 0 entries")),
					'infoFiltered' => json_encode(lang("(filtered from _MAX_ total entries)")),
					'infoPostFix' => json_encode(""),
					'thousands' => json_encode(","),
					'lengthMenu' => json_encode(lang("Show _MENU_ entries")),
					'loadingRecords' => json_encode(lang("Loading...")),
					'processing' => json_encode(lang("Processing...")),
					'search' => json_encode(lang('search')),
					'zeroRecords' => json_encode(lang("No matching records found")),
					'paginate' => json_encode(array(
						'first' => $first,
						'last' => $last,
						'next' => $next,
						'previous' => $previous
					)),
					'aria' => json_encode(array(
						'sortAscending' => $sort_asc,
						'sortDescending' => $sort_desc
					)),
					'select' => json_encode(array('rows' => array('0'=> '','_'=> '%d ' . lang('rows selected'))))
				),
				'lengthmenu' => array('_' => json_encode($lengthmenu)),
				'lengthmenu_allrows' => array('_' => json_encode(array(-1, lang('all')))),
				'csv_download' => array('_' => json_encode(array(
						'show_button' => empty($GLOBALS['phpgw_info']['user']['preferences']['common']['csv_download']) ? false : true,
						'title'			=> lang('download visible data')
						)
					))

			);
//			_debug_array($data['jquery_phpgw_i18n']);die();
		}

		public function add_template_helpers()
		{
			$this->add_template_file('helpers');
		}

		public function render_template_xsl( $files, $data, $xsl_rootdir = '' , $base = 'data')
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if($xsl_rootdir)
			{
				if(!in_array($xsl_rootdir, $this->tmpl_search_path))
				{
					array_push($this->tmpl_search_path, $xsl_rootdir);
				}
			}

			if ($this->flash_msgs)
			{
				$data['msgbox_data'] = $GLOBALS['phpgw']->common->msgbox($this->flash_msgs);
			}
			else
			{
				$this->add_template_file('msgbox');
			}

			$this->reset_flash_msgs();

			$this->add_jquery_translation($data);
			$data['webserver_url'] = $GLOBALS['phpgw_info']['server']['webserver_url'];

			if (phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
			{
				echo json_encode($data);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
			$GLOBALS['phpgw']->xslttpl->set_output($output);
			$this->add_template_file($files);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array($base => $data));
		}

		// Add link key to a result array
		// Add link key to a result array
		public function _add_links( &$value, $key, $data )
		{
			$unset = 0;
			// FIXME: Fugly workaround
			// I cannot figure out why this variable isn't set, but it is needed
			// by the ->link() method, otherwise we wind up in the phpgroupware
			// errorhandler which does lot of weird things and breaks the output
			if (!isset($GLOBALS['phpgw_info']['server']['webserver_url']))
			{
				$GLOBALS['phpgw_info']['server']['webserver_url'] = "/";
				$unset = 1;
			}

			if (is_array($data))
			{
				$link_array = $data;
				$link_array['id'] = $value['id'];
			}
			else
			{
				$link_array = array('menuaction' => $data, 'id' => $value['id']);
			}

			$value['link'] = self::link($link_array);

			// FIXME: Fugly workaround
			// I kid you not my friend. There is something very wonky going on
			// in phpgroupware which I cannot figure out.
			// If this variable isn't unset() (if it wasn't set before that is)
			// then it will contain extra slashes and break URLs
			if ($unset)
			{
				unset($GLOBALS['phpgw_info']['server']['webserver_url']);
			}
		}

		// Build a YUI result style array
		public function yui_results( $results )
		{
			if (!$results)
			{
				$results['total_records'] = 0;
				$result['results'] = array();
			}

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int)$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;

			return array(
				'ResultSet' => array(
					'totalResultsAvailable' => $results['total_records'],
					'totalRecords' => $results['total_records'], // temeporary
					'recordsReturned' => count($results['results']),
					'pageSize' => $num_rows,
					'startIndex' => $results['start'],
					'sortKey' => $results['sort'],
					'sortDir' => $results['dir'],
					'Result' => $results['results'],
					'actions' => $results['actions']
				)
			);
		}

		// Build a jquery result style array
		public function jquery_results( $result = array() )
		{
			if (!$result)
			{
				$result['recordsTotal'] = 0;
				$result['recordsFiltered'] = 0;
				$result['data'] = array();
			}

			$result['recordsTotal'] = $result['total_records'];
			$result['recordsFiltered'] = $result['recordsTotal'];
			$result['data'] = (array)$result['results'];
			unset($result['results']);
			unset($result['total_records']);

			return $result;
		}

		/**
		 * Initiate rich text editor for selected targets
		 * @param array $targets
		 */
		public function rich_text_editor( $targets )
		{
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'])
				|| $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'] != 'ckeditor')
			{
				return;
			}

			if (!is_array($targets))
			{
				$targets = array($targets);
			}
			foreach ($targets as $target)
			{
				phpgwapi_jquery::init_ckeditor($target);
			}
		}

		/**
		 * Initiate rich text editor for selected targets
		 * @param array $targets
		 */
		public function use_yui_editor( $targets )
		{
			$this->rich_text_editor($targets);
		}

		public function render( $template, $local_variables = array() )
		{
			foreach ($local_variables as $name => $value)
			{
				$$name = $value;
			}

			ob_start();
			foreach (array_reverse($this->tmpl_search_path) as $path)
			{
				$filename = $path . '/' . $template;
				if (file_exists($filename))
				{
					include($filename);
					break;
				}
			}
			$output = ob_get_contents();
			ob_end_clean();
			self::render_template($output);
		}

		/**
		 * Method for JSON queries.
		 *
		 * @return YUI result
		 */
		public abstract function query();

		/**
		 * Generate javascript for the extra column definitions for a partial list
		 *
		 * @param $array_name the name of the javascript variable that contains the column definitions
		 * @param $extra_cols the list of extra columns to set
		 * @return string javascript
		 */
		public static function get_extra_column_defs( $array_name, $extra_cols = array() )
		{
			$result = "";

			foreach ($extra_cols as $col)
			{
				$literal = '{';
				$literal .= 'key: "' . $col['key'] . '",';
				$literal .= 'label: "' . $col['label'] . '"';
				if (isset($col['formatter']))
				{
					$literal .= ',formatter: ' . $col['formatter'];
				}
				if (isset($col['parser']))
				{
					$literal .= ',parser: ' . $col['parser'];
				}
				$literal .= '}';

				if ($col["index"])
				{
					$result .= "{$array_name}.splice(" . $col["index"] . ", 0," . $literal . ");";
				}
				else
				{
					$result .= "{$array_name}.push($literal);";
				}
			}

			return $result;
		}

		/**
		 * Generate javascript definitions for any editor widgets set on columns for
		 * a partial list.
		 *
		 * @param $array_name the name of the javascript variable that contains the column definitions
		 * @param $editors the list of editors, keyed by column key
		 * @return string javascript
		 */
		public static function get_column_editors( $array_name, $editors = array() )
		{
			$result = "for (var i in {$array_name}) {\n";
			$result .= "	switch ({$array_name}[i].key) {\n";
			foreach ($editors as $field => $editor)
			{
				$result .= "		case '{$field}':\n";
				$result .= "			{$array_name}[i].editor = {$editor};\n";
				$result .= "			break;\n";
			}
			$result .= " }\n";
			$result .= "}";

			return $result;
		}

		/**
		 * Returns a html-formatted error message if one is defined in the
		 * list of validation errors on the object we're given.  If no
		 * error is defined, an empty string is returned.
		 *
		 * @param $object the object to display errors for
		 * @param $field the name of the attribute to display errors for
		 * @return string a html formatted error message
		 */
		public static function get_field_error( $object, $field )
		{
			if (isset($object))
			{
				$errors = $object->get_validation_errors();

				if ($errors[$field])
				{
					return '<label class="error" for="' . $field . '">' . $errors[$field] . '</label>';
				}
				return '';
			}
		}

		public static function get_messages( $messages, $message_type )
		{
			$output = '';
			if (is_array($messages) && count($messages) > 0) // Array of messages
			{
				$output = "<div class=\"{$message_type}\">";
				foreach ($messages as $message)
				{
					$output .= "<p class=\"message\">{$message}</p>";
				}
				$output .= "</div>";
			}
			else if ($messages)
			{
				$output = "<div class=\"{$message_type}\"><p class=\"message\">{$messages}</p></div>";
			}
			return $output;
		}

		/**
		 * Returns a html-formatted error message to display on top of the page.  If
		 * no error is defined, an empty string is returned.
		 *
		 * @param $error the error to display
		 * @return string a html formatted error message
		 */
		public static function get_page_error( $errors )
		{
			return self::get_messages($errors, 'error');
		}

		/**
		 * Returns a html-formatted error message to display on top of the page.  If
		 * no error is defined, an empty string is returned.
		 *
		 * @param $error the error to display
		 * @return string a html formatted error message
		 */
		public static function get_page_warning( $warnings )
		{
			return self::get_messages($warnings, 'warning');
		}

		/**
		 * Returns a html-formatted info message to display on top of the page.  If
		 * no message is defined, an empty string is returned.
		 *
		 * @param $message the message to display
		 * @return string a html formatted info message
		 */
		public static function get_page_message( $messages )
		{
			return self::get_messages($messages, 'info');
		}

		/**
		 * Download xls, csv or similar file representation of a data table
		 */
		public function download()
		{
			$list = $this->query();
			$list = $list['ResultSet']['Result'];

			$keys = array();

			if (count($list[0]) > 0)
			{
				foreach ($list[0] as $key => $value)
				{
					if (!is_array($value))
					{
						array_push($keys, $key);
					}
				}
			}

			// Remove newlines from output
			$count = count($list);
			for ($i = 0; $i < $count; $i++)
			{
				foreach ($list[$i] as $key => &$data)
				{
					$data = str_replace(array("\n", "\r\n", "<br>"), '', $data);
				}
			}

			// Use keys as headings
			$headings = array();
			$count_keys = count($keys);
			for ($j = 0; $j < $count_keys; $j++)
			{
				array_push($headings, lang($keys[$j]));
			}

			$property_common = CreateObject('property.bocommon');
			$property_common->download($list, $keys, $headings);
		}

		/**
		 * Returns a human-readable string from a lower case and underscored word by replacing underscores
		 * with a space, and by upper-casing the initial characters.
		 *
		 * @param  string $lower_case_and_underscored_word String to make more readable.
		 *
		 * @return string Human-readable string.
		 */
		public static function humanize( $lower_case_and_underscored_word )
		{
			if (substr($lower_case_and_underscored_word, -3) === '_id')
			{
				$lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
			}

			return ucfirst(str_replace('_', ' ', $lower_case_and_underscored_word));
		}

		/**
		 * Retrieves an array of files from $_FILES
		 *
		 * @param  string $key  	A key
		 * @return array  		An associative array of files
		 */
		public function get_files( $key = null )
		{
			if (!$this->filesArray)
			{
				$this->filesArray = self::convert_file_information($_FILES);
			}

			return is_null($key) ? $this->filesArray : (isset($this->filesArray[$key]) ? $this->filesArray[$key] : array());
		}

		public function toggle_show_showall()
		{
			if (isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				unset($_SESSION['showall']);
			}
			else
			{
				$_SESSION['showall'] = "1";
			}
			$this->redirect(array('menuaction' => $this->url_prefix . '.index'));
		}

		public function toggle_show_inactive()
		{
			if (isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				unset($_SESSION['showall']);
			}
			else
			{
				$_SESSION['showall'] = "1";
			}
			$this->redirect(array('menuaction' => $this->url_prefix . '.index'));
		}

		static protected function fix_php_files_array( $data )
		{
			$fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
			$keys = array_keys($data);
			sort($keys);

			if ($fileKeys != $keys || !isset($data['name']) || !is_array($data['name']))
			{
				return $data;
			}

			$files = $data;
			foreach ($fileKeys as $k)
			{
				unset($files[$k]);
			}
			foreach (array_keys($data['name']) as $key)
			{
				$files[$key] = self::fix_php_files_array(array(
						'error' => $data['error'][$key],
						'name' => $data['name'][$key],
						'type' => $data['type'][$key],
						'tmp_name' => $data['tmp_name'][$key],
						'size' => $data['size'][$key],
				));
			}

			return $files;
		}

		/**
		 * It's safe to pass an already converted array, in which case this method just returns the original array unmodified.
		 *
		 * @param  array $taintedFiles An array representing uploaded file information
		 *
		 * @return array An array of re-ordered uploaded file information
		 */
		static public function convert_file_information( array $taintedFiles )
		{
			$files = array();
			foreach ($taintedFiles as $key => $data)
			{
				$files[$key] = self::fix_php_files_array($data);
			}

			return $files;
		}
	}