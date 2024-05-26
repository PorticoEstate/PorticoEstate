<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('bookingfrontend.bosearch');

	class bookingfrontend_uisearch extends booking_uicommon
	{
	    public $bo;

		public $public_functions = array
			(
			'autocomplete'      => true,
			'events'            => true,
			'get_filterboxdata' => true,
			'index'             => true,
			'query'             => true,
			'query_available_resources' => true,
			'resquery'          => true,
			'resquery_available_resources' => true,
			'get_all_available_buildings' => true,
			'autocomplete_resource_and_building' => true,
			'get_all_towns' => true,
            'get_search_data_location' => true,
            'get_search_data_all' => true,
			'search_available_resources' => true
		);

		function __construct()
		{

			parent::__construct();
			$this->bo = CreateObject('bookingfrontend.bosearch');
//			$old_top = array_pop(parent::$tmpl_search_path);
//			array_push(parent::$tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
//			array_push(parent::$tmpl_search_path, $old_top);
		}

		function index()
		{
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('treeview');
			phpgwapi_jquery::load_widget('daterangepicker');
			phpgwapi_jquery::load_widget('timepicker');

			self::add_javascript('bookingfrontend', 'base', 'search.js', true);
			self::add_javascript('bookingfrontend', 'base', 'util.js', true);


			$GLOBALS['phpgw']->js->add_external_file("phpgwapi/templates/bookingfrontend/js/build/aui/aui-min.js");
			$GLOBALS['phpgw']->css->add_external_file("phpgwapi/templates/base/css/rubik-font.css");
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$searchterm = trim(phpgw::get_var('searchterm', 'string', 'REQUEST', ''));
			$type = phpgw::get_var('type', 'string', 'REQUEST', null);
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);
			$filter_part_of_town = explode(',', phpgw::get_var('filter_part_of_town', 'string', 'REQUEST', ''));
			$imploded_filter_part_of_town = implode(',', $filter_part_of_town);
			$search = null;

			$criteria = phpgw::get_var('criteria');
//			_debug_array($config->config_data['landing_sections']);die();


			if ($config->config_data['frontpagetitle'] != '')
			{
				$frontpagetitle = $config->config_data['frontpagetitle'];
			}
			else
			{
				$frontpagetitle = 'Aktiv kommune';
			}

			if ($config->config_data['frontpagetext'] != '')
			{
				$frontpagetext = $config->config_data['frontpagetext'];
			}
			else
			{
				$frontpagetext = 'Velkommen til Aktiv kommune.<br />Her finner du informasjon om idrettsanlegg som leies ut<br />av idrettsavdelingen.';
			}

			if ($config->config_data['frontimagetext'] != '')
			{
				$frontimagetext = $config->config_data['frontimagetext'];
			}
			else
			{
				$frontimagetext = '<span>Din portal til</span><h1><b>AKTIVITETER OG LOKALER</b></h1><span>Nært deg.</span>';
			}

			if ($config->config_data['frontpage_filterboxtitle'] != '')
			{
				$filterboxtitle = $config->config_data['frontpage_filterboxtitle'];
			}
			else
			{
				$filterboxtitle = lang('Choose categories');
			}

            // Sample object structure
            $landing_sections = (object) [
                'booking' => true,
                'organization' => true,
                'event' => true
            ];
            // If the 'landing_sections' key is not set or its array is empty, set all to true
            if (!isset($config->config_data['landing_sections']) || empty($config->config_data['landing_sections'])) {
                $landing_sections->booking = true;
                $landing_sections->organization = true;
                $landing_sections->event = true;
            } else {
                // Otherwise, set only those present in the array to true and others to false
                $landing_sections->booking = in_array('booking', $config->config_data['landing_sections']);
                $landing_sections->organization = in_array('organization', $config->config_data['landing_sections']);
                $landing_sections->event = in_array('event', $config->config_data['landing_sections']);
            }
            $bogeneric = createObject('booking.bogeneric');
            $multi_domain_list = $bogeneric->read(array('location_info' => array('type' => 'multi_domain')));

            $params = array(
                'landing_sections' => $landing_sections,
                'landing_sections_json' => json_encode($landing_sections),
				'baseurl' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}",
				'filterboxtitle' => $filterboxtitle,
				'frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/bkbooking/images/newlayout/forsidebilde.jpg",
				'frontpagetitle' => $frontpagetitle,
				'frontpagetext' => $frontpagetext,
				'frontimagetext' => $frontimagetext,
				'activity_top_level' => $activity_top_level,
                'multi_domain_list' => json_encode($multi_domain_list)
			);
			$bobuilding = CreateObject('booking.bobuilding');
			$building = $bobuilding->read_single($building_id);
			$params['building_name'] = $building['name'];
			$params['building_id'] = $building_id;

			$activities = ExecMethod('booking.boactivity.get_top_level');

			$top_levels = array();
			$filter_tree = array();
			foreach ($activities as $activity)
			{
				if(!$activity['active'])
				{
					continue;
				}
				$top_levels[] = array(
					'id' => $activity['id'],
					'location' => "resource_{$activity['id']}",
					'name' => $activity['name']
				);
				$_url = self::link(array(
						'menuaction' => 'bookingfrontend.uisearch.index',
						'activity_top_level' => $activity['id'],
						'building_id' => $building_id,
						'filter_part_of_town' => $imploded_filter_part_of_town));

				$organized_fields = $GLOBALS['phpgw']->custom_fields->get_attribute_tree('booking', ".resource.{$activity['id']}", 0, $activity['id']);
				if (!$organized_fields)
				{
					continue;
				}
				$filter_tree[] = array(
					'text' => $activity['name'],
					'state' => array(
						'opened' => false,
						'selected' => $activity['id'] == $activity_top_level ? true : false,
						'checked' => false,
					//		'checkbox_disabled'	 => true,
					//		'checkbox_hide'		 => true
					),
					'parent' => '#',
//					'a_attr'	 => array('href' => $_url,
//						'activity_top_level' => $activity['id'],
//						'class' => "no_checkbox"
//						),
					'activity_top_level' => $activity['id'],
					'activity_location' => "resource_{$activity['id']}",
//					'id' => "resource_{$activity['id']}",
					'children' => $organized_fields,
				);
			}
