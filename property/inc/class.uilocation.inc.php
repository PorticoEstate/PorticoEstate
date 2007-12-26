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
	* @subpackage location
 	* @version $Id: class.uilocation.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uilocation
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;

		var $public_functions = array
		(
			'excel'  	=> True,
			'index'  	=> True,
			'view'   	=> True,
			'edit'   	=> True,
			'delete' 	=> True,
			'update_cat'=> True,
			'stop'		=> True,
			'summary'	=> True,
			'columns'	=> True
		);

		function property_uilocation()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bolocation',True);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->soadmin_location		= CreateObject('property.soadmin_location');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
		//	$this->acl 					= CreateObject('phpgwapi.acl');

			$this->type_id				= $this->bo->type_id;

			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location,1);
			$this->acl_add 				= $this->acl->check($this->acl_location,2);
			$this->acl_edit 			= $this->acl->check($this->acl_location,4);
			$this->acl_delete 			= $this->acl->check($this->acl_location,8);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->type_id				= $this->bo->type_id;
			$this->allrows				= $this->bo->allrows;
			$this->lookup				= $this->bo->lookup;
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
				'part_of_town_id'	=> $this->part_of_town_id,
				'district_id'		=> $this->district_id,
				'status'		=> $this->status,
				'type_id'		=> $this->type_id,
			//	'allrows'		=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function excel()
		{
			$summary	= phpgw::get_var('summary', 'bool', 'GET');
			$type_id	= phpgw::get_var('type_id', 'int', 'GET');
			$lookup 	= phpgw::get_var('lookup', 'bool');
			$lookup_name 	= phpgw::get_var('lookup_name');
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');

			if(!$summary)
			{
				$list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>True));
			}
			else
			{
				$list= $this->bo->read_summary();
			}

			$uicols	= $this->bo->uicols;
			$this->bocommon->excel($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function columns()
		{
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			
			$values 		= phpgw::get_var('values');

			if (isset($values['save']) && $values['save'] && $this->type_id)
			{
				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read_repository();
				$GLOBALS['phpgw']->preferences->add('property',location_columns_ . $this->type_id . !!$this->lookup,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> 'property.uilocation.columns',
				'type_id'		=> $this->type_id,
				'lookup'		=> $this->lookup
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'		=> $this->bo->column_list(isset($values['columns']) ? $values['columns']:'',$type_id=$this->type_id,$allrows=True),
				'function_msg'		=> $function_msg,
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'		=> lang('columns'),
				'lang_none'		=> lang('None'),
				'lang_save'		=> lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}


		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array(
									'menuaction'=> 'property.uilocation.stop',
									'perm'=>1,
									'acl_location'=> $this->acl_location
									)
								);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location',
										'nextmatchs',
										'search_field'));

			$type_id	= $this->type_id;
			$lookup 	= $this->lookup;
			$lookup_name 	= phpgw::get_var('lookup_name');
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->set_onload('document.search.query.focus();');

			if(!$type_id)
			{
				$type_id=1;
			}
			if($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			}

			$location_list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>$this->allrows));

			$uicols = $this->bo->uicols;
//_debug_array($location_list);
//_debug_array($uicols);

			$content = array();
			$j=0;
			if (isSet($location_list) AND is_array($location_list))
			{
				foreach($location_list as $location)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{

						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($location['query_location'][$uicols['name'][$i]]))
							{
								$content[$j]['row'][$i]['statustext']			= lang('search');
								$content[$j]['row'][$i]['text']				= $location[$uicols['name'][$i]];
								$content[$j]['row'][$i]['link']				= $GLOBALS['phpgw']->link('/index.php',array(
																			'menuaction'	=> 'property.uilocation.index',
																			'query' 	=> $location['query_location'][$uicols['name'][$i]],
																			'lookup'	=> $lookup,
																			'type_id'	=> $type_id,
																			'lookup_tenant'	=> $lookup_tenant,
																			'lookup_name'	=> $lookup_name
																			)
																		);
							}
							else
							{
								$content[$j]['row'][$i]['value'] 			= $location[$uicols['name'][$i]];
								$content[$j]['row'][$i]['name'] 			= $uicols['name'][$i];
								$content[$j]['row'][$i]['lookup'] 			= $lookup;
								$content[$j]['row'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');
								if($uicols['input_type'][$i]=='link' && $location[$uicols['name'][$i]])
								{
									$content[$j]['row'][$i]['text']		= lang('link');
									$content[$j]['row'][$i]['link']		= $location[$uicols['name'][$i]];
									$content[$j]['row'][$i]['target']	= '_blank';
								}
							}
						}

						$content[$j]['hidden'][$i]['value'] 			= $location[$uicols['name'][$i]];
						$content[$j]['hidden'][$i]['name'] 				= $uicols['name'][$i];
					}

					if(!$lookup)
					{
						if($this->acl_read)
						{
							$content[$j]['row'][$i]['statustext']			= lang('view the location');
							$content[$j]['row'][$i]['text']				= lang('view');
							$content[$j]['row'][$i]['link']				= $GLOBALS['phpgw']->link('/index.php',array(
																		'menuaction'=> 'property.uilocation.view',
																		'location_code'=> $location['location_code'],
																		'lookup_tenant'=>$lookup_tenant
																		)
																	);
							$i++;
						}

						if($this->acl_edit)
						{
							$content[$j]['row'][$i]['statustext']			= lang('edit the location');
							$content[$j]['row'][$i]['text']				= lang('edit');
							$content[$j]['row'][$i]['link']				= $GLOBALS['phpgw']->link('/index.php',array(
																		'menuaction'=> 'property.uilocation.edit',
																		'location_code'=> $location['location_code'],
																		'lookup_tenant'=>$lookup_tenant
																		)
																	);
							$i++;
						}

						if($this->acl_delete)
						{
							$content[$j]['row'][$i]['statustext']			= lang('delete the location');
							$content[$j]['row'][$i]['text']				= lang('delete');
							$content[$j]['row'][$i]['link']				= $GLOBALS['phpgw']->link('/index.php',array(
																		'menuaction'=> 'property.uilocation.delete',
																		'location_code'=> $location['location_code'],
																		'type_id'=> $type_id,
																		'lookup_tenant'=>$lookup_tenant
																		)
																	);
						}
					}
					$j++;
				}
			}
