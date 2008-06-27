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

	class property_uiasync
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

		function property_uiasync()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::async';

		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('property.boasync',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
	//		$this->allrows				= $this->bo->allrows;

			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
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
	//			'allrows'	=> $this->allrows,
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('async','nextmatchs','search_field'));

			$method_list = $this->bo->read();

			while (is_array($method_list) && list(,$method) = each($method_list))
			{
				$data_set = unserialize($method['data']);
				$run_link_data = array();
				$run_link_data['menuaction']	= $method['name'];
				$run_link_data['data'] 			= urlencode($method['data']);

				$method_data=array();
				while (is_array($data_set) && list($key,$value) = each($data_set))
				{
					$method_data[] = $key . '=' . $value;
				}

				$content[] = array
				(
					'id'					=> $method['id'],
					'name'					=> $method['name'],
					'first'					=> $method['descr'],
					'data'					=> @implode (',',$method_data),
					'link_run'				=> $GLOBALS['phpgw']->link('/index.php',$run_link_data),
					'link_schedule'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uialarm.edit', 'method_id'=> $method['id'])),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.edit', 'id'=> $method['id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.delete', 'id'=> $method['id'])),
					'lang_schedule_statustext'		=> lang('schedule the method'),
					'lang_run_statustext'			=> lang('Run the method now'),
					'lang_view_statustext'			=> lang('view the method'),
					'lang_edit_statustext'			=> lang('edit the method'),
					'lang_delete_statustext'		=> lang('delete the method'),
					'text_schedule'				=> lang('Schedule'),
					'text_run'				=> lang('Run Now'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'		=> lang('Descr'),
				'lang_data'		=> lang('Data'),
				'lang_schedule'		=> lang('Schedule'),
				'lang_run'		=> lang('Run Now'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiasync.index')
										)),
				'lang_id'		=> lang('method id'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiasync.index')
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a method'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.edit')),
				'lang_done'		=> lang('done'),
				'lang_done_statustext'	=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/admin/index.php')
			);


			$data = array
			(
				'allow_allrows'				=> false,
				'start_record'				=> $this->start,
				'record_limit'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'				=> count($method_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.index')),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add
			);

			$appname	= lang('method');
			$function_msg	= lang('list async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit()
		{
			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('async'));

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

				$data = str_replace(' ' ,'',stripslashes($values['data']));
				$data = stripslashes($values['data']);

				$data= explode(",", $data);

				if(is_array($data))
				{
					foreach($data as $set)
					{
						$set= explode("=", $set);
						$data_set[$set[0]]=$set[1];
					}
				}

				if($values['data'])
				{
					$values['data']=serialize($data_set);
				}

				$receipt = $this->bo->save($values,$action);
				$id = $receipt['id'];
			}

			if ($id)
			{
				$method = $this->bo->read_single($id);
				$data_set = unserialize($method['data']);
				while (is_array($data_set) && list($key,$value) = each($data_set))
				{
					$method_data[] = $key . '=' . $value;
				}

				$method_data= @implode (',',$method_data);
				$function_msg = lang('edit method');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add method');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiasync.edit',
				'id'		=> $id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.index')),
				'lang_id'				=> lang('method ID'),
				'lang_name'				=> lang('Name'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'value_id'				=> $id,
				'value_name'				=> $method['name'],
				'lang_id_statustext'			=> lang('Enter the method ID'),
				'lang_descr_statustext'			=> lang('Enter a description the method'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the method'),
				'type_id'				=> $method['type_id'],
				'location_code'				=> $method['location_code'],
				'value_descr'				=> $method['descr'],
				'value_data'				=> $method_data,
				'lang_data'				=> lang('Data'),
				'lang_data_statustext'			=> lang('Input data for the nethod'),
			);

			$appname	= lang('async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			$id		= phpgw::get_var('id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uiasync.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.delete', 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('async method');
			$function_msg		= lang('delete async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

	}

