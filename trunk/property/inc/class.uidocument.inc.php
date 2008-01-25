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
	* @subpackage document
 	* @version $Id: class.uidocument.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uidocument
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'  	=> True,
			'list_doc'	=> True,
			'view' 		=> True,
			'view_file' => True,
			'edit'   	=> True,
			'delete' 	=> True
		);

		function property_uidocument()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation";

		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bodocument',True);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->bolocation			= CreateObject('property.bolocation');
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->boadmin_entity		= CreateObject('property.boadmin_entity');

			$this->acl 					= CreateObject('phpgwapi.acl');
			$this->acl_location			= '.document';
			$this->acl_read 			= $this->acl->check('.document',1);
			$this->acl_add 				= $this->acl->check('.document',2);
			$this->acl_edit 			= $this->acl->check('.document',4);
			$this->acl_delete 			= $this->acl->check('.document',8);

			$this->rootdir 				= $this->bo->rootdir;
			$this->fakebase 			= $this->bo->fakebase;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->status_id			= $this->bo->status_id;
			$this->entity_id			= $this->bo->entity_id;
			$this->doc_type				= $this->bo->doc_type;
			$this->query_location			= $this->bo->query_location;

			// FIXME: $this->entity_id always has a value set here - skwashd jan08
			if ( $this->entity_id )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$this->entity_id}";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::location';
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
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id,
				'status_id'	=> $this->status_id,
				'entity_id'	=> $this->entity_id,
				'doc_type'	=> $this->doc_type,
				'query_location'=> $this->query_location
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array(
										'document',
										'values',
										'table_header',
										'nextmatchs',
										'search_field'
										)
			);

			$entity_id = phpgw::get_var('entity_id', 'int');

			$preserve = phpgw::get_var('preserve', 'bool');

			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start				= $this->bo->start;
				$this->query				= $this->bo->query;
				$this->sort					= $this->bo->sort;
				$this->order				= $this->bo->order;
				$this->filter				= $this->bo->filter;
				$this->cat_id				= $this->bo->cat_id;
				$this->status_id			= $this->bo->status_id;
				$this->entity_id			= $this->bo->entity_id;
			}

			$document_list = $this->bo->read();

