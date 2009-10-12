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
			'event_schedule_week'=> true
		);

		function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boevent',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->custom				= & $this->bo->custom;

			$this->location_info		= $this->bo->location_info;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $this->location_info['menu_selection'];
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.entity.1.1';//$this->location_info['acl_location'];
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
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$type		= phpgw::get_var('type');
			$type_id	= phpgw::get_var('type_id', 'int');
			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$type}_{$type_id}");
			$this->save_sessiondata();

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "general.index.{$type}";

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uievent.index',
					'type'		=> $type,
					'type_id'		=> $type_id
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uievent.index',"
	    												."type:'{$type}',"
	    												."type_id:'{$type_id}'";

				$link_data = array
				(
					'menuaction'	=> 'property.uievent.index',
					'type'		=> $type,
					'type_id'		=> $type_id
				);

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
								( //button     SEARCH
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
									'value'    => $this->query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 6
								)
							)
						)
					)
				);
				$dry_run = true;
			}

			$values = $this->bo->read();
			$uicols = $this->bo->uicols;

/*			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('category ID');
			$uicols['name'][1]	= 'descr';
			$uicols['descr'][1]	= lang('Descr');
*/
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($values) AND is_array($values))
			{
				foreach($values as $category_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']				= $category_entry[$uicols['name'][$k]];
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

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'edit',
					'statustext' 	=> lang('edit the actor'),
					'text'			=> lang('edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'		=> 'property.uievent.edit',
											'type'				=> $type,
											'type_id'			=> $type_id
										)),
					'parameters'	=> $parameters
				);
				$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('open edit in new window'),
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
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['sortable']			= true;
						$datatable['headers']['header'][$i]['sort_field']   	= $uicols['name'][$i];
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($values);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			=  $this->location_info['name'];
			$function_msg		= lang('list %1', $appname);

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'category.index', 'property' );
		}

		function edit()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			if(!$this->acl_add)
			{
				$this->bocommon->no_access();
				return;
			}

			$location	= phpgw::get_var('location');
			$attrib_id	= phpgw::get_var('attrib_id');
			$item_id	= phpgw::get_var('item_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

		
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
					if(!isset($values['responsible']) || !$values['responsible'])
					{
		//				$receipt['error'][]=array('msg'=>lang('Please select a responsible'));									
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
				else
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
				'menuaction'	=> 'property.uievent.schedule',
				'location'		=> $location,
				'attrib_id'		=> $attrib_id,
				'item_id'		=> $item_id,
				'id'			=> $id
			);

//_debug_array($link_data);

			$tabs = array();

			phpgwapi_yui::tabview_setup('general_edit_tabview');
			$tabs['general']	= array('label' => lang('general'), 'link' => '#general');
			$tabs['repeat']	= array('label' => lang('repeat'), 'link' => '#repeat');

/*
			$GLOBALS['phpgw']->jscal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jscal->add_listener('values_end_date');
			$start_date = $GLOBALS['phpgw']->jscal->input('values_start_date', $date, $format = 'input', lang('start date'));
*/
			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'link_schedule'					=> $GLOBALS['phpgw']->link('/index.php',$link_schedule_data),
				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),

				'lang_start_date_statustext'	=> lang('Select the date for the event'),
				'lang_start_date'				=> lang('date'),
				'value_start_date'				=> $values['start_date'],
	//			'start_date'					=> $start_date,

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

		$data = '{"ResultSet":{"totalResultsAvailable":13,"Result":[{"time":"00:00-15:30","_from":"00:00","_to":"15:30","link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"15:30-16:30","_from":"15:30","_to":"16:30","Tue":{"id":759,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"17:00","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-06","wday":"Tue"},"Wed":{"id":771,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"16:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-07","wday":"Wed"},"Fri":{"id":792,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"18:00","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-09","wday":"Fri"},"Mon":{"id":750,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"16:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[12,11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Thu":{"id":784,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"15:30","to_":"19:15","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-08","wday":"Thu"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"16:30-17:00","_from":"16:30","_to":"17:00","Tue":{"id":759,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"17:00","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-06","wday":"Tue"},"Fri":{"id":792,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"18:00","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-09","wday":"Fri"},"Thu":{"id":784,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"15:30","to_":"19:15","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-08","wday":"Thu"},"Wed":{"id":770,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"16:30","to_":"18:30","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-07","wday":"Wed"},"Mon":{"id":749,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"16:30","to_":"18:30","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-05","wday":"Mon"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"17:00-18:00","_from":"17:00","_to":"18:00","Fri":{"id":792,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"15:30","to_":"18:00","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-09","wday":"Fri"},"Thu":{"id":784,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"15:30","to_":"19:15","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-08","wday":"Thu"},"Wed":{"id":770,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"16:30","to_":"18:30","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-07","wday":"Wed"},"Mon":{"id":749,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"16:30","to_":"18:30","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-05","wday":"Mon"},"Tue":{"id":757,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"17:00","to_":"19:00","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-06","wday":"Tue"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"18:00-18:30","_from":"18:00","_to":"18:30","Thu":{"id":784,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"15:30","to_":"19:15","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-08","wday":"Thu"},"Wed":{"id":770,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"16:30","to_":"18:30","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-07","wday":"Wed"},"Mon":{"id":749,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"16:30","to_":"18:30","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-05","wday":"Mon"},"Tue":{"id":757,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"17:00","to_":"19:00","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-06","wday":"Tue"},"Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"18:30-19:00","_from":"18:30","_to":"19:00","Thu":{"id":784,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"15:30","to_":"19:15","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-08","wday":"Thu"},"Tue":{"id":757,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"17:00","to_":"19:00","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-06","wday":"Tue"},"Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"Wed":{"id":2870,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"18:30","to_":"19:30","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-07","wday":"Wed"},"Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"19:00-19:15","_from":"19:00","_to":"19:15","Thu":{"id":784,"active":1,"application_id":null,"organization_id":20,"season_id":11,"from_":"15:30","to_":"19:15","cost":0,"completed":0,"organization_name":"Tertnes H\u00e5ndball Elite","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Tertnes H\u00e5ndball Elite","type":"allocation","date":"2009-10-08","wday":"Thu"},"Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Wed":{"id":2870,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"18:30","to_":"19:30","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-07","wday":"Wed"},"Tue":{"id":755,"active":1,"application_id":null,"organization_id":29,"season_id":11,"from_":"19:00","to_":"22:30","cost":0,"completed":0,"organization_name":"NHF Region Vest","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"NHF Region Vest","type":"allocation","date":"2009-10-06","wday":"Tue"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"19:15-19:30","_from":"19:15","_to":"19:30","Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Wed":{"id":2870,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"18:30","to_":"19:30","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-07","wday":"Wed"},"Tue":{"id":755,"active":1,"application_id":null,"organization_id":29,"season_id":11,"from_":"19:00","to_":"22:30","cost":0,"completed":0,"organization_name":"NHF Region Vest","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"NHF Region Vest","type":"allocation","date":"2009-10-06","wday":"Tue"},"Thu":{"id":778,"active":1,"application_id":null,"organization_id":24,"season_id":11,"from_":"19:15","to_":"20:15","cost":0,"completed":0,"organization_name":"Nornen H\u00e5ndball","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Nornen H\u00e5ndball","type":"allocation","date":"2009-10-08","wday":"Thu"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"19:30-20:15","_from":"19:30","_to":"20:15","Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Tue":{"id":755,"active":1,"application_id":null,"organization_id":29,"season_id":11,"from_":"19:00","to_":"22:30","cost":0,"completed":0,"organization_name":"NHF Region Vest","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"NHF Region Vest","type":"allocation","date":"2009-10-06","wday":"Tue"},"Thu":{"id":778,"active":1,"application_id":null,"organization_id":24,"season_id":11,"from_":"19:15","to_":"20:15","cost":0,"completed":0,"organization_name":"Nornen H\u00e5ndball","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Nornen H\u00e5ndball","type":"allocation","date":"2009-10-08","wday":"Thu"},"Wed":{"id":765,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"19:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-07","wday":"Wed"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"20:15-21:15","_from":"20:15","_to":"21:15","Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Tue":{"id":755,"active":1,"application_id":null,"organization_id":29,"season_id":11,"from_":"19:00","to_":"22:30","cost":0,"completed":0,"organization_name":"NHF Region Vest","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"NHF Region Vest","type":"allocation","date":"2009-10-06","wday":"Tue"},"Wed":{"id":765,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"19:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-07","wday":"Wed"},"Thu":{"id":777,"active":1,"application_id":null,"organization_id":18,"season_id":11,"from_":"20:15","to_":"21:15","cost":0,"completed":0,"organization_name":"Bergen H\u00e5ndballklubb","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Bergen H\u00e5ndballklubb","type":"allocation","date":"2009-10-08","wday":"Thu"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"21:15-22:00","_from":"21:15","_to":"22:00","Fri":{"id":788,"active":1,"application_id":null,"organization_id":66,"season_id":11,"from_":"18:00","to_":"22:00","cost":0,"completed":0,"organization_name":"Hordaland Bedrift Idrettskrets","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Hordaland Bedrift Idrettskrets","type":"allocation","date":"2009-10-09","wday":"Fri"},"Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Tue":{"id":755,"active":1,"application_id":null,"organization_id":29,"season_id":11,"from_":"19:00","to_":"22:30","cost":0,"completed":0,"organization_name":"NHF Region Vest","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"NHF Region Vest","type":"allocation","date":"2009-10-06","wday":"Tue"},"Wed":{"id":765,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"19:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-07","wday":"Wed"},"Thu":{"id":775,"active":1,"application_id":null,"organization_id":63,"season_id":11,"from_":"21:15","to_":"22:30","cost":0,"completed":0,"organization_name":"Baune Spkl.","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Baune Spkl.","type":"allocation","date":"2009-10-08","wday":"Thu"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"resource":"Bane 1","resource_id":11,"time":"22:00-22:30","_from":"22:00","_to":"22:30","Mon":{"id":744,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"18:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-05","wday":"Mon"},"Tue":{"id":755,"active":1,"application_id":null,"organization_id":29,"season_id":11,"from_":"19:00","to_":"22:30","cost":0,"completed":0,"organization_name":"NHF Region Vest","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"NHF Region Vest","type":"allocation","date":"2009-10-06","wday":"Tue"},"Wed":{"id":765,"active":1,"application_id":null,"organization_id":8,"season_id":11,"from_":"19:30","to_":"22:30","cost":0,"completed":0,"organization_name":"\u00c5rstad IL","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"\u00c5rstad IL","type":"allocation","date":"2009-10-07","wday":"Wed"},"Thu":{"id":775,"active":1,"application_id":null,"organization_id":63,"season_id":11,"from_":"21:15","to_":"22:30","cost":0,"completed":0,"organization_name":"Baune Spkl.","building_id":"8","season_name":"Innend\u00f8rs Idrett 2009\/2010","resources":[11],"name":"Baune Spkl.","type":"allocation","date":"2009-10-08","wday":"Thu"},"link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"},{"time":"22:30-00:00","_from":"22:30","_to":"00:00","link":"\/~sn5607\/dev-sigurd-2\/index.php?menuaction=booking.uibooking.show&amp;click_history=5951568367836dc85c92b13f4f15ef83"}]}}';
