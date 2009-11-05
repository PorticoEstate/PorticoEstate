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
	* @subpackage helpdesk
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	phpgw::import_class('phpgwapi.yui');

	class property_uitts
	{
		var $public_functions = array
		(
			'index'		=> true,
			'index2'	=> true,
			'view'		=> true,
			'view2'		=> true,
			'add'		=> true,
			'add2'		=> true,
			'delete'	=> true,
			'download'	=> true,
			'download2'	=> true,
			'view_file'	=> true,
			'edit_status'=> true
		);

		/**
		 * @var boolean $_simple use simplified interface
		 */
		protected $_simple = false;
		protected $_show_finnish_date = false;

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::helpdesk';
			if($this->tenant_id	= $GLOBALS['phpgw']->session->appsession('tenant_id','property'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->bo					= CreateObject('property.botts',true);
			$this->bocommon 			= & $this->bo->bocommon;
			$this->cats					= & $this->bo->cats;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, PHPGW_ACL_PRIVATE, 'property'); // manage
			$this->bo->acl_location		= $this->acl_location;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->status_id			= $this->bo->status_id;
			$this->user_id				= $this->bo->user_id;
			$this->cat_id				= $this->bo->cat_id;
			$this->district_id			= $this->bo->district_id;
			$this->allrows				= $this->bo->allrows;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;

			$user_groups =  $GLOBALS['phpgw']->accounts->membership($this->account);
			$simple_group = isset($this->bo->config->config_data['fmttssimple_group']) ? $this->bo->config->config_data['fmttssimple_group'] : array();
			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $simple_group))
				{
					$this->_simple = true;
					break;
				}
			}


			reset($user_groups);
			$group_finnish_date = isset($this->bo->config->config_data['fmtts_group_finnish_date']) ? $this->bo->config->config_data['fmtts_group_finnish_date'] : array();
			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $group_finnish_date))
				{
					$this->_show_finnish_date = true;
					break;
				}
			}



		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'status_id'		=> $this->status_id,
				'user_id'		=> $this->user_id,
				'cat_id'		=> $this->cat_id,
				'district_id'	=> $this->district_id,
				'allrows'		=> $this->allrows,
				'start_date'	=> $this->start_date,
				'end_date'		=> $this->end_date
			);
			$this->bo->save_sessiondata($data);
		}

		function download2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$this->download($external = true);
		}

		function download($external='')
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$this->bo->allrows = true;
			$list = $this->bo->read($start_date,$end_date,$external, '', $download = true);

			if (isset($list) AND is_array($list))
			{
				$i=0;
				foreach($list as &$entry)
				{
					if($entry['subject'])
					{
						$entry['category'] = $entry['subject'];
					}

					if (isset($entry['child_date']) AND is_array($entry['child_date']))
					{
						$j=0;
						foreach($entry['child_date'] as $date)
						{
							if($date['date_info'][0]['descr'])
							{
							 	$entry['date_' . $j]=$date['date_info'][0]['entry_date'];
							 	$name_temp['date_' . $j]=true;
							 	$descr_temp[$date['date_info'][0]['descr']]=true;
							 }
							 $j++;
						}
						unset($entry['child_date']);
					}
					$i++;
				}
			}
//_debug_array($descr_temp);

			$name	= array('id',
						'category',
						'location_code',
						'address',
						'user',
						'assignedto',
						'entry_date'
						);

			while (is_array($name_temp) && list($name_entry,) = each($name_temp))
			{
				array_push($name,$name_entry);
			}

			array_push($name,'finnish_date','delay');

			$descr	= array(lang('ID'),
					lang('category'),
					lang('location'),
					lang('address'),
					lang('user'),
					lang('Assigned to'),
					lang('Started')
					);

			while (is_array($descr_temp) && list($descr_entry,) = each($descr_temp))
			{
				array_push($descr,$descr_entry);
			}

			array_push($descr,lang('finnish date'),lang('delay'));

			$this->bocommon->download($list,$name,$descr);
		}

		function edit_status()
		{
			if(!$this->acl_edit)
			{
				return lang('sorry - insufficient rights');
			}

			$new_status = phpgw::get_var('new_status', 'string', 'GET');
			$id 		= phpgw::get_var('id', 'int');
			$receipt 	= $this->bo->update_status(array('status'=>$new_status),$id);
			if (isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
			{
				$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
			}
		//	$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
			return "id ".$id." ".lang('Status has been changed');
		}

		function index()
		{
			if($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index2'));
			}

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$this->save_sessiondata();

			$dry_run=false;

			$second_display = phpgw::get_var('second_display', 'bool');

			$default_category 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');
			//FIXME: differentiate mainsreen and helpdesk if this should be used.
			$default_status 	= '';//isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status']:'';
			$start_date 		= urldecode($this->start_date);
			$end_date 			= urldecode($this->end_date);

			if ($default_category && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_category;
				$this->district_id		= $default_category;
			}

			if ($default_status && !$second_display)
			{
				$this->bo->status_id	= $default_status;
				$this->status_id		= $default_status;
			}

/*
			$bgcolor_array[1]	= '#dadada';
			$bgcolor_array[2]	= '#dad0d0';
			$bgcolor_array[3]	= '#dacaca';
			$bgcolor_array[4]	= '#dac0c0';
			$bgcolor_array[5]	= '#dababa';
			$bgcolor_array[6]	= '#dab0b0';
			$bgcolor_array[7]	= '#daaaaa';
			$bgcolor_array[8]	= '#da9090';
			$bgcolor_array[9]	= '#da8a8a';
			$bgcolor_array[10]	= '#da7a7a';
*/
			$bgcolor_array[1]	= '#da7a7a';
			$bgcolor_array[2]	= '#dababa';
			$bgcolor_array[3]	= '#dadada';

			$lookup 		= phpgw::get_var('lookup', 'bool');
			$from 			= phpgw::get_var('from');
			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));
			$allrows  		= phpgw::get_var('allrows', 'bool');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
		    	$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
		    		(
						'menuaction'		=> 'property.uitts.index',
						'query'				=> $this->query,
						'district_id'		=> $this->district_id,
						'part_of_town_id'	=> $this->part_of_town_id,
						'cat_id'			=> $this->cat_id,
						'status'			=> $this->status
	   				)
	   			);

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uitts.index',"
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
					'menuaction'	=> 'property.uitts.index',
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

				$values_combo_box[2]  = $this->bo->filter(array('format' => $group_filters, 'filter'=> $this->status_id,'default' => 'O'));
				$default_value = array ('id'=>'','name'=>lang('Open'));
				array_unshift ($values_combo_box[2],$default_value);

				if(!$this->_simple)
				{
					$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => True));
					$default_value = array ('cat_id'=>'','name'=> lang('no category'));
					array_unshift ($values_combo_box[0]['cat_list'],$default_value);

					$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
					$default_value = array ('id'=>'','name'=>lang('no district'));
					array_unshift ($values_combo_box[1],$default_value);

					$values_combo_box[3]  = $this->bocommon->get_user_list_right2('filter',2,$this->user_id,$this->acl_location);
					$default_value = array ('id'=>'','name'=>lang('no user'));
					array_unshift ($values_combo_box[3],$default_value);

					$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' 		=> 'property.uitts.index',
											'second_display'       => $second_display,
											'district_id'       => $this->district_id,
											'part_of_town_id'   => $this->part_of_town_id,
											'cat_id'        	=> $this->cat_id,
											'status'			=> $this->status
										)
									),
							'fields'	=> array
							(
	                       		'field' => array
	                       		(
									array
									( //boton 	CATEGORY
										'id' => 'btn_cat_id',
										'name' => 'cat_id',
										'value'	=> lang('Category'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 1
									),
									array
										( //boton 	STATUS
										'id' => 'btn_district_id',
										'name' => 'district_id',
										'value'	=> lang('District'),
										'type' => 'button',
										'style' => 'filter',
											'tab_index' => 2
									),
									array
									( //boton 	HOUR CATEGORY
										'id' => 'btn_status_id',
										'name' => 'status_id',
										'value'	=> lang('Status'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 3
									),
									array
									( //boton 	USER
										'id' => 'btn_user_id',
										'name' => 'user_id',
										'value'	=> lang('User'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 4
									),
									array
									(
										'type'	=> 'button',
										'id'	=> 'btn_export',
										'value'	=> lang('download'),
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
										'tab_index' => 7
									),
									array
									( //boton     SEARCH
										'id' => 'btn_search',
										'name' => 'search',
										'value'    => lang('search'),
										'type' => 'button',
										'tab_index' => 6
									),
									array
									( // TEXT INPUT
										'name'     => 'query',
										'id'     => 'txt_query',
										'value'    => $this->query,
										'type' => 'text',
										'onkeypress' => 'return pulsar(event)',
										'size'    => 28,
										'tab_index' => 5
									)
								),
			                   	'hidden_value' => array
			                	(
								array
									( //div values  combo_box_0
										'id' => 'values_combo_box_0',
										'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
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
									),
									array
									( //div values  combo_box_3
										'id' => 'values_combo_box_3',
										'value'	=> $this->bocommon->select2String($values_combo_box[3])
									)
								)
							)
						)
					);
				}
				else
				{
					$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' 		=> 'property.uitts.index',
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
										'tab_index' => 3
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
										'tab_index' => 6
									),
									array
									( // TEXT INPUT
										'name'     => 'query',
										'id'     => 'txt_query',
										'value'    => '',//$query,
										'type' => 'text',
										'onkeypress' => 'return pulsar(event)',
										'size'    => 28,
										'tab_index' => 5
									)
								),
			                   	'hidden_value' => array
			                	(
									array
									( //div values  combo_box_0
										'id' => 'values_combo_box_0',
										'value'	=> $this->bocommon->select2String($values_combo_box[2])
									)
								)
							)
						)
					);				
				}

				$dry_run = true;
			}

			if($dry_run)
			{
				$ticket_list = array();
			}
			else
			{
				$ticket_list = $this->bo->read($start_date,$end_date);
			}
			$this->bo->get_origin_entity_type();
			$uicols_related = $this->bo->uicols_related;
