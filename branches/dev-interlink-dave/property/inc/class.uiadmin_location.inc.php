<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uiadmin_location
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
			'index'  		=> true,
			'config'  		=> true,
			'edit_config'	=> true,
			'view'   		=> true,
			'edit'   		=> true,
			'delete' 		=> true,
			'list_attribute'=> true,
			'edit_attrib' 	=> true,
		);

		function property_uiadmin_location()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::location';
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boadmin_location',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= CreateObject('phpgwapi.acl');
			$this->acl_location			= '.admin.location';
			$this->acl_read 			= $this->acl->check($this->acl_location,1);
			$this->acl_add 				= $this->acl->check($this->acl_location,2);
			$this->acl_edit 			= $this->acl->check($this->acl_location,4);
			$this->acl_delete 			= $this->acl->check($this->acl_location,8);
			$this->acl_manage 			= $this->acl->check($this->acl_location,16);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;

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
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::location';

			$this->bocommon->reset_fm_cache();
			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_location',
								'nextmatchs',
								'search_field'));

			$standard_list = $this->bo->read();

			while (is_array($standard_list) && list(,$standard) = each($standard_list))
			{
				$content[] = array
				(
					'id'							=> $standard['id'],
					'name'							=> $standard['name'],
					'first'							=> $standard['descr'],
					'link_categories'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type'=>'location', 'type_id'=> $standard['id'])),
					'link_attribute'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'type_id'=> $standard['id'])),
					'link_edit'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit' ,'id'=> $standard['id'])),
					'link_delete'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.delete' ,'id'=> $standard['id'])),
					'lang_view_standardtext'		=> lang('view the standard'),
					'lang_category_text'			=> lang('categories for the location type'),
					'lang_attribute_standardtext'	=> lang('attributes for the location type'),
					'lang_edit_standardtext'		=> lang('edit the standard'),
					'lang_delete_standardtext'		=> lang('delete the standard'),
					'text_categories'				=> lang('Categories'),
					'text_attribute'				=> lang('Attributes'),
					'text_edit'						=> lang('edit'),
					'text_delete'					=> lang('delete')
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'		=> lang('Descr'),
				'lang_categories'	=> lang('Categories'),
				'lang_attribute'	=> lang('Attributes'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_id'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_location.index')
										)),
				'lang_id'			=> lang('standard id'),
				'sort_name'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_location.index')
										)),
				'lang_name'			=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_standardtext'	=> lang('add a standard'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit')),
				'lang_done'				=> lang('done'),
				'lang_done_standardtext'=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php')
			);

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'						=> $msgbox_data,
				'allow_allrows'						=> false,
				'start_record'						=> $this->start,
				'record_limit'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'						=> count($standard_list),
				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_standardtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_standardtext'	=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header'						=> $table_header,
				'values'							=> $content,
				'table_add'							=> $table_add
			);

			$appname		= lang('location');
			$function_msg		= lang('list location standard');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']))
			{
				if (!isset($values['name']) || !$values['name'])
				{
					$receipt['error'][] = array('msg'=>lang('Name not entered!'));
				}

				if($id)
				{
					$values['id']=$id;
				}

				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save($values);
					$id=$receipt['id'];
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Table has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single($id);
				$function_msg = lang('edit standard');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add standard');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_location.edit',
				'id'		=> $id
			);
