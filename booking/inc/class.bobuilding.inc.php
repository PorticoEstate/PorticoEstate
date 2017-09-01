<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_bobuilding extends booking_bocommon_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sobuilding');
		}

		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			return array_merge(
				array
				(
				booking_sopermission::ROLE_MANAGER => array
					(
					'write' => true,
				),
				booking_sopermission::ROLE_CASE_OFFICER => array
					(
					'write' => array_fill_keys(array('name', 'homepage', 'description', 'email',
						'phone', 'street', 'zip_code', 'city', 'district', 'deactivate_application',
						'deactivate_calendar', 'deactivate_sendmessage'), true),
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'read' => true,
						'write' => true,
						'create' => true,
						'delete' => true,
					),
				)
				), $defaultPermissions
			);
		}

		protected function get_collection_role_permissions( $defaultPermissions )
		{
			return array_merge(
				array(
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'create' => true, #means that this role may create new objects of the present type
						'delete' => true,
					),
				),
				), $defaultPermissions
			);
		}

		public function get_schedule( $id, $module )
		{
			$date = new DateTime(phpgw::get_var('date'));
			// Make sure $from is a monday
			if ($date->format('w') != 1)
			{
				$date->modify('last monday');
			}
			$prev_date = clone $date;
			$next_date = clone $date;
			$prev_date->modify('-1 week');
			$next_date->modify('+1 week');
			$building = $this->read_single($id);

			$building['buildings_link'] = self::link(array('menuaction' => $module . '.index'));
			$building['building_link'] = self::link(array('menuaction' => $module . '.show',
					'id' => $building['id']));
			$building['date'] = $date->format('Y-m-d');
			$building['week'] = intval($date->format('W'));
			$building['year'] = intval($date->format('Y'));
			$building['prev_link'] = self::link(array('menuaction' => $module . '.schedule',
					'id' => $building['id'], 'date' => $prev_date->format('Y-m-d')));
			$building['next_link'] = self::link(array('menuaction' => $module . '.schedule',
					'id' => $building['id'], 'date' => $next_date->format('Y-m-d')));
			for ($i = 0; $i < 7; $i++)
			{
				$building['days'][] = array('label' => sprintf('%s<br/>%s %s', lang($date->format('l')), lang($date->format('M')), $date->format('d')),
					'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			return $building;
		}

		/**
		 * @see sobuilding
		 */
		function find_buildings_used_by( $organization_id )
		{
			return $this->so->find_buildings_used_by($organization_id, $this->build_default_read_params());
		}
	}