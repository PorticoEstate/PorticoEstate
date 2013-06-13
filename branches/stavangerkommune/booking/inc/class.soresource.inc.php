<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soresource extends booking_socommon
	{
		const TYPE_LOCATION = 'Location';
		const TYPE_EQUIPMENT = 'Equipment';
		
		function __construct()
		{
			parent::__construct('bb_resource', 
				array(
					'id'			=> array('type' => 'int'),
					'active'		=> array('type' => 'int', 'required' => true),
					'sort'			=> array('type' => 'int', 'required' => false),
					'building_id'	=> array('type' => 'int', 'required' => true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'type'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'description'			=> array('type' => 'string', 'query' => true, 'required' => false),
					'activity_id'			=> array('type' => 'int', 'required' => false),
					'building_name'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'building_street'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'street'
					)),
					'building_city'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'city'
					)),
					'building_district'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'district'
					)),
					'activity_name'	=> array('type' => 'string', 'query' => true, 
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					))
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_metainfo($id)
		{
			$this->db->limit_query("SELECT br.name, bb.name as building, bb.city, bb.district, br.description FROM bb_resource as br, bb_building as bb where br.building_id=bb.id and br.id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', false),
						  'building' => $this->db->f('building', false),
						  'district' => $this->db->f('district', false),
						  'city' => $this->db->f('city', false),
						  'description' => $this->db->f('description', false));
		}
		
		public static function allowed_types()
		{
			return array(self::TYPE_LOCATION, self::TYPE_EQUIPMENT);
		}
		
		function doValidate($entity, booking_errorstack $errors)
		{
			parent::doValidate($entity, $errors);
			if (!isset($errors['type']) && !in_array($entity['type'], self::allowed_types(), true)) {
				$errors['type'] = lang('Invalid Resource Type');
			}
		}
	}
