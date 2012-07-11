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

	class property_uiadmin
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $submodule_id;
		var $permission;
		var $sub;
		var $currentapp;

		var $public_functions = array
			(
				'list_acl'		=> true,
				'aclprefs'		=> true,
				'edit_id'		=> true,
				'contact_info'	=> true
			);

		function property_uiadmin()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property';

			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo					= CreateObject('property.boadmin',true);
			$this->bopreferences		= createObject('preferences.boadmin_acl',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

	//		$this->acl_app				= $this->bo->acl_app;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->location				= $this->bo->location;
			$this->granting_group		= $this->bo->granting_group;
			$this->allrows				= $this->bo->allrows;


		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'				=> $this->start,
					'query'				=> $this->query,
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'filter'			=> $this->filter,
					'cat_id'			=> $this->cat_id,
					'location'			=> $this->location,
					'granting_group'	=> $this->granting_group,
					'allrows'			=> $this->allrows
				);

			$this->bo->save_sessiondata($data);
		}

		function aclprefs()
		{
			if (!isset($GLOBALS['phpgw_info']['user']['apps']['preferences']))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin','nextmatchs',
				'search_field'));

			$values 			= phpgw::get_var('values');
			$r_processed		= phpgw::get_var('processed');
	//		$acl_app			= get_var('acl_app',array('GET'));
			$set_permission 	= phpgw::get_var('set_permission', 'bool');

			if($set_permission)
			{
				$receipt	= $this->bo->set_permission($values,$r_processed,true);
			}

			if ($this->location)
			{
				if(!$this->cat_id || $this->cat_id=='accounts')
				{
					$user_list = $this->bo->get_user_list('accounts',true);
				}

				if(isset($user_list) && is_array($user_list))
				{
					while (is_array($user_list) && list(,$user) = each($user_list))
					{
						$processed[] = $user['account_id'];
						$users[] = array
							(
								'account_id'			=> $user['account_id'],
								'lid'					=> $user['account_lid'],
								'name'					=> $user['account_firstname'] . ' ' . $user['account_lastname'],
								'read_right'			=> (isset($user['right'][1])?$user['right'][1]:''),
								'add_right'				=> (isset($user['right'][2])?$user['right'][2]:''),
								'edit_right'			=> (isset($user['right'][4])?$user['right'][4]:''),
								'delete_right'			=> (isset($user['right'][8])?$user['right'][8]:''),
								'read_mask'				=> (isset($user['mask'][1])?$user['mask'][1]:''),
								'add_mask'				=> (isset($user['mask'][2])?$user['mask'][2]:''),
								'edit_mask'				=> (isset($user['mask'][4])?$user['mask'][4]:''),
								'delete_mask'			=> (isset($user['mask'][8])?$user['mask'][8]:''),
								'read_result'			=> (isset($user['result'][1])?$user['result'][1]:''),
								'add_result'			=> (isset($user['result'][2])?$user['result'][2]:''),
								'edit_result'			=> (isset($user['result'][4])?$user['result'][4]:''),
								'delete_result'			=> (isset($user['result'][8])?$user['result'][8]:''),
								'lang_right'			=> lang('right'),
								'lang_mask'				=> lang('mask'),
								'lang_result'			=> lang('result'),
								'lang_read'				=> lang('Read'), 		//1
								'lang_add'				=> lang('Add'), 		//2
								'lang_edit'				=> lang('Edit'),		//4
								'lang_delete'			=> lang('Delete'),		//8
								'type'					=> 'users'
							);
					}
				}

				if(!$this->cat_id || $this->cat_id=='groups')
				{
					$group_list = $this->bo->get_user_list('groups',true);

				}


				if(isset($group_list) && is_array($group_list))
				{
					while (is_array($group_list) && list(,$group) = each($group_list))
					{
						$processed[] = $group['account_id'];
						$groups[] = array
							(
								'account_id'			=> $group['account_id'],
								'lid'					=> $group['account_lid'],
								'name'					=> $group['account_firstname'],
								'read_right'			=> (isset($group['right'][1])?$group['right'][1]:''),
								'add_right'				=> (isset($group['right'][2])?$group['right'][2]:''),
								'edit_right'			=> (isset($group['right'][4])?$group['right'][4]:''),
								'delete_right'			=> (isset($group['right'][8])?$group['right'][8]:''),
								'read_mask'				=> (isset($group['mask'][1])?$group['mask'][1]:''),
								'add_mask'				=> (isset($group['mask'][2])?$group['mask'][2]:''),
								'edit_mask'				=> (isset($group['mask'][4])?$group['mask'][4]:''),
								'delete_mask'			=> (isset($group['mask'][8])?$group['mask'][8]:''),
								'read_result'			=> (isset($group['result'][1])?$group['result'][1]:''),
								'add_result'			=> (isset($group['result'][2])?$group['result'][2]:''),
								'edit_result'			=> (isset($group['result'][4])?$group['result'][4]:''),
								'delete_result'			=> (isset($group['result'][8])?$group['result'][8]:''),
								'lang_right'			=> lang('right'),
								'lang_mask'				=> lang('mask'),
								'lang_result'			=> lang('result'),
								'lang_read'				=> lang('Read'), 		//1
								'lang_add'				=> lang('Add'), 		//2
								'lang_edit'				=> lang('Edit'),		//4
								'lang_delete'			=> lang('Delete'),		//8
								'type'					=> 'groups'
							);
					}
				}

				$processed=@implode("_", $processed);
			}

			$table_header[] = array
				(
					'lang_read'			=> lang('Read'), 	//1
					'lang_add'			=> lang('Add'), 	//2
					'lang_edit'			=> lang('Edit'),	//4
					'lang_delete'		=> lang('Delete'),	//8
					'lang_manager'		=> lang('Manager')	//16
				);


			$link_data = array
				(
					'menuaction'		=> 'property.uiadmin.aclprefs',
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'cat_id'			=> $this->cat_id,
					'filter'			=> $this->filter,
					'query'				=> $this->query,
					'module'			=> $this->location,
					'granting_group'	=> $this->granting_group,
					'acl_app'			=> $acl_app
				);

			if(!$this->location)
			{
				$receipt['error'][] = array('msg' => lang('select a location!'));
			}

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$num_records = 0;
			if(isset($user_list) && is_array($user_list))
			{
				$num_records = count($user_list);
			}
			if(isset($group_list) && is_array($group_list))
			{
				$num_records = $num_records + count($group_list);
			}

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/preferences/index.php'),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),
					'processed'						=> (isset($processed)?$processed:''),
					'location'						=> $this->location,

					'allow_allrows'					=> false,
					'start_record'					=> $this->start,
					'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
					'num_records'					=> $num_records,
					'all_records'					=> (isset($this->bo->total_records)?$this->bo->total_records:''),
					'link_url'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin.aclprefs')),
					'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

					'lang_groups'					=> lang('groups'),
					'lang_users'					=> lang('users'),
					'lang_no_cat'					=> lang('no category'),
					'lang_cat_statustext'			=> lang('Select the category the permissions belongs to. To do not use a category select NO CATEGORY'),
					'select_name'					=> 'cat_id',
					'cat_list'						=> $this->bo->select_category_list('filter',$this->cat_id),
					'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'cat_id'						=> $this->cat_id,
					'permission'					=> false,
					'grant'							=> 1,

					'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
					'lang_searchbutton_statustext'	=> lang('Submit the search string'),
					'query'							=> $this->query,
					'lang_search'					=> lang('search'),
					'table_header_permission'		=> $table_header,
					'values_groups'					=> (isset($groups)?$groups:''),
					'values_users'					=> (isset($users)?$users:''),
					'lang_no_location'				=> lang('No location'),
					'lang_location_statustext'		=> lang('Select submodule'),
					'select_name_location'			=> 'module',
					'location_list'					=> $this->bopreferences->select_location('filter',$this->location,true),

					'is_admin'						=> $GLOBALS['phpgw_info']['user']['apps']['admin'],
					'lang_group_statustext'			=> lang('Select the granting group. To do not use a granting group select NO GRANTING GROUP'),
					'select_group_name'				=> 'granting_group',
					'lang_no_group'					=> lang('No granting group'),
					'group_list'					=> $this->bocommon->get_group_list('filter',$this->granting_group,$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
					'lang_enable_inheritance'       => lang('enable inheritance'), 
					'lang_enable_inheritance_statustext'        => lang('rights are inherited down the hierarchy')
				);

			$appname	= lang('preferences');
			$function_msg	= lang('set grants');
			$owner_name = $GLOBALS['phpgw']->accounts->id2name($this->account);		// get owner name for title

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg . ': ' . $owner_name;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_permission' => $data));
			$this->save_sessiondata();
		}

		function list_acl()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::permissions';

			if (!isset($GLOBALS['phpgw_info']['user']['apps']['admin'])) //this one is different
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin','nextmatchs',
				'search_field'));

			$values 		= phpgw::get_var('values');
			$r_processed	= phpgw::get_var('processed');
			$initials		= phpgw::get_var('initials');

			$set_permission = phpgw::get_var('set_permission', 'bool');

			if($set_permission)
			{
				$receipt	= $this->bo->set_permission($values,$r_processed,false,$initials);
			}

			$num_records = 0;
			if ($this->location)
			{
				if($this->cat_id=='accounts')
				{
					$user_list = $this->bo->get_user_list('accounts');
				}

				if (isSet($user_list) AND is_array($user_list))
				{
					$num_records = count($user_list);
					foreach($user_list as $user)
					{
						$processed[] = $user['account_id'];
						$users[] = array
							(
								'account_id'				=> $user['account_id'],
								'lid'						=> $user['account_lid'],
								'name'						=> $user['account_firstname'] . ' ' . $user['account_lastname'] . ' [' . $user['account_lid'] . ']',
								'read_right'				=> (isset($user['right'][1])?$user['right'][1]:''),
								'add_right'					=> (isset($user['right'][2])?$user['right'][2]:''),
								'edit_right'				=> (isset($user['right'][4])?$user['right'][4]:''),
								'delete_right'				=> (isset($user['right'][8])?$user['right'][8]:''),
								'manage_right'				=> (isset($user['right'][16])?$user['right'][16]:''),
								'janitor_right'				=> (isset($user['right'][32])?$user['right'][32]:''),
								'supervisor_right'			=> (isset($user['right'][64])?$user['right'][64]:''),
								'budget_responsible_right'	=> (isset($user['right'][128])?$user['right'][128]:''),
								'read_mask'					=> (isset($user['mask'][1])?$user['mask'][1]:''),
								'add_mask'					=> (isset($user['mask'][2])?$user['mask'][2]:''),
								'edit_mask'					=> (isset($user['mask'][4])?$user['mask'][4]:''),
								'delete_mask'				=> (isset($user['mask'][8])?$user['mask'][8]:''),
								'manage_mask'				=> (isset($user['mask'][16])?$user['mask'][16]:''),
								'janitor_mask'				=> (isset($user['mask'][32])?$user['mask'][32]:''),
								'supervisor_mask'			=> (isset($user['mask'][64])?$user['mask'][64]:''),
								'budget_responsible_mask'	=> (isset($user['mask'][128])?$user['mask'][128]:''),
								'read_result'				=> (isset($user['result'][1])?$user['result'][1]:''),
								'add_result'				=> (isset($user['result'][2])?$user['result'][2]:''),
								'edit_result'				=> (isset($user['result'][4])?$user['result'][4]:''),
								'delete_result'				=> (isset($user['result'][8])?$user['result'][8]:''),
								'manage_result'				=> (isset($user['result'][16])?$user['result'][16]:''),
								'janitor_result'			=> (isset($user['result'][32])?$user['result'][32]:''),
								'supervisor_result'			=> (isset($user['result'][64])?$user['result'][64]:''),
								'budget_responsible_result'	=> (isset($user['result'][128])?$user['result'][128]:''),
								'initials'					=> (isset($user['initials'])?$user['initials']:''),
								'lang_right'				=> lang('right'),
								'lang_mask'					=> lang('mask'),
								'lang_result'				=> lang('result'),
								'lang_read'					=> lang('Read'), 					//1
								'lang_add'					=> lang('Add'), 					//2
								'lang_edit'					=> lang('Edit'),					//4
								'lang_delete'				=> lang('Delete'),					//8
								'lang_manage'				=> lang('Manage'),					//16
								'lang_janitor'				=> lang('Janitor'),					//32
								'lang_supervisor'			=> lang('Supervisor'),				//64
								'lang_budget_responsible'	=> lang('Budget Responsible'),		//128
								'lang_initials'				=> lang('Initials'),
								'type'						=> 'users'
							);
					}
				}

				if($this->cat_id=='groups')
				{
					$group_list = $this->bo->get_user_list('groups');
				}

				if (isSet($group_list) AND is_array($group_list))
				{
					$num_records = count($group_list);
					foreach($group_list as $group)
					{
						$processed[] = $group['account_id'];
						$groups[] = array
							(
								'account_id'				=> $group['account_id'],
								'lid'						=> $group['account_lid'],
								'name'						=> $group['account_firstname'],
								'read_right'				=> (isset($group['right'][1])?$group['right'][1]:''),
								'add_right'					=> (isset($group['right'][2])?$group['right'][2]:''),
								'edit_right'				=> (isset($group['right'][4])?$group['right'][4]:''),
								'delete_right'				=> (isset($group['right'][8])?$group['right'][8]:''),
								'manage_right'				=> (isset($group['right'][16])?$group['right'][16]:''),
								'read_mask'					=> (isset($group['mask'][1])?$group['mask'][1]:''),
								'add_mask'					=> (isset($group['mask'][2])?$group['mask'][2]:''),
								'edit_mask'					=> (isset($group['mask'][4])?$group['mask'][4]:''),
								'delete_mask'				=> (isset($group['mask'][8])?$group['mask'][8]:''),
								'manage_mask'				=> (isset($group['mask'][16])?$group['mask'][16]:''),
								'read_result'				=> (isset($group['result'][1])?$group['result'][1]:''),
								'add_result'				=> (isset($group['result'][2])?$group['result'][2]:''),
								'edit_result'				=> (isset($group['result'][4])?$group['result'][4]:''),
								'delete_result'				=> (isset($group['result'][8])?$group['result'][8]:''),
								'manage_result'				=> (isset($group['result'][16])?$group['result'][16]:''),
					//			'initials'					=> (isset($group['initials'])?$group['initials']:''),
								'lang_right'				=> lang('right'),
								'lang_mask'					=> lang('mask'),
								'lang_result'				=> lang('result'),
								'lang_read'					=> lang('Read'), 		//1
								'lang_add'					=> lang('Add'), 		//2
								'lang_edit'					=> lang('Edit'),		//4
								'lang_delete'				=> lang('Delete'),		//8
								'lang_manage'				=> lang('Manage'),		//16
								'lang_janitor'				=> lang('Janitor'),		//32
								'lang_supervisor'			=> lang('Supervisor'),		//64
								'lang_budget_responsible'	=> lang('Budget Responsible'),	//128
								'lang_initials'				=> lang('Initials'),
								'type'						=> 'groups'
							);
					}
				}


				$processed=@implode("_", $processed);
			}


			$table_header[] = array
				(
					'sort_lid'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'account_lid',
						'order'	=> $this->order,
						'extra'	=> array('menuaction'	=> 'property.uiadmin.list_acl',
						'cat_id'		=> $this->cat_id,
						'query'			=> $this->query,
						'module'		=> $this->location,
						'submodule_id'	=> $this->submodule_id)
					)),
					'sort_lastname'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'account_lastname',
						'order'	=> $this->order,
						'extra'	=> array('menuaction'	=> 'property.uiadmin.list_acl',
						'cat_id'		=> $this->cat_id,
						'query'			=> $this->query,
						'module'		=> $this->location,
						'submodule_id'	=> $this->submodule_id)
					)),
					'sort_firstname'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'account_firstname',
						'order'	=> $this->order,
						'extra'	=> array('menuaction'	=> 'property.uiadmin.list_acl',
						'cat_id'		=> $this->cat_id,
						'query'			=> $this->query,
						'module'		=> $this->location,
						'submodule_id'	=> $this->submodule_id)
					)),


					'lang_values'				=> lang('values'),
					'lang_read'					=> lang('Read'), 				//1
					'lang_add'					=> lang('Add'), 				//2
					'lang_edit'					=> lang('Edit'),				//4
					'lang_delete'				=> lang('Delete'),				//8
					'lang_manager'				=> lang('Manager'),				//16
					'lang_janitor'				=> lang('Janitor'),				//32
					'lang_supervisor'			=> lang('Supervisor'),			//64
					'lang_budget_responsible'	=> lang('Budget Responsible'),	//128
					'lang_initials'				=> lang('Initials')
				);

			$link_data = array
				(
					'menuaction'=> 'property.uiadmin.list_acl',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'cat_id'	=> $this->cat_id,
					'filter'	=> $this->filter,
					'query'		=> $this->query,
					'module'	=> $this->location

				);

			if(!$this->location)
			{
				$receipt['error'][] = array('msg' => lang('select a location!'));
			}

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
				(
					'allrows'						=> $this->allrows,
					'allow_allrows'					=> true,
					'start_record'					=> $this->start,
					'record_limit'					=> $record_limit,

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/admin/index.php'),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),
					'processed'						=> (isset($processed)?$processed:''),
					'location'						=> $this->location,

					'num_records'					=> $num_records,
					'all_records'					=> isset($this->bo->total_records) && $this->bo->total_records ? $this->bo->total_records : 0,
					'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

					'lang_no_cat'					=> lang('no category'),
					'lang_cat_statustext'			=> lang('Select the category the permissions belongs to. To do not use a category select NO CATEGORY'),
					'select_name'					=> 'cat_id',
					'cat_list'						=> $this->bo->select_category_list('filter',$this->cat_id),
					'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'cat_id'						=> $this->cat_id,
					'permission'					=> 1,

					'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
					'lang_searchbutton_statustext'	=> lang('Submit the search string'),
					'query'							=> $this->query,
					'lang_search'					=> lang('search'),
					'table_header_permission'		=> $table_header,
			//		'table_header_acl'				=> $table_header,
			//		'values_acl'					=> $content,
					'values_groups'					=> (isset($groups)?$groups:''),
					'values_users'					=> (isset($users)?$users:''),
					'lang_groups'					=> lang('groups'),
					'lang_users'					=> lang('users'),

					'lang_no_location'				=> lang('No location'),
					'lang_location_statustext'		=> lang('Select submodule'),
					'select_name_location'			=> 'module',
					'location_list'					=> $this->bopreferences->select_location('filter',$this->location,false),
					'lang_enable_inheritance'       => lang('enable inheritance'), 
					'lang_enable_inheritance_statustext'        => lang('rights are inherited down the hierarchy')
				);

			$appname	= lang('permission');
			$function_msg	= lang('set permission');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_permission' => $data));
			$this->save_sessiondata();
		}

		function edit_id()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::id_control';

			if (!$this->acl_edit)
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin'));

			$values		= phpgw::get_var('values');

			if ($values['select'])
			{
				$receipt = $this->bo->edit_id($values);
			}

			$content = $this->bo->read_fm_id();


			$dateformat	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach($content as $i => & $entry)
			{
				$GLOBALS['phpgw']->jqcal->add_listener("date_{$entry['name']}");
				$entry['key_id'] = $i;
				$entry['start_date']	= $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
			}