//_debug_array($uicols_related);
			$uicols = array();
			$i = 0;
			//$uicols['name'][0] = 'color';
			$uicols['name'][$i++] = 'priority';
			$uicols['name'][$i++] = 'id';
			$uicols['name'][$i++] = 'bgcolor';
			$uicols['name'][$i++] = 'subject';
			$uicols['name'][$i++] = 'location_code';
			$uicols['name'][$i++] = 'address';
//			$uicols['name'][$i++] = 'user';
			$uicols['name'][$i++] = 'assignedto';
			$uicols['name'][$i++] = 'entry_date';
			$uicols['name'][$i++] = 'status';
			foreach($uicols_related as $related)
			{
				$uicols['name'][$i++] = $related;			
			}

			$uicols['name'][$i++] = 'finnish_date';
			$uicols['name'][$i++] = 'delay';

			$uicols['name'][$i++] = 'child_date';
			$uicols['name'][$i++] = 'link_view';
			$uicols['name'][$i++] = 'lang_view_statustext';
			$uicols['name'][$i++] = 'text_view';

			$count_uicols_name = count($uicols['name']);

			$j = 0;
			$k = 0;
			if(is_array($ticket_list))
			{
				$status['X'] = array
				(
					'bgcolor'			=> '#5EFB6E',
					'status'			=> lang('closed'),
					'text_edit_status'	=> lang('Open'),
					'new_status' 		=> 'O'
				);

				$custom_status	= $this->bo->get_custom_status();

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

				foreach($ticket_list as $ticket)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='O')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value'] 	= lang('Open');
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

						$n = 0;
						foreach($uicols_related as $related)
						{
							if($uicols['name'][$k] == $related)
							{
								$datatable['rows']['row'][$j]['column'][$k]['format'] 	= 'link';
								$datatable['rows']['row'][$j]['column'][$k]['link']		= $ticket['child_date'][$n]['date_info'][0]['link'];
								$datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket['child_date'][$n]['date_info'][0]['entry_date'];
								$datatable['rows']['row'][$j]['column'][$k]['statustext']	= $ticket['child_date'][$n]['statustext'];
								$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
								
							}
							$n++;
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

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name' 			=> 'view',
					'statustext' 	=> lang('view the project'),
					'text'			=> lang('view'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uitts.view'
							)),
				'parameters'	=> $parameters
				);
			}

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'] == 'yes'
				&& $this->acl_edit)
			{
				
				foreach ($status as $status_code => $status_info)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name' 		=> 'status',
						'statustext' 	=> $status_info['status'],
						'text' 			=> lang('change to') . ':  ' .$status_info['status'],
						'confirm_msg'	=> lang('do you really want to change the status to %1',$status_info['status']),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'		=> 'property.uitts.edit_status',
									'edit_status'		=> true,
									'new_status'		=> $status_code,
									'second_display'	=> true,
									'sort'				=> $this->sort,
									'order'				=> $this->order,
									'cat_id'			=> $this->cat_id,
									'filter'			=> $this->filter,
									'user_filter'		=> $this->user_filter,
									'query'				=> $this->query,
									'district_id'		=> $this->district_id,
									'allrows'			=> $this->allrows,
									'delete'			=> 'dummy'// FIXME to trigger the json in property.js.
									)),
						'parameters'	=> $parameters
					);
				}
			}

			if($this->acl_add)
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
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= lang($uicols['name'][$i]);
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='priority' || $uicols['name'][$i]=='id' || $uicols['name'][$i]=='assignedto' || $uicols['name'][$i]=='finnish_date'|| $uicols['name'][$i]=='user'|| $uicols['name'][$i]=='entry_date')
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
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($ticket_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

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

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
		  	phpgwapi_yui::load_widget('paginator');

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
		    				  $json_row[$column['name']] = "<a href='".$column['link']."' title = '{$column['statustext']}'>" .$column['value']."</a>";
		    				}
		    				else
		    				{
		    				  $json_row[$column['name']] = $column['value'];
		    				}

		    				if($column['name'] == 'priority')
		    				{
			    				$_value = $column['value'];//str_repeat("||", abs(6 - 2*$column['value'])) . $column['value'];
			    				$json_row[$column['name']] = $_value;
								switch($column['value'])
								{
									case 1:
										$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[1].";'>".$_value."</div>";
										break;
										case 2:
										$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[2].";'>".$_value."</div>";
										break;
									case 3:
										$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[3].";'>".$_value."</div>";
										break;
								}
								unset($_value);

		    				}
		    			}
		    			$json['records'][] = $json_row;
	    			}
	    		}

				// values for control select
				$opt_cb_depend =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
		 		$default_value = array ('id'=>'','name'=>'!no part of town');
				array_unshift ($opt_cb_depend,$default_value);
					$json['hidden']['dependent'][] = array ( 'id' => $this->part_of_town_id,
                                                      'value' => $this->bocommon->select2String($opt_cb_depend)
													);
					// right in datatable
				if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
				{
					$json ['rights'] = $datatable['rowactions']['action'];
				}
		    		return $json;
			}
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
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
	
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
	
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', $this->_simple ? 'tts.index.simple' : 'tts.index' , 'property' );
		}

		function index2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts',
										'nextmatchs'));


			$second_display = phpgw::get_var('second_display', 'bool');

			$default_category = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');
			$default_status = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status']:'');
			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			if ($default_category && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_category;
				$this->district_id		= $default_category;
			}

			if ($default_status && !$second_display)
			{
				$this->bo->status_id	= $default_status;
				$this->status_id	= $default_status;
			}

			$bgcolor_array[1]	= '#da7a7a';
			$bgcolor_array[2]	= '#dababa';
			$bgcolor_array[3]	= '#dadada';


			$ticket_list = $this->bo->read($start_date,$end_date);
			$uicols = $this->bo->uicols;

