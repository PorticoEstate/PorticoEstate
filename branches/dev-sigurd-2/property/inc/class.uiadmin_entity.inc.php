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

	class property_uiadmin_entity
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
			'index'  				=> true,
			'category' 				=> true,
			'edit'   				=> true,
			'edit_category'			=> true,
			'view'   				=> true,
			'delete' 				=> true,
			'list_attribute_group'	=> true,
			'list_attribute'		=> true,
			'edit_attrib_group'		=> true,
			'edit_attrib' 			=> true,
			'list_custom_function'	=> true,
			'edit_custom_function'	=> true
		);

		function property_uiadmin_entity()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boadmin_entity',true);
			$this->bocommon				= & $this->bo->bocommon;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->entity_id			= $this->bo->entity_id;
			$this->cat_id				= $this->bo->cat_id;
			$this->allrows				= $this->bo->allrows;
			$this->type					= $this->bo->type;
			$this->type_app				= $this->bo->type_app;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin.entity';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->type_app[$this->type]);
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->type_app[$this->type]);
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->type_app[$this->type]);
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]);
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, $this->type_app[$this->type]);

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$this->type_app[$this->type]}::entity";
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
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$this->bocommon->reset_fm_cache();
			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			$entity_list = $this->bo->read();

			if (isSet($entity_list) AND is_array($entity_list))
			{
				foreach($entity_list as $entry)
				{
					$content[] = array
					(
						'id'				=> $entry['id'],
						'name'				=> $entry['name'],
						'descr'				=> $entry['descr'],
						'link_categories'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category',' entity_id'=> $entry['id'])),
						'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit', 'id'=> $entry['id'])),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete',' entity_id'=> $entry['id'])),
						'lang_view_standardtext'	=> lang('view the standard'),
						'lang_category_text'		=> lang('categories for the entity type'),
						'lang_edit_standardtext'	=> lang('edit the entity'),
						'lang_delete_standardtext'	=> lang('delete the entity'),
						'text_categories'		=> lang('Categories'),
						'text_edit'			=> lang('edit'),
						'text_delete'			=> lang('delete')
					);
				}
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'		=> lang('Descr'),
				'lang_categories'	=> lang('Categories'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.index',
																'allrows'=> $this->allrows)
										)),
				'lang_id'		=> lang('entity id'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.index',
																'allrows'=> $this->allrows)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_standardtext'	=> lang('add a standard'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit')),
				'lang_done'		=> lang('done'),
				'lang_done_standardtext'=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/admin/index.php')
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
				'menuaction'	=> 'property.uiadmin_entity.index',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'query'		=>$this->query
			);

			$data = array
			(
				'allow_allrows'				=> true,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($entity_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_standardtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_standardtext'	=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add
			);

			$appname	= lang('entity');
			$function_msg	= lang('list entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}


		function category()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}";

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			$category_list = $this->bo->read_category($entity_id);

			if (isSet($category_list) AND is_array($category_list))
			{
				foreach($category_list as $entry)
				{
					$content[] = array
					(
						'id'								=> $entry['id'],
						'name'								=> $entry['name'],
						'prefix'							=> $entry['prefix'],
						'descr'								=> $entry['descr'],
						'link_custom_function'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'cat_id'=> $entry['id'], 'entity_id'=> $entity_id)),
						'link_attribute'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'cat_id'=> $entry['id'], 'entity_id'=> $entity_id)),
						'link_attribute_group'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'cat_id'=> $entry['id'], 'entity_id'=> $entity_id)),
						'link_edit'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_category','id'=> $entry['id'], 'entity_id'=> $entity_id)),
						'link_delete'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete', 'cat_id'=> $entry['id'], 'entity_id'=> $entity_id)),
						'lang_view_standardtext'			=> lang('view the category'),
						'lang_status_standardtext'			=> lang('Status for the entity category'),
						'lang_attribute_standardtext'		=> lang('attributes for the entity category'),
						'lang_custom_function_standardtext'	=> lang('custom functions for the entity category'),
						'lang_edit_standardtext'			=> lang('edit the standard'),
						'lang_delete_standardtext'			=> lang('delete the standard'),
						'text_status'						=> lang('Status'),
						'text_attribute'					=> lang('Attributes'),
						'text_attribute_group'				=> lang('attribute groups'),
						'text_custom_function'				=> lang('Custom functions'),
						'text_edit'							=> lang('edit'),
						'text_delete'						=> lang('delete')
					);
				}
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'				=> lang('Descr'),
				'lang_prefix'				=> lang('prefix'),
				'lang_attribute'			=> lang('Attributes'),
				'lang_attribute_group'		=> lang('attribute groups'),
				'lang_custom_function'		=> lang('custom functions'),
				'lang_edit'					=> lang('edit'),
				'lang_delete'				=> lang('delete'),
				'sort_id'					=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.category',
																'entity_id' =>$entity_id,
																'allrows'=>$this->allrows)
										)),
				'lang_id'		=> lang('category id'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.category',
																'entity_id' =>$entity_id,
																'allrows'=>$this->allrows)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_standardtext'		=> lang('add a category'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_category', 'entity_id'=> $entity_id)),
				'lang_done'			=> lang('done'),
				'lang_done_standardtext'	=> lang('back to entity'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.index'))
			);

			$entity = $this->bo->read_single($entity_id,false);

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
				'menuaction'	=> 'property.uiadmin_entity.category',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'entity_id'	=> $entity_id
			);

			$data = array
			(
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($category_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_standardtext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_standardtext'		=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_category'				=> $table_header,
				'values_category'				=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang('entity');
			$function_msg	= lang('list entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_category' => $data));
			$this->save_sessiondata();
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');
			$config = CreateObject('phpgwapi.config','property');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				if (!$values['name'])
				{
					$receipt['error'][] = array('msg'=>lang('Name not entered!'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if (!$receipt['error'])
				{

					$receipt = $this->bo->save($values,$action);
					if(!$id)
					{
						$id=$receipt['id'];
					}
					$config->read();

					if(!is_array($config->config_data['location_form']))
					{
						$config->config_data['location_form'] = array();
					}

					if($values['location_form'])
					{

						$config->config_data['location_form']['entity_' . $id] = 'entity_' . $id;

					}
					else
					{
						unset($config->config_data['location_form']['entity_' . $id]);
					}

					$config->save_repository();
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Entity has NOT been saved'));
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
				$function_msg = lang('add entity');
				$action='add';
			}

			$include_list	= $this->bo->get_entity_list($values['lookup_entity']);
			$include_list_2	= $this->bo->get_entity_list_2($values['include_entity_for']);
			$include_list_3	= $this->bo->get_entity_list_3($values['start_entity_from']);

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit',
				'id'		=> $id
			);
//_debug_array($include_list);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_name_standardtext'			=> lang('Enter a name of the standard'),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.index')),
				'lang_id'					=> lang('standard ID'),
				'lang_name'					=> lang('Name'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,
				'value_name'					=> $values['name'],
				'lang_id_standardtext'				=> lang('Enter the standard ID'),
				'lang_descr_standardtext'			=> lang('Enter a description of the standard'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the standard'),
				'type_id'					=> $values['type_id'],
				'value_descr'					=> $values['descr'],
				'lang_location_form'				=> lang('location form'),
				'value_location_form'				=> $values['location_form'],
				'lang_location_form_statustext'			=> lang('If this entity type is to be linked to a location'),
				'lang_include_in_location_form'			=> lang('include in location form'),
				'include_list'					=> $include_list,
				'lang_include_statustext'			=> lang('Which entity type is to show up in location forms'),
				'lang_include_this_entity'			=> lang('include this entity'),
				'include_list_2'				=> $include_list_2,
				'lang_include_2_statustext'			=> lang('Let this entity show up in location form'),
				'lang_start_this_entity'			=> lang('start this entity'),
				'include_list_3'				=> $include_list_3,
				'lang_include_3_statustext'			=> lang('Start this entity from'),
				'lang_select'					=> lang('select'),
				'lang_documentation'				=> lang('documentation'),
				'value_documentation'				=> $values['documentation'],
				'lang_documentation_statustext'			=> lang('If this entity type is to be linked to documents'),
			);

			$appname	= lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_category()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				$values['entity_id']	= $entity_id;

				if (!$values['name'])
				{
					$receipt['error'][] = array('msg'=>lang('Name not entered!'));
				}
				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('Entity not chosen'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_category($values,$action);
					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Category has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_category($entity_id,$id);
				$function_msg = lang('edit category');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add category');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit_category',
				'entity_id'	=>$entity_id,
				'id'		=> $id
			);
//_debug_array($link_data);

			$entity = $this->bo->read_single($entity_id,false);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_prefix_standardtext'			=> lang('Enter a standard prefix for the id'),
				'lang_name_standardtext'			=> lang('Enter a name of the standard'),

				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id)),
				'lang_id'							=> lang('Category'),
				'lang_name'							=> lang('Name'),
				'lang_descr'						=> lang('Descr'),
				'lang_prefix'						=> lang('Prefix'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,
				'value_name'						=> $values['name'],
				'value_prefix'						=> $values['prefix'],
				'lang_id_standardtext'				=> lang('Enter the standard ID'),
				'lang_descr_standardtext'			=> lang('Enter a description of the standard'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the standard'),
				'type_id'							=> $values['type_id'],
				'value_descr'						=> $values['descr'],
				'lang_lookup_tenant'				=> lang('lookup tenant'),
				'value_lookup_tenant'				=> $values['lookup_tenant'],
				'lang_lookup_tenant_statustext'		=> lang('If this entity type is to look up tenants'),
				'lang_location_level'				=> lang('location level'),
				'location_level_list'				=> $this->bo->get_location_level_list($values['location_level']),
				'lang_location_level_statustext'	=> lang('select location level'),
				'lang_no_location_level'			=> lang('None'),
				'lang_tracking'						=> lang('tracking'),
				'value_tracking'					=> $values['tracking'],
				'lang_tracking_statustext'			=> lang('If this entity type is to be tracket in ticket list'),
				'lang_fileupload'					=> lang('Enable file upload'),
				'value_fileupload'					=> $values['fileupload'],
				'lang_fileupload_statustext'		=> lang('If files can be uploaded for this category'),
				'lang_loc_link'						=> lang('Link from location'),
				'value_loc_link'					=> $values['loc_link'],
				'lang_loc_link_statustext'			=> lang('Enable link from location detail'),
				'lang_start_project'				=> lang('Start project'),
				'value_start_project'				=> $values['start_project'],
				'lang_start_project_statustext'		=> lang('Enable start project from this category'),
				'lang_start_ticket'					=> lang('Start ticket'),
				'value_start_ticket'				=> $values['start_ticket'],
				'lang_start_ticket_statustext'		=> lang('Enable start ticket from this category')
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$entity_id		= phpgw::get_var('entity_id', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$attrib_id		= phpgw::get_var('attrib_id', 'int');
			$group_id		= phpgw::get_var('group_id', 'int');
			$acl_location		= phpgw::get_var('acl_location');
			$custom_function_id	= phpgw::get_var('custom_function_id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			if($group_id)
			{
				$function='list_attribute_group';
			}
			else if($attrib_id)
			{
				$function='list_attribute';
			}
			else if($custom_function_id)
			{
				$function='list_custom_function';
			}

			if (!$acl_location && $entity_id && $cat_id)
			{
				$acl_location = '.entity.' . $entity_id . '.' . $cat_id;
			}

			if(!$function)
			{
				if($cat_id)
				{
					$function='category';
				}
				else
				{
					$function='index';
				}
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.'.$function,
				'cat_id' 	=> $cat_id,
				'entity_id'	=> $entity_id,
				'attrib_id'	=> $attrib_id
			);

			$delete_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.delete',
				'cat_id'	=> $cat_id,
				'entity_id'	=> $entity_id,
				'group_id'	=> $group_id,
				'attrib_id'	=> $attrib_id,
				'acl_location'	=> $acl_location,
				'custom_function_id' => $custom_function_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($cat_id,$entity_id,$attrib_id,$acl_location,$custom_function_id,$group_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',$delete_data),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('entity');
			$function_msg		= lang('delete entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function list_attribute_group()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id	= $this->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib_group($id,$resort);
			}
			$attrib_list = $this->bo->read_attrib_group($entity_id,$cat_id);

			if (isset($attrib_list) AND is_array($attrib_list))
			{
				foreach($attrib_list as $entry)
				{

					$content[] = array
					(
						'name'					=> $entry['name'],
						'descr'					=> $entry['descr'],
						'sorting'				=> $entry['group_sort'],
						'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_attrib_group', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'])),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'group_id'=> $entry['id'])),
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
				'lang_sorting'		=> lang('sorting'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_attribute',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'=>$this->allrows)
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_attribute',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'=>$this->allrows)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_attrib_group', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id)),
				'lang_done'		=> lang('done'),
				'lang_done_attribtext'	=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id)),
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
				'menuaction'	=> 'property.uiadmin_entity.list_attribute',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id
			);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'lang_category'						=> lang('category'),
				'category_name'						=> $category['name'],
				'allow_allrows'						=> true,
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
				'table_header_attrib_group'			=> $table_header,
				'values_attrib_group'				=> $content,
				'table_add'							=> $table_add
			);

			$appname	= lang('attribute');
			$function_msg	= lang('list entity attribute group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute_group' => $data));
			$this->save_sessiondata();
		}

		function list_attribute()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id	= $this->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib($id,$resort);
			}
			$attrib_list = $this->bo->read_attrib($entity_id,$cat_id);

			if (isset($attrib_list) AND is_array($attrib_list))
			{
				foreach($attrib_list as $entry)
				{

					$content[] = array
					(
						'name'				=> $entry['name'],
						'datatype'			=> $entry['trans_datatype'],
						'column_name'		=> $entry['column_name'],
						'input_text'		=> $entry['input_text'],
						'attrib_group'		=> $entry['group_id'],
						'sorting'			=> $entry['attrib_sort'],
						'search'			=> $entry['search'],
						'link_up'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'resort'=> 'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_down'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'resort'=> 'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_attrib', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'])),
						'link_delete'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'attrib_id'=> $entry['id'])),
						'lang_up_text'		=> lang('shift up'),
						'lang_down_text'	=> lang('shift down'),
						'lang_edit_text'	=> lang('edit the attrib'),
						'lang_delete_text'	=> lang('delete the attrib'),
						'text_attribute'	=> lang('Attributes'),
						'text_up'			=> lang('up'),
						'text_down'			=> lang('down'),
						'text_edit'			=> lang('edit'),
						'text_delete'		=> lang('delete')
					);
				}
			}

