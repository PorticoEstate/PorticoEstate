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
	* @subpackage admin
 	* @version $Id$
	*/
	phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uiasync
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
			(
				'index'  => true,
				'view'   => true,
				'edit'   => true,
				'delete' => true
			);

		function property_uiasync()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::async';

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boasync',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;

			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'		=> $this->start,
					'query'		=> $this->query,
					'sort'		=> $this->sort,
					'order'		=> $this->order,
		//			'allrows'	=> $this->allrows,
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiasync.index',
						'order'			=> $this->order,
						'query'		=> $this->query,
						'sort'		=> $this->sort
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uiasync.index',"
					."order:'{$this->order}',"
					."query:'{$this->query}',"
					."sort:'{$this->sort}'";
				$link_data = array
					(
						'menuaction'	=> 'property.uiasync.index',
						'order'			=> $this->order,
						'query'		=> $this->query,
						'sort'		=> $this->sort
					);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uiasync.index',
								'order'			=> $this->order,
								'query'		=> $this->query,
								'sort'		=> $this->sort
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'value'	=> lang('done'),
									'tab_index' => 9
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 8
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 7
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 6
								)
							)
						)
					)
				);
			}

			$method_list = $this->bo->read();
			$uicols['name'][0]			= 'id';
			$uicols['descr'][0]			= lang('method ID');
			$uicols['className'][0]		= 'rightClasss';
			$uicols['name'][1]			= 'name';
			$uicols['descr'][1]			= lang('Name');
			$uicols['name'][2]			= 'data';
			$uicols['descr'][2]			= lang('Data');
			$uicols['name'][3]			= 'descr';
			$uicols['descr'][3]			= lang('Description');
			$uicols['name'][4]			= 'url';
			$uicols['descr'][4]			= lang('URL');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($method_list) AND is_array($method_list))
			{
				foreach($method_list as $method_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']				= $method_entry[$uicols['name'][$k]];
							if($uicols['name'][$k] == 'data')
							{
								$data_set = unserialize($method_entry[$uicols['name'][$k]]);
								$method_data=array();
								foreach ($data_set as $key => $value)
								{
									$method_data[] = $key . '=' . $value;
								}
								$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
								$datatable['rows']['row'][$j]['column'][$k]['value']				= @implode (',',$method_data);
							}
							if($uicols['name'][$k] == 'url')
							{
								$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
								$run_link_data = array();
								$run_link_data['menuaction']	= $method_entry['name'];
								$run_link_data['data'] 			= urlencode($method_entry['data']);
								$datatable['rows']['row'][$j]['column'][$k]['value']			=	$run_link_data['menuaction']."&data=".urlencode($run_link_data['data']);
							}
						}
					}
					$j++;
				}
			}

			//_debug_array($datatable);die;
			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'menuaction',
							'source'	=> 'url'
						),
					)
				);

			$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'method_id',
							'source'	=> 'id'
						)
					)
				);

			$parameters3 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						)
					)
				);


			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'run',
					'statustext' 	=> lang('Run Now'),
					'text'			=> lang('Run Now'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							//'menuaction'		=> 'property.uiasync.edit'
							)
						),
						'parameters'	=> $parameters
					);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'schedule',
					'statustext' 	=> lang('Schedule'),
					'text'			=> lang('Schedule'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uialarm.edit'
						)
					),
					'parameters'	=> $parameters2
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'edit',
					'statustext' 	=> lang('Edit'),
					'text'			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uiasync.edit'
						)
					),
					'parameters'	=> $parameters3
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'delete',
					'statustext' 	=> lang('Delete'),
					'text'			=> lang('Delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uiasync.delete'
						)
					),
					'parameters'	=> $parameters3
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'add',
					'statustext' 	=> lang('add'),
					'text'			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiasync.edit'
					))
				);


			unset($parameters);
			unset($parameters2);
			unset($parameters3);

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['className'] 		= $uicols['className'][$i];
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='url')
					{
						$datatable['headers']['header'][$i]['visible'] 		= false;
					}

					if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'id';
					}

					if($uicols['name'][$i]=='name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'name';
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($method_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('method');
			$function_msg	= lang('list async method');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'uiasync.index', 'property' );

			$this->save_sessiondata();

		}

		function edit()
		{
			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('async'));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				else
				{
					$id =	$values['id'];
				}

				$data = str_replace(' ' ,'',stripslashes($values['data']));
				$data = stripslashes($values['data']);

				$data= explode(",", $data);

				if(is_array($data))
				{
					foreach($data as $set)
					{
						$set= explode("=", $set);
						$data_set[$set[0]]=$set[1];
					}
				}

				if($values['data'])
				{
					$values['data']=serialize($data_set);
				}

				$receipt = $this->bo->save($values,$action);
				$id = $receipt['id'];
			}

			if ($id)
			{
				$method = $this->bo->read_single($id);
				$data_set = unserialize($method['data']);
				while (is_array($data_set) && list($key,$value) = each($data_set))
				{
					$method_data[] = $key . '=' . $value;
				}

				$method_data= @implode (',',$method_data);
				$function_msg = lang('edit method');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add method');
				$action='add';
			}


			$link_data = array
				(
					'menuaction'	=> 'property.uiasync.edit',
					'id'		=> $id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.index')),
					'lang_id'					=> lang('method ID'),
					'lang_name'					=> lang('Name'),
					'lang_descr'				=> lang('Descr'),
					'lang_save'					=> lang('save'),
					'lang_done'					=> lang('done'),
					'value_id'					=> $id,
					'value_name'				=> $method['name'],
					'lang_id_statustext'		=> lang('Enter the method ID'),
					'lang_descr_statustext'		=> lang('Enter a description the method'),
					'lang_done_statustext'		=> lang('Back to the list'),
					'lang_save_statustext'		=> lang('Save the method'),
					'type_id'					=> $method['type_id'],
					'location_code'				=> $method['location_code'],
					'value_descr'				=> $method['descr'],
					'value_data'				=> $method_data,
					'lang_data'					=> lang('Data'),
					'lang_data_statustext'		=> lang('Input data for the nethod'),
				);

			$appname	= lang('async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			$id			= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return "id ".$id." ".lang("has been deleted");
			}

			$link_data = array
				(
					'menuaction' => 'property.uiasync.index'
				);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiasync.delete', 'id'=> $id)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname		= lang('async method');
			$function_msg		= lang('delete async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
