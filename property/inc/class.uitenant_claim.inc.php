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
	* @subpackage project
 	* @version $Id: class.uitenant_claim.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */


	class property_uitenant_claim
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
			'index'  => True,
			'check'  => True,
			'view'   => True,
			'edit'   => True,
			'delete' => True
		);

		function property_uitenant_claim()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject('property.botenant_claim',True);
			$this->bocommon		= CreateObject('property.bocommon');
			$this->menu		= CreateObject('property.menu');

			$this->acl		= CreateObject('phpgwapi.acl');
			$this->acl_location	= '.tenant_claim';

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
			$this->status		= $this->bo->status;
			$this->cat_id		= $this->bo->cat_id;
			$this->allrows		= $this->bo->allrows;
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
				'status'		=> $this->status,
				'cat_id'		=> $this->cat_id,
				'allrows'		=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index($project_id='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim',
										'menu',
										'receipt',
										'search_field',
										'nextmatchs'));

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}


			$this->menu->sub	= 'project';
			$links = $this->menu->links('tenant_claim');

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt','');

			$claim_list = $this->bo->read(array('project_id' => $project_id));

			while (is_array($claim_list) && list(,$claim) = each($claim_list))
			{
				if($claim['tenant_id'])
				{
					$tenant= $this->bocommon->read_single_tenant($claim['tenant_id']);
					$name = $tenant['last_name'] . ', ' . $tenant['first_name'];
				}

				$content[] = array
				(
					'claim_id'				=> $claim['claim_id'],
					'project_id'				=> $claim['project_id'],
					'status'				=> lang($claim['status']),
					'name'					=> $name,
					'entry_date'				=> $claim['entry_date'],
					'category'				=> $claim['category'],
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.view', 'claim_id'=> $claim['claim_id'])),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.edit', 'claim_id'=> $claim['claim_id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.delete', 'claim_id'=> $claim['claim_id'])),
					'lang_view_statustext'			=> lang('view the claim'),
					'lang_edit_statustext'			=> lang('edit the claim'),
					'lang_delete_statustext'		=> lang('delete the claim'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
				unset ($tenant);
				unset ($name);
			}

			$table_header = array
			(
				'lang_project'		=> lang('Project'),
				'lang_name'		=> lang('name'),
				'lang_status'		=> lang('Status'),
				'lang_time_created'	=> lang('time created'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_claim_id'		=> lang('claim id'),
				'sort_project'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'project_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => $this->currentapp.'.uitenant_claim.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=>$this->query,
																	'status'	=>$this->status,
																	'allrows'	=>$this->allrows)
										)),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'org_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => $this->currentapp.'.uitenant_claim.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=>$this->query,
																	'status'	=>$this->status,
																	'allrows'	=>$this->allrows)
										)),
				'sort_claim_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'claim_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => $this->currentapp.'.uitenant_claim.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=>$this->query,
																	'status'	=>$this->status,
																	'allrows'	=>$this->allrows)
										)),
				'sort_time_created'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'entry_date',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => $this->currentapp.'.uitenant_claim.index',
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'status'	=>$this->status,
																	'allrows'	=>$this->allrows)
										)),
				'sort_category'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'descr',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => $this->currentapp.'.uitenant_claim.index',
																	'cat_id'	=>$this->cat_id,
																	'query'		=>$this->query,
																	'status'	=>$this->status,
																	'allrows'	=>$this->allrows)
										)),
				'lang_category'		=> lang('category')
			);

			if ($project_id)
			{
				$lang_add = lang('add another');
				$add_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.edit', 'project_id'=> $project_id));
			}
			else
			{
				$lang_add = lang('add');
				$add_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiproject.index', 'lookup'=>true, 'from'=>'tenant_claim'));

			}

			$table_add = array
			(
				'lang_add'		=> $lang_add,
				'lang_add_statustext'	=> lang('add a claim'),
				'add_action'		=> $add_action
			);

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uitenant_claim.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'status'	=> $this->status,
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

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'					=> $links,
 				'allow_allrows'				=> true,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($claim_list),
 				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the claim belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'cat_id',
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'tenant_claim','order'=>'descr')),

				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_list'				=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter)),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),

				'status_name'				=> 'status',
				'status_list'				=> $this->bo->get_status_list(array('format' => 'filter', 'selected' => $this->status,'default' => 'open')),
				'lang_no_status'			=> lang('Open'),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),

				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . ': ' . lang('list claim');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function check()
		{
			$project_id	= phpgw::get_var('project_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim'));

			$claim = $this->bo->check_claim_project($project_id);
			$total_records	= $this->bo->total_records;

			if($total_records > 0)
			{
				$receipt['message'][] = array('msg'=>lang('%1 claim is already registered for this project',$total_records));
				$GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt',$receipt);
				$this->bo->status = 'all';
				$this->status = 'all';
				$this->index($project_id);
			}
			else
			{
				$this->edit($project_id);
			}

			return;
		}

		function edit($project_id='')
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$claim_id = phpgw::get_var('claim_id', 'int');

			$values	 = phpgw::get_var('values');
			$values['project_id']		= phpgw::get_var('project_id', 'int');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');
			$values['tenant_id']		= phpgw::get_var('tenant_id', 'int', 'POST');
			$values['last_name']		= phpgw::get_var('last_name', 'string', 'POST');
			$values['first_name']		= phpgw::get_var('first_name', 'string', 'POST');

			if($project_id)
			{
				$values['project_id'] = $project_id;
			}

			$this->boproject= CreateObject('property.boproject');

			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim'));

			if ($values['save'] || $values['apply'])
			{
				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
				}

				if(!$values['b_account_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if(!$values['workorder'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a workorder !'));
				}

				if(!$receipt['error'])
				{
					$values['claim_id']	= $claim_id;
					$receipt = $this->bo->save($values);
					$claim_id = $receipt['claim_id'];
					$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.index'));
			}


			if ($claim_id)
			{
				$values = $this->bo->read_single($claim_id);
			}

