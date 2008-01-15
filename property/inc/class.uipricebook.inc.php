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
 	* @version $Id: class.uipricebook.inc.php,v 1.25 2007/01/26 14:53:47 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uipricebook
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
			'activity'		=> True,
			'index'  		=> True,
			'agreement_group'   	=> True,
			'edit_agreement_group' 	=> True,
			'edit_activity' 	=> True,
			'activity_vendor'	=> True,
			'prizing'		=> True,
			'delete' 		=> True,
			'excel'			=> True,
			'excel_2'		=> True
		);

		function property_uipricebook()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');

			$this->bo				= CreateObject('property.bopricebook',True);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->contacts				= CreateObject('property.soactor');
			$this->contacts->role			= 'vendor';

			$this->acl 				= CreateObject('phpgwapi.acl');
			$this->acl_location			= '.agreement';
			$this->acl_read 			= $this->acl->check('.agreement',1);
			$this->acl_add 				= $this->acl->check('.agreement',2);
			$this->acl_edit 			= $this->acl->check('.agreement',4);
			$this->acl_delete 			= $this->acl->check('.agreement',8);
			$this->acl_manage 			= $this->acl->check('.agreement',16);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
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
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id,
				'allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function excel()
		{
			$list = $this->bo->read();

			$name	= array('num','branch','vendor_id','m_cost','w_cost','total_cost','this_index','unit','descr','index_count');
			$descr	= array(lang('Activity Num'),
						lang('Branch'),
						lang('Vendor'),
						lang('Material cost'),
						lang('Labour cost'),
						lang('Total Cost'),
						lang('Last index'),
						lang('Unit'),
						lang('Description'),
						lang('Index Count')
					);

			$this->bocommon->excel($list,$name,$descr);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->session->appsession('referer','property','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook',
										'nextmatchs',
										'search_field'));

			$values			= phpgw::get_var('values');

