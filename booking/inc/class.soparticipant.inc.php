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
				'from_' => array('type' => 'timestamp', 'required' => False),
				'to_' => array('type' => 'timestamp', 'required' => False),
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
			if (empty($entity['from_']))
			{
				$entity['from_'] = date("Y-m-d H:i:s");
			}
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			$sql = "SELECT id"
				. " FROM bb_participant"
				. " WHERE reservation_type='{$entity['reservation_type']}'"
				. " AND reservation_id=" . (int) $entity['reservation_id']
				. " AND phone='{$entity['phone']}'";

			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$errors['phone'] = lang('Duplicate');
			}
		}

		function get_number_of_participants($reservation_type, $reservation_id)
		{
			$sql = "SELECT sum(quantity) as cnt"
				. " FROM bb_participant"
				. " WHERE reservation_type='{$reservation_type}'"
				. " AND reservation_id=" . (int) $reservation_id;
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return (int)$this->db->f('cnt');
		}
	}