<?php

	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
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

	phpgw::import_class('frontend.uifrontend');

	/**
	 * Helpdesk
	 *
	 * @package Frontend
	 */

	class frontend_uihelpdesk extends frontend_uifrontend
	{

		public $public_functions = array
			(
			'index'     	=> true,
			'add_ticket'    => true,
			'view'          => true,
		);

		public function __construct()
		{
			phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.ticket'));
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
			$GLOBALS['phpgw']->translation->add_app('property');
		}

		public function index()
		{
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'helpdesk.index';
			$this->insert_links_on_header_state();
			$bo	= CreateObject('property.botts',true);

			$dry_run = false;
			$second_display = phpgw::get_var('second_display', 'bool');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'frontend.uihelpdesk.index',"
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

				$this->bocommon = CreateObject('property.bocommon', true);

				$values_combo_box = array(
					0 => array(
						'id'        => 'all',
						'name'      => lang("All"),
						'selected'  => 'selected'
					),
					1 => array(
						'id'        => 'X',
						'name'      => lang("Closed")
					),
					2 => array(
						'id'        => 'O',
						'name'      => lang("Open"),
					)
				);

				$datatable['actions']['form'] = array(
					array
					(
						'action'        => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' 	=> 'frontend.uihelpdesk.index',
						'second_display'=> $second_display,
						'status'		=> $this->status
						)
						),
						'fields' => array(
							'field' => array(
								array
								(
									'type'      => 'button',
									'id'        => 'btn_new',
									'value'     => lang('new_ticket'),
									'tab_index' => 3
								),
								array
								(
									'id'        => 'btn_status_id',
									'name'      => 'status_id',
									'value'     => lang('Status'),
									'type'      => 'button',
									'style'     => 'filter',
									'tab_index' => 2
								),
							),
							'hidden_value' => array
							(
								array
								( //status values
									'id' => 'values_combo_box',
									'value'	=> $this->bocommon->select2String($values_combo_box)
								)
							)
						)
					)
				);

				$dry_run = true;
			}

			if(isset($this->location_code) && $this->location_code != '')
			{
				$bo->location_code = $this->location_code;
				//to make sure the list select "All" not only the open ones
				if(phpgw::get_var("status_id")== ""){
					$bo->status_id = 'all';
				}
				$ticket_list = $bo->read('','','',$dry_run);
			}
			else
			{
				$ticket_list = null;
			}
			
			
			$uicols = array();

			$uicols['name'][] = 'id';
			$uicols['descr'][] = lang('id');
			$uicols['name'][] = 'subject';
			$uicols['descr'][] = lang('subject');
			$uicols['name'][] = 'entry_date';
			$uicols['descr'][] = lang('entry_date');
			$uicols['name'][] = 'status';
			$uicols['descr'][] = lang('status');
			$uicols['name'][] = 'user';
			$uicols['descr'][] = lang('user');

			$count_uicols_name = count($uicols['name']);

			if(is_array($ticket_list))
			{
				$status['X'] = array
				(
					'status'			=> lang('closed')
				);

				$status['O'] = array
				(
					'status'			=> isset($bo->config->config_data['tts_lang_open']) && $bo->config->config_data['tts_lang_open'] ? $bo->config->config_data['tts_lang_open'] : lang('Open'),
				);

				$custom_status	= $bo->get_custom_status();

				foreach($custom_status as $custom)
				{
					$status["C{$custom['id']}"] = array
					(
						'status'			=> $custom['name'],
					);
				}

				$j = 0;
				foreach($ticket_list as $ticket)
				{
					for ($k = 0 ; $k < $count_uicols_name ; $k++)
					{
						if($uicols['name'][$k] == 'status' && array_key_exists($ticket[$uicols['name'][$k]],$status))
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value'] 	= $status[$ticket[$uicols['name'][$k]]]['status'];
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket[$uicols['name'][$k]];
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

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'view',
				'statustext' 	=> lang('view the ticket'),
				'text'			=> lang('view'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
				'menuaction'	=> 'frontend.uihelpdesk.view'
				)),
				'parameters'	=> $parameters
			);

			$link =	$GLOBALS['phpgw']->link(
					'/index.php',
					array('menuaction'	=> 'frontend.uihelpdesk.view'));
			$datatable['exchange_values'] = "document.location = '{$link}&id=' + data.getData().id;";
			
			unset($parameters);
			for ($i = 0 ; $i < $count_uicols_name ; $i++)
			{
				$datatable['headers']['header'][$i]['formatter'] 		= !isset($uicols['formatter'][$i]) || $uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i];
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= false;
				if($uicols['name'][$i]=='id' || $uicols['name'][$i]=='user' || $uicols['name'][$i]=='entry_date')
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
				}
				if($uicols['name'][$i]=='id')
				{
					$datatable['headers']['header'][$i]['visible'] 		= false;
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

			$appname		= lang('helpdesk');
			$function_msg	= lang('list ticket');

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
			$json = array(
				'recordsReturned' 	=> $datatable['pagination']['records_returned'],
				'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
				'startIndex' 		=> $datatable['pagination']['records_start'],
				'sort'				=> $datatable['sorting']['order'],
				'dir'				=> $datatable['sorting']['sort'],
				'records'			=> array()
			);

			// values for datatable
			if(is_array($datatable['rows']['row']))
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
			if(is_array($datatable['rowactions']['action']))
			{
				$json['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
//-------------------- JSON CODE ----------------------

// Prepare template variables and process XSLT

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

			$GLOBALS['phpgw']->js->validate_file('yahoo', 'helpdesk.list' , 'frontend');

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array(
				'header' 		=> $this->header_state,
				'tabs'			=> $this->tabs,
				'helpdesk' 		=> array('datatable' => $datatable, 'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog))),
				'lightbox_name'	=> lang('add ticket')
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'helpdesk', 'datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('app_data' => $data));
			//print_r( $GLOBALS['phpgw']->xslttpl->get_vars());
		}

		private function cmp($a, $b)
		{
			$timea = explode('/', $a['date']);
			$timeb = explode('/', $b['date']);
			$year_and_maybe_time_a = explode(' - ', $timea[2]);
			$year_and_maybe_time_b = explode(' - ', $timeb[2]);
			$time_of_day_a = explode(':', $year_and_maybe_time_a[1]);
			$time_of_day_b = explode(':', $year_and_maybe_time_b[1]);

			$timestamp_a = mktime($time_of_day_a[0], $time_of_day_a[1], 0, $timea[1], $timea[0], $year_and_maybe_time_a[0]);
			$timestamp_b = mktime($time_of_day_b[0], $time_of_day_b[1], 0, $timeb[1], $timeb[0], $year_and_maybe_time_b[0]);

			if($timestamp_a < $timestamp_b)
			{
				return 1;
			}

			return -1;
		}


		public function view()
		{
			$GLOBALS['phpgw']->translation->add_app('property');
			$bo	= CreateObject('property.botts');
			$ticketid = phpgw::get_var('id');
			$ticket = $bo->read_single($ticketid);

			$assignedto = $ticket['assignedto'];
			if(isset($assignedto) && $assignedto != '')
			{
				$assignedto_account = $GLOBALS['phpgw']->accounts->get($assignedto);
				//var_dump($assignedto_account);
				if($assignedto_account)
				{
					$ticket['assigned_to_name'] = $assignedto_account->__toString();
				}
			}
			
			$contact_id = $ticket['contact_id'];
			if(isset($contact_id) && $contact_id != '')
			{
				$contacts							= CreateObject('phpgwapi.contacts');
				$contact_data						= $contacts->read_single_entry($contact_id, array('fn','tel_work','email'));
				$ticket['value_contact_name']		= $contact_data[0]['fn'];
				$ticket['value_contact_email']		= $contact_data[0]['email'];
				$ticket['value_contact_tel']		= $contact_data[0]['tel_work'];
			}	
				
			$vendor_id = $ticket['vendor_id'];
			if(isset($vendor_id) && $vendor_id != '')
			{
				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				$custom 		= createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data	= $contacts->read_single(array('id' => $vendor_id),$vendor_data);

				if(is_array($vendor_data))
				{
					foreach($vendor_data['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$ticket['value_vendor_name']=$attribute['value'];
							break;
						}
					}
				}
			}

			$notes = $bo->read_additional_notes($ticketid);
			//$history = $bo->read_record_history($ticketid);

			$tickethistory = array();

			foreach($notes as $note)
			{
				if($note['value_publish'])
				{
					$tickethistory[] = array(
						'date' => $note['value_date'],
						'user' => $note['value_user'],
						'note' => $note['value_note']
					);
				}
			}

			/*
			foreach($history as $story)
			{
				
				 // String search for filtering out ticket history. If the status contains e.g. "Budget changed"
				 // the history bullet will not be incuded in the ticket history shown to frontend users.
				 
				if(
					(	
						strpos($story['value_action'],'Budget changed') === false && 
						strpos($story['value_action'],'Priority changed') === false &&
						strpos($story['value_action'],'actual cost changed') === false &&
						strpos($story['value_action'],'Billable hours changed') === false
					)
				)
				{
					$tickethistory[] = array(
						'date' => $story['value_date'],
						'user' => $story['value_user'],
						'action'=> $story['value_action'],
						'new_value' => $story['value_new_value'],
						'old_value' => $story['value_old_value']
					);
				}
			}*/

			usort($tickethistory, array($this, "cmp"));


			$i=0;
			foreach($tickethistory as $foo)
			{
				$tickethistory2['record'.$i] = $foo;
				$i++;
			}

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array(
				'header' 		=> $this->header_state,
				'msgbox_data'   => isset($msglog) ? $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)) : array(),
				'tabs'			=> $this->tabs,
				'ticketinfo'	=> array(
					'ticket'        => $ticket,
					'tickethistory'	=> $tickethistory2)
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'ticketview'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('app_data' => $data));
		}


		public function add_ticket()
		{

			$values         = phpgw::get_var('values');

			$bypass 		= phpgw::get_var('bypass', 'bool');
			if($bypass)
			{
				$boadmin_entity		= CreateObject('property.boadmin_entity');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id			= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');

				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];

					$id = phpgw::get_var('p_num');
					$item = execMethod('property.boentity.read_single',(array('id' => $id, 'entity_id' => $p_entity_id, 'cat_id' => $p_cat_id, 'view' => true)));
				}

			}

			$bo	= CreateObject('property.botts',true);
			$boloc	= CreateObject('property.bolocation',true);

			$location_details = $boloc->read_single($this->location_code, array('noattrib' => true));


			$missingfields  = false;
			$msglog         = array();

			// Read default assign-to-group from config
			$config = CreateObject('phpgwapi.config', 'frontend');
			$config->read();
			$default_cat = $config->config_data['tts_default_cat'] ? $config->config_data['tts_default_cat'] : 0;
					
			if(!$default_cat)
			{
				throw new Exception('Default category is not set in config');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$cat_id = isset($values['cat_id']) && $values['cat_id'] ? $values['cat_id'] : $default_cat;

 			if(isset($values['save']))
			{
				foreach($values as $key => $value)
				{
					if(empty($value) && $key !== 'file')
					{
						$missingfields = true;
					}
				}

				if(!$missingfields && !phpgw::get_var('added'))
				{
					$location = array();
					$_location_arr = explode('-', $this->location_code);
					$i = 1;
					foreach($_location_arr as $_loc)
					{
						$location["loc{$i}"] = $_loc;
						$i++;
					}

					$assignedto = execMethod('property.boresponsible.get_responsible', array('location' => $location, 'cat_id' => $cat_id));

					if(!$assignedto)
					{
						$default_group = (int)$config->config_data['tts_default_group'];
					}
					else
					{
						$default_group = 0;
					}

					$ticket = array(
						'origin'    => null,
						'origin_id' => null,
						'cat_id'    => $cat_id,
						'group_id'  => ($default_group ? $default_group : null),
						'assignedto'=> $assignedto,
						'priority'  => 3,
						'status'    => 'O', // O = Open
						'subject'   => $values['title'],
						'details'   => $values['locationdesc'].":\n\n".$values['description'],
						'apply'     => lang('Apply'),
						'contact_id'=> 0,
						'location'  => $location,
						'location_code' => $this->location_code,
						'street_name'   => $location_details['street_name'],
						'street_number' => $location_details['street_number'],
						'location_name' => $location_details['loc1_name'],
						//'locationdesc'  => $values['locationdesc']
					);

					$result = $bo->add($ticket);
					if($result['message'][0]['msg'] != null && $result['id'] > 0)
					{
						$msglog['message'][] = array('msg' => lang('Ticket added'));
						$noform = true;


						// Files
						$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);
						if($values['file_name'] && $result['id'])
						{
							$bofiles = CreateObject('property.bofiles');
							$to_file = $bofiles->fakebase . '/fmticket/' . $result['id'] . '/' . $values['file_name'];

							if($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => array(RELATIVE_NONE)
							)))
							{
								$msglog['error'][] = array('msg'=>lang('This file already exists !'));
							}
							else
							{
								$bofiles->create_document_dir("fmticket/{$result['id']}");
								$bofiles->vfs->override_acl = 1;

								if(!$bofiles->vfs->cp(array (
								'from'	=> $_FILES['file']['tmp_name'],
								'to'	=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
								{
									$msglog['error'][] = array('msg' => lang('Failed to upload file!'));
								}
								$bofiles->vfs->override_acl = 0;
							}
						}

						$redirect = true;
						phpgwapi_cache::session_set('frontend', 'msgbox', $msglog);
						// /Files
					}
				}
				else
				{
					$msglog['error'][] = array('msg'=>lang('Missing field(s)'));
				}
			}


			$tts_frontend_cat_selected = $config->config_data['tts_frontend_cat'] ? $config->config_data['tts_frontend_cat'] : array();

			$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$cats->supress_info = true;
			$categories = $cats->return_sorted_array(0, false, '', '', '', true, '', false);

			$category_list = array();
			foreach ( $categories as $category)
			{
				if ( in_array($category['id'], $tts_frontend_cat_selected))
				{
					$category_list[] = array
					(
						'id'		=> $category['id'],
						'name'		=> $category['name'],
						'selected'	=> $category['id'] == $default_cat ? 1 : 0
					); 
				}
			}

			$data = array(
				'redirect'			=> isset($redirect) ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uihelpdesk.index')) : null,
				'msgbox_data'   	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uihelpdesk.add_ticket', 'noframework' => '1')),
				'title'         	=> $values['title'],
				'locationdesc'  	=> $values['locationdesc'],
				'description'   	=> $values['description'],
				'noform'        	=> $noform,
				'category_list'		=> $category_list,
				'custom_attributes'	=> array('attributes' => $item['attributes']),
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','helpdesk','attributes_view'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add_ticket' => $data));
		}

	}
