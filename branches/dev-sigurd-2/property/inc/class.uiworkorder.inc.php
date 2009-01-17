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

	phpgw::import_class('phpgwapi.yui');

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
			'download'  => true,
			'index'  => true,
			'view'   => true,
			'add'   => true,
			'edit'   => true,
			'delete' => true,
			'view_file'	=> true
		);

		function property_uiworkorder()
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project::workorder';

			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo					= CreateObject('property.boworkorder',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->cats					= & $this->bo->cats;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.project';
			$this->acl_read 			= $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->status_id			= $this->bo->status_id;
			$this->search_vendor		= $this->bo->search_vendor;
			$this->wo_hour_cat_id		= $this->bo->wo_hour_cat_id;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->b_group				= $this->bo->b_group;
			$this->paid					= $this->bo->paid;
			$this->b_account			= $this->bo->b_account;
			$this->district_id			= $this->bo->district_id;
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
				'search_vendor'		=> $this->search_vendor,
				'status_id'			=> $this->status_id,
				'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
				'start_date'		=> $this->start_date,
				'end_date'			=> $this->end_date,
				'b_group'			=> $this->b_group,
				'paid'				=> $this->paid,
				'b_account'			=> $this->b_account,
				'district_id'		=> $this->district_id,
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 		= urldecode($this->end_date);
			$list 			= $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'allrows' => true));
			$uicols			= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('workorder');
		}

		function index()
		{

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$allrows  = phpgw::get_var('allrows', 'bool');

			$lookup = ''; //Fix this
			$dry_run = false;

			$datatable = array();
			$values_combo_box = array();

			$start_date 	= urldecode($this->start_date);
			$end_date 		= urldecode($this->end_date);

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

				if(!$lookup)
				{
					$datatable['menu']				= $this->bocommon->get_menu();
				}

	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uiworkorder.index',
									//'sort'			=> $this->sort,
									//'order'			=> $this->order,
									'lookup'        	=> $lookup,
									'cat_id'			=> $this->cat_id,
									'status_id'			=> $this->status_id,
									'filter'			=> $this->filter,
									'query'				=> $this->query,
									'search_vendor'		=> $this->search_vendor,
									'start_date'		=> $start_date,
									'end_date'			=> $end_date,
									'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
									'b_group'			=> $this->b_group,
									'paid'				=> $this->paid

	    				));
	    		$datatable['config']['allow_allrows'] = false;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiworkorder.index',"

	    											."query:'{$this->query}',"
 	                        						."search_vendor:'{$this->search_vendor}',"
						 	                        ."lookup:'{$lookup}',"
 	                        						."start_date:'{$start_date}',"
						 	                        ."end_date:'{$end_date}',"
						 	                        ."wo_hour_cat_id:'{$this->wo_hour_cat_id}',"
						 	                        ."filter:'{$this->filter}',"
						 	                        ."status_id:'{$this->status_id}',"
						 	                        ."cat_id:'{$this->cat_id}'";

				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => True));
				$default_value = array ('cat_id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$values_combo_box[1]  = $this->bo->select_status_list('filter',$this->status_id);
				$default_value = array ('id'=>'','name'=> lang('no status'));
				array_unshift ($values_combo_box[1],$default_value);

		        $values_combo_box[2] =  $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id'));
		 		$default_value = array ('id'=>'','name'=> lang('no hour category'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bocommon->get_user_list_right2('filter',2,$this->filter,$this->acl_location);
				$default_value = array ('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[3],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uiworkorder.index',
									'lookup'        	=> $lookup,
									'cat_id'			=> $this->cat_id,
									'status_id'			=> $this->status_id,
									'filter'			=> $this->filter,
									'query'				=> $this->query,
									'search_vendor'		=> $this->search_vendor,
									'start_date'		=> $start_date,
									'end_date'			=> $end_date,
									'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
									'paid'			=> $this->paid,
								)
							),
						'fields'	=> array(
                                    	'field' => array(
			                                        array( //boton 	CATEGORY
			                                            'id' => 'btn_cat_id',
			                                            'name' => 'cat_id',
			                                            'value'	=> lang('Category'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 1
			                                        ),
			                                        array( //boton 	STATUS
			                                            'id' => 'btn_status_id',
			                                            'name' => 'status_id',
			                                            'value'	=> lang('Status'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 2
			                                        ),
			                                        array( //boton 	HOUR CATEGORY
			                                            'id' => 'btn_wo_hour_cat_id',
			                                            'name' => 'wo_hour_cat_id',
			                                            'value'	=> lang('Hour category'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 3
			                                        ),
			                                        array( //boton 	USER
			                                            'id' => 'btn_user_id',
			                                            'name' => 'filter',
			                                            'value'	=> lang('User'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 4
			                                        ),
													array(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_export',
						                                'value'	=> lang('download'),
						                                'tab_index' => 10
						                            ),
													array(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_new',
						                                'value'	=> lang('add'),
						                                'tab_index' => 9
						                            ),
			                                        array( //boton     SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 8
			                                        ),
			   										array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'onkeypress' => 'return pulsar(event)',
			                                            'size'    => 18,
			                                            'tab_index' => 7
			                                        ),
			   										array( // TEXT IMPUT
			                                            'name'     => 'search_vendor',
			                                            'id'     => 'txt_search_vendor',
			                                            'value'    => '',
			                                            'type' => 'text',
			                                            'onkeypress' => 'return pulsar(event)',
			                                            'size'    => 6,
			                                            'tab_index' => 6
			                                        ),
			                                        array(
						                                'type'	=> 'hidden',
						                            	'id'	=> 'start_date',
						                                'value'	=> $start_date
						                            ),
			                                        array(
						                                'type'	=> 'hidden',
						                            	'id'	=> 'end_date',
						                                'value'	=> $end_date
						                            ),
	                                                array(
	                                                 	'type'=> 'label_date'
	                                                ),
			                                        array(
	                                                    'type'=> 'link',
	                                                    'id'  => 'btn_data_search',
	                                                    'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
	                                                           array(
	                                                               'menuaction' => 'property.uiproject.date_search'))."','','width=350,height=250')",
	                                                     'value' => lang('Date search'),
	                                                     'tab_index' => 5
                                                    )
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
							                                      ),
							                                array( //div values  combo_box_1
							                                            'id' => 'values_combo_box_1',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[1])
							                                      ),
															 array( //div values  combo_box_2
							                                            'id' => 'values_combo_box_2',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[2])
							                                      ),
							                                array( //div values  combo_box_3
							                                            'id' => 'values_combo_box_3',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[3])
							                                      )
		                       								)
												)
										  )
				);

				$dry_run = true;

			}

			$workorder_list = array();

			$workorder_list = $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'allrows' =>$allrows, 'dry_run' => $dry_run));
			$uicols = $this->bo->uicols;

			$content = array();
			$j=0;
			if (isset($workorder_list) && is_array($workorder_list))
			{
				foreach($workorder_list as $workorder)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($workorder['query_location'][$uicols['name'][$i]]))
							{

								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $workorder[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $workorder['query_location'][$uicols['name'][$i]];

							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $workorder[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

								if($uicols['name'][$i]=='vendor_id')
								{
									$datatable['rows']['row'][$j]['column'][$i]['statustext']		= $workorder['org_name'];
									$datatable['rows']['row'][$j]['column'][$i]['overlib']		= true;
									$datatable['rows']['row'][$j]['column'][$i]['text']			= $workorder[$uicols['name'][$i]];
								}

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $workorder[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $workorder[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $workorder[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $workorder[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}

					$j++;
				}
			}

			// NO pop-up
			if(!$lookup)
			{
				$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'workorder_id'
						),
					)
				);

				$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'workorder_id',
							'source'	=> 'workorder_id'
						),
					)
				);
				if($this->acl_read && $this->bocommon->check_perms($workorder['grants'],PHPGW_ACL_READ))
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiworkorder.view'
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'view',
						'text' 			=> lang('open view in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiworkorder.view',
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_edit && $this->bocommon->check_perms($workorder['grants'],PHPGW_ACL_EDIT))
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiworkorder.edit'
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'edit',
						'text'	 		=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiworkorder.edit',
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);

					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'calculate',
						'text' 			=> lang('calculate'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiwo_hour.index'
										)),
						'parameters'	=> $parameters2
					);
				}
				if($this->acl_delete && $this->bocommon->check_perms($workorder['grants'],PHPGW_ACL_DELETE))
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'delete',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiworkorder.delete'
										)),
						'parameters'	=> $parameters
					);
				}

				$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'add',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiworkorder.add'
										))
				);
				unset($parameters);
			}
			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
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
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='project_id' || $uicols['name'][$i]=='workorder_id' ||  $uicols['name'][$i]=='address')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
					if($uicols['name'][$i]=='loc1')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= "fm_location1.loc1";
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

			// path for property.js
			$datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned'] = count($workorder_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;


			$appname			= lang('Workorder');
			$function_msg		= lang('list workorder');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
			    $datatable['sorting']['order']	= 'entry_date'; // name key Column in myColumnDef
			    $datatable['sorting']['sort']	= 'desc'; // ASC / DESC
			}
			else
			{
			    $datatable['sorting']['order']  = phpgw::get_var('order', 'string'); // name of column of Database
			    $datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC
		    }

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
		  	phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('tabview');


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
					'records'			=> array()
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
		    				  $json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
		    				}else
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
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'workorder.index', 'property' );

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

			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

			$config->read();

			if (isset($values['save']))
			{
				if(!$values['title'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a workorder title !'));
				}
				if(!$values['project_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a valid project !'));
				}

				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}
				if(!$values['b_account_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if(isset($values['budget']) && $values['budget'] && !ctype_digit($values['budget']))
				{
					$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter an integer !'));
				}

				if(isset($values['addition_rs']) && $values['addition_rs'] && !ctype_digit($values['addition_rs']))
				{
					$receipt['error'][]=array('msg'=>lang('Rig addition') . ': ' . lang('Please enter an integer !'));
				}

				if(isset($values['addition_percentage']) && $values['addition_percentage'] && !ctype_digit($values['addition_percentage']))
				{
					$receipt['error'][]=array('msg'=>lang('Percentage addition') . ': ' . lang('Please enter an integer !'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if(!$receipt['error'])
				{
					if($values['copy_workorder'])
					{
						$action='add';
					}
					$receipt = $this->bo->save($values,$action);
					if (! $receipt['error'])
					{
						$id = $receipt['id'];
					}
					$function_msg = lang('Edit Workorder');
//----------files
					$bofiles	= CreateObject('property.bofiles');
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/workorder/{$id}/", $values);
					}

					$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);

					if($values['file_name'])
					{
						$to_file = $bofiles->fakebase . '/workorder/' . $id . '/' . $values['file_name'];

						if($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("workorder/$id");
							$bofiles->vfs->override_acl = 1;

							if(!$bofiles->vfs->cp (array (
								'from'	=> $_FILES['file']['tmp_name'],
								'to'	=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}
					}
//-----------
					if ($values['approval'] && $values['mail_address'] && $config->config_data['workorder_approval'])
					{
						$coordinator_name=$GLOBALS['phpgw_info']['user']['fullname'];
						$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
						$headers = "Return-Path: <". $coordinator_email .">\r\n";
						$headers .= "From: " . $coordinator_name . "<" . $coordinator_email .">\r\n";
						$headers .= "Bcc: " . $coordinator_name . "<" . $coordinator_email .">\r\n";
						$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
						$headers .= "MIME-Version: 1.0\r\n";

						$subject = lang(Approval).": ". $id;
						$message = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id'=> $values['project_id'])).'">' . lang('Workorder %1 needs approval',$id) .'</a>';

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
				if (isset($receipt['notice_owner']) AND is_array($receipt['notice_owner']) && $config->config_data['mailnotification'])
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

						$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject=lang('workorder %1 has been edited',$id),$body, false,false,false, $from_email, $from_name, 'html');

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

/*				if( $project['charge_tenant'] && !$id)
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
						'no_link'		=> false, // disable lookup links for location type less than type_id
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

			$link_file_data = array
			(
				'menuaction'	=> 'property.uiworkorder.view_file',
				'id'		=> $id
			);

			$categories = $this->cats->formatted_xslt_list(array('selected' => $project['cat_id']));

			$data = array
			(
				'tabs'							=> self::_generate_tabs(),
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
				'value_workorder_id'			=> (isset($id)?$id:''),

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
				'cat_list'					=> $categories['cat_list'],

				'sum_workorder_budget'			=> (isset($values['sum_workorder_budget'])?$values['sum_workorder_budget']:''),
				'workorder_budget'			=> (isset($values['workorder_budget'])?$values['workorder_budget']:''),

				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'select_user_name'			=> 'values[coordinator]',
				'user_list'				=> $this->bocommon->get_user_list('select',$project['coordinator'],$extra=false,$default=false,$start=-1,$sort=false,$order=false,$query='',$offset=-1),

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
				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'					=> (isset($this->bo->config->config_data['files_url'])?$this->bo->config->config_data['files_url']:''),
				'files'							=> isset($values['files'])?$values['files']:'',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_file_action'				=> lang('Delete file'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_file_action_statustext'	=> lang('Check to delete file'),
				'lang_upload_file'				=> lang('Upload file'),
				'lang_file_statustext'			=> lang('Select file to upload')
			);

			$appname						= lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder','files'));
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

			$id = phpgw::get_var('id', 'int');

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				$json = array
				(
					'result' 			=> 1,
				);
				return $json ;
			}

			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>8, 'acl_location'=> $this->acl_location));
			}
			//$id = phpgw::get_var('id', 'int');
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

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder', 'hour_data_view', 'files'));

			$uiwo_hour	= CreateObject('property.uiwo_hour');
			$hour_data	= $uiwo_hour->common_data($id,$view=true);
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

			$link_file_data = array
			(
				'menuaction'	=> 'property.uiworkorder.view_file',
				'id'		=> $id
			);

			$categories = $this->cats->formatted_xslt_list(array('selected' => $project['cat_id']));

			$data = array
			(
				'tabs'							=> self::_generate_tabs(),
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

				'cat_list'					=> $categories['cat_list'],
				'lang_workorder_id'			=> lang('Workorder ID'),
				'value_workorder_id'			=> $values['workorder_id'],

				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'user_list'				=> $this->bocommon->get_user_list('select',$project['coordinator'],$extra=false,$default=false,$start=-1,$sort=false,$order=false,$query='',$offset=-1),

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
				'contact_phone'				=> $project['contact_phone'],

				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'files'							=> $values['files'],
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_remark'				=> lang('remark'),
				'value_remark'				=> (isset($values['remark'])?$values['remark']:'')
			);

			$appname					= lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		protected function _generate_tabs()
		{
			$tabs = array
			(
				'project'		=> array('label' => lang('Project info'), 'link' => '#project'),
				'general'		=> array('label' => lang('general'), 'link' => '#general'),
				'budget'		=> array('label' => lang('Time and budget'), 'link' => '#budget'),
				'coordination'	=> array('label' => lang('coordination'), 'link' => '#coordination'),
				'extra'			=> array('label' => lang('extra'), 'link' => '#extra'),
				'documents'		=> array('label' => lang('documents'), 'link' => '#documents'),
				'history'		=> array('label' => lang('history'), 'link' => '#history')
			);

			phpgwapi_yui::tabview_setup('workorder_tabview');

			return phpgwapi_yui::tabview_generate($tabs, 'project');
		}

	}