//_debug_array($content);

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_datatype'		=> lang('Datatype'),
				'lang_attrib_group'	=> lang('group'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_attribute',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'=>$this->allrows)
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_attribute',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'=>$this->allrows)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_attrib', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id)),
				'lang_done'		=> lang('done'),
				'lang_done_attribtext'	=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id)),
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
				'menuaction'	=> 'property.uiadmin_entity.list_attribute',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id
			);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$data = array
			(
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'],
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'start_record'					=> $this->start,
				'num_records'					=> count($attrib_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_attribtext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_attrib'				=> $table_header,
				'values_attrib'					=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang('attribute');
			$function_msg	= lang('list entity attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute' => $data));
			$this->save_sessiondata();
		}

		function edit_attrib_group()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
			if(!$values)
			{
				$values=array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['entity_id']=$entity_id;
				$values['cat_id']=$cat_id;

				if (!$values['group_name'])
				{
					$receipt['error'][] = array('msg'=>lang('group name not entered!'));
				}

				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg'=>lang('description not entered!'));
				}

				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
				}


				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib_group($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute group has NOT been saved'));
				}
			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib_group($entity_id,$cat_id,$id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit attribute group'). ' ' . lang($type_name);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute group');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit_attrib_group',
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'id'		=> $id
			);


			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'lang_category'						=> lang('category'),
				'category_name'						=> $category['name'],

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id)),
				'lang_id'							=> lang('Attribute group ID'),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,

				'lang_group_name'					=> lang('group name'),
				'value_group_name'					=> $values['group_name'],
				'lang_group_name_statustext'		=> lang('enter the name for the group'),

				'lang_descr'						=> lang('descr'),
				'value_descr'						=> $values['descr'],
				'lang_descr_statustext'				=> lang('enter the input text for records'),

				'lang_remark'						=> lang('remark'),
				'lang_remark_statustext'			=> lang('Enter a remark for the group'),
				'value_remark'						=> $values['remark'],

				'lang_done_attribtext'				=> lang('Back to the list'),
				'lang_save_attribtext'				=> lang('Save the attribute')
			);
