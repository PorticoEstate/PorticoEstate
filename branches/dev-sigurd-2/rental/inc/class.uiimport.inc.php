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
			
			/*
			 * Read parties
			 */
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
				
				$party->set_address_1($this->decode($data[3]));
				$party->set_address_2($this->decode($data[4]));
				$party->set_postal_code($this->decode($data[5]));
				$party->set_mobile_phone($this->decode($data[7]));
				$party->set_phone($this->decode($data[8]));
				
				$party->set_company_name($this->decode($data[9]));
				$party->set_department($this->decode($data[10]));
				
				$party->set_account_number($this->decode($data[12]));
				
				$party->set_reskontro($this->decode($data[xx]));
				
				// Fødselsnr/Foretaksnr/AgressoID
				$pin = $this->decode($data[23]);
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
				
				$party->set_comment($this->decode($data[3]));
				
				
				// Store party
				// rental_soparty::get_instance()->store($party);
			}
			fclose($handle);
			
			/*
			 * Read composites/areas
			 */
			$handle = fopen($path . "/u_Leieobjekt.csv", "r");
			while (($data = fgetcsv($handle, 1000, ",", "'")) !== false) {
				$composite = new rental_composite();
				
				// TODO: What is location id here?
				$location_id = (int)phpgw::get_var('location_id');
				// TODO: Have to parse loc1 to xxxx-xx-xx-xx...?
				$loc1 = $this->decode($data[1]);
				$composite->add_new_unit(new rental_property($loc1, $location_id));
				
				$composite->set_description($this->decode($data[3]));
				$composite->set_name($this->decode($data[6]));
				$composite->set_custom_address_1($this->decode($data[7]));
				// TODO: Check export, this field doesn't seem to get exported?
				//$composite->set_custom_address_2($this->decode($data[8]));
				$composite->set_custom_postcode($this->decode($data[7]));
				
				$composite->set_is_active($data[20] == "1");
				
				// TODO: Store composite
			}
			fclose($handle);
			
			/*
			 * Read contracts
			 */
			$handle = fopen($path . "/u_Kontrakt.csv", "r");
			while (($data = fgetcsv($handle, 1000, ",", "'")) !== false) {
				$contract = new rental_contract();				
			}
			fclose($handle);
			
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