//_debug_array($values);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_name_standardtext'		=> lang('Enter a name of the standard'),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
				'lang_id'						=> lang('standard ID'),
				'lang_name'						=> lang('Name'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'value_id'						=> (isset($id)?$id:''),
				'value_name'					=> (isset($values['name'])?$values['name']:''),
				'lang_id_standardtext'			=> lang('Enter the standard ID'),
				'lang_descr_standardtext'		=> lang('Enter a description of the standard'),
				'lang_done_standardtext'		=> lang('Back to the list'),
				'lang_save_standardtext'		=> lang('Save the standard'),
				'value_descr'					=> (isset($values['descr'])?$values['descr']:''),
				'lang_list_info'				=> lang('list info'),
				'lang_select'					=> lang('select'),
				'value_list_info'				=> $this->bo->get_list_info((isset($id)?$id:''),$values['list_info']),
				'lang_location'					=> lang('location'),
				'lang_list_address'				=> lang('list address'),
				'lang_list_info_statustext'		=> lang('Names of levels to list at this level'),
				'lang_list_address_statustext'	=> lang('List address at this level'),
				'value_list_address'			=> (isset($values['list_address'])?$values['list_address']:'')
			);

			$appname	= lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$attrib		= phpgw::get_var('attrib');
			$type_id	= phpgw::get_var('type_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			if($attrib)
			{
				$function='list_attribute';
			}
			else
			{
				$function='index';
			}
			$link_data = array
			(
				'menuaction' => 'property.uiadmin_location.'.$function,
				'type_id' => $type_id
			);

			if ($confirm)
			{
				$this->bo->delete($type_id,$id,$attrib);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.delete', 'id'=> $id, 'attrib'=> $attrib, 'type_id'=> $type_id)),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Back to the list'),
				'lang_no'					=> lang('no')
			);

			$appname		= lang('location');
			$function_msg	= lang('delete location standard');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function list_attribute()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_location',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib(array('resort'=>$resort,'type_id' => $type_id,'id'=>$id));
			}

			$type = $this->bo->read_single($type_id);
			
			$attrib_list = $this->bo->read_attrib($type_id);

			while (is_array($attrib_list) && list(,$attrib) = each($attrib_list))
			{
				$content[] = array
				(
					'datatype'					=> $attrib['trans_datatype'],
					'column_name'				=> $attrib['column_name'],
					'input_text'				=> $attrib['input_text'],
					'sorting'					=> $attrib['attrib_sort'],
					'link_up'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'resort'=> 'up', 'id'=> $attrib['id'], 'type_id'=> $type_id, 'allrows'=> $this->allrows)),
					'link_down'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'resort'=> 'down', 'id'=> $attrib['id'], 'type_id'=> $type_id, 'allrows'=> $this->allrows)),
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit_attrib', 'id'=> $attrib['id'], 'type_id'=> $type_id)),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.delete', 'id'=> $attrib['id'], 'type_id'=> $type_id, 'attrib'=>true)),
					'lang_view_attribtext'		=> lang('view the attrib'),
					'lang_attribute_attribtext'	=> lang('attributes for the attrib'). ' ' . lang('location'),
					'lang_edit_attribtext'		=> lang('edit the attrib'),
					'lang_delete_attribtext'	=> lang('delete the attrib'),
					'text_attribute'			=> lang('Attributes'),
					'text_up'					=> lang('up'),
					'text_down'					=> lang('down'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

//_debug_array($content);

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_datatype'		=> lang('Datatype'),
				'lang_sorting'		=> lang('sorting'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_location.list_attribute',
															'type_id'	=> $type_id,
															'allrows'	=> $this->allrows)
										)),

				'sort_name'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra' => array('menuaction'	=> 'property.uiadmin_location.list_attribute',
																'type_id'	=>$type_id,
																'allrows'	=>$this->allrows)
										)),
				'lang_name'			=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit_attrib', 'type_id'=> $type_id)),
				'lang_done'				=> lang('done'),
				'lang_done_attribtext'	=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
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
				'menuaction'	=> 'property.uiadmin_location.list_attribute',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
				'type_id'		=> $type_id
			);

			$data = array
			(
				'value_type_name'				=> $type['name'],
				'lang_type'						=> lang('Location type'),
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($attrib_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_attribtext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_attrib'			=> $table_header,
				'values_attrib'					=> $content,
				'table_add'						=> $table_add
			);

			$appname	= lang('attribute');
			$function_msg	= lang('list location attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit_attrib()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			if(!$values)
			{
			  $values = array();
			}

//_debug_array($values);
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				$type_id			= $values['type_id'];

				if (!$values['column_name'])
				{
					$receipt['error'][] = array('msg'=>lang('Column name not entered!'));
				}

				if (!$values['input_text'])
				{
					$receipt['error'][] = array('msg'=>lang('Input text not entered!'));
				}
				if (!$values['statustext'])
				{
					$receipt['error'][] = array('msg'=>lang('Statustext not entered!'));
				}

				if (!$values['type_id'])
				{
					$receipt['error'][] = array('msg'=>lang('Location type not chosen!'));
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


				if (!$receipt['error'])
				{

					$receipt = $this->bo->save_attrib($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib($type_id,$id);
				$function_msg = lang('edit attribute'). ' ' . $values['input_text'];
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_location.edit_attrib',
				'id'		=> $id
			);
//_debug_array($values);

			$multiple_choice = '';
			if($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB')
			{
				$multiple_choice= true;
			}


			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'lang_choice'					=> lang('Choice'),
				'lang_new_value'				=> lang('New value'),
				'lang_new_value_statustext'		=> lang('New value for multiple choice'),
				'multiple_choice'				=> $multiple_choice,
				'value_choice'					=> (isset($values['choice'])?$values['choice']:''),
				'lang_delete_value'				=> lang('Delete value'),
				'lang_value'					=> lang('value'),
				'lang_delete_choice_statustext'	=> lang('Delete this value from the list of multiple choice'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'type_id'=> $type_id)),
				'lang_id'						=> lang('Attribute ID'),
				'lang_location_type'			=> lang('Type'),
				'lang_no_location_type'			=> lang('No entity type'),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'value_id'						=> $id,

				'lang_column_name'				=> lang('Column name'),
				'value_column_name'				=> $values['column_name'],
				'lang_column_name_statustext'	=> lang('enter the name for the column'),

				'lang_input_text'				=> lang('input text'),
				'value_input_text'				=> $values['input_text'],
				'lang_input_name_statustext'	=> lang('enter the input text for records'),

				'lang_id_attribtext'			=> lang('Enter the attribute ID'),
				'lang_entity_statustext'		=> lang('Select a entity type'),

				'lang_statustext'				=> lang('Statustext'),
				'lang_statustext_attribtext'	=> lang('Enter a statustext for the inputfield in forms'),
				'value_statustext'				=> $values['statustext'],

				'lang_done_attribtext'			=> lang('Back to the list'),
				'lang_save_attribtext'			=> lang('Save the attribute'),
				'type_id'						=> $values['type_id'],
				'entity_list'					=> $this->bo->select_location_type($type_id),
				'select_location_type'			=> 'values[type_id]',
				'lang_datatype'					=> lang('Datatype'),
				'lang_datatype_statustext'		=> lang('Select a datatype'),
				'lang_no_datatype'				=> lang('No datatype'),
				'datatype_list'					=> $this->bocommon->select_datatype($values['column_info']['type']),

				'lang_precision'				=> lang('Precision'),
				'lang_precision_statustext'		=> lang('enter the record length'),
				'value_precision'				=> $values['column_info']['precision'],

				'lang_scale'					=> lang('scale'),
				'lang_scale_statustext'			=> lang('enter the scale if type is decimal'),
				'value_scale'					=> $values['column_info']['scale'],

				'lang_default'					=> lang('default'),
				'lang_default_statustext'		=> lang('enter the default value'),
				'value_default'					=> $values['column_info']['default'],

				'lang_nullable'					=> lang('Nullable'),
				'lang_nullable_statustext'		=> lang('Chose if this column is nullable'),
				'lang_select_nullable'			=> lang('Select nullable'),
				'nullable_list'					=> $this->bocommon->select_nullable($values['column_info']['nullable']),
				'value_lookup_form'				=> $values['lookup_form'],
				'lang_lookup_form'				=> lang('show in lookup forms'),
				'lang_lookup_form_statustext'	=> lang('check to show this attribue in lookup forms'),
				'value_list'					=> $values['list'],
				'lang_list'						=> lang('show in list'),
				'lang_list_statustext'			=> lang('check to show this attribute in location list')
			);
//_debug_array($data);

			$appname = lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function config()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::config';

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_location',
								'nextmatchs',
								'search_field'));

			$standard_list = $this->bo->read_config();

			while (is_array($standard_list) && list(,$standard) = each($standard_list))
			{
				$content[] = array
				(
					'column_name'				=> $standard['column_name'],
					'name'						=> $standard['location_name'],
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit_config', 'column_name'=> $standard['column_name'])),
					'lang_edit_standardtext'	=> lang('edit the column relation'),
					'text_edit'					=> lang('edit')
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_attribute'	=> lang('Attributes'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_column_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_location.config')
										)),
				'lang_column_name'	=> lang('column name'),
				'sort_name'			=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_location.config')
										)),
				'lang_name'			=> lang('Table Name'),
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_standardtext'	=> lang('add a standard'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit')),
				'lang_done'				=> lang('done'),
				'lang_done_standardtext'=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php')
			);


			$data = array
			(
				'allow_allrows'						=> false,
				'start_record'						=> $this->start,
				'record_limit'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'						=> count($standard_list),
				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_standardtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_standardtext'	=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_list_config'			=> $table_header,
				'values_list_config'				=> $content,
				'table_add'							=> $table_add
			);

			$appname	= lang('location');
			$function_msg	= lang('list config');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_config' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit_config()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::config';

			$column_name	= phpgw::get_var('column_name');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']) && $values['save'])
			{
				$receipt = $this->bo->save_config($values,$column_name);
			}

			$type_id	= $this->bo->read_config_single($column_name);

			$function_msg = lang('edit location config for') . ' ' .$column_name;

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_location.edit_config',
				'column_name'	=> $column_name
			);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.config')),

				'lang_column_name'			=> lang('Column name'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'column_name'				=> $column_name,
				'value_name'				=> (isset($values['name'])?$values['name']:''),

				'location_list'				=> $this->bo->select_location_type($type_id),

				'lang_config_statustext'	=> lang('Select the level for this information'),
				'lang_done_standardtext'	=> lang('Back to the list'),
				'lang_save_standardtext'	=> lang('Save the standard'),
				'type_id'					=> (isset($values['type_id'])?$values['type_id']:''),
				'value_descr'				=> (isset($values['descr'])?$values['descr']:'')
			);

			$appname	= lang('location');

//_debug_array($data);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_config' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}

