<?php
	phpgw::import_class('booking.socommon');
	
	class booking_socompleted_reservation_export extends booking_socommon
	{
		protected 
			$file_storage,
			$completed_reservation_so,
			$completed_reservation_bo,
			$account_code_set_so,
			$customer_id;
			
		protected static $export_type_to_file_type_map = array(
			'internal' => 'csv',
			'external' => 'txt',
		);
		
		function __construct()
		{
			$this->file_storage = CreateObject('booking.filestorage', $this);
			$this->customer_id = CreateObject('booking.customer_identifier');
			$this->completed_reservation_so = CreateObject('booking.socompleted_reservation');
			$this->completed_reservation_bo = CreateObject('booking.bocompleted_reservation');
			$this->account_code_set_so = CreateObject('booking.soaccount_code_set');
			
			parent::__construct('bb_completed_reservation_export', 
				array(
					'id' 						=> array('type' => 'int'),
					'season_id' 			=> array('type' => 'int'),
					'building_id'    		=> array('type' => 'int'),
					'from_'					=> array('type' => 'timestamp', 'required' => true),
					'to_'						=> array('type' => 'timestamp', 'required' => true),
					key(booking_socommon::$AUTO_CREATED_ON) => current(booking_socommon::$AUTO_CREATED_ON),
					key(booking_socommon::$AUTO_CREATED_BY) => current(booking_socommon::$AUTO_CREATED_BY),
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
					'export_files' => array('manytomany' => array(
							'table' => 'bb_completed_reservation_export_file',
							'key' => 'export_id',
							'column' => array(
								'id' => array('type' => 'int'),
								'filename' => array('type' => 'string'),
								'type' => array('type' => 'string', 'required' => true),
								'account_code_set_id' => array('type' => 'int', 'required' => true),
							)
					)),
					'created_by_name' => booking_socommon::$REL_CREATED_BY_NAME,
				)
			);
		}
		
		protected function file_type_for_export_type($export_type) {
			return isset(self::$export_type_to_file_type_map[$export_type]) ? 
						self::$export_type_to_file_type_map[$export_type] :
						'txt';
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
				$errors['nothing_to_export'] = 'Nothing to export';
				return;
			}
			
			foreach($exportable_reservations as &$reservation) {
				if (!$this->get_customer_identifier_value_for($reservation)) {
					$errors['invalid_customer_ids'] = 'Unable to export: Missing a valid Customer ID on some rows';
				}
			}
		}
		
		function read_single($id)
		{
			$entity = parent::read_single($id);
			
			//re-index export files by their types
			$export_files = array();
			if (!(array_key_exists('export_files', $entity) && is_array($entity['export_files']))) {
				return $entity;
			}
			
			foreach($entity['export_files'] as $file) {
				$export_files[$file['type']] = $file;
			}
			$entity['export_files'] = $export_files;
			
			return $entity;
		}
		
		function get_export_file($entity, $type) {
			$entity_file = null;
			
			foreach($entity['export_files'] as $file) {
				if ($file['type'] == $type) {
					$entity_file = $file; 
					break;
				}
			}
			
			if (!$entity_file) {
				throw new InvalidArgumentException(sprintf('Could not locate export file of type "%s" for export id "%s"', $type, $entity['id']));
			}
			
			if (isset($entity_file['filename']) && !empty($entity_file['filename'])) {
				return $this->file_storage->get($entity_file['filename']);
			}
			
			if (!isset($entity_file['id'])) {
				throw new LogicException('Export file is missing id');
			}
			
			$export_reservations =& $this->get_completed_reservations_for($entity['id']);
			
			$entity_file['filename'] = 'export_'.$entity_file['type'].'_'.$entity_file['id'].'.'.$this->file_type_for_export_type($entity_file['type']);
			
			$export_file = new booking_storage_object($entity_file['filename']);
			
			$account_codes = $this->account_code_set_so->read_single($entity_file['account_code_set_id']);
			
			$export_method = "export_{$entity_file['type']}";
			
			if (!method_exists($this, $export_method)) {
				throw new LogicException(sprintf('Cannot generate export for type "%s"', $entity_file['type']));
			}
			
			$export_file->set_data(
				$this->$export_method($export_reservations, $account_codes)
			);
			
			$this->db->transaction_begin();
			$this->file_storage->attach($export_file)->persist();
			$this->db_query(
				"UPDATE {$this->fields['export_files']['manytomany']['table']} SET filename=".
					$this->_marshal($entity_file['filename'], $this->fields['export_files']['manytomany']['column']['filename']['type']).
					' WHERE id='.$entity_file['id'],
				__LINE__, __FILE__
			);
			
			if ($this->db->transaction_commit()) { 
				return $export_file;
			}
			
			try {
				if ($export_file->exists()) {
					$export_file->delete();
				}
			} catch (booking_unattached_storage_object $e) { }
			
			throw new UnexpectedValueException('Transaction failed.');
		}
		
		function add($entry) {
			$export_reservations =& $this->get_completed_reservations_for($entry);
			
			if (!$export_reservations) {
				throw new LogicException('Nothing to export');
			}
			
			$entry['from_'] = $export_reservations[0]['to_'];
			$entry['to_'] = $export_reservations[count($export_reservations)-1]['to_'];
			
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
			
			$reservations = $this->completed_reservation_so->read(array('filters' => $filters, 'results' => 'all', 'order' => 'to_', 'dir' => 'asc'));
			
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
		
		public function select_external($reservation) {
			return $reservation['customer_type'] == booking_socompleted_reservation::CUSTOMER_TYPE_EXTERNAL;
		}
		
		public function select_internal($reservation) {
			return $reservation['customer_type'] == booking_socompleted_reservation::CUSTOMER_TYPE_INTERNAL;
		}
		
		public function export_external(array &$reservations, array $account_codes) {
			if (is_array($reservations)) {
				if (count($external_reservations = array_filter($reservations, array($this, 'select_external'))) > 0) {
					return $this->format_agresso($external_reservations, $account_codes);
				}
			}
			return '';
		}
		
		public function export_internal(array &$reservations, array $account_codes) {
			if (is_array($reservations)) {
				if (count($internal_reservations = array_filter($reservations, array($this, 'select_internal'))) > 0) {
					return $this->format_csv($internal_reservations, $account_codes);
				}
			}
			return '';
		}
		
		public function format_csv(array &$reservations, array $account_codes) {
			$output = array();
			
			$columns = array(
				'amount', 
				'art_descr', 
				'art', 
				'responsible_code', 
				'service', 
				'object_number', 
				'project_number', 
				'unit_number',
				'ext_ord_ref',
				'invoice_instruction', 
				'order_id',
				'period',
				'short_info',
			);
			
			$output[] = $this->format_to_csv_line($columns);
			
			foreach ($reservations as $reservation) {
				$reservation = array_map('utf8_decode', $reservation);
				
				$item = array();
				$item['amount'] = str_pad($reservation['cost']*100, 17, 0, STR_PAD_LEFT); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
				$item['art_descr'] = str_pad(substr($reservation['article_description'], 0, 35), 35, ' '); //35 chars long
				$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');
				//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
				$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' '); 

				//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
				$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');

				//Objektnr. vil være knyttet til hvert hus (FDVU)
				$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');

				//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt 
				$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');

				$item['dim_value_1'] = str_pad(strtoupper(substr($account_codes['unit_number'], 0, 12)), 12, ' ');
				$item['ext_ord_ref'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 15), 15, ' ');
				$item['long_info1'] = str_pad(substr($account_codes['invoice_instruction'], 0, 120), 120, ' ');
				$item['order_id'] = str_pad($reservation['id'], 9, 0, STR_PAD_LEFT);
				$item['period'] = str_pad(substr('00'.date('Ym'), 0, 8), 8, '0', STR_PAD_LEFT);
				$item['short_info'] = str_pad(substr($reservation['description'], 0, 60), 60, ' ');

				$output[] = $this->format_to_csv_line(array_values($item));
			}
			
			return join($output, '');
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
		
		public function format_agresso(array &$reservations, array $account_codes) {
			//$orders = array();
			$output = array();
			
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
			$voucher_type = str_pad(substr(strtoupper('XX'), 0, 2), 2, ' ');
			
			foreach($reservations as &$reservation) {
				foreach($reservation as &$field) {
					$field = utf8_decode($field);
				}
				
				//header level
				$header = $this->get_agresso_row_template();
				$header['accept_flag'] = '1';
				
				/* TODO: Introduce a unique id if several transfers in one day?
				 */
				$header['batch_id'] = $batch_id;
				
				$header['client'] = $client_id;
				$header['confirm_date'] = $date;
				$header['currency'] = $currency;
				$header['deliv_date'] = $header['confirm_date'];
				
				//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
				$header['dim_value_1'] = str_pad(strtoupper(substr($account_codes['unit_number'], 0, 12)), 12, ' ');
				
				//Nøkkelfelt, kundens personnr/orgnr.
				$header['ext_ord_ref'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 15), 15, ' ');
				 
				$header['line_no'] = '0000'; //Nothing here according to example file but spec. says so
				
				//Topptekst til faktura, knyttet mot fagavdeling
				$header['long_info1'] = str_pad(substr($account_codes['invoice_instruction'], 0, 120), 120, ' ');
				
				//Ordrenr. UNIKT, løpenr. genereres i booking ut fra gitt serie, eks. 38000000
				$header['order_id'] = str_pad($reservation['id'], 9, 0, STR_PAD_LEFT);
				
				$header['order_type'] = $order_type;
				$header['pay_method'] = $pay_method;
				$header['period'] = $period;
				$header['responsible'] = $responsible;
				$header['responsible2'] = $responsible2;
				//$header['sequence_no'] = str_repeat('0', 8); //Shouldn't be here although some examples provide it here
				$header['status'] = $status;
				$header['trans_type'] = $trans_type;
				$header['voucher_type'] = $voucher_type;
				
				//item level
				$item = $this->get_agresso_row_template();
				$line_no = 1;
				
				$item['amount'] = str_pad($reservation['cost']*100, 17, 0, STR_PAD_LEFT); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
				$item['amount_set'] = '1';
				
				/* Data hentes fra booking, tidspunkt legges i eget felt som kommer på 
				 * linjen under: 78_short_info. <navn på bygg>,  <navn på ressurs>
				 */
				$item['art_descr'] = str_pad(substr($reservation['article_description'], 0, 35), 35, ' '); //35 chars long
				
				//Artikkel opprettes i Agresso (4 siffer), en for kultur og en for idrett, inneholder konteringsinfo.
				$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');
				
				$item['batch_id'] = $header['batch_id'];
				$item['client'] = $header['client'];
				
				//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
				$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' '); 
				
				//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
				$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
				
				//Objektnr. vil være knyttet til hvert hus (FDVU)
				$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
				
				//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt 
				$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
				
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
				$text['batch_id'] = $header['batch_id'];
				$text['client'] = $header['client'];
				$text['line_no'] = $item['line_no']; 
				$text['short_info'] = str_pad(substr($reservation['description'], 0, 60), 60, ' ');
				$text['trans_type'] = $header['trans_type'];
				$text['voucher_type'] = $header['voucher_type'];
				
				$text['sequence_no'] = str_pad(intval($item['sequence_no'])+1, 8, '0', STR_PAD_LEFT);
				
				//Add to orders
				//$orders[] = array('header' => $header, 'items' => array('item' => $item, 'text' => $text));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $header));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $item));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $text));
			}
			
			return implode("\n", $output);
		}
		
		protected function get_agresso_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }
			
			$row_template = array('accept_flag' => str_repeat(' ', 1), 'account' => str_repeat(' ', 8), 'accountable' => str_repeat(' ', 20), 'address' => str_repeat(' ', 160), 'allocation_key' => str_repeat(' ', 2), 'amount' => str_repeat(' ', 17), 'amount_set' => str_repeat(' ', 1), 'apar_id' => str_repeat(' ', 8), 'apar_name' => str_repeat(' ', 30), 'art_descr' => str_repeat(' ', 35), 'article' => str_repeat(' ', 15), 'att_1_id' => str_repeat(' ', 2), 'att_2_id' => str_repeat(' ', 2), 'att_3_id' => str_repeat(' ', 2), 'att_4_id' => str_repeat(' ', 2), 'att_5_id' => str_repeat(' ', 2), 'att_6_id' => str_repeat(' ', 2), 'att_7_id' => str_repeat(' ', 2), 'bank_account' => str_repeat(' ', 35), 'batch_id' => str_repeat(' ', 12), 'client' => str_repeat(' ', 2), 'client_ref' => str_repeat(' ', 2), 'confirm_date' => str_repeat(' ', 17), 'control' => str_repeat(' ', 1), 'cur_amount' => str_repeat(' ', 17), 'currency' => str_repeat(' ', 3), 'del_met_descr' => str_repeat(' ', 60), 'del_term_descr' => str_repeat(' ', 60), 'deliv_addr' => str_repeat(' ', 255), 'deliv_attention' => str_repeat(' ', 50), 'deliv_countr' => str_repeat(' ', 3), 'deliv_date' => str_repeat(' ', 17), 'deliv_method' => str_repeat(' ', 8), 'deliv_terms' => str_repeat(' ', 8), 'dim_1' => str_repeat(' ', 8), 'dim_2' => str_repeat(' ', 8), 'dim_3' => str_repeat(' ', 8), 'dim_4' => str_repeat(' ', 8), 'dim_5' => str_repeat(' ', 12), 'dim_6' => str_repeat(' ', 4), 'dim_7' => str_repeat(' ', 4), 'dim_value_1' => str_repeat(' ', 12), 'dim_value_2' => str_repeat(' ', 12), 'dim_value_3' => str_repeat(' ', 12), 'dim_value_4' => str_repeat(' ', 12), 'dim_value_5' => str_repeat(' ', 12), 'dim_value_6' => str_repeat(' ', 12), 'dim_value_7' => str_repeat(' ', 12), 'disc_percent' => str_repeat(' ', 17), 'exch_rate' => str_repeat(' ', 17), 'ext_ord_ref' => str_repeat(' ', 15), 'intrule_id' => str_repeat(' ', 6), 'line_no' => str_repeat(' ', 4), 'location' => str_repeat(' ', 4), 'long_info1' => str_repeat(' ', 120), 'long_info2' => str_repeat(' ', 120), 'lot' => str_repeat(' ', 10), 'main_apar_id' => str_repeat(' ', 8), 'mark_attention' => str_repeat(' ', 50), 'mark_ctry_cd' => str_repeat(' ', 3), 'markings' => str_repeat(' ', 120), 'obs_date' => str_repeat(' ', 17), 'order_date' => str_repeat(' ', 17), 'order_id' => str_repeat(' ', 9), 'order_type' => str_repeat(' ', 2), 'pay_method' => str_repeat(' ', 2), 'period' => str_repeat(' ', 8), 'place' => str_repeat(' ', 30), 'province' => str_repeat(' ', 40), 'rel_value' => str_repeat(' ', 12), 'responsible' => str_repeat(' ', 8), 'responsible2' => str_repeat(' ', 8), 'sequence_no' => str_repeat(' ', 8), 'sequence_ref' => str_repeat(' ', 8), 'serial_no' => str_repeat(' ', 20), 'short_info' => str_repeat(' ', 60), 'status' => str_repeat(' ', 1), 'tax_code' => str_repeat(' ', 2), 'tax_system' => str_repeat(' ', 2), 'template_id' => str_repeat(' ', 8), 'terms_id' => str_repeat(' ', 2), 'tekx1' => str_repeat(' ', 12), 'tekst2' => str_repeat(' ', 12), 'tekst3' => str_repeat(' ', 12), 'text4' => str_repeat(' ', 12), 'trans_type' => str_repeat(' ', 2), 'unit_code' => str_repeat(' ', 3), 'unit_descr' => str_repeat(' ', 50), 'value_1' => str_repeat(' ', 17), 'voucher_ref' => str_repeat(' ', 9), 'voucher_type' => str_repeat(' ', 2), 'warehouse' => str_repeat(' ', 4), 'zip_code' => str_repeat(' ', 15));
			return $row_template;
		}
	}