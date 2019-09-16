<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_bogroup extends booking_bocommon_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sogroup');
			$this->org_bo = CreateObject('booking.boorganization');
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles( array $for_object = null )
		{
			$parent_roles = null;
			$parent_org = null;

			if (is_array($for_object))
			{
				if (!isset($for_object['organization_id']))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless organization_id is provided');
				}

				$parent_org = $this->org_bo->read_single($for_object['organization_id']);
			}

			//Note that a null value for $parent_org is acceptable. That only signifies
			//that any roles specified for any organization are returned rather than the roles 
			//for a specific organization
			$parent_roles['organization'] = $this->org_bo->get_subject_roles($parent_org);

			return $parent_roles;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT] = array
					(
					'read' => true,
					'delete' => true,
					'write' => true,
					'create' => true,
				);

				$defaultPermissions['parent_role_permissions']['organization'] = array();
			}

			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions['parent_role_permissions']['organization'][booking_boorganization::ROLE_ADMIN] = array
					(
					'write' => true,
					'create' => true,
				);
			}

			return $defaultPermissions;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions( $defaultPermissions )
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['create'] = true;
			}

			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions['parent_role_permissions']['organization'][booking_boorganization::ROLE_ADMIN]['create'] = true;
			}

			return $defaultPermissions;
		}

		function tree_walker( &$result, $children, $prefix, $node )
		{
			if (!$node['active'])
			{
				return;
			}
			$result[] = array('id' => $node['id'], 'name' => $prefix . $node['name']);
			foreach ($children[$node['id']] as $child)
			{
				$this->tree_walker($result, $children, $prefix . $node['name'] . ' / ', $child);
			}
		}

		function fetch_groups( $parent_id = 0, $organization_id )
		{
			$groups = $this->so->read(array('results' => -1, 'filters' => array('organization_id' => $organization_id )));
			$groups = $groups['results'];

			$children = array();
			foreach ($groups as $group)
			{
				$_group_parent_id = $group['parent_id'] ? $group['parent_id'] : null;
				if (!array_key_exists($group['id'], $children))
				{
					$children[$group['id']] = array();
				}
				if (!array_key_exists($_group_parent_id, $children))
				{
					$children[$_group_parent_id] = array();
				}
				$children[$_group_parent_id][] = $group;
			}
			$result = array();
			foreach ($children[null] as $child)
			{
				if ($parent_id && $child['id'] != $parent_id)
				{
					continue;
				}
				$this->tree_walker($result, $children, '', $child);
			}
			usort($result, 'node_sort');
			return array('results' => $result);
		}

		public function get_path( $id )
		{
			return $this->so->get_path($id);
		}

		public function get_children( $parent, $level = 0, $reset = false )
		{
			return $this->so->get_children($parent, $level, $reset);
		}


		public function get_children_detailed($parent)
		{
			return $this->so->get_children_detailed($parent);
		}


		public function get_parents( $organization_id, $id )
		{
			$parent_list = $this->so->read_tree2($organization_id);
			$exclude	 = array($id);
			$children	 = $this->so->get_children2( $id, 0, true);

			foreach ($children as $child)
			{
				$exclude[] = $child['id'];
			}

			$k = count($parent_list);
			for ($i = 0; $i < $k; $i++)
			{
				if (in_array($parent_list[$i]['id'], $exclude))
				{
					unset($parent_list[$i]);
				}
			}
			return $parent_list;
		}

	}