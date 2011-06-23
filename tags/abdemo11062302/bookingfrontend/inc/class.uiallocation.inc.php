<?php
	phpgw::import_class('booking.uiallocation');

	class bookingfrontend_uiallocation extends booking_uiallocation
	{
		public $public_functions = array
		(
			'info'				=>	true,
			'cancel'				=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->org_bo = CreateObject('booking.boorganization');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->system_message_bo = CreateObject('booking.bosystem_message');
		}
		public function cancel()
		{
        	$allocation = $this->bo->read_single(intval(phpgw::get_var('allocation_id', 'GET')));

   			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
            
                $from = $_POST['from_'];
                $to =  $_POST['to_'];
                $organization_id = $_POST['organization_id'];
                $outseason = $_POST['outseason'];
                $recurring = $_POST['recurring'];
                $repeat_until = $_POST['repeat_until'];
                $field_interval = $_POST['field_interval'];
                    
				date_default_timezone_set("Europe/Oslo");
				$date = new DateTime(phpgw::get_var('date'));
				$system_message = array();
				$system_message['building_id'] = intval($allocation['building_id']);
				$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
				$system_message['created'] =  $date->format('Y-m-d  H:m');
				$system_message = array_merge($system_message, extract_values($_POST, array('message')));
                $system_message['type'] = 'cancelation';
				$system_message['status'] = 'NEW';
				$system_message['name'] = ' ';
				$system_message['phone'] = ' ';
				$system_message['email'] = ' ';
				$system_message['title'] = lang('Cancelation of allocation from')." ".$allocation['organization_name'];
                $link = self::link(array('menuaction' => 'booking.uiallocation.delete','allocation_id' => $allocation['id'], 'outseason' => $outseason, 'recurring' => $recurring, 'repeat_until' => $repeat_until, 'field_interval' => $field_interval));
                $link = mb_strcut($link,16,strlen($link));
                $system_message['message'] = $system_message['message']."\n\n".lang('To cancel allocation use this link')." - <a href='".$link."'>".lang('Delete')."</a>";

				$receipt = $this->system_message_bo->add($system_message);
				$this->redirect(array('menuaction' =>  'bookingfrontend.uibuilding.schedule', 'id' => $system_message['building_id']));

            }
            $this->flash_form_errors($errors);
			$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $allocation['building_id']));

			$this->use_yui_editor();
			self::render_template('allocation_cancel', array('allocation'=>$allocation));
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
			$allocation['resource'] = phpgw::get_var('resource', 'GET');
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $allocation['resources'][0]['building_id']));
			$allocation['org_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $allocation['organization_id']));
			$bouser = CreateObject('bookingfrontend.bouser');
			if($bouser->is_organization_admin($allocation['organization_id'])) {
				$allocation['add_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.add', 'allocation_id'=>$allocation['id'], 'from_'=>$allocation['from_'], 'to_'=>$allocation['to_'], 'resource'=>$allocation['resource']));
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiallocation.cancel', 'allocation_id'=>$allocation['id'], 'from_'=>$allocation['from_'], 'to_'=>$allocation['to_'], 'resource'=>$allocation['resource']));
            }
			$allocation['when'] = pretty_timestamp($allocation['from_']).' - '.pretty_timestamp($allocation['to_']);
			self::render_template('allocation_info', array('allocation'=>$allocation));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}
