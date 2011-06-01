<?php
	phpgw::import_class('activitycalendar.uiactivities');
	phpgw::import_class('activitycalendar.soactivity');

	class activitycalendarfrontend_uiactivity extends activitycalendar_uiactivities
	{
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
			'view'			=>	true,
			'index'			=>	true
		);

		function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			var_dump($id);
			$so = activitycalendar_soactivity::get_instance();
			$activity = $so->get_single($id);

			if($activity->get_secret() != phpgw::get_var('secret', 'GET'))
			{
				$this->redirect(array('menuaction' => 'bookingfrontend.uisearch.index'));
			}
			
			//$this->redirect(array('menuaction' => 'activitycalendar.uiactivities.edit', 'id' => $id, 'frontend' => 'true'));
						
/*			$application['resource_ids'] = $resource_ids;
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			self::render_template('application', array('application' => $application, 'audience' => $audience, 'agegroups' => $agegroups, 'frontend'=>'true'));
*/
		}
		
		function index()
		{
			var_dump("inni index");
		}
	}
