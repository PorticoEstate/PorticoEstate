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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');
	
	class property_uiwo_hour
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;

		var $public_functions = array
			(
				'index'  		=> true,
				'tender'  		=> true,
				'view'  		=> true,
				'template'		=> true,
				'save_template'		=> true,
				'prizebook'		=> true,
				'add'			=> true,
				'edit'			=> true,
				'delete'		=> true,
				'deviation'		=> true,
				'edit_deviation'=> true,
				'pdf_order'		=> true
			);

		function property_uiwo_hour()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->create_html			= CreateObject('phpgwapi.xslttemplates');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo					= CreateObject('property.bowo_hour',true);
			$this->boworkorder			= CreateObject('property.boworkorder');
			$this->boproject			= CreateObject('property.boproject');
			$this->bopricebook			= CreateObject('property.bopricebook');

			$this->bocommon				= CreateObject('property.bocommon');
			$this->config				= CreateObject('phpgwapi.config','property');

			$this->config->read();

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.project';
			$this->acl_read 			= $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bopricebook->start;
			$this->query				= $this->bopricebook->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->chapter_id			= $this->bo->chapter_id;
			$this->allrows				= $this->bopricebook->allrows;
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
					'chapter_id'		=> $this->chapter_id,
					'allrows'			=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}

		function deviation()
		{
			$workorder_id 	= phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id	 	= phpgw::get_var('hour_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour'));
			$list = $this->bo->read_deviation(array('workorder_id'=>$workorder_id,'hour_id'=>$hour_id));

			$sum_deviation = 0;

			if (isset($list) AND is_array($list))
			{
				$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				foreach($list as $entry)
				{
					$sum_deviation = $sum_deviation + $entry['amount'];

					$entry_date = (isset($entry['entry_date'])?$GLOBALS['phpgw']->common->show_date($entry['entry_date'],$dateformat):'');

					$content[] = array
						(
							'id'					=> $entry['id'],
							'amount'				=> $entry['amount'],
							'descr'					=> $entry['descr'],
							'entry_date'			=> $entry_date,
							'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.edit_deviation', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour_id, 'id'=> $entry['id'])),
							'lang_edit_statustext'	=> lang('edit the deviation'),
							'text_edit'				=> lang('edit'),
							'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.delete', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour_id, 'deviation_id'=> $entry['id'])),
							'lang_delete_statustext'=> lang('delete the deviation'),
							'text_delete'			=> lang('delete')
						);
				}
			}


			$table_header[] = array
				(
					'lang_id'		=> lang('ID'),
					'lang_amount'	=> lang('amount'),
					'lang_descr'	=> lang('Descr'),
					'lang_date'		=> lang('date'),
					'lang_edit'		=> lang('edit'),
					'lang_delete'	=> lang('delete')
				);


			$link_data = array
				(
					'menuaction'	=> 'property.uiwo_hour.edit_deviation',
					'workorder_id'	=> $workorder_id,
					'hour_id'		=> $hour_id
				);

			//---datatable0 settings---------------------------------------------------	

			$parameters['edit'] = array('parameter' => array(
				array('name'  => 'workorder_id','source' => $workorder_id,	'ready'  => 1),
				array('name'  => 'hour_id',		'source' => $hour_id,		'ready'  => 1),
				array('name'  => 'id',			'source' => 'id')));				

			$parameters['delete'] = array('parameter' => array(
				array('name'  => 'workorder_id','source' => $workorder_id,	'ready'  => 1),
				array('name'  => 'hour_id',		'source' => $hour_id,		'ready'  => 1),
				array('name'  => 'deviation_id','source' => 'id')));

			$permissions['rowactions'][] = array(
				'text'    		=> lang('edit'),
				'action'  		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiwo_hour.edit_deviation')),
				'parameters' 	=> $parameters['edit']);

			$permissions['rowactions'][] = array(
				'text'    		=> lang('delete'),
				'action'  		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiwo_hour.delete' )),
				'confirm_msg'	=> lang('do you really want to delete this entry'),
				'parameters'	=> $parameters['delete']);


			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($content),
					'total_records'	=> count($content),
					'permission'   	=> json_encode($permissions['rowactions']),
					'is_paginator'	=> 0,
					'footer'		=> 0
				);					
			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array('key' => 'id',		'label' => $table_header[0]['lang_id'],		'sortable' => true,'resizeable' => true),
															array('key' => 'amount',	'label' => $table_header[0]['lang_amount'],	'sortable' => true,'resizeable' => true, 'formatter' => 'FormatterRight'),
															array('key' => 'descr',		'label' => $table_header[0]['lang_descr'],	'sortable' => true,'resizeable' => true),
															array('key' => 'entry_date','label' => $table_header[0]['lang_date'],	'sortable' => true,'resizeable' => true)))
				);	

			//------------------------------------datatable0 settings------------------				
			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
					'property_js'				=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
					'base_java_url'				=> json_encode(array(menuaction => "property.uiwo_hour.deviation")),
					'datatable'					=> $datavalues,
					'myColumnDefs'				=> $myColumnDefs,

					'sum_deviation'				=> $sum_deviation,
					'table_header_deviation'	=> $table_header,
					'values_deviation'			=> $content,
					'lang_add'					=> lang('add'),
					'lang_add_statustext'		=> lang('add a deviation'),
					'add_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_done'					=> lang('done'),
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.index', 'workorder_id'=> $workorder_id))
			);

			//---datatable settings--------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'wo_hour.deviation', 'property' );
			//-----------------------datatable settings---					

			$appname			= lang('Workorder');
			$function_msg		= lang('list deviation');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_deviation' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_deviation()
		{
			$workorder_id 	= phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id	= phpgw::get_var('hour_id', 'int');
			$id	 	= phpgw::get_var('id', 'int');
			$values	 	= phpgw::get_var('values');
			$dateformat	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour'));

			if ($values['save'])
			{
				$values['workorder_id']=$workorder_id;
				$values['hour_id']=$hour_id;
				if (!$values['amount'])
				{
					$receipt['error'][] = array('msg'=>lang('amount not entered!'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_deviation($values,$action);
					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Status has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_deviation(array('workorder_id'=>$workorder_id,'hour_id'=>$hour_id,'id'=>$id));
				$function_msg = lang('edit deviation');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add deviation');
				$action='add';
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiwo_hour.edit_deviation',
					'entity_id'	=> $this->entity_id,
					'cat_id'	=> $this->cat_id,
					'id'		=> $id
				);

			$hour = $this->bo->read_single_hour($hour_id);

			//_debug_array($workorder);
			//_debug_array($hour);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$entry_date = (isset($values['entry_date'])?$GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat):'');

			$data = array
				(
					'lang_workorder'				=> lang('Workorder ID'),
					'lang_hour_id'					=> lang('Post'),
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.deviation', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour_id)),
					'lang_id'						=> lang('deviation ID'),
					'lang_descr'					=> lang('Descr'),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),
					'lang_date'						=> lang('date'),
					'value_id'						=> $id,
					'value_workorder_id'			=> $workorder_id,
					'value_hour_id'					=> $hour_id,
					'entry_date'					=> $entry_date,
					'value_id'						=> $id,
					'lang_descr_standardtext'		=> lang('Enter a description of the deviation'),
					'lang_done_standardtext'		=> lang('Back to the list'),
					'lang_save_standardtext'		=> lang('Save the deviation'),
					'lang_amount'					=> lang('amount'),
					'value_amount'					=> $values['amount'],
					'value_descr'					=> $values['descr']
				);

			$appname						= lang('workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_deviation' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function common_data($workorder_id,$view='')
		{

			$workorder	= $this->boworkorder->read_single($workorder_id);
/*			if (!$this->bocommon->check_perms($workorder['grants'],PHPGW_ACL_EDIT))
			{
				$receipt['error'][]=array('msg'=>lang('You have no edit right for this project'));
				$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.view','id'=> $workorder_id));
			}
 */
			$hour_list = $this->bo->read($workorder_id);
			//_debug_array($hour_list);
			$grouping_descr_old	= '';
			$content			= array();

			if (isset($hour_list) AND is_array($hour_list))
			{
				foreach($hour_list as $hour)
				{
					$sum_hour	= $sum_hour + $hour['cost'];
					$sum_deviation	= $sum_deviation + $hour['deviation'];

					if($hour['grouping_descr']!=$grouping_descr_old)
					{
						$new_grouping	= true;
					}
					else
					{
						$new_grouping	= false;
					}

					$grouping_descr_old	= $hour['grouping_descr'];

					if($hour['activity_num'])
					{
						$code	= $hour['activity_num'];
					}
					else
					{
						$code	= str_replace("-",$hour['tolerance'],$hour['ns3420_id']);
					}

					if($hour['count_deviation'] || $view)
					{
						$deviation=$hour['deviation'];
					}
					else
					{
						$deviation=lang('edit');
					}

					$content[] = array
						(
							'post'						=> sprintf("%02s",$workorder['chapter_id']) . '.' . sprintf("%02s",$hour['building_part']) . '.' . sprintf("%02s",$hour['grouping_id']) . '.' . sprintf("%03s",$hour['record']),
							'hour_id'					=> $hour['hour_id'],
							'activity_num'				=> $hour['activity_num'],
							'hours_descr'				=> $hour['hours_descr'],
							'activity_descr'			=> $hour['activity_descr'],
							'new_grouping'				=> $new_grouping,
							'grouping_id'				=> $hour['grouping_id'],
							'grouping_descr'			=> $hour['grouping_descr'],
							'ns3420_id'					=> $hour['ns3420_id'],
							'code'						=> $code,
							'remark'					=> $hour['remark'],
							'building_part'				=> $hour['building_part'],
							'quantity'					=> $hour['quantity'],
							'cost'						=> $hour['cost'],
							'unit'						=> $hour['unit'],
							'unit_name'					=> $hour['unit_name'],
							'billperae'					=> $hour['billperae'],
							'deviation'					=> $deviation,
							'result'					=> ($hour['deviation']+$hour['cost']),
							'wo_hour_category'			=> $hour['wo_hour_category'],
							'cat_per_cent'				=> $hour['cat_per_cent'],
							'link_deviation'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.deviation', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour['hour_id'])),
							'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.edit','workorder_id'=> $workorder_id, 'hour_id'=> $hour['hour_id'])),
							'lang_edit_statustext'		=> lang('edit/customise this hour'),
							'lang_delete_statustext'	=> lang('delete this hour'),
							'text_edit'					=> lang('edit'),
							'text_delete'				=> lang('delete')
						);
				}
			}

			$this->bo->update_deviation(array('workorder_id'=>$workorder_id,'sum_deviation'=>$sum_deviation));

			//_debug_array($content);

			$table_header[] = array
				(
					'lang_post'			=> lang('Post'),
					'lang_code'			=> lang('Code'),
					'lang_descr'		=> lang('descr'),
					'lang_unit'			=> lang('Unit'),
					'lang_billperae'	=> lang('Bill per unit'),
					'lang_quantity'		=> lang('Quantity'),
					'lang_cost'			=> lang('cost'),
					'lang_deviation '	=> lang('deviation'),
					'lang_result'		=> lang('result'),
					'lang_view'			=> lang('view'),
					'lang_edit'			=> lang('edit'),
					'lang_delete'		=> lang('delete'),
					'lang_category'		=> lang('category'),
					'lang_per_cent'		=> lang('percent'),
				);

			$tax = $this->config->config_data['fm_tax'];

			$sum_result	= $sum_hour+$sum_deviation;

			$addition_percentage = $sum_result * $workorder['addition_percentage']/100;
			$sum_tax	= ($sum_result + $addition_percentage + $workorder['addition_rs'])*$tax/100;
			$total_sum	= $sum_result + $addition_percentage + $workorder['addition_rs'] + $sum_tax;

			$this->bo->update_calculation(array('workorder_id'=>$workorder_id,'calculation'=>($sum_result+$addition_percentage+ $workorder['addition_rs'])));

			$table_sum[] = array
				(
					'lang_sum_calculation'			=> lang('Sum calculation'),
					'value_sum_calculation'			=> number_format($sum_hour, 2, ',', ''),
					'lang_addition_rs'				=> lang('Rig addition'),
					'value_addition_rs'				=> number_format($workorder['addition_rs'], 2, ',', ''),
					'lang_addition_percentage'		=> lang('Percentage addition'),
					'value_addition_percentage'		=> number_format($addition_percentage, 2, ',', ''),
					'lang_sum_tax'					=> lang('Sum tax'),
					'value_sum_tax'					=> number_format($sum_tax, 2, ',', ''),
					'lang_total_sum'				=> lang('Total sum'),
					'value_total_sum'				=> number_format($total_sum, 2, ',', ''),
					'lang_sum_deviation'			=> lang('Sum deviation'),
					'sum_deviation'					=> number_format($sum_deviation, 2, ',', ''),
					'sum_result'					=> number_format($sum_result, 2, ',', '')
				);

			$workorder_data = array
				(
					'link_workorder'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id'=> $workorder_id)),
					'lang_vendor_name'				=> lang('Vendor'),
					'vendor_name'					=> $workorder['vendor_name'],
					'vendor_email'					=> $workorder['vendor_email'],
					'descr'							=> htmlentities($workorder['descr']),

					'lang_workorder_id'				=> lang('Workorder ID'),
					'workorder_id'					=> $workorder['workorder_id'],
					'lang_project_id'				=> lang('Project ID'),
					'link_project'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uiproject.edit', 'id'=> $workorder['project_id'])),
					'project_id'					=> $workorder['project_id'],

					'lang_workorder_title'			=> lang('Workorder title'),
					'workorder_title'				=> $workorder['title']
				);



			$common_data = array
				(
					'content' 						=> $content,
					'total_hours_records'			=> count($content),
					'table_header' 					=> $table_header,
					'table_sum' 					=> $table_sum,
					'workorder' 					=> $workorder,
					'workorder_data' 				=> $workorder_data,
				);

			return $common_data;
		}

		function save_template()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour'));

			$values 		= phpgw::get_var('values');
			$workorder_id 	= phpgw::get_var('workorder_id'); // in case of bigint

			if($values['name'])
			{
				$receipt	= $this->bo->add_template($values,$workorder_id);
			}

			$common_data=$this->common_data($workorder_id);

			$link_data = array
				(
					'menuaction' 	=> 'property.uiwo_hour.index',
					'workorder_id'	=> $workorder_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			//------JSON code-------------------
			//join columns hours_descr and remark
			for($i=0;$i<count($common_data['content']);$i++ ) 
			{

				if($common_data['content'][$i]['remark']!="")
				{
					if(trim($common_data['content'][$i]["hours_descr"]) == "")
					{
						$conector = "";
					}
					else
					{
						$conector = "<br>";
					}
					$extra = $common_data['content'][$i]["hours_descr"].$conector.$common_data['content'][$i]["remark"];
				}
				else
				{
					$extra = $common_data['content'][$i]["hours_descr"];
				}
				$common_data['content'][$i]['extra_hours_descr'] = $extra;
			}	


			//---datatable1 settings---------------------------------------------------


			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($common_data['content']),
					'total_records'	=> count($common_data['content']),
					'is_paginator'	=> 1,
					'footer'		=> 0
				);		

			//_debug_array($common_data['table_header'][0]['lang_post']);die;

			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array('key' => 'post',				'label' => $common_data['table_header'][0]['lang_post'],	'sortable' => true,'resizeable' => true),
															array('key' => 'code',				'label' => $common_data['table_header'][0]['lang_code'],	'sortable' => true,'resizeable' => true),
															array('key' => 'extra_hours_descr',	'label' => $common_data['table_header'][0]['lang_descr'],	'sortable' => true,'resizeable' => true),
															array('key' => 'unit',				'label' => $common_data['table_header'][0]['lang_unit'],	'sortable' => true,'resizeable' => true),
															array('key' => 'quantity',			'label' => $common_data['table_header'][0]['lang_quantity'],'sortable' => true,'resizeable' => true, 'formatter' => 'FormatterRight'),
															array('key' => 'billperae',			'label' => $common_data['table_header'][0]['lang_billperae'],'sortable' => true,'resizeable' => true, 'formatter' => 'FormatterRight'),
															array('key' => 'cost',				'label' => $common_data['table_header'][0]['lang_cost'],	'sortable' => true,'resizeable' => true, 'formatter' => 'FormatterRight'),
															array('key' => 'result',			'label' => $common_data['table_header'][0]['lang_result'],	'sortable' => true,'resizeable' => true, 'formatter' => 'FormatterRight'),
															array('key' => 'wo_hour_category',	'label' => $common_data['table_header'][0]['lang_category'],'sortable' => true,'resizeable' => true),
															array('key' => 'cat_per_cent',		'label' => $common_data['table_header'][0]['lang_per_cent'],'sortable' => true,'resizeable' => true, 'formatter' => 'FormatterCenter')
				)));	
			//----------------------------------------------datatable settings--------			

			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
					'property_js'				=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
					'base_java_url'				=> json_encode(array(menuaction => "property.uiwo_hour.index",workorder_id=>$workorder_id)),
					'datatable'					=> $datavalues,
					'myColumnDefs'				=> $myColumnDefs,

					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'add_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.save_template', 'workorder_id'=> $workorder_id)),

					'lang_done_statustext'		=> lang('Back to the workorder list'),
					'lang_add_statustext'		=> lang('Adds this workorders calculation as a template for later use'),
					'lang_search_statustext'	=> lang('Adds a new workorder to an existing project'),

					'lang_done'					=> lang('Done'),
					'lang_add'					=> lang('Add'),
					'lang_search'				=> lang('Search'),

					'lang_name'					=> lang('name'),
					'lang_name_statustext'		=> lang('Enter the name the template'),

					'lang_descr'				=> lang('Description'),
					'lang_descr_statustext'		=> lang('Enter a short description of this template'),

					'total_hours_records'		=> $common_data['total_hours_records'],
					'lang_total_records'		=> lang('Total records'),
					'table_header_hour'			=> $common_data['table_header'],
					'values_hour'				=> $common_data['content'],
					'workorder_data' 			=> $common_data['workorder_data']
				);
			//---datatable settings--------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'wo_hour.save_template', 'property' );
			//-----------------------datatable settings---	
			//_debug_array($data);die;

			$appname		= lang('Workorder');
			$function_msg	= lang('Add template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add_template' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint

			if($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id,$workorder_id);
				return "hour_id ".$hour_id." ".lang("has been deleted");
			}

			$common_data=$this->common_data($workorder_id);

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uiwo_hour.index',
						'workorder_id'	=> $workorder_id

					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiwo_hour.index',"
					."workorder_id:'{$workorder_id}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiwo_hour.index',
								'workorder_id'	=> $workorder_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( // mensaje
									'type'	=> 'label',
									'id'	=> 'msg_header',
									'value'	=> '',
									'style' => 'filter'
								),	
								array
								( 
									'id' => 'btn_save_template',
									'value'    => lang('Save as template'),
									'type' => 'button',
									'tab_index' => 4
								),	
								array
								( 
									'id' => 'btn_add_custom',
									'name' => 'custom',
									'value'    => lang('Add custom'),
									'type' => 'button',
									'tab_index' => 3
								),
								array
								( 
									'type'	=> 'button',
									'id'	=> 'btn_add_template',
									'tab_index' => 2,
									'value'	=> lang('Add from template')
								),			                                        			                                        		                                        		                                        																								
								array
								( 
									'type'	=> 'button',
									'id'	=> 'btn_add_prizebook',
									'tab_index' => 1,
									'value'	=> lang('Add from prizebook')
								)											   				                                        			                                        			                                                                                		                                                                                
							),
							'hidden_value' => array
							(
								)
							)
						)
					);

				$datatable['actions']['down-toolbar'] = array
					(
						'fields'	=> array
						(
							'field' => array
							(
								array
								(
									'id' => 'btn_print_preview',
									'value'    => lang('Print view'),
									'type' => 'button',
									'tab_index' => 5,
									'style' => 'filter'
								),
								array
								( // check label
									'type' => 'label',
									'id' => 'lbl_check_details',
									'value' => lang('Show details'),
									'style' => 'filter'												
								),
								array
								( 
									'id'     => 'check_show_details',
									'value'    => 0,
									'type' => 'checkbox',
									'tab_index' => 6,
									'style' => 'filter'			                                            
								),
								array
								( // check label
									'type' => 'label',
									'id' => 'lbl_check_cost',
									'value' => lang('Show calculated cost'),
									'style' => 'filter'													
								),
								array
								( 
									'id'     => 'check_calculated_cost',
									'value'    => 0,
									'type' => 'checkbox',
									'tab_index' => 7,
									'style' => 'filter'			                                            
								),
								array
								( 		                                       
									'id'     => 'check_mark_draft',
									'value'    => 0,
									'type' => 'checkbox',
									'tab_index' => 10			                                            
								),														
								array
								( // check label
									'type' => 'label',
									'id' => 'lbl_check_mark',
									'value' => lang('Mark as DRAFT')														
								),			                                        		
								array
								( 
									'id'     => 'check_calculated_cost_tender',
									'value'    => 0,
									'type' => 'checkbox',
									'tab_index' => 9			                                        
								),													
								array
								( // check label
									'type' => 'label',
									'id' => 'lbl_check_cost_tender',
									'value' => lang('Show calculated cost')													
								),			                                        		
								array
								( 
									'id' => 'btn_view_tender',
									'value'    => lang('View tender'),
									'type' => 'button',
									'tab_index' => 8
								)
							)
						)
					);				

			}

			$uicols = array (
				'name'			=>	array('hour_id','post','code','hours_descr','unit_name','billperae','quantity','cost','deviation','result','wo_hour_category','cat_per_cent'),
				'input_type'	=>	array('hidden','text','text','text','text','text','text','text','text','text','text','text'),
				'descr'			=>	array('',lang('Post'),lang('Code'),lang('Descr'),lang('Unit'),lang('Bill per unit'),lang('Quantity'),lang('Cost'),lang('deviation'),lang('result'),lang('Category'),lang('percent')),
				'className'		=> 	array('','','','','','rightClasss','rightClasss','rightClasss','rightClasss','rightClasss','','rightClasss')
			);

			$wo_hour_list = array();
			$wo_hour_list = $common_data['content'];

			$j=0;
			if (isset($wo_hour_list) && is_array($wo_hour_list))
			{
				foreach($wo_hour_list as $wo_hour)
				{											
					for ($i=0;$i<count($uicols['name']);$i++)
					{											
						if ($uicols['name'][$i] == 'deviation') 
						{
							if (is_numeric($wo_hour[$uicols['name'][$i]])) 
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $wo_hour[$uicols['name'][$i]];
							} else {
								$datatable['rows']['row'][$j]['column'][$i]['value'] 	= '';
							}
						} else {
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $wo_hour[$uicols['name'][$i]];
						}													
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}

					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();						
			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'hour_id',
							'source'	=> 'hour_id'
						)
					)
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'deviation',
					'text' 			=> lang('Deviation'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.deviation',
						'workorder_id'	=> $workorder_id

					)),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'deviation',
					'text' 				=> lang('open deviation in new window'),
					'action'			=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.deviation',
						'workorder_id'	=> $workorder_id,
						'target'		=> '_blank'

					)),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'edit',
					'text' 			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'workorder_id'	=> $workorder_id,
						'from'			=> 'index'
					)),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'edit',
					'text' 				=> lang('open edit in new window'),
					'action'			=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'workorder_id'	=> $workorder_id,
						'from'			=> 'index',								
						'target'		=> '_blank'

					)),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'delete',
					'text' 			=> lang('Delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.index',
						'workorder_id'	=> $workorder_id,
						'delete'	=> true
					)),
					'parameters'	=> $parameters
				);

			unset($parameters);

			$datatable['rowactions']['action_form'][] = array
				(
					'my_name' 		=> 'add_prizebook',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.prizebook',
						'workorder_id'	=> $workorder_id
					))
				);

			$datatable['rowactions']['action_form'][] = array
				(
					'my_name' 		=> 'add_template',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uitemplate.index',
						'lookup'=> true,
						'workorder_id'	=> $workorder_id
					))
				);	

			$datatable['rowactions']['action_form'][] = array
				(
					'my_name' 		=> 'add_custom',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'from'=> 'index',
						'workorder_id'	=> $workorder_id
					))
				);

			$datatable['rowactions']['action_form'][] = array
				(
					'my_name' 		=> 'save_template',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.save_template',
						'from'=> 'index',
						'workorder_id'	=> $workorder_id
					))
				);

			$datatable['rowactions']['action_form'][] = array
				(
					'my_name' 		=> 'print_view',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.view',
						'from'=> 'index',
						'workorder_id'	=> $workorder_id
					))
				);

			$datatable['rowactions']['action_form'][] = array
				(
					'my_name' 		=> 'view_tender',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.tender',
						'from'=> 'index',
						'workorder_id'	=> $workorder_id
					))
				);	

			$uicols_count	= count($uicols['descr']);
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['className']		= $uicols['className'][$i];
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			$datatable['exchange_values'] = '';
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($wo_hour_list);
			$datatable['pagination']['records_total'] 	= $this->bopricebook->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'hour_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname	= lang('Workorder');
			$function_msg	= lang('list hours');

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');	

			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'table_sum'			=> $common_data['table_sum'][0],
					'workorder_data'	=> $common_data['workorder_data'],
					'total_hours_records'	=> $common_data['total_hours_records'],
					'lang_total_records'	=> lang('Total records')
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						elseif(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if(isset($datatable['rowactions']['action_form']) && is_array($datatable['rowactions']['action_form']))
			{
				$json ['rights_form'] = $datatable['rowactions']['action_form'];
			}

			// message when editting & deleting records
			if(isset($receipt) && is_array($receipt))
			{
				$json ['message'][] = $receipt;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'wo_hour.index', 'property' );

			$this->save_sessiondata();												
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$workorder_id	= phpgw::get_var('workorder_id'); // in case of bigint
			if( phpgw::get_var('done', 'bool') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiwo_hour.index', 'workorder_id'=> $workorder_id));
			}
			
			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour', 'files'));

			$show_cost		= phpgw::get_var('show_cost', 'bool');
			$show_details	= true;//phpgw::get_var('show_details', 'bool');
			$to_email 		= phpgw::get_var('to_email', 'string');
			$update_email	= phpgw::get_var('update_email', 'bool');
			$send_order		= phpgw::get_var('send_order', 'bool');
			$no_email		= phpgw::get_var('no_email', 'bool');
			$values			= phpgw::get_var('values');
			$print			= phpgw::get_var('print', 'bool');
			$sent_ok		= phpgw::get_var('print', 'bool');
			$send_as_pdf	= phpgw::get_var('send_as_pdf', 'bool');
			$email_receipt	= phpgw::get_var('email_receipt', 'bool');
			
/*
			if($update_email)
			{
				$this->bo->update_email($to_email,$workorder_id);
			}
*/

			$sms_client_order_notice =  isset($this->config->config_data['sms_client_order_notice']) ? $this->config->config_data['sms_client_order_notice'] : '';

			if($sms_client_order_notice)
			{
				$sms_client_order_notice = str_replace(array('__order_id__'), array($workorder_id), $sms_client_order_notice);
			}

			$workorder = $this->boworkorder->read_single($workorder_id);
			$workorder_history = $this->boworkorder->read_record_history($workorder_id);

			$table_header_history[] = array
				(
					'lang_date'		=> lang('Date'),
					'lang_user'		=> lang('User'),
					'lang_action'		=> lang('Action'),
					'lang_new_value'	=> lang('New value')
				);


			$common_data	= $this->common_data($workorder_id);
			if($show_details)
			{
				$values_hour = $common_data['content'];
			}

			$project	= $this->boproject->read_single($common_data['workorder']['project_id'],array(),true);

			$bolocation	= CreateObject('property.bolocation');

			$location_data=$bolocation->initiate_ui_location(array(
				'values'	=> $project['location_data'],
				'type_id'	=> count(explode('-',$project['location_data']['location_code'])),
				'no_link'	=> false, // disable lookup links for location type less than type_id
				'tenant'	=> $project['location_data']['tenant_id'],
				'lookup_type'	=> 'view'
			));

			if($project['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if(!$show_cost)
			{
				$m	= count($values_hour);
				for ($i=0;$i<$m;$i++)
				{
					unset($values_hour[$i]['cost']);
					unset($values_hour[$i]['billperae']);
				}
				unset($common_data['table_sum'][0]['value_total_sum']);
			}

			$table_header[] = array
				(
					'lang_post'			=> lang('Post'),
					'lang_code'			=> lang('Code'),
					'lang_descr'		=> lang('descr'),
					'lang_unit'			=> lang('Unit'),
					'lang_billperae'	=> lang('Bill per unit'),
					'lang_quantity'		=> lang('Quantity'),
					'lang_cost'			=> lang('cost')
				);


			if( !$print && !$no_email)
			{
				$table_send[] = array
					(
						'lang_send_order'		=> lang('Send Order'),
						'lang_send_order_statustext'	=> lang('Send this order by email')
					);

				$table_done[] = array
					(
						'lang_done'			=> lang('Done'),
						'lang_done_statustext'		=> lang('Back to calculation'),
						'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.index', 'workorder_id'=> $workorder_id))
					);
			}

			$dateformat				= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date					= $GLOBALS['phpgw']->common->show_date(time(),$dateformat);


			$GLOBALS['phpgw']->preferences->set_account_id($workorder['user_id'], true);

			$from_name =	$GLOBALS['phpgw']->accounts->get($workorder['user_id'])->__toString();
			$from_email =	"{$from_name}<{$GLOBALS['phpgw']->preferences->data['property']['email']}>";

			if($this->config->config_data['wo_status_sms'])
			{
				$sms_location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');
				$config_sms	= CreateObject('admin.soconfig',$sms_location_id);

				$sms_data['heading'] = lang('Send the following SMS-message to %1 to update status for this order:',$config_sms->config_data['common']['gateway_number']);
				$sms_data['message'] = 'status ' . $workorder_id . ' [' . lang('status code') .']';
				$sms_data['status_code'][0]['name'] = '1 => ' . lang('performed');
				$sms_data['status_code'][1]['name'] = '2 => ' . lang('No access');
				$sms_data['status_code'][2]['name'] = '3 => ' . 'I arbeid';
				$sms_data['status_code_text'] = lang('status code');
				$sms_data['example'] = 'status ' . $workorder_id . ' 1';
				$sms_data['lang_example'] = lang('Example');
			}

			$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.project.workorder',
					'id'				=> $workorder_id,
					'responsible'		=> $workorder['vendor_id'],
					'responsible_type'  => 'vendor',
					'action'			=> 'remind',
					'deadline'			=> '',
					'created_by'		=> '',
				);


			$lang_reminder = '';
