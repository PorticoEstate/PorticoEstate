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

	class property_uistandard_2
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
			'index'  => true,
			'view'   => true,
			'edit'   => true,
			'delete' => true
		);

		function property_uistandard_2()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$type	= phpgw::get_var('type');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = phpgw::get_var('menu_selection');

		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bostandard_2',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 				= CreateObject('phpgwapi.acl');
			$this->acl_location			= '.admin';
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
				'allrows'	=> $this->allrows,
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$type	= phpgw::get_var('type');

			$GLOBALS['phpgw']->xslttpl->add_file(array('standard_2','nextmatchs',
										'search_field'));

			$standard_list = $this->bo->read($type);

			while (is_array($standard_list) && list(,$standard) = each($standard_list))
			{

				$content[] = array
				(
					'id'					=> $standard['id'],
					'first'					=> $standard['descr'],
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_2.edit', 'id'=> $standard['id'], 'type'=> $type, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_2.delete', 'id'=> $standard['id'], 'type'=> $type, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
					'lang_view_standardtext'		=> lang('view the standard'),
					'lang_edit_standardtext'		=> lang('edit the standard'),
					'lang_delete_standardtext'		=> lang('delete the standard'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'		=> lang('Descr'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uistandard_2.index',
														'type'	=>$type,
														'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
														)
										)),
				'lang_id'		=> lang('standard id'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_standardtext'	=> lang('add a standard'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_2.edit', 'type'=> $type, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
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


			$data = array
			(
				'allow_allrows'				=> true,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($standard_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_2.index', 'type'=> $type)),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_standardtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_standardtext'	=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add
			);

			$appname	= lang($type);
			$function_msg	= lang('list '.$type.' standard');

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

			$type		= phpgw::get_var('type');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('standard_2'));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				else
				{
					$id =	$values['id'];
				}


				$receipt = $this->bo->save($values,$action,$type);
			}

			if ($id)
			{
				$standard = $this->bo->read_single($id,$type);
				$function_msg = lang('edit standard');
				$action='edit';
			}
			else
			{
				$function_msg	= lang('add standard');
				$action		='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uistandard_2.edit',
				'id'		=> $id,
				'type'		=> $type,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);
//_debug_array($link_data);
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_2.index', 'type'=> $type, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_id'					=> lang('standard ID'),
				'lang_name'					=> lang('name'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,
				'value_name'					=> $standard['name'],
				'value_generaladdress'				=> $standard['general_address'],
				'lang_id_standardtext'				=> lang('Enter the standard ID'),
				'lang_descr_standardtext'			=> lang('Enter a description the standard'),
				'lang_generaladdress_standardtext'		=> lang('Enter the general address'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the standard'),
				'type_id'					=> $standard['type_id'],
				'location_code'					=> $standard['location_code'],
				'value_descr'					=> $standard['descr']
			);

			$appname	= lang($type);

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

			$type	= phpgw::get_var('type');
			$id		= phpgw::get_var('id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uistandard_2.index',
				'type' => $type,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id,$type);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_2.delete', 'id'=> $id, 'type'=>$type, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang($type);
			$function_msg	= lang('delete '.$type.' standard');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}

