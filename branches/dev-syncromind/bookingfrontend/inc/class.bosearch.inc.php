<?php
	phpgw::import_class('booking.bocommon');

	class bookingfrontend_bosearch extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->sobuilding = CreateObject('booking.sobuilding');
			$this->soorganization = CreateObject('booking.soorganization');
			$this->soresource = CreateObject('booking.soresource');
			$this->soevent = CreateObject('booking.soevent');
		}

		function search( $searchterm, $building_id, $filter_part_of_town, $filter_top_level, $activity_criteria = array() )
		{
			$building_filter = array(-1);
			$filter_top_level = $filter_top_level ? $filter_top_level : array(-1);
			$_filter_search_type = explode(',', phpgw::get_var('filter_search_type', 'string'));
			$types = array();
			foreach ($_filter_search_type as $key => $value)
			{
				if ($value)
				{
					$types[] = $value;
				}
			}

			if (!$types)
			{
				$types = array('building', 'resource');//default
			}

			if ($type = phpgw::get_var('type', 'string', 'REQUEST', null))
			{
				$types[] = $type;
			}

			$bui_result = $org_result = $res_result = $event_result = array();

			$_filter_building = array("active" => "1");
			if (!isset($filter_part_of_town) || !$filter_part_of_town)
			{
				$part_of_towns = execMethod('property.sogeneric.get_list', array('type' => 'part_of_town'));

				$filter_part_of_town = array();
				foreach ($part_of_towns as &$part_of_town)
				{
					$filter_part_of_town[] = $part_of_town['id'];
				}
			}

			$_filter_building['part_of_town_id'] = $filter_part_of_town;

			$buildings = array();
			if ($filter_top_level && !$building_id && !$searchterm)
			{
				$buildings = $this->sobuilding->get_buildings_from_activity($filter_top_level);
			}
			if ($buildings)
			{
				$_filter_building['id'] = $buildings;
			}
			if ($building_id)
			{
				$_filter_building['id'] = $building_id;
			}

//			if(in_array('building', $types))
			{

				$bui_result = $this->sobuilding->read(array("query" => $searchterm, "sort" => "name",
					"dir" => "asc", "filters" => $_filter_building));
				foreach ($bui_result['results'] as &$bui)
				{
					$building_filter[] = $bui['id'];
					$bui['type'] = "building";
					$bui['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uibuilding.show',
						'id' => $bui['id']));
					$bui['img_container'] = "building-" . $bui['id'];
					$bui['img_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uidocument_building.index_images',
						'filter_owner_id' => $bui['id'], 'phpgw_return_as' => 'json', 'results' => '1'));
					if (trim($bui['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($bui['homepage'])))
					{
						$bui['homepage'] = 'http://' . $bui['homepage'];
					}
				}
				unset($bui);
			}
//			_debug_array($bui_result);

			if (in_array('organization', $types))
			{
				$org_result = $this->soorganization->read(array("query" => $searchterm, "sort" => "name",
					"dir" => "asc", "filters" => array("active" => "1")));
				foreach ($org_result['results'] as &$org)
				{
					$org['type'] = "organization";
					$org['description'] = nl2br(strip_tags($org['description']));
					$org['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uiorganization.show',
						'id' => $org['id']));
					if (trim($org['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($org['homepage'])))
					{
						$org['homepage'] = 'http://' . $org['homepage'];
					}
				}
			}

//			if(in_array('resource', $types))
			{
				$_filter_resource = array("active" => "1");

				if ($filter_top_level)
				{
					$_filter_resource['filter_top_level'] = $filter_top_level;
				}

				if ($building_filter && !$searchterm)
				{
					$_filter_resource['building_id'] = $building_filter;
				}

				if ($building_id)
				{
					$_filter_resource['building_id'][] = $building_id;
				}
				if (isset($filter_part_of_town) && $filter_part_of_town)// && !$bui_result)
				{
					$_bui_result = $this->sobuilding->read(array("filters" => $_filter_building));
					foreach ($_bui_result['results'] as $_bui)
					{
						$_filter_resource['building_id'][] = $_bui['id'];
					}

					$_filter_resource['building_id'] = array_unique($_filter_resource['building_id']);
				}

				if ($activity_criteria)
				{
					$_filter_resource['custom_fields_criteria'] = $activity_criteria;
				}

				$_res_result = $this->soresource->read(array("query" => $searchterm, "sort" => "name",
					"dir" => "asc", "filters" => $_filter_resource));

				$_check_duplicate = array();
				$res_result = array(
					'total_records' => 0,
					'start' => $_res_result['start'],
					'sort' => $_res_result['sort'],
					'dir' => $_res_result['dir']
				);
				$_resource_buildings = array();
				foreach ($_res_result['results'] as &$res)
				{
					if (isset($res['buildings']) && is_array($res['buildings']))
					{
						foreach ($res['buildings'] as $_building_id)
						{
							$_resource_buildings[$_building_id] = true;
						}
					}
					else if (isset($res['building_id']) && $res['building_id'])
					{
						$_resource_buildings[$res['building_id']] = true;
					}

					if (isset($_check_duplicate[$res['id']]))
					{
						continue;
					}

					$res['name'] = $res['building_name'] . ' / ' . $res['name'];
					$res['type'] = "resource";
					$res['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uiresource.show',
						'id' => $res['id']));
					$res['img_container'] = "resource-" . $res['id'];
					$res['img_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uidocument_resource.index_images',
						'filter_owner_id' => $res['id'], 'phpgw_return_as' => 'json', 'results' => '1'));

					$_check_duplicate[$res['id']] = true;

					$res_result['total_records'] ++;
					$res_result['results'][] = $res;
				}
/*
				if (isset($bui_result['total_records']) && $bui_result['total_records'] > 0)
				{
					$_bui_result = array(
						'total_records' => 0,
						'start' => $bui_result['start'],
						'sort' => $bui_result['sort'],
						'dir' => $bui_result['dir']
					);
					foreach ($bui_result['results'] as $bui)
					{
						if (isset($_resource_buildings[$bui['id']]))
						{
							$_bui_result['results'][] = $bui;
							$_bui_result['total_records'] ++;
						}
					}
					$bui_result = $_bui_result;
				}
 */
			}


			if (!in_array('building', $types))
			{
				$bui_result = array();
			}
			if (!in_array('resource', $types))
			{
				$res_result = array();
			}
//			_debug_array($_resource_buildings);
//			_debug_array($bui_result);
			if (in_array('event', $types))
			{
				$now = date('Y-m-d');
				$expired_conditions = "(bb_event.active != 0 AND bb_event.completed = 0 AND bb_event.from_ > '{$now}' AND bb_event.description != '')";
				$event_result = $this->soevent->read(array("query" => $searchterm, "sort" => "name",
					"dir" => "asc", "filters" => array('where' => $expired_conditions)));
				foreach ($event_result['results'] as &$event)
				{
					$event['name'] = $event['building_name'] . ' / ' . $event['description'];
					$event['type'] = "Event";
					$date = date('Y-m-d', strtotime($event['from_']));
		//			$event_res = $this->soresource->read(array('filters' => array('id' => $event['resources'][0])));
					$event['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => $event['building_id'], 'date' => $date));
				}
			}

			$final_array = array_merge_recursive($bui_result, $org_result, $res_result, $event_result);
			$final_array['total_records_sum'] = array_sum((array)$final_array['total_records']);
			return $final_array;
		}
	}