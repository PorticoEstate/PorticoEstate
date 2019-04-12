<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package admin
	* @subpackage custom fields and functions
	 * @version $Id$
	*/
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class admin_ui_custom extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $local_menu_selection = false;
		var $menu_selection;
		var $public_functions = array
		(
			'delete'				 => true,
			'list_attribute'		 => true,
			'edit_attrib'			 => true,
			'list_custom_function'	 => true,
			'edit_custom_function'	 => true,
			'list_attribute_group'	 => true,
			'edit_attrib_group'		 => true,
			'query_attrib_group'	 => true,
			'delete_attrib_group'	 => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('admin.bo_custom', true);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->appname				= $this->bo->appname;
			$this->location				= $this->bo->location;
			$this->allrows				= $this->bo->allrows;

			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bolocation			= CreateObject('preferences.boadmin_acl');
			$this->bolocation->acl_app 	= $this->appname;

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.admin';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->appname);
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->appname);
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->appname);
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->appname);

			if($GLOBALS['phpgw_info']['flags']['menu_selection'] = phpgw::get_var('menu_selection'))
			{
				$this->menu_selection = $GLOBALS['phpgw_info']['flags']['menu_selection'];
			}
			else
			{

				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$this->appname}";
				$this->local_menu_selection = true;
			}
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'allrows'	=> $this->allrows,
				'appname'	=> $this->appname,
				'location'	=> $this->location
			);
			$this->bo->save_sessiondata($data);
		}

		function list_attribute()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access($this->appname);
			}

			$appname	= $this->appname;
			$location	= $this->location;
			$id			= phpgw::get_var('id', 'int');
			$resort		= phpgw::get_var('resort');

			if($this->local_menu_selection)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::list_atrribs';
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'custom',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib($id, $resort);
			}
			$attrib_list = $this->bo->get_attribs($appname, $location);

			if(is_array($attrib_list))
			{
				foreach($attrib_list as $entry)
				{
					$content[] = array
					(
						'name'					=> $entry['name'],
						'datatype'				=> $entry['datatype'],
						'column_name'			=> $entry['column_name'],
						'input_text'			=> $entry['input_text'],
						'group_id'			 => $entry['group_id'],
						'sorting'				=> $entry['attrib_sort'],
						'search'				=> $entry['search'],
						'link_up'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute',
							'resort' => 'up', 'appname' => $appname, 'location' => $location, 'id' => $entry['id'],
							'allrows' => $this->allrows, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'link_down'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute',
							'resort' => 'down', 'appname' => $appname, 'location' => $location, 'id' => $entry['id'],
							'allrows' => $this->allrows, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'link_edit'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.edit_attrib',
							'appname' => $appname, 'location' => $location, 'id' => $entry['id'], 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'link_delete'		 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.delete',
							'appname' => $appname, 'location' => $location, 'attrib_id' => $entry['id'],
							'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'lang_up_text'			=> lang('shift up'),
						'lang_down_text'		=> lang('shift down'),
						'lang_edit_text'		=> lang('edit the attrib'),
						'lang_delete_text'		=> lang('delete the attrib'),
						'text_attribute'		=> lang('Attributes'),
						'text_up'				=> lang('up'),
						'text_down'				=> lang('down'),
						'text_edit'				=> lang('edit'),
						'text_delete'			=> lang('delete')
					);
				}
			}

