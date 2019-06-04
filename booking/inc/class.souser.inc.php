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
			$this->db->query("SELECT id FROM bb_user where customer_ssn ='{$ssn}'", __LINE__, __FILE__);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id');
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

		function collect_users()
		{
			$ssn = (string)$_SERVER['HTTP_UID'];
			$sf_validator = createObject('booking.sfValidatorNorwegianSSN', array(), array(
				'invalid' => 'ssn is invalid'));
			$sf_validator->setOption('required', true);


			$sql = "SELECT DISTINCT customer_ssn,  contact_name ,contact_email, contact_phone, responsible_street, responsible_zip_code, responsible_city FROM
				(
					SELECT  organizer, contact_name, contact_email, contact_phone, customer_ssn, responsible_street, responsible_zip_code, responsible_city FROM bb_application WHERE length(customer_ssn) = 11 AND substring(customer_ssn, 1, 4) != '0000'
					UNION
					SELECT  organizer, contact_name, contact_email, contact_phone, customer_ssn, null as responsible_street,  null as responsible_zip_code, null as responsible_city FROM bb_event WHERE length(customer_ssn) = 11 AND substring(customer_ssn, 1, 4) != '0000'
				)
				AS t ORDER BY customer_ssn, responsible_street";


			$sql = "SELECT DISTINCT customer_ssn,  contact_name ,contact_email, contact_phone, responsible_street, responsible_zip_code, responsible_city FROM
				(
					SELECT  organizer, contact_name, contact_email, contact_phone, bb_application.customer_ssn, responsible_street, responsible_zip_code, responsible_city FROM bb_application
					LEFT JOIN bb_user ON bb_user.customer_ssn = bb_application.customer_ssn
					WHERE length(bb_application.customer_ssn) = 11 AND substring(bb_application.customer_ssn, 1, 4) != '0000' AND bb_user.customer_ssn IS NULL
					UNION
					SELECT  organizer, contact_name, contact_email, contact_phone, bb_event.customer_ssn, null as responsible_street,  null as responsible_zip_code, null as responsible_city FROM bb_event
					LEFT JOIN bb_user ON bb_user.customer_ssn = bb_event.customer_ssn
					WHERE length(bb_event.customer_ssn) = 11 AND substring(bb_event.customer_ssn, 1, 4) != '0000'  AND bb_user.customer_ssn IS NULL
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
	}