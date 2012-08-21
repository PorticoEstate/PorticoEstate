<?php
	phpgw::import_class('phpgwapi.yui');

	define("ACTIVITYCALENDAR_TEMPLATE_PATH", "activitycalendar/templates/base/");
	define("ACTIVITYCALENDAR_IMAGE_PATH", "rental/templates/base/");
	
	
	/**
	 * Cherry pick selected values into a new array
	 * 
	 * @param array $array    input array
	 * @param array $keys     array of keys to pick
	 *
	 * @return array containg values from $array for the keys in $keys.
	 */
	

	function extract_values($array, $keys)
	{
		$result = array();
		foreach($keys as $key)
		{
			if(in_array($key, array_keys($array)))
			{
				$result[$key] = $array[$key];
			}
		}
		return $result;
	}
	
	function array_set_default(&$array, $key, $value)
	{
		if(!isset($array[$key])) $array[$key] = $value;
	}
	
	define('MANAGER','MANAGER');
	define('EXECUTIVE_OFFICER','EXECUTIVE_OFFICER');
	define('ADMINISTRATOR','ADMINISTRATOR');
	
	abstract class activitycalendar_uicommon
	{
		protected static $old_exception_handler;
		
		const LOCATION_ROOT = '.';
		const LOCATION_IN = '.RESPONSIBILITY.INTO';
		const LOCATION_OUT = '.RESPONSIBILITY.OUT';
		const LOCATION_INTERNAL = '.RESPONSIBILITY.INTERNAL';
		
		public $dateFormat;
		
		public $type_of_user;
		
		public $flash_msgs;
		
		public function __construct()
		{
			self::set_active_menu('activitycalendar');
			self::add_stylesheet('phpgwapi/js/yahoo/calendar/assets/skins/sam/calendar.css');
			self::add_stylesheet('phpgwapi/js/yahoo/autocomplete/assets/skins/sam/autocomplete.css');
			self::add_stylesheet('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			self::add_stylesheet('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			self::add_stylesheet('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			self::add_stylesheet('phpgwapi/js/yahoo/treeview/assets/skins/sam/treeview.css');
			//self::add_stylesheet('rental/templates/base/css/base.css');
			self::add_javascript('activitycalendar', 'activitycalendar', 'common.js');
                        self::add_javascript('activitycalendar', 'activitycalendar', 'jquery.js');
			$this->tmpl_search_path = array();
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $GLOBALS['phpgw_info']['flags']['currentapp'] . '/templates/base');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			phpgwapi_yui::load_widget('animation');
			
			$dateFormat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->locations = & $GLOBALS['phpgw']->locations;
			
/*			$this->type_of_user = array(
			MANAGER => $this->isManager(),
				EXECUTIVE_OFFICER => $this->isExecutiveOfficer(),
				ADMINISTRATOR => $this->isAdministrator()
			);*/
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		}
		
		/**
		 * Permission check. Proxy method for method check in phpgwapi->acl
		 * 
		 * @param $location
		 * @param $permission
		 * @return true if check is ok, false othewise
		 */
		protected function hasPermissionOn($location = activitycalendar_uicommon::LOCATION_ROOT, $permission = PHPGW_ACL_PRIVATE){
			return $this->acl->check($location,$permission,'bkbooking');
		}
		
		
		/**
		 * Check to see if this user is an administrator
		 * 
		 * @return true if private permission on root, false otherwise
		 */
		protected function isAdministrator(){
			return $this->acl->check(activitycalendar_uicommon::LOCATION_ROOT,PHPGW_ACL_PRIVATE,'activitycalendar');
		}
		
		/**
		 * Check to see if the user is an executive officer
		 * 
		 * @return true if at least add permission on fields of responsibilities (locations: .RESPONSIBIITY.*)
		 */
		protected function isExecutiveOfficer(){
			return (
				$this->acl->check(activitycalendar_uicommon::LOCATION_IN,PHPGW_ACL_ADD,'activitycalendar')	||
				$this->acl->check(activitycalendar_uicommon::LOCATION_OUT,PHPGW_ACL_ADD,'activitycalendar')	||
				$this->acl->check(activitycalendar_uicommon::LOCATION_INTERNAL,PHPGW_ACL_ADD,'activitycalendar')
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
		
		public static function process_rental_unauthorized_exceptions()
		{
			self::$old_exception_handler = set_exception_handler(array(__CLASS__, 'handle_rental_unauthorized_exception'));
		}
		
		public static function handle_rental_unauthorized_exception(Exception $e)
		{
			if ($e instanceof rental_unauthorized_exception)
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
        	
        public function check_active($url)
		{
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$activate = extract_values($_POST, array("status", "activate_id"));
				$this->bo->set_active(intval($activate['activate_id']), intval($activate['status']));
				$this->redirect(array('menuaction' => $url, 'id' => $activate['activate_id']));
			}
		}

		/**
		 * Build a YUI result of the data
		 * 
		 * @param $data	the data
		 * @return YUI result { ResultSet => { totalRecords => ?, Result => ?}
		 */
		public function yui_results($data,$field_total = 'total_records', $field_results = 'results')
		{
             return array
			(   
				'ResultSet' => array(
					'totalRecords' => $data[$field_total], 
					'Result' => $data[$field_results]
				)   
			);  
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
		
		protected function generate_secret($length = 10)
		{
			return substr(base64_encode(rand(1000000000,9999999999)),0, $length);
		}
		
	}
?>
