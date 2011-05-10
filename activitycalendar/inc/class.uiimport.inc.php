<?php
	phpgw::import_class('activitycalendar.uicommon');
	phpgw::import_class('activitycalendar.soarena');
	phpgw::import_class('activitycalendar.soactivity');
	
	include_class('activitycalendar', 'arena', 'inc/model/');
	include_class('activitycalendar', 'activity', 'inc/model/');

	class activitycalendar_uiimport extends activitycalendar_uicommon
	{
		const DELIMITER = ";";
		const ENCLOSING = "'";
		
		// List of messages, warnings and errors to be displayed to the user after the import
		protected $messages;
		protected $warnings;
		protected $errors;
		
		// File system path to import folder on server
		protected $path;
		protected $district;
		
		// Label on the import button. Changes as we step through the import process.
		protected $import_button_label;
		
		protected $defalt_values;
		
		public $public_functions = array
		(
			'index'	=> true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('import');
			set_time_limit(10000); //Set the time limit for this request oto 3000 seconds
		}
		
		/**
		 * Dummy method
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			// Do nothing
		}
		

		/**
		 * Public method. 
		 * 
		 * @return unknown_type
		 */
		public function index()
		{
			setlocale(LC_ALL, 'no_NO');
			
			// Set the submit button label to its initial state
			$this->import_button_label = "Start import";
//			var_dump(phpgw::get_var("importstep"));

			// If the parameter 'importsubmit' exist (submit button in import form), set path
			if (phpgw::get_var("importsubmit")) 
			{
				// Get the path for user input or use a default path
				$this->path = phpgw::get_var("import_path") ? phpgw::get_var("import_path") : '/home/notroot/FacilitExport';
				$this->office = phpgw::get_var("district") ? phpgw::get_var("district") : '1';
				phpgwapi_cache::session_set('activitycalendar', 'import_path', $this->path);
				phpgwapi_cache::session_set('activitycalendar', 'import_district', $this->office);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiimport.index', 'importstep' => 'true'));
			} 
			else if(phpgw::get_var("importstep"))
			{
				$start_time = time(); // Start time of import
				$start = date("G:i:s",$start_time);
				echo "<h3>Import started at: {$start}</h3>";
				echo "<ul>";
				$this->path = phpgwapi_cache::session_get('activitycalendar', 'import_path') . '/aktiviteter';
				$this->office = phpgwapi_cache::session_get('activitycalendar', 'import_district');
				//$this->path = '/home/notroot/FacilitExport/aktiviteter';
				
				$result = $this->import(); // Do import step, result determines if finished for this area
				echo '<li class="info">Aktiviteter: finished step ' .$result. '</li>';
				while($result != '1')
				{
					$result = $this->import();
					echo '<li class="info">Aktiviteter: finished step ' .$result. '</li>';
					flush();
				}

				echo "</ul>";
				$end_time = time();
				$difference = ($end_time - $start_time) / 60;
				$end = date("G:i:s",$end_time);
				echo "<h3>Import ended at: {$end}. Import lasted {$difference} minutes.";
				
				if ($this->errors) { 
					echo "<ul>";
					foreach ($this->errors as $error) {
						echo '<li class="error">Error: ' . $error . '</li>';
					}
		
					echo "</ul>";
				}
		
				if ($this->warnings) { 
					echo "<ul>";
					foreach ($this->warnings as $warning) {
						echo '<li class="warning">Warning: ' . $warning . '</li>';
					}
					echo "</ul>";
				}
		
				if ($this->messages) {
					echo "<ul>";
		
					foreach ($this->messages as $message) {
						echo '<li class="info">' . $message . '</li>';
					}
					echo "</ul>";
				}
			}
			else
			{
				$this->render('activity_import.php', array(
				'messages' => $this->messages,
				'warnings' => $this->warnings,
				'errors' => $this->errors, 
				'button_label' => $this->import_button_label,
				'import_path' => $path)
			);
			}
		}
		
		/**
		 * Import Facilit data to Portico Estate's rental module
		 * The function assumes CSV files have been uploaded to a location on the server reachable by the
		 * web server user.  The CSV files must correspond to the table names from Facilit, as exported
		 * from Access. Field should be enclosed in single quotes and separated by comma.  The CSV files
		 * must contain the column headers on the first line.
		 * 
		 * @return unknown_type
		 */
		public function import()
		{
			
			$steps = 1;
			
			/* Import logic:
			 * 
			 * 1. Do step logic if the session variable is not set
			 * 2. Set step result on session
			 * 3. Set label for import button
			 * 4. Log messages for this step
			 *  
			 */
			
			$this->messages = array();
			$this->warnings = array();
			$this->errors = array();
			
			// Import arenas if not done before and put them on the users session
			if (!phpgwapi_cache::session_get('activitycalendar', 'arenas')) {
				phpgwapi_cache::session_set('activitycalendar', 'arenas', $this->import_arenas()); 
                $this->log_messages(1);
				return '1';
				$this->clean_up();
			}

			// We're done with the import, so clear all session variables so we're ready for a new one
			// We do not clear parties (same for all responsibility areas)
			// We do not clear event data, the array is just added for each
			phpgwapi_cache::session_clear('activitycalendar', 'arenas');
			return '1';
		}
		
		protected function import_arenas()
		{
			$start_time = time();
			
			$soarena = activitycalendar_soarena::get_instance();
			$soactivity = activitycalendar_soactivity::get_instance();
			
			$datalines = $this->getcsvdata($this->path . "/import_all.csv");
			//$activity_district = $this->district;
			//var_dump($this->district);
			
			$this->messages[] = "Read 'import_all.csv' file in " . (time() - $start_time) . " seconds";
			$this->messages[] = "'importfile.csv' contained " . count($datalines) . " lines";
			
			foreach ($datalines as $data) {
/*				if(count($data) <= 8)
				{
					continue;
				}
*/				
				$arena = new activitycalendar_arena();
				//8: sted, 9:adresse
				$arena_name = $this->decode($data[7]);
				$arena_address = $this->decode($data[8]);
				$curr_arena_id = 0;

				if($arena_name){
					$arena->set_arena_name($arena_name);
					$arena->set_address($arena_address);
	
					// All is good, store notification
					if ($soarena->store($arena)) {
						$this->messages[] = "Successfully imported arena: Name ({$this->decode($data[7])})";
						$curr_arena_id = $arena->get_id();
					} else {
						$this->errors[] = "Error importing arena: Name ({$this->decode($data[7])})";
						$curr_arena_id = 0;
					}
				}
				else
				{
					$this->errors[] = "Error importing arena: Name ({$this->decode($data[7])})";
					$curr_arena_id = 0;
				}
				
				$activity = new activitycalendar_activity();
				$activity_title = $this->decode($data[1]);
				$activity_org = $this->decode($data[2]);
				if($activity_org)
				{
					$activity_org = $soactivity->get_orgid_from_orgno($activity_org);
				}
				$activity_adapted = $this->decode($data[3]);
				$activity_category = $this->decode($data[4]);
				if($activity_category)
				{
					$activity_category = $soactivity->get_category_from_name($activity_category);
				}
				$activity_description = $this->decode($data[5]);
				if($activity_description)
				{
					//var_dump(strlen($activity_description));
					if(strlen($activity_description) > 255)
					{
						$activity_description = substr($activity_description,0,254);
					}
				}
				$activity_target = $this->decode($data[6]);
				//var_dump($activity_target);
				if($activity_target)
				{
					$act_target_array = explode(",", $activity_target);
					foreach($act_target_array as $at)
					{
						$act_targets[] = $soactivity->get_target_from_sort_id($at);
					}
					$activity_target = implode(",", $act_targets);
				}
				unset($act_targets);
				$activity_day = $this->decode($data[9]);
				$activity_time = $this->decode($data[10]);
				$activity_update_date = $this->decode($data[20]);
				if($activity_update_date)
				{
					$act_update_array = explode(".", $activity_update_date);
					if(count($act_update_array) == 3)
					{
						$y = $act_update_array[2];
						$m = $act_update_array[1];
						$d = $act_update_array[0];
						$activity_updated_date = strtotime($y."-".$m."-".$d);
					}
				}
				//$activity_district = $this->decode($data[21]);
				
				if($activity_title){
					$activity->set_title($activity_title);
					$activity->set_organization_id($activity_org);
					$activity->set_category($activity_category);
					$activity->set_target($activity_target);
					$activity->set_description($activity_description);
					$activity->set_arena($curr_arena_id);
					$activity->set_time($activity_day.' '.$activity_time);
					if($activity_adapted)
					{
						$activity->set_special_adaptation(true);
					}
					$activity->set_office($this->office);
					$activity->set_last_change_date($activity_updated_date);
					//var_dump($activity);
					// All is good, store activity
					if ($soactivity->store($activity)) {
						$this->messages[] = "Successfully imported activity: Title ({$this->decode($data[1])})";
					} else {
						$this->errors[] = "Error importing activity: Title ({$this->decode($data[1])})";
					}
				}
			}
			
			$this->messages[] = "Imported activities. (" . (time() - $start_time) . " seconds)";
			return true;
		}
		
		protected function getcsvdata($path, $skipfirstline = true)
		{
			// Open the csv file
			$handle = fopen($path, "r");
			
			if ($skipfirstline) {
				// Read the first line to get the headers out of the way
				$this->getcsv($handle);
			}
			
			$result = array();
			
			while(($data = $this->getcsv($handle)) !== false) {
				$result[] = $data;
			}
			
			fclose($handle);
			
			return $result;
		}
			
		
		/**
		 * Read the next line from the given file handle and parse it to CSV according to the rules set up
		 * in the class constants DELIMITER and ENCLOSING.  Returns FALSE like getcsv on EOF.
		 * 
		 * @param file-handle $handle
		 * @return array of values from the parsed csv line
		 */
		protected function getcsv($handle)
		{
			return fgetcsv($handle, 1000, self::DELIMITER, self::ENCLOSING);
		}
		
		/**
		 * Convert from the locale encoding to UTF-8 encoding and escape single quotes
		 * 
		 * @param string $value The value to convert
		 * @return string
		 */
		protected function decode($value)
		{
			$converted = mb_convert_encoding($value, 'UTF-8');
			if ($this->is_null(trim($converted))) {
				return null;
			}
			
			// Escape single quotes
			// TODO: This is a DB problem and doesn't belong here
			return str_replace("'", "\'", $converted);
		}
		
		/**
		 * Test a value for null according to several formats that can exist in the export.
		 * Returns true if the value is null according to these rules, false otherwise.
		 * 
		 * @param string $value The value to test
		 * @return bool
		 */
		protected function is_null($value)
		{
			return ((trim($value) == "") || ($data == "<NULL>") || ($data == "''"));
		}


        /**
         * Do end-of-import clean up
         */
        protected function clean_up() {
            $socontract = rental_socontract::get_instance();
            $socontract->clear_last_edited_table();
        }

        private function log_messages($step) {
        	sort($this->errors);
        	sort($this->warnings);
        	sort($this->messages);
        	
            $msgs = array_merge(
            	array('----------------Errors--------------------'),
            	$this->errors,
            	array('---------------Warnings-------------------'),
            	$this->warnings,
            	array('---------------Messages-------------------'),
            	$this->messages
            );
            $path = $this->path;

            if(is_dir($path.'/logs') || mkdir($path.'/logs')) {
                file_put_contents("$path/logs/$step.log", implode(PHP_EOL, $msgs));
            }
            else { // Path not writeable

            }
        }
	}
?>
