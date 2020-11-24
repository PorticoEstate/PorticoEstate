<?php
	/**************************************************************************\
	* phpGroupWare - Admin - Global categories                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id$ */
	/* $Source$ */

	class admin_uicategories
	{
		var $bo;
		var $nextmatchs;
		var $xslttpl;

		var $start;
		var $query;
		var $sort;
		var $order;
		var $cat_id;
		var $debug = False;

		public $public_functions = array
		(
			'index'  => True,
			'edit'   => True,
			'delete' => True
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;

			$appname = phpgw::get_var('appname', 'string', 'REQUEST', 'admin');

			if(!$GLOBALS['phpgw_info']['flags']['menu_selection'] = phpgw::get_var('menu_selection'))
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$appname}::categories";
			}

			$this->bo			= CreateObject('admin.bocategories');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->cat_id		= $this->bo->cat_id;
			$this->allrows		= $this->bo->allrows;
			if($this->debug) { $this->_debug_sqsof(); }
		}

		function _debug_sqsof()
		{
			$data = array(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id
			);
			echo '<br>UI:<br>';
			_debug_array($data);
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'	=> $this->start,
				'query'	=> $this->query,
				'sort'	=> $this->sort,
				'order'	=> $this->order
			);

			if(isset($this->cat_id))
			{
				$data['cat_id'] = $this->cat_id;
			}
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$appname = phpgw::get_var('appname');
			$location = phpgw::get_var('location');
			$global_cats  = phpgw::get_var('global_cats');

			$GLOBALS['phpgw']->xslttpl->add_file('cats');

			$link_data = array
			(
				'menuaction'  => 'admin.uicategories.index',
				'appname'     => $appname,
				'location'     => $location,
				'global_cats' => $global_cats,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			if ( phpgw::get_var('add', 'bool') )
			{
				$link_data['menuaction'] = 'admin.uicategories.edit';
				$link_data['menu_selection'] = $GLOBALS['phpgw_info']['flags']['menu_selection'];
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			if ( phpgw::get_var('done', 'bool') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uimainscreen.mainscreen') );
			}

			if ( $appname )
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($appname) . ' ' . lang('global categories') . ': ' . lang('category list');
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('global categories') . ': ' . lang('category list');
			}

			if (!$global_cats)
			{
				$global_cats = False;
			}

			$categories = $this->bo->get_list($global_cats);

			$cat_header[] = array
			(
				'sort_name'				=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'cat_name',
											'order'	=> $this->order,
											'extra'	=> $link_data,
											'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
										)),
				'lang_add_sub'			=> lang('add sub'),
				'lang_name'				=> lang('name'),
				'lang_descr'			=> lang('description'),
				'lang_status'			=> lang('status'),
				'lang_edit'				=> lang('edit'),
				'lang_delete'			=> lang('delete'),
				'lang_sort_statustext'	=> lang('sort the entries'),
				'sort_descr'			=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $this->sort,
												'var'	=> 'cat_description',
												'order'	=> $this->order,
												'extra'	=> $link_data,
												'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
											))
			);


			$lang_add_sub_statustext	= lang('add a subcategory');
			$lang_edit_statustext		= lang('edit this category');
			$lang_delete_statustext		= lang('delete this category');
			$lang_add_sub				= lang('add sub');

			$content = array();
			foreach ($categories as $cat)
			{
				$level		= $cat['level'];
				$cat_name	= $GLOBALS['phpgw']->strip_html($cat['name']);

				$main = 'yes';
				if ($level > 0)
				{
					$space = ' . ';
					$spaceset = str_repeat($space,$level);
					$cat_name = $spaceset . $cat_name;
					$main = 'no';
				}

				$descr = $GLOBALS['phpgw']->strip_html($cat['description']);

				if ( $appname && $cat['app_name'] == 'phpgw')
				{
					$appendix = '&nbsp;[' . lang('Global') . ']';
				}
				else
				{
					$appendix = '';
				}

				if ( $appname && $cat['app_name'] == $appname )
				{
					$show_edit_del = True;
				}
				elseif ( !$appname && $cat['app_name'] == 'phpgw')
				{
					$show_edit_del = True;
				}
				else
				{
					$show_edit_del = False;
				}

				if ($show_edit_del)
				{
					$link_data['cat_id']		= $cat['id'];
					$link_data['menuaction']	= 'admin.uicategories.edit';
					$link_data['menu_selection'] = $GLOBALS['phpgw_info']['flags']['menu_selection'];
					$edit_url			= $GLOBALS['phpgw']->link('/index.php',$link_data);
					$lang_edit			= lang('edit');

					$link_data['menuaction']	= 'admin.uicategories.delete';
					$delete_url			= $GLOBALS['phpgw']->link('/index.php',$link_data);
					$lang_delete			= lang('delete');
				}
				else
				{
					$edit_url					= '';
					$lang_edit					= '';
					$delete_url					= '';
					$lang_delete				= '';
				}

				$link_data['menuaction'] = 'admin.uicategories.edit';
				$link_data['parent'] = $cat['id'];
				$link_data['menu_selection'] = $GLOBALS['phpgw_info']['flags']['menu_selection'];
				unset($link_data['cat_id']);
				$add_sub_url = $GLOBALS['phpgw']->link('/index.php',$link_data);

				$content[] = array
				(
					'name'						=> $cat_name . $appendix,
					'descr'						=> $descr,
					'main'						=> $main,
					'status'					=> $cat['active'],
					'status_text'				=> $cat['active'] == 1 ? 'active' : 'disabled',
					'add_sub_url'				=> $add_sub_url,
					'edit_url'					=> $edit_url,
					'delete_url'				=> $delete_url,
					'lang_add_sub_statustext'	=> $lang_add_sub_statustext,
					'lang_edit_statustext'		=> $lang_edit_statustext,
					'lang_delete_statustext'	=> $lang_delete_statustext,
					'lang_add_sub'				=> $lang_add_sub,
					'lang_edit'					=> $lang_edit,
					'lang_delete'				=> $lang_delete
				);
			}

			$link_data['menuaction'] = 'admin.uicategories.index';
			$link_data['parent'] = '';

			$cat_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a category'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('return to admin mainscreen')
			);

			$nm = array
			(
				'start'			=> $this->start,
 				'num_records'	=> count($categories),
 				'all_records'	=> $this->bo->cats->total_records,
				'link_data'		=> array_merge($link_data, array('query' => $this->query)),
				'allow_all_rows'=> true,
				'allrows'		=> $this->allrows
			);

			$data = array
			(
				'nm_data'		=> $this->nextmatchs->xslt_nm($nm),
				'search_data'	=> $this->nextmatchs->xslt_search(array('query' => $this->query,'link_data' => $link_data)),
				'cat_header'	=> $cat_header,
				'cat_data'		=> $content,
				'cat_add'		=> $cat_add
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('cat_list' => $data));
		}

		function edit()
		{
			$appname		= phpgw::get_var('appname');
			$location		= phpgw::get_var('location');
			$global_cats	= phpgw::get_var('global_cats');
			$parent			= phpgw::get_var('parent', 'int', 'GET', 0);
			$values			= phpgw::get_var('values', 'string', 'POST');

			$message = '';
			$link_data = array
			(
				'menuaction'  => 'admin.uicategories.index',
				'appname'     => $appname,
				'location'     => $location,
				'global_cats' => $global_cats,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			if ( isset($values['cancel']) && $values['cancel'] )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ( (isset($values['save']) && $values['save'] )
				|| (isset($values['apply']) && $values['apply']) )
			{
				$values['cat_id'] = $this->cat_id;
				$values['access'] = 'public';

				$error = $this->bo->check_values($values);
				if (is_array($error))
				{
					$message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$this->cat_id = $this->bo->save_cat($values);
					if ( isset($values['apply']) && $values['apply'] )
					{
						$message = lang('Category %1 has been saved !',$values['name']);
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
					}
				}
			}

			if ($this->cat_id)
			{
				$cats = $this->bo->cats->return_single($this->cat_id);
			}
			else
			{
				$cats = array(array
				(
					'id'			=> 0,
					'name'			=> '',
					'description'	=> '',
					'parent'		=> $parent
				));
			}
			$parent = $cats[0]['parent'];

			if($this->cat_id)
			{
				$function = lang('edit category');
			}
			else
			{
				$function =  lang('add category');
			}

			if ( $appname )
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($appname);
				if($location)
				{
					$GLOBALS['phpgw_info']['flags']['app_header'] .= "::{$location}";
				}
				$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('global categories') . "::$function";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('global categories') . "::$function";
			}

			$GLOBALS['phpgw']->xslttpl->add_file('cats');

/*
			if ( $appname )
			{
				$GLOBALS['phpgw']->template->set_var('title_categories',lang('Edit global category for %1',lang($appname) . $location?"::{$location}":''));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('title_categories',lang('Edit global category'));
			}
*/
			$active = array
			(
				array
				(
					'id'	=> 0,
					'name'	=> lang('inactive')
				),
				array
				(
					'id'	=> 1,
					'name'	=> lang('active')
				),
				array
				(
					'id'	=> 2,
					'name'	=> lang('inactive and hidden')
				)
			);

			foreach ($active as &$entry)
			{
				$entry['selected'] = $entry['id'] == $cats[0]['active'] ? 1 : 0;
			}

			$data = array
			(
				'img_color_selector'	=> $GLOBALS['phpgw']->common->image('phpgwapi', 'color_selector'),
				'lang_name'				=> lang('name'),
				'lang_color'			=> lang('color'),
				'lang_color_selector'	=> lang('color selector'),
				'lang_descr'			=> lang('description'),
				'lang_parent'			=> lang('parent category'),
				'old_parent'			=> $parent,
				'lang_save'				=> lang('save'),
				'lang_apply'			=> lang('apply'),
				'lang_cancel'			=> lang('cancel'),
				'value_name'			=> $GLOBALS['phpgw']->strip_html($cats[0]['name']),
				'value_descr'			=> $GLOBALS['phpgw']->strip_html($cats[0]['description']),
				'message'				=> $message,
				'value_color'			=> $cats[0]['color'],
				'value_icon'			=> $cats[0]['icon'],
				'lang_content_statustext'	=> lang('enter a description for the category'),
				'lang_cancel_statustext'	=> lang('leave the category untouched and return back to the list'),
				'lang_save_statustext'		=> lang('save the category and return back to the list'),
				'lang_apply_statustext'		=> lang('save the category'),
				'cat_select'			=> $this->bo->cats->formatted_xslt_list(array('select_name' => 'values[parent]', 'selected' => $parent,'self' => $this->cat_id,'globals' => $global_cats)),
				'active_list'			=> array('options' => $active)
			);

			$link_data['menuaction'] = 'admin.uicategories.edit';
			if ($this->cat_id)
			{
				$link_data['cat_id']	= $this->cat_id;
			}
			$data['edit_url'] = $GLOBALS['phpgw']->link('/index.php',$link_data);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('cat_edit' => $data));
		}

		function delete()
		{
			$appname = phpgw::get_var('appname');
			$location= phpgw::get_var('location');
			$global_cats  = phpgw::get_var('global_cats');
			$receipt = array();
			$link_data = array
			(
				'menuaction'  => 'admin.uicategories.index',
				'appname'     => $appname,
				'location'     => $location,
				'global_cats' => $global_cats,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			if ( phpgw::get_var('cancel', 'bool') || !$this->cat_id )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			if ( phpgw::get_var('confirm', 'bool') )
			{
				$subs = phpgw::get_var('subs');
				if ( $subs )
				{
					switch ( $subs )
					{
						case 'move':
							$this->bo->delete($this->cat_id, false, true);
							$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
							break;
						case 'drop':
							$this->bo->delete($this->cat_id, true);
							$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
							break;
						default:
							$receipt['error'][]=array('msg'=>'Please choose one of the methods to handle the subcategories');
							break;
					}
				}
				else
				{
					$this->bo->delete($this->cat_id);
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_delete'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = ( $appname ? lang($appname) . ' ' : '' ) .($location?"::{$location}":'') . lang('global categories') . ': ' . lang('delete category');

			$type = $appname ? 'noglobalapp' : 'noglobal';

			$apps_cats = $this->bo->exists(array
			(
				'type'		=> $type,
				'cat_name'	=> '',
				'cat_id'	=> $this->cat_id
			));

			//Initialize our variables
			$msgbox_error = '';
			$show_done = '';
			$subs = '';
			$lang_sub_select_move = '';
			$lang_sub_select_drop = '';

			if ($apps_cats)
			{
				$receipt['message'][]=array('msg'=>'This category is currently being used by applications as a parent category');
				$receipt['message'][]=array('msg'=>'You will need to reassign these subcategories before you can delete this category');
				$show_done		= 'yes';
			}
			else
			{
				$confirm_msg = lang('Are you sure you want to delete this global category ?');

				$exists = $this->bo->exists(array
				(
					'type'     => 'subs',
					'cat_name' => '',
					'cat_id'   => $this->cat_id
				));

				if ($exists)
				{
					$subs					= 'yes';
					$lang_sub_select_move	= lang('Do you want to move all global subcategories one level down ?');
					$lang_sub_select_drop	= lang('Do you want to delete all global subcategories ?');
				}
			}

			$link_data = array
			(
				'menuaction'	=> 'admin.uicategories.delete',
				'cat_id'		=> $this->cat_id,
				'appname'      => $appname,
				'location'     => $location,
				'global_cats' => $global_cats,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);
			$link_data['menu_selection'] = $GLOBALS['phpgw_info']['flags']['menu_selection'];

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$data = array
			(
				'form_action'			=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'show_done'				=> $show_done,
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_delete'			=> lang('delete'),
				'subs'					=> $subs,
				'lang_confirm_msg'		=> $confirm_msg,
				'lang_sub_select_move'	=> $lang_sub_select_move,
				'lang_sub_select_drop'	=> $lang_sub_select_drop,
				'lang_done_statustext'	=> lang('back to the list'),
				'lang_no'				=> lang('no'),
				'lang_no_statustext'	=> lang('do NOT delete the category and return back to the list'),
				'lang_yes'				=> lang('yes'),
				'lang_yes_statustext'	=> lang('delete the category')
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
