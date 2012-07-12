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
			if ($event['customer_organization_number'] != '')
			{
				$orginfo = $this->bo->so->get_org($event['customer_organization_number']);		
				if ($orginfo != array()) { 
					$orginfo['link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $orginfo['id']));
				}
			} else {
				$orginfo = array();
			}
			$event['resources'] = $resources['results'];
			$res_names = array();
			foreach($event['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$event['resource_info'] = join(', ', $res_names);
			$event['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $event['resources'][0]['building_id']));
			$event['when'] = pretty_timestamp($event['from_']).' - '.pretty_timestamp($event['to_']);
			self::render_template('event_info', array('event'=>$event,'orginfo' => $orginfo));
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

			if ($event['secret'] != phpgw::get_var('secret', 'GET'))
			{
				$step = -1; // indicates that an error message should be displayed in the template
				self::render_template('report_numbers', array('event_object' => $booking, 'agegroups' => $agegroups, 'building' => $building, 'step' => $step));
				return false;
			}

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				//reformatting the post variable to fit the booking object
				$temp_agegroup = array();
				$sexes = array('male', 'female');
				foreach($sexes as $sex)
				{
					$i = 0;
					foreach(phpgw::get_var($sex, 'POST') as $agegroup_id => $value)
					{
						$temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
						$temp_agegroup[$i][$sex] = $value;
						$i++;
					}
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
