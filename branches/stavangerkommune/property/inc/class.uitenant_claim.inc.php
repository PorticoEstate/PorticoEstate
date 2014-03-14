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
				'index'		=> true,
				'check'		=> true,
				'view'		=> true,
				'edit'		=> true,
				'delete'	=> true,
				'view_file'	=> true
			);

		function property_uitenant_claim()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice::claim';
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.botenant_claim',true);
			$this->bocommon		= CreateObject('property.bocommon');
			$this->acl			= & $GLOBALS['phpgw']->acl;
			$this->acl_location	= '.tenant_claim';

			$this->acl_read 	= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	= $this->acl->check($this->acl_location, 16, 'property');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->user_id		= $this->bo->user_id;
			$this->status		= $this->bo->status;
			$this->cat_id		= $this->bo->cat_id;
			$this->allrows		= $this->bo->allrows;
			$this->project_id	= $this->bo->project_id;
		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'			=> $this->start,
					'query'			=> $this->query,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'user_id'		=> $this->user_id,
					'district_id'	=> $this->district_id,
					'status'		=> $this->status,
					'cat_id'		=> $this->cat_id,
					'allrows'		=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>PHPGW_ACL_READ, 'acl_location'=> $this->acl_location));
			}
			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('tenant_claim');
		}

		function index($project_id='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim',
				'receipt',
				'search_field',
				'nextmatchs'));

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>PHPGW_ACL_READ, 'acl_location'=> $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt','');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uitenant_claim.index',
						'query'            		=> $this->query,
						'cat_id'				=> $this->cat_id,
						'order'					=> $this->order,
						'user_id'				=> $this->user_id,
						'district_id'			=> $this->district_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uitenant_claim.index',"
					."sort: '{$this->sort}',"
					."order: '{$this->order}',"
					."status: '{$this->status}',"
					."project_id: '{$this->project_id}',"
					."user_id: '{$this->user_id}',"
					."district_id: '{$this->district_id}',"
					."query: '{$this->query}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
					(
						'menuaction'	=> 'property.uitenant_claim.index',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'cat_id'	=> $this->cat_id,
						'user_id'	=> $this->user_id,
						'status_id'	=> $this->status_id,
						'project_id'=> $this->project_id,
						'query'		=> $this->query
					);

				$values_combo_box[0] = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'tenant_claim','order'=>'descr'));
				$default_value = array ('id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->get_status_list(array('format' => 'filter', 'selected' => $this->status,'default' => 'open'));
				$default_value = array ('id'=>'','name'=>lang('open'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bocommon->get_user_list_right2('filter',2,$this->filter,$this->acl_location);
				array_unshift ($values_combo_box[3],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine tickets')));
				$default_value = array ('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[3],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uitenant_claim.index',
								'query'            		=> $this->query,
								'cat_id'				=> $this->cat_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1

								),
								array
								( //boton 	STATUS
									'id' => 'btn_district_id',
									'name' => 'district_id',
									'value'	=> lang('District'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	STATUS
									'id' => 'btn_status_id',
									'name' => 'status_id',
									'value'	=> lang('Status'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	USER
									'id' => 'sel_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[3],
									'onchange'=> 'onChangeSelect();',
									'tab_index' => 4
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 7
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 6
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 5
								),
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0])
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								),
								array
								( //div values  combo_box_3
									'id' => 'values_combo_box_3',
									'value'	=> $this->bocommon->select2String($values_combo_box[3])
								),

							)
						)
					)
				);

			}


			$claim_list = $this->bo->read(array('project_id' => $project_id));

			$uicols = array();
			$uicols['name'][0]['name'] = 'claim id';
			$uicols['name'][0]['value'] = 'claim_id';

			$uicols['name'][1]['name'] = 'district_id';
			$uicols['name'][1]['value'] = 'district_id';

			$uicols['name'][2]['name'] = 'location';
			$uicols['name'][2]['value'] = 'location_code';

			$uicols['name'][3]['name'] = 'loc1_name';
			$uicols['name'][3]['value'] = 'loc1_name';

			$uicols['name'][4]['name'] = 'address';
			$uicols['name'][4]['value'] = 'address';

			$uicols['name'][5]['name'] = 'category';
			$uicols['name'][5]['value'] = 'loc_category';

			$uicols['name'][6]['name'] = 'Project';
			$uicols['name'][6]['value'] = 'project_id';

			$uicols['name'][7]['name'] = 'name';
			$uicols['name'][7]['value'] = 'name';

			$uicols['name'][8]['name'] = 'entry date';
			$uicols['name'][8]['value'] = 'entry_date';

			$uicols['name'][9]['name'] = 'user';
			$uicols['name'][9]['value'] = 'user';

			$uicols['name'][10]['name'] = 'category';
			$uicols['name'][10]['value'] = 'claim_category';

			$uicols['name'][11]['name'] = 'Status';
			$uicols['name'][11]['value'] = 'status';

			$uicols['name'][12]['name'] = 'tenant_id';
			$uicols['name'][12]['value'] = 'tenant_id';

			$count_uicols_name = count($uicols['name']);

			$j = 0;
			if (isset($claim_list) AND is_array($claim_list))
			{
				foreach($claim_list as $claim_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k]['value'];
						$datatable['rows']['row'][$j]['column'][$k]['value']	= $claim_entry[$uicols['name'][$k]['value']];

					}
					$j++;
				}
			}

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'claim_id',
							'source'	=> 'claim_id'
						),
					)
				);

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'view',
						'statustext' 	=> lang('view the claim'),
						'text'			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitenant_claim.view'
						)
					),
					'parameters'	=> $parameters
				);
			}

			if ($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'edit',
						'statustext' 		=> lang('edit the claim'),
						'text'				=> lang('edit'),
						'action'			=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitenant_claim.edit'
						)
					),
					'parameters'	=> $parameters
				);
			}

			$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

			foreach ($jasper as $report)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uijasper.view',
							'jasper_id'			=> $report['id'],
							'target'		=> '_blank'
						)),
						'parameters'			=> $parameters
					);
			}

			if ($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'delete',
						'statustext' 		=> lang('delete the claim'),
						'text'				=> lang('delete'),
						'confirm_msg'		=> lang('do you really want to delete this entry'),
						'action'			=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitenant_claim.delete'
						)
					),
					'parameters'	=> $parameters
				);
			}
			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiproject.index',
						'lookup'		=>	1,
						'from'			=>  'tenant_claim'
					)));

			unset($parameters);

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i]['value'];
					$datatable['headers']['header'][$i]['text'] 			= lang($uicols['name'][$i]['name']);
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
				}

				if($uicols['name'][$i]['value']=='claim_id'
					|| $uicols['name'][$i]['value']=='project_id'
					|| $uicols['name'][$i]['value']=='name'
					|| $uicols['name'][$i]['value']=='district_id'
					|| $uicols['name'][$i]['value']=='entry_date'
				)
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i]['value'];
				}

				if($uicols['name'][$i]['value']=='category')
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']   = 'descr';
				}

				if($uicols['name'][$i]['value']=='tenant_id')
				{
					$datatable['headers']['header'][$i]['visible'] 		= false;
					$datatable['headers']['header'][$i]['format'] 		= 'hidden';
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($claim_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;


			$appname					= lang('Tenant claim');
			$function_msg				= lang('list claim');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'claim_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
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
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
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

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tenant_claim.index', 'property' );
			$this->save_sessiondata();
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
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$claim_id = phpgw::get_var('claim_id', 'int');

			$values	 = phpgw::get_var('values');
			//_debug_array($values);die;
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

			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim','files'));

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

				//----------files
					$bofiles	= CreateObject('property.bofiles');
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/tenant_claim/{$claim_id}/", $values);
					}

					$file_name = @str_replace(' ','_',$_FILES['file']['name']);

					if($file_name)
					{
						$to_file = "{$bofiles->fakebase}/tenant_claim/{$claim_id}/{$file_name}";

						if($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("tenant_claim/$claim_id");
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

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','tenant_claim_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitenant_claim.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitenant_claim.index'));
			}


			if ($claim_id)
			{
				$values = $this->bo->read_single($claim_id);
			}

			$project_values	= $this->boproject->read_single($values['project_id'], array(), true);

			$project_values['workorder_budget'] = $this->boproject->get_orders(array('project_id'=> $values['project_id'],'year'=> 0));
			//_debug_array($project_values);die();
			$soinvoice	= CreateObject('property.soinvoice');

			foreach ($project_values['workorder_budget'] as &$workorder)
			{
				$_vouchers = array();
				$vouchers = $soinvoice->read_invoice(array('paid'=>'1','workorder_id' => $workorder['workorder_id'], 'user_lid' => 'all'));
				foreach($vouchers as $entry)
				{
					$_vouchers[] = $entry['voucher_id'];
				}
				$vouchers = $soinvoice->read_invoice(array('workorder_id' => $workorder['workorder_id'], 'user_lid' => 'all'));
				unset($entry);
				foreach($vouchers as $entry)
				{
					$_vouchers[] = $entry['voucher_id'];
				}

				$workorder['voucher_id'] = implode(', ', $_vouchers);

				$workorder['selected'] = in_array($workorder['workorder_id'],$values['workorders']);
				$workorder['claim_issued'] = in_array($workorder['workorder_id'],$values['claim_issued']);

				}


			//_debug_array($project_values);die();

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
				'no_link'	=> false, // disable lookup links for location type less than type_id
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


			if($project_values['location_data']['tenant_id'] && !$values['tenant_id'])
			{
				$values['tenant_id']		= $project_values['location_data']['tenant_id'];
				$values['last_name']		= $project_values['location_data']['last_name'];
				$values['first_name']		= $project_values['location_data']['first_name'];
			}
			else if($values['tenant_id'])
			{
				$tenant= $this->bocommon->read_single_tenant($values['tenant_id']);
				$values['last_name']		= $tenant['last_name'];
				$values['first_name']		= $tenant['first_name'];
			}

			$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);
			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'		=> $values['b_account_id'],
				'b_account_name'	=> $values['b_account_name']));

			$link_data = array
				(
					'menuaction'	=> 'property.uitenant_claim.edit',
					'claim_id'		=> $claim_id,
					'project_id' 	=> $values['project_id']
				);

			$cats				= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$cats->supress_info	= true;

			$cat_list_project	= $cats->return_array('',0,false,'','','',false);
			$cat_list_project	= $this->bocommon->select_list($project_values['cat_id'],$cat_list_project);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			for($d=0;$d<count($project_values['workorder_budget']);$d++)
			{
				if($project_values['workorder_budget'][$d]['charge_tenant']==1)
				{
					$project_values['workorder_budget'][$d]['charge_tenant']='x';
				}

				if($project_values['workorder_budget'][$d]['selected']==1)
				{
					
					$project_values['workorder_budget'][$d]['budget_hidden'] = $project_values['workorder_budget'][$d]['budget'];
					$project_values['workorder_budget'][$d]['calculation_hidden'] = $project_values['workorder_budget'][$d]['calculation'];
					$project_values['workorder_budget'][$d]['actual_cost_hidden'] = $project_values['workorder_budget'][$d]['actual_cost'];
					$project_values['workorder_budget'][$d]['selected']='<input type="checkbox" name="values[workorder][]" checked value="'.$project_values['workorder_budget'][$d]['workorder_id'].'">';
				}
				else
				{
					$project_values['workorder_budget'][$d]['budget_hidden'] = 0;
					$project_values['workorder_budget'][$d]['calculation_hidden'] = 0;
					$project_values['workorder_budget'][$d]['actual_cost_hidden'] = 0;
					$project_values['workorder_budget'][$d]['selected']='<input type="checkbox" name="values[workorder][]" value="'.$project_values['workorder_budget'][$d]['workorder_id'].'">';
				}
				$project_values['workorder_budget'][$d]['selected'].= $project_values['workorder_budget'][$d]['claim_issued'] ? 'ok' : '';
			}

			//---datatable0 settings---------------------------------------------------

			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($project_values['workorder_budget']),
					'total_records'	=> count($project_values['workorder_budget']),
					'edit_action'	=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit'))),
					'is_paginator'	=> 1,
					'footer'		=> 0
				);

			//_debug_array($project_values['workorder_budget']);die();

			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array('key' => 'workorder_id',	'label'=>lang('Workorder'),	'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
															array('key' => 'budget',	'label'=>lang('Budget'),	'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
															array('key' => 'budget_hidden','hidden'=>true),
															array('key' => 'calculation',	'label'=>lang('Calculation'),	'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
															array('key' => 'calculation_hidden','hidden'=>true),
															array('key' => 'actual_cost','label'=>lang('actual cost'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
															array('key' => 'actual_cost_hidden','hidden'=>true),
															array('key' => 'vendor_name','label'=>lang('Vendor'),'sortable'=>true,'resizeable'=>true),
															array('key' => 'charge_tenant','label'=>lang('Charge tenant'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterCenter'),
															array('key' => 'status','label'=>'Status','sortable'=>true,'resizeable'=>true),
															array('key' => 'voucher_id','label'=>lang('voucher'),'sortable'=>true,'resizeable'=>true),
															array('key' => 'selected','label'=> lang('select'),	'sortable'=>false,'resizeable'=>false)))
				);


			if($claim_id)
			{
				$record_history = $this->bo->read_record_history($claim_id);
//_debug_array($content_budget);die();
			}
			else
			{
				$record_history = array();
			}


//--------------files
			$link_file_data = array
			(
				'menuaction'	=> 'property.uitenant_claim.view_file',
				'id'		=> $claim_id
			);

			$link_to_files =(isset($config->config_data['files_url'])?$config->config_data['files_url']:'');

			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			$_files = $this->bo->get_files($claim_id);

			$lang_view_file = lang('click to view file');
			$lang_delete_file = lang('Check to delete file');
			$z=0;
			$content_files = array();
			foreach( $_files as $_file )
			{
				if ($link_to_files)
				{
					$content_files[$z]['file_name'] = "<a href='{$link_to_files}/{$_file['directory']}/{$_file['file_name']}' target=\"_blank\" title='{$lang_view_file}'>{$_file['name']}</a>";
				}
				else
				{
					$content_files[$z]['file_name'] = "<a href=\"{$link_view_file}&amp;file_name={$_file['file_name']}\" target=\"_blank\" title=\"{$lang_view_file}\">{$_file['name']}</a>";
				}
				$content_files[$z]['delete_file'] = "<input type=\"checkbox\" name=\"values[file_action][]\" value=\"{$_file['name']}\" title=\"{$lang_delete_file}\">";
				$z++;
			}

			$datavalues[1] = array
			(
				'name'					=> "1",
				'values' 				=> json_encode($content_files),
				'total_records'			=> count($content_files),
				'edit_action'			=> "''",
				'is_paginator'			=> 1,
				'rows_per_page'			=> 5,//$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'footer'				=> 0
			);

			$myColumnDefs[1] = array
				(
					'name'		=> "1",
					'values'	=>	json_encode(array(	array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true)))
				);

//--------------files

			
			
			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(	array('key' => 'value_date','label' => lang('Date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_user','label' => lang('User'),'Action'=>true,'resizeable'=>true),
														array('key' => 'value_action','label' => lang('Action'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_old_value','label' => lang('old value'), 'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_new_value','label' => lang('New Value'),'sortable'=>true,'resizeable'=>true)))
				);



			$data = array
				(
					'table_header_workorder'			=> $table_header_workorder,
					'lang_no_workorders'				=> lang('No workorder budget'),
					'workorder_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.view')),
					'lang_start_date'					=> lang('Project start date'),
					'value_start_date'					=> $project_values['start_date'],
					'value_entry_date'					=> $values['entry_date'] ? $GLOBALS['phpgw']->common->show_date($values['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',

					'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'						=> json_encode(array(menuaction => "property.uitenant_claim.edit",claim_id=>$claim_id)),
					'datatable'							=> $datavalues,
					'myColumnDefs'						=> $myColumnDefs,
					//'myButtons'						=> $myButtons,

					'lang_end_date'						=> lang('Project end date'),
					'value_end_date'					=> $project_values['end_date'],

					'lang_charge_tenant'				=> lang('Charge tenant'),
					'charge_tenant'						=> $project_values['charge_tenant'],

					'lang_power_meter'					=> lang('Power meter'),
					'value_power_meter'					=> $project_values['power_meter'],

					'lang_budget'						=> lang('Budget'),
					'value_budget'						=> $project_values['budget'],

					'lang_reserve'						=> lang('reserve'),
					'value_reserve'						=> $project_values['reserve'],
					'lang_reserve_statustext'			=> lang('Enter the reserve'),

					'lang_reserve_remainder'			=> lang('reserve remainder'),
					'value_reserve_remainder'			=> $reserve_remainder,
					'value_reserve_remainder_percent'	=> $remainder_percent,

					'vendor_data'						=> $vendor_data,
					'location_data'						=> $location_data,
					'location_type'						=> 'view',

					'lang_project_id'					=> lang('Project ID'),
					'value_project_id'					=> $project_values['project_id'],
					'lang_name'							=> lang('Name'),
					'value_name'						=> $project_values['name'],

					'lang_descr'						=> lang('Description'),

					'sum_workorder_budget'				=> $project_values['sum_workorder_budget'],
					'sum_workorder_calculation'			=> $project_values['sum_workorder_calculation'],
					'workorder_budget'					=> $project_values['workorder_budget'],
					'sum_workorder_actual_cost'			=> $project_values['sum_workorder_actual_cost'],
					'lang_actual_cost'					=> lang('Actual cost'),
					'lang_coordinator'					=> lang('Coordinator'),
					'lang_sum'							=> lang('Sum'),
					'select_user_name'					=> 'project_values[coordinator]',
					'lang_no_user'						=> lang('Select coordinator'),
					'user_list'							=> $this->bocommon->get_user_list('select',$project_values['coordinator'],$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

					'currency'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

					'lang_contact_phone'				=> lang('Contact phone'),
					'contact_phone'						=> $project_values['contact_phone'],

					'b_account_data'					=> $b_account_data,

					'lang_select_workorder_statustext'	=> lang('Include the workorder to this claim'),

					'cat_list_project'					=> $cat_list_project,
					//------------------
					'lang_status'						=> lang('Status'),
					'lang_status_statustext'			=> lang('Select status'),
					'status_list'						=> $this->bo->get_status_list(array('format' => 'select', 'selected' => $values['status'],'default' => 'open')),
					'lang_no_status'					=> lang('No status'),
					'status_name'						=> 'values[status]',

					'lang_amount'						=> lang('amount'),
					'lang_amount_statustext'			=> lang('The total amount to claim'),
					'value_amount'						=> $values['amount'],

					'tenant_link'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.tenant')),
					'lang_tenant'						=> lang('tenant'),
					'value_tenant_id'					=> $values['tenant_id'],
					'value_last_name'					=> $values['last_name'],
					'value_first_name'					=> $values['first_name'],
					'lang_tenant_statustext'			=> lang('Select a tenant'),
					'size_last_name'					=> strlen($values['last_name']),
					'size_first_name'					=> strlen($values['first_name']),

					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_claim_id'						=> lang('ID'),
					'value_claim_id'					=> $claim_id,
					'lang_remark'						=> lang('remark'),
					'lang_category'						=> lang('category'),
					'lang_save'							=> lang('save'),
					'lang_cancel'						=> lang('cancel'),
					'lang_apply'						=> lang('apply'),
					'value_remark'						=> $values['remark'],
					'value_cat'							=> $values['cat'],
					'lang_remark_statustext'			=> lang('Enter a remark for this claim'),
					'lang_apply_statustext'				=> lang('Apply the values'),
					'lang_cancel_statustext'			=> lang('Leave the claim untouched and return back to the list'),
					'lang_save_statustext'				=> lang('Save the claim and return back to the list'),
					'lang_no_cat'						=> lang('no category'),
					'lang_cat_statustext'				=> lang('Select the category the claim belongs to. To do not use a category select NO CATEGORY'),
					'select_name'						=> 'values[cat_id]',
					'cat_list'							=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'tenant_claim','order'=>'descr')),
				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . ': ' . ($claim_id?lang('edit claim'):lang('add claim'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

			//_debug_array($data);die;

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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'uitenant.edit', 'property' );
			//-----------------------datatable settings---
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{

			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$claim_id	= phpgw::get_var('claim_id', 'int');
			$delete		= phpgw::get_var('delete', 'bool', 'POST');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($claim_id);
				return "claim_id ".$claim_id." ".lang("has been deleted");
			}

			$link_data = array
				(
					'menuaction' => 'property.uitenant_claim.index'
				);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.delete', 'claim_id'=> $claim_id)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname	= lang('Tenant claim');
			$function_msg	= lang('delete claim');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
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
				'no_link'	=> false, // disable lookup links for location type less than type_id
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


			if($project_values['location_data']['tenant_id'] && !$values['tenant_id'])
			{
				$values['tenant_id']		= $project_values['location_data']['tenant_id'];
				$values['last_name']		= $project_values['location_data']['last_name'];
				$values['first_name']		= $project_values['location_data']['first_name'];
			}
			else if($values['tenant_id'])
			{
				$tenant= $this->bocommon->read_single_tenant($values['tenant_id']);
				$values['last_name']		= $tenant['last_name'];
				$values['first_name']		= $tenant['first_name'];
			}


			if($values['workorder'] && $project_values['workorder_budget'])
			{
				foreach ($values['workorder'] as $workorder_id)
				{
					for ($i=0;$i<count($project_values['workorder_budget']);$i++)
					{
						if($project_values['workorder_budget'][$i]['workorder_id'] == $workorder_id)
						{
							$project_values['workorder_budget'][$i]['selected'] = true;
						}
					}
				}
			}

/*
			for ($i=0;$i<count($project_values['workorder_budget']);$i++)
			{
				$claimed= $this->bo->check_claim_workorder($project_values['workorder_budget'][$i]['workorder_id']);

				if($claimed)
				{
					$project_values['workorder_budget'][$i]['claimed'] = $claimed;
				}
			}

 */

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'		=> $values['b_account_id'],
				'b_account_name'	=> $values['b_account_name'],
				'type'	=> 'view'));


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$cats				= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$cats->supress_info	= true;

			$cat_list_project	= $cats->return_array('',0,false,'','','',false);
			$cat_list_project	= $this->bocommon->select_list($project_values['cat_id'],$cat_list_project);

			$data = array
				(
					'table_header_workorder'			=> $table_header_workorder,
					'lang_no_workorders'				=> lang('No workorder budget'),
					'workorder_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.view')),
					'lang_start_date'					=> lang('Project start date'),
					'value_start_date'					=> $project_values['start_date'],

					'lang_end_date'						=> lang('Project end date'),
					'value_end_date'					=> $project_values['end_date'],

					'lang_charge_tenant'				=> lang('Charge tenant'),
					'charge_tenant'						=> $project_values['charge_tenant'],

					'lang_power_meter'					=> lang('Power meter'),
					'value_power_meter'					=> $project_values['power_meter'],

					'lang_budget'						=> lang('Budget'),
					'value_budget'						=> $project_values['budget'],

					'lang_reserve'						=> lang('reserve'),
					'value_reserve'						=> $project_values['reserve'],
					'lang_reserve_statustext'			=> lang('Enter the reserve'),

					'lang_reserve_remainder'			=> lang('reserve remainder'),
					'value_reserve_remainder'			=> $reserve_remainder,
					'value_reserve_remainder_percent'	=> $remainder_percent,

					'location_data'						=> $location_data,
					'location_type'						=> 'view',

					'lang_project_id'					=> lang('Project ID'),
					'value_project_id'					=> $project_values['project_id'],
					'lang_name'							=> lang('Name'),
					'value_name'						=> $project_values['name'],

					'lang_descr'						=> lang('Description'),

					'sum_workorder_budget'				=> $project_values['sum_workorder_budget'],
					'sum_workorder_calculation'			=> $project_values['sum_workorder_calculation'],
					'workorder_budget'					=> $project_values['workorder_budget'],
					'sum_workorder_actual_cost'			=> $project_values['sum_workorder_actual_cost'],
					'lang_actual_cost'					=> lang('Actual cost'),
					'lang_coordinator'					=> lang('Coordinator'),
					'lang_sum'							=> lang('Sum'),
					'select_user_name'					=> 'project_values[coordinator]',
					'lang_no_user'						=> lang('Select coordinator'),
					'user_list'							=> $this->bocommon->get_user_list('select',$project_values['coordinator'],$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

					'lang_no_status'					=> lang('Select status'),

					'currency'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

					'lang_contact_phone'				=> lang('Contact phone'),
					'contact_phone'						=> $project_values['contact_phone'],

					'b_account_data'					=> $b_account_data,

					'cat_list_project'					=> $cat_list_project,

					//------------------

					'lang_status'						=> lang('Status'),
					'status_list'						=> $this->bo->get_status_list(array('format' => 'select', 'selected' => $values['status'],'default' => 'open')),

					'lang_amount'						=> lang('amount'),
					'value_amount'						=> $values['amount'],

					'lang_tenant'						=> lang('tenant'),
					'value_tenant_id'					=> $values['tenant_id'],
					'value_last_name'					=> $values['last_name'],
					'value_first_name'					=> $values['first_name'],
					'size_last_name'					=> strlen($values['last_name']),
					'size_first_name'					=> strlen($values['first_name']),

					'lang_claim_id'						=> lang('ID'),
					'value_claim_id'					=> $claim_id,
					'lang_remark'						=> lang('remark'),
					'lang_category'						=> lang('category'),
					'lang_save'							=> lang('save'),
					'lang_cancel'						=> lang('cancel'),
					'lang_apply'						=> lang('apply'),
					'value_remark'						=> $values['remark'],
					'value_cat'							=> $values['cat'],
					'cat_list'							=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'tenant_claim','order'=>'descr')),

					'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.index')),
					'lang_done'							=> lang('done'),
					'value_entry_date'					=> $values['entry_date'] ? $GLOBALS['phpgw']->common->show_date($values['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . '::' . lang('view claim');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}
