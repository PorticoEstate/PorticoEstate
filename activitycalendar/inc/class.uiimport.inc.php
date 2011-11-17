<?php
	phpgw::import_class('activitycalendar.uicommon');
	phpgw::import_class('activitycalendar.soarena');
	phpgw::import_class('activitycalendar.soactivity');
	phpgw::import_class('activitycalendar.soorganization');
	
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
		protected $file;
		protected $district;
		protected $csvdata;
		
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
				$this->file = $_FILES['file']['tmp_name'];
				$this->csvdata = $this->getcsvdata($_FILES['file']['tmp_name']);
				//var_dump($this->office);
				//var_dump($_FILES['file']['name']);
				//var_dump($_FILES['file']['tmp_name']);
				phpgwapi_cache::session_set('activitycalendar', 'file', $this->file);
				phpgwapi_cache::session_set('activitycalendar', 'csvdata', $this->csvdata);
				phpgwapi_cache::session_set('activitycalendar', 'import_district', $this->office);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiimport.index', 'importstep' => 'true'));
			} 
			else if(phpgw::get_var("importstep"))
			{
				$start_time = time(); // Start time of import
				$start = date("G:i:s",$start_time);
				echo "<h3>Import started at: {$start}</h3>";
				echo "<ul>";
				$this->file = phpgwapi_cache::session_get('activitycalendar', 'file');
				$this->csvdata = phpgwapi_cache::session_get('activitycalendar', 'csvdata');
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
			
			//var_dump($_FILES['file']['name']);
			//var_dump($this->file);
			//$datalines = $this->getcsvdata($_FILES['file']['tmp_name']);;
			$datalines = $this->csvdata;
			//$datalines = $this->getcsvdata($this->file);
			//$activity_district = $this->district;
			
			$this->messages[] = "Read 'import_all.csv' file in " . (time() - $start_time) . " seconds";
			$this->messages[] = "'importfile.csv' contained " . count($datalines) . " lines";
			
			//$so_arena = rental_socontract::get_instance();
			$db_activity = $soactivity->get_db();
			$db_activity->transaction_begin();
			
			foreach ($datalines as $data) {
				$arenaOK = true;
				$activityOK = true;
				unset($act_targets);
				unset($activity_persons);
				unset($activity_description);
				unset($org_info);
				unset($group_info);
				unset($contact_person);
				unset($contact1);
				unset($contact2);
				unset($contact3);
				unset($contact4);
				unset($new_group_id);
				
				$internal_arena = $this->decode($data[9]);
				if($internal_arena)
				{
					$internal_arena_id = $internal_arena;
					$curr_arena_id = "";
				}
				else
				{				
					$arena = new activitycalendar_arena();
					//8: sted, 10:adresse
					$arena_name = $this->decode($data[8]);
					$arena_address = $this->decode($data[10]);
					$curr_arena_id = 0;
					$existing_arena_id = 0;
					$internal_arena_id = "";
	
					if($arena_name){
						$existing_arena_id = $soarena->get_arena_id_by_name($arena_name);
						if($existing_arena_id > 0)
						{
							$curr_arena_id = $existing_arena_id;
						}
						else
						{
							$arena->set_arena_name($arena_name);
							$arena->set_address($arena_address);
							$arena->set_active(true);
			
							// All is good, store notification
							if ($soarena->store($arena)) {
								$this->messages[] = "Successfully imported arena: Name ({$arena_name})";
								$curr_arena_id = $arena->get_id();
							} else {
								$this->errors[] = "Error importing arena: Name ({$arena_name})";
								$curr_arena_id = 0;
								$arenaOK = false;
							}
						}
					}
					else
					{
						$this->errors[] = "Error importing arena: Name not supplied";
						$curr_arena_id = 0;
					}
				}
				
				$activity = new activitycalendar_activity();
				$activity_title = $this->decode($data[1]);
				$activity_group = $this->decode($data[2]);
				$activity_org = $this->decode($data[3]);
				$activity_category = $this->decode($data[5]);
				$org_name_tmp = $this->decode($data[1]);
				if(strlen($org_name_tmp) > 50)
				{
					$org_name_tmp = substr($org_name_tmp,0,49);
				}
				$org_email = $this->decode($data[15]);
				if(strlen($org_email) > 50)
				{
					$org_email = substr($org_email,0,49);
				}
				$org_phone = $this->decode($data[14]);
				if(strlen($org_phone) > 50)
				{
					$org_phone = substr($org_phone,0,49);
				}
				$contact_mail_2 = $this->decode($data[21]);
				if(strlen($contact_mail_2) > 50)
				{
					$contact_mail_2 = substr($contact_mail_2,0,49);
				}
				if($activity_category)
				{
					$activity_category = $soactivity->get_category_from_name($activity_category);
				}
				
				$contact1_name = $this->decode($data[13]);
				if(strlen($contact1_name) > 50)
				{
					$contact1_name = substr($contact1_name,0,49);
				}
				$contact1_phone = $this->decode($data[14]);
				if(strlen($contact1_phone) > 50)
				{
					$contact1_phone = substr($contact1_phone,0,49);
				}
				$contact2_name = $this->decode($data[17]);
				if(strlen($contact2_name) > 50)
				{
					$contact2_name = substr($contact2_name,0,49);
				}
				$contact2_phone = $this->decode($data[20]);
				if(strlen($contact2_phone) > 50)
				{
					$contact2_phone = substr($contact2_phone,0,49);
				}
				
				if($activity_org)
				{
					$activity_org = $soactivity->get_orgid_from_orgno($activity_org);
					if($activity_org)
					{
						//update the organization found
						$org_info = array();
						$org_info['orgid'] = $activity_org;
						$org_info['name'] = $org_name_tmp; //new
						$org_info['homepage'] = $this->decode($data[16]);
						$org_info['phone'] = $org_phone;
						$org_info['email'] = $org_email;
						$org_info['description'] = $this->decode($data[6]);
						$org_info['street'] = $this->decode($data[10]);
						$org_info['zip'] = $this->decode($data[19]);
						$org_info['activity_id'] = $activity_category;
						$org_info['district'] = $this->decode($data[23]); 
						$soactivity->update_organization($org_info);
						//$new_org_id = $activity_org;
						
						$soactivity->delete_contact_persons($activity_org);
						
						$contact1 = array();
						$contact1['name'] = $contact1_name;
						$contact1['phone'] = $contact1_phone;
						$contact1['mail'] = $org_email;
						$contact1['org_id'] = $this->decode($activity_org);
						$soactivity->add_contact_person_org($contact1);
						
						$contact2 = array();
						$contact2['name'] = $contact2_name;
						$contact2['phone'] = $contact2_phone;
						$contact2['mail'] = $contact_mail_2;
						$contact2['org_id'] = $this->decode($activity_org);
						$soactivity->add_contact_person_org($contact2);
						
						//group-stuff:
						if($activity_group)
						{
							$group_info = array();
							$group_info['organization_id'] = $activity_org;
							$group_info['description'] = $this->decode($data[6]);
							$group_info['name'] = $this->decode($data[1]);
							$group_info['activity_id'] = $activity_category;
							$new_group_id = $soactivity->add_group($group_info);
							
							$contact3 = array();
							$contact3['name'] = $contact1_name;
							$contact3['phone'] = $contact1_phone;
							$contact3['mail'] = $org_email;
							$contact3['group_id'] = $this->decode($new_group_id);
							$soactivity->add_contact_person_group($contact3);
							
							$contact4 = array();
							$contact4['name'] = $contact2_name;
							$contact4['phone'] = $contact2_phone;
							$contact4['mail'] = $contact_mail_2;
							$contact4['group_id'] = $this->decode($new_group_id);
							$soactivity->add_contact_person_group($contact4);
							
							$activity_persons = activitycalendar_sogroup::get_instance()->get_contacts($new_group_id);
						}
						else
						{
							$activity_persons = activitycalendar_soorganization::get_instance()->get_contacts($activity_org);
						}
/*						
						foreach($activity_persons as $pers)
						{
							unset($contact_person);
							$contact_person['id'] = $pers;
							$contact_person['name'] = $this->decode($data[11]);
							$contact_person['phone'] = $this->decode($data[12]);
							$contact_person['mail'] = $this->decode($data[13]);
							$contact_person['org_id'] = $this->decode($new_org_id);
							$soactivity->update_contact_person_org($contact_person);							
						}						
*/						
						/*
						$soactivity->set_org_active($activity_org);
						$activity_description = $this->decode($data[5]);
						if($activity_description)
						{
							//update description on organization
							$soactivity->update_org_description($activity_org, $activity_description);
							//var_dump(strlen($activity_description));
							//if(strlen($activity_description) > 255)
							//{
								//$activity_description = substr($activity_description,0,254);
							//} 
						}
						*/
						//$activity_persons = activitycalendar_soorganization::get_instance()->get_contacts($activity_org);
					}
					else	//add org unit
					{
						$org_info = array();
						$org_info['name'] = $org_name_tmp; //new
						$orgno_tmp = $this->decode($data[3]);
						if(strlen($orgno_tmp) > 9)
						{
							$orgno_tmp = NULL;
						}
						$org_info['orgnr'] = $orgno_tmp; 
						
						$org_info['homepage'] = $this->decode($data[16]);
						$org_info['phone'] = $org_phone;
						$org_info['email'] = $org_email;
						$org_info['description'] = $this->decode($data[6]);
						$org_info['street'] = $this->decode($data[10]);
						$org_info['zip'] = $this->decode($data[19]);
						$org_info['activity_id'] = $activity_category;
						$org_info['district'] = $this->decode($data[23]); 
						$new_org_id = $soactivity->add_organization($org_info);
						
						$contact1 = array();
						$contact1['name'] = $contact1_name;
						$contact1['phone'] = $contact1_phone;
						$contact1['mail'] = $org_email;
						$contact1['org_id'] = $this->decode($new_org_id);
						$soactivity->add_contact_person_org($contact1);
						
						$contact2 = array();
						$contact2['name'] = $contact2_name;
						$contact2['phone'] = $contact2_phone;
						$contact2['mail'] = $contact_mail_2;
						$contact2['org_id'] = $this->decode($new_org_id);
						$soactivity->add_contact_person_org($contact2);
						
						//group-stuff:
						if($activity_group)
						{
							$group_info = array();
							$group_info['organization_id'] = $new_org_id;
							$group_info['description'] = $this->decode($data[6]);
							$group_info['name'] = $this->decode($data[1]);
							$group_info['activity_id'] = $activity_category;
							$new_group_id = $soactivity->add_group($group_info);
							
							$contact3 = array();
							$contact3['name'] = $contact1_name;
							$contact3['phone'] = $contact1_phone;
							$contact3['mail'] = $org_email;
							$contact3['group_id'] = $this->decode($new_group_id);
							$soactivity->add_contact_person_group($contact3);
							
							$contact4 = array();
							$contact4['name'] = $contact2_name;
							$contact4['phone'] = $contact2_phone;
							$contact4['mail'] = $contact_mail_2;
							$contact4['group_id'] = $this->decode($new_group_id);
							$soactivity->add_contact_person_group($contact4);
							
							$activity_persons = activitycalendar_sogroup::get_instance()->get_contacts($new_group_id);
						}
						else
						{
							$activity_persons = activitycalendar_soorganization::get_instance()->get_contacts($new_org_id);
						}
					}
				}
				else	//add org unit without org no
				{
					$org_info = array();
					//if($activity_group && !$activity_group == '')
					//{
					//	$org_info['name'] = $activity_group;
					//}
					//else
					//{
						$org_info['name'] = $org_name_tmp;
					//}
					 
					//$org_info['orgnr'] = $this->decode($data[2]);
					$org_info['homepage'] = $this->decode($data[16]);
					$org_info['phone'] = $org_phone;
					$org_info['email'] = $org_email;
					$org_info['description'] = $this->decode($data[6]);
					$org_info['street'] = $this->decode($data[10]);
					$org_info['zip'] = $this->decode($data[19]);
					$org_info['activity_id'] = $activity_category;
					$org_info['district'] = $this->decode($data[23]); 
					$new_org_id = $soactivity->add_organization($org_info);
						
					$contact1 = array();
					$contact1['name'] = $contact1_name;
					$contact1['phone'] = $contact1_phone;
					$contact1['mail'] = $org_email;
					$contact1['org_id'] = $this->decode($new_org_id);
					$soactivity->add_contact_person_org($contact1);
					
					$contact2 = array();
					$contact2['name'] = $contact2_name;
					$contact2['phone'] = $contact2_phone;
					$contact2['mail'] = $contact_mail_2;
					$contact2['org_id'] = $this->decode($new_org_id);
					$soactivity->add_contact_person_org($contact2);
					
					//group-stuff:
					if($activity_group)
					{
						$group_info = array();
						$group_info['organization_id'] = $new_org_id;
						$group_info['description'] = $this->decode($data[6]);
						$group_info['name'] = $this->decode($data[1]);
						$group_info['activity_id'] = $activity_category;
						$new_group_id = $soactivity->add_group($group_info);
						
						$contact3 = array();
						$contact3['name'] = $contact1_name;
						$contact3['phone'] = $contact1_phone;
						$contact3['mail'] = $org_email;
						$contact3['group_id'] = $this->decode($new_group_id);
						$soactivity->add_contact_person_group($contact3);
						
						$contact4 = array();
						$contact4['name'] = $contact2_name;
						$contact4['phone'] = $contact2_phone;
						$contact4['mail'] = $contact_mail_2;
						$contact4['group_id'] = $this->decode($new_group_id);
						$soactivity->add_contact_person_group($contact4);
						
						$activity_persons = activitycalendar_sogroup::get_instance()->get_contacts($new_group_id);
					}
					else
					{
						$activity_persons = activitycalendar_soorganization::get_instance()->get_contacts($new_org_id);
					}
				}
				
				
				$activity_adapted = $this->decode($data[4]);
				$activity_target = $this->decode($data[7]);
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

				$activity_day = $this->decode($data[11]);
				$activity_time = $this->decode($data[12]);
				$activity_update_date = $this->decode($data[22]);
				if($activity_update_date)
				{
					$act_update_array = explode(".", $activity_update_date);
					if(count($act_update_array) == 3)
					{
						$y = $act_update_array[2];
						$m = $act_update_array[1];
						$d = $act_update_array[0];
						$activity_updated_date = strtotime($y."-".$m."-".$d);
						//var_dump($activity_updated_date);
					}
				}
				$activity_district = $this->decode($data[23]);
				if($activity_district)
				{
					$activity_district = $soactivity->get_district_from_name($activity_district);
				}
				
				$activity_contact_person_2_address = $this->decode($data[18]);
				$activity_contact_person_2_zip = $this->decode($data[19]);
				
				if($activity_title){
					$activity->set_title($activity_title);
					$activity->set_organization_id($activity_org);
					$activity->set_group_id($new_group_id);
					$activity->set_category($activity_category);
					$activity->set_target($activity_target);
					$activity->set_description($activity_description);
					$activity->set_arena($curr_arena_id);
					$activity->set_internal_arena($internal_arena_id);
					$activity->set_state(3);
					$activity->set_time($activity_day.' '.$activity_time);
					if($activity_adapted)
					{
						$activity->set_special_adaptation(true);
					}
					$activity->set_office($this->office);
					$activity->set_district($activity_district);
					$activity->set_contact_person_2_address($activity_contact_person_2_address);
					$activity->set_contact_person_2_zip($activity_contact_person_2_zip);
					$activity->set_last_change_date($activity_updated_date);
					if($activity_persons)
					{
						//set contact persons
						$activity->set_contact_persons($activity_persons);
					}
					//var_dump($activity);
					// All is good, store activity
					if ($soactivity->import_activity($activity)) {
						$this->messages[] = "Successfully imported activity: Title ({$this->decode($data[1])})";
					} else {
						$this->errors[] = "Error importing activity: Title ({$this->decode($data[1])})";
						$activityOK = false;
					}
				}
			}
			
			if($arenaOK && $activityOK)
			{
				$this->messages[] = "Imported activities. (" . (time() - $start_time) . " seconds)";
				$db_activity->transaction_commit();
				return true;
			}
			else
			{
				if(!$arenaOK)
				{
					$this->messages[] = "Import of arenas failed. (" . (time() - $start_time) . " seconds)";
				}
				else if(!$activityOK)
				{
					$this->messages[] = "Import of activities failed. (" . (time() - $start_time) . " seconds)";
				}
				else
				{
					$this->messages[] = "Import of activities/arenas failed. (" . (time() - $start_time) . " seconds)";
				}
				$db_activity->transaction_abort();
				return false;
			}
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
            //$socontract = rental_socontract::get_instance();
            //$socontract->clear_last_edited_table();
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
