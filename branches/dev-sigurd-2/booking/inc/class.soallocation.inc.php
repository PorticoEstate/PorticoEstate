<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soallocation extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_allocation', 
				array(
					'id'			=> array('type' => 'int'),
					'id'			=> array('type' => 'int'),
					'organization_id'		=> array('type' => 'int', 'required' => true),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'building_id'	=> array('type' => 'string',
						  'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'building_id'
					)),
					'season_id'		=> array('type' => 'int', 'required' => 'true'),
					'season_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_allocation_resource',
							'key' => 'allocation_id',
							'column' => 'resource_id'
					)),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true)
				)
			);
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);
			// FIXME: Validate: Season contains all resources
			// FIXME: Validate: Season from <= date, season to >= date
			// Make sure to_ > from_
			if(!$errors)
			{
				$from_ = date_parse($entity['from_']);
				$to_ = date_parse($entity['to_']);
				if($from_ > $to_)
				{
					$errors['from_'] = 'Invalid from date';
				}
			}
			return $errors;
		}
	}
