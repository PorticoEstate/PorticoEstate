<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	phpgw::import_class('property.uicommon');

	/**
	 * Description
	 * @package property
	 */

	class property_uievent extends property_uicommon
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;
		var $location_info;

		var $public_functions = array
			(
				'index'		=> true,
				'view'		=> true,
				'edit'		=> true,
				'delete'	=> true,
				'schedule'	=> true,
				'schedule2'	=> true,
				'schedule_week'	=> true
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boevent',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->custom				= & $this->bo->custom;

			$this->location_info		= $this->bo->location_info;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $this->location_info['menu_selection'];
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= phpgw::get_var('location');
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
			$this->location_id			= $this->bo->location_id;
			$this->user_id				= $this->bo->user_id;
			$this->status_id			= $this->bo->status_id;
		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'			=> $this->start,
					'query'			=> $this->query,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'allrows'		=> $this->allrows,
					'location_id'	=> $this->location_id,
					'user_id'		=> $this->user_id,
					'status_id'		=> $this->status_id
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			//_debug_array($_REQUEST);
			$this->acl_location = '.scheduled_events';
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::scheduled_events";

			$values = phpgw::get_var('values');
			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));


			$receipt = array();
			if($values && $this->acl_edit)
			{
				$receipt = $this->bo->update_receipt($values);
			}
			$this->save_sessiondata();

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uievent.index',
						'location_id'	=> $this->location_id,
						'user_id'		=> $this->user_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uievent.index',"
					."location_id:'{$this->location_id}',"
					."user_id:'{$this->user_id}',"
					."status_id:'{$this->status_id}'";					

				$values_combo_box = array();

				$values_combo_box[0]  = $this->bo->get_event_location();

				$default_value = array ('id'=> -1, 'name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->get_user_list_right2('filter',2,$this->user_id,$this->acl_location);
				array_unshift ($values_combo_box[1],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine tasks')));
				$default_value = array('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = array();
				array_unshift ($values_combo_box[2],array ('id'=>'exception','name'=> lang('exception')));
				array_unshift ($values_combo_box[2],array ('id'=>'closed','name'=> lang('closed')));
				array_unshift ($values_combo_box[2],array ('id'=>'all','name'=> lang('all')));
				array_unshift ($values_combo_box[2],array ('id'=>'open','name'=> lang('open')));

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uievent.index',
								'type'			=> $type,
								'type_id'		=> $type_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	CATEGORY
									'id' => 'btn_location_id',
									'name' => 'location_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	USER
									'id' => 'btn_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
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
								( // boton SAVE
									'id'	=> 'btn_save',
									//'name' => 'save',
									'value'	=> lang('save'),
									'tab_index' => 6,
									'type'	=> 'button'
								),
								array
								( //hidden start_date
									'type' => 'hidden',
									'id' => 'start_date',
									'value' => $start_date
								),
								array
								( //hidden end_date
									'type' => 'hidden',
									'id' => 'end_date',
									'value' => $end_date
								),
								array
								(//for link "None",
									'type'=> 'label_date'
								),
								array
								(//for link "Date search",
									'type'=> 'link',
									'id'  => 'btn_data_search',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uiproject.date_search')
									)."','','width=350,height=250')",
									'value' => lang('Date search'),
									'tab_index' => 6
								),
								array
								( //button     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 5
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => $this->query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 4
								),
								array
								( //place holder for selected events
									'type'	=> 'hidden',
									'id'	=> 'event',
									'value'	=> ''
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
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								)
							)
						)
					)
				);				
				$dry_run = true;
			}

			$values = $this->bo->read($dry_run);
			$uicols = array();$this->bo->uicols;

			$uicols['name'][]		= 'schedule_time';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'location';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'location_item_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'attrib_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'id';
			$uicols['descr'][]		= lang('id');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'id';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'date';
			$uicols['descr'][]		= lang('date');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'date';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'account_lid';
			$uicols['descr'][]		= lang('account');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'account_lid';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'descr';
			$uicols['descr'][]		= lang('Descr');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'exception';
			$uicols['descr'][]		= lang('exception');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'FormatterCenter';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'receipt_date';
			$uicols['descr'][]		= lang('receipt date');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'location_name';
			$uicols['descr'][]		= lang('location name');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'url';
			$uicols['descr'][]		= lang('url');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= 'link';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'select';
			$uicols['descr'][]		= lang('select');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'myFormatterCheck';
			$uicols['input_type'][]	= '';

			$j = 0;
			$count_uicols_name = count($uicols['name']);

			foreach($values as $entry)
			{
				for ($k=0;$k<$count_uicols_name;$k++)
				{
					$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
					$datatable['rows']['row'][$j]['column'][$k]['value']			= $entry[$uicols['name'][$k]];
					if($uicols['format'][$k]=='link' &&  $entry[$uicols['name'][$k]])
					{
						$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
						$datatable['rows']['row'][$j]['column'][$k]['value']		= lang('link');
						$datatable['rows']['row'][$j]['column'][$k]['link']			= $entry[$uicols['name'][$k]];
						$datatable['rows']['row'][$j]['column'][$k]['target']	   = '_blank';
					}
				}
				$j++;
			}

			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'location',
							'source'	=> 'location'
						),
						array
						(
							'name'		=> 'attrib_id',
							'source'	=> 'attrib_id'
						),
						array
						(
							'name'		=> 'item_id',
							'source'	=> 'location_item_id'
						),
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						)
					)
				);

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text' 			=> lang('edit serie'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'property.uievent.edit',
							'type'				=> $type,
							'type_id'			=> $type_id,
							'target'			=> '_blank'
						)),
						'parameters'	=> $parameters
					);
			}