//_debug_array($filter_tree);
//die();
//			$params['part_of_towns'] = execMethod('property.sogeneric.get_list', array('type' => 'part_of_town'));
			$params['part_of_towns'] = execMethod('property.solocation.get_booking_part_of_towns');

			foreach ($params['part_of_towns'] as &$part_of_town)
			{
				$part_of_town['checked'] = in_array($part_of_town['id'], $filter_part_of_town);
			}

			$params['top_levels'] = $top_levels;
			$params['filter_tree'] = json_encode($filter_tree);

			self::render_template_xsl('search', $params);
		}

		function query()
		{
			$length = phpgw::get_var('length', 'int', 'REQUEST', null);
			$searchterm = trim(phpgw::get_var('searchterm', 'string', 'REQUEST', null));
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);
			$_filter_part_of_town = explode(',', phpgw::get_var('filter_part_of_town', 'string'));

			$filter_part_of_town = array();
			foreach ($_filter_part_of_town as $key => $value)
			{
				if ($value)
				{
					$filter_part_of_town[] = $value;
				}
			}
			unset($value);
			$_filter_top_level = explode(',', phpgw::get_var('filter_top_level', 'string'));

			$filter_top_level = array();
			foreach ($_filter_top_level as $key => $value)
			{
				if ($value)
				{
					$filter_top_level[] = $value;
				}
			}
			unset($value);

			if (!$filter_top_level)
			{
				$activities = ExecMethod('booking.boactivity.get_top_level');
				foreach ($activities as $activity)
				{
					$filter_top_level[] = $activity['id'];
				}
			}

			$criteria = phpgw::get_var('criteria', 'string', 'REQUEST', array());
			$activity_criteria = array();
			foreach ($criteria as $entry)
			{
				if (isset($entry['activity_top_level']) && !in_array($entry['activity_top_level'], $filter_top_level))
				{
					continue;
				}
				if (isset($entry['activity_top_level']) && $entry['activity_top_level'])
				{
					$activity_criteria[$entry['activity_top_level']]['activity_top_level'] = $entry['activity_top_level'];
				}
				if (isset($entry['cat_id']) && !in_array($entry['cat_id'], $filter_top_level))
				{
					continue;
				}
//				if (isset($entry['choice_id']) && isset($entry['cat_id']))
				if (!empty($entry['cat_id']))
				{
					$activity_criteria[$entry['cat_id']]['activity_top_level'] = $entry['cat_id'];
					$activity_criteria[$entry['cat_id']]['choice'][] = $entry;
				}
			}

			if ($searchterm || $building_id || $activity_criteria || $filter_part_of_town || phpgw::get_var('filter_top_level', 'string') || (phpgw::get_var('filter_search_type') && $searchterm))
			{
				$data = array(
					'results' => $this->bo->search($searchterm, $building_id, $filter_part_of_town, $filter_top_level, $activity_criteria, $length)
				);
			}

			if (phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
			{
				return $data;
			}
			else
			{
				self::render_template_xsl('search_details', $data);
			}
		}


		function resquery()
		{
			$length = phpgw::get_var('length', 'int', 'REQUEST', null);
			$rescategory_id = phpgw::get_var('rescategory_id', 'int', 'REQUEST', null);
			$fields_multiids = array('facility_id', 'part_of_town_id', 'activity_id');
			$multiids = array();
			foreach ($fields_multiids as $field)
			{
				$_ids = explode(',', phpgw::get_var($field, 'string', 'REQUEST', null));
				$ids = array();
				foreach ($_ids as $id)
				{
					if (ctype_digit($id))
					{
						$ids[] = (int)$id;
					}
				}
				$multiids[$field] = array_unique($ids);
			}
			return $this->bo->resquery(array('rescategory_id' => $rescategory_id, 'activity_id' => $multiids['activity_id'],
				'facility_id' => $multiids['facility_id'], 'part_of_town_id' => $multiids['part_of_town_id'], 'length' => $length));
		}

		function query_available_resources()
		{
			$length = phpgw::get_var('length', 'int', 'REQUEST', null);
			$searchterm = trim(phpgw::get_var('searchterm', 'string', 'REQUEST', null));
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);
			$_filter_part_of_town = explode(',', phpgw::get_var('part_of_town_id', 'string'));
			$from_date = phpgw::get_var('from_date', 'string', 'REQUEST', '');
			$to_date = phpgw::get_var('to_date', 'string', 'REQUEST', '');
			$from_time = phpgw::get_var('from_time', 'string', 'REQUEST', '');
			$to_time = phpgw::get_var('to_time', 'string', 'REQUEST', '');

			$filter_part_of_town = array();
			foreach ($_filter_part_of_town as $key => $value)
			{
				if ($value && ctype_digit($value))
				{
					$filter_part_of_town[] = (int)$value;
				}
			}
			unset($value);
			$_filter_top_level = explode(',', phpgw::get_var('filter_top_level', 'string'));

			$filter_top_level = array();
			foreach ($_filter_top_level as $key => $value)
			{
				if ($value)
				{
					$filter_top_level[] = $value;
				}
			}
			unset($value);

			if (!$filter_top_level)
			{
				$activities = ExecMethod('booking.boactivity.get_top_level');
				foreach ($activities as $activity)
				{
					$filter_top_level[] = $activity['id'];
				}
			}

			$criteria = phpgw::get_var('criteria', 'string', 'REQUEST', array());
			$activity_criteria = array();
			foreach ($criteria as $entry)
			{
				if (isset($entry['activity_top_level']) && !in_array($entry['activity_top_level'], $filter_top_level))
				{
					continue;
				}
				if (isset($entry['activity_top_level']) && $entry['activity_top_level'])
				{
					$activity_criteria[$entry['activity_top_level']]['activity_top_level'] = $entry['activity_top_level'];
				}
				if (isset($entry['cat_id']) && !in_array($entry['cat_id'], $filter_top_level))
				{
					continue;
				}
				if (!empty($entry['cat_id']))
				{
					$activity_criteria[$entry['cat_id']]['activity_top_level'] = $entry['cat_id'];
					$activity_criteria[$entry['cat_id']]['choice'][] = $entry;
				}
			}
			$data = $this->bo->search_available_resources($searchterm, $building_id, $filter_part_of_town, $filter_top_level,
					$activity_criteria, $length, array('from_date' => $from_date,
						'to_date' => $to_date, 'from_time' => $from_time, 'to_time' => $to_time, 'length' => $length));

			return $data;
		}

		function resquery_available_resources()
		{
			$length = phpgw::get_var('length', 'int', 'REQUEST', null);
			$rescategory_id = phpgw::get_var('rescategory_id', 'int', 'REQUEST', null);
			$from_date = phpgw::get_var('from_date', 'string', 'REQUEST', '');
			$to_date = phpgw::get_var('to_date', 'string', 'REQUEST', '');
			$from_time = phpgw::get_var('from_time', 'string', 'REQUEST', '');
			$to_time = phpgw::get_var('to_time', 'string', 'REQUEST', '');
			$fields_multiids = array('part_of_town_id');
			$multiids = array();
			foreach ($fields_multiids as $field)
			{
				$_ids = explode(',', phpgw::get_var($field, 'string', 'REQUEST', null));
				$ids = array();
				foreach ($_ids as $id)
				{
					if (ctype_digit($id))
					{
						$ids[] = (int)$id;
					}
				}
				$multiids[$field] = array_unique($ids);
			}
			return $this->bo->resquery_available_resources(array('rescategory_id' => $rescategory_id,
				'part_of_town_id' => $multiids['part_of_town_id'], 'from_date' => $from_date,
				'to_date' => $to_date, 'from_time' => $from_time, 'to_time' => $to_time,  'length' => $length));
		}

		function search_available_resources()
		{
			$_ids = explode(',', phpgw::get_var('resource_ids', 'string', 'REQUEST', ''));
			$ids = array();
			foreach ($_ids as $id)
			{
				$ids[] = (int)$id;
			}
			$from_date = DateTime::createFromFormat('d.m.Y H:i:s', phpgw::get_var('from_date', 'string', 'REQUEST', ''));
			$to_date = DateTime::createFromFormat('d.m.Y H:i:s', phpgw::get_var('to_date', 'string', 'REQUEST', ''));
			return $this->bo->get_all_allocations_and_events_for_resource($ids, $from_date, $to_date);
//			return $this->bo->available_resources($ids, array('from_date' => $from_date, 'to_date' => $to_date));
		}


		function events()
		{
			return $this->bo->events();
		}


		function get_filterboxdata()
		{
			return $this->bo->get_filterboxdata();
		}


		function autocomplete()
		{
			self::link(array(
				'menuaction' => 'bookingfrontend.uisearch.autocomplete',
					'phpgw_return_as' => 'json'));
			echo json_encode($this->bo->getAutoCompleteData());
			exit();
		}

		function autocomplete_resource_and_building()
		{
			self::link(array(
				'menuaction' => 'bookingfrontend.uisearch.autocomplete_resource_and_building',
				'phpgw_return_as' => 'json'));
			return ($this->bo->get_resource_and_building_autocomplete_data());
			return ($this->bo->get_resource_and_building_autocomplete_data());
		}

		function get_all_towns()
		{
			return execMethod('property.solocation.get_booking_part_of_towns');
		}

        function get_search_data_location()
		{
			return execMethod('property.solocation.get_search_data_location');
        }

        function get_search_data_all()
		{
			return execMethod('property.solocation.get_search_data_all');
        }
	}
