<?php
	phpgw::import_class('activitycalendar.uicommon');
	phpgw::import_class('activitycalendar.soorganization');
	phpgw::import_class('activitycalendar.sogroup');
	phpgw::import_class('activitycalendar.soactivity');

	include_class('activitycalendar', 'organization', 'inc/model/');
	include_class('activitycalendar', 'group', 'inc/model/');

	class activitycalendar_uiorganization extends activitycalendar_uicommon
	{

		protected $so_org;
		protected $so_group;
		protected $so_contact;
		protected $so_activity;
		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'changed_organizations' => true,
			'get_organization_groups' => true,
			'view' => true,
			'edit' => true,
			'save' => true
		);

		public function __construct()
		{
			parent::__construct();
			$so_org = activitycalendar_soorganization::get_instance();
			$so_group = activitycalendar_sogroup::get_instance();
			$so_contact = activitycalendar_socontactperson::get_instance();
			$so_activity = activitycalendar_soactivity::get_instance();
			self::set_active_menu('activitycalendar::organizationList');
			$config = CreateObject('phpgwapi.config', 'activitycalendar');
			$config->read();
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$appname = lang('organizations');

			$function_msg = lang('list %1', $appname);
			$type = 'all_organizations';

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'activitycalendar.uiorganization.index',
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array('menuaction' => 'activitycalendar.uiorganization.download',
						'type' => $type,
						'export' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array('key' => 'organization_number', 'label' => lang('organization_number'),
							'sortable' => true, 'hidden' => false),
						array('key' => 'name', 'label' => lang('name'), 'sortable' => true, 'hidden' => false),
						array('key' => 'district', 'label' => lang('district'), 'sortable' => true,
							'hidden' => false),
						array('key' => 'office', 'label' => lang('office'), 'sortable' => true, 'hidden' => false),
						array('key' => 'description', 'label' => lang('description'), 'sortable' => true,
							'hidden' => false),
						array('key' => 'operations', 'label' => lang('operations'), 'sortable' => false,
							'className' => 'center')
					)
				)
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function save()
		{
			$id = (int)phpgw::get_var('id');

			$so_org = activitycalendar_soorganization::get_instance();
			$so_activity = activitycalendar_soactivity::get_instance();
			//$so_contact	 = activitycalendar_socontactperson::get_instance();
			$org_array = $so_org->get(0, 0, '', false, '', '', array('id' => $id,
				'changed_orgs' => 'true'));
			if (count($org_array) > 0)
			{
				$keys = array_keys($org_array);
				$org = $org_array[$keys[0]];
			}

			if (isset($_POST['store_organization'])) // The user has pressed the store button
			{
				$orgno = phpgw::get_var('orgno');
				$district = phpgw::get_var('org_district');
				if (isset($district) && is_numeric($district))
				{
					//get district name before storing to booking
					$district_name = $so_activity->get_district_from_id($district);
				}
				else
				{
					$district_name = "";
				}
				$homepage = phpgw::get_var('homepage');
				$email = phpgw::get_var('email');
				$phone = phpgw::get_var('phone');
				$address = phpgw::get_var('address');
				$zip = phpgw::get_var('zip_code');
				$city = phpgw::get_var('city');
				//phpgw::get_var('address') . ' ' . phpgw::get_var('number') . ', ' . phpgw::get_var('postaddress');
				//$address_array = explode(",",$address_tmp);
				$desc = phpgw::get_var('org_description');

				$org_info = array();
				$org_info['name'] = $org->get_name(); //new
				$orgno_tmp = $orgno;
				if (strlen($orgno_tmp) > 9)
				{
					$orgno_tmp = NULL;
				}
				$org_info['orgnr'] = $orgno_tmp;

				$org_info['homepage'] = $homepage;
				$org_info['phone'] = $phone;
				$org_info['email'] = $email;
				$org_info['description'] = $desc;
				$org_info['street'] = $address;
				$org_info['zip'] = $zip;
				$org_info['postaddress'] = $city;
				$org_info['activity_id'] = '';
				$org_info['district'] = $district_name;

				$contact1_id = phpgw::get_var('contact1_id');
				$contact2_id = phpgw::get_var('contact2_id');

				$contact1_name = phpgw::get_var('contact1_name');
				$contact1_phone = phpgw::get_var('contact1_phone');
				$contact1_email = phpgw::get_var('contact1_email');

				$contact2_name = phpgw::get_var('contact2_name');
				$contact2_phone = phpgw::get_var('contact2_phone');
				$contact2_email = phpgw::get_var('contact2_email');


				$new_org_id = $so_org->transfer_organization($org_info);
				if ($new_org_id)
				{
					//update activity with new org id
					//add contact persons to booking
					$contact1 = array();
					$contact1['name'] = $contact1_name;
					$contact1['phone'] = $contact1_phone;
					$contact1['mail'] = $contact1_email;
					$contact1['org_id'] = $new_org_id;
					$so_activity->add_contact_person_org($contact1);

					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');

					//get affected activities and update with new org id
					$update_activities = $so_activity->get_activities_for_update($id);
					//var_dump($update_activities);
					foreach ($update_activities as $act_id)
					{
						$act = $so_activity->get_single($act_id);
						$act->set_organization_id($new_org_id);
						$act->set_new_org(false);
						$so_activity->store($act);
					}

					//set local organization as stored
					$org->set_change_type("added");
					$org->set_transferred(true);
					$so_org->update_local($org);
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uidashboard.index'));
			}
			if (isset($_POST['reject_organization'])) // The user has pressed the reject button
			{
				$reject_org_id = $id;
				if ($so_org->reject_organization($reject_org_id))
				{
					$update_activities = $so_activity->get_activities_for_update($reject_org_id);
					//var_dump($update_activities);
					foreach ($update_activities as $act_id)
					{
						$act = $so_activity->get_single($act_id);
						$act->set_state(5);
						$so_activity->store($act);
					}
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uidashboard.index'));
			}
			if (isset($_POST['reject_organization_update'])) // The user has pressed the reject button
			{
				$reject_org_id = $id;
				$so_org->reject_organization($reject_org_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uidashboard.index'));
			}
			else if (isset($_POST['update_organization'])) // The user has pressed the store button
			{
				$original_org_id = phpgw::get_var('original_org_id');
				$orgno = phpgw::get_var('orgno');
				$district = phpgw::get_var('org_district');
				if (isset($district) && is_numeric($district))
				{
					//get district name before storing to booking
					$district_name = $so_activity->get_district_from_id($district);
				}
				else
				{
					$district_name = "";
				}
				$homepage = phpgw::get_var('homepage');
				$email = phpgw::get_var('email');
				$phone = phpgw::get_var('phone');
				$address = phpgw::get_var('address');
				$zip = phpgw::get_var('zip_code');
				$city = phpgw::get_var('city');
				$desc = phpgw::get_var('org_description');

				$org_info = array();
				$org_info['name'] = $org->get_name(); //new
				$orgno_tmp = $orgno;
				if (strlen($orgno_tmp) > 9)
				{
					$orgno_tmp = NULL;
				}
				$org_info['orgnr'] = $orgno_tmp;

				$org_info['homepage'] = $homepage;
				$org_info['phone'] = $phone;
				$org_info['email'] = $email;
				$org_info['description'] = $desc;
				$org_info['street'] = $address;
				$org_info['zip_code'] = $zip;
				$org_info['city'] = $city;
				$org_info['activity_id'] = '';
				$org_info['district'] = $district_name;
				$org_info['orgid'] = $original_org_id;

				$contact1_id = phpgw::get_var('contact1_id');
				$contact2_id = phpgw::get_var('contact2_id');

				$contact1_name = phpgw::get_var('contact1_name');
				$contact1_phone = phpgw::get_var('contact1_phone');
				$contact1_email = phpgw::get_var('contact1_email');

				$contact2_name = phpgw::get_var('contact2_name');
				$contact2_phone = phpgw::get_var('contact2_phone');
				$contact2_email = phpgw::get_var('contact2_email');


				$so_org->update_organization($org_info);
				$so_activity->delete_contact_persons($original_org_id);

				//add contact persons to booking
				$contact1 = array();
				$contact1['name'] = $contact1_name;
				$contact1['phone'] = $contact1_phone;
				$contact1['mail'] = $contact1_email;
				$contact1['org_id'] = $original_org_id;
				$so_activity->add_contact_person_org($contact1);

				$contact2 = array();
				$contact2['name'] = $contact2_name;
				$contact2['phone'] = $contact2_phone;
				$contact2['mail'] = $contact2_email;
				$contact2['org_id'] = $original_org_id;
				$so_activity->add_contact_person_org($contact2);

				phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');

				//set local organization as stored
				$org->set_change_type("added");
				$org->set_transferred(true);
				$so_org->update_local($org);

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uidashboard.index'));
			}
		}

		public function changed_organizations()
		{
			self::set_active_menu('activitycalendar::organizationList::changed_organizations');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$appname = lang('changed_organizations_groups');

			$function_msg = lang('list %1', $appname);
			$type = 'changed_organizations';

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'activitycalendar.uiorganization.index',
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array('key' => 'organization_number', 'label' => lang('organization_number'),
							'sortable' => true, 'hidden' => false),
						array('key' => 'name', 'label' => lang('name'), 'sortable' => true, 'hidden' => false),
						array('key' => 'district', 'label' => lang('district'), 'sortable' => true,
							'hidden' => false),
						array('key' => 'office', 'label' => lang('office'), 'sortable' => true, 'hidden' => false),
						array('key' => 'description', 'label' => lang('description'), 'sortable' => true,
							'hidden' => false),
						array('key' => 'change_type', 'label' => lang('change_type'), 'sortable' => false,
							'hidden' => false),
						array('key' => 'operations', 'label' => lang('operations'), 'sortable' => false,
							'className' => 'center')
					)
				)
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function index_json()
		{
			$organizations = activitycalendar_soorganization::get_instance()->get(0, 0, '', false, '', '',array()); //get organizations
			array_walk($organizations["results"], array($this, "_add_links"), "booking.uiorganization.show");

			foreach ($organizations["results"] as &$organization)
			{

				$contact = (isset($organization['contacts']) && isset($organization['contacts'][0])) ? $organization['contacts'][0] : null;

				if ($contact)
				{
					$organization += array(
						"primary_contact_name" => ($contact["name"]) ? $contact["name"] : '',
						"primary_contact_phone" => ($contact["phone"]) ? $contact["phone"] : '',
						"primary_contact_email" => ($contact["email"]) ? $contact["email"] : '',
					);
				}
			}

			return $this->yui_results($organizations);
		}

		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			$id = (int)phpgw::get_var('id');

			$so_org = activitycalendar_soorganization::get_instance();
			$so_activity = activitycalendar_soactivity::get_instance();
			$so_contact = activitycalendar_socontactperson::get_instance();
			$org_array = $so_org->get(0, 0, '', false, '', '', array('id' => $id,
				'changed_orgs' => 'true'));
			if (count($org_array) > 0)
			{
				$keys = array_keys($org_array);
				$org = $org_array[$keys[0]];
			}

			$districts = $so_activity->get_districts();

			$contact_persons = $so_contact->get_local_contact_persons($org->get_id());
			$cp1 = $contact_persons[0];
			$cp2 = $contact_persons[1];

			$curr_district = $org->get_district();
			if (!is_numeric($curr_district))
			{
				$curr_district = activitycalendar_soactivity::get_instance()->get_district_from_name($org->get_district());
			}
			$district_options[] = array('id' => '', 'name' => lang('Ingen bydel valgt'), 'selected' => 0);
			foreach ($districts as $d)
			{
				$selected = ($curr_district == $d['part_of_town_id']) ? 1 : 0;
				$district_options[] = array('id' => $d['part_of_town_id'], 'name' => $d['name'],
					'selected' => $selected);
			}

			$tabs = array();
			$tabs['organization'] = array('label' => lang('organization'), 'link' => '#organization');
			$active_tab = 'organization';

			$data = array
				(
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiorganization.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiorganization.changed_organizations')),
				'lang_update_org' => lang('update_org'),
				'lang_reject' => lang('reject'),
				'lang_store' => lang('store'),
				'lang_cancel' => lang('cancel'),
				'organization_id' => $org->get_id(),
				'original_org_id' => ($org->get_original_org_id()) ? $org->get_original_org_id() : '',
				'organization_name' => $org->get_name(),
				'organization_no' => $org->get_organization_number(),
				'homepage' => $org->get_homepage(),
				'email' => $org->get_email(),
				'address' => $org->get_address() . ' ' . $org->get_addressnumber(),
				'phone' => $org->get_phone(),
				'zip_code' => $org->get_zip_code(),
				'city' => $org->get_city(),
				'description' => $org->get_description(),
				'contact1_id' => $cp1->get_id(),
				'contact1_name' => $cp1->get_name(),
				'contact1_phone' => $cp1->get_phone(),
				'contact1_email' => $cp1->get_email(),
				'contact2_id' => ($cp2) ? $cp2->get_id() : '',
				'contact2_name' => ($cp2) ? $cp2->get_name() : '',
				'contact2_phone' => ($cp2) ? $cp2->get_phone() : '',
				'contact2_email' => ($cp2) ? $cp2->get_email() : '',
				'list_district_options' => array('options' => $district_options),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);

			self::render_template_xsl(array('organization'), array('edit' => $data));
		}

		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');

			$id = (int)phpgw::get_var('id');

			$so_org = activitycalendar_soorganization::get_instance();
			$so_contact = activitycalendar_socontactperson::get_instance();
			$org_array = $so_org->get(0, 0, '', false, '', '', array('id' => $id,
				'changed_orgs' => 'true'));
			if (count($org_array) > 0)
			{
				$keys = array_keys($org_array);
				$org = $org_array[$keys[0]];
			}

			$contact_persons = $so_contact->get_local_contact_persons($org->get_id());
			$cp1 = $contact_persons[0];
			$cp2 = $contact_persons[1];

			if ($org->get_change_type() == 'new')
			{
				if ($org->get_district())
				{
					$dictrict = activitycalendar_soactivity::get_instance()->get_district_from_id($org->get_district());
				}
			}
			else
			{
				if ($org->get_district() && is_numeric($org->get_district()))
				{

					$dictrict = activitycalendar_soactivity::get_instance()->get_district_from_id($org->get_district());
				}
				else
				{
					$dictrict = $org->get_district();
				}
			}

			$tabs = array();
			$tabs['organization'] = array('label' => lang('organization'), 'link' => '#organization');
			$active_tab = 'organization';

			$data = array
				(
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiorganization.changed_organizations')),
				'edit_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiorganization.edit',
					'id' => $id)),
				'lang_edit' => lang('edit'),
				'lang_cancel' => lang('cancel'),
				'organization_id' => $org->get_id(),
				'original_org_id' => ($org->get_original_org_id()) ? $org->get_original_org_id() : '',
				'organization_name' => $org->get_name(),
				'organization_no' => $org->get_organization_number(),
				'homepage' => $org->get_homepage(),
				'email' => $org->get_email(),
				'address' => $org->get_address() . ' ' . $org->get_addressnumber(),
				'phone' => $org->get_phone(),
				'zip_code' => $org->get_zip_code(),
				'city' => $org->get_city(),
				'description' => $org->get_description(),
				'dictrict' => $dictrict,
				'transferred' => ($org->get_transferred() ? 1 : ''),
				'contact1_id' => $cp1->get_id(),
				'contact1_name' => $cp1->get_name(),
				'contact1_phone' => $cp1->get_phone(),
				'contact1_email' => $cp1->get_email(),
				'contact2_id' => ($cp2) ? $cp2->get_id() : '',
				'contact2_name' => ($cp2) ? $cp2->get_name() : '',
				'contact2_phone' => ($cp2) ? $cp2->get_phone() : '',
				'contact2_email' => ($cp2) ? $cp2->get_email() : ''
			);

			self::render_template_xsl(array('organization'), array('view' => $data));
		}

		/**
		 * (non-PHPdoc)
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'identifier';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for = (string)$search['value'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', '');

			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			$length = phpgw::get_var('length', 'int');
			$user_rows_per_page = $length > 0 ? $length : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$num_of_objects = $length == -1 ? null : $user_rows_per_page;

			$export = phpgw::get_var('export', 'bool');
			if ($export)
			{
				$num_of_objects = null;
			}

			//Retrieve the type of query and perform type specific logic
			$type = phpgw::get_var('type');
			$changed_org = false;
			$changed_group = false;
			switch ($type)
			{
				case 'changed_organizations':
					$filters = array('changed_orgs' => 'true');
					$changed_org = true;
					break;
				case 'new_organizations':
					$filters = array('new_orgs' => 'true');
					$changed_org = true;
					$sort_field = 'identifier';
					break;
				case 'changed_groups':
					$filters = array('changed_groups' => 'true');
					$changed_group = true;
					break;
				case 'new_groups':
					$filters = array('new_groups' => 'true');
					$changed_group = true;
					break;
				default: // ... get all parties of a given type
					//$filters = array('party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
					$filters = array();
					break;
			}
			if ($changed_group)
			{
				$result_objects = activitycalendar_sogroup::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$result_count = activitycalendar_sogroup::get_instance()->get_count($search_for, $search_type, $filters);
			}
			else
			{
				$result_objects = activitycalendar_soorganization::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$result_count = activitycalendar_soorganization::get_instance()->get_count($search_for, $search_type, $filters);
			}

			//var_dump($result_objects);
			// Create an empty row set
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$res = $result->serialize();
					$org_id = $result->get_id();
					//$rows[] = $result->serialize();
					$rows[] = $res;
					if (!$changed_group && !$changed_org)
					{
						$filter_group = array('org_id' => $org_id);
						$result_groups = activitycalendar_sogroup::get_instance()->get(0, 0, $sort_field, $sort_ascending, $search_for, $search_type, $filter_group);
						foreach ($result_groups as $result_group)
						{
							if (isset($result_group))
							{
								$res_g = $result_group->serialize();
								$rows[] = $res_g;
							}
						}
					}
				}
			}

			if (!$export)
			{
				array_walk($rows, array($this, 'add_actions'), array(// Parameters (non-object pointers)
					$type   // [2] The type of query
					)
				);
			}

			if ($export)
			{
				return $rows;
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $result_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function get_organization_groups()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$org_id = phpgw::get_var('orgid');
			$group_id = phpgw::get_var('groupid');
			$returnHTML = "<option value='0'>Ingen gruppe valgt</option>";
			if ($org_id)
			{
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
		 * Public method. Called when a user wants to view information about a party.
		 * @param HTTP::id	the party ID
		 */
		/* public function view()
		  {
		  $GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');
		  // Get the contract part id
		  $party_id = (int)phpgw::get_var('id');
		  if(isset($party_id) && $party_id > 0)
		  {
		  $party = rental_soparty::get_instance()->get_single($party_id);
		  }
		  else
		  {
		  $this->render('permission_denied.php', array('error' => lang('invalid_request')));
		  return;
		  }

		  if(isset($party) && $party->has_permission(PHPGW_ACL_READ))
		  {
		  return $this->render(
		  'party.php', array(
		  'party' 	=> $party,
		  'editable' => false,
		  'cancel_link' => self::link(array('menuaction' => 'rental.uiparty.index', 'populate_form' => 'yes')),
		  )
		  );
		  }
		  else
		  {
		  $this->render('permission_denied.php', array('error' => lang('permission_denied_view_party')));
		  }
		  } */

		public function download_agresso()
		{
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header('export.txt', 'text/plain');
			print rental_soparty::get_instance()->get_export_data();
		}

		/**
		 * Add action links and labels for the context menu of the list items
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [composite_id, type of query, editable]
		 */
		public function add_actions( &$value, $key, $params )
		{
			$actions = array();

			$query_type = $params[0];

			switch ($query_type)
			{
				case 'all_organizations':
					if ($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'booking.uigroup.show',
								'id' => $value['id'])));
					}
					else
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'booking.uiorganization.show',
								'id' => $value['id'])));
					}
					$actions[] = '<a href="' . $url . '">' . lang('show') . '</a>';
					break;

				case 'changed_organizations':
					if ($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'], 'type' => 'group')));
					}
					else
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'])));
					}
					$actions[] = '<a href="' . $url . '">' . lang('show') . '</a>';
					if ($value['transferred'] == false)
					{
						if ($value['organization_id'] != '' && $value['organization_id'] != null)
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
									'id' => $value['id'], 'type' => 'group')));
						}
						else
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
									'id' => $value['id'])));
						}
						$actions[] = '<a href="' . $url . '">' . lang('edit') . '</a>';
					}
					break;
				case 'new_organizations':
					if ($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'], 'type' => 'group')));
					}
					else
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'])));
					}
					$actions[] = '<a href="' . $url . '">' . lang('show') . '</a>';
					if ($value['transferred'] == false)
					{
						if ($value['organization_id'] != '' && $value['organization_id'] != null)
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
									'id' => $value['id'], 'type' => 'group')));
						}
						else
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
									'id' => $value['id'])));
						}
						$actions[] = '<a href="' . $url . '">' . lang('edit') . '</a>';
					}
					break;
				case 'changed_groups':
					if ($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'], 'type' => 'group')));
					}
					else
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'])));
					}
					$actions[] = '<a href="' . $url . '">' . lang('show') . '</a>';
					if ($value['transferred'] == false)
					{
						if ($value['organization_id'] != '' && $value['organization_id'] != null)
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
									'id' => $value['id'], 'type' => 'group')));
						}
						else
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
									'id' => $value['id'])));
						}
						$actions[] = '<a href="' . $url . '">' . lang('edit') . '</a>';
					}
					break;
				case 'new_groups':
					if ($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'], 'type' => 'group')));
					}
					else
					{
						$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.view',
								'id' => $value['id'])));
					}
					$actions[] = '<a href="' . $url . '">' . lang('show') . '</a>';
					if ($value['transferred'] == false)
					{
						if ($value['organization_id'] != '' && $value['organization_id'] != null)
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
									'id' => $value['id'], 'type' => 'group')));
						}
						else
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
									'id' => $value['id'])));
						}
						$actions[] = '<a href="' . $url . '">' . lang('edit') . '</a>';
					}
					break;
			}

			return $value['operations'] = implode(' | ', $actions);
		}
	}