<?php
/**************************************************************************\
 * phpGroupWare - Addressbook                                               *
 * http://www.phpgroupware.org                                              *
 * Originally Written by Joseph Engo <jengo@phpgroupware.org> and           *
 * Miles Lott <miloschphpgroupware.org>                                     *
 * Heavy changes (near rewrite) by Jonathan Alberto Rivera <jarg AT co.com.mx> *
 * --------------------------------------------                             *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id$ */


	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

class addressbook_uiaddressbook_organizations extends phpgwapi_uicommon
{
	var $contacts;
	var $bo;
	var $cat;
	var $company;
	var $prefs;
	var $abc;
	var $currentapp;
	var $fields;

	var $debug = False;
	private $receipt = array();

	//Public functions
	var $public_functions = array
	(
		'index'			=> true,
		'query'			=> true,
		'view'			=> true,
		'add' 			=> true,
		'edit'			=> true,
		'save'			=> true,
		'delete'		=> true,
		'copy'			=> true,
		'get_others_data' => true,
		'delete_others'	=> true,
		'add_others' => true
	);

	function __construct()
	{
		parent::__construct();

		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('Organizations');
		$this->bo = createObject('addressbook.boaddressbook');
		$this->cats = CreateObject('phpgwapi.categories');
		$this->fields = $this->get_fields();
		self::set_active_menu("{$this->currentapp}::organizations");
		$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
		$this->tab_main_persons		= $this->bo->tab_main_persons;
		$this->tab_main_organizations = $this->bo->tab_main_organizations;
	}

	private function _get_filters()
	{
		$combos = array();
		
		$filter_obj = array(
			array('id' => 'none', 'name' => lang('Show all')),
			array('id' => 'yours', 'name' => lang('Only yours')),
			array('id' => 'private', 'name' => lang('private'))
		);
					
		$combos[] = array('type' => 'filter',
			'name' => 'access',
			'extra' => '',
			'text' => lang('access'),
			'list' => $filter_obj
		);
		
		$qfield = array(
			array('id' => 'org', 'name' => 'Organization Data'),
			array('id' => 'comms', 'name' => 'Communications Data'), 
			array('id' => 'location', 'name' => 'Locations Data'), 
			array('id' => 'other', 'name' => 'Other Data')		
		);
		
		$combos[] = array('type' => 'filter',
			'name' => 'qfield',
			'extra' => '',
			'text' => lang('type'),
			'list' => $qfield
		);
		
		$categories = $this->cats->formatted_xslt_list(array('format' => 'filter',
				'selected' => $this->cat_id, 'globals' => true, 'use_acl' => $this->_category_acl));
		$default_value = array('cat_id' => '', 'name' => lang('all'));
		array_unshift($categories['cat_list'], $default_value);

		$_categories = array();
		foreach ($categories['cat_list'] as $_category)
		{
			$_categories[] = array('id' => $_category['cat_id'], 'name' => $_category['name']);
		}

		$combos[] = array('type' => 'filter',
			'name' => 'category',
			'extra' => '',
			'text' => lang('category'),
			'list' => $_categories
		);
		
		return $combos;
	}
	
	function _get_fields()
	{
		$columns_to_display = $this->bo->get_columns_to_display($this->tab_main_organizations);
		$values = array();
		
		if (is_array($columns_to_display) && count($columns_to_display) > 0)
		{
			$values[] = array('key' => 'contact_id', 'label' => lang('contact id'), 'sortable' => true, 'hidden' => true);
			
			foreach ($columns_to_display as $field => $field_info)
			{
				$values[] = array(
					'key' => $field,
					'label' =>  lang($field),
					'sortable' => true,
					'hidden' => false
				);
			}
			
			$values[] = array('key' => 'owner', 'label' => lang('owner'), 'sortable' => true, 'hidden' => false);			
		}
		else 
		{
			foreach ($this->fields as $field => $field_info)
			{
				if($field_info['action'] & PHPGW_ACL_READ)
				{
					$label = !empty($field_info['translated_label'])  ? $field_info['translated_label'] :'';
					if(!$label)
					{
						$label =!empty($field_info['label']) ? lang($field_info['label']) : $field;
					}

					$data = array(
						'key' => $field,
						'label' =>  $label,
						'sortable' => !empty($field_info['sortable']) ? true : false,
						'hidden' => !empty($field_info['hidden']) ? true : false,
					);

					if(!empty($field_info['formatter']))
					{
						$data['formatter'] = $field_info['formatter'];
					}

					$values[] = $data;
				}
			}
		}
		
		return $values;
	}
	
