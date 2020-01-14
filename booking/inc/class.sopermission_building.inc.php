<?php
	phpgw::import_class('booking.sopermission');

	class booking_sopermission_building extends booking_sopermission
	{

		public function get_roles_at_building( $building_id )
		{
			$building_id = (int)$building_id;

			$sql = "SELECT subject_id AS user_id, role FROM bb_permission"
				. " WHERE object_id ={$building_id} AND object_type = 'building'";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array(
					'user_id'	=> $this->db->f('user_id'),
					'role'	=> $this->db->f('role'),
				);
			}
			return $values;
		}
		
	}