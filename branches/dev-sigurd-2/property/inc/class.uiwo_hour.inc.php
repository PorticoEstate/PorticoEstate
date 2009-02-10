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
			'edit_deviation'=> true
		);

		function property_uiwo_hour()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->create_html			= CreateObject('phpgwapi.xslttemplates');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.bowo_hour',true);
			$this->boworkorder			= CreateObject('property.boworkorder');
			$this->boproject			= CreateObject('property.boproject');
			$this->bopricebook			= CreateObject('property.bopricebook');

			$this->bocommon				= CreateObject('property.bocommon');
			$this->config				= CreateObject('phpgwapi.config');

			$this->config->read();

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.project';
			$this->acl_read 			= $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bopricebook->start;
			$this->query				= $this->bopricebook->query;
			$this->sort				= $this->bo->sort;
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
			$workorder_id 	= phpgw::get_var('workorder_id', 'int');
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
						'id'				=> $entry['id'],
						'amount'			=> $entry['amount'],
						'descr'				=> $entry['descr'],
						'entry_date'			=> $entry_date,
						'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.edit_deviation', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour_id, 'id'=> $entry['id'])),
						'lang_edit_statustext'		=> lang('edit the deviation'),
						'text_edit'			=> lang('edit'),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.delete', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour_id, 'deviation_id'=> $entry['id'])),
						'lang_delete_statustext'	=> lang('delete the deviation'),
						'text_delete'			=> lang('delete'),
					);
				}
			}


			$table_header[] = array
			(
				'lang_id'	=> lang('ID'),
				'lang_amount'	=> lang('amount'),
				'lang_descr'	=> lang('Descr'),
				'lang_date'	=> lang('date'),
				'lang_edit'	=> lang('edit'),
				'lang_delete'	=> lang('delete')
			);


			$link_data = array
			(
				'menuaction'	=> 'property.uiwo_hour.edit_deviation',
						'workorder_id'	=> $workorder_id,
						'hour_id'	=> $hour_id
			);


			$data = array
			(
				'sum_deviation'				=> $sum_deviation,
				'table_header_deviation'		=> $table_header,
				'values_deviation'			=> $content,
				'lang_add'				=> lang('add'),
				'lang_add_statustext'			=> lang('add a deviation'),
				'add_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_done'				=> lang('done'),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.index', 'workorder_id'=> $workorder_id))
			);

			$appname			= lang('Workorder');
			$function_msg			= lang('list deviation');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_deviation' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_deviation()
		{
			$workorder_id 	= phpgw::get_var('workorder_id', 'int');
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
				'lang_id'					=> lang('deviation ID'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'lang_date'					=> lang('date'),
				'value_id'					=> $id,
				'value_workorder_id'				=> $workorder_id,
				'value_hour_id'					=> $hour_id,
				'entry_date'					=> $entry_date,
				'value_id'					=> $id,
				'lang_descr_standardtext'			=> lang('Enter a description of the deviation'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the deviation'),
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
			$grouping_descr_old='';

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

						'post'					=> sprintf("%02s",$workorder['chapter_id']) . '.' . sprintf("%02s",$hour['building_part']) . '.' . sprintf("%02s",$hour['grouping_id']) . '.' . sprintf("%03s",$hour['record']),
						'hour_id'				=> $hour['hour_id'],
						'activity_num'				=> $hour['activity_num'],
						'hours_descr'				=> $hour['hours_descr'],
						'activity_descr'			=> $hour['activity_descr'],
						'new_grouping'				=> $new_grouping,
						'grouping_id'				=> $hour['grouping_id'],
						'grouping_descr'			=> $hour['grouping_descr'],
						'ns3420_id'				=> $hour['ns3420_id'],
						'code'					=> $code,
						'remark'				=> $hour['remark'],
						'building_part'				=> $hour['building_part'],
						'quantity'				=> $hour['quantity'],
						'cost'					=> $hour['cost'],
						'unit'					=> $hour['unit'],
						'billperae'				=> $hour['billperae'],
						'deviation'				=> $deviation,
						'result'				=> ($hour['deviation']+$hour['cost']),
						'wo_hour_category'			=> $hour['wo_hour_category'],
						'cat_per_cent'				=> $hour['cat_per_cent'],
						'link_deviation'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.deviation', 'workorder_id'=> $workorder_id, 'hour_id'=> $hour['hour_id'])),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.edit','workorder_id'=> $workorder_id, 'hour_id'=> $hour['hour_id'])),
						'lang_edit_statustext'			=> lang('edit/customise this hour'),
						'lang_delete_statustext'		=> lang('delete this hour'),
						'text_edit'				=> lang('edit'),
						'text_delete'				=> lang('delete')
					);
				}
			}

			$this->bo->update_deviation(array('workorder_id'=>$workorder_id,'sum_deviation'=>$sum_deviation));

