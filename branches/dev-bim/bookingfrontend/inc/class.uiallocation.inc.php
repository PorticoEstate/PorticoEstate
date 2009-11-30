<?php
	phpgw::import_class('booking.uiallocation');

	class bookingfrontend_uiallocation extends booking_uiallocation
	{
		public $public_functions = array
		(
			'info'				=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->org_bo = CreateObject('booking.boorganization');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
		}
		
		public function info()
		{
			$allocation = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
			$resources = $this->resource_bo->so->read(array('filters'=>array('id'=>$allocation['resources']), 'sort'=>'name'));
			$allocation['resources'] = $resources['results'];
			$res_names = array();
			foreach($allocation['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $allocation['resources'][0]['building_id']));
			$allocation['org_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $allocation['organization_id']));
			$bouser = CreateObject('bookingfrontend.bouser');
			if($bouser->is_organization_admin($allocation['organization_id']))
				$allocation['add_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.add', 'allocation_id'=>$allocation['id'], 'from_'=>$allocation['from_'], 'to_'=>$allocation['to_']));
			$allocation['when'] = pretty_timestamp($allocation['from_']).' - '.pretty_timestamp($allocation['to_']);
			self::render_template('allocation_info', array('allocation'=>$allocation));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}
