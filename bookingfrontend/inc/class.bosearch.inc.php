<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class('booking.soevent');
	phpgw::import_class('booking.sobuilding');
	phpgw::import_class('booking.sobooking');
	phpgw::import_class('booking.soallocation');


	class bookingfrontend_bosearch extends booking_bocommon
	{

	    public $soevent;
	    public $sobuilding;
	    public $sobooking;
	    public $soallocation;

		function __construct()
		{
			parent::__construct();
			$this->sobuilding = new booking_sobuilding();
			$this->soorganization = CreateObject('booking.soorganization');
			$this->soresource = CreateObject('booking.soresource');
			$this->soevent = new booking_soevent();
			$this->sobooking = new booking_sobooking();
			$this->soallocation = new booking_soallocation();
			$this->borescategory = CreateObject('booking.borescategory');
			$this->boresource = CreateObject('booking.boresource');
			$this->boactivity = CreateObject('booking.boactivity');
			$this->bofacility = CreateObject('booking.bofacility');
		}

		function search( $searchterm, $building_id, $filter_part_of_town, $filter_top_level, $activity_criteria = array() , $length)
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
					"dir" => "asc", "filters" => $_filter_building, 'results' => $length));
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
					"dir" => "asc", "filters" => array("active" => "1"), 'results' => $length));
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
					$_bui_result = $this->sobuilding->read(array("filters" => $_filter_building, 'results' => $length));
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
					"dir" => "asc", "filters" => $_filter_resource, 'results' => $length));

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
					"dir" => "asc", "filters" => array('where' => $expired_conditions), 'results' => $length));
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
			$returnres = array(
				'buildings'   => array(),
				'activities'  => array(),
				'facilities'  => array(),
				'partoftowns' => array(),
				);
			$fields_resource = array('id','name','activities_list','facilities_list', 'simple_booking');
			$fields_building = array('id','name','street','zip_code','city','part_of_town_id','part_of_town_name');

			// Get a list of all part_of_town that are used for buildings, creating a list keyed on the part_of_town id
			$partoftownlist = array();
			$potlist = execMethod('property.solocation.get_booking_part_of_towns');
			foreach ($potlist as $pot)
			{
				$id = $pot['id'];
				$partoftownlist[$id] = $pot;
			}

			// Validate parameters. rescategory_id must always be present
			$invalid_params = False;
			if (!(isset($params['rescategory_id']) && is_int($params['rescategory_id']) && $params['rescategory_id'] > 0))
			{
				$invalid_params = True;
			}
			$multiintparams = array('facility_id','part_of_town_id', 'activity_id');
			foreach ($multiintparams as $multiintparam)
			{
				if (isset($params[$multiintparam]))
				{
					if (empty($params[$multiintparam]))
					{
						$params[$multiintparam] = null;
					}
					else
					{
						foreach ($params[$multiintparam] as $val)
						{
							if (!(is_int($val) && $val > 0))
							{
								$invalid_params = True;
								break;
							}
						}
					}

					if (!$invalid_params && is_array($params[$multiintparam]))
					{
						sort($params[$multiintparam]);
					}
				}
			}
			if ($invalid_params)
			{
				return $returnres;
			}

			// Get resources
			$resource_filters = array('active' => 1, 'rescategory_active' => 1);
			$resource_filters['rescategory_id'] = $params['rescategory_id'];
			$resources = $this->soresource->read(array('filters' => $resource_filters, 'sort' => 'sort', 'results' => $params['length']));
			$this->boresource->add_activity_facility_data($resources['results']);

			// Group the resources on buildings, and get data on the buildings to which the resources belong as well as
			// check the activities and facilities
			$building_resources = array();
			$all_activities = array();
			$all_facilities = array();
			foreach ($resources['results'] as &$resource)
			{
				// Keep only the wanted fields
				$building_ids = $resource['buildings'];
				foreach ($resource as $k => $v)
				{
					if (!in_array($k,$fields_resource))
					{
						unset($resource[$k]);
					}
				}
				// Handle the activities
				$include_on_activity = False;
				$activities_matched = array();
				foreach ($resource['activities_list'] as $activity)
				{
					if (!array_key_exists($activity['id'], $all_activities))
					{
						$all_activities[$activity['id']] = array('id' => $activity['id'], 'name' => $activity['name']);
					}
					// Check filter criteria
					if (isset($params['activity_id']) && in_array($activity['id'], $params['activity_id']))
					{
						$activities_matched[] = $activity['id'];
					}
				}
				// If applicable, check if all activity criterias are met
				if (!empty($activities_matched))
				{
					sort($activities_matched);
					if ($activities_matched === $params['activity_id'])
					{
						$include_on_activity = True;
					}
				}

				// Handle the facilities
				$include_on_facility = False;
				$facilities_matched = array();
				foreach ($resource['facilities_list'] as $facility)
				{
					if (!array_key_exists($facility['id'], $all_facilities))
					{
						$all_facilities[$facility['id']] = array('id' => $facility['id'], 'name' => $facility['name']);
					}
					// Check filter criteria
					if (isset($params['facility_id']) && in_array($facility['id'], $params['facility_id']))
					{
						$facilities_matched[] = $facility['id'];
					}
				}
				// If applicable, check if all facility criterias are met
				if (!empty($facilities_matched))
				{
					sort($facilities_matched);
					if ($facilities_matched === $params['facility_id'])
					{
						$include_on_facility = True;
					}
				}
				// Add the resource to the building, unless given filter criterias are not met
				if ((isset($params['activity_id']) && !$include_on_activity) || (isset($params['facility_id']) && !$include_on_facility)) {
					$resource['ignore'] = True;
				}
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
			$buildingsres = $this->sobuilding->read(array('filters' => $building_filters, 'sort' => 'name', 'results' => $params['length']));
			$buildings = array();
			foreach ($buildingsres['results'] as &$building)
			{
				if (array_key_exists($building['part_of_town_id'],$partoftownlist))
				{
					$partoftown = $partoftownlist[$building['part_of_town_id']];
					// Include the part of town name for the building
					$building['part_of_town_name'] = $partoftown['name'];
					if (!array_key_exists($partoftown['id'], $all_partoftown))
					{
						// Include the part of town name for the all parts of town list
						$all_partoftown[$partoftown['id']] = array('id' => $partoftown['id'], 'name' => $partoftown['name']);
					}
				}
				else
				{
					$building['part_of_town_name'] = $building['district'];
				}
				// Check filter criteria
				if (isset($params['part_of_town_id']) && !in_array($building['part_of_town_id'],$params['part_of_town_id']))
				{
					continue;
				}

				foreach ($building as $k => $v)
				{
					if (!in_array($k,$fields_building))
					{
						unset($building[$k]);
					}
				}
				// When adding resources to the building ignore those that are so marked. If no resources for a
				// building, ignore the building
				$building['resources'] = array();
				foreach ($building_resources[$building['id']] as $resource)
				{
					if (!$resource['ignore'])
					{
						$building['resources'][] = $resource;
					}
				}
				if (!empty($building['resources']))
				{
					$buildings[] = $building;
				}
			}
			unset($building);
			$all_partoftown_list = array_values($all_partoftown);
			usort($all_partoftown_list, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
			$returnres['resources'] = $resources;
			$returnres['buildings']   = $buildings;
			$returnres['activities']  = $all_activities_list;
			$returnres['facilities']  = $all_facilities_list;
			$returnres['partoftowns'] = $all_partoftown_list;

			return $returnres;
		}

		function events()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$headertext = lang('Upcoming events');
			$headertext_config = $config->config_data['frontpage_upcomingevents'];
			if (!empty($headertext_config))
			{
				$headertext = $headertext_config;
			}

			$fields_events = array('building_name','from_','homepage','id','name','organizer','to_');
			$now = date('Y-m-d');
			$conditions = "(bb_event.active != 0 AND bb_event.include_in_list = 1 AND bb_event.completed = 0 AND bb_event.to_ > '{$now}' AND bb_event.name != '')";


			/**
			 * All ( Alter to enable pagination )
			 */
			$length = -1;

			$event_result = $this->soevent->read(array("sort" => "from_", "dir" => "asc",
				"filters" => array('where' => $conditions),'results' => $length));
			foreach ($event_result['results'] as &$event)
			{
				foreach ($event as $k => $v)
				{
					if (!in_array($k,$fields_events))
					{
						unset($event[$k]);
					}
				}
				$event['name'] = html_entity_decode($event['name']);
				$event['organizer'] = html_entity_decode($event['organizer']);
				if (trim($event['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($event['homepage'])))
				{
					$event['homepage'] = 'http://' . $event['homepage'];
				}
				$ts_from = strtotime($event['from_']);
				$ts_to   = strtotime($event['to_']);
				$day_from = date('d', $ts_from);
				$day_to   = date('d', $ts_to);
				if ($day_from == $day_to)
				{
					$event['datetime_day'] = sprintf('%d', $day_from);
				}
				else
				{
					$event['datetime_day'] = sprintf('%d-%d', $day_from, $day_to);
				}
				$month_from = date('M', $ts_from);
				$month_to   = date('M', $ts_to);
				if ($month_from == $month_to)
				{
					$event['datetime_month'] = lang($month_from);
				}
				else
				{
					$event['datetime_month'] = sprintf('%s-%s', lang($month_from), lang($month_to));
				}
				$event['datetime_time'] = sprintf('%s-%s', date('H:i',$ts_from), date('H:i',$ts_to));
			}
			unset($event);

			$event_result['header'] = $headertext;

			return $event_result;
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
					// Resource category names containing special characters (such as parentheses) seems to be doubly
					// escaped when retrieved, so decode the name once here (ie. from "&amp;#40;" to "&#40;") and then
					// the templates can handle the second decoding


					$filtered_entries = array();
					foreach ($rescategories as &$rescategory)
					{
						$rescategory['name'] = html_entity_decode($rescategory['name']);

						if(empty($rescategory['disabled']))
						{
							$filtered_entries[] = $rescategory;
						}
					}
					unset($rescategory);
					$data[] = array('text' => $boxtext, 'rescategories' => $filtered_entries);
				}
			}
			return $data;
		}

		function search_available_resources($searchterm, $building_id, $filter_part_of_town, $filter_top_level, $activity_criteria = array(), $filter_activities, $filter_facilitites, $length, $params)
		{
			$result = $this->search($searchterm, $building_id, $filter_part_of_town, $filter_top_level, $activity_criteria, $length);
			$this->boresource->add_activity_facility_data($result['results']);

			$partoftownlist = array();
			$potlist = execMethod('property.solocation.get_booking_part_of_towns');
			foreach ($potlist as $pot)
			{
				$id = $pot['id'];
				$partoftownlist[$id] = $pot;
			}

			$building_resources = array();

			foreach ($result['results'] as $resource)
			{

				// Handle the activities
				$include_on_activity = False;
				$activities_matched = array();
				foreach ($resource['activities_list'] as $activity)
				{
					// Check filter criteria
					if (!empty($filter_activities) && in_array($activity['id'], $filter_activities))
					{
						$activities_matched[] = $activity['id'];
					}
				}
				// If applicable, check if all activity criterias are met
				if (!empty($activities_matched))
				{
					sort($activities_matched);
					if ($activities_matched === $filter_activities)
					{
						$include_on_activity = True;
					}
				}

				// Handle the facilities
				$include_on_facility = False;
				$facilities_matched = array();
				foreach ($resource['facilities_list'] as $facility)
				{
					// Check filter criteria
					if (!empty($filter_facilitites) && in_array($facility['id'], $filter_facilitites))
					{
						$facilities_matched[] = $facility['id'];
					}
				}
				// If applicable, check if all facility criterias are met
				if (!empty($facilities_matched))
				{
					sort($facilities_matched);
					if ($facilities_matched === $filter_facilitites)
					{
						$include_on_facility = True;
					}
				}
				// Add the resource to the building, unless given filter criterias are not met
				if ((!empty($filter_activities) && !$include_on_activity) || (!empty($filter_facilitites) && !$include_on_facility))
				{
					$resource['ignore'] = True;
				}
				foreach ($resource['buildings'] as $building_id)
				{
					$building_resources[$building_id][] = $resource;
				}
			}

			// Get building data
			$building_filters = array('id' => array_keys($building_resources), 'active' => 1);
			$buildings_res = $this->sobuilding->read(array('filters' => $building_filters, 'sort' => 'name', 'results' => $length));
			$buildings = array();
			foreach ($buildings_res['results'] as &$building)
			{
				if (array_key_exists($building['part_of_town_id'],$partoftownlist))
				{
					$partoftown = $partoftownlist[$building['part_of_town_id']];
					// Include the part of town name for the building
					$building['part_of_town_name'] = $partoftown['name'];
				}
				else
				{
					$building['part_of_town_name'] = $building['district'];
				}
				// Check filter criteria
				if (isset($params['part_of_town_id']) && !in_array($building['part_of_town_id'],$params['part_of_town_id']))
				{
					continue;
				}

				// When adding resources to the building ignore those that are so marked. If no resources for a
				// building, ignore the building
				$building['resources'] = array();
				foreach ($building_resources[$building['id']] as $resource)
				{
					if (!$resource['ignore'])
					{
						$building['resources'][] = $resource;
					}
				}
				if (!empty($building['resources']))
				{
					$buildings[] = $building;
				}
			}
			unset($building);

			$returnres['available_resources'] = $this->available_resources($buildings, $params);

			$all_activities = array();
			$all_facilities = array();
			foreach ($returnres['available_resources'] as $resource)
			{
				foreach ($resource['activities_list'] as $activity)
				{
					if (!array_key_exists($activity['id'], $all_activities))
					{
						$all_activities[$activity['id']] = array('id' => $activity['id'], 'name' => $activity['name']);
					}
				}

				foreach ($resource['facilities_list'] as $facility)
				{
					if (!array_key_exists($facility['id'], $all_facilities))
					{
						$all_facilities[$facility['id']] = array('id' => $facility['id'], 'name' => $facility['name']);
					}
				}
			}

			usort($returnres['available_resources'], function ($a,$b) { return strcmp(strtolower($a['resource_name']),strtolower($b['resource_name'])); });
			usort($all_activities, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
			usort($all_facilities, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });

			$returnres['activities']  = $all_activities;
			$returnres['facilities']  = $all_facilities;

			return $returnres;
		}

		function resquery_available_resources ($params = array()) {
			$returnres = array(
				'buildings'   => array(),
				'activities'  => array(),
				'facilities'  => array(),
				'partoftowns' => array(),
			);
			$fields_resource = array('id','name','activities_list','facilities_list', 'simple_booking', 'activities', 'facilities', 'building');
			$fields_building = array('id','name','street','zip_code','city','part_of_town_id','part_of_town_name');

			// Get a list of all part_of_town that are used for buildings, creating a list keyed on the part_of_town id
			$partoftownlist = array();
			$potlist = execMethod('property.solocation.get_booking_part_of_towns');
			foreach ($potlist as $pot)
			{
				$id = $pot['id'];
				$partoftownlist[$id] = $pot;
			}

			// Validate parameters. rescategory_id must always be present
			$invalid_params = False;
			if (!(isset($params['rescategory_id']) && is_int($params['rescategory_id']) && $params['rescategory_id'] > 0))
			{
				$invalid_params = True;
			}
			$multiintparams = array('part_of_town_id');
			foreach ($multiintparams as $multiintparam)
			{
				if (isset($params[$multiintparam]))
				{
					if (empty($params[$multiintparam]))
					{
						$params[$multiintparam] = null;
					}
					else
					{
						foreach ($params[$multiintparam] as $val)
						{
							if (!(is_int($val) && $val > 0))
							{
								$invalid_params = True;
								break;
							}
						}
					}
					if (!$invalid_params && is_array($params[$multiintparam]))
					{
						sort($params[$multiintparam]);
					}
				}
			}
			if ($invalid_params)
			{
				return $returnres;
			}

			// Get resources
			$resource_filters = array('active' => 1, 'rescategory_active' => 1);
			$resource_filters['rescategory_id'] = $params['rescategory_id'];
			$resources = $this->soresource->read(array('filters' => $resource_filters, 'sort' => 'sort', 'results' => $params['length']))['results'];
			$this->boresource->add_activity_facility_data($resources);

			// Group the resources on buildings, and get data on the buildings to which the resources belong as well as
			// check the activities and facilities

			$applicable_resources = array();
			foreach ($resources as &$resource)
			{
				//Get building data
				$building_filters = array('id' => $resource['buildings'][0], 'active' => 1);
				$resource['building'] = $this->sobuilding->read(array('filters' => $building_filters, 'sort' => 'name', 'results' => $params['length']))['results'][0];

				// Keep only the wanted fields
				$building_ids = $resource['buildings'];
				foreach ($resource as $k => $v)
				{
					if (!in_array($k,$fields_resource))
					{
						unset($resource[$k]);
					}
				}

				foreach ($resource['building'] as $k => $v)
				{
					if (!in_array($k,$fields_building))
					{
						unset($resource['building'][$k]);
					}
				}

				//Handle the part of towns filter
				if (isset($params['part_of_town_id']) && !in_array($resource['building']['part_of_town_id'],$params['part_of_town_id']))
				{
					unset($resource);
				}
				else
				{
					$applicable_resources[] = $resource;
				}
			}
			unset($resource);

			// HER
			$returnres = $this->resquery($params);

			$returnres['available_resources']   = $this->available_resources($applicable_resources, $params);

			$all_activities = array();
			$all_facilities = array();
			foreach ($returnres['available_resources'] as $resource)
			{
				foreach ($resource['activities_list'] as $activity)
				{
					if (!array_key_exists($activity['id'], $all_activities))
					{
						$all_activities[$activity['id']] = array('id' => $activity['id'], 'name' => $activity['name']);
					}
				}

				foreach ($resource['facilities_list'] as $facility)
				{
					if (!array_key_exists($facility['id'], $all_facilities))
					{
						$all_facilities[$facility['id']] = array('id' => $facility['id'], 'name' => $facility['name']);
					}
				}
			}

			usort($returnres['available_resources'], function ($a,$b) { return strcmp(strtolower($a['resource_name']),strtolower($b['resource_name'])); });
			usort($all_activities, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
			usort($all_facilities, function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });

			$returnres['activities'] = $all_activities;
			$returnres['facilities'] = $all_facilities;
			return $returnres;
		}

		function available_resources($buildings, $params = array()) {
			$from_date = DateTime::createFromFormat('d.m.Y H:i:s', $params['from_date']);
			$from_date_test = $from_date->format('Y-m-d H:i:s');
			$to_date = DateTime::createFromFormat('d.m.Y H:i:s', $params['to_date']);
			$to_date_test = $to_date->format('Y-m-d H:i:s');


			$is_time_set = false;
			if($params['from_time'] != "" && $params['to_time'] != "")
			{
				$from_time = DateTime::createFromFormat('H:i', $_GET['from_time'])->format('H:i');
				$to_time = DateTime::createFromFormat('H:i', $_GET['to_time'])->format('H:i');

				$is_time_set = True;
			}
			$available_resources = array();
				foreach ($buildings as $resource)
				{
					array_set_default($available_resource, 'facilities', array());
					array_set_default($available_resource, 'activities', array());
					array_set_default($available_resource, 'facilities_list', array());
					array_set_default($available_resource, 'activities_list', array());

					$available_resource['building_id'] = $resource['building']['id'];
					$available_resource['building_name'] = $resource['building']['name'];
					$available_resource['building_city'] = $resource['building']['city'];
					$available_resource['building_street'] = $resource['building']['street'];
					$available_resource['building_zip_code'] = $resource['building']['zip_code'];
					$available_resource['part_of_town_id'] = $resource['building']['part_of_town_id'];


					$available_resource['resource_id'] = $resource['id'];
					$available_resource['resource_name'] = $resource['name'];
					$available_resource['facilities'] = array_column($resource['facilities_list'], 'id');
					$available_resource['activities'] = array_column($resource['activities_list'], 'id');
					$available_resource['facilities_list'] = $resource['facilities_list'];
					$available_resource['activities_list'] = $resource['activities_list'];
					$allocation_ids = $this->sobooking->allocation_ids_for_resource($resource['id'], $from_date, $to_date);
					$booking_ids = $this->sobooking->booking_ids_for_resource($resource['id'], $from_date, $to_date);
					$event_ids = $this->sobooking->event_ids_for_resource($resource['id'], $from_date, $to_date);

					$allocations = $bookings = $events = $booked_times = array();

					if (!empty($allocation_ids))
					{
						$allocations = $this->soallocation->read(array('filters' => array('id' => $allocation_ids), 'results' => -1));
						$allocations = $allocations['results'];
					}

					if (!empty($booking_ids))
					{
						$bookings = $this->sobooking->read(array('filters' => array('id' => $booking_ids), 'results' => -1));
						$bookings = $bookings['results'];
					}

					if (!empty($event_ids))
					{
						$events = $this->soevent->read(array('filters' => array('id' => $event_ids), 'results' => -1));
						$events = $events['results'];
					}

					$booked_times = array_merge($allocations, $events, $bookings);

					if (!empty($booked_times))
					{
						usort($booked_times, function ($a, $b) {
							$ad = strtotime($a['from_']);
							$bd = strtotime($b['from_']);
							return ($ad - $bd);
						});
					}

					if (empty($booked_times))
					{
						$available_resource['from'] = $from_date;
						$available_resource['to'] = $to_date;

						$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
						$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
						array_push($available_resources, $available_resource);
					}
					else
					{
						$booked_times_len = count($booked_times);
						$last_end_date = $booked_times[0]['to_'];

						for ($i = 0; $i < $booked_times_len; $i++)
						{
							$from = $booked_times[$i]['from_'];
							$to = $booked_times[$i]['to_'];

							if ($from > $from_date_test)
							{
								if ($i == 0)
								{
									$available_resource['from'] = $from_date;
									$available_resource['to'] = DateTime::createFromFormat('Y-m-d H:i:s', $booked_times[$i]['from_']);

									if ($is_time_set)
									{
										$available_resource_time = $available_resource['from']->format('H:i');
										$available_resource_to = $available_resource['to']->format('H:i');

										if ($available_resource_time <= $from_time && $available_resource_to >= $to_time)
										{
											$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
											$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
											array_push($available_resources, $available_resource);
										}
									}
									else
									{
										$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
										$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
										array_push($available_resources, $available_resource);
									}
								}
								else if ($from > $last_end_date)
								{
									$available_resource['from'] = DateTime::createFromFormat('Y-m-d H:i:s', $last_end_date);
									$available_resource['to'] = DateTime::createFromFormat('Y-m-d H:i:s', $from);

									if ($is_time_set)
									{
										$available_resource_time = $available_resource['from']->format('H:i');
										$available_resource_to = $available_resource['to']->format('H:i');

										if ($available_resource_time <= $from_time && $available_resource_to >= $to_time)
										{
											$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
											$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
											array_push($available_resources, $available_resource);
										}
									}
									else
									{
										$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
										$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
										array_push($available_resources, $available_resource);
									}

									$last_end_date = $to;
								}
								else if ($from <= $last_end_date && $to >= $last_end_date)
								{
									$last_end_date = $to;
								}

								if ($i + 1 == $booked_times_len)
								{
									if ($last_end_date < $to_date_test)
									{
										$available_resource['from'] = DateTime::createFromFormat('Y-m-d H:i:s', $last_end_date);
										$available_resource['to'] = $to_date;

										if ($is_time_set)
										{
											$available_resource_time = $available_resource['from']->format('H:i');
											$available_resource_to = $available_resource['to']->format('H:i');

											if ($available_resource_time <= $from_time && $available_resource_to >= $to_time)
											{
												$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
												$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
												array_push($available_resources, $available_resource);
											}
										}
										else
										{
											$available_resource['from'] = $available_resource['from']->format('d.m.Y H:i');
											$available_resource['to'] = $available_resource['to']->format('d.m.Y H:i');
											array_push($available_resources, $available_resource);
										}
									}
								}
							}
						}
					}
				}
			return $available_resources;
		}


		function getAutoCompleteData()
		{
			$sql = "SELECT DISTINCT bb_organization.name AS names,
					'organisasjon' AS type,
					'bookingfrontend.uiorganization.show' AS menuaction,
					bb_organization.id AS id
					FROM bb_organization
					WHERE bb_organization.active=1
					UNION
					SELECT DISTINCT bb_building.name AS names,
					'bygg' AS type,
					'bookingfrontend.uibuilding.show' AS menuaction,
					bb_building.id AS id
					FROM bb_building
					WHERE bb_building.active=1
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

		public function get_resource_and_building_autocomplete_data() {
			$sql = "SELECT DISTINCT bb_rescategory.name AS names,
					'lokale' AS type,
					'bookingfrontend.uiresource.show' AS menuaction,
					bb_rescategory.id AS id
					FROM bb_rescategory
					WHERE bb_rescategory.active=1
					UNION
					SELECT DISTINCT bb_building.name AS names,
					'anlegg' AS type,
					'bookingfrontend.uibuilding.show' AS menuaction,
					bb_building.id AS id
					FROM bb_building
					WHERE bb_building.active=1
					ORDER BY names asc";

			$results = array();
			$db = & $GLOBALS['phpgw']->db;
			$db->query($sql, __LINE__, __FILE__);
			$i = 0;
			while ($db->next_record())
			{
				$results[$i]["name"] = $db->f('names', true);
				$results[$i]["type"] = $db->f('type', true);
				$results[$i]["id"] = $db->f('id');
				$results[$i]["menuaction"] = $db->f('menuaction', true);
				$i++;
			}

			return $results;

	}

		public function get_all_booked_ids($from_date, $to_date)
		{
			return $this->sobuilding->get_all_booked_ids($from_date, $to_date);
		}

	}
