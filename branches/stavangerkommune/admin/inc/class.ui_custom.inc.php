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

	class admin_ui_custom
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $local_menu_selection = false;

		var $public_functions = array
		(
			'delete' 				=> True,
			'list_attribute'		=> True,
			'edit_attrib' 			=> True,
			'list_custom_function'	=> True,
			'edit_custom_function'	=> True
		);

		public function __construct()
		{
			$this->bo					= CreateObject('admin.bo_custom',True);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->appname				= $this->bo->appname;
			$this->location				= $this->bo->location;
			$this->allrows				= $this->bo->allrows;

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bolocation			= CreateObject('preferences.boadmin_acl');
			$this->bolocation->acl_app 	= $this->appname;

			if(!$GLOBALS['phpgw_info']['flags']['menu_selection'] = phpgw::get_var('menu_selection'))
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

			if ( is_array($attrib_list))
			{
				foreach($attrib_list as $entry)
				{
					$content[] = array
					(
						'name'					=> $entry['name'],
						'datatype'				=> $entry['datatype'],
						'column_name'			=> $entry['column_name'],
						'input_text'			=> $entry['input_text'],
						'sorting'				=> $entry['attrib_sort'],
						'search'				=> $entry['search'],
						'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.list_attribute', 'resort'=>'up', 'appname'=> $appname, 'location'=> $location, 'id'=> $entry['id'], 'allrows'=> $this->allrows, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.list_attribute', 'resort'=>'down', 'appname'=> $appname, 'location'=> $location, 'id'=> $entry['id'], 'allrows'=> $this->allrows, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.edit_attrib', 'appname'=> $appname, 'location'=> $location, 'id'=> $entry['id'], 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.delete', 'appname'=> $appname, 'location'=> $location, 'attrib_id'=> $entry['id'], 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
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
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.edit_attrib', 'appname'=> $appname, 'location'=> $location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
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
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_attribtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_attrib'				=> $table_header,
				'values_attrib'						=> (isset($content)?$content:''),
				'table_add'							=> $table_add,

				'lang_no_location'					=> lang('No location'),
				'lang_location_statustext'			=> lang('Select submodule'),
				'select_name_location'				=> 'location',
				'location_list'						=> $this->bolocation->select_location('filter',$location,False,True),

			);

			$function_msg	= lang('list custom attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' .lang('attribute') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute' => $data));
			$this->save_sessiondata();
		}

		function edit_attrib()
		{
			$appname	= $this->appname;
			$location	= $this->location;
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST', array());

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['appname']= $appname;
				$values['location'] = $location;

				if (!$values['location'])
				{
					$receipt['error'][] = array('msg'=>lang('location not selected!'));
				}
				else
				{
					$location = $values['location'];
				}

				if (!$values['column_name'])
				{
					$receipt['error'][] = array('msg'=>lang('Column name not entered!'));
				}

				if(!preg_match('/^[a-z0-9_]+$/i',$values['column_name']))
				{
					$receipt['error'][] = array('msg'=>lang('Column name %1 contains illegal character', $values['column_name']));
				}

				if (!$values['input_text'])
				{
					$receipt['error'][] = array('msg'=>lang('Input text not entered!'));
				}
				if (!$values['statustext'])
				{
					$receipt['error'][] = array('msg'=>lang('Statustext not entered!'));
				}

				if (!$values['appname'])
				{
					$receipt['error'][] = array('msg'=>lang('application not chosen!'));
				}

				if (!$values['column_info']['type'])
				{
					$receipt['error'][] = array('msg'=>lang('Datatype type not chosen!'));
				}

				if(!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter precision as integer !'));
					unset($values['column_info']['precision']);
				}

				if($values['column_info']['scale'] && !ctype_digit($values['column_info']['scale']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter scale as integer !'));
					unset($values['column_info']['scale']);
				}

				if (!$values['column_info']['nullable'])
				{
					$receipt['error'][] = array('msg'=>lang('Nullable not chosen!'));
				}

				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib($values);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				//	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.ui_custom.edit_attrib', 'appname' => $values['appname'], 'location' => $values['location'], 'id' => $id, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']));

				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->get_attrib_single($appname,$location,$id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit attribute'). ' ' . lang($type_name);
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

			$multiple_choice = '';
			if(isset($values['column_info']['type']) && ($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB'))
			{
				$multiple_choice= True;
			}

//_debug_array($values);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'appname'							=> $appname,

				'lang_choice'						=> lang('Choice'),
				'lang_new_value'					=> lang('New value'),
				'lang_new_value_statustext'			=> lang('New value for multiple choice'),
				'multiple_choice'					=> $multiple_choice,
				'value_choice'						=> (isset($values['choice'])?$values['choice']:''),
				'lang_delete_value'					=> lang('Delete value'),
				'lang_value'						=> lang('value'),
				'lang_delete_choice_statustext'		=> lang('Delete this value from the list of multiple choice'),

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.list_attribute', 'appname'=> $appname, 'location'=>$location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,

				'value_column_name'					=> (isset($values['column_name'])?$values['column_name']:''),

				'value_input_text'					=> (isset($values['input_text'])?$values['input_text']:''),

				'lang_id_attribtext'				=> lang('Enter the attribute ID'),
				'lang_entity_statustext'			=> lang('Select a entity type'),

				'value_statustext'					=> (isset($values['statustext'])?$values['statustext']:''),

				'lang_done_attribtext'				=> lang('Back to the list'),
				'lang_save_attribtext'				=> lang('Save the attribute'),

				'lang_datatype_statustext'			=> lang('Select a datatype'),
				'lang_no_datatype'					=> lang('No datatype'),
				'datatype_list'						=> $this->bo->select_datatype((isset($values['column_info']['type'])?$values['column_info']['type']:'')),
				'attrib_group_list'					=> array('options' => $this->bo->get_attrib_group_list($this->appname,$this->location, $values['group_id'])),
				'lang_precision'					=> lang('Precision'),
				'lang_precision_statustext'			=> lang('enter the record length'),
				'value_precision'					=> (isset($values['column_info']['precision'])?$values['column_info']['precision']:''),

				'lang_scale'						=> lang('scale'),
				'lang_scale_statustext'				=> lang('enter the scale if type is decimal'),
				'value_scale'						=> (isset($values['column_info']['scale'])?$values['column_info']['scale']:''),

				'lang_default'						=> lang('default'),
				'lang_default_statustext'			=> lang('enter the default value'),
				'value_default'						=> (isset($values['column_info']['default'])?$values['column_info']['default']:''),

				'lang_nullable'						=> lang('Nullable'),
				'lang_nullable_statustext'			=> lang('Chose if this column is nullable'),
				'lang_select_nullable'				=> lang('Select nullable'),
				'nullable_list'						=> $this->bo->select_nullable((isset($values['column_info']['nullable'])?$values['column_info']['nullable']:'')),
				'value_lookup_form'					=> (isset($values['lookup_form'])?$values['lookup_form']:''),
				'lang_lookup_form'					=> lang('show in lookup forms'),
				'lang_lookup_form_statustext'		=> lang('check to show this attribue in lookup forms'),
				'value_list'						=> (isset($values['list'])?$values['list']:''),
				'lang_list'							=> lang('show in list'),
				'lang_list_statustext'				=> lang('check to show this attribute in entity list'),
				'value_search'						=> (isset($values['search'])?$values['search']:''),
				'lang_include_search'				=> lang('Include in search'),
				'lang_include_search_statustext'	=> lang('check to show this attribute in location list'),

				'value_history'						=> (isset($values['history'])?$values['history']:''),
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
				'value_location'					=> $location
			);
//_debug_array($values);

			$appname	= lang('appname');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		}

		function list_custom_function()
		{
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
				$this->bo->resort_custom_function($id,$resort);
			}
			$custom_function_list = $this->bo->read_custom_function($appname,$location);
			$content = array();
			if (isset($custom_function_list) AND is_array($custom_function_list))
			{
				foreach($custom_function_list as $entry)
				{

					$content[] = array
					(
						'file_name'					=> $entry['file_name'],
						'descr'						=> $entry['descr'],
						'sorting'					=> $entry['sorting'],
						'active'					=> $entry['active']?'X':'',
						'link_up'					=> $GLOBALS['phpgw']->link('/index.php',array
														(
															'menuaction'		=> 'admin.ui_custom.list_custom_function',
															'resort'			=> 'up',
															'appname'			=> $appname,
															'location'			=> $location,
															'id'				=> $entry['id'],
															'allrows'			=> $this->allrows,
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
														)),
						'link_down'					=> $GLOBALS['phpgw']->link('/index.php',array
														(
															'menuaction'		=> 'admin.ui_custom.list_custom_function',
															'resort'			=> 'down',
															'appname'			=> $appname,
															'location'			=> $location,
															'id'				=> $entry['id'],
															'allrows'			=> $this->allrows,
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
														)),
						'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array
														(
															'menuaction'		=> 'admin.ui_custom.edit_custom_function',
															'appname'			=> $appname,
															'location'			=> $location,
															'id'				=> $entry['id'],
															'menu_selection'	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
															)),
						'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array
														(
															'menuaction'		=> 'admin.ui_custom.delete',
															'appname'			=> $appname,
															'location'			=> $location,
															'custom_function_id'=> $entry['id'],
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
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
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
											'var'	=> 'custom_function_sort',
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
				'add_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.edit_custom_function', 'appname'=> $appname, 'location'=> $location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
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
				'link_url'									=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'									=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
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
				'location_list'								=> $this->bolocation->select_location('filter',$location,False,True),

			);

			$function_msg	= lang('list custom function');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' .lang('custom function') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_custom_function' => $data));
			$this->save_sessiondata();
		}

		function edit_custom_function()
		{
	//		$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::list_functions';
			$appname	= $this->appname;
			$location	= $this->location;
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST');

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if (isset($values['save']) && $values['save'])
			{
				if(isset($id) && $id)
				{
					$values['id']=$id;
					$action='edit';
				}
				else
				{
					$action='add';
				}

				$values['appname']=$appname;
				$values['location']=$location;

				if (!$values['appname'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
				}

				if (!$values['custom_function_file'])
				{
					$receipt['error'][] = array('msg'=>lang('custom function file not chosen!'));
				}

				if(!$values['location'] || !$values['appname'])
				{
				 	$receipt['error'][] = array('msg' => lang('location or appname is missing'));
				}

				if (!isset($receipt['error']) || !$receipt['error'])
				{
					$receipt = $this->bo->save_custom_function($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Custom function has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_custom_function($appname,$location,$id);
				$type_name=$values['custom_function_file'];
				$function_msg = lang('edit custom function'). ': ' . $type_name;
				$action='edit';
			}
			else
			{
				$function_msg = lang('add custom function');
				$action='add';
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

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'lang_appname'						=> lang('appname'),
				'appname'							=> $appname,
				'lang_location'						=> lang('location'),
				'location'							=> $location,

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.ui_custom.list_custom_function', 'appname'=> $appname, 'location'=> $location, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_id'							=> lang('Custom function ID'),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,

				'lang_descr'						=> lang('descr'),
				'lang_descr_custom_functiontext'	=> lang('Enter a descr for the custom function'),
				'value_descr'						=> isset($values['descr']) ? $values['descr']:'',

				'lang_done_custom_functiontext'		=> lang('Back to the list'),
				'lang_save_custom_functiontext'		=> lang('Save the custom function'),

				'lang_custom_function'				=> lang('custom function'),
				'lang_custom_function_statustext'	=> lang('Select a custom function'),
				'lang_no_custom_function'			=> lang('No custom function'),
				'custom_function_list'				=> $this->bo->select_custom_function(isset($values['custom_function_file'])?$values['custom_function_file']:'',$appname),

				'value_active'						=> isset($values['active'])?$values['active']:'',
				'lang_active'						=> lang('Active'),
				'lang_active_statustext'			=> lang('check to activate custom function'),
			);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . lang('custom function') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_custom_function' => $data));
		}

		function delete()
		{
			$appname				= phpgw::get_var('appname');
			$location				= phpgw::get_var('location');
			$attrib_id				= phpgw::get_var('attrib_id', 'int');
			$custom_function_id		= phpgw::get_var('custom_function_id', 'int');

			$function = 'list_attribute';
			if ( $custom_function_id )
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

			if ( phpgw::get_var('confirm', 'bool', 'POST') )
			{
				$this->bo->delete($location, $appname, $attrib_id, $custom_function_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $redirect_args);
			}

			if ( phpgw::get_var('cancel', 'bool', 'POST') )
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
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
