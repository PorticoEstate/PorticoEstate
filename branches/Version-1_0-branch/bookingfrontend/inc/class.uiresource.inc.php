<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uiresource extends booking_uicommon
	{
		public $public_functions = array
		(
			'index_json'	=>	true,
			'show'			=>	true,
			'schedule'		=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boresource');
            $this->building_bo = CreateObject('booking.bobuilding');
            $old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}
		
		public function index_json()
		{
			return $this->bo->populate_grid_data("bookingfrontend.uiresource.show");
		}

		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$resource['building_link']  = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $resource['building_id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index', 'type' => 'building'));
			$resource['resources_link'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index', 'type' => 'resource'));
			$resource['schedule_link']  = self::link(array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $resource['id']));
			$data = array(
				'resource'	=>	$resource
			);
			
			self::render_template('resource', $data);
		}

		public function schedule()
		{
            $resource = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'bookingfrontend.uibuilding', 'bookingfrontend.uiresource', 'bookingfrontend.uisearch.index');
            $building = $this->building_bo->read_single($resource['building_id']);
            $resource['deactivate_application'] = $building['deactivate_application'];
            if ($building['deactivate_application'] == 0) {
                $resource['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uiapplication.add',
                    'building_id' => $resource['building_id'],
                    'building_name'=>$resource['building_name'],
                    'resource' => $resource['id']));
            } else {
                $resource['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uiresource.schedule',
                    'id' => $resource['id']));
            }
            $resource['datasource_url'] = self::link(array(
				'menuaction' => 'bookingfrontend.uibooking.resource_schedule', 
				'resource_id' => $resource['id'], 
				'phpgw_return_as' => 'json',
			));
            self::add_javascript('booking', 'booking', 'schedule.js');
            self::render_template('resource_schedule', array('resource' => $resource,));
		}
	}