//_debug_array($values);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib_group' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_attrib()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
			if(!$values)
			{
				$values=array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['entity_id']=$entity_id;
				$values['cat_id']=$cat_id;

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

				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
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
					$receipt = $this->bo->save_attrib($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib($entity_id,$cat_id,$id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit attribute'). ' ' . lang($type_name);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit_attrib',
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'id'		=> $id
			);

			if($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB')
			{
				$multiple_choice= true;
			}

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'lang_category'						=> lang('category'),
				'category_name'						=> $category['name'],

				'lang_choice'						=> lang('Choice'),
				'lang_new_value'					=> lang('New value'),
				'lang_new_value_statustext'			=> lang('New value for multiple choice'),
				'multiple_choice'					=> (isset($multiple_choice)?$multiple_choice:''),
				'value_choice'						=> (isset($values['choice'])?$values['choice']:''),
				'lang_delete_value'					=> lang('Delete value'),
				'lang_value'						=> lang('value'),
				'lang_delete_choice_statustext'		=> lang('Delete this value from the list of multiple choice'),

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id)),
				'lang_id'							=> lang('Attribute ID'),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,

				'lang_column_name'					=> lang('Column name'),
				'value_column_name'					=> $values['column_name'],
				'lang_column_name_statustext'		=> lang('enter the name for the column'),

				'lang_input_text'					=> lang('input text'),
				'value_input_text'					=> $values['input_text'],
				'lang_input_name_statustext'		=> lang('enter the input text for records'),

				'lang_id_attribtext'				=> lang('Enter the attribute ID'),
				'lang_entity_statustext'			=> lang('Select a entity type'),

				'lang_statustext'					=> lang('Statustext'),
				'lang_statustext_attribtext'		=> lang('Enter a statustext for the inputfield in forms'),
				'value_statustext'					=> $values['statustext'],

				'lang_done_attribtext'				=> lang('Back to the list'),
				'lang_save_attribtext'				=> lang('Save the attribute'),

				'lang_datatype'						=> lang('Datatype'),
				'lang_datatype_statustext'			=> lang('Select a datatype'),
				'lang_no_datatype'					=> lang('No datatype'),
				'datatype_list'						=> $this->bocommon->select_datatype($values['column_info']['type']),

				'lang_group'						=> lang('group'),
				'lang_group_statustext'				=> lang('Select a group'),
				'lang_no_group'					=> lang('no group'),
				'attrib_group_list'					=> $this->bo->get_attrib_group_list($entity_id,$cat_id, $values['group_id']),

				'lang_precision'					=> lang('Precision'),
				'lang_precision_statustext'			=> lang('enter the record length'),
				'value_precision'					=> $values['column_info']['precision'],

				'lang_scale'						=> lang('scale'),
				'lang_scale_statustext'				=> lang('enter the scale if type is decimal'),
				'value_scale'						=> $values['column_info']['scale'],

				'lang_default'						=> lang('default'),
				'lang_default_statustext'			=> lang('enter the default value'),
				'value_default'						=> $values['column_info']['default'],

				'lang_nullable'						=> lang('Nullable'),
				'lang_nullable_statustext'			=> lang('Chose if this column is nullable'),
				'lang_select_nullable'				=> lang('Select nullable'),
				'nullable_list'						=> $this->bocommon->select_nullable($values['column_info']['nullable']),
				'value_lookup_form'					=> $values['lookup_form'],
				'lang_lookup_form'					=> lang('show in lookup forms'),
				'lang_lookup_form_statustext'		=> lang('check to show this attribue in lookup forms'),
				'value_list'						=> $values['list'],
				'lang_list'							=> lang('show in list'),
				'lang_list_statustext'				=> lang('check to show this attribute in entity list'),
				'value_search'						=> $values['search'],
				'lang_include_search'				=> lang('Include in search'),
				'lang_include_search_statustext'	=> lang('check to show this attribute in location list'),

				'value_history'						=> $values['history'],
				'lang_history'						=> lang('history'),
				'lang_history_statustext'			=> lang('Enable history for this attribute'),

				'value_disabled'					=> $values['disabled'],
				'lang_disabled'						=> lang('disabled'),
				'lang_disabled_statustext'			=> lang('This attribute turn up as disabled in the form'),

				'value_helpmsg'						=> $values['helpmsg'],
				'lang_helpmsg'						=> lang('help message'),
				'lang_helpmsg_statustext'			=> lang('Enables help message for this attribute'),
			);
