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

	class property_uitemplate
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
				'index'			=> true,
				'view'			=> true,
				'edit_template'	=> true,
				'edit_hour'		=> true,
				'delete'		=> true,
				'hour'			=> true
			);

		function property_uitemplate()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project::template';

			$this->bo					= CreateObject('property.botemplate',true);
			$this->bowo_hour			= CreateObject('property.bowo_hour');
			$this->bocommon				= CreateObject('property.bocommon');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->chapter_id			= $this->bo->chapter_id;
			$this->allrows				= $this->bo->allrows;
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

		function index()
		{
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$lookup 	= phpgw::get_var('lookup', 'bool');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['menu']					= $this->bocommon->get_menu();

				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uitemplate.index',
						'query'            		=> $this->query,
						'chapter_id'			=> $this->chapter_id,
						'order'					=> $this->order
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uitemplate.index',"
					."sort: '{$this->sort}',"
					."order: '{$this->order}',"
					."status: '{$this->status}',"
					."workorder_id:'{$workorder_id}',"
					."lookup:'{$lookup}',"
					."query: '{$this->query}'";

				$values_combo_box[0] = $this->bowo_hour->get_chapter_list('filter',$this->chapter_id);
				$default_value = array ('id'=>'','name'=> lang('select chapter'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->get_user_list('filter',$this->filter,$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1);
				$default_value = array ('user_id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[1],$default_value);


				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uitemplate.index',
								'query'            		=> $this->query,
								'chapter_id'			=> $this->chapter_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //Chapter button
									'id' => 'btn_chap_id',
									'name' => 'chap_id',
									'value'	=> lang('Chapter'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //User button
									'id' => 'btn_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),						                            				                                        
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'value'	=> lang('done'),
									'tab_index' => 7
								),				                                        
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_select',
									'value'	=> lang('Select'),
									'tab_index' => 6
								),				                                        
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 5
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 4
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 3
								),
								array
								( //hidden 
									'type'	=> 'hidden',
									'name'     => 'workorder_id',
									'id'	=> 'workorder_id',
									'value'	=> 0,
									'style' => 'filter'
								),
								array
								( //hidden 
									'type'	=> 'hidden',
									'name'     => 'template_id',
									'id'	=> 'template_id',
									'value'	=> 0,
									'style' => 'filter'
								)				                                        
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
									'value'	=> $this->bocommon->select2String($values_combo_box[1],'user_id')
								)
							)
						)
					)
				);

				if($lookup)
				{
					unset($datatable['actions']['form'][0]['fields']['field'][4]);
				} 
				if(!$lookup) {
					unset($datatable['actions']['form'][0]['fields']['field'][3]);
					unset($datatable['actions']['form'][0]['fields']['field'][2]);
				}

