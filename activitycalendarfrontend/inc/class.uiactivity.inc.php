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
		
		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.edit', 'action' => 'new_activity'));
		}
		
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
			
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$this->render('activity.php', $data);
		}

		function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$action = phpgw::get_var('action', 'GET');
			//var_dump($id);
			$so_activity = activitycalendar_soactivity::get_instance();
			//$activity = $so->get_single($id);
			
			//var_dump(phpgw::get_var('secret', 'GET'));
			//var_dump($activity->get_secret());

			
			
			//var_dump($activity->get_title());
			//$this->redirect(array('menuaction' => 'activitycalendar.uiactivities.edit', 'id' => $id, 'frontend' => 'true'));
						
/*			$application['resource_ids'] = $resource_ids;
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			self::render_template('application', array('application' => $application, 'audience' => $audience, 'agegroups' => $agegroups, 'frontend'=>'true'));
*/
			$categories = $so_activity->get_categories();
			$targets = $so_activity->get_targets();
			$offices = $so_activity->select_district_list();
			$districts = $so_activity->get_districts();
			$arenas = activitycalendar_soarena::get_instance()->get(null, null, null, null, null, null, null);
			$organizations = activitycalendar_soorganization::get_instance()->get(null, null, null, null, null, null, null);
			$groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, null);
			
			// Retrieve the activity object or create a new one
			if(isset($id) && $id > 0)
			{	
				$activity = $so_activity->get_single($id); 
			}
			else
			{
				$activity = new activitycalendar_activity();
			}
			
			if($activity->get_secret() != phpgw::get_var('secret', 'GET'))
			{
				if($action != 'new_activity')
				{
					$this->redirect(array('menuaction' => 'bookingfrontend.uisearch.index'));
				}
			}
			
			$g_id = phpgw::get_var('group_id');
			$o_id = phpgw::get_var('organization_id');
			if(isset($g_id) && $g_id > 0)
			{
				$persons = activitycalendar_sogroup::get_instance()->get_contacts($g_id);
				$desc = activitycalendar_sogroup::get_instance()->get_description($g_id);
			}
			else if(isset($o_id) && $o_id > 0)
			{
				$persons = activitycalendar_soorganization::get_instance()->get_contacts($o_id);
				$desc = activitycalendar_soorganization::get_instance()->get_description($o_id);
			}
			
			if(isset($_POST['save_activity'])) // The user has pressed the save button
			{
				if(isset($activity)) // If an activity object is created
				{
					$old_state = $activity->get_state();
					$new_state = phpgw::get_var('state');
	
					// ... set all parameters
					$activity->set_title(phpgw::get_var('title'));
					$activity->set_organization_id(phpgw::get_var('organization_id'));
					$activity->set_group_id(phpgw::get_var('group_id'));
					$activity->set_arena(phpgw::get_var('arena_id'));
					$district_array = phpgw::get_var('district');
					$activity->set_district(implode(",", $district_array));
					$activity->set_office(phpgw::get_var('office'));
					$activity->set_state($new_state);
					$activity->set_category(phpgw::get_var('category'));
					$target_array = phpgw::get_var('target');
					$activity->set_target(implode(",", $target_array));
					$activity->set_description($desc);
					$activity->set_time(phpgw::get_var('time'));
					$activity->set_contact_persons($persons);
					$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
					
					if($so_activity->store($activity)) // ... and then try to store the object
					{
						$message = lang('messages_saved_form');	
					}
					else
					{
						$error = lang('messages_form_error');
					}
	
					if($new_state == 3 || $new_state == 4 || $new_state == 5 )
					{
						$kontor = $so_activity->get_office_name($activity->get_office());
						$subject = "Melding fra AktivBy";
						$body = lang('mail_body_state_' . $new_state, $kontor);
						
						if(isset($g_id) && $g_id > 0)
						{
							activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_2(),$subject,$body);
						}
						else if (isset($o_id) && $o_id > 0)
						{
							activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_2(),$subject,$body);
						}
					}
				}
			}
			
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

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