//_debug_array($content);die();
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin.edit_id')),
					'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
					'lang_submit'			=> lang('submit'),
					'lang_save'				=> lang('Edit'),
					'lang_add_statustext'	=> lang('Edit ID'),
					'lang_done'				=> lang('done'),
					'lang_done_statustext'	=> lang('Back to Admin'),
					'id_values'				=> $content,
				);

			$appname	= lang('ID');
			$function_msg	= lang('edit ID');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_id' => $data));
			$this->save_sessiondata();
		}

		function contact_info()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::user_contact';

			if (!$this->acl_edit)
			{
				$this->bocommon->no_access();
				return;
			}

			$user_id	= phpgw::get_var('user_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin'));

			$values		= phpgw::get_var('values');

			if ($values['save'])
			{
				$GLOBALS['phpgw']->preferences->set_account_id($user_id, true);

				if ($values['old_email'] != $values['email'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"email",$values['email'],'user');
					$receipt['message'][] = array('msg' => lang('Users email is updated'));
				}
				if ($values['old_phone'] != $values['phone'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"cellphone",$values['phone'],'user');
					$receipt['message'][] = array('msg' => lang('Users phone is updated'));
				}
				if ($values['old_approval_from'] != $values['approval_from'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"approval_from",$values['approval_from'],'user');
					$receipt['message'][] = array('msg' => lang('Approval from is updated'));
				}
				if ($values['old_default_vendor_category'] != $values['default_vendor_category'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"default_vendor_category",$values['default_vendor_category'],'user');
					$receipt['message'][] = array('msg' => lang('default vendor category is updated'));
				}
				if ($values['old_default_tts_category'] != $values['default_tts_category'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"tts_category",$values['default_tts_category'],'user');
					$receipt['message'][] = array('msg' => lang('default ticket category is updated'));
				}
				if ($values['old_assigntodefault'] != $values['assigntodefault'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"assigntodefault",$values['assigntodefault'],'user');
					$receipt['message'][] = array('msg' => lang('default ticket assigned to is updated'));
				}
				if ($values['old_groupdefault'] != $values['groupdefault'])
				{
					$GLOBALS['phpgw']->preferences->add('property',"groupdefault",$values['groupdefault'],'user');
					$receipt['message'][] = array('msg' => lang('default ticket group is updated'));
				}
				$GLOBALS['phpgw']->preferences->save_repository();
			}

			if($user_id)
			{
				$prefs = $this->bocommon->create_preferences('property',$user_id);
			}

			$cats		= CreateObject('phpgwapi.categories', -1, 'property', '.vendor');

			$cat_data	= $cats->formatted_xslt_list(array('selected' => $prefs['default_vendor_category'],'globals' => true, 'link_data' =>array()));

			$cats->set_appname('property','.ticket');

			$cat_data_tts	= $cats->formatted_xslt_list(array('selected' => $prefs['tts_category'],'globals' => true, 'link_data' =>array()));

			$acc = & $GLOBALS['phpgw']->accounts;
			$group_list = $acc->get_list('groups',-1,'ASC');
			foreach ( $group_list as $entry )
			{
				$groups_tts[] = array
					(
						'id'	=> $entry->id,
						'name'	=> $entry->lid,
						'selected' => $entry->id == $prefs['groupdefault']
					);
			}

			$account_list = $acc->get_list('accounts',-1,'ASC','account_lastname');

			foreach ( $account_list as $entry )
			{
				if($entry->enabled == true)
				{
					$accounts_tts[] = array
						(
							'id'	=> $entry->id,
							'name'	=> $entry->__toString(),
							'selected' => $entry->id == $prefs['assigntodefault']
						);
				}
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin.contact_info')),
					'done_action'					=> $GLOBALS['phpgw']->link('/admin/index.php'),
					'lang_submit'					=> lang('submit'),
					'lang_save'						=> lang('Edit'),
					'lang_add_statustext'			=> lang('Edit ID'),
					'lang_done'						=> lang('done'),
					'lang_done_statustext'			=> lang('Back to Admin'),

					'lang_email_statustext'			=> lang('Enter the email-address for this user'),

					'lang_user'						=> lang('User'),
					'lang_email'					=> lang('Email'),
					'value_old_email'				=> $prefs['email'],
					'value_email'					=> $prefs['email'],

					'lang_phone'					=> lang('Phone'),
					'value_old_phone'				=> $prefs['cellphone'],
					'value_phone'					=> $prefs['cellphone'],

					'lang_approval_from'			=> lang('Approval from'),
					'value_old_approval_from'		=> $prefs['approval_from'],
					'approval_from'					=> $this->bocommon->get_user_list('select',$prefs['approval_from'],$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

					'select_user_name'				=> 'approval_from',
					'lang_approval_from_statustext'	=> lang('Select the users supervisor'),

					'lang_default_vendor_category'	=> lang('default vendor category'),
					'value_old_default_vendor_category'	=> $prefs['default_vendor_category'],
					'vendor_category'				=> $cat_data['cat_list'],

					'lang_default_tts_category'		=> lang('default ticket categories'),
					'value_old_default_tts_category'	=> $prefs['tts_category'],
					'tts_category'					=> $cat_data_tts['cat_list'],

					'select_user_name'				=> 'approval_from',
					'lang_default_vendor_category_statustext'=> lang('Select default vendor category'),
					'lang_no_cat'					=> lang('No category'),


					'lang_user_statustext'			=> lang('Select the user to edit email'),
					'select_user_name'				=> 'user_id',
					'lang_no_user'					=> lang('No user'),
					'value_user_id'					=> $user_id,
					'user_list'						=> $this->bocommon->get_user_list('filter',$user_id,$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1, true),
					'group_list_tts'				=> $groups_tts,
					'account_list_tts'				=> $accounts_tts,
					'lang_group_select'				=> lang('Default group TTS'),
					'lang_account_select'			=> lang('Default assign to TTS'),
					'value_old_assigntodefault'		=> $prefs['assigntodefault'],
					'value_old_groupdefault'		=> $prefs['groupdefault'],
					'lang_no_assigntodefault'		=> lang('no user'),
					'lang_no_groupdefault'			=> lang('no group'),
				);

			$appname	= lang('User contact info');
			$function_msg	= lang('edit info');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('contact_info' => $data));
			$this->save_sessiondata();
		}
	}
