<?php
	phpgw::import_class('booking.socommon');

	class booking_soparticipant extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_participant', array(
				'id' => array('type' => 'int'),
				'reservation_type' => array('type' => 'string', 'required' => True, 'nullable' => False),
				'reservation_id' => array('type' => 'int', 'required' => True, 'nullable' => False),
				'from_' => array('type' => 'timestamp', 'required' => False,'read_callback' => 'modify_by_timezone'),
				'to_' => array('type' => 'timestamp', 'required' => False, 'read_callback' => 'modify_by_timezone'),
				'phone' => array('type' => 'string', 'query' => true, 'required' => true),
				'email' => array('type' => 'string', 'required' => False,'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% is invalid'))),
				'quantity' => array('type' => 'int', 'query' => false, 'required' => true),
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		protected function preValidate( &$entity )
		{
			if ($entity['register_type'] == 'register_in')
			{
				$entity['from_'] = date("Y-m-d H:i:s");
			}
			if ($entity['register_type'] == 'register_out')
			{
				if (empty($entity['from_']))
				{
					$entity['from_'] = date("Y-m-d H:i:s");
				}
				$entity['to_'] = date("Y-m-d H:i:s");
			}
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{

			$filter = "";

			if ($entity['register_type'] == 'register_pre')
			{
				$filter = " AND from_ IS NULL AND to_ IS NULL";
			}

			if ($entity['register_type'] == 'register_in')
			{
				$filter = " AND from_ IS NOT NULL AND to_ IS NULL";
			}

			if ($entity['register_type'] == 'register_out')
			{
	//			$filter = " AND from_ IS NOT NULL AND to_ IS NOT NULL";
				$filter = " AND 1=2";
			}

			$sql = "SELECT id"
				. " FROM bb_participant"
				. " WHERE reservation_type='{$entity['reservation_type']}'"
				. " AND reservation_id=" . (int) $entity['reservation_id']
				. " AND phone='{$entity['phone']}' {$filter}";

			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$errors['phone'] = lang('Duplicate');
			}
		}

		function get_previous_registration($reservation_type, $reservation_id, $phone, $register_type)
		{
			$sql = "SELECT id, email, from_, to_, quantity"
				. " FROM bb_participant"
				. " WHERE reservation_type='{$reservation_type}'"
				. " AND reservation_id=" . (int) $reservation_id
				. " AND phone LIKE '%{$phone}'"
				. " ORDER BY id DESC";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$id			 = $this->db->f('id');
			$to_		 = $this->db->f('to_');
			$from_		 = $this->db->f('from_');
			$quantity	 = $this->db->f('quantity');

			/**
			 * Re-entry to the event
			 */
			if(($register_type == 'register_pre' || $register_type == 'register_in') && $to_)
			{
				$id = null;
				$to_ = null;
				$quantity = null;

				if($register_type == 'register_pre')
				{
					$from_ = null;
				}
			}

			return array(
				'id'		 => $id,
				'email'		 => $this->db->f('email'),
				'quantity'	 => $quantity,
				'from_'		 => $from_,
				'to_'		 => $to_
			);
		}

		function get_number_of_participants($reservation_type, $reservation_id)
		{
			$sql = "SELECT sum(quantity) as cnt"
				. " FROM bb_participant"
				. " WHERE reservation_type='{$reservation_type}'"
				. " AND reservation_id=" . (int) $reservation_id
				. " AND to_ IS NULL";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return (int)$this->db->f('cnt');
		}
		
		function delete_from_completed($reservation_type, $age_days)
		{
			$interval = (int) $age_days;
			
			switch ($reservation_type)
			{
				case 'event':
				case 'booking':
				case 'allocation':
					$sql = "DELETE FROM bb_participant WHERE reservation_type = '{$reservation_type}' AND reservation_id IN"
					. " (SELECT id FROM bb_{$reservation_type}"
					. " WHERE to_ IS NOT NULL"
					. " AND to_ <  (now() - '{$interval} days'::interval))";

					$this->db->query($sql, __LINE__, __FILE__);

					break;

				default:
					break;
			}
			
		}

	}