//_debug_array($values);
			if($values['submit_update'])
			{
				$receipt=$this->bo->update_pricebook($values);
			}

			$pricebook_list = $this->bo->read();

			$i=0;
			if (isSet($pricebook_list) AND is_array($pricebook_list))
			{
				foreach($pricebook_list as $pricebook)
				{
					if($this->acl_manage)
					{
						$link_edit			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.edit_activity', 'activity_id'=> $pricebook['activity_id']));
						$link_prizing			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.prizing', 'activity_id'=> $pricebook['activity_id'], 'agreement_id'=> $pricebook['agreement_id'], 'cat_id'=> $this->cat_id));
						$lang_edit_statustext		= lang('edit the pricebook');
						$lang_prizing_statustext	= lang('view or edit prizing history of this element');
						$text_edit			= lang('edit');
						$text_prizing			= lang('prizing');
					}

					$content[] = array
					(
						'counter'			=> $i,
						'activity_id'			=> $pricebook['activity_id'],
						'num'				=> $pricebook['num'],
						'branch'			=> $pricebook['branch'],
						'vendor_id'			=> $pricebook['vendor_id'],
						'agreement_id'			=> $pricebook['agreement_id'],
						'm_cost'			=> $pricebook['m_cost'],
						'w_cost'			=> $pricebook['w_cost'],
						'total_cost'			=> $pricebook['total_cost'],
						'this_index'			=> $pricebook['this_index'],
						'unit'				=> $pricebook['unit'],
						'descr'				=> $pricebook['descr'],
						'index_count'			=> $pricebook['index_count'],
						'link_edit'			=> $link_edit,
						'link_prizing'			=> $link_prizing,
						'lang_edit_statustext'		=> $lang_edit_statustext,
						'lang_prizing_statustext'	=> $lang_prizing_statustext,
						'text_edit'			=> $text_edit,
						'text_prizing'			=> $text_prizing
					);
					$i++;
				}
			}

			$table_header[] = array
			(
				'sort_num'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'num',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uipricebook.index',
																	'cat_id'	=>$this->cat_id,
																	'allrows'	=>$this->allrows)
										)),
				'lang_index_count'	=> lang('Index Count'),
				'lang_num'		=> lang('Activity Num'),
				'lang_branch'		=> lang('Branch'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_select'		=> lang('Select'),
				'lang_total_cost'	=> lang('Total Cost'),
				'lang_prizing'		=> lang('Prizing'),
				'lang_last_index'	=> lang('Last index'),
				'lang_descr'		=> lang('Description'),
				'lang_m_cost'		=> lang('Material cost'),
				'lang_w_cost'		=> lang('Labour cost'),
				'lang_prizing'		=> lang('Prizing'),
				'lang_unit'		=> lang('Unit'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'sort_total_cost'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'total_cost',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uipricebook.index',
																	'cat_id'	=>$this->cat_id,
																	'allrows'	=>$this->allrows)
										))
			);


			if($this->acl_manage)
			{
				$jscal = CreateObject('phpgwapi.jscalendar');
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

			$link_data = array
			(
				'menuaction'	=> 'property.uipricebook.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
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

			$link_excel = array
			(
				'menuaction'	=> 'property.uipricebook.excel',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'allrows'	=> $this->allrows,
				'start'		=> $this->start
			);

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
			(
				'lang_excel'					=> 'excel',
				'link_excel'					=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'				=> lang('Download table to MS Excel'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allrows'					=> $this->allrows,
				'allow_allrows'					=> true,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($pricebook_list),
				'all_records'					=> $this->bo->total_records,
				'lang_select_all'				=> lang('Select All'),
				'img_check'					=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the pricebook belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'					=> $this->bo->get_vendor_list('filter',$this->cat_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_update'					=> $table_update,
				'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.index'))			);

			$appname	= lang('pricebook');
			$function_msg	= lang('list pricebook per vendor');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function agreement_group()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::pricebook::group';

			$GLOBALS['phpgw']->session->appsession('referer','property','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook',
										'nextmatchs',
										'search_field'));

			$agreement_list = $this->bo->read_agreement_group();

			while (is_array($agreement_list) && list(,$agreement) = each($agreement_list))
			{
				$content[] = array
				(
					'agreement_group_id'		=> $agreement['agreement_group_id'],
					'num'				=> $agreement['num'],
					'status'			=> lang($agreement['status']),
					'descr'				=> $agreement['descr'],
					'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.edit_agreement_group', 'agreement_group_id'=> $agreement['agreement_group_id'])),
					'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=>'agreement_group', 'agreement_group_id'=> $agreement['agreement_group_id'], 'start'=>$this->start)),
					'lang_edit_statustext'		=> lang('edit the agreement_group'),
					'lang_delete_statustext'	=> lang('Delete this agreement group'),
					'text_edit'			=> lang('edit'),
					'text_delete'			=> lang('delete')
				);
			}

			$table_header[] = array
			(
				'lang_id'		=> lang('ID'),
				'sort_num'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'num',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uipricebook.agreement_group',
																	'cat_id'	=>$this->cat_id,
																	'allrows'	=>$this->allrows)
										)),
				'lang_num'		=> lang('Activity Num'),
				'lang_delete'		=> lang('Delete'),
				'lang_descr'		=> lang('Description'),
				'lang_edit'		=> lang('edit')
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uipricebook.agreement_group',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add an activity'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.edit_agreement_group'))
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
			(
				'allrows'					=> $this->allrows,
				'allow_allrows'					=> true,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($agreement_list),
				'all_records'					=> $this->bo->total_records,
				'lang_select_all'				=> lang('Select All'),
				'img_check'					=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_status_statustext'			=> lang('Select the status the agreement group belongs to. To do not use a category select NO STATUS'),
				'status_name'					=> 'cat_id',
				'lang_no_status'				=> lang('No status'),
				'status_list'					=> $this->bo->select_status_list('filter',$this->cat_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_agreement_group'			=> $table_header,
				'values_agreement_group'			=> $content,
				'table_add'					=> $table_add,
				'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.agreement_group'))
				);

			$appname	= lang('pricebook');
			$function_msg	= lang('list agreement group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('agreement_group' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit_agreement_group()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$agreement_group_id	 	= phpgw::get_var('agreement_group_id', 'int');
			$values				= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook'));

			if ($values['save'])
			{
				$values['agreement_group_id']	= $agreement_group_id;

				if(!$values['num'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an agreement group code !'));
					$error_id=true;
				}
				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}


				if($values['num']  && !$agreement_group_id)
				{
					if($this->bo->check_agreement_group_num($values['num']))
					{
						$receipt['error'][]=array('msg'=>lang('This agreement group code is already registered!') . '[ '.$values['num'] .' ]');
						$error_id=true;
					}
				}

				if($agreement_group_id)
				{
					$action='edit';
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_agreement_group($values,$action);
					if(!$agreement_group_id)
					{
						$agreement_group_id=$receipt['agreement_group_id'];
					}

				}

				if($agreement_group_id)
				{
					$values['agreement_group_id']=$agreement_group_id;
					$action='edit';
				}
				else
				{
					$agreement_group_id =	$values['agreement_group_id'];
				}
			}
			else
			{
				$values['agreement_group_id']= $agreement_group_id;
				if($agreement_group_id)
				{
					$values = $this->bo->read_single_agreement_group($agreement_group_id);
				}
			}

//_debug_array($values);
			if ($agreement_group_id)
			{
				$function_msg = lang('edit agreement group');
			}
			else
			{
				$function_msg = lang('add agreement group');
			}

			if ($values['cat_id'] > 0)
			{
				$this->cat_id = $values['cat_id'];
			}

			if($error_id)
			{
				unset($values['num']);
			}

			$link_data = array
			(
				'menuaction'		=> 'property.uipricebook.edit_agreement_group',
				'agreement_group_id'	=> $agreement_group_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.agreement_group')),
				'lang_agreement_group_id'			=> lang('Agreement group ID'),
				'lang_num'					=> lang('Agreement group code'),
				'lang_status'					=> lang('Status'),
				'status_list'					=> $this->bo->select_status_list('select',$values['status']),
				'status_name'					=> 'values[status]',
				'lang_no_status'				=> lang('Select status'),

				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'lang_descr'					=> lang('description'),
				'value_agreement_group_id'			=> $values['agreement_group_id'],
				'value_num'					=> $values['num'],
				'value_descr'					=> $values['descr'],
				'lang_num_statustext'				=> lang('An unique code for this activity'),
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Save the building'),
				'lang_descr_statustext'				=> lang('Enter the description for this activity')
			);

			$appname	= lang('pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_agreement_group' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function prizing()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook',
										'nextmatchs',
										'search_field'));

			$cat_id			= phpgw::get_var('cat_id', 'int', 'GET');
			$activity_id	= phpgw::get_var('activity_id', 'int');
			$vendor_id	= phpgw::get_var('vendor_id', 'int', 'GET');
			$agreement_id	= phpgw::get_var('agreement_id', 'int', 'GET');
			$values			= phpgw::get_var('values');

			$referer	= $GLOBALS['phpgw']->session->appsession('referer','property');
			if(!$referer)
			{
				$referer = $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] : $GLOBALS['HTTP_REFERER'];
				$referer = $referer . '&cat_id=' . $cat_id;
				$GLOBALS['phpgw']->session->appsession('referer','property',$referer);
			}

			if($values['submit_update'])
			{
				if(!$values['date'])
				{
					$receipt['error'][] = array('msg'=>lang('Please select a date !'));
				}

				if(!$values['new_index'])
				{
					$receipt['error'][] = array('msg'=>lang('Please enter a new index for calculating next value(s)!'));
				}

				if(!$receipt['error'])
				{
					$receipt=$this->bo->update_pricebook($values);
				}
			}

			if($values['submit_add'])
			{
				if(!$values['date'])
				{
					$receipt['error'][] = array('msg'=>lang('Please select a date !'));
				}

				if(!$values['m_cost'] && !$values['w_cost'])
				{
					$receipt['error'][] = array('msg'=>lang('Please enter a value for either material cost, labour cost or both !'));
				}

				if(!$receipt['error'])
				{
					$receipt=$this->bo->add_activity_first_prize($values);
				}
			}


			$pricebook_list = $this->bo->read_activity_prize($activity_id,$agreement_id);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if (isSet($pricebook_list) AND is_array($pricebook_list))
			{
				foreach($pricebook_list as $pricebook)
				{

					if($pricebook['current_index'])
					{
						$link_delete		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=>'prize', 'activity_id'=> $activity_id, 'agreement_id'=> $agreement_id, 'index_count'=> $pricebook['index_count']));
						$value_m_cost		= $pricebook['m_cost'];
						$value_w_cost		= $pricebook['w_cost'];
						$value_total_cost	= $pricebook['total_cost'];
					}

					$content[] = array
					(
						'm_cost'			=> $pricebook['m_cost'],
						'w_cost'			=> $pricebook['w_cost'],
						'total_cost'			=> $pricebook['total_cost'],
						'this_index'			=> $pricebook['this_index'],
						'date'				=> $GLOBALS['phpgw']->common->show_date($pricebook['date'],$dateformat),
						'current_index'			=> $pricebook['current_index'],
						'index_count'			=> $pricebook['index_count'],
						'link_delete'			=> $link_delete,
						'lang_delete_statustext'	=> lang('Delete this entry'),
						'text_delete'			=> lang('delete'),
					);
				}
			}

