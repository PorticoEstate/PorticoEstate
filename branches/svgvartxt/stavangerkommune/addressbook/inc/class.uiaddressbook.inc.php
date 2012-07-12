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


phpgw::import_class('phpgwapi.datetime');

class uiaddressbook
{
	var $contacts;
	var $bo;
	var $cat;
	var $company;
	var $prefs;
	var $abc;

	var $debug = False;

	var $start;
	var $limit;
	var $query;
	var $sort;
	var $order;
	var $filter;
	var $cat_id;
	var $bname;
	var $template;
	var $options_data;
	var $old_tab;

	var $all_orgs_data;
	var $my_orgs_data;

	var $comm_v;
	/**
	 * @var array $fields_data holds the data to navigate between the tabs
	 */
	var $fields_data = array();
	var $entry_data;

	/**
	 * @var string $action the current modifying action
	 */
	var $action = '';

	/**
	 * @var string $submit the form submit action, only set to a real value on POST
	 */
	var $submit = '';

	/**
	* @var int $owner the user who will "own" any records created, can be different to current user
	* @todo confirm that this is docmented correctly
	*/ 
	var $owner;

	// 		//This are the principal tabs
	// 		var $tab_main_persons = 'Persons';
	// 		var $tab_main_organizations = 'Organizations';

	//This are the tabs for each principal tab
	// 		var $tab_person_data = 'Person Data';
	// 		var $tab_org_data = 'Org Data';
	// 		var $tab_orgs = 'Orgs';
	// 		var $tab_persons = 'Persons';
	// 		var $tab_cats = 'Categories';
	// 		var $tab_comms = 'Communications';
	// 		var $tab_address = 'Address';
	// 		var $tab_others = 'Others';
	// 		var $tab_extra = 'More data';

	//Public functions
	var $public_functions = array
	(
		'index'			=> true,
		'view'			=> true,
		'add' 			=> true,
		'add_email'		=> true,
		'copy'			=> true,
		'edit'			=> true,
		'delete'		=> true,
		'preferences' 	=> true,
		'add_person'	=> true,
		'add_org'		=> true,
		'edit_person'	=> true,
		'edit_org'		=> true,
		'view_contact'	=> true,
		'view_person'	=> true,
		'view_org'		=> true,
		'copy_person'	=> true,
		'copy_org'		=> true,
		'delete_person'	=> true,
		'delete_org'	=> true,
		'java_script'	=> true,
	);

	function __construct()
	{
		//$GLOBALS['phpgw']->country	= CreateObject('phpgwapi.country'); // commented out as it is never used - skwashd nov07
		$GLOBALS['phpgw']->browser	= CreateObject('phpgwapi.browser');
		$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
		$this->custom_fields		= CreateObject('addressbook.uifields');
		$this->bo					= CreateObject('addressbook.boaddressbook');
		$this->template				=& $GLOBALS['phpgw']->template;
		$this->cat					= CreateObject('phpgwapi.categories');
		$this->company				= CreateObject('phpgwapi.categories','addressbook_company');
		$this->prefs				= isset($GLOBALS['phpgw_info']['user']['preferences']['addressbook']) ? $GLOBALS['phpgw_info']['user']['preferences']['addressbook'] : array();
		$this->owner				= $GLOBALS['phpgw_info']['user']['account_id']; 

		$this->contact_type			= $this->bo->contact_type;
		$this->comm_descr			= $this->bo->comm_descr;
		$this->comm_type			= $this->bo->comm_type;
		$this->addr_type			= $this->bo->addr_type;
		$this->note_type			= $this->bo->note_type;
		$this->tab_main_persons		= $this->bo->tab_main_persons;
		$this->tab_main_organizations = $this->bo->tab_main_organizations;

		$this->tab_person_data		= lang('Person Data');
		$this->tab_org_data			= lang('Org Data');
		$this->tab_orgs				= lang('Orgs');
		$this->tab_persons			= lang('Persons');
		$this->tab_cats				= lang('Categories');
		$this->tab_comms			= lang('Communications');
		$this->tab_address			= lang('Address');
		$this->tab_others			= lang('Others');
		$this->tab_extra			= lang('More data');
		$this->nonavbar 			= phpgw::get_var('nonavbar','bool');
		$this->_set_sessiondata();
	}

	function _set_sessiondata()
	{
		$this->start    = $this->bo->start;
		$this->limit    = $this->bo->limit;
		$this->query    = $this->bo->query;
		$this->sort     = $this->bo->sort;
		$this->order    = $this->bo->order;
		$this->filter   = $this->bo->filter;
		$this->cat_id   = $this->bo->cat_id;
		$this->qfield   = $this->bo->qfield;

		if($this->debug) { $this->_debug_sqsof(); }
	}

	function _debug_sqsof()
	{
		$data = array
			(
			 'start'  => $this->start,
			 'limit'  => $this->limit,
			 'query'  => $this->query,
			 'sort'   => $this->sort,
			 'order'  => $this->order,
			 'filter' => $this->filter,
			 'cat_id' => $this->cat_id,
			 'qfield' => $this->qfield
			);
		echo '<br />UI:';
		_debug_array($data);
	}

	/* Called only by index(), just prior to page footer. */
	function save_sessiondata()
	{
		$data = array
			(
			 'start'  => $this->start,
			 'limit'  => $this->limit,
			 'query'  => $this->query,
			 'sort'   => $this->sort,
			 'order'  => $this->order,
			 'filter' => $this->filter,
			 'cat_id' => $this->cat_id,
			 'qfield' => $this->qfield
			);
		$this->bo->save_sessiondata($data);
	}