//_debug_array($document_list);


			$uicols	= $this->bo->uicols;

			$j=0;
			while (is_array($document_list) && list(,$document_entry) = each($document_list))
			{
				for ($k=0;$k<count($uicols['name']);$k++)
				{
					if($uicols['input_type'][$k]!='hidden')
					{
						if(isset($document_entry['query_location'][$uicols['name'][$k]]) && $document_entry['query_location'][$uicols['name'][$k]])
						{

							$content[$j]['row'][]= array(
								'statustext'	=> lang('search'),
								'text'		=> $document_entry[$uicols['name'][$k]],
								'link'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.index', 'query'=> $document_entry['query_location'][$uicols['name'][$k]], 'entity_id'=> isset($document_entry['p_entity_id'])?$document_entry['p_entity_id']:'', 'cat_id'=> isset($document_entry['p_cat_id'])?$document_entry['p_cat_id']:''))
								);
						}
						else
						{
							$content[$j]['row'][]= array(
								'value' 	=> $document_entry[$uicols['name'][$k]],
								'name' 		=> $uicols['name'][$k],
								);
						}

					}
				}

				if($this->acl_read)
				{
					$content[$j]['row'][]= array(
						'statustext'		=> lang('view documents for this location/entity'),
						'text'			=> lang('documents'),
						'link'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.list_doc', 'location_code'=>  $document_entry['location_code'], 'p_num'=> isset($document_entry['p_num']) ? $document_entry['p_num'] :'', 'entity_id'=> isset($document_entry['p_entity_id']) ? $document_entry['p_entity_id'] : '', 'cat_id'=> isset($document_entry['p_cat_id']) ? $document_entry['p_cat_id'] : '', 'doc_type'=> $this->doc_type))
						);
				}

				$j++;
			}

			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 		= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['name'][$i]=='loc1')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'location_code',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uidocument.index',
																	'query'		=> $this->query,
																	'cat_id'	=> $this->cat_id,
																	'doc_type'	=> $this->doc_type,
																	'entity_id'	=> $this->entity_id)
										));
					}
					if($uicols['name'][$i]=='document_id')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'document_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uidocument.index',
																	'query'		=> $this->query,
																	'cat_id'	=> $this->cat_id,
																	'doc_type'	=> $this->doc_type,
																	'entity_id'	=> $this->entity_id)
										));
					}
					if($uicols['name'][$i]=='address')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'address',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uidocument.index',
																	'query'		=> $this->query,
																	'cat_id'	=> $this->cat_id,
																	'doc_type'	=> $this->doc_type,
																	'entity_id'	=> $this->entity_id)
										));
					}
				}
			}

			if($this->acl_read)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('view');
				$i++;
			}

			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a document'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.edit', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id))

				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uidocument.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'status_id'	=> $this->status_id,
				'query'		=> $this->query,
				'doc_type'	=> $this->doc_type,
				'entity_id'	=> $this->entity_id
			);

			if($this->entity_id)
			{
				$boentity	= CreateObject('property.boentity');
				$boentity->entity_id=$this->entity_id;

				$cat_list	= $this->bo->select_category_list('filter',$this->cat_id);
				$entity 	= $this->boadmin_entity->read_single($this->entity_id,false);
				$appname_sub	= $entity['name'];
			}
			else
			{
				$appname_sub	= lang('location');
			}

			$data = array
			(
				'link_history'							=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.index', 'cat_id'=> $this->cat_id)),
				'lang_history_statustext'				=> lang('search for history at this location'),
				'lang_select'							=> lang('select'),
				'allow_allrows'							=> false,
				'start_record'							=> $this->start,
				'record_limit'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'							=> count($document_list),
				'all_records'							=> $this->bo->total_records,
				'link_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'type'									=> $this->cat_id,
				'select_action'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_no_cat'							=> lang('no category'),
				'lang_cat_statustext'					=> lang('Select the category. To do not use a category select NO CATEGORY'),
				'select_name'							=> 'cat_id',
				'cat_list'								=> (isset($cat_list)?$cat_list:''),

				'lang_no_doc_type'						=> lang('no document type'),
				'lang_doc_type_statustext'				=> lang('Select the document type the document belongs to.'),
				'doc_type'								=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->doc_type,'type' =>'document','order'=>'descr')),

				'lang_status_statustext'				=> lang('Select the status the document belongs to. To do not use a category select NO STATUS'),
				'status_name'							=> 'status_id',
				'lang_no_status'						=> lang('No status'),
				'status_list'							=> $this->bo->select_status_list('filter',$this->status_id),

				'lang_user_statustext'					=> lang('Select the user the document belongs to. To do not use a category select NO USER'),
				'select_user_name'						=> 'filter',
				'lang_no_user'							=> lang('No user'),
				'user_list'								=> $this->bocommon->get_user_list_right2('filter',4,$this->filter,$this->acl_location,array('all'),$default=$this->account),

				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'									=> $this->query,
				'lang_search'							=> lang('search'),
				'table_header'							=> $table_header,
				'values'								=> (isset($content)?$content:''),
				'table_add'								=> $table_add
			);

			$appname	= lang('document');
			$function_msg	= lang('list document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg . ' - ' . $appname_sub;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function list_doc()
		{

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$preserve = phpgw::get_var('preserve', 'bool');

			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start				= $this->bo->start;
				$this->query				= $this->bo->query;
				$this->sort				= $this->bo->sort;
				$this->order				= $this->bo->order;
				$this->filter				= $this->bo->filter;
				$this->entity_id			= $this->bo->entity_id;
				$this->cat_id				= $this->bo->cat_id;
				$this->status_id			= $this->bo->status_id;
			}
//_debug_array($this->cat_id);

			$GLOBALS['phpgw']->xslttpl->add_file(array('document',
										'receipt',
										'nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','document_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','document_receipt','');

			$location_code = phpgw::get_var('location_code');
			if($this->query_location)
			{
				$location_code = $this->query_location;
			}

			$p_num = phpgw::get_var('p_num');

			$location=$this->bo->read_location_data($location_code);

			if($this->cat_id)
			{
				$entity_data[$this->entity_id]['p_num']=$p_num;
				$entity_data[$this->entity_id]['p_entity_id']=$this->entity_id;
				$entity_data[$this->entity_id]['p_cat_id']=$this->cat_id;
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$lookup_entity	= $this->bocommon->get_lookup_entity('document');
				$appname_sub	= $entity['name'];
			}
			else
			{
				$appname_sub	= lang('location');
			}

			if($category['name'])
			{
				$entity_data[$this->entity_id]['p_cat_name']=$category['name'];
			}

			$this->config->read_repository();
			$files_url = $this->config->config_data['files_url'];

			$document_list = $this->bo->read_at_location($location_code);

//_debug_array($document_list);

			if($this->cat_id)
			{
				$directory = $this->fakebase. '/' . 'document' . '/' . $location['loc1'] . '/' . $entity['name'] . '/' . $category['name'] . '/' . $p_num;
			}
			else
			{
				$directory = $this->fakebase. '/' . 'document' . '/' . $location['loc1'];
			}

			while (is_array($document_list) && list(,$document) = each($document_list))
			{
				if($document['link'])
				{
					$link_view_file=$document['link'];
					$document['document_name']='link';
					unset($link_to_files);
				}
				else
				{
					if(!$link_to_files)
					{
						$link_view_file = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.view_file', 'document_id'=> $document['document_id'], 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $p_num));
						$link_to_files = $files_url;
					}
				}

				$content[] = array
				(
					'directory'				=> $directory,
					'document_id'				=> $document['document_id'],
					'document_name'				=> $document['document_name'],
					'title'					=> $document['title'],
					'user'					=> $document['user'],
					'doc_type'				=> $document['doc_type'],
					'link_view_file'			=> $link_view_file,
					'link_to_files'				=> $link_to_files,
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.view', 'document_id'=> $document['document_id'], 'from'=> 'list_doc')),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.edit', 'document_id'=> $document['document_id'], 'from'=> 'list_doc')),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.delete', 'document_id'=> $document['document_id'], 'location_code'=> $location_code, 'p_num'=> $p_num)),
					'lang_view_file_statustext'		=> lang('view the document'),
					'lang_view_statustext'			=> lang('view information about the document'),
					'lang_edit_statustext'			=> lang('edit information about the document'),
					'lang_delete_statustext'		=> lang('delete this document'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}


			$table_header[] = array
			(
				'sort_document_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'document_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uidocument.list_doc',
														'entity_id'	=> $this->entity_id,
														'cat_id'	=> $this->cat_id,
														'doc_type'	=> $this->doc_type,
														'p_num'		=> $p_num,
														'location_code'	=> $location_code,
														'filter'	=> $this->filter,
														'query'		=> $this->query,
														'query_location' => $this->query_location
														)
										)),
				'lang_document_name'	=> lang('Document name'),
				'lang_doc_type'		=> lang('Doc type'),
				'lang_user'		=> lang('user'),
				'lang_title'		=> lang('Title'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				);


			$link_data_add = array
			(
				'menuaction'		=> 'property.uidocument.edit',
				'location_code'		=> $location_code,
				'p_entity_id'		=> $this->entity_id,
				'entity_id'		=> $this->entity_id,
				'p_cat_id'		=> $this->cat_id,
				'cat_id'		=> $this->cat_id,
				'p_num'			=> $p_num,
				'from'			=> 'list_doc',
				'bypass'		=> True
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a document'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data_add)
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uidocument.list_doc',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id,
				'p_num'		=> $p_num,
				'doc_type'	=> $this->doc_type,
				'location_code'	=> $location_code,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'query_location'=> $this->query_location
			);


			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'		=> $location,
						'type_id'		=> count(explode('-',$location_code)),
						'no_link'		=> False, // disable lookup links for location type less than type_id
						'tenant'		=> False,
						'lookup_type'		=> 'view',
						'lookup_entity'		=> $lookup_entity,
						'entity_data'		=> $entity_data,
						'link_data'		=> $link_data,
						'query_link'		=> True
						));

