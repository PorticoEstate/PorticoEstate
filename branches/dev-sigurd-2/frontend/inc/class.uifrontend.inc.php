<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uifrontend.inc.php 4859 2010-02-18 23:09:16Z sigurd $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	/**
	 * Frontend
	 *
	 * @package Frontend
	 */

	class frontend_uifrontend
	{

		public $public_functions = array(
			'index'				=> true,
			'drawings'			=> true,
			'pictures'			=> true,
			'maintenance'		=> true,
			'refurbishment'		=> true,
			'services'			=> true,
			'contract'			=> true,
			'helpdesk'			=> true,
			'helpdesk_download'	=> true,
			'add_ticket'		=> true,
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$noframework = phpgw::get_var('noframework', 'bool');
			$GLOBALS['phpgw_info']['flags']['noframework'] = $noframework;

			$locations = $GLOBALS['phpgw']->locations->get_locations();

			unset($locations['.']);
			unset($locations['admin']);

			$config	= CreateObject('phpgwapi.config','frontend');
			$config->read();


			$_locations = array();
			foreach ($locations as $location => $name)
			{
				$_locations[] = array
				(
					'location'	=> $location,
					'name'		=> $name,
					'sort'		=> isset($config->config_data['tab_sorting'][$name]) ? $config->config_data['tab_sorting'][$name] : 99
				);
			}
		
			if(isset($config->config_data['tab_sorting']) && $config->config_data['tab_sorting'])
			{
				array_multisort($config->config_data['tab_sorting'], SORT_ASC, $_locations);
			}

			$tabs = array();
			foreach ($_locations as $key => $entry)
			{
				$name = $entry['name'];
				$location = $entry['location'];

				if ( $GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'frontend') )
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', $location);
					$tabs[$location_id] = array(
						'label' => lang($name),
						'link'  => $GLOBALS['phpgw']->link('/',array('menuaction' => "frontend.uifrontend.{$name}", 'type'=>$location_id, 'noframework' => $noframework))
					);
				}			
			}
			
			$selected = phpgw::get_var('type', 'int', 'REQUEST', 0);
			$this->tabs = $GLOBALS['phpgw']->common->create_tabs($tabs, $selected);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "frontend::{$selected}";

			$this->acl 					= & $GLOBALS['phpgw']->acl;
		}

		/**
		 * TODO
		 */
		public function index()
		{
			$data = array
			(
				'tabs'		=> $this->tabs
			);
            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('demo_1' => $data));
		}

		public function drawings()
		{
			$receipt = array();

			$receipt['error'][]=array('msg'=>'Eksempel på feilmelding');
			$receipt['message'][]=array('msg'=>'Eksempel på gladmelding');

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'tabs'			=> $this->tabs,
				'date_start'	=> $GLOBALS['phpgw']->yuical->add_listener('date_start', $date_start),
				'date_end'		=> $GLOBALS['phpgw']->yuical->add_listener('date_end', $date_end),
			);
            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('demo_2' => $data));

		}

		public function pictures()
		{
			$allusers = $GLOBALS['phpgw']->accounts->get_list('accounts', -1);
			$content = array();
			foreach ($allusers as $user)
			{
				$content[] = array
				(
					'id'	=> $user->id,
					'name'	=> $user->__toString(), 
				);
			}


       		$myColumnDefs[0] = array
       		(
       			'name'		=> "0",
       			'values'	=>	json_encode(array(	array('key' => 'id','label'=> lang('id') ,'sortable'=>true,'resizeable'=>true,'hidden'=>false),
       												array('key' => 'name',	'label'=> lang('name'),	'sortable'=>true,'resizeable'=>true),
		       				       					array('key' => 'select','label'=> lang('select'), 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>30)))
			);	

			$datavalues[0] = array
			(
					'name'					=> "0",
					'values' 				=> json_encode($content),
					'total_records'			=> 0,
					'permission'   			=> "''",
					'is_paginator'			=> 1,
					'footer'				=> 1
			);


			$data = array
			(
				'td_count'			=> 2,
				'base_java_url'		=> "{menuaction:'frontend.ui_demo_tabs.first'}",
				'property_js'		=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'			=> $datavalues,
				'myColumnDefs'		=> $myColumnDefs,
				'tabs'				=> $this->tabs,
			);

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('paginator');

		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'demo_tabs.second', 'frontend' );

			self::render_template(array('frontend'), array('demo_3' => $data));
		}

		public function maintenance()
		{
			$this->index();
		}

		public function refurbishment()
		{
			$this->index();
		}

		public function services()
		{
			$this->index();
		}

		public function contract()
		{
			$this->index();
		}

		public function helpdesk()
		{

			$bo					= CreateObject('property.botts',true);
			$bocommon 			= & $bo->bocommon;

			if(!$this->acl_read)
			{
//				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

//			$this->save_sessiondata();
			$dry_run = false;
			$second_display = phpgw::get_var('second_display', 'bool');


			$default_status 	= '';//isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status']:'';
			$start_date 		= urldecode($this->start_date);
			$end_date 			= urldecode($this->end_date);

			if ($default_status && !$second_display)
			{
				$bo->status_id	= $default_status;
				$this->status_id		= $default_status;
			}

			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));
			$allrows  		= phpgw::get_var('allrows', 'bool');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
		    	$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
		    		(
						'menuaction'		=> 'frontend.uifrontend.helpdesk',
						'query'				=> $this->query,
						'district_id'		=> $this->district_id,
						'part_of_town_id'	=> $this->part_of_town_id,
						'cat_id'			=> $this->cat_id,
						'status'			=> $this->status
	   				)
	   			);

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'frontend.uifrontend.helpdesk',"
	    											."second_display:1,"
 	                        						."sort: '{$this->sort}',"
 	                        						."order: '{$this->order}',"
 	                        						."cat_id:'{$this->cat_id}',"
			                						."status_id: '{$this->status_id}',"
 	                        						."user_id: '{$this->user_id}',"
 	                        						."query: '{$this->query}',"
 	                        						."district_id: '{$this->district_id}',"
 	                        						."start_date: '{$start_date}',"
 	                        						."end_date: '{$end_date}',"
 	                        						."allrows:'{$this->allrows}'";

				$link_data = array
				(
					'menuaction'	=> 'frontend.uifrontend.helpdesk',
					'second_display'=> true,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'cat_id'		=> $this->cat_id,
					'status_id'		=> $this->status_id,
					'user_id'		=> $this->user_id,
					'query'			=> $this->query,
					'district_id'	=> $this->district_id,
					'start_date'	=> $start_date,
					'end_date'		=> $end_date,
					'allrows'		=> $this->allrows
				);

				$group_filters = 'select';

				$values_combo_box = array();

				$values_combo_box[0]  = $bo->filter(array('format' => '', 'filter'=> $this->status_id,'default' => 'O'));
				
				if(isset($bo->config->config_data['tts_lang_open']) && $bo->config->config_data['tts_lang_open'])
				{
					array_unshift ($values_combo_box[0],array ('id'=>'O2','name'=>$bo->config->config_data['tts_lang_open']));
				}
				$default_value = array ('id'=>'','name'=>lang('Open'));
				array_unshift ($values_combo_box[0],$default_value);

					$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' 		=> 'frontend.uifrontend.helpdesk',
											'second_display'       => $second_display,
											'status'			=> $this->status
										)
									),
							'fields'	=> array
							(
	                       		'field' => array
	                       		(
									array
									( //boton 	HOUR CATEGORY
										'id' => 'btn_status_id',
										'name' => 'status_id',
										'value'	=> lang('Status'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 1
									),
									array
									(
										'type'	=> 'button',
										'id'	=> 'btn_export',
										'value'	=> lang('download'),
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
										'menuaction' => 'property.uiproject.date_search'))."','','width=350,height=250')",
										'value' => lang('Date search'),
										'tab_index' => 4
									),
									array
									( //boton     SEARCH
										'id' => 'btn_search',
										'name' => 'search',
										'value'    => lang('search'),
										'type' => 'button',
										'tab_index' => 3
									),
									array
									( // TEXT INPUT
										'name'     => 'query',
										'id'     => 'txt_query',
										'value'    => $this->query,
										'type' => 'text',
										'onkeypress' => 'return pulsar(event)',
										'size'    => 28,
										'tab_index' => 2
									)
								),
			                   	'hidden_value' => array
			                	(
									array
									( //div values  combo_box_0
										'id' => 'values_combo_box_0',
										'value'	=> $bocommon->select2String($values_combo_box[0])
									)
								)
							)
						)
					);
				$dry_run = true;
			}

			$ticket_list = array();
			if(!$dry_run)
			{
				$ticket_list = $bo->read($start_date, $end_date);
			}

			$uicols = array();
			$i = 0;
			$uicols['name'][$i++] = 'priority';
			$uicols['name'][$i++] = 'id';
			$uicols['name'][$i++] = 'subject';
			$uicols['name'][$i++] = 'loc1_name';
			$uicols['name'][$i++] = 'location_code';
			$uicols['name'][$i++] = 'address';
			$uicols['name'][$i++] = 'assignedto';
			$uicols['name'][$i++] = 'entry_date';
			$uicols['name'][$i++] = 'status';
			
			if( $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property') )
			{
				$uicols['name'][$i++] = 'order_id';
				$uicols['name'][$i++] = 'vendor';
			}

			$count_uicols_name = count($uicols['name']);

			if(is_array($ticket_list))
			{
				$status['X'] = array
				(
					'bgcolor'			=> '#5EFB6E',
					'status'			=> lang('closed'),
					'text_edit_status'	=> isset($bo->config->config_data['tts_lang_open']) && $bo->config->config_data['tts_lang_open'] ? $bo->config->config_data['tts_lang_open'] : lang('Open'),
					'new_status' 		=> 'O'
				);

				$custom_status	= $bo->get_custom_status();

				foreach($custom_status as $custom)
				{
					$status["C{$custom['id']}"] = array
					(
						'bgcolor'			=> $custom['color'] ? $custom['color'] : '',
						'status'			=> $custom['name'],
						'text_edit_status'	=> lang('close'),
						'new_status'		=> 'X'
					);
				}

				$j = 0;
				foreach($ticket_list as $ticket)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='O')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value'] 	= isset($bo->config->config_data['tts_lang_open']) && $bo->config->config_data['tts_lang_open'] ? $bo->config->config_data['tts_lang_open'] : lang('Open');
						}
						else if($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='C')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value'] 	= lang('Closed');
						}
						else if($uicols['name'][$k] == 'status' && array_key_exists($ticket[$uicols['name'][$k]],$status))
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value'] 	= $status[$ticket[$uicols['name'][$k]]]['status'];
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket[$uicols['name'][$k]];
						}
						if($uicols['name'][$k] == 'id' || $uicols['name'][$k] == 'entry_date')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 	= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['link']		=	$GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uitts.view',
									'id'			=> $ticket['id']
								));
							$datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket[$uicols['name'][$k]] .  $ticket['new_ticket'];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
						}

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
						'name'		=> 'id',
						'source'	=> 'id'
					),
				)
			);

