<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uiresource extends booking_uicommon
	{

		public $public_functions = array
			(
			'index_json' => true,
			'query' => true,
			'show' => true,
			'get_custom' => true,
			'schedule' => true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->activity_bo = CreateObject('booking.boactivity');
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/bookingfrontend/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}

		public function index_json()
		{
			if ($sub_activity_id = phpgw::get_var('sub_activity_id'))
			{
				
				$boactivity = createObject('booking.boactivity');
				$activity_path = $boactivity->get_path($sub_activity_id);
				$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;

				$filter_activity = array($top_level_activity);

				$children = $boactivity->get_children($top_level_activity);

				$_REQUEST['filter_activity_id'] = array_merge($filter_activity, $children);
			}
			return $this->bo->populate_grid_data("bookingfrontend.uiresource.show");
		}

		public function query()
		{
			return $this->index_json();
		}

		public function show()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$resource = $this->bo->read_single(phpgw::get_var('id', 'int', 'GET'));
			$array_resource = array(&$resource);
			$this->bo->add_activity_facility_data($array_resource);
			$pathway = array();
			$lang_home = lang('home');
			$buildinginfo = array();
			$building_fields = array('city', 'deactivate_application', 'deactivate_calendar',
				'email', 'homepage', 'name', 'opening_hours', 'phone', 'street', 'zip_code');
			foreach ($resource['buildings'] as $building_id)
			{
				$building = $this->building_bo->read_single($building_id);
				$building_link = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $building['id']));
				$pathway[] = array(
					'lang_home' => $lang_home,
					'building_name' => $building['name'],
					'building_name' => $building['name'],
					'building_link' => $building_link,
					'resource_name' => $resource['name'],
				);
				foreach ($building_fields as $field)
				{
					$buildinginfo[$field] = $building[$field];
				}
				$buildinginfo['link'] = $building_link;
			}
			if (empty($resource['opening_hours']))
			{
				$resource['opening_hours'] = $buildinginfo['opening_hours'];
			}
			if (empty($resource['contact_info']))
			{
				$contactdata = array();
				foreach (array('homepage','email','phone') as $field)
				{
					if (!empty(trim($buildinginfo[$field])))
					{
						$value = trim($buildinginfo[$field]);
						if ($field == 'homepage')
						{
							if (!preg_match("/^(http|https):\/\//",$value))
							{
								$value = 'http://' . $value;
							}
							$value = sprintf('<a href="%s" target="_blank">%s</a>', $value, $value);
						}
						$contactdata[] = sprintf('%s: %s', lang($field), $value);
					}
				}
				if (!empty($contactdata))
				{
					$resource['contact_info'] = sprintf('<p>%s</p>', join('<br/>',$contactdata));
				}
			}

//			$resource['building']		 = ExecMethod('booking.bobuilding.read_single', $resource['building_id']);
			$resource['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
					'id' => $resource['building_id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index',
					'type' => 'building'));
			$resource['resources_link'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index',
					'type' => 'resource'));
			$resource['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uiresource.schedule',
					'id' => $resource['id']));
			$data = array(
				'resource' => $resource,
				'building' => $buildinginfo,
				'pathway' => $pathway,
				'config_data' => $config->config_data
			);

			self::render_template_xsl('resource', $data);
		}

		private function get_location()
		{
			$activity_id = phpgw::get_var('activity_id', 'int');
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;
			return ".resource.{$top_level_activity}";
		}

		public function get_custom()
		{
			$resource_id = phpgw::get_var('resource_id', 'int');
			$resource = $this->bo->read_single($resource_id);
			$location = $this->get_location();
			$location_id = $GLOBALS['phpgw']->locations->get_id('booking', $location);
			$custom_values = $resource['json_representation'][$location_id];
//			_debug_array($custom_values);

			$custom_fields = createObject('booking.custom_fields');
			$fields = $custom_fields->get_fields($location);
			foreach ($fields as $attrib_id => &$attrib)
			{
				$attrib['value'] = isset($custom_values[$attrib['name']]) ? $custom_values[$attrib['name']] : null;

				if (isset($attrib['choice']) && is_array($attrib['choice']) && $attrib['value'])
				{
					foreach ($attrib['choice'] as &$choice)
					{
						if (is_array($attrib['value']))
						{
							$choice['selected'] = in_array($choice['id'], $attrib['value']) ? 1 : 0;
						}
						else
						{
							$choice['selected'] = $choice['id'] == $attrib['value'] ? 1 : 0;
						}
					}
				}
			}
			$organized_fields = $custom_fields->organize_fields($location, $fields);
//			_debug_array($organized_fields);

			$data = array(
				'attributes_group' => $organized_fields,
			);
			$GLOBALS['phpgw']->xslttpl->add_file(array('attributes_view'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('custom_fields' => $data));
		}

		public function schedule()
		{
			$resource = $this->bo->get_schedule(phpgw::get_var('id', 'int', 'GET'), 'bookingfrontend.uibuilding', 'bookingfrontend.uiresource', 'bookingfrontend.uisearch.index');
			/* FIXME: Sigurd: handle multiple buildings */

			$pathway = array();
			$lang_home = lang('home');
			$lang_schedule = lang('schedule');
			foreach ($resource['buildings'] as $building_id)
			{
				$building = $this->building_bo->read_single($building_id);
				$pathway[] = array(
					'lang_home' => $lang_home,
					'building_name' => $building['name'],
					'building_name' => $building['name'],
					'building_link' => self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
						'id' => $building['id'])),
					'resource_name' => $resource['name'],
					'resource_link' => self::link(array('menuaction' => 'bookingfrontend.uiresource.show',
						'id' => $resource['id'])),
					'lang_schedule' => $lang_schedule,
				);
			}

			//$building = $this->building_bo->read_single($resource['building_id']);

			$building = $this->building_bo->read_single($resource['buildings'][0]);
			$resource['deactivate_application'] = $building['deactivate_application'];
			if ($building['deactivate_application'] == 0)
			{
				$resource['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uiapplication.add',
						//			'building_id' => $resource['building_id'],
						//			'building_name' => $resource['building_name'],
						'building_id' => $building['id'],
						'building_name' => $building['name'],
						'resource' => $resource['id']));
			}
			else
			{
				$resource['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uiresource.schedule',
						'id' => $resource['id']));
			}
			$resource['datasource_url'] = self::link(array(
					'menuaction' => 'bookingfrontend.uibooking.resource_schedule',
					'resource_id' => $resource['id'],
					'phpgw_return_as' => 'json',
			));
			self::add_javascript('bookingfrontend', 'base', 'schedule.js');
			phpgwapi_jquery::load_widget("datepicker");
			$resource['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			self::render_template_xsl('resource_schedule', array('resource' => $resource,
				'pathway' => $pathway));
		}
	}