//_debug_array($content);
			$table_header[] = array
			(
				'lang_index_count'	=> lang('Index Count'),
				'lang_total_cost'	=> lang('Total Cost'),
				'lang_prizing'		=> lang('Prizing'),
				'lang_last_index'	=> lang('Last index'),
				'lang_m_cost'		=> lang('Material cost'),
				'lang_w_cost'		=> lang('Labour cost'),
				'lang_date'		=> lang('Date'),
				'lang_delete'		=> lang('Delete')
			);

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_date');

			$table_update[] = array
			(
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),
				'lang_new_index'			=> lang('New index'),
				'lang_new_index_statustext'		=> lang('Enter a new index'),
				'lang_date_statustext'			=> lang('Select the date for the update'),
				'lang_update'				=> lang('Update'),
				'lang_update_statustext'		=> lang('update selected investments')
			);

			$table_first_entry[] = array
			(
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),
				'lang_m_cost'				=> lang('Material cost'),
				'lang_m_cost_statustext'		=> lang('Enter a value for the material cost'),
				'lang_w_cost'				=> lang('Labour cost'),
				'lang_w_cost_statustext'		=> lang('Enter a value for the labour cost'),
				'lang_date'				=> lang('Date'),
				'lang_date_statustext'			=> lang('Select the date for the first value'),
				'lang_add'				=> lang('Add'),
				'lang_add_statustext'			=> lang('Add first value for this prizing')
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uipricebook.prizing',
				'activity_id'	=> $activity_id,
				'agreement_id'	=> $agreement_id
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$num_records	= count($pricebook_list);


			$vendor_data = $this->contacts->read_single(array('actor_id'=>$vendor_id));

			if(is_array($vendor_data))
			{
				foreach($vendor_data['attributes'] as $attribute)
				{
					if($attribute['name']=='org_name')
					{
						$value_vendor_name=$attribute['value'];
						break;
					}
				}
			}

			$activity = $this->bo->read_single_activity($activity_id);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'					=> $referer,
				'lang_done'					=> lang('done'),
				'lang_done_statustext'				=> lang('Back to the list'),
				'allrows'					=> $this->allrows,
				'allow_allrows'					=> true,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> $num_records,
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_vendor'					=> lang('Vendor'),
				'lang_activity'					=> lang('Activity'),

				'value_vendor_name'				=> $value_vendor_name,
				'value_activity_id'				=> $activity_id,
				'value_activity_code'				=> $activity['num'],
				'value_vendor_id'				=> $vendor_id,
				'value_m_cost'					=> $value_m_cost,
				'value_w_cost'					=> $value_w_cost,
				'value_total_cost'				=> $value_total_cost,
				'table_header_prizing'				=> $table_header,
				'values_prizing'				=> $content,
				'table_update'					=> $table_update,
				'table_first_entry'				=> $table_first_entry,
				'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.prizing', 'activity_id'=> $activity_id, 'vendor_id'=> $vendor_id))
				);

			$appname	= lang('pricebook');
			$function_msg	= lang('edit pricing');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('prizing' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}


		function excel_2()
		{

			$list = $this->bo->read_activities_pr_agreement_group();

			$name	= array(
						'activity_id',
						'base_descr',
						'num',
						'descr',
					//	'branch',
					//	'dim_d',
						'ns3420',
						'unit',
						);

			$descr	= array(
							'ID',
							lang('Base'),
							lang('Activity Num'),
							lang('Description'),
						//	lang('Branch'),
						//	lang('Dim d'),
							lang('NS3420'),
							lang('Unit'),
							);


			$this->bocommon->excel($list,$name,$descr);
		}


		function activity()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::pricebook::activities';

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook',
										'nextmatchs',
										'search_field'));

			$pricebook_list = $this->bo->read_activities_pr_agreement_group();
