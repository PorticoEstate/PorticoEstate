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
			set_time_limit(3000);
			
            /*if (!phpgwapi_cache::session_get('rental', 'msgarchive')) {
                $this->msgarchive = array(date().': Import started');
                phpgwapi_cache::session_set('rental', 'msgarchive', $this->msgarchive);
            }
            else {
                $this->msgarchive = phpgwapi_cache::session_get('rental', 'msgarchive');
            }*/
		}
		
		public function query()
		{
			// Do nothing
		}

		public function index()
		{
			setlocale(LC_ALL, 'no_NO');
			
			// Set the submit button label to its initial state
			$this->import_button_label = "Start import";
			
			$path = phpgw::get_var("facilit_path") ? phpgw::get_var("facilit_path") : '/home/notroot/FacilitExport';
			if (phpgw::get_var("importsubmit")) {
				$this->path = $path;
				$this->messages = array();
				$this->warnings = array();
				$this->errors = array();
				$result = $this->import($path);
			} else if (phpgw::get_var("cancelsubmit")) {
				// User cancelled import, clear session variables so we're ready to start over
				phpgwapi_cache::session_clear('rental', 'facilit_parties');
				phpgwapi_cache::session_clear('rental', 'facilit_composites');
				phpgwapi_cache::session_clear('rental', 'facilit_rentalobject_to_contract');
				phpgwapi_cache::session_clear('rental', 'facilit_contracts');
				phpgwapi_cache::session_clear('rental', 'facilit_contract_price_items');
				phpgwapi_cache::session_clear('rental', 'facilit_composite_price_items');
				phpgwapi_cache::session_clear('rental', 'facilit_events');
				
				$this->messages = array("Import reset");
			}
			
			$this->render('facilit_import.php', array(
				'messages' => $this->messages,
				'warnings' => $this->warnings,
				'errors' => $this->errors, 
				'button_label' => $this->import_button_label,
				'facilit_path' => $path,
				'location_id' => phpgw::get_var("location_id"))
			);
		}
		
		/**
		 * Import Facilit data to Portico Estate's rental module
		 * The function assumes CSV files have been uploaded to a location on the server reachable by the
		 * web server user.  The CSV files must correspond to the table names from Facilit, as exported
		 * from Access.  Field should be enclosed in single quotes and separated by comma.  The CSV files
		 * must contain the column headers on the first line.
		 * 
		 * @return unknown_type
		 */
		public function import()
		{
			$steps = 6;
			// TODO: For each import type, check what we need as a minimum information for each before saving
			
			// TODO: Remove after testing
			//phpgwapi_cache::session_set('rental', 'facilit_parties', true);
			//phpgwapi_cache::session_set('rental', 'facilit_composites', true);
			
			
			// Import rental parties
			if (!phpgwapi_cache::session_get('rental', 'facilit_parties')) {
				phpgwapi_cache::session_set('rental', 'facilit_parties', $this->import_parties());
				$this->import_button_label = "2/{$steps}: Continue to import composites";
                $this->log_messages(1);
				return;
			}
			
			// Import composites and units
			if (!phpgwapi_cache::session_get('rental', 'facilit_composites')) {
				phpgwapi_cache::session_set('rental', 'facilit_composites', $this->import_composites());
				$this->import_button_label = "3/{$steps}: Continue to import composite-to-contract link table";
                $this->log_messages(2);
				return;
			}
			
			// Import composite to contract link table.  Assumes 1-1 link.
			if (!phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract')) {
				phpgwapi_cache::session_set('rental', 'facilit_rentalobject_to_contract', $this->import_rentalobject_to_contract());
				$this->import_button_label = "4/{$steps}: Continue to import contracts";
                $this->log_messages(3);
				return;
			}
			
			// Import contracts
			if (!phpgwapi_cache::session_get('rental', 'facilit_contracts')) {
				$composites = phpgwapi_cache::session_get('rental', 'facilit_composites');
				$rentalobject_to_contract = phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract');
				$parties = phpgwapi_cache::session_get('rental', 'facilit_parties');
				$location_id = phpgw::get_var("location_id");
				$defalt_values['account_in'] = rental_socontract::get_instance()->get_default_account($location_id, true); //IN
				$defalt_values['account_out'] = rental_socontract::get_instance()->get_default_account($location_id, false); //OUT
				$defalt_values['project_number'] = rental_socontract::get_instance()->get_default_project_number($location_id); //PROJECTNUMBER
				phpgwapi_cache::session_set('rental', 'facilit_contracts', $this->import_contracts($composites, $rentalobject_to_contract, $parties, $defalt_values));
				$this->import_button_label = "5/{$steps}: Continue to import contract price items";
                $this->log_messages(4);
				return;
			}
			
			// Import price items
			if (!phpgwapi_cache::session_get('rental', 'facilit_contract_price_items')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				phpgwapi_cache::session_set('rental', 'facilit_contract_price_items', $this->import_contract_price_items($contracts));
				$this->import_button_label = "6/{$steps}: Continue to import events"; 
                $this->log_messages(5);
				return;
			}
			
			// Import price items - This information should not be imported
			/*if (!phpgwapi_cache::session_get('rental', 'facilit_composite_price_items')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				$rentalobject_to_contract = phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract');
				phpgwapi_cache::session_set('rental', 'facilit_composite_price_items', $this->import_composite_price_items($contracts, $rentalobject_to_contract));
				$this->import_button_label = "7/{$steps}: Continue to import events";
                $this->log_messages(6);
				return;
			}*/
			
			// Import events
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
			//phpgwapi_cache::session_clear('rental', 'facilit_composite_price_items');
			phpgwapi_cache::session_clear('rental', 'facilit_facilit_events');
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
				//Check for only digits
				$int_value_of_identifier = (int) $identifier;
				
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
                
				// Insert contract person in comment if present
				if(strlen($this->decode($data[6]) > 1)) {				
                    $party->set_comment($party->get_comment()."\n\nKontaktperson: ".$this->decode($data[6]));	//cKontaktPerson
                }
                
                // If the identifier contains only numbers
                if($int_value_of_identifier > 0 )
                {
	                switch(strlen(''.$identifier)) {	
	                    case 4: // Intern organisasjonstilknytning
	                        $party->set_company_name($this->decode($data[2]));	//cForetaksnavn
	                        $party->set_first_name(null);
	                        $party->set_last_name(null);
	                        
	                        // Get location ID
	                        $locations = $GLOBALS['phpgw']->locations;
	                        $subs = $locations->get_subs_from_pattern('rental', '.ORG.BK.__.'.$this->decode($data[24]));	//cPersonForetaknr
	                        $party->set_location_id($subs[0]['location_id']);
	                        break;
	                    case 6: // Foretak (agresso-id)
	                    case 9: // Foretak (org.nr)
	                        $party->set_company_name($this->decode($data[2]));	//cForetaksnavn  
	                        $party->set_first_name(null);
	                        $party->set_last_name(null);
	                        break;
	                    case 11: // Personnr
	                        if (!$this->is_null($data[0])) {
	                            $party->set_first_name($this->decode($data[0]));	//cFornavn
	                            $party->set_last_name($this->decode($data[1]));		//cEtternavn
	                        } else {
	                            $company_name = explode(' ', $this->decode($data[2]), 2);	//cForetaksnavn
	                            $party->set_first_name($company_name[0]);					//cFornavn
	                            $party->set_last_name($company_name[1]);					//cEtternavn
	                        }
	                        break;
	                    default:
	                        $party->set_first_name($this->decode($data[0]));		//cFornavn
	                        $party->set_last_name($this->decode($data[1]));			//cEtternavn
	                        $party->set_company_name($this->decode($data[2]));		//cForetaksnavn
	                        $party->set_is_inactive(true);
	                        $this->warnings[] = "Party with unknown 'cPersonForetaknr' format ({$identifier}). Setting as inactive.";	//cPersonForetaknr
	                }
                }
                else
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
				$title = $socontract->get_responsibility_title(phpgw::get_var("location_id"));
				
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
				
				if($set_custom_address)
				{
					// Set address
					$composite->set_custom_address_1($address1);
					$composite->set_custom_address_2($this->decode($data[7]));
					$composite->set_custom_postcode($this->decode($data[8]));
					$composite->set_has_custom_address(true);
				}

				//Get 
				$comps = $socomposite->get(0, 1, null, null, null, null, array('location_code' => $loc1));
				
				$composite = $comps[0];
				
				if(!isset($composite))
				{
				
					$composite = new rental_composite();
					
					// Use the first address line as name if no name
					$name = $this->decode($data[26]);		//cLeieobjektnavn
					$address1 = $this->decode($data[6]);	//cAdresse1
					if(!isset($name)){
						$name = $address1;
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
				}
				else
				{
					$this->messages[] = "Loaded already existing composite " . $composite->get_name() . " (" . $composite->get_id() . ") with ";
					// Add composite to collection of composite so we can refer to it later.
					$composites[$data[0]] = $composite->get_id();	
				}
				
				
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
            
            $external_types = array($contract_types[12],$contract_types[13],$contract_types[14],$contract_types[18],$contract_types[19]);
            $internal_types = array($contract_types[2],$contract_types[3],$contract_types[5],$contract_types[15]);


			foreach ($datalines as $data) {
				if(count($data) <= 27)
				{
					continue;
				}
				
				$contract = new rental_contract();
				
				// TODO: link this with previously imported rental party. 
				$personId = $this->decode($data[2]);						//nPersonForetakId
				
				$date_start = $this->decode($data[3]);						//dFra
				$date_end = $this->decode($data[4]);						//dTil
				
				$contract->set_contract_date(new rental_contract_date(strtotime($date_start), strtotime($date_end)));

                $contract->set_old_contract_id($this->decode($data[5]));	//cKontraktnr
				
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
				
				// What period the prices are calculated from.  4=month, 8=year
				$price_period = $data[14];										//nPrisPeriode
				if ($price_period == 4) {
					// The price period is month.  We ignore this but print a warning.
					// TODO: What to use as reference here?  Currently using K-number
					$this->warnings[] = "Price period of contract " . $this->decode($data[5]) . " is month.  Ignored.";
				}
                elseif($price_period == 5) {
                    // The price period is 5, which is unknown.  We ignore this but print a warning.
					$this->warnings[] = "Price period of contract " . $this->decode($data[5]) . " is unknown (value: 5).  Ignored.";
                }

                // Send warning if contract status is '3' (Under avslutning)
                if($data[6] == 3) {
                    $this->warnings[] = "Status of contract " . $this->decode($data[5]) . " is '".lang('contract_under_dismissal')."'";
                }
				
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
				
				// Set the location ID according to what the user selected
				$contract->set_location_id(phpgw::get_var("location_id"));


                $composite_id = $composites[$rentalobject_to_contract[$data[0]]];

                // If composite_id has value and contract type is external
                if(in_array($contract->get_contract_type_id(), $external_types)) {
                	//specific logic for external responsibility area
                	if($composite_id)
                	{
                		// Get the rented area for this contract from the composite
                    	$socomposite = rental_socomposite::get_instance();
                    	$contract->set_rented_area($socomposite->get_area($composite_id));
                	}	
                } 
                else 
                {
                	// Get the rented area from the contract
                	$contract->set_rented_area($this->decode($data[21]));
                }
                
                //Get the account in/out and project number from database
                $contract->set_account_in($default_values['account_in']);
				$contract->set_account_out($default_values['account_out']);
				$contract->set_project_id($default_values['project_number']);
				
				// Store contract
				if ($socontract->store($contract)) {
					$contracts[$data[0]] = $contract->get_id(); // Map contract ids in Facilit and PE contract id (should be the same)
					
					// Check if this contract has a composite
					if (!$this->is_null($rentalobject_to_contract[$data[0]]) && !$this->is_null($composite_id)) {
						// Add rental composite to contract
						$socontract->add_composite($contract->get_id(), $composite_id);
					}
					
					if (!$this->is_null($data[2])) { //nPersonForetakId
						// Add party to contract
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
				$detail_price_items[$data[1]] = 	//Priselementid
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
			
			foreach ($datalines as $data) {
				if(count($data) <= 24)
				{
					continue;
				}
				// Create new admin price item if one doesn't exist in the admin price list
				// Add new price item to contract with correct reference from the $contracts array
				// Remember fields from detail price item.
				
				// The Agresso-ID is unique for price items
				$id = $this->decode($data[12]);									//cVarenr
				
				$admin_price_item = null;
				
				if(isset($id))
				{
					$admin_price_item = $soprice_item->get_single_with_id($id);
				}
				
				$facilit_id = $this->decode($data[0]);							//nPrisElementId
				
				// Create a new admin price item, store it if it har a new unique agresso-id. First price item with unique 
				// agresso-id determines title, area or "nr of items", and the price (from the price item details)
				if ($admin_price_item == null) {
					$admin_price_item = new rental_price_item();
					$admin_price_item->set_title($this->decode($data[3]));								//cPrisElementNavn
					$admin_price_item->set_agresso_id($id);												//cVareNr
					// This assumes 1 for AREA, and anything else for count, even blanks
					$admin_price_item->set_is_area($this->decode($data[4]) == '1' ? true : false);		//nMengdeTypeId
					$admin_price_item->set_price($detail_price_items[$facilit_id]['price']);
					$admin_price_item->set_responsibility_id(phpgw::get_var("location_id"));
					
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
					$socontract->import_contract_reference($contract_id,$this->decode($data[13]));	//cLonnsArt
				
					// Copy fields from admin price item first
					$price_item->set_title($admin_price_item->get_title());
					$price_item->set_agresso_id($admin_price_item->get_agresso_id());
					$price_item->set_is_area($admin_price_item->is_area());
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
		
		protected function import_composite_price_items($contracts, $rentalobject_to_contract)
		{
			$start_time = time();
			$soprice_item = rental_soprice_item::get_instance();
			$socontract_price_item = rental_socontract_price_item::get_instance();
			$socontract = rental_socontract::get_instance();
			
			// Read priselementdetaljkontrakt list first so we can create our complete price items in the next loop
			// This is an array keyed by the main price item ID
			$detail_price_items = array();
			
			$datalines = $this->getcsvdata($this->path . "/u_PrisElementDetaljLeieobjekt.csv");
			
			foreach ($datalines as $data) {
				$detail_price_items[$data[1]] = 	//nPrisElementId
				array(
					'price' => $data[2],			//nPris
					'amount' => $data[3],			//nMengde
					'date_start' => null			//dGjelderFra
				);
				
				if (!$this->is_null($data[4])) {
					$detail_price_items[$data[1]]['date_start'] = strtotime($this->decode($data[4]));
				}
			}
			
			$datalines = $this->getcsvdata($this->path . "/u_PrisElementLeieobjekt.csv");
			foreach ($datalines as $data) {
				
				// The Agresso-ID is unique for price items
				$id = $this->decode($data[11]);									//cVarenr
				
				$admin_price_item = null;
				
				if(isset($id))
				{
					$admin_price_item = $soprice_item->get_single_with_id($id);
				}
				
				$facilit_id = $this->decode($data[0]);							//nPrisElementId
				
				// Create a new admin price item, store it if it har a new unique agresso-id. First price item with unique 
				// agresso-id determines title, area or "nr of items", and the price (from the price item details)
				if ($admin_price_item == null) {
					$admin_price_item = new rental_price_item();
					$admin_price_item->set_title($this->decode($data[2]));								//cPrisElementNavn
					$admin_price_item->set_agresso_id($id);												//cVareNr
					// This assumes 1 for AREA, and anything else for count, even blanks
					$admin_price_item->set_is_area($this->decode($data[3]) == '1' ? true : false);		//nMengdeTypeId
					$admin_price_item->set_price($detail_price_items[$facilit_id]['price']);
					
					if(isset($id))
					{
						$soprice_item->store($admin_price_item);
						$this->messages[] = "Stored price item {$id} with title " . $admin_price_item->get_title() . " in 'Prisbok'";
					}
				}
				
				
				//TODO: Document this snippet
				$contract_id = null;
				$decoded_data_1 = $this->decode($data[1]);		//nLeieobjektId
				foreach ($rentalobject_to_contract as $facilit_contract_id => $facilit_composite_id) {
					if ($facilit_composite_id == $decoded_data_1) {
						$contract_id = $facilit_contract_id;
					}
				}
				$contract_id = $contracts[$contract_id];
				
				if ($contract_id) {
					
					$contract = $socontract->get_single($contract_id);
					
					// Create a new contract price item that we can tie to our contract
					$price_item = new rental_contract_price_item();
					
					// Copy fields from admin price item first
					$price_item->set_title($admin_price_item->get_title());
					$price_item->set_agresso_id($admin_price_item->get_agresso_id());
					$price_item->set_is_area($admin_price_item->is_area());
					$price_item->set_price($admin_price_item->get_price());
					
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
					
					// Tie the price item to the contract it belongs to
					$price_item->set_contract_id($contract_id);
					// .. and save
					$socontract_price_item->import($price_item);
					$this->messages[] = "Successfully imported price item {$id}" . $price_item->get_title();
				} else {
					$this->warnings[] = "Skipped price item  with no contract attached: " . join(", ", $data);
				}
				
			}
			
			$this->messages[] = "Imported composite price items. (" . (time() - $start_time) . " seconds)";

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
					$location_id = phpgw::get_var("location_id");
					
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
            $path = phpgw::get_var("facilit_path");

            if(is_dir($path.'/logs') || mkdir($path.'/logs')) {
                file_put_contents("$path/logs/$step.log", implode(PHP_EOL, $msgs));
            }
            else { // Path not writeable

            }
        }
	}
?>
