<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uiresource extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'schedule'		=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boresource');
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}
		
		public function index()
		{
			return $this->bo->populate_grid_data("bookingfrontend.uiresource.show");
		}

		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$resource['edit_link']      = self::link(array('menuaction' => 'bookingfrontend.uiresource.edit', 'id' => $resource['id']));
			$resource['building_link']  = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $resource['building_id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.index'));
			$resource['schedule_link']  = self::link(array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $resource['id']));
			$resource['activity']       = $this->bo->get_activity_name($resource['activity_id']);
			$data = array(
				'resource'	=>	$resource
			);
			
			self::render_template('resource', $data);
		}

		public function schedule()
		{
            $resource = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'bookingfrontend.uibuilding', 'bookingfrontend.uiresource');

            self::add_javascript('booking', 'booking', 'schedule.js');
            self::render_template('resource_schedule', array('resource' => $resource,));
		}
	}

