<?php
	phpgw::import_class('rental.socommon');
	
	class rental_sorentalcomposites extends rental_socommon
	{
		function __construct()
		{
			parent::__construct('rental_composite', 
				array(
					'composite_id'	=> array('type' => 'int'),
					'name'	=> array('type' => 'string'),
					'address_1'	=> array('type' => 'int'),
					/*'active'		=> array('type' => 'int', 'required'=>true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'group_id'		=> array('type' => 'int', 'required' => true),
					'from_'		=> array('type' => 'timestamp', 'required'=> true),
					'to_'		=> array('type' => 'timestamp', 'required'=> true),
					'season_id'		=> array('type' => 'int', 'required' => true),
					'group_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_group',
							'fkey' => 'group_id',
							'key' => 'id',
							'column' => 'name'
					))*/
					));
		}
	}
?>