<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.soparty');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sounit');
	phpgw::import_class('rental.soprice_item');
	phpgw::import_class('rental.socontract_price_item');
	
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');

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
		}
		
		public function query()
		{
			// Do nothing
		}

		public function index()
		{
			// Set the submit button label to its initial state
			$this->import_button_label = "Start import";
			
			$path = phpgw::get_var("facilit_path");
			if ($path) {
				$this->path = $path;
				$messages = array();
				$warnings = array();
				$errors = array();
				setlocale(LC_ALL, 'no_NO');
				$result = $this->import($path);
			}
			
			$this->render('facilit_import.php', array(
				'messages' => $this->messages,
				'warnings' => $this->warnings,
				'errors' => $this->errors, 
				'button_label' => $this->import_button_label));
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
			// TODO: Don't read first line
			
			// TODO: Fill error messages in sub functions
			
			// Import rental parties
			if (!phpgwapi_cache::session_get('rental', 'facilit_parties')) {
				phpgwapi_cache::session_set('rental', 'facilit_parties', $this->import_parties());
				$this->import_button_label = "Continue to import composites";
				return;
			}
			
			// Import composites and units
			if (!phpgwapi_cache::session_get('rental', 'facilit_composites')) {
				phpgwapi_cache::session_set('rental', 'facilit_composites', $this->import_composites());
				$this->import_button_label = "Continue to import composite-to-contract link table";
				return;
			}
			
			// Import composite to contract link table.  Assumes 1-1 link.
			if (!phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract')) {
				phpgwapi_cache::session_set('rental', 'facilit_rentalobject_to_contract', $this->import_rentalobject_to_contract());
				$this->import_button_label = "Continue to import contracts";
				return;
			}
			
			// Import contracts
			if (!phpgwapi_cache::session_get('rental', 'facilit_contracts')) {
				$composites = phpgwapi_cache::session_get('rental', 'facilit_composites');
				$rentalobject_to_contract = phpgwapi_cache::session_get('rental', 'facilit_rentalobject_to_contract');
				$parties = phpgwapi_cache::session_get('rental', 'facilit_parties');
				phpgwapi_cache::session_set('rental', 'facilit_contracts', $this->import_contracts($composites, $rentalobject_to_contract, $parties));
				$this->import_button_label = "Continue to import price items";
				return;
			}
			
			// Import price items
			if (!phpgwapi_cache::session_get('rental', 'facilit_price_items')) {
				$contracts = phpgwapi_cache::session_get('rental', 'facilit_contracts');
				phpgwapi_cache::session_set('rental', 'facilit_price_items', $this->import_price_items($contracts));
				$this->import_button_label = "Import done"; // Not really - events will be after this
				//return;
			}
			
			// We're done with the import, so clear all session variables so we're ready for a new one
			phpgwapi_cache::session_clear('rental', 'facilit_parties');
			phpgwapi_cache::session_clear('rental', 'facilit_composites');
			phpgwapi_cache::session_clear('rental', 'facilit_rentalobject_to_contract');
			phpgwapi_cache::session_clear('rental', 'facilit_contracts');
			phpgwapi_cache::session_clear('rental', 'facilit_price_items');
		}
		
		protected function import_parties()
		{
			$parties = array();
			
			// Open the Facilit file containing rental parties
			$handle = fopen($this->path . "/u_PersonForetak.csv", "r");
			
			// Read the first line to get the headers out of the way
			$this->getcsv($handle);
			
			$counter = 1;
			// Loop through each line of the file, parsing CSV data to a php array
			while (($data = $this->getcsv($handle)) !== false) {
				// Create a new rental party we can fill with info from this line from the file
				$party = new rental_party();

				// Fill in first/last name if applicable, otherwise use Foretaksnavn
				if (!$this->is_null($data[0])) {
					$party->set_first_name($this->decode($data[0]));
					$party->set_last_name($this->decode($data[1]));
				} else {
					$party->set_first_name($this->decode($data[2]));
				}
				
				// Contact information
				$party->set_address_1($this->decode($data[3]));
				$party->set_address_2($this->decode($data[4]));
				$party->set_postal_code($this->decode($data[5]));
				$party->set_mobile_phone($this->decode($data[7]));
				$party->set_phone($this->decode($data[8]));
				
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
				
				// Store party and log message
				if (rental_soparty::get_instance()->store($party)) {
					// Add party to collection of parties keyed by its facilit ID so we can refer to it later.
					$facilit_id = $data[17];
					$parties[$facilit_id] = $party->get_id();
					
					$this->messages[] = "Successfully added party " . $party->get_last_name() . ", " . $party->get_first_name() . " (" . $party->get_id() . ")";
				} else {
					$this->errors[] = "Failed to store party " . $party->get_last_name() . ", " . $party->get_first_name();
				}
			}
			
			fclose($handle);
			
			return $parties;
		}
		
		protected function import_composites()
		{
			$composites = array();
			
			$handle = fopen($this->path . "/u_Leieobjekt.csv", "r");
			
			// Read the first line to get the headers out of the way
			$this->getcsv($handle);
			
			while (($data = $this->getcsv($handle)) !== false) {
				$composite = new rental_composite();
				
				$composite->set_description($this->decode($data[3]));
				$composite->set_name($this->decode($data[6]));
				$composite->set_custom_address_1($this->decode($data[7]));
				$composite->set_custom_postcode($this->decode($data[8]));
				
				$composite->set_is_active($data[19] == "1");
				
				// Store composite
				if (rental_socomposite::get_instance()->store($composite)) {
					// Convert location code to the correct format, xxxx-xx-xx-xx...
					$loc1 = $this->decode($data[1]);
					$loc1 = $this->format_location_code($loc1);
					
					
					// Add units only if composite stored ok.
					rental_sounit::get_instance()->store(new rental_unit(null, $composite->get_id(), new rental_property_location($loc1, null)));
					
					// Add composite to collection of composite so we can refer to it later.
					$composites[$data[0]] = $composite->get_id();
					
					$this->messages[] = "Successfully added composite " . $composite->get_name() . " (" . $composite->get_id() . ")";
				} else {
					$this->errors[] = "Failed to store composite " . $composite->get_name();
				}
			}
			
			fclose($handle);
			
			return $composites;
		}
		
		protected function import_rentalobject_to_contract()
		{
			$rentalobject_to_contract = array();
			
			$handle = fopen($this->path . "/u_Leieobjekt_Kontrakt.csv", "r");
			
			// Read the first line to get the headers out of the way
			$this->getcsv($handle);
			
			$first_line = fgetcsv($handle, 0, ",", "'");
			
			while (($data = $this->getcsv($handle)) !== false) {
				// Array with Facilit Contract ID => Facilit composite ID
				$rentalobject_to_contract[$data[1]] = $data[0];
			}
			
			fclose($handle);
			
			$this->messages[] = "Successfully imported " . count($rentalobject_to_contract) . " contract links";
			
			return $rentalobject_to_contract;
		}
		
		protected function import_contracts($composites, $rentalobject_to_contract, $parties)
		{
			$contracts = array();
			
			$handle = fopen($this->path . "/u_Kontrakt.csv", "r");
			
			// Read the first line to get the headers out of the way
			$this->getcsv($handle);
			
			while (($data = $this->getcsv($handle)) !== false) {
				$contract = new rental_contract();
				
				// TODO: link this with previously imported rental party. 
				$personId = $this->decode($data[2]);
				
				$date_start = $this->decode($data[3]);
				$date_end = $this->decode($data[4]);
				
				$contract->set_contract_date(new rental_contract_date(strtotime($date_start), strtotime($date_end)));
				
				$contract->set_old_contract_id($this->decode($data[5]));
				
				$term = $data[10];
				switch ($term) {
					case 1: // Monthly
						$contract->set_term_id(1);
						break;
					case 2: // Quarterly
						$contract->set_term_id(4);
						break;
					case 3: // Yearly
						$contract->set_term_id(2);
						break;
				}
				
				// What period the prices are calculated from.  4=month, 8=year
				$price_period = $data[14];
				if ($price_period == 4) {
					// The price period is month.  We ignore this but print a warning.
					// TODO: What to use as reference here?  Currently using K-number
					$this->warnings[] = "Price period of contract " . $this->decode($data[5]) . " is month.  Ignored.";
				}
				
				$contract->set_billing_start_date(strtotime($this->decode($data[16])));
				
				// Deres ref.
				$contract->set_invoice_header($this->decode($data[17]));
				
				$contract->set_comment($this->decode($data[18]));
				
				// Ansvar/Tjenestested: F.eks: 080400.13000
				$ansvar_tjeneste = $this->decode($data[26]);
				$ansvar_tjeneste_components = split(".", $ansvar_tjeneste);
				$contract->set_responsibility_id($ansvar_tjeneste_components[0]);
				$contract->set_service_id($ansvar_tjeneste_components[1]);
				// TODO: Check other types of contracts.  The above is correct for internal
				
				// Set the location ID according to what the user selected
				$contract->set_location_id(phpgw::get_var("location_id"));
				
				// Add rental composite to contract
					//$composite_id = $rentalobject_to_contract[$data[0]];
					//$composite = new rental_composite($composites[$composite_id]);
					//$contract->add_composite(new rental_composite($rentalobject_to_contract[$data[0]]));
					
				// Store contract
				if (rental_socontract::get_instance()->store($contract)) {
					$contracts[$data[0]] = $contract->get_id();
					
					if (isset($rentalobject_to_contract[$data[0]])) {
						// Add rental composite to contract
						$composite_id = $composites[$rentalobject_to_contract[$data[0]]];
						rental_socontract::get_instance()->add_composite($contract->get_id(), $composite_id);
					}
					
					if (!$this->is_null($data[2])) {
						// Add party to contract
						$party_id = $parties[$this->decode($data[2])];
						rental_socontract::get_instance()->add_party($contract->get_id(), $party_id);
					}
					
					$this->messages[] = "Successfully added contract for property " . $contract->get_composite_name() . " (" . $contract->get_id() . ")";
				} else {
					$this->errors[] = "Failed to store contract " . $this->decode($data[5]);
				}
			}
			
			fclose($handle);
			
			return $contracts;
		}
		
		protected function import_price_items($contracts)
		{
			// Read priselementdetaljkontrakt list first so we can create our complete price items in the next loop
			// This is an array keyed by the main price item ID
			$detail_price_items = array();
			
			$handle = fopen($this->path . "/u_PrisElementDetaljKontrakt.csv", "r");
			
			// Read the first line to get the headers out of the way
			$this->getcsv($handle);
			
			while (($data = $this->getcsv($handle)) !== false) {
				$detail_price_items[$data[1]] = array(
					'price' => $data[2],
					'amount' => $data[3],
					'date_start' => null,
					'date_end' => null
				);
				
				if (!$this->is_null($data[4])) {
					$detail_price_items[$data[1]]['date_start'] = strtotime($this->decode($data[4]));
				}
				if (!$this->is_null($data[5])) {
					$detail_price_items[$data[1]]['date_end'] = strtotime($this->decode($data[5]));
				}
			}
			
			fclose($handle);
		
			$handle = fopen($this->path . "/u_PrisElementKontrakt.csv", "r");
			
			// Read the first line to get the headers out of the way
			$this->getcsv($handle);
			
			while (($data = $this->getcsv($handle)) !== false) {
				// Create new admin price item if one doesn't exist in the admin price list
				// Add new price item to contract with correct reference from the $contracts array
				// Remember fields from detail price item.
				
				$title = $this->decode($data[3]);
				
				$admin_price_item = rental_soprice_item::get_instance()->get_single_with_title($title);
				
				// Add new admin price item if one with this title doesn't already exist
				if (!$admin_price_item) {
					$facilit_id = $this->decode($data[0]);
					
					$admin_price_item = new rental_price_item();
					$admin_price_item->set_title($title);
					$admin_price_item->set_agresso_id($this->decode($data[12]));
					// TODO: This assumes 1 for AREA, and anything else for count.  is this correct?
					$admin_price_item->set_is_area($this->decode($data[12]) == '4');
					$admin_price_item->set_price($detail_price_items[$facilit_id]['price']);
					rental_soprice_item::get_instance()->store($admin_price_item);
				}
				
				$contract_id = $contracts[$this->decode($data[1])];
				
				if ($contract_id) {
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
						$price_item->set_area($detail_price_items[$facilit_id]['amount']);
					} else {
						$price_item->set_count($detail_price_items[$facilit_id]['amount']);
					}
					
					$price_item->set_date_start($detail_price_items[$facilit_id]['date_start']);
					$price_item->set_date_end($detail_price_items[$facilit_id]['date_end']);
					
					// Tie the price item to the contract it belongs to
					$price_item->set_contract_id($contract_id);
					// .. and save
					rental_socontract_price_item::get_instance()->store($price_item);
				} else {
					$this->messages[] = "Skipped price item with no contract attached: " . join(", ", $data);
				}
				
				
			}
			
			fclose($handle);
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
	}
?>
