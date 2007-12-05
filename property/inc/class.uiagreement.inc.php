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
	* @subpackage agreement
 	* @version $Id: class.uiagreement.inc.php,v 1.37 2007/08/14 10:45:09 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uiagreement
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
		(
			'index'  		=> True,
			'view'   		=> True,
			'edit'   		=> True,
			'delete' 		=> True,
			'list_attribute'	=> True,
			'edit_attrib'		=> True,
			'columns'		=> True,
			'edit_item'		=> True,
			'view_item'		=> True,
			'view_file'		=> True,
			'excel'			=> True,
			'add_activity'		=> True
		);

		function property_uiagreement()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject('property.boagreement',True);
			$this->bocommon		= CreateObject('property.bocommon');
			$this->menu		= CreateObject('property.menu');

			$this->role		= $this->bo->role;

			$this->cats		= CreateObject('phpgwapi.categories');
			$this->cats->app_name = 'fm_vendor';

			$this->acl		= CreateObject('phpgwapi.acl');
			$this->acl_location	= '.agreement';

			$this->acl_read 	= $this->acl->check($this->acl_location,1);
			$this->acl_add		= $this->acl->check($this->acl_location,2);
			$this->acl_edit		= $this->acl->check($this->acl_location,4);
			$this->acl_delete	= $this->acl->check($this->acl_location,8);
			$this->acl_manage	= $this->acl->check($this->acl_location,16);

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort		= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->vendor_id	= $this->bo->vendor_id;
			$this->allrows		= $this->bo->allrows;
			$this->member_id	= $this->bo->member_id;
			$this->fakebase 	= $this->bo->fakebase;
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
				'vendor_id'	=> $this->vendor_id,
				'allrows'	=> $this->allrows,
				'member_id'	=> $this->member_id
			);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = True;

			$values		= phpgw::get_var('values');
			$receipt	= array();
			 
			if ($values['save'])
			{

				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read_repository();
				$GLOBALS['phpgw']->preferences->add($this->currentapp,'agreement_columns',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.columns',
				'role'		=> $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'	=> $this->bo->column_list($values['columns'],$allrows=True),
				'function_msg'	=> $function_msg,
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'	=> lang('columns'),
				'lang_none'		=> lang('None'),
				'lang_save'		=> lang('save'),
				'select_name'	=> 'period'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view_file()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = True;
			$GLOBALS['phpgw_info']['flags'][nofooter] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$file_name	= urldecode(phpgw::get_var('file_name'));
			$id 		= phpgw::get_var('id', 'int');

			$file = $this->fakebase. SEP . 'agreement' . SEP . $id . SEP . $file_name;

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

		function index()
		{
			$this->menu->sub	= 'agreement';

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement',
										'menu',
										'receipt',
										'search_field',
										'nextmatchs',
										'filter_member_of'));

			$links = $this->menu->links('agreement','agreement');

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt','');

			$list = $this->bo->read();

			$uicols		= $this->bo->uicols;
			$content = array();
			$j=0;
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 	= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 	= $uicols['name'][$i];
						}
					}

					if($this->acl_read)
					{
						$content[$j]['row'][$i]['statustext']		= lang('view the entity');
						$content[$j]['row'][$i]['text']			= lang('view');
						$content[$j]['row'][$i++]['link']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.view','id'=> $entry['id'], 'role'=> $this->role));
					}
					if($this->acl_edit)
					{
						$content[$j]['row'][$i]['statustext']		= lang('edit the agreement');
						$content[$j]['row'][$i]['text']			= lang('edit');
						$content[$j]['row'][$i++]['link']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit','id'=> $entry['id'], 'role'=> $this->role));
					}
					if($this->acl_delete)
					{
						$content[$j]['row'][$i]['statustext']		= lang('delete the agreement');
						$content[$j]['row'][$i]['text']			= lang('delete');
						$content[$j]['row'][$i++]['link']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.delete', 'agreement_id'=> $entry['id'], 'role'=> $this->role));
					}

					$j++;
				}
			}