//_debug_array($values);

			$project_values	= $this->boproject->read_single($values['project_id']);

//_debug_array($project_values);

			$table_header_workorder[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_budget'		=> lang('Budget'),
				'lang_calculation'	=> lang('Calculation'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_charge_tenant'	=> lang('Charge tenant'),
				'lang_select'		=> lang('Select')
			);

			$bolocation			= CreateObject('property.bolocation');

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $project_values['location_data'],
						'type_id'	=> count(explode('-',$project_values['location_data']['location_code'])),
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'tenant'	=> $project_values['location_data']['tenant_id'],
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
						'entity_data'	=> $project_values['p']
						));

			if($project_values['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}


			if($project_values['location_data']['tenant_id'] && !$values['tenant_id']):
			{
				$values['tenant_id']		= $project_values['location_data']['tenant_id'];
				$values['last_name']		= $project_values['location_data']['last_name'];
				$values['first_name']		= $project_values['location_data']['first_name'];
			}
			elseif($values['tenant_id']):
			{
				$tenant= $this->bocommon->read_single_tenant($values['tenant_id']);
				$values['last_name']		= $tenant['last_name'];
				$values['first_name']		= $tenant['first_name'];
			}
			endif;


			if($values['workorder'] && $project_values['workorder_budget'])
			{
				foreach ($values['workorder'] as $workorder_id)
				{
					for ($i=0;$i<count($project_values['workorder_budget']);$i++)
					{
						if($project_values['workorder_budget'][$i]['workorder_id'] == $workorder_id)
						{
							$project_values['workorder_budget'][$i]['selected'] = True;
						}
					}
				}
			}


			for ($i=0;$i<count($project_values['workorder_budget']);$i++)
			{
				$claimed= $this->bo->check_claim_workorder($project_values['workorder_budget'][$i]['workorder_id']);

				if($claimed)
				{
					$project_values['workorder_budget'][$i]['claimed'] = $claimed;
				}
			}



			$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);
			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $values['b_account_id'],
						'b_account_name'	=> $values['b_account_name']));

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uitenant_claim.edit',
				'claim_id'		=> $claim_id,
				'project_id' 	=> $values['project_id']
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'table_header_workorder'		=> $table_header_workorder,
				'lang_no_workorders'			=> lang('No workorder budget'),
				'workorder_link'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiworkorder.view')),
				'lang_start_date'			=> lang('Project start date'),
				'value_start_date'			=> $project_values['start_date'],

				'lang_end_date'				=> lang('Project end date'),
				'value_end_date'			=> $project_values['end_date'],

				'lang_charge_tenant'			=> lang('Charge tenant'),
				'charge_tenant'				=> $project_values['charge_tenant'],

				'lang_power_meter'			=> lang('Power meter'),
				'value_power_meter'			=> $project_values['power_meter'],

				'lang_budget'				=> lang('Budget'),
				'value_budget'				=> $project_values['budget'],

				'lang_reserve'				=> lang('reserve'),
				'value_reserve'				=> $project_values['reserve'],
				'lang_reserve_statustext'		=> lang('Enter the reserve'),

				'lang_reserve_remainder'		=> lang('reserve remainder'),
				'value_reserve_remainder'		=> $reserve_remainder,
				'value_reserve_remainder_percent'	=> $remainder_percent,

				'vendor_data'				=> $vendor_data,
				'location_data'				=> $location_data,
				'location_type'				=> 'view',

				'lang_project_id'			=> lang('Project ID'),
				'value_project_id'			=> $project_values['project_id'],
				'lang_name'				=> lang('Name'),
				'value_name'				=> $project_values['name'],

				'lang_descr'				=> lang('Description'),

				'sum_workorder_budget'			=> $project_values['sum_workorder_budget'],
				'sum_workorder_calculation'		=> $project_values['sum_workorder_calculation'],
				'workorder_budget'			=> $project_values['workorder_budget'],
				'sum_workorder_actual_cost'		=> $project_values['sum_workorder_actual_cost'],
				'lang_actual_cost'			=> lang('Actual cost'),
				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'select_user_name'			=> 'project_values[coordinator]',
				'lang_no_user'				=> lang('Select coordinator'),
				'user_list'				=> $this->bocommon->get_user_list('select',$project_values['coordinator'],$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'status_list'				=> $this->boproject->select_status_list('select',$project_values['status']),
				'lang_no_status'			=> lang('Select status'),
				'lang_status'				=> lang('Status'),

				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> $project_values['contact_phone'],

				'b_account_data'			=> $b_account_data,

				'lang_select_workorder_statustext'	=> lang('Include the workorder to this claim'),

				'cat_list_project'			=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $project_values['cat_id'],'type' =>'project','order'=>'descr')),