//	_debug_array(json_decode($data));die();

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$resource = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'property.uievent', 'property.uievent');

			$lang['resource_schedule'] = lang('Resource schedule');
			$lang['prev_week'] = lang('Previous week');
			$lang['next_week'] = lang('Next week');
			$lang['week'] = lang('Week');
			$lang['buildings'] = lang('Buildings');
			$lang['schedule'] = lang('Schedule');
			$lang['time'] = lang('Time');

			self::add_javascript('property', 'yahoo', 'schedule.js');
			self::render_template('event_schedule', array('resource' => $resource, 'lang' => $lang));
		}

		public function event_schedule_week()
		{
//		    $date = new DateTime(phpgw::get_var('date')); Use this one when moving to php 5.3

			$datetime = CreateObject('phpgwapi.datetime');
			$date = $datetime->convertDate(phpgw::get_var('date'), 'Y-m-d', $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$timestamp = $datetime->date_to_timestamp($date);
	    
			$schedules = $this->bo->event_schedule_week(phpgw::get_var('resource_id', 'int'), $timestamp);

			$total_records = 0;
			foreach($schedules as $_date => $set)
			{
				if(count($set) > $total_records)
				{
					$total_records = count($set);
				}
			}

			$lang_exception	 = lang('exception');
			$values = array();
			for($i = 0; $i < $total_records; $i++)
			{
				$values[$i] = array
				(
					'resource'			=> 'descr',
					'resource_id'		=> 11,
					'time'				=> $i+1,
					'_from'				=> '16:30',
					'_to'				=> '17:00'
				);

				foreach($schedules as $_date => $set)
				{
					$__date = substr($_date,0,4) . '-' . substr($_date,4,2) . '-' . substr($_date,6,2);
					$date = new DateTime($__date);
					$day_of_week = $date->format('D');
					$values[$i][$day_of_week] = array
					(
						'exception' => $set[$i]['exception'],
						'lang_exception' => $lang_exception,
						'type' => 'event',
						'name' => $set[$i]['descr'],
						'link' => $this->link(array('menuaction' => 'booking.uievent.show', 'location_id' => $set[$i]['location_id'], 'location_item_id' => $set[$i]['location_item_id']))
					);
				}
			}

			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $total_records, 
					"Result" => $values
				)
			);
//_debug_array($data);die();
			return $data;

		}
	}

