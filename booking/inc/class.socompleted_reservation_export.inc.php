<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.sopermission');
	phpgw::import_class('phpgwapi.datetime');

	class booking_socompleted_reservation_export extends booking_socommon
	{

		protected
			$completed_reservation_so,
			$completed_reservation_bo,
			$account_code_set_so,
			$customer_id,
			$sequential_number_generator_so,
			$config_data,
			$sopurchase_order,
			$event_so, $application_bo, $application_so,$allocation_bo,$booking_bo,$event_bo,$organization_bo;

		function __construct()
		{
			$this->event_so = CreateObject('booking.soevent');
			$this->application_bo = CreateObject('booking.boapplication');
			$this->application_so = CreateObject('booking.soapplication');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->booking_bo = CreateObject('booking.bobooking');
			$this->event_bo = CreateObject('booking.boevent');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->customer_id = CreateObject('booking.customer_identifier');
			$this->completed_reservation_so = CreateObject('booking.socompleted_reservation');
			$this->completed_reservation_bo = CreateObject('booking.bocompleted_reservation');
			$this->account_code_set_so = CreateObject('booking.soaccount_code_set');
			$this->sequential_number_generator_so = CreateObject('booking.sobilling_sequential_number_generator');
			$this->sopurchase_order = createObject('booking.sopurchase_order');

			parent::__construct('bb_completed_reservation_export', array(
				'id' => array('type' => 'int'),
				'season_id' => array('type' => 'int'),
				'building_id' => array('type' => 'int'),
				'from_' => array('type' => 'timestamp', 'required' => true),
				'to_' => array('type' => 'timestamp', 'required' => true),
				'total_cost' => array('type' => 'decimal'), //NOT NULL in database, but automatically computed in add method
				'total_items' => array('type' => 'int'), ////NOT NULL in database, but automatically computed in add method
				key(booking_socommon::$AUTO_CREATED_ON) => current(booking_socommon::$AUTO_CREATED_ON),
				key(booking_socommon::$AUTO_CREATED_BY) => current(booking_socommon::$AUTO_CREATED_BY),
				'created_by_name' => booking_socommon::$REL_CREATED_BY_NAME,
				'season_name' => array('type' => 'string', 'query' => true, 'join' => array(
						'table' => 'bb_season',
						'fkey' => 'season_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'building_name' => array('type' => 'string', 'query' => true, 'join' => array(
						'table' => 'bb_building',
						'fkey' => 'building_id',
						'key' => 'id',
						'column' => 'name'
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

			$this->config_data = CreateObject('phpgwapi.config', 'booking')->read();
		}

		protected function _get_search_to_date( &$entity )
		{
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$to_date = isset($entity['to_']) && $entity['to_'] ? $entity['to_'] : date($dateformat);

			$to_date = date('Y-m-d', phpgwapi_datetime::date_to_timestamp($to_date));

			if (strtotime($to_date) > strtotime('tomorrow'))
			{
				$to_date = date('Y-m-d');
			}

			$to_date .= ' 23:59:59';

			return $to_date;
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			$exportable_reservations = & $this->get_completed_reservations_for($entity);
			if (!$exportable_reservations)
			{
				$errors['nothing_to_export'] = lang('Nothing to export');
				return;
			}

			$invalid_customer_ids = array();
			foreach ($exportable_reservations as &$reservation)
			{
				if (!$this->get_customer_identifier_value_for($reservation) && $this->get_cost_value($reservation['cost']) > 0 /* Exclude free reservations from this check */)
				{
					$invalid_customer_ids[] = $reservation['id'];
				}
			}
			if($invalid_customer_ids)
			{
					$errors['invalid_customer_ids'] = lang('Unable to export: Missing a valid Customer ID on some rows') . ': ' .implode(', ', $invalid_customer_ids);
			}
		}

		function read_single( $id )
		{
			$entity = parent::read_single($id);
			$this->initialize_entity($entity);
			return $entity;
		}



		/**
		 * Reverse only not exported
		 * @param int $id
		 * @return bool on success
		 */
		function reverse_reservation( int $id )
		{
			$ret = false;
			$sql0 = "SELECT id FROM bb_completed_reservation WHERE exported = {$id} AND export_file_id IS NULL";
			$this->db->query($sql0, __LINE__, __FILE__);
			$ids = array();
			while($this->db->next_record())
			{
				$ids[] = $this->db->f('id');
			}

			if($ids)
			{
				$this->db->transaction_begin();

				$sql1 = "UPDATE bb_completed_reservation SET exported = NULL WHERE id IN(" . implode(',', $ids) . ')';
				$sql2 = "DELETE FROM bb_completed_reservation_export_configuration where export_id = {$id}";
				$sql3 = "DELETE FROM bb_completed_reservation_export WHERE id = {$id}";
				$this->db->query($sql1, __LINE__, __FILE__);
				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->query($sql3, __LINE__, __FILE__);
				$ret = $this->db->transaction_commit();
			}
			return $ret;
		}
		/**
		 * Normalizes data on entity.
		 */
		public function initialize_entity( &$entity )
		{
			if (isset($entity['__initialized__']) && $entity['__initialized__'] === true)
			{
				return $entity;
			}

			$entity['__initialized__'] = true;
			//re-index export configurations on their types
			if (!(array_key_exists('export_configurations', $entity) && is_array($entity['export_configurations'])))
			{
				return $entity;
			}

			$export_configs = array();
			foreach ($entity['export_configurations'] as $conf)
			{
				$export_configs[$conf['type']] = $conf;
			}
			$entity['export_configurations'] = $export_configs;

			return $entity;
		}

		public static function get_available_export_types()
		{
			return array('internal', 'external');
		}

		public function has_generated_file( &$export, $type )
		{
			$this->initialize_entity($export);

			if (!isset($export['export_configurations']) || !is_array($export['export_configurations']))
			{
				throw new InvalidArgumentException("Missing or invalid export_configurations");
			}

			if (!isset($export['export_configurations'][$type]) || !is_array($export['export_configurations'][$type]))
			{
				throw new InvalidArgumentException("Missing export configuration for type '{$type}'");
			}

			if (!array_key_exists('export_file_id', $export['export_configurations'][$type]))
			{
				throw new InvalidArgumentException("Missing export configuration file information");
			}

			if (empty($export['export_configurations'][$type]['export_file_id']))
			{
				return false;
			}

			return true;
		}

		public function get_export_file_data( $entity, $type )
		{
			$this->initialize_entity($entity);

			if (!isset($entity['export_configurations']) || !isset($entity['export_configurations'][$type]))
			{
				throw new InvalidArgumentException(sprintf("Missing export configuration of type '%s'", $type));
			}

			$export_conf = $entity['export_configurations'][$type];
			$account_codes = $this->account_code_set_so->read_single($export_conf['account_code_set_id']);

			if (!is_array($account_codes))
			{
				throw new LogicException(sprintf("Unable to locate accounts codes for export file data"));
			}

			$export_reservations = & $this->get_completed_reservations_for($entity['id']);

			$export_method = "export_{$type}";

			if (!method_exists($this, $export_method))
			{
				throw new LogicException(sprintf('Cannot generate export for type "%s"', $type));
			}

			return array($export_conf, $this->$export_method($export_reservations, $account_codes));
		}

		function add( $entry )
		{
			$export_reservations = & $this->get_completed_reservations_for($entry);

			if (!$export_reservations)
			{
				throw new LogicException('Nothing to export');
			}

			$entry['from_'] = $export_reservations[0]['to_'];
			$entry['to_'] = $export_reservations[count($export_reservations) - 1]['to_'];
			$entry['total_cost'] = $this->calculate_total_cost($export_reservations);
			$entry['total_items'] = count(array_filter($export_reservations, array($this,
				'not_free')));

			$this->db->transaction_begin();

			$receipt = parent::add($entry);
			$entry['id'] = $receipt['id'];
			$this->update_completed_reservations_exported_state($entry, $export_reservations);

			if (!($this->db->transaction_commit()))
			{
				throw new UnexpectedValueException('Transaction failed.');
			}

			return $receipt;
		}

		public function &get_completed_reservations_for( $entity )
		{
			$filters = array();

			if (is_array($entity))
			{
				$filters['where'] = array("%%table%%" . sprintf(".to_ <= '%s'", $this->_get_search_to_date($entity)));
				$filters['exported'] = null;

				if ($entity['season_id'])
				{
					$filters['season_id'] = $entity['season_id'];
				}

				if ($entity['building_id'])
				{
					$filters['building_id'] = $entity['building_id'];
				}
				if (!empty($entity['process']))
				{
					$filters['id'] = $entity['process'];
				}
			}
			else if ($entity)
			{
				$filters['exported'] = $entity;
			}
			else
			{
				throw new InvalidArgumentException('Invalid entity parameter');
			}

			if (!isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && // admin users should have access to all buildings
				!$this->completed_reservation_bo->has_role(booking_sopermission::ROLE_MANAGER))
			{ // users with the booking role admin should have access to all buildings
				if (!isset($filters['building_id']))
				{
					$filters['building_id'] = $this->completed_reservation_bo->accessable_buildings($GLOBALS['phpgw_info']['user']['id']);
				}
			}

			$reservations = $this->completed_reservation_so->read(array('filters' => $filters,
				'results' => 'all', 'sort' => 'customer_type,customer_identifier_type,customer_organization_number,customer_number,customer_ssn,to_',
				'dir' => 'asc'));

			if (count($reservations['results']) > 0)
			{
				return $reservations['results'];
			}

			return array();
		}

		protected function update_completed_reservations_exported_state( $entity, &$reservations )
		{
			return $this->completed_reservation_so->update_exported_state_of($reservations, $entity['id']);
		}

		protected function get_customer_identifier_value_for( &$reservation )
		{
			return $this->customer_id->get_current_identifier_value($reservation);
		}

		public function not_free( $reservation )
		{
			return $this->get_cost_value($reservation['cost']) > 0;
		}

		public function calculate_total_cost( &$reservations )
		{
			return array_reduce($reservations, array($this, "_rcost"), 0);
		}

		public function _rcost( $total_cost, $entity )
		{
			return $total_cost + $this->get_cost_value($entity['cost']);
		}

		public function select_external( $reservation )
		{
			if ($this->config_data['output_files'] == 'single')
			{
				return true;
			}
			else
			{
				return $reservation['customer_type'] == booking_socompleted_reservation::CUSTOMER_TYPE_EXTERNAL;
			}
		}

		public function select_internal( $reservation )
		{
			if ($this->config_data['output_files'] == 'single')
			{
				return false;
			}
			else
			{
				return $reservation['customer_type'] == booking_socompleted_reservation::CUSTOMER_TYPE_INTERNAL;
			}
		}

		/**
		 * @return array with three elements where index 0: total_rows, index 1: total_cost, index 2: formatted data
		 */
		public function export_external( array &$reservations, array $account_codes )
		{

			if ($this->config_data['external_format'] == 'CSV')
			{
				$export_format = 'csv';
			}
			elseif ($this->config_data['external_format'] == 'AGRESSO')
			{
				$export_format = 'agresso';
			}
			elseif ($this->config_data['external_format'] == 'FACTUM')
			{
				$export_format = 'factum';
			}
			elseif ($this->config_data['external_format'] == 'KOMMFAKT')
			{
				$export_format = 'kommfakt';
			}
			elseif ($this->config_data['external_format'] == 'VISMA')
			{
				$export_format = 'visma';
			}

			if (is_array($reservations))
			{
				if (count($external_reservations = array_filter($reservations, array($this, 'select_external'))) > 0)
				{

					if (!($number_generator = $this->sequential_number_generator_so->get_generator_instance('external')))
					{
						throw new UnexpectedValueException("Unable to find sequential number generator for external export");
					}

					if ($this->config_data['external_format'] == 'CSV')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($external_reservations, array($this, 'not_free'))), $this->calculate_total_cost($external_reservations), $this->format_csv($external_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['external_format'] == 'AGRESSO')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($external_reservations, array($this, 'not_free'))), $this->calculate_total_cost($external_reservations), $this->format_agresso($external_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['external_format'] == 'FACTUM')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($external_reservations, array($this, 'not_free'))), $this->calculate_total_cost($external_reservations), $this->format_factum($external_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['external_format'] == 'KOMMFAKT')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($external_reservations, array($this, 'not_free'))), $this->calculate_total_cost($external_reservations), $this->format_kommfakt($external_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['external_format'] == 'VISMA')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($external_reservations, array($this, 'not_free'))), $this->calculate_total_cost($external_reservations), $this->format_visma($external_reservations, $account_codes, $number_generator)
						);
					}
				}
			}
			return $this->build_export_result($export_format, 0, 0.0);
		}

		/**
		 * @return array with three elements where index 0: total_rows, index 1: total_cost, index 2: formatted data
		 */
		public function export_internal( array &$reservations, array $account_codes )
		{

			if ($this->config_data['internal_format'] == 'CSV')
			{
				$export_format = 'csv';
			}
			elseif ($this->config_data['internal_format'] == 'AGRESSO')
			{
				$export_format = 'agresso';
			}
			elseif ($this->config_data['internal_format'] == 'FACTUM')
			{
				$export_format = 'factum';
			}
			elseif ($this->config_data['internal_format'] == 'KOMMFAKT')
			{
				$export_format = 'kommfakt';
			}
			elseif ($this->config_data['internal_format'] == 'VISMA')
			{
				$export_format = 'visma';
			}

			if (is_array($reservations))
			{
				if (count($internal_reservations = array_filter($reservations, array($this, 'select_internal'))) > 0)
				{

					if (!($number_generator = $this->sequential_number_generator_so->get_generator_instance('internal')))
					{
						throw new UnexpectedValueException("Unable to find sequential number generator for internal export");
					}
					if ($this->config_data['internal_format'] == 'CSV')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($internal_reservations, array($this, 'not_free'))), $this->calculate_total_cost($internal_reservations), $this->format_csv($internal_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['internal_format'] == 'AGRESSO')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($internal_reservations, array($this, 'not_free'))), $this->calculate_total_cost($internal_reservations), $this->format_agresso($internal_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['internal_format'] == 'FACTUM')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($internal_reservations, array($this, 'not_free'))), $this->calculate_total_cost($internal_reservations), $this->format_factum($internal_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['internal_format'] == 'KOMMFAKT')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($internal_reservations, array($this, 'not_free'))), $this->calculate_total_cost($internal_reservations), $this->format_kommfakt($internal_reservations, $account_codes, $number_generator)
						);
					}
					elseif ($this->config_data['internal_format'] == 'VISMA')
					{
						return $this->build_export_result(
								$export_format, count(array_filter($internal_reservations, array($this, 'not_free'))), $this->calculate_total_cost($internal_reservations), $this->format_visma($internal_reservations, $account_codes, $number_generator)
						);
					}
				}
			}
			return $this->build_export_result($export_format, 0, 0.0);
		}

		protected function build_export_result( $export_format, $total_items, $total_cost, $data = null )
		{
			return array('total_items' => $total_items, 'total_cost' => $total_cost, 'export_format' => $export_format,
				'export' => $data);
		}

		public function format_cost( $cost )
		{
			$cost = $this->get_cost_value($cost);
			return str_pad(round($cost, 2) * 100, 17, 0, STR_PAD_LEFT);
		}

		public function get_cost_value( $cost )
		{
			if (is_null($cost))
			{
				$cost = floatval(0); //floatval and doubleval, the same thing in php
			}

			if (gettype($cost) != 'double')
			{
				$cost = floatval($cost); //floatval and doubleval, the same thing in php
			}

			return $cost;
		}

		public function create_export_item_info( &$entity, $generated_order_id )
		{
			if (!is_array($entity))
			{
				throw new InvalidArgumentException("Invalid entity");
			}

			if (!isset($entity['id']))
			{
				throw new InvalidArgumentException("Invalid entity - missing id");
			}

			if (!isset($entity['reservation_id']))
			{
				throw new InvalidArgumentException("Invalid entity - missing reservation_id");
			}

			if (!isset($entity['reservation_type']))
			{
				throw new InvalidArgumentException("Invalid entity - missing reservation_type");
			}

			if (!isset($generated_order_id) || empty($generated_order_id))
			{
				throw new InvalidArgumentException("Invalid order_id");
			}

			return array('id' => $entity['id'], 'reservation_id' => $entity['reservation_id'],
				'reservation_type' => $entity['reservation_type'], 'invoice_file_order_id' => $generated_order_id);
		}

		public function combine_export_data( array &$export_results )
		{
			$combined_data = array();
			$export_format = null;
			$combine_method = null;

			foreach ($export_results as &$export_result)
			{
				if (!isset($export_result['export_format']) || !is_string($export_result['export_format']))
				{
					throw new InvalidArgumentException('export_format must be specified');
				}

				if ($export_format == null)
				{
					$export_format = $export_result['export_format'];
					$combine_method = array($this, sprintf('combine_%s_export_data', $export_format));
					$format_out_method = sprintf('format_%s_out', $export_format);
				}
				elseif ($export_format != $export_result['export_format'])
				{
					throw new InvalidArgumentException('Different export formats cannot be combined into a single result');
				}

				if (!array_key_exists('export', $export_result))
				{
					throw new InvalidArgumentException('Missing export key');
				}

				if (is_null($export_result['export']))
				{
					continue;
				}

				if (!is_array($export_result['export']) || !isset($export_result['export']['data']))
				{
					throw new InvalidArgumentException('Missing export data');
				}

				call_user_func_array($combine_method, array(&$combined_data, &$export_result['export']));
			}


			if(!$combined_data)
			{
				return '';
			}

			switch ($export_format)
			{
				case 'factum':
					return $this->format_factum_out($combined_data);
				default:
					return join('', $combined_data);
			}
		}

		protected function format_factum_out( $combined_data )
		{
			/*
			 * Create xml file
			 */
			$xmltool = CreateObject('phpgwapi.xmltool');
			$xmltool->set_encoding('ISO-8859-1');

			$xml	 = $xmltool->import_var('BkPffFakturagrunnlags', $combined_data, true, true);

			return $xml;

		}
		protected function combine_factum_export_data( array &$combined_data, $export )
		{
			if(isset($combined_data['BkPffFakturagrunnlag']) && $export['data']['BkPffFakturagrunnlag'] && is_array($export['data']['BkPffFakturagrunnlag']))
			{
				foreach ($export['data']['BkPffFakturagrunnlag'] as $BkPffFakturagrunnlag)
				{
					$combined_data['BkPffFakturagrunnlag'][] = $BkPffFakturagrunnlag;
				}
			}
			else
			{
				$combined_data = array_merge($combined_data, $export['data']);
			}
		}

		protected function &combine_csv_export_data( array &$combined_data, $export )
		{
			if (count($combined_data) == 0)
			{
				$combined_data[] = $export['data']; //Insert with headers and all
			}
			else
			{
				$combined_data[] = "csv_break";
				$combined_data[] = substr($export['data'], strpos($export['data'], "\n") + 1); //Remove first line (i.e don't to repeat headers in file)
			}
		}

		protected function combine_visma_export_data( array &$combined_data, $export )
		{
			if (count($combined_data) == 0)
			{
				$combined_data[] = $export['data'];
			}
			else
			{
				$combined_data[] = "\n";
				$combined_data[] = $export['data'];
			}
		}

		/**
		 * Implement me
		 * @param array $reservations
		 * @param array $account_codes
		 * @param type $sequential_number_generator
		 */
		public function format_visma( array &$reservations, array $account_codes, $sequential_number_generator )
		{
//			Format for overføring av fakturagrunnlag til Visma Enterprise Fakturering via fil
//			=================================================================================
//
//			Fom. Fakturering 2014.1.04 er det lagt til rette for et utvidet format på FL-linjene som blant
//			annet inneholder kontering og profil-informasjon. Dokumentasjon av dette ligger nederst i
//			denne beskrivelsen. Innlesningsprogrammet skiller mellom de to formatene ved å sjekke
//			på verdien av FORMAT-feltet på ST-linjen.
//
//
//			POSTTYPER
//			=========
//
//			ST = Startpost
//			FL = Fakturalinje
//			LT = Linjetekst  (er mulig å knytte fritekst til fakturalinjen)
//			SL = Sluttpost
//
//			M/K
//			M = Må angis
//			K = Kan angis
//
//
//			Type Felt    Lengde Posisjon Beskrivelse             M/K Merknader
//			---- ------- ------ -------- ----------------------- --- ----------
//			ST   POSTTYPE   2   001-002  Posttype                 M  Verdi 'ST'
//			ST   REFERANSE 60   003-062  Referanse                K  ST01
//			ST   FORMAT     1   063-063  Utvidet format           K  ST02
//
//			FL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'FL'
//			FL   KUNDENR   11   003-013  Kundenummer              M
//			FL   NAVN      30   014-043  Kundens navn             K
//			FL   ADRESSE1  30   044-073  Adresselinje 1           K
//			FL   ADRESSE2  30   074-103  Adresselinje 2           K
//			FL   POSTNR     4   104-107  Postnummer               K
//			FL   BETFORM    2   108-109  Betalingstype (BG,PG)    M  MRK01
//			FL   OPPDRGNR   3   110-112  Oppdragsgivernummer      M  MRK02
//			FL   VARENR     4   113-116  Varenummer               M  MRK02
//			FL   LØPENR     2   117-118  Løpenummer               M  MRK03
//			FL   PRIS       9   119-127  Varens pris              M  MRK04
//			FL   GRUNNLAG   9   128-136  Antall av varen          M  MRK05
//			FL   BELØP     11   137-147  Utregnet beløp           M  MRK04
//			FL   SAKSNR    16   148-163  Saksnr                   K
//
//			LT   POSTTYPE   2   001-002  Posttype                 M  Verdi 'LT'
//			LT   KUNDENR   11   003-013  Kundenummer              M
//			LT   OPPDRGNR   3   014-016  Oppdragsgivernummer      M
//			LT   VARENR     4   017-020  Varenummer               M
//			LT   LØPENR     2   021-022  Løpenummer               M
//			LT   LINJENR    2   023-024  Linjenummer              M  MRK06
//			LT   TEKST     50   025-074  Fritekstlinje            K
//
//			SL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'SL'
//			SL   ANTPOST    8   003-010  Antall poster            M  Inkl. Start/Sluttpost
//
//
//			UTVIDET FORMAT PÅ FL-LINJENE
//			============================
//
//			FL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'FL'
//			FL   KUNDENR   11   003-013  Kundenummer              M
//			FL   NAVN      40   014-053  Kundens navn             K
//			FL   ADRESSE1  40   054-093  Adresselinje 1           K
//			FL   ADRESSE2  40   094-133  Adresselinje 2           K
//			FL   POSTNR     4   134-137  Postnummer               K
//			FL   BETFORM    2   138-139  Betalingstype (BG,PG)    M  MRK01
//			FL   OPPDRGNR   3   140-142  Oppdragsgivernummer      M  MRK02
//			FL   VARENR     4   143-146  Varenummer               M  MRK02
//			FL   LØPENR     2   147-148  Løpenummer               M  MRK03
//			FL   PRIS       9   149-157  Varens pris              M  MRK04
//			FL   GRUNNLAG   9   158-166  Antall av varen          M  MRK05
//			FL   BELØP     11   167-177  Utregnet beløp           M  MRK04
//			FL   SAKSNR    16   178-193  Saksnr                   K
//			FL   INTFAKT    1   194-194  Internfaktura            K  MRK07
//			FL   KB01      12   195-206  1. konteringsverdi       K
//			FL   KB02      12   207-218  2. konteringsverdi       K
//			FL   KB03      12   219-230  3. konteringsverdi       K
//			FL   KB04      12   231-242  4. konteringsverdi       K
//			FL   KB05      12   243-254  5. konteringsverdi       K
//			FL   KB06      12   255-266  6. konteringsverdi       K
//			FL   KB07      12   267-278  7. konteringsverdi       K
//			FL   KB08      12   279-290  8. konteringsverdi       K
//			FL   KB09      12   291-302  9. konteringsverdi       K
//			FL   KB10      12   303-314  10. konteringsverdi      K
//			FL   MVAKODE    3   315-317  Mva-kode                 K
//			FL   PROFIL    20   318-337  Profil                   K
//			FL   DERESREF  40   338-377  Kontaktinformasjon       K
//			FL   ORDREREF  20   378-397  Ordrereferanse           K
//
//
//			MERKNADER
//			=========
//
//			ST01  -  Teksten i dette feltet kommer ut på kvitteringslisten og kan
//					 brukes som referanse på overføringen.
//					 (f.eks. hvilket system/dato etc...)
//			ST02  -  Ved bruk av nytt format, må verdien 'U' legges i feltet.
//			MRK01 -  BG = Bankgiro, PG = Postgiro.
//			MRK02 -  Må være opprettet i Visma Enterprise Fakturering.
//			MRK03 -  Fortløpende nummerering hvis flere forekomster av samme vare på kunden.
//			MRK04 -  De 2 nest siste posisjoner er desimaler, siste posisjon angir
//					 fortegn. (f.eks. 10000- er lik 100.00-)
//			MRK05 -  De 2 siste posisjoner er desimaler.
//			MRK06 -  Fortløpende nummerering av fritekst. Start på 1 for hvert
//					 fakturagrunnlag.
//			MRK07 -  Internfaktura merkes ved verdi 1 i dette feltet.


			$export_info = array();
			$output = array();

			$log = array();

			$date = str_pad(date('Ymd'), 17, ' ', STR_PAD_LEFT);


			static $stored_header = array();
			$line_no = 0;
			$header_count = 0;
			$log_order_id = '';
			$log_customer_name = '';
			$log_customer_nr = '';
			$log_buidling = '';
			$contact_name = '';

			$internal = false;

			static $linjenr = 1;
			static $lopenr = array();
			static $ant_post = 0;

			$ant_post ++;
			//Startpost ST
			$startpost = $this->get_visma_ST_row_template();
			$startpost['posttype'] = 'ST';
			$startpost['referanse'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['invoice_instruction']), 0, 60), 60, ' ');

