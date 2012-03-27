<?php
	/**
	* phpGroupWare - registration
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @package registration
 	* @version $Id: class.uicommon.inc.php 8830 2012-02-13 06:57:11Z erikhl $
	*/	

	phpgw::import_class('phpgwapi.yui');

	/**
	 * Cherry pick selected values into a new array
	 * 
	 * @param array $array	input array
	 * @param array $keys	 array of keys to pick
	 *
	 * @return array containg values from $array for the keys in $keys.
	 */
	function extract_values($array, $keys, $options = array())
	{
		static $default_options = array(
			'prefix' => '',
			'suffix' => '', 
			'preserve_prefix' => false,
			'preserve_suffix' => false
		);

		$options = array_merge($default_options, $options);

		$result = array();
		foreach($keys as $write_key)
		{
			$array_key = $options['prefix'].$write_key.$options['suffix'];
			if(isset($array[$array_key])) {
				$result[($options['preserve_prefix'] ? $options['prefix'] : '').$write_key.($options['preserve_suffix'] ? $options['suffix'] : '')] = $array[$array_key];
			}
		}
		return $result;
	}

	function array_set_default(&$array, $key, $value)
	{
		if(!isset($array[$key])) $array[$key] = $value;
	}

	/**
	 * Reformat an ISO timestamp into norwegian format
	 * 
	 * @param string $date	date
	 *
	 * @return string containg timestamp in norwegian format
	 */
	function pretty_timestamp($date)
	{
		if (empty($date)) return "";

		if(is_array($date) && is_object($date[0]) && $date[0] instanceof DOMNode)
		{
			$date = $date[0]->nodeValue;
		}
		preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}))?/', $date, $match);

		$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		if($match[4]) 
		{
			$dateformat .= ' H:i';
			$timestamp = mktime($match[5], $match[6], 0, $match[2], $match[3], $match[1]);
		}
		else
		{
			$timestamp = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
		}
		$text = date($dateformat,$timestamp);

		return $text;
	}

	/**
	 * Generates a javascript translator object/hash for the specified fields.
	 */
	function js_lang()
	{
		$keys = func_get_args();
		$strings = array();
		foreach($keys as $key)
		{
			$strings[$key] = is_string($key) ? lang($key) : call_user_func_array('lang', $key);
		}
		return json_encode($strings);
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

	abstract class registration_uicommon
	{
		const UI_SESSION_FLASH = 'flash_msgs';

		protected
			$filesArray;

		protected static 
			$old_exception_handler;

		private 
			$ui_session_key,
			$flash_msgs;


		const LOCATION_ROOT = '.';
		const LOCATION_SUPERUSER = '.usertype.superuser';
//		const LOCATION_ADMINISTRATOR = '.RESPONSIBILITY.ADMIN';
		const LOCATION_USER = '.usertype.user';

		public $dateFormat;

		public $type_of_user;

	//	public $flash_msgs;

		public function __construct()
		{
			self::set_active_menu('registration');
			self::add_stylesheet('phpgwapi/js/yahoo/calendar/assets/skins/sam/calendar.css');
			self::add_stylesheet('phpgwapi/js/yahoo/autocomplete/assets/skins/sam/autocomplete.css');
			self::add_stylesheet('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			self::add_stylesheet('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			self::add_stylesheet('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			self::add_stylesheet('phpgwapi/js/yahoo/treeview/assets/skins/sam/treeview.css');
			//self::add_stylesheet('registration/templates/base/css/base.css');
			self::add_javascript('controller', 'yahoo', 'common.js');//Use this one for now
			$this->tmpl_search_path = array();
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $GLOBALS['phpgw_info']['flags']['currentapp'] . '/templates/base');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('history');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			phpgwapi_yui::load_widget('animation');

			$this->url_prefix = str_replace('_', '.', get_class($this));

			$this->dateFormat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->locations = & $GLOBALS['phpgw']->locations;

			$this->type_of_user = array(
				MANAGER => $this->isManager(),
				EXECUTIVE_OFFICER => $this->isExecutiveOfficer(),
				ADMINISTRATOR => $this->isAdministrator()
			);
			//var_dump($this->type_of_user);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($GLOBALS['phpgw_info']['flags']['currentapp']);
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

		/**
		 * Permission check. Proxy method for method check in phpgwapi->acl
		 * 
		 * @param $location
		 * @param $permission
		 * @return true if check is ok, false othewise
		 */
		protected function hasPermissionOn($location = registration_uicommon::LOCATION_ROOT, $permission = PHPGW_ACL_PRIVATE){
			return $this->acl->check($location,$permission,'registration');
		}


		/**
		 * Check to see if this user is an administrator
		 * 
		 * @return true if private permission on root, false otherwise
		 */
		protected function isAdministrator(){
			return $this->acl->check(registration_uicommon::LOCATION_ROOT,PHPGW_ACL_PRIVATE,'registration');
		}

		/**
		 * Check to see if the user is an executive officer
		 * 
		 * @return true if at least add permission on fields of responsibilities (locations: .RESPONSIBIITY.*)
		 */
		protected function isExecutiveOfficer(){
			return (
				$this->acl->check(registration_uicommon::LOCATION_SUPERUSER,PHPGW_ACL_ADD,'registration')	||
				$this->acl->check(registration_uicommon::LOCATION_USER,PHPGW_ACL_ADD,'registration')
			);
		}

		/**
		 * Check to see if the user is a manager
		 * 
		 * @return true if no read,add,delete,edit permission on fields of responsibilities (locations: .RESPONSIBILITY.*)
		 */
		protected function isManager(){
			return !$this->isExecutiveOfficer();
		}

		public static function process_registration_unauthorized_exceptions()
		{
			self::$old_exception_handler = set_exception_handler(array(__CLASS__, 'handle_registration_unauthorized_exception'));
		}

		public static function handle_registration_unauthorized_exception(Exception $e)
		{
			if ($e instanceof registration_unauthorized_exception)
			{
				$message = htmlentities('HTTP/1.0 401 Unauthorized - '.$e->getMessage(), null, self::encoding());
				header($message);
				echo "<html><head><title>$message</title></head><body><strong>$message</strong></body></html>";
			} else {
				call_user_func(self::$old_exception_handler, $e);
			}
		}

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

		public function add_yui_translation(&$data)
		{
			$this->add_template_file('yui_booking_i18n');
			$previous = lang('prev');
			$next = lang('next');
			
			$data['yui_booking_i18n'] = array(
				'Calendar' => array(
					'WEEKDAYS_SHORT' => json_encode(lang_array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa')),
					'WEEKDAYS_FULL' => json_encode(lang_array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')),
					'MONTHS_LONG' => json_encode(lang_array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')),
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
					'pageReportTemplate' => json_encode(lang("Showing items {startRecord} - {endRecord} of {totalRecords}")),
					'previousPageLinkLabel' => json_encode("&lt; {$previous}"),
					'nextPageLinkLabel' => json_encode("{$next} &gt;"),
				),
				'common' => array(
					'LBL_NAME' => json_encode(lang('Name')),
					'LBL_TIME' => json_encode(lang('Time')),
					'LBL_WEEK' => json_encode(lang('Week')),
					'LBL_RESOURCE' => json_encode(lang('Resource')),
				),
			);
		}
  

		public function add_template_helpers() {
			$this->add_template_file('helpers');
		}

  		public function render_template_xsl($files, $data)
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if($this->flash_msgs) {
				$data['msgbox_data'] = $GLOBALS['phpgw']->common->msgbox($this->flash_msgs);
			} else {
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

  
		public function check_active($url)
		{
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$activate = extract_values($_POST, array("status", "activate_id"));
				$this->bo->set_active(intval($activate['activate_id']), intval($activate['status']));
				$this->redirect(array('menuaction' => $url, 'id' => $activate['activate_id']));
			}
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
			if (!$results) { 
				$results['total_records'] = 0;
				$result['results'] = array();
			}

			return array(   
				'ResultSet' => array(
					'totalRecords' 		=> $results['total_records'],
					'recordsReturned'	=> count($results['results']),
					'startIndex' 		=> $results['start'], 
					'sortKey' 			=> $results['sort'], 
					'sortDir' 			=> $results['dir'], 
					'Result' 			=> $results['results']
				)   
			);  
		}

		public function use_yui_editor($targets)
		{
			/*
			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');
			*/
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
		 * Added because error reporting facilities in phpgw tries to serialize the PDO
		 * instance in $this->db which causes an error. This method removes $this->db from the 
		 * serialized values to avoid this problem.
		 */
		public function __sleep()
		{
			return array('table_name', 'fields');
		}
	}