//				$dry_run = true;
			}

			$template_list	= $this->bo->read();

			$uicols = array();

			$uicols['name'][0]['name'] = 'ID';
			$uicols['name'][0]['value'] = 'template_id';

			$uicols['name'][1]['name'] = 'Name';
			$uicols['name'][1]['value'] = 'name';

			$uicols['name'][2]['name'] = 'Description';
			$uicols['name'][2]['value'] = 'descr';

			$uicols['name'][3]['name'] = 'Chapter';
			$uicols['name'][3]['value'] = 'chapter';

			$uicols['name'][4]['name'] = 'owner';
			$uicols['name'][4]['value'] = 'owner';

			$uicols['name'][5]['name'] = 'Entry Date';
			$uicols['name'][5]['value'] = 'entry_date';


			$count_uicols_name = count($uicols['name']);

			$j = 0;
			if (isset($template_list) AND is_array($template_list))
			{
				foreach($template_list as $template_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k]['value'];
						$datatable['rows']['row'][$j]['column'][$k]['value']	= $template_entry[$uicols['name'][$k]['value']];
					}
					if($lookup)
					{
						$datatable['rows']['row'][$j]['column'][$k + 1]['name'] 	= 'select';
						$datatable['rows']['row'][$j]['column'][$k + 1]['value']	= '<input type="radio" name="rad_template" value="'.$template_entry['template_id'].'" class="myValuesForPHP"">';
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

			if(!$lookup) 
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'template_id',
								'source'	=> 'template_id'
							),
						)
					);

				$parameters2 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'template_id'
							),
						)
					);

				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'view',
						'statustext' 	=> lang('view the claim'),
						'text'			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitemplate.hour'
						)
					),
					'parameters'	=> $parameters
				);

				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'edit',
						'statustext' 			=> lang('edit the claim'),
						'text'		=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitemplate.edit_template'
						)
					),
					'parameters'	=> $parameters
				);

				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'delete',
						'statustext' 			=> lang('delete the claim'),
						'text'		=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitemplate.delete'
						)
					),
					'parameters'	=> $parameters2
				);

				$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'add',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitemplate.edit_template'
						)
					)
				);

				unset($parameters);
				unset($parameters2);
			}

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

				if($uicols['name'][$i]['value']=='name')
				{
					$datatable['headers']['header'][$i]['sortable']			= true;
					$datatable['headers']['header'][$i]['sort_field']   	= $uicols['name'][$i]['value'];
				}

				if($uicols['name'][$i]['value']=='template_id')
				{
					$datatable['headers']['header'][$i]['sortable']			= true;
					$datatable['headers']['header'][$i]['sort_field']   	= "fm_template.id";
				}
			}

			if($lookup)
			{
				$i++;
				$datatable['headers']['header'][$i]['name'] 			= 'select';
				$datatable['headers']['header'][$i]['text'] 			= lang('select');
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= false;
				$datatable['headers']['header'][$i]['format'] 			= '';
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['formatter']		= '""';

			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($template_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;


			$appname					= lang('template');
			$function_msg				= lang('list template');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'template_id'; // name key Column in myColumnDef
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'template.index', 'property' );

			$this->save_sessiondata();
		}

		function hour()
		{
			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$template_id = phpgw::get_var('template_id', 'int');

			if($delete && $hour_id && phpgw::get_var('phpgw_return_as') == 'json')
			{
				$receipt = $this->bo->delete_hour($hour_id,$template_id);
				return "hour ".$hour_id." ".lang("has been deleted");
			}
			else
			{
				$receipt = array();
			}

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uitemplate.hour',
						'query'            		=> $this->query,
						'template_id'			=> $template_id,

					));
				$datatable['config']['allow_allrows'] = true;
				$datatable['config']['base_java_url'] = "menuaction:'property.uitemplate.hour',"
					."template_id:'{$template_id}'";
				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uitemplate.hour',
								'query' 			=> $this->query,
								'template_id'		=> $template_id
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
									'tab_index' => 4
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 3
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 1
								)
							)
						)
					)
				);
			}
			$uicols = array (
				array(
					'col_name'=>hour_id,		'visible'=>false,	'name'=>hour_id,		'label'=>'',					'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>template_id,	'visible'=>false,	'name'=>template_id,	'label'=>'',					'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>counter,		'visible'=>false,	'name'=>counter,		'label'=>'',					'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>records,		'visible'=>true,	'name'=>record,			'label'=>lang('Record'),		'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>building_part ,	'visible'=>true,	'name'=>building_part,	'label'=>lang('Building part'),	'className'=>'centerClasss',	'sortable'=>true,	'sort_field'=>'building_part','formatter'=>''),
				array(
					'col_name'=>code,			'visible'=>true,	'name'=>'',				'label'=>lang('Code'),			'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>grouping_descr,	'visible'=>true,	'name'=>grouping_descr,	'label'=>lang('Grouping'),		'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>hours_descr,	'visible'=>true,	'name'=>hours_descr,	'label'=>lang('Description'),	'className'=>'leftClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>unit,			'visible'=>true,	'name'=>unit,			'label'=>lang('Unit'),			'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>billperae,		'visible'=>true,	'name'=>billperae,		'label'=>lang('Bill per unit'),	'className'=>'rightClasss',		'sortable'=>true,	'sort_field'=>'billperae',	'formatter'=>'')
				);

			$template_list	= $this->bo->read_template_hour($template_id);
			//_debug_array($template_list);
			$i=0;
			$grouping_descr_old='';			
			while (is_array($template_list) && list(,$template) = each($template_list))
			{

				if($template['grouping_descr']!=$grouping_descr_old)
				{
					$new_grouping = true;
				}
				else
				{
					$new_grouping = false;
				}

				$grouping_descr_old = $template['grouping_descr'];

				if($template['activity_num'])
				{
					$code = $template['activity_num'];
				}
				else
				{
					$code = str_replace("-",$template['tolerance'],$template['ns3420_id']);
				}

				$content[] = array
					(
						'hour_id'			=>	$template['hour_id'],
						'template_id'		=>	$template_id,
						'counter'			=> $i,
						'record'			=> $template['record'],
						'grouping_descr'	=> $template['grouping_descr'],
						'building_part'		=> $template['building_part'],
						'code'				=> $code,
						'hours_descr'		=> $template['remark']!= "" ? $template['hours_descr']."<br>".$template['remark'] : $template['hours_descr'],
						'unit'				=> $template['unit'],
						'billperae'			=> $template['billperae'],
					);
				unset($new_grouping);
				unset($grouping_descr_old);
				unset($code);

				$i++;
			}

			$j=0;
			if (isset($content) && is_array($content))
			{
				foreach($content as $template)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols[$i]['col_name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']		= $template[$uicols[$i]['name']];
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();
			$parameters = array();
			$parameters[] = array('parameter' => array(	array('name'=> 'hour_id',		'source'	=> 'hour_id'),
				array('name'=> 'template_id',	'source'	=> 'template_id')));

			$parameters[] = array('parameter' => array(	array('name'=> 'hour_id',		'source'	=> 'hour_id'),
				array('name'=> 'template_id',	'source'	=> 'template_id'),
				array('name'=> 'delete',		'source'	=> 'template_id')));

			$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_hour')),
					'parameters'	=> $parameters[0]
				);
			$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'delete',
					'text' 			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.hour')),
					'parameters'	=> $parameters[1]
				);
			$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_hour','template_id'=> $template_id)),
				);
			unset($parameters);	

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['name']			= $uicols[$i]['col_name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['label'];
				$datatable['headers']['header'][$i]['visible'] 		= $uicols[$i]['visible'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['formatter']	= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($content);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[1]['col_name']; // name key Column in myColumnDef
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
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . lang('template') . ': ' . lang('view template detail');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'template.hour', 'property' );

			//$this->save_sessiondata();
		}


		function edit_template()
		{
			$template_id 	= phpgw::get_var('template_id', 'int');
			$values		= phpgw::get_var('values');
			$receipt = array();

			$GLOBALS['phpgw']->xslttpl->add_file(array('template'));

			if ($values['save'])
			{
				$values['template_id'] = $template_id;

				if(!isset($receipt['error']) || !$receipt['error'])
				{
					$receipt = $this->bo->save_template($values);

					$template_id=$receipt['template_id'];
				}
			}

			if ($template_id)
			{
				$values = $this->bo->read_single_template($template_id);
				$function_msg = lang('Edit template');
			}
			else
			{
				$function_msg = lang('Add template');
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uitemplate.edit_template',
					'template_id'	=> $template_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.index', 'template_id'=> $template_id)),

					'lang_template_id'			=> lang('Template ID'),
					'value_template_id'			=> $template_id,

					'lang_name'					=> lang('Name'),
					'value_name'				=> $values['name'],

					'lang_save'					=> lang('save'),
					'lang_done'					=> lang('done'),
					'lang_descr'				=> lang('description'),
					'value_descr'				=> $values['descr'],
					'lang_descr_statustext'		=> lang('Enter the description for this template'),
					'lang_done_statustext'		=> lang('Back to the list'),
					'lang_save_statustext'		=> lang('Save the building'),

					'lang_remark'				=> lang('Remark'),
					'value_remark'				=> isset($values['remark']) ? $values['remark'] : '',
					'lang_remark_statustext'	=> lang('Enter additional remarks to the description - if any'),

					'lang_chapter'				=> lang('chapter'),
					'chapter_list'				=> $this->bowo_hour->get_chapter_list('select',$values['chapter_id']),
					'select_chapter'			=> 'values[chapter_id]',
					'lang_no_chapter'			=> lang('Select chapter'),
					'lang_chapter_statustext'	=> lang('Select the chapter (for tender) for this activity.'),
					'lang_add'					=> lang('add a hour'),
					'lang_add_statustext'		=> lang('add a hour to this template'),
					'add_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_hour', 'template_id'=> $template_id))
				);

			$appname	= lang('Workorder template');
			$function_msg	= lang('view ticket detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_template' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_hour()
		{
			$template_id 		= phpgw::get_var('template_id', 'int');
			$activity_id		= phpgw::get_var('activity_id', 'int');
			$hour_id		= phpgw::get_var('hour_id', 'int');
			$values			= phpgw::get_var('values');
			$values['ns3420_id']	= phpgw::get_var('ns3420_id');
			$values['ns3420_descr']	= phpgw::get_var('ns3420_descr');
			$error_id = false;
			$receipt = array();

			$bopricebook	= CreateObject('property.bopricebook');

			$GLOBALS['phpgw']->xslttpl->add_file(array('template'));

			if (isset($values['save']) && $values['save'])
			{
				if(isset($values['copy_hour']) && $values['copy_hour'])
				{
					unset($hour_id);
				}

				$values['hour_id'] = $hour_id;
				if(!isset($values['ns3420_descr']) || !$values['ns3420_descr'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a description!'));
					$error_id=true;
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_hour($values,$template_id);
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

			$template = $this->bo->read_single_template($template_id);

			if($error_id)
			{
				unset($values['hour_id']);
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uitemplate.edit_hour',
					'template_id'	=> $template_id,
					'hour_id'	=> $hour_id
				);

			$config				= CreateObject('phpgwapi.config','property');
			$config->read();
			
			$_filter_buildingpart = array();
			$filter_buildingpart = isset($config->config_data['filter_buildingpart']) ? $config->config_data['filter_buildingpart'] : array();
			
			if($filter_key = array_search('.project', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.hour', 'template_id'=> $template_id)),
					'lang_template'				=> lang('template'),
					'value_template_id'			=> $template['template_id'],
					'value_template_name'			=> $template['name'],

					'lang_hour_id'				=> lang('Hour ID'),
					'value_hour_id'				=> $hour_id,

					'lang_copy_hour'			=> lang('Copy hour ?'),
					'lang_copy_hour_statustext'		=> lang('Choose Copy Hour to copy this hour to a new hour'),

					'lang_activity_num'			=> lang('Activity code'),
					'value_activity_num'		=> isset($values['activity_num']) ? $values['activity_num'] : '',
					'value_activity_id'			=> isset($values['activity_id']) ? $values['activity_id'] : '',

					'lang_unit'					=> lang('Unit'),
					'lang_save'					=> lang('save'),
					'lang_done'					=> lang('done'),
					'lang_descr'				=> lang('description'),
					'value_descr'				=> isset($values['hours_descr']) ? $values['hours_descr'] : '',
					'lang_descr_statustext'		=> lang('Enter the description for this activity'),
					'lang_done_statustext'		=> lang('Back to the list'),
					'lang_save_statustext'		=> lang('Save the building'),

					'lang_remark'				=> lang('Remark'),
					'value_remark'				=> isset($values['remark']) ? $values['remark'] : '',
					'lang_remark_statustext'	=> lang('Enter additional remarks to the description - if any'),

					'lang_quantity'				=> lang('quantity'),
					'value_quantity'			=> isset($values['quantity']) ? $values['quantity'] : '',
					'lang_quantity_statustext'	=> lang('Enter quantity of unit'),

					'lang_billperae'			=> lang('Cost per unit'),
					'value_billperae'			=> isset($values['billperae']) ? $values['billperae'] : '',
					'lang_billperae_statustext'	=> lang('Enter the cost per unit'),

					'lang_total_cost'			=> lang('Total cost'),
					'value_total_cost'			=> isset($values['cost']) ? $values['cost'] : '',
					'lang_total_cost_statustext'=> lang('Enter the total cost of this activity - if not to be calculated from unit-cost'),

					'lang_dim_d'				=> lang('Dim D'),
					'dim_d_list'				=> $bopricebook->get_dim_d_list(isset($values['dim_d']) ? $values['dim_d'] : ''),
					'select_dim_d'				=> 'values[dim_d]',
					'lang_no_dim_d'				=> lang('No Dim D'),
					'lang_dim_d_statustext'		=> lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),

					'lang_unit'					=> lang('Unit'),
					'unit_list'					=> $bopricebook->get_unit_list(isset($values['unit']) ? $values['unit'] : ''),
					'select_unit'				=> 'values[unit]',
					'lang_no_unit'				=> lang('Select Unit'),
					'lang_unit_statustext'		=> lang('Select the unit for this activity.'),

					'lang_chapter'				=> lang('chapter'),
					'chapter_list'				=> $this->bowo_hour->get_chapter_list('select',$template['chapter_id']),
					'select_chapter'			=> 'values[chapter_id]',
					'lang_no_chapter'			=> lang('Select chapter'),
					'lang_chapter_statustext'	=> lang('Select the chapter (for tender) for this activity.'),

					'lang_tolerance'			=> lang('tolerance'),
					'tolerance_list'			=> $this->bowo_hour->get_tolerance_list(isset($values['tolerance_id'])?$values['tolerance_id']:''),
					'select_tolerance'			=> 'values[tolerance_id]',
					'lang_no_tolerance'			=> lang('Select tolerance'),
					'lang_tolerance_statustext'	=> lang('Select the tolerance for this activity.'),

					'lang_grouping'				=> lang('grouping'),
					'grouping_list'				=> $this->bo->get_grouping_list(isset($values['grouping_id']) ? $values['grouping_id']:'',isset($template_id) ? $template_id:''),
					'select_grouping'			=> 'values[grouping_id]',
					'lang_no_grouping'			=> lang('Select grouping'),
					'lang_grouping_statustext'	=> lang('Select the grouping for this activity.'),

					'lang_new_grouping'			=> lang('New grouping'),
					'lang_new_grouping_statustext'	=> lang('Enter a new grouping for this activity if not found in the list'),

					'building_part_list'			=> array('options' => $this->bocommon->select_category_list(array('type'=> 'building_part','selected' =>$values['building_part_id'], 'order' => 'id', 'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),

					'ns3420_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.ns3420')),
					'lang_ns3420'				=> lang('NS3420'),
					'value_ns3420_id'			=> $values['ns3420_id'],
					'lang_ns3420_statustext'	=> lang('Select a standard-code from the norwegian standard'),
					'currency'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']

				);

			$appname	= lang('Workorder template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_hour' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			$id	= phpgw::get_var('id', 'int');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return "id ".$id." ".lang("has been deleted");
			}

			$confirm = phpgw::get_var('confirm', 'bool', 'POST');
			$link_data = array
				(
					'menuaction' => 'property.uitemplate.index'
				);
			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.delete', 'id'=> $id)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname		= lang('Workorder template');
			$function_msg		= lang('delete template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
