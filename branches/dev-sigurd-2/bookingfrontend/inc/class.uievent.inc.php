<?php
	phpgw::import_class('booking.uievent');

	class bookingfrontend_uievent extends booking_uievent
	{
		public $public_functions = array
		(
			'info'				=>	true,
			'report_numbers' 	=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->group_bo = CreateObject('booking.bogroup');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->season_bo = CreateObject('booking.boseason');
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

		public function report_numbers()
		{
			$step = 1;
			$id = intval(phpgw::get_var('id', 'GET'));
			$event = $this->bo->read_single($id);
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];

			$building_info = $this->bo->so->get_building_info($id);
			$building = $this->building_bo->read_single($building_info['id']);

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				//reformatting the post variable to fit the booking object
				$i = 0;
				$temp_agegroup = array();
				foreach(phpgw::get_var('male', 'POST') as $agegroup_id => $value)
				{
					$temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
					$temp_agegroup[$i]['male'] = $value;
					$i++;
				}

				$i = 0;
				foreach(phpgw::get_var('female', 'POST') as $agegroup_id => $value)
				{
					$temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
					$temp_agegroup[$i]['female'] = $value;
					$i++;
				}
				$event['agegroups'] = $temp_agegroup;
				$event['reminder'] = 2; // status set to delivered
				$errors = $this->bo->validate($event);
				if(!$errors)
				{
					$receipt = $this->bo->update($event);
					$step++;
				}
			}

			self::render_template('report_numbers', array('event_object' => $event, 'agegroups' => $agegroups, 'building' => $building, 'step' => $step));
		}
	}