//			if (isset($this->config_data['dim_value_5']))
//			{
//				$_vaar_ref = str_pad(substr($account_codes['dim_value_5'], 0, 12), 12, ' ');
//			}

			foreach ($reservations as &$reservation)
			{

				switch ($reservation['reservation_type'])
				{
					case 'allocation':
						$test = $this->allocation_bo->read_single($reservation['reservation_id']);
						break;
					case 'booking':
						$test = $this->booking_bo->read_single($reservation['reservation_id']);
						break;
					case 'event':
						$test = $this->event_bo->read_single($reservation['reservation_id']);
						break;
					default:
						break;
				}

				if(empty($test['id']))
				{
					continue; //Reservation has been deleted
				}

				if(empty($test['active']))
				{
					continue; //Reservation has been de-activated
				}

				if ($this->get_cost_value($reservation['cost']) <= 0)
				{
					continue; //Don't export costless rows
				}

				$output[] = implode('', str_replace(array("\n", "\r"), '', $startpost));

				/**
				 * Get contact person
				 */
				switch ($reservation['reservation_type'])
				{
					case 'allocation':
						if (!empty($reservation['organization_id']))
						{
							$org = $this->organization_bo->read_single($reservation['organization_id']);
							if(!empty($org['contacts'][0]['name']))
							{
								$contact_name = iconv("utf-8", "ISO-8859-1//TRANSLIT", $org['contacts'][0]['name']);
							}
						}
						break;
					case 'booking':
						if(!empty($test['group_id']))
						{
							$group = CreateObject('booking.sogroup')->read_single($test['group_id']);
							if(!empty($group['contacts'][0]['name']))
							{
								$contact_name = iconv("utf-8", "ISO-8859-1//TRANSLIT", $group['contacts'][0]['name']);
							}
						}
						break;
					case 'event':
						$contact_name = iconv("utf-8", "ISO-8859-1//TRANSLIT", $test['contact_name']);
						break;
					default:
						break;
				}


				$application_id = null;

				if ($reservation['reservation_type'] == 'event')
				{
					$data = $this->event_bo->read_single($reservation['reservation_id']);
					$application_id = $data['application_id'];
				}
				else if ($reservation['reservation_type'] == 'booking')
				{
					$data = $this->booking_bo->read_single($reservation['reservation_id']);
					$application_id = $data['application_id'];
				}
				else
				{
					$data = $this->allocation_bo->read_single($reservation['reservation_id']);
					$application_id = $data['application_id'];
				}

				if($application_id)
				{
					$application = $this->application_bo->read_single($application_id);
					$street = $application['responsible_street'];
					$zip_code = $application['responsible_zip_code'];
					$city = $application['responsible_city'];
				}
				else
				{
					$street = '';
					$zip_code = '';
					$city = '';
				}


				$customer_number = '';
				if (!empty($reservation['organization_id']))
				{
					$org = $this->organization_bo->read_single($reservation['organization_id']);
					$reservation['organization_name'] = $org['name'];
					$customer_number =  $org['customer_number'];
					if(empty($street))
					{
						$street = $org['street'];
						$zip_code = $org['zip_code'];
						$city = $org['city'];
					}
				}
				else
				{
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if (!empty($data['id']))
					{
						$reservation['organization_name'] = $data['name'];
						if(empty($street))
						{
							$street = $data['street'];
							$zip_code = $data['zip_code'];
							$city = $data['city'];
						}
					}
					else
					{
						if ($reservation['reservation_type'] == 'event')
						{
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

				$kundenr = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 11), 11, '0', STR_PAD_LEFT);
				$stored_header['kundenr'] = $kundenr;

				if(empty($lopenr[$kundenr]))
				{
					$lopenr[$kundenr] = 1;
				}
				else
				{
					$lopenr[$kundenr]++;
				}

				if (strlen($this->get_customer_identifier_value_for($reservation)) > 9)
				{
					$name = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $contact_name), 0, 40), 40, ' '); //40 chars long
				}
				else
				{
					$name = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['organization_name']), 0, 40), 40, ' '); //40 chars long
				}


				$purchase_order = $this->sopurchase_order->get_purchase_order(0, $reservation['reservation_type'], $reservation['reservation_id']);
				/**
				 * For vipps kan det være flere krav, for etterfakturering vil det være ett
				 */
				$payments = $this->sopurchase_order->get_order_payments($purchase_order['order_id']);
				if(isset($payments[0]))
				{
					$payment = $payments[0];

					/**
					 * Already paid for, or cancelled
					 */
					if(in_array($payment['status'], array( 'completed', 'voided', 'refunded')))
					{
						continue;
					}

					//FIXME: move method from soapplication
					// status: new, pending, completed, voided, partially_refunded, refunded
					$this->application_so->update_payment_status($payment['remote_id'], 'completed', 'RESERVE');

					/**
					 * sjekk status / opdater status
					 */
				}

				/**
				 * precheck
				 */
				
				$bypass = false;
				$found_amount = 0;
				if($purchase_order && !empty($purchase_order['lines']))
				{

					foreach ($purchase_order['lines'] as $order_line)
					{
						if (!empty($order_line['amount']))
						{
							$found_amount++;
						}
					}
					unset($order_line);

					if(!$found_amount && $reservation['cost'])
					{
						$bypass = true;
					}
				}

				if($purchase_order && !$bypass && !empty($purchase_order['lines']))
				{

					foreach ($purchase_order['lines'] as $order_line)
					{
						if(empty($order_line['amount']))
						{
							continue;
						}

						/**
						 * artikkelkoden kommer fra valgt konterings-oppsett pr fakturering
						 * Samme ressurs kan ha flere artikkelkoder
						 */
						if($order_line['parent_mapping_id'] == 0)
						{
							$article_name = $order_line['name']  . ' - ' . $reservation['description'];
					//		$_article_code = $account_codes['article'];
							$_article_code = $order_line['article_code'];
						}
						else
						{
							$article_name = $order_line['name'];
							$_article_code = $order_line['article_code'];
						}


						if($order_line['tax_percent'])
						{
							$unit_tax = (float)$order_line['unit_price'] * $order_line['tax_percent'] / 100;
						}
						else
						{
							$unit_tax = 0;
						}

						$pris_inkl_mva = (float)$order_line['unit_price'] + $unit_tax;

						$ant_post ++;

						//Fakturalinje FL
						$fakturalinje = $this->get_visma_FL_row_template();
						$fakturalinje['posttype'] = 'FL';
						$fakturalinje['kundenr'] = $kundenr;
						$fakturalinje['navn'] = $name;
						$fakturalinje['deresref'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $contact_name), 0, 40), 40, ' '); //40 chars long
						$fakturalinje['adresse1'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $street), 0, 40), 40, ' '); //40 chars long
						$fakturalinje['adresse2'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $city), 0, 40), 40, ' '); //40 chars long
						$fakturalinje['postnr'] = str_pad(substr($zip_code, 0, 4), 4, ' '); //4 chars long

						$fakturalinje['betform'] = 'BG';

						//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
						if (isset($this->config_data['dim_value_1']))
						{
							$fakturalinje['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['unit_number']), 3, '0', STR_PAD_LEFT);
						}

						$fakturalinje['varenr']		 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $_article_code), 0 ,4), 4, '0', STR_PAD_LEFT);

						$fakturalinje['lopenr']		 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr[$kundenr]), 2, '0', STR_PAD_LEFT);
						$fakturalinje['pris']		 = str_pad($pris_inkl_mva * 100, 8, '0', STR_PAD_LEFT) . ' ';
						$fakturalinje['grunnlag']	 = str_pad($order_line['quantity'], 9, '0', STR_PAD_LEFT);//'000000001'; // antall
						$fakturalinje['belop']		 = str_pad(($order_line['amount'] + $order_line['tax']) * 100, 10, '0', STR_PAD_LEFT) . ' ';
						$fakturalinje['mvakode']	 = str_pad($order_line['tax_code'], 3, ' ', STR_PAD_LEFT);

						#				$fakturalinje['saksnr'] = ;

						$output[] = implode('', str_replace(array("\n", "\r"), '', $fakturalinje));

						$ant_post ++;

						//Linjetekst LT
						$linjetekst = $this->get_visma_LT_row_template();
						$linjetekst['posttype'] = 'LT';
						$linjetekst['kundenr'] = $kundenr;

						//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
						if (isset($this->config_data['dim_value_1']))
						{
							$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['unit_number']), 3, '0', STR_PAD_LEFT);
						}

						$linjetekst['varenr']	 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $_article_code), 0 ,4), 4, '0', STR_PAD_LEFT);
						$linjetekst['lopenr']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr[$kundenr]), 2, '0', STR_PAD_LEFT);
						$linjetekst['linjenr']	 = str_pad($linjenr, 2, '0', STR_PAD_LEFT);
						$linjetekst['tekst']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $article_name), 50, ' ');

						$output[] = implode('', str_replace(array("\n", "\r"), '', $linjetekst));


					}

				}
				else
				{
					$ant_post ++;

					//Fakturalinje FL
					$fakturalinje = $this->get_visma_FL_row_template();
					$fakturalinje['posttype'] = 'FL';
					$fakturalinje['kundenr'] = $kundenr;
					$fakturalinje['navn'] = $name;
					$fakturalinje['deresref'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $contact_name), 0, 40), 40, ' '); //40 chars long

					$fakturalinje['adresse1']	 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $street), 0, 40), 40, ' '); //40 chars long
					$fakturalinje['adresse2']	 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $city), 0, 40), 40, ' '); //40 chars long
					$fakturalinje['postnr']		 = str_pad(substr($zip_code, 0, 4), 4, ' '); //4 chars long

					$fakturalinje['betform'] = 'BG';

					//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
					if (isset($this->config_data['dim_value_1']))
					{
						$fakturalinje['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['unit_number']), 3, '0', STR_PAD_LEFT);
					}

					$fakturalinje['varenr']		 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['article']), 0 ,4), 4, '0', STR_PAD_LEFT);

					$fakturalinje['lopenr']		 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr[$kundenr]), 2, '0', STR_PAD_LEFT);
					$fakturalinje['pris']		 = str_pad($reservation['cost'] * 100, 8, '0', STR_PAD_LEFT) . ' ';
					$fakturalinje['grunnlag']	 = '000000001';
					$fakturalinje['belop']		 = str_pad($reservation['cost'] * 100, 10, '0', STR_PAD_LEFT) . ' ';
					#				$fakturalinje['saksnr'] = ;

					$output[] = implode('', str_replace(array("\n", "\r"), '', $fakturalinje));

					$ant_post ++;

					//Linjetekst LT
					$linjetekst = $this->get_visma_LT_row_template();
					$linjetekst['posttype'] = 'LT';
					$linjetekst['kundenr'] = $kundenr;

					//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
					if (isset($this->config_data['dim_value_1']))
					{
	//					$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['object_number']), 3, '0', STR_PAD_LEFT);
						$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['unit_number']), 3, '0', STR_PAD_LEFT);
					}

					$linjetekst['varenr']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['article']), 4, '0', STR_PAD_LEFT);
					$linjetekst['lopenr']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr[$kundenr]), 2, '0', STR_PAD_LEFT);
					$linjetekst['linjenr']	 = str_pad($linjenr, 2, '0', STR_PAD_LEFT);
					$linjetekst['tekst']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['article_description']), 50, ' ');

					$output[] = implode('', str_replace(array("\n", "\r"), '', $linjetekst));

					//Linjetekst LT
					$linjetekst = $this->get_visma_LT_row_template();
					$linjetekst['posttype'] = 'LT';
					$linjetekst['kundenr'] = $kundenr;

					//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
					if (isset($this->config_data['dim_value_1']))
					{
	//					$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['object_number']), 3, '0', STR_PAD_LEFT);
						$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['unit_number']), 3, '0', STR_PAD_LEFT);
					}

					$linjetekst['varenr']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['article']), 4, '0', STR_PAD_LEFT);
					$linjetekst['lopenr']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr[$kundenr]), 2, '0', STR_PAD_LEFT);
					$linjetekst['linjenr']	 = str_pad($linjenr +1, 2, '0', STR_PAD_LEFT);
					$linjetekst['tekst']	 = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['description']), 50, ' ');

					$output[] = implode('', str_replace(array("\n", "\r"), '', $linjetekst));


				}

				$log_order_id = $order_id;

				if (!empty($reservation['organization_id']))
				{
					$org = $this->organization_bo->read_single($reservation['organization_id']);
					$log_customer_name = $org['name'];
				}
				else
				{
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if (!empty($data['id']))
					{
						$log_customer_name = $data['name'];
					}
					else
					{
						if ($reservation['reservation_type'] == 'event')
						{
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
				$log_varelinjer_med_dato = $reservation['article_description'] . ' - ' . $reservation['description'];

				$line_field = array();

				$line_field[] = "\"{$reservation['reservation_id']}\"";
				$line_field[] = "\"{$reservation['reservation_type']}\"";
				$line_field[] = "\"{$log_order_id}\"";
				$line_field[] = "\"{$log_customer_name}\"";
				$line_field[] = "\"{$log_customer_nr}\"";
				$line_field[] = "\"{$log_varelinjer_med_dato}\"";
				$line_field[] = "\"{$log_buidling}\"";
				$line_field[] = "\"{$log_cost}\"";

				$log[] = implode(';',  $line_field);

		//		$log[] = $reservation['id'] . ';' . $reservation['reservation_type'] . ';' . $log_order_id . ';' . $log_customer_name . ' - ' . $log_customer_nr . ';' . $log_varelinjer_med_dato . ';' . $log_buidling . ';' . $log_cost;


			}

			$ant_post ++;
			//Sluttpost SL
			$sluttpost = $this->get_visma_SL_row_template();
			$sluttpost['posttype'] = 'SL';
			$sluttpost['antpost'] = str_pad($ant_post, 8, '0', STR_PAD_LEFT);
			$output[] = implode('', str_replace(array("\n", "\r"), '', $sluttpost));

			if (count($export_info) == 0)
			{
				return null;
			}
			if ($this->config_data['external_format_linebreak'] == 'Windows')
			{
				$file_format_linebreak = "\r\n";
			}
			else
			{
				$file_format_linebreak = "\n";
			}

			return array('data' => implode($file_format_linebreak, $output), 'data_log' => implode(PHP_EOL, $log),
				'info' => $export_info, 'header_count' => $header_count);

		}


		public function format_csv( array &$reservations, array $account_codes, $sequential_number_generator )
		{
			$export_info = array();
			$output = array();

			$columns[] = 'amount';
			$columns[] = 'art_descr';
			$columns[] = 'article';
			if (isset($this->config_data['dim_1']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_1']);
			}
			if (isset($this->config_data['dim_2']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_2']);
			}
			if (isset($this->config_data['dim_3']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_3']);
			}
			if (isset($this->config_data['dim_4']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_4']);
			}
			if (isset($this->config_data['dim_5']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_5']);
			}
			if (isset($this->config_data['dim_value_1']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_value_1']);
			}
			if (isset($this->config_data['dim_value_2']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_value_2']);
			}
			if (isset($this->config_data['dim_value_3']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_value_3']);
			}
			if (isset($this->config_data['dim_value_4']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_value_4']);
			}
			if (isset($this->config_data['dim_value_5']))
			{
				$columns[] = iconv("utf-8", "ISO-8859-1//TRANSLIT", $this->config_data['dim_value_5']);
			}
			$columns[] = 'ext_ord_ref';
			$columns[] = 'invoice_instruction';
			$columns[] = 'order_id';
			$columns[] = 'period';
			$columns[] = 'short_info';

			$output[] = $this->format_to_csv_line($columns);
			foreach ($reservations as $reservation)
			{
				if ($this->get_cost_value($reservation['cost']) <= 0)
				{
					continue; //Don't export costless rows
				}
				$order_id = $sequential_number_generator->increment()->get_current();
				$export_info[] = $this->create_export_item_info($reservation, $order_id);

				foreach ($reservation as $key => &$value)
				{
					if(!is_array($value))
					{
						$value = iconv("utf-8", "ISO-8859-1//TRANSLIT", $value);
					}
				}

				$item = array();
				$item['amount'] = $this->format_cost($reservation['cost']); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
				$item['art_descr'] = str_pad(substr($reservation['article_description'], 0, 35), 35, ' '); //35 chars long
				$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');
				//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
				if (isset($this->config_data['dim_1']))
				{
					$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' ');
				}

				//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
				if (isset($this->config_data['dim_2']))
				{
					$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
				}

				//Objektnr. vil være knyttet til hvert hus (FDVU)
				if (isset($this->config_data['dim_3']))
				{
					$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
				}

				if (isset($this->config_data['dim_4']))
				{
					$item['dim_4'] = str_pad(substr($account_codes['dim_4'], 0, 8), 8, ' ');
				}

				//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt
				if (isset($this->config_data['dim_5']))
				{
					$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
				}

				if (isset($this->config_data['dim_value_1']))
				{
					$item['dim_value_1'] = str_pad(strtoupper(substr($account_codes['unit_number'], 0, 12)), 12, ' ');
				}
				if (isset($this->config_data['dim_value_2']))
				{
					$item['dim_value_2'] = str_pad(strtoupper(substr($account_codes['dim_value_2'], 0, 12)), 12, ' ');
				}
				if (isset($this->config_data['dim_value_3']))
				{
					$item['dim_value_3'] = str_pad(strtoupper(substr($account_codes['dim_value_3'], 0, 12)), 12, ' ');
				}

				if (isset($this->config_data['dim_value_4']))
				{
					$item['dim_value_4'] = str_pad(substr($account_codes['dim_value_4'], 0, 12), 12, ' ');
				}

				if (isset($this->config_data['dim_value_5']))
				{
					$item['dim_value_5'] = str_pad(substr($account_codes['dim_value_5'], 0, 12), 12, ' ');
				}
				$item['ext_ord_ref'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 15), 15, ' ');
				$item['long_info1'] = str_pad(substr($account_codes['invoice_instruction'], 0, 120), 120, ' ');

				$item['order_id'] = str_pad($order_id, 9, 0, STR_PAD_LEFT);

				$item['period'] = str_pad(substr('00' . date('Ym'), 0, 8), 8, '0', STR_PAD_LEFT);
				$item['short_info'] = str_pad(substr($reservation['description'], 0, 60), 60, ' ');

				$output[] = $this->format_to_csv_line(array_values($item));
			}

			if (count($export_info) == 0)
			{
				return null;
			}

			return array('data' => join('', $output), 'info' => $export_info);
		}

		/**
		 * @param array  $fields Ordered array with the data
		 * @param array  $conf   (optional) The configuration of the dest CSV
		 *
		 * @return String Fields in csv format
		 */
		function format_to_csv_line( &$fields, $conf = array() )
		{
			$conf = array_merge(array('sep' => ',', 'quote' => '"', 'crlf' => "\n"), $conf);

			$field_count = count($fields);

			$write = '';
			$quote = $conf['quote'];
			for ($i = 0; $i < $field_count; ++$i)
			{
				// Write a single field
				$quote_field = false;
				// Only quote this field in the following cases:
				if (is_numeric($fields[$i]))
				{
					// Numeric fields should not be quoted
				}
				elseif (isset($conf['sep']) && (strpos($fields[$i], $conf['sep']) !== false))
				{
					// Separator is present in field
					$quote_field = true;
				}
				elseif (strpos($fields[$i], $quote) !== false)
				{
					// Quote character is present in field
					$quote_field = true;
				}
				elseif
				(
					strpos($fields[$i], "\n") !== false || strpos($fields[$i], "\r") !== false
				)
				{
					// Newline is present in field
					$quote_field = true;
				}
				elseif (!is_numeric($fields[$i]) && (substr($fields[$i], 0, 1) == " " || substr($fields[$i], -1) == " "))
				{
					// Space found at beginning or end of field value
					$quote_field = true;
				}

				if ($quote_field)
				{
					// Escape the quote character within the field (e.g. " becomes "")
					$quoted_value = str_replace($quote, $quote . $quote, $fields[$i]);

					$write .= $quote . $quoted_value . $quote;
				}
				else
				{
					$write .= $fields[$i];
				}

				$write .= ($i < ($field_count - 1)) ? $conf['sep'] : $conf['crlf'];
			}

			return $write;
		}

		public function format_factum( array &$reservations, array $account_codes, $sequential_number_generator )
		{
			$headers = array();
			$fakturalinjer = array();
			$export_info = array();
			$output = array();

			$log = array();

			if (!empty($this->config_data['voucher_client']))
			{
				$client_id = strtoupper($this->config_data['voucher_client']);
			}
			else
			{
				$client_id = 'BY';
			}

			$status = 'N';
			$trans_type = '42';

			if (!empty($this->config_data['voucher_type']))
			{
				$voucher_type = substr(strtoupper($this->config_data['voucher_type']), 0, 2);
			}
			else
			{
				$voucher_type = 'FK';
			}

			$stored_header = array('tekst2' => false);
			$line_no = 0;
			$header_count = 0;
			$log_order_id = '';
			$log_customer_name = '';
			$log_customer_nr = '';
			$log_buidling = '';
			$tax_code = 0;
			$contact_name = '';

			foreach ($reservations as &$reservation)
			{
				switch ($reservation['reservation_type'])
				{
					case 'allocation':
						$test = $this->allocation_bo->read_single($reservation['reservation_id']);
						break;
					case 'booking':
						$test = $this->booking_bo->read_single($reservation['reservation_id']);
						break;
					case 'event':
						$test = $this->event_bo->read_single($reservation['reservation_id']);
						break;
					default:
						break;
				}

				if(empty($test['id']))
				{
					continue; //Reservation has been deleted
				}

				if(empty($test['active']))
				{
					continue; //Reservation has been de-activated
				}

				if ($this->get_cost_value($reservation['cost']) <= 0)
				{
					continue; //Don't export costless rows
				}

				/**
				 * Get contact person
				 */
				switch ($reservation['reservation_type'])
				{
					case 'allocation':
						if (!empty($reservation['organization_id']))
						{
							$org = $this->organization_bo->read_single($reservation['organization_id']);
							if(!empty($org['contacts'][0]['name']))
							{
								$contact_name = iconv("utf-8", "ISO-8859-1//TRANSLIT", $org['contacts'][0]['name']);
							}
						}
						break;
					case 'booking':
						if(!empty($test['group_id']))
						{
							$group = CreateObject('booking.sogroup')->read_single($test['group_id']);
							if(!empty($group['contacts'][0]['name']))
							{
								$contact_name = iconv("utf-8", "ISO-8859-1//TRANSLIT", $group['contacts'][0]['name']);
							}
						}
						break;
					case 'event':
						$contact_name = iconv("utf-8", "ISO-8859-1//TRANSLIT", $test['contact_name']);
						break;
					default:
						break;
				}

				$purchase_order = $this->sopurchase_order->get_purchase_order(0, $reservation['reservation_type'], $reservation['reservation_id']);
				/**
				 * For vipps kan det være flere krav, for etterfakturering vil det være ett
				 */
				$payments = $this->sopurchase_order->get_order_payments($purchase_order['order_id']);
				if(isset($payments[0]))
				{
					$payment = $payments[0];

					/**
					 * Already paid for, or cancelled
					 */
					if(in_array($payment['status'], array( 'completed', 'voided', 'refunded')))
					{
						continue;
					}

					//FIXME: move method from soapplication
					// status: new, pending, completed, voided, partially_refunded, refunded
					$this->application_so->update_payment_status($payment['remote_id'], 'completed', 'RESERVE');

					/**
					 * sjekk status / opdater status
					 */
				}

				$type = $reservation['customer_type'];

				$from_date = new DateTime($reservation['from_']);
				$to_date = new DateTime($reservation['to_']);

				$log_customer_name = '';
				if (!empty($reservation['organization_id']))
				{
					$org = $this->organization_bo->read_single($reservation['organization_id']);
					$log_customer_name = $org['name'];
					$customer_number =  $org['customer_number'];
					if(!empty($org['in_tax_register']))
					{
						$tax_code = 1;
					}
				}
				else
				{
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if (!empty($data['id']))
					{
						$log_customer_name = $data['name'];
					}
					else
					{
						if ($reservation['reservation_type'] == 'event')
						{
							$data = $this->event_bo->read_single($reservation['reservation_id']);
							$log_customer_name = $data['contact_name'];
						}
					}
				}

				if ($type == 'internal')
				{
					//Nøkkelfelt, kundens personnr/orgnr.
					$check_customer_identifier = $this->get_customer_identifier_value_for($reservation);
				}
				else
				{
					//Nøkkelfelt, kundens personnr/orgnr. - men differensiert for undergrupper innenfor samme orgnr
					$check_customer_identifier = $this->get_customer_identifier_value_for($reservation) . '::' . $customer_number;
				}


				if ($stored_header == array() || $stored_header['tekst2'] != $check_customer_identifier)
				{
					$order_id = $sequential_number_generator->increment()->get_current();
					$export_info[] = $this->create_export_item_info($reservation, $order_id);
					$header_count += 1;
					//header level

					$stored_header['client'] = $client_id;

					//Nøkkelfelt, kundens personnr/orgnr. - men differensiert for undergrupper innenfor samme orgnr
					$stored_header['tekst2'] = $check_customer_identifier;

//					if ($type == 'internal')
//					{
//						$ext_ord_ref = substr($this->get_customer_identifier_value_for($reservation), 0, 30);
//					}
//					else
					{
						$ext_ord_ref = iconv("utf-8", "ISO-8859-1//TRANSLIT", $customer_number);
					}

					$kundenr = trim($this->get_customer_identifier_value_for($reservation));
					$stored_header['kundenr'] = $kundenr;

					$stored_header['order_id'] = $order_id;

					$stored_header['status'] = $status;
					$stored_header['trans_type'] = $trans_type;
					$stored_header['voucher_type'] = $voucher_type;


					$header = array();

					$header['Blanketttype'] = 'F';//char(1) F = Faktura
					$header['datoendr'] = date('d.m.Y');//dato 31.01.1997
					$header['Deresref'] = $ext_ord_ref;//char(30)
					$header['Fagsystemkundeid'] = $kundenr;

					$fakturalinje = array();

					//item level
					//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
					if (isset($this->config_data['dim_1']))
					{
						$fakturalinje['AnsvarDim'] = strtoupper(substr($account_codes['responsible_code'], 0, 8));	//char(8)
					}

					$fakturalinje['antall']	 = 1;	//Desimal
					$fakturalinje['ArtDim']	 = '';  //char(8)
					$fakturalinje['Avgift']	 = '';  //Beløp
					$fakturalinje['BalanseDim']	 = '';  //char(8)
					$fakturalinje['Grunnlagstype']	 = 'KRV';  //char(8)
					$fakturalinje['enhetspris']	 = $reservation['cost'];  //Beløp
					$fakturalinje['Fagsystemkontoid']	 = '';  //char(30) ???
					$fakturalinje['FeiletLinjeFelt']	 = '';  //Char
					$fakturalinje['FormalDim']	 = '';  //char(8)
					$fakturalinje['fradato']	 = $from_date->format('d.m.Y');  //dato

//					$fakturalinje['mvakode']	 = $tax_code;  //char(1)

					//Formål. eks.
					if ($type == 'internal' && isset($this->config_data['dim_2']))
					{
						$fakturalinje['FormalDim'] = strtoupper(substr($account_codes['service'], 0, 8));
					}

					//Objektnr. vil være knyttet til hvert hus (FDVU)
					if (isset($this->config_data['dim_3']))
					{
						$fakturalinje['ObjektDim'] = strtoupper(substr($account_codes['object_number'], 0, 8));//char(8)
					}

					$fakturalinje['orgkode']	 = '';  //char(8)
					$fakturalinje['SumPrisUtenAvgift']	 =$reservation['cost'];  //Beløp
					$fakturalinje['tildato']	 = $to_date->format('d.m.Y');  //Dato
					$fakturalinje['Tilleggstekst'] = substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['article_description'] . ' - ' . $reservation['description']), 0, 225);
					$fakturalinje['Varekode']	 = iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['article']);  //char(8)
					$fakturalinje['Fakturaorgkode']	 = '';  //
					//Topptekst til faktura, knyttet mot fagavdeling
					$fakturalinje['Fakturaoverskrift']	 = substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['invoice_instruction']), 0, 60);  //char(60)

//
//					//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt
					if (isset($this->config_data['dim_5']))
					{
						$fakturalinje['orgkode'] = trim(strtoupper($account_codes['project_number']));
					}

					/*
					'order_id'
					'status'
					'parent_mapping_id'
					'article_mapping_id'
					'quantity'
					'unit_price'
					'overridden_unit_price'
					'currency'
					'amount'
					'tax_code'
					'article_code'
					'tax'
					'name'
					*/
					$line_no = 0;

					$log_cost = 0;
					$log_cost2 = 0;

					$fakturalinje['contact_name'] = $contact_name;

					if($purchase_order && !empty($purchase_order['lines']))
					{

						foreach ($purchase_order['lines'] as $order_line)
						{
							if(empty($order_line['amount']))
							{
								continue;
							}

							if($order_line['parent_mapping_id'] == 0)
							{
								$article_name = $order_line['name']  . ' - ' . $reservation['description'];
							}
							else
							{
								$article_name = $order_line['name'];
							}

							$line_no += 1;
							$fakturalinje['Linjenr']			 = $line_no;
							$fakturalinje['Varekode']			 = iconv("utf-8", "ISO-8859-1//TRANSLIT", $order_line['article_code']);
							$fakturalinje['SumPrisUtenAvgift']	 = $order_line['amount'];
							$fakturalinje['Avgift']				 = $order_line['tax'];
							$fakturalinje['Tilleggstekst']		 = substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $article_name), 0, 255);
//							$fakturalinje['mvakode']			 = $order_line['tax_code'];
							$fakturalinje['mvakode']			 = $order_line['tax_code'] == 38 ? "U" : $order_line['tax_code'];
							$fakturalinje['antall']				 = $order_line['quantity'];
							$fakturalinje['enhetspris']			 = $order_line['unit_price'];
							$fakturalinjer[$check_customer_identifier]['BkPffFakturagrunnlaglinje'][] = $fakturalinje;
							$log_cost							+= $order_line['amount'];
							$log_cost2							+= $order_line['tax'];

						}

					}
					else
					{
						$line_no += 1;
						$fakturalinje['Linjenr']			 = $line_no;
						$fakturalinjer[$check_customer_identifier]['BkPffFakturagrunnlaglinje'][] = $fakturalinje;
						$log_cost							 = $reservation['cost'];
					}


					$headers[$check_customer_identifier] = $header;

					$log_order_id = $order_id;

					$log_customer_nr = $stored_header['kundenr'];

					$log_buidling = $reservation['building_name'];
					$log_varelinjer_med_dato = $reservation['article_description'] . ' - ' . $reservation['description'];

					$line_field = array();

					$line_field[] = "\"{$reservation['reservation_id']}\"";
					$line_field[] = "\"{$reservation['reservation_type']}\"";
					$line_field[] = "\"{$log_order_id}\"";
					$line_field[] = "\"{$log_customer_name}\"";
					$line_field[] = "\"{$log_customer_nr}\"";
					$line_field[] = "\"{$log_varelinjer_med_dato}\"";
					$line_field[] = "\"{$log_buidling}\"";
					$line_field[] = '"' . number_format($log_cost, 2, ",", '') . '"';
					$line_field[] = '"' . number_format($log_cost2, 2, ",", '') . '"';

					$log[] = implode(';',  $line_field);

				}
				else
				{
					//item level

					$fakturalinje = array();

					//item level
					//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
					if (isset($this->config_data['dim_1']))
					{
						$fakturalinje['AnsvarDim'] = strtoupper(trim($account_codes['responsible_code']));	//char(8)
					}

					$fakturalinje['antall']				 = 1; //Desimal
					$fakturalinje['ArtDim']				 = '';  //char(8)
					$fakturalinje['Avgift']				 = '';  //Beløp
					$fakturalinje['BalanseDim']			 = '';  //char(8)
					$fakturalinje['Grunnlagstype']	 = 'KRV';  //char(8)
					$fakturalinje['enhetspris']			 = $reservation['cost'];  //Beløp
					$fakturalinje['Fagsystemkontoid']	 = '';  //char(30)
//					$fakturalinje['Fagsystemvareid']	 = '';  //char(30)
					$fakturalinje['FeiletLinjeFelt']	 = '';  //Char
					$fakturalinje['FormalDim']			 = '';  //char(8)
					$fakturalinje['fradato']			 = $from_date->format('d.m.Y');  //dato

//					$fakturalinje['mvakode']			 = $tax_code;  //char(1)

					//Formål. Eks Idrett
					if ($type == 'internal' && isset($this->config_data['dim_2']))
					{
						$fakturalinje['FormalDim'] = trim(strtoupper($account_codes['service']));
					}

					//Objektnr. vil være knyttet til hvert hus (FDVU)
					if (isset($this->config_data['dim_3']))
					{
						$fakturalinje['ObjektDim'] = strtoupper(trim($account_codes['object_number']));//char(8)
					}

					$fakturalinje['orgkode']	 = '';  //char(8)
					if (isset($this->config_data['dim_5']))
					{
						$fakturalinje['orgkode'] = trim(strtoupper($account_codes['project_number']));
					}


					$fakturalinje['SumPrisUtenAvgift'] = $reservation['cost'];  //Beløp

					$fakturalinje['tildato']			 = $to_date->format('d.m.Y');  //Dato
					$fakturalinje['Tilleggstekst']		 = substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['article_description'] . ' - ' . $reservation['description']), 0, 225);
					$fakturalinje['Varekode']			 = iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['article']);  //char(8)
					$fakturalinje['Fakturaoverskrift']	 = substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['invoice_instruction']), 0, 60);  //char(60)

					$log_cost = 0;
					$log_cost2 = 0;

					$fakturalinje['contact_name'] = $contact_name;

					if($purchase_order && !empty($purchase_order['lines']))
					{
						foreach ($purchase_order['lines'] as $order_line)
						{
							if(empty($order_line['amount']))
							{
								continue;
							}

							if($order_line['parent_mapping_id'] == 0)
							{
								$article_name = $order_line['name']  . ' - ' . $reservation['description'];
							}
							else
							{
								$article_name = $order_line['name'];
							}

							$line_no += 1;
							$fakturalinje['Linjenr']			 = $line_no;
							$fakturalinje['Varekode']			 = iconv("utf-8", "ISO-8859-1//TRANSLIT",$order_line['article_code']);
							$fakturalinje['SumPrisUtenAvgift']	 = $order_line['amount'];
							$fakturalinje['Avgift']				 = $order_line['tax'];
							$fakturalinje['Tilleggstekst']		 = substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $article_name), 0, 255);
