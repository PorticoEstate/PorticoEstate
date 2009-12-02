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
			$steps = 7;
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
				phpgwapi_cache::session_set('rental', 'facilit_contracts', $this->import_contracts($composites, $rentalobject_to_contract, $parties));
				$this->import_button_label = "5/{$steps}: Continue to import contract price items";
                $this->log_messages(4);
				return;
			}
			
			// Import price items
			if (!phpgwapi_cache::session_get('rental', 'facilit_contract_price_items')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				phpgwapi_cache::session_set('rental', 'facilit_contract_price_items', $this->import_contract_price_items($contracts));
				$this->import_button_label = "6/{$steps}: Continue to import composite price items"; // Not really - events will be after this
                $this->log_messages(5);
				return;
			}
			
			// Import price items
			if (!phpgwapi_cache::session_get('rental', 'facilit_composite_price_items')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				$rentalobject_to_contract = phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract');
				phpgwapi_cache::session_set('rental', 'facilit_composite_price_items', $this->import_composite_price_items($contracts, $rentalobject_to_contract));
				$this->import_button_label = "7/{$steps}: Continue to import events";
                $this->log_messages(6);
				return;
			}
			
			// Import events
			if (!phpgwapi_cache::session_get('rental', 'facilit_events')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				phpgwapi_cache::session_set('rental', 'facilit_events', $this->import_events($contracts));
                $this->log_messages(7);
                $this->clean_up();
				$this->import_button_label = "Import done";
				//return;
			}
			
			// We're done with the import, so clear all session variables so we're ready for a new one
			phpgwapi_cache::session_clear('rental', 'facilit_parties');
			phpgwapi_cache::session_clear('rental', 'facilit_composites');
			phpgwapi_cache::session_clear('rental', 'facilit_rentalobject_to_contract');
			phpgwapi_cache::session_clear('rental', 'facilit_contracts');
			phpgwapi_cache::session_clear('rental', 'facilit_contract_price_items');
			phpgwapi_cache::session_clear('rental', 'facilit_composite_price_items');
			phpgwapi_cache::session_clear('rental', 'facilit_facilit_events');
		}
		
		protected function import_parties()
		{
			$start_time = time();
			$soparty = rental_soparty::get_instance();
			$parties = array();			
			$datalines = $this->getcsvdata($this->path . "/u_PersonForetak.csv", true);
			$this->messages[] = "Read CSV file in " . (time() - $start_time) . " seconds";
			$counter = 1;
			
			// Loop through each line of the file, parsing CSV data to a php array
			foreach ($datalines as $data) {
				// Create a new rental party we can fill with info from this line from the file
				$party = new rental_party();

				// Contact information
				$party->set_address_1($this->decode($data[3]));
				$party->set_address_2($this->decode($data[4]));
				$party->set_postal_code($this->decode($data[5]));
				$party->set_mobile_phone($this->decode($data[7]));
				$party->set_phone($this->decode($data[8]));

                $party->set_fax($this->decode($data[9]));
                $party->set_title($this->decode($data[12]));
                $party->set_email($this->decode($data[25]));
				
				// Company information
				$party->set_company_name($this->decode($data[10]));
				$party->set_department($this->decode($data[11]));
				
				// Account number.  Can be a variety of things.  TODO: check this out.  4 digits at least on internal.
				$party->set_account_number($this->decode($data[14]));
				
				$party->set_reskontro($this->decode($data[23]));
				
				// TODO: PIN/AgressoID/CompanyID should go to the same field
				
				// Fødselsnr/Foretaksnr/AgressoID
				$party->set_identifier($this->decode($data[24]));
				
				$party->set_comment($this->decode($data[26]));
                if(strlen($this->decode($data[6]) > 1)) {
                    $party->set_comment($party->get_comment()."\n\nKontaktperson: ".$this->decode($data[6]));
                }
                
                // TODO: Do regex to check for only digits too, not just length
                switch(strlen(''.$this->decode($data[24]))) {
                    case 4: // Intern organisasjonstilknytning
                        $party->set_company_name($this->decode($data[2]));
                        $party->set_first_name(null);
                        $party->set_last_name(null);
                        
                        // Get location ID
                        $locations = $GLOBALS['phpgw']->locations;
                        $subs = $locations->get_subs_from_pattern('rental', '.ORG.BK.__.'.$this->decode($data[24]));
                        $party->set_location_id($subs[0]['location_id']);
                        break;
                    case 6: // Foretak (agresso-id)
                    case 9: // Foretak (org.nr)
                        $party->set_company_name($this->decode($data[2]));
                        $party->set_identifier($this->decode($data[24]));
                        $party->set_first_name(null);
                        $party->set_last_name(null);
                        break;
                    case 11: // Personnr
                        if (!$this->is_null($data[0])) {
                            $party->set_first_name($this->decode($data[0]));
                            $party->set_last_name($this->decode($data[1]));
                        } else {
                            $company_name = explode(' ', $this->decode($data[2]), 2);
                            $party->set_first_name($company_name[0]);
                            $party->set_last_name($company_name[1]);
                        }
                        break;
                    default:
                        $party->set_first_name($this->decode($data[0]));
                        $party->set_last_name($this->decode($data[1]));
                        $party->set_company_name($this->decode($data[2]));
                        $party->set_is_inactive(true);
                        $this->warnings[] = "Party with unknown 'cPersonForetaknr' format: ".$this->decode($data[24]).". Setting as inactive.";
                }

				
				// Store party and log message
				if ($soparty->store($party)) {
					// Add party to collection of parties keyed by its facilit ID so we can refer to it later.
					$facilit_id = $data[17];
					$parties[$facilit_id] = $party->get_id();
					$this->messages[] = "Successfully added party " . $party->get_name() . " (" . $party->get_id() . ")";
				} else {
					$this->errors[] = "Failed to store party " . $party->get_name();
				}
			}
			
			$this->messages[] = "Successfully imported " . count($parties) . " contract parties. (" . (time() - $start_time) . " seconds)";

			return $parties;
		}
		
		protected function import_composites()
		{
			$start_time = time();
			$socomposite = rental_socomposite::get_instance();
			$sounit = rental_sounit::get_instance();
			$composites = array();
			$datalines = $this->getcsvdata($this->path . "/u_Leieobjekt.csv");
			$this->messages[] = "Read CSV file in " . (time() - $start_time) . " seconds";
			
			foreach ($datalines as $data) {
				$composite = new rental_composite();
				
				// Use the first address line as name if no name
				$name = $this->decode($data[26]);
				$address1 = $this->decode($data[6]);
				if(!isset($name)){
					$name = $address1;
				}
				
				$composite->set_name($name);
				$composite->set_custom_address_1($address1);
				$composite->set_custom_address_2($this->decode($data[7]));
				$composite->set_custom_postcode($this->decode($data[8]));
				$composite->set_description($this->decode($data[3]));
                $composite->set_object_type_id($this->decode($data[25]));

                $composite->set_area($this->decode($data[2]));
				
				$composite->set_is_active($data[19] == "-1");
				
				// Store composite
				if ($socomposite->store($composite)) {
					// Convert location code to the correct format, xxxx-xx-xx-xx...
					
					/* TODO: waiting on feedback on property module content
					Code for getting correct location code
					
					$composite_number = $this->decode($data[1]);
					$property_identifier = $this->decode($data[4]);
					$building_identifier = $this->decode($data[5]);
					$farm_number = $this->decode($data[27]);
					$use_number = $this->decode($data[28]);
					
					$location_code = $this->get_location_code
					(
						$composite_number,
						$property_identifier,
						$building_identifier,
						$farm_number,
						$use_number
					);*/
					
					
					if (phpgw::get_var("location_id") == '1176') {
						// Get internal composite location code from different field
						$loc1 = $this->decode($data[5]);
					} else {
						$loc1 = $this->decode($data[1]);
					}
					
					$loc1 = $this->format_location_code($loc1);
					
					// Add units only if composite stored ok.
					$sounit->store(new rental_unit(null, $composite->get_id(), new rental_property_location($loc1, null)));
					
					// Add composite to collection of composite so we can refer to it later.
					$composites[$data[0]] = $composite->get_id();
					
					$this->messages[] = "Successfully added composite " . $composite->get_name() . " (" . $composite->get_id() . ")";
				} else {
					$this->errors[] = "Failed to store composite " . $composite->get_name();
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
		
		protected function import_contracts($composites, $rentalobject_to_contract, $parties)
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
                $contract->set_contract_type_id($contract_types[$this->decode($data[1])]);	
				
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
                } else if(in_array($contract->get_contract_type_id(), $internal_types)) {
                	//specific logic for internal responsibility area
                	
                	// Set default values accounts and project id
                	$contract->set_account_in(119001);
					$contract->set_account_out(119001);
					$contract->set_project_id(9);
					
					// Get the rented area from the contract
					$contract->set_rented_area($this->decode($data[22]));
                } else {
                	// Get the rented area from the contract
                	$contract->set_rented_area($this->decode($data[22]));
                }
				
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
					
					$this->messages[] = "Successfully added contract for property " . $contract->get_composite_name() . " (" . $contract->get_id() . "/". $contract->get_old_contract_id() .")";
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
                        $this->messages[] = "Successfully imported price item {$id} for contract {$contract_id}";
                    }
                    else {
                        $this->warning[] = "Could not store price item {$id} - " . $price_item->get_title();
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
							$this->messages[] = "Successfully imported event '" . $notification->get_message() . "'";
						} else {
							$this->errors[] = "Error importing event " . $notification->get_message();
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
		
		protected function get_location_code($composite_number,$property_identifier,$building_identifier,$farm_number,$use_number)
		{
			$location_code = '';
			
			// 1. Check that the property identifier consists of 4 numbers
			// ... is the length 4 characters (?)
			$correct_length = strlen($property_identifier) == 4 ? true : false;
			// ... is it a number (?)
			$integer_value_property = ((int) $property_identifier) > 0 ? true : false;
			
			if($correct_length && $integer_value_property)
			{
				$location_code = $property_identifier;
				
				// 1.1 Check the building identifier for consistency (6 numbers and match property identifier)
				$correct_length = strlen($building_identifier) == 6 ? true : false;
				$integer_value_building = ((int) $building_identifier) > 0 ? true : false;
				
				if($correct_length && $integer_value_building)
				{
					if(substr($building_identifier,0,3) == $property_identifier)
					{
						$building = substr($building_identifier,4,5);
						
					}
					
				}
				
			}
			
			
			
			
			
			// 1.2 Check the composite number for a building identifier (6 numbers after punctuation)
			
			
			// 2 If no location code, check the farm- and use number (fm_gab_location.gab_id)
			
			
			// 3 Check for an existing property given the location code
			
			
		}
		
		/**
		 * Format a given location code according to the rule xxxx-xx-xx-xx...
		 * 
		 * @param string $value A location code as a continuous string without - delimiters
		 * @return string The formatted location code
		 */
		protected function format_location_code($value)
		{
			$length = strlen($value);
			$i = 0;
			
			$result = array();
			
			while ($i < $length) {
				if ($i == 0) {
					// The four first characters should be in one group
					$result[] = substr($value, $i, 4);
					$i += 4;
				} else {
					// .. after that it's all 2 characters per group
					$result[] = substr($value, $i, 2);
					$i += 2;
				}
			}
			
			return join("-", $result);
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
