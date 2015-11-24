<?php
	phpgw::import_class('activitycalendar.uicommon');
	phpgw::import_class('activitycalendar.soorganization');
	phpgw::import_class('activitycalendar.sogroup');
	phpgw::import_class('activitycalendar.soactivity');

	include_class('activitycalendar', 'organization', 'inc/model/');
	include_class('activitycalendar', 'group', 'inc/model/');
	include_class('activitycalendar', 'activity', 'inc/model/');

	class activitycalendar_uidashboard extends activitycalendar_uicommon
	{

		public $public_functions = array
			(
			'index' => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('activitycalendar::dashboard');
			$config = CreateObject('phpgwapi.config', 'activitycalendar');
			$config->read();
		}

		public function index()
		{
			$columns_def_organization = array(
				array('key'=>'organization_number', 'label'=>lang('organization_number'), 'sortable'=>false),
				array('key'=>'name', 'label'=>lang('name'), 'sortable'=>false),
				array('key'=>'district', 'label'=>lang('district'), 'sortable'=>false),
				array('key'=>'office', 'label'=>lang('office'), 'sortable'=>false),				
				array('key'=>'description', 'label'=>lang('description'), 'sortable'=>false),
				array('key'=>'change_type', 'label'=>lang('change_type'), 'sortable'=>false)
			);
		
			/*$tabletools_organization[] = array
				(
					'my_name'		=> 'edit',
					'text'			=> lang('edit'),
					'action'		=> self::link(array(
							'menuaction'	=> 'rental.uicontract.edit'
					)),
					'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
				);
			
			$tabletools_organization[] = array
				(
					'my_name'		=> 'show',
					'text'			=> lang('show'),
					'action'		=> self::link(array(
							'menuaction'	=> 'rental.uicontract.view'
					)),
					'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
				);*/

			$datatable_def[] = array
			(
				'container'		=> 'datatable-container_0',
				'requestUrl'	=> json_encode(self::link(array('menuaction'=>'activitycalendar.uiorganization.query', 'type'=>'new_organizations', 'phpgw_return_as'=>'json'))),
				'data'			=> json_encode(array()),
				'ColumnDefs'	=> $columns_def_organization,
				//'tabletools'	=> $tabletools_organization,
				'config'		=> array(
					array('disableFilter'	=> true)
				)
			);
			
			$columns_def_activities = array(
				array('key'=>'id', 'label'=>lang('id'), 'sortable'=>true),
				array('key'=>'title', 'label'=>lang('title'), 'sortable'=>true),
				array('key'=>'state', 'label'=>lang('status'), 'sortable'=>true),
				array('key'=>'organization_id', 'label'=>lang('organization'), 'sortable'=>true),				
				array('key'=>'group_id', 'label'=>lang('group'), 'sortable'=>true),
				array('key'=>'district', 'label'=>lang('district'), 'sortable'=>true),
				array('key'=>'office', 'label'=>lang('office'), 'sortable'=>true),
				array('key'=>'category', 'label'=>lang('category'), 'sortable'=>true),
				array('key'=>'description', 'label'=>lang('description'), 'sortable'=>true),
				array('key'=>'arena', 'label'=>lang('arena'), 'sortable'=>true),
				array('key'=>'time', 'label'=>lang('time'), 'sortable'=>true),
				array('key'=>'contact_person_1', 'label'=>lang('contact_person_1'), 'sortable'=>true),
				array('key'=>'contact_person_2', 'label'=>lang('contact_person_2'), 'sortable'=>true),
				array('key'=>'last_change_date', 'label'=>lang('last_change_date'), 'sortable'=>true)
			);
			
			$datatable_def[] = array
			(
				'container'		=> 'datatable-container_1',
				'requestUrl'	=> json_encode(self::link(array('menuaction'=>'activitycalendar.uiactivities.query', 'type'=>'new_activities', 'phpgw_return_as'=>'json'))),
				'data'			=> json_encode(array()),
				'ColumnDefs'	=> $columns_def_activities,
				//'tabletools'	=> $tabletools_organization,
				'config'		=> array(
					array('disableFilter'	=> true)
				)
			);
			
			$data = array
				(
					'datatable_def'					=> $datatable_def
				);
			
			self::render_template_xsl(array('dashboard', 'datatable_inline'), array('edit' => $data));			
		}

		public function changed_organizations()
		{
			self::set_active_menu('activitycalendar::organizationList::changed_organizations');
			$this->render('organization_list_changed.php');
		}

		public function edit()
		{
			return false;
		}

		/**
		 * (non-PHPdoc)
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}
			// YUI variables for paging and sorting
			$start_index	 = phpgw::get_var('startIndex', 'int');
			$num_of_objects	 = phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		 = phpgw::get_var('sort', 'string', 'GET', 'identifier');
			$sort_ascending	 = phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for		 = phpgw::get_var('query');
			$search_type	 = phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects	 = array();
			$result_count	 = 0;

			//Create an empty result set
			$parties = array();

			$exp_param	 = phpgw::get_var('export');
			$export		 = false;
			if(isset($exp_param))
			{
				$export			 = true;
				$num_of_objects	 = null;
			}

			//Retrieve the type of query and perform type specific logic
			$type			 = phpgw::get_var('type');
			$changed_org	 = false;
			$changed_group	 = false;
			switch($type)
			{
				case 'changed_organizations':
					$filters		 = array('changed_orgs' => 'true');
					$changed_org	 = true;
					break;
				case 'changed_groups':
					$filters		 = array('changed_groups' => 'true');
					$changed_group	 = true;
					break;
				default: // ... get all parties of a given type
					//$filters = array('party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
					break;
			}
			if($changed_group)
			{
				$result_objects	 = activitycalendar_sogroup::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$result_count	 = activitycalendar_sogroup::get_instance()->get_count($search_for, $search_type, $filters);
			}
			else
			{
				$result_objects	 = activitycalendar_soorganization::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$result_count	 = activitycalendar_soorganization::get_instance()->get_count($search_for, $search_type, $filters);
			}

			//var_dump($result_objects);
			// Create an empty row set
			$rows = array();
			foreach($result_objects as $result)
			{
				if(isset($result))
				{
					$res	 = $result->serialize();
					$org_id	 = $result->get_id();
					//$rows[] = $result->serialize();
					$rows[]	 = $res;
					if(!$changed_group && !$changed_org)
					{
						$filter_group	 = array('org_id' => $org_id);
						$result_groups	 = activitycalendar_sogroup::get_instance()->get(null, null, $sort_field, $sort_ascending, $search_for, $search_type, $filter_group);
						foreach($result_groups as $result_group)
						{
							if(isset($result_group))
							{
								$res_g	 = $result_group->serialize();
								$rows[]	 = $res_g;
							}
						}
					}
				}
			}
			// ... add result data
			$organization_data = array('results' => $rows, 'total_records' => $result_count);

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			if(!$export)
			{
				array_walk(
				$organization_data['results'], array($this, 'add_actions'), array(// Parameters (non-object pointers)
					$type			// [2] The type of query
				)
				);
			}


			return $this->yui_results($organization_data, 'total_records', 'results');
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
								$selected_group = " selected";
							}
						}
						$group_html[] = "<option value='" . $group->get_id() . "'" . $selected_group . ">" . $group->get_name() . "</option>";
					}
				}
				$html		 = implode(' ', $group_html);
				$returnHTML	 = $returnHTML . ' ' . $html;
			}


			return $returnHTML;
			//return "<option>Ingen gruppe valgt</option>";
		}

		/**
		 * Public method. Called when a user wants to view information about a party.
		 * @param HTTP::id	the party ID
		 */
		public function view()
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
					'party'			 => $party,
					'editable'		 => false,
					'cancel_link'	 => self::link(array('menuaction' => 'rental.uiparty.index', 'populate_form' => 'yes')),
				)
				);
			}
			else
			{
				$this->render('permission_denied.php', array('error' => lang('permission_denied_view_party')));
			}
		}

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
		public function add_actions(&$value, $key, $params)
		{
			//Defining new columns
			$value['ajax']		 = array();
			$value['actions']	 = array();
			$value['labels']	 = array();

			$query_type = $params[0];

			switch($query_type)
			{
				case 'all_organizations':
					$value['ajax'][] = false;
					if($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'booking.uigroup.show',
							'id' => $value['id'])));
					}
					else
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'booking.uiorganization.show',
							'id' => $value['id'])));
					}
					$value['labels'][] = lang('show');
					break;

				case 'changed_organizations':
					$value['ajax'][] = false;
					if($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show',
							'id' => $value['id'], 'type' => 'group')));
					}
					else
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show',
							'id' => $value['id'])));
					}
					$value['labels'][] = lang('show');
					if($value['transferred'] == false)
					{
						$value['ajax'][] = false;
						if($value['organization_id'] != '' && $value['organization_id'] != null)
						{
							$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show',
								'id' => $value['id'], 'type' => 'group')));
						}
						else
						{
							$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
								'id' => $value['id'])));
						}
						$value['labels'][] = lang('edit');
					}
					break;
				case 'changed_groups':
					$value['ajax'][] = false;
					if($value['organization_id'] != '' && $value['organization_id'] != null)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show',
							'id' => $value['id'], 'type' => 'group')));
					}
					else
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show',
							'id' => $value['id'])));
					}
					$value['labels'][] = lang('show');
					if($value['transferred'] == false)
					{
						$value['ajax'][] = false;
						if($value['organization_id'] != '' && $value['organization_id'] != null)
						{
							$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
								'id' => $value['id'], 'type' => 'group')));
						}
						else
						{
							$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit',
								'id' => $value['id'])));
						}
						$value['labels'][] = lang('edit');
					}
					break;
			}
		}
	}