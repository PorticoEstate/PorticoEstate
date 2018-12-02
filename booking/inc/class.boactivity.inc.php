<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	function node_sort( $a, $b )
	{
		return strcmp($a['name'], $b['name']);
	}

	class booking_boactivity extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soactivity');
		}

		function tree_walker( &$result, $children, $prefix, $node )
		{
			if (!$node['active'])
				return;
			$result[] = array('id' => $node['id'], 'name' => $prefix . $node['name']);
			foreach ($children[$node['id']] as $child)
			{
				$this->tree_walker($result, $children, $prefix . $node['name'] . ' / ', $child);
			}
		}

		function fetch_activities( $parent_id = 0 )
		{
			$activities = $this->so->read(array());
			$activities = $activities['results'];

			$children = array();
			foreach ($activities as $activity)
			{
				if (!array_key_exists($activity['id'], $children))
				{
					$children[$activity['id']] = array();
				}
				if (!array_key_exists($activity['parent_id'], $children))
				{
					$children[$activity['parent_id']] = array();
				}
				$children[$activity['parent_id']][] = $activity;
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


		function fetch_activities_hierarchy()
		{
			$activitylist = array();
			foreach ($this->get_top_level() as $toplevel)
			{
				$id = $toplevel['id'];
				$activitylist[$id] = $toplevel;
			}
			foreach ($activitylist as &$toplevel)
			{
				$toplevel['children'] = $this->get_children_detailed($toplevel['id']);
			}
			unset($toplevel);
			return $activitylist;
		}


		function get_activity( $id )
		{
			return $this->activity_so->read_single($id);
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


		public function get_top_level( $selected = 0 )
		{
			$values = $this->so->get_top_level();
			if ($selected)
			{
				foreach ($values as &$entry)
				{
					$entry['selected'] = $entry['id'] == $selected ? 1 : 0;
				}
			}
			return $values;
		}

		public function populate_grid_data($filter_top_level = 0)
		{
			$parent_id = 0;
			if (phpgw::get_var('filter_top_level', 'int', 'REQUEST', 0) == 1)
			{
				$filter_top_level = 1;
			}
			else
			{
				$parent_id = phpgw::get_var('parent_id', 'int', 'REQUEST', 0);
			}
			$activities = $this->read();

			$results = array();
			$total_records = 0;
			foreach ($activities['results'] as $activity) {
				if ($filter_top_level && $activity['parent_id'] != null)
				{
					continue;
				}
				if ($parent_id > 0 && $activity['parent_id'] != $parent_id)
				{
					continue;
				}
				$results[] = $activity;
				$total_records++;
			}

			$data = array(
				'total_records' => $total_records,
				'start' => $activities['start'],
				'sort' => $activities['sort'],
				'dir' => $activities['dir'],
				'results' => $results,
			);
			return $data;
		}

}
