<?php
	phpgw::import_class('activitycalendar.uiactivities');
	phpgw::import_class('activitycalendar.soactivity');
	phpgw::import_class('activitycalendar.sogroup');
	phpgw::import_class('activitycalendar.soarena');
	phpgw::import_class('activitycalendar.soorganization');
	phpgw::import_class('activitycalendar.socontactperson');

	include_class('activitycalendar', 'activity', 'inc/model/');
	include_class('activitycalendar', 'group', 'inc/model/');
	include_class('activitycalendar', 'organization', 'inc/model/');
	include_class('activitycalendar', 'arena', 'inc/model/');

	class activitycalendarfrontend_uiactivity extends activitycalendar_uiactivities
	{

		private $so_organization;
//    private $so_activity;
		public $public_functions = array
			(
			'add' => true,
			'edit' => true,
			'view' => true,
			'index' => true,
			'get_organization_groups' => true,
			'get_address_search' => true,
			'edit_organization_values' => true,
			'get_organization_activities' => true,
			'test_sql_injection' => true
		);

		public function __construct()
		{
			parent::__construct();
			$this->so_organization = activitycalendar_soorganization::get_instance();
//        $this->so_activity = activitycalendar_soactivity::get_instance();
		}

		public function test_sql_injection()
		{
			$c = createobject('phpgwapi.config', 'activitycalendarfrontend');
			$c->read();
			$config = $c->config_data;

			$allow_test = $c->config_data['allow_test'];
			if($allow_test != 1)
			{
				echo "<H1>Test not activated in config</H>";
				exit;
			}
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$district_id = phpgw::get_var('district_id');

			$district = $this->so_activity->get_district( $district_id );
			print_r($district);
		}
	
		/**
		 * Public method. Add new activity.
		 */
		public function add()
		{
			//$GLOBALS['phpgw']->redirect_link('/activitycalendarfrontend/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.edit', 'action' => 'new_activity'));
			$GLOBALS['phpgw']->js->validate_file('json', 'json', 'phpgwapi');
			$c = createobject('phpgwapi.config', 'activitycalendarfrontend');
			$c->read();
			$config = $c->config_data;

			$ajaxUrl = $c->config_data['AJAXURL'];
			$helpImg = $GLOBALS['phpgw']->common->image('activitycalendarfrontend', 'hjelp.gif');

			$categories = $this->so_activity->get_categories();
			$targets = $this->so_activity->get_targets();
			$offices = $this->so_activity->select_district_list();
			$districts = $this->so_activity->get_districts();
			$buildings = $this->so_arena->get_buildings();
			$arenas = $this->so_arena->get(0, 0, 'arena.arena_name', true, '', '', array());
			$organizations = $this->so_organization->get(0, 0, 'org.name', true, '', '', array());

			$activity = new activitycalendar_activity();

			$o_id = phpgw::get_var('organization_id');
			$o_id_new = phpgw::get_var('organization_id_hidden');

			$organization_options = Array();
			foreach ($organizations as $o)
			{
				$organization_options[] = array(
					'id' => $o->get_id(),
					'name' => $o->get_name()
				);
			}

			$category_options = Array();
			foreach ($categories as $c)
			{
				$category_options['list'][] = array(
					'id' => $c->get_id(),
					'name' => $c->get_name()
				);
			}

			$arena_options = Array();
			foreach ($arenas as $a)
			{
				$arena_options[] = array(
					'id' => $a->get_id(),
					'name' => $a->get_arena_name()
				);
			}

			$building_options = Array();
			foreach ($buildings as $building_id => $building_name)
			{
				$building_options[] = array(
					'id' => $building_id,
					'name' => $building_name
				);
			}

			$office_options = Array();
			foreach ($offices as $o)
			{
				$office_options['list'][] = array(
					'id' => $o['id'],
					'name' => $o['name']
				);
			}

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'));

			if (isset($_POST['step_1']))
			{ //activity shall be registred on a new organization
				self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_new.js');

				if ($o_id_new == "new_org")
				{
					//add new organization to internal activitycalendar organization register
					$org_homepage = phpgw::get_var('homepage');
					if ($org_homepage == 'http://')
					{
						$org_homepage = "";
					}
					$org_info['name'] = phpgw::get_var('orgname');
					$org_info['orgnr'] = phpgw::get_var('orgno');
					$org_info['homepage'] = $org_homepage;
					$org_info['street'] = phpgw::get_var('address');
					$org_info['streetnumber'] = phpgw::get_var('number');
					$org_info['zip'] = phpgw::get_var('postzip');
					$org_info['postaddress'] = phpgw::get_var('postaddress');
					$org_info['status'] = "new";
					$o_id = $this->so_activity->add_organization_local($org_info);

					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('org_contact1_name');
					$contact1['phone'] = phpgw::get_var('org_contact1_phone');
					$contact1['mail'] = phpgw::get_var('org_contact1_mail');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = 0;
					$this->so_activity->add_contact_person_local($contact1);

					$person_arr = $this->so_contact->get_local_contact_persons($o_id);
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}

					$person_ids = $this->so_organization->get_contacts_local($o_id);
					$desc = phpgw::get_var('org_description');
					$organization = $this->so_organization->get_organization_local($o_id);
					$new_org = true;

					$organization = $this->so_organization->get_organization_local($o_id);
					$person_arr = $this->so_organization->get_contacts_local_as_objects($o_id);
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}

					$message = lang('organization_saved_form');
				}
				else
				{
					$new_org = false;
					$organization = $this->so_organization->get_single($o_id);
					$person_arr = $this->so_contact->get(0, 0, '', false, '', '', array(
						'organization_id' => $o_id));
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}

					$activity->set_organization_id($o_id);
					$activity->set_description($organization->get_description());
					$activity->set_contact_persons($pers);
				}

				$activity_options = Array();
				$activity_options['id'] = ($activity->get_id()) ? $activity->get_id() : 0;
				$activity_options['time'] = $activity->get_time();
				$activity_options['title'] = $activity->get_title();
				$category_options['current_category_id'] = ($activity->get_category()) ? $activity->get_category() : "";
				$office_options['selected_office'] = ($activity->get_office()) ? $activity->get_office() : "";

				$organization_id = $organization->get_id();

				$current_target_ids = $activity->get_target();
				$current_target_id_array = explode(",", $current_target_ids);
				$target_options = Array();
				foreach ($targets as $t)
				{
					$checked = (in_array($t->get_id(), $current_target_id_array)) ? "checked" : "";
					$target_options[] = array(
						'id' => $t->get_id(),
						'name' => $t->get_name(),
						'checked' => $checked
					);
				}

				return self::render_template_xsl('activity_new', array
						(
						'activity' => $activity_options,
						'new_organization' => $new_org,
						'organization_id' => $organization_id,
						'contact1' => $persons[0],
						'arenas' => $arena_options,
						'buildings' => $building_options,
						'categories' => $category_options,
						'targets' => $target_options,
						'districts' => $districts,
						'offices' => $office_options,
						'editable' => true,
						'message' => isset($message) ? $message : phpgw::get_var('message'),
						'error' => isset($error) ? $error : phpgw::get_var('error'),
						'helpImg' => $helpImg,
						'ajaxURL' => $ajaxUrl
						)
				);
			}
			else if (isset($_POST['save_activity']))
			{
				$get_org_from_local = false;
				$new_org_group = false;
				$new_org = phpgw::get_var('new_organization');
				if ($new_org != null && $new_org == 'yes')
				{
					$get_org_from_local = true;
				}

				if ($get_org_from_local)
				{
					$activity->set_new_org(true);
					//$person_arr = $this->so_contact->get_local_contact_persons($o_id);
					//foreach($person_arr as $p)
					//{
					//$persons[] = $p;
					//}
					//$person_ids = $this->so_organization->get_contacts_local($o_id);
					//$desc = $this->so_organization->get_description_local($o_id);
					$organization = $this->so_organization->get_organization_local($o_id);
					$new_org = true;
					//$new_org_group = true;
					//Add new group for the activity
					$group_info['name'] = phpgw::get_var('title');
					$group_info['organization_id'] = $o_id;
					$group_info['description'] = phpgw::get_var('description');
					$group_info['status'] = "new";
					$g_id = $this->so_activity->add_group_local($group_info);

					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('contact_name');
					$contact1['phone'] = phpgw::get_var('contact_phone');
					$contact1['mail'] = phpgw::get_var('contact_mail');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = $g_id;
					$this->so_activity->add_contact_person_local($contact1);

					$person_arr = $this->so_contact->get_local_contact_persons($g_id, true);
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}
					$desc = phpgw::get_var('description');
					$group = $this->so_group->get_group_local($g_id);
					$person_ids = $this->so_group->get_contacts_local($g_id);
					$new_group = true;
				}
				else if (is_numeric($o_id) && $o_id > 0)
				{
					$group_info['name'] = phpgw::get_var('title');
					$group_info['organization_id'] = $o_id;
					$group_info['description'] = phpgw::get_var('description');
					$group_info['status'] = "new";
					$g_id = $this->so_activity->add_group_local($group_info);

					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('contact_name');
					$contact1['phone'] = phpgw::get_var('contact_phone');
					$contact1['mail'] = phpgw::get_var('contact_mail');
					$contact1['org_id'] = 0;
					$contact1['group_id'] = $g_id;
					$this->so_activity->add_contact_person_local($contact1);

					$person_arr = $this->so_contact->get_local_contact_persons($g_id, true);
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}
					$desc = phpgw::get_var('description');
					$group = $this->so_group->get_group_local($g_id);
					$person_ids = $this->so_group->get_contacts_local($g_id);
					$organization = $this->so_organization->get_single($o_id);
					$new_group = true;
				}

				if (strlen($desc) > 254)
				{
					$desc = substr($desc, 0, 254);
				}

				$arena_id = phpgw::get_var('internal_arena_id');
				$new_arena = phpgw::get_var('new_arena_hidden');
				if ($new_arena != null && $new_arena == 'new_arena')
				{
					$arena = new activitycalendar_arena();
					$arena_name = phpgw::get_var('arena_name');
					$arena_address = phpgw::get_var('arena_address');
					$arena_addressnumber = phpgw::get_var('arena_number');
					$arena_zip_code = phpgw::get_var('arena_zip_code');
					$arena_city = phpgw::get_var('arena_city');

					$arena->set_arena_name($arena_name);
					$arena->set_address($arena_address);
					$arena->set_addressnumber($arena_addressnumber);
					$arena->set_zip_code($arena_zip_code);
					$arena->set_city($arena_city);
					$arena->set_active(true);

					// All is good, store arena
					if ($this->so_arena->store($arena))
					{
						$arena_id = $arena->get_id();
						$activity->set_arena($arena_id);
					}
				}
				else
				{
					$arena_arr = explode("_", $arena_id);
					if ($arena_arr[0] == 'i')
					{
						$activity->set_internal_arena($arena_arr[1]);
					}
					else
					{
						$activity->set_arena($arena_arr[1]);
					}
				}

				//... set all parameters
				$activity->set_title(phpgw::get_var('title'));
				$activity->set_organization_id($o_id);
				$activity->set_group_id($g_id);
				$activity->set_district(phpgw::get_var('district'));
				$activity->set_office(phpgw::get_var('office'));
				$activity->set_state(1);
				$activity->set_category(phpgw::get_var('category'));
				$target_array = phpgw::get_var('target');
				$activity->set_target(implode(",", $target_array));
				$activity->set_description($desc);
				$activity->set_time(phpgw::get_var('time'));
				$activity->set_contact_persons($persons);
				$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
				$activity->set_contact_person_2_address(phpgw::get_var('contact2_address') . ", " . phpgw::get_var('contact2_number'));
				$activity->set_contact_person_2_zip(phpgw::get_var('contact2_postaddress'));
				$activity->set_frontend(true);
				$activity->set_new_org($new_org);
				$activity->set_new_group($new_group);
				$target_ok = false;
				$district_ok = false;

				if ($get_org_from_local)
				{
					//update new organization with district-id from activity.
					$this->so_organization->update_org_district_local($organization->get_id(), $activity->get_district());
				}

				if ($activity->get_target() && $activity->get_target() != '')
				{
					$target_ok = true;
				}
				if ($activity->get_district() && $activity->get_district() != '')
				{
					$district_ok = true;
				}

				if ($target_ok && $district_ok)
				{
					if ($this->so_activity->store($activity))
					{ // ... and then try to store the object
						$message = lang('messages_saved_form');
					}
					else
					{
						$error = lang('messages_form_error');
					}
					//$org_info_edit_url = self::link('/index.php' ,array('menuaction' => 'activitycalendarfrontend.uiactivity.edit_organization_values'));

					$GLOBALS['phpgw_info']['flags']['noframework'] = true;

					$activity_options = Array();
					$activity_options['id'] = ($activity->get_id()) ? $activity->get_id() : "0";
					$activity_options['title'] = $activity->get_title();
					$activity_options['description'] = $activity->get_description();
					$activity_options['category'] = ($activity->get_category()) ? $this->so_activity->get_category_name($activity->get_category()) : "";
					$activity_options['targets'] = "";
					$activity_options['special_adaptation'] = ($activity->get_special_adaptation()) ? true : false;
					$activity_options['internal_arena'] = ($activity->get_internal_arena()) ? true : false;
					$activity_options['building_name'] = $this->so_arena->get_building_name($activity->get_internal_arena());
					$activity_options['arena'] = ($activity->get_arena()) ? true : false;
					$activity_options['arena_name'] = $this->so_arena->get_arena_name($activity->get_arena());
					$activity_options['districts'] = "";
					$activity_options['time'] = $activity->get_time();
					$activity_options['contact_person_1'] = ($activity->get_contact_person_1()) ? true : false;
					$activity_options['contact1_name'] = (isset($persons[0])) ? $persons[0]->get_name() : "";
					$activity_options['contact1_phone'] = (isset($persons[0])) ? $persons[0]->get_phone() : "";
					$activity_options['contact1_mail'] = (isset($persons[0])) ? $persons[0]->get_email() : "";
					$activity_options['office'] = ($activity->get_office()) ? $this->so_activity->get_office_name($activity->get_office()) : "";

					if ($activity->get_target())
					{
						$current_target_ids = $activity->get_target();
						$current_target_id_array = explode(",", $current_target_ids);
						foreach ($current_target_id_array as $ct)
						{
							$activity_options['targets'] .= $this->so_activity->get_target_name($ct) . "<br />";
						}
					}

					if ($activity->get_district())
					{
						$current_district_ids = $activity->get_district();
						$current_district_id_array = explode(",", $current_district_ids);
						foreach ($current_district_id_array as $cd)
						{
							$activity_options['districts'] .= $this->so_activity->get_district_name($cd) . "<br />";
						}
					}

					$organization_options = Array();
					$organization_options['id'] = $organization->get_id();
					$organization_options['name'] = $organization->get_name();
					$organization_options['new_org'] = $activity->get_new_org();
					$organization_options['edit_link'] = self::link(array('menuaction' => 'activitycalendarfrontend.uiactivity.edit_organization_values',
							'organization_id' => $organization_options['id']));


					return self::render_template_xsl('activity', array
							(
							'activity' => $activity_options,
							'organization' => $organization_options,
							'group' => $group,
							'contact1' => $persons[0],
							'arenas' => $arenas,
							'buildings' => $buildings,
							'categories' => $categories,
							'targets' => $targets,
							'districts' => $districts,
							'offices' => $offices,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error'),
							'ajaxURL' => $ajaxUrl
							)
					);
				}
				else
				{
					if (!$target_ok)
					{
						$error .= "<br/>" . lang('target_not_selected');
					}
					if (!$district_ok)
					{
						$error .= "<br/>" . lang('district_not_selected');
					}

					$activity_options = Array();
					$activity_options['id'] = ($activity->get_id()) ? $activity->get_id() : 0;
					$activity_options['time'] = $activity->get_time();
					$activity_options['title'] = $activity->get_title();
					$category_options['current_category_id'] = ($activity->get_category()) ? $activity->get_category() : "";
					$office_options['selected_office'] = ($activity->get_office()) ? $activity->get_office() : "";

					$organization_id = $organization->get_id();

					$current_target_ids = $activity->get_target();
					$current_target_id_array = explode(",", $current_target_ids);
					$target_options = Array();
					foreach ($targets as $t)
					{
						$checked = (in_array($t->get_id(), $current_target_id_array)) ? "checked" : "";
						$target_options[] = array(
							'id' => $t->get_id(),
							'name' => $t->get_name(),
							'checked' => $checked
						);
					}

					return self::render_template_xsl('activity_new', array(
							'activity' => $activity_options,
							'organization_id' => $organization_id,
							'contact1' => $persons[0],
							'contact2' => $persons[1],
							'new_org' => $new_org,
							'arenas' => $arena_options,
							'buildings' => $building_options,
							'categories' => $category_options,
							'targets' => $target_options,
							'districts' => $districts,
							'offices' => $office_options,
							'editable' => true,
							'cancel_link' => $cancel_link,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error'),
							'helpImg' => $helpImg,
							'ajaxURL' => $ajaxUrl
							)
					);
				}
			}
			else
			{
				self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_new_step_1.js');

				self::render_template_xsl(
					'activity_new_step_1', array(
					'ajaxURL' => $ajaxUrl,
					'helpImg' => $helpImg,
					'organizations' => $organization_options
					)
				);
			}
		}

		function view()
		{
			$errorMsgs = array();
			$infoMsgs = array();
			$activity = $this->so_activity->get_single((int)phpgw::get_var('id'));

			if ($activity == null)
			{ // Not found
				$errorMsgs[] = lang('Could not find specified activity.');
			}

			$activity_options = Array();
			$activity_options['id'] = ($activity->get_id()) ? $activity->get_id() : "0";
			$activity_options['title'] = $activity->get_title();
			$activity_options['description'] = $activity->get_description();
			$activity_options['category'] = ($activity->get_category()) ? $this->so_activity->get_category_name($activity->get_category()) : "";
			$activity_options['targets'] = "";
			$activity_options['special_adaptation'] = ($activity->get_special_adaptation()) ? true : false;
			$activity_options['internal_arena'] = ($activity->get_internal_arena()) ? true : false;
			$activity_options['building_name'] = $this->so_arena->get_building_name($activity->get_internal_arena());
			$activity_options['arena'] = ($activity->get_arena()) ? true : false;
			$activity_options['arena_name'] = $this->so_arena->get_arena_name($activity->get_arena());
			$activity_options['districts'] = "";
			$activity_options['time'] = $activity->get_time();
			$activity_options['contact_person_1'] = ($activity->get_contact_person_1()) ? true : false;
			$activity_options['contact1_name'] = (isset($persons[0])) ? $persons[0]->get_name() : "";
			$activity_options['contact1_phone'] = (isset($persons[0])) ? $persons[0]->get_phone() : "";
			$activity_options['contact1_mail'] = (isset($persons[0])) ? $persons[0]->get_email() : "";
			$activity_options['office'] = ($activity->get_office()) ? $this->so_activity->get_office_name($activity->get_office()) : "";

			$activity_options['organization_id'] = $activity->get_organization_id();

			$organization = $this->so_organization->get_single($activity_options['organization_id']);
			$organization_options = Array();
			$organization_options['id'] = $organization->get_id();
			$organization_options['name'] = $organization->get_name();

			$activity_options['contact_person_1_id'] = $activity->get_contact_person_1();
			$activity_options['group_id'] = $activity->get_group_id();

			$persons = $this->so_contact->get_local_contact_persons($activity_options['group_id'], true);
			$person = $persons[0];
			$activity_options['contact1_name'] = $person->get_name();
			$activity_options['contact1_phone'] = $person->get_phone();
			$activity_options['contact1_mail'] = $person->get_email();

			if ($activity->get_target())
			{
				$current_target_ids = $activity->get_target();
				$current_target_id_array = explode(",", $current_target_ids);
				foreach ($current_target_id_array as $ct)
				{
					$activity_options['targets'] .= $this->so_activity->get_target_name($ct) . "<br />";
				}
			}

			if ($activity->get_district())
			{
				$current_district_ids = $activity->get_district();
				$current_district_id_array = explode(",", $current_district_ids);
				foreach ($current_district_id_array as $cd)
				{
					$activity_options['districts'] .= $this->so_activity->get_district_name($cd) . "<br />";
				}
			}

			$data = array(
				'activity' => $activity_options,
				'organization' => $organization_options,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
//		$this->render('activity.php', $data);
			self::render_template_xsl('activity', $data);
			//self::render_template('activity_tmp', array('activity' => $activity, 'frontend'=>'true'));
		}

		function edit()
		{
			$GLOBALS['phpgw']->js->validate_file('json', 'json', 'phpgwapi');

			$c = createobject('phpgwapi.config', 'activitycalendarfrontend');
			$c->read();
			$config = $c->config_data;

			$ajaxUrl = $c->config_data['AJAXURL'];
			$helpImg = $GLOBALS['phpgw']->common->image('activitycalendarfrontend', 'hjelp.gif');

			$id = intval(phpgw::get_var('id', 'GET'));

			$categories = $this->so_activity->get_categories();
			$targets = $this->so_activity->get_targets();
			$offices = $this->so_activity->select_district_list();
			$districts = $this->so_activity->get_districts();
			$buildings = $this->so_arena->get_buildings();
			$arenas = $this->so_arena->get(0, 0, 'arena.arena_name', true, '', '', array());

			$category_options = array();
			foreach ($categories as $c)
			{
				$category_options['list'][] = array(
					'id' => $c->get_id(),
					'name' => $c->get_name()
				);
			}

			$building_options = array();
			foreach ($buildings as $building_id => $building_name)
			{
				$building_options['list'][] = array(
					'id' => $building_id,
					'name' => $building_name
				);
			}

			$arena_options = array();
			foreach ($arenas as $a)
			{
				$arena_options['list'][] = array(
					'id' => $a->get_id(),
					'name' => $a->get_arena_name()
				);
			}

			$district_options = array();
			foreach ($districts as $d)
			{
				$district_options['list'][] = array(
					'part_of_town_id' => $d['part_of_town_id'],
					'name' => $d['name']
				);
			}

			$office_options = array();
			foreach ($offices as $o)
			{
				$office_options['list'][] = array(
					'id' => $o['id'],
					'name' => $o['name']
				);
			}

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'));

			if (isset($_POST['step_1']))
			{ //change_request
				$activity_id = phpgw::get_var('activity_id');
				$activity = $this->so_activity->get_single($activity_id);
				$org = $this->so_organization->get_single($activity->get_organization_id());


				//store update-request
				//$activity->set_state(2);
				//if($this->so_activity->store($activity))
				//{
				$this->send_email_to_selection(array($activity));
				$message = lang('update_request_sent', $activity->get_title(), $org->get_name());
				return self::render_template_xsl('activity_edit_step_1', array
						(
						'activities' => $activities,
						'message' => $message,
						'ajaxURL' => $ajaxUrl
						)
				);
				//}
			}
			else
			{
				$secret_param = phpgw::get_var('secret', 'GET');
				if (!isset($id) || $id == '')
				{
					//select activity to edit
					$activities = $this->so_activity->get(0, 0, 'title', true, '', '', array(
						'activity_state' => 3));
					$organizations = $this->so_organization->get(0, 0, 'org.name', true, '', '', array(
						'edit_from_frontend' => 'yes'));
					$organization_options = Array();
					foreach ($organizations as $o)
					{
						$organization_options[] = array(
							'id' => $o->get_id(),
							'name' => $o->get_name()
						);
					}

					self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_edit_step_1.js');

					return self::render_template_xsl('activity_edit_step_1', array(
							'activities' => $activities,
							'organizations' => $organization_options,
							'ajaxURL' => $ajaxUrl
							)
					);
				}
				if (!isset($secret_param) || $secret_param == '')
				{
					//select activity to edit
					$activities = $this->so_activity->get(0, 0, 'title', true, '', '', array(
						'activity_state' => 3));

					self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_edit_step_1.js');

					return self::render_template_xsl('activity_edit_step_1', array
							(
							'activities' => $activities,
							'ajaxURL' => $ajaxUrl
							)
					);
				}
				else
				{
					// Retrieve the activity object or create a new one
					if (isset($id) && $id > 0)
					{
						$activity = $this->so_activity->get_single($id);
					}
					else
					{
						$activities = $this->so_activity->get(0, 0, 'title', true, '', '', array(
							'activity_state' => 3));

						self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_edit_step_1.js');

						return self::render_template_xsl('activity_edit_step_1', array
								(
								'activities' => $activities,
								'ajaxURL' => $ajaxUrl
								)
						);
					}

					if ($activity->get_secret() != phpgw::get_var('secret', 'GET'))
					{
						//select activity to edit
						$activities = $this->so_activity->get(0, 0, 'title', true, '', '', array(
							'activity_state' => 3));

						self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_edit_step_1.js');

						return self::render_template_xsl('activity_edit_step_1', array
								(
								'activities' => $activities,
								'ajaxURL' => $ajaxUrl
								)
						);
					}

					if ($activity->get_group_id())
					{
						$person_arr = $this->so_contact->get_booking_contact_persons($activity->get_group_id(), true);
						foreach ($person_arr as $p)
						{
							$persons_array[] = $p;
						}
						$desc = $this->so_group->get_description($activity->get_group_id());
						$group = $this->so_group->get_single($activity->get_group_id());
						$person_ids = $this->so_group->get_contacts($activity->get_group_id());
					}
					else if ($activity->get_organization_id())
					{
						$person_arr = $this->so_contact->get_booking_contact_persons($activity->get_organization_id());
						foreach ($person_arr as $p)
						{
							$persons_array[] = $p;
						}
						$desc = $this->so_organization->get_description($activity->get_organization_id());
						$person_ids = $this->so_organization->get_contacts($activity->get_organization_id());
					}
					if (strlen($desc) > 254)
					{
						$desc = substr($desc, 0, 254);
					}

					$activity_options = array();
					$activity_options['id'] = ($activity->get_id()) ? $activity->get_id() : "0";
					$activity_options['title'] = $activity->get_title();
					$activity_options['description'] = $activity->get_description();
					$activity_options['category'] = ($activity->get_category()) ? $this->so_activity->get_category_name($activity->get_category()) : "";
					$activity_options['targets'] = "";
					$activity_options['special_adaptation'] = ($activity->get_special_adaptation()) ? true : false;
					$activity_options['internal_arena'] = ($activity->get_internal_arena()) ? true : false;
					$activity_options['building_name'] = $this->so_arena->get_building_name($activity->get_internal_arena());
					$activity_options['arena'] = ($activity->get_arena()) ? true : false;
					$activity_options['arena_name'] = $this->so_arena->get_arena_name($activity->get_arena());
					$activity_options['districts'] = "";
					$activity_options['time'] = $activity->get_time();
					$activity_options['contact_person_1'] = ($activity->get_contact_person_1()) ? true : false;
					$activity_options['contact1_name'] = (isset($persons_array[0])) ? $persons_array[0]->get_name() : "";
					$activity_options['contact1_phone'] = (isset($persons_array[0])) ? $persons_array[0]->get_phone() : "";
					$activity_options['contact1_mail'] = (isset($persons_array[0])) ? $persons_array[0]->get_email() : "";
					$activity_options['office'] = ($activity->get_office()) ? $this->so_activity->get_office_name($activity->get_office()) : "";
					$activity_options['group_id'] = $activity->get_group_id();

					$category_options['current_category_id'] = ($activity->get_category()) ? $activity->get_category() : "";
					$district_options['current_district_id'] = ($activity->get_district()) ? $activity->get_district() : "";
					$office_options['selected_office'] = ($activity->get_office()) ? $activity->get_office() : "";
					$building_options['selected_internal_arena'] = ($activity->get_internal_arena()) ? $activity->get_internal_arena() : "";
					$arena_options['selected_arena'] = ($activity->get_arena()) ? $activity->get_arena() : "";

					$current_target_ids = $activity->get_target();
					$current_target_id_array = explode(",", $current_target_ids);
					$target_options = array();
					foreach ($targets as $t)
					{
						$checked = (in_array($t->get_id(), $current_target_id_array)) ? "checked" : "";
						$target_options[] = array(
							'id' => $t->get_id(),
							'name' => $t->get_name(),
							'checked' => $checked
						);
					}

					if ($activity->get_target())
					{
						$current_target_ids = $activity->get_target();
						$current_target_id_array = explode(",", $current_target_ids);
						foreach ($current_target_id_array as $ct)
						{
							$activity_options['targets'] .= $this->so_activity->get_target_name($ct) . "<br />";
						}
					}

					if ($activity->get_district())
					{
						$current_district_ids = $activity->get_district();
						$current_district_id_array = explode(",", $current_district_ids);
						foreach ($current_district_id_array as $cd)
						{
							$activity_options['districts'] .= $this->so_activity->get_district_name($cd) . "<br />";
						}
					}

					$organization = $this->so_organization->get_single($activity->get_organization_id());
					$organization_options = array();
					$organization_options['id'] = $organization->get_id();
					$organization_options['name'] = $organization->get_name();

					$change_activity_request = false;
					if (isset($_POST['save_activity']))
					{ // The user has pressed the save button
						if (isset($activity))
						{ // If an activity object is created
							$act_description = phpgw::get_var('description');
							$old_state = $activity->get_state();
							$new_state = phpgw::get_var('state');
							// ... set all parameters
							$activity->set_state(2);
							$activity->set_title(phpgw::get_var('title'));
							$arena_id = phpgw::get_var('internal_arena_id');
							$arena_arr = explode("_", $arena_id);
							if ($arena_arr[0] == 'i')
							{
								$activity->set_internal_arena($arena_arr[1]);
								$activity->set_arena(0);
							}
							else
							{
								$activity->set_internal_arena(0);
								$activity->set_arena($arena_arr[1]);
							}
							//$district_array = phpgw::get_var('district');
							$activity->set_district(phpgw::get_var('district'));
							$activity->set_office(phpgw::get_var('office'));
							$activity->set_state(2);
							$activity->set_category(phpgw::get_var('category'));
							$target_array = phpgw::get_var('target');
							$activity->set_target(implode(",", $target_array));
							$activity->set_description($act_description);
							$activity->set_time(phpgw::get_var('time'));
							$activity->set_contact_persons($persons);
							$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
							$activity->set_frontend(true);

							$contact_person = array();
							$cp_tmp = $persons_array[0];
							$contact_person['original_id'] = $cp_tmp->get_id();
							$contact_person['name'] = phpgw::get_var('contact_name');
							$contact_person['phone'] = phpgw::get_var('contact_phone');
							$contact_person['mail'] = phpgw::get_var('contact_mail');
							$contact_person['group_id'] = $activity->get_group_id();


							$target_ok = false;
							$district_ok = false;
							if ($activity->get_target() && $activity->get_target() != '')
							{
								$target_ok = true;
							}
							if ($activity->get_district() && $activity->get_district() != '')
							{
								$district_ok = true;
							}

							$activity_options = array();
							$activity_options['id'] = ($activity->get_id()) ? $activity->get_id() : "0";
							$activity_options['title'] = $activity->get_title();
							$activity_options['description'] = $activity->get_description();
							$activity_options['category'] = ($activity->get_category()) ? $this->so_activity->get_category_name($activity->get_category()) : "";
							$activity_options['targets'] = "";
							$activity_options['special_adaptation'] = ($activity->get_special_adaptation()) ? true : false;
							$activity_options['internal_arena'] = ($activity->get_internal_arena()) ? true : false;
							$activity_options['building_name'] = $this->so_arena->get_building_name($activity->get_internal_arena());
							$activity_options['arena'] = ($activity->get_arena()) ? true : false;
							$activity_options['arena_name'] = $this->so_arena->get_arena_name($activity->get_arena());
							$activity_options['districts'] = "";
							$activity_options['time'] = $activity->get_time();
							$activity_options['contact_person_1'] = ($activity->get_contact_person_1()) ? true : false;
							$activity_options['contact1_name'] = (isset($persons_array[0])) ? $persons_array[0]->get_name() : "";
							$activity_options['contact1_phone'] = (isset($persons_array[0])) ? $persons_array[0]->get_phone() : "";
							$activity_options['contact1_mail'] = (isset($persons_array[0])) ? $persons_array[0]->get_email() : "";
							$activity_options['office'] = ($activity->get_office()) ? $this->so_activity->get_office_name($activity->get_office()) : "";
							$activity_options['group_id'] = $activity->get_group_id();

							$category_options['current_category_id'] = ($activity->get_category()) ? $activity->get_category() : "";
							$district_options['current_district_id'] = ($activity->get_district()) ? $activity->get_district() : "";
							$office_options['selected_office'] = ($activity->get_office()) ? $activity->get_office() : "";
							$building_options['selected_internal_arena'] = ($activity->get_internal_arena()) ? $activity->get_internal_arena() : "";
							$arena_options['selected_arena'] = ($activity->get_arena()) ? $activity->get_arena() : "";

							$current_target_ids = $activity->get_target();
							$current_target_id_array = explode(",", $current_target_ids);
							$target_options = array();
							foreach ($targets as $t)
							{
								$checked = (in_array($t->get_id(), $current_target_id_array)) ? "checked" : "";
								$target_options[] = array(
									'id' => $t->get_id(),
									'name' => $t->get_name(),
									'checked' => $checked
								);
							}

							if ($activity->get_target())
							{
								$current_target_ids = $activity->get_target();
								$current_target_id_array = explode(",", $current_target_ids);
								foreach ($current_target_id_array as $ct)
								{
									$activity_options['targets'] .= $this->so_activity->get_target_name($ct) . "<br />";
								}
							}

							if ($activity->get_district())
							{
								$current_district_ids = $activity->get_district();
								$current_district_id_array = explode(",", $current_district_ids);
								foreach ($current_district_id_array as $cd)
								{
									$activity_options['districts'] .= $this->so_activity->get_district_name($cd) . "<br />";
								}
							}

							$organization = $this->so_organization->get_single($activity->get_organization_id());
							$organization_options = array();
							$organization_options['id'] = $organization->get_id();
							$organization_options['name'] = $organization->get_name();

							if ($target_ok && $district_ok)
							{

								if ($this->so_activity->store($activity))
								{ // ... and then try to store the object
									$message = lang('messages_saved_form');
									//update group description
									if ($activity->get_group_id())
									{
										$this->so_group->update_group_description($activity->get_group_id(), $act_description);
										$this->so_group->update_group_contact($contact_person);

										$person_arr_tmp = $this->so_contact->get_booking_contact_persons($activity->get_group_id(), true);
										foreach ($person_arr_tmp as $p_t)
										{
											$persons_array_tmp[] = $p_t;
										}
									}
								}
								else
								{
									$error = lang('messages_form_error');
								}

								$GLOBALS['phpgw_info']['flags']['noframework'] = true;

								return self::render_template_xsl('activity', array
										(
										'activity' => $activity_options,
										'organization' => $organization_options,
										'group' => $group,
										'contact1' => $persons_array_tmp[0],
										'arenas' => $arenas,
										'buildings' => $buildings,
										'categories' => $categories,
										'targets' => $targets,
										'districts' => $districts,
										'offices' => $offices,
										'message' => isset($message) ? $message : phpgw::get_var('message'),
										'error' => isset($error) ? $error : phpgw::get_var('error'),
										'helpImg' => $helpImg,
										'ajaxURL' => $ajaxUrl
										)
								);
							}
							else
							{
								if (!$target_ok)
								{
									$error .= "<br/>" . lang('target_not_selected');
								}
								if (!$district_ok)
								{
									$error .= "<br/>" . lang('district_not_selected');
								}

								self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_edit.js');

								return self::render('activity_edit', array
										(
										'activity' => $activity_options,
										'organization' => $organization_options,
										'contact1' => $persons_array[0],
										'org_name' => $org_name,
										'group' => $group,
										'arenas' => $arena_options,
										'buildings' => $building_options,
										'categories' => $category_options,
										'targets' => $target_options,
										'districts' => $district_options,
										'offices' => $office_options,
										'editable' => true,
										'cancel_link' => $cancel_link,
										'message' => isset($message) ? $message : phpgw::get_var('message'),
										'error' => isset($error) ? $error : phpgw::get_var('error'),
										'helpImg' => $helpImg,
										'ajaxURL' => $ajaxUrl
										)
								);
							}
						}
					}
					else if (isset($_POST['change_request']))
					{
						$GLOBALS['phpgw_info']['flags']['noframework'] = true;

						self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'activity_edit.js');

						return self::render_template_xsl('activity_edit', array
								(
								'activity' => $activity_options,
								'organization' => $organization_options,
								'group' => $group,
								'contact1' => $persons_array[0],
								'arenas' => $arena_options,
								'buildings' => $building_options,
								'categories' => $category_options,
								'targets' => $target_options,
								'districts' => $district_options,
								'offices' => $office_options,
								'editable' => true,
								'message' => isset($message) ? $message : phpgw::get_var('message'),
								'error' => isset($error) ? $error : phpgw::get_var('error'),
								'helpImg' => $helpImg,
								'ajaxURL' => $ajaxUrl
								)
						);
					}
					else if (isset($_POST['activity_ok']))
					{ // The user has pressed the save button
						if (isset($activity))
						{ // If an activity object is created
							$activity->set_frontend(true);

							if ($this->so_activity->save_with_no_changes($activity))
							{ // ... and then try to store the object
								$message = lang('activity_ok_message');
							}
							$GLOBALS['phpgw_info']['flags']['noframework'] = true;

							return self::render_template_xsl('activity', array
									(
									'activity' => $activity_options,
									'organization' => $organization_options,
									'group' => $group,
									'contact1' => $persons_array[0],
									'arenas' => $arenas,
									'buildings' => $buildings,
									'categories' => $categories,
									'targets' => $targets,
									'districts' => $districts,
									'offices' => $offices,
									'message' => isset($message) ? $message : phpgw::get_var('message'),
									'error' => isset($error) ? $error : phpgw::get_var('error'),
									'helpImg' => $helpImg,
									'ajaxURL' => $ajaxUrl
									)
							);
						}
					}
					else
					{
						$GLOBALS['phpgw_info']['flags']['noframework'] = true;

						return self::render_template_xsl('activity', array
								(
								'activity' => $activity_options,
								'organization' => $organization_options,
								'group' => $group,
								'contact1' => $persons_array[0],
								'arenas' => $arenas,
								'buildings' => $buildings,
								'categories' => $categories,
								'targets' => $targets,
								'districts' => $districts,
								'offices' => $offices,
								'editable' => false,
								'change_request' => true,
								'message' => isset($message) ? $message : phpgw::get_var('message'),
								'error' => isset($error) ? $error : phpgw::get_var('error'),
								'helpImg' => $helpImg,
								'ajaxURL' => $ajaxUrl
								)
						);
					}
				}
			}
		}

		function index()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.add'));
		}

		function get_organization_groups()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$org_id = phpgw::get_var('orgid');
			$group_id = phpgw::get_var('groupid');
			$returnHTML = "<option value='0'>Ingen gruppe valgt</option>";
			if ($org_id)
			{
				$group_html[] = "<option value='new_group'>Ny gruppe</option>";
				$groups = activitycalendar_sogroup::get_instance()->get(0, 0, '', false, '', '', array(
					'org_id' => $org_id));
				foreach ($groups as $group)
				{
					if (isset($group))
					{
						//$res_g = $group->serialize();
						$selected = "";
						if ($group_id && $group_id > 0)
						{
							$gr_id = (int)$group_id;
							if ($gr_id == (int)$group->get_id())
							{
								$selected_group = " selected";
							}
						}
						$group_html[] = "<option value='" . $group->get_id() . "'" . $selected_group . ">" . $group->get_name() . "</option>";
					}
				}
				$html = implode(' ', $group_html);
				$returnHTML = $returnHTML . ' ' . $html;
			}


			return $returnHTML;
			//return "<option>Ingen gruppe valgt</option>";
		}

		/**
		 * Public method.
		 */
		function get_address_search()
		{
			$search_string = phpgw::get_var('search');
			//var_dump($search_string);
			return activitycalendar_soarena::get_instance()->get_address($search_string);
		}

		function edit_organization_values()
		{
			$org_id = phpgw::get_var('organization_id');
			if (isset($org_id))
			{

				$helpImg = $GLOBALS['phpgw']->common->image('activitycalendarfrontend', 'hjelp.gif');

				phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
					'file'));

				if (isset($_POST['save_org']))
				{ //save updated organization info
					$organization = $this->so_organization->get_single($org_id);

					$org_homepage = phpgw::get_var('homepage');
					if ($org_homepage == 'http://')
					{
						$org_homepage = "";
					}
					$org_info['name'] = phpgw::get_var('orgname');
					$org_info['orgnr'] = phpgw::get_var('orgno');
					$org_info['homepage'] = $org_homepage;
					$org_info['street'] = phpgw::get_var('address');
					$org_info['streetnumber'] = phpgw::get_var('number');
					$org_info['zip'] = phpgw::get_var('postzip');
					$org_info['postaddress'] = phpgw::get_var('postaddress');
					$org_info['district'] = $organization->get_district();
					$org_info['status'] = "change";
					$org_info['original_org_id'] = $org_id;
					$o_id = $this->so_activity->add_organization_local($org_info);

					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('org_contact1_name');
					$contact1['phone'] = phpgw::get_var('org_contact1_phone');
					$contact1['mail'] = phpgw::get_var('org_contact1_mail');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = 0;
					$this->so_activity->add_contact_person_local($contact1);

					$message = lang('change_request_ok', $org_info['name']);

					return self::render_template_xsl('organization_reciept', array
							(
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error'),
							'helpImg' => $helpImg
							)
					);
				}
				else
				{
					$c = createobject('phpgwapi.config', 'activitycalendarfrontend');
					$c->read();
					$config = $c->config_data;

					$ajaxUrl = $c->config_data['AJAXURL'];
					$organization = $this->so_organization->get_single($org_id);
					$person_arr = $this->so_contact->get(0, 0, '', false, '', '',  array(
						'organization_id' => $org_id));
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}

					$organization_options = array();
					$organization_options['id'] = $organization->get_id();
					$organization_options['name'] = $organization->get_name();
					$organization_options['number'] = $organization->get_organization_number();
					$organization_options['address'] = $organization->get_address();
					$organization_options['zip_code'] = $organization->get_zip_code();
					$organization_options['city'] = $organization->get_city();
					$organization_options['homepage'] = $organization->get_homepage();

					$organization_options['contact1_name'] = $persons[0]->get_name();
					$organization_options['contact1_phone'] = $persons[0]->get_phone();
					$organization_options['contact1_mail'] = $persons[0]->get_email();

					self::add_javascript('activitycalendarfrontend', 'activitycalendarfrontend', 'organization_edit.js');

					return self::render_template_xsl('organization_edit', array
							(
							'organization' => $organization_options,
							'contact1' => $persons[0],
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error'),
							'helpImg' => $helpImg,
							'ajaxURL' => $ajaxUrl
							)
					);
				}
			}
		}

		function edit_group_values()
		{
			$group_id = phpgw::get_var('group_id');
			if (isset($group_id))
			{
				if (isset($_POST['save_group']))
				{ //save updated organization info
					$group = $this->so_group->get_single($group_id);

					$group_info['name'] = phpgw::get_var('groupname');
					$group_info['organization_id'] = phpgw::get_var('orgid');
					$group_info['description'] = phpgw::get_var('org_description');
					$group_info['status'] = "change";
					$group_info['original_group_id'] = $group_id;
					$g_id = $this->so_activity->add_group_local($group_info);

					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('group_contact1_name');
					$contact1['phone'] = phpgw::get_var('group_contact1_phone');
					$contact1['mail'] = phpgw::get_var('group_contact1_email');
					$contact1['org_id'] = 0;
					$contact1['group_id'] = $g_id;
					$this->so_activity->add_contact_person_local($contact1);

					$contact2 = array();
					$contact2['name'] = phpgw::get_var('group_contact2_name');
					$contact2['phone'] = phpgw::get_var('group_contact2_phone');
					$contact2['mail'] = phpgw::get_var('group_contact2_email');
					$contact2['org_id'] = 0;
					$contact2['group_id'] = $g_id;
					$this->so_activity->add_contact_person_local($contact2);

					$message = lang('change_request_ok', $group_info['name']);

					$this->render('group_reciept.php', array
						(
						'message' => isset($message) ? $message : phpgw::get_var('message'),
						'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
				}
				else
				{
					$group = $this->so_group->get_single($group_id);
					$person_arr = $this->so_contact->get(0, 0, '', false, '', '', array(
						'group_id' => $group_id));
					foreach ($person_arr as $p)
					{
						$persons[] = $p;
					}

					$this->render('group_edit.php', array
						(
						'group' => $group,
						'contact1' => $persons[0],
						'contact2' => $persons[1],
						'editable' => true,
						'message' => isset($message) ? $message : phpgw::get_var('message'),
						'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
				}
			}
		}

		public function get_organization_activities()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$org_id = phpgw::get_var('orgid');
			$returnHTML = "<option value='0'>Ingen aktivitet valgt</option>";
			if ($org_id)
			{
				$activities = $this->so_activity->get(0, 0, 'title', true, '', '', array(
					'activity_state' => 3, 'activity_org' => $org_id));
				foreach ($activities as $act)
				{
					if (isset($act))
					{
						//$res_g = $group->serialize();
						$activity_html[] = "<option value='" . $act->get_id() . "'>" . $act->get_title() . "</option>";
					}
				}
				$html = implode(' ', $activity_html);
				$returnHTML = $returnHTML . ' ' . $html;
			}


			return $returnHTML;
		}
	}