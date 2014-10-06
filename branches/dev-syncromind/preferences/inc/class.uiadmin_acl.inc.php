<?php
	/**
	* phpGroupWare - preferences - Advanced Access Control Lists Management User Interface
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package preferences
	* @subpackage acl
 	* @version $Id$
	*/

	/**
	 * Advanced Access Control Lists Management User Interface
	 * @package preferences
	 * @subpackage acl
	 */

	class uiadmin_acl
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
			'aclprefs'		=> True
		);

		/**
		* @constructor
		*/
		function uiadmin_acl()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'preferences';
			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= createObject('preferences.boadmin_acl',true);

			$this->acl_app			= $this->bo->acl_app;
			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->location			= $this->bo->location;
			$this->granting_group	= $this->bo->granting_group;
			$this->allrows			= $this->bo->allrows;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$this->acl_app}::acl";
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
				'granting_group'	=> $this->granting_group,
				'allrows'		=> $this->allrows
			);

			$this->bo->save_sessiondata($data);
		}

		function aclprefs()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_acl','nextmatchs',
										'search_field'));

			$values 	= get_var('values',array('POST'));
			$r_processed	= get_var('processed',array('POST'));
			$set_permission = get_var('set_permission',array('POST'));

			if($set_permission)
			{
				$receipt	= $this->bo->set_permission($values,$r_processed,true);
			}

			$processed = array();
			if ($this->location)
			{
				if ( $this->cat_id == 'accounts' )
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
							'account_id'	=> $user['account_id'],
							'lid'			=> $user['account_lid'],
							'name'			=> $user['account_firstname'] . ' ' . $user['account_lastname'],
							'read_right'	=> isset($user['right'][PHPGW_ACL_READ]) ? $user['right'][PHPGW_ACL_READ] : false,
							'add_right'		=> isset($user['right'][PHPGW_ACL_ADD]) ? $user['right'][PHPGW_ACL_ADD] : false,
							'edit_right'	=> isset($user['right'][PHPGW_ACL_EDIT]) ? $user['right'][PHPGW_ACL_EDIT] : false,
							'delete_right'	=> isset($user['right'][PHPGW_ACL_DELETE]) ? $user['right'][PHPGW_ACL_DELETE] : false,
							'read_mask'		=> isset($user['mask'][PHPGW_ACL_READ]) ? $user['mask'][PHPGW_ACL_READ] : false,
							'add_mask'		=> isset($user['mask'][PHPGW_ACL_ADD]) ? $user['mask'][PHPGW_ACL_ADD] : false,
							'edit_mask'		=> isset($user['mask'][PHPGW_ACL_EDIT]) ? $user['mask'][PHPGW_ACL_EDIT] : false,
							'delete_mask'	=> isset($user['mask'][PHPGW_ACL_DELETE]) ? $user['mask'][PHPGW_ACL_DELETE] : false,
							'read_result'	=> isset($user['result'][PHPGW_ACL_READ]) ? $user['result'][PHPGW_ACL_READ] : false,
							'add_result'	=> isset($user['result'][PHPGW_ACL_ADD]) ? $user['result'][PHPGW_ACL_ADD] : false,
							'edit_result'	=> isset($user['result'][PHPGW_ACL_EDIT]) ? $user['result'][PHPGW_ACL_EDIT] : false,
							'delete_result'	=> isset($user['result'][PHPGW_ACL_DELETE]) ? $user['result'][PHPGW_ACL_DELETE] : false,
							'lang_right'	=> lang('right'),
							'lang_mask'		=> lang('mask'),
							'lang_result'	=> lang('result'),
							'lang_read'		=> lang('Read'), 				//1
							'lang_add'		=> lang('Add'), 				//2
							'lang_edit'		=> lang('Edit'),				//4
							'lang_delete'	=> lang('Delete'),				//8
							'type'			=> 'users'
						);
					}
				}

				if($this->cat_id=='groups')
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
							'account_id'	=> $group['account_id'],
							'lid'			=> $group['account_lid'],
							'name'			=> $group['account_firstname'],
							'read_right'	=> isset($group['right'][PHPGW_ACL_READ]) ? $group['right'][PHPGW_ACL_READ] : false,
							'add_right'		=> isset($group['right'][PHPGW_ACL_ADD]) ? $group['right'][PHPGW_ACL_ADD] : false,
							'edit_right'	=> isset($group['right'][PHPGW_ACL_EDIT]) ? $group['right'][PHPGW_ACL_EDIT] : false,
							'delete_right'	=> isset($group['right'][PHPGW_ACL_DELETE]) ? $group['right'][PHPGW_ACL_DELETE] : false,
							'read_mask'		=> isset($group['mask'][PHPGW_ACL_READ]) ? $group['mask'][PHPGW_ACL_READ] : false,
							'add_mask'		=> isset($group['mask'][PHPGW_ACL_ADD]) ? $group['mask'][PHPGW_ACL_ADD] : false,
							'edit_mask'		=> isset($group['mask'][PHPGW_ACL_EDIT]) ? $group['mask'][PHPGW_ACL_EDIT] : false,
							'delete_mask'	=> isset($group['mask'][PHPGW_ACL_DELETE]) ? $group['mask'][PHPGW_ACL_DELETE] : false,
							'read_result'	=> isset($group['result'][PHPGW_ACL_READ]) ? $group['result'][PHPGW_ACL_READ] : false,
							'add_result'	=> isset($group['result'][PHPGW_ACL_ADD]) ? $group['result'][PHPGW_ACL_ADD] : false,
							'edit_result'	=> isset($group['result'][PHPGW_ACL_EDIT]) ? $group['result'][PHPGW_ACL_EDIT] : false,
							'delete_result'	=> isset($group['result'][PHPGW_ACL_DELETE]) ? $group['result'][PHPGW_ACL_DELETE] : false,
							'lang_right'	=> lang('right'),
							'lang_mask'		=> lang('mask'),
							'lang_result'	=> lang('result'),
							'lang_read'		=> lang('Read'), 				//1
							'lang_add'		=> lang('Add'), 				//2
							'lang_edit'		=> lang('Edit'),				//4
							'lang_delete'	=> lang('Delete'),				//8
							'type'			=> 'groups'
						);
					}
				}
				//_debug_array($groups);
				$processed = implode("_", $processed);
			}


			$table_header[] = array
			(
				'lang_read'		=> lang('Read'), 				//1
				'lang_add'		=> lang('Add'), 				//2
				'lang_edit'		=> lang('Edit'),				//4
				'lang_delete'		=> lang('Delete'),				//8
				'lang_manager'		=> lang('Manager')				//16
			);


			$link_data = array
			(
				'menuaction'		=> $this->currentapp . '.uiadmin_acl.aclprefs',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'module'		=> $this->location,
				'granting_group'	=> $this->granting_group,
				'acl_app'		=> $this->acl_app
			);

			if(!$this->location)
			{
				$receipt['error'][] = array('msg' => lang('select a location!'));
			}

			$num_records = 0;
			if(isset($user_list) && is_array($user_list))
			{
				$num_records = count($user_list);
			}
			if(isset($group_list) && is_array($group_list))
			{
				$num_records = $num_records + count($group_list);
			}

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

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
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

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
				'values_groups'					=> (isset($groups)?$groups:''),
				'values_users'					=> (isset($users)?$users:''),
				'lang_no_location'				=> lang('No location'),
				'lang_location_statustext'		=> lang('Select submodule'),
				'select_name_location'			=> 'module',
				'location_list'					=> $this->bo->select_location('filter',$this->location,True),

				'is_admin'						=> $GLOBALS['phpgw_info']['user']['apps']['admin'],
				'lang_group_statustext'			=> lang('Select the granting group. To do not use a granting group select NO GRANTING GROUP'),
				'select_group_name'				=> 'granting_group',
				'lang_no_group'					=> lang('No granting group'),
				'group_list'					=> $this->bo->get_group_list('filter',$this->granting_group,$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'lang_enable_inheritance'       => lang('enable inheritance'), 
                'lang_enable_inheritance_statustext'        => lang('rights are inherited down the hierarchy')
			);

			$appname			= lang('preferences');
			$function_msg		= lang('set grants');
			$owner_name 		= $GLOBALS['phpgw']->accounts->id2name($this->account);		// get owner name for title

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . $this->acl_app . ': ' . $function_msg . ': ' . $owner_name;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_permission' => $data));
			$this->save_sessiondata();
		}

		function list_acl()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_acl','nextmatchs','search_field'));

			$values 		= get_var('values',array('POST'));
			$r_processed	= get_var('processed',array('POST'));

			$set_permission = get_var('set_permission',array('POST'));

			if($set_permission)
			{
				$receipt = $this->bo->set_permission($values, $r_processed);
			}

			$processed = array();
			$user_list = array();
			$group_list = array();
			$users = array();
			$groups = array();
			if ( $this->location )
			{
				if ( $this->cat_id == 'accounts' )
				{
					$user_list = $this->bo->get_user_list('accounts');
				}

				if ( isset($user_list) && is_array($user_list) )
				{
					foreach($user_list as $user)
					{
						$processed[] = $user['account_id'];
						$users[] = array
						(
							'account_id'	=> $user['account_id'],
							'lid'			=> $user['account_lid'],
							'name'			=> $user['account_firstname'] . ' ' . $user['account_lastname'] . ' [' . $user['account_lid'] . ']',
							'read_right'	=> isset($user['right'][PHPGW_ACL_READ]) ? $user['right'][PHPGW_ACL_READ] : false,
							'add_right'		=> isset($user['right'][PHPGW_ACL_ADD]) ? $user['right'][PHPGW_ACL_ADD] : false,
							'edit_right'	=> isset($user['right'][PHPGW_ACL_EDIT]) ? $user['right'][PHPGW_ACL_EDIT] : false,
							'delete_right'	=> isset($user['right'][PHPGW_ACL_DELETE]) ? $user['right'][PHPGW_ACL_DELETE] : false,
							'manage_right'	=> isset($user['right'][PHPGW_ACL_PRIVATE]) ? $user['right'][PHPGW_ACL_PRIVATE] : false, //should be PHPGW_ACL_GROUP_MANAGERS
							'read_mask'		=> isset($user['mask'][PHPGW_ACL_READ]) ? $user['mask'][PHPGW_ACL_READ] : false,
							'add_mask'		=> isset($user['mask'][PHPGW_ACL_ADD]) ? $user['mask'][PHPGW_ACL_ADD] : false,
							'edit_mask'		=> isset($user['mask'][PHPGW_ACL_EDIT]) ? $user['mask'][PHPGW_ACL_EDIT] : false,
							'delete_mask'	=> isset($user['mask'][PHPGW_ACL_DELETE]) ? $user['mask'][PHPGW_ACL_DELETE] : false,
							'manage_mask'	=> isset($user['mask'][PHPGW_ACL_PRIVATE]) ? $user['mask'][PHPGW_ACL_PRIVATE] : false, //should be PHPGW_ACL_GROUP_MANAGERS
							'read_result'	=> isset($user['result'][PHPGW_ACL_READ]) ? $user['result'][PHPGW_ACL_READ] : false,
							'add_result'	=> isset($user['result'][PHPGW_ACL_ADD]) ? $user['result'][PHPGW_ACL_ADD] : false,
							'edit_result'	=> isset($user['result'][PHPGW_ACL_EDIT]) ? $user['result'][PHPGW_ACL_EDIT] : false,
							'delete_result'	=> isset($user['result'][PHPGW_ACL_DELETE]) ? $user['result'][PHPGW_ACL_DELETE] : false,
							'manage_result'	=> isset($user['result'][PHPGW_ACL_PRIVATE]) ? $user['result'][PHPGW_ACL_PRIVATE] : false, //should be PHPGW_ACL_GROUP_MANAGERS
							'lang_right'	=> lang('right'),
							'lang_mask'		=> lang('mask'),
							'lang_result'	=> lang('result'),
							'lang_read'		=> lang('Read'), 				//1
							'lang_add'		=> lang('Add'), 				//2
							'lang_edit'		=> lang('Edit'),				//4
							'lang_delete'	=> lang('Delete'),				//8
							'lang_manage'	=> lang('Manage'),				//16
							'type'			=> 'users'
						);
					}
				}

				if ( $this->cat_id == 'groups')
				{
					$group_list = $this->bo->get_user_list('groups');
				}

				if ( isset($group_list) && is_array($group_list))
				{
					foreach($group_list as $group)
					{
						$processed[] = $group['account_id'];
						$groups[] = array
						(
							'account_id'	=> $group['account_id'],
							'lid'			=> $group['account_lid'],
							'name'			=> $group['account_firstname'],
							'read_right'	=> isset($group['right'][PHPGW_ACL_READ]) ? $group['right'][PHPGW_ACL_READ] : false,
							'add_right'		=> isset($group['right'][PHPGW_ACL_ADD]) ? $group['right'][PHPGW_ACL_ADD] : false,
							'edit_right'	=> isset($group['right'][PHPGW_ACL_EDIT]) ? $group['right'][PHPGW_ACL_EDIT] : false,
							'delete_right'	=> isset($group['right'][PHPGW_ACL_DELETE]) ? $group['right'][PHPGW_ACL_DELETE] : false,
							'manage_right'	=> isset($group['right'][PHPGW_ACL_PRIVATE]) ? $group['right'][PHPGW_ACL_PRIVATE] : false, //should be PHPGW_ACL_GROUP_MANAGERS
							'read_mask'		=> isset($group['mask'][PHPGW_ACL_READ]) ? $group['mask'][PHPGW_ACL_READ] : false,
							'add_mask'		=> isset($group['mask'][PHPGW_ACL_ADD]) ? $group['mask'][PHPGW_ACL_ADD] : false,
							'edit_mask'		=> isset($group['mask'][PHPGW_ACL_EDIT]) ? $group['mask'][PHPGW_ACL_EDIT] : false,
							'delete_mask'	=> isset($group['mask'][PHPGW_ACL_DELETE]) ? $group['mask'][PHPGW_ACL_DELETE] : false,
							'manage_mask'	=> isset($group['mask'][PHPGW_ACL_PRIVATE]) ? $group['mask'][PHPGW_ACL_PRIVATE] : false, //should be PHPGW_ACL_GROUP_MANAGERS
							'read_result'	=> isset($group['result'][PHPGW_ACL_READ]) ? $group['result'][PHPGW_ACL_READ] : false,
							'add_result'	=> isset($group['result'][PHPGW_ACL_ADD]) ? $group['result'][PHPGW_ACL_ADD] : false,
							'edit_result'	=> isset($group['result'][PHPGW_ACL_EDIT]) ? $group['result'][PHPGW_ACL_EDIT] : false,
							'delete_result'	=> isset($group['result'][PHPGW_ACL_DELETE]) ? $group['result'][PHPGW_ACL_DELETE] : false,
							'manage_result'	=> isset($group['result'][PHPGW_ACL_PRIVATE]) ? $group['result'][PHPGW_ACL_PRIVATE] : false, //should be PHPGW_ACL_GROUP_MANAGERS
							'lang_right'	=> lang('right'),
							'lang_mask'		=> lang('mask'),
							'lang_result'	=> lang('result'),
							'lang_read'		=> lang('Read'), 				//1
							'lang_add'		=> lang('Add'), 				//2
							'lang_edit'		=> lang('Edit'),				//4
							'lang_delete'	=> lang('Delete'),				//8
							'lang_manage'	=> lang('Manage'),				//16
							'type'			=> 'groups'
						);
					}
				}
				$processed = implode('_', $processed);
			}

			$table_header[] = array
			(
				'sort_lid'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'account_lid',
											'order'	=>	$this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp . '.uiadmin_acl.list_acl',
																	'acl_app' 	=> $this->acl_app,
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'module'	=> $this->location,
																	'submodule_id'	=>$this->submodule_id)
										)),
				'sort_lastname'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=>	$this->sort,
											'var'	=>	'account_lastname',
											'order'	=>	$this->order,
											'extra'	=>	array('menuaction'	=> $this->currentapp . '.uiadmin_acl.list_acl',
																	'acl_app' 	=> $this->acl_app,
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'module'	=> $this->location,
																	'submodule_id'	=>$this->submodule_id)
										)),
				'sort_firstname'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=>	$this->sort,
											'var'	=>	'account_firstname',
											'order'	=>	$this->order,
											'extra'	=>	array('menuaction'	=> $this->currentapp . '.uiadmin_acl.list_acl',
																	'acl_app' 	=> $this->acl_app,
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'module'	=> $this->location,
																	'submodule_id'	=>$this->submodule_id)
										)),


				'lang_values'				=> lang('values'),
				'lang_read'				=> lang('Read'), 				//1
				'lang_add'				=> lang('Add'), 				//2
				'lang_edit'				=> lang('Edit'),				//4
				'lang_delete'				=> lang('Delete'),				//8
				'lang_manager'				=> lang('Manager'),				//16
			);

			$link_data = array
			(
				'menuaction'	=> 'preferences.uiadmin_acl.list_acl',
				'acl_app' 		=> $this->acl_app,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
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

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

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
				'location_list'					=> $this->bo->select_location('filter',$this->location,False),
				'lang_enable_inheritance'       => lang('enable inheritance'), 
                'lang_enable_inheritance_statustext'        => lang('rights are inherited down the hierarchy')
			);

			$appname		= lang('permission');
			$function_msg		= lang('set permission');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ' - ' . $this->acl_app . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_permission' => $data));
			$this->save_sessiondata();
		}
	}