//_debug_array($values);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function list_custom_function()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id		= $this->cat_id;
			$id		= phpgw::get_var('id', 'int');
			$resort		= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_custom_function($id,$resort);
			}
			$custom_function_list = $this->bo->read_custom_function($entity_id,$cat_id);

			if (isset($custom_function_list) AND is_array($custom_function_list))
			{
				foreach($custom_function_list as $entry)
				{

					$content[] = array
					(
						'file_name'				=> $entry['file_name'],
						'descr'					=> $entry['descr'],
						'sorting'				=> $entry['sorting'],
						'active'				=> $entry['active']?'X':'',
						'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'resort'=>'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'resort'=>'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows)),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_custom_function', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'])),
						'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'custom_function_id'=> $entry['id'])),
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
				'lang_descr'		=> lang('Descr'),
				'lang_active'		=> lang('Active'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_custom_function',
																'entity_id'	=> $entity_id,
																'cat_id'	=> $cat_id,
																'allrows'	=> $this->allrows)
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'custom_function_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_custom_function',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'	=>$this->allrows)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_custom_functiontext'	=> lang('add a custom_function'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_custom_function', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id)),
				'lang_done'			=> lang('done'),
				'lang_done_custom_functiontext'	=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id)),
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
				'menuaction'	=> 'property.uiadmin_entity.list_custom_function',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id
			);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$data = array
			(
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'],
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'start_record'					=> $this->start,
				'num_records'					=> count($custom_function_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_custom_functiontext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_custom_functiontext'		=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_custom_function'			=> $table_header,
				'values_custom_function'			=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang('custom function');
			$function_msg	= lang('list entity custom function');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_custom_function' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit_custom_function()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['entity_id']=$entity_id;
				$values['cat_id']=$cat_id;


				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
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
					$receipt['error'][] = array('msg' => lang('Custom function has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_custom_function($entity_id,$cat_id,$id);
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
				'menuaction'	=> 'property.uiadmin_entity.edit_custom_function',
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'id'		=> $id
			);


//_debug_array($values);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'],

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id)),
				'lang_id'					=> lang('Custom function ID'),
				'lang_entity_type'				=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,

				'lang_descr'					=> lang('descr'),
				'lang_descr_custom_functiontext'		=> lang('Enter a descr for the custom function'),
				'value_descr'					=> $values['descr'],

				'lang_done_custom_functiontext'			=> lang('Back to the list'),
				'lang_save_custom_functiontext'			=> lang('Save the custom function'),

				'lang_custom_function'				=> lang('custom function'),
				'lang_custom_function_statustext'		=> lang('Select a custom function'),
				'lang_no_custom_function'			=> lang('No custom function'),
				'custom_function_list'				=> $this->bo->select_custom_function($values['custom_function_file']),

				'value_active'					=> $values['active'],
				'lang_active'					=> lang('Active'),
				'lang_active_statustext'			=> lang('check to activate custom function'),
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_custom_function' => $data));
		}
	}