//_debug_array($content);
			$uicols_count	= count($uicols['descr']);
			for ($i=0;$i<$uicols_count;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['name'][$i]=='loc1'):
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_location1.loc1',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uilocation.index',
																	'type_id'		=> $type_id,
																	'query'			=> $this->query,
																	'district_id'		=> $this->district_id,
																	'part_of_town_id'	=> $this->part_of_town_id,
																	'lookup'		=> $lookup,
																	'lookup_tenant'		=> $lookup_tenant,
																	'lookup_name'		=> $lookup_name,
																	'cat_id'		=> $this->cat_id,
																	'status'		=> $this->status)
										));
					}
					elseif($uicols['name'][$i]=='street_name'):
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'street_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uilocation.index',
																	'type_id'		=> $type_id,
																	'query'			=> $this->query,
																	'district_id'		=> $this->district_id,
																	'part_of_town_id'	=> $this->part_of_town_id,
																	'lookup'		=> $lookup,
																	'lookup_tenant'		=> $lookup_tenant,
																	'lookup_name'		=> $lookup_name,
																	'cat_id'		=> $this->cat_id,
																	'status'		=> $this->status)
										));
					}
					elseif(isset($uicols['cols_return_extra'][$i]) && ($uicols['cols_return_extra'][$i]!='T' || $uicols['cols_return_extra'][$i]!='CH')):
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
							(
								'sort'	=> $this->sort,
								'var'	=> $uicols['name'][$i],
								'order'	=> $this->order,
								'extra'	=> array('menuaction'	=> 'property.uilocation.index',
																	'type_id'		=> $type_id,
																	'query'			=> $this->query,
																	'district_id'		=> $this->district_id,
																	'part_of_town_id'	=> $this->part_of_town_id,
																	'lookup'		=> $lookup,
																	'lookup_tenant'		=> $lookup_tenant,
																	'lookup_name'		=> $lookup_name,
																	'cat_id'		=> $this->cat_id,
																	'status'		=> $this->status)

							));
					}
					endif;
				}
			}

			if(!$lookup)
			{
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
			}
			else
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('select');
			}

			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a location'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array(
													'menuaction'=> 'property.uilocation.edit',
													'type_id'=>$type_id
													)
											  )
				);
			}

			$link_data = array
			(
				'menuaction'		=> 'property.uilocation.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'district_id'		=> $this->district_id,
				'part_of_town_id'	=> $this->part_of_town_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'lookup'		=> $lookup,
				'lookup_tenant'		=> $lookup_tenant,
				'lookup_name'		=> $lookup_name,
				'type_id'		=> $type_id,
				'status'		=> $this->status
			);

			$input_name		= $GLOBALS['phpgw']->session->appsession('lookup_fields','property');

			$function_exchange_values = '';
			if(is_array($input_name))
			{
				for ($k=0;$k<count($input_name);$k++)
				{
					$function_exchange_values .= "opener.document.form." . $input_name[$k] . ".value = '';" ."\r\n";
				}
			}

			for ($i=0;$i<count($uicols['name']);$i++)
			{
				if(isset($uicols['exchange'][$i]) && $uicols['exchange'][$i])
				{
					$function_exchange_values .= 'opener.document.form.' . $uicols['name'][$i] .'.value = thisform.elements[' . $i . '].value;' ."\r\n";
				}
			}

			$function_exchange_values .='window.close()';