//_debug_array($content);
			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['datatype'][$i]!='T' && $uicols['datatype'][$i]!='CH')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
							(
								'sort'	=> $this->sort,
								'var'	=> $uicols['name'][$i],
								'order'	=> $this->order,
								'extra'	=> array('menuaction'	=> $this->currentapp.'.uiagreement.index',
													'query'		=> $this->query,
													'role'		=> $this->role,
													'member_id'	=> $this->member_id,
													'allrows'	=> $this->allrows
													)
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
			if($this->acl_edit)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('edit');
				$i++;
			}
			if($this->acl_delete)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('delete');
				$i++;
			}


			if($this->acl_add)
			{
				$table_add = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add an agreement'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'role'=> $this->role))
				);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.index',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query,
				'role'		=> $this->role,
				'member_id'	=> $this->member_id
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_columns = array
			(
				'menuaction' 	=> $this->currentapp.'.uiagreement.columns',
				'role'		=> $this->role
			);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => True,'link_data' =>$link_data));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);

			$data = array
			(
				'lang_columns'					=> lang('columns'),
				'link_columns'					=> $GLOBALS['phpgw']->link('/index.php',$link_columns),
				'lang_columns_help'				=> lang('Choose columns'),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'							=> $links,
 				'allow_allrows'					=> True,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the agreement belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'branch','order'=>'descr')),

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_no_vendor'				=> lang('no vendor'),
				'lang_vendor_statustext'		=> lang('Select the vendor the agreement belongs to.'),
				'vendor_list'					=> $this->bo->select_vendor_list('filter',$this->vendor_id),

				'lang_no_member'				=> lang('no member'),
				'member_of_name'				=> 'member_id',
				'member_of_list'				=> $member_of_data['cat_list'],

				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter)),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'						=> $content,
				'table_add'						=> $table_add
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('list ' . $this->role);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function list_content($list,$uicols,$edit_item='',$view_only='')
		{
			$j=0;
//_debug_array($list);
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$content[$j]['id'] 				= $entry['id'];
					$content[$j]['activity_id'] 	= $entry['activity_id'];
					$content[$j]['index_count']		= $entry['index_count'];
					$content[$j]['m_cost'] 			= $entry['m_cost'];
					$content[$j]['w_cost'] 			= $entry['w_cost'];
					$content[$j]['total_cost'] 		= $entry['total_cost'];
					$content[$j]['index_count'] 	= $entry['index_count'];
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 	= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 	= $uicols['name'][$i];
						}
					}

					if($this->acl_read && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']		= lang('view the entity');
						$content[$j]['row'][$i]['text']			= lang('view');
						$content[$j]['row'][$i++]['link']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.view_item', 'agreement_id'=> $entry['agreement_id'], 'id'=> $entry['id']));
					}
					if($this->acl_edit && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']		= lang('edit the agreement');
						$content[$j]['row'][$i]['text']			= lang('edit');
						$content[$j]['row'][$i++]['link']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_item', 'agreement_id'=> $entry['agreement_id'], 'id'=> $entry['id']));
					}
					if($this->acl_delete && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']		= lang('delete this item');
						$content[$j]['row'][$i]['text']			= lang('delete');
						$content[$j]['row'][$i++]['link']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit','delete_item'=>1, 'agreement_id'=> $entry['agreement_id'], 'activity_id'=> $entry['id']));
					}

					$j++;
				}
			}

//html_print_r($content);
			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
				}
			}

			if($this->acl_read && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('view');
				$i++;
			}
			if($this->acl_edit && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('edit');
				$i++;
			}
			if($this->acl_delete && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('delete');
				$i++;
			}
			if($this->acl_manage && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('Update');
				$i++;
			}

			return array('content'=>$content,'table_header'=>$table_header);
		}

		function add_activity()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}


			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$group_id	= phpgw::get_var('group_id', 'int');
			$values	= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement'));

			$agreement = $this->bo->read_single(array('agreement_id'=>$agreement_id));

			if($this->acl_add && (is_array($values)))
			{
				if ($values['save'] || $values['apply'])
				{
					$receipt = $this->bo->add_activity($values,$agreement_id);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'id'=> $agreement_id));
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'id'=> $agreement_id));

				}
			}

			$content = $this->bo->read_group_activity($group_id,$agreement_id);

//_debug_array($content);
			$uicols		= $this->bo->uicols;
			$uicols['descr'][]			= lang('select');

			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
				}
			}

			$GLOBALS['phpgw']->js->validate_file('core','check',$this->currentapp);

			$data = array
			(
				'lang_id'				=> lang('ID'),
				'value_agreement_id'			=> $agreement_id,
				'lang_name'				=> lang('name'),
				'value_name'				=> $agreement['name'],
				'lang_descr'				=> lang('descr'),
				'value_descr'				=> $agreement['descr'],
				'lang_select_all'			=> lang('Select All'),
				'img_check'				=> $GLOBALS['phpgw']->common->get_image_path($this->currentapp).'/check.png',
				'add_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.add_activity', 'group_id'=> $group_id, 'agreement_id'=> $agreement_id)),
				'agreement_id'				=> $agreement_id,
				'table_header'				=> $table_header,
				'values'				=> $content,
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the agreement untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the agreement and return back to the list'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('add activity');
//_debug_array($data);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add_activity' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}


			$id	= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
			$delete_item	= phpgw::get_var('delete_item', 'bool');
			$activity_id	= phpgw::get_var('activity_id', 'int');

			$config		= CreateObject('phpgwapi.config',$this->currentapp);
			$boalarm	= CreateObject('property.boalarm');
			$receipt 	= array();

			if($delete_item && $id && $activity_id)
			{
				$this->bo->delete_item($id,$activity_id);
			}

			$values_attribute  = phpgw::get_var('values_attribute');

			$insert_record_agreement = $GLOBALS['phpgw']->session->appsession('insert_record_agreement',$this->currentapp);

