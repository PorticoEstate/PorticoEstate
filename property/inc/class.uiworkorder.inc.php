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
 	* @version $Id: class.uiworkorder.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uiworkorder
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
			'excel'  => True,
			'index'  => True,
			'view'   => True,
			'add'   => True,
			'edit'   => True,
			'delete' => True
		);

		function property_uiworkorder()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.boworkorder',True);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->menu				= CreateObject('property.menu');
			$this->acl 				= CreateObject('phpgwapi.acl');
			$this->acl_location			= '.project';
			$this->acl_read 			= $this->acl->check('.project',1);
			$this->acl_add 				= $this->acl->check('.project',2);
			$this->acl_edit 			= $this->acl->check('.project',4);
			$this->acl_delete 			= $this->acl->check('.project',8);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->status_id			= $this->bo->status_id;
			$this->search_vendor			= $this->bo->search_vendor;
			$this->wo_hour_cat_id			= $this->bo->wo_hour_cat_id;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->b_group				= $this->bo->b_group;
			$this->paid				= $this->bo->paid;			

			$this->menu->sub			='project';
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
				'search_vendor'			=> $this->search_vendor,
				'status_id'			=> $this->status_id,
				'wo_hour_cat_id'		=> $this->wo_hour_cat_id,
				'start_date'			=> $this->start_date,
				'end_date'			=> $this->end_date,
				'b_group'			=> $this->b_group,
				'paid'				=> $this->paid,				
			);
			$this->bo->save_sessiondata($data);
		}

		function excel()
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 		= urldecode($this->end_date);
			$list 			= $this->bo->read($start_date,$end_date,$allrows=True);
			$uicols			= $this->bo->uicols;
			$this->bocommon->excel($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}


		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder','values','table_header',
										'menu',
										'nextmatchs'));
			$lookup = ''; //Fix this

			$links = $this->menu->links('workorder');

			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);
			$workorder_list = $this->bo->read($start_date,$end_date);

			$uicols	= $this->bo->uicols;
			$count_uicols_name=count($uicols['name']);

			$j=0;
			if (isSet($workorder_list) AND is_array($workorder_list))
			{
				foreach($workorder_list as $workorder_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							if(isset($workorder_entry['query_location'][$uicols['name'][$k]]) && $workorder_entry['query_location'][$uicols['name'][$k]])
							{
								$content[$j]['row'][$k]['statustext']			= lang('search');
								$content[$j]['row'][$k]['text']				= $workorder_entry[$uicols['name'][$k]];
								$content[$j]['row'][$k]['link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index','query'=> $workorder_entry['query_location'][$uicols['name'][$k]],'lookup'=> $lookup, 'filter'=> $this->filter));
							}
							else
							{
								$content[$j]['row'][$k]['value'] 			= $workorder_entry[$uicols['name'][$k]];
								$content[$j]['row'][$k]['name'] 			= $uicols['name'][$k];
								if($uicols['name'][$k]=='vendor_id')
								{
									$content[$j]['row'][$k]['statustext']		= $workorder_entry['org_name'];
									$content[$j]['row'][$k]['overlib']		= True;
									$content[$j]['row'][$k]['text']			= $workorder_entry[$uicols['name'][$k]];
								}
							}
						}
						if($lookup && $k==($count_uicols_name - 2))
						$content[$j]['row'][$k]['lookup_action'] 				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'workorder_id'=> $workorder_entry['workorder_id']));
					}
					if(!$lookup)
					{
						if ($this->acl_read && $this->bocommon->check_perms($workorder_entry['grants'],PHPGW_ACL_READ))
						if($this->acl_read)
						{
							$content[$j]['row'][$k]['statustext']				= lang('view the workorder');
							$content[$j]['row'][$k]['text']					= lang('view');
							$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.view','id'=> $workorder_entry['workorder_id']));
							$k++;
						}
						else
						{
							$content[$j]['row'][$k++]['link']='dummy';
						}

						if ($this->acl_edit && $this->bocommon->check_perms($workorder_entry['grants'],PHPGW_ACL_EDIT))
						{
							$content[$j]['row'][$k]['statustext']				= lang('edit the workorder');
							$content[$j]['row'][$k]['text']					= lang('edit');
							$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id' => $workorder_entry['workorder_id']));
							$k++;

							$content[$j]['row'][$k]['statustext']				= lang('calculate the workorder');
							$content[$j]['row'][$k]['text']					= lang('calculate');
							$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uiwo_hour.index', 'workorder_id'=> $workorder_entry['workorder_id']));
							$k++;
						}
						else
						{
							$content[$j]['row'][$k++]['link']='dummy';
							$content[$j]['row'][$k++]['link']='dummy';
						}

						if ($this->acl_delete && $this->bocommon->check_perms($workorder_entry['grants'],PHPGW_ACL_DELETE))
						{
							$content[$j]['row'][$k]['statustext']				= lang('delete the workorder');
							$content[$j]['row'][$k]['text']					= lang('delete');
							$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.delete', 'id'=> $workorder_entry['workorder_id']));
							$k++;
						}
						else
						{
							$content[$j]['row'][$k++]['link']='dummy';
						}
					}
					$j++;
				}
			}

			$count_uicols_descr=count($uicols['descr']);
			for ($i=0;$i<$count_uicols_descr;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['name'][$i]=='loc1')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'fm_location1.loc1',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uiworkorder.index',
																//	'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
																//	'district_id'	=> $this->district_id,
																	'search_vendor'	=>$this->search_vendor,
																	'cat_id'	=>$this->cat_id,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date,
																	'wo_hour_cat_id'=>$this->wo_hour_cat_id,
																	'b_group'	=> $this->b_group,
																	'paid'		=> $this->paid
																)
										));
					}
					if($uicols['name'][$i]=='project_id')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'project_id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uiworkorder.index',
															//		'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
															//		'district_id'	=> $this->district_id,
																	'search_vendor'	=>$this->search_vendor,
																	'cat_id'	=>$this->cat_id,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date,
																	'wo_hour_cat_id'=>$this->wo_hour_cat_id,
																	'b_group'	=> $this->b_group,
																	'paid'		=> $this->paid
																)
										));
					}					
					if($uicols['name'][$i]=='workorder_id')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'workorder_id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uiworkorder.index',
															//		'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
															//		'district_id'	=> $this->district_id,
																	'search_vendor'	=>$this->search_vendor,
																	'cat_id'	=>$this->cat_id,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date,
																	'wo_hour_cat_id'	=>$this->wo_hour_cat_id,
																	'b_group'	=> $this->b_group,
																	'paid'		=> $this->paid
																)
										));
					}
					if($uicols['name'][$i]=='address')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'address',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uiworkorder.index',
															//		'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
															//		'district_id'	=> $this->district_id,
																	'search_vendor'	=>$this->search_vendor,
																	'cat_id'	=>$this->cat_id,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date,
																	'wo_hour_cat_id'=>$this->wo_hour_cat_id,
																	'b_group'	=> $this->b_group,
																	'paid'		=> $this->paid
																)
										));
					}

				}
			}

			if(!$lookup)
			{
				if($this->acl_read)
				{
					$table_header[$i]['width'] 	= '5%';
					$table_header[$i]['align'] 	= 'center';
					$table_header[$i]['header']	= lang('view');
					$i++;
				}
				if($this->acl_edit)
				{
					$table_header[$i]['width'] 	= '5%';
					$table_header[$i]['align'] 	= 'center';
					$table_header[$i]['header']	= lang('edit');
					$i++;

					$table_header[$i]['width'] 	= '5%';
					$table_header[$i]['align'] 	= 'center';
					$table_header[$i]['header']	= lang('calculate');
					$i++;
				}
				if($this->acl_delete)
				{
					$table_header[$i]['width'] 	= '5%';
					$table_header[$i]['align'] 	= 'center';
					$table_header[$i]['header']	= lang('delete');
					$i++;
				}
			}
			else
			{
				$table_header[$i]['width'] 		= '5%';
				$table_header[$i]['align'] 		= 'center';
				$table_header[$i]['header']		= lang('select');
			}

