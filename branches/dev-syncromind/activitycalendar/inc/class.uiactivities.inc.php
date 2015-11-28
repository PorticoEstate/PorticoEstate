<?php
	phpgw::import_class('activitycalendar.uicommon');
	phpgw::import_class('activitycalendar.soactivity');
	phpgw::import_class('activitycalendar.soarena');
	phpgw::import_class('activitycalendar.soorganization');
	phpgw::import_class('activitycalendar.sogroup');

	include_class('activitycalendar', 'activity', 'inc/model/');

	class activitycalendar_uiactivities extends activitycalendar_uicommon
	{

		protected $so_org;
		protected $so_group;
		protected $so_contact;
		protected $so_activity;
		protected $so_arena;
		private $validator;
		private $config_booking;
		private $debug;
		public $public_functions = array
			(
			'index'						 => true,
			'index_json'				 => true,
			'query'						 => true,
			'view'						 => true,
			'add'						 => true,
			'edit'						 => true,
			'download'					 => true,
			'send_mail'					 => true,
			'get_organization_groups'	 => true,
			'create_groups'				 => true,
			'remove_old_activities'		 => true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo_org			 = CreateObject('booking.boorganization');
			$this->bo_group			 = CreateObject('booking.bogroup');
			$this->so_org			 = activitycalendar_soorganization::get_instance();
			$this->so_group			 = activitycalendar_sogroup::get_instance();
			$this->so_contact		 = activitycalendar_socontactperson::get_instance();
			$this->so_activity		 = activitycalendar_soactivity::get_instance();
			$this->so_arena			 = activitycalendar_soarena::get_instance();
			self::set_active_menu('activitycalendar::activities');
			$config					 = CreateObject('phpgwapi.config', 'activitycalendar');
			$config->read();
			$this->config_booking	 = CreateObject('phpgwapi.config', 'booking');
			$this->config_booking->read();

			$this->validator = CreateObject('phpgwapi.EmailAddressValidator');

			$this->debug = false;
		}

		private function _get_filters()
		{
			$filters = array();
			
			$activity_state_options = array
			(
				array('id' => 'all', 'name' => lang('all')),
				array('id' => '1', 'name' => lang('new')),
				array('id' => '2', 'name' => lang('change')),
				array('id' => '3', 'name' => lang('published')),
				array('id' => '5', 'name' => lang('rejected'))
			);
			
			$filters[] = array
						(
							'type'   => 'filter',
							'name'   => 'activity_state',
							'text'   => lang('activity_state'),
							'list'   => $activity_state_options
						);
		
			$activity_district_options[] = array('id'=>'all', 'name'=>lang('all'));
			$districts = activitycalendar_soactivity::get_instance()->select_district_list();
			foreach($districts as $district)
			{
				$activity_district_options[] = array('id'=>$district['id'], 'name'=>$district['name']);	
			}
				
			$filters[] = array
						(
							'type'   => 'filter',
							'name'   => 'activity_district',
							'text'   => lang('office'),
							'list'   => $activity_district_options
						);
			
			$activity_category_options[] = array('id'=>'all', 'name'=>lang('all'));
			$categories = activitycalendar_soactivity::get_instance()->get_categories();
			foreach($categories as $category)
			{
				$activity_category_options[] = array('id'=>$category->get_id(), 'name'=>$category->get_name());				
			}
			
			$filters[] = array
						(
							'type'   => 'filter',
							'name'   => 'activity_category',
							'text'   => lang('Category'),
							'list'   => $activity_category_options
						);
			
			return $filters;
		}
		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.edit'));
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$appname = lang('activities');

			$function_msg = lang('list %1', $appname);
			$type = 'all_activities';

			$data = array(
				'datatable_name'	=> $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type'	 => 'date-picker',
								'id'	 => 'date_change',
								'name'	 => 'date_change',
								'value'	 => '',
								'text'	 => lang('date')
							),							
							array(
								'type'   => 'link',
								'value'  => lang('new'),
								'href'   => self::link(array(
									'menuaction'	=> 'activitycalendar.uiactivities.add'
								)),
								'class'  => 'new_item'
							)							
						)
					)
				),
				'datatable' => array(
					'source'	=> self::link(array(
						'menuaction'	=> 'activitycalendar.uiactivities.index', 
						'type'			=> $type,
						'phpgw_return_as' => 'json'
					)),
					'download'	=> self::link(array('menuaction' => 'activitycalendar.uiactivities.download',
							'type'		=> $type,
							'export'    => true,
							'allrows'   => true
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array(
							array('key'=>'id', 'label'=>lang('id'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'title', 'label'=>lang('title'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'state', 'label'=>lang('status'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'organization_id', 'label'=>lang('organization'), 'sortable'=>true, 'hidden'=>false),				
							array('key'=>'group_id', 'label'=>lang('group'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'district', 'label'=>lang('district'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'office', 'label'=>lang('office'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'category', 'label'=>lang('category'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'description', 'label'=>lang('description'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'arena', 'label'=>lang('arena'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'time', 'label'=>lang('time'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'contact_person_1', 'label'=>lang('contact_person_1'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'contact_person_2', 'label'=>lang('contact_person_2'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'last_change_date', 'label'=>lang('last_change_date'), 'sortable'=>true, 'hidden'=>false)
					)
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'show',
					'text'			=> lang('show'),
					'action'		=> self::link(array(
							'menuaction'	=> 'activitycalendar.uiactivities.view'
					)),
					'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))		
				);

			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'edit',
					'text'			=> lang('edit'),			
					'action'		=> self::link(array(
							'menuaction'	=> 'activitycalendar.uiactivities.edit'
					)),
					'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
				);
			
			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'send_mail',
					'text'			=> lang('send_mail'),
					'type'			=> 'custom',
					'custom_code'	=> "
						var oArgs = ".json_encode(array(
								'menuaction'		=> 'activitycalendar.uiactivities.send_mail', 
								'message_type'		=> 'update',
								'phpgw_return_as'	=> 'json'
							)).";
						var parameters = ".json_encode(array('parameter'=>array(array('name'=>'activity_id', 'source'=>'id')))).";
						sendMail(oArgs, parameters);
					"
				);
			
			$GLOBALS['phpgw']->jqcal->add_listener('filter_date_change');
			
			self::add_javascript('activitycalendar', 'activitycalendar', 'activities.index.js');
			self::render_template_xsl('datatable_jquery', $data);			
		}

		/**
		 * Displays info about one single billing job.
		 */
		public function view()
		{
			$errorMsgs	 = array();
			$infoMsgs	 = array();

			$activity	 = $this->so_activity->get_single((int)phpgw::get_var('id'));
			$cancel_link = self::link(array('menuaction' => 'activitycalendar.uiactivities.index'));
			$saved_OK	 = phpgw::get_var('saved_ok');
			if($saved_OK)
			{
				$message = lang('activity_saved_form');
			}

			if($activity == null) // Not found
			{
				$errorMsgs[] = lang('Could not find specified activity.');
			}

			if(isset($_POST['edit_activity'])) // The user has pressed the save button
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.edit',
					'id' => phpgw::get_var('id')));
			}

			$data = array
				(
				'activity'		 => $activity,
				'cancel_link'	 => $cancel_link,
				'message'		 => $message,
				'errorMsgs'		 => $errorMsgs,
				'infoMsgs'		 => $infoMsgs
			);
			$this->render('activity.php', $data);
		}

		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			// Get the contract part id
			$activity_id = (int)phpgw::get_var('id');
			$cancel_link = self::link(array('menuaction' => 'activitycalendar.uiactivities.index'));
			$categories	 = $this->so_activity->get_categories();
			$targets	 = $this->so_activity->get_targets();
			$offices	 = $this->so_activity->select_district_list();
			$districts	 = $this->so_activity->get_districts();
			$buildings	 = $this->so_arena->get_buildings();
			// Retrieve the activity object or create a new one
			if(isset($activity_id) && $activity_id > 0)
			{
				$activity = $this->so_activity->get_single($activity_id);
			}
			else
			{
				$activity = new activitycalendar_activity();
			}
			$new_group	 = $activity->get_new_group();
			$g_id		 = phpgw::get_var('group_id');
			$o_id		 = phpgw::get_var('organization_id');
			if($new_group)
			{
				$persons = $this->so_group->get_contacts_local($activity->get_group_id());
				$desc	 = $this->so_group->get_description_local($activity->get_group_id());
			}
			else if(isset($g_id) && $g_id > 0)
			{
				$persons = $this->so_group->get_contacts($g_id);
				$desc	 = $this->so_group->get_description($g_id);
			}
			else if(isset($o_id) && $o_id > 0)
			{
				$persons = $this->so_org->get_contacts($o_id);
				$desc	 = $this->so_org->get_description($o_id);
			}

			if(strlen($desc) > 254)
			{
				$desc = substr($desc, 0, 254);
			}
			$arenas = $this->so_arena->get(null, null, 'arena.arena_name', true, null, null, null);
			if($activity->get_new_org())
			{
				$org_name = $this->so_org->get_organization_name_local($activity->get_organization_id());
			}
			else
			{
				$organizations = $this->so_org->get(null, null, 'org.name', true, null, null, null);
			}
			if($new_group)
			{
				$group_array = $this->so_group->get(null, null, null, null, null, null, array(
					'group_id' => $activity->get_group_id(), 'new_groups' => 'true'));
				//var_dump($group_array);
				if(count($group_array) > 0)
				{
					$keys		 = array_keys($group_array);
					$local_group = $group_array[$keys[0]];
					//$group_name = $local_group->get_name();
				}
			}
			else
			{
				$groups = $this->so_group->get(null, null, null, null, null, null, null);
			}

			if(isset($_POST['save_activity'])) // The user has pressed the save button
			{
				if(isset($activity)) // If an activity object is created
				{
					$old_state		 = $activity->get_state();
					$new_state		 = phpgw::get_var('state');
					// ... set all parameters
					$activity->set_title(phpgw::get_var('title'));
					$activity->set_organization_id(phpgw::get_var('organization_id'));
					$activity->set_group_id(phpgw::get_var('group_id'));
					$internal_arena	 = phpgw::get_var('internal_arena_id');
					if(isset($internal_arena) && $internal_arena > 0)
					{
						$activity->set_arena(0);
						$activity->set_internal_arena($internal_arena);
					}
					else
					{
						$activity->set_arena(phpgw::get_var('arena_id'));
						$activity->set_internal_arena(0);
					}
					$district_array	 = phpgw::get_var('district');
					$activity->set_district(implode(",", $district_array));
					$activity->set_office(phpgw::get_var('office'));
					$activity->set_state($new_state);
					$activity->set_category(phpgw::get_var('category'));
					$target_array	 = phpgw::get_var('target');
					$activity->set_target(implode(",", $target_array));
					$activity->set_description($desc);
					$activity->set_time(phpgw::get_var('time'));
					$activity->set_contact_persons($persons);
					$activity->set_contact_person_2_address(phpgw::get_var('contact_person_2_address'));
					$activity->set_contact_person_2_zip(phpgw::get_var('contact_person_2_zip'));
					$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));

					$target_ok	 = false;
					$district_ok = false;
					if($new_state != 5)
					{
						$target_ok	 = true;
						$district_ok = true;
					}
					else
					{
						if($activity->get_target() && $activity->get_target() != '')
						{
							$target_ok = true;
						}
						if($activity->get_district() && $activity->get_district() != '')
						{
							$district_ok = true;
						}
					}

					if($target_ok && $district_ok)
					{
						if($this->so_activity->store($activity)) // ... and then try to store the object
						{
							if($new_group)
							{
								//transfer group to booking
								$group_array = $this->so_group->get(null, null, null, null, null, null, array(
									'group_id' => $activity->get_group_id(), 'new_groups' => 'true'));
								if(count($group_array) > 0)
								{
									$keys	 = array_keys($group_array);
									$group	 = $group_array[$keys[0]];
								}

								$group_info						 = array();
								$group_info['name']				 = $group->get_name(); //new
								$group_info['organization_id']	 = $activity->get_organization_id();
								$group_info['description']		 = $group->get_description();

								$contacts	 = $this->so_contact->get_local_contact_persons($group->get_id(), true);
								$contact_1	 = $contacts[0];

								$new_group_id = $this->so_group->transfer_group($group_info);
								if($new_group_id)
								{
									//update activity with new org id
									//add contact persons to booking
									$contact1				 = array();
									$contact1['name']		 = $contact_1->get_name();
									$contact1['phone']		 = $contact_1->get_phone();
									$contact1['mail']		 = $contact_1->get_email();
									$contact1['group_id']	 = $new_group_id;
									$this->so_activity->add_contact_person_group($contact1);

									$message = lang('messages_saved_form');

									//get organization_id for the group:
									$group_org_id = $this->so_group->get_orgid_from_group($new_group_id);

									//get affected activities and update with new org id
									$update_activities = $this->so_activity->get_activities_for_update($group->get_id(), true);

									foreach($update_activities as $act_id)
									{
										$act = $this->so_activity->get_single($act_id);
										$act->set_organization_id($group_org_id);
										$act->set_group_id($new_group_id);
										$act->set_new_org(false);
										$act->set_new_group(false);
										$this->so_activity->store($act);
									}

									//set local group as stored
									$group->set_change_type('added');
									$group->set_transferred(true);

									$this->so_group->update_local($group);
									$message		 = lang('messages_saved_form');
									//var_dump($new_group_id);
									$contact_persons = $this->so_contact->get_booking_contact_persons($new_group_id, true);
									//var_dump(2);
									$cp1			 = $contact_persons[0];
								}
							}
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}

						if(isset($activity_id) && $activity_id > 0)
						{
							$activity = $this->so_activity->get_single($activity_id);
						}

						if($old_state != $new_state && ($new_state == 3 || $new_state == 5))
						{
							$kontor	 = $this->so_activity->get_office_name($activity->get_office());
							$subject = lang('mail_subject_update');
							$body	 = lang('mail_body_state_' . $new_state, $activity->get_title(), $kontor);

							if($activity->get_group_id() && $activity->get_group_id() > 0)
							{
								$activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_group_id(), true));
								activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_1(), $subject, $body);
							}
							else if($activity->get_organization_id() && $activity->get_organization_id() > 0)
							{
								$activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_organization_id()));
								activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_1(), $subject, $body);
							}
						}
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.view',
							'id' => $activity->get_id(), 'saved_ok' => 'yes'));
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
							'activity'		 => $activity,
							'organizations'	 => $organizations,
							'org_name'		 => $org_name,
							'groups'		 => $groups,
							'local_group'	 => $local_group,
							'arenas'		 => $arenas,
							'buildings'		 => $buildings,
							'categories'	 => $categories,
							'targets'		 => $targets,
							'districts'		 => $districts,
							'offices'		 => $offices,
							'editable'		 => true,
							'cancel_link'	 => $cancel_link,
							'message'		 => isset($message) ? $message : phpgw::get_var('message'),
							'error'			 => isset($error) ? $error : phpgw::get_var('error')
						)
						);
					}
				}
			}
			$editable = true;
			if($activity->get_new_org())
			{
				$error		 = lang('org_not_transferred');
				$editable	 = false;
			}

			if($activity->get_group_id())
			{
				if($activity->get_new_group())
				{
					$description = activitycalendar_sogroup::get_instance()->get_description_local($activity->get_group_id());
				}
				else
				{
					$description = activitycalendar_sogroup::get_instance()->get_description($activity->get_group_id());
				}
			}
			else if($activity->get_organization_id())
			{
				if($activity->get_new_org())
				{
					$description = activitycalendar_soorganization::get_instance()->get_description_local($activity->get_organization_id());
				}
				else
				{
					$description = activitycalendar_soorganization::get_instance()->get_description($activity->get_organization_id());
				}
			}
			
			$selected_state = $activity->get_state();
			$state_options = array
			(
				array('id'=>'1', 'name'=>lang('new'), 'selected'=>(($selected_state == 1) ? 1 : 0)),
				array('id'=>'2', 'name'=>lang('change'), 'selected'=>(($selected_state == 2) ? 1 : 0)),
				array('id'=>'3', 'name'=>lang('published'), 'selected'=>(($selected_state == 3) ? 1 : 0)),
				array('id'=>'5', 'name'=>lang('rejected'), 'selected'=>(($selected_state == 4) ? 1 : 0))
			);
			
			$category_options[] = array('id'=>'0', 'name'=>lang('Ingen kategori valgt'), 'selected'=>0);
			$current_category_id = $activity->get_category();
			foreach($categories as $category)
			{
				$id = $category->get_id();
				$selected = ($current_category_id == $id) ? 1 : 0;
				$category_options[] = array('id'=>$id, 'name'=>$category->get_name(), 'selected'=>$selected);					
			}
						
			$current_target_ids		 = $activity->get_target();
			$current_target_id_array = explode(",", $current_target_ids);
			$target_checks = array();
			foreach($targets as $t)
			{
				$checked = (in_array($t->get_id(), $current_target_id_array)) ? 'checked' : '';
				$target_checks[] = array('value'=>$t->get_id(), 'label'=>$t->get_name(), 'checked'=>$checked, 'name'=>'target[]');
			}		
			
			$current_district_ids		 = $activity->get_district();
			$current_district_id_array	 = explode(",", $current_district_ids);
			$district_checks = array();
			foreach($districts as $d)
			{
				$checked = (in_array($d['part_of_town_id'], $current_district_id_array)) ? 'checked' : '';
				$district_checks[] = array('value'=>$d['part_of_town_id'], 'label'=>$d['name'], 'checked'=>$checked, 'name'=>'district[]');				
			}
			
			$building_options[] = array('id'=>'0', 'name'=>lang('Ingen kommunale bygg valgt'), 'selected'=>0);
			$current_internal_arena_id = $activity->get_internal_arena();
			foreach($buildings as $building_id => $building_name)
			{
				$selected = ($current_internal_arena_id == $building_id) ? 1 : 0;
				$building_options[] = array('id'=>$building_id, 'name'=>$building_name, 'selected'=>$selected);					
			}
			
			$arena_external_options[] = array('id'=>'0', 'name'=>lang('Ingen arena valgt'), 'selected'=>0);
			$current_arena_id = $activity->get_arena();
			foreach($arenas as $arena)
			{
				$selected = ($current_arena_id == $arena->get_id()) ? 1 : 0;
				$arena_external_options[] = array('id'=>$arena->get_id(), 'name'=>$arena->get_arena_name(), 'selected'=>$selected);					
			}
			
			$office_options[] = array('id'=>'0', 'name'=>lang('Ingen kontor valgt'), 'selected'=>0);
			$selected_office = $activity->get_office();
			foreach($offices as $office)
			{
				$selected = ($selected_office == $office['id']) ? 1 : 0;
				$office_options[] = array('id'=>$office['id'], 'name'=>$office['name'], 'selected'=>$selected);					
			}
			
			$organization_options[] = array('id'=>'', 'name'=>lang('Ingen organisasjon valgt'), 'selected'=>0);
			$current_organization_id = $activity->get_organization_id();
			foreach($organizations as $organization)
			{
				$selected = ($current_organization_id == $organization->get_id()) ? 1 : 0;
				$organization_options[] = array('id'=>$organization->get_id(), 'name'=>$organization->get_name(), 'selected'=>$selected);					
			}
			
			/*return $this->render('activity.php', array
				(
				'activity'		 => $activity,
				'organizations'	 => $organizations,
				'org_name'		 => $org_name,
				'groups'		 => $groups,
				'local_group'	 => $local_group,
				'arenas'		 => $arenas,
				'buildings'		 => $buildings,
				'categories'	 => $categories,
				'targets'		 => $targets,
				'districts'		 => $districts,
				'offices'		 => $offices,
				'editable'		 => $editable,
				'cancel_link'	 => $cancel_link,
				'message'		 => isset($message) ? $message : phpgw::get_var('message'),
				'error'			 => isset($error) ? $error : phpgw::get_var('error')
			)
			);*/
			
			$tabs = array();
			$tabs['details']	= array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			$data = array
			(
				'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, $active_tab),		
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uicomposite.save')),
				'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uicomposite.save')),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				
				'activity_id'					=> $activity->get_id(),
				'value_title'					=> $activity->get_title(),
				'value_description'				=> $description,
				'list_state_options'			=> array('options' => $state_options),
				'list_category_options'			=> array('options' => $category_options),
				'list_target_checks'			=> array('choice' => $target_checks),
				'list_district_checks'			=> array('choice' => $district_checks),
				'special_adaptation_checked'	=> ($activity->get_special_adaptation() ? 1 : 0),
				'list_building_options'			=> array('options' => $building_options),
				'list_arena_external_options'	=> array('options' => $arena_external_options),
				'value_time'					=> $activity->get_time(),
				'list_office_options'			=> array('options' => $office_options),
				'list_organization_options'		=> array('options' => $organization_options),
				'organization_selected'			=> ($current_organization_id ? 1 : 0),
				'organization_url'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'booking.uiorganization.show', 'id'=>$current_organization_id)),
				
				/*'has_custom_address'			=> ($composite->has_custom_address()) ? 1 : 0,
				'value_custom_address_1'		=> $composite->get_custom_address_1(),
				'value_custom_house_number'		=> $composite->get_custom_house_number(),
				'value_custom_address_2'		=> $composite->get_custom_address_2(),
				'value_custom_postcode'			=> $composite->get_custom_postcode(),
				'value_custom_place'			=> $composite->get_custom_place(),
				'value_area_gros'				=> $composite->get_area_gros(). ' ' .$this->area_suffix,
				'value_area_net'				=> $composite->get_area_net(). ' ' .$this->area_suffix,
				'is_active'						=> ($composite->is_active()) ? 1 : 0,
				'value_description'				=> $composite->get_description(),

				'list_fields_of_responsibility_options'	=> array('options' => $fields_of_responsibility_options),
				
				'composite_id'					=> $composite_id,*/

				'validator'				=> phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'))
			);
			
			self::render_template_xsl(array('activity'), array('edit' => $data));
		}

		public function query()
		{
			$search			= phpgw::get_var('search');
			$order			= phpgw::get_var('order');
			$draw			= phpgw::get_var('draw', 'int');
			$columns		= phpgw::get_var('columns');

			$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects	= (phpgw::get_var('length', 'int') <= 0) ? $this->user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'id'; 
			$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for 	= $search['value'];
			$search_type	= phpgw::get_var('search_option');

			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			$export			= phpgw::get_var('export','bool');
			if ($export)
			{
				$num_of_objects = null;
			}
			
			//Retrieve the type of query and perform type specific logic
			$query_type		 = phpgw::get_var('type');

			$email_param = phpgw::get_var('email');
			$email		 = false;
			if(isset($email_param))
			{
				$email			 = true;
				$num_of_objects	 = null;
			}

			$uid = $GLOBALS['phpgw_info']['user']['account_id'];

			switch($query_type)
			{
				case 'new_activities':
					$filters		 = array('new_activities' => 'yes', 'activity_state' => phpgw::get_var('activity_state'),
						'activity_category' => phpgw::get_var('activity_category'), 'activity_district' => phpgw::get_var('activity_district'),
						'user_id' => $uid, 'updated_date_hidden' => phpgw::get_var('date_change'));
					$result_objects	 = activitycalendar_soactivity::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$result_count	 = activitycalendar_soactivity::get_instance()->get_count($search_for, $search_type, $filters);				
					break;
				case 'all_activities':
				default:
					$filters		 = array('activity_state' => phpgw::get_var('activity_state'), 'activity_category' => phpgw::get_var('activity_category'),
						'activity_district' => phpgw::get_var('activity_district'), 'user_id' => $uid,
						'updated_date_hidden' => phpgw::get_var('date_change'));
					$result_objects	 = activitycalendar_soactivity::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$result_count	 = activitycalendar_soactivity::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}

			//Create an empty row set
			$rows		 = array();
			$mail_rows	 = array();
			foreach($result_objects as $result)
			{
				if(isset($result))
				{
					// ... add a serialized result
					if($export)
					{
						$rows[] = $result->serialize_for_export();
					}
					else
					{
						$rows[] = $result->serialize();
					}
					$mail_rows[] = $result;
				}
			}

			if($export)
			{
				return $rows;
			}
			
			if(!$export && !$email)
			{
				//Add action column to each row in result table
				array_walk($rows, array($this, 'add_actions'), array($query_type));
			}
			if($email)
			{
				$this->send_email_to_selection($mail_rows);
			}
			else
			{
				$result_data    =   array('results' =>  $rows);
				$result_data['total_records']	= $result_count;
				$result_data['draw']    = $draw;
				
				return $this->jquery_results($result_data);
			}
		}

		/**
		 * Add action links and labels for the context menu of the list items
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [composite_id, type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
		{
			//Defining new columns
			$value['ajax']		 = array();
			$value['actions']	 = array();
			$value['labels']	 = array();

			$query_type = $params[0];

			switch($query_type)
			{
				case 'all_activities':
					$value['ajax'][]	 = false;
					$value['actions'][]	 = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.edit',
						'id' => $value['id'])));
					$value['labels'][]	 = lang('edit');
					$value['ajax'][]	 = false;
					$value['actions'][]	 = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.view',
						'id' => $value['id'])));
					$value['labels'][]	 = lang('show');
					$value['ajax'][]	 = true;
					$value['actions'][]	 = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.send_mail',
						'activity_id' => $value['id'], 'message_type' => 'update')));
					$value['labels'][]	 = lang('send_mail');
					break;

				case 'new_activities':
					$value['ajax'][]	 = false;
					$value['actions'][]	 = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.edit',
						'id' => $value['id'])));
					$value['labels'][]	 = lang('edit');
					$value['ajax'][]	 = false;
					$value['actions'][]	 = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.view',
						'id' => $value['id'])));
					$value['labels'][]	 = lang('show');
					$value['ajax'][]	 = true;
					$value['actions'][]	 = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.send_mail',
						'activity_id' => $value['id'], 'message_type' => 'update')));
					$value['labels'][]	 = lang('send_mail');
					break;
			}
		}

		function send_email_to_selection($activities)
		{
			$c			 = createobject('phpgwapi.config', 'activitycalendarfrontend');
			$c->read();
			$config		 = $c->config_data;
			$_subject	 = lang('mail_subject_update');

			$mailBaseURL = $c->config_data['mailBaseURL'];
			foreach($activities as $activity)
			{
				$subject	 = "{$_subject}::" . $activity->get_title();
				//$activity = activitycalendar_soactivity::get_instance()->get_single($activity_id);
				//$link_text = "<a href='http://www.bergen.kommune.no/aktivby/registreringsskjema/ny/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
				//$link_text = "<a href='{$mailBaseURL}?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
				$link_text	 = "<a href='http://www.bergen.kommune.no/aktivitetsoversikt/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
				$office_name = activitycalendar_soactivity::get_instance()->get_office_name($activity->get_office());
				/*
				  $uid = $GLOBALS['phpgw_info']['user']['account_id'];
				  $user_office_id =  activitycalendar_soactivity::get_instance()->get_office_from_user($uid);
				  $office_footer = activitycalendar_soactivity::get_instance()->get_office_description($user_office_id);
				 */
				$office_id	 = $activity->get_office();
				if($office_id == 1)
				{
					$office_id_new = 2;
				}
				else if($office_id == 2)
				{
					$office_id_new = 1;
				}
				else
				{
					$office_id_new = (int)$office_id;
				}
				$office_footer = activitycalendar_soactivity::get_instance()->get_office_description($office_id_new);
				if($activity->get_state() == 2)
				{
					$body = lang('mail_body_update_frontend', $activity->get_title(), $link_text, $office_footer, $office_name);
				}
				else
				{
					$body = lang('mail_body_update', $activity->get_title(), $link_text, $office_footer, $office_name);
				}

				//var_dump($subject);
				//var_dump($body);
				//var_dump($activity->get_organization_id() . " ; " . $activity->get_group_id());

				if($activity->get_group_id() && $activity->get_group_id() > 0)
				{
					$activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_group_id(), true));
					/*if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
					  {
					  activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_2(), $subject, $body);
					  }
					  else */
					if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
					{
						activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_1(), $subject, $body);
					}
				}
				else if($activity->get_organization_id() && $activity->get_organization_id() > 0)
				{
					$activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_organization_id()));
					/*if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
					  {
					  activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_2(), $subject, $body);
					  }
					  else */
					if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
					{
						activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_1(), $subject, $body);
					}
				}
			}

			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.index',
				'message' => 'E-post sendt'));
		}

		public function send_mail()
		{
			$c		 = createobject('phpgwapi.config', 'activitycalendarfrontend');
			$c->read();
			$config	 = $c->config_data;

			$mailBaseURL = $c->config_data['mailBaseURL'];
			$activity_id = (int)phpgw::get_var('activity_id');
			$activity	 = activitycalendar_soactivity::get_instance()->get_single($activity_id);

			$message_type = phpgw::get_var('message_type');
			if($message_type)
			{
				//$subject = lang('mail_subject_update', $avtivity->get_id() . '-' . $activity->get_title(), $activity->get_link());
				$subject		 = lang('mail_subject_update');
				//$link_text = "http://www.bergen.kommune.no/aktivby/registreringsskjema/ny/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}";
				//$link_text = "{$mailBaseURL}?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}";
				//$link_text = "http://www.bergen.kommune.no/aktivitetsoversikt/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}";
				$link_text		 = "<a href='http://www.bergen.kommune.no/aktivitetsoversikt/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
				$office_name	 = activitycalendar_soactivity::get_instance()->get_office_name($activity->get_office());
				$office_id		 = $activity->get_office();
				if($office_id == 1)
				{
					$office_id_new	 = 2;
				}
				else if($office_id == 2)
				{
					$office_id_new	 = 1;
				}
				else
				{
					$office_id_new	 = (int)$office_id;
				}
				$office_footer	 = activitycalendar_soactivity::get_instance()->get_office_description($office_id_new);
				$body			 = lang('mail_body_update', $activity->get_title(), $link_text, $office_footer, $office_name);
			}
			else
			{
				$subject = "dette er en test";
				$body	 = "testmelding fra Aktivitetsoversikt";
			}

//    	var_dump($subject);
//    	var_dump($body);
//    	var_dump($activity->get_organization_id() . " ; " . $activity->get_group_id());

			if($activity->get_group_id() && $activity->get_group_id() > 0)
			{
				//$contact_person2 = activitycalendar_socontactperson::get_instance()->get_group_contact2($activity>get_group_id());
				$activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_group_id(), true));
				/*if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
				  activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_2(), $subject, $body);
				  else */
				if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
				{
					activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_1(), $subject, $body);
				}
			}
			else if($activity->get_organization_id() && $activity->get_organization_id() > 0)
			{
				//$contact_person2 = activitycalendar_socontactperson::get_instance()->get_oup_contact2($activity>get_group_id());
				$activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_organization_id()));
				/*if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
				  activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_2(), $subject, $body);
				  else */
				if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
				{
					activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_1(), $subject, $body);
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$message['message'][] = array('msg'=>lang('E-post sendt'));
				return $message;
			}
		
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.index',
				'message' => 'E-post sendt'));
		}

		function send_mailnotification_to_organization($contact_person, $subject, $body)
		{

			//var_dump($contact_person_id . ',' . $subject . ',' . $body);
			if(!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$config	 = $this->config_booking;
			$from	 = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			//$from = "erik.holm-larsen@bouvet.no";

			if(strlen(trim($body)) == 0)
			{
				return false;
			}

			$mailtoAddress = trim(activitycalendar_socontactperson::get_instance()->get_mailaddress_for_org_contact($contact_person->get_id()));
			//$mailtoAddress = "erik.holm-larsen@bouvet.no";
			//var_dump($mailtoAddress);
			//var_dump($mailtoAddress.';'.$from.';'.$subject);
			if(strlen($mailtoAddress) > 0)
			{
				if(!$this->validator->check_email_address($mailtoAddress))
				{
					$GLOBALS['phpgw']->log->error(array(
						'text'	 => 'uiactivities::send_mailnotification_to_group() : not a valid address.: %1',
						'p1'	 => $mailtoAddress,
						'line'	 => __LINE__,
						'file'	 => __FILE__
					));
					$msg = "Overskrift: \"{$subject}\"; Adressen feiler på validering:\"{$mailtoAddress}\"";
					_debug_array($msg);
					return false;
				}

				_debug_array($mailtoAddress);
				_debug_array($subject);

				if($this->debug)
				{
					return false;
				}

				try
				{
					$GLOBALS['phpgw']->send->msg('email', $mailtoAddress, $subject, $body, '', '', '', $from, '', 'html');
				}
				catch(phpmailerException $e)
				{
					if($e)
					{
						$GLOBALS['phpgw']->log->error(array(
							'text'	 => 'uiactivities::send_mailnotification_to_organization() : error when trying to execute %1. Error: %2',
							'p1'	 => 'send()',
							'p2'	 => $e->getMessage(),
							'line'	 => __LINE__,
							'file'	 => __FILE__
						));
					}
				}
			}
		}

		function send_mailnotification_to_group($contact_person, $subject, $body)
		{
			if(!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$config	 = $this->config_booking;
			$from	 = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			//$from = "tester@bouvet.no";

			if(strlen(trim($body)) == 0)
			{
				return false;
			}

			$mailtoAddress = trim(activitycalendar_socontactperson::get_instance()->get_mailaddress_for_group_contact($contact_person->get_id()));
			//$mailtoaddress = "erik.holm-larsen@bouvet.no";
			//var_dump($mailtoAddress.';'.$from.';'.$subject);
			if(strlen($mailtoAddress) > 0)
			{
				if(!$this->validator->check_email_address($mailtoAddress))
				{
					$GLOBALS['phpgw']->log->error(array(
						'text'	 => 'uiactivities::send_mailnotification_to_group() : not a valid address.: %1',
						'p1'	 => $mailtoAddress,
						'line'	 => __LINE__,
						'file'	 => __FILE__
					));
					$msg = "Overskrift: \"{$subject}\"; Adressen feiler på validering:\"{$mailtoAddress}\"";
					_debug_array($msg);

					return false;
				}

				_debug_array($mailtoAddress);
				_debug_array($subject);

				if($this->debug)
				{
					return false;
				}

				try
				{
					$GLOBALS['phpgw']->send->msg('email', $mailtoAddress, $subject, $body, '', '', '', $from, '', 'html');
				}
				catch(phpmailerException $e)
				{
					if($e)
					{
						$GLOBALS['phpgw']->log->error(array(
							'text'	 => 'uiactivities::send_mailnotification_to_group() : error when trying to execute %1. Error: %2',
							'p1'	 => 'send()',
							'p2'	 => $e->getMessage(),
							'line'	 => __LINE__,
							'file'	 => __FILE__
						));
					}
				}
			}
		}

		public function get_organization_groups()
		{
			$GLOBALS['phpgw_info']['flags']['noheader']	 = true;
			$GLOBALS['phpgw_info']['flags']['nofooter']	 = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = false;

			$org_id		 = phpgw::get_var('orgid');
			$group_id	 = phpgw::get_var('groupid');
			$returnHTML	 = "<option value='0'>Ingen gruppe valgt</option>";
			if($org_id)
			{
				$groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, array(
					'org_id' => $org_id));
				foreach($groups as $group)
				{
					if(isset($group))
					{
						//$res_g = $group->serialize();
						$selected = "";
						if($group_id && $group_id > 0)
						{
							$gr_id = (int)$group_id;
							if($gr_id == (int)$group->get_id())
							{
								$selected_group = " selected='selected'";
							}
							else
							{
								$selected_group = "";
							}
						}
						$group_html[] = "<option value='" . $group->get_id() . "'" . $selected_group . ">" . $group->get_name() . "</option>";
					}
				}
				$html		 = implode(' ', $group_html);
				$returnHTML	 = $returnHTML . ' ' . $html;
			}


			return $returnHTML;
		}

		public function create_groups()
		{
			$activities = $this->so_activity->get_activities_without_groups();

			foreach($activities as $a)
			{
				$group_info	 = array();
				$title_new	 = $a['title'];
				if(strlen($title_new) > 50)
				{
					$title_new = substr($title_new, 0, 49);
				}
				$group_info['name']				 = $title_new;
				$group_info['organization_id']	 = $a['organization'];
				$group_info['description']		 = $a['description'];

				//add new group
				$new_group_id	 = $this->so_group->add_new_group_from_activity($group_info);
				var_dump("lagt til gruppen " . $group_info['name'] . " med id " . $new_group_id . "<br/>");
				$this->so_activity->update_activity_group($a['id'], $new_group_id);
				$cp				 = $this->so_contact->get_booking_contact_persons($a['organization']);
				foreach($cp as $c)
				{
					$c->set_group_id($new_group_id);
					$contact_id = $this->so_contact->add_new_group_contact($c);
					var_dump("Lagt til kontaktperson " . $c->get_name() . " på gruppe " . $group_info['name'] . "<br/>");
					//_debug_array($c);
				}
			}
		}

		public function remove_old_activities()
		{
			$activity_id = phpgw::get_var('act_id');
			if($activity_id && $activity_id > 0)
			{
				$activity_del = $this->so_activity->get_single($activity_id);
				$this->so_activity->remove_old_activities($activity_id);

				echo "<h2>Aktiviteten '" . $activity_del->get_title() . "' ble slettet fra systemet.</h2>";
				echo "<a href='#' onclick='history.go(-1);'>Tilbake til forrige side</a>";
			}
			else
			{
				echo "<h2>Du må fylle ut en aktivitets-id!</h2>";
				echo "<a href='#' onclick='history.go(-1);'>Tilbake til forrige side</a>";
			}
		}
	}