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

	class property_uib_account
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
			'edit'   => true,
			'delete' => true
		);

		function property_uib_account()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice::budget';

		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.bob_account',true);
			$this->bocommon			= CreateObject('property.bocommon');

			$this->acl 			= CreateObject('phpgwapi.acl');
			$this->acl_location		= '.b_account';
			$this->acl_read 		= $this->acl->check('.b_account',1);
			$this->acl_add 			= $this->acl->check('.b_account',2);
			$this->acl_edit 		= $this->acl->check('.b_account',4);
			$this->acl_delete 		= $this->acl->check('.b_account',8);

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('b_account', 'nextmatchs', 'search_field'));

			$b_account_list = $this->bo->read($type);

			while (is_array($b_account_list) && list(,$b_account) = each($b_account_list))
			{
				if($this->acl_edit)
				{
					$link_edit	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.edit', 'id'=> $b_account['id']));
				}

				if($this->acl_delete)
				{
					$link_delete	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.delete', 'id'=> $b_account['id']));
				}

				$content[] = array
				(
					'id'				=> $b_account['id'],
					'first'				=> $b_account['descr'],
					'link_edit'			=> $link_edit,
					'link_delete'			=> $link_delete,
					'lang_view_b_accounttext'	=> lang('view the budget account'),
					'lang_edit_b_accounttext'	=> lang('edit the budget account'),
					'lang_delete_b_accounttext'	=> lang('delete the budget account'),
					'text_view'			=> lang('view'),
					'text_edit'			=> lang('edit'),
					'text_delete'			=> lang('delete')
				);

		}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'		=> lang('Descr'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_id'		=> $this->nextmatchs->show_sort_order(array(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uib_account.index')
										)),
				'lang_id'	=> lang('budget account'),
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_b_accounttext'	=> lang('add a budget account'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.edit')),
				'lang_done'			=> lang('done'),
				'lang_done_b_accounttext'	=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php')
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
				'menu'						=> $this->bocommon->get_menu(),
				'allow_allrows'				=> true,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($b_account_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.index', 'type'=> $type)),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_b_accounttext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_b_accounttext'	=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add
			);

			$appname		= lang('budget account');
			$function_msg		= lang('list budget account');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('b_account'));

			if ($values['save'])
			{
				if(!$id && !ctype_digit($values['id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an integer !'));
					unset($values['id']);
				}

				if(!isset($values['responsible']) || !$values['responsible'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget reponsible!'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				else
				{
					$id =	$values['id'];
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save($values,$action);
				}
			}

			if ($id)
			{
				$b_account = $this->bo->read_single($id);
				$function_msg = lang('edit budget account');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add budget account');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uib_account.edit',
				'id'		=> $id
			);
//_debug_array($b_account);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.index', 'type'=> $type)),
				'lang_id'				=> lang('budget account'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'value_id'				=> $id,
				'lang_id_b_accounttext'			=> lang('Enter the budget account'),
				'lang_descr_b_accounttext'		=> lang('Enter a description the budget account'),
				'lang_done_b_accounttext'		=> lang('Back to the list'),
				'lang_save_b_accounttext'		=> lang('Save the budget account'),
				'value_descr'				=> $b_account['descr'],
				'lang_responsible'			=> lang('Responsible'),
				'lang_user_statustext'			=> lang('Select the budget responsible'),
				'select_user_name'			=> 'values[responsible]',
				'lang_no_user'				=> lang('Select responsible'),
				'user_list'				=> $this->bocommon->get_user_list_right2('select',128,$b_account['responsible'],'.invoice'),

				'lang_category'				=> lang('category'),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the selection belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[cat_id]',
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $b_account['cat_id'],'type' =>'b_account','order'=>'id')),
			);

			$appname						= lang('budget account');

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

			$id		= phpgw::get_var('id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uib_account.index'
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
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.delete', 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_b_accounttext'	=> lang('Delete the entry'),
				'lang_no_b_accounttext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('budget account');
			$function_msg		= lang('delete budget account');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

	}

