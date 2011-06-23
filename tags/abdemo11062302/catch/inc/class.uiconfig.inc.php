<?php
	/**
	* phpGroupWare - CATCH: An application for importing data from handhelds into property.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage config
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package catch
	 */

	class catch_uiconfig
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
			'index'				=> True,
			'edit_type'			=> True,
			'delete_type'		=> True,
			'list_attrib'		=> True,
			'edit_attrib'		=> True,
			'delete_attrib'		=> True,
			'list_value'		=> True,
			'edit_value'		=> True,
			'delete_value'		=> True,
			'daemon_manual'		=> True,
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('catch.boconfig',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location 		= '.config';
			$this->menu->sub			= $this->acl_location;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::catch::config';
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
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','catch_c_type_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','catch_c_type_receipt','');

			$config_info = $this->bo->read_type();

			foreach ($config_info as $entry)
			{
				$content[] = array
				(
					'name'						=> $entry['name'],
					'schema'					=> $entry['schema'],
					'link_attribute'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.list_attrib', 'type_id'=> $entry['id'])),
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_type', 'type_id'=> $entry['id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.delete_type', 'type_id'=> $entry['id'])),
					'lang_edit_config_text'		=> lang('edit the config'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete'),
					'text_attribute'			=> lang('attributes'),
					'lang_delete_config_text'	=> lang('delete the config'),
					'lang_attribute_text'		=> lang('attributes for this config type'),
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'catch.uiconfig.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
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
				'menuaction'	=> 'catch.uiconfig.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a type'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_type')),
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'							=> $this->bocommon->get_menu('catch'),
				'allow_allrows'					=> True,
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

			$appname		= lang('config');
			$function_msg	= lang('list type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_type' => $data));
			$this->save_sessiondata();
		}


		function edit_type()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
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
					if(!$values['schema'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a schema !'));
					}

					if($type_id)
					{
						$values['type_id'] = $type_id;
						$action = 'edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_type($values,$action);
						$type_id = $receipt['type_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','catch_c_type_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.index', 'type_id'=> $type_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.index', 'type_id'=> $type_id));
				}
			}


			if ($type_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_type($type_id);
				}
				$function_msg = lang('edit type');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add type');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.edit_type',
				'type_id'		=> $type_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'value_name'					=> $values['name'],
				'value_descr'					=> $values['descr'],
				'schema_list'					=> $this->bo->get_schema_list(isset($values['schema']) ? $values['schema'] : ''),
				'schema_text'					=> isset($values['schema_text']) ? $values['schema_text'] : '',
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'value_id'						=> $type_id,
			);

			$appname		= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_type' => $data));
		}

		function list_attrib()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');

			if(!$type_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.index'));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','catch_c_attrib_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','catch_c_attrib_receipt','');

			$config_info = $this->bo->read_attrib($type_id);

			while (is_array($config_info) && list(,$entry) = each($config_info))
			{

				$content[] = array
				(
					'name'						=> $entry['name'],
					'link_value'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_value', 'type_id'=> $type_id, 'attrib_id'=> $entry['id'])),
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_attrib', 'type_id'=> $type_id, 'attrib_id'=> $entry['id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.delete_attrib', 'type_id'=> $type_id, 'attrib_id'=> $entry['id'])),
					'lang_edit_config_text'		=> lang('edit the config'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete'),
					'text_value'				=> $entry['value']?$entry['value']:lang('value'),
					'lang_delete_config_text'	=> lang('delete the config'),
					'lang_value_text'			=> lang('values for this config type'),
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'catch.uiconfig.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
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

			$type = $this->bo->read_single_type($type_id);

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.list_attrib',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'type_id'		=> $type_id
			);

			$table_add[] = array
			(
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_attrib', 'type_id'=> $type_id)),
				'cancel_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.index')),
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_type'						=> lang('type'),
				'value_type_name'				=> $type['name'],
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'							=> $this->bocommon->get_menu('catch'),
				'allow_allrows'					=> True,
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
				'table_header_attrib'			=> $table_header,
				'table_add'						=> $table_add,
				'values_attrib'					=> $content
			);

			$appname		= lang('config');
			$function_msg	= lang('list attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attrib' => $data));
			$this->save_sessiondata();
		}

		function edit_attrib()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

					$values[type_id] = $type_id;

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
							$GLOBALS['phpgw']->session->appsession('session_data','catch_c_attrib_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.list_attrib', 'type_id'=> $type_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.list_attrib', 'type_id'=> $type_id));
				}
			}


			if ($attrib_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_attrib($type_id,$attrib_id);
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
				'menuaction'	=> 'catch.uiconfig.edit_attrib',
				'type_id'	=> $type_id,
				'attrib_id'	=> $attrib_id
			);


			$type = $this->bo->read_single_type($type_id);


			if($values['input_type']=='listbox')
			{
				$multiple_choice= True;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

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
				'lang_type'							=> lang('type'),
				'value_type'						=> $type['name'],
				'value_id'							=> $attrib_id,
				'lang_done_status_text'				=> lang('Back to the list'),
				'lang_save_status_text'				=> lang('Save the training'),
				'lang_apply'						=> lang('apply'),
				'lang_apply_status_text'			=> lang('Apply the values'),
			);

			$appname	= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		}



		function list_value()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');

			if(!$type_id && !$attrib_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.index'));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('config','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','catch_c_value_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','catch_c_attrib_value','');

			$config_info = $this->bo->read_value($type_id,$attrib_id);

			while (is_array($config_info) && list(,$entry) = each($config_info))
			{

				$content[] = array
				(
					'value'						=> $entry['value'],
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_value', 'type_id'=> $entry['type_id'], 'attrib_id'=> $entry['attrib_id'], 'id'=> $entry['id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.delete_value', 'type_id'=> $entry['type_id'], 'attrib_id'=> $entry['attrib_id'], 'id'=> $entry['id'])),
					'link_view'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.view_value', 'type_id'=> $entry['type_id'], 'attrib_id'=> $entry['attrib_id'], 'id'=> $entry['id'])),
					'lang_view_config_text'		=> lang('view the config'),
					'lang_edit_config_text'		=> lang('edit the config'),
					'text_view'					=> lang('view'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete'),
					'text_value'				=> lang('value'),
					'lang_delete_config_text'	=> lang('delete the config'),
					'lang_value_text'			=> lang('value for this config type'),
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
											'extra'	=> array('menuaction'	=> 'catch.uiconfig.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
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

			$type = $this->bo->read_single_type($type_id);
			$attrib = $this->bo->read_single_attrib($type_id,$attrib_id);

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.list_value',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'type_id'		=> $type_id,
				'attrib_id'		=> $attrib_id
			);

			if(!$content)
			{
				$table_add[] = array
				(
					'lang_add'				=> lang('add'),
					'lang_add_statustext'	=> lang('add a value'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.edit_value', 'type_id'=> $type_id, 'attrib_id'=> $attrib_id)),
				);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'link_type' 					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.list_attrib', 'type_id'=> $type_id)),
				'lang_type'						=> lang('type'),
				'value_type_name'				=> $type['name'],
				'lang_attrib'					=> lang('attribute'),
				'value_attrib_name'				=> $attrib['name'],

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'							=> $this->bocommon->get_menu('catch'),
				'allow_allrows'					=> True,
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
;
			$function_msg	= lang('list values');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_value' => $data));
			$this->save_sessiondata();
		}


		function edit_value()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('config'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{
					$values[type_id] = $type_id;
					$values[attrib_id] = $attrib_id;

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
							$GLOBALS['phpgw']->session->appsession('session_data','catch_c_attrib_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.list_attrib', 'type_id'=> $type_id, 'attrib_id'=> $attrib_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'catch.uiconfig.list_attrib', 'type_id'=> $type_id, 'attrib_id'=> $attrib_id));
				}
			}


			if ($attrib_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_value($type_id,$attrib_id);
				}
				$function_msg	= lang('edit value');
				$action			= 'edit';
			}
			else
			{
				$function_msg	= lang('add value');
				$action			= 'add';
			}

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.edit_value',
				'type_id'		=> $type_id,
				'attrib_id'		=> $attrib_id
			);

			$type = $this->bo->read_single_type($type_id);
			$attrib = $this->bo->read_single_attrib($type_id,$attrib_id);

			if($attrib['input_type']=='listbox')
			{
				$choice_list = $this->bo->select_choice_list($type_id,$attrib_id,$values['value']);
			}


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_type'					=> lang('type'),
				'value_type_name'			=> $type['name'],
				'lang_attrib'				=> lang('attribute'),
				'value_attrib_name'			=> $attrib['name'],

				'value_value'				=> $values['value'],
				'lang_value'				=> lang('value'),
				'choice_list'				=> $choice_list,
				'lang_no_value'				=> lang('no value'),
				'lang_value_status_text'	=> lang('select value'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'					=> lang('ID'),
				'lang_save'					=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_type'					=> lang('type'),
				'value_type'				=> $type['name'],
				'lang_attrib'				=> lang('attribute'),
				'value_attrib'				=> $attrib['name'],
				'value_id'					=> $id,

				'lang_done_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'		=> lang('Save the training'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'	=> lang('Apply the values'),
			);

			$appname	= lang('config');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_value' => $data));
		}

		function delete_type()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_type($type_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.delete_type', 'type_id'=> $type_id)),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'					=> lang('no')
			);

			$appname		= lang('config');
			$function_msg	= lang('delete type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function delete_attrib()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.list_attrib',
				'type_id'	=> $type_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_attrib($type_id,$attrib_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.delete_attrib','type_id'=> $type_id, 'attrib_id'=> $attrib_id)),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'					=> lang('no')
			);

			$appname			= lang('config');
			$function_msg		= lang('delete attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}


		function delete_value()
		{
			if(!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
				$this->bocommon->no_access();
				return;
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$attrib_id	= phpgw::get_var('attrib_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'catch.uiconfig.index',
				'type_id'		=> $type_id,
				'attrib_id'		=> $attrib_id,
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_value($type_id,$attrib_id,$id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.delete_value', 'type_id'=> $type_id, 'attrib_id'=> $attrib_id)),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'					=> lang('no')
			);

			$appname		= lang('config');
			$function_msg	= lang('delete value');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('catch') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