//_debug_array($location_data);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'					=> $location_data,
				'link_history'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.list_doc', 'cat_id'=> $this->cat_id)),
				'lang_history_statustext'			=> lang('search for history at this location'),
				'lang_select'					=> lang('select'),
				'lookup_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uiworkorder.edit')),
				'lookup'					=> $lookup,
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($document_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'type'						=> $this->doc_type,
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'doc_type',
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->doc_type,'type' =>'document','order'=>'descr')),

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_status_statustext'			=> lang('Select the status the document belongs to. To do not use a category select NO STATUS'),
				'status_name'					=> 'status_id',
				'lang_no_status'				=> lang('No status'),
				'status_list'					=> $this->bo->select_status_list('filter',$this->status_id),

				'lang_user_statustext'				=> lang('Select the user the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'filter',
				'lang_no_user'					=> lang('No user'),
				'user_list'					=> $this->bocommon->get_user_list_right2('filter',4,$this->filter,$this->acl_location,array('all'),$default=$this->account),

				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_document'				=> $table_header,
				'values_document'				=> $content,
				'table_add'					=> $table_add,
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.index', 'preserve'=> 1)),
				'lang_done'					=> lang('done'),
				'lang_done_statustext'				=> lang('Back to the list')
			);

			$appname	= lang('document');
			$function_msg	= lang('list document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg . ' - ' . $appname_sub;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_document' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function view_file()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = True;
			$GLOBALS['phpgw_info']['flags'][nofooter] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$document_id 		= phpgw::get_var('document_id', 'int');
			$p_num = phpgw::get_var('p_num');


			$values = $this->bo->read_single($document_id);

			if($this->cat_id)
			{
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$file	= $this->fakebase. '/' . 'document' . '/' . $values['location_data']['loc1'] . '/' . $entity['name'] . '/' . $category['name'] . '/' . $p_num . '/' . $values['document_name'];
			}
			else
			{
				$file	= $this->fakebase. '/' . 'document' . '/' .$values['location_data']['loc1'] . '/' . $values['document_name'];
			}

			if($this->bo->vfs->file_exists(array(
				'string' => $file,
				'relatives' => Array(RELATIVE_NONE)
				)))
			{

				$ls_array = $this->bo->vfs->ls (array (
						'string'	=>  $file,
						'relatives' => Array(RELATIVE_NONE),
						'checksubdirs'	=> False,
						'nofiles'	=> True
					)
				);

				$this->bo->vfs->override_acl = 1;

				$document= $this->bo->vfs->read(array(
					'string' => $file,
					'relatives' => Array(RELATIVE_NONE)));

				$this->bo->vfs->override_acl = 0;

				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($ls_array[0]['name'],$ls_array[0]['mime_type'],$ls_array[0]['size']);

				echo $document;
			}
		}


		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$from 			= phpgw::get_var('from');
			$document_id 		= phpgw::get_var('document_id', 'int');
