<?php
	phpgw::import_class('booking.uievent');

	class bookingfrontend_uievent extends booking_uievent
	{
		public $public_functions = array
		(
			'info'				=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
		}
		
		public function info()
		{
			$event = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
			$resources = $this->resource_bo->so->read(array('filters'=>array('id'=>$event['resources']), 'sort'=>'name'));
			$event['resources'] = $resources['results'];
			$res_names = array();
			foreach($event['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$event['resource_info'] = join(', ', $res_names);
			$event['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $allocation['resources'][0]['building_id']));
			$event['when'] = pretty_timestamp($event['from_']).' - '.pretty_timestamp($event['to_']);
			self::render_template('event_info', array('event'=>$event));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}
