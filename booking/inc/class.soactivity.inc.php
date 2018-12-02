<?php
	phpgw::import_class('booking.socommon');

	class booking_soactivity extends booking_socommon
	{

		private $activity_tree = array();

		function __construct()
		{
			parent::__construct('bb_activity', array(
				'id' => array('type' => 'int'),
				'parent_id' => array('type' => 'int', 'required' => false),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'description' => array('type' => 'string', 'query' => true),
				'active' => array('type' => 'int', 'required' => true)
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate( $entity )
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

			$sql = "SELECT name, parent_id FROM bb_activity WHERE id =" . (int)$id;

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

		public function get_top_level()
		{

			$sql = "SELECT name, id, active FROM bb_activity WHERE parent_id = 0 OR parent_id IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);
			$values = array();

			while ($this->db->next_record())
			{
				$values[] = array(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name', true),
					'active' => $this->db->f('active'),
				);
			}
			return $values;
		}

		public function get_children( $parent, $level = 0, $reset = false )
		{
			if ($reset)
			{
				$this->activity_tree = array();
			}
			$parent = (int)$parent;
			$db = clone($this->db);
			$sql = "SELECT * FROM bb_activity WHERE  parent_id = {$parent} ORDER BY name ASC";
			$db->query($sql, __LINE__, __FILE__);

			while ($db->next_record())
			{
				$id = $db->f('id');
				$this->activity_tree[] = $id;
				$this->get_children($id, $level + 1);
			}
			return $this->activity_tree;
		}


		/*
		 * Gets data on activities which are children of the given parent. Note that there is only one level of
		 * activities being fetched, ie. any children of a child activity are ignored and are not part of the return
		 * list
		 */
		public function get_children_detailed($parent)
		{
			$activitylist = array();
			$parent = (int)$parent;
			$sql = "SELECT * FROM bb_activity WHERE parent_id={$parent} ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$activitylist[$id] = array('id' => $id, 'name' => $this->db->f('name'), 'active' => $this->db->f('active'));
			}
			return $activitylist;
		}

	}
