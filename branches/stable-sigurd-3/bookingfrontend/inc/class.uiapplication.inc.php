<?php
	phpgw::import_class('booking.uiapplication');

	class bookingfrontend_uiapplication extends booking_uiapplication
	{
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
			'show'			=>	true,
		);

		function show()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$application = $this->bo->read_single($id);

			if($application['secret'] != phpgw::get_var('secret', 'GET'))
			{
				$this->redirect(array('menuaction' => 'bookingfrontend.uisearch.index'));
			}

			if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['comment'])
			{
				$this->add_comment($application, $_POST['comment']);
				$this->set_display_in_dashboard($application, true, array('force' => true));
				$application['frontend_modified'] = 'now';
				$receipt = $this->bo->update($application);
				$this->redirect(array('menuaction' => $this->url_prefix . '.show', 'id'=>$application['id'], 'secret'=>$application['secret']));
			}
			
			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];
			$resource_ids = '';
			foreach($application['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			
			//Filter application comments only after update has been attempted unless 
			//you wish to delete the comments not matching the specified types
			$this->filter_application_comments($application, array('comment'));
			
			$application['resource_ids'] = $resource_ids;
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			self::render_template('application', array('application' => $application, 'audience' => $audience, 'agegroups' => $agegroups, 'frontend'=>'true'));
		}
	}
