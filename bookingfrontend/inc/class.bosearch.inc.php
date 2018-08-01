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
			$this->borescategory = CreateObject('booking.borescategory');
			$this->boactivity = CreateObject('booking.boactivity');
			$this->bofacility = CreateObject('booking.bofacility');
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
				$types = array('building', 'resource', 'organization');//default
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
				unset($_filter_building['part_of_town_id']);
			}

			if(in_array('building', $types))
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

			if ($searchterm && in_array('organization', $types))
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

			if(in_array('resource', $types))
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
						$building_names = array();
						$building_district = array();
						foreach ($res['buildings'] as $_building_id)
						{
							$_resource_buildings[$_building_id] = true;
							$building = $this->sobuilding->read_single($_building_id);
							$building_names[] = $building['name'];
							$building_district[] = $building['district'];
							$res['building_name'] = implode('</br>', $building_names);
							$res['district'] = implode('</br>', $building_district);
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


		function resquery($params = array())
		{
			$fields_resource = array('id','name');
			$fields_building = array('id','name','street','zip_code','city','part_of_town_name');

			// Get a list of all activities, grouped on top level activities, as well as all facilities
			$activitylist = $this->boactivity->fetch_activities_hierarchy();
			$facilitylist = $this->bofacility->get_facilities();
			// Also get a list of all part_of_town that are used for buildings, creating a list keyed on the
			// part_of_town id
			$partoftownlist = array();
			$potlist = execMethod('property.solocation.get_booking_part_of_towns');
			foreach ($potlist as $pot)
			{
				$id = $pot['id'];
				$partoftownlist[$id] = $pot;
			}

			// Validate parameters
			if (!(isset($params['rescategory_id']) && is_int($params['rescategory_id']) && $params['rescategory_id'] > 0))
			{
				return array();
			}

			// Get resources
			$resource_filters = array('active' => 1, 'rescategory_active' => 1);
			$resource_filters['rescategory_id'] = $params['rescategory_id'];
			$resources = $this->soresource->read(array('filters' => $resource_filters, 'sort' => 'sort'));

			// Group the resources on buildings, and get data on the buildings to which the resources belong as well as
			// activities and facilities
			$building_resources = array();
			$all_activities = array();
			$all_facilities = array();
			foreach ($resources['results'] as &$resource)
			{
				$building_ids = $resource['buildings'];
				$toplevelactivity_id = $resource['activity_id'];
				$activity_ids = $resource['activities'];
				$facility_ids = $resource['facilities'];
				foreach ($resource as $k => $v)
				{
					if (!in_array($k,$fields_resource))
					{
						unset($resource[$k]);
					}
				}
				// Add a list of activities with id and name. Only active activities are included, and only activities
				// belonging to the top level activity defined for the resource
				$childactivities = array();
				if (array_key_exists($toplevelactivity_id, $activitylist))
				{
					$childactivities = $activitylist[$toplevelactivity_id]['children'];
				}
				$resource['activities'] = array();
				foreach ($activity_ids as $activity_id)
				{
					if (array_key_exists($activity_id, $childactivities))
					{
						$childactivity = $childactivities[$activity_id];
						if ($childactivity['active'])
						{
							$resource['activities'][] = array('id' => $childactivity['id'], 'name' => $childactivity['name']);
							if (!array_key_exists($childactivity['id'], $all_activities))
							{
								$all_activities[$childactivity['id']] = array('id' => $childactivity['id'], 'name' => $childactivity['name']);
							}
						}
					}
				}
				usort($resource['activities'], function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
				// Add a list of facilities with id and name. Only active facilities are included
				$resource['facilities'] = array();
				foreach ($facility_ids as $facility_id)
				{
					if (array_key_exists($facility_id,$facilitylist))
					{
						$facility = $facilitylist[$facility_id];
						if ($facility['active'])
						{
							$resource['facilities'][] = array('id' => $facility['id'], 'name' => $facility['name']);
							if (!array_key_exists($facility['id'], $all_facilities))
							{
								$all_facilities[$facility['id']] = array('id' => $facility['id'], 'name' => $facility['name']);
							}
						}
					}
				}
				usort($resource['facilities'], function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
				// Add the resource to the building
				foreach ($building_ids as $building_id)
				{
					$building_resources[$building_id][] = $resource;
				}
			}
			unset($resource);
			$all_activities_list = array_values($all_activities);
			usort($all_activities_list, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
			$all_facilities_list = array_values($all_facilities);
			usort($all_facilities_list, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
			// Get building data
			$all_partoftown = array();
			$building_filters = array('id' => array_keys($building_resources), 'active' => 1);
			$buildings = $this->sobuilding->read(array('filters' => $building_filters, 'sort' => 'name'));
			foreach ($buildings['results'] as &$building)
			{
				if (array_key_exists($building['part_of_town_id'],$partoftownlist))
				{
					$partoftown = $partoftownlist[$building['part_of_town_id']];
					$building['part_of_town_name'] = $partoftown['name'];
					if (!array_key_exists($partoftown['id'], $all_partoftown))
					{
						$all_partoftown[$partoftown['id']] = array('id' => $partoftown['id'], 'name' => $partoftown['name']);
					}
				}
				else
				{
					$building['part_of_town_name'] = $building['district'];
				}

				foreach ($building as $k => $v)
				{
					if (!in_array($k,$fields_building))
					{
						unset($building[$k]);
					}
				}
				$building['resources'] = $building_resources[$building['id']];
			}
			unset($building);
			$all_partoftown_list = array_values($all_partoftown);
			usort($all_partoftown_list, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });

			return array('buildings' => $buildings, 'activities' => $all_activities_list, 'facilities' => $all_facilities_list,
				'partoftowns' => $all_partoftown_list);
		}


		function get_filterboxdata()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			$data = array();
			$datatext = $config->config_data['frontpage_filterboxdata'];
			if ($datatext != null)
			{
				$lines = preg_split("[\r\n|\r|\n]", $datatext);
				foreach ($lines as $line)
				{
					$parts = explode(':', $line);
					if (count($parts) != 2)
					{
						// Ignore line as it doesn't conform to syntax
						continue;
					}

					$boxtext = trim($parts[0]);
					if ($boxtext == '')
					{
						continue;
					}

					$activities = explode(',', $parts[1]);
					$activity_ids = array();
					foreach ($activities as $activity)
					{
						$activity_id = trim($activity);
						if ($activity_id != '' && ctype_digit($activity_id))
						{
							$activity_ids[] = $activity_id;
						}
					}
					if (count($activity_ids) == 0)
					{
						continue;
					}
					$rescategories = $this->borescategory->get_rescategories_by_activities($activity_ids);

					$data[] = array('text' => $boxtext, 'rescategories' => $rescategories);
				}
			}
			return $data;
		}


		function getAutoCompleteData()
		{
			$sql = "SELECT DISTINCT bb_organization.name AS names,
					'organisasjon' AS type,
					'bookingfrontend.uiorganization.show' AS menuaction,
					bb_organization.id AS id
					FROM bb_organization
					UNION
					SELECT DISTINCT bb_building.name AS names,
					'bygg' AS type,
					'bookingfrontend.uibuilding.show' AS menuaction,
					bb_building.id AS id
					FROM bb_building
					ORDER BY names asc";

			$results = array();
			$db = & $GLOBALS['phpgw']->db;
			$db->query($sql, __LINE__, __FILE__);
			$i = 0;
			while ($db->next_record())
			{
				$results[$i]["name"] = $db->f('names', true);
				$results[$i]["type"] = $db->f('type', true);
				$results[$i]["id"] = $db->f('id', true);
				$results[$i]["menuaction"] = $db->f('menuaction', true);
				$i++;
			}
			return $results;
		}

	}
