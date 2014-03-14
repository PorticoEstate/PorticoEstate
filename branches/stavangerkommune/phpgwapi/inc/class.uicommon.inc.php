<?php
	/**
	* phpGroupWare
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
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
 	* @version $Id: class.uicommon.inc.php 11260 2013-08-11 11:36:24Z sigurdne $
	*/	

	phpgw::import_class('phpgwapi.yui');


	abstract class phpgwapi_uicommon
	{
		const UI_SESSION_FLASH = 'flash_msgs';

		protected
			$filesArray;

/*
		protected static 
			$old_exception_handler;
*/
		private 
			$ui_session_key,
			$flash_msgs;

		public $dateFormat;

		public $type_of_user;

	//	public $flash_msgs;

		public function __construct($currentapp ='')
		{

			$currentapp = $currentapp ? $currentapp : $GLOBALS['phpgw_info']['flags']['currentapp'];

		//	self::set_active_menu('controller');
			self::add_stylesheet('phpgwapi/js/yahoo/calendar/assets/skins/sam/calendar.css');
			self::add_stylesheet('phpgwapi/js/yahoo/autocomplete/assets/skins/sam/autocomplete.css');
			self::add_stylesheet('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			self::add_stylesheet('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			self::add_stylesheet('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			self::add_stylesheet('phpgwapi/js/yahoo/treeview/assets/skins/sam/treeview.css');
			//self::add_stylesheet('controller/templates/base/css/base.css');
			$this->tmpl_search_path = array();
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $currentapp . '/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $currentapp . '/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('history');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			phpgwapi_yui::load_widget('animation');

			self::add_javascript('phpgwapi', 'yahoo', 'common.js');

			$this->url_prefix = str_replace('_', '.', get_class($this));

			$this->dateFormat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->locations = & $GLOBALS['phpgw']->locations;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($currentapp);
		}

		private function get_ui_session_key() {
			return $this->ui_session_key;
		}

		private function restore_flash_msgs() {
			if (($flash_msgs = $this->session_get(self::UI_SESSION_FLASH))) {
				if (is_array($flash_msgs)) {
					$this->flash_msgs = $flash_msgs;
					$this->session_set(self::UI_SESSION_FLASH, array());
					return true;
				}
			}

			$this->flash_msgs = array();
			return false;
		}

		private function store_flash_msgs() {
			return $this->session_set(self::UI_SESSION_FLASH, $this->flash_msgs);
		}

		private function reset_flash_msgs() {
			$this->flash_msgs = array();
			$this->store_flash_msgs();
		}

		private function session_set($key, $data) {
			return phpgwapi_cache::session_set($this->get_ui_session_key(), $key, $data);
		}

		private function session_get($key) {
			return phpgwapi_cache::session_get($this->get_ui_session_key(), $key);
		}

		/**
		 * Provides a private session cache setter per ui class.
		 */
		protected function ui_session_set($key, $data) {
			return $this->session_set(get_class($this).'_'.$key, $data);
		}

		/**
		 * Provides a private session cache getter per ui class .
		 */
		protected function ui_session_get($key) {
			return $this->session_get(get_class($this).'_'.$key);
		}

		protected function generate_secret($length = 10)
		{
			return substr(base64_encode(rand(1000000000,9999999999)),0, $length);
		}

		public function add_js_event($event, $js) {
			$GLOBALS['phpgw']->js->add_event($event, $js);
		}

		public function add_js_load_event($js) {
			$this->add_js_event('load', $js);
		}

/*
		public static function process_unauthorized_exceptions()
		{
			self::$old_exception_handler = set_exception_handler(array(__CLASS__, 'handle_unauthorized_exception'));
		}

		public static function handle_unauthorized_exception(Exception $e)
		{
			if ($e instanceof unauthorized_exception)
			{
				$message = htmlentities('HTTP/1.0 401 Unauthorized - '.$e->getMessage(), null, self::encoding());
				header($message);
				echo "<html><head><title>$message</title></head><body><strong>$message</strong></body></html>";
			} else {
				call_user_func(self::$old_exception_handler, $e);
			}
		}
*/
		public function link($data)
		{
			return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		public function redirect($link_data)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		public function flash($msg, $type='success')
		{
			$this->flash_msgs[$msg] = $type == 'success';
		}

		public function flash_form_errors($errors)
		{
			foreach($errors as $field => $msg)
			{
				$this->flash_msgs[$msg] = false;
			}
		}

		public function add_stylesheet($path)
		{
			$GLOBALS['phpgw']->css->add_external_file($path);
		}

		public function add_javascript($app, $pkg, $name)
		{
  			return $GLOBALS['phpgw']->js->validate_file($pkg, str_replace('.js', '', $name), $app);
		}

		public function set_active_menu($item)
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $item;
		}

		/**
		* A more flexible version of xslttemplate.add_file
		*/
		public function add_template_file($tmpl)
		{
			if(is_array($tmpl))
			{
				foreach($tmpl as $t)
				{
					$this->add_template_file($t);
				}
				return;
			}
			foreach(array_reverse($this->tmpl_search_path) as $path)
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

		public function render_template($output)
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			if($this->flash_msgs)
			{
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				foreach($msgbox_data as & $message)
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
			foreach($keys as &$key)
			{
				$key = lang($key);
			}
			return $keys;
		}

		public function add_yui_translation(&$data)
		{
			$this->add_template_file('yui_phpgw_i18n');
			$previous = lang('prev');
			$next = lang('next');
			$first = lang('first');
			$last = lang('last');
			$showing_items = lang('showing items');
			$of = lang('of');
			$to = lang('to');
			$shows_from = lang('shows from');
			$of_total = lang('of total');

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$rows_per_page = 10;
			}

			$data['yui_phpgw_i18n'] = array(
				'Calendar' => array(
					'WEEKDAYS_SHORT' => json_encode($this->lang_array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa')),
					'WEEKDAYS_FULL' => json_encode($this->lang_array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')),
					'MONTHS_LONG' => json_encode($this->lang_array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')),
				),
				'DataTable' => array(
					'MSG_EMPTY' => json_encode(lang('No records found.')),
					'MSG_LOADING' => json_encode(lang("Loading...")),
					'MSG_SORTASC' => json_encode(lang('Click to sort ascending')),
					'MSG_SORTDESC' => json_encode(lang('Click to sort descending')),
				),
				'setupDatePickerHelper' => array(
					'LBL_CHOOSE_DATE' => json_encode(lang('Choose a date')),
				),
				'setupPaginator' => array(
					'pageReportTemplate' => json_encode("{$showing_items} {startRecord} - {endRecord} {$of} {totalRecords}"),
					'previousPageLinkLabel' => json_encode("&lt; {$previous}"),
					'nextPageLinkLabel' => json_encode("{$next} &gt;"),
					'firstPageLinkLabel' => json_encode("&lt;&lt; {$first}"),
					'lastPageLinkLabel' => json_encode("{$last} &gt;&gt;"),
					'template' => json_encode("{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}"),
					'pageReportTemplate' => json_encode("{$shows_from} {startRecord} {$to} {endRecord} {$of_total} {totalRecords}."),
					'rowsPerPage'	=> $rows_per_page
				),
				'common' => array(
					'LBL_NAME' => json_encode(lang('Name')),
					'LBL_TIME' => json_encode(lang('Time')),
					'LBL_WEEK' => json_encode(lang('Week')),
					'LBL_RESOURCE' => json_encode(lang('Resource')),
				),
			);
		}
  

		public function add_template_helpers()
		{
			$this->add_template_file('helpers');
		}

  		public function render_template_xsl($files, $data)
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if($this->flash_msgs)
			{
				$data['msgbox_data'] = $GLOBALS['phpgw']->common->msgbox($this->flash_msgs);
			}
			else
			{
				$this->add_template_file('msgbox');
			}

			$this->reset_flash_msgs();

			$this->add_yui_translation($data);
			$data['webserver_url'] = $GLOBALS['phpgw_info']['server']['webserver_url'];

			$output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
			$GLOBALS['phpgw']->xslttpl->set_output($output);
			$this->add_template_file($files);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('data' => $data));
		}

		// Add link key to a result array
		public function _add_links(&$value, $key, $menuaction)
		{
			$unset = 0;
			// FIXME: Fugly workaround
			// I cannot figure out why this variable isn't set, but it is needed 
			// by the ->link() method, otherwise we wind up in the phpgroupware 
			// errorhandler which does lot of weird things and breaks the output
			if (!isset($GLOBALS['phpgw_info']['server']['webserver_url'])) {
				$GLOBALS['phpgw_info']['server']['webserver_url'] = "/";
				$unset = 1;
			}

			$value['link'] = self::link(array('menuaction' => $menuaction, 'id' => $value['id']));

			// FIXME: Fugly workaround
			// I kid you not my friend. There is something very wonky going on 
			// in phpgroupware which I cannot figure out.
			// If this variable isn't unset() (if it wasn't set before that is) 
			// then it will contain extra slashes and break URLs
			if ($unset) {
				unset($GLOBALS['phpgw_info']['server']['webserver_url']);
			}
		}

		// Build a YUI result style array
		public function yui_results($results)
		{ 
			if (!$results)
			{ 
				$results['total_records'] = 0;
				$result['results'] = array();
			}

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;			

			return array(   
				'ResultSet' => array(
					'totalResultsAvailable'	=> $results['total_records'],
					'totalRecords' 		=> $results['total_records'],// temeporary 
					'recordsReturned'	=> count($results['results']),
					'pageSize' 			=> $num_rows,
					'startIndex' 		=> $results['start'], 
					'sortKey' 			=> $results['sort'], 
					'sortDir' 			=> $results['dir'], 
					'Result' 			=> $results['results'],
					'actions'			=> $results['actions']
				)   
			);  
		}

		public function use_yui_editor($targets)
		{
			/*
			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');
			*/
			if(!is_array($targets))
			{
				$targets = array($targets);
			}

			$lang_font_style = lang('Font Style');
			$lang_lists = lang('Lists');
			$lang_insert_item = lang('Insert Item');
			$js = '';
			foreach ( $targets as $target )
			{
				$js .= <<<SCRIPT
			(function() {
				var Dom = YAHOO.util.Dom,
				Event = YAHOO.util.Event;

				var editorConfig = {
					toolbar:
						{buttons: [
	 						{ group: 'textstyle', label: '{$lang_font_style}',
								buttons: [
									{ type: 'push', label: 'Fet CTRL + SHIFT + B', value: 'bold' }
								]
							},
							{ type: 'separator' },
							{ group: 'indentlist', label: '{$lang_lists}',
								buttons: [
									{ type: 'push', label: 'Opprett punktliste', value: 'insertunorderedlist' },
									{ type: 'push', label: 'Opprett nummerert liste', value: 'insertorderedlist' }
								]
							},
							{ type: 'separator' },
							{ group: 'insertitem', label: '{$lang_insert_item}',
								buttons: [
									{ type: 'push', label: 'HTML Lenke CTRL + SHIFT + L', value: 'createlink', disabled: true },
									{ type: 'push', label: 'Sett inn bilde', value: 'insertimage' }
								]
							},
							{ type: 'separator' },
							{ group: 'undoredo', label: 'Angre/Gjenopprett',
								buttons: [
									{ type: 'push', label: 'Angre', value: 'undo' },
									{ type: 'push', label: 'Gjenopprett', value: 'redo' }
								]
							}
						]
					},
					height: '200px',
					width: '700px',
					animate: true,
					dompath: true,
 					handleSubmit: true
				};

				var editorWidget = new YAHOO.widget.Editor('{$target}', editorConfig);
				editorWidget.render();
			})();

SCRIPT;
			}

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/editor/assets/skins/sam/editor.css');
			phpgw::import_class('phpgwapi.yui');
			phpgwapi_yui::load_widget('editor');
			$GLOBALS['phpgw']->js->add_event('load', $js);
		}

		/**
		 * Returns formatted version of gab id. The format of the string returned
		 * is '[Cadastral unit number] / [Property unit number] / [Leasehold unit number] / [Section unit number]'.
		 * 
		 * @param $gab_id string with id to to format.
		 * @return string formatted version of the string passed to the method,
		 * or the same string if the one passed is of an incorrect format.
		 */
		public static function get_nicely_formatted_gab_id(string $gab_id)
		{
			if(strlen($gab_id) == 20)
			{
				$gab_id = substr($gab_id,4,5).' / '.substr($gab_id,9,4).' / '.substr($gab_id,13,4).' / '.substr($gab_id,17,3);
			}
			return $gab_id;
		}

		public function render($template,$local_variables = array())
		{
			foreach($local_variables as $name => $value)
			{
				$$name = $value;
	
			}

			ob_start();
			foreach(array_reverse($this->tmpl_search_path) as $path)
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
		public static function get_extra_column_defs($array_name, $extra_cols = array())
		{
			$result = "";

			foreach($extra_cols as $col){
				$literal  = '{';
				$literal .= 'key: "' . $col['key'] . '",';
				$literal .= 'label: "' . $col['label'] . '"';
				if (isset($col['formatter'])) {
					$literal .= ',formatter: ' . $col['formatter'];
				}
				if (isset($col['parser'])) {
					$literal .= ',parser: ' . $col['parser'];
				}
				$literal .= '}';

				if($col["index"]){
					$result .= "{$array_name}.splice(".$col["index"].", 0,".$literal.");";
				} else {
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
		public static function get_column_editors($array_name, $editors = array())
		{
			$result  = "for (var i in {$array_name}) {\n";
			$result .= "	switch ({$array_name}[i].key) {\n";
			foreach ($editors as $field => $editor) {
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
		public static function get_field_error($object, $field)
		{
			if(isset($object))
			{
				$errors = $object->get_validation_errors();

				if ($errors[$field]) {
					return '<label class="error" for="' . $field . '">' . $errors[$field] . '</label>';
				}
				return '';
			}
		}

		public static function get_messages($messages, $message_type)
		{
			$output = '';
			if(is_array($messages) && count($messages) > 0) // Array of messages
			{
				$output = "<div class=\"{$message_type}\">";
				foreach($messages as $message)
				{
					$output .= "<p class=\"message\">{$message}</p>";
				}
				$output .= "</div>";
			}
			else if($messages) {
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
		public static function get_page_error($errors)
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
		public static function get_page_warning($warnings)
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
		public static function get_page_message($messages)
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

			if(count($list[0]) > 0) {
				foreach($list[0] as $key => $value) {
					if(!is_array($value)) {
						array_push($keys, $key);
					}
				}
			}

			// Remove newlines from output
			$count = count($list);
			for($i = 0; $i < $count; $i++)
			{
 				foreach ($list[$i] as $key => &$data)
 				{
	 				$data = str_replace(array("\n","\r\n", "<br>"),'',$data);
 				}
			}

			 // Use keys as headings
			$headings = array();
			$count_keys = count($keys);
			for($j=0;$j<$count_keys;$j++)
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
		public static function humanize($lower_case_and_underscored_word)
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
		public function get_files($key = null)
		{
			if (!$this->filesArray)
			{
				$this->filesArray = self::convert_file_information($_FILES);
			}

			return is_null($key) ? $this->filesArray : (isset($this->filesArray[$key]) ? $this->filesArray[$key] : array());
		}

		public function toggle_show_showall()
		{
			if(isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				$this->bo->unset_show_all_objects();
			}
			else
			{
				$this->bo->show_all_objects();
			}
			$this->redirect(array('menuaction' => $this->url_prefix.'.index'));
		}

/*		
		public function use_yui_editor()
		{
			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');
		}

*/		static protected function fix_php_files_array($data)
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
					'error'    => $data['error'][$key],
					'name'     => $data['name'][$key],
					'type'     => $data['type'][$key],
					'tmp_name' => $data['tmp_name'][$key],
					'size'     => $data['size'][$key],
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
		static public function convert_file_information(array $taintedFiles)
		{
			$files = array();
			foreach ($taintedFiles as $key => $data)
			{
				$files[$key] = self::fix_php_files_array($data);
			}

			return $files;
		}
		
		protected static function get_file_type_from_extension($file, $defaultType = 'application/octet-stream')
		{
			if (false === ($extension = (false === $pos = strrpos($file, '.')) ? false : substr($file, $pos+1)))
			{
				return $defaultType;
			}
			
			if (strlen($extension) == 0)
			{
				return $defaultType;
			}
			
			switch ($extension) {
				case 'ez': 			return 'application/andrew-inset';
				case 'base64': 		return 'application/x-word';
				case 'dp': 			return 'application/commonground';
				case 'pqi': 		return 'application/cprplayer';
				case 'tsp': 		return 'application/dsptype';
				case 'xls': 		return 'application/x-msexcel';
				case 'pfr': 		return 'application/font-tdpfr';
				case 'spl': 		return 'application/x-futuresplash';
				case 'stk': 		return 'application/hyperstudio';
				case 'js': 			return 'application/x-javascript';
				case 'hqx': 		return 'application/mac-binhex40';
				case 'cpt': 		return 'application/x-mac-compactpro';
				case 'mbd': 		return 'application/mbed';
				case 'mfp': 		return 'application/mirage';
				case 'doc': 		return 'application/x-msword';
				case 'orq': 		return 'application/ocsp-request';
				case 'ors': 		return 'application/ocsp-response';
				case 'bin': 		return 'application/octet-stream';
				case 'oda': 		return 'application/oda';
				case 'ogg': 		return 'application/ogg';
				case 'pdf': 		return 'application/x-pdf';
				case '7bit': 		return 'application/pgp-keys';
				case 'sig': 		return 'application/pgp-signature';
				case 'p10': 		return 'application/pkcs10';
				case 'p7m': 		return 'application/pkcs7-mime';
				case 'p7s': 		return 'application/pkcs7-signature';
				case 'cer': 		return 'application/pkix-cert';
				case 'crl': 		return 'application/pkix-crl';
				case 'pkipath': 	return 'application/pkix-pkipath';
				case 'pki': 		return 'application/pkixcmp';
				case 'ps': 			return 'application/postscript';
				case 'shw': 		return 'application/presentations';
				case 'cw': 			return 'application/prs.cww';
				case 'rnd': 		return 'application/prs.nprend';
				case 'qrt': 		return 'application/quest';
				case 'rtf': 		return 'text/rtf';
				case 'soc': 		return 'application/sgml-open-catalog';
				case 'siv': 		return 'application/sieve';
				case 'smi': 		return 'application/smil';
				case 'tbk': 		return 'application/toolbook';
				case 'plb': 		return 'application/vnd.3gpp.pic-bw-large';
				case 'psb': 		return 'application/vnd.3gpp.pic-bw-small';
				case 'pvb': 		return 'application/vnd.3gpp.pic-bw-var';
				case 'sms': 		return 'application/vnd.3gpp.sms';
				case 'atc': 		return 'application/vnd.acucorp';
				case 'xfdf': 		return 'application/vnd.adobe.xfdf';
				case 'ami': 		return 'application/vnd.amiga.amu';
				case 'mpm': 		return 'application/vnd.blueice.multipass';
				case 'cdy': 		return 'application/vnd.cinderella';
				case 'cmc': 		return 'application/vnd.cosmocaller';
				case 'wbs': 		return 'application/vnd.criticaltools.wbs+xml';
				case 'curl': 		return 'application/vnd.curl';
				case 'rdz': 		return 'application/vnd.data-vision.rdz';
				case 'dfac': 		return 'application/vnd.dreamfactory';
				case 'fsc': 		return 'application/vnd.fsc.weblauch';
				case 'txd': 		return 'application/vnd.genomatix.tuxedo';
				case 'hbci': 		return 'application/vnd.hbci';
				case 'les': 		return 'application/vnd.hhe.lesson-player';
				case 'plt': 		return 'application/vnd.hp-hpgl';
				case 'emm': 		return 'application/vnd.ibm.electronic-media';
				case 'irm': 		return 'application/vnd.ibm.rights-management';
				case 'sc': 			return 'application/vnd.ibm.secure-container';
				case 'rcprofile': 	return 'application/vnd.ipunplugged.rcprofile';
				case 'irp': 		return 'application/vnd.irepository.package+xml';
				case 'jisp': 		return 'application/vnd.jisp';
				case 'karbon': 		return 'application/vnd.kde.karbon';
				case 'chrt': 		return 'application/vnd.kde.kchart';
				case 'kfo': 		return 'application/vnd.kde.kformula';
				case 'flw': 		return 'application/vnd.kde.kivio';
				case 'kon': 		return 'application/vnd.kde.kontour';
				case 'kpr': 		return 'application/vnd.kde.kpresenter';
				case 'ksp': 		return 'application/vnd.kde.kspread';
				case 'kwd': 		return 'application/vnd.kde.kword';
				case 'htke': 		return 'application/vnd.kenameapp';
				case 'kia': 		return 'application/vnd.kidspiration';
				case 'kne': 		return 'application/vnd.kinar';
				case 'lbd': 		return 'application/vnd.llamagraphics.life-balance.desktop';
				case 'lbe': 		return 'application/vnd.llamagraphics.life-balance.exchange+xml';
				case 'wks': 		return 'application/vnd.lotus-1-2-3';
				case 'mcd': 		return 'application/x-mathcad';
				case 'mfm': 		return 'application/vnd.mfmp';
				case 'flo': 		return 'application/vnd.micrografx.flo';
				case 'igx': 		return 'application/vnd.micrografx.igx';
				case 'mif': 		return 'application/x-mif';
				case 'mpn': 		return 'application/vnd.mophun.application';
				case 'mpc': 		return 'application/vnd.mophun.certificate';
				case 'xul': 		return 'application/vnd.mozilla.xul+xml';
				case 'cil': 		return 'application/vnd.ms-artgalry';
				case 'asf': 		return 'video/x-ms-asf';
				case 'lrm': 		return 'application/vnd.ms-lrm';
				case 'ppt': 		return 'application/vnd.ms-powerpoint';
				case 'mpp': 		return 'application/vnd.ms-project';
				case 'wpl': 		return 'application/vnd.ms-wpl';
				case 'mseq': 		return 'application/vnd.mseq';
				case 'ent': 		return 'application/vnd.nervana';
				case 'rpst': 		return 'application/vnd.nokia.radio-preset';
				case 'rpss': 		return 'application/vnd.nokia.radio-presets';
				case 'odt': 		return 'application/vnd.oasis.opendocument.text';
				case 'ott': 		return 'application/vnd.oasis.opendocument.text-template';
				case 'oth': 		return 'application/vnd.oasis.opendocument.text-web';
				case 'odm': 		return 'application/vnd.oasis.opendocument.text-master';
				case 'odg': 		return 'application/vnd.oasis.opendocument.graphics';
				case 'otg': 		return 'application/vnd.oasis.opendocument.graphics-template';
				case 'odp': 		return 'application/vnd.oasis.opendocument.presentation';
				case 'otp': 		return 'application/vnd.oasis.opendocument.presentation-template';
				case 'ods': 		return 'application/vnd.oasis.opendocument.spreadsheet';
				case 'ots': 		return 'application/vnd.oasis.opendocument.spreadsheet-template';
				case 'odc': 		return 'application/vnd.oasis.opendocument.chart';
				case 'odf': 		return 'application/vnd.oasis.opendocument.formula';
				case 'odb': 		return 'application/vnd.oasis.opendocument.database';
				case 'odi': 		return 'application/vnd.oasis.opendocument.image';
				case 'prc': 		return 'application/vnd.palm';
				case 'efif':		return 'application/vnd.picsel';
				case 'pti': 		return 'application/vnd.pvi.ptid1';
				case 'qxd': 		return 'application/vnd.quark.quarkxpress';
				case 'sdoc': 		return 'application/vnd.sealed.doc';
				case 'seml': 		return 'application/vnd.sealed.eml';
				case 'smht': 		return 'application/vnd.sealed.mht';
				case 'sppt': 		return 'application/vnd.sealed.ppt';
				case 'sxls': 		return 'application/vnd.sealed.xls';
				case 'stml': 		return 'application/vnd.sealedmedia.softseal.html';
				case 'spdf': 		return 'application/vnd.sealedmedia.softseal.pdf';
				case 'see': 		return 'application/vnd.seemail';
				case 'mmf': 		return 'application/vnd.smaf';
				case 'sxc': 		return 'application/vnd.sun.xml.calc';
				case 'stc': 		return 'application/vnd.sun.xml.calc.template';
				case 'sxd': 		return 'application/vnd.sun.xml.draw';
				case 'std': 		return 'application/vnd.sun.xml.draw.template';
				case 'sxi': 		return 'application/vnd.sun.xml.impress';
				case 'sti': 		return 'application/vnd.sun.xml.impress.template';
				case 'sxm': 		return 'application/vnd.sun.xml.math';
				case 'sxw': 		return 'application/vnd.sun.xml.writer';
				case 'sxg': 		return 'application/vnd.sun.xml.writer.global';
				case 'stw': 		return 'application/vnd.sun.xml.writer.template';
				case 'sus': 		return 'application/vnd.sus-calendar';
				case 'vsc': 		return 'application/vnd.vidsoft.vidconference';
				case 'vsd': 		return 'application/vnd.visio';
				case 'vis': 		return 'application/vnd.visionary';
				case 'sic': 		return 'application/vnd.wap.sic';
				case 'slc': 		return 'application/vnd.wap.slc';
				case 'wbxml': 		return 'application/vnd.wap.wbxml';
				case 'wmlc': 		return 'application/vnd.wap.wmlc';
				case 'wmlsc': 		return 'application/vnd.wap.wmlscriptc';
				case 'wtb': 		return 'application/vnd.webturbo';
				case 'wpd': 		return 'application/vnd.wordperfect';
				case 'wqd': 		return 'application/vnd.wqd';
				case 'wv': 			return 'application/vnd.wv.csp+wbxml';
				case '8bit': 		return 'multipart/parallel';
				case 'hvd': 		return 'application/vnd.yamaha.hv-dic';
				case 'hvs': 		return 'application/vnd.yamaha.hv-script';
				case 'hvp': 		return 'application/vnd.yamaha.hv-voice';
				case 'saf': 		return 'application/vnd.yamaha.smaf-audio';
				case 'spf': 		return 'application/vnd.yamaha.smaf-phrase';
				case 'vmd': 		return 'application/vocaltec-media-desc';
				case 'vmf': 		return 'application/vocaltec-media-file';
				case 'vtk': 		return 'application/vocaltec-talker';
				case 'wif': 		return 'image/cewavelet';
				case 'wp5': 		return 'application/wordperfect5.1';
				case 'wk': 			return 'application/x-123';
				case '7ls': 		return 'application/x-7th_level_event';
				case 'aab': 		return 'application/x-authorware-bin';
				case 'aam': 		return 'application/x-authorware-map';
				case 'aas': 		return 'application/x-authorware-seg';
				case 'bcpio': 		return 'application/x-bcpio';
				case 'bleep': 		return 'application/x-bleeper';
				case 'bz2': 		return 'application/x-bzip2';
				case 'vcd': 		return 'application/x-cdlink';
				case 'chat': 		return 'application/x-chat';
				case 'pgn': 		return 'application/x-chess-pgn';
				case 'z': 			return 'application/x-compress';
				case 'cpio': 		return 'application/x-cpio';
				case 'pqf': 		return 'application/x-cprplayer';
				case 'csh': 		return 'application/x-csh';
				case 'csm': 		return 'chemical/x-csml';
				case 'co': 			return 'application/x-cult3d-object';
				case 'deb': 		return 'application/x-debian-package';
				case 'dcr': 		return 'application/x-director';
				case 'dvi': 		return 'application/x-dvi';
				case 'evy': 		return 'application/x-envoy';
				case 'gtar': 		return 'application/x-gtar';
				case 'gz': 			return 'application/x-gzip';
				case 'hdf': 		return 'application/x-hdf';
				case 'hep': 		return 'application/x-hep';
				case 'rhtml': 		return 'application/x-html+ruby';
				case 'mv': 			return 'application/x-httpd-miva';
				case 'phtml': 		return 'application/x-httpd-php';
				case 'ica': 		return 'application/x-ica';
				case 'imagemap': 	return 'application/x-imagemap';
				case 'ipx': 		return 'application/x-ipix';
				case 'ips': 		return 'application/x-ipscript';
				case 'jar': 		return 'application/x-java-archive';
				case 'jnlp': 		return 'application/x-java-jnlp-file';
				case 'ser': 		return 'application/x-java-serialized-object';
				case 'class': 		return 'application/x-java-vm';
				case 'skp': 		return 'application/x-koan';
				case 'latex':		return 'application/x-latex';
				case 'frm': 		return 'application/x-maker';
				case 'mid': 		return 'audio/x-midi';
				case 'mda': 		return 'application/x-msaccess';
				case 'com': 		return 'application/x-msdos-program';
				case 'nc': 			return 'application/x-netcdf';
				case 'pac': 		return 'application/x-ns-proxy-autoconfig';
				case 'pm5': 		return 'application/x-pagemaker';
				case 'pl': 			return 'application/x-perl';
				case 'rp': 			return 'application/x-pn-realmedia';
				case 'py': 			return 'application/x-python';
				case 'qtl': 		return 'application/x-quicktimeplayer';
				case 'rar': 		return 'application/x-rar-compressed';
				case 'rb': 			return 'application/x-ruby';
				case 'sh': 			return 'application/x-sh';
				case 'shar': 		return 'application/x-shar';
				case 'swf': 		return 'application/x-shockwave-flash';
				case 'spr': 		return 'application/x-sprite';
				case 'sav': 		return 'application/x-spss';
				case 'spt': 		return 'application/x-spt';
				case 'sit': 		return 'application/x-stuffit';
				case 'sv4cpio': 	return 'application/x-sv4cpio';
				case 'sv4crc': 		return 'application/x-sv4crc';
				case 'tar': 		return 'application/x-tar';
				case 'tcl': 		return 'application/x-tcl';
				case 'tex': 		return 'application/x-tex';
				case 'texinfo': 	return 'application/x-texinfo';
				case 't': 			return 'application/x-troff';
				case 'man': 		return 'application/x-troff-man';
				case 'me': 			return 'application/x-troff-me';
				case 'ms': 			return 'application/x-troff-ms';
				case 'vqf': 		return 'application/x-twinvq';
				case 'vqe': 		return 'application/x-twinvq-plugin';
				case 'ustar': 		return 'application/x-ustar';
				case 'bck': 		return 'application/x-vmsbackup';
				case 'src': 		return 'application/x-wais-source';
				case 'wz': 			return 'application/x-wingz';
				case 'wp6': 		return 'application/x-wordperfect6.1';
				case 'crt': 		return 'application/x-x509-ca-cert';
				case 'zip': 		return 'application/zip';
				case 'xhtml': 		return 'application/xhtml+xml';
				case '3gpp': 		return 'audio/3gpp';
				case 'amr': 		return 'audio/amr';
				case 'awb': 		return 'audio/amr-wb';
				case 'au': 			return 'audio/basic';
				case 'evc': 		return 'audio/evrc';
				case 'l16': 		return 'audio/l16';
				case 'mp3': 		return 'audio/mpeg';
				case 'sid': 		return 'audio/prs.sid';
				case 'qcp': 		return 'audio/qcelp';
				case 'smv': 		return 'audio/smv';
				case 'koz': 		return 'audio/vnd.audiokoz';
				case 'eol': 		return 'audio/vnd.digital-winds';
				case 'plj': 		return 'audio/vnd.everad.plj';
				case 'lvp': 		return 'audio/vnd.lucent.voice';
				case 'mxmf': 		return 'audio/vnd.nokia.mobile-xmf';
				case 'vbk': 		return 'audio/vnd.nortel.vbk';
				case 'ecelp4800': 	return 'audio/vnd.nuera.ecelp4800';
				case 'ecelp7470': 	return 'audio/vnd.nuera.ecelp7470';
				case 'ecelp9600': 	return 'audio/vnd.nuera.ecelp9600';
				case 'smp3': 		return 'audio/vnd.sealedmedia.softseal.mpeg';
				case 'vox': 		return 'audio/voxware';
				case 'aif': 		return 'audio/x-aiff';
				case 'mp2': 		return 'audio/x-mpeg';
				case 'mpu': 		return 'audio/x-mpegurl';
				case 'rm': 			return 'audio/x-pn-realaudio';
				case 'rpm': 		return 'audio/x-pn-realaudio-plugin';
				case 'ra': 			return 'audio/x-realaudio';
				case 'wav': 		return 'audio/x-wav';
				case 'emb': 		return 'chemical/x-embl-dl-nucleotide';
				case 'cube': 		return 'chemical/x-gaussian-cube';
				case 'gau': 		return 'chemical/x-gaussian-input';
				case 'jdx': 		return 'chemical/x-jcamp-dx';
				case 'mol': 		return 'chemical/x-mdl-molfile';
				case 'rxn': 		return 'chemical/x-mdl-rxnfile';
				case 'tgf': 		return 'chemical/x-mdl-tgf';
				case 'mop': 		return 'chemical/x-mopac-input';
				case 'pdb': 		return 'x-chemical/x-pdb';
				case 'scr': 		return 'chemical/x-rasmol';
				case 'xyz': 		return 'x-chemical/x-xyz';
				case 'dwf': 		return 'x-drawing/dwf';
				case 'ivr': 		return 'i-world/i-vrml';
				case 'bmp': 		return 'image/x-bmp';
				case 'cod': 		return 'image/cis-cod';
				case 'fif': 		return 'image/fif';
				case 'gif': 		return 'image/gif';
				case 'ief': 		return 'image/ief';
				case 'jp2': 		return 'image/jp2';
				case 'jpg': 		return 'image/pjpeg';
				case 'jpm': 		return 'image/jpm';
				case 'jpf': 		return 'image/jpx';
				case 'pic': 		return 'image/pict';
				case 'png': 		return 'image/x-png';
				case 'tga': 		return 'image/targa';
				case 'tif': 		return 'image/tiff';
				case 'tiff': 		return 'image/tiff';
				case 'svf': 		return 'image/vn-svf';
				case 'dgn': 		return 'image/vnd.dgn';
				case 'djvu': 		return 'image/vnd.djvu';
				case 'dwg': 		return 'image/vnd.dwg';
				case 'pgb': 		return 'image/vnd.glocalgraphics.pgb';
				case 'ico': 		return 'image/vnd.microsoft.icon';
				case 'mdi': 		return 'image/vnd.ms-modi';
				case 'spng': 		return 'image/vnd.sealed.png';
				case 'sgif': 		return 'image/vnd.sealedmedia.softseal.gif';
				case 'sjpg': 		return 'image/vnd.sealedmedia.softseal.jpg';
				case 'wbmp': 		return 'image/vnd.wap.wbmp';
				case 'ras': 		return 'image/x-cmu-raster';
				case 'fh4': 		return 'image/x-freehand';
				case 'pnm': 		return 'image/x-portable-anymap';
				case 'pbm': 		return 'image/x-portable-bitmap';
				case 'pgm': 		return 'image/x-portable-graymap';
				case 'ppm': 		return 'image/x-portable-pixmap';
				case 'rgb': 		return 'image/x-rgb';
				case 'xbm': 		return 'image/x-xbitmap';
				case 'xpm': 		return 'image/x-xpixmap';
				case 'xwd': 		return 'image/x-xwindowdump';
				case 'igs': 		return 'model/iges';
				case 'msh': 		return 'model/mesh';
				case 'x_b': 		return 'model/vnd.parasolid.transmit.binary';
				case 'x_t': 		return 'model/vnd.parasolid.transmit.text';
				case 'wrl': 		return 'x-world/x-vrml';
				case 'csv': 		return 'text/comma-separated-values';
				case 'css': 		return 'text/css';
				case 'html': 		return 'text/html';
				case 'txt': 		return 'text/plain';
				case 'rst': 		return 'text/prs.fallenstein.rst';
				case 'rtx': 		return 'text/richtext';
				case 'sgml': 		return 'text/x-sgml';
				case 'tsv': 		return 'text/tab-separated-values';
				case 'ccc': 		return 'text/vnd.net2phone.commcenter.command';
				case 'jad': 		return 'text/vnd.sun.j2me.app-descriptor';
				case 'si': 			return 'text/vnd.wap.si';
				case 'sl': 			return 'text/vnd.wap.sl';
				case 'wml': 		return 'text/vnd.wap.wml';
				case 'wmls': 		return 'text/vnd.wap.wmlscript';
				case 'hdml': 		return 'text/x-hdml';
				case 'etx': 		return 'text/x-setext';
				case 'talk': 		return 'text/x-speech';
				case 'vcs': 		return 'text/x-vcalendar';
				case 'vcf': 		return 'text/x-vcard';
				case 'xml': 		return 'text/xml';
				case 'uvr': 		return 'ulead/vrml';
				case '3gp': 		return 'video/3gpp';
				case 'dl': 			return 'video/dl';
				case 'gl': 			return 'video/gl';
				case 'mj2': 		return 'video/mj2';
				case 'mpeg': 		return 'video/mpeg';
				case 'mov': 		return 'video/quicktime';
				case 'vdo': 		return 'video/vdo';
				case 'viv': 		return 'video/vivo';
				case 'fvt': 		return 'video/vnd.fvt';
				case 'mxu': 		return 'video/vnd.mpegurl';
				case 'nim': 		return 'video/vnd.nokia.interleaved-multimedia';
				case 'mp4': 		return 'video/vnd.objectvideo';
				case 's11': 		return 'video/vnd.sealed.mpeg1';
				case 'smpg': 		return 'video/vnd.sealed.mpeg4';
				case 'sswf': 		return 'video/vnd.sealed.swf';
				case 'smov': 		return 'video/vnd.sealedmedia.softseal.mov';
				case 'vivo': 		return 'video/vnd.vivo';
				case 'fli': 		return 'video/x-fli';
				case 'wmv': 		return 'video/x-ms-wmv';
				case 'avi': 		return 'video/x-msvideo';
				case 'movie': 		return 'video/x-sgi-movie';
				case 'ice': 		return 'x-conference/x-cooltalk';
				case 'd': 			return 'x-world/x-d96';
				case 'svr': 		return 'x-world/x-svr';
				case 'vrw': 		return 'x-world/x-vream';
				default: 
					return $defaultType;
			}
		}
		/**
		 * Added because error reporting facilities in phpgw tries to serialize the PDO
		 * instance in $this->db which causes an error. This method removes $this->db from the 
		 * serialized values to avoid this problem.
		 */
		public function __sleep()
		{
			return array('table_name', 'fields');
		}
	}