//			$location_code 		= phpgw::get_var('location_code');
			$values			= phpgw::get_var('values');

			if(!$from)
			{
				$from='index';
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('document'));

			$bypass = phpgw::get_var('bypass', 'bool');

			$receipt= $this->bo->create_home_dir();

			if($_POST && !$bypass)
			{
				$insert_record 		= $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity	= $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			else
			{
				$location_code 		= phpgw::get_var('location_code');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id			= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');
				$values['p_entity_id']=$p_entity_id;
				$values['p_cat_id']=$p_cat_id;

				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $this->boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if($location_code)
				{
					$values['location_data'] = $this->bolocation->read_single($location_code,array());
				}
			}

//_debug_array($values);
			if($values[extra]['p_entity_id'])
			{
				$this->entity_id=$values[extra]['p_entity_id'];
				$this->cat_id=$values[extra]['p_cat_id'];
				$p_num=$values['extra']['p_num'];
			}

			if($this->cat_id)
			{
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$values['entity_name']=$entity['name'];
				$values['category_name']=$category['name'];
			}

			if ($values['save'])
			{
				$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');

				if(!$values['link'])
				{
					$values['document_name']=str_replace (' ','_',$_FILES['document_file']['name']);
				}

				if((!$values['document_name'] && !$values['document_name_orig']) && !$values['link'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a file to upload !'));
				}

				if(!$values['doc_type'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					$error_id=true;
				}

				if(!$values['status'])
				{
//					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}
				if(!$values['location'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
				}

				if($values['extra']['p_num'])
				{
					$to_file = $this->fakebase. '/' . 'document' . '/' . $values['location']['loc1'] . '/' . $entity['name'] . '/' . $category['name'] . '/' . $values['extra']['p_num'] . '/' . $values['document_name'];
				}
				else
				{
					$to_file = $this->fakebase. '/' . 'document' . '/' . $values['location']['loc1'] . '/' . $values['document_name'];
				}

				if(!$values['document_name_orig'] && $this->bo->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$receipt['error'][]=array('msg'=>lang('This file already exists !'));
				}

				$receipt=$this->bo->create_document_dir(array('loc1'=>$values['location']['loc1'],'entity_name'=>$entity['name'],'category_name'=>$category['name'], 'p_num'=>$values['extra']['p_num']),$receipt);

				$values['document_id'] = $document_id;

				if(!$receipt['error'])
				{
					if($values['document_name'] && !$values['link'])
					{
						$this->bo->vfs->override_acl = 1;

						if(!$this->bo->vfs->cp (array (
							'from'		=> $_FILES['document_file']['tmp_name'],
							'to'		=> $to_file,
							'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
						}
						$this->bo->vfs->override_acl = 0;
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save($values);
	//					$document_id=$receipt['document_id'];
						$GLOBALS['phpgw']->session->appsession('session_data','document_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uidocument.list_doc', 'location_code'=> implode("-", $values['location']), 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $values['extra']['p_num']));
					}
				}
				else
				{
					$values['document_name']='';
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['location_data'] = $this->bolocation->read_single($location_code,$values['extra']);
					}
					if($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=$_POST['entity_cat_name_'.$values['extra']['p_entity_id']];
					}
				}
			}

			if ($document_id ||(!$receipt['error'] && $values['document_id']))
			{
				$values = $this->bo->read_single($document_id);
				$record_history = $this->bo->read_record_history($document_id);
				$function_msg = lang('Edit document');
			}
			else
			{
				$function_msg = lang('Add document');
			}

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			if ($values['doc_type'])
			{
				$this->doc_type = $values['doc_type'];
			}
			if ($values['location_code'])
			{
				$location_code = $values['location_code'];
			}
/*			if ($values['p_num'])
			{
				$p_num = $values['p_num'];
			}
*/
			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'		=> $values['location_data'],
						'type_id'		=> -1, // calculated from location_types
						'no_link'		=> False, // disable lookup links for location type less than type_id
						'tenant'		=> False,
						'lookup_type'		=> 'form',
						'lookup_entity'		=> $this->bocommon->get_lookup_entity('document'),
						'entity_data'		=> $values['p']
						));


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'	=> $values['vendor_id'],
						'vendor_name'	=> $values['vendor_name']));


			$link_data = array
			(
				'menuaction'	=> 'property.uidocument.edit',
				'document_id'	=> $document_id,
				'from'		=> $from,
				'location_code' => $values['location_code'],
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id,
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_document_date');

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'vendor_data'					=> $vendor_data,
				'record_history'				=> $record_history,
				'table_header_history'				=> $table_header_history,
				'lang_history'					=> lang('History'),
				'lang_no_history'				=> lang('No history'),

				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'				=> lang('Select date'),

				'lang_document_date_statustext'			=> lang('Select date the document was created'),
				'lang_document_date'				=> lang('document date'),
				'value_document_date'				=> $values['document_date'],

				'vendor_data'					=> $vendor_data,
				'location_data'					=> $location_data,
				'location_type'					=> 'form',
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_year'					=> lang('Year'),
				'lang_category'					=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_save_statustext'				=> lang('Save the document'),

				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.' .$from, 'location_code'=> $location_code, 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $p_num, 'preserve'=> 1)),
				'lang_done'					=> lang('done'),
				'lang_done_statustext'				=> lang('Back to the list'),

				'lang_update_file'				=> lang('Update file'),

				'lang_document_id'				=> lang('document ID'),
				'value_document_id'				=> $document_id,

				'lang_document_name'				=> lang('document name'),
				'value_document_name'				=> $values['document_name'],
				'lang_document_name_statustext'			=> lang('Enter document Name'),

				'lang_floor_id'					=> lang('Floor ID'),
				'value_floor_id'				=> $values['floor_id'],
				'lang_floor_statustext'				=> lang('Enter the floor ID'),

				'lang_title'					=> lang('title'),
				'value_title'					=> $values['title'],
				'lang_title_statustext'				=> lang('Enter document title'),

				'lang_version'					=> lang('Version'),
				'value_version'					=> $values['version'],
				'lang_version_statustext'			=> lang('Enter document version'),

				'lang_link'					=> lang('Link'),
				'value_link'					=> $values['link'],
				'lang_link_statustext'				=> lang('Alternative - link instead of uploading a file'),

				'lang_descr_statustext'				=> lang('Enter a description of the document'),
				'lang_descr'					=> lang('Description'),
				'value_descr'					=> $values['descr'],
				'lang_no_cat'					=> lang('Select category'),
				'lang_cat_statustext'				=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[doc_type]',
				'value_cat_id'					=> $values['doc_type'],
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['doc_type'],'type' =>'document','order'=>'descr')),

				'lang_coordinator'				=> lang('Coordinator'),
				'lang_user_statustext'				=> lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'values[coordinator]',
				'lang_no_user'					=> lang('Select coordinator'),
				'user_list'					=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),

				'status_list'					=> $this->bo->select_status_list('select',$values['status']),
				'status_name'					=> 'values[status]',
				'lang_no_status'				=> lang('Select status'),
				'lang_status'					=> lang('Status'),
				'lang_status_statustext'			=> lang('What is the current status of this document ?'),

				'value_location_code'				=> $values['location_code'],

				'branch_list'					=> $this->bo->select_branch_list($values['branch_id']),
				'lang_no_branch'				=> lang('No branch'),
				'lang_branch'					=> lang('branch'),
				'lang_branch_statustext'			=> lang('Select the branch for this document')
			);

			$appname		= lang('document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$location_code = phpgw::get_var('location_code');
			$p_num = phpgw::get_var('p_num');
			$document_id = phpgw::get_var('document_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' 	=> 'property.uidocument.list_doc',
				'location_code'	=> $location_code,
				'p_num'		=> $p_num
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($document_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.delete', 'document_id'=> $document_id, 'location_code'=> $location_code, 'p_num'=> $p_num)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('document');
			$function_msg	= lang('delete document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$from 		= phpgw::get_var('from');
			$document_id 	= phpgw::get_var('document_id', 'int');

			if(!$from)
			{
				$from='index';
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('document'));

			$values = $this->bo->read_single($document_id);
			$function_msg = lang('view document');
			$record_history = $this->bo->read_record_history($document_id);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			if ($values['doc_type'])
			{
				$this->cat_id = $values['doc_type'];
			}

			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> count(explode('-',$values['location_data']['location_code'])),
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'tenant'	=> False,
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('document'),
						'entity_data'	=> $values['p']
						));


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'type'		=> 'view',
						'vendor_id'	=> $values['vendor_id'],
						'vendor_name'	=> $values['vendor_name']));


			$link_data = array
			(
				'menuaction'	=> 'property.uidocument.edit',
				'document_id'	=> $document_id
			);

			$data = array
			(
				'vendor_data'					=> $vendor_data,
				'record_history'				=> $record_history,
				'table_header_history'				=> $table_header_history,
				'lang_history'					=> lang('History'),
				'lang_no_history'				=> lang('No history'),

				'lang_document_date'				=> lang('document date'),
				'value_document_date'				=> $values['document_date'],

				'vendor_data'					=> $vendor_data,
				'location_data'					=> $location_data,
				'location_type'					=> 'form',
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.' .$from, 'location_code'=> $values['location_code'], 'entity_id'=> $values['p_entity_id'], 'cat_id'=> $values['p_cat_id'], 'preserve'=> 1)),
				'lang_year'					=> lang('Year'),
				'lang_category'					=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),

				'lang_update_file'				=> lang('Update file'),

				'lang_document_id'				=> lang('document ID'),
				'value_document_id'				=> $document_id,

				'lang_document_name'				=> lang('document name'),
				'value_document_name'				=> $values['document_name'],
				'lang_document_name_statustext'			=> lang('Enter document Name'),

				'lang_floor_id'					=> lang('Floor ID'),
				'value_floor_id'				=> $values['floor_id'],
				'lang_floor_statustext'				=> lang('Enter the floor ID'),

				'lang_title'					=> lang('title'),
				'value_title'					=> $values['title'],
				'lang_title_statustext'				=> lang('Enter document title'),

				'lang_version'					=> lang('Version'),
				'value_version'					=> $values['version'],
				'lang_version_statustext'			=> lang('Enter document version'),

				'lang_descr_statustext'				=> lang('Enter a description of the document'),
				'lang_descr'					=> lang('Description'),
				'value_descr'					=> $values['descr'],
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Save the document'),
				'lang_no_cat'					=> lang('Select category'),
				'lang_cat_statustext'				=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[doc_type]',
				'value_cat_id'					=> $values['doc_type'],
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['doc_type'],'type' =>'document','order'=>'descr')),

				'lang_coordinator'				=> lang('Coordinator'),
				'lang_user_statustext'				=> lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'values[coordinator]',
				'lang_no_user'					=> lang('Select coordinator'),
				'user_list'					=> $this->bocommon->get_user_list('select',$values['coordinator'],$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'status_list'					=> $this->bo->select_status_list('select',$values['status']),
				'status_name'					=> 'values[status]',
				'lang_no_status'				=> lang('Select status'),
				'lang_status'					=> lang('Status'),
				'lang_status_statustext'			=> lang('What is the current status of this document ?'),


				'branch_list'					=> $this->bo->select_branch_list($values['branch_id']),
				'lang_no_branch'				=> lang('No branch'),
				'lang_branch'					=> lang('branch'),
				'lang_branch_statustext'			=> lang('Select the branch for this document'),

				'edit_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.edit', 'document_id'=> $document_id, 'from'=> $from)),
				'lang_edit_statustext'				=> lang('Edit this entry'),
				'lang_edit'					=> lang('Edit')
			);

			$appname = lang('document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
