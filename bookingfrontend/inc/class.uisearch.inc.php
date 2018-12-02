<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uisearch extends booking_uicommon
	{

		public $public_functions = array
			(
			'autocomplete'      => true,
			'events'            => true,
			'get_filterboxdata' => true,
			'index'             => true,
			'query'             => true,
			'resquery'          => true,
		);

		function __construct()
		{

			parent::__construct();
			$this->bo = CreateObject('bookingfrontend.bosearch');
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}

		function index()
		{
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('treeview');

			self::add_javascript('bookingfrontend', 'base', 'search.js');
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$searchterm = trim(phpgw::get_var('searchterm', 'string', 'REQUEST', null));
			$type = phpgw::get_var('type', 'string', 'REQUEST', null);
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);
			$filter_part_of_town = explode(',', phpgw::get_var('filter_part_of_town', 'string'));
			$imploded_filter_part_of_town = implode(',', $filter_part_of_town);
			$search = null;

			$criteria = phpgw::get_var('criteria');
//			_debug_array($criteria);die();

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
				$frontimagetext = '<h2>Din portal til</h2><h1><b>AKTIVITETER OG LOKALER</b></h1><h2>Nært deg.</h2>';
			}

			if ($config->config_data['frontpage_filterboxtitle'] != '')
			{
				$filterboxtitle = $config->config_data['frontpage_filterboxtitle'];
			}
			else
			{
				$filterboxtitle = lang('Choose categories');
			}

			$params = array(
				'baseurl' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}",
				'filterboxtitle' => $filterboxtitle,
				'frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/bkbooking/images/newlayout/forsidebilde.jpg",
				'frontpagetitle' => $frontpagetitle,
				'frontpagetext' => $frontpagetext,
				'frontimagetext' => $frontimagetext,
				'activity_top_level' => $activity_top_level
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

//			_debug_array($building_id);
//			_debug_array($filter_part_of_town);
//			_debug_array($activity_criteria);
//			_debug_array($criteria);
//			die();
			if ($searchterm || $building_id || $activity_criteria || $filter_part_of_town || phpgw::get_var('filter_top_level', 'string') || (phpgw::get_var('filter_search_type') && $searchterm))
			{
				$data = array(
					'results' => $this->bo->search($searchterm, $building_id, $filter_part_of_town, $filter_top_level, $activity_criteria)
				);
			}
			self::render_template_xsl('search_details', $data);
		}


		function resquery()
		{
			$rescategory_id = phpgw::get_var('rescategory_id', 'int', 'REQUEST', null);
			$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', null);
			$fields_multiids = array('facility_id', 'part_of_town_id');
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
			return $this->bo->resquery(array('rescategory_id' => $rescategory_id, 'activity_id' => $activity_id,
				'facility_id' => $multiids['facility_id'], 'part_of_town_id' => $multiids['part_of_town_id']));
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

	}
