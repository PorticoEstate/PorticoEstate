<?php
	phpgw::import_class('booking.socommon');

	class booking_sorescategory extends booking_socommon
	{

		protected $entity_tree;

		function __construct()
		{
			parent::__construct('bb_rescategory', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'required' => true, 'query' => true),
				'active' => array('type' => 'int', 'required' => true),
				'parent_id' => array('type' => 'int', 'required' => false),
				'capacity' => array('type' => 'int', 'required' => false),
				'e_lock' => array('type' => 'int', 'required' => false),
				'activities' => array('type' => 'string', 'required' => true,
					'manytomany' => array(
						'table' => 'bb_rescategory_activity',
						'key' => 'rescategory_id',
						'column' => 'activity_id'
					)),
				)
			);
			$this->activity_so = CreateObject('booking.soactivity');
		}


		protected function doValidate( $entity, booking_errorstack $errors )
		{
			set_time_limit(300);

			if (count($errors) > 0)
			{
				// Basic validation failed
				return;
			}
			
			// Check that selected activities are on the top level, and that there is at least one activity
			$count_activities = 0;
			$top_level_activities = $this->activity_so->get_top_level();
			foreach ($entity['activities'] as $activity_id)
			{
				$count_activities++;
				$verified = 0;
				foreach ($top_level_activities as $tlactivity)
				{
					if ($tlactivity['id'] == $activity_id)
					{
						$verified = 1;
						continue;
					}
				}
				if (!$verified)
				{
					$errors['activities'] = lang('Not a top level activity');
				}
			}
			if ($count_activities == 0)
			{
				$errors['activities'] = lang('At least one activity must be selected');
			}
		}


		/**
		 * Gets resource categories which belong to the given top level activities
		 *
		 * @param array activity_ids
		 * @return array resource categories
		 */
		function get_rescategories_by_activities($activity_ids = array())
		{
			$rescategories = array();
			if (count($activity_ids) == 0)
			{
				return $rescategories;
			}
			$sql = 'SELECT DISTINCT br.* FROM bb_rescategory br ' .
					'JOIN bb_rescategory_activity bra on bra.rescategory_id=br.id ' .
					'JOIN bb_activity ba on bra.activity_id=ba.id ' .
					'WHERE br.active=1 and ba.parent_id is NULL and bra.activity_id in (' . implode(',', $activity_ids) . ')' .
					'ORDER BY br.name';
			$this->db->query($sql, __LINE__, __FILE__);

			$map = array();
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$rescategory = array(
					'id'		 => $id,
					'name'		 => $this->db->f('name'),
					'capacity'	 => $this->db->f('capacity'),
					'e_lock'	 => $this->db->f('e_lock'),
				);

				$map[$id]		 = $rescategory;
			}


			$tree = $this->read_tree2();
			foreach ($tree as &$entry)
			{
				if(! in_array($entry['id'],array_keys($map )))
				{
					$entry['disabled'] = true;
				}
				else
				{
					$entry['capacity'] = $map[$entry['id']]['capacity'];
					$entry['e_lock'] = $map[$entry['id']]['e_lock'];
				}

			}
			return $tree;
		}


		public function get_path( $id )
		{

			$sql = "SELECT name, parent_id FROM bb_rescategory WHERE id =" . (int)$id;

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
				$this->entity_tree = array();
			}
			$parent = (int)$parent;
			$db = clone($this->db);
			$sql = "SELECT * FROM bb_rescategory WHERE  parent_id = {$parent} ORDER BY name ASC";
			$db->query($sql, __LINE__, __FILE__);

			while ($db->next_record())
			{
				$id = $db->f('id');
				$this->entity_tree[] = $id;
				$this->get_children($id, $level + 1);
			}
			return $this->entity_tree;
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
			$sql = "SELECT * FROM bb_rescategory WHERE parent_id={$parent} ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$grouplist[$id] = array('id' => $id, 'name' => $this->db->f('name'), 'active' => $this->db->f('active'));
			}
			return $grouplist;
		}




		function get_children2( $parent_id, $level, $reset = false )
		{
			if ($reset)
			{
				$this->entity_tree = array();
			}
			$db		 = clone($this->db);
			$table	 = "bb_rescategory";
			$sql	 = "SELECT id, name FROM {$table} WHERE  parent_id = {$parent_id} ORDER BY name ASC";
			$db->query($sql, __LINE__, __FILE__);

			while ($db->next_record())
			{
				$id	 = $db->f('id');
				$this->entity_tree[]	 = array(
					'id'			 => $id,
					'name'			 => str_repeat('..', $level) . $db->f('name'),
					'parent_id'		 => $parent_id,
				);
				$this->get_children2($id, $level + 1);
			}
			return $this->entity_tree;
		}

		public function read_tree2( )
		{
			$table	 = "bb_rescategory";

			$sql = "SELECT id, name FROM $table WHERE parent_id = 0 OR parent_id IS NULL ORDER BY name ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			$this->entity_tree = array();
			$entries = array();

			while ($this->db->next_record())
			{
				$entries[] = array(
					'id'			 => $this->db->f('id'),
					'name'			 => $this->db->f('name', true),
					'parent_id'		 => 0,
				);
			}

			foreach ($entries as $entry)
			{
				$this->entity_tree[] = array
				(
					'id'			 => $entry['id'],
					'name'			 => $entry['name'],
					'parent_id'		 => 0,
				);
				$this->get_children2( $entry['id'], 1);
			}
			return $this->entity_tree;
		}


	}
