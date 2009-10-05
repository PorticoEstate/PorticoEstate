<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.soparty');
	
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');

	class rental_uiimport extends rental_uicommon
	{
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
			$path = phpgw::get_var("facilit_path");
			if ($path) {
				$result = $this->import($path);
			}
			$this->render('facilit_import.php', array('importresult', $result));
			$this->render('permission_denied.php',array('error' => lang('permission_denied_view_contract')));
		}
		
		public function import($path)
		{
			setlocale(LC_ALL, 'no_NO');
			
			$row = 1;
			
			// TODO: Don't read first line
			
			// How to connect objects?  Might be possible to store 
			// the created rental_ objects in an associative array keyed by their old ID
			
			/*
			 * Read parties
			 */
			$parties = array();
			
			$handle = fopen($path . "/u_PersonForetak.csv", "r");
			
			while (($data = fgetcsv($handle, 1000, ",", "'")) !== false) {
				
				$party = new rental_party();
				
				// TODO: Where is the PersonForetak_id in the export?
				
				// Fill in first/last name if applicable, otherwise use Foretaksnavn
				if ($this->is_null($data[0])) {
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
				
				// Fødselsnr/Foretaksnr/AgressoID
				$pin = $this->decode($data[24]);
				if (strlen($pin) == 11) {
					// Fødselsnummer
					$party->set_personal_identification_number($pin);
				} else if (strlen($pin) == 9) {
					// Foretaksnummer.  Always 9?
					// Do we need a new field here?
					$party->set_personal_identification_number($pin);
				} else if (strlen($pin) == 6) {
					// Agresso ID
					$party->set_agresso_id($pin);
				}
				
				$party->set_comment($this->decode($data[26]));
				
				
				// Store party
				rental_soparty::get_instance()->store($party);
				
				// Add party to collection of parties keyed by its facilit ID so we can refer to it later.
				$facilit_id = $data[17];
				$parties[$facilit_id] = $party;
			}
			fclose($handle);
			
			/*
			 * Read composites/areas
			 */
			$composites = array();
			$handle = fopen($path . "/u_Leieobjekt.csv", "r");
			while (($data = fgetcsv($handle, 1000, ",", "'")) !== false) {
				$composite = new rental_composite();
				
				// Convert location code to the correct format, xxxx-xx-xx-xx...
				$loc1 = $this->decode($data[1]);
				$loc1 = $this->format_location_code($loc1);
				
				$composite->add_new_unit(new rental_unit(null, -1, $loc1));
				
				$composite->set_description($this->decode($data[3]));
				$composite->set_name($this->decode($data[6]));
				$composite->set_custom_address_1($this->decode($data[7]));
				$composite->set_custom_postcode($this->decode($data[8]));
				
				$composite->set_is_active($data[19] == "1");
				
				// Store composite
				rental_socomposite::get_instance()->store($composite);
				
				// Add composite to collection of composite so we can refer to it later.
				$composites[$data[0]] = $composite;
			}
			fclose($handle);
			
			/*
			 * Read rental object-to-contract link table
			 */
			$rentalobject_to_contract = array();
			$handle = fopen($path . "/u_Leieobjekt_Kontrakt.csv", "r");
			while (($data = fgetcsv($handle, 1000, ",", "'")) !== false) {
				// Array keyed by contract id.  Assuming 1-1 even for link table.
				$rentalobject_to_contract[$data[1]] = $data[0];
			}
			
			/*
			 * Read contracts
			 */
			$contracts = array();
			
			$handle = fopen($path . "/u_Kontrakt.csv", "r");
			
			while (($data = fgetcsv($handle, 1000, ",", "'")) !== false) {
				$contract = new rental_contract();
				
				// Add rental composite to contract
				$composite_id = $rentalobject_to_contract[$data[0]];
				$contract->add_composite($composites[$composite_id]);
				
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
				
				// Store contract
				rental_socontract::get_instance()->store($contract);
				
				$contracts[$data[0]] = $contract;
			}
			fclose($handle);
			
		}
		
		protected function format_location_code($value)
		{
			$length = strlen($value);
			$i = 0;
			
			$result = array();
			
			while ($i < $length) {
				if ($i == 0) {
					$result[] = substr($value, $i, 4);
					$i += 4;
				} else {
					$result[] = substr($value, $i, 2);
					$i += 2;
				}
			}
			
			return join("-", $result);
		}
		
		protected function decode($value)
		{
			$converted = mb_convert_encoding($value, 'UTF-8');
			if ($converted == "<NULL>") {
				return null;
			}
			
			return $converted;
		}
		
		protected function is_null($value)
		{
			return (trim($data[0]) != "" && $data != "<NULL>");
		}
	}
?>
