<?php
	phpgw::import_class('booking.uiapplication');

	class bookingfrontend_uiapplication extends booking_uiapplication
	{
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
			'show'			=>	true,
			'get_activity_data'	=> true
		);

		function get_activity_data()
		{
			return parent::get_activity_data();
		}

		function show()
		{
			$id = phpgw::get_var('id', 'int');
			$application = $this->bo->read_single($id);

			if($application['secret'] != phpgw::get_var('secret', 'string'))
			{
				$this->redirect(array('menuaction' => 'bookingfrontend.uisearch.index'));
			}

			if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['comment'])
			{
				$this->add_comment($application, $_POST['comment']);
				$this->set_display_in_dashboard($application, true, array('force' => true));
				$application['frontend_modified'] = 'now';
                $this->bo->send_admin_notification($application, $_POST['comment']);

				$receipt = $this->bo->update($application);
				$this->redirect(array('menuaction' => $this->url_prefix . '.show', 'id'=>$application['id'], 'secret'=>$application['secret']));
			}
			

			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['print'])
			{
				$output_type = 'PDF';
				$jasper_parameters = sprintf("\"BK_BUILDING_NAME|%s;BK_APPLICATION_ID|%s\"",
					$application['building_name'],
					$id);
				// DEBUG
				// print_r($jasper_parameters);
				// exit(0);

				$jasper_wrapper 	= CreateObject('phpgwapi.jasper_wrapper');
				$report_source		= PHPGW_SERVER_ROOT.'/booking/jasper/templates/application.jrxml';
				try
				{
					$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
				}
				catch(Exception $e)
				{
					$errors[] = $e->getMessage();
					echo "<pre>\nError: ";print_r($errors[0]);exit;
				}
			}

			$resource_ids = '';
			foreach($application['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			
			//Filter application comments only after update has been attempted unless 
			//you wish to delete the comments not matching the specified types
			$this->filter_application_comments($application, array('comment'));
            
            $activity_path = $this->activity_bo->get_path($application['activity_id']);
            $top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			
			$application['resource_ids'] = $resource_ids;
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];
			self::render_template('application', array('application' => $application, 'audience' => $audience, 'agegroups' => $agegroups, 'frontend'=>'true'));
		}
		
		


	}
