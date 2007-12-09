<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id: class.uiadmin.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_uiadmin
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
			'list_acl'		=> True,
			'aclprefs'		=> True,
			'edit_id'		=> True,
			'contact_info'	=> True
		);

		function hrm_uiadmin()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo					= CreateObject('hrm.boadmin',true);
			$this->bocommon				= CreateObject('hrm.bocommon');

			$this->acl_app				= $this->bo->acl_app;
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
				'start'			=> $this->start,
				'query'			=> $this->query,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'cat_id'		=> $this->cat_id,
				'location'		=> $this->location,
				'granting_group'		=> $this->granting_group,
				'allrows'	=> $this->allrows
			);

			$this->bo->save_sessiondata($data);
		}

		function aclprefs()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin','nextmatchs',
										'search_field'));

			$values 	= phpgw::get_var('values');
			$r_processed	= phpgw::get_var('processed');
			$set_permission = phpgw::get_var('set_permission', 'bool');

			if($set_permission)
			{
				$receipt	= $this->bo->set_permission($values,$r_processed,true);
			}

			if ($this->location)
			{
				if($this->cat_id=='accounts')
				{
					$user_list = $this->bo->get_user_list('accounts',true);
				}

				while (is_array($user_list) && list(,$user) = each($user_list))
				{
					$processed[] = $user['account_id'];
					$users[] = array
					(
						'account_id'			=> $user['account_id'],
						'lid'					=> $user['account_lid'],
						'name'					=> $user['account_firstname'] . ' ' . $user['account_lastname'],
						'read_right'				=> $user['right'][1],
						'add_right'				=> $user['right'][2],
						'edit_right'				=> $user['right'][4],
						'delete_right'				=> $user['right'][8],
						'read_mask'				=> $user['mask'][1],
						'add_mask'				=> $user['mask'][2],
						'edit_mask'				=> $user['mask'][4],
						'delete_mask'				=> $user['mask'][8],
						'read_result'				=> $user['result'][1],
						'add_result'				=> $user['result'][2],
						'edit_result'				=> $user['result'][4],
						'delete_result'				=> $user['result'][8],
						'lang_right'				=> lang('right'),
						'lang_mask'				=> lang('mask'),
						'lang_result'				=> lang('result'),
						'lang_read'				=> lang('Read'), 				//1
						'lang_add'				=> lang('Add'), 				//2
						'lang_edit'				=> lang('Edit'),				//4
						'lang_delete'				=> lang('Delete'),				//8
						'type'					=> 'users'
					);
				}

				if($this->cat_id=='groups')
				{
					$group_list = $this->bo->get_user_list('groups',true);
				}


				while (is_array($group_list) && list(,$group) = each($group_list))
				{
					$processed[] = $group['account_id'];
					$groups[] = array
					(
						'account_id'			=> $group['account_id'],
						'lid'					=> $group['account_lid'],
						'name'					=> $group['account_firstname'],
						'read_right'				=> $group['right'][1],
						'add_right'				=> $group['right'][2],
						'edit_right'				=> $group['right'][4],
						'delete_right'				=> $group['right'][8],
						'read_mask'				=> $group['mask'][1],
						'add_mask'				=> $group['mask'][2],
						'edit_mask'				=> $group['mask'][4],
						'delete_mask'				=> $group['mask'][8],
						'read_result'				=> $group['result'][1],
						'add_result'				=> $group['result'][2],
						'edit_result'				=> $group['result'][4],
						'delete_result'				=> $group['result'][8],
						'lang_right'				=> lang('right'),
						'lang_mask'				=> lang('mask'),
						'lang_result'				=> lang('result'),
						'lang_read'				=> lang('Read'), 				//1
						'lang_add'				=> lang('Add'), 				//2
						'lang_edit'				=> lang('Edit'),				//4
						'lang_delete'				=> lang('Delete'),				//8
						'type'					=> 'groups'
					);
				}
//_debug_array($groups);

				$processed=@implode("_", $processed);
			}


			$table_header[] = array
			(
				'lang_read'				=> lang('Read'), 				//1
				'lang_add'				=> lang('Add'), 				//2
				'lang_edit'				=> lang('Edit'),				//4
				'lang_delete'				=> lang('Delete'),				//8
				'lang_manager'				=> lang('Manager')				//16
			);


			$link_data = array
			(
				'menuaction'	=> 'hrm.uiadmin.aclprefs',
						'sort'				=>$this->sort,
						'order'				=>$this->order,
						'cat_id'			=>$this->cat_id,
						'filter'			=>$this->filter,
						'query'				=>$this->query,
						'module'			=> $this->location,
						'granting_group'	=> $this->granting_group,
						'acl_app'			=> $this->acl_app
			);

			if(!$this->location)
			{
				$receipt['error'][] = array('msg' => lang('select a location!'));
			}

			$num_records = count($user_list) + count($group_list);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/preferences/index.php'),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'processed'						=> $processed,
				'location'						=> $this->location,
				'links'							=> $links,
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> $num_records,
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php','menuaction='.'hrm.uiadmin.aclprefs'),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'lang_groups'					=> lang('groups'),
				'lang_users'					=> lang('users'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the permissions belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bo->select_category_list('filter',$this->cat_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'cat_id'						=> $this->cat_id,
				'permission'					=> False,
				'grant'							=> 1,

				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_permission'		=> $table_header,
				'values_groups'					=> $groups,
				'values_users'					=> $users,
				'lang_no_location'				=> lang('No location'),
				'lang_location_statustext'		=> lang('Select submodule'),
				'select_name_location'			=> 'module',
				'location_list'					=> $this->bo->select_location('filter',$this->location,True),

				'is_admin'						=> $GLOBALS['phpgw_info']['user']['apps']['admin'],
				'lang_group_statustext'			=> lang('Select the granting group. To do not use a granting group select NO GRANTING GROUP'),
				'select_group_name'				=> 'granting_group',
				'lang_no_group'					=> lang('No granting group'),
				'group_list'					=> $this->bocommon->get_group_list('filter',$this->granting_group,$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
			);

			$appname						= lang('preferences');
			$function_msg					= lang('set grants');
			$owner_name = $GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->accounts->account_id);		// get owner name for title

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg . ': ' . $owner_name;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_permission' => $data));
			$this->save_sessiondata();
		}

		function list_acl()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin','nextmatchs',
										'search_field'));

			$values 		= phpgw::get_var('values');
			$r_processed	= phpgw::get_var('processed');

			$set_permission = phpgw::get_var('set_permission', 'bool');

			if($set_permission)
			{
				$receipt	= $this->bo->set_permission($values,$r_processed);
			}

			if ($this->location)
			{
				if($this->cat_id=='accounts')
				{
					$user_list = $this->bo->get_user_list('accounts');
				}

				if (isSet($user_list) AND is_array($user_list))
				{
					foreach($user_list as $user)
					{
						$processed[] = $user['account_id'];
						$users[] = array
						(
							'account_id'				=> $user['account_id'],
							'lid'					=> $user['account_lid'],
							'name'					=> $user['account_firstname'] . ' ' . $user['account_lastname'] . ' [' . $user['account_lid'] . ']',
							'read_right'				=> $user['right'][1],
							'add_right'				=> $user['right'][2],
							'edit_right'				=> $user['right'][4],
							'delete_right'				=> $user['right'][8],
							'manage_right'				=> $user['right'][16],
							'read_mask'				=> $user['mask'][1],
							'add_mask'				=> $user['mask'][2],
							'edit_mask'				=> $user['mask'][4],
							'delete_mask'				=> $user['mask'][8],
							'manage_mask'				=> $user['mask'][16],
							'read_result'				=> $user['result'][1],
							'add_result'				=> $user['result'][2],
							'edit_result'				=> $user['result'][4],
							'delete_result'				=> $user['result'][8],
							'manage_result'				=> $user['result'][16],
							'lang_right'				=> lang('right'),
							'lang_mask'				=> lang('mask'),
							'lang_result'				=> lang('result'),
							'lang_read'				=> lang('Read'), 				//1
							'lang_add'				=> lang('Add'), 				//2
							'lang_edit'				=> lang('Edit'),				//4
							'lang_delete'				=> lang('Delete'),				//8
							'lang_manage'				=> lang('Manage'),				//16
							'type'					=> 'users'
						);
					}
				}

				if($this->cat_id=='groups')
				{
					$group_list = $this->bo->get_user_list('groups');
				}

				if (isSet($group_list) AND is_array($group_list))
				{
					foreach($group_list as $group)
					{
						$processed[] = $group['account_id'];
						$groups[] = array
						(
							'account_id'				=> $group['account_id'],
							'lid'					=> $group['account_lid'],
							'name'					=> $group['account_firstname'],
							'read_right'				=> $group['right'][1],
							'add_right'				=> $group['right'][2],
							'edit_right'				=> $group['right'][4],
							'delete_right'				=> $group['right'][8],
							'manage_right'				=> $group['right'][16],
							'read_mask'				=> $group['mask'][1],
							'add_mask'				=> $group['mask'][2],
							'edit_mask'				=> $group['mask'][4],
							'delete_mask'				=> $group['mask'][8],
							'manage_mask'				=> $group['mask'][16],
							'read_result'				=> $group['result'][1],
							'add_result'				=> $group['result'][2],
							'edit_result'				=> $group['result'][4],
							'delete_result'				=> $group['result'][8],
							'manage_result'				=> $group['result'][16],
							'lang_right'				=> lang('right'),
							'lang_mask'				=> lang('mask'),
							'lang_result'				=> lang('result'),
							'lang_read'				=> lang('Read'), 				//1
							'lang_add'				=> lang('Add'), 				//2
							'lang_edit'				=> lang('Edit'),				//4
							'lang_delete'				=> lang('Delete'),				//8
							'lang_manage'				=> lang('Manage'),				//16
							'type'					=> 'groups'
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
											'var'	=>	'account_lid',
											'order'	=>	$this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uiadmin.list_acl',
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'module'		=> $this->location,
																	'submodule_id'	=>$this->submodule_id)
										)),
				'sort_lastname'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=>	$this->sort,
											'var'	=>	'account_lastname',
											'order'	=>	$this->order,
											'extra'	=>	array('menuaction'	=> 'hrm.uiadmin.list_acl',
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'module'		=> $this->location,
																	'submodule_id'	=>$this->submodule_id)
										)),
				'sort_firstname'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=>	$this->sort,
											'var'	=>	'account_firstname',
											'order'	=>	$this->order,
											'extra'	=>	array('menuaction'	=> 'hrm.uiadmin.list_acl',
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'module'		=> $this->location,
																	'submodule_id'	=>$this->submodule_id)
										)),


				'lang_values'				=> lang('values'),
				'lang_read'					=> lang('Read'), 				//1
				'lang_add'					=> lang('Add'), 				//2
				'lang_edit'					=> lang('Edit'),				//4
				'lang_delete'				=> lang('Delete'),				//8
				'lang_manager'				=> lang('Manager'),				//16
			);

			$link_data = array
			(
				'menuaction'	=> 'hrm.uiadmin.list_acl',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'filter'		=>$this->filter,
						'query'			=>$this->query,
						'module'		=> $this->location

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

				$num_records = count($user_list) + count($group_list);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

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
				'processed'						=> $processed,
				'location'						=> $this->location,
				'links'							=> $links,

				'num_records'					=> $num_records,
				'all_records'					=> $this->bo->total_records,
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
				'values_groups'					=> $groups,
				'values_users'					=> $users,
				'lang_groups'					=> lang('groups'),
				'lang_users'					=> lang('users'),

				'lang_no_location'				=> lang('No location'),
				'lang_location_statustext'		=> lang('Select submodule'),
				'select_name_location'			=> 'module',
				'location_list'					=> $this->bo->select_location('filter',$this->location,False)
			);

			$appname						= lang('permission');
			$function_msg					= lang('set permission');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_permission' => $data));
			$this->save_sessiondata();
		}

		function contact_info()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin'));

			$values		= phpgw::get_var('values');

			if ($values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id=$this->filter;
				$GLOBALS['phpgw']->preferences->read_repository();

				if ($values['old_email'] != $values['email'])
				{
					$GLOBALS['phpgw']->preferences->add('hrm',"email",$values['email'],'user');
					$receipt['message'][] = array('msg' => lang('Users email is updated'));
				}
				if ($values['old_phone'] != $values['phone'])
				{
					$GLOBALS['phpgw']->preferences->add('hrm',"cellphone",$values['phone'],'user');
					$receipt['message'][] = array('msg' => lang('Users phone is updated'));
				}
				if ($values['old_approval_from'] != $values['approval_from'])
				{
					$GLOBALS['phpgw']->preferences->add('hrm',"approval_from",$values['approval_from'],'user');
					$receipt['message'][] = array('msg' => lang('Approval from is updated'));
				}
				if ($values['old_default_vendor_category'] != $values['default_vendor_category'])
				{
					$GLOBALS['phpgw']->preferences->add('hrm',"default_vendor_category",$values['default_vendor_category'],'user');
					$receipt['message'][] = array('msg' => lang('default vendor category is updated'));
				}
				$GLOBALS['phpgw']->preferences->save_repository();
			}

			if($this->filter)
			{
				$prefs = $this->bocommon->create_preferences('hrm',$this->filter);
			}

			$cats		= CreateObject('phpgwapi.categories');
			$cats->app_name = 'fm_vendor';

			$cat_data	= $cats->formatted_xslt_list(array('selected' => $prefs['default_vendor_category'],'globals' => True, 'link_data' =>array()));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php','menuaction='.'hrm.uiadmin.contact_info'),
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
				'approval_from'					=> $this->bocommon->get_user_list('select',$prefs['approval_from'],$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'select_user_name'				=> 'approval_from',
				'lang_approval_from_statustext'	=> lang('Select the users supervisor'),

				'lang_default_vendor_category'		=> lang('default vendor category'),
				'value_old_default_vendor_category'	=> $prefs['default_vendor_category'],
				'vendor_category'					=> $cat_data['cat_list'],
				'select_user_name'					=> 'approval_from',
				'lang_default_vendor_category_statustext'	=> lang('Select default vendor category'),
				'lang_no_cat'						=> lang('No category'),


				'lang_user_statustext'			=> lang('Select the user to edit email'),
				'select_user_name'				=> 'filter',
				'lang_no_user'					=> lang('No user'),
				'value_user_id'					=> $this->filter,
				'user_list'						=> $this->bocommon->get_user_list('filter',$this->filter,$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),
			);

			$appname							= lang('User contact info');
			$function_msg						= lang('edit info');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('contact_info' => $data));
			$this->save_sessiondata();
		}
	}
