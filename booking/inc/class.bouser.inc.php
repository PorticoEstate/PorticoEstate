<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_bouser extends booking_bocommon_authorized
	{

		const ROLE_ADMIN = 'user_admin';

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.souser');
		}

		public function anonymisation( $id )
		{
			return $this->so->anonymisation($id);
		}

		/**
		 * @see booking_bocommon_authorized
		 */
		protected function get_subject_roles( $for_object = null, $initial_roles = array() )
		{
			if ($this->current_app() == 'bookingfrontend')
			{

				$bouser = CreateObject('bookingfrontend.bouser');

				$external_login_info = $bouser->validate_ssn_login( array
				(
					'menuaction' => 'bookingfrontend.uiuser.edit'
				));

				if(!empty($external_login_info['ssn']) && $external_login_info['ssn'] == $for_object['customer_ssn'])
				{
					$initial_roles[] = array('role' => self::ROLE_ADMIN);
				}
			}

			return parent::get_subject_roles($for_object, $initial_roles);
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT] = array
					(
					'read' => true,
					'delete' => true,
					'write' => true,
					'create' => true,
				);
			}

			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions[self::ROLE_ADMIN] = array
				(
					'write' => array_fill_keys(array('name', 'homepage', 'phone', 'email', 'description',
						'street', 'zip_code', 'district', 'city', 'active', 'user_number',
						'contacts'), true),
					'create' =>  true,
				);
			}

			return $defaultPermissions;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions( $defaultPermissions )
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['create'] = true;
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['write'] = true;
			}

			return $defaultPermissions;
		}

		public function get_permissions( array $entity )
		{
			return parent::get_permissions($entity);
		}

		/**
		 * Removes any extra contacts from entity if such exists (only two contacts allowed).
		 */
		protected function trim_contacts( &$entity )
		{
			if (isset($entity['contacts']) && is_array($entity['contacts']) && count($entity['contacts']) > 2)
			{
				$entity['contacts'] = array($entity['contacts'][0], $entity['contacts'][1]);
			}

			return $entity;
		}

		function add( $entity )
		{
			return parent::add($this->trim_contacts($entity));
		}

		function update( $entity )
		{
			return parent::update($this->trim_contacts($entity));
		}

		/**
		 * Used?????
		 * @see souser
		 */
		function find_building_users( $building_id, $split = false, $activities = array() )
		{
			return $this->so->find_building_users($building_id, $this->build_default_read_params(), $split, $activities);
		}

		function collect_users()
		{
			return $this->so->collect_users();
		}

		public function get_customer_list()
		{
			$config		 = CreateObject('phpgwapi.config', 'booking')->read();
			$customers	 = $this->so->get_customer_list();

			if ($config['external_format'] == 'AGRESSO')
			{
				$agresso_cs15 = new agresso_cs15($config);
				return $agresso_cs15->get_customer_list($customers);
			}
		}
	}

	class agresso_cs15
	{
		private $client, $apar_gr_id, $pay_method;


		public function __construct($config)
		{
			$this->client = !empty($config['voucher_client']) ? $config['voucher_client'] : 'BY';
			$this->apar_gr_id = !empty($config['apar_gr_id']) ? $config['apar_gr_id'] : '10';
			$this->pay_method = !empty($config['pay_method']) ? $config['pay_method'] : 'IP';//'BG';//'IP' 
		}


		public function get_customer_list( $customers )
		{
			$lines = array();
			$counter = 1; // set to 1 initially to satisfy agresso requirements
			foreach ($customers as $entry) // Runs through all parties
			{
				if(empty($entry['zip_code']))
				{
					phpgwapi_cache::message_set("{$entry['name']} mangler PostNr", 'error');
					continue;
				}

				$country_code = 'NO';
				$place = '';
				// TODO: Which standard for the country codes does Agresso follow?
				if ($country_code != 'NO' && $country_code != 'SV' && $country_code != 'IS') // Shouldn't get postal place for Norway, Sweden and Iceland
				{
					/**
					 * Not implemented
					 */
					//$this->get_postal_place();
				}


				$identifier = $entry['organization_number'] ? $entry['organization_number'] : $entry['customer_ssn'];
				$customer_type = $entry['organization_number'] ? 'C' : 'P';
				/**
				 *	C - Company
				 *	P - Private
				 *	B - Both
				 */


				$lines[] = $this->get_line_agresso_cs15($entry['name'], $identifier, $entry['street'], $entry['city'], $country_code, $place, $entry['phone'], $entry['zip_code'] , $counter, $customer_type);
				$counter++;
			}

			$contents = implode("\n", $lines);

			return $contents;


		}
		/**
		 * Builds one single line of the Agresso file.

		 * @return string
		 */
		protected function get_line_agresso_cs15( $name, $identifier, $address1, $address2, $country_code, $postal_place, $phone, $postal_code, $counter, $customer_type )
		{
			// muligens format 52
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			$line = '1'  //  1	full_record
				. 'I'  //  2	change_status
				. sprintf("%-2s", $this->apar_gr_id)  //  3	apar_gr_id, Gyldig reskontrogruppe i Agresso.Hvis blank benyttes parameterverdi.
				. sprintf("%9s", $counter)   //  4	apar_id, sequence number, right justified
				. sprintf("%9s", '')  //  5	apar_id_ref
				. sprintf("%-50.50s", iconv("UTF-8", "ISO-8859-1", $name)) //  6	apar_name
				. 'R'  //  7	apar_type (key):P - Supplier (Payable)R - Customer (Receivable
				. sprintf("%-35s", '') //  8	bank_account
				. sprintf("%-4s", '') //  9	bonus_gr
				. sprintf("%3s", '')  // 10	cash_delay
				. sprintf("%-13s", '') // 11	clearing_code
				. sprintf("%-2s", $this->client) // 12	client
				. sprintf("%1s", '')  // 13	collect_flag
				. sprintf("%-25.25s", $identifier) // 14	comp_reg_no
				. $customer_type  // 15	control
				. sprintf("%20s", '') // 16	credit_limit
				. 'NOK'  // 17	NOK
				. sprintf("%1s", '')  // 18	currency_set
				. sprintf("%-4s", '') // 19	disc_code
				. sprintf("%-15s", '') // 20	ext_apar_ref
				. sprintf("%-8s", '') // 21	factor_short
				. sprintf("%-35s", '') // 22	foreign_acc
				. sprintf("%-6s", '') // 23	int_rule_id
				. sprintf("%-12s", '') // 24	invoice_code
				. 'NO'  // 25	language
				. sprintf("%9s", '')  // 26	main_apar_id
				. sprintf("%-80s", '') // 27	message_text
				. sprintf("%3s", '')  // 28	pay_delay
				. sprintf("%-2s", $this->pay_method) // 29	pay_method
				. sprintf("%-13s", '') // 30	postal_acc
				. sprintf("%-1s", '') // 31	priority_no
				. sprintf("%-10s", '') // 32	short_name
				. 'N'  // 33	status
				. sprintf("%-11s", '') // 34	swift
				. sprintf("%-1s", '') // 35	tax_set
				. sprintf("%-2s", '') // 36	tax_system
				. sprintf("%-2s", '') // 37	terms_id
				. sprintf("%-1s", '') // 38	terms_set
				. sprintf("%-25s", '') // 39	vat_reg_no
				. sprintf("%-40.40s", iconv("UTF-8", "ISO-8859-1", $address1)) // 40	address1
				. sprintf("%-40.40s", iconv("UTF-8", "ISO-8859-1", $address2)) // 40	address2
				. sprintf("%-40.40s", '')   // 40	address3
				. sprintf("%-40.40s", '')   // 40	address4
				. '1'  // 41	address_type
				. sprintf("%-6s", '') // 42	agr_user_id
				. sprintf("%-255s", '') // 43	cc_name
				. sprintf("%-3.3s", $country_code) // 44	country_code
				. sprintf("%-50s", '') // 45	description
				. sprintf("%-40.40s", iconv("UTF-8", "ISO-8859-1", $postal_place)) // 46	place
				. sprintf("%-40s", '') // 47	province
				. sprintf("%-35.35s", $phone)  // 48	telephone_1
				. sprintf("%-35s", '') // 49	telephone_2
				. sprintf("%-35s", '') // 50	telephone_3
				. sprintf("%-35s", '') // 51	telephone_4
				. sprintf("%-35s", '') // 52	telephone_5
				. sprintf("%-35s", '') // 53	telephone_6
				. sprintf("%-35s", '') // 54	telephone_7
				. sprintf("%-255s", '') // 55	to_name
				. sprintf("%-15.15s", $postal_code) // 56	zip_code
				. sprintf("%-50s", '') // 57	e_mail
				. sprintf("%-35s", '') // 58	pos_title
				. sprintf("%-4s", '') // 59	pay_temp_id
				. sprintf("%-25s", '') // 60	reference_1
			;

			return str_replace(array("\n", "\r"), '', $line);
		}

	}