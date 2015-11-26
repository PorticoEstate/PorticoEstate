<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uisearch extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'	=>	true,
			'query'	=>	true
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

			self::add_javascript('bookingfrontend', 'bookingfrontend', 'search.js');
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$searchterm = trim(phpgw::get_var('searchterm', 'string', 'REQUEST', null));
			$type = phpgw::get_var('type','string', 'REQUEST', null);
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);
			$filter_part_of_town = explode(',', phpgw::get_var('filter_part_of_town','string'));
			$imploded_filter_part_of_town = implode(',', $filter_part_of_town);
			$search = null;

			$criteria = phpgw::get_var('criteria');
//			_debug_array($criteria);die();

			if ($config->config_data['frontpagetext'] != '')
			{
				$frontpagetext = $config->config_data['frontpagetext'];
			}
			else
			{
				$frontpagetext = 'Velkommen til AktivBy.<br />Her finner du informasjon om idrettsanlegg som leies ut<br />av idrettsavdelingen.';
			}
			

			$params	= array(
				'baseurl'		=> "{$GLOBALS['phpgw_info']['server']['webserver_url']}",
				'frontimage'	=> "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/bkbooking/images/newlayout/forsidebilde.jpg",
				'frontpagetext' => $frontpagetext,
				'activity_top_level' => $activity_top_level
			);

			$bobuilding = CreateObject('booking.bobuilding');
			$building = $bobuilding->read_single($building_id);
			$params['building_name'] = $building['name'];
			$params['building_id'] = $building_id;

			$activities	= ExecMethod('booking.boactivity.get_top_level');

			$filter_tree = array();
			foreach($activities as &$activity)
			{
				$_url	= self::link(array(
							'menuaction'			=> 'bookingfrontend.uisearch.index',
							'activity_top_level'	=> $activity['id'],
							'building_id'			=> $building_id,
							'filter_part_of_town'	=> $imploded_filter_part_of_town));

				$organized_fields = $GLOBALS['phpgw']->custom_fields->get_attribute_tree('booking', ".resource.{$activity['id']}");
				$filter_tree[] = array(
					'text'		=> $activity['id'] == $activity_top_level ? "[{$activity['name']}]" : $activity['name'],
					'state'		=> array (
							'opened' => false,
							'selected'	=> $activity['id'] == $activity_top_level ? true : false,
							'checked'	=> false,
							'checkbox_disabled' => true,
							'checkbox_hide' => true
						),
					'parent'	=> '#',
					'a_attr'	=> array('href' => $_url,'activity_top_level' => $activity['id'], 'class' => "no_checkbox"),
					'children'	=> $organized_fields,
 				);


			}
//_debug_array($filter_tree);
//die();
			$params['part_of_towns'] = execMethod('property.sogeneric.get_list', array('type' => 'part_of_town'));
			
			foreach ($params['part_of_towns'] as &$part_of_town)
			{
				$part_of_town['checked'] = in_array($part_of_town['id'], $filter_part_of_town);
			}

			$params['filter_tree'] = json_encode($filter_tree);


            self::render_template_xsl('search', $params);

		}

		function query()
		{
			$searchterm = trim(phpgw::get_var('searchterm', 'string', 'REQUEST', null));
			$type = phpgw::get_var('type','string', 'REQUEST', null);
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);
			$filter_part_of_town = explode(',', phpgw::get_var('filter_part_of_town','string'));
			$imploded_filter_part_of_town = implode(',', $filter_part_of_town);
			$search = null;

			$criteria = phpgw::get_var('criteria');
//			_debug_array($criteria);die();
			if ($building_id || $type || $activity_top_level || (isset($filter_part_of_town[0]) && $filter_part_of_town[0]))
			{
				$data = array(
					'results'    => $this->bo->search($searchterm, $activity_top_level, $building_id, $filter_part_of_town)
				);
			}
			self::render_template_xsl('search_details', $data);
		}	
	}
