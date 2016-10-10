<?php
	/**
	* phpGroupWare.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package admin
	* @subpackage config
 	* @version $Id: class.uiconfig2.inc.php 3748 2009-09-29 12:58:22Z sigurd $
	*/

	/**
	 * Description
	 * @package admin
	 */

	class admin_uiconfig2
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'				=> true,
			'view_section'			=> true,
			'edit_section'			=> true,
			'delete_section'		=> true,
			'list_attrib'		=> true,
			'edit_attrib'		=> true,
			'delete_attrib'		=> true,
			'list_value'		=> true,
			'edit_value'		=> true,
			'delete_value'		=> true,
			'no_access'			=> true,
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('admin.boconfig',true);
			$this->acl					= & $GLOBALS['phpgw']->acl;
			$this->location_id			= $this->bo->location_id;
			$location_info				= $GLOBALS['phpgw']->locations->get_name($this->location_id);
			$this->currentapp			= $location_info['appname'];
			$this->acl_location 		= $location_info['location'];

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::config';
			$GLOBALS['phpgw_info']['flags']['app_header'] = "{$this->currentapp}::{$this->acl_location}::";
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','admin_c_section_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','admin_c_section_receipt','');

			$config_info = $this->bo->read_section();

			$lang_view_config_text		= lang('view the config');
			$lang_edit_config_text		= lang('edit the config');
			$text_view					= lang('view');
			$text_edit					= lang('edit');
			$text_delete				= lang('delete');
			$text_attribute				= lang('attributes');
			$lang_delete_config_text	= lang('delete the config');
			$lang_attribute_text		= lang('attributes for this config section');

			$content = array();
			foreach($config_info as $entry)
			{
				$content[] = array
				(
					'name'						=> $entry['name'],
					'link_attribute'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.list_attrib', 'section_id'=> $entry['id'] , 'location_id' => $this->location_id)),
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_section', 'section_id'=> $entry['id'] , 'location_id' => $this->location_id)),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.delete_section', 'section_id'=> $entry['id'] , 'location_id' => $this->location_id)),
					'link_view'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.view_section', 'section_id'=> $entry['id'] , 'location_id' => $this->location_id)),
					'lang_view_config_text'		=> $lang_view_config_text,
					'lang_edit_config_text'		=> $lang_edit_config_text,
					'text_view'					=> $text_view,
					'text_edit'					=> $text_edit,
					'text_delete'				=> $text_delete,
					'text_attribute'			=> $text_attribute,
					'lang_delete_config_text'	=> $lang_delete_config_text,
					'lang_attribute_text'		=> $lang_attribute_text,
				);
			}

//_debug_array($content);die();

			$table_header = array();
			$table_header[] = array
			(
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.uiconfig2.index',
														'query'		=> $this->query,
														'allrows'	=> $this->allrows,
														'location_id' => $this->location_id)
										)),
				'lang_name'		=> lang('name'),
				'lang_delete'	=> lang('delete'),
				'lang_edit'		=> lang('edit'),
				'lang_view'		=> lang('view'),
				'lang_attribute'=> lang('attribute'),
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
				'menuaction'	=> 'admin.uiconfig2.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'location_id' => $this->location_id
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a section'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_section', 'location_id' => $this->location_id)),
			);

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($config_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'table_add'						=> $table_add,
				'values'						=> $content
			);

			$appname	= lang('config');
			$function_msg	= lang('list section');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_section' => $data));
			$this->save_sessiondata();
		}


		function edit_section()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if($section_id)
					{
						$values['section_id'] = $section_id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_section($values,$action);
						$section_id = $receipt['section_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','admin_c_section_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.index', 'section_id'=> $section_id, 'location_id' => $this->location_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.index', 'section_id'=> $section_id, 'location_id' => $this->location_id));
				}
			}


			if ($section_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_section($section_id);
				}
				$function_msg = lang('edit section');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add section');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.edit_section',
				'section_id'		=> $section_id,
				'location_id'	=> $this->location_id
			);

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);


			$data = array
			(

				'value_name'				=> $values['name'],
				'value_descr'				=> $values['descr'],
				'lang_name'					=> lang('name'),
				'lang_descr'				=> lang('descr'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'					=> lang('ID'),
				'lang_save'					=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'					=> $section_id,
				'lang_done_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'		=> lang('Save the training'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'	=> lang('Apply the values'),
			);

			$appname					= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_section' => $data));
		}

		function view_section()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			if ($section_id)
			{
				$values = $this->bo->read_single_section($section_id);
				$function_msg = lang('view section');
			}
			else
			{
				return;
			}


			$data = array
			(
				'value_name'			=> $values['name'],
				'value_descr'			=> $values['descr'],
				'lang_id'				=> lang('section ID'),
				'lang_name'				=> lang('name'),
				'lang_descr'			=> lang('descr'),
				'form_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.index', 'location_id' => $this->location_id)),
				'lang_cancel'			=> lang('cancel'),
				'value_id'				=> $section_id,
			);

			$appname					= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_section' => $data));
		}

		function list_attrib()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');

			if(!$section_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.index', 'location_id' => $this->location_id));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','admin_c_attrib_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','admin_c_attrib_receipt','');

			$config_info = $this->bo->read_attrib($section_id);
