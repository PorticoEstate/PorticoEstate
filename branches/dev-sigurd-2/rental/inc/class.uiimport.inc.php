<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.soparty');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sounit');
	phpgw::import_class('rental.soprice_item');
	phpgw::import_class('rental.socontract_price_item');
	phpgw::import_class('rental.sonotification');
	
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');
	include_class('rental', 'notification', 'inc/model/');

	class rental_uiimport extends rental_uicommon
	{
		const DELIMITER = ",";
		const ENCLOSING = "'";
		
		// List of messages, warnings and errors to be displayed to the user after the import
		protected $messages;
		protected $warnings;
		protected $errors;
		
		// File system path to import folder on server
		protected $path;
		protected $location_id;
		
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

			// If the parameter 'importsubmit' exist (submit button in import form), set path
			if (phpgw::get_var("importsubmit")) 
			{
				// Get the path for user input or use a default path
				$this->path = phpgw::get_var("facilit_path") ? phpgw::get_var("facilit_path") : '/home/notroot/FacilitExport';
				phpgwapi_cache::session_set('rental', 'import_path', $this->path);
				//phpgwapi_cache::session_set('rental', 'import_path_folder','/Ekstern');
				//$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				//$location_id = array_search('contract_type_eksternleie', $types);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiimport.index', 'importstep' => 'true'));
			} 
			else if(phpgw::get_var("importstep"))
			{
				echo "<ul>";
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				$this->location_id = array_search('contract_type_eksternleie', $types);
				$this->path = phpgwapi_cache::session_get('rental', 'import_path') . '/Ekstern';
				
				$result = $this->import(); // Do import step, result determines if finished for this area
				echo '<li class="info">Eksternleie: finished step ' .$result. '</li>';
				while($result != '6')
				{
					$result = $this->import();
					echo '<li class="info">Eksternleie: finished step ' .$result. '</li>';
					flush();
				}
				
				$this->location_id = array_search('contract_type_internleie', $types);
				$this->path = phpgwapi_cache::session_get('rental', 'import_path') . '/Intern';
				
				$result = $this->import(); // Do import step, result determines if finished for this area
				echo '<li class="info">Internleie: finished step ' .$result. '</li>';
				while($result != '6')
				{
					$result = $this->import();
					echo '<li class="info">Internleie: finished step ' .$result. '</li>';
					flush();
				}
				
				$this->location_id = array_search('contract_type_innleie', $types);
				$this->path = phpgwapi_cache::session_get('rental', 'import_path') . '/Innleie';
				
				$result = $this->import(); // Do import step, result determines if finished for this area
				echo '<li class="info">Innleie: finished step ' .$result. '</li>';
				while($result != '6')
				{
					$result = $this->import();
					echo '<li class="info">Innleie: finished step ' .$result. '</li>';
					flush();
				}
				echo "</ul>";
				
				/*if(phpgw::get_var("importstep") == 'contract_type_eksternleie' && $result == '6') {
					$types = rental_socontract::get_instance()->get_fields_of_responsibility();
					$location_id = array_search('contract_type_internleie', $types);
					phpgwapi_cache::session_set('rental', 'import_path_folder','/Intern');
					$import_messages = phpgwapi_cache::session_get('rental', 'import_messages');
					$import_messages[] = "Finished importing responsibility area (". phpgw::get_var("importstep") .") on step " . $result . " of 6";
					phpgwapi_cache::session_set('rental', 'import_messages', $import_messages);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiimport.index', 'importstep' => 'contract_type_internleie', 'location_id' => $location_id));
					return;
				} 
				else if(phpgw::get_var("importstep") == 'contract_type_internleie' && $result == '6') 
				{
					$types = rental_socontract::get_instance()->get_fields_of_responsibility();
					$location_id = array_search('contract_type_innleie', $types);
					phpgwapi_cache::session_set('rental', 'import_path_folder','/Innleie');
					$import_messages = phpgwapi_cache::session_get('rental', 'import_messages');
					$import_messages[] = "Finished importing responsibility area (". phpgw::get_var("importstep") .") on step " . $result . " of 6";
					phpgwapi_cache::session_set('rental', 'import_messages', $import_messages);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiimport.index', 'importstep' => 'contract_type_innleie', 'location_id' => $location_id));
					return;
				} 
				else if(phpgw::get_var("importstep") == 'contract_type_innleie' && $result == '6')
				{
					$import_messages = phpgwapi_cache::session_get('rental', 'import_messages');
					$import_messages[] = "Import finished";
					$this->messages = $import_messages;
				}
				else
				{
					$import_messages = phpgwapi_cache::session_get('rental', 'import_messages');
					$import_messages[] = "Finished importing responsibility area (". phpgw::get_var("importstep") .") on step " . $result . " of 6";
					phpgwapi_cache::session_set('rental', 'import_messages', $import_messages);
					$url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiimport.index', 'importstep' => phpgw::get_var("importstep"), 'location_id' => phpgw::get_var("location_id")));	
					$header = lang('facilit_import');
					$p = RENTAL_TEMPLATE_PATH;
					echo "<html>\n<head>\n<title>Import fra Facilit</title>";
					echo "\n<meta http-equiv=\"refresh\" content=\"0; URL=$url\">";
					echo "\n</head>\n<body>";
					echo "<h1><img src='{$p}images/32x32/actions/document-save.png' /> {$header}</h1>";
					echo "<ul>";
					foreach ($import_messages as $message) {
						echo '<li class="info">' . $message . '</li>';
					}
					echo "</ul>";
					echo "\n</body>\n</html>";
					//flush();
					//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiimport.index', 'importstep' => phpgw::get_var("importstep"), 'location_id' => phpgw::get_var("location_id")));	
					return;
				}	*/
			}
			
			//Render import page
			$this->render('facilit_import.php', array(
				'messages' => $this->messages,
				'warnings' => $this->warnings,
				'errors' => $this->errors, 
				'button_label' => $this->import_button_label,
				'facilit_path' => $path,
				'location_id' => $this->location_id)
			);
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
					
			$steps = 6;
			
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
			
			
			// Import contract parts
			// Step result:
			if (!phpgwapi_cache::session_get('rental', 'facilit_parties')) {
				phpgwapi_cache::session_set('rental', 'facilit_parties', $this->import_parties()); 
				$this->import_button_label = "2/{$steps}: Continue to import composites";
                $this->log_messages(1);
				return '1';
			}
			
			// Import composites and units
			// Step result:
			if (!phpgwapi_cache::session_get('rental', 'facilit_composites')) {
				phpgwapi_cache::session_set('rental', 'facilit_composites', $this->import_composites());
				$this->import_button_label = "3/{$steps}: Continue to import composite-to-contract link table";
                $this->log_messages(2);
				return '2';
			}
			
			// Import composite to contract link table.  Assumes 1-1 link.
			// Step result:
			if (!phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract')) {
				phpgwapi_cache::session_set('rental', 'facilit_rentalobject_to_contract', $this->import_rentalobject_to_contract());
				$this->import_button_label = "4/{$steps}: Continue to import contracts";
                $this->log_messages(3);
				return '3';
			}
			
			// Import contracts
			// Prerequisites: Composites, parties, contract to composite bindings, and default values for accounts/project number for 
			// Step result:
			if (!phpgwapi_cache::session_get('rental', 'facilit_contracts')) {
				$composites = phpgwapi_cache::session_get('rental', 'facilit_composites');
				$rentalobject_to_contract = phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract');
				$parties = phpgwapi_cache::session_get('rental', 'facilit_parties');
				$location_id = $this->location_id;
				$defalt_values['account_in'] = rental_socontract::get_instance()->get_default_account($location_id, true); //IN
				$defalt_values['account_out'] = rental_socontract::get_instance()->get_default_account($location_id, false); //OUT
				$defalt_values['project_number'] = rental_socontract::get_instance()->get_default_project_number($location_id); //PROJECTNUMBER
				phpgwapi_cache::session_set('rental', 'facilit_contracts', $this->import_contracts($composites, $rentalobject_to_contract, $parties, $defalt_values));
				$this->import_button_label = "5/{$steps}: Continue to import contract price items";
                $this->log_messages(4);
				return '4';
			}
			
			// Import price items
			// Prerequisites: Contracts
			// Step result:
			if (!phpgwapi_cache::session_get('rental', 'facilit_contract_price_items')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				phpgwapi_cache::session_set('rental', 'facilit_contract_price_items', $this->import_contract_price_items($contracts));
				$this->import_button_label = "6/{$steps}: Continue to import events"; 
                $this->log_messages(5);
				return '5';
			}
			
			// Import events
			// Prerequistes: Contracts
			// Step result
			if (!phpgwapi_cache::session_get('rental', 'facilit_events')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				phpgwapi_cache::session_set('rental', 'facilit_events', $this->import_events($contracts));
                $this->log_messages(6);
                $this->clean_up();
				$this->import_button_label = "Import done";
				//return;
			}

			// We're done with the import, so clear all session variables so we're ready for a new one
			//phpgwapi_cache::session_clear('rental', 'facilit_parties');
			phpgwapi_cache::session_clear('rental', 'facilit_composites');
			phpgwapi_cache::session_clear('rental', 'facilit_rentalobject_to_contract');
			phpgwapi_cache::session_clear('rental', 'facilit_contracts');
			phpgwapi_cache::session_clear('rental', 'facilit_contract_price_items');
			phpgwapi_cache::session_clear('rental', 'facilit_events');
			return '6';
		}
		
		protected function import_parties()
		{
			$start_time = time();
			$soparty = rental_soparty::get_instance();
			$parties = array();			
			
			//Check to see if there is any parties in the database. If so, do not store these 
			//... double checking to  ensure that the user has not logged out and in again during import
			$alreay_imported_parties = false;
			$number_of_parties = $soparty->get_number_of_parties();
			if($number_of_parties > 0)
			{
				return;
			}
			
			$datalines = $this->getcsvdata($this->path . "/u_PersonForetak.csv", true);
			$this->messages[] = "Read CSV file in " . (time() - $start_time) . " seconds";
			$counter = 1;
			
			// Loop through each line of the file, parsing CSV data to a php array
			foreach ($datalines as $data) {
				if(count($data) <= 30)
				{
					continue;
				}
				
				// Create a new rental party we can fill with info from this line from the file
				$party = new rental_party();

				$identifier = $this->decode($data[24]); //cPersonForetaknr
				//Removed whitespace characters
				$identifier = str_replace(' ','',''.$identifier);
				
				// Fødselsnr/Foretaksnr/AgressoID
				$party->set_identifier($identifier);		
				
				// Default information
				$party->set_address_1($this->decode($data[3]));			//cAdresse1
				$party->set_address_2($this->decode($data[4]));			//cAdresse2
				$party->set_postal_code($this->decode($data[5]));		//cPostnr
				$party->set_mobile_phone($this->decode($data[7]));		//cMobil
				$party->set_phone($this->decode($data[8]));				//cTelefon
                $party->set_fax($this->decode($data[9]));				//cTelefaks
                $party->set_title($this->decode($data[12]));			//cArbeidstittel
                $party->set_email($this->decode($data[25]));			//cEpost
				$party->set_company_name($this->decode($data[10]));		//cArbeidsgiver
				$party->set_department($this->decode($data[11]));		//cAvdeling
				$party->set_account_number($this->decode($data[14]));	//cBankkontonr
				$party->set_reskontro($this->decode($data[23]));		//cReskontronr
				$party->set_comment($this->decode($data[26]));			//cMerknad
                
				$contact_person = $this->decode($data[6]);
				// Insert contract person in comment if present
				if(isset($contact_person)) {				
                    $party->set_comment($party->get_comment()."\n\nKontaktperson: ".$contact_person);	//cKontaktPerson
                }
                
           		$valid_identifier = false;
                switch(strlen(''.$identifier)) {	
                    case 4: // Intern organisasjonstilknytning
               			//Should be four number or on the form 'KFxx'     	
                    	if(
                    		is_numeric($identifier) 
                    		|| 
                    		((substr($identifier,0,2) == 'KF') && is_numeric(substr($identifier,2,2)))
                    	)
                    	{
	                        $party->set_company_name($this->decode($data[2]));	//cForetaksnavn
	                        $party->set_first_name(null);
	                        $party->set_last_name(null);
	                        
	                        // Get location ID
	                        $locations = $GLOBALS['phpgw']->locations;
	                        $subs = $locations->get_subs_from_pattern('rental', '.ORG.BK.__.'.$this->decode($data[24]));	//cPersonForetaknr
	                        $party->set_location_id($subs[0]['location_id']);
                    		$valid_identifier = true;
                    	}
                    	break;
                    case 6: // Foretak (agresso-id)
                    case 9: // Foretak (org.nr)
                    	if(is_numeric($identifier))
                    	{
	                        $party->set_company_name($this->decode($data[2]));	//cForetaksnavn  
	                        $party->set_first_name(null);
	                        $party->set_last_name(null);
	                        
	                        
                    	}
                    	break;
                    case 11: // Personnr
                    	if(is_numeric($identifier))
                    	{
	                        if (!$this->is_null($data[0])) {
	                            $party->set_first_name($this->decode($data[0]));	//cFornavn
	                            $party->set_last_name($this->decode($data[1]));		//cEtternavn
	                        } else {
	                            $company_name = explode(' ', $this->decode($data[2]), 2);	//cForetaksnavn
	                            $party->set_first_name($company_name[0]);					//cFornavn
	                            $party->set_last_name($company_name[1]);					//cEtternavn
	                        }
	                        $valid_identifier = true;
	                       
                    	}
                    	break;
                }
                
                if(!$valid_identifier)
                {
                    $party->set_first_name($this->decode($data[0]));		//cFornavn
                	$party->set_last_name($this->decode($data[1]));			//cEtternavn
                    $party->set_company_name($this->decode($data[2]));		//cForetaksnavn
                    $party->set_is_inactive(true);
                    $this->warnings[] = "Party with unknown 'cPersonForetaknr' format ({$identifier}). Setting as inactive.";	//cPersonForetaknr
                }

				// Store party and log message
				if ($soparty->store($party)) 
				{
					// Add party to collection of parties keyed by its facilit ID so we can refer to it later.
					$facilit_id = $data[17];	//nPersonForetakId
					$parties[$facilit_id] = $party->get_id();
					$this->messages[] = "Successfully added party " . $party->get_name() . " (" . $party->get_id() . ")";
				} 
				else 
				{
					$this->errors[] = "Failed to store party " . $party->get_name();
				}
			}
			
			$this->messages[] = "Successfully imported " . count($parties) . " contract parties. (" . (time() - $start_time) . " seconds)";

			return $parties;
		}
		
		protected function import_composites()
		{
			$start_time = time();
			
			// Storage objects
			$socomposite = rental_socomposite::get_instance();
			$socontract = rental_socontract::get_instance();
			$sounit = rental_sounit::get_instance();
			
			// Array for mapping the composite ids to the facilit ids
			$composites = array();
			
			//Read source data
			$datalines = $this->getcsvdata($this->path . "/u_Leieobjekt.csv");
			$this->messages[] = "Read CSV file in " . (time() - $start_time) . " seconds";
			
			foreach ($datalines as $data) {
				
				if(count($data) <= 34)
				{
					continue;
				}
				
				//If the composite differs in terms of object number the custom address should be set (default false) 
				$set_custom_address = false;
				
				//Retrieve the title for the responsibility area we are importing (to hande the respoonsibility areas differently)
				$title = $socontract->get_responsibility_title($this->location_id);
				
				// Variable for the location code (objektnummer)
				$loc1 = null;
				
				//Three columns for detemining the correct object number
				$object_identifier = trim($this->decode($data[1]));		//cLeieobjektnr
				$property_identifier = trim($this->decode($data[4]));		//cInstNr
				$building_identifier = trim($this->decode($data[5]));		//cByggNr
				
				
				if($title == 'contract_type_internleie')
				{
					$property_ok = false;
					
					//Priority 1: The property identifier (most up to date)
					if(isset($property_identifier))
					{
						$correct_length_property = strlen($property_identifier) == 4 ? true : false;
						$integer_value_property = ((int) $property_identifier) > 0 ? true : false;
						if($correct_length_property && $integer_value_property)
						{
							$loc1 = $property_identifier;
							$property_ok = true;
						}
					}
	
					//Priority 2: Use the object identifier
					if(isset($object_identifier))
					{	
						$correct_length = strlen($object_identifier) == 6 ? true : false;
						$integer_value = ((int) $object_identifier) > 0 ? true : false;

						if($correct_length && $integer_value)
						{
							if($property_ok)
							{
								 // ... add only the building number if the property number is ok
								$loc1 = $loc1 . "-" . substr($object_identifier, 4, 2);
							}
							else
							{
								// ... just use the object identifier if not
								$loc1 = substr_replace($object_identifier,"-",4,0);	
							}
						}
						else
						{
							// Using non-conforming object identifier. Gives a warning.
							$loc1 = $object_identifier;
							$set_custom_address = true;
							$this->warnings[] = "Composite (internal contract) have wrong object-number ({$loc1}). Should consist of 6 numbers. Setting custom address.";
						}
					}
					else if($property_ok)
					{
						//If no object number, only property number
						$set_custom_address = true;
						$this->warnings[] = "Composite (internal contract) have no object-number ({$object_identifier}). Using property identifier. Setting custom address.";
					}
					
					if(!isset($loc1))
					{
						// No data exist to determine the object number
						$this->warnings[] = "No data exist to determine the object number. Setting custom address.";
						$set_custom_address = true;
					}
				}
				else if($title == 'contract_type_eksternleie')
				{
					// Two forms for object number (xxxx.xxxx) AND (xxxx.xxxxxx.xxxx)
					$parts = explode('.',$object_identifier);
					
					for( $i = 0; $i < count($parts); $i++)
					{
						$parts[$i] = trim($parts[$i]);
					}
					
					if(count($parts) == 2) // (xxxx.xxxx)
					{	
						//Checking parts for correct length
						$correct_length1 = strlen($parts[0]) == 4 ? true : false;
						$correct_length2 = strlen($parts[1]) == 4 ? true : false;
						
						if($correct_length1 && $correct_length2)
						{	
							//If the first part contains any characters from the alphabet
							if(!is_numeric($parts[0]))
							{
								// ... relace the punctuation with an '-'
								$loc1 = $parts[0] . "-" . $parts[1];
							}
						}
					}
					else if(count($parts) == 3) // (xxxx.xxxxxx.xxxx)
					{
						$correct_length = strlen($parts[1]) == 6 ? true : false;
						$correct_length_property = strlen($property_identifier) == 4 ? true : false;
						
						if($correct_length && is_numeric($parts[1]))
						{
							if(isset($property_identifier) && $correct_length_property)
							{
								 // ... add only the building number if the property number is ok
								$loc1 = $property_identifier . "-" . substr($parts[1], 4, 2);
							}
							else
							{
								// ... insert a '-' at position 4 if not
								$loc1 = substr_replace($parts[1],"-",4,0);
							}
						}
					}
					
					// If the object identifier is non-conforming
					
					// Alernative 1: Try to use the buiding identifier 
					if(!isset($loc1) && isset($building_identifier))
					{
						$correct_length = strlen($building_identifier) == 6 ? true : false;
						if($correct_length && is_numeric($building_identifier))
						{
							$loc1 = substr_replace($building_identifier,"-",4,0);
							$set_custom_address = true;
							$this->warnings[] = "Composite (external) lacks conforming object number ({$object_identifier}). Using building identifier ({$loc1}). Setting custom address.";
						}
					} 
						
					// Alternative 2: Try to use the property identifier
					if(!isset($loc1) && isset($property_identifier))
					{
						$correct_length = strlen($property_identifier) == 4 ? true : false;
						if($correct_length)
						{
							//Give a warning
							$loc1 = $property_identifier;
							$set_custom_address = true;
							$this->warnings[] = "Composite (external) lacks conforming object number ({$object_identifier}). Using property identifier ({$loc1}). Setting custom address.";
						}	
					}
					
					 // Alternative 3: Use the non-conforming object number	
					if(!isset($loc1))
					{
						$loc1 = $object_identifier;
						$set_custom_address = true;
						$this->warnings[] = "Composite (external) lacks data to create an object number. Using object number ({$loc1}) Setting custom address.";
					}
				}
				else if($title == 'contract_type_innleie')
				{
					$correct_length = strlen($building_identifier) == 6 ? true : false;
					$integer_value = ((int) $building_identifier) > 0 ? true : false;
					$correct_length_property = strlen($property_identifier) == 4 ? true : false;
					if($correct_length && $integer_value)
					{
						if(isset($property_identifier) && $correct_length_property)
						{
							 // ... add only the building number if the property number is ok
							$loc1 = $property_identifier . "-" . substr($building_identifier, 4, 2);
						}
						else
						{
							$loc1 = substr_replace($building_identifier,"-",4,0);
							
						}
					}
					else if(isset($property_identifier) && $correct_length_property)
					{
						 // ... add only the building number if the property number is ok
						$loc1 = $property_identifier;
						$set_custom_address = true;
						$this->warnings[] = "Composite (innleie) has non-conforming building identifier ({$building_identifier}). Using property identifier instead ({$loc1}). Setting custom address.";
					}
					
					if(!isset($loc1))
					{
						$loc1 = $object_identifier;								
						$set_custom_address = true;
						$this->warnings[] = "Composite (innleie) lacks building identifier/property identifier ({$building_identifier}/{$property_identifier}). Using object identifier instead ({$loc1}). Setting custom address."; 
					}	
				}
				else
				{
					$this->errors[] = "The type of import ({$title}) is invalid";
				}
				
				

				//Retrieve 
				//$comps = $socomposite->get(0, 1, null, null, null, null, array('location_code' => $loc1));
				//$composite = $comps[0];
				
				//if(!isset($composite))
				//{
				
				$composite = new rental_composite();
				
				// Use the first address line as name if no name
				$name = $this->decode($data[26]);		//cLeieobjektnavn
				$address1 = $this->decode($data[6]);	//cAdresse1
				if(!isset($name)){
					$name = $address1;
				}
				
				if($set_custom_address)
				{
					// Set address
					$composite->set_custom_address_1($address1);
					$composite->set_custom_address_2($this->decode($data[7]));
					$composite->set_custom_postcode($this->decode($data[8]));
					$composite->set_has_custom_address(true);
				}
				
				$composite->set_name($name);
				$composite->set_description($this->decode($data[3]));		//cLeieobjektBeskrivelse
                $composite->set_object_type_id($this->decode($data[25]));	//nLeieobjektTypeId
                $composite->set_area($this->decode($data[2]));				//nMengde
				$composite->set_is_active($data[19] == "-1");				//bTilgjengelig
			
				// Store composite
				if ($socomposite->store($composite)) {
					// Add composite to collection of composite so we can refer to it later.
					$composites[$data[0]] = $composite->get_id();
				
					// Add units only if composite stored ok.
					$res = $sounit->store(new rental_unit(null, $composite->get_id(), new rental_property_location($loc1, null)));
					$this->messages[] = "Successfully added composite " . $composite->get_name() . " (" . $composite->get_id() . ")";
					if($res)
					{
						$this->messages[] = "Successfully added unit " . $loc1 . " to composite (" . $composite->get_id() . ")";
					}
					
				} else {
					$this->errors[] = "Failed to store composite " . $composite->get_name();
				}
				/*}
				else
				{
					$this->messages[] = "Loaded already existing composite " . $composite->get_name() . " (" . $composite->get_id() . ") with ";
					// Add composite to collection of composite so we can refer to it later.
					$composites[$data[0]] = $composite->get_id();	
				}*/
				
				
			}
			
			$this->messages[] = "Successfully imported " . count($composites) . " composites (" . (time() - $start_time) . " seconds)";

			return $composites;
		}
		
		protected function import_rentalobject_to_contract()
		{
			$rentalobject_to_contract = array();
			$datalines = $this->getcsvdata($this->path . "/u_Leieobjekt_Kontrakt.csv");
			
			foreach ($datalines as $data) {
				// Array with Facilit Contract ID => Facilit composite ID
				$rentalobject_to_contract[$data[1]] = $data[0];
			}
			
			$this->messages[] = "Successfully imported " . count($rentalobject_to_contract) . " contract links";

			return $rentalobject_to_contract;
		}
		
		/**
		 * Step 4: import the contracts from the file 'u_Kontrakt.csv'
		 * @param $composites	array mapping facilit ids and protico ids for composites
		 * @param $rentalobject_to_contract	array mapping composites and contracts
		 * @param $parties	array mapping party ids
		 * @param $default_values	the default accounts and project numbers
		 * @return array	of contracts
		 */
		protected function import_contracts($composites, $rentalobject_to_contract, $parties, $default_values)
		{
			$start_time = time();
			$socontract = rental_socontract::get_instance();
			$contracts = array();
			$datalines = $this->getcsvdata($this->path . "/u_Kontrakt.csv");
			$this->messages[] = "Read CSV file in " . (time() - $start_time) . " seconds";

            // Old->new ID mapping
            $contract_types = array(
                2 => 2, // "Internleie - innleid" -> Innleie
                3 => 1, // "Internleie - egne" -> Egne
                4 => 8, // "Tidsbegrenset" -> Annen (ekstern)
                5 => 4, // "Internleie - KF" -> KF
                12=> 5, // "Eksten Feste" -> Feste
                13=> 7, // "Ekstern Leilighet" -> Leilighet
                14=> 8, // "Ekstern Annen" -> Annen
                15=> 3, // "Intern - I-kontrakt" -> Inversteringskontrakt
                17=> NULL, // "Innleie" -> null
                18=> 8, // "Ekstern KF" -> Annen
                19=> 8  // "Ekstern I-kontrakt" -> Annen
            );
            
			foreach ($datalines as $data) {
				// Skip this contract if its data is incomplete 
				if(count($data) <= 27)
				{
					continue;
				}
				
				// Create a new contract object
				$contract = new rental_contract();
				
				// Set the contract dates
				$date_start = $this->decode($data[3]);						//dFra
				$date_end = $this->decode($data[4]);						//dTil
				$contract->set_contract_date(new rental_contract_date(strtotime($date_start), strtotime($date_end)));

                // Set the old contract identifier
				$contract->set_old_contract_id($this->decode($data[5]));	//cKontraktnr
				
				// Set the contract biling term
				$term = $data[10];											//nTermin
				switch ($term) {
					case 1: // Monthly
						$contract->set_term_id(1);
						break;
					case 2: // Quarterly
						$contract->set_term_id(4);
						break;
                    case 4: // Half-year
                        $contract->set_term_id(3);
                        break;
                    case 5: // Yearly
                        $contract->set_term_id(2);
                        break;
				}
				
				// Report non-conforming price periods 
				$price_period = $data[14];										//nPrisPeriode (4=month, 8=year)
				if ($price_period == 4) {
					// The price period is month.  We ignore this but print a warning.
					$this->warnings[] = "Price period of contract " . $contract->get_old_contract_id() . " is month.  Ignored.";
				}
                elseif($price_period == 5) {
                    // The price period is 5, which is unknown.  We ignore this but print a warning.
					$this->warnings[] = "Price period of contract " . $contract->get_old_contract_id() . " is unknown (value: 5).  Ignored.";
                }

                // Report contracts under dismissal. Send warning if contract status is '3' (Under avslutning)
                if($data[6] == 3) {
                    $this->warnings[] = "Status of contract " . $contract->get_old_contract_id() . " is '".lang('contract_under_dismissal')."'";
                }
				
                // Set the billing start date for the contract
				$contract->set_billing_start_date(strtotime($this->decode($data[16])));		//dFakturaFraDato
				
				// Deres ref.
				$contract->set_invoice_header($this->decode($data[17]));					//cFakturaRef
				$contract->set_comment($this->decode($data[18]));							//cMerknad
                $contract->set_contract_type_id($contract_types[$this->decode($data[1])]);	//
				
				// Ansvar/Tjenestested: F.eks: 080400.13000
				$ansvar_tjeneste = $this->decode($data[26]);								//cSikkerhetsTekst
				$ansvar_tjeneste_components = explode(".", $ansvar_tjeneste);
				$contract->set_responsibility_id($ansvar_tjeneste_components[0]);
				$contract->set_service_id($ansvar_tjeneste_components[1]);
				
				// Set the location identifier (responsibiity area)
				$contract->set_location_id($this->location_id);

				// Get the composite identifier for the composite included in this contract
                $composite_id = $composites[$rentalobject_to_contract[$data[0]]];

                // Retrieve the title for the responsibility area we are importing (to hande the respoonsibility areas differently)
				$title = $socontract->get_responsibility_title($this->location_id);
                
                // For external contract types the rented area resides on the composite ...
                if($title == 'contract_type_eksternleie') {
                	if($composite_id)
                	{
                    	$socomposite = rental_socomposite::get_instance();
                    	$contract->set_rented_area($socomposite->get_area($composite_id));
                	}	
                } 
                else 
                {
                	// ... and for others contract types the rented area resides on the contract
                	$contract->set_rented_area($this->decode($data[21]));
                }
                
                
                // Retrieve default values for accounts and project numbers
				if($title == 'contract_type_eksternleie')
				{
					// The account out depends on the typer of external contract
					$type_id = $contract->get_contract_type_id();
					if(isset($type_id) && $type_id > 0)
					{
						$account = $socontract->get_contract_type_account($type_id);
						$contract->set_account_out($account);
					}
					else
					{
						// If no specific external contract type, use default value
						$contract->set_account_out($default_values['account_out']);
					}
				}
				else if($title == 'contract_type_internleie')
				{
					//Set default account in/out and project numbers for internal contracts
               		$contract->set_account_in($default_values['account_in']);
					$contract->set_account_out($default_values['account_out']);
					$contract->set_project_id($default_values['project_number']);
				}
				
				// Store contract
				if ($socontract->store($contract)) {
					 // Map contract ids in Facilit and PE contract id (should be the same)
					$contracts[$data[0]] = $contract->get_id();
					
					// Check if this contract has a composite and if so add rental composite to contract
					if (!$this->is_null($rentalobject_to_contract[$data[0]]) && !$this->is_null($composite_id)) {
						$socontract->add_composite($contract->get_id(), $composite_id);
					}
					
					// Check if this contract has a contract part and if so add party to contract
					if (!$this->is_null($data[2])) { 															//nPersonForetakId
						$party_id = $parties[$this->decode($data[2])];
						$socontract->add_party($contract->get_id(), $party_id);
						// Set this party to be the contract invoice recipient
						$socontract->set_payer($contract->get_id(), $party_id);
					}
					
					$this->messages[] = "Successfully added contract (" . $contract->get_id() . "/". $contract->get_old_contract_id() .")";
				} else {
					$this->errors[] = "Failed to store contract " . $this->decode($data[5]);
				}
			}
			
			$this->messages[] = "Successfully imported " . count($contracts) . " contracts. (" . (time() - $start_time) . " seconds)";

			return $contracts;
		}
		
		protected function import_contract_price_items($contracts)
		{
			$start_time = time();
			$soprice_item = rental_soprice_item::get_instance();
			$socontract_price_item = rental_socontract_price_item::get_instance();
			$socontract = rental_socontract::get_instance();
			
			// Read priselementdetaljkontrakt list first so we can create our complete price items in the next loop
			// This is an array keyed by the main price item ID
			$detail_price_items = array();
			$datalines = $this->getcsvdata($this->path . "/u_PrisElementDetaljKontrakt.csv");
			
			
			foreach ($datalines as $data) {			//Felt fra 'PrisElementDetaljKontrakt'
				if(count($data) <= 10)
				{
					continue;
				}
				
				//Create a row in the array holding the details (price, amount, dates) for the price item
				$detail_price_items[$data[1]] = 	//nPrisElementId
				array(
					'price' => $data[2],			//nPris
					'amount' => $data[3],			//nMengde
					'date_start' => null,			//dGjelderFra	
					'date_end' =>  null				//dGjelderTil
				);
				
				if (!$this->is_null($data[4])) {
					$detail_price_items[$data[1]]['date_start'] = strtotime($this->decode($data[4]));
				}
				if (!$this->is_null($data[5])) {
					$detail_price_items[$data[1]]['date_end'] = strtotime($this->decode($data[5]));
				}
			}
			
			$datalines = $this->getcsvdata($this->path . "/u_PrisElementKontrakt.csv");
			
			//Retrieve the title for the responsibility area we are importing (to hande the respoonsibility areas differently)
			$title = $socontract->get_responsibility_title($this->location_id);
			//If we are importing price items for 'Innleie', we have a default price item in the 'Prisbok' with agresso-id 'INNLEIE'
			if($title == 'contract_type_innleie'){
				$admin_price_item = $soprice_item->get_single_with_id('INNLEIE');
			}
			
			foreach ($datalines as $data) {
				if(count($data) <= 24)
				{
					continue;
				}
				
				/* If we are importing contract price items for external or internal:
				 * - see if a pricebook element exist
				 */
				if($title != 'contract_type_innleie')
				{
					// The Agresso-ID is unique for price items
					$id = $this->decode($data[12]);					//cVarenr				
					$admin_price_item = null;
					if(isset($id) && $id != '')
					{
						$admin_price_item = $soprice_item->get_single_with_id($id);
					}
					else
					{
						$admin_price_item = $soprice_item->get_single_with_id('UNKNOWN');
					}
				}
				
				// Get the facilit price item id so that we can retrieve the price item details
				$facilit_id = $this->decode($data[0]);							//nPrisElementId
				
				/* Create a new pricebook price item if one does not exist in the pricebook; store it if it has a new unique agresso-id. 
				 * Note: First price item with unique agresso-id determines title, area or "nr of items", and the price (from the price item details) */
				if ($admin_price_item == null) {
					$admin_price_item = new rental_price_item();
					$admin_price_item->set_title($this->decode($data[3]));								//cPrisElementNavn
					$admin_price_item->set_agresso_id($id);												//cVareNr
					// This assumes 1 for AREA, and anything else for count, even blanks
					$admin_price_item->set_is_area($this->decode($data[4]) == '1' ? true : false);		//nMengdeTypeId
					// Get the price for this price item
					$admin_price_item->set_price($detail_price_items[$facilit_id]['price']);
					$admin_price_item->set_responsibility_id($this->location_id);
					
					if(isset($id))
					{
						$soprice_item->store($admin_price_item);
						$this->messages[] = "Stored price item {$id} in with title " . $admin_price_item->get_title() . " in 'Prisbok'";
					}
				}
				
				$contract_id = $contracts[$this->decode($data[1])];				//nKontraktId
				
				if ($contract_id) {
					// Create a new contract price item that we can tie to our contract
					$price_item = new rental_contract_price_item();
					
					// Retrieve the contract
					$contract = $socontract->get_single($contract_id);

					// Set cLonnsArt for price item as contract reference
					$contract->set_reference($this->decode($data[13]));
			
					// The contract price item title should be the same as in the price book for external and internal
					if($title != 'contract_type_innleie')
					{
						$price_item->set_title($admin_price_item->get_title());
					}
					else
					{
						// ... and overridden by the price item for innleie
						$price_item->set_title($data[3]);
					}
					
					// Set the price book element's agresso-id and type (area/piece) 
					$price_item->set_agresso_id($admin_price_item->get_agresso_id());
					
					// If the price item is unknown do not use the 'is_area' from the price book
					if($admin_price_item->get_agresso_id() != 'UNKNOWN')
					{
						$price_item->set_is_area($admin_price_item->is_area());
					}
					else
					{
						$price_item->set_is_area($this->decode($data[4]) == '1' ? true : false);
					}	
                    
					// Get the price for the price item details
					$price_item->set_price($detail_price_items[$facilit_id]['price']);
                    
                    // Give a warning if a contract has a price element of type area with are like 1
                   	if($price_item->is_area() && ($detail_price_items[$facilit_id]['amount'] == '1'))
                   	{
                   		$this->warning[] = "Contract " . $contract->get_old_contract_id() . " has a price item of type area with amount like 1";
                   	}
                    
					// Tie this price item to its parent admin price item
					$price_item->set_price_item_id($admin_price_item->get_id());
					
					if ($admin_price_item->is_area()) {
                        
                            $rented_area = $contract->get_rented_area();
                            if(isset($rented_area))
                            {
                            	if($detail_price_items[$facilit_id]['amount'] != $rented_area)
                            	{
                            		$this->warning[] = "Price item {$id} - (Facilit ID {$facilit_id}) has area " . $detail_price_items[$facilit_id]['amount'] 
                            		. " while contract {$contract_id} already has rented area {$rented_area}. Using rented area on contract." ;
                            	}
                            }
                            else
                            {
                            	//Store price item area on contract if the contract has no area (not from contract)
                            	$contract->set_rented_area($detail_price_items[$facilit_id]['amount']);
                            	//Store the contract
                           		$socontract->store($contract);
                            }
              
                        	
                        	// Set the the contract area on the price item
                            $price_item->set_area($contract->get_rented_area());

                            //Calculate the total price for the price item
                            $price_item->set_total_price($price_item->get_area() * $price_item->get_price());
                        
					} 
					else 
					{
						$price_item->set_count($detail_price_items[$facilit_id]['amount']);
						$price_item->set_total_price($price_item->get_count() * $price_item->get_price());
					}
					
					$price_item->set_date_start($detail_price_items[$facilit_id]['date_start']);
					$price_item->set_date_end($detail_price_items[$facilit_id]['date_end']);
				
					// Tie the price item to the contract it belongs to
					$price_item->set_contract_id($contract_id);
					
					// Tie this price item to its parent admin price item
					$price_item->set_price_item_id($admin_price_item->get_id());
					
					// .. and save
					if($socontract_price_item->import($price_item)) {
                        $this->messages[] = "Successfully imported price item ({$id}) for contract {$contract_id}";
                    }
                    else {
                        $this->warning[] = "Could not store price item ({$id}) - " . $price_item->get_title();
                    }
				} else {
					$this->warning[] = "Skipped price item with no contract attached: " . join(", ", $data);
				}
			}
			
			$this->messages[] = "Imported contract price items. (" . (time() - $start_time) . " seconds)";

            return true;
		}
		
		protected function import_events($contracts)
		{
			$start_time = time();
			
			$sonotification = rental_sonotification::get_instance();
			
			$datalines = $this->getcsvdata($this->path . "/u_Hendelse.csv");
			
			foreach ($datalines as $data) {
				$type_id = $data[2];
				
				// We do not import adjustments.  And only import if there is a title.
				if ($type_id != 1 && $type_id != '1' && !$this->is_null($data[3])) {
					$date = strtotime($this->decode($data[7]));
					$contract_id = $contracts[$data[1]];
					$location_id = $this->location_id;
					
					$title = $this->decode($data[3]);
					if (!$this->is_null($data[4])) {
						$title .= " " . $this->decode($data[4]);
					}
					
					$repeating = ($data[7] == '0');
					$interval = $data[8];
					
					if ($repeating && ($interval > 1) || ($contract_id == 0)) {
						$this->warnings[] = "Skipping price item " . $data[0] . " because the repeat interval is larger than 1 year or it has no contract.";
					} else {
						// All is good, store notification
						$notification = new rental_notification(null, null, $location_id, $contract_id, $date, $title);
						if ($sonotification->store($notification)) {
							$this->messages[] = "Successfully imported event '" . $notification->get_message() . "' for contract {$contract_id}";
						} else {
							$this->errors[] = "Error importing event " . $notification->get_message() . " for contract {$contract_id}";
						}
					}
				} else {
					$this->warnings[] = "Skipping price item " . $data[0] . " because it has no title or is an adjustment (regulering).";
				}
			}
			
			$this->messages[] = "Imported events. (" . (time() - $start_time) . " seconds)";

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
            $msgs = array_merge($this->errors, $this->warnings, $this->messages);
            $path = $this->path;

            if(is_dir($path.'/logs') || mkdir($path.'/logs')) {
                file_put_contents("$path/logs/$step.log", implode(PHP_EOL, $msgs));
            }
            else { // Path not writeable

            }
        }
	}
?>
