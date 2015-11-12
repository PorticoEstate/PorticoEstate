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
			'query'			=> true
		);

		public function __construct()
		{
			phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.ticket'));
			$this->location_id			= phpgw::get_var('location_id', 'int', 'REQUEST', 0);
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
			$GLOBALS['phpgw']->translation->add_app('property');
		}
		
		public function index()
		{
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'helpdesk.index';
			$this->insert_links_on_header_state();
			
			$filters = array();

			$search_option = array
			(
					array(
						'id'        => 'all',
						'name'      => lang("All"),
						'selected'  => 1
					),
					array(
						'id'        => 'X',
						'name'      => lang("Closed")
					),
					array(
						'id'        => 'O',
						'name'      => lang("Open"),
					)
			);
			
			$filters[] = array
						(
							'type'   => 'filter',
							'name'   => 'status_id',
							'text'   => lang('Status'),
							'list'   => $search_option
						);
		
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
			
			$uicols_helpdesk = array();
			for($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key'		 => $uicols['name'][$k],
					'label'		 => $uicols['descr'][$k],
					'sortable'	 => ($uicols['sortable'][$k]) ? true : false,
					'hidden'	 => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if($uicols['name'][$k]=='id' || $uicols['name'][$k]=='user' || $uicols['name'][$k]=='entry_date')
				{
					$params['sortable']	= true;
				}
				if($uicols['name'][$k]=='id')
				{
					$params['hidden'] = true;
				}

				array_push($uicols_helpdesk, $params);
			}

			$parameters = array
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

			$tabletools[] = array
				(
					'my_name'		=> 'view',
					'text' 			=> lang('view'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'frontend.uihelpdesk.view',
						'location_id'	=> $this->location_id,
					)),
					'parameters'	=> json_encode($parameters)
				);
			
			$tabletools[] = array
				(
					'my_name'		=> 'new_ticket',
					'text' 			=> lang('new_ticket'),
					'type'			=> 'custom',
					'custom_code'	=> "
						var oArgs = ".json_encode(array(
								'menuaction'	=> 'frontend.uihelpdesk.add_ticket',
								'noframework'	=> 1
							)).";
						newTicket(oArgs);
					"
				);
			
			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array
					(
						'menuaction'			=> 'frontend.uihelpdesk.query',
						'location_id'			=> $this->location_id,
						'phpgw_return_as'		=> 'json'))
				),
				'ColumnDefs' => $uicols_helpdesk,
				'tabletools' => $tabletools
			);

			/*$link =	$GLOBALS['phpgw']->link(
					'/index.php',
					array('menuaction'	=> 'frontend.uihelpdesk.view'));
			$datatable['exchange_values'] = "document.location = '{$link}&id=' + data.getData().id;";*/
			
			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array(
				'header' 		=> $this->header_state,
				'helpdesk' 		=> array('datatable_def' => $datatable_def, 'tabs' => $this->tabs, 'filters' => $filters, 'location_id' => $this->location_id, 'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog))),
				'lightbox_name'	=> lang('add ticket')
			);
			
			self::add_javascript('frontend', 'jquery', 'helpdesk.list.js');
			self::render_template_xsl(array('helpdesk', 'datatable_inline', 'frontend'), array('data' => $data));
		}
		
		public function query()
		{
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$bo	= CreateObject('property.botts');
			
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$status_id = phpgw::get_var('status_id', 'string', 'REQUEST', 'all');

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => ($columns[$order[0]['column']]['data'] == 'subject') ? 'entry_date' : $columns[$order[0]['column']]['data'],
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1,
				'status_id'	 => $status_id
			);

			if(isset($this->location_code) && $this->location_code != '')
			{
				$bo->location_code = $this->location_code;
				$ticket_list = $bo->read($params);
			}
			else
			{
				$ticket_list = null;
			}

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
						'status'		=> $custom['name'],
					);
				}
			
				foreach($ticket_list as &$ticket)
				{
					if(array_key_exists($ticket['status'], $status))
					{
						$ticket['status'] 	= $status[$ticket['status']]['status'];
					}
				}			 
			}

			$result_data = array('results' => $ticket_list);

			$result_data['total_records']	 = $bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
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
				'ticketinfo'	=> array(
					'helpdesklist'	=> $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction'		=> 'frontend.uihelpdesk.index',
									'location_id'		=> $this->location_id
								)),					
					'ticket'        => $ticket,
					'tickethistory'	=> $tickethistory2,
					'tabs'			=> $this->tabs,
					'location_id'	=> $this->location_id
				)
			);
			
			self::render_template_xsl(array('frontend', 'ticketview'), array('data' => $data));
		}


		public function add_ticket()
		{
			$values         = phpgw::get_var('values');
			$p_entity_id	= phpgw::get_var('p_entity_id', 'int');
			$p_cat_id		= phpgw::get_var('p_cat_id', 'int');
			$p_num			= phpgw::get_var('p_num');
			$origin			= phpgw::get_var('origin');

			if($p_entity_id && $p_cat_id && $p_num)
			{
				$item = execMethod('property.boentity.read_single',(array('id' => $p_num, 'entity_id' => $p_entity_id, 'cat_id' => $p_cat_id, 'view' => true)));
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

					$ticket = array
					(
						'origin_id'			=> $GLOBALS['phpgw']->locations->get_id('property', $origin),
						'origin_item_id'	=> $p_num,
						'cat_id'			=> $cat_id,
						'group_id'			=> ($default_group ? $default_group : null),
						'assignedto'		=> $assignedto,
						'priority'			=> 3,
						'status'			=> 'O', // O = Open
						'subject'			=> $values['title'],
						'details'			=> $values['locationdesc'].":\n\n".$values['description'],
						'apply'				=> lang('Apply'),
						'contact_id'		=> 0,
						'location'			=> $location,
						'location_code'		=> $this->location_code,
						'street_name'		=> $location_details['street_name'],
						'street_number'		=> $location_details['street_number'],
						'location_name'		=> $location_details['loc1_name'],
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

			$form_action_data = array
			(
				'menuaction'	=> 'frontend.uihelpdesk.add_ticket',
				'noframework'	=> '1',
				'origin'    	=> $origin,
				'p_entity_id'	=> $p_entity_id,
				'p_cat_id'		=> $p_cat_id,
				'p_num'			=> $p_num,
			);

			$data = array(
				'redirect'			=> isset($redirect) ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uihelpdesk.index')) : null,
				'msgbox_data'   	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$form_action_data),
				'title'         	=> $values['title'],
				'locationdesc'  	=> $values['locationdesc'],
				'description'   	=> $values['description'],
				'noform'        	=> $noform,
				'category_list'		=> $category_list,
				'custom_attributes'	=> array('attributes' => $item['attributes']),
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','helpdesk','attributes_view'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add_ticket' => $data));
			
			//self::render_template_xsl(array('frontend','helpdesk','attributes_view'), array('add_ticket' => $data));
		}
		

	}
