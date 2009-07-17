<?php
	phpgw::import_class('phpgwapi.yui');

	define("RENTAL_TEMPLATE_PATH", "rental/templates/portico/");
	
	
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
	
	abstract class rental_uicommon
	{
		protected static $old_exception_handler;
		
		/*
		 * TODO: Hardcoded user group ids. Should maybe be administered through admin-interface?
		 */
		public static $admin_group_id = 2002;
		public static $write_group_id = 2001;
		public static $read_group_id = 2000;

		// Default state for access rights
		private $has_admin_rights = false;
		private $has_write_permission = false;
		private $has_read_permission = false;
		
		public $dateFormat;
			
		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			self::set_active_menu('rental');
			self::add_stylesheet('phpgwapi/js/yahoo/calendar/assets/skins/sam/calendar.css');
			self::add_stylesheet('phpgwapi/js/yahoo/autocomplete/assets/skins/sam/autocomplete.css');
			self::add_stylesheet('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			self::add_stylesheet('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			self::add_stylesheet('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			self::add_stylesheet('phpgwapi/js/yahoo/treeview/assets/skins/sam/treeview.css');
			self::add_stylesheet('rental/templates/base/css/base.css');
			self::add_javascript('rental', 'rental', 'common.js');
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
			
			/*
			 * Assign correct permissions for this user based on the group the user belongs to
			 */
			$groups = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw_info']['user']['account_id']);
			foreach($groups as $group)
			{
				if( $group->id == self::$admin_group_id )
				{
					$this->has_admin_rights = true;
					$this->has_write_permission = true;
					$this->has_read_permission = true;
					break;
				} 
				else if($group->id == self::$write_group_id)
				{
					$this->has_write_permission = true;
					$this->has_read_permission = true;
				} 
				else if($group->id == self::$read_group_id)
				{
					$this->has_read_permission = true;
				}
			}
		}
		
		/**
		 * Is the user allowed to read and write in the rental module?
		 * 
		 * @return boolean
		 */
		protected function hasWritePermission(){
			return $this->has_write_permission;
		}
		
		/**
		 * Is the user allowed to read (only) the rental module?
		 * 
		 * @return boolean
		 */
		protected function hasReadPermission(){
			return $this->has_read_permission;
		}
		
		/**
		 * Is the user an administrator and thereby granted full access to the rental module?
		 * 
		 * @return boolean
		 */
		protected function isAdmin(){
			return $this->has_admin_rights;
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

        public function render_template($files, $data)
        {
			if($this->flash_msgs)
			{
				$data['msgbox_data'] = $GLOBALS['phpgw']->common->msgbox($this->flash_msgs);
			}
			else
			{
           		$this->add_template_file('msgbox');
			}
			$output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
			$GLOBALS['phpgw']->xslttpl->set_output($output);
			//$GLOBALS['phpgw']->xslttpl->add_file(array($files));
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
					//$GLOBALS['phpgw']->xslttpl->xslfiles[$tmpl] = $filename;
				}
			}
			//include();
			//var_dump(include_class('rental','tplcontract_ist'));
			//var_dump(include $template);
			//return;
			$output = ob_get_contents();
			ob_end_clean();
			self::render_template('php_template',array('output' =>$output));
		}
		
		/**
		 * Method for JSON queries.
		 * 
		 * @return YUI result
		 */
		public abstract function query();
	}
?>