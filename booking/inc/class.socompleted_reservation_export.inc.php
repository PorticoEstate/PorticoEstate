<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.sopermission');
	
	class booking_socompleted_reservation_export extends booking_socommon
	{
		protected 
			$completed_reservation_so,
			$completed_reservation_bo,
			$account_code_set_so,
			$customer_id,
			$sequential_number_generator_so;
		
		function __construct()
		{
			$this->event_so = CreateObject('booking.soevent');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->booking_bo = CreateObject('booking.bobooking');
			$this->event_bo = CreateObject('booking.boevent');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->customer_id = CreateObject('booking.customer_identifier');
			$this->completed_reservation_so = CreateObject('booking.socompleted_reservation');
			$this->completed_reservation_bo = CreateObject('booking.bocompleted_reservation');
			$this->account_code_set_so = CreateObject('booking.soaccount_code_set');
			$this->sequential_number_generator_so = CreateObject('booking.sobilling_sequential_number_generator');
			
			parent::__construct('bb_completed_reservation_export', 
				array(
					'id' 						=> array('type' => 'int'),
					'season_id' 			=> array('type' => 'int'),
					'building_id'    		=> array('type' => 'int'),
					'from_'					=> array('type' => 'timestamp', 'required' => true),
					'to_'						=> array('type' => 'timestamp', 'required' => true),
					'total_cost'			=> array('type' => 'decimal'), //NOT NULL in database, but automatically computed in add method
					'total_items'			=> array('type' => 'int'), ////NOT NULL in database, but automatically computed in add method
					key(booking_socommon::$AUTO_CREATED_ON) => current(booking_socommon::$AUTO_CREATED_ON),
					key(booking_socommon::$AUTO_CREATED_BY) => current(booking_socommon::$AUTO_CREATED_BY),
					'created_by_name' 	=> booking_socommon::$REL_CREATED_BY_NAME,
					'season_name'	=> array('type' => 'string', 'query' => true, 'join' => array(
							'table' 		=> 'bb_season',
							'fkey' 		=> 'season_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'building_name'	=> array('type' => 'string', 'query' => true, 'join' => array(
							'table' 		=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'export_configurations' => array('manytomany' => array(
							'table' => 'bb_completed_reservation_export_configuration',
							'key' => 'export_id',
							'column' => array(
								'id' => array('type' => 'int'),
								'type' => array('type' => 'string', 'required' => true),
								'account_code_set_id' => array('type' => 'int', 'required' => true),
								'export_file_id' => array('type' => 'int'),
							)
					)),
				)
			);
		}
		
		protected function _get_search_to_date(&$entity) {
			$to_date = (isset($entity['to_']) && !empty($entity['to_']) ? $entity['to_'] : date('Y-m-d'));
			
			$to_date = date('Y-m-d', strtotime($to_date));
			
			if (strtotime($to_date) > strtotime('tomorrow')) {
				$to_date = date('Y-m-d');
			}
			
			$to_date .= ' 23:59:59';
			
			return $to_date;
		}
		
		protected function doValidate($entity, booking_errorstack $errors)
		{	
			$exportable_reservations =& $this->get_completed_reservations_for($entity);
			if (!$exportable_reservations) {
				$errors['nothing_to_export'] = lang('Nothing to export');
				return;
			}
			
			foreach($exportable_reservations as &$reservation) {
				if (!$this->get_customer_identifier_value_for($reservation) 
				 	&& $this->get_cost_value($reservation['cost']) > 0 /* Exclude free reservations from this check */ ) { 
					$errors['invalid_customer_ids'] = lang('Unable to export: Missing a valid Customer ID on some rows');
				}
			}
		}
		
		function read_single($id)
		{
			$entity = parent::read_single($id);
			$this->initialize_entity($entity);
			return $entity;
		}
	 	
		/**
		 * Normalizes data on entity.
		 */
		public function initialize_entity(&$entity) {
        	if (isset($entity['__initialized__']) && $entity['__initialized__'] === true) { return $entity; }
			
			$entity['__initialized__'] = true;
			//re-index export configurations on their types
			if (!(array_key_exists('export_configurations', $entity) && is_array($entity['export_configurations']))) {
				return $entity;
			}
			
			$export_configs = array();
			foreach($entity['export_configurations'] as $conf) {
				$export_configs[$conf['type']] = $conf;
			}
			$entity['export_configurations'] = $export_configs;
			
			return $entity;
		}
		
		public static function get_available_export_types() {
			return array('internal', 'external');
		}
		
		public function has_generated_file(&$export, $type) {
			$this->initialize_entity($export);
			
			if (!isset($export['export_configurations']) || !is_array($export['export_configurations'])) {
				throw new InvalidArgumentException("Missing or invalid export_configurations");
			}
			
			if (!isset($export['export_configurations'][$type]) 
					|| !is_array($export['export_configurations'][$type])) {
				throw new InvalidArgumentException("Missing export configuration for type '{$type}'");
			}
			
			if (!array_key_exists('export_file_id', $export['export_configurations'][$type])) {
				throw new InvalidArgumentException("Missing export configuration file information");
			}
			
			if (empty($export['export_configurations'][$type]['export_file_id'])) {
				return false;
			}
			
			return true;
		}
		
		public function get_export_file_data($entity, $type) {
			$this->initialize_entity($entity);
			
			if (!isset($entity['export_configurations']) || !isset($entity['export_configurations'][$type]))
			{
				throw new InvalidArgumentException(sprintf("Missing export configuration of type '%s'", $type));
			}
			
			$export_conf = $entity['export_configurations'][$type];
			$account_codes = $this->account_code_set_so->read_single($export_conf['account_code_set_id']);
			
			if (!is_array($account_codes)) { 
				throw new LogicException(sprintf("Unable to locate accounts codes for export file data"));
			}
			
			$export_reservations =& $this->get_completed_reservations_for($entity['id']);
		
			$export_method = "export_{$type}";
			
			if (!method_exists($this, $export_method)) {
				throw new LogicException(sprintf('Cannot generate export for type "%s"', $type));
			}
			
			return array($export_conf, $this->$export_method($export_reservations, $account_codes));
		}
		
		function add($entry) {
			$export_reservations =& $this->get_completed_reservations_for($entry);
			
			if (!$export_reservations) {
				throw new LogicException('Nothing to export');
			}
			
			$entry['from_'] = $export_reservations[0]['to_'];
			$entry['to_'] = $export_reservations[count($export_reservations)-1]['to_'];
			$entry['total_cost'] = $this->calculate_total_cost($export_reservations);
			$entry['total_items'] = count(array_filter($export_reservations, array($this, 'not_free')));
			
			$this->db->transaction_begin();
			
			$receipt = parent::add($entry);
			$entry['id'] = $receipt['id'];
			$this->update_completed_reservations_exported_state($entry, $export_reservations);
			
			if (!($this->db->transaction_commit())) {
				throw new UnexpectedValueException('Transaction failed.');
			}
			
			return $receipt;
		}
		
		public function &get_completed_reservations_for($entity) {
			$filters = array();
			
			if (is_array($entity)) {			
				$filters['where'] = array("%%table%%".sprintf(".to_ <= '%s'", $this->_get_search_to_date($entity)));
				$filters['exported'] = null;
			
				if ($entity['season_id']) {
					$filters['season_id'] = $entity['season_id'];
				}
			
				if ($entity['building_id']) {
					$filters['building_id'] = $entity['building_id'];
				}
			} else if ($entity) {
				$filters['exported'] = $entity;
			}
			else
			{
				throw new InvalidArgumentException('Invalid entity parameter');
			}

			if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && // admin users should have access to all buildings
			     !$this->completed_reservation_bo->has_role(booking_sopermission::ROLE_MANAGER) ) { // users with the booking role admin should have access to all buildings

				if ( !isset($filters['building_id']) )
				{
					$filters['building_id'] = $this->completed_reservation_bo->accessable_buildings($GLOBALS['phpgw_info']['user']['id']);
				}
			}
			
			$reservations = $this->completed_reservation_so->read(array('filters' => $filters, 'results' => 'all', 'sort' => 'customer_type,customer_identifier_type,customer_organization_number,customer_ssn,to_', 'dir' => 'asc'));
			
			if (count($reservations['results']) > 0) {
				return $reservations['results'];
			}
			
			return null;
		}
		
		protected function update_completed_reservations_exported_state($entity, &$reservations) {
			return $this->completed_reservation_so->update_exported_state_of($reservations, $entity['id']);
		}
		
		protected function get_customer_identifier_value_for(&$reservation) {
			return $this->customer_id->get_current_identifier_value($reservation);
		}
		
		public function not_free($reservation) {
			return $this->get_cost_value($reservation['cost']) > 0;
		}
		
		public function calculate_total_cost(&$reservations) {
			return array_reduce($reservations, array($this,"_rcost"), 0);
		}
		
		public function _rcost($total_cost, $entity) {
			return $total_cost+$this->get_cost_value($entity['cost']);
		}
		
		public function select_external($reservation) {
            $config	= CreateObject('phpgwapi.config','booking');
			$config->read();
            if ($config->config_data['output_files'] == 'single')
			{
				return true;
			} else {
				return $reservation['customer_type'] == booking_socompleted_reservation::CUSTOMER_TYPE_EXTERNAL;
			}
		}
		
		public function select_internal($reservation) {
            $config	= CreateObject('phpgwapi.config','booking');
			$config->read();
            if ($config->config_data['output_files'] == 'single')
			{
				return false;
			} else {
				return $reservation['customer_type'] == booking_socompleted_reservation::CUSTOMER_TYPE_INTERNAL;
			}
		}
		/**
		 * @return array with three elements where index 0: total_rows, index 1: total_cost, index 2: formatted data
		 */
		public function export_external(array &$reservations, array $account_codes) {
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

            if ($config->config_data['external_format'] == 'CSV')
            {
                $export_format = 'csv';
            }
            elseif ($config->config_data['external_format'] == 'AGRESSO')
            {
    			$export_format = 'agresso';
			} 
            elseif ($config->config_data['external_format'] == 'KOMMFAKT')
            {
    			$export_format = 'kommfakt';
			} 
			
			if (is_array($reservations)) {
				if (count($external_reservations = array_filter($reservations, array($this, 'select_external'))) > 0) {
					
					if (!($number_generator = $this->sequential_number_generator_so->get_generator_instance('external'))) {
						throw new UnexpectedValueException("Unable to find sequential number generator for external export");
					}
					
                    if ($config->config_data['external_format'] == 'CSV')
                    {
      					return $this->build_export_result(
   						$export_format,
   						count(array_filter($internal_reservations, array($this, 'not_free'))),
   						$this->calculate_total_cost($internal_reservations),
   						$this->format_csv($internal_reservations, $account_codes, $number_generator)
       					);
                    }
                    elseif ($config->config_data['external_format'] == 'AGRESSO')
                    {
						return $this->build_export_result(
						$export_format,
						count(array_filter($external_reservations, array($this, 'not_free'))),
						$this->calculate_total_cost($external_reservations),
						$this->format_agresso($external_reservations, $account_codes, $number_generator)
						);
					}
                    elseif ($config->config_data['external_format'] == 'KOMMFAKT')
                    {
						return $this->build_export_result(
						$export_format,
						count(array_filter($external_reservations, array($this, 'not_free'))),
						$this->calculate_total_cost($external_reservations),
						$this->format_kommfakt($external_reservations, $account_codes, $number_generator)
						);
					}
				}
			}
			return $this->build_export_result($export_format, 0, 0.0);
		}
		
		/**
		 * @return array with three elements where index 0: total_rows, index 1: total_cost, index 2: formatted data
		 */
		public function export_internal(array &$reservations, array $account_codes) {
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

            if ($config->config_data['internal_format'] == 'CSV')
            {
                $export_format = 'csv';
            }
            elseif ($config->config_data['internal_format'] == 'AGRESSO')
            {
    			$export_format = 'agresso';
			} 
            elseif ($config->config_data['internal_format'] == 'KOMMFAKT')
            {
    			$export_format = 'kommfakt';
			} 
			
			if (is_array($reservations)) {
				if (count($internal_reservations = array_filter($reservations, array($this, 'select_internal'))) > 0) {
					
					if (!($number_generator = $this->sequential_number_generator_so->get_generator_instance('internal'))) {
						throw new UnexpectedValueException("Unable to find sequential number generator for internal export");
					}
                        if ($config->config_data['internal_format'] == 'CSV')
                        {
        					return $this->build_export_result(
    						$export_format,
    						count(array_filter($internal_reservations, array($this, 'not_free'))),
    						$this->calculate_total_cost($internal_reservations),
    						$this->format_csv($internal_reservations, $account_codes, $number_generator)
        					);
                        }
                        elseif ($config->config_data['internal_format'] == 'AGRESSO')
                        {
        					return $this->build_export_result(
    						$export_format,
    						count(array_filter($internal_reservations, array($this, 'not_free'))),
    						$this->calculate_total_cost($internal_reservations),
    						$this->format_agresso($internal_reservations, $account_codes, $number_generator)
        					);
                        }
                        elseif ($config->config_data['internal_format'] == 'KOMMFAKT')
                        {
        					return $this->build_export_result(
    						$export_format,
    						count(array_filter($internal_reservations, array($this, 'not_free'))),
    						$this->calculate_total_cost($internal_reservations),
    						$this->format_kommfakt($internal_reservations, $account_codes, $number_generator)
        					);
                        }
				}
			}
			return $this->build_export_result($export_format, 0, 0.0);
		}
		
		protected function build_export_result($export_format, $total_items, $total_cost, &$data = null) {
			return array('total_items' => $total_items, 'total_cost' => $total_cost, 'export_format' => $export_format, 'export' => $data);
		}
		
		public function format_cost($cost) {
			$cost = $this->get_cost_value($cost);
			return str_pad(round($cost, 2)*100, 17, 0, STR_PAD_LEFT);
		}
		
		public function get_cost_value($cost) {
			if (is_null($cost)) {
				$cost = floatval(0); //floatval and doubleval, the same thing in php
			}
			
			if (gettype($cost) != 'double') {
				$cost = floatval($cost); //floatval and doubleval, the same thing in php
			}
			
			return $cost;
		}
		
		public function create_export_item_info(&$entity, $generated_order_id) {
			if (!is_array($entity)) {
				throw new InvalidArgumentException("Invalid entity");
			}
			
			if (!isset($entity['id'])) {
				throw new InvalidArgumentException("Invalid entity - missing id");
			} 
			
			if (!isset($entity['reservation_id'])) {
				throw new InvalidArgumentException("Invalid entity - missing reservation_id");
			}
			
			if (!isset($entity['reservation_type'])) {
				throw new InvalidArgumentException("Invalid entity - missing reservation_type");
			}
			
			if (!isset($generated_order_id) || empty($generated_order_id)) {
				throw new InvalidArgumentException("Invalid order_id");
			}
			
			return array('id' => $entity['id'], 'reservation_id' => $entity['reservation_id'], 'reservation_type' => $entity['reservation_type'], 'invoice_file_order_id' => $generated_order_id);
		}
		
		public function combine_export_data(array &$export_results) {
			$combined_data = array();
			$export_format = null;
			$combine_method = null;

			foreach($export_results as &$export_result) {
				if (!isset($export_result['export_format']) || !is_string($export_result['export_format'])) {
					throw new InvalidArgumentException('export_format must be specified');
				}
				
				if ($export_format == null) {
					$export_format = $export_result['export_format'];
					$combine_method = array($this, sprintf('combine_%s_export_data', $export_format));
				} elseif ($export_format != $export_result['export_format']) {
					throw new InvalidArgumentException('Different export formats cannot be combined into a single result');
				}
				
				if (!array_key_exists('export', $export_result)) {
					throw new InvalidArgumentException('Missing export key');
				}
				
				if (is_null($export_result['export'])) {
					continue;
				}
				
				if (!is_array($export_result['export']) || !isset($export_result['export']['data'])) {
					throw new InvalidArgumentException('Missing export data');
				}
				
				call_user_func_array($combine_method, array(&$combined_data, &$export_result['export']));
			}
			
			return count($combined_data) > 0 ? join($combined_data, '') : '';
		}
		
		protected function &combine_csv_export_data(array &$combined_data, $export) {
			if (count($combined_data) == 0) {
				$combined_data[] = $export['data']; //Insert with headers and all
			} else {
				$combined_data[] = "csv_break";
				$combined_data[] = substr($export['data'], strpos($export['data'], "\n")+1); //Remove first line (i.e don't to repeat headers in file)
			}
		}

		public function format_csv(array &$reservations, array $account_codes, $sequential_number_generator) {
			$export_info = array();
			$output = array();
			
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			$columns[] = 'amount';
			$columns[] = 'art_descr';
			$columns[] = 'art';
			if (isset($config->config_data['dim_1'])) $columns[] = $config->config_data['dim_1'];
			if (isset($config->config_data['dim_2'])) $columns[] = $config->config_data['dim_2'];
			if (isset($config->config_data['dim_3'])) $columns[] = $config->config_data['dim_3'];
			if (isset($config->config_data['dim_4'])) $columns[] = $config->config_data['dim_4'];
			$columns[] = 'article';
			if (isset($config->config_data['dim_5'])) $columns[] = $config->config_data['dim_5']; 
			if (isset($config->config_data['dim_value_1'])) $columns[] = $config->config_data['dim_value_1']; 
			if (isset($config->config_data['dim_value_4'])) $columns[] = $config->config_data['dim_value_4']; 
			if (isset($config->config_data['dim_value_5'])) $columns[] = $config->config_data['dim_value_5']; 
			$columns[] = 'ext_ord_ref';
			$columns[] = 'invoice_instruction';
			$columns[] = 'order_id';
			$columns[] = 'period';
			$columns[] = 'short_info';

			$output[] = $this->format_to_csv_line($columns);
			foreach ($reservations as $reservation) {
				if ($this->get_cost_value($reservation['cost']) <= 0) {
					continue; //Don't export costless rows
				}
				$order_id = $sequential_number_generator->increment()->get_current();
				$export_info[] = $this->create_export_item_info($reservation, $order_id);
				
				$reservation = array_map('utf8_decode', $reservation);
				
				$item = array();
				$item['amount'] = $this->format_cost($reservation['cost']); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
				$item['art_descr'] = str_pad(substr($reservation['article_description'], 0, 35), 35, ' '); //35 chars long
				$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');
				//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
				if (isset($config->config_data['dim_1'])) 
				{
					$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' ');
				}

				//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
				if (isset($config->config_data['dim_2'])) 
				{
					$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
				}

				//Objektnr. vil være knyttet til hvert hus (FDVU)
				if (isset($config->config_data['dim_3'])) 
				{
					$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
				}

				if (isset($config->config_data['dim_4'])) 
				{
					$item['dim_4'] = str_pad(substr($account_codes['dim_4'], 0, 8), 8, ' ');
				}

				//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt 
				if (isset($config->config_data['dim_5'])) 
				{
					$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
				}

				if (isset($config->config_data['dim_value_1'])) 
				{
					$item['dim_value_1'] = str_pad(strtoupper(substr($account_codes['unit_number'], 0, 12)), 12, ' ');
				}

				if (isset($config->config_data['dim_value_4'])) 
				{
					$item['dim_value_4'] = str_pad(substr($account_codes['dim_value_4'], 0, 12), 12, ' ');
				}

				if (isset($config->config_data['dim_value_5'])) 
				{
					$item['dim_value_5'] = str_pad(substr($account_codes['dim_value_5'], 0, 12), 12, ' ');
				}
				$item['ext_ord_ref'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 15), 15, ' ');
				$item['long_info1'] = str_pad(substr($account_codes['invoice_instruction'], 0, 120), 120, ' ');
				
				$item['order_id'] = str_pad($order_id, 9, 0, STR_PAD_LEFT);
				
				$item['period'] = str_pad(substr('00'.date('Ym'), 0, 8), 8, '0', STR_PAD_LEFT);
				$item['short_info'] = str_pad(substr($reservation['description'], 0, 60), 60, ' ');

				$output[] = $this->format_to_csv_line(array_values($item));
			}
			
			if (count($export_info) == 0) {
				return null;
			}

			return array('data' => join($output, ''), 'info' => $export_info);
		}
		
		/**
		 * @param array  $fields Ordered array with the data
		 * @param array  $conf   (optional) The configuration of the dest CSV
		 *
		 * @return String Fields in csv format
		 */
		function format_to_csv_line(&$fields, $conf = array())
		{
			$conf = array_merge(array('sep' => ',', 'quote' => '"', 'crlf' => "\n"), $conf);

			$field_count = count($fields);

			$write = '';
			$quote = $conf['quote'];
			for ($i = 0; $i < $field_count; ++$i) {
				// Write a single field
 				$quote_field = false;
				// Only quote this field in the following cases:
				if (is_numeric($fields[$i])) {
				// Numeric fields should not be quoted
				} elseif (isset($conf['sep']) && (strpos($fields[$i], $conf['sep']) !== false)) {
					// Separator is present in field
					$quote_field = true;
				} elseif (strpos($fields[$i], $quote) !== false) {
					// Quote character is present in field
					$quote_field = true;
				} elseif (
					strpos($fields[$i], "\n") !== false
					|| strpos($fields[$i], "\r") !== false
				) {
					// Newline is present in field
					$quote_field = true;
				} elseif (!is_numeric($fields[$i]) && (substr($fields[$i], 0, 1) == " " || substr($fields[$i], -1) == " ")) {
					// Space found at beginning or end of field value
					$quote_field = true;
				}

				if ($quote_field) {
					// Escape the quote character within the field (e.g. " becomes "")
					$quoted_value = str_replace($quote, $quote.$quote, $fields[$i]);

					$write .= $quote . $quoted_value . $quote;
				} else {
					$write .= $fields[$i];
				}

				$write .= ($i < ($field_count - 1)) ? $conf['sep']: $conf['crlf'];
			}

			return $write;
		}
		
		protected function combine_agresso_export_data(array &$combined_data, $export) {
			if (count($combined_data) == 0) {
				$combined_data[] = $export['data'];
			} else {
				$combined_data[] = "\n";
				$combined_data[] = $export['data'];
			}
		}
		
		public function format_agresso(array &$reservations, array $account_codes, $sequential_number_generator) {
			//$orders = array();
			$export_info = array();
			$output = array();

			$log = array();

			/* NOTE: The specification states that values of type date
			 * should be left padded with spaces. The example file,
			 * however, is right padded with spaces.
			 *
			 * Using left padding with spaces (i.e specced version).
			 *
			 * Quote from spec. about values of type date:
			 * Dato. Begynner med mellomrom. Format: ÅÅMMDD
			 */
			$date = str_pad(date('Ymd'), 17, ' ', STR_PAD_LEFT);
			//$date = str_pad(date('ymd'), 17, ' ');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			
			$batch_id = strtoupper(sprintf('BO%s%s', $account_codes['unit_prefix'], date('ymd')));
			$batch_id = str_pad(substr($batch_id, 0, 12), 12, ' ');
			
			$client_id = str_pad(substr(strtoupper('BY'), 0, 2), 2, ' ');
			$currency = str_pad(substr(strtoupper('NOK'), 0, 3), 3, ' ');
			$order_type = str_pad(substr(strtoupper('FS'), 0, 2), 2, ' ');
			$pay_method = str_pad(substr(strtoupper('IP'), 0, 2), 2, ' ');
			
			/* NOTE: The specification states i8 format (integer left padded with zeroes)
			 * whereas the example file uses c8 format (8 characters right padded with spaces).
			 *
			 * Using i8 for now (i.e specced version)
			 *
			 * Quoted from spec (note the use of leading zeroes):
			 * ÅÅÅÅMM (ok leveres, eksempel 00200806). Skal leveres, perioden for ordren - blir liggende på selve ordren i Agresso, har ikke betydning for reskontro/hoevdbok - som vil få aktuell måned ved fakturering (SO13)
			 */
			$period = str_pad(substr('00'.date('Ym'), 0, 8), 8, '0', STR_PAD_LEFT);
			//$period = str_pad(substr(date('Ym'), 0, 8), 8, ' ');
			
			$responsible = str_pad(substr(strtoupper('BOOKING'), 0, 8), 8, ' ');
			$responsible2 = str_pad(substr(strtoupper($responsible), 0, 8), 8, ' ');
			$status = str_pad(substr(strtoupper('N'), 0, 1), 1, ' ');
			$trans_type = str_pad(substr(strtoupper('42'), 0, 2), 2, ' ');
			$voucher_type = str_pad(substr(strtoupper('FK'), 0, 2), 2, ' ');

			$stored_header = array();			
			$line_no = 0;
            $header_count = 0;
			$log_order_id = '';
			$log_customer_name = '';
			$log_customer_nr = '';
			$log_buidling = '';
			
			$internal = false;

			foreach($reservations as &$reservation) {

				if ($this->get_cost_value($reservation['cost']) <= 0) {
					continue; //Don't export costless rows
				}

				$type = $reservation['customer_type'];
	
				if ($stored_header == array() || $stored_header['tekst2'] != $this->get_customer_identifier_value_for($reservation))
				{
					$order_id = $sequential_number_generator->increment()->get_current();
					$export_info[] = $this->create_export_item_info($reservation, $order_id);
                    $header_count += 1;
					//header level
					$header = $this->get_agresso_row_template();
					$header['accept_flag'] = '1';
				
					// TODO: Introduce a unique id if several transfers in one day?
					$header['batch_id'] = 
					$stored_header['batch_id'] = $batch_id;
				
					$header['client'] = $client_id;
					$stored_header['client'] = $client_id;
					$header['confirm_date'] = $date;
					$header['currency'] = $currency;
					$header['deliv_date'] = $header['confirm_date'];
				
					//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
					if (isset($config->config_data['dim_value_1']))
					{
						$header['dim_value_1'] = str_pad(strtoupper(substr($account_codes['unit_number'], 0, 12)), 12, ' ');
					}
				
					if (isset($config->config_data['dim_value_4']))
					{
						$header['dim_value_4'] = str_pad(substr($account_codes['dim_value_4'], 0, 12), 12, ' ');
					}
				
					if (isset($config->config_data['dim_value_5']))
					{
						$header['dim_value_5'] = str_pad(substr($account_codes['dim_value_5'], 0, 12), 12, ' ');
					}
				
					//Nøkkelfelt, kundens personnr/orgnr.
					$stored_header['tekst2'] = $this->get_customer_identifier_value_for($reservation);

		            if ($type == 'internal') {
						$header['tekst2'] = str_pad(substr($config->config_data['organization_value'], 0, 12), 12, ' ');
						$header['ext_ord_ref'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 15), 15, ' ');
		            } else {
						$header['tekst2'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 12), 12, ' ');
		            }

					$header['line_no'] = '0000'; //Nothing here according to example file but spec. says so
				
					//Topptekst til faktura, knyttet mot fagavdeling
					$header['long_info1'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$account_codes['invoice_instruction']), 0, 120), 120, ' ');

					//Ordrenr. UNIKT, løpenr. genereres i booking ut fra gitt serie, eks. 38000000
					$header['order_id'] = str_pad($order_id, 9, 0, STR_PAD_LEFT);
					$stored_header['order_id'] = str_pad($order_id, 9, 0, STR_PAD_LEFT);
				
					$header['order_type'] = $order_type;
					$header['pay_method'] = $pay_method;
					$header['period'] = $period;
					$stored_header['period'] = $period;
					$header['responsible'] = $responsible;
					$header['responsible2'] = $responsible2;
					//$header['sequence_no'] = str_repeat('0', 8); //Shouldn't be here although some examples provide it here
					$header['status'] = $status;
					$stored_header['status'] = $status;
					$header['trans_type'] = $trans_type;
					$stored_header['trans_type'] = $trans_type;
					$header['voucher_type'] = $voucher_type;
					$stored_header['voucher_type'] = $voucher_type;

					//item level
					$item = $this->get_agresso_row_template();
					$line_no = 1;
					$item['accept_flag'] = '0';
				
					$item['amount'] = $this->format_cost($reservation['cost']); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
					$item['amount_set'] = '1';
				
					/* Data hentes fra booking, tidspunkt legges i eget felt som kommer på 
					 * linjen under: 78_short_info. <navn på bygg>,  <navn på ressurs>
					 */
					$item['art_descr'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['article_description']), 0, 35), 35, ' '); //35 chars long
				
					//Artikkel opprettes i Agresso (4 siffer), en for kultur og en for idrett, inneholder konteringsinfo.
					$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');
				
					$item['batch_id'] = $header['batch_id'];
					$item['client'] = $header['client'];

					//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
					if (isset($config->config_data['dim_1']))
					{
						$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' '); 
					}
				
					//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
					if (isset($config->config_data['dim_2']))
					{
						$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
					}
				
					//Objektnr. vil være knyttet til hvert hus (FDVU)
					if (isset($config->config_data['dim_3']))
					{
						$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
					}
				
					if (isset($config->config_data['dim_4']))
					{
						$item['dim_4'] = str_pad(substr($account_codes['dim_4'], 0, 8), 8, ' ');
					}
				
					//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt 
					if (isset($config->config_data['dim_5']))
					{
						$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
					}
				
					$item['line_no'] = str_pad($line_no, 4, 0, STR_PAD_LEFT);
				
					$item['order_id'] = $header['order_id'];
					$item['period'] = $header['period'];
					$item['sequence_no'] = str_repeat('0', 8);

					$item['status'] = $header['status'];
					$item['trans_type'] = $header['trans_type'];
				
					$item['value_1'] = str_pad(1*100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
					$item['voucher_type'] = $header['voucher_type'];
				
					//text level
					$text = $this->get_agresso_row_template();
					$text['accept_flag'] = '0';
					$text['order_id'] = $header['order_id'];
					$text['batch_id'] = $header['batch_id'];
					$text['client'] = $header['client'];
					$text['line_no'] = $item['line_no']; 
					$text['short_info'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['description']), 0, 60), 60, ' ');
					$text['trans_type'] = $header['trans_type'];
					$text['voucher_type'] = $header['voucher_type'];
				
					$text['sequence_no'] = str_pad(intval($item['sequence_no'])+1, 8, '0', STR_PAD_LEFT);
				
					//Add to orders
					//$orders[] = array('header' => $header, 'items' => array('item' => $item, 'text' => $text));
					$output[] = implode('', str_replace(array("\n", "\r"), '', $header));
					$output[] = implode('', str_replace(array("\n", "\r"), '', $item));
					$output[] = implode('', str_replace(array("\n", "\r"), '', $text));

					$log_order_id = $order_id;

		            if ($type == 'internal') {
						$log_customer_nr = $header['tekst2'].' '.$header['ext_ord_ref'];
					} else {
						$log_customer_nr = $header['tekst2'];
					}
					if(!empty($reservation['organization_id'])) {
						$org = $this->organization_bo->read_single($reservation['organization_id']);				
						$log_customer_name = $org['name'];
					} else {
						$data = $this->event_so->get_org($reservation['customer_organization_number']);
						if(!empty($data['id'])) {
							$log_customer_name = $data['name'];
						} else {
							if($reservation['reservation_type'] == 'event') {
								$data = $this->event_bo->read_single($reservation['reservation_id']);
								$log_customer_name = $data['contact_name'];
#							} elseif ($reservation['reservation_type'] == 'booking') {
#								$data = $this->booking_bo->read_single($reservation['reservation_id']);
#								error_log('b'.$data['id']." ".$data['group_id']);
#							} else {
#								$data = $this->allocation_bo->read_single($reservation['reservation_id']);
#								error_log('a'.$data['id']." ".$data['organization_id']);
							}
						}
					}

					$log_buidling = $reservation['building_name'];
					$log_cost = $reservation['cost'];
					$log_varelinjer_med_dato = $reservation['article_description'].' - '.$reservation['description'];

					$log[] = $log_order_id.';'.$log_customer_name.' - '.$log_customer_nr.';'.$log_varelinjer_med_dato.';'.$log_buidling.';'.$log_cost;
				} else {

					//item level
					$item = $this->get_agresso_row_template();
					$line_no += 1;
					$item['accept_flag'] = '0';
				
					$item['amount'] = $this->format_cost($reservation['cost']); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
					$item['amount_set'] = '1';
				
					/* Data hentes fra booking, tidspunkt legges i eget felt som kommer på 
					 * linjen under: 78_short_info. <navn på bygg>,  <navn på ressurs>
					 */
					$item['art_descr'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['article_description']), 0, 35), 35, ' '); //35 chars long
				
					//Artikkel opprettes i Agresso (4 siffer), en for kultur og en for idrett, inneholder konteringsinfo.
					$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');
				
					$item['batch_id'] = $stored_header['batch_id'];
					$item['client'] = $stored_header['client'];

					//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
					if (isset($config->config_data['dim_1']))
					{
						$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' '); 
					}
				
					//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
					if (isset($config->config_data['dim_2']))
					{
						$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
					}
				
					//Objektnr. vil være knyttet til hvert hus (FDVU)
					if (isset($config->config_data['dim_3']))
					{
						$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
					}
				
					if (isset($config->config_data['dim_4']))
					{
						$item['dim_4'] = str_pad(substr($account_codes['dim_4'], 0, 8), 8, ' ');
					}
				
					//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt 
					if (isset($config->config_data['dim_5']))
					{
						$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
					}
				
					$item['line_no'] = str_pad($line_no, 4, 0, STR_PAD_LEFT);
				
					$item['order_id'] = $stored_header['order_id'];
					$item['period'] = $stored_header['period'];
					$item['sequence_no'] = str_repeat('0', 8);

					$item['status'] = $stored_header['status'];
					$item['trans_type'] = $stored_header['trans_type'];
				
					$item['value_1'] = str_pad(1*100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
					$item['voucher_type'] = $stored_header['voucher_type'];
				
					//text level
					$text = $this->get_agresso_row_template();
					$text['accept_flag'] = '0';
					$text['order_id'] = $stored_header['order_id'];
					$text['batch_id'] = $stored_header['batch_id'];
					$text['client'] = $stored_header['client'];
					$text['line_no'] = $item['line_no']; 
					$text['short_info'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['description']), 0, 60), 60, ' ');
					$text['trans_type'] = $stored_header['trans_type'];
					$text['voucher_type'] = $stored_header['voucher_type'];
				
					$text['sequence_no'] = str_pad(intval($item['sequence_no'])+1, 8, '0', STR_PAD_LEFT);
				
					//Add to orders
					//$orders[] = array('header' => $header, 'items' => array('item' => $item, 'text' => $text));
					$output[] = implode('', str_replace(array("\n", "\r"), '', $item));
					$output[] = implode('', str_replace(array("\n", "\r"), '', $text));

					$log_cost = $reservation['cost'];
					$log_varelinjer_med_dato = $reservation['article_description'].' - '.$reservation['description'];

					$log[] = $log_order_id.';'.$log_customer_name.' - '.$log_customer_nr.';'.$log_varelinjer_med_dato.';'.$log_buidling.';'.$log_cost;

				}
			}
			
			if (count($export_info) == 0) {
				return null;
			}

            if ($config->config_data['external_format_linebreak'] == 'Windows') {
                $file_format_linebreak = "\r\n";
            } else {
                $file_format_linebreak = "\n";
            }    
		
			return array('data' => implode($file_format_linebreak, $output), 'data_log' => implode("\n", $log), 'info' => $export_info, 'header_count' => $header_count);
		}
		
		protected function get_agresso_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }
			
			$row_template = array('accept_flag' => str_repeat(' ', 1), 'account' => str_repeat(' ', 8), 'accountable' => str_repeat(' ', 20), 'address' => str_repeat(' ', 160), 'allocation_key' => str_repeat(' ', 2), 'amount' => str_repeat(' ', 17), 'amount_set' => str_repeat(' ', 1), 'apar_id' => str_repeat(' ', 8), 'apar_name' => str_repeat(' ', 30), 'art_descr' => str_repeat(' ', 35), 'article' => str_repeat(' ', 15), 'att_1_id' => str_repeat(' ', 2), 'att_2_id' => str_repeat(' ', 2), 'att_3_id' => str_repeat(' ', 2), 'att_4_id' => str_repeat(' ', 2), 'att_5_id' => str_repeat(' ', 2), 'att_6_id' => str_repeat(' ', 2), 'att_7_id' => str_repeat(' ', 2), 'bank_account' => str_repeat(' ', 35), 'batch_id' => str_repeat(' ', 12), 'client' => str_repeat(' ', 2), 'client_ref' => str_repeat(' ', 2), 'confirm_date' => str_repeat(' ', 17), 'control' => str_repeat(' ', 1), 'cur_amount' => str_repeat(' ', 17), 'currency' => str_repeat(' ', 3), 'del_met_descr' => str_repeat(' ', 60), 'del_term_descr' => str_repeat(' ', 60), 'deliv_addr' => str_repeat(' ', 255), 'deliv_attention' => str_repeat(' ', 50), 'deliv_countr' => str_repeat(' ', 3), 'deliv_date' => str_repeat(' ', 17), 'deliv_method' => str_repeat(' ', 8), 'deliv_terms' => str_repeat(' ', 8), 'dim_1' => str_repeat(' ', 8), 'dim_2' => str_repeat(' ', 8), 'dim_3' => str_repeat(' ', 8), 'dim_4' => str_repeat(' ', 8), 'dim_5' => str_repeat(' ', 12), 'dim_6' => str_repeat(' ', 4), 'dim_7' => str_repeat(' ', 4), 'dim_value_1' => str_repeat(' ', 12), 'dim_value_2' => str_repeat(' ', 12), 'dim_value_3' => str_repeat(' ', 12), 'dim_value_4' => str_repeat(' ', 12), 'dim_value_5' => str_repeat(' ', 12), 'dim_value_6' => str_repeat(' ', 12), 'dim_value_7' => str_repeat(' ', 12), 'disc_percent' => str_repeat(' ', 17), 'exch_rate' => str_repeat(' ', 17), 'ext_ord_ref' => str_repeat(' ', 15), 'intrule_id' => str_repeat(' ', 6), 'line_no' => str_repeat(' ', 4), 'location' => str_repeat(' ', 4), 'long_info1' => str_repeat(' ', 120), 'long_info2' => str_repeat(' ', 120), 'lot' => str_repeat(' ', 10), 'main_apar_id' => str_repeat(' ', 8), 'mark_attention' => str_repeat(' ', 50), 'mark_ctry_cd' => str_repeat(' ', 3), 'markings' => str_repeat(' ', 120), 'obs_date' => str_repeat(' ', 17), 'order_date' => str_repeat(' ', 17), 'order_id' => str_repeat(' ', 9), 'order_type' => str_repeat(' ', 2), 'pay_method' => str_repeat(' ', 2), 'period' => str_repeat(' ', 8), 'place' => str_repeat(' ', 30), 'province' => str_repeat(' ', 40), 'rel_value' => str_repeat(' ', 12), 'responsible' => str_repeat(' ', 8), 'responsible2' => str_repeat(' ', 8), 'sequence_no' => str_repeat(' ', 8), 'sequence_ref' => str_repeat(' ', 8), 'serial_no' => str_repeat(' ', 20), 'short_info' => str_repeat(' ', 60), 'status' => str_repeat(' ', 1), 'tax_code' => str_repeat(' ', 2), 'tax_system' => str_repeat(' ', 2), 'template_id' => str_repeat(' ', 8), 'terms_id' => str_repeat(' ', 2), 'tekx1' => str_repeat(' ', 12), 'tekst2' => str_repeat(' ', 12), 'tekst3' => str_repeat(' ', 12), 'text4' => str_repeat(' ', 12), 'trans_type' => str_repeat(' ', 2), 'unit_code' => str_repeat(' ', 3), 'unit_descr' => str_repeat(' ', 50), 'value_1' => str_repeat(' ', 17), 'voucher_ref' => str_repeat(' ', 9), 'voucher_type' => str_repeat(' ', 2), 'warehouse' => str_repeat(' ', 4), 'zip_code' => str_repeat(' ', 15));
			return $row_template;
		}

		protected function combine_kommfakt_export_data(array &$combined_data, $export) {
			if (count($combined_data) == 0) {
				$combined_data[] = $export['data'];
			} else {
				$combined_data[] = "\n";
				$combined_data[] = $export['data'];
			}
		}

		public function format_kommfakt(array &$reservations, array $account_codes, $sequential_number_generator) {
			$export_info = array();
			$output = array();
			
			$log = array();

			$date = str_pad(date('Ymd'), 17, ' ', STR_PAD_LEFT);

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			
			

			$stored_header = array();			
			$line_no = 0;
            $header_count = 0;
			$log_order_id = '';
			$log_customer_name = '';
			$log_customer_nr = '';
			$log_buidling = '';
			
			$internal = false;

			$ant_post = 0;
			$linjenr = 1;	
			$lopenr = 1;	

			foreach($reservations as &$reservation) {

				if ($this->get_cost_value($reservation['cost']) <= 0) {
					continue; //Don't export costless rows
				}

				if(!empty($reservation['organization_id'])) {
					$org = $this->organization_bo->read_single($reservation['organization_id']);				
					$reservation['organization_name'] = $org['name'];
				} else {
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if(!empty($data['id'])) {
						$reservation['organization_name'] = $data['name'];
					} else {
						if($reservation['reservation_type'] == 'event') {
							$data = $this->event_bo->read_single($reservation['reservation_id']);
							$reservation['organization_name'] = $data['contact_name'];
#						} elseif ($reservation['reservation_type'] == 'booking') {
#							$data = $this->booking_bo->read_single($reservation['reservation_id']);
#							error_log('b'.$data['id']." ".$data['group_id']);
#						} else {
#							$data = $this->allocation_bo->read_single($reservation['reservation_id']);
#							error_log('a'.$data['id']." ".$data['organization_id']);
						}
					}
				}

				$type = $reservation['customer_type'];

				$order_id = $sequential_number_generator->increment()->get_current();
				$export_info[] = $this->create_export_item_info($reservation, $order_id);
				$header_count += 1;
				$stored_header['kundenr'] = $kundenr;

				$kundenr = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 11), 11, '0',STR_PAD_LEFT);


				if (strlen($this->get_customer_identifier_value_for($reservation)) > 9) {
					$name = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['organization_name']), 30, ' ');
				} else {
					$name = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['organization_name']), 30, ' ');
				} 		

				//Startpost ST
				$startpost = $this->get_kommfakt_ST_row_template();
				$startpost['posttype'] = 'ST';
				$startpost['referanse'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['article_description']), 0, 60), 60, ' ');