//_debug_array($content);
			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'			=> lang('add'),
					'lang_add_statustext'		=> lang('add a workorder'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.add'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiworkorder.index',
						'sort'			=> $this->sort,
						'order'			=> $this->order,
						'cat_id'		=> $this->cat_id,
					//	'district_id'		=> $this->district_id,
						'status_id'		=> $this->status_id,
						'filter'		=> $this->filter,
						'query'			=> $this->query,
						'search_vendor'		=> $this->search_vendor,
						'start_date'		=> $start_date,
						'end_date'		=> $end_date,
						'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
						'b_group'		=> $this->b_group,
						'paid'			=> $this->paid
			);

			$link_date_search				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_excel = array
			(
				'menuaction'	=> 'property.uiworkorder.excel',
						'sort'			=> $this->sort,
						'order'			=> $this->order,
						'cat_id'		=> $this->cat_id,
					//	'district_id'		=> $this->district_id,
						'status_id'		=> $this->status_id,
						'filter'		=> $this->filter,
						'query'			=> $this->query,
						'search_vendor'		=> $this->search_vendor,
						'start_date'		=> $start_date,
						'end_date'		=> $end_date,
						'start'			=> $this->start,
						'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
						'b_group'		=> $this->b_group,
						'paid'			=> $this->paid
			);


			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters'])
			{
				$group_filters = 'select';
				$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour_cat_select'));
			}
			else
			{
				$group_filters = 'filter';
				$GLOBALS['phpgw']->xslttpl->add_file(array('wo_hour_cat_filter'));
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'group_filters'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters']:'',
				'lang_excel'					=> 'excel',
				'link_excel'					=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'				=> lang('Download table to MS Excel'),

				'start_date'					=>$start_date,
				'end_date'						=>$end_date,
				'lang_none'						=>lang('None'),
				'lang_date_search'				=> lang('Date search'),
				'lang_date_search_help'			=> lang('Narrow the search by dates'),
				'link_date_search'				=> $link_date_search,

				'link_history'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index')),
				'lang_history_statustext'		=> lang('search for history at this location'),
				'links'							=> $links,
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($workorder_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the workorder belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>$group_filters,'selected' => $this->cat_id,'type' =>'wo','order'=>'descr')),

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_status_statustext'		=> lang('Select the status the agreement belongs to. To do not use a category select NO STATUS'),
				'status_name'					=> 'status_id',
				'lang_no_status'				=> lang('No status'),
				'status_list'					=> $this->bo->select_status_list($group_filters,$this->status_id),

				'lang_wo_hour_cat_statustext'	=> lang('Select the workorder hour category'),
				'lang_no_wo_hour_cat'			=> lang('no hour category'),
				'wo_hour_cat_list'				=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id')),

				'lang_user_statustext'			=> lang('Select the user the workorder belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'filter',
				'lang_no_user'					=> lang('No user'),
				'user_list'						=> $this->bocommon->get_user_list_right2($group_filters,2,$this->filter,$this->acl_location),

				'lang_searchvendor_statustext'	=> lang('Enter the vendor name to search for'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'search_vendor'					=> $this->search_vendor,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'						=> $content,
				'table_add'						=> $table_add
			);

			$appname			= lang('Workorder');
			$function_msg		= lang('list workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_workorder' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>2, 'acl_location'=> $this->acl_location));
			}
			$boproject			= CreateObject('property.boproject');
			$bolocation			= CreateObject('property.bolocation');
			$config				= CreateObject('phpgwapi.config');
			$id 				= phpgw::get_var('id', 'int');
			$project_id 			= phpgw::get_var('project_id', 'int');
			$values				= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder'));

			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

			$config->read_repository();

			if (isset($values['save']))
			{
				if(!$values['title'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a workorder title !'));
					$error_id=true;
				}
				if(!$values['project_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a valid project !'));
					$error_id=true;
				}

				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}
				if(!$values['b_account_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if($id)
				{
					$values['workorder_id']=$id;
					$action='edit';
				}

				if(!$receipt['error'])
				{
					if(!$id)
					{
						$values['workorder_id']=$this->bo->next_id();
						$id	= $values['workorder_id'];
					}
					if($values['copy_workorder'])
					{
						$action='add';
						$values['workorder_id']	= $this->bo->next_id();
						$id	= $values['workorder_id'];
					}
					$receipt = $this->bo->save($values,$action);
					$id = $values['workorder_id'];
					$function_msg = lang('Edit Workorder');

					if ($values['approval'] && $values['mail_address'])
					{
						$coordinator_name=$GLOBALS['phpgw_info']['user']['fullname'];
						$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
						$headers = "Return-Path: <". $coordinator_email .">\r\n";
						$headers .= "From: " . $coordinator_name . "<" . $coordinator_email .">\r\n";
						$headers .= "Bcc: " . $coordinator_name . "<" . $coordinator_email .">\r\n";
						$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
						$headers .= "MIME-Version: 1.0\r\n";

						$subject = lang(Approval).": ". $values['workorder_id'];
					//	$message = lang('Workorder %1 needs approval',$values['workorder_id']);
						$message = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id'=> $values['project_id'])).'">' . lang('Workorder %1 needs approval',$values['workorder_id']) .'</a>';

						if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
						{
							if (!is_object($GLOBALS['phpgw']->send))
							{
								$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
							}
							$bcc = $coordinator_email;
							$rcpt = $GLOBALS['phpgw']->send->msg('email', $values['mail_address'], $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'plain');
						}
						else
						{
							$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
						}
					}

					if($rcpt)
					{
						$receipt['message'][]=array('msg'=>lang('%1 is notified',$values['mail_address']));
					}

				}
			}

			if($project_id && !isset($values['project_id']))
			{
				$values['project_id']=$project_id;
			}

			$project	= (isset($values['project_id'])?$boproject->read_single_mini($values['project_id']):'');

			if(!isset($receipt['error']))
			{
				if($id)
				{
					$values		= $this->bo->read_single($id);
				}
				if($project_id && !isset($values['project_id']))
				{
					$values['project_id']=$project_id;
				}

				if(!$project && isset($values['project_id']) && $values['project_id'])
				{
					$project	= $boproject->read_single_mini($values['project_id']);
				}

				if (!$this->bocommon->check_perms($project['grants'],PHPGW_ACL_EDIT))
				{
					$receipt['error'][]=array('msg'=>lang('You have no edit right for this project'));
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.view', 'id'=>$id));
				}
				if (isset($receipt['notice_owner']) AND is_array($receipt['notice_owner']))
				{
					if($this->account!=$project['coordinator'] && $config->config_data['workorder_approval'])
					{
						$prefs_coordinator = $this->bocommon->create_preferences('property',$project['coordinator']);
						$to = $prefs_coordinator['email'];
						$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
						$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
						$body = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit','id'=> $id)).'">' . lang('workorder %1 has been edited',$id) .'</a>' . "\n";
						foreach($receipt['notice_owner'] as $notice)
						{
							$body .= $notice . "\n";
						}
							$body .= lang('Altered by') . ': ' . $from_name . "\n";
						$body .= lang('remark') . ': ' . $values['remark'] . "\n";
						$body = nl2br($body);

						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject=lang('workorder %1 has been edited',$id),$body, False,False,False, $from_email, $from_name, 'html');

						if (!$returncode)	// not nice, but better than failing silently
						{
							$receipt['error'][]=array('msg'=>"uiworkorder::edit: sending message to '$to' subject='$subject' failed !!!");
							$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
						}
						else
						{
							$receipt['message'][]=array('msg'=>lang('%1 is notified',$to));
						}
					}
				}

				if( $project['key_fetch'] && !$values['key_fetch'])
				{
					$values['key_fetch']=$project['key_fetch'];
				}

				if( $project['key_deliver'] && !$values['key_deliver'])
				{
					$values['key_deliver']=$project['key_deliver'];
				}

/*				if( $project['charge_tenant'] && !$values['workorder_id'])
				{
					$values['charge_tenant']=$project['charge_tenant'];
				}
*/
				if( $project['start_date'] && !$values['start_date'])
				{
					$values['start_date']=$project['start_date'];
				}
				if( $project['end_date'] && !$values['end_date'])
				{
					$values['end_date']=$project['end_date'];
				}
				if( $project['name'] && !isset($values['title']))
				{
					$values['title']=$project['name'];
				}
				if( $project['descr'] && !isset($values['descr']))
				{
					$values['descr']=$project['descr'];
				}
				if( $project['status'] && !isset($values['status']))
				{
					$values['status']=$project['status'];
				}
			}

			if($id)
			{
				$record_history = $this->bo->read_record_history($id);
			}
			else
			{
				$record_history = '';
			}