//_debug_array($uicols);
//_debug_array($ticket_list);
			while (is_array($ticket_list) && list(,$ticket) = each($ticket_list))
			{
				if ($ticket['status']=='O')
				{
					$status = lang('Open');
				}
				else
				{
					$status = lang('Closed');
				}

				$content[] = array
				(
					'id'					=> $ticket['id'],
					'bgcolor'				=> $bgcolor[$ticket['priority']],
					'new_ticket'			=> $ticket['new_ticket']?$ticket['new_ticket']:'',
					'priostr'				=> str_repeat("||", $ticket['priority']),
					'subject'				=> $ticket['subject'],
					'location_code'			=> $ticket['location_code'],
					'address'				=> $ticket['address'],
					'date'					=> $ticket['entry_date'],
					'finnish_date'			=> $ticket['finnish_date'],
					'delay'					=> (isset($ticket['delay'])?$ticket['delay']:''),
					'user'					=> $ticket['user'],
					'assignedto'			=> $ticket['assignedto'],
					'child_date'			=> $ticket['child_date'],
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view2', 'id'=> $ticket['id'])),
					'lang_view_statustext'	=> lang('view the ticket'),
					'text_view'				=> lang('view'),
					'status'				=> $status,
				);
			}

			$table_header[] = array
			(
				'sort_priority'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'priority',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),

				'lang_priority'		=> lang('Priority'),
				'lang_priority_statustext'		=> lang('Sort the tickets by their priority'),

				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_tts_tickets.id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),

				'lang_id'		=> lang('ID'),
				'lang_id_statustext'	=> lang('Sort the tickets by their ID'),

				'lang_subject'		=> lang('Subject'),
				'lang_time_created'	=> lang('Started'),
				'lang_view'		=> lang('view'),
				'lang_location_code'	=> lang('Location'),
				'lang_address'		=> lang('Address'),
				'lang_user'		=> lang('user'),
				'sort_assigned_to'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'assignedto',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'lang_assigned_to'	=> lang('Assigned to'),
				'sort_opened_by'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'user_lid',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'sort_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_tts_tickets.entry_date',
											'order'	=> $this->order,
											'extra' => array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'sort_finnish_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'finnish_date',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=> $this->cat_id,
																	'filter'	=> $this->status_id,
																	'user_id'	=> $this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=> $this->query,
																	'second_display'=> true,
																	'allrows'	=> $this->allrows,
																	'start_date'	=> $start_date,
																	'end_date'	=> $end_date
																	)
										)),
				'lang_finnish_date'	=> lang('finnish date'),
				'lang_delay'		=> lang('delay'),
				'lang_finnish_statustext'=> lang('presumed finnish date'),
				'lang_opened_by'	=> lang('Opened by'),
				'lang_status'		=> lang('Status')
			);

			for ($i=0;$i<count($uicols);$i++)
			{
				$table_header[0]['extra'][$i]['header'] = $uicols[$i];
			}

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a ticket'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.add2'))
			);

			$link_data = array
			(
				'menuaction'		=> 'property.uitts.index2',
				'second_display'	=> true,
				'sort'				=> $this->sort,
				'order'				=> $this->order,
				'cat_id'			=> $this->cat_id,
				'filter'			=> $this->status_id,
				'user_id'		=> $this->user_id,
				'query'				=> $this->query,
				'district_id'		=> $this->district_id,
				'start_date'		=> $start_date,
				'end_date'			=> $end_date,
				'allrows'			=> $this->allrows
			);

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');

			$GLOBALS['phpgw']->preferences->read();
			$autorefresh ='';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['refreshinterval']))
			{
				$autorefresh = $GLOBALS['phpgw_info']['user']['preferences']['property']['refreshinterval'].'; URL='.$GLOBALS['phpgw']->link('/index.php',$link_data);
			}

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_date_search	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_download = array
			(
				'menuaction' 		=> 'property.uitts.download2',
				'second_display'	=> true,
				'sort'				=> $this->sort,
				'order'				=> $this->order,
				'cat_id'			=> $this->cat_id,
				'filter'			=> $this->status_id,
				'user_id'		=> $this->user_id,
				'query'				=> $this->query,
				'district_id'		=> $this->district_id,
				'allrows'			=> $this->allrows,
				'start_date'		=> $start_date,
				'end_date'			=> $end_date,
				'start'				=> $this->start
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('search_field'));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'lang_download'					=> 'download',
				'link_download'					=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'				=> lang('Download table to your browser'),

				'start_date'					=> $start_date,
				'end_date'						=> $end_date,
				'lang_none'						=> lang('None'),
				'lang_date_search'				=> lang('Date search'),
				'lang_date_search_help'			=> lang('Narrow the search by dates'),
				'link_date_search'				=> $link_date_search,

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'autorefresh'					=> $autorefresh,
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($ticket_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_name'					=> 'filter',
				'filter_list'					=> $this->bo->filter(array('format' => 'filter', 'filter'=> $this->status_id,'default' => 'open')),
				'lang_show_all'					=> lang('Open'),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header2'					=> $table_header,
				'values2'						=> (isset($content)?$content:''),
				'table_add'						=> $table_add,
			);

			$appname					= lang('helpdesk');
			$function_msg					= lang('list ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list2' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function add()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$bolocation		= CreateObject('property.bolocation');

			$values		= phpgw::get_var('values');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			if ((isset($values['cancel']) && $values['cancel']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index'));
			}

//------------------- start ticket from other location
			$bypass 		= phpgw::get_var('bypass', 'bool');
			if(isset($_POST) && $_POST && isset($bypass) && $bypass)
			{
				$boadmin_entity		= CreateObject('property.boadmin_entity');
				$location_code 		= phpgw::get_var('location_code');
				$values['descr']	= phpgw::get_var('descr');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id		= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');

				$origin		= phpgw::get_var('origin');
				$origin_id	= phpgw::get_var('origin_id', 'int');

				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num, 'view' => true));
				}
			}

			if(isset($values['origin']) && $values['origin'])
			{
				$origin		= $values['origin'];
				$origin_id	= $values['origin_id'];
			}

			$interlink 	= CreateObject('property.interlink');

			if(isset($origin) && $origin)
			{
				unset($values['origin']);
				unset($values['origin_id']);
				$values['origin'][0]['location']= $origin;
				$values['origin'][0]['descr']= $interlink->get_location_name($origin);
				$values['origin'][0]['data'][]= array(
					'id'	=> $origin_id,
					'link'	=> $interlink->get_relation_link(array('location' => $origin), $origin_id),
					);
			}
