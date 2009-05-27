<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boorganization extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soorganization');
		}
		
        function get_groups($organization_id) {
            return $this->so->get_groups($organization_id);
        }
		
		/**
		 * Removes any extra contacts from entity if such exists (only two contacts allowed).
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