//_debug_array($content);

			$table_header[] = array
			(
				'lang_post'		=> lang('Post'),
				'lang_code'		=> lang('Code'),
				'lang_descr'		=> lang('descr'),
				'lang_unit'		=> lang('Unit'),
				'lang_billperae'	=> lang('Bill per unit'),
				'lang_quantity'		=> lang('Quantity'),
				'lang_cost'		=> lang('cost'),
				'lang_deviation '	=> lang('deviation'),
				'lang_result'		=> lang('result'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_category'		=> lang('category'),
				'lang_per_cent'		=> lang('Per Cent'),
				);

			$tax = $this->config->config_data['fm_tax'];

			$sum_result	= $sum_hour+$sum_deviation;

			$addition_percentage = $sum_result * $workorder['addition_percentage']/100;
			$sum_tax	= ($sum_result + $addition_percentage + $workorder['addition_rs'])*$tax/100;
			$total_sum	= $sum_result + $addition_percentage + $workorder['addition_rs'] + $sum_tax;

			$this->bo->update_calculation(array('workorder_id'=>$workorder_id,'calculation'=>($sum_result+$addition_percentage)));

			$table_sum[] = array
			(
				'lang_sum_calculation'			=> lang('Sum calculation'),
				'value_sum_calculation'			=> number_format($sum_hour, 2, ',', ''),
				'lang_addition_rs'			=> lang('Rig addition'),
				'value_addition_rs'			=> number_format($workorder['addition_rs'], 2, ',', ''),
				'lang_addition_percentage'		=> lang('Percentage addition'),
				'value_addition_percentage'		=> number_format($addition_percentage, 2, ',', ''),
				'lang_sum_tax'				=> lang('Sum tax'),
				'value_sum_tax'				=> number_format($sum_tax, 2, ',', ''),
				'lang_total_sum'			=> lang('Total sum'),
				'value_total_sum'			=> number_format($total_sum, 2, ',', ''),
				'lang_sum_deviation'			=> lang('Sum deviation'),
				'sum_deviation'				=> number_format($sum_deviation, 2, ',', ''),
				'sum_result'				=> number_format($sum_result, 2, ',', '')
			);

			$workorder_data = array(
				'link_workorder'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id'=> $workorder_id)),
				'lang_vendor_name'			=> lang('Vendor'),
				'vendor_name'				=> $workorder['vendor_name'],
				'vendor_email'				=> $workorder['vendor_email'],
				'descr'					=> $workorder['descr'],

				'lang_workorder_id'			=> lang('Workorder ID'),
				'workorder_id'				=> $workorder['workorder_id'],
				'lang_project_id'			=> lang('Project ID'),
				'link_project'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uiproject.edit', 'id'=> $workorder['project_id'])),
				'project_id'				=> $workorder['project_id'],

				'lang_workorder_title'			=> lang('Workorder title'),
				'workorder_title'			=> $workorder['title']
				);



			$common_data = array(
				'content' 			=> $content,
				'total_hours_records'		=> count($content),
				'table_header' 			=> $table_header,
				'table_sum' 			=> $table_sum,
				'workorder' 			=> $workorder,
				'workorder_data' 		=> $workorder_data,
				);

			return $common_data;
		}

		function save_template()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour'));

			$values 		= phpgw::get_var('values');
			$workorder_id 	= phpgw::get_var('workorder_id', 'int');

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

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'add_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.save_template', 'workorder_id'=> $workorder_id)),

				'lang_done_statustext'			=> lang('Back to the workorder list'),
				'lang_add_statustext'			=> lang('Adds this workorders calculation as a template for later use'),
				'lang_search_statustext'		=> lang('Adds a new workorder to an existing project'),

				'lang_done'				=> lang('Done'),
				'lang_add'				=> lang('Add'),
				'lang_search'				=> lang('Search'),

				'lang_name'				=> lang('name'),
				'lang_name_statustext'			=> lang('Enter the name the template'),

				'lang_descr'				=> lang('Description'),
				'lang_descr_statustext'			=> lang('Enter a short description of this template'),

				'total_hours_records'			=> $common_data['total_hours_records'],
				'lang_total_records'			=> lang('Total records'),
				'table_header_hour'			=> $common_data['table_header'],
				'values_hour'				=> $common_data['content'],
				'workorder_data' 			=> $common_data['workorder_data']
			);

			$appname	= lang('Workorder');
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
			$workorder_id = phpgw::get_var('workorder_id', 'int');

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

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uiwo_hour.index',
									'workorder_id'	=> $workorder_id
									)
						),
						'fields'	=> array(
                                    'field' => array(
													array( // mensaje
														'type'	=> 'label',
														'id'	=> 'msg_header',
														'value'	=> '',
														'style' => 'filter'
													),	
 			                                        array( 
			                                            'id' => 'btn_save_template',
			                                            'value'    => lang('Save as template'),
			                                            'type' => 'button',
			                                            'tab_index' => 4
			                                        ),	
 			                                        array( 
			                                            'id' => 'btn_add_custom',
			                                            'name' => 'custom',
			                                            'value'    => lang('Add custom'),
			                                            'type' => 'button',
			                                            'tab_index' => 3
			                                        ),
													array( 
														'type'	=> 'button',
														'id'	=> 'btn_add_template',
														'tab_index' => 2,
														'value'	=> lang('Add from template')
													),			                                        			                                        		                                        		                                        																								
													array( 
														'type'	=> 'button',
														'id'	=> 'btn_add_prizebook',
														'tab_index' => 1,
														'value'	=> lang('Add from prizebook')
													)											   				                                        			                                        			                                                                                		                                                                                
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
				
				$datatable['actions']['down-toolbar'] = array('fields'	=> array('field' => array (
                                         array(
                                            'id' => 'btn_print_preview',
                                            'value'    => lang('Print view'),
                                            'type' => 'button',
                                            'tab_index' => 5,
                                            'style' => 'filter'
                                        ),
                                        array( // check label
			                                'type' => 'label',
			                                'id' => 'lbl_check_details',
											'value' => lang('Show details'),
											'style' => 'filter'												
										),
										array( 
                                            'id'     => 'check_show_details',
                                            'value'    => 0,
                                            'type' => 'checkbox',
                                            'tab_index' => 6,
                                            'style' => 'filter'			                                            
                                        ),
                                        array( // check label
			                                'type' => 'label',
			                                'id' => 'lbl_check_cost',
											'value' => lang('Show calculated cost'),
											'style' => 'filter'													
										),
										array( 
                                            'id'     => 'check_calculated_cost',
                                            'value'    => 0,
                                            'type' => 'checkbox',
                                            'tab_index' => 7,
                                            'style' => 'filter'			                                            
                                        ),
                                        array( 		                                       
                                            'id'     => 'check_mark_draft',
                                            'value'    => 0,
                                            'type' => 'checkbox',
                                            'tab_index' => 10			                                            
                                        ),														
		                                array( // check label
			                                'type' => 'label',
			                                'id' => 'lbl_check_mark',
											'value' => lang('Mark as DRAFT')														
										),			                                        		
   										array( 
                                            'id'     => 'check_calculated_cost_tender',
                                            'value'    => 0,
                                            'type' => 'checkbox',
                                            'tab_index' => 9			                                        
                                        ),													
		                                array( // check label
			                                'type' => 'label',
			                                'id' => 'lbl_check_cost_tender',
											'value' => lang('Show calculated cost')													
										),			                                        		
                                         array( 
                                            'id' => 'btn_view_tender',
                                            'value'    => lang('View tender'),
                                            'type' => 'button',
                                            'tab_index' => 8
                                        ))));				
				
			}

			$uicols = array (
				'name'	=>	array(hour_id,post,code,hours_descr,unit,billperae,quantity,cost,deviation,result,wo_hour_category,cat_per_cent),
				'input_type'	=>	array(hidden,text,text,text,text,text,text,text,text,text,text,text),
				'descr'	=>	array('',lang('Post'),lang('Code'),lang('Descr'),lang('Unit'),lang('Bill per unit'),lang('Quantity'),lang('Cost'),lang('deviation'),lang('result'),lang('Category'),lang('Per Cent')),
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

			$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'deviation',
					'text' 			=> lang('Deviation'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiwo_hour.deviation',
										'workorder_id'	=> $workorder_id
					
									)),
					'parameters'	=> $parameters
			);

			$datatable['rowactions']['action'][] = array(
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
							
			$datatable['rowactions']['action'][] = array(
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

			$datatable['rowactions']['action'][] = array(
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
			
			$datatable['rowactions']['action'][] = array(
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

			$datatable['rowactions']['action_form'][] = array(
					'my_name' 		=> 'add_prizebook',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiwo_hour.prizebook',
										'workorder_id'	=> $workorder_id
									))
			);

			$datatable['rowactions']['action_form'][] = array(
					'my_name' 		=> 'add_template',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uitemplate.index',
										'lookup'=> true,
										'workorder_id'	=> $workorder_id
									))
			);	
								
			$datatable['rowactions']['action_form'][] = array(
					'my_name' 		=> 'add_custom',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiwo_hour.edit',
										'from'=> 'index',
										'workorder_id'	=> $workorder_id
									))
			);
			
			$datatable['rowactions']['action_form'][] = array(
					'my_name' 		=> 'save_template',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiwo_hour.save_template',
										'from'=> 'index',
										'workorder_id'	=> $workorder_id
									))
			);
			
			$datatable['rowactions']['action_form'][] = array(
					'my_name' 		=> 'print_view',
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiwo_hour.view',
										'from'=> 'index',
										'workorder_id'	=> $workorder_id
									))
			);
			
			$datatable['rowactions']['action_form'][] = array(
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
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

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

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
				
	    		return $json;
			}
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

			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour', 'files'));

			$show_cost		= phpgw::get_var('show_cost', 'bool');
			$show_details	= phpgw::get_var('show_details', 'bool');
			$workorder_id	= phpgw::get_var('workorder_id', 'int');
			$to_email 		= phpgw::get_var('to_email', 'email');
			$update_email	= phpgw::get_var('update_email', 'bool');
			$send_order		= phpgw::get_var('send_order', 'bool');
			$no_email		= phpgw::get_var('no_email', 'bool');
			$values			= phpgw::get_var('values');
			$print			= phpgw::get_var('print', 'bool');

			if($update_email)
			{
				$this->bo->update_email($to_email,$workorder_id);
			}
			$workorder = $this->boworkorder->read_single($workorder_id);

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
			$project	= $this->boproject->read_single($common_data['workorder']['project_id']);

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
				'lang_post'		=> lang('Post'),
				'lang_code'		=> lang('Code'),
				'lang_descr'		=> lang('descr'),
				'lang_unit'		=> lang('Unit'),
				'lang_billperae'	=> lang('Bill per unit'),
				'lang_quantity'		=> lang('Quantity'),
				'lang_cost'		=> lang('cost')
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

			$from_name =	$GLOBALS['phpgw_info']['user']['fullname'];
			$from_email =	$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

			if($this->config->config_data['wo_status_sms'])
			{
				$config_sms	= CreateObject('sms.soconfig');
				if(is_object($config_sms))
				{
					$config_sms->read_repository();
					$sms_data['heading'] = lang('Send the following SMS-message to %1 to update status for this order:',$config_sms->config_data['common']['gateway_number']);
					$sms_data['message'] = 'status ' . $workorder_id . ' [' . lang('status code') .']';
					$sms_data['status_code'][0]['name'] = '1 => ' . lang('closed');
					$sms_data['status_code'][1]['name'] = '2 => ' . lang('No access');
					$sms_data['status_code'][2]['name'] = '3 => ' . 'I arbeid';
					$sms_data['status_code_text'] = lang('status code');
					$sms_data['example'] = 'status ' . $workorder_id . ' 1';
					$sms_data['lang_example'] = lang('Example');

			//		_debug_array($sms_data);

				}
			}
			
			$email_data = array
			(
				'org_name'						=> isset($this->config->config_data['org_name']) ? "{$this->config->config_data['org_name']}::" : '',
				'location_data'					=> $location_data,
				'lang_workorder'				=> lang('Workorder ID'),
				'workorder_id'					=> $workorder_id,

				'lang_date'					=> lang('Date'),
				'date'						=> $date,

				'lang_start_date'				=> lang('Start date'),
				'start_date'					=> $workorder['start_date'],

				'lang_end_date'					=> lang('End date'),
				'end_date'					=> $workorder['end_date'],

				'lang_from'					=> lang('From'),
				'from_name'					=> $from_name,
				'from_email'					=> $from_email,
				'from_phone'					=> $GLOBALS['phpgw_info']['user']['preferences']['property']['cellphone'],
				'lang_district'					=> lang('District'),
				'district'					=> $project['location_data']['district_id'],

				'lang_to'					=> lang('To'),
				'to_name'					=> $workorder['vendor_name'],

				'lang_title'					=> lang('Title'),
				'title'						=> $workorder['title'],

				'lang_descr'					=> lang('Description'),
				'descr'						=> $workorder['descr'],

				'lang_budget_account'				=> lang('Budget account'),
				'budget_account'				=> $workorder['b_account_id'],

				'lang_sum_calculation'				=> lang('Sum of calculation'),
				'sum_calculation'				=>$common_data['table_sum'][0]['value_total_sum'],

				'lang_contact_phone'				=> lang('Contact phone'),
				'contact_phone'					=> $project['contact_phone'],

//				'lang_vendor'					=>	lang('vendor'),

				'lang_branch'					=> lang('branch'),
				'branch_list'					=> $this->boproject->select_branch_p_list($project['project_id']),
				'other_branch'					=> $project['other_branch'],

				'key_responsible_list'				=> $this->boproject->select_branch_list($project['key_responsible']),
				'lang_key_responsible'				=> lang('key responsible'),

				'key_fetch_list'				=> $this->boproject->select_key_location_list($workorder['key_fetch']),
				'lang_key_fetch'				=> lang('Where to pick up the key'),

				'key_deliver_list'				=> $this->boproject->select_key_location_list($workorder['key_deliver']),
				'lang_key_deliver'				=> lang('Where to deliver the key'),

				'currency'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

				'lang_cost_tax'					=> lang('Cost (incl tax):'),
				'lang_materials'				=> lang('Materials:__________'),
				'lang_work'					=> lang('work:____________'),

				'table_header_view_order'			=> $table_header,
				'values_view_order'				=> $values_hour,
				'sms_data'					=> $sms_data,
				'use_yui_table' 			=> true
			);

			if($send_order && !$to_email)
			{
					$receipt['error'][]=array('msg'=>lang('No mailaddress is selected'));
			}

			if($to_email || $print)
			{
				$email_data['use_yui_table'] = false;
				
				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/wo_hour'));
				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/location_view'));

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

				$headers = "Return-Path: <". $from_email .">\r\n";
				$headers .= "From: " . $from_name . "<" . $from_email .">\r\n";
				if($GLOBALS['phpgw_info']['user']['preferences']['property']['order_email_rcpt']==1)
				{
					$headers .= "Bcc: " . $from_name . "<" . $from_email .">\r\n";
					$bcc = $from_email;
				}
				$headers .= "Content-type: text/html; charset=utf-8\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$subject = lang('Workorder').": ".$workorder_id;

				$attachment_log = '';
				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles	= CreateObject('property.bofiles');
						$attachments = $bofiles->get_attachments("/workorder/{$workorder_id}/", $values);
						$attachment_log = lang('attachments') . ': ' . implode(', ',$values['file_action']);
					}

					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}
					$rcpt = $GLOBALS['phpgw']->send->msg('email', $to_email, $subject, $header . $html . $footer, '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments);
				}
				else
				{
						$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
				}

				if ($rcpt)
				{
					$historylog	= CreateObject('property.historylog','workorder');
					$historylog->add('M',$workorder_id,"{$to_email} {$attachment_log}");
					$receipt['message'][]=array('msg'=>lang('Workorder is sent by email!'));
					if($attachment_log)
					{
						$receipt['message'][]=array('msg' => $attachment_log);
					}
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('The recipient did not get the email:'));
					$receipt['error'][]=array('msg'=>lang('From') . ' ' . $from_email);
					$receipt['error'][]=array('msg'=>lang('To') . ' ' . $to_email);
				}
			}

			$workorder_history = $this->boworkorder->read_record_history($workorder_id);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$email_list	= $this->bo->get_email($to_email,$workorder['vendor_id']);
			if(count($email_list)==1)
			{
				$to_email= $email_list[0]['email'];
				unset($email_list);
			}

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
       			'values'	=>	json_encode(array(	array(key => post,		label=>lang('Post'),		sortable=>true,resizeable=>true),
									       			array(key => code,		label=>lang('Code'),		sortable=>true,resizeable=>true),
									       			array(key => descr,		label=>lang('descr'),		sortable=>true,resizeable=>true),
		       				       					array(key => unit,		label=>lang('Unit'),		sortable=>true,resizeable=>true),
		       				       					array(key => quantity,label=>lang('Quantity'),		sortable=>true,resizeable=>true),
		       				       					array(key => billperae,label=>lang('Bill per unit'),sortable=>true,resizeable=>true),
		       				       					array(key => cost,		label=>lang('cost'),		sortable=>true,resizeable=>true)))
			);	
			
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
       			'values'	=>	json_encode(array(	array(key => value_date,	label=>lang('Date'),	sortable=>true,resizeable=>true),
									       			array(key => value_user,	label=>lang('User'),	sortable=>true,resizeable=>true),
									       			array(key => value_action,	label=>lang('Action'),	sortable=>true,resizeable=>true),
		       				       					array(key => value_new_value,label=>lang('New value'),sortable=>true,resizeable=>true)))
			);	
			
			
			//----------------------------------------------datatable settings--------	
			$data = array
			(
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
			
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_mail'						=> lang('E-Mail'),
				'lang_update_email'				=> lang('Update email'),
				'lang_update_email_statustext'	=> lang('Check to update the email-address for this vendor'),
				'lang_to_email_address_statustext'		=> lang('The address to which this order will be sendt'),
				'to_email'						=> $to_email,
				'email_list'					=> $email_list,
				'lang_select_email'				=> lang('Select email'),
				'send_order_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.view', 'send'=>true, 'workorder_id'=> $workorder_id, 'show_details'=> $show_details)),

				'lang_no_history'				=> lang('No history'),
				'lang_history'					=> lang('History'),
				'workorder_history'				=> $workorder_history,
				'table_header_history'				=> $table_header_history,
				'email_data'					=> $email_data,
				'no_email'						=> $no_email,
				'table_send'					=> $table_send,
				'table_done'					=> $table_done,

				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'files'							=> isset($workorder['files']) ? $workorder['files'] : '',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_file_action'				=> lang('attach file'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_file_action_statustext'	=> lang('Check to attach file'),
				'lang_print'					=> lang('print'),
				'lang_print_statustext'			=> lang('open this page as printerfrendly'),
				'print_action'					=> "javascript:openwindow('"
												 . $GLOBALS['phpgw']->link('/index.php', array
												 (
												 	'menuaction'	=> 'property.uiwo_hour.view',
												 	'workorder_id'	=> $workorder_id,
												 	'show_cost'		=> $show_cost,
												 	'show_details'	=> $show_details,
												 	'print'			=> true
												 )) . "','1000','1200')"
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
			$function_msg	= lang('Send order');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
			//$GLOBALS['phpgw']->xslttpl->pp();
			//$this->save_sessiondata();
			
		}


		function tender()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$pdf					= CreateObject('phpgwapi.pdf');
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			$show_cost = phpgw::get_var('show_cost', 'bool');
			$mark_draft = phpgw::get_var('mark_draft', 'bool');
			$workorder_id = phpgw::get_var('workorder_id', 'int');

			$common_data		= $this->common_data($workorder_id);
			$values_hour		= $common_data['content'];
			$project			= $this->boproject->read_single($common_data['workorder']['project_id']);

			$grouping_descr_old	= '';

			if (isSet($values_hour) AND is_array($values_hour))
			{
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
							lang('post')		=> $hour['grouping_descr'],
							lang('code')		=> '',
							lang('descr')		=> '',
							lang('unit')		=> '',
							lang('quantity')	=> '',
							lang('bill per unit')	=> '',
							lang('cost')		=> ''
						);
					}

					$grouping_descr_old	= $hour['grouping_descr'];

					$content[] = array
					(
						lang('post')			=> $hour['post'],
						lang('code')			=> $hour['code'],
						lang('descr')			=> $descr,
						lang('unit')			=> $hour['unit'],
						lang('quantity')		=> $hour['quantity'],
						lang('bill per unit')		=> $hour['billperae'],
						lang('cost')			=> $hour['cost']
					);
				}
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date('',$dateformat);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf -> ezSetMargins(50,70,50,50);
			$pdf->selectFont(PHPGW_APP_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,40,578,40);
			$pdf->line(20,822,578,822);
			$pdf->addText(50,823,6,lang('Chapter') . ' ' .$common_data['workorder']['chapter_id'] . ' ' . $common_data['workorder']['chapter'] );
			$pdf->addText(50,34,6,'BBB');
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

			if(is_array($values_hour))
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

			$workorder_id = phpgw::get_var('workorder_id', 'int');
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

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uiwo_hour.prizebook',
									'workorder_id'	=> $workorder_id,
									'query'				=> $this->query
									)
						),
						'fields'	=> array(
                                    'field' => array(
													array( // mensaje
														'type'	=> 'label',
														'id'	=> 'msg_header',
														'value'	=> '',
														'style' => 'filter'
													),												
													array( // boton done
														'type'	=> 'button',
														'id'	=> 'btn_done',
														'tab_index' => 4,
														'value'	=> lang('done')
													),												
													array( // boton SAVE
														'type'	=> 'button',
														'id'	=> 'btn_save',
														'tab_index' => 3,
														'value'	=> lang('save')
													),			                                        
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}
				
			$uicols = array (
				'input_type'	=>	array(hidden,text,hidden,hidden,text,text,text,text,text,text,hidden,varchar,select,varchar),
				'type'			=>	array('','','','','','','','','','','','text','',''),				
				'name'			=>	array(activity_id,num,branch,vendor_id,descr,base_descr,unit,w_cost,m_cost,total_cost,this_index,quantity,wo_hour_cat,cat_per_cent),
				'formatter'		=>	array('','','','','','','','','','','','','',''),
				'descr'			=>	array('',lang('Activity Num'),lang('Branch'),lang('Vendor'),lang('Description'),lang('Base'),lang('Unit'),lang('Labour cost'),lang('Material cost'),lang('Total Cost'),'',lang('Quantity'),lang('category'),lang('Per Cent')),
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
				'name'	=>	array(hour_id,post,code,hours_descr,unit,billperae,quantity,cost,deviation,result,wo_hour_category,cat_per_cent),
				'input_type'	=>	array(hidden,text,text,text,text,text,text,text,text,text,text,text),
				'descr'	=>	array('',lang('Post'),lang('Code'),lang('Descr'),lang('Unit'),lang('Bill per unit'),lang('Quantity'),lang('Cost'),lang('deviation'),lang('result'),lang('Category'),lang('Per Cent')),
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
							} else {
								$details['rows'][$j][$uicols_details['name'][$i]] 	= '';
							}
						} else {
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

			$details['rowactions'][] = array(
				'my_name' 			=> 'deviation',
				'text' 			=> lang('Deviation'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uiwo_hour.deviation',
									'workorder_id'	=> $workorder_id
				
								)),
				'parameters'	=> $parameters
			);

			$details['rowactions'][] = array(
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
			
			$details['rowactions'][] = array(
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

			$details['rowactions'][] = array(
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
			
			$details['rowactions'][] = array(
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
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

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

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
				
				// message when editting & deleting records
				if(isset($receipt) && is_array($receipt))
				{
					$json ['message'][] = $receipt;
				}
				
	    		return $json;
			}
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

			$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour',
									'nextmatchs',
									'search_field'));

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$workorder_id = phpgw::get_var('workorder_id', 'int');
			$template_id = phpgw::get_var('template_id', 'int');

			$values = phpgw::get_var('values');
//_debug_array($values);

			if($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id,$workorder_id);
			}


			if($values['add'])
			{
				$receipt=$this->bo->add_hour_from_template($values,$workorder_id);
			}

			$common_data=$this->common_data($workorder_id);

			$workorder	= $common_data['workorder'];

			$botemplate		= CreateObject('property.botemplate');
			$template_list	= $botemplate->read_template_hour($template_id);

			$i=0;
			$grouping_descr_old='';

			while (is_array($template_list) && list(,$template) = each($template_list))
			{

				if($template['grouping_descr']!=$grouping_descr_old)
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


				$content_template_hour[] = array
				(
					'counter'			=> $i,
					'chapter_id'			=> $template['chapter_id'],
					'grouping_descr'		=> $template['grouping_descr'],
					'building_part'			=> $template['building_part'],
					'new_grouping'			=> $new_grouping,
					'code'				=> $code,
					'activity_id'			=> $template['activity_id'],
					'activity_num'			=> $template['activity_num'],
					'hours_descr'			=> $template['hours_descr'],
					'remark'			=> $template['remark'],
					'ns3420_id'			=> $template['ns3420_id'],
					'tolerance'			=> $template['tolerance'],
					'cost'				=> $template['cost'],
					'unit'				=> $template['unit'],
					'billperae'			=> $template['billperae'],
					'building_part'			=> $template['building_part'],
					'dim_d'				=> $template['dim_d']
				);

				$i++;
			}

			$table_header_template_hour[] = array
			(
				'lang_code'		=> lang('Code'),
				'lang_descr'		=> lang('Description'),
				'lang_unit'		=> lang('Unit'),
				'lang_quantity'		=> lang('Quantity'),
				'lang_billperae'	=> lang('Bill per unit'),
				'lang_cost'		=> lang('Cost'),

				'sort_billperae'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'billperae',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uiwo_hour.template',
																	'workorder_id'	=>$workorder_id,
																	'template_id'	=>$template_id,
																	'query'			=>$this->query,
																	'allrows'		=>$this->allrows)
										)),
				'lang_select'		=> lang('Select'),
				'sort_building_part'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'building_part',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uiwo_hour.template',
																	'workorder_id'	=>$workorder_id,
																	'template_id'	=>$template_id,
																	'query'			=>$this->query,
																	'allrows'		=>$this->allrows)
										)),
				'lang_building_part'	=> lang('Building part')
			);


			$table_done[] = array
			(
				'lang_done'		=> lang('Done'),
				'lang_done_statustext'	=> lang('Back to list'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.index', 'workorder_id'=> $workorder_id))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uiwo_hour.template',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'workorder_id'	=> $workorder_id,
				'template_id'	=> $template_id,
				'allrows'	=> $this->allrows,
				'query'		=> $this->query
			);

			$link_data_nextmatch = array
			(
				'menuaction'	=> 'property.uiwo_hour.template',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'workorder_id'	=> $workorder_id,
				'template_id'	=> $template_id,
				'query'		=> $this->query
			);

			$link_data_delete = array
			(
				'menuaction'	=> 'property.uiwo_hour.template',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'workorder_id'	=> $workorder_id,
				'allrows'	=> $this->allrows,
				'delete'	=> true,
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

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_select_all'			=> lang('Select All'),
				'img_check'				=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',

				'template_id'				=> $template_id,
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_add_statustext'			=> lang('Add the selected items'),
				'lang_add'				=> lang('Add'),
				'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',$link_data_delete),

				'function'				=> 'template',
				'allrows'				=> $this->allrows,
				'allow_allrows'				=> true,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($template_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data_nextmatch),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'workorder_data' 			=> $common_data['workorder_data'],
				'table_header_template_hour'		=> $table_header_template_hour,
				'values_template_hour'			=> $content_template_hour,
				'total_hours_records'			=> $common_data['total_hours_records'],
				'lang_total_records'			=> lang('Total records'),
				'table_header_hour'			=> $common_data['table_header'],
				'values_hour'				=> $common_data['content'],
				'table_sum'				=> $common_data['table_sum'],
				'table_done'				=> $table_done,
				'lang_wo_hour_category'			=> lang('category'),
				'lang_select_wo_hour_category'		=> lang('no category'),
				'wo_hour_cat_list'			=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['wo_hour_cat'],'type' =>'wo_hours','order'=>'id')),

				'lang_cat_per_cent_statustext'		=> lang('the percentage of the category'),
				'value_cat_per_cent'			=> $values['cat_per_cent'],
				'lang_per_cent'				=> lang('Per Cent')
			);