//							$fakturalinje['mvakode']			 = $order_line['tax_code'];
							$fakturalinje['mvakode']			 = $order_line['tax_code'] == 38 ? "U" : $order_line['tax_code'];
							$fakturalinje['antall']				 = $order_line['quantity'];
							$fakturalinje['enhetspris']			 = $order_line['unit_price'];
							$fakturalinjer[$check_customer_identifier]['BkPffFakturagrunnlaglinje'][] = $fakturalinje;
							$log_cost							+= $order_line['amount'];
							$log_cost2							+= $order_line['tax'];
						}

					}
					else
					{
						$line_no += 1;
						$fakturalinje['Linjenr']			 = $line_no;
						$fakturalinjer[$check_customer_identifier]['BkPffFakturagrunnlaglinje'][] = $fakturalinje;
						$log_cost							 = $reservation['cost'];
					}


					$log_buidling			 = $reservation['building_name'];
					$log_varelinjer_med_dato = $reservation['article_description'] . ' - ' . $reservation['description'];

					$line_field = array();

					$line_field[] = "\"{$reservation['reservation_id']}\"";
					$line_field[] = "\"{$reservation['reservation_type']}\"";
					$line_field[] = "\"{$log_order_id}\"";
					$line_field[] = "\"{$log_customer_name}\"";
					$line_field[] = "\"{$log_customer_nr}\"";
					$line_field[] = "\"{$log_varelinjer_med_dato}\"";
					$line_field[] = "\"{$log_buidling}\"";
					$line_field[] = '"' . number_format($log_cost, 2, ",", '') . '"';
					$line_field[] = '"' . number_format($log_cost2, 2, ",", '') . '"';

					$log[] = implode(';',  $line_field);

					$header_count += 1;

				}
			}

			$invoice = array();
			foreach ($fakturalinjer as $key => $_fakturalinjer)
			{
				$fakturagrunnlag = $headers[$key];
				$fakturagrunnlag['Fakturalinjer'] = $_fakturalinjer;
				$fakturagrunnlag['Systemid']	 = $client_id;  //

				$invoice['BkPffFakturagrunnlag'][] = $fakturagrunnlag;
			}

			if (count($export_info) == 0)
			{
				return null;
			}
			return array('data' => $invoice, 'data_log' => implode(PHP_EOL, $log),
				'info' => $export_info, 'header_count' => $header_count);
		}

		protected function combine_agresso_export_data( array &$combined_data, $export )
		{
			if (count($combined_data) == 0)
			{
				$combined_data[] = $export['data'];
			}
			else
			{
				$combined_data[] = "\n";
				$combined_data[] = $export['data'];
			}
		}

		public function format_agresso( array &$reservations, array $account_codes, $sequential_number_generator )
		{
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


			$batch_id = strtoupper(sprintf('BO%s%s', $account_codes['unit_prefix'], date('ymd')));
			$batch_id = str_pad(substr($batch_id, 0, 12), 12, ' ');

			if (!empty($this->config_data['voucher_client']))
			{
				$client_id = str_pad(substr(strtoupper($this->config_data['voucher_client']), 0, 2), 2, ' ');
			}
			else
			{
				$client_id = str_pad(substr(strtoupper('BY'), 0, 2), 2, ' ');
			}

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
			$period = str_pad(substr('00' . date('Ym'), 0, 8), 8, '0', STR_PAD_LEFT);
			//$period = str_pad(substr(date('Ym'), 0, 8), 8, ' ');

			if (!empty($this->config_data['voucher_responsible']))
			{
				$responsible = str_pad(substr(strtoupper($this->config_data['voucher_responsible']), 0, 8), 8, ' ');
			}
			else
			{
				$responsible = str_pad(substr(strtoupper('BOOKING'), 0, 8), 8, ' ');
			}

			$responsible2 = str_pad(substr(strtoupper($responsible), 0, 8), 8, ' ');
			$status = str_pad(substr(strtoupper('N'), 0, 1), 1, ' ');
			$trans_type = str_pad(substr(strtoupper('42'), 0, 2), 2, ' ');

			if (!empty($this->config_data['voucher_type']))
			{
				$voucher_type = str_pad(substr(strtoupper($this->config_data['voucher_type']), 0, 2), 2, ' ');
			}
			else
			{
				$voucher_type = str_pad(substr(strtoupper('FK'), 0, 2), 2, ' ');
			}

			$stored_header = array('tekst4' => false);
			$line_no = 0;
			$header_count = 0;
			$log_order_id = '';
			$log_customer_name = '';
			$log_customer_nr = '';
			$log_buidling = '';
			$customer_number = '';

			$internal = false;

			foreach ($reservations as &$reservation)
			{

				switch ($reservation['reservation_type'])
				{
					case 'allocation':
						$test = $this->allocation_bo->read_single($reservation['reservation_id']);
						break;
					case 'booking':
						$test = $this->booking_bo->read_single($reservation['reservation_id']);
						break;
					case 'event':
						$test = $this->event_bo->read_single($reservation['reservation_id']);
						break;
					default:
						break;
				}

				if(empty($test['id']))
				{
					continue; //Reservation has been deleted
				}

				if(empty($test['active']))
				{
					continue; //Reservation has been de-activated
				}

				if ($this->get_cost_value($reservation['cost']) <= 0)
				{
					continue; //Don't export costless rows
				}

				$type = $reservation['customer_type'];

				$log_customer_name = '';
				$organization_number =  '';
				$customer_number =  '';
				$payer_organization_number = '';

				if (!empty($reservation['organization_id']))
				{
					$org = $this->organization_bo->read_single($reservation['organization_id']);
					$log_customer_name = $org['name'];
					$organization_number =  $org['organization_number'];
					$customer_number =  $org['customer_number'];
					$payer_organization_number = $org['customer_organization_number'];
				}
				else
				{
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					$payer_organization_number = $data['customer_organization_number'];
					if (!empty($data['id']))
					{
						$log_customer_name = $data['name'];
					}
					else
					{
						if ($reservation['reservation_type'] == 'event')
						{
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

				if ($type == 'internal')
				{
					//Nøkkelfelt, kundens personnr/orgnr.
					$check_customer_identifier = $this->get_customer_identifier_value_for($reservation);
				}
				else
				{
					//Nøkkelfelt, kundens personnr/orgnr. - men differensiert for undergrupper innenfor samme orgnr
					$check_customer_identifier = $this->get_customer_identifier_value_for($reservation) . '::' . $customer_number;
				}

				$purchase_order = $this->sopurchase_order->get_purchase_order(0, $reservation['reservation_type'], $reservation['reservation_id']);
				/**
				 * For vipps kan det være flere krav, for etterfakturering vil det være ett
				 */
				$payments = $this->sopurchase_order->get_order_payments($purchase_order['order_id']);

				if(isset($payments[0]))
				{
					$payment = $payments[0];

					/**
					 * Already paid for, or cancelled
					 */
					if(in_array($payment['status'], array( 'completed', 'voided', 'refunded')))
					{
						continue;
					}

					//FIXME: move method from soapplication
					// status: new, pending, completed, voided, partially_refunded, refunded
					$this->application_so->update_payment_status($payment['remote_id'], 'completed', 'RESERVE');

					/**
					 * sjekk status / opdater status
					 */
				}

				if ($stored_header == array() || $stored_header['tekst4'] != $check_customer_identifier)
				{
					$order_id = $sequential_number_generator->increment()->get_current();
					$export_info[] = $this->create_export_item_info($reservation, $order_id);
					$header_count += 1;
					//header level
					$header = $this->get_agresso_row_template();
					$header['accept_flag'] = '1';

					// TODO: Introduce a unique id if several transfers in one day?
					$header['batch_id'] = $stored_header['batch_id'] = $batch_id;

					$header['client'] = $client_id;
					$stored_header['client'] = $client_id;
					$header['confirm_date'] = $date;
					$header['currency'] = $currency;
					$header['deliv_date'] = $header['confirm_date'];

					if (!empty($this->config_data['att_1_id']))
					{
						$header['att_1_id'] = str_pad(strtoupper(substr($this->config_data['att_1_id'], 0, 2)), 2, ' ');
					}
					if (!empty($this->config_data['att_2_id']))
					{
						$header['att_2_id'] = str_pad(strtoupper(substr($this->config_data['att_2_id'], 0, 2)), 2, ' ');
					}
					if (!empty($this->config_data['att_3_id']))
					{
						$header['att_3_id'] = str_pad(strtoupper(substr($this->config_data['att_3_id'], 0, 2)), 2, ' ');
					}
					if (!empty($this->config_data['att_4_id']))
					{
						$header['att_4_id'] = str_pad(strtoupper(substr($this->config_data['att_4_id'], 0, 2)), 2, ' ');
					}
					if (!empty($this->config_data['att_5_id']))
					{
						$header['att_5_id'] = str_pad(strtoupper(substr($this->config_data['att_5_id'], 0, 2)), 2, ' ');
					}
					if (!empty($this->config_data['att_6_id']))
					{
						$header['att_6_id'] = str_pad(strtoupper(substr($this->config_data['att_6_id'], 0, 2)), 2, ' ');
					}
					if (!empty($this->config_data['att_7_id']))
					{
						$header['att_7_id'] = str_pad(strtoupper(substr($this->config_data['att_7_id'], 0, 2)), 2, ' ');
					}

					//Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
					if (isset($this->config_data['dim_value_1']))
					{
						$header['dim_value_1'] = str_pad(strtoupper(substr($account_codes['unit_number'], 0, 12)), 12, ' ');
					}

					if (isset($this->config_data['dim_value_2']))
					{
						$header['dim_value_2'] = str_pad(substr($account_codes['dim_value_2'], 0, 12), 12, ' ');
					}
					if (isset($this->config_data['dim_value_3']))
					{
						$header['dim_value_3'] = str_pad(substr($account_codes['dim_value_3'], 0, 12), 12, ' ');
					}
					if (isset($this->config_data['dim_value_4']))
					{
						$header['dim_value_4'] = str_pad(substr($account_codes['dim_value_4'], 0, 12), 12, ' ');
					}

					/**
					 * Vår ref.
					 */
					if (isset($this->config_data['dim_value_5']))
					{
						$header['dim_value_5'] = str_pad(substr($account_codes['dim_value_5'], 0, 12), 12, ' ');
					}
					if (isset($this->config_data['dim_value_6']))
					{
						$header['dim_value_6'] = str_pad(substr($account_codes['dim_value_6'], 0, 12), 12, ' ');
					}
					if (isset($this->config_data['dim_value_7']))
					{
						$header['dim_value_7'] = str_pad(substr($account_codes['dim_value_7'], 0, 12), 12, ' ');
					}

					//Nøkkelfelt, kundens personnr/orgnr. - men differensiert for undergrupper innenfor samme orgnr
					$stored_header['tekst4'] = $check_customer_identifier;
					$header['tekst3'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 12), 12, ' ');

					if ($type == 'internal')
					{
						$header['tekst4'] = str_pad(substr($this->config_data['organization_value'], 0, 12), 12, ' ');
						//referansenr/customer_number
						$header['ext_ord_ref'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $customer_number), 0, 15), 15, ' ');
	//					$header['ext_ord_ref'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 15), 15, ' ');
					}
					else
					{
						$header['tekst4'] = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 12), 12, ' ');
						$header['ext_ord_ref'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $customer_number), 0, 15), 15, ' ');
					}

					/**
					 * Skille mellom hoved-organisasjonen og betalende underliggende organisasjon
					 */
					if (!empty($this->config_data['differentiate_org_payer']) && !empty($organization_number))
					{
						$header['tekst3'] = str_pad(substr($organization_number, 0, 12), 12, ' ');
						$header['tekst4'] = str_pad(substr($payer_organization_number, 0, 12), 12, ' ');
					}

					$header['line_no'] = '0000'; //Nothing here according to example file but spec. says so
					//Topptekst til faktura, knyttet mot fagavdeling
					$header['long_info1'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['invoice_instruction']), 0, 120), 120, ' ');

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

					$output[] = implode('', str_replace(array("\n", "\r"), '', $header));

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
					$item['art_descr'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['article_description']), 0, 35), 35, ' '); //35 chars long
					//Artikkel opprettes i Agresso (4 siffer), en for kultur og en for idrett, inneholder konteringsinfo.
					$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');

					$item['batch_id'] = $header['batch_id'];
					$item['client'] = $header['client'];

					//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
					if (isset($this->config_data['dim_1']))
					{
						$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' ');
					}

					//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
					if (isset($this->config_data['dim_2']))
					{
						$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
					}

					//Objektnr. vil være knyttet til hvert hus (FDVU)
					if (isset($this->config_data['dim_3']))
					{
						$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
					}

					if (isset($this->config_data['dim_4']))
					{
						$item['dim_4'] = str_pad(substr($account_codes['dim_4'], 0, 8), 8, ' ');
					}

					//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt
					if (isset($this->config_data['dim_5']))
					{
						$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
					}
					if (isset($this->config_data['dim_6']))
					{
						$item['dim_6'] = str_pad(substr($account_codes['dim_6'], 0, 4), 4, ' ');
					}
					if (isset($this->config_data['dim_7']))
					{
						$item['dim_7'] = str_pad(substr($account_codes['dim_7'], 0, 4), 4, ' ');
					}

					$item['line_no'] = str_pad($line_no, 4, 0, STR_PAD_LEFT);

					$item['order_id'] = $header['order_id'];
					$item['period'] = $header['period'];
					$item['sequence_no'] = str_repeat('0', 8);

					$item['status'] = $header['status'];
					$item['trans_type'] = $header['trans_type'];

					$item['value_1'] = str_pad(1 * 100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
					$item['voucher_type'] = $header['voucher_type'];

					//text level
					$text = $this->get_agresso_row_template();
					$text['accept_flag'] = '0';
					$text['order_id'] = $header['order_id'];
					$text['batch_id'] = $header['batch_id'];
					$text['client'] = $header['client'];
					$text['line_no'] = $item['line_no'];
					$text['short_info'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['description']), 0, 60), 60, ' ');
					$text['trans_type'] = $header['trans_type'];
					$text['voucher_type'] = $header['voucher_type'];

					$text['sequence_no'] = str_pad(intval($item['sequence_no']) + 1, 8, '0', STR_PAD_LEFT);

					$log_cost = 0;
					$log_cost2 = 0;

					if($purchase_order && !empty($purchase_order['lines']))
					{
						$line_no -=1;

						$_item = $item;
						$_text = $text;
						foreach ($purchase_order['lines'] as $order_line)
						{
							if(empty($order_line['amount']))
							{
								continue;
							}

							$line_no += 1;

							if($order_line['parent_mapping_id'] == 0)
							{
								$article_name = $order_line['name']  . ' - ' . $reservation['description'];
							}
							else
							{
								$article_name = $order_line['name'];
							}

							if($order_line['tax_percent'])
							{
								$unit_tax = (float)$order_line['unit_price'] * $order_line['tax_percent'] / 100;
							}
							else
							{
								$unit_tax = 0;
							}

							$pris_inkl_mva = (float)$order_line['unit_price'] + $unit_tax;

							$_item['art_descr']	 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $order_line['name']), 0, 35), 35, ' '); //35 chars long
							$_item['article']	 = str_pad(substr(strtoupper($order_line['article_code']), 0, 15), 15, ' ');
							$_item['amount']	 = $this->format_cost(($order_line['amount']));
							$_item['tax_code']	 = str_pad($order_line['tax_code'], 2, ' ', STR_PAD_LEFT);
							$_item['value_1']	 = str_pad($order_line['quantity'] * 100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
							$_item['line_no']	 = str_pad($line_no, 4, 0, STR_PAD_LEFT);

							$_text['line_no']	 = $_item['line_no'];
							$_text['short_info'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['description']), 0, 60), 60, ' ');

							$log_cost	 += $order_line['amount'];
							$log_cost2	 += $order_line['tax'];

							//Add to orders
							$output[] = implode('', str_replace(array("\n", "\r"), '', $_item));
							$output[] = implode('', str_replace(array("\n", "\r"), '', $_text));

						}
					}
					else
					{
						$log_cost	 = $reservation['cost'];
						//Add to orders
						$output[] = implode('', str_replace(array("\n", "\r"), '', $item));
						$output[] = implode('', str_replace(array("\n", "\r"), '', $text));

					}


					$log_order_id = $order_id;

					if ($type == 'internal')
					{
						$log_customer_nr = $header['tekst4'] . ' ' . $header['ext_ord_ref'];
					}
					else
					{
						$log_customer_nr = $header['tekst4'];
					}


					$log_buidling = $reservation['building_name'];

					$log_varelinjer_med_dato = $reservation['article_description'] . ' - ' . $reservation['description'];

					$line_field = array();

					$line_field[] = "\"{$reservation['reservation_id']}\"";
					$line_field[] = "\"{$reservation['reservation_type']}\"";
					$line_field[] = "\"{$log_order_id}\"";
					$line_field[] = "\"{$log_customer_name}\"";
					$line_field[] = "\"{$log_customer_nr}\"";
					$line_field[] = "\"{$log_varelinjer_med_dato}\"";
					$line_field[] = "\"{$log_buidling}\"";
					$line_field[] = '"' . number_format($log_cost, 2, ",", '') . '"';
					$line_field[] = '"' . number_format($log_cost2, 2, ",", '') . '"';

					$log[] = implode(';',  $line_field);

				}
				else
				{

					//item level
					$item = $this->get_agresso_row_template();
					$line_no += 1;
					$item['accept_flag'] = '0';

					$item['amount'] = $this->format_cost($reservation['cost']); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
					$item['amount_set'] = '1';

					/* Data hentes fra booking, tidspunkt legges i eget felt som kommer på
					 * linjen under: 78_short_info. <navn på bygg>,  <navn på ressurs>
					 */
					$item['art_descr'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['article_description']), 0, 35), 35, ' '); //35 chars long
					//Artikkel opprettes i Agresso (4 siffer), en for kultur og en for idrett, inneholder konteringsinfo.
					$item['article'] = str_pad(substr(strtoupper($account_codes['article']), 0, 15), 15, ' ');

					$item['batch_id'] = $stored_header['batch_id'];
					$item['client'] = $stored_header['client'];

					//Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
					if (isset($this->config_data['dim_1']))
					{
						$item['dim_1'] = str_pad(strtoupper(substr($account_codes['responsible_code'], 0, 8)), 8, ' ');
					}

					//Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
					if (isset($this->config_data['dim_2']))
					{
						$item['dim_2'] = str_pad(strtoupper(substr($account_codes['service'], 0, 8)), 8, ' ');
					}

					//Objektnr. vil være knyttet til hvert hus (FDVU)
					if (isset($this->config_data['dim_3']))
					{
						$item['dim_3'] = str_pad(strtoupper(substr($account_codes['object_number'], 0, 8)), 8, ' ');
					}

					if (isset($this->config_data['dim_4']))
					{
						$item['dim_4'] = str_pad(substr($account_codes['dim_4'], 0, 8), 8, ' ');
					}

					//Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt
					if (isset($this->config_data['dim_5']))
					{
						$item['dim_5'] = str_pad(strtoupper(substr($account_codes['project_number'], 0, 12)), 12, ' ');
					}

					$item['line_no'] = str_pad($line_no, 4, 0, STR_PAD_LEFT);

					$item['order_id'] = $stored_header['order_id'];
					$item['period'] = $stored_header['period'];
					$item['sequence_no'] = str_repeat('0', 8);

					$item['status'] = $stored_header['status'];
					$item['trans_type'] = $stored_header['trans_type'];

					$item['value_1'] = str_pad(1 * 100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
					$item['voucher_type'] = $stored_header['voucher_type'];

					//text level
					$text = $this->get_agresso_row_template();
					$text['accept_flag'] = '0';
					$text['order_id'] = $stored_header['order_id'];
					$text['batch_id'] = $stored_header['batch_id'];
					$text['client'] = $stored_header['client'];
					$text['line_no'] = $item['line_no'];
					$text['short_info'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['description']), 0, 60), 60, ' ');
					$text['trans_type'] = $stored_header['trans_type'];
					$text['voucher_type'] = $stored_header['voucher_type'];

					$text['sequence_no'] = str_pad(intval($item['sequence_no']) + 1, 8, '0', STR_PAD_LEFT);


					$log_cost = 0;
					$log_cost2 = 0;

					if($purchase_order && !empty($purchase_order['lines']))
					{
						$line_no -=1;

						$_item = $item;
						$_text = $text;
						foreach ($purchase_order['lines'] as $order_line)
						{
							if(empty($order_line['amount']))
							{
								continue;
							}
							$line_no += 1;

							if($order_line['parent_mapping_id'] == 0)
							{
								$article_name = $order_line['name']  . ' - ' . $reservation['description'];
							}
							else
							{
								$article_name = $order_line['name'];
							}

							if($order_line['tax_percent'])
							{
								$unit_tax = (float)$order_line['unit_price'] * $order_line['tax_percent'] / 100;
							}
							else
							{
								$unit_tax = 0;
							}

							$pris_inkl_mva = (float)$order_line['unit_price'] + $unit_tax;

							$_item['art_descr']	 = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $order_line['name']), 0, 35), 35, ' '); //35 chars long
							$_item['article']	 = str_pad(substr(strtoupper($order_line['article_code']), 0, 15), 15, ' ');
							$_item['amount']	 = $this->format_cost(($order_line['amount']));
							$_item['tax_code']	 = str_pad($order_line['tax_code'], 2, ' ', STR_PAD_LEFT);
							$_item['value_1']	 = str_pad($order_line['quantity'] * 100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
							$_item['line_no']	 = str_pad($line_no, 4, 0, STR_PAD_LEFT);

							$_text['line_no']	 = $_item['line_no'];
							$_text['short_info'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['description']), 0, 60), 60, ' ');

							$log_cost			 += $order_line['amount'];
							$log_cost2			 += $order_line['tax'];

							//Add to orders
							$output[] = implode('', str_replace(array("\n", "\r"), '', $_item));
							$output[] = implode('', str_replace(array("\n", "\r"), '', $_text));

						}
					}
					else
					{
						$log_cost	 = $reservation['cost'];
						//Add to orders
						$output[] = implode('', str_replace(array("\n", "\r"), '', $item));
						$output[] = implode('', str_replace(array("\n", "\r"), '', $text));

					}

					$log_buidling = $reservation['building_name'];
					$log_varelinjer_med_dato = $reservation['article_description'] . ' - ' . $reservation['description'];

					$line_field = array();

					$line_field[] = "\"{$reservation['reservation_id']}\"";
					$line_field[] = "\"{$reservation['reservation_type']}\"";
					$line_field[] = "\"{$log_order_id}\"";
					$line_field[] = "\"{$log_customer_name}\"";
					$line_field[] = "\"{$log_customer_nr}\"";
					$line_field[] = "\"{$log_varelinjer_med_dato}\"";
					$line_field[] = "\"{$log_buidling}\"";
					$line_field[] = '"' . number_format($log_cost, 2, ",", '') . '"';
					$line_field[] = '"' . number_format($log_cost2, 2, ",", '') . '"';

					$log[] = implode(';',  $line_field);

				}
			}

			if (count($export_info) == 0)
			{
				return null;
			}

			if ($this->config_data['external_format_linebreak'] == 'Windows')
			{
				$file_format_linebreak = "\r\n";
			}
			else
			{
				$file_format_linebreak = "\n";
			}

			return array('data' => implode($file_format_linebreak, $output), 'data_log' => implode(PHP_EOL, $log),
				'info' => $export_info, 'header_count' => $header_count);
		}

		protected function get_agresso_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

			$row_template = array('accept_flag' => str_repeat(' ', 1), 'account' => str_repeat(' ', 8),
				'accountable' => str_repeat(' ', 20), 'address' => str_repeat(' ', 160), 'allocation_key' => str_repeat(' ', 2),
				'amount' => str_repeat(' ', 17), 'amount_set' => str_repeat(' ', 1), 'apar_id' => str_repeat(' ', 8),
				'apar_name' => str_repeat(' ', 30), 'art_descr' => str_repeat(' ', 35), 'article' => str_repeat(' ', 15),
				'att_1_id' => str_repeat(' ', 2), 'att_2_id' => str_repeat(' ', 2), 'att_3_id' => str_repeat(' ', 2),
				'att_4_id' => str_repeat(' ', 2), 'att_5_id' => str_repeat(' ', 2), 'att_6_id' => str_repeat(' ', 2),
				'att_7_id' => str_repeat(' ', 2), 'bank_account' => str_repeat(' ', 35), 'batch_id' => str_repeat(' ', 12),
				'client' => str_repeat(' ', 2), 'client_ref' => str_repeat(' ', 2), 'confirm_date' => str_repeat(' ', 17),
				'control' => str_repeat(' ', 1), 'cur_amount' => str_repeat(' ', 17), 'currency' => str_repeat(' ', 3),
				'del_met_descr' => str_repeat(' ', 60), 'del_term_descr' => str_repeat(' ', 60),
				'deliv_addr' => str_repeat(' ', 255), 'deliv_attention' => str_repeat(' ', 50),
				'deliv_countr' => str_repeat(' ', 3), 'deliv_date' => str_repeat(' ', 17), 'deliv_method' => str_repeat(' ', 8),
				'deliv_terms' => str_repeat(' ', 8), 'dim_1' => str_repeat(' ', 8), 'dim_2' => str_repeat(' ', 8),
				'dim_3' => str_repeat(' ', 8), 'dim_4' => str_repeat(' ', 8), 'dim_5' => str_repeat(' ', 12),
				'dim_6' => str_repeat(' ', 4), 'dim_7' => str_repeat(' ', 4), 'dim_value_1' => str_repeat(' ', 12),
				'dim_value_2' => str_repeat(' ', 12), 'dim_value_3' => str_repeat(' ', 12), 'dim_value_4' => str_repeat(' ', 12),
				'dim_value_5' => str_repeat(' ', 12), 'dim_value_6' => str_repeat(' ', 12), 'dim_value_7' => str_repeat(' ', 12),
				'disc_percent' => str_repeat(' ', 17), 'exch_rate' => str_repeat(' ', 17), 'ext_ord_ref' => str_repeat(' ', 15),
				'intrule_id' => str_repeat(' ', 6), 'line_no' => str_repeat(' ', 4), 'location' => str_repeat(' ', 4),
				'long_info1' => str_repeat(' ', 120), 'long_info2' => str_repeat(' ', 120), 'lot' => str_repeat(' ', 10),
				'main_apar_id' => str_repeat(' ', 8), 'mark_attention' => str_repeat(' ', 50),
				'mark_ctry_cd' => str_repeat(' ', 3), 'markings' => str_repeat(' ', 120), 'obs_date' => str_repeat(' ', 17),
				'order_date' => str_repeat(' ', 17), 'order_id' => str_repeat(' ', 9), 'order_type' => str_repeat(' ', 2),
				'pay_method' => str_repeat(' ', 2), 'period' => str_repeat(' ', 8), 'place' => str_repeat(' ', 30),
				'province' => str_repeat(' ', 40), 'rel_value' => str_repeat(' ', 12), 'responsible' => str_repeat(' ', 8),
				'responsible2' => str_repeat(' ', 8), 'sequence_no' => str_repeat(' ', 8), 'sequence_ref' => str_repeat(' ', 8),
				'serial_no' => str_repeat(' ', 20), 'short_info' => str_repeat(' ', 60), 'status' => str_repeat(' ', 1),
				'tax_code' => str_repeat(' ', 2), 'tax_system' => str_repeat(' ', 2), 'template_id' => str_repeat(' ', 8),
				'terms_id' => str_repeat(' ', 2), 'tekst1' => str_repeat(' ', 12), 'tekst2' => str_repeat(' ', 12),
				'tekst3' => str_repeat(' ', 12), 'tekst4' => str_repeat(' ', 12), 'trans_type' => str_repeat(' ', 2),
				'unit_code' => str_repeat(' ', 3), 'unit_descr' => str_repeat(' ', 50), 'value_1' => str_repeat(' ', 17),
				'voucher_ref' => str_repeat(' ', 9), 'voucher_type' => str_repeat(' ', 2), 'warehouse' => str_repeat(' ', 4),
				'zip_code' => str_repeat(' ', 15));
			return $row_template;
		}

		protected function combine_kommfakt_export_data( array &$combined_data, $export )
		{
			if (count($combined_data) == 0)
			{
				$combined_data[] = $export['data'];
			}
			else
			{
				$combined_data[] = "\n";
				$combined_data[] = $export['data'];
			}
		}

		public function format_kommfakt( array &$reservations, array $account_codes, $sequential_number_generator )
		{
			$export_info = array();
			$output = array();

			$log = array();

			$date = str_pad(date('Ymd'), 17, ' ', STR_PAD_LEFT);


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

			foreach ($reservations as &$reservation)
			{

				if ($this->get_cost_value($reservation['cost']) <= 0)
				{
					continue; //Don't export costless rows
				}

				if (!empty($reservation['organization_id']))
				{
					$org = $this->organization_bo->read_single($reservation['organization_id']);
					$reservation['organization_name'] = $org['name'];
					$payer_organization_number = $org['customer_organization_number'];
				}
				else
				{
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if (!empty($data['id']))
					{
						$reservation['organization_name'] = $data['name'];
						$payer_organization_number = $data['customer_organization_number'];
					}
					else
					{
						if ($reservation['reservation_type'] == 'event')
						{
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

				$kundenr = str_pad(substr($this->get_customer_identifier_value_for($reservation), 0, 11), 11, '0', STR_PAD_LEFT);
				$stored_header['kundenr'] = $kundenr;


				if (strlen($this->get_customer_identifier_value_for($reservation)) > 9)
				{
					$name = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['organization_name']), 30, ' ');
				}
				else
				{
					$name = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['organization_name']), 30, ' ');
				}

				//Startpost ST
				$startpost = $this->get_kommfakt_ST_row_template();
				$startpost['posttype'] = 'ST';
				$startpost['referanse'] = str_pad(substr(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['article_description']), 0, 60), 60, ' ');
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
				$fakturalinje['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['object_number']), 3, '0', STR_PAD_LEFT);
				$fakturalinje['varenr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['responsible_code']), 4, '0', STR_PAD_LEFT);
				$fakturalinje['lopenr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr), 2, '0', STR_PAD_LEFT);
				$fakturalinje['pris'] = str_pad($reservation['cost'] * 100, 8, '0', STR_PAD_LEFT) . ' ';
				$fakturalinje['grunnlag'] = '000000001';
				$fakturalinje['belop'] = str_pad($reservation['cost'] * 100, 8, '0', STR_PAD_LEFT) . ' ';
#				$fakturalinje['saksnr'] = ;
				//Linjetekst LT
				$linjetekst = $this->get_kommfakt_LT_row_template();
				$linjetekst['posttype'] = 'LT';
				$linjetekst['kundenr'] = $kundenr;
				$linjetekst['oppdrgnr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['object_number']), 3, '0', STR_PAD_LEFT);
				$linjetekst['varenr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $account_codes['responsible_code']), 4, '0', STR_PAD_LEFT);
				$linjetekst['lopenr'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $lopenr), 2, '0', STR_PAD_LEFT);
				$linjetekst['linjenr'] = $linjenr;
				$linjetekst['tekst'] = str_pad(iconv("utf-8", "ISO-8859-1//TRANSLIT", $reservation['description']), 50, ' ');
				$ant_post += 3;

				//Sluttpost SL
				$sluttpost = $this->get_kommfakt_SL_row_template();
				$sluttpost['posttype'] = 'SL';
				$sluttpost['antpost'] = str_pad(intval($ant_post) + 1, 8, '0', STR_PAD_LEFT);
				$ant_post = 0;


				$log_order_id = $order_id;

				if (!empty($reservation['organization_id']))
				{
					$org = $this->organization_bo->read_single($reservation['organization_id']);
					$log_customer_name = $org['name'];
				}
				else
				{
					$data = $this->event_so->get_org($reservation['customer_organization_number']);
					if (!empty($data['id']))
					{
						$log_customer_name = $data['name'];
					}
					else
					{
						if ($reservation['reservation_type'] == 'event')
						{
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
				$log_varelinjer_med_dato = $reservation['article_description'] . ' - ' . $reservation['description'];

				$line_field = array();

				$line_field[] = "\"{$reservation['id']}\"";
				$line_field[] = "\"{$reservation['reservation_type']}\"";
				$line_field[] = "\"{$log_order_id}\"";
				$line_field[] = "\"{$log_customer_name}\"";
				$line_field[] = "\"{$log_customer_nr}\"";
				$line_field[] = "\"{$log_varelinjer_med_dato}\"";
				$line_field[] = "\"{$log_buidling}\"";
				$line_field[] = "\"{$log_cost}\"";

				$log[] = implode(';',  $line_field);

		//		$log[] = $reservation['id'] . ';' . $reservation['reservation_type'] . ';' . $log_order_id . ';' . $log_customer_name . ' - ' . $log_customer_nr . ';' . $log_varelinjer_med_dato . ';' . $log_buidling . ';' . $log_cost;

				$output[] = implode('', str_replace(array("\n", "\r"), '', $startpost));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $fakturalinje));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $linjetekst));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $sluttpost));
			}

			if (count($export_info) == 0)
			{
				return null;
			}
			if ($this->config_data['external_format_linebreak'] == 'Windows')
			{
				$file_format_linebreak = "\r\n";
			}
			else
			{
				$file_format_linebreak = "\n";
			}

			return array('data' => implode($file_format_linebreak, $output), 'data_log' => implode(PHP_EOL, $log),
				'info' => $export_info, 'header_count' => $header_count);
		}

		protected function get_kommfakt_ST_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

			$row_template = array('posttype' => str_repeat(' ', 2), 'referanse' => str_repeat(' ', 60));
			return $row_template;
		}

		protected function get_kommfakt_FL_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

			$row_template = array(
				'posttype' => str_repeat(' ', 2),
				'kundenr' => str_repeat(' ', 11),
				'navn' => str_repeat(' ', 30),
				'adresse1' => str_repeat(' ', 30),
				'adresse2' => str_repeat(' ', 30),
				'postnr' => str_repeat(' ', 4),
				'betform' => str_repeat(' ', 2),
				'oppdrgnr' => str_repeat(' ', 3),
				'varenr' => str_repeat(' ', 4),
				'lopenr' => str_repeat(' ', 2),
				'pris' => str_repeat(' ', 9),
				'grunnlag' => str_repeat(' ', 9),
				'belop' => str_repeat(' ', 11),
				'saksnr' => str_repeat(' ', 16)
			);
			return $row_template;
		}

		protected function get_kommfakt_LT_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

			$row_template = array(
				'posttype' => str_repeat(' ', 2),
				'kundenr' => str_repeat(' ', 11),
				'oppdrgnr' => str_repeat(' ', 3),
				'varenr' => str_repeat(' ', 4),
				'lopenr' => str_repeat(' ', 2),
				'linjenr' => str_repeat(' ', 2),
				'tekst' => str_repeat(' ', 50));
			return $row_template;
		}

		protected function get_kommfakt_SL_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

			$row_template = array('posttype' => str_repeat(' ', 2), 'antpost' => str_repeat(' ', 8));
			return $row_template;
		}

		protected function get_visma_ST_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