//_debug_array($hour_data);
			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_workorder_budget[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_sum'		=> lang('Sum')
			);

			if ($id)
			{
				$function_msg = lang('Edit Workorder');
			}
			else
			{
				$function_msg = lang('Add workorder');
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}


			$location_data=$bolocation->initiate_ui_location(array(
						'values'		=> (isset($project['location_data'])?$project['location_data']:''),
						'type_id'		=> (isset($project['location_data']['location_code'])?count(explode('-',$project['location_data']['location_code'])):''),
						'no_link'		=> False, // disable lookup links for location type less than type_id
						'tenant'		=> (isset($project['location_data']['tenant_id'])?$project['location_data']['tenant_id']:''),
						'lookup_type'		=> 'view'
						));


			if(isset($project['contact_phone']))
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'		=> $values['vendor_id'],
						'vendor_name'		=> $values['vendor_name']));

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $values['b_account_id'],
						'b_account_name'	=> $values['b_account_name']));


			$link_data = array
			(
				'menuaction'	=> 'property.uiworkorder.edit',
				'id'		=> $id
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));


			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'] )
			{
				$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
			}
			else
			{
				$supervisor_id = '';
			}

			$need_approval = (isset($config->config_data['workorder_approval'])?$config->config_data['workorder_approval']:'');

			if ($supervisor_id && ($need_approval=='yes'))
			{
				$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
				$supervisor_email = $prefs['email'];
			}
			else
			{
				$supervisor_email = '';
			}

			$project_status=(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['project_status']:'');
			if(!$values['status'])
			{
				$values['status']=$project_status;
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');

			if( isset($receipt) && is_array($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}
			else
			{
				$msgbox_data = '';
			}

			$GLOBALS['phpgw']->js->validate_file('tabs', 'tabs');

			if(!is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->validate_file('tabs','phpgwapi');

			$data = array
			(
				'lang_project_info'				=> lang('Project info'),
				'lang_general' 					=> lang('General'),
				'lang_coordination' 			=> lang('Coordination'),
				'lang_time_and_budget' 			=> lang('Time and budget'),
				'lang_extra' 					=> lang('Extra'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'calculate_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.index')),
				'lang_calculate'			=> lang('Calculate Workorder'),
				'lang_calculate_statustext'		=> lang('Calculate workorder by adding items from vendors prizebook or adding general hours'),

				'send_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uiwo_hour.view', 'from'=>'index')),
				'lang_send'				=> lang('Send Workorder'),
				'lang_send_statustext'			=> lang('send this workorder to vendor'),

				'project_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit')),
				'b_account_data'			=> $b_account_data,
				'table_header_workorder_budget'		=> $table_header_workorder_budget,
				'lang_no_workorders'			=> lang('No workorder budget'),
				'workorder_link'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.w_edit')),
				'record_history'			=> $record_history,
				'table_header_history'			=> $table_header_history,
				'lang_history'				=> lang('History'),
				'lang_no_history'			=> lang('No history'),

				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),

				'lang_start_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_start_date'			=> lang('Workorder start date'),
				'value_start_date'			=> $values['start_date'],

				'lang_end_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_end_date'				=> lang('Workorder end date'),
				'value_end_date'			=> $values['end_date'],

				'lang_copy_workorder'			=> lang('Copy workorder ?'),
				'lang_copy_workorder_statustext'	=> lang('Choose Copy Workorder to copy this workorder to a new workorder'),

				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> (isset($project['contact_phone'])?$project['contact_phone']:''),

				'lang_charge_tenant'			=> lang('Charge tenant'),
				'lang_charge_tenant_statustext'		=> lang('Choose charge tenant if the tenant i to pay for this project'),
				'charge_tenant'				=> (isset($values['charge_tenant'])?$values['charge_tenant']:''),

				'lang_power_meter'			=> lang('Power meter'),
				'lang_power_meter_statustext'		=> lang('Enter the power meter'),
				'value_power_meter'			=> (isset($project['power_meter'])?$project['power_meter']:''),

				'lang_addition_rs'			=> lang('Rig addition'),
				'lang_addition_rs_statustext'		=> lang('Enter any round sum addition per order'),
				'value_addition_rs'			=> (isset($values['addition_rs'])?$values['addition_rs']:''),

				'lang_addition_percentage'		=> lang('Percentage addition'),
				'lang_addition_percentage_statustext'	=> lang('Enter any persentage addition per unit'),
				'value_addition_percentage'		=> (isset($values['addition_percentage'])?$values['addition_percentage']:''),

				'lang_budget'				=> lang('Budget'),
				'value_budget'				=> (isset($values['budget'])?$values['budget']:''),
				'lang_budget_statustext'		=> lang('Enter the budget'),

				'lang_incl_tax'				=> lang('incl tax'),
				'lang_calculation'			=> lang('Calculation'),
				'value_calculation'			=> (isset($values['calculation'])?$values['calculation']:''),

				'actual_cost'				=> (isset($values['actual_cost'])?$values['actual_cost']:''),
				'lang_actual_cost'			=> lang('Actual cost'),

				'vendor_data'				=> $vendor_data,
				'location_data'				=> $location_data,
				'location_type'				=> 'view',
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index')),
				'lang_year'				=> lang('Year'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_title'				=> lang('Title'),
				'value_title'				=> $values['title'],
				'lang_project_name'			=> lang('Project name'),
				'value_project_name'			=> (isset($project['name'])?$project['name']:''),

				'lang_project_id'			=> lang('Project ID'),
				'value_project_id'			=> $values['project_id'],

				'lang_workorder_id'			=> lang('Workorder ID'),
				'value_workorder_id'			=> (isset($values['workorder_id'])?$values['workorder_id']:''),

				'lang_title_statustext'			=> lang('Enter Workorder title'),

				'lang_other_branch'			=> lang('Other branch'),
				'lang_other_branch_statustext'		=> lang('Enter other branch if not found in the list'),
				'value_other_branch'			=> (isset($project['other_branch'])?$project['other_branch']:''),

				'lang_descr_statustext'			=> lang('Enter a short description of the workorder'),
				'lang_descr'				=> lang('Description'),
				'value_descr'				=> $values['descr'],

				'lang_remark_statustext'		=> lang('Enter a remark to add to the history of the order'),
				'lang_remark'				=> lang('remark'),
				'value_remark'				=> (isset($values['remark'])?$values['remark']:''),

				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the workorder'),
				'lang_no_cat'				=> lang('Select category'),
				'lang_cat_statustext'			=> lang('Select the category the project belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[cat_id]',
				'value_cat_id'				=> (isset($values['cat_id'])?$values['cat_id']:''),
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $project['cat_id'],'type' =>'wo','order'=>'descr')),

				'sum_workorder_budget'			=> (isset($values['sum_workorder_budget'])?$values['sum_workorder_budget']:''),
				'workorder_budget'			=> (isset($values['workorder_budget'])?$values['workorder_budget']:''),

				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'select_user_name'			=> 'values[coordinator]',
				'user_list'				=> $this->bocommon->get_user_list('select',$project['coordinator'],$extra=False,$default=False,$start=-1,$sort=False,$order=False,$query='',$offset=-1),

				'status_list'				=> $this->bo->select_status_list('select',$values['status']),
				'status_name'				=> 'values[status]',
				'lang_no_status'			=> lang('Select status'),
				'lang_status'				=> lang('Status'),
				'lang_status_statustext'		=> lang('What is the current status of this workorder ?'),
				'lang_confirm_status'			=> lang('Confirm status'),
				'lang_confirm_statustext'		=> lang('Confirm status to the history'),

				'branch_list'				=> $boproject->select_branch_p_list($project['project_id']),
				'lang_branch'				=> lang('branch'),
				'lang_branch_statustext'		=> lang('Select the branches for this project'),

				'key_responsible_list'			=> $boproject->select_branch_list($project['key_responsible']),
				'lang_key_responsible'			=> lang('key responsible'),

				'key_fetch_list'			=> $this->bo->select_key_location_list((isset($values['key_fetch'])?$values['key_fetch']:'')),
				'lang_no_key_fetch'			=> lang('Where to fetch the key'),
				'lang_key_fetch'			=> lang('key fetch location'),
				'lang_key_fetch_statustext'		=> lang('Select where to fetch the key'),

				'key_deliver_list'			=> $this->bo->select_key_location_list((isset($values['key_deliver'])?$values['key_deliver']:'')),
				'lang_no_key_deliver'			=> lang('Where to deliver the key'),
				'lang_key_deliver'			=> lang('key deliver location'),
				'lang_key_deliver_statustext'		=> lang('Select where to deliver the key'),

				'need_approval'				=> $need_approval,
				'lang_ask_approval'			=> lang('Ask for approval'),
				'lang_ask_approval_statustext'		=> lang('Check this to send a mail to your supervisor for approval'),
				'value_approval_mail_address'		=> $supervisor_email,
				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']
			);

			$appname						= lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function add()
		{
			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$link_data = array
			(
				'menuaction' => 'property.uiworkorder.index'
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder',
										'menu',
										'search_field'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit')),
				'search_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index', 'lookup'=> true, 'from'=> 'workorder')),

				'lang_done_statustext'		=> lang('Back to the workorder list'),
				'lang_add_statustext'		=> lang('Adds a new project - then a new workorder'),
				'lang_search_statustext'	=> lang('Adds a new workorder to an existing project'),

				'lang_done'			=> lang('Done'),
				'lang_add'			=> lang('Add'),
				'lang_search'			=> lang('Search')
			);

			$appname				= lang('Workorder');
			$function_msg				= lang('Add workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>8, 'acl_location'=> $this->acl_location));
			}
			$id = phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uiworkorder.index'
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
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.delete', 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname				= lang('workorder');
			$function_msg				= lang('delete workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$boproject			= CreateObject('property.boproject');
			$bolocation			= CreateObject('property.bolocation');

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');

			$id	= phpgw::get_var('id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder','hour_data_view'));

			$uiwo_hour	= CreateObject('property.uiwo_hour');
			$hour_data	= $uiwo_hour->common_data($id,$view=True);
			$values		= $this->bo->read_single($id);
			$project	= $boproject->read_single($values['project_id']);
			$record_history = $this->bo->read_record_history($id);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_workorder_budget[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_sum'		=> lang('Sum')
			);

			$function_msg = lang('View Workorder');

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $project['location_data'],
						'type_id'	=> count(explode('-',$project['location_data']['location_code'])),
						'no_link'	=> False, // disable lookup links for location type less than type_id
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


			$data = array
			(
				'project_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.view')),
				'table_header_workorder_budget'		=> $table_header_workorder_budget,
				'lang_no_workorders'			=> lang('No workorder budget'),
				'workorder_link'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.view')),
				'record_history'			=> $record_history,
				'table_header_history'			=> $table_header_history,
				'lang_history'				=> lang('History'),
				'lang_no_history'			=> lang('No history'),

				'lang_project_name'			=> lang('Project name'),
				'value_project_name'			=> $project['name'],

				'lang_vendor'				=> lang('Vendor'),
				'value_vendor_id'			=> $values['vendor_id'],
				'value_vendor_name'			=> $values['vendor_name'],

				'lang_b_account'			=> lang('Budget account'),
				'value_b_account_id'			=> $values['b_account_id'],
				'value_b_account_name'			=> $values['b_account_name'],

				'lang_start_date'			=> lang('Project start date'),
				'value_start_date'			=> $values['start_date'],

				'lang_end_date'				=> lang('Project end date'),
				'value_end_date'			=> $values['end_date'],

				'lang_charge_tenant'			=> lang('Charge tenant'),
				'charge_tenant'				=> $values['charge_tenant'],

				'lang_power_meter'			=> lang('Power meter'),
				'value_power_meter'			=> $project['power_meter'],

				'lang_addition_rs'			=> lang('Rig addition'),
				'lang_addition_rs_statustext'		=> lang('Enter any round sum addition per order'),
				'value_addition_rs'			=> $values['addition_rs'],

				'lang_addition_percentage'		=> lang('Percentage addition'),
				'lang_addition_percentage_statustext'	=> lang('Enter any persentage addition per unit'),
				'value_addition_percentage'				=> $values['addition_percentage'],

				'lang_budget'				=> lang('Budget'),
				'value_budget'				=> $values['budget'],

				'actual_cost'				=> $values['actual_cost'],
				'lang_actual_cost'			=> lang('Actual cost'),

				'location_data'				=> $location_data,
				'location_type'				=> 'view',
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index')),
				'lang_year'				=> lang('Year'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_name'				=> lang('Name'),

				'lang_title'				=> lang('Title'),
				'value_title'				=> $values['title'],

				'lang_project_id'			=> lang('Project ID'),
				'value_project_id'			=> $values['project_id'],
				'value_name'				=> $values['name'],

				'lang_other_branch'			=> lang('Other branch'),
				'value_other_branch'			=> $project['other_branch'],

				'lang_descr'				=> lang('Description'),
				'value_descr'				=> $values['descr'],
				'lang_done_statustext'			=> lang('Back to the list'),
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $project['cat_id'],'type' =>'wo','order'=>'descr')),

				'lang_workorder_id'			=> lang('Workorder ID'),
				'value_workorder_id'			=> $values['workorder_id'],

				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'user_list'				=> $this->bocommon->get_user_list('select',$project['coordinator'],$extra=False,$default=False,$start=-1,$sort=False,$order=False,$query='',$offset=-1),

				'status_list'				=> $this->bo->select_status_list('select',$values['status']),
				'lang_no_status'			=> lang('Select status'),
				'lang_status'				=> lang('Status'),

				'branch_list'				=> $this->bo->select_branch_p_list($values['project_id']),
				'lang_branch'				=> lang('branch'),

				'key_responsible_list'			=> $this->bo->select_branch_list($project['key_responsible']),
				'lang_key_responsible'			=> lang('key responsible'),

				'key_fetch_list'			=> $this->bo->select_key_location_list($values['key_fetch']),
				'lang_key_fetch'			=> lang('key fetch location'),

				'key_deliver_list'			=> $this->bo->select_key_location_list($values['key_deliver']),
				'lang_key_deliver'			=> lang('key deliver location'),

				'edit_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiworkorder.edit', 'id' => $id)),
				'lang_edit_statustext'			=> lang('Edit this entry workorder'),
				'lang_edit'				=> lang('Edit'),
				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_total_records'			=> lang('Total records'),
				'total_hours_records'			=> $hour_data['total_hours_records'],
				'table_header_hour'			=> $hour_data['table_header'],
				'values_hour'				=> $hour_data['content'],
				'table_sum'				=> $hour_data['table_sum'],
				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> $project['contact_phone']
			);

			$appname					= lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