//_debug_array($content);

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_datatype'		=> lang('Datatype'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_group'	 => lang('group'),
				'sort_name'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.ui_custom.list_attribute',
														'appname'	=> $appname,
														'location'	=> $location,
														'allrows'	=> $this->allrows,
														'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
														 )
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.ui_custom.list_attribute',
														'appname'	=> $appname,
														'location'	=> $location,
														'allrows'	=> $this->allrows,
														'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
														)
										)),
				'lang_name'		=> lang('Name'),
			);


			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.edit_attrib',
					'appname' => $appname, 'location' => $location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_done'				=> lang('done'),
				'lang_done_attribtext'	=> lang('Return to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.ui_custom.list_attribute',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
				'appname'		=> $appname,
				'location'		=> $location,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			$data = array
			(
				'lang_appname'						=> lang('application'),
				'appname'							=> $appname,
				'allow_allrows'						=> True,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'start_record'						=> $this->start,
				'num_records'						=> count($attrib_list),
				'all_records'						=> $this->bo->total_records,
				'link_url'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path'						 => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_searchfield_attribtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_attrib'				=> $table_header,
				'values_attrib'					 => (isset($content) ? $content : ''),
				'table_add'							=> $table_add,
				'lang_no_location'					=> lang('No location'),
				'lang_location_statustext'			=> lang('Select submodule'),
				'select_name_location'				=> 'location',
				'location_list'				 => $this->bolocation->select_location('filter', $location, False, True),
			);

			$function_msg	= lang('list custom attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . lang('attribute') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list_attribute' => $data));
			$this->save_sessiondata();
		}

		function edit_attrib()
		{
			if(!$this->acl_add)
			{
				phpgw::no_access($this->appname);
			}

			$appname	= $this->appname;
			$location	= $this->location;
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST', array());

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if(isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']	 = $id;
					$action			 = 'edit';
				}

				$values['appname']	 = $appname;
				$values['location']	 = $location ? $location : $values['location'];

				if(!$values['location'])
				{
					$receipt['error'][] = array('msg' => lang('location not selected!'));
				}
				else
				{
					$location = $values['location'];
				}

				if(!$values['column_name'])
				{
					$receipt['error'][] = array('msg' => lang('Column name not entered!'));
				}

				if(!preg_match('/^[a-z0-9_]+$/i', $values['column_name']))
				{
					$receipt['error'][] = array('msg' => lang('Column name %1 contains illegal character', $values['column_name']));
				}

				if(!$values['input_text'])
				{
					$receipt['error'][] = array('msg' => lang('Input text not entered!'));
				}
				if(!$values['statustext'])
				{
					$receipt['error'][] = array('msg' => lang('Statustext not entered!'));
				}

				if(!$values['appname'])
				{
					$receipt['error'][] = array('msg' => lang('application not chosen!'));
				}

				if(!$values['column_info']['type'])
				{
					$receipt['error'][] = array('msg' => lang('Datatype type not chosen!'));
				}

				if(!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter precision as integer !'));
					unset($values['column_info']['precision']);
				}

				if($values['column_info']['scale'] && !ctype_digit($values['column_info']['scale']))
				{
					$receipt['error'][] = array('msg' => lang('Please enter scale as integer !'));
					unset($values['column_info']['scale']);
				}

				if(!$values['column_info']['nullable'])
				{
					$receipt['error'][] = array('msg' => lang('Nullable not chosen!'));
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib($values);

					if(!$id)
					{
						$id = $receipt['id'];
					}
				//	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.ui_custom.edit_attrib', 'appname' => $values['appname'], 'location' => $values['location'], 'id' => $id, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']));
				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				}
			}

			if($id)
			{
				$values			 = $this->bo->get_attrib_single($appname, $location, $id);
				$type_name		 = $values['type_name'];
				$function_msg	 = lang('edit attribute') . ' ' . lang($type_name);
			}
			else
			{
				$function_msg = lang('add attribute');
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.ui_custom.edit_attrib',
				'appname'		=> $appname,
				'location'		=> $values['location'],
				'id'			=> $id,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			$multiple_choice = false;
			$custom_get_list = false;
			$custom_get_single = false;
			switch ($values['column_info']['type'])
			{
				case 'R':
				case 'CH':
				case 'LB':
					$multiple_choice = true;
					break;
				case 'custom1':
					$custom_get_list = true;
					break;
				case 'custom2':
				case 'custom3':
					$custom_get_list = true;
					$custom_get_single = true;
					break;
				default:
			}

//_debug_array($values);

			$msgbox_data = (isset($receipt) ? $GLOBALS['phpgw']->common->msgbox_data($receipt) : '');

			$data = array
			(
				'appname'							=> $appname,
				'lang_choice'						=> lang('Choice'),
				'lang_new_value'					=> lang('New value'),
				'lang_new_value_statustext'			=> lang('New value for multiple choice'),
				'multiple_choice'					=> $multiple_choice,
				'value_table_filter'				=> $values['table_filter'],
				'value_choice'					 => (isset($values['choice']) ? $values['choice'] : ''),
				'custom_get_list' => $custom_get_list,
				'custom_get_single' => $custom_get_single,
				'lang_delete_value'					=> lang('Delete value'),
				'lang_value'						=> lang('value'),
				'lang_delete_choice_statustext'		=> lang('Delete this value from the list of multiple choice'),
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute',
					'appname' => $appname, 'location' => $location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,
				'value_column_name' => (isset($values['column_name']) ? $values['column_name'] : ''),
				'value_input_text' => (isset($values['input_text']) ? $values['input_text'] : ''),
				'lang_id_attribtext'				=> lang('Enter the attribute ID'),
				'lang_entity_statustext'			=> lang('Select a entity type'),
				'value_statustext' => (isset($values['statustext']) ? $values['statustext'] : ''),
				'lang_done_attribtext'				=> lang('Back to the list'),
				'lang_save_attribtext'				=> lang('Save the attribute'),
				'lang_datatype_statustext'			=> lang('Select a datatype'),
				'lang_no_datatype'					=> lang('No datatype'),
				'datatype_list'				 => $this->bo->select_datatype((isset($values['column_info']['type']) ? $values['column_info']['type'] : '')),
				'attrib_group_list'			 => array('options' => $this->bo->get_attrib_group_list($this->appname, $this->location, $values['group_id'])),
				'lang_precision'					=> lang('Precision'),
				'lang_precision_statustext'			=> lang('enter the record length'),
				'value_precision'			 => (isset($values['column_info']['precision']) ? $values['column_info']['precision'] : ''),
				'lang_scale'						=> lang('scale'),
				'lang_scale_statustext'				=> lang('enter the scale if type is decimal'),
				'value_scale'			 => (isset($values['column_info']['scale']) ? $values['column_info']['scale'] : ''),
				'lang_default'						=> lang('default'),
				'lang_default_statustext'			=> lang('enter the default value'),
				'value_default'				 => (isset($values['column_info']['default']) ? $values['column_info']['default'] : ''),
				'lang_nullable'						=> lang('Nullable'),
				'lang_nullable_statustext'			=> lang('Chose if this column is nullable'),
				'lang_select_nullable'				=> lang('Select nullable'),
				'nullable_list'					 => $this->bo->select_nullable((isset($values['column_info']['nullable']) ? $values['column_info']['nullable'] : '')),
				'value_lookup_form'				 => (isset($values['lookup_form']) ? $values['lookup_form'] : ''),
				'lang_lookup_form'					=> lang('show in lookup forms'),
				'lang_lookup_form_statustext'		=> lang('check to show this attribue in lookup forms'),
				'value_list'					 => (isset($values['list']) ? $values['list'] : ''),
				'lang_list'							=> lang('show in list'),
				'lang_list_statustext'				=> lang('check to show this attribute in entity list'),
				'value_search'					 => (isset($values['search']) ? $values['search'] : ''),
				'lang_include_search'				=> lang('Include in search'),
				'lang_include_search_statustext'	=> lang('check to show this attribute in location list'),
				'value_history'				 => (isset($values['history']) ? $values['history'] : ''),
				'lang_history'						=> lang('history'),
				'lang_history_statustext'			=> lang('Enable history for this attribute'),
				'lang_no_location'					=> lang('No location'),
				'lang_location_statustext'			=> lang('Select submodule'),
				'select_name_location'				=> 'values[location]',
				'location_list'						=> $this->bolocation->select_location('select', $location, false, true),
				'value_disabled'					=> isset($values['disabled']) ? $values['disabled'] : '',
				'lang_disabled'						=> lang('disabled'),
				'lang_disabled_statustext'			=> lang('This attribute turn up as disabled in the form'),
				'value_helpmsg'						=> isset($values['helpmsg']) ? $values['helpmsg'] : '',
				'lang_helpmsg'						=> lang('help message'),
				'lang_helpmsg_statustext'			=> lang('Enables help message for this attribute'),
				'value_location'					=> $location,
				'value_get_list_function' => $values['get_list_function'],
				'value_get_list_function_input' => print_r($values['get_list_function_input'], true),
				'value_get_single_function' => $values['get_single_function'],
				'value_get_single_function_input' => print_r($values['get_single_function_input'], true),
			);
//_debug_array($values);

			$appname	= lang($this->appname);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_attrib' => $data));
		}

		function list_custom_function()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access($this->appname);
			}

			if($this->local_menu_selection)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::list_functions';
			}

			$appname	= $this->appname;
			$location	= $this->location;
			$id			= phpgw::get_var('id', 'int');
			$resort		= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'custom',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_custom_function($id, $resort);
			}
			$custom_function_list	 = $this->bo->read_custom_function($appname, $location);
			$content = array();
			if(isset($custom_function_list) AND is_array($custom_function_list))
			{
				foreach($custom_function_list as $entry)
				{

					$content[] = array
					(
						'file_name'					=> $entry['file_name'],
						'descr'						=> $entry['descr'],
						'sorting'					=> $entry['sorting'],
						'active'				 => $entry['active'] ? 'X' : '',
						'client_side'			 => $entry['client_side'] ? 'X' : '',
						'pre_commit'			 => $entry['pre_commit'] ? 'X' : '',
						'ajax'					 => $entry['ajax'] ? 'X' : '',
						'link_up'				 => $GLOBALS['phpgw']->link('/index.php', array
														(
															'menuaction'		=> 'admin.ui_custom.list_custom_function',
															'resort'			=> 'up',
															'appname'			=> $appname,
															'location'			=> $location,
															'id'				=> $entry['id'],
															'allrows'			=> $this->allrows,
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
														)),
						'link_down'				 => $GLOBALS['phpgw']->link('/index.php', array
														(
															'menuaction'		=> 'admin.ui_custom.list_custom_function',
															'resort'			=> 'down',
															'appname'			=> $appname,
															'location'			=> $location,
															'id'				=> $entry['id'],
															'allrows'			=> $this->allrows,
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
														)),
						'link_edit'				 => $GLOBALS['phpgw']->link('/index.php', array
														(
															'menuaction'		=> 'admin.ui_custom.edit_custom_function',
															'appname'			=> $appname,
															'location'			=> $location,
															'id'				=> $entry['id'],
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
															)),
						'link_delete'			 => $GLOBALS['phpgw']->link('/index.php', array
														(
															'menuaction'		=> 'admin.ui_custom.delete',
															'appname'			=> $appname,
															'location'			=> $location,
							'custom_function_id' => $entry['id'],
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
														)),
						'lang_up_text'				=> lang('shift up'),
						'lang_down_text'			=> lang('shift down'),
						'lang_edit_text'			=> lang('edit the custom_function'),
						'lang_delete_text'			=> lang('delete the custom_function'),
						'text_custom_function'			=> lang('custom functions'),
						'text_up'					=> lang('up'),
						'text_down'					=> lang('down'),
						'text_edit'					=> lang('edit'),
						'text_delete'				=> lang('delete')
					);
				}
			}

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_active'		=> lang('Active'),
				'lang_pre_commit'	=> lang('pre commit'),
				'lang_client_side'	=> lang('client side'),
				'lang_ajax'			=> 'Ajax',
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
					'var'	 => 'file_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.ui_custom.list_custom_function',
														'appname'	=> $appname,
														'location'	=> $location,
														'allrows'	=> $this->allrows,
														'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
														)
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
					'var'	 => 'custom_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.ui_custom.list_custom_function',
														'appname'	=> $appname,
														'location'	=> $location,
														'allrows'	=> $this->allrows,
														'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
														)
										)),
				'lang_name'			=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'						=> lang('add'),
				'lang_add_custom_functiontext'	=> lang('add a custom_function'),
				'add_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.edit_custom_function',
					'appname' => $appname, 'location' => $location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_done'						=> lang('done'),
				'lang_done_custom_functiontext'	=> lang('Return to admin'),
				'done_action'					=> $GLOBALS['phpgw']->link('/admin/index.php'),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.ui_custom.list_custom_function',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
				'appname'		=> $appname,
				'location'		=> $location,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			$data = array
			(
				'lang_appname'								=> lang('application'),
				'appname'									=> $appname,
				'allow_allrows'								=> True,
				'allrows'									=> $this->allrows,
				'start_record'								=> $this->start,
				'record_limit'								=> $record_limit,
				'start_record'								=> $this->start,
				'num_records'								=> count($custom_function_list),
				'all_records'								=> $this->bo->total_records,
				'link_url'								 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path'								 => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_searchfield_custom_functiontext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_custom_functiontext'		=> lang('Submit the search string'),
				'query'										=> $this->query,
				'lang_search'								=> lang('search'),
				'table_header_custom_function'				=> $table_header,
				'values_custom_function'					=> $content,
				'table_add'									=> $table_add,
				'lang_no_location'							=> lang('No location'),
				'lang_location_statustext'					=> lang('Select submodule'),
				'select_name_location'						=> 'location',
				'location_list'								=> $this->bolocation->select_location('filter', $location, false, false, true),
			);

			$function_msg	= lang('list custom function');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . lang('custom function') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list_custom_function' => $data));
			$this->save_sessiondata();
		}

		function edit_custom_function()
		{
			if(!$this->acl_add)
			{
				phpgw::no_access($this->appname);
			}

			//		$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::list_functions';
			$appname	= $this->appname;
			$location	= $this->location;
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST');

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if(isset($values['save']) && $values['save'])
			{
				if(isset($id) && $id)
				{
					$values['id']	 = $id;
					$action			 = 'edit';
				}
				else
				{
					$action = 'add';
				}

				$values['appname']	 = $appname;
				$values['location']	 = $location;

				if(!$values['appname'])
				{
					$receipt['error'][] = array('msg' => lang('entity type not chosen!'));
				}

				if(!$values['custom_function_file'])
				{
					$receipt['error'][] = array('msg' => lang('custom function file not chosen!'));
				}

				if(!$values['location'] || !$values['appname'])
				{
				 	$receipt['error'][] = array('msg' => lang('location or appname is missing'));
				}

				if(!isset($receipt['error']) || !$receipt['error'])
				{
					$receipt = $this->bo->save_custom_function($values, $action);

					if(!$id)
					{
						$id = $receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Custom function has NOT been saved'));
				}
			}

			if($id)
			{
				$values			 = $this->bo->read_single_custom_function($appname, $location, $id);
				$type_name		 = $values['custom_function_file'];
				$function_msg	 = lang('edit custom function') . ': ' . $type_name;
				$action			 = 'edit';
			}
			else
			{
				$function_msg = lang('add custom function');
				$action			 = 'add';
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.ui_custom.edit_custom_function',
				'appname'		=> $appname,
				'location'		=> $location,
				'id'			=> $id,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);


//_debug_array($values);

			$msgbox_data = (isset($receipt) ? $GLOBALS['phpgw']->common->msgbox_data($receipt) : '');

			$data = array
			(
				'lang_appname'						=> lang('appname'),
				'appname'							=> $appname,
				'lang_location'						=> lang('location'),
				'location'							=> $location,
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php', array(
															'menuaction'		=> 'admin.ui_custom.list_custom_function',
															'appname'			=> $appname,
															'location'			=> $location,
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection'])
														),
				'lang_id'							=> lang('Custom function ID'),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,
				'lang_descr'						=> lang('descr'),
				'lang_descr_custom_functiontext'	=> lang('Enter a descr for the custom function'),
				'value_descr'						=> isset($values['descr']) ? $values['descr'] : '',
				'lang_done_custom_functiontext'		=> lang('Back to the list'),
				'lang_save_custom_functiontext'		=> lang('Save the custom function'),
				'lang_custom_function'				=> lang('custom function'),
				'lang_custom_function_statustext'	=> lang('Select a custom function'),
				'lang_no_custom_function'			=> lang('No custom function'),
				'custom_function_list'				=> $this->bo->select_custom_function(isset($values['custom_function_file']) ? $values['custom_function_file'] : '', $appname),
				'value_active'						=> isset($values['active']) ? $values['active'] : '',
				'value_client_side'					=> $values['client_side'],
				'value_ajax'						=> $values['ajax'],
				'value_pre_commit'					=> $values['pre_commit'],
				'lang_active'						=> lang('Active'),
				'lang_active_statustext'			=> lang('check to activate custom function'),
			);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . lang('custom function') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_custom_function' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				phpgw::no_access($this->appname);
			}

			$appname				= phpgw::get_var('appname');
			$location				= phpgw::get_var('location');
			$attrib_id				= phpgw::get_var('attrib_id', 'int');
			$custom_function_id		= phpgw::get_var('custom_function_id', 'int');

			$function = 'list_attribute';
			if($custom_function_id)
			{
				$function = 'list_custom_function';
			}

			$redirect_args = array
			(
				'menuaction'	=> "admin.ui_custom.{$function}",
				'location'		=> $location,
				'appname'		=> $appname,
				'attrib_id'		=> $attrib_id,
				//FIXME this hack won't be merged upstream
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			if(phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($location, $appname, $attrib_id, $custom_function_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $redirect_args);
			}

			if(phpgw::get_var('cancel', 'bool', 'POST'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $redirect_args);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_delete'));

			$link_data = array
			(
				'menuaction'			=> 'admin.ui_custom.delete',
				'location'				=> $location,
				'appname'				=> $appname,
				'attrib_id'				=> $attrib_id,
				'custom_function_id' 	=> $custom_function_id,
				//FIXME this hack won't be merged upstream
				'menu_selection'		=> $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			$data = array
			(
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Return to list'),
				'lang_no'					=> lang('no')
			);

			$appname	= lang('Custom fields');
			$function_msg	= lang('delete entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function delete_attrib_group()
		{
			$acl_delete	 = $this->acl->check($this->location, PHPGW_ACL_DELETE, $this->appname);

			if(!$acl_delete)
			{
				return "NO ACCESS";
			}

			$group_id			 = phpgw::get_var('group_id', 'int');
			$confirm			 = phpgw::get_var('confirm', 'bool', 'POST');

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete_attrib_group($this->appname, $this->location, $group_id);
				return lang("this record has been deleted");
			}
		}

		function query_attrib_group()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access($this->appname);
			}

			if($location = phpgwapi_cache::session_get('admin_custom', 'location'))
			{
				$this->location = $location;
				phpgwapi_cache::session_clear('admin_custom', 'location');
			}

			$id		 = phpgw::get_var('id', 'int');
			$resort	 = phpgw::get_var('resort');
			if($resort && $this->acl_edit)
			{
				phpgwapi_cache::session_set('admin_custom', 'location', $this->location);
				$this->bo->resort_attrib_group($id, $resort);
			}

			return $this->query(array
			(
				'method'	 => 'list_attribute_group',
				'location'	 => $this->location,
			));

		}

		function list_attribute_group()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access($this->appname);
			}
			phpgwapi_cache::session_set('admin_custom', 'location', phpgw::get_var('location', 'string'));

			$location_list = $this->bolocation->select_location('filter', $this->location, false, true);
			foreach($location_list as &$entry)
			{
				$entry['name'] = $entry['descr'];
			}
			array_unshift($location_list, array('id' => '', 'name' => lang('select')));

			$appname										 = $this->appname;
			$function_msg									 = lang('list entity attribute group');
			$GLOBALS['phpgw_info']['flags']['app_header']	 = $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array('type'	 => 'filter',
								'name'	 => 'location',
								'text'	 => lang('location'),
								'list'	 => $location_list,
							),
							array('type'	 => 'hidden',
								'id'	 => 'appname',
								'name'	 => 'appname',
								'value'	 => $this->appname,
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'admin.ui_custom.query_attrib_group',
						'appname'			 => $this->appname,
						'phpgw_return_as'	 => 'json'
					)),
					'allrows'		 => true,
					'new_item' => array('onclick' => 'onNew_group()'),/*self::link(array(
									'menuaction' => 'admin.ui_custom.edit_attrib_group',
									'appname'	 => $this->appname,
									'menu_selection'	 => $this->menu_selection)),*/
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'parent_id',
							'label'		 => lang('parent'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('Descr'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'group_sort',
							'label'		 => lang('sorting'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'up',
							'label'		 => lang('up'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'down',
							'label'		 => lang('down'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => false,
							'hidden'	 => true
						)
					)
				)
			);

			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
						'source' => 'id'
					),
					array
						(
						'name'	 => 'appname',
						'source' => 'appname'
					),
					array
						(
						'name'	 => 'location',
						'source' => 'location'
					)
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'group_id',
						'source' => 'id'
					),
					array
						(
						'name'	 => 'appname',
						'source' => 'appname'
					),
					array
						(
						'name'	 => 'location',
						'source' => 'location'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'edit',
				'statustext' => lang('Edit'),
				'text'		 => lang('Edit'),
				'action'	 => $GLOBALS['phpgw']->link
				(
				'/index.php', array
					(
					'menuaction'	 => 'admin.ui_custom.edit_attrib_group',
					'menu_selection'	 => $this->menu_selection
				)
				),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'		 => 'delete',
				'statustext'	 => lang('Delete'),
				'text'			 => lang('Delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link
				(
				'/index.php', array
					(
					'menuaction'	 => 'admin.ui_custom.delete_attrib_group',
					'menu_selection'	 => $this->menu_selection
				)
				),
				'parameters'	 => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'list_attribute',
				'statustext' => lang('list attribute'),
				'text'		 => lang('list attribute'),
				'action'	 => $GLOBALS['phpgw']->link
				(
				'/index.php', array
					(
					'menuaction'	 => 'admin.ui_custom.list_attribute',
					'menu_selection' => str_replace('custom_field_groups', 'custom_fields', $this->menu_selection)
				)
				),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'new_attribute',
				'statustext' => lang('new attribute'),
				'text'		 => lang('new attribute'),
				'action'	 => $GLOBALS['phpgw']->link
				(
				'/index.php', array
					(
					'menuaction'	 => 'admin.ui_custom.edit_attrib',
					'menu_selection' => str_replace('custom_field_groups', 'custom_fields', $this->menu_selection)
				)
				),
				'parameters' => json_encode($parameters2)
			);

			unset($parameters);
			unset($parameters2);
			self::add_javascript('admin', 'base', 'ui_custom.list_attribute_group.js');

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_attrib_group()
		{
			if(!$this->acl_add)
			{
				phpgw::no_access($this->appname);
			}

			$location = $this->location;
			$appname = $this->appname;

			$id		 = phpgw::get_var('id', 'int');
			$values	 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			if(!$values)
			{
				$values = array();
			}

//			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));
			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));


			if(isset($values['save']) && $values['save'])
			{
				$action			 = 'add';

				if($id)
				{
					if(!$this->acl->check($this->location, PHPGW_ACL_EDIT, $this->appname))
					{
					   phpgw::no_access($this->appname);
					}
					$values['id']	 = $id;
					$action			 = 'edit';
				}
				else
				{
					if(!$this->acl->check($this->location, PHPGW_ACL_ADD, $this->appname))
					{
					   phpgw::no_access($this->appname);
					}
				}

				$values['location'] = $location;
				$values['appname'] = $appname;

				if(!$values['group_name'])
				{
					$receipt['error'][] = array('msg' => lang('group name not entered!'));
				}

				if(!$values['descr'])
				{
					$receipt['error'][] = array('msg' => lang('description not entered!'));
				}

				if(!$location)
				{
					$receipt['error'][] = array('msg' => lang('Missing location'));
				}


				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib_group($values, $action);

					if(!$id)
					{
						$id = $receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute group has NOT been saved'));
				}
			}

			if($id)
			{
				$values			 = $this->bo->read_single_attrib_group($appname, $location, $id);
				$type_name		 = $values['type_name'];
				$function_msg	 = lang('edit attribute group') . ' ' . lang($type_name);
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add attribute group');
				$action			 = 'add';
			}


			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			$parent_list = $GLOBALS['phpgw']->custom_fields->find_group($appname, $location, 0, '', '', '', true);

			$bocommon = CreateObject('property.bocommon');

			$parent_list = $bocommon->select_list($values['parent_id'], $parent_list);

			if($id)
			{
				$exclude	 = array($id);
				$children	 = $GLOBALS['phpgw']->custom_fields->get_attribute_group_children($location_id, $id, 0, 0, true);

				foreach($children as $child)
				{
					$exclude[] = $child['id'];
				}

				$k = count($parent_list);
				for($i = 0; $i < $k; $i++)
				{
					if(in_array($parent_list[$i]['id'], $exclude))
					{
						unset($parent_list[$i]);
					}
				}
			}

			$link_data = array
			(
				'menuaction'		 => 'admin.ui_custom.edit_attrib_group',
				'appname'			 => $appname,
				'id'				 => $id,
				'menu_selection'	 => $this->menu_selection
			);

			$msgbox_data = (isset($receipt) ? $bocommon->msgbox_data($receipt) : '');

			$data = array
			(
				'appname'					 => $appname,
				'value_location'			 => $location,
				'msgbox_data'				 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute_group',
												'appname'		=> $appname,
												'menu_selection'=> $this->menu_selection)),
				'value_id'					 => $id,
				'value_group_name'			 => $values['group_name'],
				'value_descr'				 => $values['descr'],
				'value_remark'				 => $values['remark'],
				'parent_list'				 => array('options' => $parent_list),
				'select_name_location'		 => 'location',
				'select_location_required'	 => true,
				'location_list'				 => array('options' => $this->bolocation->select_location('select', $location, false, true)),
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);
			//_debug_array($values);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($appname) . ' - ' . $location . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_attrib_group' => $data));
		}

		public function query($data = array())
		{
			if(!$this->acl_read)
			{
				phpgw::no_access($this->appname);
			}

			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');


			$params = array(
				'start'		 => $this->start,
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'sort'		 => $order[0]['dir'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1,
				'location'	 => $data['location'],
				'appname'	 => $this->appname
			);

			$result_objects	 = array();
			$result_count	 = 0;

			$values = array();
			switch($data['method'])
			{
				case 'category':
					$values	 = $this->bo->read_category($params);
					break;
				case 'list_attribute':
					$values	 = $this->bo->read_attrib($params);
					break;
				case 'list_attribute_group':
					$values	 = $this->bo->read_attrib_group($params);
					break;
				default:
					$values	 = $this->bo->read($params);
					break;
			}

			$new_values = array();
			foreach($values as &$value)
			{
				$value['appname'] = $this->appname;
				$value['location'] = $this->location;
			}

			if(phpgw::get_var('export', 'bool'))
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;
			switch($data['method'])
			{
				case 'list_attribute':
					$variable	 = array(
						'menuaction' => 'admin.ui_custom.list_attribute',
						'appname'		 => $this->appname,
						'location'		 => $this->location,
						'allrows'		 => $this->allrows,
						'menu_selection'=> $this->menu_selection

					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
				case 'list_attribute_group':
					$variable	 = array(
						'menuaction' => 'admin.ui_custom.query_attrib_group',
						'appname'		 => $this->appname,
						'location'		 => $this->location,
						'allrows'		 => $this->allrows,
						'menu_selection'	 => $this->menu_selection
					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
			}
			return $this->jquery_results($result_data);
		}
	}
