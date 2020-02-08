<?php
	phpgw::import_class('booking.socommon');

	class booking_souser extends booking_socommon
	{

		function __construct()
		{
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$fields = array(
				'id'				 => array('type' => 'int'),
				'active'			 => array('type' => 'int', 'required' => true),
				'name'				 => array('type' => 'string', 'required' => True, 'query' => True),
				'homepage'			 => array('type' => 'string', 'required' => False, 'query' => True),
				'phone'				 => array('type' => 'string', 'query' => True),
				'email'				 => array('type' => 'string', 'query' => True, 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% is invalid'))),
				'street'			 => array('type' => 'string'),
				'zip_code'			 => array('type' => 'string'),
				'city'				 => array('type' => 'string'),
				'customer_number'	 => array('type' => 'string', 'required' => False),
				'customer_ssn'		 => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'),
					'required'		 => true)
			);

			parent::__construct('bb_user', $fields);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}


		function get_userid( $ssn )
		{
			$this->db->query("SELECT id FROM bb_user WHERE customer_ssn ='{$ssn}'", __LINE__, __FILE__);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id');
		}

		function get_applications( $ssn )
		{
			if(!$ssn)
			{
				return array();
			}

			$bouser = CreateObject('bookingfrontend.bouser');

			$customer_organization_number = $bouser->orgnr ? $bouser->orgnr : -1;

			$this->db->query("SELECT * FROM bb_application WHERE customer_ssn ='{$ssn}' OR customer_organization_number = '{$customer_organization_number}'", __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'							 => $this->db->f('id'),
					'created'						 => $this->db->f('created'),
					'building_name'					 => $this->db->f('building_name', true),
					'secret'						 => $this->db->f('secret', true),
					'customer_organization_number'	 => $this->db->f('customer_organization_number', true),
					'contact_name'					 => $this->db->f('contact_name', true),
				);
			}
			return $values;
		}

		function get_invoices( $ssn )
		{
			if(!$ssn)
			{
				return array();
			}

			$bouser = CreateObject('bookingfrontend.bouser');

			$customer_organization_number = $bouser->orgnr ? $bouser->orgnr : -1;

			$this->db->query("SELECT * FROM bb_completed_reservation WHERE (customer_ssn ='{$ssn}' OR customer_organization_number = '{$customer_organization_number}')"
			. " AND cost > 0", __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'							 => $this->db->f('id'),
					'description'					 => $this->db->f('description', true),
					'cost'							 => $this->db->f('cost'),
					'building_name'					 => $this->db->f('building_name', true),
					'article_description'			 => $this->db->f('article_description', true),
					'customer_organization_number'	 => $this->db->f('customer_organization_number', true),
					'exported'						 => $this->db->f('exported'),
				);
			}
			return $values;
		}
		function get_delegate( $ssn )
		{
			if(!$ssn)
			{
				return array();
			}

			$hash = sha1($ssn);
			$ssn =  '{SHA1}' . base64_encode($hash);


			$sql = "SELECT bb_organization.* FROM bb_delegate"
				. " JOIN bb_organization ON bb_delegate.organization_id = bb_organization.id"
				. " WHERE ssn = '{$ssn}'";


			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'							 => $this->db->f('id'),
					'name'							 => $this->db->f('name', true),
					'active'						 => $this->db->f('active'),
					'organization_number'			 => $this->db->f('organization_number', true),
				);
			}
			return $values;
		}


		protected function preValidate( &$entity )
		{
			if (!empty($entity['customer_ssn']))
			{
				$entity['customer_ssn'] = str_replace(" ", "", $entity['customer_ssn']);
			}
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			$this->db->query("SELECT id FROM bb_user WHERE customer_ssn = '{$entity['customer_ssn']}'", __LINE__, __FILE__);
			$this->db->next_record();
			$id = (int)$this->db->f('id');
			if ($id && $entity['id'] != $id)
			{
				$errors['ssn'] = lang('duplicate');
			}
		}

		function collect_users($ssn = '')
		{
			$sf_validator = createObject('booking.sfValidatorNorwegianSSN', array(), array(
				'invalid' => 'ssn is invalid'));
			$sf_validator->setOption('required', true);

			if($ssn)
			{
				$ssn = $this->db->db_addslashes($ssn);
				$filter_application = "bb_application.customer_ssn = '{$ssn}' AND bb_user.customer_ssn IS NULL";
				$filter_event = "bb_event.customer_ssn = '{$ssn}' AND bb_user.customer_ssn IS NULL";
			}
			else
			{
				$filter_application = "length(bb_application.customer_ssn) = 11 AND substring(bb_application.customer_ssn, 1, 4) != '0000' AND bb_user.customer_ssn IS NULL";
				$filter_event = "length(bb_event.customer_ssn) = 11 AND substring(bb_event.customer_ssn, 1, 4) != '0000'  AND bb_user.customer_ssn IS NULL";
			}

			$sql = "SELECT DISTINCT customer_ssn,  contact_name ,contact_email, contact_phone, responsible_street, responsible_zip_code, responsible_city FROM
				(
					SELECT  organizer, contact_name, contact_email, contact_phone, bb_application.customer_ssn, responsible_street, responsible_zip_code, responsible_city FROM bb_application
					LEFT JOIN bb_user ON bb_user.customer_ssn = bb_application.customer_ssn
					WHERE {$filter_application}
					UNION
					SELECT  organizer, contact_name, contact_email, contact_phone, bb_event.customer_ssn, null as responsible_street,  null as responsible_zip_code, null as responsible_city FROM bb_event
					LEFT JOIN bb_user ON bb_user.customer_ssn = bb_event.customer_ssn
					WHERE {$filter_event}
				)
				AS t ORDER BY customer_ssn, responsible_street";

			$this->db->query($sql, __LINE__, __FILE__);

			$users = array();

			while ($this->db->next_record())
			{
				$customer_ssn = $this->db->f('customer_ssn');
				try
				{
					$sf_validator->clean($customer_ssn);
				}
				catch (sfValidatorError $e)
				{
					continue;
				}

				$contact_info =  array(
					'contact_name' => !empty($users[$customer_ssn]['contact_name']) ? $users[$customer_ssn]['contact_name'] : $this->db->f('contact_name'),
					'contact_phone' => !empty($users[$customer_ssn]['contact_phone']) ? $users[$customer_ssn]['contact_phone'] : $this->db->f('contact_phone'),
					'contact_email' => !empty($users[$customer_ssn]['contact_email']) ? $users[$customer_ssn]['contact_email'] : $this->db->f('contact_email'),
					'responsible_street' => !empty($users[$customer_ssn]['responsible_street']) ? $users[$customer_ssn]['responsible_street'] : $this->db->f('responsible_street'),
					'responsible_zip_code' => !empty($users[$customer_ssn]['responsible_zip_code']) ? $users[$customer_ssn]['responsible_zip_code'] : $this->db->f('responsible_zip_code'),
					'responsible_city' => !empty($users[$customer_ssn]['responsible_city']) ? $users[$customer_ssn]['responsible_city'] : $this->db->f('responsible_city'),
				);

				$users[$customer_ssn] = $contact_info;
			}

			$valueset = array();
			foreach ($users as $ssn => $entry)
			{
				$valueset[] = array
					(
					1	 => array
					(
						'value'	 => $ssn,
						'type'	 => PDO::PARAM_STR
					),
					2	 => array
						(
						'value'	 => 1,
						'type'	 => PDO::PARAM_INT
					),
					3	 => array
						(
						'value'	 => $entry['contact_name'],
						'type'	 => PDO::PARAM_STR
					),
					4	 => array
						(
						'value'	 => $entry['contact_phone'],
						'type'	 => PDO::PARAM_STR
					),
					5	 => array
						(
						'value'	 => $entry['contact_email'],
						'type'	 => PDO::PARAM_STR
					),
					6	 => array
						(
						'value'	 => $entry['responsible_street'],
						'type'	 => PDO::PARAM_STR
					),
					7	 => array
						(
						'value'	 => $entry['responsible_zip_code'],
						'type'	 => PDO::PARAM_STR
					),
					8	 => array
						(
						'value'	 => $entry['responsible_city'],
						'type'	 => PDO::PARAM_STR
					)
				);

			}

			$sql = 'INSERT INTO bb_user (customer_ssn, active, name, phone, email,street, zip_code, city )'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?)';

			$receipt = array();
			if($valueset)
			{
				$this->db->insert($sql, $valueset, __LINE__, __FILE__);
				$receipt['message'][] = array('msg' => lang('added %1 users', count($valueset)));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang("Found none"));

			}
			return $receipt;
		}

		public function get_user_id( $ssn )
		{
			$ssn = $this->db->db_addslashes($ssn);

			$this->db->query("SELECT id FROM bb_user WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);
			if (!$this->db->next_record())
			{
				return False;
			}

			$user_id = (int)$this->db->f('id');

			return $user_id;
		}

		public function delete( $id )
		{
			$this->db->query("SELECT customer_ssn FROM bb_user WHERE customer_ssn != '00000000000' AND id = " . (int)$id, __LINE__, __FILE__);
			if (!$this->db->next_record())
			{
				return False;
			}

			$ssn = $this->db->f('customer_ssn');

			if(false)
			{
				/**
				 * Include age and gender
				 */
				$substitute_ssn = '0000' . substr($ssn, 4, 2) . '0000' .( substr($ssn, -1) & 1 );
			}
			else
			{
				/**
				 * Or just gender
				 */
				$substitute_ssn = '0000' . '00' . '0000' .( substr($ssn, -1) & 1 );
			}

			/**
			 * Bit operation
			 * $num = 9;                // 9 == 8 + 1 == 2^3 + 2^0 == 1001b
			 * echo (string)($num & 1); // 1001b & 0001b = 0001b - prints '1'
			 *
			 * $num = 10;               // 10 == 8 + 2 == 2^3 + 2^1 == 1010b
			 * echo (string)($num & 1); // 1010b & 0001b = 0000b - prints '0'
			 *
			 */


			$this->db->transaction_begin();

			$table				 = 'bb_user';
			$dataset			 = array(
				'active'			 => 0,
				'name'				 => 'Anonymisert: ' . date('Ymd'),
				'homepage'			 => '',
				'phone'				 => '',
				'email'				 => '',
				'street'			 => '',
				'customer_number'	 => '',
				'customer_ssn'		 => $substitute_ssn
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE id = " . (int)$id, __LINE__, __FILE__);

			$table				 = 'bb_application';
			$dataset			 = array(
				'organizer'			 => 'Anonymisert: ' . date('Ymd'),
				'contact_name'		 => 'Anonymisert: ' . date('Ymd'),
				'contact_email'		 => 'Anonymisert: ' . date('Ymd'),
				'contact_phone'		 => '00000000',
				'secret'			 => $GLOBALS['phpgw']->common->randomstring(32),
				'customer_ssn'		 => $substitute_ssn,
				'responsible_street' => '',
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);

			$table				 = 'bb_event';
			$dataset			 = array(
				'organizer'		 => 'Anonymisert: ' . date('Ymd'),
				'contact_name'	 => 'Anonymisert: ' . date('Ymd'),
				'contact_email'	 => 'Anonymisert: ' . date('Ymd'),
				'contact_phone'	 => '00000000',
				'customer_ssn'	 => $substitute_ssn,
				'secret'		 => $GLOBALS['phpgw']->common->randomstring(32),
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);

			$table				 = 'bb_completed_reservation';
			$dataset			 = array(
				'customer_ssn' => $substitute_ssn
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);

			$table				 = 'bb_contact_person';
			$dataset			 = array(
				'ssn'			 => $substitute_ssn,
				'name'			 => 'Anonymisert: ' . date('Ymd'),
				'homepage'		 => '',
				'phone'			 => '00000000',
				'email'			 => '',
				'description'	 => '',
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE ssn = '{$ssn}'", __LINE__, __FILE__);

			$table				 = 'bb_organization';
			$dataset			 = array(
				'customer_ssn' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);

			$this->db->transaction_commit();
		}

		public function get_customer_list( )
		{

			$sf_validator = createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array(
				'invalid' => 'ssn is invalid'));
			$sf_validator->setOption('required', true);

			$sql = "SELECT * FROM bb_user WHERE length(bb_user.customer_ssn) = 11 AND substring(bb_user.customer_ssn, 1, 4) != '0000'";
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{

				$values[] = array(
					'customer_ssn' => $this->db->f('customer_ssn'),
					'name' => $this->db->f('name', true),
					'phone' => $this->db->f('phone'),
					'email' => $this->db->f('email', true),
					'street' => $this->db->f('street', true),
					'zip_code' => $this->db->f('zip_code'),
					'city' => $this->db->f('city', true),
				);
			}

			$sql = "SELECT DISTINCT customer_organization_number,customer_ssn, name, phone, email, street, zip_code, city"
				. " FROM bb_organization WHERE length(bb_organization.customer_organization_number) = 9"
				. " AND active = 1";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$organization_number = $this->db->f('customer_organization_number');
				try
				{
					$sf_validator->clean($organization_number);
				}
				catch (sfValidatorError $e)
				{
					continue;
				}


				$values[] = array(
					'organization_number' => $organization_number,
					'customer_ssn' => $this->db->f('customer_ssn'),
					'name' => $this->db->f('name', true),
					'phone' => $this->db->f('phone'),
					'email' => $this->db->f('email', true),
					'street' => $this->db->f('street', true),
					'zip_code' => $this->db->f('zip_code'),
					'city' => $this->db->f('city', true),
				);
			}

			$lines = array();
			$counter = 1; // set to 1 initially to satisfy agresso requirements
			foreach ($values as $entry) // Runs through all parties
			{
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

				$lines[] = $this->get_line($entry['name'], $identifier, $entry['street'], $entry['city'], $country_code, $place, $entry['phone'], $entry['zip_code'] , $counter);
				$counter++;
			}

			$contents = implode("\n", $lines);

			return $contents;
		}


		/**
		 * Builds one single line of the Agresso file.

		 * @return string
		 */
		protected function get_line( $name, $identifier, $address1, $address2, $country_code, $postal_place, $phone, $postal_code, $counter )
		{
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			$line = '1'  //  1	full_record
				. 'I'  //  2	change_status
				. '10'  //  3	apar_gr_id
				. sprintf("%9s", $counter)   //  4	apar_id, sequence number, right justified
				. sprintf("%9s", '')  //  5	apar_id_ref
				. sprintf("%-50.50s", iconv("UTF-8", "ISO-8859-1", $name)) //  6	apar_name
				. 'R'  //  7	apar_type
				. sprintf("%-35s", '') //  8	bank_account
				. sprintf("%-4s", '') //  9	bonus_gr
				. sprintf("%3s", '')  // 10	cash_delay
				. sprintf("%-13s", '') // 11	clearing_code
				. 'BY'  // 12	client
				. sprintf("%1s", '')  // 13	collect_flag
				. sprintf("%-25.25s", $identifier) // 14	comp_reg_no
				. 'P'  // 15	control
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
				. 'IP'  // 29	pay_method
				. sprintf("%-13s", '') // 30	postal_acc
				. sprintf("%-1s", '') // 31	priority_no
				. sprintf("%-10s", '.') // 32	short_name
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