/*
			$pending_action = execMethod('property.sopending_action.get_pending_action', $action_params);

			$lang_reminder = '';
			if( $pending_action )
			{
				$reminder = (int)$pending_action[0]['reminder'] +1;
				$lang_reminder = lang('reminder') . " # {$reminder}";
			}
			else if ($this->boworkorder->order_sent_adress)
			{
				$lang_reminder = lang('reminder') . " # 1";			
			}
 */			
			if ($this->boworkorder->order_sent_adress || $sent_ok)
			{
				$lang_reminder = lang('reminder');			
			}

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id'		=> $project['contact_id'],
				'field'				=> 'contact',
				'type'				=> 'view'));


			$location_code = isset($common_data['workorder']['location_code']) && $common_data['workorder']['location_code'] ? $common_data['workorder']['location_code'] : $project['location_code'];

			$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'allrows' => true));
			if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
			{
				$gabinfo = array_shift($gabinfos);
				$gab_id = $gabinfo['gab_id'];
			}

			$formatted_gab_id = '';
			if(isset($gab_id))
			{
				$formatted_gab_id = substr($gab_id,4,5).' / '.substr($gab_id,9,4).' / '.substr($gab_id,13,4).' / '.substr($gab_id,17,3);
			}

			$email_data = array
				(
					'formatted_gab_id'				=> $formatted_gab_id,
					'org_name'						=> isset($this->config->config_data['org_name']) ? "{$this->config->config_data['org_name']}::" : '',
					'location_data'					=> $location_data,
					'lang_workorder'				=> lang('Workorder ID'),
					'workorder_id'					=> $workorder_id,
					'lang_reminder'					=> $lang_reminder,

					'lang_date'						=> lang('Date'),
					'date'							=> $date,

					'lang_start_date'				=> lang('Start date'),
					'start_date'					=> $workorder['start_date'],

					'lang_end_date'					=> lang('End date'),
					'end_date'						=> $workorder['end_date'],

					'lang_from'						=> lang('From'),
					'from_name'						=> $from_name,
					'from_email'					=> $from_email,
					'from_phone'					=> $GLOBALS['phpgw']->preferences->data['property']['cellphone'],
					'lang_district'					=> lang('District'),
					'district'						=> $project['location_data']['district_id'],
					'ressursnr'						=> isset($GLOBALS['phpgw']->preferences->data['property']['ressursnr']) ? $GLOBALS['phpgw']->preferences->data['property']['ressursnr'] : '',

					'lang_to'						=> lang('To'),
					'to_name'						=> $workorder['vendor_name'],

					'lang_title'					=> lang('Title'),
					'title'							=> $workorder['title'],

					'lang_descr'					=> lang('Description'),
					'descr'							=> $workorder['descr'],

					'lang_budget_account'			=> lang('Budget account'),
					'budget_account'				=> $workorder['b_account_id'],

					'lang_sum_calculation'			=> lang('Sum of calculation'),
					'sum_calculation'				=>$common_data['table_sum'][0]['value_total_sum'],

					'lang_contact_phone'			=> lang('Contact phone'),
					'contact_phone'					=> $project['contact_phone'],

					'lang_branch'					=> lang('branch'),
					'branch_list'					=> $this->boproject->select_branch_p_list($project['project_id']),
					'other_branch'					=> $project['other_branch'],

					'key_responsible_list'			=> $this->boproject->select_branch_list($project['key_responsible']),
					'lang_key_responsible'			=> lang('key responsible'),

					'key_fetch_list'				=> $this->boproject->select_key_location_list($workorder['key_fetch']),
					'lang_key_fetch'				=> lang('Where to pick up the key'),

					'key_deliver_list'				=> $this->boproject->select_key_location_list($workorder['key_deliver']),
					'lang_key_deliver'				=> lang('Where to deliver the key'),

					'currency'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

					'lang_cost_tax'					=> lang('Cost (incl tax):'),
					'lang_materials'				=> lang('Materials:__________'),
					'lang_work'						=> lang('work:____________'),

					'table_header_view_order'		=> $table_header,
					'values_view_order'				=> $values_hour,
					'sms_data'						=> $sms_data,
					'use_yui_table' 				=> true,
					'contact_data'					=> $contact_data,
					'order_footer_header'			=> $this->config->config_data['order_footer_header'],
					'order_footer'					=> $this->config->config_data['order_footer']
				);

			if($send_order && !$to_email && !$workorder['mail_recipients'])
			{
				$receipt['error'][]=array('msg'=>lang('No mailaddress is selected'));
			}

			if($to_email || $print || ($workorder['mail_recipients'][0] && $_POST['send_order']))
			{
				if(isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
				{
					$approve_role = execMethod('property.boinvoice.check_role', $project['ecodimb'] ? $project['ecodimb'] : $workorder['ecodimb']);

					$_ok = false;
					if($approve_role['is_supervisor'])
					{
						$_ok = true;
					}
					else if( $approve_role['is_budget_responsible'] )
					{
						$_ok = true;					
					}
					else if( $workorder['approved'] )
					{
						$_ok = true;					
					}

					if(!$_ok)
					{
						phpgwapi_cache::message_set( lang('order is not approved'), 'error' );
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiwo_hour.view', 'workorder_id'=> $workorder_id, 'from' => phpgw::get_var('from')));
					}
					unset($_ok);
				}

				$_to = isset($workorder['mail_recipients'][0]) && $workorder['mail_recipients'][0] ? implode(';', $workorder['mail_recipients']) : $to_email;
				$email_data['use_yui_table'] = false;

				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/wo_hour'));
				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/location_view'));
				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/contact_view'));

				$this->create_html->set_var('phpgw',array('email_data' => $email_data));

				$email_data['use_yui_table'] = true;

				$this->create_html->xsl_parse();
				$this->create_html->xml_parse();

				$xml = new DOMDocument;
				$xml->loadXML($this->create_html->xmldata);

				$xsl = new DOMDocument;
				$xsl->loadXML($this->create_html->xsldata);

				// Configure the transformer
				$proc = new XSLTProcessor;
				$proc->registerPHPFunctions(); // enable php functions
				$proc->importStyleSheet($xsl); // attach the xsl rules

				$header =  <<<HTML
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<body>
HTML;

				$footer =  <<<HTML
	</body>
</html>
HTML;

				$html =  $proc->transformToXML($xml);

				if($print)
				{
					echo $header;
					echo <<<HTML
						<script language="Javascript1.2">
						<!--
							document.write("<form><input type=button "
							+"value=\"Print Page\" onClick=\"window.print();\"></form>");
						//-->
						</script>
HTML;

					echo $html;
					echo $footer;
					exit;
				}

				if($GLOBALS['phpgw']->preferences->data['property']['order_email_rcpt']==1)
				{
					$bcc = $from_email;
				}

				$subject  = lang('Workorder').": ".$workorder_id;

				$address_element = execMethod('property.botts.get_address_element', $location_code);
				$_address = array();
				foreach($address_element as $entry)
				{
					$_address[] = "{$entry['text']}: {$entry['value']}";
				}
				
				if($_address)
				{
					$subject .= ', ' . implode(', ', $_address);
				}
				unset($_address);
				unset($address_element);

				$attachments = array();
				$attachment_log = '';
				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles	= CreateObject('property.bofiles');
						$attachments = $bofiles->get_attachments("/workorder/{$workorder_id}/", $values['file_action']);
						$attachment_log = lang('attachments') . ': ' . implode(', ',$values['file_action']);
					}

					if($send_as_pdf)
					{
						$pdfcode = $this->pdf_order($workorder_id, $show_cost);
						if($pdfcode)
						{							
							$dir =  "{$GLOBALS['phpgw_info']['server']['temp_dir']}/pdf_files";

							//save the file
							if (!file_exists($dir))
							{
								mkdir ($dir,0777);
							}
							$fname = tempnam($dir.'/','PDF_').'.pdf';
							$fp = fopen($fname,'w');
							fwrite($fp,$pdfcode);
							fclose($fp);

							$attachments[] = array
								(
									'file' => $fname,
									'name' => "order_{$workorder_id}.pdf",
									'type' => 'application/pdf'
								);						
						}
						$body = lang('order') . '.</br></br>' . lang('see attachment');
					}
					else
					{
						$body = $header . $html . $footer;
					}

					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}
					$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, $body, '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments, $email_receipt);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
				}

				
				if ($rcpt)
				{
					$_attachment_log = $attachment_log ? "::$attachment_log" : '';
					$historylog	= CreateObject('property.historylog','workorder');
					$historylog->add('M',$workorder_id,"{$_to}{$_attachment_log}");
					$receipt['message'][]=array('msg'=>lang('Workorder is sent by email!'));
					if($attachment_log)
					{
						$receipt['message'][]=array('msg' => $attachment_log);
					}

					if( phpgw::get_var('notify_client_by_sms', 'bool') 
						&& $sms_client_order_notice
						&& (isset($project['contact_phone'])
						&& $project['contact_phone']
						|| phpgw::get_var('to_sms_phone')))
					{
						$to_sms_phone = phpgw::get_var('to_sms_phone');
						$to_sms_phone = $to_sms_phone ? $to_sms_phone : $project['contact_phone'];
						$project['contact_phone'] = $to_sms_phone;
						
						$sms	= CreateObject('sms.sms');
						$sms->websend2pv($this->account,$to_sms_phone,str_replace(array('__order_id__'), array($workorder_id), $this->config->config_data['sms_client_order_notice']));
						$historylog->add('MS',$workorder_id,$to_sms_phone);
					}
					
					if( $this->boworkorder->order_sent_adress )
					{
						$action_params = array
							(
								'appname'			=> 'property',
								'location'			=> '.project.workorder',
								'id'				=> $workorder_id,
								'responsible'		=> $workorder['vendor_id'],
								'responsible_type'  => 'vendor',
								'action'			=> 'remind',
								'remark'			=> '',
								'deadline'			=> ''
							);

						$reminds = execMethod('property.sopending_action.set_pending_action', $action_params);
					}
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('The recipient did not get the email:'));
					$receipt['error'][]=array('msg'=>lang('From') . ' ' . $from_email);
					$receipt['error'][]=array('msg'=>lang('To') . ' ' . $_to);
				}
			}

			if( $this->boworkorder->order_sent_adress )
			{
				$to_email= $this->boworkorder->order_sent_adress;
			}

			$email_list	= $this->bo->get_email($to_email,$workorder['vendor_id']);
			if(count($email_list)==1)
			{
				$to_email= $email_list[0]['email'];
				unset($email_list);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_file_data = array
				(
					'menuaction'	=> 'property.uiworkorder.view_file',
					'id'			=> $workorder_id
				);

			//---datatable settings---------------------------------------------------			

			$table_view_order = array();
			if(count($email_data['values_view_order']))
			{

				for($i = 0;$i<count($email_data['values_view_order']);$i++)
				{
					$table_view_order[$i]['post'] 		= $email_data['values_view_order'][$i]['post'];
					$table_view_order[$i]['code'] 		= $email_data['values_view_order'][$i]['code'];
					$table_view_order[$i]['descr'] 		= $email_data['values_view_order'][$i]['hours_descr']."<br>".$email_data['values_view_order']['remark'];
					$table_view_order[$i]['unit'] 		= $email_data['values_view_order'][$i]['unit'];
					$table_view_order[$i]['unit_name']	= $email_data['values_view_order'][$i]['unit_name'];
					$table_view_order[$i]['quantity'] 	= $email_data['values_view_order'][$i]['quantity'];
					$table_view_order[$i]['billperae']	= $email_data['values_view_order'][$i]['billperae'];
					$table_view_order[$i]['cost'] 		= $email_data['values_view_order'][$i]['cost'];
				}
			}

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($table_view_order),
					'total_records'			=> count($table_view_order),
					'is_paginator'			=> 0,
					'footer'				=> 0
				);	

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'post',		'label' => lang('Post'),		'sortable' => true,'resizeable' => true),
														array('key' => 'code',		'label' => lang('Code'),		'sortable' => true,'resizeable' => true),
														array('key' => 'descr',		'label' => lang('descr'),		'sortable' => true,'resizeable' => true),
														array('key' => 'unit_name',	'label' => lang('Unit'),		'sortable' => true,'resizeable' => true),
														array('key' => 'quantity',	'label' => lang('Quantity'),	'sortable' => true,'resizeable' => true),
														array('key' => 'billperae',	'label' => lang('Bill per unit'),'sortable' => true,'resizeable' => true),
														array('key' => 'cost',		'label' => lang('cost'),		'sortable' => true,'resizeable' => true)))
				);	

			$workorder_history = $this->boworkorder->read_record_history($workorder_id); // second time...(after the order is sendt)
			$datavalues[1] = array
				(
					'name'					=> "1",
					'values' 				=> json_encode($workorder_history),
					'total_records'			=> count($workorder_history),
					'is_paginator'			=> 0,
					'footer'				=> 0
				);	

			$myColumnDefs[1] = array
				(
					'name'		=> "1",
					'values'	=>	json_encode(array(	array('key' => 'value_date',	'label' => lang('Date'),	'sortable' => true,'resizeable' => true),
														array('key' => 'value_user',	'label' => lang('User'),	'sortable' => true,'resizeable' => true),
														array('key' => 'value_action',	'label' => lang('Action'),	'sortable' => true,'resizeable' => true),
														array('key' => 'value_new_value','label' => lang('New value'),'sortable' => true,'resizeable' => true)))
				);	


			//----------------------------------------------datatable settings--------	
			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
					'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
					'datatable'							=> $datavalues,
					'myColumnDefs'						=> $myColumnDefs,

					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'lang_mail'							=> lang('E-Mail'),
					'lang_update_email'					=> lang('Update email'),
					'lang_update_email_statustext'		=> lang('Check to update the email-address for this vendor'),
					'value_sms_client_order_notice'		=> $sms_client_order_notice,
					'value_sms_phone'					=> $project['contact_phone'],
					'lang_to_email_address_statustext'	=> lang('The address to which this order will be sendt'),
					'to_email'							=> $to_email,
					'email_list'						=> $email_list,
					'requst_email_receipt'				=> isset($GLOBALS['phpgw']->preferences->data['request_order_email_rcpt']) && $GLOBALS['phpgw']->preferences->data['property']['request_order_email_rcpt']==1 ? 1 : 0,
					'lang_select_email'					=> lang('Select email'),
					'send_order_action'					=> $GLOBALS['phpgw']->link('/index.php',array(
																'menuaction'	=> 'property.uiwo_hour.view',
																'send'			=> true,
																'workorder_id'	=> $workorder_id,
																'show_details'	=> $show_details,
																'sent_ok'		=> $rcpt)),
					'lang_no_history'					=> lang('No history'),
					'lang_history'						=> lang('History'),
					'workorder_history'					=> $workorder_history,
					'table_header_history'				=> $table_header_history,
					'email_data'						=> $email_data,
					'no_email'							=> $no_email,
					'table_send'						=> $table_send,
					'table_done'						=> $table_done,

					'link_view_file'					=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
					'files'								=> isset($workorder['files']) ? $workorder['files'] : '',
					'lang_files'						=> lang('files'),
					'lang_filename'						=> lang('Filename'),
					'lang_file_action'					=> lang('attach file'),
					'lang_view_file_statustext'			=> lang('click to view file'),
					'lang_file_action_statustext'		=> lang('Check to attach file'),
					'lang_print'						=> lang('print'),
					'value_show_cost'					=> $show_cost,
					'lang_print_statustext'				=> lang('open this page as printerfrendly'),
					'print_action'						=> "javascript:openwindow('"
															. $GLOBALS['phpgw']->link('/index.php', array
																(
																	'menuaction'	=> 'property.uiwo_hour.view',
																	'workorder_id'	=> $workorder_id,
																	'show_cost'		=> $show_cost,
																	'show_details'	=> $show_details,
																	'print'			=> true
																	)) . "','1000','1200')",
					'pdf_action'						=> "javascript:openwindow('"
															. $GLOBALS['phpgw']->link('/index.php', array
																(
																	'menuaction'	=> 'property.uiwo_hour.pdf_order',
																	'workorder_id'	=> $workorder_id,
																	'show_cost'		=> $show_cost,
																	'show_details'	=> $show_details,
																	'preview'		=> true,
																	)) . "','100','100')",
					'mail_recipients' 					=> isset($workorder['mail_recipients']) && is_array($workorder['mail_recipients']) ? implode(';', $workorder['mail_recipients']) : ''
				);


			//---datatable settings-----------------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'wo_hour.view', 'property' );
			//------------------------------datatable settings--

			$appname		= lang('Workorder');
			$function_msg	= $this->boworkorder->order_sent_adress ? lang('ReSend order') :lang('Send order');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}


		protected function _get_order_details($values_hour,	$show_cost = false)
		{
			$translations = array
				(
					'post'			=> lang('post'),
					'code'			=> lang('code'),
					'descr'			=> lang('descr'),
					'unit'			=> lang('unit'),
					'quantity'		=> lang('quantity'),
					'billperae'		=> lang('bill per unit'),
					'cost'			=> lang('cost')
				);

			$grouping_descr_old	= '';
			$content = array();
			foreach($values_hour as $hour)
			{
				$descr= $hour['hours_descr'];

				if($hour['remark'])
				{
					$descr .= "\n" . $hour['remark'];
				}

				if(!$show_cost)
				{
					unset($hour['billperae']);
					unset($hour['cost']);
				}

				if($hour['grouping_descr']!=$grouping_descr_old)
				{
					$content[] = array
						(
							$translations['post']		=> $hour['grouping_descr'],
							$translations['code']		=> '',
							$translations['descr']		=> '',
							$translations['unit']		=> '',
							$translations['quantity']	=> '',
							$translations['billperae']	=> '',
							$translations['cost']		=> ''
						);
				}

				$grouping_descr_old	= $hour['grouping_descr'];

				$content[] = array
					(
						$translations['post']			=> $hour['post'],
						$translations['code']			=> $hour['code'],
						$translations['descr']			=> $descr,
						$translations['unit']			=> $hour['unit_name'],
						$translations['quantity']		=> $hour['quantity'],
						$translations['billperae']		=> $hour['billperae'],
						$translations['cost']			=> $hour['cost']
					);
			}

			return $content;
		}


		function pdf_order($workorder_id = '', $show_cost = false)
		{
			$pdf					= CreateObject('phpgwapi.pdf');

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			if(!$workorder_id)
			{
				$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
				$show_cost = phpgw::get_var('show_cost', 'bool');
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			}
			if(!$show_cost)
			{
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}

			$preview = phpgw::get_var('preview', 'bool');

			$common_data		= $this->common_data($workorder_id);
			$project			= $this->boproject->read_single($common_data['workorder']['project_id'],array(),true);

			if(isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
			{
				$approve_role = execMethod('property.boinvoice.check_role', $project['ecodimb'] ? $project['ecodimb'] : $common_data['workorder']['ecodimb']);

				$_ok = false;
				if($approve_role['is_supervisor'])
				{
					$_ok = true;
				}
				else if( $approve_role['is_budget_responsible'] )
				{
					$_ok = true;					
				}
				else if( $common_data['workorder']['approved'] )
				{
					$_ok = true;					
				}

				if(!$_ok)
				{
					phpgwapi_cache::message_set( lang('order is not approved'), 'error' );
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiwo_hour.view', 'workorder_id'=> $workorder_id, 'from' => phpgw::get_var('from')));
				}
				unset($_ok);
			}

			$content = $this->_get_order_details($common_data['content'],	$show_cost);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date(time(),$dateformat);

			set_time_limit(1800);
			$pdf -> ezSetMargins(50,70,50,50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();

			if(isset($this->config->config_data['order_logo']) && $this->config->config_data['order_logo'])
			{
				$pdf->addJpegFromFile($this->config->config_data['order_logo'],
					40,
					800,
					isset($this->config->config_data['order_logo_width']) && $this->config->config_data['order_logo_width'] ? $this->config->config_data['order_logo_width'] : 80
				);
			}
			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,40,578,40);
			//	$pdf->line(20,820,578,820);
			//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50,28,6,$this->config->config_data['org_name']);
			$pdf->addText(300,28,6,$date);


			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');

			//			$pdf->ezSetDy(-100);

			$pdf->ezStartPageNumbers(500,28,6,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$data = array
				(
					array('col1'=>"{$this->config->config_data['org_name']}\n\nOrg.nr: {$this->config->config_data['org_unit_id']}",'col2'=>lang('Order'),'col3'=>lang('order id') . "\n\n{$workorder_id}")
				);		

			$pdf->ezTable($data,array('col1'=>'','col2'=>'','col3'=>''),''
				,array('showHeadings'=>0,'shaded'=>0,'xPos'=>0
				,'xOrientation'=>'right','width'=>500
				,'cols'=>array
				(
					'col1'=>array('justification'=>'right','width'=>200, 'justification'=>'left'),
					'col2'=>array('justification'=>'right','width'=>100, 'justification'=>'center'),
					'col3'=>array('justification'=>'right','width'=>200),
				)

			));

			$delivery_address = lang('delivery address'). ':';
			if(isset($this->config->config_data['delivery_address']) && $this->config->config_data['delivery_address'])
			{
				$delivery_address .= "\n{$this->config->config_data['delivery_address']}";
			}
			else
			{
				$location_code = isset($common_data['workorder']['location_code']) && $common_data['workorder']['location_code'] ? $common_data['workorder']['location_code'] : $project['location_code'];
				$address_element = execMethod('property.botts.get_address_element', $location_code);
				foreach($address_element as $entry)
				{
					$delivery_address .= "\n{$entry['text']}: {$entry['value']}";
				}
			}

			$invoice_address = lang('invoice address') . ":\n{$this->config->config_data['invoice_address']}";

			$GLOBALS['phpgw']->preferences->set_account_id($common_data['workorder']['user_id'], true);

			$from_name =	$GLOBALS['phpgw']->accounts->get($common_data['workorder']['user_id'])->__toString();

			$from = lang('date') . ": {$date}\n";
			$from .= lang('dimb') .": {$common_data['workorder']['ecodimb']}\n";
			$from .= lang('from') . ":\n   {$from_name}";
			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['email']}";
			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['cellphone']}";

			$data = array
				(
					array('col1'=>lang('vendor') . ":\n{$common_data['workorder']['vendor_name']}",'col2' => $delivery_address),
					array('col1'=>$from,'col2'=>$invoice_address)
				);		

			$pdf->ezTable($data,array('col1'=>'','col2'=>''),''
				,array('showHeadings'=>0,'shaded'=>0,'xPos'=>0
				,'xOrientation'=>'right','width'=>500,'showLines'=> 2
				,'cols'=>array
				(
					'col1'=>array('justification'=>'right','width'=>250, 'justification'=>'left'),
					'col2'=>array('justification'=>'right','width'=>250, 'justification'=>'left'),
				)

			));

			$pdf->ezText(lang('title').':',20);
			$pdf->ezText($common_data['workorder']['title'],14);
			$pdf->ezSetDy(-20);

			$pdf->ezText(lang('descr').':',20);
			$pdf->ezText($common_data['workorder']['descr'],14);

			if($content)
			{
				$pdf->ezSetDy(-20);
				$pdf->ezTable($content,'',lang('details'),
					array('xPos'=>0,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 8,'showLines'=> 2,'titleFontSize' => 12,'outerLineThickness'=>2
					,'cols'=>array(
						lang('bill per unit')=>array('justification'=>'right','width'=>50)
						,lang('quantity')=>array('justification'=>'right','width'=>50)
						,lang('cost')=>array('justification'=>'right','width'=>50)
						,lang('unit')=>array('width'=>40)
						,lang('descr')=>array('width'=>120))
					));
			}

			if(isset($this->config->config_data['order_footer_header']) && $this->config->config_data['order_footer_header'])
			{
				if(!$content)
				{
					$pdf->ezSetDy(-100);
				}
				$pdf->ezText($this->config->config_data['order_footer_header'],12);
				$pdf->ezText($this->config->config_data['order_footer'],10);
			}

			$document= $pdf->ezOutput();
			if($preview)
			{
				$pdf->print_pdf($document,'order');
			}
			else
			{
				return $document;
			}
		}

		function tender()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$pdf					= CreateObject('phpgwapi.pdf');
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			$show_cost = phpgw::get_var('show_cost', 'bool');
			$mark_draft = phpgw::get_var('mark_draft', 'bool');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint

			$common_data		= $this->common_data($workorder_id);
			$project			= $this->boproject->read_single($common_data['workorder']['project_id']);

			$content = $this->_get_order_details($common_data['content'],	$show_cost);


			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date('',$dateformat);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf -> ezSetMargins(50,70,50,50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,40,578,40);
			$pdf->line(20,822,578,822);
			$pdf->addText(50,823,6,lang('Chapter') . ' ' .$common_data['workorder']['chapter_id'] . ' ' . $common_data['workorder']['chapter'] );
			$pdf->addText(50,34,6,$this->config->config_data['org_name']);
			$pdf->addText(300,34,6,$date);
			if($mark_draft)
			{
				$pdf->setColor(1,0,0);
				//		$pdf->setColor(66,66,99);
				$pdf->addText(200,400,40,lang('DRAFT'),-10);
				$pdf->setColor(1,0,0);
			}
			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');

			$pdf->ezSetDy(-100);


			$pdf->ezStartPageNumbers(500,28,10,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$pdf->ezText($project['name'],20);
			$pdf->ezText($project['descr'],14);
			$pdf->ezSetDy(-50);
			$pdf->ezText(lang('Order') . ': ' . $workorder_id . ' ' .$common_data['workorder']['title'],14);
			$pdf->ezText(lang('Chapter') . ' ' .$common_data['workorder']['chapter_id'] . ' ' . $common_data['workorder']['chapter'] ,14);

			if($content)
			{
				$pdf->ezNewPage();
				$pdf->ezTable($content,'',$project['name'],
					array('xPos'=>70,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 8,'showLines'=> 2,'titleFontSize' => 12,'outerLineThickness'=>2
					,'cols'=>array(
						lang('bill per unit')=>array('justification'=>'right','width'=>50)
						,lang('quantity')=>array('justification'=>'right','width'=>50)
						,lang('cost')=>array('justification'=>'right','width'=>50)
						,lang('unit')=>array('width'=>40)
						,lang('descr')=>array('width'=>120))
					));
			}

			$document= $pdf->ezOutput();
			$pdf->print_pdf($document,'tender');
		}

		function prizebook()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$values = phpgw::get_var('values');


			if($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id,$workorder_id);
				if( phpgw::get_var('phpgw_return_as') == 'json' )
				{
					return "hour ".$hour_id." ".lang("has been deleted");
				}
			}


			if($values['add'])
			{
				$receipt=$this->bo->add_hour($values,$workorder_id);
			}

			$common_data=$this->common_data($workorder_id);

			$workorder	= $common_data['workorder'];

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uiwo_hour.prizebook',
						'workorder_id'	=> $workorder_id,
						'query'				=> $this->query
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiwo_hour.prizebook',"
					."workorder_id:'{$workorder_id}',"
					."query:'{$this->query}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiwo_hour.prizebook',
								'workorder_id'	=> $workorder_id,
								'query'				=> $this->query
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( // mensaje
									'type'	=> 'label',
									'id'	=> 'msg_header',
									'value'	=> '',
									'style' => 'filter'
								),												
								array
								( // boton done
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'tab_index' => 4,
									'value'	=> lang('done')
								),												
								array
								( // boton SAVE
									'type'	=> 'button',
									'id'	=> 'btn_save',
									'tab_index' => 3,
									'value'	=> lang('save')
								),			                                        
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								),
								array
								(
									'type'	=> 'label',
									'id'	=> 'lbl_template',
									'value'	=> ''

								)				                                        
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('hidden','text','hidden','hidden','text','text','text','text','text','text','hidden','varchar','select','varchar'),
				'type'			=>	array('','','','','','','','','','','','text','',''),				
				'name'			=>	array('activity_id','num','branch','vendor_id','descr','base_descr','unit_name','w_cost','m_cost','total_cost','this_index','quantity','wo_hour_cat','cat_per_cent'),
				'formatter'		=>	array('','','','','','','','','','','','','',''),
				'descr'			=>	array('',lang('Activity Num'),lang('Branch'),lang('Vendor'),lang('Description'),lang('Base'),lang('Unit'),lang('Labour cost'),lang('Material cost'),lang('Total Cost'),'',lang('Quantity'),lang('category'),lang('percent')),
				'className'		=> 	array('','','','','','','','rightClasss','rightClasss','rightClasss','','','','')
			);


			if($workorder['vendor_id'])
			{
				$this->bopricebook->cat_id = $workorder['vendor_id'];
				$this->bopricebook->start = $this->start;
				$this->bopricebook->query = $this->query;
				$pricebook_list	= $this->bopricebook->read();
			}

			$values_combo_box	= $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id'));
			$default_value = array ('id'=>'','name'=>lang('no category'));
			array_unshift ($values_combo_box,$default_value);			

			$content = array();
			$j=0;
			if (isset($pricebook_list) && is_array($pricebook_list))
			{
				foreach($pricebook_list as $pricebook)
				{
					$hidden = '';
					$hidden .= " <input name='values[activity_id][".$j."]' id='values[activity_id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['activity_id']."'/>";
					$hidden .= " <input name='values[activity_num][".$j."]' id='values[activity_num][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['num']."'/>";
					$hidden .= " <input name='values[unit][".$j."]' id='values[unit][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['unit']."'/>";
					$hidden .= " <input name='values[dim_d][".$j."]' id='values[dim_d][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['dim_d']."'/>";
					$hidden .= " <input name='values[ns3420_id][".$j."]' id='values[ns3420_id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['ns3420_id']."'/>";
					$hidden .= " <input name='values[descr][".$j."]' id='values[descr][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['descr']."'/>";
					$hidden .= " <input name='values[total_cost][".$j."]' id='values[total_cost][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$pricebook['total_cost']."'/>";

					if ($j==0) 
					{
						$hidden .= " <input name='values[add]' id='values[add]'  class='myValuesForPHP'  type='hidden' value='add'/>";
					}
					for ($i=0;$i<count($uicols['name']);$i++)
					{				
						if ($i==0) {
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $pricebook[$uicols['name'][$i]].$hidden;
						} else {
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $pricebook[$uicols['name'][$i]];
						}
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];

						if($uicols['input_type'][$i]=='varchar') 
						{
							$datatable['rows']['row'][$j]['column'][$i]['value'] = "<input name='values[".$uicols['name'][$i]."][".$j."]' id='values[".$uicols['name'][$i]."][".$j."]' size='4' class='myValuesForPHP'/>";
						}
						$select = '';
						if($uicols['input_type'][$i]=='select') 
						{
							$select  .= "<select name='values[".$uicols['name'][$i]."_list][".$j."]' id='values[".$uicols['name'][$i]."_list][".$j."]' class='select_tmp'>";
							for($k = 0; $k < count($values_combo_box); $k++)
							{
								$select  .= "<option value='".$values_combo_box[$k]['id']."'>".$values_combo_box[$k]['name']."</option>";
							}
							$select  .= "</select>";	
							$select  .= " <input name='values[".$uicols['name'][$i]."][".$j."]' id='values[".$uicols['name'][$i]."][".$j."]'  class='myValuesForPHP select'  type='hidden' value=''/>";						
							$datatable['rows']['row'][$j]['column'][$i]['value'] = $select;
						}												
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['className']		= $uicols['className'][$i];

					if ($uicols['name'][$i] == 'num' || $uicols['name'][$i] == 'total_cost')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			//*************************************************/

			$uicols_details = array (
				'name'			=>	array('hour_id','post','code','hours_descr','unit_name','billperae','quantity','cost','deviation','result','wo_hour_category','cat_per_cent'),
				'input_type'	=>	array('hidden','text','text','text','text','text','text','text','text','text','text','text'),
				'descr'			=>	array('',lang('Post'),lang('Code'),lang('Descr'),lang('Unit'),lang('Bill per unit'),lang('Quantity'),lang('Cost'),lang('deviation'),lang('result'),lang('Category'),lang('percent')),
				'className'		=> 	array('','','','','','rightClasss','rightClasss','rightClasss','rightClasss','rightClasss','','rightClasss')
			);

			$j=0;
			if (isset($common_data['content']) && is_array($common_data['content']))
			{
				foreach($common_data['content'] as $content)
				{
					for ($i=0; $i<count($uicols_details['name']); $i++)
					{
						if ($uicols_details['name'][$i] == 'deviation') 
						{
							if (is_numeric($content[$uicols_details['name'][$i]])) {
								$details['rows'][$j][$uicols_details['name'][$i]] 	= $content[$uicols_details['name'][$i]];
							}
							else
							{
								$details['rows'][$j][$uicols_details['name'][$i]] 	= '';
							}
						}
						else
						{
							$details['rows'][$j][$uicols_details['name'][$i]] 	= $content[$uicols_details['name'][$i]];
						}
					}
					$j++;
				}
			}

			$details['rowactions'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'hour_id',
							'source'	=> 'hour_id'
						)
					)
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'deviation',
					'text' 			=> lang('Deviation'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.deviation',
						'workorder_id'	=> $workorder_id

					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'deviation',
					'text' 				=> lang('open deviation in new window'),
					'action'			=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.deviation',
						'workorder_id'	=> $workorder_id,
						'target'		=> '_blank'

					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'edit',
					'text' 			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'workorder_id'	=> $workorder_id,
						'from'			=> 'prizebook'
					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'edit',
					'text' 				=> lang('open edit in new window'),
					'action'			=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'workorder_id'	=> $workorder_id,
						'from'			=> 'prizebook',								
						'target'		=> '_blank'

					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'delete',
					'text' 			=> lang('Delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.prizebook',
						'workorder_id'	=> $workorder_id,
						'delete'	=> true
					)),
					'parameters'	=> $parameters
				);

			unset($parameters);


			//************************************************/

			$datatable['exchange_values'] = '';
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($pricebook_list);
			$datatable['pagination']['records_total'] 	= $this->bopricebook->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'num'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname	= lang('pricebook');
			$function_msg	= lang('list pricebook');

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');	

			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'details'			=> $details,
					'uicols_details'	=> $uicols_details,
					'table_sum'			=> $common_data['table_sum'][0],
					'workorder_data'	=> $common_data['workorder_data'],
					'total_hours_records'	=> $common_data['total_hours_records'],
					'lang_total_records'	=> lang('Total records')
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			// message when editting & deleting records
			if(isset($receipt) && is_array($receipt))
			{
				$json ['message'][] = $receipt;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'wo_hour.prizebook', 'property' );

			$this->save_sessiondata();
		}


		function template()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$template_id = phpgw::get_var('template_id', 'int');

			$values = $_POST['values'] ? phpgw::get_var('values') : array();

			if($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id,$workorder_id);

				if( phpgw::get_var('phpgw_return_as') == 'json' )
				{
					return "hour ".$hour_id." ".lang("has been deleted");
				}				
			}

			if($values['add'])
			{
				$receipt = $this->bo->add_hour_from_template($values,$workorder_id);
			}

			$common_data=$this->common_data($workorder_id);

			$workorder	= $common_data['workorder'];

			$botemplate		= CreateObject('property.botemplate');


			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uiwo_hour.template',
						'workorder_id'		=> $workorder_id,
						'template_id'		=> $template_id,
						'query'				=> $this->query
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiwo_hour.template',"
					."workorder_id:'{$workorder_id}',"
					."template_id:'{$template_id}',"
					."query:'{$this->query}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiwo_hour.template',
								'workorder_id'	=> $workorder_id,
								'template_id'	=> $template_id,
								'query'			=> $this->query
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( // mensaje
									'type'	=> 'label',
									'id'	=> 'msg_header',
									'value'	=> '',
									'style' => 'filter'
								),												
								array
								( // boton done
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'tab_index' => 4,
									'value'	=> lang('done')
								),												
								array
								( // boton SAVE
									'type'	=> 'button',
									'id'	=> 'btn_save',
									'tab_index' => 3,
									'value'	=> lang('save')
								),			                                        
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								),
								array
								(
									'type'	=> 'label',
									'id'	=> 'lbl_template',
									'value'	=> ''

								)			                                        
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('text','text','text','text','text','varchar','combo','varchar','hidden','hidden','hidden','hidden','hidden','hidden','hidden','hidden','hidden','hidden'),
				'type'			=>	array('','','','','','text','','','text','','','',''),				
				'name'			=>	array('building_part','code','hours_descr','unit_name','billperae','quantity','wo_hour_cat','cat_per_cent','chapter_id','grouping_descr','new_grouping','activity_id','activity_num','remark','ns3420_id','tolerance','cost','dim_d'),
				'formatter'		=>	array('','','','','','','','','','','','','','','','','','',''),
				'descr'			=>	array(lang('Building part'),lang('Code'),lang('Description'),lang('Unit'),lang('Bill per unit'),lang('Quantity'),'','','','','','','','','','','',''),
				'className'		=> 	array('','','','','rightClasss','','','','','','','','','','','','','')
			);

			$values_combo_box	= $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id'));
			$default_value = array ('id'=>'','name'=>lang('no category'));
			array_unshift ($values_combo_box,$default_value);	

			$template_list	= $botemplate->read_template_hour($template_id);

			$grouping_descr_old='';
			$content = array();
			$j=0;
			if (isset($template_list) && is_array($template_list))
			{
				foreach($template_list as $template)
				{

					if($template['grouping_descr'] != $grouping_descr_old)
					{
						$new_grouping	= true;
					}
					else
					{
						$new_grouping	= false;
					}

					$grouping_descr_old	= $template['grouping_descr'];

					if($template['activity_num'])
					{
						$code	= $template['activity_num'];
					}
					else
					{
						$code	= str_replace("-",$template['tolerance'],$template['ns3420_id']);
					}

					$hidden = '';
					$hidden .= " <input name='values[chapter_id][".$j."]' id='values[chapter_id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$template['chapter_id']."'/>";
					$hidden .= " <input name='values[grouping_descr][".$j."]' id='values[grouping_descr][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$template['grouping_descr']."'/>";
					$hidden .= " <input name='values[activity_id][".$j."]' id='values[activity_id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$template['activity_id']."'/>";
					$hidden .= " <input name='values[activity_num][".$j."]' id='values[activity_num][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$template['activity_num']."'/>";
					$hidden .= " <input name='values[unit][".$j."]' id='values[unit][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$template['unit']."'/>";
					$hidden .= " <input name='values[dim_d][".$j."]' id='values[dim_d][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$template['dim_d']."'/>";
					$hidden .= " <input name='values[ns3420_id][".$j."]' id='values[ns3420_id][".$j."]' class='myValuesForPHP'  type='hidden' value='".$template['ns3420_id']."'/>";
					$hidden .= " <input name='values[tolerance][".$j."]' id='values[tolerance][".$j."]' class='myValuesForPHP'  type='hidden' value='".$template['tolerance']."'/>";
					$hidden .= " <input name='values[building_part][".$j."]' id='values[building_part][".$j."]' class='myValuesForPHP'  type='hidden' value='".$template['building_part']."'/>";
					$hidden .= " <input name='values[hours_descr][".$j."]' id='values[hours_descr][".$j."]' class='myValuesForPHP'  type='hidden' value='".$template['hours_descr']."'/>";
					$hidden .= " <input name='values[remark][".$j."]' id='values[remark][".$j."]' class='myValuesForPHP'  type='hidden' value='".$template['remark']."'/>";
					$hidden .= " <input name='values[billperae][".$j."]' id='values[billperae][".$j."]' class='myValuesForPHP'  type='hidden' value='".$template['billperae']."'/>";

					if ($j==0) 
					{
						$hidden .= " <input name='values[add]' id='values[add]'  class='myValuesForPHP'  type='hidden' value='add'/>";
					}

					for ($i=0; $i<count($uicols['name']); $i++)
					{							
						if ($i==0) {
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $template[$uicols['name'][$i]].$hidden;
						} 
						else 
						{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $template[$uicols['name'][$i]];
							if ($uicols['name'][$i] == 'code') 
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $code;
							}
							if($uicols['name'][$i] == 'activity_num')
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $new_grouping;
							}
						}

						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];

						if ($uicols['input_type'][$i]=='varchar') 
						{
							$datatable['rows']['row'][$j]['column'][$i]['value'] = "<input name='values[{$uicols['name'][$i]}][{$j}]' id='values[{$uicols['name'][$i]}][{$j}]' size='4' class='myValuesForPHP'/>";
						}

						if ($uicols['input_type'][$i]=='select') 
						{
							$datatable['rows']['row'][$j]['column'][$i]['value'] = "<input name='values[".$uicols['name'][$i]."][".$j."]' id='values[".$uicols['name'][$i]."][".$j."]' class='myValuesForPHP CheckClass' type='hidden' value=''/> <input type='checkbox' name='values[".$uicols['name'][$i]."_tmp][".$j."]' id='values[".$uicols['name'][$i]."_tmp][".$j."]' class='CheckClass_tmp' value='".$j."' />";
						}

						$select = '';
						if($uicols['input_type'][$i]=='combo') 
						{
							$select  .= "<select name='values[".$uicols['name'][$i]."_list][".$j."]' id='values[".$uicols['name'][$i]."_list][".$j."]' class='combo_tmp'>";
							for($k = 0; $k<count($values_combo_box); $k++)
							{
								$select  .= "<option value='".$values_combo_box[$k]['id']."'>".$values_combo_box[$k]['name']."</option>";
							}
							$select  .= "</select>";	
							$select  .= " <input name='values[".$uicols['name'][$i]."][".$j."]' id='values[".$uicols['name'][$i]."][".$j."]'  class='myValuesForPHP combo'  type='hidden' value=''/>";						
							$datatable['rows']['row'][$j]['column'][$i]['value'] = $select;
						}												
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();
			$uicols_count	= count($uicols['name']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['className']		= $uicols['className'][$i];

					if ($uicols['name'][$i] == 'building_part' || $uicols['name'][$i] == 'billperae')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}



			$uicols_details = array (
				'name'			=>	array('hour_id','post','code','hours_descr','unit_name','billperae','quantity','cost','deviation','result','wo_hour_category','cat_per_cent'),
				'input_type'	=>	array('hidden','text','text','text','text','text','text','text','text','text','text','text'),
				'descr'			=>	array('',lang('Post'),lang('Code'),lang('Descr'),lang('Unit'),lang('Bill per unit'),lang('Quantity'),lang('Cost'),lang('deviation'),lang('result'),lang('Category'),lang('percent')),
				'className'		=> 	array('','','','','','rightClasss','rightClasss','rightClasss','rightClasss','rightClasss','','rightClasss')
			);

			$j=0;
			if (isset($common_data['content']) && is_array($common_data['content']))
			{
				foreach($common_data['content'] as $content)
				{
					for ($i=0; $i<count($uicols_details['name']); $i++)
					{
						if ($uicols_details['name'][$i] == 'deviation') 
						{
							if (is_numeric($content[$uicols_details['name'][$i]])) {
								$details['rows'][$j][$uicols_details['name'][$i]] 	= $content[$uicols_details['name'][$i]];
							}
							else
							{
								$details['rows'][$j][$uicols_details['name'][$i]] 	= '';
							}
						}
						else
						{
							$details['rows'][$j][$uicols_details['name'][$i]] 	= $content[$uicols_details['name'][$i]];
						}
					}
					$j++;
				}
			}


			$details['rowactions'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'hour_id',
							'source'	=> 'hour_id'
						)
					)
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'deviation',
					'text' 			=> lang('Deviation'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.deviation',
						'workorder_id'	=> $workorder_id,
						'from'			=> 'template'
					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'deviation',
					'text' 				=> lang('open deviation in new window'),
					'action'			=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.deviation',
						'workorder_id'	=> $workorder_id,
						'from'			=> 'template',
						'target'		=> '_blank'
					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'edit',
					'text' 			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'workorder_id'	=> $workorder_id,
						'template_id'	=> $template_id,
						'from'			=> 'template'
					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'edit',
					'text' 				=> lang('open edit in new window'),
					'action'			=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.edit',
						'workorder_id'	=> $workorder_id,
						'template_id'	=> $template_id,
						'from'			=> 'template',								
						'target'		=> '_blank'

					)),
					'parameters'	=> $parameters
				);

			$details['rowactions'][] = array
				(
					'my_name' 			=> 'delete',
					'text' 			=> lang('Delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiwo_hour.template',
						'workorder_id'	=> $workorder_id,
						'template_id'	=> $template_id,
						'delete'	=> true
					)),
					'parameters'	=> $parameters
				);

			unset($parameters);


			$datatable['exchange_values'] = '';
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($template_list);
			$datatable['pagination']['records_total'] 	= $this->bopricebook->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'building_part'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname		= lang('Template');
			$function_msg		= lang('list template');


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 		=> $datatable['pagination']['records_returned'],
					'totalRecords' 			=> (int)$datatable['pagination']['records_total'],
					'startIndex' 			=> $datatable['pagination']['records_start'],
					'sort'					=> $datatable['sorting']['order'],
					'dir'					=> $datatable['sorting']['sort'],
					'records'				=> array(),
					'details'				=> $details,
					'uicols_details'		=> $uicols_details,
					'table_sum'				=> $common_data['table_sum'][0],
					'workorder_data'		=> $common_data['workorder_data'],
					'total_hours_records'	=> $common_data['total_hours_records'],
					'lang_total_records'	=> lang('Total records')
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			// message when editting & deleting records
			if(isset($receipt) && is_array($receipt))
			{
				$json ['message'][] = $receipt;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}

			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');	

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			// Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'wo_hour.template', 'property' );

			$this->save_sessiondata();
		}

		function edit()
		{
			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}
			$from			= phpgw::get_var('from');
			$template_id 		= phpgw::get_var('template_id', 'int');
			$workorder_id 		= phpgw::get_var('workorder_id'); // in case of bigint
			$activity_id		= phpgw::get_var('activity_id', 'int');
			$hour_id		= phpgw::get_var('hour_id', 'int');
			$values			= phpgw::get_var('values');
			$values['ns3420_id']	= phpgw::get_var('ns3420_id');
			$values['ns3420_descr']	= phpgw::get_var('ns3420_descr');


			//_debug_array($workorder);


			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour'));

			if ($values['save'])
			{
				if($values['copy_hour'])
				{
					unset($hour_id);
				}

				$values['hour_id'] = $hour_id;

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_hour($values,$workorder_id);

					$hour_id=$receipt['hour_id'];
				}
			}

			if ($hour_id)
			{
				$values = $this->bo->read_single_hour($hour_id);
				$function_msg = lang('Edit hour');
			}
			else
			{
				$function_msg = lang('Add hour');
			}

			$workorder	= $this->boworkorder->read_single($workorder_id);

			//_debug_array($values);

			if($error_id)
			{
				unset($values['hour_id']);
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiwo_hour.edit',
					'activity_id'	=> $activity_id,
					'workorder_id'	=> $workorder_id,
					'template_id'	=> $template_id,
					'hour_id'		=> $hour_id,
					'from'			=> $from
				);

			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->config->config_data['filter_buildingpart']) ? $this->config->config_data['filter_buildingpart'] : array();
			
			if($filter_key = array_search('.project', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.' . $from, 'workorder_id'=> $workorder_id, 'template_id'=> $template_id)),
					'lang_workorder'				=> lang('Workorder'),
					'value_workorder_id'			=> $workorder['workorder_id'],
					'value_workorder_title'			=> $workorder['title'],

					'lang_hour_id'					=> lang('Hour ID'),
					'value_hour_id'					=> $hour_id,

					'lang_copy_hour'				=> lang('Copy hour ?'),
					'lang_copy_hour_statustext'		=> lang('Choose Copy Hour to copy this hour to a new hour'),

					'lang_activity_num'				=> lang('Activity code'),
					'value_activity_num'			=> $values['activity_num'],
					'value_activity_id'				=> $values['activity_id'],

					'lang_unit'						=> lang('Unit'),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),
					'lang_descr'					=> lang('description'),
					'value_descr'					=> $values['hours_descr'],
					'lang_descr_statustext'			=> lang('Enter the description for this activity'),
					'lang_done_statustext'			=> lang('Back to the list'),
					'lang_save_statustext'			=> lang('Save the building'),

					'lang_remark'					=> lang('Remark'),
					'value_remark'					=> $values['remark'],
					'lang_remark_statustext'		=> lang('Enter additional remarks to the description - if any'),

					'lang_quantity'					=> lang('quantity'),
					'value_quantity'				=> $values['quantity'],
					'lang_quantity_statustext'		=> lang('Enter quantity of unit'),

					'lang_billperae'				=> lang('Cost per unit'),
					'value_billperae'				=> $values['billperae'],
					'lang_billperae_statustext'		=> lang('Enter the cost per unit'),

					'lang_total_cost'				=> lang('Total cost'),
					'value_total_cost'				=> $values['cost'],
					'lang_total_cost_statustext'	=> lang('Enter the total cost of this activity - if not to be calculated from unit-cost'),

					'lang_vendor'					=> lang('Vendor'),
					'value_vendor_id'				=> $workorder['vendor_id'],
					'value_vendor_name'				=> $workorder['vendor_name'],

					'lang_dim_d'					=> lang('Dim D'),
					'dim_d_list'					=> $this->bopricebook->get_dim_d_list($values['dim_d']),
					'select_dim_d'					=> 'values[dim_d]',
					'lang_no_dim_d'					=> lang('No Dim D'),
					'lang_dim_d_statustext'			=> lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),

					'lang_unit'						=> lang('Unit'),
					'unit_list'						=> $this->bopricebook->get_unit_list($values['unit']),
					'select_unit'					=> 'values[unit]',
					'lang_no_unit'					=> lang('Select Unit'),
					'lang_unit_statustext'			=> lang('Select the unit for this activity.'),

					'lang_chapter'					=> lang('chapter'),
					'chapter_list'					=> $this->bo->get_chapter_list('select',$workorder['chapter_id']),
					'select_chapter'				=> 'values[chapter_id]',
					'lang_no_chapter'				=> lang('Select chapter'),
					'lang_chapter_statustext'		=> lang('Select the chapter (for tender) for this activity.'),

					'lang_tolerance'				=> lang('tolerance'),
					'tolerance_list'				=> $this->bo->get_tolerance_list($values['tolerance_id']),
					'select_tolerance'				=> 'values[tolerance_id]',
					'lang_no_tolerance'				=> lang('Select tolerance'),
					'lang_tolerance_statustext'		=> lang('Select the tolerance for this activity.'),

					'lang_grouping'					=> lang('grouping'),
					'grouping_list'					=> $this->bo->get_grouping_list($values['grouping_id'],$workorder_id),
					'select_grouping'				=> 'values[grouping_id]',
					'lang_no_grouping'				=> lang('Select grouping'),
					'lang_grouping_statustext'		=> lang('Select the grouping for this activity.'),

					'lang_new_grouping'				=> lang('New grouping'),
					'lang_new_grouping_statustext'	=> lang('Enter a new grouping for this activity if not found in the list'),

					'building_part_list'			=> array('options' => $this->bocommon->select_category_list(array('type'=> 'building_part','selected' =>$values['building_part_id'], 'order' => 'id', 'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),

					'ns3420_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.ns3420')),
					'lang_ns3420'					=> lang('NS3420'),
					'value_ns3420_id'				=> $values['ns3420_id'],
					'lang_ns3420_statustext'		=> lang('Select a standard-code from the norwegian standard'),
					'currency'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
					'lang_wo_hour_category'			=> lang('category'),
					'lang_select_wo_hour_category'	=> lang('no category'),
					'wo_hour_cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['wo_hour_cat'],'type' =>'wo_hours','order'=>'id')),
					'lang_cat_per_cent_statustext'	=> lang('the percentage of the category'),
					'value_cat_per_cent'			=> $values['cat_per_cent'],
					'lang_per_cent'					=> lang('percent')
				);
			//_debug_array($data);
			$appname = lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_hour' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}
			$id 		= phpgw::get_var('id', 'int');
			$workorder_id	= phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id	= phpgw::get_var('hour_id', 'int');
			$deviation_id	= phpgw::get_var('deviation_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');


			//delete for JSON proerty2
			if( phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete_deviation($workorder_id,$hour_id,$deviation_id);	
				return "";	
			}

			if($deviation_id)
			{
				$link_data = array
					(
						'menuaction' => 'property.uiwo_hour.deviation',
						'workorder_id'=>$workorder_id,
						'hour_id'=>$hour_id
					);
				$delete_link_data = array
					(
						'menuaction' => 'property.uiwo_hour.delete',
						'workorder_id'=>$workorder_id,
						'hour_id'=>$hour_id,
						'deviation_id'=>$deviation_id
					);

				$function_msg	= lang('delete deviation');
			}
			else
			{
				$link_data = array
					(
						'menuaction' => 'property.uiwo_hour.index'
					);
				$delete_link_data = array
					(
						'menuaction' => 'property.uiwo_hour.delete',
						'id'=>$id
					);
				$function_msg	= lang('delete hour');
			}

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				if($deviation_id)
				{
					$this->bo->delete_deviation($workorder_id,$hour_id,$deviation_id);
				}
				else
				{
					$this->bo->delete($id);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',$delete_link_data),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname = lang('workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