//			Type Felt    Lengde Posisjon Beskrivelse             M/K Merknader
//			---- ------- ------ -------- ----------------------- --- ----------
//			ST   POSTTYPE   2   001-002  Posttype                 M  Verdi 'ST'
//			ST   REFERANSE 60   003-062  Referanse                K  ST01
//			ST   FORMAT     1   063-063  Utvidet format           K  ST02

			$row_template = array(
				'posttype' => 'ST',
				'referanse' => str_repeat(' ', 60),
				'format' => 'U'
			);
			return $row_template;
		}

		protected function get_visma_FL_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

//			UTVIDET FORMAT PÅ FL-LINJENE
//			============================
//
//			FL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'FL'
//			FL   KUNDENR   11   003-013  Kundenummer              M
//			FL   NAVN      40   014-053  Kundens navn             K
//			FL   ADRESSE1  40   054-093  Adresselinje 1           K
//			FL   ADRESSE2  40   094-133  Adresselinje 2           K
//			FL   POSTNR     4   134-137  Postnummer               K
//			FL   BETFORM    2   138-139  Betalingstype (BG,PG)    M  MRK01
//			FL   OPPDRGNR   3   140-142  Oppdragsgivernummer      M  MRK02
//			FL   VARENR     4   143-146  Varenummer               M  MRK02
//			FL   LØPENR     2   147-148  Løpenummer               M  MRK03
//			FL   PRIS       9   149-157  Varens pris              M  MRK04
//			FL   GRUNNLAG   9   158-166  Antall av varen          M  MRK05
//			FL   BELØP     11   167-177  Utregnet beløp           M  MRK04
//			FL   SAKSNR    16   178-193  Saksnr                   K
//			FL   INTFAKT    1   194-194  Internfaktura            K  MRK07
//			FL   KB01      12   195-206  1. konteringsverdi       K
//			FL   KB02      12   207-218  2. konteringsverdi       K
//			FL   KB03      12   219-230  3. konteringsverdi       K
//			FL   KB04      12   231-242  4. konteringsverdi       K
//			FL   KB05      12   243-254  5. konteringsverdi       K
//			FL   KB06      12   255-266  6. konteringsverdi       K
//			FL   KB07      12   267-278  7. konteringsverdi       K
//			FL   KB08      12   279-290  8. konteringsverdi       K
//			FL   KB09      12   291-302  9. konteringsverdi       K
//			FL   KB10      12   303-314  10. konteringsverdi      K
//			FL   MVAKODE    3   315-317  Mva-kode                 K
//			FL   PROFIL    20   318-337  Profil                   K
//			FL   DERESREF  40   338-377  Kontaktinformasjon       K
//			FL   ORDREREF  20   378-397  Ordrereferanse           K

			$row_template = array(
				'posttype' => 'FL',
				'kundenr' => str_repeat(' ', 11),
				'navn' => str_repeat(' ', 40),
				'adresse1' => str_repeat(' ', 40),
				'adresse2' => str_repeat(' ', 40),
				'postnr' => str_repeat(' ', 4),
				'betform' => str_repeat(' ', 2),
				'oppdrgnr' => str_repeat(' ', 3),
				'varenr' => str_repeat(' ', 4),
				'lopenr' => str_repeat(' ', 2),
				'pris' => str_repeat(' ', 9),
				'grunnlag' => str_repeat(' ', 9),
				'belop' => str_repeat(' ', 11),
				'saksnr' => str_repeat(' ', 16),
				'intfakt' => str_repeat(' ', 1),
				'kb01' => str_repeat(' ', 12),
				'kb02' => str_repeat(' ', 12),
				'kb03' => str_repeat(' ', 12),
				'kb04' => str_repeat(' ', 12),
				'kb05' => str_repeat(' ', 12),
				'kb06' => str_repeat(' ', 12),
				'kb07' => str_repeat(' ', 12),
				'kb08' => str_repeat(' ', 12),
				'kb09' => str_repeat(' ', 12),
				'kb10' => str_repeat(' ', 12),
				'mvakode' => str_repeat(' ', 3),
				'profil' => str_repeat(' ', 20),
				'deresref' => str_repeat(' ', 40),
				'ordreref' => str_repeat(' ', 20),
			);
			return $row_template;
		}

		protected function get_visma_LT_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}