#				$startpost['referanse'] = str_pad(substr(iconv("utf-8","ISO-8859-1//TRANSLIT",$account_codes['invoice_instruction']), 0, 60), 60, ' ');

				//Fakturalinje FL
				$fakturalinje = $this->get_kommfakt_FL_row_template();
				$fakturalinje['posttype'] = 'FL';
				$fakturalinje['kundenr'] = $kundenr;
				$fakturalinje['navn'] = $name;
#				$fakturalinje['adresse1'] = ;
#				$fakturalinje['adresse2'] = ;
#				$fakturalinje['postnr'] = ;
				$fakturalinje['betform'] = 'BG';
				$fakturalinje['oppdrgnr'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$account_codes['object_number']), 3, '0', STR_PAD_LEFT);
				$fakturalinje['varenr'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$account_codes['responsible_code']), 4, '0', STR_PAD_LEFT);
				$fakturalinje['lopenr'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$lopenr), 2, '0', STR_PAD_LEFT);
				$fakturalinje['pris'] = str_pad($reservation['cost']*100,8,'0',STR_PAD_LEFT).' ';
				$fakturalinje['grunnlag'] = '000000001';
				$fakturalinje['belop'] = str_pad($reservation['cost']*100,8,'0',STR_PAD_LEFT).' ';