//------------------

				'lang_status'				=> lang('Status'),
				'lang_status_statustext'		=> lang('Select status'),
				'status_list'				=> $this->bo->get_status_list(array('format' => 'select', 'selected' => $values['status'],'default' => 'open')),
				'lang_no_status'			=> lang('No status'),
				'status_name'				=> 'values[status]',

				'lang_amount'				=> lang('amount'),
				'lang_amount_statustext'		=> lang('The total amount to claim'),
				'value_amount'				=> $values['amount'],

				'tenant_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uilookup.tenant')),
				'lang_tenant'				=> lang('tenant'),
				'value_tenant_id'			=> $values['tenant_id'],
				'value_last_name'			=> $values['last_name'],
				'value_first_name'			=> $values['first_name'],
				'lang_tenant_statustext'		=> lang('Select a tenant'),
				'size_last_name'			=> strlen($values['last_name']),
				'size_first_name'			=> strlen($values['first_name']),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_claim_id'				=> lang('ID'),
				'value_claim_id'			=> $claim_id,
				'lang_remark'				=> lang('remark'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'value_remark'				=> $values['remark'],
				'value_cat'				=> $values['cat'],
				'lang_remark_statustext'		=> lang('Enter a remark for this claim'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the claim untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the claim and return back to the list'),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the claim belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[cat_id]',
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'tenant_claim','order'=>'descr')),
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . ': ' . ($claim_id?lang('edit claim'):lang('add claim'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{

			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}


			$claim_id	= phpgw::get_var('claim_id', 'int');
			$delete		= phpgw::get_var('delete', 'bool', 'POST');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => $this->currentapp.'.uitenant_claim.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($claim_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.delete', 'claim_id'=> $claim_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('Tenant claim');
			$function_msg	= lang('delete claim');

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

			$claim_id	= phpgw::get_var('claim_id', 'int');

			$this->boproject= CreateObject('property.boproject');
			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim'));

			$values = $this->bo->read_single($claim_id);

			$project_values	= $this->boproject->read_single($values['project_id']);

			$table_header_workorder[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_budget'		=> lang('Budget'),
				'lang_calculation'	=> lang('Calculation'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_charge_tenant'	=> lang('Charge tenant'),
				'lang_select'		=> lang('Select')
			);

			$bolocation			= CreateObject('property.bolocation');

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $project_values['location_data'],
						'type_id'	=> count(explode('-',$project_values['location_data']['location_code'])),
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'tenant'	=> $project_values['location_data']['tenant_id'],
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
						'entity_data'	=> $project_values['p']
						));

			if($project_values['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}


			if($project_values['location_data']['tenant_id'] && !$values['tenant_id']):
			{
				$values['tenant_id']		= $project_values['location_data']['tenant_id'];
				$values['last_name']		= $project_values['location_data']['last_name'];
				$values['first_name']		= $project_values['location_data']['first_name'];
			}
			elseif($values['tenant_id']):
			{
				$tenant= $this->bocommon->read_single_tenant($values['tenant_id']);
				$values['last_name']		= $tenant['last_name'];
				$values['first_name']		= $tenant['first_name'];
			}
			endif;


			if($values['workorder'] && $project_values['workorder_budget'])
			{
				foreach ($values['workorder'] as $workorder_id)
				{
					for ($i=0;$i<count($project_values['workorder_budget']);$i++)
					{
						if($project_values['workorder_budget'][$i]['workorder_id'] == $workorder_id)
						{
							$project_values['workorder_budget'][$i]['selected'] = True;
						}
					}
				}
			}


			for ($i=0;$i<count($project_values['workorder_budget']);$i++)
			{
				$claimed= $this->bo->check_claim_workorder($project_values['workorder_budget'][$i]['workorder_id']);

				if($claimed)
				{
					$project_values['workorder_budget'][$i]['claimed'] = $claimed;
				}
			}



			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $values['b_account_id'],
						'b_account_name'	=> $values['b_account_name'],
						'type'	=> 'view'));


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'table_header_workorder'		=> $table_header_workorder,
				'lang_no_workorders'			=> lang('No workorder budget'),
				'workorder_link'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiworkorder.view')),
				'lang_start_date'			=> lang('Project start date'),
				'value_start_date'			=> $project_values['start_date'],

				'lang_end_date'				=> lang('Project end date'),
				'value_end_date'			=> $project_values['end_date'],

				'lang_charge_tenant'			=> lang('Charge tenant'),
				'charge_tenant'				=> $project_values['charge_tenant'],

				'lang_power_meter'			=> lang('Power meter'),
				'value_power_meter'			=> $project_values['power_meter'],

				'lang_budget'				=> lang('Budget'),
				'value_budget'				=> $project_values['budget'],

				'lang_reserve'				=> lang('reserve'),
				'value_reserve'				=> $project_values['reserve'],
				'lang_reserve_statustext'		=> lang('Enter the reserve'),

				'lang_reserve_remainder'		=> lang('reserve remainder'),
				'value_reserve_remainder'		=> $reserve_remainder,
				'value_reserve_remainder_percent'	=> $remainder_percent,

				'location_data'				=> $location_data,
				'location_type'				=> 'view',

				'lang_project_id'			=> lang('Project ID'),
				'value_project_id'			=> $project_values['project_id'],
				'lang_name'				=> lang('Name'),
				'value_name'				=> $project_values['name'],

				'lang_descr'				=> lang('Description'),

				'sum_workorder_budget'			=> $project_values['sum_workorder_budget'],
				'sum_workorder_calculation'		=> $project_values['sum_workorder_calculation'],
				'workorder_budget'			=> $project_values['workorder_budget'],
				'sum_workorder_actual_cost'		=> $project_values['sum_workorder_actual_cost'],
				'lang_actual_cost'			=> lang('Actual cost'),
				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'select_user_name'			=> 'project_values[coordinator]',
				'lang_no_user'				=> lang('Select coordinator'),
				'user_list'				=> $this->bocommon->get_user_list('select',$project_values['coordinator'],$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'status_list'				=> $this->boproject->select_status_list('select',$project_values['status']),
				'lang_no_status'			=> lang('Select status'),
				'lang_status'				=> lang('Status'),

				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> $project_values['contact_phone'],

				'b_account_data'			=> $b_account_data,

				'cat_list_project'			=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $project_values['cat_id'],'type' =>'project','order'=>'descr')),

//------------------

				'lang_status'				=> lang('Status'),
				'status_list'				=> $this->bo->get_status_list(array('format' => 'select', 'selected' => $values['status'],'default' => 'open')),

				'lang_amount'				=> lang('amount'),
				'value_amount'				=> $values['amount'],

				'lang_tenant'				=> lang('tenant'),
				'value_tenant_id'			=> $values['tenant_id'],
				'value_last_name'			=> $values['last_name'],
				'value_first_name'			=> $values['first_name'],
				'size_last_name'			=> strlen($values['last_name']),
				'size_first_name'			=> strlen($values['first_name']),

				'lang_claim_id'				=> lang('ID'),
				'value_claim_id'			=> $claim_id,
				'lang_remark'				=> lang('remark'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'value_remark'				=> $values['remark'],
				'value_cat'				=> $values['cat'],
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'tenant_claim','order'=>'descr')),

				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uitenant_claim.index')),
				'lang_done'				=> lang('done'),
				'value_date'				=> $GLOBALS['phpgw']->common->show_date($tenant_claim['entry_date'])

			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . ': ' . ($claim_id?lang('edit claim'):lang('add claim'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
