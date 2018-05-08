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
			else
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

	}
