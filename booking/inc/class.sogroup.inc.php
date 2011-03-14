<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');
	
	class booking_sogroup extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_group', 
				array(
					'id'			=> array('type' => 'int'),
					'active'		=> array('type' => 'int', 'required' => true),
					'show_in_portal'		=> array('type' => 'int', 'required'=>true),
					'organization_id'	=> array('type' => 'int', 'required' => true),
					'shortname'		=> array('type' => 'string', 'required' => False, 'query' => True),
					'description'    => array('type' => 'string', 'query' => true, 'required' => false,),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'activity_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_activity',
							'fkey' => 'activity_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
						)),
					'contacts'		=> array('type' => 'string',
						'manytomany' => array(
							'table' => 'bb_group_contact',
							'key' => 'group_id',
							'column' => array('name',
							                  'email' => array('sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% contains an invalid email'))),
							                  'phone')
						)
					),
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		/**
		 * Removes any extra contacts from entity if such exists (only two contacts allowed per group).
		 */
		protected function trim_contacts(&$entity)
		{
			if (isset($entity['contacts']) && is_array($entity['contacts']) && count($entity['contacts']) > 2)
			{	
				$entity['contacts'] = array($entity['contacts'][0], $entity['contacts'][1]);
			}
			
			return $entity;
		}

		function add($entity)
		{
			return parent::add($this->trim_contacts($entity));
		}
		
		function update($entity)
		{
			return parent::update($this->trim_contacts($entity));
		}
	}

