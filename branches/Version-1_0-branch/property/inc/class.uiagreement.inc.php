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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');
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
				'index'  		=> true,
				'view'   		=> true,
				'edit'   		=> true,
				'delete' 		=> true,
				'columns'		=> true,
				'edit_item'		=> true,
				'view_item'		=> true,
				'view_file'		=> true,
				'download'		=> true,
				'add_activity'	=> true
			);

		function property_uiagreement()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.boagreement',true);
			$this->bocommon		= CreateObject('property.bocommon');

			$this->role			= $this->bo->role;

			$this->cats			= CreateObject('phpgwapi.categories', -1, 'property', '.vendor');

			$this->acl			= & $GLOBALS['phpgw']->acl;
			$this->acl_location	= '.agreement';

			$this->acl_read 	= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	= $this->acl->check($this->acl_location, 16, 'property');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->vendor_id	= $this->bo->vendor_id;
			$this->allrows		= $this->bo->allrows;
			$this->member_id	= $this->bo->member_id;
			$this->status_id	= $this->bo->status_id;
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
					'member_id'	=> $this->member_id,
					'status_id'	=> $this->status_id
				);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{
			phpgwapi_yui::load_widget('tabview');
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$values		= phpgw::get_var('values');
			$receipt	= array();

			if ($values['save'])
			{

				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('property','agreement_columns',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uiagreement.columns',
					'role'			=> $this->role
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'	=> $this->bo->column_list($values['columns'],$allrows=true),
					'function_msg'	=> $function_msg,
					'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_columns'	=> lang('columns'),
					'lang_none'		=> lang('None'),
					'lang_save'		=> lang('save'),
					'select_name'	=> 'period'
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('agreement');
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt','');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::pricebook::agreement';

			$datatable = array();
			$this->save_sessiondata();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
				(
					'menuaction'	=> 'property.uiagreement.index',
					'sort'			=>$this->sort,
					'order'			=>$this->order,
					'cat_id'		=>$this->cat_id,
					'filter'		=>$this->filter,
					'query'			=>$this->query,
					'role'			=> $this->role,
					'member_id'		=> $this->member_id,
					'status_id'		=> $this->status_id
				));

				$datatable['config']['base_java_url'] = "menuaction:'property.uiagreement.index',"
					."status_id:'{$this->status_id}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
					(
						'menuaction'	=> 'property.uiagreement.index',
						'sort'		=>$this->sort,
						'order'		=>$this->order,
						'cat_id'	=>$this->cat_id,
						'filter'	=>$this->filter,
						'query'		=>$this->query,
						'role'		=> $this->role,
						'member_id'	=> $this->member_id,
						'status_id'		=> $this->status_id
					);

				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,'link_data' =>$link_data));
				$default_value = array ('cat_id'=>'','name'=> lang('no member'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'branch','order'=>'descr'));
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->select_vendor_list('filter',$this->vendor_id);
				$default_value = array ('id'=>'','name'=>lang('no vendor'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bo->select_status_list('filter',$this->status_id);
				$default_value = array ('id'=>'','name'=>lang('no status'));
				array_unshift ($values_combo_box[3],$default_value);

				//_debug_array($values_combo_box[0]);die;

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
						array(
							'menuaction' 	=> 'property.uiagreement.index',
							'sort'			=> $this->sort,
							'order'			=> $this->order,
							'cat_id'		=> $this->cat_id,
							'filter'		=> $this->filter,
							'query'			=> $this->query,
							'role'			=> $this->role,
							'member_id'		=> $this->member_id,
							'status_id'		=> $this->status_id
						)
					),
					'fields'	=> array(
						'field' => array(
							array( //boton 	member
								'id' => 'btn_member_id',
								'name' => 'member_id',
								'value'	=> lang('Member'),
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => 1
							),
							array( //boton 	CATEGORY
								'id' => 'btn_cat_id',
								'name' => 'category_id',
								'value'	=> lang('Category'),
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => 2
							),
							array( //boton 	vendor
								'id' => 'btn_vendor_id',
								'name' => 'vendor_id',
								'value'	=> lang('Vendor'),
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => 3
							),
							array( //boton 	STATUS
								'id' => 'btn_status_id',
								'name' => 'status_id',
								'value'	=> lang('status'),
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => 4
							),
							array(
								'type'	=> 'button',
								'id'	=> 'btn_new',
								'value'	=> lang('add'),
								'tab_index' => 7
							),
							array( //boton     SEARCH
								'id' => 'btn_search',
								'name' => 'search',
								'value'    => lang('search'),
								'type' => 'button',
								'tab_index' => 6
							),
							array( // TEXT INPUT
								'name'     => 'query',
								'id'     => 'txt_query',
								'value'    => '',//$query,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 28,
								'tab_index' => 5
							),
							array(
								'type' => 'link',
								'id' => 'btn_columns',
								'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction' => 'property.uiagreement.columns',
									'role'		=> $this->role
								))."','','width=300,height=600,scrollbars=1')",
								'value' => lang('columns'),
								'tab_index' => 8
							),
							array
							(
								'type'	=> 'button',
								'id'	=> 'btn_export',
								'value'	=> lang('download'),
								'tab_index' => 10
							)
						),
						'hidden_value' => array(
							array( //div values  combo_box_0
								'id' => 'values_combo_box_0',
								'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id')
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

			$list = $this->bo->read();

			$uicols	= $this->bo->uicols;
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($list) AND is_array($list))
			{
				foreach($list as $list_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']				= $list_entry[$uicols['name'][$k]];
						}
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
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'agreement_id',
							'source'	=> 'id'
						),
					)
				);

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name' 			=> 'view',
					'statustext' 	=> lang('view this entity'),
					'text'			=> lang('view'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiagreement.view',
						'role'			=> $this->role
					)),
					'parameters'	=> $parameters
				);
				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'edit',
						'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uijasper.view',
							'jasper_id'		=> $report['id'],
							'target'		=> '_blank'
						)),
						'parameters'			=> $parameters
					);
				}
			}

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'edit',
					'statustext' 	=> lang('edit this entity'),
					'text'			=> lang('edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiagreement.edit',
						'role'			=> $this->role
					)),
					'parameters'	=> $parameters
				);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'delete',
					'statustext' 	=> lang('delete this entity'),
					'text'			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiagreement.delete',
						'role'			=> $this->role
					)),
					'parameters'	=> $parameters2
				);
			}

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'add',
					'statustext' 	=> lang('add an entity'),
					'text'			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiagreement.edit',
						'role'			=> $this->role
					))
				);
			}

			unset($parameters);
			unset($parameters2);

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='id' || $uicols['name'][$i]=='name' || $uicols['name'][$i]=='org_name' || $uicols['name'][$i]=='category' || $uicols['name'][$i]=='start_date' || $uicols['name'][$i]=='end_date' || $uicols['name'][$i]=='status'|| $uicols['name'][$i]=='termination_date')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
/*
					if($uicols['name'][$i]=='category')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'org_name';
					}
*/
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname					= lang('agreement');
			$function_msg		= lang('List') . ' ' . lang($this->role);

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'agreement.index', 'property' );
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
							$content[$j]['row'][$i]['value'] 		= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 		= $uicols['name'][$i];
						}
					}

					if($this->acl_read && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']		= lang('view the entity');
						$content[$j]['row'][$i]['text']				= lang('view');
						$content[$j]['row'][$i++]['link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.view_item', 'agreement_id'=> $entry['agreement_id'], 'id'=> $entry['id']));
					}
					if($this->acl_edit && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']		= lang('edit the agreement');
						$content[$j]['row'][$i]['text']				= lang('edit');
						$content[$j]['row'][$i++]['link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit_item', 'agreement_id'=> $entry['agreement_id'], 'id'=> $entry['id']));
					}
					if($this->acl_delete && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']		= lang('delete this item');
						$content[$j]['row'][$i]['text']				= lang('delete');
						$content[$j]['row'][$i++]['link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit','delete_item'=>1, 'agreement_id'=> $entry['agreement_id'], 'activity_id'=> $entry['id']));
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
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}


			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$group_id		= phpgw::get_var('group_id', 'int');
			$values			= phpgw::get_var('values');

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
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiagreement.edit', 'id'=> $agreement_id, 'tab' => 'items'));
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiagreement.edit', 'id'=> $agreement_id, 'tab' => 'items'));

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

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
				(
					'lang_id'					=> lang('ID'),
					'value_agreement_id'		=> $agreement_id,
					'lang_name'					=> lang('name'),
					'value_name'				=> $agreement['name'],
					'lang_descr'				=> lang('descr'),
					'value_descr'				=> $agreement['descr'],
					'lang_select_all'			=> lang('Select All'),
					'img_check'					=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
					'add_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.add_activity', 'group_id'=> $group_id, 'agreement_id'=> $agreement_id)),
					'agreement_id'				=> $agreement_id,
					'table_header'				=> $table_header,
					'values'					=> $content,
					'lang_save'					=> lang('save'),
					'lang_cancel'				=> lang('cancel'),
					'lang_apply'				=> lang('apply'),
					'lang_apply_statustext'		=> lang('Apply the values'),
					'lang_cancel_statustext'	=> lang('Leave the agreement untouched and return back to the list'),
					'lang_save_statustext'		=> lang('Save the agreement and return back to the list'),
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('add activity');
			//_debug_array($data);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add_activity' => $data));
		}

		function edit()
		{

			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}
			$id				= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');
			//return _debug_array($values);
			$delete_item	= phpgw::get_var('delete_item', 'bool');
			$activity_id	= phpgw::get_var('activity_id', 'int');
			$active_tab		= phpgw::get_var('tab', 'string', 'REQUEST', 'general');

			$config			= CreateObject('phpgwapi.config','property');
			$boalarm		= CreateObject('property.boalarm');
			$receipt 		= array();
			$get_items 		= false;

			if($delete_item && $id && $activity_id)
			{
				$this->bo->delete_item($id,$activity_id);
				$get_items = true;
			}
			$values_attribute  = phpgw::get_var('values_attribute');
			$insert_record_agreement = $GLOBALS['phpgw']->session->appsession('insert_record_values.agreement','property');

			if(isset($insert_record_agreement) && is_array($insert_record_agreement))
			{
				for ($j=0;$j<count($insert_record_agreement);$j++)
				{
					$insert_record['extra'][$insert_record_agreement[$j]]	= $insert_record_agreement[$j];
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement', 'nextmatchs', 'attributes_form', 'files'));
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
							$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
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

					$bofiles	= CreateObject('property.bofiles');
					if(isset($id) && $id && isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/agreement/{$id}/", $values);
					}

					$values['file_name']=str_replace (' ','_',$_FILES['file']['name']);
					$to_file = "{$bofiles->fakebase}/agreement/{$values['agreement_id']}/{$values['file_name']}";

					if(!$values['document_name_orig'] && $bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
					{
						$receipt['error'][]=array('msg'=>lang('This file already exists !'));
					}


					if(!$receipt['error'])
					{
//						$values['agreement_id']	= $id;

						$receipt = $this->bo->save($values,$values_attribute,$action);
						$id = $receipt['agreement_id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if($values['file_name'])
						{
							$bofiles->create_document_dir("agreement/{$id}");
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


						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','agreement_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiagreement.index', 'role'=> $this->role));
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
						$get_items = true;
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
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiagreement.index', 'role'=> $this->role));
				}
			}


			$agreement = $this->bo->read_single(array('agreement_id'=>$id));

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$agreement = $this->bocommon->preserve_attribute_values($agreement,$values_attribute);
			}

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_termination_date');

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
							$content[$j]['text_view']				= lang('view');
							$content[$j]['link_view']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.view_item', 'agreement_id'=> $id, 'id'=> $content[$j]['activity_id']));
						}
						if($this->acl_edit && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
						{
							$content[$j]['lang_edit_statustext']	= lang('edit the agreement');
							$content[$j]['text_edit']				= lang('edit');
							$content[$j]['link_edit']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit_item', 'agreement_id'=> $id, 'id'=> $content[$j]['activity_id']));
						}
						if($this->acl_delete && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
						{
							$content[$j]['lang_delete_statustext']	= lang('delete this item');
							$content[$j]['text_delete']				= lang('delete');
							$content[$j]['link_delete']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit', 'delete_item'=>1, 'id'=> $id, 'activity_id'=> $content[$j]['activity_id']));
						}

						$content[$j]['acl_manage']					= $this->acl_manage;
						$content[$j]['acl_read']					= $this->acl_read;
						$content[$j]['acl_edit']					= $this->acl_edit;
						$content[$j]['acl_delete']					= $this->acl_delete;
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
					$set_column[]=true;
				}
				if($this->acl_edit && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('edit');
					$i++;
					$set_column[]=true;
				}
				if($this->acl_delete && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('delete');
					$i++;
					$set_column[]=true;
				}
				if($this->acl_manage && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('Update');
					$i++;
					$set_column[]=true;
				}

				//				$table_header=$list['table_header'];
				for ($i=0; $i<9; $i++)
				{
					$set_column[]=true;
				}

				if (isset($content) && is_array($content))
				{
					$GLOBALS['phpgw']->jqcal->add_listener('values_date');
					$table_update[] = array
						(
							'lang_new_index'				=> lang('New index'),
							'lang_new_index_statustext'		=> lang('Enter a new index'),
							'lang_date_statustext'			=> lang('Select the date for the update'),
							'lang_update'					=> lang('Update'),
							'lang_update_statustext'		=> lang('update selected investments')
						);
				}
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiagreement.edit',
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
				$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true, 'link_data' => array()));
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

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$table_add[] = array
				(
					'lang_add'				=> lang('add detail'),
					'lang_add_standardtext'	=> lang('add an item to the details'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.add_activity', 'agreement_id'=> $id, 'group_id'=> $agreement['group_id']))
				);


			$link_file_data = array
				(
					'menuaction'	=> 'property.uiagreement.view_file',
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

			$link_download = array
				(
					'menuaction'	=> 'property.uiagreement.download',
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

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			if (isset($agreement['attributes']) && is_array($agreement['attributes']))
			{

		/*		foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
						(
							'menuaction'	=> 'property.uis_agreement.attrib_history',
							'attrib_id'	=> $attribute['id'],
							'id'		=> $id,
							'edit'		=> true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}
		 */

				phpgwapi_yui::tabview_setup('edit_tabview');
				$tabs['general']	= array('label' => lang('general'), 'link' => '#general');

				$location = $this->acl_location;
				$attributes_groups = $this->bo->get_attribute_groups($location, $agreement['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($agreement['attributes']);

				$tabs['items']	= array('label' => lang('items'), 'link' => '#items');
			}

			//------JSON code-------------------

			//---GET ITEMS
			if( phpgw::get_var('phpgw_return_as') == 'json' &&  $get_items )
			{
				if(count($content))
				{
					return json_encode($content);
				}
				else
				{
					return "";
				}
			}

			//-- ALARMS ---
			else if( phpgw::get_var('phpgw_return_as') == 'json' && !$get_items )
			{
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
				//$alarm_data['values'] = array();
				if(count($alarm_data['values']))
				{
					return json_encode($alarm_data['values']);
				}
				else
				{
					return "";
				}
			}

			//---datatable0 settings---------------------------------------------------


			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($alarm_data['values']),
					'total_records'	=> count($alarm_data['values']),
					'permission'   	=> "''",
					'is_paginator'	=> 0,
					'footer'		=> 0
				);
			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array(key => time,	label=>$alarm_data['header'][0]['lang_time'],	sortable=>true,resizeable=>true,width=>140),
					array(key => text,	label=>$alarm_data['header'][0]['lang_text'],	sortable=>true,resizeable=>true,width=>340),
					array(key => user,	label=>$alarm_data['header'][0]['lang_user'],	sortable=>true,resizeable=>true,width=>200),
					array(key => enabled,label=>$alarm_data['header'][0]['lang_enabled'],sortable=>true,resizeable=>true,formatter=>FormatterCenter,width=>60),
					array(key => alarm_id,label=>"dummy",sortable=>true,resizeable=>true,hidden=>true),
					array(key => select,label=>$alarm_data['header'][0]['lang_select'],	sortable=>false,resizeable=>false,formatter=>myFormatterCheck,width=>60)))
				);

			$myButtons[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array(id =>'values[enable_alarm]',type=>buttons,	value=>Enable,	label=>$alarm_data[alter_alarm][0][lang_enable],	funct=> onActionsClick , classname=> actionButton, value_hidden=>""),
					array(id =>'values[disable_alarm]',type=>buttons,	value=>Disable,	label=>$alarm_data[alter_alarm][0][lang_disable],	funct=> onActionsClick , classname=> actionButton, value_hidden=>""),
					array(id =>'values[delete_alarm]',type=>buttons,	value=>Delete,	label=>$alarm_data[alter_alarm][0][lang_delete],	funct=> onActionsClick , classname=> actionButton, value_hidden=>""),
				))
			);
			$myButtons[1] = array
				(
					'name'			=> "1",
					'values'		=>	json_encode(array(	array(id =>'values[time][days]',	type=>menu,		value=>$this->bocommon->make_menu_date($alarm_data['add_alarm']['day_list'],"1_0",'values[time][days]' ),	label=>"0", classname=> actionsFilter, value_hidden=>"0"),
					array(id =>'values[time][hours]',	type=>menu,		value=>$this->bocommon->make_menu_date($alarm_data['add_alarm']['hour_list'],"1_1",'values[time][hours]'),	label=>"0", classname=> actionsFilter, value_hidden=>"0"),
					array(id =>'values[time][mins]',	type=>menu,		value=>$this->bocommon->make_menu_date($alarm_data['add_alarm']['minute_list'],"1_2",'values[time][mins]'), label=>"0", classname=> actionsFilter, value_hidden=>"0"),
					array(id =>'values[user_id]',		type=>menu,		value=>$this->bocommon->make_menu_user($alarm_data['add_alarm']['user_list'],"1_3",'values[user_id]'),	label=>$this->bocommon->choose_select($alarm_data['add_alarm']['user_list'],"name"),classname=> actionsFilter, value_hidden=>$this->bocommon->choose_select($alarm_data['add_alarm']['user_list'],"id")),

					array(id =>'values[add_alarm]',		type=>buttons,	value=>Add,		label=>$alarm_data[add_alarm][lang_add],			funct=> onAddClick , classname=> actionButton, value_hidden=>"")
				))
			);
			//_debug_array($alarm_data['add_alarm']['user_list']);die;

			//---datatable1 settings---------------------------------------------------
			$parameters['view'] = array('parameter' => array(
				array('name'  => 'agreement_id','source' => 'agreement_id'),
				array('name'  => 'id',			'source' => 'id')));

			$parameters['edit'] = array('parameter' => array(
				array('name'  => 'agreement_id','source' => 'agreement_id'),
				array('name'  => 'id',			'source' => 'id')));

			$parameters['delete'] = array('parameter' => array(
				array('name'  => 'delete_item',	'source' => 1,	'ready'  => 1),
				array('name'  => 'id',			'source' => 'agreement_id'),
				array('name'  => 'activity_id',	'source' => 'activity_id')));

			$permission_update = false;
			if($this->acl_read && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permissions['rowactions'][] = array(
					'text'    => lang('view'),
					'action'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiagreement.view_item')),
					'parameters' => $parameters['view']
				);
			}
			if($this->acl_edit && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permissions['rowactions'][] = array(
					'text'    => lang('edit'),
					'action'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiagreement.edit_item')),
					'parameters' => $parameters['edit']
				);
			}
			if($this->acl_delete && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permissions['rowactions'][] = array(
					'text'    	=> lang('delete'),
					'action'  	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiagreement.edit' )),
					'confirm_msg'=> lang('do you really want to delete this entry'),
					'parameters'=> $parameters['delete']
				);
			}
			if($this->acl_manage && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permission_update = true;
			}

			$datavalues[1] = array
				(
					'name'			=> "1",
					'values' 		=> json_encode($content),
					'total_records'	=> count($content),
					'permission'   	=> json_encode($permissions['rowactions']),
					'is_paginator'	=> 0,
					'footer'		=> 1
				);



			$myColumnDefs[1] = array
				(
					'name'			=> "1",
					'values'		=>	json_encode(array(	array(key => id,			label=>$table_header[0]['header'],	sortable=>true,resizeable=>true),
					array(key => num,			label=>$table_header[1]['header'],	sortable=>true,resizeable=>true),
					array(key => descr,			label=>$table_header[2]['header'],	sortable=>true,resizeable=>true),
					array(key => unit_name,			label=>$table_header[3]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterCenter),
					array(key => m_cost,		label=>$table_header[4]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterRight),
					array(key => w_cost,		label=>$table_header[5]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterRight),
					array(key => total_cost,	label=>$table_header[6]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterRight),
					array(key => this_index,	label=>$table_header[7]['header'],	sortable=>true,resizeable=>true),
					array(key => index_count,	label=>$table_header[8]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterCenter),
					array(key => index_date,	label=>$table_header[9]['header'],	sortable=>true,resizeable=>true),
					$permission_update?array(key => select,		label=>$table_header[13]['header'],	sortable=>false,resizeable=>false,formatter=>FormatterCheckItems):"",
					array(key => activity_id,	hidden=>true),
					array(key => agreement_id,	hidden=>true)
				)));


			$myButtons[2] = array
				(
					'name'			=> "2",
					'values'		=>	json_encode(array(	array(type=>text, label=>' New index:', classname=> 'index-opt'),
					array(id =>'values[new_index]', type=>inputText, size=>12, classname=> 'mybottonsUpdates'),
					array(id =>'values[update]',	type=>buttons,		value=>Update,	label=>lang('update'),	funct=> onUpdateClick , classname=> '')
				)));


			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			for($z=0; $z<count($agreement['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$agreement['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$agreement['files'][$z]['name'].'</a>';
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$agreement['files'][$z]['name'].'" title="'.lang('Check to delete file').'" style="cursor:help">';
			}

			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(	array(key => file_name,label=>lang('Filename'),sortable=>false,resizeable=>true),
					array(key => delete_file,label=>lang('Delete file'),sortable=>false,resizeable=>true,formatter=>FormatterCenter)))
				);


			//----------------------------------------------datatable settings--------

			$data = array
				(
					'property_js'							=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'							=> json_encode(array(menuaction => "property.uiagreement.edit",id=>$id)),
					'datatable'								=> $datavalues,
					'myColumnDefs'							=> $myColumnDefs,
					'myButtons'								=> $myButtons,

					'allow_allrows'							=> true,
					'allrows'								=> $this->allrows,
					'start_record'							=> $this->start,
					'record_limit'							=> $record_limit,
					'num_records'							=> count($list),
					'all_records'							=> $this->bo->total_records,
					'link_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

					'alarm_data'							=> $alarm_data,
					'lang_alarm'							=> lang('Alarm'),
					'lang_download'							=> 'download',
					'link_download'							=> $GLOBALS['phpgw']->link('/index.php',$link_download),
					'lang_download_help'						=> lang('Download table to your browser'),

					'fileupload'							=> true,
					'link_view_file'						=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),

					'files'									=> isset($agreement['files'])?$agreement['files']:'',
					'lang_files'							=> lang('files'),
					'lang_filename'							=> lang('Filename'),
					'lang_file_action'						=> lang('Delete file'),
					'lang_view_file_statustext'				=> lang('click to view file'),
					'lang_file_action_statustext'			=> lang('Check to delete file'),
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

					'attributes_group'						=> $attributes,
					'lookup_functions'						=> isset($agreement['lookup_functions'])?$agreement['lookup_functions']:'',
					'dateformat'							=> $dateformat,

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
					'update_action'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit', 'id'=> $id)),
					'lang_select_all'						=> lang('Select All'),
					'img_check'								=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
					'set_column'							=> $set_column,

					'lang_agreement_group'					=> lang('Agreement group'),
					'lang_no_agreement_group'				=> lang('Select agreement group'),
					'agreement_group_list'					=> $this->bo->get_agreement_group_list($agreement['group_id']),

					'lang_status'							=> lang('Status'),
					'status_list'							=> $this->bo->select_status_list('select',$agreement['status']),
					'status_name'							=> 'values[status]',
					'lang_no_status'						=> lang('Select status'),
					'textareacols'							=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
					'textarearows'							=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
					'tabs'									=> phpgwapi_yui::tabview_generate($tabs, $active_tab)
				);
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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'agreement.edit', 'property' );
			//-----------------------datatable settings---

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . ($id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function download()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$this->bo->allrows	= true;
			if($id)
			{
				$list = $this->bo->read_details($id);
			}
			else
			{
				$list = $this->bo->read($id);
			}
			$uicols		= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function edit_item()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
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

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) &&$values['apply']))
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
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiagreement.edit', 'id'=> $agreement_id, 'tab' => 'items'));
						}
					}
				}
				else if($values['update'])
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
				else if (!$values['save'] && !$values['apply'] && !$values['update'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiagreement.edit', 'id'=> $agreement_id, 'tab' => 'items'));
				}
			}

			$agreement = $this->bo->read_single(array('agreement_id'=>$agreement_id));
			$values = $this->bo->read_single_item(array('agreement_id'=>$agreement_id,'id'=>$id));

			$link_data = array
				(
					'menuaction'	=> 'property.uiagreement.edit_item',
					'agreement_id'	=> $agreement_id,
					'id'		=> $id,
					'role'		=> $this->role
				);


			$GLOBALS['phpgw']->jqcal->add_listener('values_date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,link_data => array()));

			$table_add[] = array
				(
					'lang_add'				=> lang('add detail'),
					'lang_add_standardtext'	=> lang('add an item to the details'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit_item', 'agreement_id'=> $agreement_id))
				);

			if($id)
			{
				$list = $this->bo->read_prizing(array('agreement_id'=>$agreement_id,'activity_id'=>$id));
				$activity_descr =$this->bo->get_activity_descr($id);
			}

			$uicols		= $this->bo->uicols;
			$list		= $this->list_content($list,$uicols,$edit_item=true);
			$content	= $list['content'];
			$table_header=$list['table_header'];

			for ($i=0; $i<count($list['content'][0]['row']); $i++)
			{
				$set_column[]=true;
			}

			$table_update[] = array
				(
					'lang_new_index'			=> lang('New index'),
					'lang_new_index_statustext'	=> lang('Enter a new index'),
					'lang_date_statustext'		=> lang('Select the date for the update'),
					'lang_update'				=> lang('Update'),
					'lang_update_statustext'	=> lang('update selected investments')
				);

			if( phpgw::get_var('phpgw_return_as') == 'json')
			{

				$content_values = array();

				$hidden = '';
				for($y=0;$y<count($content);$y++)
				{
					for($z=0;$z<=count($content[$y]['row']);$z++)
					{
						if($content[$y]['row'][$z]['name']!='')
						{
							$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
						}
					}
				}

				$hidden .= " <input name='values[select][0]' type='hidden' value='".$content_values[$y - 1]['activity_id']."'/>";
				$hidden .= " <input name='values[total_cost][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['total_cost']."'/>";
				$hidden .= " <input name='values[w_cost][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['w_cost']."'/>";
				$hidden .= " <input name='values[m_cost][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['m_cost']."'/>";
				$hidden .= " <input name='values[id][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['index_count']."'/>";

				$content_values[$y - 1]['index_date'] .= $hidden;

				if(count($content_values))
				{
					return json_encode($content_values);
				}
				else
				{
					return "";
				}
			}

			$hidden = '';
			for($y=0;$y<count($content);$y++)
			{
				for($z=0;$z<=count($content[$y]['row']);$z++)
				{
					if($content[$y]['row'][$z]['name']!='')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$hidden .= " <input name='values[select][0]'  type='hidden' value='".$content_values[$y - 1]['activity_id']."'/>";
			$hidden .= " <input name='values[total_cost][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['total_cost']."'/>";
			$hidden .= " <input name='values[w_cost][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['w_cost']."'/>";
			$hidden .= " <input name='values[m_cost][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['m_cost']."'/>";
			$hidden .= " <input name='values[id][".$content_values[$y - 1]['activity_id']."]'  type='hidden' value='".$content_values[$y - 1]['index_count']."'/>";

			$content_values[$y - 1]['index_date'] .= $hidden;

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($content_values),
					'total_records'			=> count($content_values),
					'is_paginator'			=> 0,
					'permission'			=> '""',
					'footer'				=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array(key => activity_id,label=>lang('Activity ID'),sortable=>false,resizeable=>true),
					array(key => m_cost,label=>lang('m_cost'),sortable=>false,resizeable=>true),
					array(key => w_cost,label=>lang('w_cost'),sortable=>false,resizeable=>true),
					array(key => total_cost,label=>lang('Total Cost'),sortable=>false,resizeable=>true),
					array(key => this_index,label=>lang('index'),sortable=>false,resizeable=>true),
					array(key => index_count,label=>lang('index_count'),sortable=>false,resizeable=>true),
					array(key => index_date,label=>lang('Date'),sortable=>false,resizeable=>true)))


				);

			$myButtons[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array(type=>text, label=>'New index', classname=> 'index-opt'),
					array(id =>'values[update]',type=>buttons,	value=>Update,	label=>lang('Update'),	funct=> onUpdateClick , classname=> ''),
					array(id =>'delete',type=>buttons,	value=>Delete,	label=>lang('delete last index'),	funct=> onDeleteClick , classname=> ''),
					array(id =>'values[new_index]', type=>inputText, size=>12, classname=> 'index-opt')



				))
			);

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
				(
					'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'					=> json_encode(array(menuaction => "property.uiagreement.edit_item", agreement_id=>$agreement_id, id=>$id, role=>$this->role)),
					'datatable'						=> $datavalues,
					'myColumnDefs'					=> $myColumnDefs,
					'myButtons'						=> $myButtons,

					'activity_descr' 				=> $activity_descr,
					'lang_descr' 					=> lang('Descr'),
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_id'						=> lang('ID'),
					'value_id'						=> $values['id'],
					'value_num'						=> $values['num'],
					'value_agreement_id'			=> $agreement_id,
					'lang_category'					=> lang('category'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'lang_apply_statustext'			=> lang('Apply the values'),
					'lang_cancel_statustext'		=> lang('Leave the agreement untouched and return back to the list'),
					'lang_save_statustext'			=> lang('Save the agreement and return back to the list'),

					'attributes_values'				=> $values['attributes'],
					'lookup_functions'				=> $values['lookup_functions'],
					'dateformat'					=> $dateformat,

					'lang_agreement'				=> lang('Agreement'),
					'agreement_name'				=> $agreement['name'],

					'table_add'						=> $table_add,
					'values'						=> $content,
					'index_count'					=> $content[0]['index_count'],
					'table_header'					=> $table_header,
					'acl_manage'					=> $this->acl_manage,
					'table_update'					=> $table_update,
					'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit_item', 'agreement_id'=> $agreement_id, 'id'=> $id)),
					'lang_select_all'				=> lang('Select All'),
					'img_check'						=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',

					'lang_m_cost'					=> lang('Material cost'),
					'lang_m_cost_statustext'		=> lang('Material cost'),
					'value_m_cost'					=> $values['m_cost'],

					'lang_w_cost'					=> lang('Labour cost'),
					'lang_w_cost_statustext'		=> lang('Labour cost'),
					'value_w_cost'					=> $values['w_cost'],

					'lang_total_cost'				=> lang('Total cost'),
					'value_total_cost'				=> $values['total_cost'],

					'set_column'					=> $set_column,
					'lang_delete_last'				=> lang('delete last index'),
					'lang_delete_last_statustext'	=> lang('delete the last index'),
					'delete_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.edit_item', 'delete_last'=>1, 'agreement_id'=> $agreement_id, 'id'=> $id)),
					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
				);

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . ($values['id']?lang('edit item') . ' ' . $agreement['name']:lang('add item') . ' ' . $agreement['name']);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_item' => $data));
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'agreement.edit_item', 'property' );
		}

		function view_item()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$id	= phpgw::get_var('id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement','attributes_view'));

			$agreement = $this->bo->read_single(array('agreement_id'=>$agreement_id));
			$values = $this->bo->read_single_item(array('agreement_id'=>$agreement_id,'id'=>$id));

			$link_data = array
				(
					'menuaction'	=> 'property.uiagreement.edit',
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
			$list		= $this->list_content($list,$uicols,$edit_item=true);
			$content	= $list['content'];
			$table_header=$list['table_header'];

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			//---datatable1 settings---------------------------------------------------
			//Prepare array for $datavalues[0]
			for($y=0;$y<count($content);$y++)
			{
				for($z=0;$z<=count($content[$y]['row']);$z++)
				{
					if($content[$y]['row'][$z]['name']!='')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($content_values),
					'total_records'	=> count($content_values),
					'is_paginator'	=> 0,
					'footer'		=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array(key => activity_id,	label=>$table_header[0]['header'],	sortable=>true,resizeable=>true ),
					array(key => m_cost,		label=>$table_header[2]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterRight ),
					array(key => w_cost,		label=>$table_header[3]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterRight ),
					array(key => total_cost,	label=>$table_header[4]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterRight ),
					array(key => this_index,	label=>$table_header[5]['header'],	sortable=>true,resizeable=>true ),
					array(key => index_count,	label=>$table_header[6]['header'],	sortable=>true,resizeable=>true, formatter=>FormatterCenter ),
					array(key => index_date,	label=>$table_header[7]['header'],	sortable=>true,resizeable=>true )
				)));

			$data = array
				(
					'property_js'				=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'				=> json_encode(array(menuaction => "property.uiagreement.view_item")),
					'datatable'					=> $datavalues,
					'myColumnDefs'				=> $myColumnDefs,

					'activity_descr' 			=> $activity_descr,
					'lang_descr' 				=> lang('Descr'),
					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_id'					=> lang('ID'),
					'value_id'					=> $values['id'],
					'value_num'					=> $values['num'],
					'value_agreement_id'		=> $agreement_id,
					'lang_category'				=> lang('category'),
					'lang_cancel'				=> lang('cancel'),
					'lang_cancel_statustext'	=> lang('Leave the agreement untouched and return back to the list'),

					'lang_dateformat' 			=> lang(strtolower($dateformat)),
					'attributes_view'			=> $values['attributes'],

					'lang_agreement'			=> lang('Agreement'),
					'agreement_name'			=> $agreement['name'],

					'table_add'					=> $table_add,
					'values'					=> $content,
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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'agreement.view_item', 'property' );
			//-----------------------datatable settings---

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('view item') . ' ' . $agreement['name'];

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_item' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$agreement_id	= phpgw::get_var('agreement_id', 'int');
			$delete		= phpgw::get_var('delete', 'bool', 'POST');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction'	=> 'property.uiagreement.index',
					'role'		=> $this->role
				);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($agreement_id);
				return "agreement_id ".$agreement_id." ".lang("has been deleted");
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.delete', 'agreement_id'=> $agreement_id, 'role'=> $this->role)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'			=> lang('yes'),
					'lang_yes_statustext'		=> lang('Delete the entry'),
					'lang_no_statustext'		=> lang('Back to the list'),
					'lang_no'			=> lang('no')
				);

			$appname		= lang('agreement');
			$function_msg		= lang('delete') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$this->bo->allrows	= 1;
			$agreement_id	= phpgw::get_var('id', 'int');
			$config			= CreateObject('phpgwapi.config','property');
			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement', 'nextmatchs', 'attributes_view', 'files'));
			$agreement 		= $this->bo->read_single(array('agreement_id'=>$agreement_id));


			if ($agreement_id)
			{
				$this->cat_id = ($agreement['cat_id']?$agreement['cat_id']:$this->cat_id);
				$this->member_id = ($agreement['member_of']?$agreement['member_of']:$this->member_id);
				$list = $this->bo->read_details($agreement_id);

				$uicols		= $this->bo->uicols;
				$list		= $this->list_content($list,$uicols,$edit_item=false,$view_only=true);
				$content	= $list['content'];
				$table_header=$list['table_header'];
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiagreement.index',
					'agreement_id'	=> $agreement_id,
				);

			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		=> $agreement['vendor_id'],
				'vendor_name'	=> $agreement['vendor_name'],
				'type'			=> 'view'));

			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
				'acl_location'	=>$this->acl_location,
				'alarm_type'	=> 'agreement',
				'type'			=> 'view',
				'text'			=> 'Email notification',
				'times'			=> $times,
				'id'			=> $agreement_id,
				'method'		=> $method,
				'data'			=> $data,
				'account_id'	=> $account_id
			));


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,link_data => array()));

			$link_file_data = array
				(
					'menuaction'=> 'property.uiagreement.view_file',
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
					'menuaction'	=> 'property.uiagreement.view',
					'id'		=> $agreement_id,
				);

			//---datatable0 settings---------------------------------------------------

			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($alarm_data['values']),
					'total_records'	=> count($alarm_data['values']),
					'permission'   	=> "''",
					'is_paginator'	=> 0,
					'footer'		=> 0
				);
			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array(key => time,	label=>$alarm_data['header'][0]['lang_time'],	sortable=>true,resizeable=>true,width=>140),
					array(key => text,	label=>$alarm_data['header'][0]['lang_text'],	sortable=>true,resizeable=>true,width=>340),
					array(key => user,	label=>$alarm_data['header'][0]['lang_user'],	sortable=>true,resizeable=>true,width=>200),
					array(key => enabled,label=>$alarm_data['header'][0]['lang_enabled'],sortable=>true,resizeable=>true,formatter=>FormatterCenter,width=>60)))
				);

			//---datatable1 settings---------------------------------------------------
			//Prepare array for $datavalues[1]

			for($y=0;$y<count($content);$y++)
			{
				for($z=0;$z<=count($content[$y]['row']);$z++)
				{
					if($content[$y]['row'][$z]['name']!='')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$datavalues[1] = array
				(
					'name'			=> "1",
					'values' 		=> json_encode($content_values),
					'total_records'	=> count($content_values),
					'permission'   	=> "''",
					'is_paginator'	=> 1,
					'footer'		=> 0
				);

			$myColumnDefs[1] = array
				(
					'name'			=> "1",
					'values'		=>	json_encode(array(	array('key' => 'activity_id',	'label'=>$table_header[0]['header'],	'sortable'=>true,'resizeable'=>true),
					array('key' => 'num',			'label'=>$table_header[1]['header'],	'sortable'=>true,'resizeable'=>true),
					array('key' => 'descr',			'label'=>$table_header[2]['header'],	'sortable'=>true,'resizeable'=>true),
					array('key' => 'unit_name',		'label'=>$table_header[3]['header'],	'sortable'=>true,'resizeable'=>true, 'formatter'=>'FormatterCenter'),
					array('key' => 'm_cost',		'label'=>$table_header[4]['header'],	'sortable'=>true,'resizeable'=>true, 'formatter'=>'FormatterRight'),
					array('key' => 'w_cost',		'label'=>$table_header[5]['header'],	'sortable'=>true,'resizeable'=>true, 'formatter'=>'FormatterRight'),
					array('key' => 'total_cost',	'label'=>$table_header[6]['header'],	'sortable'=>true,'resizeable'=>true, 'formatter'=>'FormatterRight'),
					array('key' => 'this_index',	'label'=>$table_header[7]['header'],	'sortable'=>true,'resizeable'=>true),
					array('key' => 'index_count',	'label'=>$table_header[8]['header'],	'sortable'=>true,'resizeable'=>true, 'formatter'=>'FormatterCenter'),
					array('key' => 'index_date',	'label'=>$table_header[9]['header'],	'sortable'=>true,'resizeable'=>true)
				)));

			//---datatable2 settings---------------------------------------------------

			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			for($z=0; $z<count($agreement['files']); $z++)
			{
				if ($link_to_files != '')
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_to_files.'/'.$agreement['files'][$z]['directory'].'/'.$agreement['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$agreement['files'][$z]['name'].'</a>';
				}
				else
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$agreement['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$agreement['files'][$z]['name'].'</a>';
				}
			}

			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(array(key => file_name,label=>lang('Filename'),sortable=>false,resizeable=>true)))
				);


			$link_download = array
				(
					'menuaction'	=> 'property.uiagreement.download',
					'id'		=>$agreement_id
				);

			$data = array
				(
					'property_js'				=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'				=> json_encode(array(menuaction => "property.uiagreement.view")),
					'datatable'					=> $datavalues,
					'myColumnDefs'				=> $myColumnDefs,

					'allow_allrows'				=> true,
					'allrows'					=> $this->allrows,
					'start_record'				=> $this->start,
					'record_limit'				=> $record_limit,
					'num_records'				=> count($content),
					'lang_total_records'		=> lang('Total'),
					'all_records'				=> $this->bo->total_records,
					'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data2),
					'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

					'alarm_data'				=> $alarm_data,
					'lang_alarm'				=> lang('Alarm'),
					'link_view_file'			=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),

					'files'						=> isset($agreement['files'])?$agreement['files']:'',
					'lang_files'				=> lang('files'),
					'lang_filename'				=> lang('Filename'),
					'lang_view_file_statustext'	=> lang('click to view file'),

					'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_id'					=> lang('ID'),
					'value_agreement_id'		=> $agreement_id,
					'lang_category'				=> lang('category'),
					'lang_save'					=> lang('save'),
					'lang_cancel'				=> lang('done'),
					'lang_apply'				=> lang('apply'),
					'value_cat'					=> $agreement['cat'],
					'lang_cancel_statustext'	=> lang('return back to the list'),
					'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'branch','order'=>'descr')),

					'lang_member_of'			=> lang('member of'),
					'member_of_name'			=> 'member_id',
					'member_of_list'			=> $member_of_data['cat_list'],

					'lang_dateformat' 			=> lang(strtolower($dateformat)),
					'attributes_view'			=> $agreement['attributes'],
					'dateformat'				=> $dateformat,

					'lang_start_date'			=> lang('start date'),
					'value_start_date'			=> $agreement['start_date'],

					'lang_end_date'				=> lang('end date'),
					'value_end_date'			=> $agreement['end_date'],

					'lang_termination_date'		=> lang('termination date'),
					'value_termination_date'	=> $agreement['termination_date'],

					'vendor_data'				=> $vendor_data,
					'lang_name'					=> lang('name'),
					'value_name'				=> $agreement['name'],
					'lang_descr'				=> lang('descr'),
					'value_descr'				=> $agreement['descr'],
					'table_add'					=> $table_add,
					'values'					=> $content,
					'table_header'				=> $table_header,
					'lang_agreement_group'		=> lang('Agreement group'),
					'agreement_group_list'		=> $this->bo->get_agreement_group_list($agreement['group_id']),

					'lang_status'				=> lang('Status'),
					'status_list'				=> $this->bo->select_status_list('select',$agreement['status']),
					'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
					'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,

					'lang_download'				=> 'download',
					'link_download'				=> $GLOBALS['phpgw']->link('/index.php',$link_download),
					'lang_download_help'		=> lang('Download table to your browser'),


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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'agreement.view', 'property' );
			//-----------------------datatable settings---


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('view');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}