//_debug_array($config_info);die();
			foreach ($config_info as $entry)
			{
				$content[] = array
				(
					'name'						=> $entry['name'],
					'link_value'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_value', 'section_id'=> $section_id, 'attrib_id'=> $entry['id'], 'id'=> $entry['value_id'], 'location_id' => $this->location_id)),
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_attrib', 'section_id'=> $section_id, 'attrib_id'=> $entry['id'], 'location_id' => $this->location_id)),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.delete_attrib', 'section_id'=> $section_id, 'attrib_id'=> $entry['id'], 'location_id' => $this->location_id)),
					'lang_edit_config_text'		=> lang('edit the config'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete'),
					'text_value'				=> $entry['value']?$entry['value']:lang('no value'),
					'lang_delete_config_text'	=> lang('delete the config'),
					'lang_value_text'			=> $entry['descr']?$entry['descr']:lang('values for this config section'),
				);
			}


			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.uiconfig2.index',
														'query'		=> $this->query,
														'allrows' 	=> $this->allrows,
														'location_id' => $this->location_id)
										)),
				'lang_name'			=> lang('name'),
				'lang_delete'		=> lang('delete'),
				'lang_edit'			=> lang('edit'),
				'lang_value'		=> lang('value'),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$section = $this->bo->read_single_section($section_id);

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.list_attrib',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'section_id'		=> $section_id,
				'location_id' => $this->location_id
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a value'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_attrib', 'section_id'=> $section_id, 'location_id' => $this->location_id)),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('back to the list'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.index', 'location_id' => $this->location_id)),
			);

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'lang_section'						=> lang('section'),
				'value_section_name'				=> $section['name'],
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allow_allrows'						=> true,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'num_records'						=> count($config_info),
				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_attrib'				=> $table_header,
				'table_add'							=> $table_add,
				'values_attrib'						=> $content
			);

			$appname	= lang('config');
			$function_msg	= lang('list attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attrib' => $data));
			$this->save_sessiondata();
		}

		function edit_attrib()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

					$values[section_id] = $section_id;

					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if($attrib_id)
					{
						$values['attrib_id']=$attrib_id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_attrib($values,$action);
						$attrib_id = $receipt['attrib_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','admin_c_attrib_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.list_attrib', 'section_id'=> $section_id, 'location_id' => $this->location_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.list_attrib', 'section_id'=> $section_id, 'location_id' => $this->location_id));
				}
			}


			if ($attrib_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_attrib($section_id,$attrib_id);
				}
				$function_msg = lang('edit attribute');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.edit_attrib',
				'section_id'	=> $section_id,
				'attrib_id'	=> $attrib_id,
				'location_id' => $this->location_id
			);


			$section = $this->bo->read_single_section($section_id);


			if($values['input_type']=='listbox')
			{
				$multiple_choice= true;
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(

				'value_name'						=> $values['name'],
				'value_descr'						=> $values['descr'],
				'lang_name'							=> lang('name'),
				'lang_descr'						=> lang('descr'),
				'lang_input_type'					=> lang('input type'),
				'input_type_list'					=> $this->bo->select_input_type_list($values['input_type']),
				'lang_no_input_type'				=> lang('no input type'),
				'lang_lang_input_type_status_text'	=> lang('input type'),

				'lang_choice'						=> lang('Choice'),
				'lang_new_value'					=> lang('New value'),
				'lang_new_value_statustext'			=> lang('New value for multiple choice'),
				'multiple_choice'					=> $multiple_choice,
				'value_choice'						=> $values['choice'],
				'lang_delete_value'					=> lang('Delete value'),
				'lang_value'						=> lang('value'),
				'lang_delete_choice_statustext'		=> lang('Delete this value from the list of multiple choice'),

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'							=> lang('ID'),
				'lang_save'							=> lang('save'),
				'lang_cancel'						=> lang('cancel'),
				'lang_section'							=> lang('section'),
				'value_section'						=> $section['name'],
				'value_id'							=> $attrib_id,
				'lang_done_status_text'				=> lang('Back to the list'),
				'lang_save_status_text'				=> lang('Save the training'),
				'lang_apply'						=> lang('apply'),
				'lang_apply_status_text'			=> lang('Apply the values'),
			);

			$appname	= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		}

		function list_value()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');

			if(!$section_id && !$attrib_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.index', 'location_id' => $this->location_id));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','admin_c_value_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','admin_c_attrib_value','');

			$config_info = $this->bo->read_value($section_id,$attrib_id);

			foreach ($config_info as $entry)
			{
				$content[] = array
				(
					'value'							=> $entry['value'],
					'link_edit'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_value', 'section_id'=> $entry['section_id'], 'attrib_id'=> $entry['attrib_id'], 'id'=> $entry['id'], 'location_id' => $this->location_id)),
					'link_delete'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.delete_value', 'section_id'=> $entry['section_id'], 'attrib_id'=> $entry['attrib_id'], 'id'=> $entry['id'], 'location_id' => $this->location_id)),
					'link_view'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.view_value', 'section_id'=> $entry['section_id'], 'attrib_id'=> $entry['attrib_id'], 'id'=> $entry['id'], 'location_id' => $this->location_id)),
					'lang_view_config_text'			=> lang('view the config'),
					'lang_edit_config_text'			=> lang('edit the config'),
					'text_view'						=> lang('view'),
					'text_edit'						=> lang('edit'),
					'text_delete'					=> lang('delete'),
					'text_value'					=> lang('value'),
					'lang_delete_config_text'		=> lang('delete the config'),
					'lang_value_text'				=> lang('value for this config section'),
				);
			}