	function _get_columns()
	{
		$columns_to_display = $this->bo->get_columns_to_display($this->tab_main_organizations);
		
		if (!is_array($columns_to_display) || count($columns_to_display) == 0)
		{
			foreach ($this->fields as $field => $field_info)
			{
				$columns_to_display[$field] = 1;
			}			
		}
		
		return $columns_to_display;
	}
	
	function get_fields($debug = true)
	{
		$fields = array(
			'contact_id' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'int',
				'label' => 'id',
				'sortable'=> true,
				'hidden' => false,
				'public' => false,
				'group' => 'org_data'
				),
			'access' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'access',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'org_data'
				),			
			'org_name' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'org name',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'required' => true,
				'group' => 'org_data'
				),
			'owner' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'owner',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'org_data'
				),
			'addr_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'int',
				'label' => 'addr id',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_add1' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'address 1',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_add2' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'address 2',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_city' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'city',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_state' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'state',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_postal_code' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'postal code',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_country' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'country',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'addr_type' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'type',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => 'addr_data'
				),
			'current_persons' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'persons',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => ''
				),
			'current_categories' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'categories',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => ''
				),
			'comm_data' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'comm_data',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => ''
				),
			'preferred_comm_data' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
				'type' => 'string',
				'label' => 'preferred_comm_data',
				'sortable' => false,
				'query' => true,
				'public' => true,
				'group' => ''
				)
		);

		return $fields;
	}
	
	function index()
	{
		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}

		$data = array(
			'datatable_name' => lang('addressbook'),
			'form' => array(
				'toolbar' => array(
					'item' => array(
					)
				)
			),
			'datatable' => array(
				'source' => self::link(array(
					'menuaction' => "{$this->currentapp}.uiaddressbook_organizations.index",
					'phpgw_return_as' => 'json'
				)),
				'allrows' => true,
				'sorted_by'	=> array('key' => 1, 'dir' => 'asc'),
				'new_item' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_organizations.add")),
				'editor_action' => '',
				'field' => $this->_get_fields()
			)
		);

		$filters = $this->_get_filters();

		foreach ($filters as $filter)
		{
			array_unshift($data['form']['toolbar']['item'], $filter);
		}
		
		$parameters = array(
			'parameter' => array(
				array(
					'name' => 'ab_id',
					'source' => 'contact_id'
				)
			)
		);

		$data['datatable']['actions'][] = array
			(
			'my_name' => 'categorize',
			'text' => lang('Categorize'),
			'type' => 'custom',
			'custom_code' => '
					var oArgs = {"menuaction": "' . $this->currentapp . '.uicategorize_contacts.index"};
					categorize(oArgs);
			',
			'className' => '',
			'parameters' => json_encode(array())
		);
	
		$data['datatable']['actions'][] = array
			(
			'my_name' => 'import_contacts',
			'text' => lang('Import contacts'),
			'type' => 'custom',
			'custom_code' => '
					var oArgs = {"menuaction": "' . $this->currentapp . '.uiXport.import"};
					add_vcard(oArgs);
			',
			'className' => '',
			'parameters' => json_encode(array())
		);
			
		$data['datatable']['actions'][] = array
			(
			'my_name' => 'export_contacts',
			'text' => lang('Export contacts'),
			'type' => 'custom',
			'custom_code' => '
					var oArgs = {"menuaction": "' . $this->currentapp . '.uiXport.export"};
					add_vcard(oArgs);
			',
			'className' => '',
			'parameters' => json_encode(array())
		);

		$data['datatable']['actions'][] = array
			(
			'my_name' => 'view',
			'text' => lang('view'),
			'action' => $GLOBALS['phpgw']->link('/index.php', array
				(
				'menuaction' => "{$this->currentapp}.uiaddressbook_organizations.view"
			)),
			'parameters' => json_encode($parameters)
		);
			
		$data['datatable']['actions'][] = array
			(
			'my_name' => 'copy',
			'text' => lang('copy'),
			'action' => $GLOBALS['phpgw']->link('/index.php', array
				(
				'menuaction' => "{$this->currentapp}.uiaddressbook_organizations.copy"
			)),
			'parameters' => json_encode($parameters)
		);
				
		$data['datatable']['actions'][] = array
			(
			'my_name' => 'edit',
			'text' => lang('edit'),
			'action' => self::link(array
				(
				'menuaction' => "{$this->currentapp}.uiaddressbook_organizations.edit"
			)),
			'parameters' => json_encode($parameters)
		);

		$data['datatable']['actions'][] = array
			(
			'my_name' => 'delete',
			'text' => lang('delete'),
			'confirm_msg' => lang('do you really want to delete this entry'),
			'action' => $GLOBALS['phpgw']->link('/index.php', array
				(
				'menuaction' => "{$this->currentapp}.uiaddressbook_organizations.delete"
			)),
			'parameters' => json_encode($parameters)
		);

		self::add_javascript('addressbook', 'portico', 'addressbook_persons.index.js');
		self::render_template_xsl('datatable_jquery', $data);
	}

	public function query($relaxe_acl = false)
	{
		$search = phpgw::get_var('search');
		$order = phpgw::get_var('order');
		$draw = phpgw::get_var('draw', 'int');
		$columns = phpgw::get_var('columns');
		$category = phpgw::get_var('category');
		$qfield = phpgw::get_var('qfield');
		$access = phpgw::get_var('access');

		$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
		$results = phpgw::get_var('length', 'int', 'REQUEST', 0);
		$query = $search['value'];
		$ordering = $columns[$order[0]['column']]['data'];
		$sort = $order[0]['dir'];
			
		switch ($access)
		{
			case 'yours':
				$_access = PHPGW_CONTACTS_MINE;
				break;
			case 'private':
				$_access = PHPGW_CONTACTS_PRIVATE;
				break;
			default:
				$_access = PHPGW_CONTACTS_ALL;
		}
		
		if ($category)
		{
			$category_filter = $category;
		}
		else
		{
			$category_filter = PHPGW_CONTACTS_CATEGORIES_ALL;
		}

		$columns_to_display = $this->_get_columns();

		unset($columns_to_display['comm_types']);
		$fields = array_keys($columns_to_display);
		
		$fields['owner']='owner';
		$fields['contact_id']='contact_id';

		$fields_search = $fields;

		$query = urldecode(addslashes($query));

		$criteria = $this->bo->criteria_contacts($_access, $category_filter, $qfield, $query, $fields_search);
		$total_all_persons = $this->bo->get_count_orgs($criteria);
		$entries = $this->bo->get_orgs($fields, $start, $results, $ordering, $sort, '', $criteria);
	
		foreach ($entries as &$entry)
		{
			$entry['owner'] = $GLOBALS['phpgw']->accounts->id2name($entry['owner']);
			$entry['addr_type'] = $this->bo->search_location_type_id($entry['addr_type']);
		}

		$values = array_values($entries);
		$result_data = array('results' => $values);

		$result_data['total_records'] = $total_all_persons;
		$result_data['draw'] = $draw;
			
		return $this->jquery_results($result_data);
	}
	
	function add()
	{
		if(!$this->bo->check_add('', $this->owner))
		{
			phpgw::no_access();
		}
		
		$this->edit();
	}

	function view()
	{
		$this->edit(array(), $mode = 'view');
	}
	
	public function edit( $values = array(), $mode = 'edit' )
	{
		$vcard = phpgw::get_var('vcard', 'int', 'REQUEST', 0);
		
		$id = !empty($values['org_data']['contact_id']) ? $values['org_data']['contact_id'] : phpgw::get_var('ab_id', 'int');
		
		if(!$this->bo->check_edit($id, $this->owner))
		{
			phpgw::no_access();
		}

		if (!empty($values['org_data']['contact_id']))
		{
			$organizations_data = $values['org_data'];
			$comm_data = $this->_get_comm_data($id, $values);
			$addr_data = $values['addr_data'];
			
			$_current_person = $values['persons'];
			$_current_cats = $values['categories'];
		}
		else
		{	
			$organizations_data = $this->_get_organizations_data($id);
			$person_orgs_data = $this->_get_person_orgs_data($id);
			$comm_data = $this->_get_comm_data($id, array());
			$addr_data = $this->_get_addr_data($id);
			
			$_current_person = $person_orgs_data['my_person'];
			$_current_cats = $organizations_data['my_cats'];
			
			$organizations_data['owner_name'] = $GLOBALS['phpgw']->accounts->id2name($organizations_data['owner']);
		}

		$tabs = array();
		$tabs['org_data'] = array(
			'label' => lang('Org Data'),
			'link' => '#org_data'
		);
		
		if ($mode == 'edit')
		{
			$tabs['persons'] = array(
				'label' => lang('Persons'),
				'link' => '#persons'
			);
			$tabs['categories'] = array(
				'label' => lang('Categories'),
				'link' => '#categories'
			);
			$tabs['communications'] = array(
				'label' => lang('Communications'),
				'link' => '#communications'
			);
			$tabs['address'] = array(
				'label' => lang('Address'),
				'link' => '#address',
				'function' => "set_tab('address')"
			);
			
			if ($id > 0)
			{
				$tabs['others'] = array(
					'label' => lang('Others'),
					'link' => '#others'
				);
			}
		}
		$active_tab = 'org_data';
		
		$persons = $this->_get_persons();
		
		$current_persons = array();
		$all_persons = array();
		
		if ($persons)
		{
			foreach ($persons as $k => $v)
			{
				if (is_array($_current_person) && in_array($v['contact_id'], $_current_person))
				{
					$current_persons[] = array('id'=> $v['contact_id'], 'name' => $v['per_full_name'], 'selected' => 0);
				}
				else
				{
					$all_persons[] = array('id'=> $v['contact_id'], 'name' => $v['per_full_name']);
				}
			}
		}
		
		$cats = $this->_get_cats();
		$current_cats = array();
		$all_cats = array();
		
		foreach ($cats as $k => $v)
		{
			if (is_array($_current_cats) && in_array($v['id'], $_current_cats))
			{
				$current_cats[] = array('id'=> $v['id'], 'name' => $v['name'], 'selected' => 0);
			}
			else
			{
				$all_cats[] = array('id'=> $v['id'], 'name' => $v['name']);
			}
		}
		
		$addr_type = $this->_get_addr_type($addr_data['addr_type']);
		
		$datatable_def = array();
		
		if ($mode == 'edit'  && $id)
		{
			$tabletools[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'type' => 'custom',
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => "{$this->currentapp}.uiaddressbook_organizations.delete_others",
					'contact_id' => $id,
					'phpgw_return_as' => 'json'
				)) . ";
					var parameters = " . json_encode(array('parameter' => array(array('name' => 'other_id',
							'source' => 'id')))) . ";
					deleteOthersData(oArgs, parameters);
				"
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_organizations.get_others_data",
						'contact_id' => $id, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'name', 'label' => lang('Description'), 'className' => '', 'sortable' => false, 'hidden' => false),
					array('key' => 'value', 'label' => lang('Value'), 'className' => '', 'sortable' => false, 'hidden' => false)
				),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true)
				)
			);
		}
	
		$_others_data = array();
		if ($mode == 'view' && $id)
		{
			$_others_data = $this->bo->get_others_contact_data($id);
		}
		
		$data = array(
			'datatable_def' => $datatable_def,
			'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_organizations.save")),
			'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_organizations.index",)),
			'organizations_data' => $organizations_data,
			'comm_data' => $comm_data,
			'addr_data' => $addr_data,
			'others_data' => $_others_data,
			'mode' => $mode,
			'all_persons' => array('options' => $all_persons),
			'current_persons' => array('options' => $current_persons),
			'all_cats' => array('options' => $all_cats),
			'current_cats' => array('options' => $current_cats),					
			'addr_type' => array('options' => $addr_type),
			'lang_descr' => lang('Please enter a description'),
			'lang_selected' => lang('No record selected'),
			'vcard' => $vcard,
			'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
			'value_active_tab' => $active_tab
		);
		
		phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
		self::add_javascript('addressbook', 'portico', 'addressbook_organizations.edit.js');
		self::render_template_xsl(array('organizations', 'datatable_inline'), array($mode => $data));
	}
	
	private function _populate( $data = array() )
	{	
		$fields = $this->get_fields();
		
		foreach ($fields as $field	=> $field_info)
		{
			if(($field_info['action'] & PHPGW_ACL_ADD) ||  ($field_info['action'] & PHPGW_ACL_EDIT))
			{
				$value = phpgw::get_var($field, $field_info['type']);
				
				if ($field_info['required'] && (($value !== '0' && empty($value)) || empty($value)))
				{
					$this->receipt['error'][] = array('msg' => lang("Field %1 is required", lang($field_info['label'])));
				}
				
				switch ($field_info['group']) 
				{
					case 'org_data':
						$values['org_data'][$field] = $value;
						break;
					case 'addr_data':
						$values['addr_data'][$field] = $value;
						break;
					default:
						$values[$field] = $value;
				}
			}
		}
		
		$values['org_data']['owner'] = $this->owner;

		$values['org_data']['access'] = ($values['org_data']['access']) ? 'private' : 'public';
		$values['org_data']['ispublic'] = $values['org_data']['access'];
		
		$values['persons'] = $values['current_persons'];
		$values['categories'] = $values['current_categories'];

		return $values;
	}
	
	public function save($ajax = false)
	{
		$id = phpgw::get_var('contact_id', 'int');
		
		if (!$_POST)
		{
			return $this->edit();
		}

		/*
		 * Overrides with incoming data from POST
		 */
		$values = $this->_populate();

		if ($id)
		{
			$values['action'] = 'edit';
		}
		else
		{ 
			$values['action'] = 'add';
		}
		
		if ($this->receipt['error'])
		{
			self::message_set($this->receipt);
			$this->edit($values);
		}
		else
		{
			try
			{
				$id = $this->bo->save_organizations($values);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					$this->edit($values);
					return;					
				}
			}

			$this->receipt['message'][] = array('msg' => lang('Orgs has been saved'));
			
			self::message_set($this->receipt);
			self::redirect(array('menuaction' => "{$this->currentapp}.uiaddressbook_organizations.edit", 'ab_id' => $id));
		}
	}
	
	function _get_organizations_data($id)
	{
		$data = ($id) ? $this->bo->get_principal_organizations_data($id) : array();
		
		return $data;
	}
	
	function _get_person_orgs_data($id)
	{
		$data = ($id) ? $this->bo->get_person_orgs_data($id) : array();
		
		return $data;
	}
	
	function _get_comm_data($id, $values)
	{
		if (count($values))
		{
			foreach($values['comm_data'] as $key => $value)
			{						
				if ($value != '')
				{
					$_comm_data['comm_data'][$key] = $value;
					$_comm_data['preferred'] = ($values['preferred_comm_data'] == $key) ? $values['preferred_comm_data'] : '';
				}
			}			
		} 
		else 
		{
			$data = ($id) ? $this->bo->get_comm_contact_data($id,'',True) : array();

			foreach($data as $key => $value)
			{						
				$_comm_data['comm_data'][$value['comm_description']] = $value['comm_data'];
				if ( $value['comm_preferred'] == 'Y' )
				{
					$_comm_data['preferred'] = $value['comm_description'];
				}
			}			
		}

		foreach($this->bo->comm_descr as $key => $value)
		{
			$comm_data[] = array
			(
				'comm_description' => $value['comm_description'],
				'comm_data' => isset($_comm_data['comm_data'][$value['comm_description']]) ? $_comm_data['comm_data'][$value['comm_description']] : '',
				'preferred' => isset($_comm_data['preferred']) && $_comm_data['preferred'] == $value['comm_description'] ? 'Y' : 'N'
			);
		}
		
		return $comm_data;
	}
	
	function _get_persons()
	{
		$fields_to_search = array('contact_id', 'per_full_name');
		$criteria = $this->bo->criteria_contacts(PHPGW_CONTACTS_ALL,PHPGW_CONTACTS_CATEGORIES_ALL,array(),'',$fields_to_search);
		$persons = $this->bo->get_persons($fields_to_search,'','','last_name','','',$criteria);
	
		return $persons;
	}
	
	function get_others_data()
	{
		$contact_id = phpgw::get_var('contact_id', 'int');
		
		if (!$contact_id)
		{
			return array();
		}
		
		$_others_data = $this->bo->get_others_contact_data($contact_id);
		$others_data = array();
		
		foreach($_others_data as $key => $value)
		{
			$others_data[] = array('id' => $value['key_other_id'], 'name' => $value['other_name'], 'value' => $value['other_value']);
		}

		$total_records = count($others_data);

		return array
			(
			'data' => $others_data,
			'draw' => phpgw::get_var('draw', 'int'),
			'recordsTotal' => $total_records,
			'recordsFiltered' => $total_records
		);
	}
	
	function _get_addr_type($id=0)
	{
		$addr_type = array();
		$selected = 0;
		
		if (is_array($this->bo->addr_type))
		{
			foreach($this->bo->addr_type as $key => $value)
			{
				$selected = ($id == $value['addr_type_id']) ? '1' : '0';
				$addr_type[] = array('id' => $value['addr_type_id'], 'name' => $value['addr_description'], 'selected' => $selected);
			}
		}
		
		return $addr_type;
	}
	
	function _get_cats()
	{
		$cats = $this->cats->return_array('all', 0, false, '', '', '', true);
		
		return $cats;
	}
	
	function _get_addr_data($id)
	{
		if (empty($id))
		{
			return array();
		}
		
		$addr_data = $this->bo->get_addr_contact_data($id);
		
		return $addr_data[0];
	}
	
	function delete_others()
	{
		$other_id = phpgw::get_var('other_id');
		
		$result = array();
		
		if (count($other_id) > 0)
		{
			foreach ($other_id as $id)
			{
				$this->bo->delete_specified_other($id, PHPGW_SQL_RUN_SQL);
				$result['message'][] = array('msg' => lang('record has been deleted'));
			}
		}
		
		return $result;
	}
	
	function add_others()
	{
		$contact_id = phpgw::get_var('contact_id');
		$owner = $this->owner;
		
		$value = phpgw::get_var('value');
		$description = phpgw::get_var('description');
		
		$field = array('action'=>'insert', 'other_name'=>$description, 'other_value'=>$value, 
			'key_other_id'=>0, 'other_owner'=>$owner);
		$resp = $this->bo->get_insert_others($contact_id, $field, PHPGW_SQL_RUN_SQL);
		
		$result = array();
		$result['message'][] = array('msg' => lang('record has been saved'));
		
		return $result;
	}
	
	function delete()
	{
		$person_id = phpgw::get_var('ab_id');
		
		if(!$this->bo->check_delete($person_id, $this->owner))
		{
			return lang('no permission to delete');
		}
		
		return $this->delete_contact($person_id, $this->tab_main_organizations);
	}
	
	function delete_contact($contact_id='', $contact_type='')
	{
		$result = array();

		$response = $GLOBALS['phpgw']->hooks->process(array('location' => 'delete_addressbook', 'contact_id' => $contact_id ));

		if(!$this->bo->can_delete_hooks($response))
		{
			$result[] = lang("The following application(s) have requested for this contact to be protected from deletion:");
			
			foreach($this->bo->negative_responses as $appname => $reason)
			{
				$result[] = $appname.' '. lang($reason);
			}

			return implode("<br>", $result);
		}

		$this->bo->delete($contact_id, $contact_type);
		
		$result[] = lang('Organization has been deleted');
		
		return implode("<br>", $result);
	}
	
	function copy()
	{
		$contact_id = phpgw::get_var('ab_id');

		$new_contact_id = $this->bo->copy_contact($contact_id);
		
		phpgwapi_cache::message_set('Organization has been copied', 'message');
		
		$GLOBALS['phpgw']->redirect_link('/index.php', array
				(
					'menuaction'	=> "{$this->currentapp}.uiaddressbook_organizations.view",
					'ab_id'		=> $new_contact_id,
					'vcard' => 1
				));
	}
}