//_debug_array($insert_record);
			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);


				if(!$values['subject'] && isset($this->bo->config->config_data['tts_mandatory_title']) && $this->bo->config->config_data['tts_mandatory_title'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a title !'));
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
				}

				if(!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][]=array('msg'=>lang('Please give som details !'));
				}

				if((!isset($values['location']['loc1']) || !$values['location']['loc1']) && (!isset($values['extra']['p_num']) || !$values['extra']['p_num']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location - or an entity!'));
				}

				if(!$values['assignedto'] && !$values['group_id'])
				{
					$_responsible = execMethod('property.boresponsible.get_responsible', $values);
					if(!$_responsible)
					{
						$receipt['error'][]=array('msg'=>lang('Please select a person or a group to handle the ticket !'));
					}
					else
					{
						if( $GLOBALS['phpgw']->accounts->get($_responsible)->type == phpgwapi_account::TYPE_USER )
						{
							$values['assignedto'] = $_responsible;
						}
						else
						{
							$values['group_id'] = $_responsible;
						}
					}
					unset($_responsible);
				}

				if(!isset($values['priority']) || !$values['priority'])
				{
					$_priority = $this->bo->get_priority_list();
					$values['priority'] = count($_priority);
					unset($_priority);
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->add($values);

//------------ files
					$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);

					if($values['file_name'] && $receipt['id'])
					{
						$bofiles	= CreateObject('property.bofiles');
						$to_file = $bofiles->fakebase . '/fmticket/' . $receipt['id'] . '/' . $values['file_name'];

						if($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("fmticket/{$receipt['id']}");
							$bofiles->vfs->override_acl = 1;

							if(!$bofiles->vfs->cp(array (
								'from'	=> $_FILES['file']['tmp_name'],
								'to'	=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}
					}
//--------------end files
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');

					if ((isset($values['save']) && $values['save']))
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index'));
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.view', 'id' => $receipt['id'], 'tab' =>'general'));					
					}
				}
				else
				{
					if(isset($values['location']) && $values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
					}
					if(isset($values['extra']['p_num']) && $values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> (isset($values['location_data'])?$values['location_data']:''),
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> true,
						'lookup_type'	=> 'form',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
						'entity_data'	=> (isset($values['p'])?$values['p']:'')
						));


			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array(
						'contact_id'		=> $ticket['contact_id'],
						'field'				=> 'contact',
						'type'				=> 'form'));

			$link_data = array
			(
				'menuaction'	=> 'property.uitts.add'
			);

			if(!isset($values['assignedto']))
			{
				$values['assignedto']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault']:'');
			}
			if(!isset($values['group_id']))
			{
				$values['group_id']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']:'');
			}

			if(!isset($values['cat_id']))
			{
				$this->cat_id = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category']:'');
			}
			else
			{
				$this->cat_id = $values['cat_id'];
			}

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');


			if(!$this->_simple && $this->_show_finnish_date)
			{
				$jscal = CreateObject('phpgwapi.jscalendar');
				$jscal->add_listener('values_finnish_date');
			}

			$data = array
			(
				'contact_data'					=> $contact_data,
				'simple'						=> $this->_simple,
				'show_finnish_date'				=> $this->_show_finnish_date,
				'value_origin'					=> isset($values['origin']) ? $values['origin'] : '',
				'value_origin_type'				=> (isset($origin)?$origin:''),
				'value_origin_id'				=> (isset($origin_id)?$origin_id:''),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'				=> $location_data,
				'lang_assign_to'			=> lang('Assign to'),
				'lang_no_user'				=> lang('Select user'),
				'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'			=> 'values[assignedto]',
				'user_list'				=> $this->bocommon->get_user_list_right2('select',4,$values['assignedto'],$this->acl_location),

				'lang_group'				=> lang('Group'),
				'lang_no_group'				=> lang('No group'),
				'group_list'				=> $this->bocommon->get_group_list('select',$values['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'			=> 'values[group_id]',

				'lang_priority'				=> lang('Priority'),
				'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'			=> 'values[priority]',
				'priority_list'				=> $this->bo->get_priority_list((isset($values['priority'])?$values['priority']:'')),

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_subject'				=> lang('Subject'),
				'lang_subject_statustext'		=> lang('Enter the subject of this ticket'),

				'lang_details'				=> lang('Details'),
				'lang_details_statustext'		=> lang('Enter the details of this ticket'),
				'lang_category'				=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_send'					=> lang('send'),
				'value_details'				=> (isset($values['details'])?$values['details']:''),
				'value_subject'				=> (isset($values['subject'])?$values['subject']:''),

				'lang_finnish_date'			=> lang('finnish date'),
				'value_finnish_date'			=> (isset($values['finnish_date'])?$values['finnish_date']:''),
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),
				'lang_finnish_date_statustext'		=> lang('Select the estimated date for closing the task'),

				'lang_cancel_statustext'			=> lang('Back to the ticket list'),
				'lang_send_statustext'			=> lang('Save the entry and return to list'),
				'lang_save_statustext'			=> lang('Save the ticket'),
				'lang_no_cat'					=> lang('no category'),
				'lang_town_statustext'			=> lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_part_of_town'				=> lang('Part of town'),
				'lang_no_part_of_town'			=> lang('No part of town'),
				'cat_select'				=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),

				'mailnotification'			=> (isset($this->bo->config->config_data['mailnotification'])?$this->bo->config->config_data['mailnotification']:''),
				'lang_mailnotification'			=> lang('Send e-mail'),
				'lang_mailnotification_statustext'	=> lang('Choose to send mailnotification'),
				'pref_send_mail'			=> (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:''),
				'fileupload'				=> (isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:''),
			);

//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('add ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','files'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function add2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_ADD, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$bolocation		= CreateObject('property.bolocation');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','files'));


			if(!$this->tenant_id)
			{
				$receipt['error'][]=array('msg'=>lang('No Tenant selected !'));
			}
			else
			{
				$values['extra']['tenant_id'] = $this->tenant_id;
				$values['location_code'] = $bolocation->get_tenant_location($this->tenant_id);


				if(!$values['location_code'])
				{
					$receipt['error'][]=array('msg'=>lang('No location for this tenant!'));
				}
				else
				{
					$location = explode('-',$values['location_code']);
					$i = 1;
					foreach ($location as $entry)
					{
						$values['location']["loc{$i}"]=$entry;
						$i++;
					}
				}
				if(is_array($values['location_code']))
				{
					$receipt['error'][]=array('msg'=>lang('Several locations for this tenant!'));
				}
			}

			$values['location_data'] = $bolocation->read_single($values['location_code'],array('extra'=>array('tenant_id'=>$this->tenant_id)));

			$values['street_name'] = $values['location_data']['street_name'];
			$values['street_number'] = $values['location_data']['street_number'];


			$values['assignedto']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault']:'');
			if(!$values['assignedto'])
			{
				$receipt['error'][]=array('msg'=>lang('Please set default assign to in preferences for user %1!', $GLOBALS['phpgw']->accounts->id2name($this->account)));
			}

			$values['group_id']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']:'');

			$values['cat_id'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category']:'');

			if(!$values['cat_id'])
			{
				$receipt['error'][]=array('msg'=>lang('Please set default category in preferences for user %1!', $GLOBALS['phpgw']->accounts->id2name($this->account)));
			}

			if (isset($values['save']))
			{

// FIX this : relevant?
/*				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				while (is_array($insert_record['extra']) && list($key,$column) = each($insert_record['extra']))
				{
					if(isset($_POST[$key]) && $_POST[$key])
					{
						$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
					}
				}
*/
				if(!$values['subject'])
				{
					$receipt['error'][]=array('msg'=>lang('Please type a subject for this ticket !'));
				}

				if(!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][]=array('msg'=>lang('Please give som details !'));
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->add($values);
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index2'));
				}
				else
				{
					if(isset($values['extra']['p_num']) && $values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> (isset($values['location_data'])?$values['location_data']:''),
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> true,
						'lookup_type'	=> 'view',
						'lookup_entity'	=> false,
						'entity_data'	=> false
						));

			$link_data = array
			(
				'menuaction'	=> 'property.uitts.add2'
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}


			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');

			$data = array
			(
				'lang_dateformat' 					=> strtolower($dateformat),
				'dateformat_validate'				=> $dateformat_validate,
				'onKeyUp'							=> $onKeyUp,
				'onBlur'							=> $onBlur,
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'						=> $location_data,
				'lang_assign_to'					=> lang('Assign to'),
				'lang_no_user'						=> lang('Select user'),
				'lang_user_statustext'				=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'					=> 'values[assignedto]',
				'user_list'							=> $this->bocommon->get_user_list_right2('select',4,$values['assignedto'],$this->acl_location),

				'lang_group'						=> lang('Group'),
				'lang_no_group'						=> lang('No group'),
				'group_list'						=> $this->bocommon->get_group_list('select',$values['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'					=> 'values[group_id]',

				'lang_priority'						=> lang('Priority'),
				'lang_priority_statustext'			=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'				=> 'values[priority]',
				'priority_list'						=> $this->bo->get_priority_list((isset($values['priority'])?$values['priority']:'')),

				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index2')),
				'lang_subject'						=> lang('Subject'),
				'lang_subject_statustext'			=> lang('Enter the subject of this ticket'),

				'lang_details'						=> lang('Details'),
				'lang_details_statustext'			=> lang('Enter the details of this ticket'),
				'lang_category'						=> lang('category'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_details'						=> (isset($values['details'])?$values['details']:''),
				'value_subject'						=> (isset($values['subject'])?$values['subject']:''),

				'lang_finnish_date'					=> lang('finnish date'),
				'value_finnish_date'				=> (isset($values['finnish_date'])?$values['finnish_date']:''),

				'lang_done_statustext'				=> lang('Back to the ticket list'),
				'lang_save_statustext'				=> lang('Save the ticket'),
				'lang_no_cat'						=> lang('no category'),
				'lang_town_statustext'				=> lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_part_of_town'					=> lang('Part of town'),
				'lang_no_part_of_town'				=> lang('No part of town'),
				'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),
				'mailnotification'					=> (isset($this->bo->config->config_data['mailnotification'])?$this->bo->config->config_data['mailnotification']:''),
				'lang_mailnotification'				=> lang('Send e-mail'),
				'lang_mailnotification_statustext'	=> lang('Choose to send mailnotification'),
				'pref_send_mail'					=> (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:''),
				'lang_contact_phone'				=> lang('contact phone'),
				'lang_contact_phone_statustext'		=> lang('contact phone'),
				'value_contact_phone'				=> (isset($values['contact_phone'])?$values['contact_phone']:''),

				'lang_contact_email'				=> lang('contact email'),
				'lang_contact_email_statustext'		=> lang('contact email'),
				'value_contact_email'				=> (isset($values['contact_email'])?$values['contact_email']:''),
			);

//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('add ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add2' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bolocation	= CreateObject('property.bolocation');

			$id = phpgw::get_var('id', 'int', 'GET');
			$values = phpgw::get_var('values');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			$values['ecodimb']			= phpgw::get_var('ecodimb');
			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts', 'files'));

			if(isset($values['save']))
			{
				if(!$this->acl_edit)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>4, 'acl_location'=> $this->acl_location));
				}

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				if(isset($values['budget']) && $values['budget'] && !ctype_digit($values['budget']))
				{
					$values['budget'] = (int)$values['budget'];
					$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter an integer !'));
				}

				if(isset($values['takeover']) && $values['takeover'])
				{
					$values['assignedto'] = $this->account;
				}
				$receipt = $this->bo->update_ticket($values,$id);
				if (isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
				{
					$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
				}

//--------- files
				$bofiles	= CreateObject('property.bofiles');
				if(isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/fmticket/{$id}/", $values);
				}

				$values['file_name']=str_replace(' ','_',$_FILES['file']['name']);

				if($values['file_name'])
				{
					$to_file = $bofiles->fakebase . '/fmticket/' . $id . '/' . $values['file_name'];

					if($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][]=array('msg'=>lang('This file already exists !'));
					}
					else
					{
						$bofiles->create_document_dir("fmticket/{$id}");
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
				}
	//			$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index'));
			}
//---------end files
			$ticket = $this->bo->read_single($id);

			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$order_link				= '';
			$add_to_project_link	= '';
			$request_link			='';

			if($GLOBALS['phpgw']->acl->check('.project.request', PHPGW_ACL_ADD, 'property'))
			{
				$request_link_data = array
				(
					'menuaction'		=> 'property.uirequest.edit',
					'bypass'			=> true,
					'location_code'		=> $ticket['location_code'],
					'p_num'				=> $ticket['p_num'],
					'p_entity_id'		=> $ticket['p_entity_id'],
					'p_cat_id'			=> $ticket['p_cat_id'],
					'tenant_id'			=> $ticket['tenant_id'],
					'origin'			=> '.ticket',
					'origin_id'			=> $id
				);

				$request_link			= $GLOBALS['phpgw']->link('/index.php',$request_link_data);
			}

			if($GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'property'))
			{
				$order_link_data = array
				(
					'menuaction'		=> 'property.uiproject.edit',
					'bypass'			=> true,
					'location_code'		=> $ticket['location_code'],
					'p_num'				=> $ticket['p_num'],
					'p_entity_id'		=> $ticket['p_entity_id'],
					'p_cat_id'			=> $ticket['p_cat_id'],
					'tenant_id'			=> $ticket['tenant_id'],
					'origin'			=> '.ticket',
					'origin_id'			=> $id
				);

				$add_to_project_link_data = array
				(
					'menuaction'		=> 'property.uiproject.index',
					'from'				=> 'workorder',
					'lookup'			=> true,
					'query'				=> isset($ticket['location_data']['loc1']) ? $ticket['location_data']['loc1'] : '',
		//			'p_num'				=> $ticket['p_num'],
		//			'p_entity_id'		=> $ticket['p_entity_id'],
		//			'p_cat_id'			=> $ticket['p_cat_id'],
					'tenant_id'			=> $ticket['tenant_id'],
					'origin'			=> '.ticket',
					'origin_id'			=> $id
				);

				$order_link				= $GLOBALS['phpgw']->link('/index.php',$order_link_data);
				$add_to_project_link	= $GLOBALS['phpgw']->link('/index.php',$add_to_project_link_data);

			}

			$form_link = array
			(
				'menuaction'	=> 'property.uitts.view',
				'id'		=> $id
			);

/*
			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_additional_notes[] = array
			(
				'lang_count'		=> '#',
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_note'		=> lang('Note'),
			);
*/
//_debug_array($ticket['location_data']);

			if($ticket['origin'] || $ticket['target'])
			{
				$lookup_type	= 'view';
				$type_id		= count(explode('-',$ticket['location_data']['location_code']));
			}
			else
			{
				$lookup_type	= 'form';
				$type_id		= -1;
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $ticket['location_data'],
						'type_id'	=> $type_id,
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> (isset($ticket['location_data']['tenant_id'])?$ticket['location_data']['tenant_id']:''),
						'lookup_type'	=> $lookup_type,
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
						'entity_data'	=> (isset($ticket['p'])?$ticket['p']:'')
						));
			unset($type_id);

			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array(
						'contact_id'		=> $ticket['contact_id'],
						'field'				=> 'contact',
						'type'				=> 'form'));


			if($ticket['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if ($ticket['cat_id'])
			{
				$this->cat_id = $ticket['cat_id'];
			}

			$start_entity	= $this->bocommon->get_start_entity('ticket');

			$link_entity = array();
			if (isset($start_entity) AND is_array($start_entity))
			{
				$i=0;
				foreach($start_entity as $entry)
				{
					if($GLOBALS['phpgw']->acl->check(".entity.{$entry['id']}", PHPGW_ACL_ADD, 'property'))
					{
						$link_entity[$i]['link'] = $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'property.uientity.edit',
							'bypass'		=> true,
							'location_code'		=> $ticket['location_code'],
							'entity_id'		=> $entry['id'],
							'p_num'			=> $ticket['p_num'],
							'p_entity_id'		=> $ticket['p_entity_id'],
							'p_cat_id'		=> $ticket['p_cat_id'],
							'tenant_id'		=> $ticket['tenant_id'],
							'origin'		=> '.ticket',
							'origin_id'		=> $id
						));
						$link_entity[$i]['name']	= $entry['name'];
						$i++;
					}
				}
			}

