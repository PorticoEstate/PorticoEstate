<?php
	phpgw::import_class('activitycalendar.uiactivities');
	phpgw::import_class('activitycalendar.soactivity');
	phpgw::import_class('activitycalendar.sogroup');
	phpgw::import_class('activitycalendar.soarena');
	
	include_class('activitycalendar', 'activity', 'inc/model/');
	include_class('activitycalendar', 'group', 'inc/model/');
	include_class('activitycalendar', 'organization', 'inc/model/');

	class activitycalendarfrontend_uiactivity extends activitycalendar_uiactivities
	{
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
			'view'			=>	true,
			'index'			=>	true,
			'get_organization_groups'	=>	true,
			'get_address_search'	=> true
		);
		
		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/activitycalendarfrontend/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.edit', 'action' => 'new_activity'));
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
			//self::render_template('activity_tmp', array('activity' => $activity, 'frontend'=>'true'));
		}

		function edit()
		{
			$GLOBALS['phpgw']->js->validate_file( 'json', 'json', 'phpgwapi' );

			$id = intval(phpgw::get_var('id', 'GET'));
			$action = phpgw::get_var('action', 'GET');

			$so_activity = activitycalendar_soactivity::get_instance();
			$so_arena = activitycalendar_soarena::get_instance();

			$categories = $so_activity->get_categories();
			$targets = $so_activity->get_targets();
			$offices = $so_activity->select_district_list();
			$districts = $so_activity->get_districts();
			$buildings = $so_arena->get_buildings();
			$arenas = $so_arena->get(null, null, 'arena.arena_name', true, null, null, null);
			$organizations = activitycalendar_soorganization::get_instance()->get(null, null, 'org.name', true, null, null, null);
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
			if(isset($g_id) && is_numeric($g_id) && $g_id > 0)
			{
				/*if($g_id == "new_group")
				{
					//add new group to internal activitycalendar group register
				}
				else*/ 
				//if(is_numeric($g_id) && $g_id > 0)
				//{
					$persons = activitycalendar_sogroup::get_instance()->get_contacts($g_id);
					$desc = activitycalendar_sogroup::get_instance()->get_description($g_id);
				//}
			}
			else if(isset($o_id))
			{
				if($o_id == "new_org")
				{
					$activity->set_new_org(true);
					//add new organization to internal activitycalendar organization register
					$org_info['name'] = phpgw::get_var('orgname');
					$org_info['orgnr'] = phpgw::get_var('orgno');
					$org_info['homepage'] = phpgw::get_var('homepage');
					$org_info['phone'] = phpgw::get_var('phone');
					$org_info['email'] = phpgw::get_var('email');
					$org_info['description'] = phpgw::get_var('org_description');
					$org_info['street'] = phpgw::get_var('address') . ' ' . phpgw::get_var('number') . ', ' . phpgw::get_var('postaddress');
					//$org_info['zip'] = phpgw::get_var('postaddress');
					$org_info['district'] = phpgw::get_var('org_district'); 
					$org_info['status'] = "new";
					$o_id = $so_activity->add_organization_local($org_info);
					
					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('contact1_name');
					$contact1['phone'] = phpgw::get_var('contact1_phone');
					$contact1['mail'] = phpgw::get_var('contact1_email');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = 0;
					$so_activity->add_contact_person_local($contact1);
					
					$contact2 = array();
					$contact2['name'] = phpgw::get_var('contact2_name');
					$contact2['phone'] = phpgw::get_var('contact2_phone');
					$contact2['mail'] = phpgw::get_var('contact2_email');
					$contact2['org_id'] = $o_id;
					$contact2['group_id'] = 0;
					$so_activity->add_contact_person_local($contact2);
					
					$persons = activitycalendar_soorganization::get_instance()->get_contacts_local($o_id);
					$desc = phpgw::get_var('org_description');

				}
				else if($o_id == "change_org")
				{
					$change_org_id = phpgw::get_var('change_organization_id');
					$organization = activitycalendar_soorganization::get_instance()->get_single($change_org_id);
				
					$org_info['name'] = $organization->get_name();
					$org_info['orgnr'] = $organization->get_organization_number();
					$org_info['homepage'] = $organization->get_homepage();
					$org_info['phone'] = $organization->get_phone();
					$org_info['email'] = $organization->get_email();
					$org_info['description'] = $organization->get_description();
					$org_info['street'] = $organization->get_address();
					$org_info['district'] = $organization->get_district(); 
					$org_info['status'] = "change";
					$o_id = $so_activity->add_organization_local($org_info);
					
					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('contact1_name');
					$contact1['phone'] = phpgw::get_var('contact1_phone');
					$contact1['mail'] = phpgw::get_var('contact1_email');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = 0;
					$so_activity->add_contact_person_local($contact1);
					
					$contact2 = array();
					$contact2['name'] = phpgw::get_var('contact2_name');
					$contact2['phone'] = phpgw::get_var('contact2_phone');
					$contact2['mail'] = phpgw::get_var('contact2_email');
					$contact2['org_id'] = $o_id;
					$contact2['group_id'] = 0;
					$so_activity->add_contact_person_local($contact2);
					
					$message = lang('change_request_ok', $organization->get_name());
					
					$GLOBALS['phpgw_info']['flags']['noframework'] = true;

					$this->render('activity.php', array
						(
							'activity' 	=> $activity,
							'organizations' => $organizations,
							'groups' => $groups,
							'arenas' => $arenas,
							'buildings' => $buildings,
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
				else if(is_numeric($o_id) && $o_id > 0)
				{
					if(isset($g_id) && $g_id == "new_group")
					{
						$group_info['name'] = phpgw::get_var('groupname');
						$group_info['organization_id'] = $o_id;
						$group_info['description'] = phpgw::get_var('group_description');
						$group_info['status'] = "new";
						$g_id = $so_activity->add_group_local($group_info);
						
						//add contact persons
						$contact1 = array();
						$contact1['name'] = phpgw::get_var('contact1_name');
						$contact1['phone'] = phpgw::get_var('contact1_phone');
						$contact1['mail'] = phpgw::get_var('contact1_email');
						$contact1['org_id'] = 0;
						$contact1['group_id'] = $g_id;
						$so_activity->add_contact_person_local($contact1);
						
						$contact2 = array();
						$contact2['name'] = phpgw::get_var('contact2_name');
						$contact2['phone'] = phpgw::get_var('contact2_phone');
						$contact2['mail'] = phpgw::get_var('contact2_email');
						$contact2['org_id'] = 0;
						$contact2['group_id'] = $g_id;
						$so_activity->add_contact_person_local($contact2);
						
						$activity_persons = activitycalendar_sogroup::get_instance()->get_contacts_local($g_id);
						$desc = phpgw::get_var('group_description');
					}
					else
					{
						$persons = activitycalendar_soorganization::get_instance()->get_contacts($o_id);
						$desc = activitycalendar_soorganization::get_instance()->get_description($o_id);
					}
				}
			}
			
			if(isset($_POST['save_activity'])) // The user has pressed the save button
			{
				if(isset($activity)) // If an activity object is created
				{

					$old_state = $activity->get_state();
					$new_state = phpgw::get_var('state');
					// ... set all parameters
					$activity->set_title(phpgw::get_var('title'));
					$activity->set_organization_id($o_id);
					$activity->set_group_id($g_id);
					$activity->set_arena(phpgw::get_var('arena_id'));
					$activity->set_internal_arena(phpgw::get_var('internal_arena_id'));
					$district_array = phpgw::get_var('district');
					$activity->set_district(implode(",", $district_array));
					$activity->set_office(phpgw::get_var('office'));
					if($action == 'new_activity')
					{
						$activity->set_state(1);
						$new_state=1;
					}
					else
					{
						$activity->set_state($new_state);
					}
					$activity->set_category(phpgw::get_var('category'));
					$target_array = phpgw::get_var('target');
					$activity->set_target(implode(",", $target_array));
					$activity->set_description($desc);
					$activity->set_time(phpgw::get_var('time'));
					$activity->set_contact_persons($persons);
					$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
					$activity->set_frontend(true);

					$target_ok = false;
					$district_ok = false;
					if($activity->get_target() && $activity->get_target() != '')
					{
						$target_ok = true;
					}
					if($activity->get_district() && $activity->get_district() != '')
					{
						$district_ok = true;
					}
					
					if($target_ok && $district_ok)
					{
						
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
						$GLOBALS['phpgw_info']['flags']['noframework'] = true;
	
						$this->render('activity.php', array
									(
										'activity' 	=> $activity,
										'organizations' => $organizations,
										'groups' => $groups,
										'arenas' => $arenas,
										'buildings' => $buildings,
										'categories' => $categories,
										'targets' => $targets,
										'districts' => $districts,
										'offices' => $offices,
										'message' => isset($message) ? $message : phpgw::get_var('message'),
										'error' => isset($error) ? $error : phpgw::get_var('error')
									)
						);
					}
					else
					{
						if(!$target_ok)
						{
							$error .= "<br/>" . lang('target_not_selected');
						}
						if(!$district_ok)
						{
							$error .= "<br/>" . lang('district_not_selected');
						}
						return $this->render('activity.php', array
							(
								'activity' 	=> $activity,
								'organizations' => $organizations,
								'org_name' => $org_name,
								'groups' => $groups,
								'arenas' => $arenas,
								'buildings' => $buildings,
								'categories' => $categories,
								'targets' => $targets,
								'districts' => $districts,
								'offices' => $offices,
								'editable' => true,
								'cancel_link' => $cancel_link,
								'message' => isset($message) ? $message : phpgw::get_var('message'),
								'error' => isset($error) ? $error : phpgw::get_var('error')
							)	
						);
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
							'buildings' => $buildings,
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
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.add'));
			//var_dump("inni index");
		}
		
		public function get_organization_groups()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true; 
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true; 
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			
			$org_id = phpgw::get_var('orgid');
			$group_id = phpgw::get_var('groupid');
			$returnHTML = "<option value='0'>Ingen gruppe valgt</option>";
			if($org_id)
			{
				$group_html[] = "<option value='new_group'>Ny gruppe</option>";
				$groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, array('org_id' => $org_id));
				foreach ($groups as $group) {
					if(isset($group))
					{
						//$res_g = $group->serialize();
						$selected = "";
						if($group_id && $group_id > 0)
						{
							$gr_id = (int)$group_id; 
							if($gr_id == (int)$group->get_id())
							{
								$selected_group = " selected";
							}
						}
						$group_html[] = "<option value='" . $group->get_id() . "'". $selected_group . ">" . $group->get_name() . "</option>";
					}
				}
			    $html = implode(' ' , $group_html);
			    $returnHTML = $returnHTML . ' ' . $html;
			}
			
			
			return $returnHTML;
			//return "<option>Ingen gruppe valgt</option>";
		}
		
		/**
		 * Public method.
		 */
		public function get_address_search()
		{
			$search_string = phpgw::get_var('search');
			//var_dump($search_string);
			return activitycalendar_soarena::get_instance()->get_address($search_string);
		}
	}
