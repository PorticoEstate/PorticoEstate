<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	class booking_borescategory extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sorescategory');
			$this->activity_bo = CreateObject('booking.boactivity');
		}


		public function populate_grid_data ($params)
		{
			$rescategories = $this->so->read($params);
			$activities = $this->list_id_name($this->activity_bo->get_top_level());

			foreach ($rescategories['results'] as &$rescategory)
			{
				if($rescategory['parent_id'])
				{
					$path_array = $this->get_path($rescategory['id']);

					$entity_path = array();
					foreach ($path_array as $path_element)
					{
						$entity_path[] = $path_element['name'];
					}

					$rescategory['name'] = implode(' ::/:: ', $entity_path);
				}

				$_activity_names = array();
				if (is_array($rescategory['activities']))
				{
					foreach ($rescategory['activities'] as $activity_id)
					{
						$_activity_names[] = $activities[$activity_id];
					}
				}
				sort($_activity_names);
				$rescategory['activities_name'] = implode(', ', $_activity_names);
			}

			$data = array(
				'total_records' => $rescategories['total_records'],
				'start' => $rescategories['start'],
				'sort' => $rescategories['sort'],
				'dir' => $rescategories['dir'],
				'results' => $rescategories['results'],
			);

			return $data;
		}
		

		function get_rescategories_by_activities($activity_ids = null)
		{
			$idlist = array();
			if (is_array($activity_ids))
			{
				$idlist = $activity_ids;
			}
			else if($activity_ids)
			{
				$idlist[] = $activity_ids;
			}
			$rescategories = $this->so->get_rescategories_by_activities($idlist);
			return $rescategories;
		}


		function list_id_name($results = [])
		{
			$list = array();
			foreach ($results as $res)
			{
				$list[$res['id']] = $res['name'];
			}
			return $list;
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


		public function get_parents( $id = 0)
		{
			$exclude = array();
			$children = array();
			$parent_list = $this->so->read_tree2();
			if($id)
			{
				$exclude	 = array($id);
				$children	 = $this->so->get_children2( $id, 0, true);
			}

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
