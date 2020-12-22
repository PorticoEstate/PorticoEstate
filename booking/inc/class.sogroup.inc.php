<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');

	class booking_sogroup extends booking_socommon
	{

		private $group_tree = array();

		function __construct()
		{
			parent::__construct('bb_group', array(
				'id' => array('type' => 'int'),
				'active' => array('type' => 'int', 'required' => true),
				'show_in_portal' => array('type' => 'int', 'required' => true),
				'organization_id' => array('type' => 'int', 'required' => true),
				'parent_id' => array('type' => 'int', 'required' => false),
				'shortname' => array('type' => 'string', 'required' => False, 'query' => True),
				'description' => array('type' => 'string', 'query' => true, 'required' => false,),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'activity_id' => array('type' => 'int', 'required' => true),
				'activity_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'organization_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_organization',
						'fkey' => 'organization_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'contacts' => array(
					'type' => 'string',
					'manytomany' => array(
						'table' => 'bb_group_contact',
						'key' => 'group_id',
						'column' => array(
							'name',
							'email' => array('sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
									'invalid' => '%field% contains an invalid email'))),
							'phone')
					)
				)
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate( &$entity )
		{
			$errors = parent::validate($entity);
			# Detect and prevent loop creation
			$node_id = $entity['parent_id'];
			while ($entity['id'] && $node_id)
			{
				if ($node_id == $entity['id'])
				{
					$errors['parent_id'] = lang('Invalid parent activity');
					break;
				}
				$next = $this->read_single($node_id);
				$node_id = $next['parent_id'];
			}
			return $errors;
		}

		public function get_path( $id )
		{

			$sql = "SELECT name, parent_id FROM bb_group WHERE id =" . (int)$id;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$parent_id = $this->db->f('parent_id');

			$name = $this->db->f('name', true);

			$path = array(array('id' => $id, 'name' => $name));

			if ($parent_id)
			{
				$path = array_merge($this->get_path($parent_id), $path);
			}
			return $path;
		}

		public function get_children( $parent, $level = 0, $reset = false )
		{
			if ($reset)
			{
				$this->group_tree = array();
			}
			$parent = (int)$parent;
			$db = clone($this->db);
			$sql = "SELECT * FROM bb_group WHERE  parent_id = {$parent} ORDER BY name ASC";
			$db->query($sql, __LINE__, __FILE__);

			while ($db->next_record())
			{
				$id = $db->f('id');
				$this->group_tree[] = $id;
				$this->get_children($id, $level + 1);
			}
			return $this->group_tree;
		}

		/*
		 * Gets data on groups which are children of the given parent. Note that there is only one level of
		 * groups being fetched, ie. any children of a child activity are ignored and are not part of the return
		 * list
		 */
		public function get_children_detailed($parent)
		{
			$grouplist = array();
			$parent = (int)$parent;
			$sql = "SELECT * FROM bb_group WHERE parent_id={$parent} ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$grouplist[$id] = array('id' => $id, 'name' => $this->db->f('name'), 'active' => $this->db->f('active'));
			}
			return $grouplist;
		}




		function get_children2( $parent, $level, $reset = false )
		{
			if ($reset)
			{
				$this->group_tree = array();
			}
			$db		 = clone($this->db);
			$table	 = "bb_group";
			$sql	 = "SELECT id, name FROM {$table} WHERE  parent_id = {$parent} ORDER BY name ASC";
			$db->query($sql, __LINE__, __FILE__);

			while ($db->next_record())
			{
				$id	 = $db->f('id');
				$this->group_tree[]	 = array
					(
					'id'			 => $id,
					'name'			 => str_repeat('..', $level) . $db->f('name'),
					'parent_id'		 => $db->f('parent_id'),
				);
				$this->get_children2($id, $level + 1);
			}
			return $this->group_tree;
		}

		public function read_tree2( $organization_id )
		{
			$table	 = "bb_group";

			$sql = "SELECT id, name FROM $table WHERE organization_id = $organization_id AND (parent_id = 0 OR parent_id IS NULL) ORDER BY name ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			$this->group_tree = array();
			$groups = array();

			while ($this->db->next_record())
			{
				$groups[] = array
					(
					'id'			 => $this->db->f('id'),
					'name'			 => $this->db->f('name', true),
					'parent_id'		 => 0,
				);
			}

			foreach ($groups as $group)
			{
				$this->group_tree[] = array
					(
					'id'			 => $group['id'],
					'name'			 => $group['name'],
				);
				$this->get_children2( $group['id'], 1);
			}
			return $this->group_tree;
		}





		function get_metainfo( $id )
		{
			$this->db->limit_query("SELECT bg.name, bg.shortname, bo.name as organization, bo.district, bo.city, bg.description FROM bb_group as bg, bb_organization as bo where bg.organization_id=bo.id and bg.id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', false),
				'shortname' => $this->db->f('shortname', false),
				'organization' => $this->db->f('organization', false),
				'district' => $this->db->f('district', false),
				'city' => $this->db->f('city', false),
				'description' => $this->db->f('description', false));
		}

		/**
		 * Removes any extra contacts from entity if such exists (only two contacts allowed per group).
		 */
		protected function trim_contacts( &$entity )
		{
			if (isset($entity['contacts']) && is_array($entity['contacts']) && count($entity['contacts']) > 2)
			{
				$entity['contacts'] = array($entity['contacts'][0], $entity['contacts'][1]);
			}

			return $entity;
		}

		function add( $entity )
		{
			return parent::add($this->trim_contacts($entity));
		}

		function update( $entity )
		{
			return parent::update($this->trim_contacts($entity));
		}
	}