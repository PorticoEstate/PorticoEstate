<?php
	//phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon_jquery');

	//define("ACTIVITYCALENDAR_TEMPLATE_PATH", "activitycalendar/templates/base/");
	//define("ACTIVITYCALENDAR_IMAGE_PATH", "rental/templates/base/");

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
				$result[$key] = phpgw::clean_value($array[$key]);
			}
		}
		return $result;
	}

	function array_set_default(&$array, $key, $value)
	{
		if(!isset($array[$key]))
			$array[$key] = $value;
	}
	
	define('MANAGER', 'MANAGER');
	define('EXECUTIVE_OFFICER', 'EXECUTIVE_OFFICER');
	define('ADMINISTRATOR', 'ADMINISTRATOR');

	abstract class activitycalendar_uicommon extends phpgwapi_uicommon_jquery
	{
		protected static $old_exception_handler;

		const LOCATION_ROOT		 = '.';
		const LOCATION_IN			 = '.RESPONSIBILITY.INTO';
		const LOCATION_OUT		 = '.RESPONSIBILITY.OUT';
		const LOCATION_INTERNAL	 = '.RESPONSIBILITY.INTERNAL';

		//public $dateFormat;
		public $type_of_user;
		public $flash_msgs;
		public $user_rows_per_page;

		public function __construct()
		{
			parent::__construct();
			
			self::set_active_menu('activitycalendar');

			$this->acl		 = & $GLOBALS['phpgw']->acl;
			$this->locations = & $GLOBALS['phpgw']->locations;

			/* 			$this->type_of_user = array(
			  MANAGER => $this->isManager(),
			  EXECUTIVE_OFFICER => $this->isExecutiveOfficer(),
			  ADMINISTRATOR => $this->isAdministrator()
			  ); */
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($GLOBALS['phpgw_info']['flags']['currentapp']);
			
			$this->user_rows_per_page = ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 10;
		}

		/**
		 * Permission check. Proxy method for method check in phpgwapi->acl
		 * 
		 * @param $location
		 * @param $permission
		 * @return true if check is ok, false othewise
		 */
		protected function hasPermissionOn($location = activitycalendar_uicommon::LOCATION_ROOT, $permission = PHPGW_ACL_PRIVATE)
		{
			return $this->acl->check($location, $permission, 'bkbooking');
		}

		/**
		 * Check to see if this user is an administrator
		 * 
		 * @return true if private permission on root, false otherwise
		 */
		protected function isAdministrator()
		{
			return $this->acl->check(activitycalendar_uicommon::LOCATION_ROOT, PHPGW_ACL_PRIVATE, 'activitycalendar');
		}

		/**
		 * Check to see if the user is an executive officer
		 * 
		 * @return true if at least add permission on fields of responsibilities (locations: .RESPONSIBIITY.*)
		 */
		protected function isExecutiveOfficer()
		{
			return (
			$this->acl->check(activitycalendar_uicommon::LOCATION_IN, PHPGW_ACL_ADD, 'activitycalendar') ||
			$this->acl->check(activitycalendar_uicommon::LOCATION_OUT, PHPGW_ACL_ADD, 'activitycalendar') ||
			$this->acl->check(activitycalendar_uicommon::LOCATION_INTERNAL, PHPGW_ACL_ADD, 'activitycalendar')
			);
		}

		/**
		 * Check to see if the user is a manager
		 * 
		 * @return true if no read,add,delete,edit permission on fields of responsibilities (locations: .RESPONSIBILITY.*)
		 */
		protected function isManager()
		{
			return !$this->isExecutiveOfficer();
		}

		public static function process_rental_unauthorized_exceptions()
		{
			self::$old_exception_handler = set_exception_handler(array(__CLASS__, 'handle_rental_unauthorized_exception'));
		}

		public static function handle_rental_unauthorized_exception(Exception $e)
		{
			if($e instanceof rental_unauthorized_exception)
			{
				$message = htmlentities('HTTP/1.0 401 Unauthorized - ' . $e->getMessage(), null, self::encoding());
				header($message);
				echo "<html><head><title>$message</title></head><body><strong>$message</strong></body></html>";
			}
			else
			{
				call_user_func(self::$old_exception_handler, $e);
			}
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
		public function yui_results($data, $field_total = 'total_records', $field_results = 'results')
		{
			return array
				(
				'ResultSet' => array(
					'totalRecords'	 => $data[$field_total],
					'Result'		 => $data[$field_results]
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
				$gab_id = substr($gab_id, 4, 5) . ' / ' . substr($gab_id, 9, 4) . ' / ' . substr($gab_id, 13, 4) . ' / ' . substr($gab_id, 17, 3);
			}
			return $gab_id;
		}

		/**
		 * Method for JSON queries.
		 * 
		 * @return YUI result
		 */
		//public abstract function query();

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

			foreach($extra_cols as $col)
			{
				$literal = '{';
				$literal .= 'key: "' . $col['key'] . '",';
				$literal .= 'label: "' . $col['label'] . '"';
				if(isset($col['formatter']))
				{
					$literal .= ',formatter: ' . $col['formatter'];
				}
				if(isset($col['parser']))
				{
					$literal .= ',parser: ' . $col['parser'];
				}
				$literal .= '}';

				if($col["index"])
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
		 * Download xls, csv or similar file representation of a data table
		 */
		public function download()
		{
            $list = $this->query();

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