#				$fakturalinje['saksnr'] = ;

				//Linjetekst LT
				$linjetekst = $this->get_kommfakt_LT_row_template();
				$linjetekst['posttype'] = 'LT';
				$linjetekst['kundenr'] = $kundenr;
				$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$account_codes['object_number']), 3, '0', STR_PAD_LEFT);
				$linjetekst['varenr'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$account_codes['responsible_code']), 4, '0', STR_PAD_LEFT) ;
				$linjetekst['lopenr'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$lopenr), 2, '0', STR_PAD_LEFT);
				$linjetekst['linjenr'] = $linjenr;
				$linjetekst['tekst'] = str_pad(iconv("utf-8","ISO-8859-1//TRANSLIT",$reservation['description']), 50, ' ');
				$ant_post += 3;

					//Sluttpost SL
				$sluttpost = $this->get_kommfakt_SL_row_template();
				$sluttpost['posttype'] = 'SL';
				$sluttpost['antpost'] = str_pad(intval($ant_post)+1, 8, '0', STR_PAD_LEFT);
				$ant_post = 0;


				$log_order_id = $order_id;

				if(!empty($reservation['organization_id'])) {
					$org = $this->organization_bo->read_single($reservation['organization_id']);				
					$log_customer_name = $org['name'];
				} else {
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if(!empty($data['id'])) {
						$log_customer_name = $data['name'];
					} else {
						if($reservation['reservation_type'] == 'event') {
							$data = $this->event_bo->read_single($reservation['reservation_id']);
							$log_customer_name = $data['contact_name'];
#						} elseif ($reservation['reservation_type'] == 'booking') {
#							$data = $this->booking_bo->read_single($reservation['reservation_id']);
#							error_log('b'.$data['id']." ".$data['group_id']);
#						} else {
#							$data = $this->allocation_bo->read_single($reservation['reservation_id']);
#							error_log('a'.$data['id']." ".$data['organization_id']);
						}
					}
				}

    			$log_customer_nr = $this->get_customer_identifier_value_for($reservation);
				$log_buidling = $reservation['building_name'];
				$log_cost = $reservation['cost'];
				$log_varelinjer_med_dato = $reservation['article_description'].' - '.$reservation['description'];

				$log[] = $log_order_id.';'.$log_customer_name.' - '.$log_customer_nr.';'.$log_varelinjer_med_dato.';'.$log_buidling.';'.$log_cost;

				$output[] = implode('', str_replace(array("\n", "\r"), '', $startpost));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $fakturalinje));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $linjetekst));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $sluttpost));

			}			

			if (count($export_info) == 0) {
				return null;
			}
            if ($config->config_data['external_format_linebreak'] == 'Windows') {
                $file_format_linebreak = "\r\n";
            } else {
                $file_format_linebreak = "\n";
            }    

			return array('data' => implode($file_format_linebreak, $output), 'data_log' => implode("\n", $log), 'info' => $export_info, 'header_count' => $header_count);

		}		

		protected function get_kommfakt_ST_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }
	
			$row_template = array('posttype' => str_repeat(' ', 2), 'referanse' => str_repeat(' ', 60));
			return $row_template;
		}

		protected function get_kommfakt_FL_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }

			$row_template = array('posttype' => str_repeat(' ', 2), 'kundenr' => str_repeat(' ', 11), 'navn' => str_repeat(' ', 30), 'adresse1' => str_repeat(' ', 30), 'adresse2' => str_repeat(' ', 30), 'postnr' => str_repeat(' ', 4), 'betform' => str_repeat(' ', 2), 'oppdrgnr' => str_repeat(' ', 3), 'varenr' => str_repeat(' ', 4), 'lopenr' => str_repeat(' ', 2), 'pris' => str_repeat(' ', 9), 'grunnlag' => str_repeat(' ', 9), 'belop' => str_repeat(' ', 11), 'saksnr' => str_repeat(' ', 16));
			return $row_template;
	
		}

		protected function get_kommfakt_LT_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }
	
			$row_template = array('posttype' => str_repeat(' ', 2), 'kundenr' => str_repeat(' ', 11), 'oppdrgnr' => str_repeat(' ', 3), 'varenr' => str_repeat(' ', 4), 'lopenr' => str_repeat(' ', 2), 'linjenr' => str_repeat(' ', 2), 'tekst' => str_repeat(' ', 50));
			return $row_template;
		}

		protected function get_kommfakt_SL_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }
	
			$row_template = array('posttype' => str_repeat(' ', 2), 'antpost' => str_repeat(' ', 8));
			return $row_template;
		}
	}