//_debug_array($data);
			$appname		= lang('Template');
			$function_msg		= lang('list template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_template_hour' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
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
			$workorder_id 		= phpgw::get_var('workorder_id', 'int');
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
				'hour_id'	=> $hour_id,
				'from'		=> $from
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			
			
			
			
			
			
		
			
			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.' . $from, 'workorder_id'=> $workorder_id, 'template_id'=> $template_id)),
				'lang_workorder'			=> lang('Workorder'),
				'value_workorder_id'			=> $workorder['workorder_id'],
				'value_workorder_title'			=> $workorder['title'],

				'lang_hour_id'				=> lang('Hour ID'),
				'value_hour_id'				=> $hour_id,

				'lang_copy_hour'			=> lang('Copy hour ?'),
				'lang_copy_hour_statustext'		=> lang('Choose Copy Hour to copy this hour to a new hour'),

				'lang_activity_num'			=> lang('Activity code'),
				'value_activity_num'			=> $values['activity_num'],
				'value_activity_id'			=> $values['activity_id'],

				'lang_unit'				=> lang('Unit'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_descr'				=> lang('description'),
				'value_descr'				=> $values['hours_descr'],
				'lang_descr_statustext'			=> lang('Enter the description for this activity'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the building'),

				'lang_remark'				=> lang('Remark'),
				'value_remark'				=> $values['remark'],
				'lang_remark_statustext'		=> lang('Enter additional remarks to the description - if any'),

				'lang_quantity'				=> lang('quantity'),
				'value_quantity'			=> $values['quantity'],
				'lang_quantity_statustext'		=> lang('Enter quantity of unit'),

				'lang_billperae'			=> lang('Cost per unit'),
				'value_billperae'			=> $values['billperae'],
				'lang_billperae_statustext'		=> lang('Enter the cost per unit'),

				'lang_total_cost'			=> lang('Total cost'),
				'value_total_cost'			=> $values['cost'],
				'lang_total_cost_statustext'		=> lang('Enter the total cost of this activity - if not to be calculated from unit-cost'),

				'lang_vendor'				=> lang('Vendor'),
				'value_vendor_id'			=> $workorder['vendor_id'],
				'value_vendor_name'			=> $workorder['vendor_name'],

				'lang_dim_d'				=> lang('Dim D'),
				'dim_d_list'				=> $this->bopricebook->get_dim_d_list($values['dim_d']),
				'select_dim_d'				=> 'values[dim_d]',
				'lang_no_dim_d'				=> lang('No Dim D'),
				'lang_dim_d_statustext'			=> lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),

				'lang_unit'				=> lang('Unit'),
				'unit_list'				=> $this->bopricebook->get_unit_list($values['unit']),
				'select_unit'				=> 'values[unit]',
				'lang_no_unit'				=> lang('Select Unit'),
				'lang_unit_statustext'			=> lang('Select the unit for this activity.'),

				'lang_chapter'				=> lang('chapter'),
				'chapter_list'				=> $this->bo->get_chapter_list('select',$workorder['chapter_id']),
				'select_chapter'			=> 'values[chapter_id]',
				'lang_no_chapter'			=> lang('Select chapter'),
				'lang_chapter_statustext'		=> lang('Select the chapter (for tender) for this activity.'),

				'lang_tolerance'			=> lang('tolerance'),
				'tolerance_list'			=> $this->bo->get_tolerance_list($values['tolerance_id']),
				'select_tolerance'			=> 'values[tolerance_id]',
				'lang_no_tolerance'			=> lang('Select tolerance'),
				'lang_tolerance_statustext'		=> lang('Select the tolerance for this activity.'),

				'lang_grouping'				=> lang('grouping'),
				'grouping_list'				=> $this->bo->get_grouping_list($values['grouping_id'],$workorder_id),
				'select_grouping'			=> 'values[grouping_id]',
				'lang_no_grouping'			=> lang('Select grouping'),
				'lang_grouping_statustext'		=> lang('Select the grouping for this activity.'),

				'lang_new_grouping'			=> lang('New grouping'),
				'lang_new_grouping_statustext'		=> lang('Enter a new grouping for this activity if not found in the list'),

				'lang_building_part'			=> lang('building_part'),
				'building_part_list'			=> $this->bo->get_building_part_list($values['building_part_id']),
				'select_building_part'			=> 'values[building_part_id]',
				'lang_no_building_part'			=> lang('Select building part'),
				'lang_building_part_statustext'		=> lang('Select the building part for this activity.'),


				'ns3420_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.ns3420')),
				'lang_ns3420'				=> lang('NS3420'),
				'value_ns3420_id'			=> $values['ns3420_id'],
				'lang_ns3420_statustext'		=> lang('Select a standard-code from the norwegian standard'),
				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_wo_hour_category'			=> lang('category'),
				'lang_select_wo_hour_category'		=> lang('no category'),
				'wo_hour_cat_list'			=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['wo_hour_cat'],'type' =>'wo_hours','order'=>'id')),
				'lang_cat_per_cent_statustext'		=> lang('the percentage of the category'),
				'value_cat_per_cent'			=> $values['cat_per_cent'],
				'lang_per_cent'				=> lang('Per Cent')
			);
_debug_array($data);
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
			$workorder_id	= phpgw::get_var('workorder_id', 'int');
			$hour_id	= phpgw::get_var('hour_id', 'int');
			$deviation_id	= phpgw::get_var('deviation_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');


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
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',$delete_link_data),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname = lang('workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

	}