//_debug_array($content);

			$table_header[] = array
			(
				'sort_value'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'value',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'admin.uiconfig2.index',
														'query'		=> $this->query,
														'allrows'	=> $this->allrows,
														'location_id' => $this->location_id)
										)),
				'lang_value'		=> lang('value'),
				'lang_delete'		=> lang('delete'),
				'lang_edit'			=> lang('edit'),
				'lang_view'			=> lang('view'),

			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$section = $this->bo->read_single_section($section_id);
			$attrib = $this->bo->read_single_attrib($section_id,$attrib_id);

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.list_value',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'section_id'		=> $section_id,
				'attrib_id'		=> $attrib_id,
				'location_id'	=> $this->location_id
			);

			if(!$content)
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a value'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.edit_value', 'section_id'=> $section_id, 'attrib_id'=> $attrib_id, 'location_id' => $this->location_id)),
				);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'link_section' 					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.list_attrib', 'section_id'=> $section_id, 'location_id' => $this->location_id)),
				'lang_section'						=> lang('section'),
				'value_section_name'				=> $section['name'],
				'lang_attrib'					=> lang('attribute'),
				'value_attrib_name'				=> $attrib['name'],

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($config_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_values'			=> $table_header,
				'table_add'						=> $table_add,
				'values_value'					=> $content
			);

			$appname	= lang('config');
			$function_msg	= lang('list values');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_value' => $data));
			$this->save_sessiondata();
		}


		function edit_value()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values', 'raw');

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

					$values['section_id'] = $section_id;
					$values['attrib_id'] = $attrib_id;

					if(!$values['value'] && !$id)
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a value !'));
					}

					if($id)
					{
						$values['id']=$id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_value($values,$action);
						$id = $receipt['id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','admin_c_attrib_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.list_attrib', 'section_id'=> $section_id, 'attrib_id'=> $attrib_id, 'location_id' => $this->location_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'admin.uiconfig2.list_attrib', 'section_id'=> $section_id, 'attrib_id'=> $attrib_id, 'location_id' => $this->location_id));
				}
			}

			if ($id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_value($section_id,$attrib_id,$id);
				}
				$function_msg = lang('edit value');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add value');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.edit_value',
				'section_id'		=> $section_id,
				'attrib_id'		=> $attrib_id,
				'id'			=> $id,
				'location_id'	=> $this->location_id
			);

			$section = $this->bo->read_single_section($section_id);
			$attrib = $this->bo->read_single_attrib($section_id,$attrib_id);

			if($attrib['input_type']=='listbox')
			{
				$choice_list = $this->bo->select_choice_list($section_id,$attrib_id,$values['value']);
			}


			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			if($attrib['input_type'] == 'password')
			{
				$values['value'] = '';
			}

			if($attrib['input_type'] == 'date')
			{
				$values['value'] = $GLOBALS['phpgw']->common->show_date($values['value'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$jscal = CreateObject('phpgwapi.jscalendar');
				$jscal->add_listener("date_value");
			}

			$data = array
			(
				'lang_section'				=> lang('section'),
				'value_section_name'		=> $section['name'],
				'lang_attrib'				=> lang('attribute'),
				'value_attrib_name'			=> $attrib['name'],
				'value_input_type'			=> $attrib['input_type'],
				'value_attrib_value'		=> $values['value'],
				'lang_value'				=> lang('value'),
				'choice_list'				=> $choice_list,
				'lang_no_value'				=> lang('no value'),
				'lang_value_status_text'	=> lang('select value'),
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'					=> lang('ID'),
				'lang_save'					=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_section'				=> lang('section'),
				'value_section'				=> $section['name'],
				'lang_attrib'				=> lang('attribute'),
				'value_attrib'				=> $attrib['name'],
				'value_id'					=> $id,

				'lang_done_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'		=> lang('Save the training'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'	=> lang('Apply the values'),
			);

			$appname	= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_value' => $data));
		}

		function delete_section()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.index',
				'section_id'	=> $section_id,
				'location_id' => $this->location_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_section($section_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.delete_section', 'section_id'=> $section_id, 'location_id' => $this->location_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('config');
			$function_msg	= lang('delete section');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function delete_attrib()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.list_attrib',
				'section_id'	=> $section_id,
				'location_id' => $this->location_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_attrib($section_id,$attrib_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.delete_attrib','section_id'=> $section_id, 'attrib_id'=> $attrib_id, 'location_id' => $this->location_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('config');
			$function_msg	= lang('delete attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}


		function delete_value()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->no_access();
				return;
			}

			$section_id	= phpgw::get_var('section_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'admin.uiconfig2.index',
				'section_id'	=> $section_id,
				'attrib_id'	=> $attrib_id,
				'id'		=> $id,
				'location_id' => $this->location_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_value($section_id,$attrib_id,$id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'admin.uiconfig2.delete_section', 'section_id'=> $section_id, 'attrib_id'=> $attrib_id, 'id'=> $id, 'location_id' => $this->location_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('config');
			$function_msg	= lang('delete value');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function no_access($message = '')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));
			if($message)
			{
				$receipt['error'][] = array('msg'=>$message);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'message'		=> $message,
			);

			$msg	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('admin') . ' - ' . $msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}

	}
