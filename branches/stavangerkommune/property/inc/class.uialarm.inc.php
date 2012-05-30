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

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');
	class property_uialarm
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
				'index'  	=> true,
				'view'		=> true,
				'edit'		=> true,
				'delete'	=> true,
				'list_alarm'=> true,
				'run'		=> true
			);

		function property_uialarm()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::admin_async';

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.boalarm',true);
			$this->boasync		= CreateObject('property.boasync');
			$this->bocommon		= CreateObject('property.bocommon');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->method_id	= $this->bo->method_id;
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
					'method_id'		=> $this->method_id,
					'allrows'		=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			//$receipt = $GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt');
			//$GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt','');
			$values		= phpgw::get_var('values');

			if($values['delete_alarm'] && count($values['alarm']))
			{
				$receipt = $this->bo->delete_alarm('fm_async',$values['alarm']);
			}
			else if(($values['enable_alarm'] || $values['disable_alarm']) && count($values['alarm']))
			{
				$receipt = $this->bo->enable_alarm('fm_async',$values['alarm'],$values['enable_alarm']);
			}
			else if(isset($values['test_cron']) && $values['test_cron'] && isset($values['alarm']) && $values['alarm'])
			{
				$this->bo->test_cron($values['alarm']);
			}

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=>	'property.uialarm.index',
						'sort'			=>	$this->sort,
						'order'			=>	$this->order,
						'cat_id'		=>	$this->cat_id,
						'filter'		=>	$this->filter,
						'query'			=>	$this->query

					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uialarm.index',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"	
					."cat_id:'{$this->cat_id}',"
					."filter:'{$this->filter}',"													    																
					."query:'{$this->query}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'		=> 'property.uialarm.index'
							)
						),					
						'fields'	=> 	array
						(
							'field' => array
							(
								array
								( //boton TEST_CROM
									'id' 		=> 'btn_test_cron',
									'name'		=> 'test_cron',
									'value'		=> lang('test cron'),
									'type'		=> 'button',
									'style'		=> 'filter',
									'tab_index' => 1
								),
								array
								( //boton ENABLE
									'id' 		=> 'btn_enable',
									'name'		=> 'enable',
									'value'		=> lang('Enable'),
									'type'		=> 'button',
									'style'		=> 'filter',
									'tab_index' => 2
								),
								array
								( //boton DISABLE
									'id' 		=> 'btn_disable',
									'name'		=> 'disable',
									'value'		=> lang('Disable'),
									'type'		=> 'button',
									'style'		=> 'filter',
									'tab_index' => 3
								),
								array
								( //boton SEARCH
									'id' 		=> 'btn_delete',
									'name'		=> 'delete',
									'value'		=> lang('Delete'),
									'type'		=> 'button',
									'style'		=> 'filter',
									'tab_index' => 4
								),
								array
								( //boton ADD
									'id' 		=> 'btn_new',
									'name'		=> 'add',
									'value'		=> lang('add'),
									'type'		=> 'button',
									'tab_index' => 7
								),					
								array
								( //boton SEARCH
									'id' 		=> 'btn_search',
									'name'		=> 'search',
									'value'		=> lang('search'),
									'type'		=> 'button',
									'tab_index' => 6
								),
								array
								( // TEXT IMPUT
									'name'		=> 'query',
									'id'		=> 'txt_query',
									'value'		=> $this->query,
									'type'		=> 'text',
									'size'		=> 28,
									'onkeypress'=> 'return pulsar(event)',
									'tab_index'	=> 5
								),
								array
								( //boton hidden actions for button
									'id' 		=> 'values[action_button]',
									'name'		=> 'values[action_button]',
									'value'		=> '',
									'type'		=> 'hidden'
								),
								array
								( //container of  control's Form
									'type'	=> 'label',
									'id'	=> 'controlsForm_container',
									'value'	=> ''
								),
							),
							'hidden_value' => array()
						)
					)
				);
			}
			$list = array();
			$list = $this->bo->read();				

			foreach ($list as $alarm)
			{
				$link_edit				= '';
				$lang_edit_statustext	= '';
				$text_edit				= '';

				if (substr($alarm['id'],0,8)=='fm_async')
				{
					$link_edit				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uialarm.edit', 'async_id'=> urlencode($alarm['id'])));
					$text_edit				= lang('edit');
					$link_edit				= "<a href=\"$link_edit\">$text_edit</a>";
				}
				else
				{
					$link_edit				= "-";
				}

				$check_box = "<input type=\"checkbox\" name=\"values[alarm][".$alarm['id']."]\" value=\"".$alarm['id']."\" class=\"myValuesForPHP\">";

				$content[] = array
					(
						'id'					=> $alarm['id'],
						'next_run'				=> $GLOBALS['phpgw']->common->show_date($alarm['next']),
						'times'					=> is_array($alarm['times']) ? print_r($alarm['times'],true) : $GLOBALS['phpgw']->common->show_date($alarm['times']),
						'method'				=> $alarm['method'],
						'data'					=> print_r($alarm['data'],true),
						'enabled'				=> $alarm['enabled'],
						'user'					=> $alarm['user'],
						'check_box'				=> $check_box,
						'link_edit'				=> $link_edit							
					);
			}

			$uicols = array
				(
					array
					(
						'col_name'=>'alarm_id',	'input_type'=>'varchar',	'name'=>'id',		'descr'=>lang('alarm id'),	'className'=>'centerClasss', 	'sortable'=>true ,'formatter'=>'',		'sort_field'=>'id'
					),
					array
					(
						'col_name'=>'next_run',	'input_type'=>'varchar',	'name'=>'next_run',	'descr'=>lang('Next run'),	'className'=>'centerClasss', 	'sortable'=>true ,'formatter'=>'',		'sort_field'=>'next'
					),
					array
					(
						'col_name'=>'times',		'input_type'=>'varchar',	'name'=>'times',	'descr'=>lang('Times'),		'className'=>'centerClasss', 	'sortable'=>false ,'formatter'=>'',		'sort_field'=>''
					),
					array
					(
						'col_name'=>'method',		'input_type'=>'varchar',	'name'=>'method',	'descr'=>lang('Method'),	'className'=>'leftClasss',		'sortable'=>true ,'formatter'=>'',	'	sort_field'=>'method'
					),
					array
					(
						'col_name'=>'data',		'input_type'=>'varchar',	'name'=>'data',		'descr'=>lang('Data'),		'className'=>'leftClasss',		'sortable'=>false ,'formatter'=>'',	'	sort_field'=>''
					),
					array
					(
						'col_name'=>'enable',		'input_type'=>'varchar',	'name'=>'enabled',	'descr'=>lang('enabled'),	'className'=>'centerClasss', 	'sortable'=>false ,'formatter'=>'',		'sort_field'=>''
					),
					array
					(
						'col_name'=>'user',		'input_type'=>'varchar',	'name'=>'user',		'descr'=>lang('User'),		'className'=>'centerClasss', 	'sortable'=>true ,'formatter'=>'',		'sort_field'=>'account_lid'
					),
					array
					(
						'col_name'=>'select',		'input_type'=>'imput',	'name'=>'check_box','descr'=>lang('select'),	'className'=>'centerClasss', 	'sortable'=>false ,'formatter'=>'',		'sort_field'=>''
					),
					array
					(
						'col_name'=>'edit',		'input_type'=>'link',		'name'=>'link_edit','descr'=>lang('edit'),		'className'=>'centerClasss', 	'sortable'=>false ,'formatter'=>'',		'sort_field'=>''
					)
				);

			$j=0;
			if (isset($content) && is_array($content))
			{
				foreach($content as $alarm)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols[$i]['col_name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']		= $alarm[$uicols[$i]['name']];
						$datatable['rows']['row'][$j]['column'][$i]['format']		= $uicols[$i]['input_type'];
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
							'source'	=> 'alarm_id'
						),
					)
				);


			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'edit',
				'text' 			=> lang('run'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uialarm.run',
				)),
				'parameters'		=> $parameters
			);

			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'add',
				'text' 			=> lang('add'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uialarm.edit',
				))
			);

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['formatter'] 	= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
				$datatable['headers']['header'][$i]['name'] 		= $uicols[$i]['col_name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['descr'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['visible']		= true;

				if($uicols[$i]['input_type']=='hidden')
				{
					$datatable['headers']['header'][$i]['visible']	= false;
				}
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				//avoid ,in the last page, reformate paginator when records are lower than records_returned
				if(count($content) <= $datatable['pagination']['records_limit'])
				{
					$datatable['pagination']['records_returned']= count($content);
				}
				else
				{
					$datatable['pagination']['records_returned']= $datatable['pagination']['records_limit'];
				}
				$datatable['sorting']['currentPage']	= 1;
				$datatable['sorting']['order'] 			= $uicols[0]["col_name"]; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{

				$datatable['sorting']['currentPage']		= phpgw::get_var('currentPage');
				$datatable['sorting']['order']				= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort']				= phpgw::get_var('sort', 'string'); // ASC / DESC
				$datatable['pagination']['records_returned']= phpgw::get_var('recordsReturned', 'int');
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

			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'currentPage'		=> $datatable['sorting']['currentPage'],
					'records'			=> array()
				);

			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						$json_row[$column['name']] = $column['value'];
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
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('alarm') . ': ' . lang('list alarm');


			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'alarm.index', 'property' );



			//$this->save_sessiondata();		
		}

		function list_alarm()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::alarm';
			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt','');

			$values	= phpgw::get_var('values');
			if($values['delete_alarm'] && count($values['alarm']))
			{
				$receipt = $this->bo->delete_alarm('fm_async',$values['alarm']);
			}
			else if(($values['enable_alarm'] || $values['disable_alarm']) && count($values['alarm']))
			{
				$receipt = $this->bo->enable_alarm('fm_async',$values['alarm'],$values['enable_alarm']);
			}
			else if($values['test_cron'])
			{
				$this->bo->test_cron();
			}

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uialarm.list_alarm',
						'query'            		=> $this->query

					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uialarm.list_alarm',"
					."query:'{$this->query}'";


				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uialarm.list_alarm',
								'query' 			=> $this->query
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton     SEARCH
									'id' 		=> 'btn_search',
									'name'		=> 'search',
									'value'		=> lang('search'),
									'type'		=> 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'		=> 'query',
									'id'		=> 'txt_query',
									'value'		=> $this->query,
									'type'		=> 'text',
									'size'		=> 28,
									'onkeypress'=> 'return pulsar(event)',
									'tab_index'	=> 1
								)
							),
							'hidden_value' => array()
						)
					)
				);
			}

			$list = array();

			$list = $this->bo->read();

			while (is_array($list) && list($id,$alarm) = each($list))
			{
				if(is_array($alarm['times']))
				{
					while (is_array($alarm['times']) && list($key,$value) = each($alarm['times']))
					{
						$times .=$key . ' => ' .$value. ' ';
					}

				}
				else
				{
					$times = $GLOBALS['phpgw']->common->show_date($alarm['times']);
				}

				if(is_array($alarm['data']))
				{
					while (is_array($alarm['data']) && list($key,$value) = each($alarm['data']))
					{
						if($key=='owner')
						{
							$value = $GLOBALS['phpgw']->accounts->id2name($value);
						}
						$data .=$key . ' => ' .$value . ' ';
					}

				}

				$id = explode(':', $id);

				if($id[0] == 's_agreement' || $id[0] == 'agreement')
				{
					$link_edit				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.ui' .$id[0] .'.edit', 'id'=> $id[1]));
					$lang_edit_statustext	= lang('edit the alarm');
					$text_edit				= lang('edit');

				}

				$content[] = array
					(
						'id_cod'			=> $id[1],
						'id'				=> $alarm['id'],
						'next_run'			=> $GLOBALS['phpgw']->common->show_date($alarm['next']),
						'method'			=> $alarm['method'],
						'times'				=> $times,
						'data'				=> $data,
						'enabled'			=> $alarm['enabled'],
						'user'				=> $alarm['user'],
						//					'link_edit'			=> $link_edit,
						//					'lang_edit_statustext'		=> $lang_edit_statustext,
						//					'text_edit'			=> $text_edit
					);
				unset($alarm);
				unset($data);
				unset($times);
				unset($link_edit);
				unset($lang_edit_statustext);
				unset($text_edit);
			}


			//die(_debug_array($content));



			$uicols = array
				(
					array
					(
						'col_name'=>'id',			'input_type'=>'hidden',	'name'=>'id_cod',	'descr'=>'',				'className'=>'', 				'sortable'=>false ,'formatter'=>'',		'sort_field'=>''
					),
					array
					(
						'col_name'=>'alarm_id',	'input_type'=>'varchar',	'name'=>'id',		'descr'=>lang('alarm id'),	'className'=>'centerClasss', 	'sortable'=>true ,'formatter'=>'',		'sort_field'=>'id'
					),
					array
					(
						'col_name'=>'next_run',	'input_type'=>'varchar',	'name'=>'next_run',	'descr'=>lang('Next run'),	'className'=>'centerClasss', 	'sortable'=>true ,'formatter'=>'',		'sort_field'=>'next'
					),
					array
					(
						'col_name'=>'data',		'input_type'=>'varchar',	'name'=>'data',		'descr'=>lang('Data'),		'className'=>'leftClasss',		'sortable'=>false ,'formatter'=>'',	'	sort_field'=>''
					),
					array
					(
						'col_name'=>'enable',		'input_type'=>'varchar',	'name'=>'enabled',	'descr'=>lang('enabled'),	'className'=>'centerClasss', 	'sortable'=>false ,'formatter'=>'',		'sort_field'=>''
					),
					array
					(
						'col_name'=>'user',		'input_type'=>'varchar',	'name'=>'user',		'descr'=>lang('User'),		'className'=>'centerClasss', 	'sortable'=>true ,'formatter'=>'',		'sort_field'=>'account_lid')
					);

			$j=0;
			if (isset($content) && is_array($content))
			{
				foreach($content as $alarm)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols[$i]['col_name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']		= $alarm[$uicols[$i]['name']];
						$datatable['rows']['row'][$j]['column'][$i]['format']		= $uicols[$i]['input_type'];
					}
					$j++;
				}
			}

			//die(_debug_array($datatable['rows']));
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

			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'edit',
				'text' 			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uis_agreement.edit',
				)),
				'parameters'	=> $parameters
			);
			unset($parameters);


			for ($i=0;$i<count($uicols);$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);

				$datatable['headers']['header'][$i]['name'] 		= $uicols[$i]['col_name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['descr'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];

				if($uicols[$i]['input_type']!='hidden')
				{
					$datatable['headers']['header'][$i]['visible']	= true;
				}
				else
				{
					$datatable['headers']['header'][$i]['visible'] 	= false;
				}
			}

			//die(_debug_array($datatable['headers']));
			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;



			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[0]['col_name']; // name key Column in myColumnDef
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
						$json_row[$column['name']] = $column['value'];
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
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('alarm') . ': ' . lang('list alarm');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'alarm.list_alarm', 'property' );

			//$this->save_sessiondata();
		}

		function edit()
		{
			$method_id 	= phpgw::get_var('method_id', 'int');
			$async_id	= urldecode(phpgw::get_var('async_id'));
			$values		= phpgw::get_var('values');

			if($async_id)
			{
				$async_id_elements = explode(':',$async_id);
				$method_id = $async_id_elements[1];
			}

			$this->method_id = $method_id ? $method_id : $this->method_id;

			$GLOBALS['phpgw']->xslttpl->add_file(array('alarm'));


			if ($values['save'] || $values['apply'])
			{

				$units = array(
					'year',
					'month',
					'day',
					'dow',
					'hour',
					'min');

				$times = array();
				foreach($units as $u)
				{
					if ($values[$u] !== '')
					{
						$times[$u] = $values[$u];
					}
				}

				if(!$receipt['error'])
				{
					$this->method_id =  $values['method_id'] ? $values['method_id'] : $this->method_id;

					$values['alarm_id']	= $alarm_id;

					$async=$this->boasync->read_single($this->method_id);
					//_debug_array($async);
					$data_set = unserialize($async['data']);
					$data_set['enabled']	= true;
					$data_set['times'] 		= $times;
					$data_set['owner']		= $this->account;
					$data_set['event_id']	= $this->method_id;
					$data_set['id']			= $async_id;

					$async_id = $this->bo->save_alarm($alarm_type='fm_async',$entity_id=$this->method_id,$alarm=$data_set,$async['name']);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uialarm.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uialarm.index'));
			}

			if ($async_id)
			{
				$alarm = $this->bo->read_alarm($alarm_type='fm_async',$async_id);

				$this->method_id =  $alarm['event_id'] ? $alarm['event_id'] : $this->method_id;
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uialarm.edit',
					'async_id'	=> $async_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			//_debug_array($alarm);
			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'abook_data'					=> $abook_data,
					'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_async_id'					=> lang('ID'),
					'value_async_id'				=> $async_id,
					'lang_method'					=> lang('method'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'lang_apply_statustext'			=> lang('Apply the values'),
					'lang_cancel_statustext'		=> lang('Leave the owner untouched and return back to the list'),
					'lang_save_statustext'			=> lang('Save the owner and return back to the list'),
					'lang_no_method'				=> lang('no method'),
					'lang_method_statustext'		=> lang('Select the method for this times service'),
					'method_list'					=> $this->bo->select_method_list($this->method_id),
					'lang_timing'					=> lang('timing'),
					'lang_year'						=> lang('year'),
					'value_year'					=> $alarm['times']['year'],
					'lang_month'					=> lang('month'),
					'value_month'					=> $alarm['times']['month'],
					'lang_day'						=> lang('day'),
					'value_day'						=> $alarm['times']['day'],
					'lang_dow'						=> lang('Day of week (0-6, 0=Sun)'),
					'value_dow'						=> $alarm['times']['dow'],
					'lang_hour'						=> lang('hour'),
					'value_hour'					=> $alarm['times']['hour'],
					'lang_minute'					=> lang('minute'),
					'value_minute'					=> $alarm['times']['min'],
					'lang_data'						=> lang('data'),
					'lang_data_statustext'				=> lang('inputdata for the method')
				);
			//_debug_array($data);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('async') . ': ' . ($async_id?lang('edit timer'):lang('add timer'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		/**
		 * @todo remove or alter this function
		 */

		function delete()
		{
			$owner_id	= phpgw::get_var('owner_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uiowner.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($owner_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiowner.delete', 'owner_id'=> $owner_id)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname	= lang('owner');
			$function_msg	= lang('delete owner');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}



		function view()
		{
			$owner_id	= phpgw::get_var('owner_id', 'int', 'GET');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('owner') . ': ' . lang('view owner');

			$GLOBALS['phpgw']->xslttpl->add_file('owner');

			$owner = $this->bo->read_single($owner_id);

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiowner.index')),
					'lang_name'			=> lang('name'),
					'lang_category'		=> lang('category'),
					'lang_time_created'	=> lang('time created'),
					'lang_done'			=> lang('done'),
					'value_name'		=> $owner['name'],
					'value_cat'			=> $this->bo->read_category_name($owner['cat_id']),
					'value_date'		=> $GLOBALS['phpgw']->common->show_date($owner['entry_date'])
				);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function run()
		{
			$id	= phpgw::get_var('id');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uialarm.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->test_cron(array($id => $id));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uialarm.run', 'id'=> $id)),
					'lang_confirm_msg'		=> lang('do you really want to run this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Run'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::cron::run";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
