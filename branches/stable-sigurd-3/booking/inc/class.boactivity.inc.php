<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');
	
	
	function node_sort($a, $b)
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

		function tree_walker(&$result, $children, $prefix, $node)
		{
			if(!$node['active'])
				return;
			$result[] = array('id' => $node['id'], 'name' => $prefix . $node['name']);
			foreach($children[$node['id']] as $child)
			{
				$this->tree_walker($result, $children, $prefix . $node['name'] . ' / ', $child);
			}
		}

		function fetch_activities()
		{
			$activities = $this->so->read(array());
			$activities = $activities['results'];

			$children = array();
			foreach($activities as $activity)
			{
				if(!array_key_exists($activity['id'], $children)) {
					$children[$activity['id']] = array();	
				}
				if(!array_key_exists($activity['parent_id'], $children)) {
					$children[$activity['parent_id']] = array();	
				}				
				$children[$activity['parent_id']][] = $activity;
			}
			$result = array();
			foreach($children[null] as $child)
			{
				$this->tree_walker($result, $children, '', $child);
			}
			usort($result, 'node_sort');
			return array('results' => $result);
		}

		function get_activity($id)
		{
			return $this->activity_so->read_single($id);
		}
	}
