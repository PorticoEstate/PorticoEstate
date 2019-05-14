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
				'phone'				 => array('type' => 'string'),
				'email'				 => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
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
			$this->db->query("SELECT count(id) AS cnt FROM bb_user WHERE customer_ssn = '{$entity['customer_ssn']}'", __LINE__, __FILE__);
			$this->db->next_record();
			$cnt = (int)$this->db->f('cnt');
			if ($cnt > 0)
			{
				$errors['ssn'] = lang('duplicate');
			}
		}

		public function delete( $id )
		{
			$this->db->query("SELECT customer_ssn FROM bb_user WHERE customer_ssn != '00000000000' AND id = " . (int)$id, __LINE__, __FILE__);
			if (!$this->db->next_record())
			{
				return False;
			}

			$ssn = $this->db->f('customer_ssn');

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
				'customer_ssn'		 => '00000000000'
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
				'customer_ssn'		 => '00000000000',
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
				'customer_ssn'	 => '00000000000',
				'secret'		 => $GLOBALS['phpgw']->common->randomstring(32),
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);

			$table				 = 'bb_completed_reservation';
			$dataset			 = array(
				'customer_ssn' => '00000000000'
			);
			$value_set_update	 = $this->db->validate_update($dataset);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE customer_ssn = '{$ssn}'", __LINE__, __FILE__);

			$table				 = 'bb_contact_person';
			$dataset			 = array(
				'ssn'			 => '00000000000',
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