//_debug_array($insert_record_agreement);
			if(isset($insert_record_agreement) && is_array($insert_record_agreement))
			{
				for ($j=0;$j<count($insert_record_agreement);$j++)
				{
					$insert_record['extra'][$insert_record_agreement[$j]]	= $insert_record_agreement[$j];
				}
			}


			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement','nextmatchs','attributes_form'));
			$receipt = array();
			if (is_array($values))
			{
				if(isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					foreach($insert_record['extra'] as $key => $column)
				//	while (is_array($insert_record['extra']) && list($key,$column) = each($insert_record['extra']))
					{
						if($_POST[$key])
						{
							$values['extra'][$column]	= $_POST[$key];
						}
					}
				}

//_debug_array($values);

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
					$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');

					if(!$values['cat_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					}

					if(!$values['last_name'])
					{
//						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}


					if($id)
					{
						$values['agreement_id']=$id;
						$action='edit';
					}
					else
					{
						$values['agreement_id']=$this->bo->request_next_id();
					}

					$values['file_name']=str_replace (' ','_',$_FILES['file']['name']);
					$to_file = $this->fakebase. SEP . 'agreement' . SEP . $values['agreement_id'] . SEP . $values['file_name'];

					if(!$values['document_name_orig'] && $this->bo->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][]=array('msg'=>lang('This file already exists !'));
					}


					if(!$receipt['error'])
					{
//						$values['agreement_id']	= $id;
						$receipt	= $this->bo->create_home_dir($receipt);
						$receipt = $this->bo->save($values,$values_attribute,$action);
						$id = $receipt['agreement_id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if($values['file_name'])
						{
							$this->bo->create_document_dir($id);
							$this->bo->vfs->override_acl = 1;

							if(!$this->bo->vfs->cp (array (
								'from'	=> $_FILES['file']['tmp_name'],
								'to'	=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
							}
							$this->bo->vfs->override_acl = 0;
						}


						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.index', 'role'=> $this->role));
						}
					}
				}
				else if(isset($values['update']) && $values['update'])
				{
					if(!$values['date'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a date !'));
					}
					if(!$values['new_index'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a index !'));
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->update($values);
					}

				}
				else if(isset($values['delete_alarm']) && $values['delete_alarm'] && count($values['alarm']))
				{

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $boalarm->delete_alarm('agreement',$values['alarm']);
					}

				}
				else if(((isset($values['enable_alarm']) && $values['enable_alarm']) || (isset($values['disable_alarm']) && $values['disable_alarm'])) && count($values['alarm']))
				{

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $boalarm->enable_alarm('agreement',$values['alarm'],isset($values['enable_alarm'])?$values['enable_alarm']:'');
					}

				}
				else if(isset($values['add_alarm']) && $values['add_alarm'])
				{
					$time = intval($values['time']['days'])*24*3600 +
						intval($values['time']['hours'])*3600 +
						intval($values['time']['mins'])*60;

					if ($time > 0)
					{
						$receipt = $boalarm->add_alarm('agreement',$this->bo->read_event(array('agreement_id'=>$id)),$time,$values['user_id']);
					}
				}
				else if ((!isset($values['save']) || !$values['save']) && (!isset($values['apply']) || !$values['apply']) && (!isset($values['update']) || !$values['update']))
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.index', 'role'=> $this->role));
				}
			}


			$agreement = $this->bo->read_single(array('agreement_id'=>$id));

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$agreement = $this->bocommon->preserve_attribute_values($agreement,$values_attribute);
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');
			$jscal->add_listener('values_termination_date');
			
			if ($id)
			{
				$this->cat_id = ($agreement['cat_id']?$agreement['cat_id']:$this->cat_id);
				$this->member_id = ($agreement['member_of']?$agreement['member_of']:$this->member_id);
				$list = $this->bo->read_details($id);

				$content	= $list;
	//_debug_array($list);
				if (isset($list) AND is_array($list))
				{
					$k=count($list);
					for ($j=0;$j<$k;$j++)
					{
						if($this->acl_read && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
						{
							$content[$j]['lang_view_statustext']	= lang('view the entity');
							$content[$j]['text_view']		= lang('view');
							$content[$j]['link_view']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.view_item', 'agreement_id'=> $id, 'id'=> $content[$j]['activity_id']));
						}
						if($this->acl_edit && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
						{
							$content[$j]['lang_edit_statustext']	= lang('edit the agreement');
							$content[$j]['text_edit']		= lang('edit');
							$content[$j]['link_edit']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_item', 'agreement_id'=> $id, 'id'=> $content[$j]['activity_id']));
						}
						if($this->acl_delete && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
						{
							$content[$j]['lang_delete_statustext']	= lang('delete this item');
							$content[$j]['text_delete']		= lang('delete');
							$content[$j]['link_delete']		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'delete_item'=>1, 'id'=> $id, 'activity_id'=> $content[$j]['activity_id']));
						}

						$content[$j]['acl_manage']			= $this->acl_manage;
						$content[$j]['acl_read']			= $this->acl_read;
						$content[$j]['acl_edit']			= $this->acl_edit;
						$content[$j]['acl_delete']			= $this->acl_delete;
					}
				}


				$uicols		= $this->bo->uicols;

				for ($i=0;$i<count($uicols['descr']);$i++)
				{
					if($uicols['input_type'][$i]!='hidden')
					{
						$table_header[$i]['header'] 	= $uicols['descr'][$i];
						$table_header[$i]['width'] 		= '5%';
						$table_header[$i]['align'] 		= 'center';
					}
				}

				if($this->acl_read && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('view');
					$i++;
					$set_column[]=True;
				}
				if($this->acl_edit && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('edit');
					$i++;
					$set_column[]=True;
				}
				if($this->acl_delete && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('delete');
					$i++;
					$set_column[]=True;
				}
				if($this->acl_manage && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('Update');
					$i++;
					$set_column[]=True;
				}

//				$table_header=$list['table_header'];
				for ($i=0; $i<9; $i++)
				{
					$set_column[]=True;
				}

				if (isset($content) && is_array($content))
				{
					$jscal->add_listener('values_date');
					$table_update[] = array
					(
						'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
						'lang_datetitle'		=> lang('Select date'),

						'lang_new_index'		=> lang('New index'),
						'lang_new_index_statustext'	=> lang('Enter a new index'),
						'lang_date_statustext'		=> lang('Select the date for the update'),
						'lang_update'			=> lang('Update'),
						'lang_update_statustext'	=> lang('update selected investments')
					);
				}
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.edit',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'id'		=> $id,
				'role'		=> $this->role
			);

			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'		=> $agreement['vendor_id'],
						'vendor_name'	=> isset($agreement['vendor_name'])?$agreement['vendor_name']:''));

			if($agreement['vendor_id'])
			{
				$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => True, 'link_data' => array()));
			}

			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'=> 'agreement',
						'type'		=> 'form',
						'text'		=> 'Email notification',
						'times'		=> isset($times)?$times:'',
						'id'		=> $id,
						'method'	=> isset($method)?$method:'',
						'data'		=> isset($data)?$data:'',
						'account_id'=> isset($account_id)?$account_id:''
						));

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$table_add[] = array
			(
				'lang_add'				=> lang('add detail'),
				'lang_add_standardtext'	=> lang('add an item to the details'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.add_activity', 'agreement_id'=> $id, 'group_id'=> $agreement['group_id']))
			);


			$link_file_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.view_file',
				'id'		=>$id
			);

			if(isset($agreement['files']) && is_array($agreement['files']))
			{
				$j	= count($agreement['files']);
				for ($i=0;$i<$j;$i++)
				{
					$agreement['files'][$i]['file_name']=urlencode($agreement['files'][$i]['name']);
				}
			}

			$link_excel = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.excel',
				'id'		=>$id,
				'allrows'	=>$this->allrows
			);


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);
			$GLOBALS['phpgw']->js->validate_file('core','check',$this->currentapp);
			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat',$this->currentapp);

			$data = array
			(
 				'allow_allrows'							=> True,
				'allrows'								=> $this->allrows,
				'start_record'							=> $this->start,
				'record_limit'							=> $record_limit,
				'num_records'							=> count($list),
 				'all_records'							=> $this->bo->total_records,
				'link_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'alarm_data'							=> $alarm_data,
				'lang_alarm'							=> lang('Alarm'),
				'lang_excel'							=> 'excel',
				'link_excel'							=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'						=> lang('Download table to MS Excel'),

				'fileupload'							=> True,
				'link_view_file'						=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),

				'files'									=> isset($agreement['files'])?$agreement['files']:'',
				'lang_files'							=> lang('files'),
				'lang_filename'							=> lang('Filename'),
				'lang_delete_file'						=> lang('Delete file'),
				'lang_view_file_statustext'				=> lang('Klick to view file'),
				'lang_delete_file_statustext'			=> lang('Check to delete file'),
				'lang_upload_file'						=> lang('Upload file'),
				'lang_file_statustext'					=> lang('Select file to upload'),

				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'								=> lang('ID'),
				'value_agreement_id'					=> $id,
				'lang_category'							=> lang('category'),
				'lang_save'								=> lang('save'),
				'lang_cancel'							=> lang('cancel'),
				'lang_apply'							=> lang('apply'),
				'value_cat'								=> isset($agreement['cat'])?$agreement['cat']:'',
				'lang_apply_statustext'					=> lang('Apply the values'),
				'lang_cancel_statustext'				=> lang('Leave the agreement untouched and return back to the list'),
				'lang_save_statustext'					=> lang('Save the agreement and return back to the list'),
				'lang_no_cat'							=> lang('no category'),
				'lang_cat_statustext'					=> lang('Select the category the agreement belongs to. To do not use a category select NO CATEGORY'),
				'select_name'							=> 'values[cat_id]',
				'cat_list'								=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'branch','order'=>'descr')),

				'lang_member_of'						=> lang('member of'),
				'member_of_name'						=> 'member_id',
				'member_of_list'						=> $member_of_data['cat_list'],

				'lang_dateformat' 						=> lang(strtolower($dateformat)),
				'dateformat_validate'					=> $dateformat_validate,
				'onKeyUp'								=> $onKeyUp,
				'onBlur'								=> $onBlur,
				'lang_attributes'						=> lang('Attributes'),
				'attributes_values'						=> $agreement['attributes'],
				'lookup_functions'						=> isset($agreement['lookup_functions'])?$agreement['lookup_functions']:'',
				'dateformat'							=> $dateformat,

				'img_cal'								=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'						=> lang('Select date'),

				'lang_start_date_statustext'			=> lang('Select the estimated end date for the agreement'),
				'lang_start_date'						=> lang('start date'),
				'value_start_date'						=> $agreement['start_date'],

				'lang_end_date_statustext'				=> lang('Select the estimated end date for the agreement'),
				'lang_end_date'							=> lang('end date'),
				'value_end_date'						=> $agreement['end_date'],

				'lang_termination_date_statustext'		=> lang('Select the estimated termination date'),
				'lang_termination_date'					=> lang('termination date'),
				'value_termination_date'				=> $agreement['termination_date'],

				'vendor_data'							=> $vendor_data,
				'lang_name'								=> lang('name'),
				'lang_name_statustext'					=> lang('name'),
				'value_name'							=> $agreement['name'],
				'lang_descr'							=> lang('descr'),
				'lang_descr_statustext'					=> lang('descr'),
				'value_descr'							=> $agreement['descr'],
				'table_add'								=> $table_add,
				'values'								=> $content,
				'table_header'							=> $table_header,
				'table_update'							=> $table_update,
				'update_action'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'id'=> $id)),
				'lang_select_all'						=> lang('Select All'),
				'img_check'								=> $GLOBALS['phpgw']->common->get_image_path($this->currentapp).'/check.png',
				'set_column'							=> $set_column,

				'lang_agreement_group'					=> lang('Agreement group'),
				'lang_no_agreement_group'				=> lang('Select agreement group'),
				'agreement_group_list'					=> $this->bo->get_agreement_group_list($agreement['group_id']),

				'lang_status'							=> lang('Status'),
				'status_list'							=> $this->bo->select_status_list('select',$agreement['status']),
				'status_name'							=> 'values[status]',
				'lang_no_status'						=> lang('Select status'),
				'textareacols'							=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'							=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . ($id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function excel()
		{
			$id	= phpgw::get_var('id', 'int');
			$list = $this->bo->read_details($id);
			$uicols		= $this->bo->uicols;
			$this->bocommon->excel($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function edit_item()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}


			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$id				= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');
			$delete_last	= phpgw::get_var('delete_last', 'bool', 'GET');
			if($delete_last)
			{
				$this->bo->delete_last_index($agreement_id,$id);
			}

			$values_attribute  = phpgw::get_var('values_attribute');

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement','attributes_form'));

			if (is_array($values))
			{

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) &&$values['apply'])):
				{

					if(!$receipt['error'])
					{
						$values['agreement_id']	= $agreement_id;
						$values['id']	= $id;
						$receipt = $this->bo->save_item($values,$values_attribute);
						$agreement_id = $receipt['agreement_id'];
						$id 			= $receipt['id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'id'=> $agreement_id));
						}
					}
				}
				elseif($values['update']):
				{
					if(!$values['date'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a date !'));
					}
					if(!$values['new_index'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a index !'));
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->update($values);
					}

				}
				elseif (!$values['save'] && !$values['apply'] && !$values['update']):
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit', 'id'=> $agreement_id));
				}
				endif;
			}

			$agreement = $this->bo->read_single(array('agreement_id'=>$agreement_id));
			$values = $this->bo->read_single_item(array('agreement_id'=>$agreement_id,'id'=>$id));

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.edit_item',
				'agreement_id'	=> $agreement_id,
				'id'		=> $id,
				'role'		=> $this->role
			);


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => True,link_data => array()));

			$table_add[] = array
			(
				'lang_add'				=> lang('add detail'),
				'lang_add_standardtext'	=> lang('add an item to the details'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_item', 'agreement_id'=> $agreement_id))
			);

			if($id)
			{
				$list = $this->bo->read_prizing(array('agreement_id'=>$agreement_id,'activity_id'=>$id));
				$activity_descr =$this->bo->get_activity_descr($id);
			}

			$uicols		= $this->bo->uicols;
			$list		= $this->list_content($list,$uicols,$edit_item=True);
			$content	= $list['content'];
			$table_header=$list['table_header'];

			for ($i=0; $i<count($list['content'][0]['row']); $i++)
			{
				$set_column[]=True;
			}

			$table_update[] = array
			(
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'		=> lang('Select date'),
				'lang_new_index'		=> lang('New index'),
				'lang_new_index_statustext'	=> lang('Enter a new index'),
				'lang_date_statustext'		=> lang('Select the date for the update'),
				'lang_update'			=> lang('Update'),
				'lang_update_statustext'	=> lang('update selected investments')
			);

			$GLOBALS['phpgw']->js->validate_file('core','check',$this->currentapp);
			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat',$this->currentapp);

			$data = array
			(
				'activity_descr' 			=> $activity_descr,
				'lang_descr' 				=> lang('Descr'),
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('ID'),
				'value_id'				=> $values['id'],
				'value_num'				=> $values['num'],
				'value_agreement_id'			=> $agreement_id,
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the agreement untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the agreement and return back to the list'),

				'lang_dateformat' 			=> lang(strtolower($dateformat)),
				'dateformat_validate'			=> $dateformat_validate,
				'onKeyUp'				=> $onKeyUp,
				'onBlur'				=> $onBlur,
				'lang_attributes'			=> lang('Attributes'),
				'attributes_values'			=> $values['attributes'],
				'lookup_functions'			=> $values['lookup_functions'],
				'dateformat'				=> $dateformat,

				'lang_agreement'			=> lang('Agreement'),
				'agreement_name'			=> $agreement['name'],

				'table_add'				=> $table_add,
				'values'				=> $content,
				'index_count'				=> $content[0]['index_count'],
				'table_header'				=> $table_header,
				'acl_manage'				=> $this->acl_manage,
				'table_update'				=> $table_update,
				'update_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_item', 'agreement_id'=> $agreement_id, 'id'=> $id)),
				'lang_select_all'			=> lang('Select All'),
				'img_check'				=> $GLOBALS['phpgw']->common->get_image_path($this->currentapp).'/check.png',

				'lang_m_cost'				=> lang('Material cost'),
				'lang_m_cost_statustext'		=> lang('Material cost'),
				'value_m_cost'				=> $values['m_cost'],

				'lang_w_cost'				=> lang('Labour cost'),
				'lang_w_cost_statustext'		=> lang('Labour cost'),
				'value_w_cost'				=> $values['w_cost'],

				'lang_total_cost'			=> lang('Total cost'),
				'value_total_cost'			=> $values['total_cost'],

				'set_column'				=> $set_column,
				'lang_delete_last'			=> lang('delete last index'),
				'lang_delete_last_statustext'		=> lang('delete the last index'),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_item', 'delete_last'=>1, 'agreement_id'=> $agreement_id, 'id'=> $id)),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . ($values['id']?lang('edit item') . ' ' . $agreement['name']:lang('add item') . ' ' . $agreement['name']);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_item' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view_item()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$id	= phpgw::get_var('id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement','attributes_view'));

			$agreement = $this->bo->read_single(array('agreement_id'=>$agreement_id));
			$values = $this->bo->read_single_item(array('agreement_id'=>$agreement_id,'id'=>$id));

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.edit',
				'id'		=> $agreement_id
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			if($id)
			{
				$list = $this->bo->read_prizing(array('agreement_id'=>$agreement_id,'activity_id'=>$id));
				$activity_descr =$this->bo->get_activity_descr($id);
			}

			$uicols		= $this->bo->uicols;
			$list		= $this->list_content($list,$uicols,$edit_item=True);
			$content	= $list['content'];
			$table_header=$list['table_header'];

			$GLOBALS['phpgw']->js->validate_file('core','check',$this->currentapp);

			$data = array
			(
				'activity_descr' 			=> $activity_descr,
				'lang_descr' 				=> lang('Descr'),
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('ID'),
				'value_id'				=> $values['id'],
				'value_num'				=> $values['num'],
				'value_agreement_id'			=> $agreement_id,
				'lang_category'				=> lang('category'),
				'lang_cancel'				=> lang('cancel'),
				'lang_cancel_statustext'		=> lang('Leave the agreement untouched and return back to the list'),

				'lang_dateformat' 			=> lang(strtolower($dateformat)),
				'attributes_view'			=> $values['attributes'],

				'lang_agreement'			=> lang('Agreement'),
				'agreement_name'			=> $agreement['name'],

				'table_add'				=> $table_add,
				'values'				=> $content,
				'table_header'				=> $table_header,

				'lang_m_cost'				=> lang('Material cost'),
				'value_m_cost'				=> $values['m_cost'],

				'lang_w_cost'				=> lang('Labour cost'),
				'value_w_cost'				=> $values['w_cost'],

				'lang_total_cost'			=> lang('Total cost'),
				'value_total_cost'			=> $values['total_cost'],
				'set_column'				=> $set_column,
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('view item') . ' ' . $agreement['name'];

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_item' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$attrib		= phpgw::get_var('attrib');
			$id		= phpgw::get_var('id', 'int');
			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$delete		= phpgw::get_var('delete', 'bool', 'POST');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');


			if($attrib)
			{
				$function='list_attribute';
			}
			else
			{
				$function='index';
			}
			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.'.$function,
				'role'		=> $this->role
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($agreement_id,$id,$attrib);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.delete', 'agreement_id'=> $agreement_id, 'id'=> $id, 'attrib'=> $attrib, 'role'=> $this->role)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('agreement');
			$function_msg		= lang('delete') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$agreement_id	= phpgw::get_var('id', 'int');
			$config		= CreateObject('phpgwapi.config',$this->currentapp);

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement','nextmatchs','attributes_view'));


			$agreement = $this->bo->read_single(array('agreement_id'=>$agreement_id));


			if ($agreement_id)
			{
				$this->cat_id = ($agreement['cat_id']?$agreement['cat_id']:$this->cat_id);
				$this->member_id = ($agreement['member_of']?$agreement['member_of']:$this->member_id);
				$list = $this->bo->read_details($agreement_id);

				$uicols		= $this->bo->uicols;
				$list		= $this->list_content($list,$uicols,$edit_item=False,$view_only=True);
				$content	= $list['content'];
				$table_header=$list['table_header'];
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.index',
				'agreement_id'	=> $agreement_id,
			);

			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'	=> $agreement['vendor_id'],
						'vendor_name'	=> $agreement['vendor_name'],
						'type'		=> 'view'));

			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'=> 'agreement',
						'type'		=> 'view',
						'text'		=> 'Email notification',
						'times'		=> $times,
						'id'		=> $agreement_id,
						'method'	=> $method,
						'data'		=> $data,
						'account_id'=> $account_id
						));


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => True,link_data => array()));

			$link_file_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.view_file',
				'id'		=>$agreement_id
			);


			if(isset($agreement['files']) && is_array($agreement['files']))
			{
				$j	= count($agreement['files']);
				for ($i=0;$i<$j;$i++)
				{
					$agreement['files'][$i]['file_name']=urlencode($agreement['files'][$i]['name']);
				}
			}


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data2 = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.view',
				'id'		=> $agreement_id,
			);

			$data = array
			(
 				'allow_allrows'					=> True,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($content),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data2),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'alarm_data'					=> $alarm_data,
				'lang_alarm'					=> lang('Alarm'),
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),

				'files'						=> isset($agreement['files'])?$agreement['files']:'',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_view_file_statustext'			=> lang('Klick to view file'),

				'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'					=> lang('ID'),
				'value_agreement_id'				=> $agreement_id,
				'lang_category'					=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_cancel'					=> lang('done'),
				'lang_apply'					=> lang('apply'),
				'value_cat'					=> $agreement['cat'],
				'lang_cancel_statustext'			=> lang('return back to the list'),
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'branch','order'=>'descr')),

				'lang_member_of'				=> lang('member of'),
				'member_of_name'				=> 'member_id',
				'member_of_list'				=> $member_of_data['cat_list'],

				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'attributes_view'				=> $agreement['attributes'],
				'dateformat'					=> $dateformat,

				'lang_start_date'				=> lang('start date'),
				'value_start_date'				=> $agreement['start_date'],

				'lang_end_date'					=> lang('end date'),
				'value_end_date'				=> $agreement['end_date'],

				'lang_termination_date'				=> lang('termination date'),
				'value_termination_date'			=> $agreement['termination_date'],

				'vendor_data'					=> $vendor_data,
				'lang_name'					=> lang('name'),
				'value_name'					=> $agreement['name'],
				'lang_descr'					=> lang('descr'),
				'value_descr'					=> $agreement['descr'],
				'table_add'					=> $table_add,
				'values'					=> $content,
				'table_header'					=> $table_header,
				'lang_agreement_group'				=> lang('Agreement group'),
				'agreement_group_list'				=> $this->bo->get_agreement_group_list($agreement['group_id']),

				'lang_status'					=> lang('Status'),
				'status_list'					=> $this->bo->select_status_list('select',$agreement['status']),
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('view');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function list_attribute()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'agreement',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib(array('resort'=>$resort,'id'=>$id));
			}

			$attrib_list = $this->bo->read_attrib();

			while (is_array($attrib_list) && list(,$attrib) = each($attrib_list))
			{
				$content[] = array
				(
					'name'					=> $attrib['name'],
					'type_name'				=> $attrib['type_name'],
					'datatype'				=> $attrib['datatype'],
					'column_name'				=> $attrib['column_name'],
					'input_text'				=> $attrib['input_text'],
					'sorting'				=> $attrib['attrib_sort'],
					'search'				=> $attrib['search'],
					'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.list_attribute', 'resort'=>'up', 'id'=> $attrib['id'], 'allrows'=> $this->allrows, 'role'=> $this->role)),
					'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.list_attribute', 'resort'=> 'down', 'id'=> $attrib['id'], 'allrows'=> $this->allrows, 'role'=> $this->role)),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_attrib', 'id'=> $attrib['id'], 'role'=>$this->role)),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.delete', 'id'=> $attrib['id'], 'attrib'=> true, 'role'=> $this->role)),
					'lang_view_attribtext'			=> lang('view the attrib'),
					'lang_attribute_attribtext'		=> lang('attributes for the attrib'). ' ' . lang('location'),
					'lang_edit_attribtext'			=> lang('edit the attrib'),
					'lang_delete_attribtext'		=> lang('delete the attrib'),
					'text_attribute'			=> lang('Attributes'),
					'text_up'				=> lang('up'),
					'text_down'				=> lang('down'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

	//html_print_r($content);

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_datatype'		=> lang('Datatype'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_sorting'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiagreement.list_attribute',
																'allrows'=> $this->allrows,
																'role'	=> $this->role)
										)),

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiagreement.list_attribute',
																'allrows'=> $this->allrows,
																'role'	=> $this->role)
										)),
				'lang_name'	=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.edit_attrib', 'role'=> $this->role)),
				'lang_done'				=> lang('done'),
				'lang_done_attribtext'	=> lang('back to admin'),
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
				'menuaction'	=> $this->currentapp.'.uiagreement.list_attribute',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'query'		=>$this->query,
				'role'		=> $this->role

			);

			$data = array
			(
				'allow_allrows'				=> True,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($attrib_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_attribtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header_attrib'			=> $table_header,
				'values_attrib'				=> $content,
				'table_add2'				=> $table_add
			);

			$appname	= lang('agreement');
			$function_msg	= lang('list attribute') . ': ' . lang($this->role);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			//$this->save_sessiondata();
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_attrib()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
	//		$GLOBALS['phpgw']->common->msgbox(lang('Altering ColumnName OR Datatype  - deletes your data in this Column'));
	//html_print_r($values);
			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement','choice',));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				$type_id			= $values['type_id'];

				if (!$values['column_name'])
				{
					$receipt['error'][] = array('msg'=>lang('Column name not entered!'));
				}

				if (!$values['input_text'])
				{
					$receipt['error'][] = array('msg'=>lang('Input text not entered!'));
				}
				if (!$values['statustext'])
				{
					$receipt['error'][] = array('msg'=>lang('Statustext not entered!'));
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

				if(!ctype_digit($values['column_info']['scale']) && $values['column_info']['scale'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter scale as integer !'));
					unset($values['column_info']['scale']);
				}

				if (!$values['column_info']['nullable'])
				{
					$receipt['error'][] = array('msg'=>lang('Nullable not chosen!'));
				}


				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_attrib($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib($id);
				$function_msg = lang('edit attribute') . ': ' . lang($this->role);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute') . ': ' . lang($this->role);
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiagreement.edit_attrib',
				'id'		=> $id,
				'role'		=> $this->role

			);
	//html_print_r($values);

			if(is_array($values['column_info']))
			{
				if($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB')
				{
					$multiple_choice= True;
				}
				
				$column_type = $values['column_info']['type'];
				$column_precision =$values['column_info']['precision'];
				$column_scale =$values['column_info']['scale'];
				$column_default =$values['column_info']['default'];
				$column_nullable =$values['column_info']['nullable'];
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_choice'				=> lang('Choice'),
				'lang_new_value'			=> lang('New value'),
				'lang_new_value_statustext'		=> lang('New value for multiple choice'),
				'multiple_choice'			=> $multiple_choice,
				'value_choice'				=> $values['choice'],
				'lang_delete_value'			=> lang('Delete value'),
				'lang_value'				=> lang('value'),
				'lang_delete_choice_statustext'		=> lang('Delete this value from the list of multiple choice'),
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiagreement.list_attribute', 'type_id'=> $type_id, 'role'=> $this->role)),
				'lang_id'				=> lang('Attribute ID'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'value_id'				=> $id,

				'lang_column_name'			=> lang('Column name'),
				'value_column_name'			=> $values['column_name'],
				'lang_column_name_statustext'		=> lang('enter the name for the column'),

				'lang_input_text'			=> lang('input text'),
				'value_input_text'			=> $values['input_text'],
				'lang_input_name_statustext'		=> lang('enter the input text for records'),

				'lang_id_attribtext'			=> lang('Enter the attribute ID'),
				'lang_entity_statustext'		=> lang('Select a agreement type'),

				'lang_statustext'			=> lang('Statustext'),
				'lang_statustext_attribtext'		=> lang('Enter a statustext for the inputfield in forms'),
				'value_statustext'			=> $values['statustext'],

				'lang_done_attribtext'			=> lang('Back to the list'),
				'lang_save_attribtext'			=> lang('Save the attribute'),

				'lang_datatype'				=> lang('Datatype'),
				'lang_datatype_statustext'		=> lang('Select a datatype'),
				'lang_no_datatype'			=> lang('No datatype'),
				'datatype_list'				=> $this->bocommon->select_datatype($column_type),

				'lang_precision'			=> lang('Precision'),
				'lang_precision_statustext'		=> lang('enter the record length'),
				'value_precision'			=> $column_precision,

				'lang_scale'				=> lang('scale'),
				'lang_scale_statustext'			=> lang('enter the scale if type is decimal'),
				'value_scale'				=> $column_scale,

				'lang_default'				=> lang('default'),
				'lang_default_statustext'		=> lang('enter the default value'),
				'value_default'				=> $column_default,

				'lang_nullable'				=> lang('Nullable'),
				'lang_nullable_statustext'		=> lang('Chose if this column is nullable'),
				'lang_select_nullable'			=> lang('Select nullable'),
				'nullable_list'				=> $this->bocommon->select_nullable($column_nullable),

				'value_list'				=> $values['list'],
				'lang_list'					=> lang('show in list'),
				'lang_list_statustext'			=> lang('check to show this attribute in location list'),

				'value_search'				=> $values['search'],
				'lang_include_search'			=> lang('Include in search'),
				'lang_include_search_statustext'	=> lang('check to show this attribute in location list'),


			);
	//html_print_r($data);

			$appname	= lang('agreement');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