//_debug_array($input_name);
			$link_excel = array
			(
				'menuaction'		=> 'property.uilocation.excel',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'district_id'		=> $this->district_id,
				'part_of_town_id'	=> $this->part_of_town_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'lookup'		=> $lookup,
				'lookup_tenant'		=> $lookup_tenant,
				'lookup_name'		=> $lookup_name,
				'type_id'		=> $type_id,
				'status'		=> $this->status,
				'start'			=> $this->start
			);

			$link_columns = array
			(
				'menuaction'	=> 'property.uilocation.columns',
				'type_id'		=> $type_id,
				'lookup'		=> $this->lookup
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
			{
				$owner_list = $this->bo->get_owner_list('filter', $this->filter);
			}
			else
			{
				$owner_list = $this->bo->get_owner_type_list('filter', $this->filter);
			}

//_debug_array($owner_list);

			$data = array
			(
				'colspan'				=> $uicols_count+1,
				'lang_excel'				=> 'excel',
				'link_excel'				=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'			=> lang('Download table to MS Excel'),

				'lang_columns'				=> lang('columns'),
				'link_columns'				=> $GLOBALS['phpgw']->link('/index.php',$link_columns),
				'lang_columns_help'			=> lang('Choose columns'),

				'exchange_values'			=> 'Exchange_values(this.form);',
				'function_exchange_values'		=> $function_exchange_values,
				'lang_select'				=> lang('select'),
				'lookup'				=> $lookup,
				'lang_property_name'			=> lang('Property name'),
				'allow_allrows'				=> True,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($location_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'lang_status_statustext'		=> lang('Select the status. To do not use a status select NO STATUS'),
				'status_name'				=> 'status',
				'lang_no_status'			=> lang('No status'),
				'status_list'				=> $this->bo->select_status_list('filter',$this->status),

				'part_of_town_list'				=> $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id),
				'lang_no_part_of_town'				=> lang('no part of town'),
				'lang_town_statustext'				=> lang('Select the part of town the selection belongs to. To do not use a part of town select NO PART OF TOWN'),
				'select_name_part_of_town'			=> 'part_of_town_id',

				'district_list'					=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'			=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'				=> 'district_id',

				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the location belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'location','type_id' =>$type_id,'order'=>'descr')),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'owner_name'					=> 'filter',
				'owner_list'					=> $owner_list,
				'lang_show_all'					=> lang('Show all'),
				'lang_owner_statustext'				=> lang('Select the owner type. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add
			);

			$appname						= lang('location');

			if($lookup)
			{
				$lookup_list	= $GLOBALS['phpgw']->session->appsession('lookup_name','property');
				$function_msg	= $lookup_list[$lookup_name];
			}
			else
			{
				if($lookup_tenant)
				{
					$function_msg	= lang('Tenant');
				}
				else
				{
					$function_msg					= $uicols['descr'][($type_id)];
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();

		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array(
									'menuaction'=> 'property.uilocation.stop',
									'perm'=>2,
									'acl_location'=>$this->acl_location
									)
								);
			}

			$get_history 		= phpgw::get_var('get_history', 'bool', 'POST');
			$change_type 		= phpgw::get_var('change_type', 'int', 'POST');
			$lookup_tenant 		= phpgw::get_var('lookup_tenant', 'bool');
			$location_code		= phpgw::get_var('location_code');
			$values_attribute	= phpgw::get_var('values_attribute');
			$location = split('-',$location_code);

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
			$GLOBALS['phpgw']->session->appsession('insert_record','property','');

			$values = false;
			if(isset($_POST['save']))
			{
				if(isset($insert_record['location']) && is_array($insert_record['location']))
				{
					for ($i=0; $i<count($insert_record['location']); $i++)
					{
						$values[$insert_record['location'][$i]]= $_POST[$insert_record['location'][$i]];
					}
				}

				for ($i=0; $i<count($insert_record['extra']); $i++)
				{
					$values[$insert_record['extra'][$i]]= $_POST[$insert_record['extra'][$i]];
				}
			}

			$type_id	 	= $this->type_id;

			if($location_code)
			{
				$type_id = count($location);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location','attributes_form'));

			if (is_array($values) )
			{
				for ($i=1; $i<($type_id+1); $i++)
				{
					if((!$values['loc' . $i]  && (!isset($location[($i-1)])  || !$location[($i-1)])  ) || !$values['loc' . $i])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a location %1 ID !',$i));
						$error_id=true;
					}

					$values['location_code'][]= $values['loc' . $i];

					if($i<$type_id)
					{
						$location_parent[]= $values['loc' . ($i)];
					}
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category'));
				}

				if (array_search('street_id',$insert_record['extra']) && (!isset($values['street_id']) || !$values['street_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a street'));
				}
				if (array_search('part_of_town_id',$insert_record['extra']) && (!isset($values['part_of_town_id']) || !$values['part_of_town_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a part of town'));
				}
				if (array_search('owner_id',$insert_record['extra']) && (!isset($values['owner_id']) || !$values['owner_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select an owner'));
				}

				$values['location_code']=implode("-", $values['location_code']);

				if($values['location_code'] && !$location_code)
				{
					if($this->bo->check_location($values['location_code'],$type_id))
					{
						$receipt['error'][]=array('msg'=>lang('This location is already registered!') . '[ '.$values['location_code'].' ]');
						$error_location_id=true;
						$error_id=true;
					}
				}

				if($location_code)
				{
					$action='edit';
					$values['change_type'] = $change_type;


					if(!$values['change_type'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select change type'));
					}
				}
				elseif(!$location_code && !$error_id )
				{
					$location_code=$values['location_code'];
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->save($values,$values_attribute,$action,$type_id,isset($location_parent)?$location_parent:'');
				}
				else
				{
					if(isset($location_parent) && $location_parent)
					{
						$location_code_parent=implode("-", $location_parent);
						$values = $this->bo->read_single($location_code_parent);
						/* restore date from posting */
						for ($i=0; $i<count($insert_record['extra']); $i++)
						{
							$values[$insert_record['extra'][$i]]= $_POST[$insert_record['extra'][$i]];
						}
					}
				}
			}

			if(!isset($error_id) && $location_code)
			{
				$values = $this->bo->read_single($location_code,array('tenant_id'=>'lookup'));
				$check_history = $this->bo->check_history($location_code);
				if($get_history)
				{
					$history = $this->bo->get_history($location_code);
					$uicols = $this->bo->uicols;

					$j=0;
					if (isSet($history) AND is_array($history))
					{
						foreach($history as $entry)
						{
							$k=0;
							for ($i=0;$i<count($uicols['name']);$i++)
							{
								if($uicols['input_type'][$i]!='hidden')
								{
									$content[$j]['row'][$k]['value'] 	= $entry[$uicols['name'][$i]];
									$content[$j]['row'][$k]['name'] 	= $uicols['name'][$i];
								}

								$content[$j]['hidden'][$k]['value'] 		= $entry[$uicols['name'][$i]];
								$content[$j]['hidden'][$k]['name'] 		= $uicols['name'][$i];
								$k++;
							}
							$j++;
						}
					}

					$uicols_count	= count($uicols['descr']);
					for ($i=0;$i<$uicols_count;$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$table_header[$i]['header'] 	= $uicols['descr'][$i];
							$table_header[$i]['width']	= '5%';
							$table_header[$i]['align']	= 'center';
						}
					}
				}
			}
			else
			{
				unset($values['location_code']);
			}


			if ($values['cat_id'] > 0)
			{
				$this->cat_id = $values['cat_id'];
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uilocation.edit',
				'location_code'	=> $location_code,
				'type_id'	=> $type_id,
				'lookup_tenant'	=> $lookup_tenant
			);

			$lookup_type='form';
			
			$location_data=$this->bo->initiate_ui_location(array(
						'values'		=> $values,
						'type_id'		=> ($type_id-1),
						'no_link'		=> ($type_id), // disable lookup links for location type less than type_id
						'tenant'		=> False,
						'lookup_type'	=> $lookup_type
						));

			$location_types	= $this->bo->location_types;
			$config			= $this->bo->config;

			if ($location_code)
			{
				$function_msg = lang('edit');
			}
			else
			{
				$function_msg = lang('add');
			}

			$function_msg .= ' ' .$location_types[($type_id-1)]['name'];

			$custom_fields	= $this->soadmin_location->read_attrib(array('type_id'=>$type_id,'allrows'=>True));
			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

			if(!is_array($insert_record))
			{
				$insert_record = array();
			}

			$j=0;
			$additional_fields[$j]['input_text']	= $location_types[($type_id-1)]['name'];
			$additional_fields[$j]['statustext']	= $location_types[($type_id-1)]['descr'];
			$additional_fields[$j]['datatype']		= 'varchar';
			$additional_fields[$j]['input_name']	= 'loc' . $type_id;
			$additional_fields[$j]['name']			= 'loc' . $type_id;
			$additional_fields[$j]['value']			= (isset($values[$additional_fields[$j]['input_name']])?$values[$additional_fields[$j]['input_name']]:'');
			$additional_fields[$j]['class']			= 'th_text';
			$insert_record['extra'][]				= $additional_fields[$j]['input_name'];

			$j++;
			$additional_fields[$j]['input_text']	= lang('name');
			$additional_fields[$j]['statustext']	= lang('enter the name for this location');
			$additional_fields[$j]['datatype']		= 'varchar';
			$additional_fields[$j]['input_name']	= 'loc' . $type_id . '_name';
			$additional_fields[$j]['name']			= 'loc' . $type_id . '_name';
			$additional_fields[$j]['value']			= (isset($values[$additional_fields[$j]['input_name']])?$values[$additional_fields[$j]['input_name']]:'');
			$insert_record['extra'][]				= $additional_fields[$j]['input_name'];
			$j++;


			$contacts			= CreateObject('phpgwapi.contacts');

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$sep = '/';
			$dlarr[strpos($dateformat,'Y')] = 'Y';
			$dlarr[strpos($dateformat,'m')] = 'm';
			$dlarr[strpos($dateformat,'d')] = 'd';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$input_type_array = array(
				'R' => 'radio',
				'CH' => 'checkbox',
				'LB' => 'listbox'
			);

			$vendor = CreateObject('property.soactor');
			$vendor->role = 'vendor';

			$r=0;
			$m=0;
			while (is_array($custom_fields) && list(,$custom) = each($custom_fields))
			{
				$location_datatype[]= array(
					'input_name'	=> $custom['column_name'],
					'datatype'	=> $custom['datatype']
					);

				$attributes_values[$r]['attrib_id']		= $custom['id'];
				$attributes_values[$r]['id']			= $custom['id'];
				$attributes_values[$r]['input_text']	= $custom['input_text'];
				$attributes_values[$r]['statustext']	= $custom['statustext'];
				$attributes_values[$r]['datatype']		= $custom['datatype'];
				$attributes_values[$r]['name']			= $custom['column_name'];
				$attributes_values[$r]['input_name']	= $custom['column_name'];
				$attributes_values[$r]['value']			= $values[$custom['column_name']];
				
				/* Preserve attribute values from post */
				if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
				{
					$attributes_values[$r]['value'] = $values_attribute[$r]['value'];
				}

				if($attributes_values[$r]['datatype']=='D' && $attributes_values[$r]['value'])
				{
					$timestamp_date= mktime(0,0,0,date(m,strtotime($attributes_values[$r]['value'])),date(d,strtotime($attributes_values[$r]['value'])),date(y,strtotime($attributes_values[$r]['value'])));
					$attributes_values[$r]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
				}
				if($attributes_values[$r]['datatype']=='AB')
				{
					if($attributes_values[$r]['value'])
					{
						$contact_data	= $contacts->read_single_entry($attributes_values[$r]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$attributes_values[$r]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$functions[$m]['name'] = 'lookup_'. $attributes_values[$r]['name'] .'()';
					$functions[$m]['link'] = "menuaction:'" . 'property'.".uilookup.addressbook',"
													. "column:'" . $attributes_values[$r]['name'] . "'";
					$functions[$m]['action'] = 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($attributes_values[$r]['datatype']=='VENDOR')
				{
					if($attributes_values[$r]['value'])
					{
						$vendor_data	= $vendor->read_single(array('actor_id'=>$attributes_values[$r]['value']));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['name'] == 'org_name')
							{
								$attributes_values[$r]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}

					$functions[$m]['name'] = 'lookup_'. $attributes_values[$r]['name'] .'()';
					$functions[$m]['link'] = "menuaction:'" . 'property'.".uilookup.vendor',"
													. "column:'" . $attributes_values[$r]['name'] . "'";												

					$functions[$m]['action'] = 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}


				if($attributes_values[$r]['datatype']=='R' || $attributes_values[$r]['datatype']=='CH' || $attributes_values[$r]['datatype']=='LB')
				{
					$attributes_values[$r]['choice'] = $this->soadmin_location->read_attrib_choice($type_id,$attributes_values[$r]['id']);
					$input_type=$input_type_array[$attributes_values[$r]['datatype']];

					if($attributes_values[$r]['datatype']=='CH')
					{
						$attributes_values[$r]['value']=unserialize($attributes_values[$r]['value']);
						$attributes_values[$r]['choice'] = $this->bocommon->select_multi_list_2($attributes_values[$r]['value'],$attributes_values[$r]['choice'],$input_type);

					}
					else
					{
						for ($j=0;$j<count($attributes_values[$r]['choice']);$j++)
						{
							$attributes_values[$r]['choice'][$j]['input_type']=$input_type;
							if($attributes_values[$r]['choice'][$j]['id']==$attributes_values[$r]['value'])
							{
								$attributes_values[$r]['choice'][$j]['checked']='checked';
							}
						}
					}
				}

				$attributes_values[$r]['datatype_text'] = $this->bocommon->translate_datatype($attributes_values[$r]['datatype']);
				$attributes_values[$r]['counter']	= $r;
				$attributes_values[$r]['type_id']	= $type_id;
				$r++;

				if(isset($functions) && is_array($functions))
				{
					for ($j=0;$j<count($functions);$j++)
					{
						$lookup_functions .= "\t".'function ' . $functions[$j]['name'] ."\n";
						$lookup_functions .= "\t".'{'."\n";
						$lookup_functions .= "\t\tvar oArgs = {" . $functions[$j]['link'] ."};" . "\n";
						$lookup_functions .= "\t\tvar strURL = phpGWLink('index.php', oArgs);\n";
						$lookup_functions .= "\t\t".$functions[$j]['action'] ."\n";
						$lookup_functions .= "\t".'}'."\n";
					}
				}
			}

//_debug_array($attributes_values);
			$GLOBALS['phpgw']->session->appsession('location_datatype','property',$location_datatype);

			$insert_record['extra'][]						= 'cat_id';

			$config_count=count($config);
			for ($j=0;$j<$config_count;$j++)
			{
				if($config[$j]['location_type'] == $type_id)
				{

					if($config[$j]['column_name']=='street_id')
					{
						$edit_street=True;
						$insert_record['extra'][]	= 'street_id';
						$insert_record['extra'][]	= 'street_number';
					}

					if($config[$j]['column_name']=='tenant_id')
					{
						$edit_tenant=True;
						$insert_record['extra'][]	= 'tenant_id';
					}

					if($config[$j]['column_name']=='part_of_town_id')
					{
						$edit_part_of_town		= True;
						$select_name_part_of_town	= 'part_of_town_id';
						$part_of_town_list		= $this->bocommon->select_part_of_town('select',$values['part_of_town_id']);
						$lang_town_statustext		= lang('Select the part of town the property belongs to. To do not use a part of town -  select NO PART OF TOWN');
						$insert_record['extra'][]	= 'part_of_town_id';
					}
					if($config[$j]['column_name']=='owner_id')
					{
						$edit_owner			= True;
						$lang_owner			= lang('Owner');
						$owner_list			= $this->bo->get_owner_list('',$values['owner_id']);
						$lang_select_owner		= lang('Select owner');
						$lang_owner_statustext		= lang('Select the owner');
						$insert_record['extra'][]	= 'owner_id';
					}
				}
			}

			$GLOBALS['phpgw']->session->appsession('insert_record','property',$insert_record);


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

			if(isset($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}


			if($location_code)
			{
				$change_type_list = $this->bo->select_change_type($values['change_type']);

				$location_types = $this->soadmin_location->read(array('order'=>'id','sort'=>'ASC'));
				foreach ($location_types as $location_type)
				{
					if($type_id != $location_type['id'])
					{
						if($type_id > $location_type['id'])
						{
							$entities_link[] = array
							(
								'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
															'menuaction'=> 'property.uilocation.edit',
															'location_code'=>implode('-',array_slice($location, 0, $location_type['id']))
															)
														),
								'lang_entity_statustext'	=> $location_type['descr'],
								'text_entity'			=> $location_type['name'],
							);
						}
						else
						{
							$entities_link[] = array
							(
								'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
															'menuaction'=> 'property.uilocation.index',
															'type_id'=> $location_type['id'],
															'query'=>implode('-',array_slice($location, 0, $location_type['id']))
															)
														),
								'lang_entity_statustext'	=> $location_type['descr'],
								'text_entity'			=> $location_type['name'],
							);
						}
					}
				}

				$entities= $this->bo->read_entity_to_link($location_code);

				if (isset($entities) AND is_array($entities))
				{
					foreach($entities as $entity_entry)
					{
						if(isset($entity_entry['entity_link']) && $entity_entry['entity_link'])
						{
							$entities_link[] = array
							(
								'entity_link'			=> $entity_entry['entity_link'],
								'lang_entity_statustext'	=> $entity_entry['descr'],
								'text_entity'			=> $entity_entry['name'],
							);
						}
						else
						{
							$entities_link[] = array
							(
								'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
																'menuaction'=> 'property.uientity.index',
																'entity_id'=> $entity_entry['entity_id'],
																'cat_id'=> $entity_entry['cat_id'],
																'query'=> $location_code
																)
															),
								'lang_entity_statustext'	=> $entity_entry['descr'],
								'text_entity'			=> $entity_entry['name'],
							);
						}
					}
				}
			}

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');

			$data = array
			(
				'lang_change_type'				=> lang('Change type'),
				'lang_no_change_type'			=> lang('No Change type'),
				'lang_change_type_statustext'	=> lang('Type of changes'),
				'change_type_list'				=> (isset($change_type_list)?$change_type_list:''),
				'check_history'					=> (isset($check_history)?$check_history:''),
				'lang_history'					=> lang('History'),
				'lang_history_statustext'		=> lang('Fetch the history for this item'),
				'table_header'					=> (isset($table_header)?$table_header:''),
				'values'						=> (isset($content)?$content:''),

				'lang_related_info'				=> lang('related info'),
				'entities_link'					=> (isset($entities_link)?$entities_link:''),
				'edit_street'					=> (isset($edit_street)?$edit_street:''),
				'edit_tenant'					=> (isset($edit_tenant)?$edit_tenant:''),
				'edit_part_of_town'				=> (isset($edit_part_of_town)?$edit_part_of_town:''),
				'edit_owner'					=> (isset($edit_owner)?$edit_owner:''),
				'select_name_part_of_town'		=> (isset($select_name_part_of_town)?$select_name_part_of_town:''),
				'part_of_town_list'				=> (isset($part_of_town_list)?$part_of_town_list:''),
				'lang_town_statustext'			=> (isset($lang_town_statustext)?$lang_town_statustext:''),
				'lang_part_of_town'				=> lang('Part of town'),
				'lang_no_part_of_town'			=> lang('No part of town'),
				'lang_owner'					=> (isset($lang_owner)?$lang_owner:''),
				'owner_list'					=> (isset($owner_list)?$owner_list:''),
				'lang_select_owner'				=> (isset($lang_select_owner)?$lang_select_owner:''),
				'lang_owner_statustext'			=> (isset($lang_owner_statustext)?$lang_owner_statustext:''),
				'additional_fields'				=> $additional_fields,
				'attributes_values'				=> $attributes_values,
				'lookup_functions'				=> (isset($lookup_functions)?$lookup_functions:''),
				'lang_none'						=> lang('None'),

				'msgbox_data'					=> (isset($msgbox_data)?$GLOBALS['phpgw']->common->msgbox($msgbox_data):''),
				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'dateformat_validate'			=> $dateformat_validate,
				'onKeyUp'						=> $onKeyUp,
				'onBlur'						=> $onBlur,
				'street_link'					=> "menuaction:'" . 'property'.".uilookup.street'",
				'lang_street'					=> lang('Street'),
				'lang_select_street_help'		=> lang('Select the street name'),
				'lang_street_num_statustext'	=> lang('Enter the street number'),
				'value_street_id'				=> (isset($values['street_id'])?$values['street_id']:''),
				'value_street_name'				=> (isset($values['street_name'])?$values['street_name']:''),
				'value_street_number'			=> (isset($values['street_number'])?$values['street_number']:''),

				'tenant_link'					=> "menuaction:'" . 'property'.".uilookup.tenant'",
				'lang_tenant'					=> lang('tenant'),
				'value_tenant_id'				=> (isset($values['tenant_id'])?$values['tenant_id']:''),
				'value_last_name'				=> (isset($values['last_name'])?$values['last_name']:''),
				'value_first_name'				=> (isset($values['first_name'])?$values['first_name']:''),
				'lang_tenant_statustext'		=> lang('Select a tenant'),
				'size_last_name'				=> (isset($values['last_name'])?strlen($values['last_name']):''),
				'size_first_name'				=> (isset($values['first_name'])?strlen($values['first_name']):''),
				'lookup_type'					=> $lookup_type,
				'location_data'					=> $location_data,
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index','type_id'=> $type_id, 'lookup_tenant'=> $lookup_tenant)),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the location'),
				'lang_category'					=> lang('category'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the location belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'location','type_id' =>$type_id,'order'=>'descr')),
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$appname	= lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array(
									'menuaction'	=> 'property.uilocation.stop',
									'perm'			=> 8,
									'acl_location'	=> $this->acl_location
									)
								);
			}

			$location_code	 	= phpgw::get_var('location_code', 'string', 'GET');
			$type_id	 	= $this->type_id;


			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uilocation.index',
				'type_id'	=>$type_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($location_code);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.delete', 'location_code'=> $location_code, 'type_id'=> $type_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname			= lang('location');
			$function_msg			= lang('delete location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array(
									'menuaction'	=> 'property.uilocation.stop',
									'perm'		=> 1,
									'acl_location'	=> $this->acl_location
									)
								);
			}

			$get_history 		= phpgw::get_var('get_history', 'bool', 'POST');
			$lookup_tenant		= phpgw::get_var('lookup_tenant', 'bool');
			$location_code 		= phpgw::get_var('location_code');
			$location 		= split('-',$location_code);

			$type_id	 	= $this->type_id;

			if($location_code)
			{
				$type_id = count($location);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location','attributes_view'));

			$values = $this->bo->read_single($location_code,array('tenant_id'=>'lookup'));

			$check_history = $this->bo->check_history($location_code);
			if($get_history)
			{
				$history = $this->bo->get_history($location_code);
				$uicols = $this->bo->uicols;

				$j=0;
				if (isSet($history) AND is_array($history))
				{
					foreach($history as $entry)
					{
						$k=0;
						for ($i=0;$i<count($uicols['name']);$i++)
						{
							if($uicols['input_type'][$i]!='hidden')
							{
								$content[$j]['row'][$k]['value'] 		= $entry[$uicols['name'][$i]];
								$content[$j]['row'][$k]['name'] 		= $uicols['name'][$i];
								$content[$j]['row'][$k]['lookup'] 		= $lookup;
							}

							$content[$j]['hidden'][$k]['value'] 			= $entry[$uicols['name'][$i]];
							$content[$j]['hidden'][$k]['name'] 			= $uicols['name'][$i];
							$k++;
						}
						$j++;
					}
				}

				$uicols_count	= count($uicols['descr']);
				for ($i=0;$i<$uicols_count;$i++)
				{
					if($uicols['input_type'][$i]!='hidden')
					{
						$table_header[$i]['header'] 	= $uicols['descr'][$i];
						$table_header[$i]['width'] 		= '5%';
						$table_header[$i]['align'] 		= 'center';
					}
				}
			}

			$lookup_type='view';

			$location_data=$this->bo->initiate_ui_location(array(
						'values'		=> $values,
						'type_id'		=> ($type_id-1),
						'lookup_type'		=> $lookup_type
						));

			$location_types	= $this->bo->location_types;
			$config			= $this->bo->config;

			$function_msg = lang('view');

			$function_msg .= ' ' .$location_types[($type_id-1)]['name'];

			$custom_fields	= $this->soadmin_location->read_attrib(array('type_id'=>$type_id,'allrows'=>True));

			$j=0;
			$additional_fields[$j]['input_text']	= $location_types[($type_id-1)]['name'];
			$additional_fields[$j]['input_name']	= 'loc' . $type_id;
			$additional_fields[$j]['datatype']	= 'varchar';
			$additional_fields[$j]['value']		= $values[$additional_fields[$j]['input_name']];
			$additional_fields[$j]['class']		= 'th_text';

			$j++;
			$additional_fields[$j]['input_text']	= lang('name');
			$additional_fields[$j]['input_name']	= 'loc' . $type_id . '_name';
			$additional_fields[$j]['datatype']	= 'varchar';
			$additional_fields[$j]['value']		= $values[$additional_fields[$j]['input_name']];
			$j++;


			$contacts			= CreateObject('phpgwapi.contacts');

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$sep = '/';
			$dlarr[strpos($dateformat,'Y')] = 'Y';
			$dlarr[strpos($dateformat,'m')] = 'm';
			$dlarr[strpos($dateformat,'d')] = 'd';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$input_type_array = array(
				'R' => 'radio',
				'CH' => 'checkbox',
				'LB' => 'listbox'
			);

			$vendor = CreateObject('property.soactor');
			$vendor->role = 'vendor';

			$r=0;
			$m=0;

			while (is_array($custom_fields) && list(,$custom) = each($custom_fields))
			{
				$location_datatype[]= array(
					'input_name'	=> $custom['column_name'],
					'datatype'	=> $custom['datatype']
					);

				$attributes_values[$r]['id']	= $custom['id'];
				$attributes_values[$r]['input_text']	= $custom['input_text'];
				$attributes_values[$r]['statustext']	= $custom['statustext'];
				$attributes_values[$r]['datatype']	= $custom['datatype'];
				$attributes_values[$r]['name']	= $custom['column_name'];
				$attributes_values[$r]['input_name']	= $custom['column_name'];
				$attributes_values[$r]['value']			= $values[$custom['column_name']];
				if($attributes_values[$r]['datatype']=='D' && $attributes_values[$r]['value'])
				{
					$timestamp_date= mktime(0,0,0,date(m,strtotime($attributes_values[$r]['value'])),date(d,strtotime($attributes_values[$r]['value'])),date(y,strtotime($attributes_values[$r]['value'])));
					$attributes_values[$r]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
				}
				if($attributes_values[$r]['datatype']=='AB')
				{
					if($attributes_values[$r]['value'])
					{
						$contact_data	= $contacts->read_single_entry($attributes_values[$r]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$attributes_values[$r]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$functions[$m]['name'] = 'lookup_'. $attributes_values[$r]['name'] .'()';
					$functions[$m]['link'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column'=> $attributes_values[$r]['name']));
					$functions[$m]['action'] = 'Window1=window.open(link,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($attributes_values[$r]['datatype']=='VENDOR')
				{
					if($attributes_values[$r]['value'])
					{
						$vendor_data	= $vendor->read_single(array('actor_id'=>$attributes_values[$r]['value']));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['name'] == 'org_name')
							{
								$attributes_values[$r]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}


					$lookup_functions[$m]['name'] = 'lookup_'. $attributes_values[$r]['name'] .'()';
					$lookup_functions[$m]['link'] = "menuaction:'" . 'property'.".uilookup.vendor',column:'" . $attributes_values[$r]['name'] . "'";
					$lookup_functions[$m]['action'] = 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($attributes_values[$r]['datatype']=='R' || $attributes_values[$r]['datatype']=='CH' || $attributes_values[$r]['datatype']=='LB')
				{
					$attributes_values[$r]['choice']	= $this->soadmin_location->read_attrib_choice($type_id,$attributes_values[$r]['id']);
					$input_type=$input_type_array[$attributes_values[$r]['datatype']];

					if($attributes_values[$r]['datatype']=='CH')
					{
						$attributes_values[$r]['value']=unserialize($attributes_values[$r]['value']);
						$attributes_values[$r]['choice'] = $this->bocommon->select_multi_list_2($attributes_values[$r]['value'],$attributes_values[$r]['choice'],$input_type);

					}
					else
					{
						for ($j=0;$j<count($attributes_values[$r]['choice']);$j++)
						{
							$attributes_values[$r]['choice'][$j]['input_type']=$input_type;
							if($attributes_values[$r]['choice'][$j]['id']==$attributes_values[$r]['value'])
							{
								$attributes_values[$r]['choice'][$j]['checked']='checked';
							}
						}
					}
				}

				$attributes_values[$r]['datatype_text'] = $this->bocommon->translate_datatype($attributes_values[$r]['datatype']);
				$r++;
			}


//	_debug_array($custom_fields);

			for ($j=0;$j<count($config);$j++)
			{
				if($config[$j]['location_type'] == $type_id)
				{

					if($config[$j]['column_name']=='street_id')
					{
						$edit_street=True;
						$insert_record[]	= 'street_id';
					}

					if($config[$j]['column_name']=='tenant_id')
					{
						$edit_tenant=True;
						$insert_record[]	= 'tenant_id';
					}

					if($config[$j]['column_name']=='part_of_town_id')
					{
						$edit_part_of_town=True;
						$select_name_part_of_town	= 'part_of_town_id';
						$part_of_town_list		= $this->bocommon->select_part_of_town('select',$values['part_of_town_id']);
						$lang_town_statustext		= lang('Select the part of town the property belongs to. To do not use a part of town -  select NO PART OF TOWN');
						$insert_record[]		= 'part_of_town_id';
					}
					if($config[$j]['column_name']=='owner_id')
					{
						$edit_owner=True;
						$lang_owner			= lang('Owner');
						$owner_list			= $this->bo->get_owner_list('',$values['owner_id']);
						$lang_select_owner		= lang('Select owner');
						$lang_owner_statustext		= lang('Select the owner');
						$insert_record[]		= 'owner_id';
					}
				}
			}

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$location_types = $this->soadmin_location->read(array('order'=>'id','sort'=>'ASC'));
			foreach ($location_types as $location_type)
			{
				if($type_id != $location_type['id'])
				{
					if($type_id > $location_type['id'])
					{
						$entities_link[] = array
						(
							'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
														'menuaction'=> 'property.uilocation.view',
														'location_code'=>implode('-',array_slice($location, 0, $location_type['id']))
														)
													),
							'lang_entity_statustext'	=> $location_type['descr'],
							'text_entity'			=> $location_type['name'],
						);
					}
					else
					{
						$entities_link[] = array
						(
							'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
														'menuaction'=> 'property.uilocation.index',
														'type_id'=> $location_type['id'],
														'query'=>implode('-',array_slice($location, 0, $location_type['id']))
														)
													),
							'lang_entity_statustext'	=> $location_type['descr'],
							'text_entity'			=> $location_type['name'],
						);
					}
				}
			}

			$entities= $this->bo->read_entity_to_link($location_code);

			if (isset($entities) AND is_array($entities))
			{
				foreach($entities as $entity_entry)
				{
					if($entity_entry['entity_link'])
					{
						$entities_link[] = array
						(
							'entity_link'			=> $entity_entry['entity_link'],
							'lang_entity_statustext'	=> $entity_entry['descr'],
							'text_entity'			=> $entity_entry['name'],
						);
					}
					else
					{
						$entities_link[] = array
						(
							'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
																'menuaction'=> 'property.uientity.index',
																'entity_id'=> $entity_entry['entity_id'],
																'cat_id'=> $entity_entry['cat_id'],
																'query'=>$location_code
																)
															),
							'lang_entity_statustext'	=> $entity_entry['descr'],
							'text_entity'			=> $entity_entry['name'],
						);
					}
				}
			}

			$change_type_list = $this->bo->select_change_type($values['change_type']);

			$data = array
			(
				'lang_change_type'			=> lang('Change type'),
				'check_history'				=> $check_history,
				'lang_history'				=> lang('History'),
				'lang_history_statustext'		=> lang('Fetch the history for this item'),
				'table_header'				=> (isset($table_header)?$table_header:''),
				'change_type_list'			=> $change_type_list,
				'values'				=> (isset($content)?$content:''),

				'lang_related_info'			=> lang('related info'),
				'entities_link'				=> (isset($entities_link)?$entities_link:''),
				'edit_street'				=> $edit_street,
				'edit_tenant'				=> $edit_tenant,
				'edit_part_of_town'			=> $edit_part_of_town,
				'edit_owner'				=> $edit_owner,
				'select_name_part_of_town'		=> $select_name_part_of_town,
				'part_of_town_list'			=> $part_of_town_list,
				'lang_town_statustext'			=> $lang_town_statustext,
				'lang_part_of_town'			=> lang('Part of town'),
				'lang_no_part_of_town'			=> lang('No part of town'),
				'lang_owner'				=> $lang_owner,
				'owner_list'				=> $owner_list,
				'lang_select_owner'			=> $lang_select_owner,
				'lang_owner_statustext'			=> $lang_owner_statustext,
				'additional_fields'			=> $additional_fields,
				'lang_street'				=> lang('Street'),
				'lang_select_street_help'		=> lang('Select the street name'),
				'lang_street_num_statustext'	=> lang('Enter the street number'),
				'value_street_id'			=> $values['street_id'],
				'value_street_name'			=> $values['street_name'],
				'value_street_number'			=> $values['street_number'],

				'attributes_view'			=> $attributes_values,
				'dateformat'				=> $dateformat,
				'lang_dateformat' 			=> strtolower($dateformat),
				'lang_none'				=> lang('None'),

				'lang_tenant'				=> lang('tenant'),
				'value_tenant_id'			=> $values['tenant_id'],
				'value_last_name'			=> $values['last_name'],
				'value_first_name'			=> $values['first_name'],
				'lang_tenant_statustext'		=> lang('Select a tenant'),
				'size_last_name'			=> strlen($values['last_name']),
				'size_first_name'			=> strlen($values['first_name']),
				'lookup_type'				=> $lookup_type,
				'location_data'				=> $location_data,
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uilocation.index', 'type_id'=> $type_id,'lookup_tenant'=> $lookup_tenant)),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the location'),
				'lang_edit'				=> lang('Edit'),
				'edit_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uilocation.edit', 'location_code'=> $location_code, 'lookup_tenant'=> $lookup_tenant)),
				'lang_edit_statustext'			=> lang('Edit this entry'),
				'lang_category'				=> lang('category'),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the location belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'cat_id',
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'location','type_id' =>$type_id,'order'=>'descr')),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$appname					= lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function update_cat()
		{
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uilocation.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$receipt= $this->bo->update_cat();
				$lang_confirm_msg = lang('Do you really want to update the categories again');
				$lang_yes			= lang('again');
			}
			else
			{
				$lang_confirm_msg 	= lang('Do you really want to update the categories');
				$lang_yes			= lang('yes');
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'update_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.update_cat')),
				'message'			=> $receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'			=> $lang_yes,
				'lang_yes_statustext'		=> lang('Update the category to not active based on if there is only nonactive apartments'),
				'lang_no_statustext'		=> lang('Back to Admin'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('location');
			$function_msg	= lang('Update the not active category for locations');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('update_cat' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function stop()
		{
			$perm	 		= phpgw::get_var('perm', 'int');
			$location	 	= phpgw::get_var('acl_location');

			$right		= array(1=>'read',2=>'add',4=>'edit',8=>'delete',16=>'manage');

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$receipt['error'][]=array('msg'=>lang('You need the right "%1" for this application at "%2" to access this function',lang($right[$perm]),$location));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
			);

			$appname		= lang('Access error');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' : ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('stop' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function summary()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$summary_list= $this->bo->read_summary();
			$uicols	= $this->bo->uicols;

			$j=0;
			if (isSet($summary_list) AND is_array($summary_list))
			{
				foreach($summary_list as $summary)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$content[$j]['row'][$i]['value'] 			= isset($summary[$uicols['name'][$i]])?$summary[$uicols['name'][$i]]:'';
						$content[$j]['row'][$i]['name'] 			= isset($summary['name'][$i])?$summary['name'][$i]:'';
					}

					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			for ($i=0;$i<$uicols_count;$i++)
			{
				if(!isset($uicols['input_type'][$i]) || $uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '15%';
					$table_header[$i]['align'] 		= 'left';

				}
			}



			$link_excel = array
			(
				'menuaction'	=> 'property.uilocation.excel',
						'district_id'		=>$this->district_id,
						'part_of_town_id'	=>$this->part_of_town_id,
						'filter'		=>$this->filter,
					//	'type_id'		=>$type_id,
						'summary'		=>True
			);


			$link_data = array
			(
				'menuaction'	=> 'property.uilocation.summary',
						'district_id'		=>$this->district_id,
						'part_of_town_id'	=>$this->part_of_town_id,
						'filter'		=>$this->filter,
					//	'type_id'		=>$type_id
			);



			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
			{
				$owner_list = $this->bo->get_owner_list('filter', $this->filter);
			}
			else
			{
				$owner_list = $this->bo->get_owner_type_list('filter', $this->filter);
			}

			$data = array
			(
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'owner_name'				=> 'filter',
				'owner_list'				=> $owner_list,
				'lang_show_all'				=> lang('Show all'),
				'lang_owner_statustext'			=> lang('Select the owner type. To show all entries select SHOW ALL'),
				'select_name_part_of_town'		=> 'part_of_town_id',
				'part_of_town_list'			=> $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id),
				'lang_town_statustext'			=> lang('Select the part of town the property belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_no_part_of_town'			=> lang('No Part of town'),

				'district_list'				=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'			=> lang('no district'),
				'lang_district_statustext'		=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'			=> 'district_id',
				'lang_excel'				=> 'excel',
				'link_excel'				=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'			=> lang('Download table to MS Excel'),
				'table_header_summary'			=> $table_header,
				'values'				=> $content
			);

//_debug_array($data);

			$appname		= lang('Summary');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' : ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('summary' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
