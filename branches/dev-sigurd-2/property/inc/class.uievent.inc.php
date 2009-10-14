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
			'schedule3'	=> true,	
			'schedule_week'	=> true
		);

		function __construct()
		{
//			parent::__construct();
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



		function schedule3()
		{

			$id				= phpgw::get_var('id'); // in case of bigint

			if(!$this->acl_add && !$this->acl_edit)
			{
			//	$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.view', 'id'=> $id));
			}

			$values			= phpgw::get_var('values');

			$item_id		= phpgw::get_var('item_id');
			$active_tab		= phpgw::get_var('tab', 'string', 'REQUEST', 'general');


			if (is_array($values))
			{

				if($values['delete_alarm'] && count($values['alarm']))
				{
					if(!$receipt['error'])
					{
						$receipt = $boalarm->delete_alarm('s_agreement',$values['alarm']);
					}

				}
				else if(($values['enable_alarm'] || $values['disable_alarm']) && count($values['alarm']))
				{

					if(!$receipt['error'])
					{
						$receipt = $boalarm->enable_alarm('s_agreement',$values['alarm'],$values['enable_alarm']);
					}

				}
				else if($values['add_alarm'])
				{
					$time = intval($values['time']['days'])*24*3600 +
						intval($values['time']['hours'])*3600 +
						intval($values['time']['mins'])*60;

					if ($time > 0)
					{
						$receipt = $boalarm->add_alarm('s_agreement',$this->bo->read_event(array('s_agreement_id'=>$id)),$time,$values['user_id']);
					}

				}
				else if (!$values['save'] && !$values['apply'] && !$values['update'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.index', 'role'=> $this->role));
				}
			}

			$s_agreement = array();//$this->bo->read_single(array('s_agreement_id'=>$id));

			$link_data = array
			(
				'menuaction'	=> 'property.uis_agreement.edit',
				'id'		=> $id,
				'role'		=> $this->role
			);


			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'	=> 's_agreement',
						'type'		=> 'form',
						'text'		=> 'Email notification',
						'times'		=> $times,
						'id'		=> $id,
						'method'	=> $method,
						'data'		=> $data,
						'account_id'=> $account_id
						));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$link_file_data = array
			(
				'menuaction'	=> 'property.uis_agreement.view_file',
				'id'		=>$id
			);


			$link_download = array
			(
				'menuaction'	=> 'property.uis_agreement.download',
				'id'		=> $id
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$tabs = array();


			//----------JSON CODE ----------------------------------------------


			//---GET ALARM
			if( phpgw::get_var('phpgw_return_as') == 'json')
			{
				$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'=> 's_agreement',
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

			//--------------------JSON code-----


			//------- alarm--------
			$datavalues[0] = array
			(
				'name'   => "0",
				'values'   => json_encode($alarm_data['values']),
				'total_records' => count($alarm_data['values']),
				'is_paginator' => 0,
				'permission'=> '""',
				'footer'  => 0
			);

			$myColumnDefs[0] = array
			(
				'name'   => "0",
				'values'  => json_encode(array( array('key' => 'time', 'label'=>$alarm_data['header'][0]['lang_time'], 'sortable'=>true,'resizeable'=>true,'width'=>140),
			  		array('key' => 'text', 'label'=>$alarm_data['header'][0]['lang_text'], 'sortable'=>true,'resizeable'=>true,'width'=>340),
			  		array('key' => 'user', 'label'=>$alarm_data['header'][0]['lang_user'], 'sortable'=>true,'resizeable'=>true,'width'=>200),
			  		array('key' => 'enabled','label'=>$alarm_data['header'][0]['lang_enabled'],'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterCenter','width'=>60),
			  		array('key' => 'alarm_id','label'=>"dummy",'sortable'=>true,'resizeable'=>true,'hidden'=>true),
			  		array('key' => 'select','label'=>$alarm_data['header'][0]['lang_select'], 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>60)))
			  );

			$myButtons[0] = array
			(
			  	'name'   => "0",
				'values'  => json_encode(array( array('id' =>'values[enable_alarm]','type'=>'buttons', 'value'=>'Enable', 'label'=>$alarm_data[alter_alarm][0][lang_enable], 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
			  		array('id' =>'values[disable_alarm]','type'=>'buttons', 'value'=>'Disable', 'label'=>$alarm_data[alter_alarm][0][lang_disable], 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
			  		array('id' =>'values[delete_alarm]','type'=>'buttons', 'value'=>'Delete', 'label'=>$alarm_data[alter_alarm][0][lang_delete], 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
			  		))
			);

			$td_count = 0;
			$ColumnDefs_data = array();
			if(isset($uicols['input_type']) && $uicols['input_type'])
			{
				foreach($uicols['input_type'] as $key => $input_type)
				{
					if($input_type != 'hidden')
					{
						$ColumnDefs_data[] = array
						(
							'key'			=> $uicols['name'][$key],
							'label'			=> $uicols['descr'][$key],
							'sortable'		=> true,
							'resizeable'	=> true
						);
						$td_count ++;
					}
				}
			}

			$ColumnDefs_data[] = array
			(
				'key'			=> 'update',
				'label'			=> lang('Update'),
				'sortable'		=> true,
				'resizeable'	=> true,
				'formatter'		=> 'myFormatterCheckUpdate'
			);
//_debug_array($ColumnDefs_data);
       		$myColumnDefs[1] = array
       		(
       			'name'		=> "1",
       			'values'	=>	json_encode($ColumnDefs_data)
       		);
			unset($ColumnDefs_data);

			$myButtons[2] = array
       		(
       			'name'			=> "2",
       			'values'		=>	json_encode(array(	array('type'=>'text', 'label'=>' New index:', 'classname'=> 'index-opt'),
       													array('id' =>'values[new_index]', 'type'=>'inputText', size=>12, 'classname'=> 'index-opt'),
       													array('id' =>'values[update]','type'=>'buttons',	'value'=>'Update',	'label'=>'Update',	funct=> onUpdateClick , 'classname'=> '')
       													))
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			for($z=0; $z<count($s_agreement['files']); $z++)
			{
				if ($link_to_files != '') {
					$content_files[$z]['file_name'] = '<a href="'.$link_to_files.'/'.$s_agreement['files'][$z]['directory'].'/'.$s_agreement['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$s_agreement['files'][$z]['name'].'</a>';
				}
				else {
					$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$s_agreement['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$s_agreement['files'][$z]['name'].'</a>';
				}
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$s_agreement['files'][$z]['name'].'" title="'.lang('Check to delete file').'" style="cursor:help">';
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
       			'values'	=>	json_encode(array(	array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
									       			array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')))
			);

			if($id)
			{
				$content_budget = array();//$this->bo->get_budget($id);
				foreach($content_budget as & $b_entry)
				{
					$b_entry['delete_year'] = '<input type="checkbox" name="values[delete_b_year][]" value="'.$b_entry['year'].'" title="'.lang('Check to delete year').'">';				
				}
			}

			$datavalues[3] = array
			(
					'name'					=> "3",
					'values' 				=> json_encode($content_budget),
					'total_records'			=> count($content_budget),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 1
			);

	
			$myColumnDefs[3] = array
       		(
       			'name'		=> "3",
       			'values'	=>	json_encode(array(	array('key' => 'year','label'=>lang('year'),'sortable'=>false,'resizeable'=>true),
       												array('key' => 'category','label'=>lang('category'),'sortable'=>false,'resizeable'=>true),
       												array('key' => 'ecodimb','label'=>lang('dimb'),'sortable'=>false,'resizeable'=>true),
       												array('key' => 'budget_account','label'=>lang('budget account'),'sortable'=>false,'resizeable'=>true),
       												array('key' => 'budget','label'=>lang('budget'),'sortable'=>false,'resizeable'=>true),
       												array('key' => 'actual_cost','label'=>lang('actual cost'),'sortable'=>false,'resizeable'=>true),
									       			array('key' => 'delete_year','label'=>lang('Delete budget'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')))
			);

//--------------------------------------------JSON CODE------------


			$this->cats->app_name		= 'property.project';

			$data = array
			(
				'td_count'					=> $td_count,
				'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'base_java_url'						=> json_encode(array('menuaction' => "property.uis_agreement.edit",'id'=>$id)),
				'datatable'							=> $datavalues,
				'myColumnDefs'						=> $myColumnDefs,
				'myButtons'							=> $myButtons,

				'link_import'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uis_agreement.import', 'tab' => 'items')),
				'alarm_data'				=> $alarm_data,
				'lang_alarm'				=> lang('Alarm'),
				'lang_download'				=> 'download',
				'link_download'				=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'		=> lang('Download table to your browser'),

				'fileupload'				=> true,
				'link_view_file'			=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'				=> $link_to_files,
				'files'					=> $s_agreement['files'],
				'lang_files'				=> lang('files'),
				'lang_filename'				=> lang('Filename'),
				'lang_file_action'			=> lang('Delete file'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_file_action_statustext'		=> lang('Check to delete file'),
				'lang_upload_file'			=> lang('Upload file'),
				'lang_file_statustext'			=> lang('Select file to upload'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('ID'),
				'value_s_agreement_id'			=> $id,
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'value_cat'				=> $s_agreement['cat'],
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the service agreement untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the service agreement and return back to the list'),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the s_agreement belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[cat_id]',

				'lang_member_of'			=> lang('member of'),
				'member_of_name'			=> 'member_id',
				'member_of_list'			=> $member_of_data['cat_list'],

				'attributes_group'				=> $attributes,
				'lookup_functions'			=> $s_agreement['lookup_functions'],
				'dateformat'				=> $dateformat,

				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),

				'lang_start_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_start_date'			=> lang('start date'),
				'value_start_date'			=> $s_agreement['start_date'],

				'lang_end_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_end_date'				=> lang('end date'),
				'value_end_date'			=> $s_agreement['end_date'],

				'lang_termination_date_statustext'	=> lang('Select the estimated termination date'),
				'lang_termination_date'			=> lang('termination date'),
				'value_termination_date'		=> $s_agreement['termination_date'],

				'vendor_data'					=> $vendor_data,

				'lang_budget'					=> lang('Budget'),
				'lang_budget_statustext'		=> lang('Budget for selected year'),
				'value_budget'					=> $s_agreement['budget'],
				'currency'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

				'lang_year'						=> lang('year'),
				'lang_year_statustext'			=> lang('Budget year'),
				'year'							=> array(),//$this->bocommon->select_list($s_agreement['year'],$this->bo->get_year_list($id)),

				'b_account_data'			=> $b_account_data,
				'ecodimb_data'				=> $ecodimb_data,
				'lang_category'						=> lang('category'),
				'lang_no_cat'						=> lang('Select category'),

				'lang_name'				=> lang('name'),
				'lang_name_statustext'			=> lang('name'),
				'value_name'				=> $s_agreement['name'],
				'lang_descr'				=> lang('descr'),
				'lang_descr_statustext'			=> lang('descr'),
				'value_descr'				=> $s_agreement['descr'],
				'table_add'				=> $table_add,
				'values'				=> $content,
				'table_header'				=> $table_header,
				'acl_manage'				=> $this->acl_manage,
				'table_update'				=> $table_update,
				'update_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'id'=> $id)),
				'lang_select_all'			=> lang('Select All'),
				'img_check'				=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'set_column'				=> $set_column,
				'lang_import_detail'		=> lang('import detail'),
				'lang_detail_import_statustext'=> lang('import details to this agreement from spreadsheet'),
				'lang_import'				=> lang('import'),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
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


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . ($id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));

			$GLOBALS['phpgw']->xslttpl->add_file(array('event', 'attributes_form', 'files'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('schedule' => $data));
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'event.schedule', 'property' );
		}


 		public function schedule2()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('attributes_form', 'files'));
			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'	=> $s_agreement['vendor_id'],
						'vendor_name'	=> $s_agreement['vendor_name']));

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $s_agreement['b_account_id'],
						'b_account_name'	=> $s_agreement['b_account_name']));

			$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array(
						'ecodimb'			=> $s_agreement['ecodimb'],
						'ecodimb_descr'		=> $s_agreement['ecodimb_descr']));

			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'	=> 's_agreement',
						'type'		=> 'form',
						'text'		=> 'Email notification',
						'times'		=> $times,
						'id'		=> $id,
						'method'	=> $method,
						'data'		=> $data,
						'account_id'=> $account_id
						));


			$alarm_data['values'] = array();
			$datavalues[0] = array
			(
				'name'   => "0",
				'values'   => json_encode($alarm_data['values']),
				'total_records' => count($alarm_data['values']),
				'is_paginator' => 0,
				'permission'=> '""',
				'footer'  => 0
			);

			$myColumnDefs[0] = array
			(
				'name'   => "0",
				'values'  => json_encode(array( array('key' => 'time', 'label'=>$alarm_data['header'][0]['lang_time'], 'sortable'=>true,'resizeable'=>true,'width'=>140),
				array('key' => 'text', 'label'=>$alarm_data['header'][0]['lang_text'], 'sortable'=>true,'resizeable'=>true,'width'=>340),
				array('key' => 'user', 'label'=>$alarm_data['header'][0]['lang_user'], 'sortable'=>true,'resizeable'=>true,'width'=>200),
				array('key' => 'enabled','label'=>$alarm_data['header'][0]['lang_enabled'],'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterCenter','width'=>60),
				array('key' => 'alarm_id','label'=>"dummy",'sortable'=>true,'resizeable'=>true,'hidden'=>true),
				array('key' => 'select','label'=>$alarm_data['header'][0]['lang_select'], 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>60)))
			);

			$myButtons[0] = array
			(
				'name'   => "0",
				'values'  => json_encode(array( array('id' =>'values[enable_alarm]','type'=>'buttons', 'value'=>'Enable', 'label'=>$alarm_data['alter_alarm'][0]['lang_enable'], 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
				array('id' =>'values[disable_alarm]','type'=>'buttons', 'value'=>'Disable', 'label'=>$alarm_data['alter_alarm'][0]['lang_disable'], 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
				array('id' =>'values[delete_alarm]','type'=>'buttons', 'value'=>'Delete', 'label'=>$alarm_data['alter_alarm'][0]['lang_delete'], 'funct'=> 'onActionsClick' , 'classname'=> 'actionButton', 'value_hidden'=>""),
				))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uievent.schedule2',
				'id'		=>		$id
			);


			$data = array
			(
				'property_js'				=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'base_java_url'				=> json_encode(array('menuaction' => "property.uievent.schedule2",'id'=>$id)),
				'datatable'					=> $datavalues,
				'myColumnDefs'				=> $myColumnDefs,
				'myButtons'					=> $myButtons,

				'alarm_data'				=> $alarm_data,
				'lang_alarm'				=> lang('Alarm'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'td_count'					=> 0,
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
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('schedule' => $data));
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'event.schedule', 'property' );

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