/*
			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'delete',
					'statustext' 	=> lang('delete the actor'),
					'text'			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uievent.delete',
											'type'			=> $type,
											'type_id'		=> $type_id
										)),
					'parameters'	=> $parameters
				);
			}
 */
			unset($parameters);

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('add'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uievent.edit',
							'type'			=> $type,
							'type_id'		=> $type_id
						))
					);
			}

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				$datatable['headers']['header'][$i]['formatter'] 		= $uicols['formatter'][$i] ? $uicols['formatter'][$i] : '""';
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= $uicols['input_type'][$i]!='hidden';
				$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];
				$datatable['headers']['header'][$i]['sort_field']   	= $uicols['sort_field'][$i];
				$datatable['headers']['header'][$i]['format'] 			= $uicols['format'][$i];
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($values);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			= lang('scheduled events');
			$function_msg		= lang('list %1', $appname);

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			= 'date'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= $this->order; // name of column of Database
				$datatable['sorting']['sort'] 			= $this->sort; // ASC / DESC
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

			if(isset($receipt) && is_array($receipt) && count($receipt))
			{
				$json['message'][] = $receipt;
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

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'event.index', 'property' );
		}

		function edit()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			if(!$this->acl_add)
			{
				$this->bocommon->no_access();
				return;
			}

			$location					= phpgw::get_var('location');
			$attrib_id					= phpgw::get_var('attrib_id');
			$item_id					= phpgw::get_var('item_id');//might be bigint
			$id							= phpgw::get_var('id', 'int');
			$values						= phpgw::get_var('values');
			$values['responsible_id']	= phpgw::get_var('contact', 'int', 'POST');

			//			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'general.edit.' . $type;

			$GLOBALS['phpgw']->xslttpl->add_file(array('event'));
			$receipt = array();

			if (is_array($values))
			{
				$values['location_id']	=  $GLOBALS['phpgw']->locations->get_id('property', $location);
				$values['attrib_id']	=  $attrib_id;
				$values['item_id']		=  $item_id;
				$attrib = $this->custom->get('property', $location, $attrib_id);
				$field_name = $attrib ? $attrib['column_name'] : $attrib_id;

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					if(!isset($values['descr']) || !$values['descr'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a description'));									
					}
					if(!isset($values['responsible_id']) || !$values['responsible_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a responsible'));									
					}
					if(!isset($values['action']) || !$values['action'])
					{
		//				$receipt['error'][]=array('msg'=>lang('Please select an action'));									
					}

/*					if(isset($values['repeat_day']))
					{
						$values['repeat_interval'] = 0;
					}
 */
					if($id)
					{
						$values['id']=$id;
					}
					else
					{
						$id =	$values['id'];
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save($values,$action);

						$js = "opener.document.form.{$field_name}.value = '{$receipt['id']}';\n";
						$js .= "opener.document.form.{$field_name}_descr.value = '{$values['descr']}';\n";

						if (isset($values['save']) && $values['save'])
						{
							$js .= "window.close();";
						}
						$GLOBALS['phpgw']->js->add_event('load', $js);
						$id = $receipt['id'];
					}
					else
					{
						unset($values['id']);
						$id = '';
					}

				}
				else if ((isset($values['delete']) && $values['delete']))
				{
					$attrib = $this->custom->get('property', $location, $attrib_id);
					$js = "opener.document.form.{$field_name}.value = '';\n";
					$js .= "opener.document.form.{$field_name}_descr.value = '';\n";
					if($this->delete($id))
					{
						$GLOBALS['phpgw']->js->add_event('load', $js);
						unset($values);
						unset($id);
					}
				}
				else if ((isset($values['cancel']) && $values['cancel']))
				{
					$GLOBALS['phpgw']->js->add_event('load', "window.close();");
				}
				unset($js);
				unset($attrib);
			}

			if ($id)
			{
				$values = $this->bo->read_single($id);
				$function_msg = lang('edit event');
			}
			else
			{
				$function_msg = lang('add event');
				$values['enabled'] = true;
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uievent.edit',
					'location'		=> $location,
					'attrib_id'		=> $attrib_id,
					'item_id'		=> $item_id,
					'id'			=> $id
				);

			$link_schedule_data = array
				(
					'menuaction'	=> 'property.uievent.schedule_week',
					'location'		=> $location,
					'attrib_id'		=> $attrib_id,
					'item_id'		=> $item_id,
					'id'			=> $id
				);

			//_debug_array($link_data);

			$tabs = array();

			phpgwapi_yui::tabview_setup('general_edit_tabview');
			$tabs['general']	= array('label' => lang('general'), 'link' => '#general');
			$tabs['repeat']		= array('label' => lang('repeat'), 'link' => '#repeat');
			if ($id)
			{
				$tabs['plan']		= array('label' => lang('plan'), 'link' => '#plan');
			}

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id'		=> $values['responsible_id'],
				'field'				=> 'contact',
				'type'				=> 'form'));

			$data = array
				(
					'contact_data'					=> $contact_data,
					'link_schedule'					=> $GLOBALS['phpgw']->link('/index.php',$link_schedule_data),

					'lang_start_date_statustext'	=> lang('Select the date for the event'),
					'lang_start_date'				=> lang('date'),
					'value_start_date'				=> $values['start_date'],

					'value_enabled'					=> isset($values['enabled']) ? $values['enabled'] : '',
					'lang_enabled'					=> lang('enabled'),
					'lang_end_date_statustext'		=> lang('Select the estimated end date for the event'),
					'lang_end_date'					=> lang('end date'),
					'value_end_date'				=> $values['end_date'],
					'repeat_type'					=> $this->bo->get_rpt_type_list(isset($values['repeat_type']) ? $values['repeat_type'] : ''),
					'lang_repeat_type'				=> lang('repeat type'),

					'repeat_day'					=> $this->bo->get_rpt_day_list(isset($values['repeat_day']) ? $values['repeat_day'] : ''),
					'lang_repeat_day'				=> lang('repeat day'),

					'lang_repeat_interval'			=> lang('interval'),
					'value_repeat_interval'			=> isset($values['repeat_interval']) ? $values['repeat_interval'] : 0,
					'lang_repeat_interval_statustext'=> lang('interval'),

					'lang_responsible'				=> lang('responsible'),
					'responsible'					=> $this->bo->get_responsible(isset($values['responsible']) ? $values['responsible'] : ''),

					'lang_action'					=> lang('action'),
					'action'						=> $this->bo->get_action(isset($values['action']) ? $values['action'] : ''),

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uievent.index', 'type'=> $type, 'type_id'=> $type_id)),
					'lang_id'						=> lang('ID'),
					'lang_descr'					=> lang('Description'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'value_id'						=> isset($values['id']) ? $values['id'] : '',

					'lang_next_run'					=> lang('next run'),
					'value_next_run'				=> isset($values['next']) ? $values['next'] : '',				
					'value_descr'					=> $values['descr'],
					'lang_descr_text'				=> lang('Enter a description of the record'),
					'lang_save_text'				=> lang('Save the record'),
					'lang_apply_statustext'			=> lang('Apply the values'),
					'lang_cancel_statustext'		=> lang('Leave the actor untouched and return back to the list'),
					'lang_save_statustext'			=> lang('Save the actor and return back to the list'),

					'lang_delete'					=> lang('delete'),
					'lang_delete_text'				=> lang('delete the record'),
					'lang_delete_statustext'		=> lang('delete the record'),

					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 60,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 10,
					'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general'),
				);

			$schedule = array();

			if ($id)
			{
				$schedule = $this->schedule2($id);
			}
			else
			{
				$data['td_count']		= '""';
				$data['base_java_url']	= '""';
				$data['property_js']	= '""';
			}

			$data = array_merge($schedule, $data);
			$appname	=  lang('event');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete($id)
		{
			if(!$this->acl_delete)
			{
				$this->bocommon->no_access();
				return;
			}

			return $this->bo->delete($id);
		}


		public function schedule()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$id = phpgw::get_var('id', 'int', 'GET');

			$resource = $this->bo->read_single($id);
			$resource['cols'][] = array('label' => lang('date'), 'key' => 'date');
			$resource['cols'][] = array('label' => lang('exception'), 'key' => 'exception');

			$lang['resource_schedule'] = lang('Resource schedule');
			$lang['schedule'] = lang('Schedule');
			$lang['time'] = lang('Time');

			self::add_javascript('property', 'yahoo', 'schedule.js');
			self::render_template('event_schedule', array('resource' => $resource, 'lang' => $lang));
		}

		function schedule2($id = 0)
		{
			if(!$id)
			{
				$id = phpgw::get_var('id', 'int');
			}
			$values			= phpgw::get_var('values');

			if (is_array($values))
			{
				if($values['alarm'])
				{
					$receipt = $this->bo->set_exceptions(
						array
						(
							'event_id'		=> $id,
							'alarm'			=> array_keys($values['alarm']),
							'set_exception'	=> !!$values['disable_alarm'],
							'enable_alarm'	=> !!$values['enable_alarm'],
							'set_receipt'	=> !!$values['set_receipt'],
							'delete_receipt'=> !!$values['delete_receipt']
						)
					);
				}
			}

			//_debug_array($_REQUEST);
			//------------------------------get data
			$event = $this->bo->so->read_single2($id);

			$dateformat	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$i = 1;
			$values = array();
			foreach($event as $entry)
			{
				$values[] = array
					(
						'number'			=> $i,
						'time'				=> $GLOBALS['phpgw']->common->show_date($entry['schedule_time'],$dateformat),
						'performed'			=> $GLOBALS['phpgw']->common->show_date($entry['receipt_date'],$dateformat),
						'alarm_id'			=> $GLOBALS['phpgw']->common->show_date($entry['schedule_time'],'Ymd'),
						'enabled'			=> isset($entry['exception']) && $entry['exception']==true ? '' : 1,
						'location_id' 		=> $entry['location_id'],
						'location_item_id'	=> $entry['location_item_id'],
						'remark'				=> $entry['descr'],
						'url'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'booking.uievent.show', 'location_id' => $entry['location_id'], 'location_item_id' => $entry['location_item_id']))
					);
				$i++;
			}


			//------------------------------end get data

			$link_data = array
				(
					'menuaction'	=> 'property.uis_agreement.edit',
					'id'		=> $id,
					'role'		=> $this->role
				);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$link_download = array
				(
					'menuaction'	=> 'property.uis_agreement.download',
					'id'		=> $id
				);

			$tabs = array();


			//----------JSON CODE ----------------------------------------------


			//---GET ALARM
			if( phpgw::get_var('phpgw_return_as') == 'json')
			{
				if(count($values))
				{
					_debug_array($values);
					return json_encode($values);
				}
				else
				{
					return "";
				}
			}

			//--------------------JSON code-----


			//------- alarm--------
			$datavalues[0] = array
				(
					'name'   => "0",
					'values'   => json_encode($values),
					'total_records' => count($values),
					'is_paginator' => 0,
					'permission'=> '""',
					'footer'  => 1
				);

			$myColumnDefs[0] = array
				(
					'name'   => "0",
					'values'  => json_encode(array( 
						array('key' => 'number', 'label'=>'#', 'sortable'=>true,'resizeable'=>true,'width'=>20),
						array('key' => 'time', 'label'=>lang('plan'), 'sortable'=>true,'resizeable'=>true,'width'=>80),
						array('key' => 'performed', 'label'=>lang('performed'), 'sortable'=>true,'resizeable'=>true,'width'=>80),					
						array('key' => 'remark', 'label'=>lang('remark'), 'sortable'=>true,'resizeable'=>true,'width'=>140),					
						array('key' => 'enabled','label'=> lang('enabled'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterCenter','width'=>30),
						array('key' => 'alarm_id','label'=> 'alarm_id','sortable'=>true,'resizeable'=>true,'hidden'=>false),
						array('key' => 'select','label'=> lang('select'), 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>30)))
					);

			$myButtons[0] = array
				(
					'name'   => "0",
					'values'  => json_encode(array( 
						array('id' =>'values[set_receipt]','type'=>'buttons', 'value'=>'Receipt', 'label'=> lang('Receipt'), 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
						array('id' =>'values[delete_receipt]','type'=>'buttons', 'value'=>'Delete Receipt', 'label'=> lang('Delete receipt'), 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
						array('id' =>'values[enable_alarm]','type'=>'buttons', 'value'=>'Enable', 'label'=> lang('enable'), 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
						array('id' =>'values[disable_alarm]','type'=>'buttons', 'value'=>'Disable', 'label'=>lang('disable'), 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
					))
				);

			$td_count = 0;

			//--------------------------------------------JSON CODE------------

			$link_data = array
				(
					'menuaction'	=> 'property.uievent.schedule2',
					'id'		=>		$id
				);


			$data = array
				(
					'td_count'					=> 6,
					'property_js'				=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'				=> "{menuaction:'property.uievent.schedule2',id:'{$id}'}",
					'datatable'					=> $datavalues,
					'myColumnDefs'				=> $myColumnDefs,
					'myButtons'					=> $myButtons,

					'value_location_id'			=> $event['location_id'],
					'value_location_item_id'	=> $event['location_item_id'],


					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'tabs'						=> phpgwapi_yui::tabview_generate($tabs, $active_tab)
				);

			//_debug_array($data);die;

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('schedule');

			$GLOBALS['phpgw']->xslttpl->add_file(array('event'));
			//		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('schedule' => $data));
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'event.schedule', 'property' );
			return $data;
		}

		public function schedule_week()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$id = phpgw::get_var('id', 'int', 'GET');
			$resource = $this->bo->init_schedule_week($id, 'property.uievent', 'property.uievent');

			$lang['resource_schedule'] = lang('Resource schedule');
			$lang['prev_week'] = lang('Previous week');
			$lang['next_week'] = lang('Next week');
			$lang['week'] = lang('Week');
			$lang['buildings'] = lang('Buildings');
			$lang['schedule'] = lang('Schedule');
			$lang['time'] = lang('Time');

			self::add_javascript('property', 'yahoo', 'schedule.js');
			self::render_template('event_schedule_week', array('resource' => $resource, 'lang' => $lang));
		}
	}
