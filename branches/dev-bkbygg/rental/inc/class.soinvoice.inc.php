<?php
	phpgw::import_class('rental.socommon');

	class rental_soinvoice extends rental_socommon
	{

		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.soinvoice');
			}
			return self::$so;
		}

		protected function get_id_field_name()
		{
			return 'id';
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$clauses = array('1=1');
			if (isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "{$this->marshal($this->get_id_field_name(), 'field')} = {$this->marshal($filters[$this->get_id_field_name()], 'int')}";
			}
			if (isset($filters['contract_id']))
			{
				$filter_clauses[] = "rental_invoice.contract_id = {$this->marshal($filters['contract_id'], 'int')}";
				$filter_clauses[] = "rental_billing.deleted IS FALSE";
			}
			if (isset($filters['billing_id']))
			{
				$filter_clauses[] = "rental_invoice.billing_id = {$this->marshal($filters['billing_id'], 'int')}";
			}
			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}
			$condition = join(' AND ', $clauses);

			$tables = "rental_invoice";
			$joins = "	{$this->left_join} rental_contract_composite ON (rental_contract_composite.contract_id = rental_invoice.contract_id)";
			$joins .= "	{$this->left_join} rental_composite ON (rental_contract_composite.composite_id = rental_composite.id)";
			$joins .= "	{$this->left_join} rental_contract_party ON (rental_contract_party.contract_id = rental_invoice.contract_id)";
			$joins .= "	{$this->left_join} rental_party party ON (rental_invoice.party_id = party.id)";
			$joins .= " {$this->left_join} rental_contract contract ON (contract.id = rental_invoice.contract_id)";
			$joins .= " {$this->left_join} rental_billing ON (rental_billing.id = rental_invoice.billing_id)";
			$joins .= " {$this->left_join} rental_billing_info ON (rental_billing_info.billing_id = rental_billing.id AND rental_billing_info.term_id=contract.term_id)";
			$order = '';
			if ($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(rental_invoice.id)) AS count';
			}
			else
			{
				$cols = 'rental_invoice.id, rental_invoice.contract_id, rental_invoice.billing_id,'
					. ' rental_invoice.party_id, timestamp_created, rental_invoice.timestamp_start,'
					. ' timestamp_end, rental_invoice.total_sum, total_area, header,'
					. ' rental_invoice.account_in, rental_invoice.account_out, rental_invoice.service_id,'
					. ' rental_invoice.responsibility_id, rental_invoice.project_id,'
					. ' rental_invoice.serial_number, rental_composite.name AS composite_name,'
					. ' party.identifier AS party_identifier, party.first_name AS party_first_name,'
					. ' party.last_name AS party_last_name, party.title AS party_title,'
					. ' party.company_name AS party_company_name, party.department AS party_department,'
					. ' party.address_1 AS party_address_1, party.address_2 AS party_address_2,'
					. ' party.postal_code AS party_postal_code, party.place AS party_place,'
					. ' party.phone AS party_phone, party.mobile_phone AS party_mobile_phone,'
					. ' party.fax AS party_fax, party.email AS party_email, party.url AS party_url,'
					. ' party.account_number AS party_account_number, party.reskontro AS party_reskontro,'
					. ' party.location_id AS party_location_id, party.is_inactive as party_in_active, party.customer_id,'
					. ' contract.old_contract_id, rental_billing.title as billing_title,'
					. ' rental_billing_info.term_id, rental_billing_info.month, contract.reference,'
					. ' contract.customer_order_id';
				$dir = $ascending ? 'ASC' : 'DESC';
				if ($sort_field == null || $sort_field == '') // Sort field not set
				{
					$sort_field = 'rental_invoice.id'; // Set to default
				}
				$sort_field = $this->marshal($sort_field, 'field');
				if ($sort_field == 'party_name')
				{
					$order = "ORDER BY party.last_name {$dir}, party.first_name {$dir}, party.company_name {$dir}";
				}
				else
				{
					$order = "ORDER BY {$sort_field} {$dir}";
				}
			}

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		protected function populate( int $invoice_id, &$invoice )
		{
			if ($invoice == null)
			{
				$invoice = new rental_invoice($this->db->f('id', true), $this->db->f('billing_id', true), $this->db->f('contract_id', true), $this->db->f('timestamp_created', true), $this->db->f('timestamp_start', true), $this->db->f('timestamp_end', true), $this->db->f('total_sum', true), $this->db->f('total_area', true), $this->db->f('header', true), $this->db->f('account_in', true), $this->db->f('account_out', true), $this->db->f('service_id', true), $this->db->f('responsibility_id', true), $this->db->f('project_id', true));
				$invoice->set_party_id($this->unmarshal($this->db->f('party_id'), 'int'));
				$invoice->set_project_id($this->unmarshal($this->db->f('project_id'), 'string'));
				$invoice->set_old_contract_id($this->unmarshal($this->db->f('old_contract_id'), 'string'));
				$invoice->set_term_id($this->unmarshal($this->db->f('term_id'), 'int'));
				$invoice->set_month($this->unmarshal($this->db->f('month'), 'int'));
				$invoice->set_billing_title($this->unmarshal($this->db->f('billing_title'), 'string'));
				$invoice->set_serial_number($this->unmarshal($this->db->f('serial_number'), 'int'));
				$invoice->set_reference($this->unmarshal($this->db->f('reference',true), 'string'));
				$invoice->set_customer_order_id($this->unmarshal($this->db->f('customer_order_id'), 'int'));
				$party = new rental_party($this->unmarshal($this->db->f('party_id'), 'int'));
				$party->set_account_number($this->unmarshal($this->db->f('party_account_number'), 'string'));
				$party->set_address_1($this->unmarshal($this->db->f('party_address_1'), 'string'));
				$party->set_address_2($this->unmarshal($this->db->f('party_address_2'), 'string'));
				$party->set_comment($this->unmarshal($this->db->f('party_comment'), 'string'));
				$party->set_company_name($this->unmarshal($this->db->f('party_company_name'), 'string'));
				$party->set_department($this->unmarshal($this->db->f('party_department'), 'string'));
				$party->set_email($this->unmarshal($this->db->f('party_email'), 'string'));
				$party->set_fax($this->unmarshal($this->db->f('party_fax'), 'string'));
				$party->set_first_name($this->unmarshal($this->db->f('party_first_name'), 'string'));
				$party->set_is_inactive($this->unmarshal($this->db->f('party_in_active'), 'bool'));
				$party->set_last_name($this->unmarshal($this->db->f('party_last_name'), 'string'));
				$party->set_location_id($this->unmarshal($this->db->f('party_org_location_id'), 'int'));
				$party->set_identifier($this->unmarshal($this->db->f('party_identifier'), 'string'));
				$party->set_customer_id($this->unmarshal($this->db->f('customer_id'), 'int'));
				$party->set_mobile_phone($this->unmarshal($this->db->f('party_mobile_phone'), 'string'));
				$party->set_place($this->unmarshal($this->db->f('party_place'), 'string'));
				$party->set_postal_code($this->unmarshal($this->db->f('party_postal_code'), 'string'));
				$party->set_reskontro($this->unmarshal($this->db->f('party_reskontro'), 'string'));
				$party->set_title($this->unmarshal($this->db->f('party_title'), 'string'));
				$party->set_url($this->unmarshal($this->db->f('party_url'), 'string'));
				$invoice->set_party($party);

				if ($invoice->get_term_id() == 2)
				{ // yearly
					$invoice->set_term_label(lang('annually'));
				}
				else if ($invoice->get_term_id() == 3)
				{ // half year
					if ($invoice->get_month() == 6)
					{
						$invoice->set_term_label(lang('first_half'));
					}
					else
					{
						$invoice->set_term_label(lang('second_half'));
					}
				}
				else if ($invoice->get_term_id() == 4)
				{ // quarterly
					if ($invoice->get_month() == 3)
					{
						$invoice->set_term_label(lang('first_quarter'));
					}
					else if ($invoice->get_month() == 6)
					{
						$invoice->set_term_label(lang('second_quarter'));
					}
					else if ($invoice->get_month() == 9)
					{
						$invoice->set_term_label(lang('third_quarter'));
					}
					else
					{
						$invoice->set_term_label(lang('fourth_quarter'));
					}
				}
				else
				{
					$invoice->set_term_label(lang('month ' . $invoice->get_month() . ' capitalized'));
				}
			}
			$invoice->add_composite_name($this->unmarshal($this->db->f('composite_name'), 'string'));

			return $invoice;
		}

		public function add( &$invoice )
		{
			$values = array
				(
				$this->marshal($invoice->get_contract_id(), 'int'),
				$this->marshal($invoice->get_billing_id(), 'int'),
				$this->marshal($invoice->get_party_id(), 'int'),
				$this->marshal($invoice->get_timestamp_created(), 'int'),
				$this->marshal($invoice->get_timestamp_start(), 'int'),
				$this->marshal($invoice->get_timestamp_end(), 'int'),
				$this->marshal($invoice->get_total_sum(), 'float'),
				$this->marshal($invoice->get_total_area(), 'float'),
				$this->marshal($invoice->get_header(), 'string'),
				$this->marshal($invoice->get_account_in(), 'string'),
				$this->marshal($invoice->get_account_out(), 'string'),
				$this->marshal($invoice->get_service_id(), 'string'),
				$this->marshal($invoice->get_responsibility_id(), 'string'),
				$this->marshal($invoice->get_project_id(), 'string')
			);
			$query = "INSERT INTO rental_invoice(contract_id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end, total_sum, total_area, header, account_in, account_out, service_id, responsibility_id, project_id) VALUES (" . join(',', $values) . ")";
			$receipt = null;
			if ($this->db->query($query))
			{
				$receipt = array();
				$receipt['id'] = $this->db->get_last_insert_id('rental_invoice', 'id');
				$invoice->set_id($receipt['id']);
			}
			return $receipt;
		}

		public function update( $invoice )
		{
			$values = array(
				'contract_id = ' . $this->marshal($invoice->get_contract_id(), 'int'),
				'billing_id = ' . $this->marshal($invoice->get_billing_id(), 'int'),
				'party_id = ' . $this->marshal($invoice->get_party_id(), 'int'),
				'timestamp_created = ' . $this->marshal($invoice->get_timestamp_created(), 'int'),
				'timestamp_start = ' . $this->marshal($invoice->get_timestamp_start(), 'int'),
				'timestamp_end = ' . $this->marshal($invoice->get_timestamp_end(), 'int'),
				'total_sum = ' . $this->marshal($invoice->get_total_sum(), 'float'),
				'total_area = ' . $this->marshal($invoice->get_total_area(), 'float'),
				'header = ' . $this->marshal($invoice->get_header(), 'string'),
				'account_in = ' . $this->marshal($invoice->get_account_in(), 'string'),
				'account_out = ' . $this->marshal($invoice->get_account_out(), 'string'),
				'service_id = ' . $this->marshal($invoice->get_service_id(), 'string'),
				'responsibility_id = ' . $this->marshal($invoice->get_responsibility_id(), 'string'),
				'project_id = ' . $this->marshal($invoice->get_project_id(), 'string'),
				'serial_number = ' . $this->marshal($invoice->get_serial_number(), 'int')
			);
			$result = $this->db->query('UPDATE rental_invoice SET ' . join(',', $values) . " WHERE id=" . $invoice->get_id(), __LINE__, __FILE__);
		}

		public function get_max_serial_number_used( $serial_config_start, $serial_config_stop )
		{
			$query = "SELECT MAX(serial_number) AS max_serial FROM rental_invoice WHERE serial_number > ({$serial_config_start} - 1) AND serial_number < ({$serial_config_stop} + 1)";
			$this->db->query($query);
			if ($this->db->next_record())
			{
				return $this->unmarshal($this->db->f('max_serial', true), 'int');
			}
			return false;
		}

		public function number_of_open_and_exported_rental_billings( $location_id )
		{
			$query = "SELECT COUNT(id) AS open_and_exported FROM rental_billing WHERE export_data IS NOT NULL AND timestamp_commit IS NULL AND deleted IS FALSE AND location_id = {$location_id}";
			$this->db->query($query);
			if ($this->db->next_record())
			{
				return $this->unmarshal($this->db->f('open_and_exported', true), 'int');
			}
			return false;
		}
	}