	function index()
	{
		if(phpgw::get_var('section'))
		{
			$this->section = phpgw::get_var('section');
		}
		else
		{
			$this->section = $this->tab_main_persons;
		}

		$tabs = array();
		$tabs[] = array(
			'label' => lang('persons'),
			'link'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->tab_main_persons, 'nonavbar' => $this->nonavbar))
		);
		$tabs[] = array(
			'label' => lang('Organizations'),
			'link'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->tab_main_organizations, 'nonavbar' => $this->nonavbar))
		);

		switch($this->section)
		{
			case 'Persons':
				$selected = 0;
				break;
			case 'Organizations':
				$selected = 1;
				break;
			default:
				$selected = 0;
		}

		$GLOBALS['phpgw']->template->set_var('tabs', $GLOBALS['phpgw']->common->create_tabs($tabs, $selected));

		switch ($this->filter)
		{
			case 'yours':
				$this->access = PHPGW_CONTACTS_MINE;
				break;
			case 'private':
				$this->access = PHPGW_CONTACTS_PRIVATE;
				break;
			default:
				$this->access = PHPGW_CONTACTS_ALL;
		}

		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		$comms_array = array();

		$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('addressbook'));
		$this->template->set_file(array('addressbook_list_t' => 'index.tpl'));
		$this->template->set_block('addressbook_list_t','addressbook_header','addressbook_header');
		$this->template->set_block('addressbook_list_t','column','column');
		$this->template->set_block('addressbook_list_t','row','row');
		$this->template->set_block('addressbook_list_t','addressbook_footer','addressbook_footer');

		$this->template->set_file(array('principal_tabs' => 'principal_tabs.tpl'));
		$this->template->set_block('principal_tabs','principal_tab','principal_tab');
		$this->template->set_block('principal_tabs', 'principal_button', 'principal_button');

		if(!$this->start)
		{
			$this->start = 0;
		}

		if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
				$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
		{
			$this->limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		else
		{
			$this->limit = 15;
		}

		if(!isset($this->cat_id) && isset($this->prefs['default_category']) )
		{
			$this->cat_id = $this->prefs['default_category'];
		}

		if($this->cat_id && $this->cat_id!=0)
		{
			$category_filter = $this->cat_id;
		}
		else
		{
			$category_filter = PHPGW_CONTACTS_CATEGORIES_ALL;
		}

		$columns_to_display = $this->bo->get_columns_to_display($this->section);

		if ($this->section == $this->tab_main_persons)
		{
			//Check if both the main column array and the comtype subarray
			//are empty
			if((!$columns_to_display || !is_array($columns_to_display))
					|| ( isset($columns_to_display['comm_types']) && count($columns_to_display['comm_types']) < 1 ) && count($columns_to_display) == 1 )
			{
				$noprefs=lang('Please set your preferences for this application');
				// FIXME: Default values here! this is bad but is something
				$columns_to_display = array(
						'per_first_name'  => 'per_first_name',
						'per_last_name' => 'per_last_name',
						'per_department'=>'department',
						'per_title'=> 'title',
						'addr_add1'=>'address1',
						'addr_city'=>'city',
						'org_name' => 'org_name'
						);
			}
			$this->edit_mode = 'edit_person'; 
			$this->view_mode = 'view_person';
			$count_function = 'get_count_persons';
			$get_data_function = 'get_persons';

			$this->template->set_var('add_url', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_person', 'nonavbar' => $this->nonavbar)));

			$search_obj=array('query' => $this->query,
					'search_obj' => array(
						array('person', 'Person Data'),
						array('org', 'Organization Data'),
						array('person_last', 'person - last name'),
						array('person_first', 'person - first name'),
						array('comms', 'Communications Data'), 
						array('location', 'Locations Data'), 
						array('other', 'Other Data'),
						//array('note', 'Note Data'),
						));
		}
		elseif ($this->section == $this->tab_main_organizations)
		{
			if ( !is_array($columns_to_display) || !count($columns_to_display) )
			{
				$noprefs = lang('Please set your preferences for this application');
				// FIXME: Default values here! this is bad but is something
				$columns_to_display = array
					(
					 'org_name'  => 'org_name'
					);
			}
			$this->edit_mode = 'edit_org';
			$this->view_mode = 'view_org'; 
			$count_function = 'get_count_orgs';
			$get_data_function = 'get_orgs';

			$this->template->set_var('add_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.add_org', 'nonavbar' => $this->nonavbar)));

			$search_obj=array('query' => $this->query,
					'search_obj' => array(
						array('org', 'Organization Data'),
						array('comms', 'Communications Data'), 
						array('location', 'Locations Data'), 
						array('other', 'Other Data'),
						//array('note', 'Note Data'),
						));
		}

		//$comms_array = isset($columns_to_display['comm_types']) ? $columns_to_display['comm_types'] : array();
		unset($columns_to_display['comm_types']);
		$fields = array_keys($columns_to_display);

		if(!in_array($this->order, $fields))
		{
			$this->order = (($this->section == $this->tab_main_organizations) 
					? 'org_name' 
					: 'last_name');
		}

		$fields_comms = array_keys($comms_array);
		$fields['owner']='owner';
		$fields['contact_id']='contact_id';

		$fields_search = $fields;
		$fields_search['comm_media'] = $fields_comms;

		$this->query = urldecode(addslashes($this->query));

		//$criteria = $this->bo->criteria_contacts($this->access, $category_filter, $fields_search, $this->query);
		$criteria = $this->bo->criteria_contacts($this->access, $category_filter, $this->qfield, $this->query, $fields_search);
		$total_all_persons = $this->bo->$count_function($criteria);
		$entries = $this->bo->$get_data_function($fields, $this->start, $this->limit, $this->order, $this->sort, '', $criteria);

		if(is_array($entries) && count($entries) > 0)
		{
			if(count($fields_comms) > 0)
			{
				$this->entries_comm = $this->bo->get_comm_contact_data(array_keys($entries), $fields_comms);
			}
		}
		else
		{
			$entries=array();
		}

		$total_records = $this->bo->total;
		$cols='';
		while ($column = @each($columns_to_display))
		{
			$showcol = $this->bo->display_name($column[0]);

			if (!$showcol) { $showcol = $column[1]; }
			$cols .= '<td>';
			$cols .= $GLOBALS['phpgw']->nextmatchs->show_sort_order($this->sort,
					$column[0],$this->order,"/index.php",$showcol,
					'&menuaction=addressbook.uiaddressbook.index&section='.$this->section.'&fcat_id='.$this->cat_id.'&nonavbar='.$this->nonavbar);
			$cols .= '</td>';
			$cols .= "\n";
		}
		//FIXME: NEeed to determine how are we going to handle comm types translations
		while ($column = @each($comms_array))
		{
			$showcol = $column[0]; 
			$cols .= '<td>';
			$cols .= $GLOBALS['phpgw']->nextmatchs->show_sort_order($this->sort,
					$column[0],$this->order,"/index.php",$showcol,
					'&menuaction=addressbook.uiaddressbook.index&section='.$this->section.'&fcat_id='.$this->cat_id .'&nonavbar='.$this->nonavbar);
			$cols .= '</td>';
			$cols .= "\n";
		}

		/* set basic vars and parse the header */
		//$this->template->set_var('principal_tab',$this->get_principal_tabs($this->section));
		$this->get_principal_tabs( array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->tab_main_persons, 'nonavbar' => $this->nonavbar),
								$this->get_class_css($this->tab_main_persons, $this->section), 
								$this->tab_main_persons);

		$this->get_principal_tabs( array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->tab_main_organizations, 'nonavbar' => $this->nonavbar),
								$this->get_class_css($this->tab_main_organizations, $this->section),
								$this->tab_main_organizations);

		/* global here so nextmatchs accepts our setting of $query and $filter */
		$GLOBALS['query']  = $this->query;
		$GLOBALS['filter'] = $this->filter;			

		//FIXME make show_tpl stop using theme values
		$search_filter = $this->nextmatchs->show_tpl(
				'/index.php', $this->start, $total_all_persons,
				array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->section, 'nonavbar' => $this->nonavbar),
				'90%', '',
				$search_obj,1,True,array('filter'=>$this->filter,'yours'=>1),$this->cat_id);
		$query = $filter = '';

		if($this->query)
		{
			$lang_showing = lang('%1 was found %2 times in %3',htmlentities('"'.$this->query.'"', ENT_QUOTES, 'UTF-8'), $total_all_persons, lang($this->section));
		}
		else
		{
			//$lang_showing = $GLOBALS['phpgw']->nextmatchs->show_hits($total_records,$this->start);
			$lang_showing = lang('%1 - %2 of %3 %4', 
					($total_records!=0)?$this->start+1:$this->start, 
					$this->start+$total_records,$total_all_persons,lang($this->section));
		}


		$this->template->set_var('principal_tabs_inc', $this->template->fp('out', 'principal_tab'));

		$this->template->set_var('lang_view',lang('View'));
		$this->template->set_var('lang_vcard',lang('VCard'));
		$this->template->set_var('lang_edit',lang('Edit'));
		$this->template->set_var('lang_owner',lang('Owner'));

		$this->template->set_var('searchreturn', $noprefs);
		$this->template->set_var('lang_showing',$lang_showing);
		$this->template->set_var('search_filter',$search_filter);
		$this->template->set_var('cats',lang('Category'));
		$this->template->set_var('cats_url',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->section, 'nonavbar' => $this->nonavbar)));
		/* $this->template->set_var('cats_link',$this->cat_option($this->cat_id)); */
		$this->template->set_var('lang_cats',lang('Select'));
		//			$this->template->set_var('lang_addressbook',lang('Address book'));
		$this->template->set_var('lang_add',lang('Add'));
		$this->template->set_var('lang_cat_cont',lang('Categorize'));
		$this->template->set_var('cat_cont_url', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicategorize_contacts.index', 'nonavbar' => $this->nonavbar)));

		$this->template->set_var('lang_addvcard',lang('AddVCard'));
		$this->template->set_var('vcard_url',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uivcard.in', 'nonavbar' => $this->nonavbar)));
		$this->template->set_var('lang_import',lang('Import Contacts'));
		$this->template->set_var('import_url',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiXport.import', 'nonavbar' => $this->nonavbar)));
		$this->template->set_var('lang_import_alt',lang('Alt. CSV Import'));
		$this->template->set_var('import_alt_url',$GLOBALS['phpgw']->link('/addressbook/csv_import.php'));
		$this->template->set_var('lang_export',lang('Export Contacts'));
		$this->template->set_var('export_url',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiXport.export', 'nonavbar' => $this->nonavbar)));

		$export_vars = array
		(
			'get_data_function'	=> $get_data_function,
			'fields'			=> $fields,
			'limit'				=> $this->limit,
			'start'				=> $this->start,
			'order'				=> $this->order ? $this->order : '',
			'sort'				=> $this->sort ? $this->sort : '',
			'criteria'			=> $criteria,
			'fields_comms'		=> $fields_comms,
			'category_filter'	=>$category_filter
		);
		$GLOBALS['phpgw']->session->appsession('export_vars','addressbook', $export_vars);

		$this->template->set_var('start', $this->start);
		$this->template->set_var('sort', $this->sort);
		$this->template->set_var('order', $this->order);
		$this->template->set_var('filter', $this->filter);
		$this->template->set_var('query', $this->query);
		$this->template->set_var('cat_id', $this->cat_id);

		$this->template->set_var('qfield', $this->qfield);
		$this->template->set_var('cols', $cols);

		$this->template->pparse('out','addressbook_header');

		/* Show the entries */
		/* each entry */

		$all_cols_to_display = array_merge($columns_to_display, $comms_array);

		$tr_class = 'row_off';

		foreach($entries as $entry)
		{
			$this->template->set_var('columns','');
			$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
			$this->template->set_var('row_tr_class',$tr_class);
			$myid    = $entry['contact_id'];
			$myowner = $entry['owner'];

			/* each entry column */
			foreach ( array_keys($all_cols_to_display) as $column )
			{
				$ref = $data = '';
				$coldata = isset($entry[$column]) ? $entry[$column] : '';
				// jecinc marker
				if( $column == 'org_name' ) 
				{
					if($get_data_function == 'get_persons' )
					{
						$org_data = $this->bo->get_orgs_person_data($myid);
						if ( is_array($org_data) && count($org_data) )
						{
							$ref = '<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.view_org',
																		'ab_id'=>$org_data[0]['my_org_id'], 'nonavbar' => $this->nonavbar)) . '">' ;
						}
						else
						{
							$ref = '';
						}
					}
					else
					{
						$ref = '<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.view_org',
																		'ab_id'=> $myid, 'nonavbar' => $this->nonavbar)) . '">' ;
					}
					$data = 	htmlspecialchars($coldata) . '</a>';
				}
				else if ( in_array($column, $fields_comms) )
				{
					$data = $this->get_comm_value($myid, $column[key]);
					$data = htmlentities($data, ENT_QUOTES, 'UTF-8');
					if(strpos($column[key], 'email'))
					{
						if ($GLOBALS['phpgw_info']['user']['apps']['email'])
						{
							$ref='<a href="'.$GLOBALS['phpgw']->link('/email/compose.php',array('to'=> urlencode($data)) . '" target="_new">');
						}
						else
						{
							$ref='<a href="mailto:'.$data.'">';
						}
						$data=$data . '</a>';
					}
					if($column[key]=='website')
					{
						if ( !empty($data) && (substr($data,0,7) != 'http://') ) { $data = 'http://' . $data; }
						$ref='<a href="'.$data.'" target="_new">';
						$data=$data.'</a>';
					}
				}
				else
				{
					$ref = ''; $data = htmlentities($coldata, ENT_QUOTES, 'UTF-8');
				}
				$this->template->set_var('col_data',$ref.$data);
				$this->template->parse('columns','column',True);
			}
			$this->template->set_var('row_view_link',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => "addressbook.uiaddressbook.{$this->view_mode}", 'ab_id' => $entry['contact_id'], 'nonavbar' => $this->nonavbar ) ) );
			$this->template->set_var('row_vcard_link',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uivcard.out', 'ab_id' => $entry['contact_id'], 'nonavbar' => $this->nonavbar ) ) );

			if($this->bo->check_edit($entry['contact_id'], $myowner))
			{
				$this->template->set_var('row_edit','<a href="' 
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "addressbook.uiaddressbook.{$this->edit_mode}", 'ab_id' => $entry['contact_id'], 'nonavbar' => $this->nonavbar) )
					. '">' . lang('Edit') . '</a>');
			}				
			else
			{
				$this->template->set_var('row_edit','&nbsp;');
			}

			$this->template->set_var('row_owner',$GLOBALS['phpgw']->accounts->id2name($myowner));
			$this->template->parse('rows','row',True);
			$this->template->pparse('out','row');
		}

		$this->template->pparse('out','addressbook_footer');
		$this->save_sessiondata();
		/* $GLOBALS['phpgw']->common->phpgw_footer(); */
	}

	/*************************************************************\
	 * Deprecated functions section                                *
	 \*************************************************************/

	function add()
	{
		$this->add_person();
	}

	function edit()
	{
		$this->edit_person();
	}

	function copy()
	{
		$contact_id = phpgw::get_var('ab_id');
		$new_contact_id = $this->bo->copy_contact($contact_id);
		$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.edit_person', 'ab_id' => $new_contact_id, 'nonavbar' => $this->nonavbar));
	}

	/*************************************************************\
	 * Person Functions Section                                    *
	 \*************************************************************/

	/**
	 * Add Person Enviromet, this controls all process to add a person
	 *
	 * @param 
	 * @return 
	 */
	function add_person()
	{
		//set some variables which will be used
		$this->mode = 'add';
		$this->section = $this->tab_main_persons;
		$this->form_action = array('menuaction' => 'addressbook.uiaddressbook.add_person', 'nonavbar' => $this->nonavbar);
		$this->form_index = array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->section, 'nonavbar' => $this->nonavbar);
		//get all vars which was send in post or get
		$this->get_vars();

		//check if is the first time that get into edit
		if($this->firsttime)
		{
			$this->entry = '';
			$this->clear_tab_session();
			$this->load_tabs('cache');
		}

		//validate if add/edit/delete functions would be run
		$this->managment_functions($this->action);

		//save the old tab data if they exists
		if(isset($this->entry['old_tab']))
		{
			$this->save_tab_session(stripslashes($this->entry['old_tab']), $this->entry);
		}

		//if not exist tab selected use as defaul person_data
		if(!$this->tab)
		{
			$this->tab = $this->tab_person_data;
		}

		//evaluate the submit action
		switch($this->submit)
		{
			case 'save':
				$fields = $this->get_all_entry();
				
				if(!$fields['tab_person_data']['per_first_name'] && !$fields['tab_person_data']['per_last_name'])
				{				
					$message ='Need at least First Name or Last Name';
					phpgwapi_cache::message_set($message, 'error');
					break;
				}

				$fields['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];

				$ab_id = $this->bo->add_person($fields);
				$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
						 'menuaction'	=> 'addressbook.uiaddressbook.view_person',
						 'ab_id'		=> $ab_id,
						 'referer'	=> $this->referer, 'nonavbar' => $this->nonavbar
						));
				$GLOBALS['phpgw']->common->phpgw_exit();
				break;
			case 'cancel':
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->form_index);
				break;
			case 'delete':
				break;
			case 'clear':
				$this->entry = '';
				$this->clear_tab_session();
				//$this->load_tabs();
				break;
		}

		//read the current tab information
		$this->entry = $this->read_tab_session($this->tab);

		//start to draw the add window
		// 			$GLOBALS['phpgw']->common->phpgw_header();
		// 			echo parse_navbar();

		//draw the tabs and detail form
		$this->entry['old_tab'] = $this->tab;
		$this->main_form($this->entry, $this->section, $this->tab);
	}

	/**
	 * Edit a Person entry
	 *
	 * @return 
	 */
	function edit_person()
	{
		//set some variables which will be used
		$this->mode = 'edit';
		$this->section = $this->tab_main_persons;
		$this->form_action = array('menuaction' => 'addressbook.uiaddressbook.edit_person', 'nonavbar' => $this->nonavbar);
		$this->form_index = array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->section, 'nonavbar' => $this->nonavbar);

		//get all vars which was send in post or get
		$this->get_vars();

		/* First, make sure they have permission to this entry */
		$this->owner = isset($this->entry['owner']) ? $this->entry['owner'] : $this->owner;
		if(!$this->bo->check_edit($this->contact_id, $this->owner))
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $this->form_index);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		//check if is the first time that get into edit
		if($this->firsttime)
		{
			$this->entry = array();
			$this->clear_tab_session();
			$this->load_tabs('db');
		}
		else
		{
			//save the old tab data
			$this->save_tab_session(stripslashes($this->entry['old_tab']), $this->entry);
		}

		//validate if add/edit/delete functions would be run
		$this->managment_functions($this->action);

		//if not exist tab selected use as defaul person_data
		if(!$this->tab)
		{
			$this->tab = $this->tab_person_data;
		}

		//evaluate the submit action
		switch($this->submit)
		{
			case 'save':
				$fields = $this->get_all_entry();

				$fields['owner'] = $this->owner;

				$this->bo->edit_person($this->contact_id, $fields);

				$GLOBALS['phpgw']->redirect_link('/index.php', 
				array
				(
					'menuaction'	=> 'addressbook.uiaddressbook.view_person',
					'ab_id'		=> $this->contact_id,
					'referer'	=> $this->referer,
					'nonavbar' => $this->nonavbar
				));
				break;
			case 'cancel':
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->form_index);
				break;
			case 'delete':
				$GLOBALS['phpgw']->redirect_link('/index.php',
				array
				(
					'menuaction'	=> 'addressbook.uiaddressbook.delete_person',
					'ab_id'		=> $this->contact_id,
					'nonavbar' => $this->nonavbar
				));
				//$this->delete_person($this->contact_id);
				break;
			case 'clear':
				$this->entry = '';
				$this->clear_tab_session();
				break;
		}

		//read the current tab information
		$this->entry = $this->read_tab_session($this->tab);



		//draw the tabs and detail form
		$this->entry['old_tab'] = $this->tab;
		$this->main_form($this->entry, $this->section, $this->tab);
	}

	/**
	 * Delete persons from db
	 *
	 * @param int $person_id the person to delete from the db
	 * @return nothing? wtf? it should be bool 
	 */
	function delete_person($person_id = 0)
	{
		if ( $person_id == 0 )
		{
			$person_id = phpgw::get_var('ab_id');
		}
		$this->delete_contact($person_id, $this->tab_main_persons);
	}

	/**
	 * Viwe all  persons data from db
	 *
	 * @param 
	 * @return 
	 */
	function view_person()
	{
		$person_id = phpgw::get_var('ab_id');
		$referer = phpgw::get_var('referer');
		$this->view_contact($person_id, $this->tab_main_persons, $referer);
	}

	/**
	 * Copy all data from a person to new person
	 *
	 * @param 
	 * @return 
	 */
	function copy_person()
	{
		$contact_id = phpgw::get_var('ab_id');
		$new_contact_id = $this->bo->copy_contact($contact_id);
		$GLOBALS['phpgw']->redirect_link('/index.php', array
				(
				 'menuaction'	=> 'addressbook.uiaddressbook.edit_person',
				 'ab_id'		=> $new_contact_id,
				 'nonavbar' => $this->nonavbar
				));
	}

	/*************************************************************\
	 * Organizations Functions Section                             *
	 \*************************************************************/

	/**
	 * Add Organization Enviromet, this controls all process to add orgs
	 *
	 * @param 
	 * @return 
	 */
	function add_org()
	{
		//set some variables which will be used
		$this->mode = 'add';
		$this->section = $this->tab_main_organizations;
		$this->form_action = array('menuaction' => 'addressbook.uiaddressbook.add_org', 'nonavbar' => $this->nonavbar);
		$this->form_index = array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => $this->section, 'nonavbar' => $this->nonavbar);
		//get all vars which was send in post or get
		$this->get_vars();

		//check if is the first time that get into edit
		if($this->firsttime)
		{
			$this->entry = '';
			$this->clear_tab_session();
			$this->load_tabs('cache');
		}

		//validate if add/edit/delete functions would be run
		$this->managment_functions($this->action);

		//save the old tab data if it exists
		if(isset($this->entry['old_tab']))
		{
			$this->save_tab_session(stripslashes($this->entry['old_tab']), $this->entry);
		}

		//if not exist tab selected use as defaul person_data
		if(!$this->tab)
		{
			$this->tab = $this->tab_org_data;
		}

		//evaluate the submit action
		switch($this->submit)
		{
			case 'save':
				$fields = $this->get_all_entry();

				$fields['tab_person_data']['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
				$ab_id = $this->bo->add_org($fields);
				$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
						 'menuaction'	=> 'addressbook.uiaddressbook.view_org',
						 'ab_id'		=> $ab_id,
						 'nonavbar' => $this->nonavbar
						));
				break;
			case 'cancel':
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->form_index);
				break;
			case 'delete':
				break;
			case 'clear':
				$this->entry = '';
				$this->clear_tab_session();
				break;
		}

		//read the current tab information
		$this->entry = $this->read_tab_session($this->tab);

		//start to draw the add window
		// 			$GLOBALS['phpgw']->common->phpgw_header();
		// 			echo parse_navbar();

		//draw the tabs and detail form
		$this->entry['old_tab'] = $this->tab;
		$this->main_form($this->entry, $this->section, $this->tab);
	}

	/**
	 * Edit Organization Enviromet, this controls all process to add orgs
	 *
	 * @param 
	 * @return 
	 */
	function edit_org()
	{
		//set some variables which will be used
		$this->mode = 'edit';
		$this->section = $this->tab_main_organizations;
		$this->form_action = array('menuaction'=>'addressbook.uiaddressbook.edit_org', 'nonavbar' => $this->nonavbar);
		$this->form_index = array('menuaction'=>'addressbook.uiaddressbook.index','section'=>$this->section, 'nonavbar' => $this->nonavbar);

		//get all vars which was send in post or get
		$this->get_vars();

		//check if is the first time that get into edit
		if($this->firsttime)
		{
			$this->entry = '';
			$this->clear_tab_session();
			$this->load_tabs('db');
		}

		//validate if add/edit/delete functions would be run
		$this->managment_functions($this->action);

		//save the old tab data
		$this->save_tab_session(stripslashes($this->entry['old_tab']), $this->entry);

		//if not exist tab selected use as defaul person_data
		if(!$this->tab)
		{
			$this->tab = $this->tab_org_data;
		}

		//evaluate the submit action
		switch($this->submit)
		{
			case 'save':
				$fields = $this->get_all_entry();

				$fields['owner'] = $this->owner;

				$this->bo->edit_org($this->contact_id, $fields);

				$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
						 'menuaction'	=> 'addressbook.uiaddressbook.view_org',
						 'ab_id'		=> $this->contact_id,
						 'nonavbar' => $this->nonavbar
						));
				break;
			case 'cancel':
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->form_index);
				break;
			case 'delete':
				$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
						 'menuaction'	=> 'addressbook.uiaddressbook.delete_org',
						 'ab_id'		=> $this->contact_id,
						 'nonavbar' => $this->nonavbar
						));
				break;
			case 'clear':
				$this->entry = '';
				$this->clear_tab_session();
				$this->load_tabs('db');
				break;
		}

		//read the current tab information
		$this->entry = $this->read_tab_session($this->tab);

		/* First, make sure they have permission to this entry */
		$this->owner = $this->entry['owner']?$this->entry['owner']:$this->owner;
		if(!$this->bo->check_edit($this->contact_id, $this->owner))
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $this->form_index);
		}

		//start to draw the add window
		// 			$GLOBALS['phpgw']->common->phpgw_header();
		// 			echo parse_navbar();

		//draw the tabs and detail form
		$this->entry['old_tab'] = $this->tab;
		$this->main_form($this->entry, $this->section, $this->tab);
		$GLOBALS['phpgw']->common->phpgw_exit();
	}		

	/**
	 * Delete Organization 
	 *
	 * @param 
	 * @return 
	 */
	function delete_org($org_id='')
	{
		if($org_id=='')
		{
			$org_id = phpgw::get_var('ab_id');
		}
		$this->delete_contact($org_id, $this->tab_main_organizations);
	}

	/**
	 * View Organization 
	 *
	 * @param 
	 * @return 
	 */
	function view_org()
	{
		$org_id = phpgw::get_var('ab_id');
		$referer = phpgw::get_var('referer');
		$this->view_contact($org_id, $this->tab_main_organizations, $referer);
	}

	/**
	 * Copy Organization 
	 *
	 * @param 
	 * @return 
	 */
	function copy_org()
	{
		$contact_id = phpgw::get_var('ab_id');
		$new_contact_id = $this->bo->copy_contact($contact_id);
		$GLOBALS['phpgw']->redirect_link('/index.php', array
				(
				 'menuaction'	=> 'addressbook.uiaddressbook.edit_org',
				 'ab_id'		=> $new_contact_id,
				 'nonavbar' => $this->nonavbar
				));
	}

	/*************************************************************\
	 * Drawing Windows Functions Section                             *
	 \*************************************************************/

	/**
	 * Start to draw the html screens
	 *
	 * @param 
	 * @return 
	 */
	function main_form($fields, $section, $form_tab)
	{
		$this->jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header()

		$GLOBALS['phpgw_info']['flags']['nonavbar'] = phpgw::get_var('nonavbar','bool');
		$GLOBALS['phpgw']->common->phpgw_header(true);
		$this->template->set_root(PHPGW_APP_TPL);

		//print 'fields:<br />';
		$js_submit = '';
		$delete = '';
		$fields['owner'] = isset($fields['owner']) ? $fields['owner'] : $this->owner;
		if ($this->bo->check_delete($this->contact_id))
		{
	//		$delete = '<input type="submit" name="delete" value="' . lang('Delete') . '">';
			$delete = '<input type="button" name="button3" onclick="submit_form(\'delete\')" value="' . lang('Delete') . '">';
		}

		$this->template->set_file(array('form' => 'form.tpl'));

		$this->template->set_file(array('tabs'	=> 'tab.tpl'));
		$this->template->set_block('tabs','tab','tab');
		$this->template->set_block('tabs', 'button', 'button');

		if ($this->mode == 'add')
		{
			$this->template->set_file(array('principal_tabs' => 'principal_tabs.tpl'));
			$this->template->set_block('principal_tabs','principal_tab','principal_tab');
			$this->template->set_block('principal_tabs', 'principal_button', 'principal_button');

			$this->get_principal_tabs( array('menuaction' => 'addressbook.uiaddressbook.add_person', 'nonavbar' => $this->nonavbar),
								$this->get_class_css($this->tab_main_persons, $this->section), 
								'principal_persons', $this->tab_main_persons);

			$this->get_principal_tabs( array('menuaction' => 'addressbook.uiaddressbook.add_org', 'nonavbar' => $this->nonavbar),
								$this->get_class_css($this->tab_main_organizations, $this->section),
								'principal_orgs', $this->tab_main_organizations);

			$this->template->set_var('principal_tabs_inc', $this->template->fp('out', 'principal_tab'));
		}
		else
		{
			$this->template->set_var('principal_tabs_inc', '');
		}

		$this->template->set_var('old_tab_name', 'entry[old_tab]');
		$this->template->set_var('old_tab', $fields['old_tab']);
		$this->template->set_var('referer', $this->referer);
		$this->template->set_var('ab_id', $this->contact_id);
		$this->template->set_var('owner', $this->owner);
		$this->template->set_var('record_name', $this->record_name);

		switch ($form_tab)
		{
			case $this->tab_orgs:
				$this->template->set_var('onsubjs1', 'entry[all_orgs][]');
				$this->template->set_var('onsubjs2', 'entry[my_orgs][]');
				break;
			case $this->tab_cats:
				$this->template->set_var('onsubjs1', 'entry[all_cats][]');
				$this->template->set_var('onsubjs2', 'entry[my_cats][]');
				break;
			case $this->tab_persons:
				$this->template->set_var('onsubjs1', 'entry[all_person][]');
				$this->template->set_var('onsubjs2', 'entry[my_person][]');
				break;
			default:
				$this->template->set_var('onsubjs1', '');
				$this->template->set_var('onsubjs2', '');
		}

		switch ($section) 
		{
			case $this->tab_main_organizations:
				if (!$form_tab)
				{
					$form_tab=$this->tab_org_data;
				}
				$this->template->set_var('action', $GLOBALS['phpgw']->link('/index.php',$this->form_action));
				$this->get_tabs('bname', $this->tab_org_data, $this->get_class_css($this->tab_org_data, $form_tab));
				$this->get_tabs('bname', $this->tab_persons, $this->get_class_css($this->tab_persons, $form_tab));
				$this->get_tabs('bname', $this->tab_cats, $this->get_class_css($this->tab_cats, $form_tab));				
				$this->get_tabs('bname', $this->tab_comms, $this->get_class_css($this->tab_comms, $form_tab));
				$this->get_tabs('bname', $this->tab_address, $this->get_class_css($this->tab_address, $form_tab));
				$this->get_tabs('bname', $this->tab_others, $this->get_class_css($this->tab_others, $form_tab));
				break;

			case $this->tab_main_persons:
			default:
				if (!$form_tab)
				{
					$form_tab = $this->tab_person_data;
				}

				$this->template->set_var('action', $GLOBALS['phpgw']->link('/index.php', $this->form_action));
				$this->get_tabs('bname', $this->tab_person_data, $this->get_class_css($this->tab_person_data, $form_tab));
				$this->get_tabs('bname', $this->tab_orgs, $this->get_class_css($this->tab_orgs, $form_tab));
				$this->get_tabs('bname', $this->tab_cats, $this->get_class_css($this->tab_cats, $form_tab));
				$this->get_tabs('bname', $this->tab_comms, $this->get_class_css($this->tab_comms, $form_tab));
				$this->get_tabs('bname', $this->tab_address, $this->get_class_css($this->tab_address, $form_tab));
				$this->get_tabs('bname', $this->tab_others, $this->get_class_css($this->tab_others, $form_tab));
				$this->get_tabs('bname', $this->tab_extra, $this->get_class_css($this->tab_extra, $form_tab));
				break;
		}

		$this->template->set_var('tab', $this->template->fp('out', 'tab'));
		$this->template->set_var('current_tab_body', $this->current_body($form_tab,$fields,$section));
		$this->template->set_var('control_buttons', $this->get_action_buttons($js_submit, $delete));

		$this->template->pparse('out', 'form');
	}

	/**
	 * Get the screen from the selected tab
	 *
	 * @param 
	 * @return 
	 */
	function current_body($form_section,$fields, $section)
	{
		switch ($form_section) 
		{
			case $this->tab_person_data:
				return $this->person_form($fields);
				break;
			case $this->tab_extra:
				return $this->person_extra($fields);
				break;
			case $this->tab_org_data:
				return $this->org_form($fields);
				break;
			case $this->tab_orgs:
				$my_orgs_name='entry[my_orgs][]';
				$all_orgs_name='entry[all_orgs][]';
				$defaul_orgs_name='entry[preferred_org]';

				$fields_to_search=array('contact_id', 'org_name');
				if ( isset($fields['my_orgs']) )
				{
					$this->get_orgs($fields_to_search, $fields['my_orgs']);
				}

				if ( !isset($fields['preferred_org']) )
				{
					$fields['preferred_org'] = 0;
				}

				return $this->many_actions_form($this->tab_orgs, $all_orgs_name, 
						$my_orgs_name, $defaul_orgs_name, 'all_orgs_data',
						'my_orgs_data', 'my_orgs_array', $fields['preferred_org'],
						$section, $fields);

				break;
			case $this->tab_cats:
				$my_cats_name='entry[my_cats][]';
				$all_cats_name='entry[all_cats][]';
				//$defaul_cats_name='entry[current_cats]';
				if ( !isset($fields['my_cats']) )
				{
					$fields['my_cats'] = array();
				}

				$fields_to_search = array('cat_id', 'cat_name');
				$this->get_cats($fields_to_search, $fields['my_cats']);

				return $this->many_actions_form($this->tab_cats, $all_cats_name, 
						$my_cats_name, '', 'all_cats_data', 
						'my_cats_data', 'my_cats_array', '',
						$section, $fields);
				break;
			case $this->tab_persons:
				$my_person_name='entry[my_person][]';
				$all_person_name='entry[all_person][]';
				//$defaul_person_name='entry[current_person]';

				$fields_to_search=array('contact_id', 'per_full_name');
				$this->get_persons($fields_to_search, isset($fields['my_person']) ? $fields['my_person'] : '');

				return $this->many_actions_form($this->tab_persons, $all_person_name, 
						$my_person_name, '', 'all_person_data',
						'my_person_data', 'my_person_array', '',
						$section, $fields);
				break;
			case $this->tab_comms:
				return $this->comm_form($fields);
				break;	
			case $this->tab_address:
				return $this->address_form($fields);
				break;
			case $this->tab_others:
				return $this->others_form($fields);
				//return $this->custom_fields_form($fields);
				break;
			default:
				return $this->person_form($fields);
		}
	}

	/**
	 * Draw the principal persons data tab window
	 *
	 * @param 
	 * @return 
	 */
	function person_form($fields)
	{
		$userformat =& $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		if($userformat != $this->bo->bday_internformat)
		{
			$fields['per_birthday'] = phpgwapi_datetime::convertDate($fields['per_birthday'], $this->bo->bday_internformat, $userformat);
		}
		$bday = $this->jscal->input('entry[per_birthday]',$fields['per_birthday']);

		if ($fields['ispublic']=='private') 
		{
			$access_check = '<input type="checkbox" name="entry[access]" checked>';
		}
		else
		{
			$access_check = '<input type="checkbox" name="entry[access]">';
		}

		$this->form_start();			
		$this->template->set_var('lang_general_data', lang('General Data'));
		$this->set_form_fields(array
				(
				 1	=> array('Prefix', 'entry[per_prefix]', $fields['per_prefix']),
				 2	=> array('First Name', 'entry[per_first_name]', $fields['per_first_name']),
				 3	=> array('Middle Name', 'entry[per_middle_name]', $fields['per_middle_name']),
				 4	=> array('Last Name', 'entry[per_last_name]', $fields['per_last_name']),
				 5	=> array('Title', 'entry[per_title]', $fields['per_title']),
				 6	=> array('Department', 'entry[per_department]', $fields['per_department']),
				 7	=> array('Email', 'entry[email]', isset($fields['email']) ? $fields['email'] : ''),
				 8	=> array('Phone', 'entry[wphone]', isset($fields['wphone']) ? $fields['wphone'] : ''),
				 9	=> array('Private', $access_check, 'special'),
				 10	=> array('Birthday', $bday, 'special')
				));

		return $this->template->fp('out', 'tab_body_general_data');
	}

	/**
	 * Draw the principal organizations data tab window
	 *
	 * @param 
	 * @return 
	 */
	function org_form($fields)
	{
		if (isset($fields['ispublic']) && $fields['ispublic']=='private')
		{
			$access_check = '<input type="checkbox" name="entry[access]" checked>';
		}
		else
		{
			$access_check = '<input type="checkbox" name="entry[access]">';
		}

		$this->form_start();

		$this->template->set_var('lang_general_data', lang('Organizations Data'));

		$this->set_form_fields(array(1 => array('Name', 'entry[org_name]', isset($fields['org_name']) ? $fields['org_name'] : ''),
					2 => array('Phone', 'entry[wphone]', isset($fields['wphone']) ? $fields['wphone'] : ''),
					3 => array('Private', $access_check, 'special'),
					4 => array('','','special')));
		return $this->template->fp('out', 'tab_body_general_data');
	}

	/**
	 * Draw the comunications media tab window
	 *
	 * @param 
	 * @return 
	 */
	function comm_form($fields)
	{
		$this->form_start();

		$this->template->set_var('lang_general_data', lang('Communication Data for') . ' ' . $this->record_name);

		$types_data = array
		(
			'data1' => array('type'  => 'data', 'field' => 'comm_description'),
			'text1' => array('type'  => 'text', 'name'  => 'entry[comm_data]', 'field' => 'comm_description', 'value' => 'comm_data'),
			'radio1'=> array('type'  => 'radio', 'name'  => 'entry[preferred]',	'field' => 'preferred',	'value' => 'comm_description')
		);

		foreach($this->comm_descr as $key => $value)
		{
			$this->array_data[] = array
			(
				'comm_description' => $value['comm_description'],
				'comm_data' => isset($fields['comm_data'][$value['comm_description']]) ? $fields['comm_data'][$value['comm_description']] : '',
				'preferred' => isset($fields['preferred']) && $fields['preferred'] == $value['comm_description'] ? 'Y' : 'N'
			);
		}

		$this->template->set_var('detail_fields', $this->get_detail_form('comm', array('Description', 'Value','Preferred'), 'array_data', $types_data, 'key_comm_id', False));

		return $this->template->fp('out', 'tab_body_general_data');
	}

	/**
	 * Draw the address tab window
	 *
	 * @param 
	 * @return 
	 */
	function address_form($fields)
	{
		$this->form_start();

		$addresstype='<select name="entry[tmp_data][addr][addr_type]">'
			. $this->get_addr_type($fields['tmp_data']['addr']['addr_type']) . '</select>';
		$addr_preferred = '<input type="hidden" name="entry[tmp_data][addr][addr_preferred]" value="'
			.$fields['tmp_data']['addr']['addr_preferred'].'">';


		if ( isset($fields['tmp_data']['addr']) 
			&& is_array($fields['tmp_data']['addr'])  
			&& isset($fields['tmp_data']['addr']['addr_add1']) )
		{
//			echo '<pre>' . print_r($fields['tmp_data']['addr'], true) . '</pre>';
			if ( isset($fields['addr_preferred']) && isset($this->array_data[$fields['addr_preferred']]) )
			{
				$this->array_data[$fields['addr_preferred']]['addr_preferred'] = 'Y';
			}
		}
		else
		{
			$fields['tmp_data']['addr'] = array
			(
				'addr_add1'			=> '',
				'addr_add2'			=> '',
				'addr_city'			=> '',
				'addr_state'		=> '',
				'addr_postal_code'	=> '',
				'addr_country'		=> '',
				'addr_type'			=> '',
				'addr_preferred'	=> false,
				'key_addr_id'		=> 0,
				'action'			=> ''
			);
		}

		$this->set_form_fields(array
		(
			 1 => array('Address 1', 'entry[tmp_data][addr][addr_add1]', $fields['tmp_data']['addr']['addr_add1']),
			 2 => array('Address 2', 'entry[tmp_data][addr][addr_add2]', $fields['tmp_data']['addr']['addr_add2']),
			 3 => array('City', 'entry[tmp_data][addr][addr_city]', $fields['tmp_data']['addr']['addr_city']),
			 4 => array('State', 'entry[tmp_data][addr][addr_state]', $fields['tmp_data']['addr']['addr_state']),
			 5 => array('Postal Code', 'entry[tmp_data][addr][addr_postal_code]', $fields['tmp_data']['addr']['addr_postal_code']),
			 6 => array('Country', 'entry[tmp_data][addr][addr_country]', $fields['tmp_data']['addr']['addr_country']),
			 7 => array('Type', $addresstype, 'special'),
			 8 => array('', $addr_preferred, 'special')
		));


		$key_addr_id_name = 'entry[tmp_data][addr][key_addr_id]';
		$key_addr_id = $fields['tmp_data']['addr']['key_addr_id'];


		$this->template->set_var('lang_general_data', lang('Address Data for').' '.$this->record_name);

		$this->template->set_var('current_id_name', $key_addr_id_name);
		$this->template->set_var('current_id', $key_addr_id);
		$this->template->set_var('current_action_name', 'entry[tmp_data][addr][action]');
		$this->template->set_var('current_action', $fields['tmp_data']['addr']['action']);

		$types_data = array
		(
			'data1' => array('type'  => 'data',	'field' => 'addr_description'),
			'data2' => array('type'  => 'data', 'field' => 'addr_add1'),
			'radio1'=> array('type'  => 'radio', 'name'  => 'entry[addr_preferred]', 'field' => 'addr_preferred', 'value' => 'key_addr_id'),
			'link1' => array('type'  => 'link', 'mode'  => 'edit', 'key'   => 'key_addr_id', 'action'=> 'addr_edit_row', 'extra' => array('owner' => $this->owner, 'ab_id' => $this->contact_id, 'record_name' => $this->record_name) ),
			'link2' => array('type'  => 'link', 'mode'  => 'delete', 'key'   => 'key_addr_id', 'action'=> 'addr_del_row', 'extra' => array('owner' => $this->owner, 'ab_id' => $this->contact_id, 'record_name' => $this->record_name))
		);
//		$this->array_data = $this->read_tab_session('addr_data');
		//var_export($fields);
		//var_export($this->array_data);

//		$this->template->set_var('detail_fields', 
//			$this->get_detail_form('address', array('Type', 'Address', 'Preferred', 'Edit','Delete'), 'array_data', $types_data, 'key_addr_id'));
		return $this->template->fp('out', 'tab_body_general_data');
	}

	/**
	 * This function draw the others tab screen
	 *
	 * @param array $fields Values to be shown on this "screen"
	 * @return string The html required for the "others screen"
	 */
	function others_form($fields)
	{
		if ( !isset($fields['tmp_data']['others'])
			|| !is_array($fields['tmp_data']['others']) )
		{
			$fields['tmp_data']['others'] = array
			(
				'other_name'	=> '',
				'other_value'	=> '',
				'key_other_id'	=> 0,
				'action'		=> ''
			);
		}
		//preparate vars to use, this are the html form objects
		$other_descr='<input type="text" name="'
			. 'entry[tmp_data][others][other_name]'
			. '" value="'
			. $fields['tmp_data']['others']['other_name'] . '">';
		$other_value='<input type="text" name="'
			.'entry[tmp_data][others][other_value]'
			.'" value="'
			. $fields['tmp_data']['others']['other_value'] . '">';

		$key_other_id_name = 'entry[tmp_data][others][key_other_id]';
		$key_other_id = $fields['tmp_data']['others']['key_other_id'];

		//start the form
		$this->form_start();

		//set values
		$this->template->set_var('lang_general_data', lang('Other Data for').' '.$this->record_name);

		$this->template->set_var('current_id_name', $key_other_id_name);
		$this->template->set_var('current_id', $key_other_id);
		$this->template->set_var('current_action_name', 'entry[tmp_data][others][action]');
		$this->template->set_var('current_action', $fields['tmp_data']['others']['action']);

		//send to draw the html objects
		$this->set_form_fields(array(1 => array('Description', $other_descr, 'special'),
					2 => array('Value', $other_value, 'special')));

		//specified what html objects and data you want in the detail
		$types_data = array('data1' => array
		(
			'type'	=> 'data',
			'field'	=> 'other_name'),
			'text1'	=> array('type' => 'text', 'name' => 'entry[other_value]', 'field' => 'key_other_id', 'value' => 'other_value'),
			'link1'	=> array('type' => 'link', 'mode' => 'delete', 'key' => 'key_other_id', 'action' => 'other_del_row', 'extra' => array('owner' => $this->owner, 'ab_id' => $this->contact_id, 'record_name' => $this->record_name) )
		);
		//$this->array_data = $fields['others'];			
		$this->array_data = $this->read_tab_session('others_data');
		$custom_fields = $this->get_custom_fields();

		foreach($custom_fields as $data)
		{
			$this->entry['tmp_data']['others']['other_name'] = $data['title'];
			$this->entry['tmp_data']['others']['other_value'] = '';
			$this->entry['tmp_data']['others']['key_other_id'] = '';
			$this->entry['tmp_data']['others']['other_owner'] = $GLOBALS['phpgw_info']['server']['addressmaster'];
			$this->add_general('others');
		}

		$this->array_data = $this->read_tab_session('others_data');

		if ( isset($fields['other_value']) && is_array($fields['other_value']) )
		{
			foreach($fields['other_value'] as $key => $data)
			{
				$this->array_data[$key]['other_value'] = $data;
			}
		}

		//draw the detail form
		//$this->template->set_var('custom_fields', $this->custom_fields_form($fields));
		$this->template->set_var('detail_fields', $this->get_detail_form('other', 
					array('Description', 'Value','Delete'),
					'array_data', $types_data, 'key_other_id'));

		return $this->template->fp('out', 'tab_body_general_data');			
	}

	function get_custom_fields()
	{
		if($this->section == $this->tab_main_persons)
		{
			$custom_fields = $this->custom_fields->read_custom_fields(0,0,'','','person');
		}
		elseif($this->section == $this->tab_main_organizations)
		{
			$custom_fields = $this->custom_fields->read_custom_fields(0,0,'','','org');
		}
		return $custom_fields;
	}

	/**
	 * This function draw the extra person data tab screen
	 *
	 * @param array $fields The array with all data for show 
	 * in this screen
	 * @return mixed The extra tab screen whit all data
	 */
	function person_extra($fields)
	{
		$sound='<input type="text" name="entry[per_sound]" value="'
			. $fields['per_sound'] .'">';
		$pubkey='<input type="textarea" name="entry[per_pubkey]" value="'
			. $fields['per_pubkey'] . '">';

		$this->form_start();
		$this->template->set_var('lang_general_data', lang('Person extra fields for').' '.$this->record_name);

		$this->set_form_fields(array(1 => array('Suffix', 'entry[per_suffix]', $fields['per_suffix']),
					2 => array('Initials', 'entry[per_initials]', $fields['per_initials']),
					3 => array('Sound', $sound, 'special'),
					4 => array('Public Key', $pubkey, 'special')));


		return $this->template->fp('out', 'tab_body_general_data');
	}


	/**
	 * This function draw the tab screen what is used for categories,
	 * Persons from an Organization and Organizations from a Person
	 *
	 * @param 
	 * @return
	 */
	function many_actions_form($option, $all_data_name, $my_data_name, $defaul_data_name, $all_opts, 
			$my_opts, $my_opts_array, $selected, $section, $fields)
	{			
		if($section == $this->tab_main_persons)
		{
			$owner_title = 'Person';
			$owner_value = $this->record_name;
		}

		if($section == $this->tab_main_organizations)
		{
			$owner_title = 'Org';
			$owner_value = $this->record_name;

		}

		$all_data = '<select multiple size="5" name="' 
			. $all_data_name
			. '" style="width:220">'
			. $this->$all_opts . '</select>';

		$my_data = '<select multiple size="5" name="'
			. $my_data_name
			. '" style="width:220">'
			. $this->$my_opts . '</select>';

		$defaul_data_cbo = '<select name="'
			. $defaul_data_name
			. '" style="width:150">'
			. $this->get_my_option_selected($my_opts_array, $selected) . '</select>';

		$this->template->set_file(array('person_action'	=> 'many_actions.tpl'));
		$this->template->set_block('person_action', 'many_actions', 'many_actions');

		if ($option==$this->tab_orgs)
		{
			$option_label = 'Organizations';
			$this->template->set_var('lang_defaul', lang('Default ' . $option_label) . ':');
			$this->template->set_var('options', $defaul_data_cbo);
		}
		elseif($option==$this->tab_persons)
		{
			$option_label = 'Persons';
		}
		elseif($option==$this->tab_cats)
		{
			$option_label = 'Categories';
		}


		$this->template->set_var('lang_general_data', lang($option_label.' Data for').' '.$this->record_name);
		$this->template->set_var('lang_title_left', lang('All ' . $option_label));
		$this->template->set_var('person', $owner_value);

		$this->template->set_var('lang_person', lang($owner_title). ' ');
		$this->template->set_var('lang_title_rigth', lang('Current ' . $option_label));

		$this->template->set_var('options_left', $all_data);
		$this->template->set_var('options_rigth', $my_data);

		$this->template->set_var('my_opt', $my_data_name);
		$this->template->set_var('all_opt', $all_data_name);
		$this->template->set_var('current_opt', $defaul_data_name);

		return $this->template->fp('out', 'many_actions');
	}

	/**
	 * Draw the detail form, this form is as show in comms, addr and othes windows
	 *	     
	 * @param string $tab The name which identify the section
	 * @param array $headers Array with all headers which you want to show in the form
	 * @param string $array_name The array name from you want to show data
	 * @param array $objs_data Array with all properties of all data which you want to show
	 * @param string $idx The index name (for edit mode use)
	 * @param boolean $button Flag for indicate if you want draw the Add button
	 * @return string All the detail form html code in the template
	 */
	function get_detail_form($tab, $headers, $array_name, $objs_data, $idx, $button = true)
	{
		$this->template->set_file(array('detail_data'   => 'body_detail.tpl'));
		$this->template->set_block('detail_data','detail_body','detail_body');
		$this->template->set_block('detail_data','input_detail_row','input_detail_row');
		$this->template->set_block('detail_data','input_detail_data','input_detail_data');

		$add_button = '<input type="submit" name="'. $tab .'_add_row" value="'.lang('Add').'">';

		if($button)
		{
			//$this->template->set_var('caption_detail', $title);
			$this->template->set_var('add_button', $add_button);
		}
		else
		{
			$this->template->set_var('caption_detail', '');
			$this->template->set_var('add_button', '');
		}

		$this->template->set_var('row_class', 'th');
		$tr_class = 'th';

		$cols='';
		foreach($headers as $head)
		{
			$cols .= '<td>' . lang($head) . '</td>';
		}

		$this->template->set_var('input_cols', $cols);

		$this->template->fp('input_detail', 'input_detail_data', True);
		$this->template->fp('detail_body_set', 'input_detail_row');

		if (is_array($this->$array_name))
		{
			//wtf is going on here?
			foreach($this->$array_name as $k => $v)
			{
				$this->array_value = $v;
				
				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$this->template->set_var('row_class', $tr_class);

				$cols = '';			
				foreach($objs_data as $type => $properties)
				{
					$cols .= '<td>' . $this->get_column_data($properties) . '</td>';
				}

				$this->template->set_var('input_cols', $cols);

				$this->template->fp('input_detail', 'input_detail_data', True);
				$this->template->fp('detail_body_set', 'input_detail_row');
			}
		}

		return $this->template->fp('out', 'detail_body');
	}

	/**
	 * This function initialize the template for draw the tabs windows
	 *
	 * @param 
	 * @return
	 */
	function form_start()
	{
		$this->template->set_file(array('person_data'	=> 'current_tab_body.tpl'));
		$this->template->set_block('person_data','tab_body_general_data','general_data');
		$this->template->set_block('person_data','input_data','input_data');
		$this->template->set_block('person_data','input_data_col','input_data_col');
		$this->template->set_block('person_data','other_data','other_data');
		$this->template->set_block('person_data','input_other_data','input_other_data');
	}

	/**
	 * Get the principal tabs (Persons and Organizations)
	 *
	 * @param array $action link args usually array('menuaction' => 'app.class'method')
	 * @param string $class_css the css class/es to apply to the tab
	 * @param string $label the label for the tab
	 */
	function get_principal_tabs($action, $class_css, $value)
	{
		$tab = array
		(
			 'principal_action' 	=> $GLOBALS['phpgw']->link('/index.php', $action),
			 'principal_value'		=> lang($value),
			 'principal_tab_css'	=> ''
		);
		if ( strlen($class_css) )
		{
			$tab['principal_tab_css'] = "class=\"$class_css\"";
		}
		$this->template->set_var($tab);
		$this->template->parse('principal_buttons', 'principal_button', True);
	}

	/**
	 * Get the tabs
	 *
	 * @param 
	 * @return
	 */
	function get_tabs($tab_name, $tab_caption, $class_css)
	{
		$tab = array
		(
			'tab_name'		=> $tab_name,
			'tab_caption'	=> $tab_caption,
			'tab_css'		=> $class_css
		);
		$this->template->set_var($tab);
		$this->template->parse('buttons', 'button', True);
	}

	/**
	 * Get the correct css for the tab
	 *
	 * @param string $tab the tab to test
	 * @param string $current_tab the current section
	 * @return string the clasname
	 */
	function get_class_css($tab, $current_tab)
	{
		if ($tab == $current_tab)
		{
			return 'selected';
		}
		return '';
	}

	/**
	 * Strip slashes from all elements of array
	 *
	 * @todo FIXME this should not be done at the UI level, slashes are a db level thing, and should be stripped at that level! - skwashd 20060901
	 * @param array $data The array with all data what you want
	 * @return array The same array with stripslashes
	 */
	function stripslashes_from_array($data = array())
	{
		$record = array();
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				if(is_array($value))
				{
					$record[$key] = $this->stripslashes_from_array($value);
				}
				else
				{
					$record[$key] = stripslashes($value);
				}
			}
		}
		return $record;
	}

	function addslashes_from_array($data=array())
	{
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				if(is_array($value))
				{
					$record[$key] = $this->addslashes_from_array($value);
				}
				else
				{
					$record[$key] = addslashes($value);
				}
			}
		}
		return $record;
	}

	/*************************************************************\
	 * Management tabs sessions functions                          *
	 \*************************************************************/

	/**
	 * Get all the variables which are in post or get
	 *
	 * @param integer|Array $contact_id id of the contact or array of the same
	 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
	 * @return Array|string Array with records or string with sql query
	 */
	function get_vars()
	{
		if(phpgw::get_var('principal_persons'))
		{
			$this->submit = 'clear';
		}

		if(phpgw::get_var('principal_organizations'))
		{
			$this->submit = 'clear';
		}

		if(!phpgw::get_var('principal_organizations') && !phpgw::get_var('bname'))
		{
			$this->firsttime = true;
		}
		else
		{
			$this->firsttime = false;
		}

		if(!phpgw::get_var('principal_persons') && !phpgw::get_var('bname'))
		{
			$this->firsttime = true;
		}
		else
		{
			$this->firsttime = false;
		}

		//set submit action
		if(phpgw::get_var('submit'))
		{
			$this->submit = 'save';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('cancel'))
		{
			$this->submit = 'cancel';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('delete'))
		{
			$this->submit = 'delete';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('_submit') == 'submit')
		{
			$this->submit = 'save';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('_submit') == 'cancel')
		{
			$this->submit = 'cancel';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('_submit') == 'delete')
		{
			$this->submit = 'delete';
			$this->firsttime = false;
		}


		//set add/edit/delete action
		if(phpgw::get_var('address_add_row'))
		{
			$this->action = 'add_addr';
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('other_add_row'))
		{
			$this->action = 'add_other';
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('comm_edit_row') >= '0')
		{
			$this->action = 'edit_comm';
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('comm_del_row') >= '0')
		{
			$this->action = 'delete_comm';
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('addr_edit_row') >= '0')
		{
			$this->action = 'edit_addr';
			$this->addr_edit_row = phpgw::get_var('addr_edit_row');
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('addr_del_row') >= '0')
		{
			$this->action = 'delete_addr';
			$this->addr_del_row = phpgw::get_var('addr_del_row');
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('other_edit_row') >= '0')
		{
			$this->action = 'edit_other';
			$this->submit = 'none';
			$this->firsttime = false;
		}
		elseif(phpgw::get_var('other_del_row') >= '0')
		{
			$this->action = 'delete_other';
			$this->other_del_row = phpgw::get_var('other_del_row');
			$this->submit = 'none';
			$this->firsttime = false;
		}

		$_tab = phpgw::get_var('bname');
		$this->tab = stripslashes($_tab);
		$this->entry = phpgw::get_var('entry');

		$this->contact_id = phpgw::get_var('ab_id');
		$this->owner = phpgw::get_var('owner', 'int', 'REQUEST', $this->owner);
		$this->referer = phpgw::get_var('referer');
		$this->record_name = htmlentities(phpgw::get_var('record_name'), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Read the data of the correct tab in session variables
	 * or database.
	 *
	 * @param string $tab The tab name what you want to read
	 * @param boolean $load Flag for use if you want read from 
	 * session variable
	 * @return array The array with all data from the tab what 
	 * you specified
	 */
	function read_tab_session($tab)
	{
		$entry = array();
		$load_vars = $GLOBALS['phpgw']->session->appsession('load_vars', 'addressbook');
		switch ($tab)
		{
			case $this->tab_person_data:
				if($load_vars[$this->tab_person_data]=='db')
				{
					$entry = $this->bo->get_principal_persons_data($this->contact_id, False);
					$entry['ispublic'] = $entry['access'];
					$this->save_tab_session($this->tab_cats, $entry['tab_cats']);
					$this->save_tab_session($this->tab_extra, $entry['tab_extra']);
					unset($entry['tab_cats']);
					unset($entry['tab_extra']);
					$load_vars[$this->tab_person_data] = 'cache';
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('tab_person_data', 'addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				$comms = $this->read_tab_session($this->tab_comms);
				if ( is_array($comms) && count($comms) )
				{
					$entry['email'] = isset($comms['comm_data']['work email']) ? $comms['comm_data']['work email'] : '';
					$entry['wphone'] = isset($comms['comm_data']['work phone']) ?  $comms['comm_data']['work phone'] : '';
				}
				break;
			case $this->tab_orgs:
				if($load_vars[$this->tab_orgs]=='db')
				{
					$entry = $this->bo->get_orgs_person_data($this->contact_id);
					$old_my_orgs = $entry;
					$GLOBALS['phpgw']->session->appsession('old_my_orgs','addressbook',$old_my_orgs);
					$load_vars[$this->tab_orgs] = 'cache';
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('tab_orgs', 'addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				break;
			case $this->tab_cats:
				$entry = $GLOBALS['phpgw']->session->appsession('tab_cats', 'addressbook');
				$entry = $this->stripslashes_from_array($entry);
				break;
			case $this->tab_comms:
				if($load_vars[$this->tab_comms]=='db')
				{
					$data = $this->bo->get_comm_contact_data($this->contact_id,'',True);
					foreach($data as $key => $value)
					{						
						$entry['comm_data'][$value['comm_description']] = $value['comm_data'];
						if ( $value['comm_preferred'] == 'Y' )
						{
							$entry['preferred'] = $value['comm_description'];
						}
					}
					$GLOBALS['phpgw']->session->appsession('comm_data','addressbook',$data);
					if ( isset($entry['comm_data']) && is_array($entry['comm_data']) )
					{
						$GLOBALS['phpgw']->session->appsession('old_comm','addressbook', $entry['comm_data']);
					}
					$load_vars[$this->tab_comms] = 'cache';
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('tab_comms', 'addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				break;
			case $this->tab_address:
				$entry = $GLOBALS['phpgw']->session->appsession('tab_address', 'addressbook');
				$entry = $this->stripslashes_from_array($entry);
				break;
			case $this->tab_others:
				$entry = $GLOBALS['phpgw']->session->appsession('tab_others', 'addressbook');
				$entry = $this->stripslashes_from_array($entry);
				break;
			case $this->tab_extra:
				$entry = $GLOBALS['phpgw']->session->appsession('tab_extra', 'addressbook');
				$entry = $this->stripslashes_from_array($entry);
				break;
			case $this->tab_org_data:
				if($load_vars[$this->tab_org_data] == 'db')
				{
					$entry = $this->bo->get_principal_organizations_data($this->contact_id);
					$entry['ispublic'] = $entry['access'];
					$this->save_tab_session($this->tab_cats, $entry['tab_cats']);
					unset($entry['tab_cats']);
					$load_vars[$this->tab_org_data] = 'cache';
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('tab_org_data', 'addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				$comms = $this->read_tab_session($this->tab_comms);
				$entry['wphone'] = isset($comms['comm_data']['work phone']) ? $comms['comm_data']['work phone'] : '';
				//$this->record_name = $entry['org_name'];
				break;
			case $this->tab_persons:
				if($load_vars[$this->tab_persons] == 'db')
				{
					$entry = $this->bo->get_person_orgs_data($this->contact_id);
					$load_vars[$this->tab_persons] = 'cache';
					$old_my_person = $entry;
					$GLOBALS['phpgw']->session->appsession('old_my_person','addressbook',$old_my_person);
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('tab_persons', 'addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				break;
			case 'others_data':
				if($load_vars['others_data'] == 'db')
				{
					$data = $this->bo->get_others_contact_data($this->contact_id);
					if(!is_array($data))
					{
						$data = array();
					}
					foreach($data as $key => $value)
					{
						$entry[$value['key_other_id']] = $value;
					}
					$GLOBALS['phpgw']->session->appsession('old_others','addressbook',$entry);
					$this->save_tab_session('others_data', $entry);
					$load_vars['others_data'] = 'cache';
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('others_data','addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				//$this->save_tab_session('others_data', $entry);
				break;
			case 'addr_data':
				if($load_vars['addr_data'] == 'db')
				{
					$data = $this->bo->get_addr_contact_data($this->contact_id);
					if(!is_array($data))
					{
						$data = array();
					}
					foreach($data as $key => $value)
					{
						$entry[$value['key_addr_id']] = $value;
					}
					$this->save_tab_session('addr_data', $entry);
					$load_vars['addr_data'] = 'cache';
				}
				else
				{
					$entry = $GLOBALS['phpgw']->session->appsession('addr_data','addressbook');
					$entry = $this->stripslashes_from_array($entry);
				}
				break;
		}
		$GLOBALS['phpgw']->session->appsession('load_vars','addressbook', $load_vars);
		//$entry = $this->htmlentities_from_array($entry);
		return $entry;
	}

	/**
	 * Save the data of the correct tab
	 *
	 * @param string $tab The tab name what you want to save
	 * @param array $entry The tab data what you want to save
	 * @return mixed The data saved in correct session variable
	 */
	function save_tab_session($tab, $entry)
	{
		switch ($tab)
		{
			case $this->tab_person_data:
				$entry_name = 'tab_person_data';

				//get emain and phone info from comms var
				$comms = $this->read_tab_session($this->tab_comms);
				$comms['comm_data']['work email'] = $entry['email'];
				$comms['comm_data']['work phone'] = $entry['wphone'];
				$comms['preferred'] = isset($comms['preferred']) ? $comms['preferred'] : 'work email';

				$this->save_tab_session($this->tab_comms, $comms);
				unset($entry['email']);
				unset($entry['wphone']);

				//mini hack for save access check box
				if(isset($entry['access']))
				{
					$entry['ispublic'] = 'private';
					$entry['access'] = 'private';
				}
				else
				{
					$entry['ispublic'] = 'public';
					$entry['access'] = 'public';
				}

				$userformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				if($userformat != $this->bo->bday_internformat)
				{
					$entry['per_birthday'] = phpgwapi_datetime::convertDate($entry['per_birthday'], $userformat, $this->bo->bday_internformat);
				}

				$this->record_name = htmlentities(stripslashes($entry['per_first_name'].
							($entry['per_middle_name']?' '.$entry['per_middle_name']:'').
							($entry['per_last_name']?' '.$entry['per_last_name']:'')), ENT_QUOTES, 'UTF-8');
				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case $this->tab_orgs:
				$entry_name = 'tab_orgs';
				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case $this->tab_cats:
				$entry_name = 'tab_cats';
				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case $this->tab_comms:
				$entry_name = 'tab_comms';
				$entry['comm_data'] = $this->save_simple_array($entry['comm_data']);
				$entry_data = $entry;
				break;
			case $this->tab_address:
				$entry_name = 'tab_address';
				if( isset($entry['addr_preferred']) && $entry['addr_preferred'] != '')
				{
					$this->addr_data = $this->read_tab_session('addr_data');
					$this->addr_data[$entry['addr_preferred']]['addr_preferred'] = $this->set_addr_preferred('addr_data', 'Y');
					$this->save_tab_session('addr_data', $this->addr_data);
					unset($this->addr_data);
				}

				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case $this->tab_others:
				$entry_name = 'tab_others';
				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case $this->tab_extra:
				$entry_name = 'tab_extra';
				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case $this->tab_org_data:
				$entry_name = 'tab_org_data';

				//get emain and phone info from comms var
				$comms = $this->read_tab_session($this->tab_comms);
				$comms['comm_data']['work phone'] = $entry['wphone'];
				$comms['preferred'] = isset($comms['preferred']) ? $comms['preferred'] : 'work email';
				$this->save_tab_session($this->tab_comms, $comms);
				unset($entry['wphone']);

				//mini hack for save access check box
				if(isset($entry['access']))
				{
					$entry['ispublic'] = 'private';
					$entry['access'] = 'private';
				}
				else
				{
					$entry['ispublic'] = 'public';
					$entry['access'] = 'public';
				}
				//$entry_data = $this->save_simple_array($entry);
				$this->record_name = htmlentities($entry['org_name'], ENT_QUOTES, 'UTF-8');
				$entry_data = $entry;
				break;
			case $this->tab_persons:
				$entry_name = 'tab_persons';
				//$entry_data = $this->save_simple_array($entry);
				$entry_data = $entry;
				break;
			case 'others_data':
				$entry_name = 'others_data';
				$entry = $this->addslashes_from_array($entry);
				$entry_data = $entry;
				break;
			case 'addr_data':
				$entry_name = 'addr_data';
				$entry = $this->addslashes_from_array($entry);
				$entry_data = $entry;
				break;
		}
		$GLOBALS['phpgw']->session->appsession($entry_name,'addressbook', $entry_data);
	}

	/**
	 * Clear all session variables which have tabs data
	 *
	 * @param string $tab The tab name what you want to clear or nothing
	 * if you want clear all tabs
	 * @return mixed The variable what you specified clear or all tabs variables
	 */
	function clear_tab_session($tab = '')
	{
		$tabs_data = array
			(
			 'tab_person_data',
			 'tab_orgs',
			 'tab_cats',
			 'tab_comms',
			 'tab_address',
			 'tab_others',
			 'tab_extra',
			 'tab_org_data',
			 'tab_persons',
			 'others_data',
			 'addr_data',
			 'comm_data',
			 'load_vars',
			 'transactions',
			 'old_my_orgs',
			 'old_my_person',
			 'old_comm',
			 'old_others'
			);

		if($tab!='')
		{
			$GLOBALS['phpgw']->session->appsession($tab, 'addressbook', NULL);
		}
		else
		{
			foreach($tabs_data as $tab)
			{
				$GLOBALS['phpgw']->session->appsession($tab, 'addressbook', '');
			}
		}
	}

	/**
	 * Initialize the load_vars array with all tabs asigment the value
	 *
	 * @param string $from The value cache|db depending where you can read the 
	 * tab data, this is cache in add mode and db in edit mode.
	 * @return array The array with all tabs name and correct value for read data
	 */
	function load_tabs($from='cache')
	{
		$tabs_data = array($this->tab_person_data,
				$this->tab_orgs,
				$this->tab_cats,
				$this->tab_comms,
				$this->tab_address,
				$this->tab_others,
				$this->tab_extra,
				$this->tab_org_data,
				$this->tab_persons,

				'others_data',
				'addr_data');
		$load_vars = array();
		//	$load_vars = $GLOBALS['phpgw']->session->appsession('load_vars', 'addressbook');
		foreach($tabs_data as $tab)
		{
			$load_vars[$tab] = $from;
		}
		$GLOBALS['phpgw']->session->appsession('load_vars','addressbook', $load_vars);
	}

	/**
	 * Get all data from all tabs
	 *
	 * @param Array $data Array with all data what you want to save
	 * @return Array Array with all data which are not empty
	 */
	function get_all_entry()
	{
		$tabs_data = array('tab_person_data',
				'tab_orgs',
				'tab_cats',
				'tab_comms',
				'tab_address',
				'tab_others',
				'tab_extra',
				'tab_org_data',
				'tab_persons',

				'others_data',
				'addr_data',
				'comm_data',

				'transactions',

				'old_my_orgs',
				'old_my_person',
				'old_comm',
				'old_others');
		$fields = array();
		foreach($tabs_data as $tab)
		{
			$fields[$tab] = $GLOBALS['phpgw']->session->appsession($tab, 'addressbook');
			$record[$tab] = $this->stripslashes_from_array($fields[$tab]);
		}
		return $record;
	}

	/**
	 * Save in array all data if this are not empty
	 *
	 * @param Array $data Array with all data what you want to save
	 * @return Array Array with all data which are not empty
	 */
	function save_simple_array($data=array())
	{
		$new_data = array();
		foreach($data as $key => $value)
		{
			if($value!='')
			{
				$new_data[$key] = $value;
			}
		}
		return $new_data;
	}


	/*************************************************************\
	 * Management functions for catalogs                           *
	 \*************************************************************/

	/**
	 * Run management functions, this functions only are called from
	 * address and others tabs and are used for add/delete/update
	 * the records of this tabs
	 *
	 * @param string $action The action which will be executed
	 * @return mixed The correct action
	 */
	function managment_functions($action)
	{
		switch($action)
		{
			case 'add_addr':
				$this->add_address();
				break;
			case 'edit_addr':
				$this->edit_address();
				break;
			case 'delete_addr':
				$this->delete_address();
				break;
			case 'add_other':
				$this->add_other();
				break;
			case 'edit_other':
				$this->edit_other();
				break;
			case 'delete_other':
				$this->delete_other();
				break;
		}
	}

	/**
	 * Add and update an address in address array
	 *
	 * @param array $this->entry This array contain all data to insert in array
	 * @return mixed The record inserted in addr data array
	 */
	function add_address()
	{
		$this->tab = $this->tab_address;
		$this->entry['tmp_data']['addr']['addr_description'] = $this->bo->search_location_type_id(
				$this->entry['tmp_data']['addr']['addr_type']);
		$this->add_general('addr');
		$this->entry['old_tab'] = $this->tab_address;
	}

	/**
	 * Return data from the address what you want to edit to temp array which
	 * are display in html form, also remove this from the address array
	 *
	 * @param integer $this->addr_edit_row This is the address id what you want to edit
	 * @return mixed The $this->entry['tmp_data']['addr'] array with the data what you want to edit
	 */
	function edit_address()
	{
		$this->tab = $this->tab_address;
		//FIXME: if a record is in edit mode return this to general array, consider this data
		// are send for post and in this moment the form was send in get
		$addr_data = $GLOBALS['phpgw']->session->appsession('addr_data','addressbook');
		$this->entry['tmp_data']['addr'] = $addr_data[$this->addr_edit_row];
		unset($addr_data[$this->addr_edit_row]);
		if($this->entry['tmp_data']['addr']['addr_preferred']=='Y')
		{
			if(count($addr_data)>0)
			{
				$addr_data[key($addr_data)]['addr_preferred'] = 'Y';
			}
		}

		$GLOBALS['phpgw']->session->appsession('addr_data','addressbook', $addr_data);
		$this->entry['old_tab'] = $this->tab_address;
	}

	function delete_address()
	{
		$this->tab = $this->tab_address;
		$addr_data = $GLOBALS['phpgw']->session->appsession('addr_data','addressbook');
		$pref = $addr_data[$this->addr_del_row]['addr_preferred'];
		unset($addr_data[$this->addr_del_row]);

		if(count($addr_data)==0)
		{
			$GLOBALS['phpgw']->session->appsession('addr_data','addressbook', '');
		}
		else
		{
			if($pref=='Y')
			{
				reset($addr_data);
				$addr_data[key($addr_data)]['addr_preferred'] = 'Y';
			}

			$GLOBALS['phpgw']->session->appsession('addr_data','addressbook', $addr_data);
		}

		$this->entry['old_tab'] = $this->tab_address;

		if($this->mode == 'edit')
		{
			$key_action = $this->addr_del_row . 'delete' . 'addr';
			$trans = $this->bo->delete_specified_location($this->addr_del_row);
			$this->save_trans_actions($key_action, $trans);
		}
	}

	function add_other()
	{
		$this->tab = $this->tab_others;
		$this->add_general('others');
		$this->entry['old_tab'] = $this->tab_others;
	}

	function delete_other()
	{
		$this->tab = $this->tab_others;
		$others_data = $GLOBALS['phpgw']->session->appsession('others_data','addressbook');
		unset($others_data[$this->other_del_row]);

		if(count($others_data)==0)
		{
			$GLOBALS['phpgw']->session->appsession('others_data','addressbook', '');
		}
		else
		{
			$GLOBALS['phpgw']->session->appsession('others_data','addressbook', $others_data);
		}

		$this->entry['old_tab'] = $this->tab_others;

		if($this->mode == 'edit')
		{
			$key_action = $this->other_del_row . 'delete' . 'others';
			$trans = $this->bo->delete_specified_other($this->other_del_row);
			$this->save_trans_actions($key_action, $trans);
		}
	}

	/**
	 * Insert or update one element to correct array
	 *
	 * @param string $tab_section The section what you
	 * want to add elements, for example others, addr.
	 * @return mixed The elemet from entry in the array
	 */
	function add_general($tab_section)
	{
		if($tab_section=='others')
		{
			$key_tab_id = 'key_other_id';
			$tab_data = 'others_data';
		}
		elseif($tab_section=='addr')
		{
			$key_tab_id = 'key_addr_id';
			$tab_data = 'addr_data';
		}

		$this->entry_data = $this->read_tab_session($tab_data);
		if(is_array($this->entry_data))
		{
			ksort($this->entry_data);
			end($this->entry_data);
			$key_id = key($this->entry_data);
		}

		$add_data = $this->entry['tmp_data'][$tab_section];

		if(isset($add_data['action']) && $add_data['action'] == 'insert')
		{
			$get_query = 'get_insert_'.$tab_section;
			$add_data['action'] = 'insert';
			$key_id = $add_data[$key_tab_id];
		}
		else
		{
			if(!$add_data[$key_tab_id] || $add_data[$key_tab_id]=='')
			{
				$key_id = $key_id +1;
				$get_query = 'get_insert_'.$tab_section;
				$add_data['action'] = 'insert';
			}
			else
			{
				$key_id = $add_data[$key_tab_id];
				$get_query = 'get_update_'.$tab_section;
				$add_data['action'] = 'update';
			}
		}
		$add_data[$key_tab_id] = $key_id;
		$key_action = $add_data[$key_tab_id] . $add_data['action'] . $tab_section;

		if($tab_section=='others')
		{
			if ( !isset($add_data['other_owner']) || !$add_data['other_owner'] )
			{
				$add_data['other_owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			}
		}

		if($tab_section=='addr')
		{
			$add_data['addr_preferred'] = $this->set_addr_preferred('entry_data', $add_data['addr_preferred']);
		}
		//var_export($add_data);

		if($tab_section=='others')
		{
			$exist_name = False;
			if(!is_array($this->entry_data))
			{
				$this->entry_data = array();
			}

			foreach($this->entry_data as $key => $data)
			{
				if($data['other_name'] == $add_data['other_name'])
				{
					$exist_name = true;
				}
			}
			if($exist_name==False)
			{
				//$add_data['other_name'] = addslashes($add_data['other_name']);
				$this->entry_data[$key_id] = $add_data;
			}
		}
		else
		{
			$exist_name==False;
			$this->entry_data[$key_id] = $add_data;
		}

		//var_export($this->entry_data);
		$this->save_tab_session($tab_data, $this->entry_data);

		//if is edit mode make a query and save this in stock which will be executed in save
		if($this->mode == 'edit' && $exist_name==False && $tab_section!='others')
		{
			if($get_query == 'get_insert_'.$tab_section)
			{
				$trans =  $this->bo->$get_query($this->contact_id, 
						$this->entry_data[$key_id]);
			}
			elseif($get_query == 'get_update_'.$tab_section)
			{
				$trans =  $this->bo->$get_query($key_id, 
						$this->entry_data[$key_id]);
			}
			$this->save_trans_actions($key_action, $trans);
		}

		unset($this->entry);
		unset($add_data);
		unset($this->entry_data);
	}

	function set_addr_preferred($entry_data, $preferred)
	{
		if($preferred == 'Y')
		{
			//var_export($this->$entry_data);
			if(!is_array($this->$entry_data))
			{
				$this->$entry_data=array();
			}
			foreach($this->$entry_data as $key => $value)
			{
				$this->{$entry_data}[$key]['addr_preferred'] = 'N';
			}
			//var_export($this->$entry_data);
			return 'Y';
		}
		else
		{
			if(is_array($this->$entry_data))
			{
				foreach($this->$entry_data as $key => $value)
				{
					if($value['addr_preferred']=='Y')
					{
						return 'N';
					}
				}
			}
			return 'Y';
		}
	}


	/**
	 * Save the stock of queries for management functions
	 *
	 * @param string $key_action This is the identifier of the query,
	 * this key is composed for record_id-action-section
	 * @param string $trans This is the query string which you want to save
	 * @return array The array with all queries
	 */
	function save_trans_actions($key_action, $trans)
	{
		//$transactions = $GLOBALS['phpgw']->session->appsession('transactions','addressbook');
		$transactions[$key_action] = $trans;
		$GLOBALS['phpgw']->session->appsession('transactions','addressbook', $transactions);
	}



	function delete_contact($contact_id='', $contact_type='')
	{
		$confirm = phpgw::get_var('confirm');
		$contact_type = $contact_type?$contact_type:$this->bo->search_contact_type_id(
				$this->bo->get_type_contact($contact_id));

		if($contact_type == $this->tab_main_persons)
		{
			$type = 'person';
			$this->section = $this->tab_main_persons;
			$data = $this->bo->get_principal_persons_data($contact_id, False);
		}
		elseif($contact_type == $this->tab_main_organizations)
		{
			$type = 'org';
			$this->section = $this->tab_main_organizations;
			$data = $this->bo->get_principal_organizations_data($contact_id);
		}

		$owner = $data['owner'];

		if(!$this->bo->check_delete($contact_id, $owner))
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
					 'menuaction'	=> 'addressbook.uiaddressbook.index',
					 'section'	=> $contact_type,
					 'nonavbar' => $this->nonavbar
					));
		}
		//LEX: Calling the  delete_addressbook hook to make
		//shure we are allowed to delete this contacts by the 
		//other applications

		$response= $GLOBALS['phpgw']->hooks->process(array(
					'location' => 'delete_addressbook',
					'contact_id' => $contact_id )
				);

		if(!$this->bo->can_delete_hooks($response))
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			(count($this->bo->negative_responses) >1) ? $plur='s' : $plur='';
			$this->template->set_file(array('cant_delete_apps' =>
						'cannot_delete.tpl')
					);

			$this->template->set_var('lang_maynot_delete',
					lang("The following application(s) have
						requested for this contact to be 
						protected from deletion:")
					);
			$this->template->set_var('lang_application',lang('applications'));
			$this->template->set_var('lang_reason',lang('reason'));
			$this->template->set_var('lang_go_back',lang('Go back'));
			$this->template->set_var('link_go_back',
					$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.index','section'=>$contact_type, 'nonavbar' => $this->nonavbar)));
			$this->template->set_block('cant_delete_apps',
					'apps',
					'apps_l');
			foreach($this->bo->negative_responses as $appname => $reason)
			{
				$this->template->set_var('appname',$appname);
				$this->template->set_var('reason',$reason);
				$this->template->parse('apps_l','apps',True);
			}

			$this->template->pparse('out','cant_delete_apps');
			$GLOBALS['phpgw']->common->phpgw_exit();

			return;
		}
		$this->template->set_root(PHPGW_APP_TPL);
		$this->template->set_file(array('delete' => 'delete.tpl'));

		if ($confirm != 'true')
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
		//	echo parse_navbar();
			$this->template->set_var('lang_sure',lang('Are you sure you want to delete this entry ?'));
			$this->template->set_var('no_link',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.index','section'=>$contact_type, 'nonavbar' => $this->nonavbar)));
			$this->template->set_var('lang_no',lang('NO'));
			$this->template->set_var('yes_link',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.delete_' . $type,
															 							'ab_id'=> $contact_id,
															 							'confirm'=>'true',
															 							'nonavbar' => $this->nonavbar)));
			$this->template->set_var('lang_yes',lang('YES'));
			$this->template->pparse('out','delete');
		}
		else
		{
			$this->bo->delete($contact_id, $contact_type);
			$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
					 'menuaction'	=> 'addressbook.uiaddressbook.index',
					 'section'	=> $contact_type,
					 'nonavbar' => $this->nonavbar
					));
		}

	}

	function view_contact($contact_id='', $contact_type='', $referer='')
	{
		$contact_id = (empty($contact_id))? phpgw::get_var('ab_id') : $contact_id;
		$contact_type = $contact_type?$contact_type:$this->bo->search_contact_type_id(
				$this->bo->get_type_contact($contact_id));
		$referer = ($referer=='')?phpgw::get_var('referer'):$referer;
		$referer = urldecode($referer);

		if($contact_type == $this->tab_main_persons)
		{
			$type = 'person';
			$contacts = $this->bo->get_principal_persons_data($contact_id);
			$userformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if($userformat != $this->bo->bday_internformat)
			{
				$contacts['org_name'] = '<a href="' . $contacts['org_link'] . '">' 
					. htmlentities($contacts['org_name'], ENT_QUOTES, 'UTF-8') . '</a>';
				unset($contacts['org_link']);

				$cat_link_url = $GLOBALS['phpgw']->link('/index.php', 
						array
						(
						 'menuaction'	=> 'addressbook.uiaddressbook.index',
						 'section'	=> 'Persons',
						 'nonavbar' => $this->nonavbar
						)
						);

				$contacts['per_birthday'] = phpgwapi_datetime::convertDate($contacts['per_birthday'], $this->bo->bday_internformat, $userformat);
			}
		}
		else if($contact_type == $this->tab_main_organizations)
		{
			$cat_link_url = $GLOBALS['phpgw']->link('/index.php', 
					array
					(
					 'menuaction'	=> 'addressbook.uiaddressbook.index',
					 'section'	=> 'Organizations',
					 'nonavbar' => $this->nonavbar
					)
					);
			$type = 'org';
			$contacts = $this->bo->get_principal_organizations_data($contact_id);
		}

		if(isset($contacts['tab_extra']) && is_array($contacts['tab_extra']))
		{
			foreach($contacts['tab_extra'] as $key => $value)
			{
				$contacts[$key] =  $value;
			}
		}

		$owner=$contacts['owner'];
		$cats=$contacts['tab_cats']['my_cats'];
		$access=$contacts['access'];

		unset($contacts['tab_cats']);
		unset($contacts['tab_extra']);

		if(!$this->bo->check_read($contact_id, $owner))
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
					 'menuaction'	=> 'addressbook.uiaddressbook.index',
					 'section'	=> $contact_type,
					 'nonavbar' => $this->nonavbar
					));
		}

		$tmp_cats = array();
		foreach($cats as $key => $cat_id)
		{
			if($cat_id)
			{
				$catinfo = $this->cat->return_single(intval($cat_id));
				$tmp_cats[] = '<a href="' . $cat_link_url . '&fcat_id=' . $cat_id . '">'
					. $catinfo[0]['name'] . '</a>';
			}
		}
		$catname = implode(', ', $tmp_cats);
		unset($tmp_cats);

		unset($contacts['contact_id']);
		unset($contacts['cat_id']);
		unset($contacts['access']);
		unset($contacts['owner']);
		unset($contacts['email']);
		unset($contacts['wphone']);
		unset($contacts['load']);
		unset($contacts['my_cats']);

		$comms = $this->bo->get_comm_contact_data($contact_id);

		if ( isset($comms[$contact_id]) && is_array($comms[$contact_id]))
		{
			$contacts = $contacts + $comms[$contact_id];
			$comms_media[] = array_keys($comms[$contact_id]);
		}
		else
		{
			$comms_media[0] = array();
		}


		$addr = $this->bo->get_addr_contact_data($contact_id, array('addr_pref_val'=>'Y'));

		if ( isset($addr) && isset($addr[0]) && is_array($addr[0]) )
		{
			unset($addr[0]['contact_id']);
			unset($addr[0]['key_addr_id']);
			unset($addr[0]['addr_type']);
			unset($addr[0]['addr_preferred']);
			unset($addr[0]['addr_description']);
			unset($addr[0]['addr_contact_id']);

			$contacts = array_merge($contacts, $addr[0]);
		}

		$others = $this->bo->get_others_contact_data($contact_id);
		if(is_array($others))
		{
			foreach($others as $key => $other_data)
			{
				$contacts[$other_data['other_name']] = $other_data['other_value'];
			}
		}

		$this->template->set_root(PHPGW_APP_TPL);
		$this->template->set_file(array('view_t' => 'view.tpl'));
		$this->template->set_block('view_t','view_header','view_header');
		$this->template->set_block('view_t','view_row','view_row');
		$this->template->set_block('view_t','view_footer','view_footer');
		$this->template->set_block('view_t','view_buttons','view_buttons');

		$GLOBALS['phpgw']->common->phpgw_header(true);

		$this->template->set_var('lang_viewpref',lang('Address book - view'));

		$tr_class = '';
		$row_cnt = 0;
		foreach($contacts as $field => $data)
		{
			if ( $field != 'org_name' )
			{
				$data = htmlentities($data, ENT_QUOTES, 'UTF-8');
			}

			$ref='';
			if(!is_numeric($field) && $data!='')
			{
				if(in_array($field, $comms_media[0]))
				{
					$this->template->set_var('display_col',lang($field));
					if(strpos($field, 'email'))
					{
						if (isset($GLOBALS['phpgw_info']['user']['apps']['email']) && $GLOBALS['phpgw_info']['user']['apps']['email'])
						{
							$ref='<a href="'.$GLOBALS['phpgw']->link('/email/compose.php',array('to'=> urlencode($data))) .'" target="_new">';
						}
						else
						{
							$ref = '<a href="mailto:'.$data.'">';
						}
						$data .= '</a>';
					}
					if($field=='website')
					{
						if ( !empty($data) && (substr($data,0,7) != 'http://') ) 
						{ 
							$data = 'http://' . $data; 
						}
						$ref='<a href="'.$data.'" target="_new">';
						$data .= '</a>';
					}
				}
				else
				{
					$this->template->set_var('display_col',$this->bo->display_name($field));
				}

				$tr_class = $row_cnt % 2 ? 'row_off' : 'row_on';
				$this->template->set_var('tr_class', $tr_class);
				++$row_cnt;

				$this->template->set_var('ref_data',$ref.$data);
				$this->template->parse('cols','view_row',True);
			}
		}

		if($contact_type == $this->tab_main_organizations)
		{
			$persons = $this->bo->get_person_orgs_data($contact_id);
			if($persons && is_array($persons))
			{
				$per_link = $GLOBALS['phpgw']->link('/index.php', 
						array('menuaction'	=> 'addressbook.uiaddressbook.view_person', 'nonavbar' => $this->nonavbar));

				$ppl = array();

				foreach($persons as $per)
				{
					if($per['per_first_name'] || $per['per_last_name'])
					{
						$ppl[] = '<a href="'.$per_link.'&ab_id='.$per['my_person_id'].'">'
							. $per['per_first_name'] . ' ' . $per['per_last_name'] 
							. '</a>';
					}
				}
				$tr_class = $row_cnt % 2 ? 'row_off' : 'row_on';
				$this->template->set_var('tr_class', $tr_class);
				++$row_cnt;

				$this->template->set_var('display_col', lang('contacts'));
				$this->template->set_var('ref_data', implode('<br />', $ppl));

				$this->template->parse('cols', 'view_row', True);
			}
		}

		/* Following cleans up view_row, since we were only using it to fill {cols} */
		$this->template->set_var('view_row','');

		/* These are in the footer */
		if ( $row_cnt % 2 )
		{
			$this->template->set_var(array
					(
					 'access_class'	=> 'row_on',
					 'cat_class'		=> 'row_off',
					 'owner_class'	=> 'row_off'
					));
		}
		else
		{
			$this->template->set_var(array
					(
					 'access_class'	=> 'row_off',
					 'cat_class'		=> 'row_on',
					 'owner_class'	=> 'row_on'
					));
		}
		unset($row_cnt);

		$this->template->set_var('lang_owner',lang('Record owner'));
		if( isset($GLOBALS['phpgw_info']['server']['addressmaster']) 
				&& $owner == $GLOBALS['phpgw_info']['server']['addressmaster'])
		{
			$this->template->set_var('owner', 'addressmaster');
		}
		else
		{
			$this->template->set_var('owner',$GLOBALS['phpgw']->common->grab_owner_name($owner));
		}
		$this->template->set_var('lang_access',lang('Record access'));
		$this->template->set_var('access',$access);
		$this->template->set_var('lang_category',lang('Category'));
		$this->template->set_var('catname',$catname);
		if($this->bo->check_edit($contact_id))
		{				
			$this->template->set_var('edit_button',$this->html_1button_form('edit','Edit',
				$GLOBALS['phpgw']->link('/index.php', array('menuaction' => "addressbook.uiaddressbook.edit_{$type}", 'ab_id' => $contact_id, 'nonavbar' => $this->nonavbar) ) ) );
		}
		else
		{
			$this->template->set_var('edit_button', '');
		}

		$this->template->set_var('copy_button',$this->html_1button_form('submit','copy',
				$GLOBALS['phpgw']->link('/index.php', array('menuaction' => "addressbook.uiaddressbook.copy_{$type}", 'ab_id' => $contact_id, 'nonavbar' => $this->nonavbar) ) ) );

		if ($contacts['per_first_name'] && $contacts['per_last_name'])
		{
			$this->template->set_var('vcard_button',$this->html_1button_form('VCardForm','VCard',
				$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uivcard.out', 'ab_id' => $contact_id, 'nonavbar' => $this->nonavbar) ) ) );
		}
		else
		{
			$this->template->set_var('vcard_button',lang('no vcard'));
		}

		$this->template->set_var('done_button',$this->html_1button_form('DoneForm','Done',
			isset($referer) && $referer 
				? $referer 
				: $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.index', 'action' => $contact_type, 'nonavbar' => $this->nonavbar) ) ) );

		$this->template->pparse('out','view_t');

		if($contact_type == $this->tab_main_persons)
		{
			$GLOBALS['phpgw']->hooks->process(array
			(
				'location' => 'addressbook_view',
				'ab_id'    => $contact_id
			));
		}
	}

	function get_comm_value($contact_id, $column)
	{
		return $this->entries_comm[$contact_id][$column];
	}

	function html_1button_form($name,$lang,$link)
	{
		$html  = '<form method="POST" action="' . $link . '">' . "\n";
		$html .= '<button type="submit" name="' . $name .'" value="1">' . lang($lang) . "</button>\n";
		$html .= '</form>' . "\n";
		return $html;
	}

	function get_action_buttons($js_submit='', $delete)
	{
		$this->template->set_file(array('add' => 'add.tpl'));
		$this->template->set_var('lang_save',lang('Save'));
		$this->template->set_var('lang_cancel',lang('Cancel'));
		$this->template->set_var('lang_delete', $delete);
		$this->template->set_var('js_submit', $js_submit);
		return $this->template->parse('out','add');
	}

	function get_column_data( $properties = array() )
	{			
		switch($properties['type'])
		{
			case 'data':
				$column_data = htmlentities($this->array_value[$properties['field']], ENT_QUOTES, 'UTF-8');
				break;
			case 'text':
				$column_data = '<input type="text" '
					.'name="' . $properties['name'] . '[' . $this->array_value[$properties['field']] . ']" '
					.'value="' . $this->array_value[$properties['value']] . '">';
				break;
			case 'radio':
				$checked = $this->array_value[$properties['field']] == 'Y' ? 'checked' : '';
				$column_data = '<input type="radio" name="' . $properties['name'] 
					.'" value="' . htmlentities($this->array_value[$properties['value']], ENT_QUOTES, 'UTF-8') . '"'. $checked . '>';
				break;
			case 'link':
				$link = $GLOBALS['phpgw']->link('/index.php', $this->form_action + array($properties['action'] => $this->array_value[$properties['key']]) + $properties['extra']);
				$column_data = '<a href="'.$link.'">'.lang($properties['mode']).'</a>';
				break;
		}
		return $column_data;
	}

	function set_form_fields($form_fields)
	{
		$tr_class = 'row_off';

		ksort($form_fields, SORT_NUMERIC);

		foreach($form_fields as $key => $row)
		{
			$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
			$this->template->set_var('row_class', $tr_class);
			if($row[2] == 'special')
			{
				if(($key % 2) == 0)
				{
					$this->set_row_other_input($row[0],$row[1],2);
					$this->template->fp('input_other_fields_cols', 'other_data', True);
					$this->template->fp('other_fields', 'input_other_data');
				}
				else
				{
					$this->set_row_other_input($row[0],$row[1],1);
				}
			}
			else
			{
				if(($key % 2) == 0)
				{
					$this->set_row_input($row[0],$row[1],$row[2],2);
					$this->template->fp('input_fields_cols', 'input_data_col', True);
					$this->template->fp('input_fields', 'input_data');
				}
				else
				{
					$this->set_row_input($row[0],$row[1],$row[2],1);
				}
			}
		}
	}

	function set_row_input($field_name, $input_name, $input_value, $col)
	{
		if ($col==1)
		{
			$this->template->set_var('field_name_one', $field_name ? lang($field_name) : '');
			$this->template->set_var('input_name_one', $input_name);
			$this->template->set_var('input_value_one', $input_value);
		}
		else
		{
			$this->template->set_var('field_name_two',  $field_name ? lang($field_name) : '');
			$this->template->set_var('input_name_two', $input_name);
			$this->template->set_var('input_value_two', $input_value);
		}
	}

	function set_row_other_input($field_name, $field_value, $col)
	{
		if ($col==1)
		{
			$this->template->set_var('field_other_name1',  $field_name ? lang($field_name) : '');
			$this->template->set_var('value_other_name1', $field_value);
		}
		else
		{
			$this->template->set_var('field_other_name2',  $field_name ? lang($field_name) : '');
			$this->template->set_var('value_other_name2', $field_value);
		}
	}

	function get_persons($fields_to_search, $current_person)
	{
		$criteria = $this->bo->criteria_contacts(PHPGW_CONTACTS_ALL,PHPGW_CONTACTS_CATEGORIES_ALL,array(),'',$fields_to_search);
		$persons = $this->bo->get_persons($fields_to_search,'','','last_name','','',$criteria);

		if ($persons)
		{
			foreach ($persons as $k => $v)
			{
				if (is_array($current_person) && in_array($v['contact_id'], $current_person))
				{
					$this->my_person_data .= '<option value="' . $v['contact_id'] . '">'
						.$v['per_full_name'].'</option>';
					$this->my_person_array[$v['contact_id']] = $v['per_first_name'];
				}
				else
				{
					$this->all_person_data .= '<option value="' . $v['contact_id'] . '">'
						.$v['per_full_name'].'</option>';
				}
			}
		}
	}

	function get_orgs($fields_to_search, $current_orgs)
	{
		$criteria = $this->bo->criteria_contacts(PHPGW_CONTACTS_ALL,PHPGW_CONTACTS_CATEGORIES_ALL,array(),'',$fields_to_search);
		$orgs = $this->bo->get_orgs($fields_to_search,'','','org_name','','',$criteria);
		if ($orgs)
		{
			foreach ($orgs as $k => $v)
			{
				if (is_array($current_orgs) && in_array($v['contact_id'], $current_orgs))
				{
					$this->my_orgs_data .= '<option value="' . $v['contact_id'] . '">'
						.$v['org_name'].'</option>';
					$this->my_orgs_array[$v['contact_id']] = $v['org_name'];
				}
				else
				{
					$this->all_orgs_data .= '<option value="' . $v['contact_id'] . '">'
						.$v['org_name'].'</option>';
				}
			}
		}
	}

	function get_cats($fields_to_search, $current_cats)
	{
		$this->all_cats_data = '';
		$this->my_cats_data = '';
		$cats = $this->cat->return_array('all', 0, false, '', '', '', true);
		
		if ( is_array($cats) && count($cats) );
		{
			foreach ($cats as $k => $v)
			{
				if (is_array($current_cats) && in_array($v['id'], $current_cats))
				{
					$this->my_cats_data .= '<option value="' . $v['id'] . '">'
						.$v['name'].'</option>';
					$this->my_cats_array[$v['id']] = $v['name'];
				}
				else
				{
					$this->all_cats_data .= '<option value="' . $v['id'] . '">'
						.$v['name'].'</option>';
				}
			}		
		}
	}

	/**
	* Return some java_script
	*
	* @todo TODO switch to js class in the constructor for this
	*/
	function java_script()
	{
		$code = <<<JS
			<script type="text/javascript">
			function move(fboxname, tboxname, sboxname, cboxname)
			{
				var arrFbox = new Array();
				var arrTbox = new Array();
				var arrLookup = new Array();
				var i;

				fbox = document.body_form.elements[fboxname];
				tbox = document.body_form.elements[tboxname];

				for (i = 0; i < tbox.options.length; i++) 
				{
					arrLookup[tbox.options[i].text] = tbox.options[i].value;
					arrTbox[i] = tbox.options[i].text;
				}
				var fLength = 0;
				var tLength = arrTbox.length;
				for(i = 0; i < fbox.options.length; i++) 
				{
					arrLookup[fbox.options[i].text] = fbox.options[i].value;
					if (fbox.options[i].selected && fbox.options[i].value != "") 
					{
						arrTbox[tLength] = fbox.options[i].text;
						tLength++;
					}
					else 
					{
						arrFbox[fLength] = fbox.options[i].text;
						fLength++;
					}
				}
				arrFbox.sort();
				arrTbox.sort();
				fbox.length = 0;
				tbox.length = 0;

				var c;
				for(c = 0; c < arrFbox.length; c++) 
				{
					var no = new Option();
					no.value = arrLookup[arrFbox[c]];
					no.text = arrFbox[c];
					fbox[c] = no;
				}
				for(c = 0; c < arrTbox.length; c++) 
				{
					var no = new Option();
					no.value = arrLookup[arrTbox[c]];
					no.text = arrTbox[c];
					tbox[c] = no;
				}

				if(sboxname && cboxname)
				{
					move_cbo(sboxname, cboxname);
				}
			}

		function move_cbo(sboxname, cboxname)
		{
			sbox = document.body_form.elements[sboxname];
			cbox = document.body_form.elements[cboxname];
			if(sbox.length > 0)
			{
				sel_opt = sbox.options[sbox.selectedIndex].text;
			}
			else
			{
				sel_opt="";
			}
			sbox.length = 0;
			for(c = 0; c < cbox.length; c++) 
			{
				var no = new Option();
				no.value = cbox[c].value;
				no.text = cbox[c].text;
				if(no.text == sel_opt)
				{
					i = c;
				}
				sbox[c] = no;
			}
			if(i>0)
			{
				sbox.options[i].selected = true;
			}
		}

		function process_list(allboxname, myboxname)
		{
			if(myboxname)
			{
				mybox = document.body_form.elements[myboxname];
				for(c = 0; c < mybox.options.length; c++) 
				{
					mybox.options[c].selected = true;
				}
			}
		}

		function showHide(sDiv)
		{
			var oDiv = document.getElementById(sDiv);
			if (oDiv)
				oDiv.style.display = oDiv.style.display == "none" ? "" : "none";
		}
		</script>'
JS;
		return $code;
	}

	function get_comm_descr($comm_selected, $type='')
	{
		if(is_array($this->comm_descr))
		{
			foreach($this->comm_descr as $key => $value)
			{
				if ($value['comm_descr_id'] == $comm_selected)
				{
					$comm_descr .= '<option value="' . $value['comm_descr_id'] . '" selected>'
						. $value['comm_description'] . '</option>';
					$comm_description = $value['comm_description'];
				}
				else
				{
					$comm_descr .= '<option value="' . $value['comm_descr_id'] . '">'
						. $value['comm_description'] . '</option>';
				}
			}

			if ($type=='text')
			{
				return $comm_description;
			}
			else
			{
				return $comm_descr;
			}
		}
	}

	function get_addr_type($selected, $type='')
	{
		$addr_type = '';
		if(is_array($this->addr_type))
		{
			foreach($this->addr_type as $key => $value)
			{
				if ( $value['addr_type_id'] == $selected)
				{
					$addr_type .= '<option value="' . $value['addr_type_id'] . '" selected>'
						. $value['addr_description'] . '</option>';
					$addr_description = $value['addr_description'];
				}
				else
				{
					$addr_type .= '<option value="' . $value['addr_type_id'] . '">'
						. $value['addr_description'] . '</option>';
				}
			}

			if ($type=='text')
			{
				return $addr_description;
			}
		}
		return $addr_type;
	}

	function get_my_option_selected($my_opt_array, $selected)
	{
		$my_opt = '';
		if ( isset($this->$my_opt_array) 
			&& is_array($this->$my_opt_array) )
		{
			foreach($this->$my_opt_array as $key => $value)
			{
				if ($key == $selected)
				{
					$my_opt .= "<option value=\"{$key}\" selected>{$value}</option>";
				}
				else
				{
					$my_opt .= "<option value=\"{$key}\">{$value}</option>";
				}
			}
		}
		return $my_opt;
	}

	function add_email()
	{
		$name      = phpgw::get_var('name');
		$referer   = phpgw::get_var('referer');
		$email = phpgw::get_var('add_email');

		$name = urldecode($name);
		$email = urldecode($email);

		$contact_id = $this->bo->add_email($name, $email);

		$GLOBALS['phpgw']->redirect_link('/index.php', array
				(
				 'menuaction'	=> 'addressbook.uiaddressbook.view_person',
				 'ab_id'		=> $contact_id,
				 'referer'	=> $referer,
				 'nonavbar' => $this->nonavbar
				));
	}
}
?>