//			LT   POSTTYPE   2   001-002  Posttype                 M  Verdi 'LT'
//			LT   KUNDENR   11   003-013  Kundenummer              M
//			LT   OPPDRGNR   3   014-016  Oppdragsgivernummer      M
//			LT   VARENR     4   017-020  Varenummer               M
//			LT   LØPENR     2   021-022  Løpenummer               M
//			LT   LINJENR    2   023-024  Linjenummer              M  MRK06
//			LT   TEKST     50   025-074  Fritekstlinje            K

			$row_template = array(
				'posttype' => 'LT',
				'kundenr' => str_repeat(' ', 11),
				'oppdrgnr' => str_repeat(' ', 3),
				'varenr' => str_repeat(' ', 4),
				'lopenr' => str_repeat(' ', 2),
				'linjenr' => str_repeat(' ', 2),
				'tekst' => str_repeat(' ', 50)
			);
			return $row_template;
		}

		protected function get_visma_SL_row_template()
		{
			static $row_template = false;
			if ($row_template)
			{
				return $row_template;
			}
//			SL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'SL'
//			SL   ANTPOST    8   003-010  Antall poster            M  Inkl. Start/Sluttpost

			$row_template = array(
				'posttype' => 'SL',
				'antpost' => str_repeat(' ', 8)
			);

			return $row_template;
		}
	}
