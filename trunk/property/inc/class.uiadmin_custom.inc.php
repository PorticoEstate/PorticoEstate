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
 	* @version $Id: class.uiadmin_custom.inc.php,v 1.10 2007/01/26 14:53:47 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uiadmin_custom
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
			'index'  	=> True,
			'edit'   	=> True,
			'delete' 	=> True
		);

		function property_uiadmin_custom()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('property.boadmin_custom',True);
			$this->boadmin			= CreateObject('property.boadmin',True);
			$this->bocommon			= CreateObject('property.bocommon');
			$this->menu				= CreateObject('property.menu');

			$this->acl 				= CreateObject('phpgwapi.acl');
			$this->acl_location		= '.admin';
			$this->acl_read 		= $this->acl->check($this->acl_location,1);
			$this->acl_add 			= $this->acl->check($this->acl_location,2);
			$this->acl_edit 		= $this->acl->check($this->acl_location,4);
			$this->acl_delete 		= $this->acl->check($this->acl_location,8);
			$this->acl_manage 		= $this->acl->check($this->acl_location,16);

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->cat_id			= $this->bo->cat_id;
			$this->allrows			= $this->bo->allrows;

			$this->menu->sub		='admin_custom';
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
				'cat_id'	=> $this->cat_id
			);
			$this->bo->save_sessiondata($data);
		}

	

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_custom',
									'nextmatchs',
									'search_field'));

			if($resort)
			{
				$this->bo->resort_custom_function($id,$resort);
			}
	
			$custom_function_list = $this->bo->read();

			if (isset($custom_function_list) AND is_array($custom_function_list))
			{
				foreach($custom_function_list as $entry)
				{
					$content[] = array
					(
						'acl_location' 				=> $entry['acl_location'],
						'file_name'				=> $entry['file_name'],
						'descr'					=> $entry['descr'],
						'sorting'				=> $entry['sorting'],
						'active'				=> $entry['active']?'X':'',
						'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiadmin_custom.index', 'resort'=>'up', 'cat_id'=> $entry['acl_location'], 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiadmin_custom.index', 'resort'=>'down', 'cat_id'=> $entry['acl_location'], 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiadmin_custom.edit', 'cat_id'=> $entry['acl_location'], 'id'=> $entry['id'])),
						'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiadmin_custom.delete', 'cat_id'=> $entry['acl_location'], 'id'=> $entry['id'])),
						'lang_up_text'				=> lang('shift up'),
						'lang_down_text'			=> lang('shift down'),
						'lang_edit_text'			=> lang('edit the custom_function'),
						'lang_delete_text'			=> lang('delete the custom_function'),
						'text_custom_function'			=> lang('custom functions'),
						'text_up'				=> lang('up'),
						'text_down'				=> lang('down'),
						'text_edit'				=> lang('edit'),
						'text_delete'				=> lang('delete')
					);
				}
			}

			$table_header[] = array
			(
				'lang_acl_location'	=> lang('Location'),
				'lang_descr'		=> lang('Descr'),
				'lang_active'		=> lang('Active'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_acl_location'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'acl_location',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiadmin_custom.index',
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiadmin_custom.index',
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'sort_sorting'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'custom_function_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiadmin_custom.index',
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'lang_name'	=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_custom_functiontext'	=> lang('add a custom_function'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiadmin_custom.edit', 'cat_id'=> $this->cat_id)),
				'lang_done'			=> lang('done'),
				'lang_done_custom_functiontext'	=> lang('back to admin'),
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
				'menuaction'	=> $this->currentapp.'.uiadmin_custom.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'cat_id'	=> $this->cat_id
			);

			$data = array
			(
				'lang_custom'							=> lang('entity'),
				'entity_name'							=> $entity['name'],
				'lang_category'							=> lang('category'),
				'category_name'							=> $category['name'],
				'allow_allrows'							=> True,
				'allrows'								=> $this->allrows,
				'start_record'							=> $this->start,
				'record_limit'							=> $record_limit,
				'start_record'							=> $this->start,
				'num_records'							=> count($custom_function_list),
				'all_records'							=> $this->bo->total_records,
				'link_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_custom_functiontext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_custom_functiontext'	=> lang('Submit the search string'),
				'query'									=> $this->query,
				'lang_search'							=> lang('search'),
				'table_header_custom_function'			=> $table_header,
				'values_custom_function'				=> $content,
				'table_add'								=> $table_add,

				'lang_no_location'						=> lang('No location'),
				'lang_location_statustext'				=> lang('Select submodule'),
				'select_name_location'					=> 'cat_id',
				'location_list'							=> $this->boadmin->select_location('filter',$this->cat_id,False)
			);

			$appname	= lang('custom function');
			$function_msg	= lang('list custom function');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_custom'));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['acl_location'] = $this->cat_id;

				if (!$values['acl_location'])
				{
					$receipt['error'][] = array('msg'=>lang('location not chosen!'));
				}

				if (!$values['custom_function_file'])
				{
					$receipt['error'][] = array('msg'=>lang('custom function file not chosen!'));
				}


				if (!$receipt['error'])
				{

					$receipt = $this->bo->save_custom_function($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Custom function has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_custom_function($id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit custom function'). ' ' . lang($type_name);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add custom function');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiadmin_custom.edit',
				'cat_id'	=> $this->cat_id,
				'id'		=> $id
			);


//_debug_array($values);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_custom'						=> lang('custom'),
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiadmin_custom.index', 'cat_id'=> $this->cat_id)),
				'lang_id'							=> lang('Custom function ID'),
				'lang_custom_type'					=> lang('Entity type'),
				'lang_no_custom_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,

				'lang_descr'						=> lang('descr'),
				'lang_descr_custom_functiontext'	=> lang('Enter a descr for the custom function'),
				'value_descr'						=> $values['descr'],

				'lang_done_custom_functiontext'		=> lang('Back to the list'),
				'lang_save_custom_functiontext'		=> lang('Save the custom function'),

				'lang_custom_function'				=> lang('custom function'),
				'lang_custom_function_statustext'	=> lang('Select a custom function'),
				'lang_no_custom_function'			=> lang('No custom function'),
				'custom_function_list'				=> $this->bo->select_custom_function($values['custom_function_file']),

				'lang_location'						=> lang('Location'),
				'lang_no_location'					=> lang('No location'),
				'lang_location_statustext'			=> lang('Select submodule'),
				'select_name_location'				=> 'cat_id',
				'location_list'						=> $this->boadmin->select_location('select',$this->cat_id),

				'value_active'						=> $values['active'],
				'lang_active'						=> lang('Active'),
				'lang_active_statustext'			=> lang('check to activate custom function'),
			);

			$appname	= lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiadmin_custom.index',
				'id' 		=> $id,
				'cat_id'	=> $this->cat_id
			);

			$delete_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiadmin_custom.delete',
				'cat_id'	=> $this->cat_id,
				'id'		=> $id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',$delete_data),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Back to the list'),
				'lang_no'					=> lang('no')
			);

			$appname	= lang('entity');
			$function_msg	= lang('delete entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
?>