//_debug_array($pricebook_list);
			while (is_array($pricebook_list) && list(,$pricebook) = each($pricebook_list))
			{
				$content[] = array
				(
					'activity_id'				=> $pricebook['activity_id'],
					'num'					=> $pricebook['num'],
					'branch'				=> $pricebook['branch'],
					'base_descr'				=> $pricebook['base_descr'],
					'dim_d'					=> $pricebook['dim_d'],
					'ns3420'				=> $pricebook['ns3420'],
					'unit'					=> $pricebook['unit'],
					'descr'					=> $pricebook['descr'],
					'link_vendor'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.activity_vendor', 'activity_id'=> $pricebook['activity_id'], 'agreement_group'=> $this->cat_id)),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.edit_activity', 'activity_id'=> $pricebook['activity_id'], 'agreement_group'=> $this->cat_id)),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=> 'activity', 'activity_id'=> $pricebook['activity_id'])),
					'lang_vendor_statustext'		=> lang('view the vendor(s) for this activity'),
					'lang_edit_statustext'			=> lang('edit this activity'),
					'lang_delete_statustext'		=> lang('delete this activity'),
					'text_vendor'				=> lang('vendor'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

			$table_header[] = array
			(
				'sort_num'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'num',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uipricebook.activity',
																	'cat_id'	=>$this->cat_id,
																	'allrows'	=>$this->allrows)
										)),
				'lang_num'		=> lang('Activity Num'),
				'lang_branch'		=> lang('Branch'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_descr'		=> lang('Description'),
				'lang_base_descr'	=> lang('Base'),
				'lang_dim_d'		=> lang('Dim d'),
				'lang_ns3420'		=> lang('NS3420'),
				'lang_unit'		=> lang('Unit'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete')
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add an activity'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.edit_activity', 'agreement_group'=> $this->cat_id))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uipricebook.activity',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_excel = array
			(
				'menuaction'	=> 'property.uipricebook.excel_2',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'allrows'	=> $this->allrows,
				'start'		=> $this->start
			);

			$data = array
			(
				'lang_excel'					=> 'excel',
				'link_excel'					=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'				=> lang('Download table to MS Excel'),
				'allrows'					=> $this->allrows,
				'allow_allrows'					=> true,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($pricebook_list),
				'all_records'					=> $this->bo->total_records,
				'lang_select_all'				=> lang('Select All'),
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('select agreement_group'),
				'lang_cat_statustext'				=> lang('Select the agreement_group the pricebook belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'					=> $this->bo->get_agreement_group_list('filter',$this->cat_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_activities'			=> $table_header,
				'values_activities'				=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang('pricebook');
			$function_msg	= lang('list activities per agreement_group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_activities' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function activity_vendor()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->session->appsession('referer','property','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook',
										'nextmatchs',
										'search_field'));

			$activity_id		= phpgw::get_var('activity_id', 'int');
			$values				= phpgw::get_var('values');
			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');

			if($values['add'])
			{
				if(!$values['vendor_id'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select a vendor!'));
				}
				else
				{
					$receipt = $this->bo->add_activity_vendor($values);
				}
			}

			$pricebook_list = $this->bo->read_vendor_pr_activity($activity_id);

			foreach ($pricebook_list as $pricebook)
			{
				$content[] = array
				(
					'activity_id'				=> $pricebook['activity_id'],
					'num'					=> $pricebook['num'],
					'branch'				=> $pricebook['branch'],
					'vendor_name'				=> $pricebook['vendor_name'],
					'link_prizing'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit_item', 'id'=> $pricebook['activity_id'], 'agreement_id'=> $pricebook['agreement_id'], 'from' =>'uipricebook.activity_vendor')),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=>'activity_vendor','activity_id'=> $pricebook['activity_id'], 'agreement_id'=> $pricebook['agreement_id'])),
					'lang_prizing_statustext'		=> lang('view edit the prize for this activity'),
					'lang_delete_statustext'		=> lang('delete this vendor from this activity'),
					'text_prizing'				=> lang('Prizing'),
					'text_delete'				=> lang('delete')
				);
			}

			$table_header[] = array
			(
				'sort_vendor'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'org_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uipricebook.activity_vendor',
																	'cat_id'	=>$this->cat_id,
																	'activity_id'	=>$activity_id,
																	'allrows'	=>$this->allrows)
										)),
				'lang_num'		=> lang('Activity Num'),
				'lang_branch'		=> lang('Branch'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_prizing'		=> lang('Prizing'),
				'lang_delete'		=> lang('delete')
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uipricebook.activity_vendor',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'activity_id'	=> $activity_id
			);


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'		=> ''));


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'activity_id'					=> $activity_id,
				'vendor_data'					=> $vendor_data,
				'allrows'					=> $this->allrows,
				'allow_allrows'					=> true,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($pricebook_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('select agreement_group'),
				'lang_cat_statustext'				=> lang('Select the agreement_group the pricebook belongs to. To do not use a category select NO CATEGORY'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_activity_vendor'			=> $table_header,
				'values_activity_vendor'			=> $content,
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Add this vendor to this activity'),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.activity', 'cat_id'=> $values['cat_id'])),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),

			);

			$appname	= lang('pricebook');
			$function_msg	= lang('list vendors per activity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_activity_vendor' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit_activity()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$activity_id	 	= phpgw::get_var('activity_id', 'int');
			$agreement_group 	= phpgw::get_var('agreement_group', 'int', 'GET');
			$values			= phpgw::get_var('values');
			$values['ns3420_id']	= phpgw::get_var('ns3420_id');

			if(!$values['cat_id'])
			{
				$values['cat_id'] = $agreement_group;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook'));

			if ($values['save'])
			{
				if(!$values['num'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an activity code !'));
					$error_id=true;
				}
				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select an agreement_group !'));
				}

				if(!$values['branch_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a branch !'));
				}

				if($values['num']  && !$activity_id)
				{
					if($this->bo->check_activity_num($values['num'],$values['cat_id']))
					{
						$receipt['error'][]=array('msg'=>lang('This activity code is already registered!') . '[ '.$values['num'] .' ]');
						$error_id=true;
					}
				}

				if($activity_id)
				{
					$values['activity_id']=$activity_id;
					$action='edit';
				}
				else
				{
					$activity_id =	$values['activity_id'];
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_activity($values,$action);
					$activity_id= $receipt['activity_id'];
					$values['activity_id']= $activity_id;
				}

			}
			else
			{
				$values['activity_id']= $activity_id;
				if($activity_id)
				{
					$values = $this->bo->read_single_activity($activity_id);
				}
			}

//_debug_array($values);
			if ($activity_id)
			{
				$function_msg = lang('edit activity');
			}
			else
			{
				$function_msg = lang('add activity');
			}

			if ($values['cat_id'] > 0)
			{
				$this->cat_id = $values['cat_id'];
			}

			if($error_id)
			{
				unset($values['num']);
			}

			$link_data = array
			(
				'menuaction'		=> 'property.uipricebook.edit_activity',
				'activity_id'		=> $activity_id,
				'agreement_group'	=> $agreement_group
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.activity', 'cat_id'=> $values['cat_id'])),
				'lang_activity_id'				=> lang('Activity ID'),
				'lang_num'					=> lang('Activity code'),
				'lang_category'					=> lang('Agreement group'),
				'lang_unit'					=> lang('Unit'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'lang_descr'					=> lang('description'),
				'lang_base_descr'				=> lang('Base description'),
				'value_activity_id'				=> $values['activity_id'],
				'value_num'					=> $values['num'],
				'value_general_address'				=> $values['general_address'],
				'value_access'					=> $values['access'],
				'value_descr'					=> $values['descr'],
				'value_base_descr'				=> $values['base_descr'],
				'lang_num_statustext'				=> lang('An unique code for this activity'),
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Save the building'),
				'lang_no_cat'					=> lang('Select agreement group'),
				'lang_cat_statustext'				=> lang('Select the agreement group this activity belongs to.'),
				'select_name'					=> 'values[cat_id]',
				'lang_descr_statustext'				=> lang('Enter the description for this activity'),
				'lang_base_descr_statustext'			=> lang('Enter a description for prerequisitions for this activity - if any'),
				'cat_list'					=> $this->bo->get_agreement_group_list('select',$values['cat_id']),

				'lang_dim_d'					=> lang('Dim D'),
				'dim_d_list'					=> $this->bo->get_dim_d_list($values['dim_d']),
				'select_dim_d'					=> 'values[dim_d]',
				'lang_no_dim_d'					=> lang('No Dim D'),
				'lang_dim_d_statustext'				=> lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),

				'lang_unit'					=> lang('Unit'),
				'unit_list'					=> $this->bo->get_unit_list($values['unit']),
				'select_unit'					=> 'values[unit]',
				'lang_no_unit'					=> lang('Select Unit'),
				'lang_unit_statustext'				=> lang('Select the unit for this activity.'),

				'lang_branch'					=> lang('Branch'),
				'branch_list'					=> $this->bo->get_branch_list($values['branch_id']),
				'select_branch'					=> 'values[branch_id]',
				'lang_no_branch'				=> lang('Select branch'),
				'lang_branch_statustext'			=> lang('Select the branch for this activity.'),

				'ns3420_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.ns3420')),
				'lang_ns3420'					=> lang('NS3420'),
				'value_ns3420_id'				=> $values['ns3420_id'],
				'lang_ns3420_statustext'			=> lang('Select a standard-code from the norwegian standard'),
			);

			$appname = lang('pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_activity' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$method			= phpgw::get_var('method');
			$activity_id	= phpgw::get_var('activity_id', 'int');
			$agreement_id		= phpgw::get_var('agreement_id', 'int', 'GET');
			$index_count	= phpgw::get_var('index_count', 'int', 'GET');
			$agreement_group_id	= phpgw::get_var('agreement_group_id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			if($method=='activity_vendor')
			{
				$link_data = array
				(
					'menuaction' => 'property.uipricebook.activity_vendor',
					'activity_id' => $activity_id
				);

				$function_msg	=lang('delete vendor activity');
				$delete_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=> $method, 'activity_id'=> $activity_id, 'agreement_id'=> $agreement_id));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_activity_vendor($activity_id,$agreement_id);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}
			elseif($method=='activity')
			{
				$link_data = array
				(
					'menuaction' => 'property.uipricebook.activity'
				);

				$function_msg	=lang('delete activity');
				$delete_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=> $method, 'activity_id'=> $activity_id));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_activity($activity_id);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}
			elseif($method=='prize')
			{
				$link_data = array
				(
					'menuaction'	=> 'property.uipricebook.prizing',
					'activity_id'	=> $activity_id,
					'agreement_id'	=> $agreement_id
				);

				$function_msg	=lang('delete prize-index');
				$delete_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=> $method, 'activity_id'=> $activity_id, 'agreement_id'=> $agreement_id, 'index_count'=> $index_count));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_prize_index($activity_id,$agreement_id,$index_count);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}
			elseif($method=='agreement_group')
			{
				$link_data = array
				(
					'menuaction'	=> 'property.uipricebook.agreement_group',
					'start'		=> $this->start
				);

				$function_msg	=lang('Delete agreement group and all the activities associated with it!');
				$delete_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.delete', 'method'=> $method, 'agreement_group_id'=> $agreement_group_id, 'start'=> $this->start));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_agreement_group($agreement_group_id);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $delete_action,
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname						= lang('pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
