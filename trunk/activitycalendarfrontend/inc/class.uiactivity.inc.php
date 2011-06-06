<?php
	phpgw::import_class('activitycalendar.uiactivities');
	phpgw::import_class('activitycalendar.soactivity');
	
	include_class('activitycalendar', 'activity', 'inc/model/');

	class activitycalendarfrontend_uiactivity extends activitycalendar_uiactivities
	{
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
			'view'			=>	true,
			'index'			=>	true
		);
		
		function view()
		{
			$errorMsgs = array();
			$infoMsgs = array();
			$activity = activitycalendar_soactivity::get_instance()->get_single((int)phpgw::get_var('id'));
			
			if($activity == null) // Not found
			{
				$errorMsgs[] = lang('Could not find specified activity.');
			}
	
			$data = array
			(
				'activity' => $activity,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);
			$this->render('activity.php', $data);
		}

		function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			//var_dump($id);
			$so = activitycalendar_soactivity::get_instance();
			$activity = $so->get_single($id);
			
			//var_dump(phpgw::get_var('secret', 'GET'));
			//var_dump($activity->get_secret());

			if($activity->get_secret() != phpgw::get_var('secret', 'GET'))
			{
				$this->redirect(array('menuaction' => 'bookingfrontend.uisearch.index'));
			}
			
			//var_dump($activity->get_title());
			//$this->redirect(array('menuaction' => 'activitycalendar.uiactivities.edit', 'id' => $id, 'frontend' => 'true'));
						
/*			$application['resource_ids'] = $resource_ids;
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			self::render_template('application', array('application' => $application, 'audience' => $audience, 'agegroups' => $agegroups, 'frontend'=>'true'));
*/
			$categories = $so->get_categories();
			$targets = $so->get_targets();
			$offices = $so->select_district_list();
			$districts = $so->get_districts();
			$arenas = activitycalendar_soarena::get_instance()->get(null, null, null, null, null, null, null);
			$organizations = activitycalendar_soorganization::get_instance()->get(null, null, null, null, null, null, null);
			$groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, null);

			$this->render('activity.php', array
						(
							'activity' 	=> $activity,
							'organizations' => $organizations,
							'groups' => $groups,
							'arenas' => $arenas,
							'categories' => $categories,
							'targets' => $targets,
							'districts' => $districts,
							'offices' => $offices,
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
			);
		}
		
		function index()
		{
			var_dump("inni index");
		}
	}