//			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name' 			=> 'view',
					'statustext' 	=> lang('view the ticket'),
					'text'			=> lang('view'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uitts.view'
							)),
				'parameters'	=> $parameters
				);
			}

	//		if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('Add new ticket'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uitts.add'
										))
				);
			}

			unset($parameters);
			for ($i=0;$i<$count_uicols_name;$i++)
			{
		//		if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= !isset($uicols['formatter'][$i]) || $uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i];
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= lang($uicols['name'][$i]);
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='priority' || $uicols['name'][$i]=='id' || $uicols['name'][$i]=='assignedto' || $uicols['name'][$i]=='finnish_date'|| $uicols['name'][$i]=='user'|| $uicols['name'][$i]=='entry_date' || $uicols['name'][$i]=='order_id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
					}
					if($uicols['name'][$i]=='text_view' || $uicols['name'][$i]=='bgcolor' || $uicols['name'][$i]=='child_date' || $uicols['name'][$i]== 'link_view' || $uicols['name'][$i]=='lang_view_statustext')
					{
						$datatable['headers']['header'][$i]['visible'] 		= false;
						$datatable['headers']['header'][$i]['format'] 		= 'hidden';
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			if($dry_run)
			{
					$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($ticket_list);
			}
			$datatable['pagination']['records_total'] 	= $bo->total_records;

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column

			$appname						= lang('helpdesk');
			$function_msg					= lang('list ticket');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'entry_date'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

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
		    				if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
		    				{
		    					$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				else if(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."' title = '{$column['statustext']}'>" .$column['value']."</a>";
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

// Prepare template variables and process XSLT
			
			$data = array
			(
				'tabs'			=> $this->tabs,
				'datatable' 	=> $datatable,
				'lightbox_name'	=> lang('add ticket')
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('helpdesk' => $data));

	      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
	      	{
	        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	      	}

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
		  	phpgwapi_yui::load_widget('paginator');

			// Prepare CSS Style
		  	$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
	
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('frontend') . ' - ' . $appname . ': ' . $function_msg;
	
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'helpdesk.list' , 'frontend' );

		}

		public function helpdesk_download()
		{
			echo 'implement me';
		}
		public function add_ticket()
		{

			$GLOBALS['phpgw_info']['flags']['noframework'] =  true;

			$values	= phpgw::get_var('values');

			$receipt = array();
			if (isset($values['save']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('repost'));
				}

				if(!isset($values['address']) || !$values['address'])
				{
					$receipt['error'][]=array('msg'=>lang('Missing address'));
				}

				if(!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][]=array('msg'=>lang('Please give som details'));
				}

				$attachments = array();
				
				if(isset($_FILES['file']['name']) && $_FILES['file']['name'])
				{
					$file_name	= str_replace(' ','_',$_FILES['file']['name']);
					$mime_magic = createObject('phpgwapi.mime_magic');
					$mime       = $mime_magic->filename2mime($file_name);

					$attachments[] = array
					(
						'file' => $_FILES['file']['tmp_name'],
						'name' => $file_name,
						'type' => $mime
					);
				}

				if (!$receipt['error'])
				{
					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'] )
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}
					
						$from = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$values['from_address']}>";

						$receive_notification = true;
						$rcpt = $GLOBALS['phpgw']->send->msg('email', $values['address'],'Support',
							 stripslashes(nl2br($values['details'])), '', '', '',
							 $from , $GLOBALS['phpgw_info']['user']['fullname'],
							 'html', '', $attachments , $receive_notification);

						if($rcpt)
						{
							$receipt['message'][]=array('msg'=>lang('message sent'));
						}
					}
					else
					{
						$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
					}
				}
			}

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'from_name'		=> $GLOBALS['phpgw_info']['user']['fullname'],
				'from_address'	=> $GLOBALS['phpgw_info']['user']['preferences']['property']['email'],
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uifrontend.add_ticket')),
				'support_address'	=> $GLOBALS['phpgw_info']['server']['support_address'],
			);

            $GLOBALS['phpgw']->xslttpl->add_file('frontend');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add_ticket' => $data));
		}


	}