//_debug_array($link_entity);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_file_data = array
			(
				'menuaction'	=> 'property.uitts.view_file',
				'id'		=> $id
			);

			if(!$this->_simple && $this->_show_finnish_date)
			{
				$jscal = CreateObject('phpgwapi.jscalendar');
				$jscal->add_listener('values_finnish_date');
			}

			//---datatable settings---------------------------------------------------	
			$datavalues[0] = array
			(
					'name'					=> "0",
					'values' 				=> json_encode($additional_notes),
					'total_records'			=> count($additional_notes),
					'is_paginator'			=> 0,
					'footer'				=> 0
			);					
       		$myColumnDefs[0] = array
       		(
       			'name'		=> "0",
       			'values'	=>	json_encode(array(	array('key' => 'value_count',	'label'=>'#',			'sortable'=>true,'resizeable'=>true),
									       			array('key' => 'value_date',	'label'=>lang('Date'),'sortable'=>true,'resizeable'=>true),
									       			array('key' => 'value_user',	'label'=>lang('User'),'sortable'=>true,'resizeable'=>true),
		       				       					array('key' => 'value_note',	'label'=>lang('Note'),'sortable'=>true,'resizeable'=>true)))
			);		
			$datavalues[1] = array
			(
					'name'					=> "1",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'is_paginator'			=> 0,
					'footer'				=> 0
			);					
       		$myColumnDefs[1] = array
       		(
       			'name'		=> "1",
       			'values'	=>	json_encode(array(	array('key' => 'value_date',	'label'=>lang('Date'),	'sortable'=>true,'resizeable'=>true),
									       			array('key' => 'value_user',	'label'=>lang('User'),	'sortable'=>true,'resizeable'=>true),
									       			array('key' => 'value_action',	'label'=>lang('Action'),	'sortable'=>true,'resizeable'=>true),
									       			array('key' => 'value_old_value','label'=>lang('old value'),	'sortable'=>true,'resizeable'=>true),
		       				       					array('key' => 'value_new_value','label'=>lang('New value'),'sortable'=>true,'resizeable'=>true)))
			);	
			
			
			$link_to_files = (isset($this->bo->config->config_data['files_url'])?$this->bo->config->config_data['files_url']:'');
			
			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);
			
			for($z=0; $z<count($ticket['files']); $z++)
			{				
				if ($link_to_files != '')
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_to_files.'/'.$ticket['files'][$z]['directory'].'/'.$ticket['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$ticket['files'][$z]['name'].'</a>';
				}
				else
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$ticket['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$ticket['files'][$z]['name'].'</a>';
				}				
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$ticket['files'][$z]['name'].'" title="'.lang('Check to delete file').'" style="cursor:help">';
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
			
			//----------------------------------------------datatable settings--------			
			
			// -------- start order section
			$order_read 			= $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property');
			$order_add 				= $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property');
			$order_edit 			= $this->acl->check('.ticket.order', PHPGW_ACL_EDIT, 'property');
			
			$access_order = false;
			if($order_read || $order_add || $order_edit)
			{
				$access_order = true;
			}

			if($order_read)
			{
				$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'			=> $ticket['vendor_id'],
						'vendor_name'		=> $ticket['vendor_name']));

				$vendor_email = execMethod('property.sowo_hour.get_email', $ticket['vendor_id']);

				$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $ticket['b_account_id'] ? $ticket['b_account_id'] : $ticket['b_account_id'],
						'b_account_name'	=> $ticket['b_account_name'],
						'disabled'			=> !!$ticket['b_account_id']));

				$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array
					(
						'ecodimb'			=> $ticket['ecodimb'] ? $ticket['ecodimb'] : $ticket['ecodimb'],
						'ecodimb_descr'		=> $ticket['ecodimb_descr'],
						'disabled'			=> !!$ticket['ecodimb']
					)
				);
			
				// approval
				$supervisor_id = 0;

				if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'] )
				{
					$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
				}

				$need_approval = isset($this->bo->config->config_data['workorder_approval']) ? $this->bo->config->config_data['workorder_approval'] : '';

				$supervisor_email = array();
				if ($supervisor_id && $need_approval)
				{
					$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
					$supervisor_email[] = array
					(
						'id'	  => $supervisor_id,
						'address' => $prefs['email'],
					);
					if ( isset($prefs['approval_from']) )
					{
						$prefs2 = $this->bocommon->create_preferences('property', $prefs['approval_from']);

						if(isset($prefs2['email']))
						{
							$supervisor_email[] = array
							(
								'id'	  => $prefs['approval_from'],
								'address' => $prefs2['email'],
							);
							$supervisor_email = array_reverse($supervisor_email);
						}
						unset($prefs2);
					}
					unset($prefs);
				}
				// approval					
			}


			if(isset($values['send_order']) && $values['send_order'])
			{
				if(isset($values['vendor_email']) && $values['vendor_email'])
				{
					$subject = lang(workorder).": {$ticket['order_id']}";
				//	$body = lang('Category').': '. $this->bo->get_category_name($ticket['cat_id']) ."\n";
					$body = lang('order id').": {$ticket['order_id']}<br>";
					$body .= lang('from').': ';
					if(isset($this->bo->config->config_data['org_name']))
					{
						$body .= "{$this->bo->config->config_data['org_name']}::";
					}
					$body .= "{$GLOBALS['phpgw_info']['user']['fullname']}<br>";
					$body .= "RessursNr: {$GLOBALS['phpgw_info']['user']['preferences']['property']['ressursnr']}<br>";
		//			$body .= lang('Location').': '. $ticket['location_code'] ."<br>";
					$body .= lang('Address').': '. $ticket['address'] ."<br>";

					$address_element = $this->bo->get_address_element($ticket['location_code']);

					foreach($address_element as $address_entry)
					{
						$body .= $address_entry['text'].': '. $address_entry['value'] ."<br>";
					}

					if(isset($contact_data['value_contact_name']) && $contact_data['value_contact_name'])
					{
						$body .= lang(contact).': '. $contact_data['value_contact_name'];
					}
					if(isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
					{
						$body .= "/ <a href='mailto:{$contact_data['value_contact_email']}'>{$contact_data['value_contact_email']}</a>";
					}
					if(isset($contact_data['value_contact_tel']) && $contact_data['value_contact_tel'])
					{
						$body .= " / {$contact_data['value_contact_tel']}<br>";
					}

					if(isset($this->bo->config->config_data['order_email_footer']))
					{
						$body .= "{$this->bo->config->config_data['order_email_footer']}<br>";
					}

					$body .= '<h2>' . lang('description') .'</h2>';
					$body .= nl2br($ticket['order_descr']);


					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
						$coordinator_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

						$bcc = $coordinator_email;
						if(isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
						{
							$bcc .= ";{$contact_data['value_contact_email']}";
						}

						$rcpt = $GLOBALS['phpgw']->send->msg('email', $values['vendor_email'], $subject, stripslashes($body), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html', '', '' , true);
						if($rcpt)
						{
							$receipt['message'][]=array('msg'=>lang('%1 is notified',$_address));
							$historylog	= CreateObject('property.historylog','tts');
							$historylog->add('M',$id,"{$values['vendor_email']}");
							$receipt['message'][]=array('msg'=>lang('Workorder is sent by email!'));
							$action_params = array
							(
								'appname'			=> 'property',
								'location'			=> '.ticket',
								'id'				=> $id,
								'responsible'		=> $values['vendor_id'],
								'responsible_type'  => 'vendor',
								'action'			=> 'remind',
								'remark'			=> '',
								'deadline'			=> ''
							);
				
							$reminds = execMethod('property.sopending_action.set_pending_action', $action_params);
						}
					}
					else
					{
						$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
					}
				}
			}
			// start approval
			if ($values['approval'] && $values['mail_address'] && $this->bo->config->config_data['workorder_approval'])
			{
				$coordinator_name=$GLOBALS['phpgw_info']['user']['fullname'];
				$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

				$subject = lang(Approval).": ".$ticket['order_id'];
				$message = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view', 'id'=> $id)).'">' . lang('Workorder %1 needs approval',$ticket['order_id']) .'</a>';

				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					$action_params = array
					(
						'appname'			=> 'property',
						'location'			=> '.ticket',
						'id'				=> $id,
						'responsible'		=> '',
						'responsible_type'  => 'user',
						'action'			=> 'approval',
						'remark'			=> '',
						'deadline'			=> ''
					);
					$bcc = '';//$coordinator_email;
					foreach ($values['mail_address'] as $_account_id => $_address)
					{
						if(isset($values['approval'][$_account_id]) && $values['approval'][$_account_id])
						{
							$action_params['responsible'] = $_account_id;
							$rcpt = $GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html');
							if($rcpt)
							{
								$receipt['message'][]=array('msg'=>lang('%1 is notified',$_address));
							}

							 execMethod('property.sopending_action.set_pending_action', $action_params);
						}
					}
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
				}
			}
	
			// end approval

			// -------- end order section


			$data = array
			(
				'access_order'					=> $access_order,
				'currency'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'value_order_id'				=> $ticket['order_id'],
				'value_order_descr'				=> $ticket['order_descr'],
				'vendor_data'					=> $vendor_data,
				'b_account_data'				=> $b_account_data,
				'ecodimb_data' 					=> $ecodimb_data,
				'value_budget'					=> $ticket['budget'],
				'value_actual_cost'				=> $ticket['actual_cost'],
				'need_approval'					=> $need_approval,
				'value_approval_mail_address'	=> $supervisor_email,
				'vendor_email'					=> $vendor_email,

				'contact_data'					=> $contact_data,
				'lookup_type'					=> $lookup_type,
				'simple'						=> $this->_simple,
				'show_finnish_date'				=> $this->_show_finnish_date,
				'tabs'							=> self::_generate_tabs(true),
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
			
				'value_origin'					=> $ticket['origin'],
				'value_target'					=> $ticket['target'],
				'value_finnish_date'			=> $ticket['finnish_date'],
				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'				=> lang('Select date'),

				'link_entity'					=> $link_entity,
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'location_data'					=> $location_data,

				'status_name'					=> 'values[status]',
				'value_status'					=> $ticket['status'],
				'status_list'					=> $this->bo->get_status_list($ticket['status']),

				'lang_no_user'					=> lang('Select user'),
				'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'				=> 'values[assignedto]',
				'value_assignedto_id'			=> $ticket['assignedto'],
				'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$ticket['assignedto'],$this->acl_location),

				'lang_no_group'					=> lang('No group'),
				'group_list'					=> $this->bocommon->get_group_list('select',$ticket['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'				=> 'values[group_id]',
				'value_group_id'				=> $ticket['group_id'],

				'lang_takeover'					=> (isset($values['assignedto']) && $values['assignedto'] != $this->account)  || (!isset($values['assignedto']) || !$values['assignedto']) ? lang('take over') : '',

				'value_priority'				=> $ticket['priority'],
				'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'			=> 'values[priority]',
				'priority_list'					=> $this->bo->get_priority_list($ticket['priority']),

				'lang_no_cat'					=> lang('no category'),
				'value_cat_id'					=> $this->cat_id,
				'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),
				'value_category_name'			=> $ticket['category_name'],

				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$form_link),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uitts.index')),
				'value_subject'					=> $ticket['subject'],

				'value_id'						=> '[ #'. $id . ' ] - ',

				'value_details'					=> $ticket['details'],

				'value_opendate'				=> $ticket['entry_date'],
				'value_assignedfrom'			=> $ticket['user_name'],
				'value_assignedto_name'			=> isset($ticket['assignedto_name'])?$ticket['assignedto_name']:'',

				'additional_notes'				=> $additional_notes,
				'record_history'				=> $record_history,
				'request_link'					=> $request_link,
				'order_link'					=> $order_link,
				'add_to_project_link'			=> $add_to_project_link,
				'lang_name'						=> lang('name'),
				'contact_phone'					=> $ticket['contact_phone'],
				'mailnotification'				=> isset($this->bo->config->config_data['mailnotification'])?true:'',
				'pref_send_mail'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:'',
				'fileupload'					=> isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:'',
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'					=> isset($this->bo->config->config_data['files_url'])?$this->bo->config->config_data['files_url']:'',
				'files'							=> isset($ticket['files'])?$ticket['files']:'',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_file_action'				=> lang('Delete file'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_file_action_statustext'	=> lang('Check to delete file'),
				'lang_upload_file'				=> lang('Upload file'),
				'lang_file_statustext'			=> lang('Select file to upload'),
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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tts.view', 'property' );
			//-----------------------datatable settings---
			
			
			
			$appname		= lang('helpdesk');
			$function_msg	= lang('view ticket detail');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$bolocation	= CreateObject('property.bolocation');

			$id = phpgw::get_var('id', 'int', 'GET');
			$values = phpgw::get_var('values');
			$receipt = '';

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts'));

			if(isset($values['save']))
			{
				if(!$this->acl->check('.ticket.external', PHPGW_ACL_ADD, 'property'))
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>4, 'acl_location'=> '.ticket.external'));
				}

				$values['assignedto'] = 'ignore';
				$values['group_id'] = 'ignore';
				$values['cat_id'] = 'ignore';

				$so	= CreateObject('property.sotts');
				$so->acl_location	= '.ticket.external';
				$receipt = $so->update_ticket($values,$id);
				if (isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
				{
					$receipt = $this->bo->mail_ticket($id, $so->fields_updated, $receipt);
				}
			}

			$ticket = $this->bo->read_single($id);

			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$form_link = array
			(
				'menuaction'	=> 'property.uitts.view2',
				'id'		=> $id
			);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_additional_notes[] = array
			(
				'lang_count'		=> '#',
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_note'		=> lang('Note'),
			);

//_debug_array($ticket['location_data']);

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $ticket['location_data'],
						'type_id'	=> count(explode('-',$ticket['location_data']['location_code'])),
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> (isset($ticket['location_data']['tenant_id'])?$ticket['location_data']['tenant_id']:''),
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
						'entity_data'	=> (isset($ticket['p'])?$ticket['p']:'')
						));


			if($ticket['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if ($ticket['cat_id'])
			{
				$this->cat_id = $ticket['cat_id'];
			}


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			if (isset($ticket['origin']) AND is_array($ticket['origin']))
			{
				for ($i=0;$i<count($ticket['origin']);$i++)
				{
					$ticket['origin'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$ticket['origin'][$i]['link']);

					if(substr($ticket['origin'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$ticket['origin'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject('property.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$ticket['origin'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$ticket['origin'][$i]['descr']= lang($ticket['origin'][$i]['type']);
					}
				}
			}

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');

			$data = array
			(
				'value_origin'					=> (isset($ticket['origin'])?$ticket['origin']:''),
				'lang_dateformat' 				=> strtolower($dateformat),
				'dateformat_validate'			=> $dateformat_validate,
				'onKeyUp'						=> $onKeyUp,
				'onBlur'						=> $onBlur,
				'lang_finnish_date'				=> lang('finnish date'),
				'value_finnish_date'			=> $ticket['finnish_date'],

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'location_data'					=> $location_data,
				'lang_location_code'			=> lang('Location Code'),

				'lang_ticket'					=> lang('Ticket'),
				'table_header_additional_notes'	=> $table_header_additional_notes,
				'table_header_history'			=> $table_header_history,
				'lang_status'					=> lang('Status'),
				'status_name'					=> 'values[status]',
				'status_list'					=> $this->bo->get_status_list($ticket['status']),
				'lang_status_statustext'		=> lang('Set the status of the ticket'),

				'lang_no_user'					=> lang('Select user'),
				'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'				=> 'values[assignedto]',
				'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$ticket['assignedto'],$this->acl_location),

				'lang_group'					=> lang('Group'),
				'lang_no_group'					=> lang('No group'),
				'group_list'					=> $this->bocommon->get_group_list('select',$ticket['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'				=> 'values[group_id]',

				'lang_priority'					=> lang('Priority'),
				'value_priority'				=> $ticket['priority'],
				'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'			=> 'values[priority]',
				'priority_list'					=> $this->bo->get_priority_list($ticket['priority']),

				'lang_no_cat'					=> lang('no category'),
				'cat_select'				=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),
				'lang_category'					=> lang('category'),
				'value_category_name'			=> $ticket['category_name'],

				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$form_link),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uitts.index2')),
				'value_subject'					=> $ticket['subject'],
				'lang_subject_statustext'		=> lang('update subject'),
				'value_id'						=> '[ #'. $id . ' ] - ',

				'lang_details'					=> lang('Details'),
				'value_details'					=> $ticket['details'],
				'lang_details_statustext'		=> lang('Add new comments'),

				'lang_additional_notes'			=> lang('Additional notes'),

				'lang_new_note'					=> lang('New Note'),
				'lang_opendate'					=> lang('Open Date'),
				'value_opendate'				=> $ticket['entry_date'],

				'lang_assignedfrom'				=> lang('Assigned from'),
				'value_assignedfrom'			=> $ticket['user_name'],
				'lang_assignedto'				=> lang('Assigned to'),
				'value_assignedto_name'				=> (isset($ticket['assignedto_name'])?$ticket['assignedto_name']:''),

				'lang_no_additional_notes'		=> lang('No additional notes'),
				'lang_history'					=> lang('History'),
				'lang_no_history'				=> lang('No history for this record'),
				'additional_notes'				=> $additional_notes,
				'record_history'				=> $record_history,

				'lang_save'						=> lang('save'),
				'lang_name'						=> lang('name'),
				'lang_done'						=> lang('done'),
				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'					=> $ticket['contact_phone'],
			);
//_debug_array($data);
			$appname		= lang('helpdesk');
			$function_msg	= lang('view ticket detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view2' => $data));
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('fmticket');
		}

		protected function _generate_tabs($history='')
		{
			if(!$tab = phpgw::get_var('tab'))
			{
				$tab = 'general';
			}

			$tabs = array
			(
				'general'		=> array('label' => lang('general'), 'link' => '#general')
			);

			if($history)
			{
				$tabs['history']	= array('label' => lang('history'), 'link' => '#history');
			}

			phpgwapi_yui::tabview_setup('ticket_tabview');

			return  phpgwapi_yui::tabview_generate($tabs, $tab);
		}

	}
