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
			'index'				=> true,
			'index2'			=> true,
			'view'				=> true,
			'view2'				=> true,
			'add'				=> true,
			'add2'				=> true,
			'delete'			=> true,
			'download'			=> true,
			'download2'			=> true,
			'view_file'			=> true,
			'edit_status'		=> true,
			'edit_priority'		=> true,
			'update_data'		=> true,
			'_print'			=> true,
			'columns'			=> true
		);

		/**
		 * @var boolean $_simple use simplified interface
		 */
		protected $_simple = false;
		protected $_show_finnish_date = false;
		protected $_category_acl = false;
		var $part_of_town_id;
		var $status;
		var $filter;
		var $user_filter;

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::helpdesk';
			if($this->tenant_id	= $GLOBALS['phpgw']->session->appsession('tenant_id','property'))
			{
				//			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
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

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->status_id			= $this->bo->status_id;
			$this->user_id				= $this->bo->user_id;
			$this->cat_id				= $this->bo->cat_id;
			$this->vendor_id			= $this->bo->vendor_id;
			$this->district_id			= $this->bo->district_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->allrows				= $this->bo->allrows;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->location_code		= $this->bo->location_code;
			$this->p_num				= $this->bo->p_num;
			$this->simple				= $this->bo->simple;
			$this->show_finnish_date	= $this->bo->show_finnish_date;

			$this->_category_acl = isset($this->bo->config->config_data['acl_at_tts_category']) ? $this->bo->config->config_data['acl_at_tts_category'] : false;
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
					'vendor_id'		=> $this->vendor_id,
					'district_id'	=> $this->district_id,
					'part_of_town_id'=> $this->part_of_town_id,
					'allrows'		=> $this->allrows,
					'start_date'	=> $this->start_date,
					'end_date'		=> $this->end_date
				);
			$this->bo->save_sessiondata($data);
		}

		function _print()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$id 	= phpgw::get_var('id', 'int');

			$ticket = $this->bo->mail_ticket($id, $fields_updated=true, $receipt = array(),$location_code='', $get_message = true);

			$html = "<html><head><title>{$ticket['subject']}</title></head>";
			$html .= "<body>";
			$html .= $ticket['subject'] . '</br></br>';
			$html .= nl2br($ticket['body']);
			$html .= "</body></html>";

			echo $html;
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

			$custom_status	= $this->bo->get_custom_status();

			$status = array();
			$status['O'] = isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open');
			$status['X'] = lang('Closed');
			foreach($custom_status as $custom)
			{
				$status["C{$custom['id']}"] = $custom['name'];
			}

			foreach($list as &$entry)
			{
				$entry['status'] = $status[$entry['status']];

				if (isset($entry['child_date']) AND is_array($entry['child_date']))
				{
					$j=0;
					foreach($entry['child_date'] as $date)
					{
						if($date['date_info'][0]['descr'])
						{
							$entry["date_{$j}"]			= $date['date_info'][0]['entry_date'];
							$name_temp["date_{$j}"]		= true;
							$descr_temp["date_{$j}"]	= $date['date_info'][0]['descr'];
						}
						$j++;
					}
					unset($entry['child_date']);
				}
			}

			$name	= array();
			$name[] = 'priority';
			$name[] = 'id';
			$name[] = 'category';
			$name[] = 'subject';
			$name[] = 'loc1_name';
			$name[] = 'location_code';
			$name[] = 'address';
			$name[] = 'user';
			$name[] = 'assignedto';
			$name[] = 'entry_date';
			$name[] = 'status';

			if( $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property') )
			{
				$name[] = 'order_id';
				$name[] = 'vendor';
			}

			if( $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property') )
			{
				$name[] = 'estimate';
				$name[] = 'actual_cost';
				$name[] = 'difference';
			}

			$uicols_related = $this->bo->uicols_related;

			foreach($uicols_related as $related)
			{
				//					$name[] = $related;
			}

			$descr = array();
			foreach($name as $_entry)
			{
				//				$descr[] = str_replace('_', ' ', $_entry);
				$descr[] = lang(str_replace('_', ' ', $_entry));
			}

			foreach($name_temp as $_key => $_name)
			{
				array_push($name,$_key);			
			}


			foreach($descr_temp as $_key => $_name)
			{
				array_push($descr,$_name);			
			}

			$name[] = 'finnish_date';
			$name[] = 'delay';

			array_push($descr,lang('finnish date'),lang('delay'));


			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : array();

			foreach ($custom_cols as $col)
			{
				$name[]			= $col;
				$descr[]		= lang(str_replace('_', ' ', $col));
			}

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

			$ticket = $this->bo->read_single($id);

			if($ticket['order_id'] &&  abs($ticket['actual_cost']) == 0)
			{
				$sogeneric		= CreateObject('property.sogeneric');
				$sogeneric->get_location_info('ticket_status',false);
				$status_data	= $sogeneric->read_single(array('id' => (int)ltrim($new_status,'C')),array());

				if($status_data['actual_cost'])
				{
					return "id ".$id." ".lang('actual cost') . ': ' . lang('Missing value');
				}
			}

			$this->bo->update_status(array('status'=>$new_status),$id);
			if ((isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
				|| (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']==1
						&& $this->bo->fields_updated
					)
			)
			{
				$this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
			}
			
			if($this->bo->fields_updated)
			{
				return "id {$id} " . lang('Status has been changed');
			}
			else
			{
				return "id {$id} " . lang('Status has not been changed');
			}
		}

		function edit_priority()
		{
			if(!$this->acl_edit)
			{
				return lang('sorry - insufficient rights');
			}

			$new_priority = phpgw::get_var('new_priority', 'string', 'GET');
			$id 		= phpgw::get_var('id', 'int');

			$ticket = $this->bo->read_single($id);

			$receipt 	= $this->bo->update_priority(array('priority'=>$new_priority),$id);
			if ((isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
					|| (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']==1
						&& $this->bo->fields_updated
						)
			)
			{
				$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
			}
			return "id {$id} " . lang('priority has been changed');
		}


		function delete()
		{
			if(!$this->acl_delete)
			{
				return lang('sorry - insufficient rights');
			}

			$id = phpgw::get_var('id', 'int');
			if( $this->bo->delete($id) )
			{
				return lang('ticket %1 has been deleted', $id);
			}
			else
			{
				return lang('delete failed');			
			}
		}

		function columns()
		{
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values 		= phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property','ticket_columns', $values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uitts.columns',
				);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'		=> $this->bo->column_list($selected),
					'function_msg'		=> $function_msg,
					'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_columns'		=> lang('columns'),
					'lang_none'			=> lang('None'),
					'lang_save'			=> lang('save'),
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}


		function index()
		{
			if($this->tenant_id)
			{
				//				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index2'));
			}

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$this->save_sessiondata();
			$dry_run = false;
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

			$bgcolor_array[1]	= '#da7a7a';
			$bgcolor_array[2]	= '#dababa';
			$bgcolor_array[3]	= '#dadada';

			$lookup 		= phpgw::get_var('lookup', 'bool');
			$from 			= phpgw::get_var('from');
			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));
			$allrows  		= phpgw::get_var('allrows', 'bool');

			$datatable = array();
			$order_read 	= $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property');

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
//				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'		=> 'property.uitts.index',
						'query'				=> $this->query,
						'district_id'		=> $this->district_id,
						'part_of_town_id'	=> $this->part_of_town_id,
						'cat_id'			=> $this->cat_id,
						'status_id'			=> $this->status_id,
						'p_num'				=> $this->p_num
					)
				);

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uitts.index',"
					."second_display:1,"
					."sort: '{$this->sort}',"
					."order: '{$this->order}',"
					."cat_id:'{$this->cat_id}',"
					."vendor_id:'{$this->vendor_id}',"
					."status_id: '{$this->status_id}',"
					."user_id: '{$this->user_id}',"
					."query: '{$this->query}',"
					."p_num: '{$this->p_num}',"
					."district_id: '{$this->district_id}',"
					."part_of_town_id: '{$this->part_of_town_id}',"
					."start_date: '{$start_date}',"
					."end_date: '{$end_date}',"
					."location_code: '{$this->location_code}',"
					."allrows:'{$this->allrows}'";

				$link_data = array
					(
						'menuaction'		=> 'property.uitts.index',
						'second_display'	=> true,
						'sort'				=> $this->sort,
						'order'				=> $this->order,
						'cat_id'			=> $this->cat_id,
						'vendor_id'			=> $this->vendor_id,
						'status_id'			=> $this->status_id,
						'user_id'			=> $this->user_id,
						'query'				=> $this->query,
						'district_id'		=> $this->district_id,
						'part_of_town_id'   => $this->part_of_town_id,
						'start_date'		=> $start_date,
						'end_date'			=> $end_date,
						'location_code'		=> $this->location_code,
						'allrows'			=> $this->allrows
					);

				$group_filters = 'select';

				$values_combo_box = array();

				$values_combo_box[3]  = $this->bo->filter(array('format' => $group_filters, 'filter'=> $this->status_id,'default' => 'O'));

				if(isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'])
				{
					array_unshift ($values_combo_box[3],array ('id'=>'O2','name'=>$this->bo->config->config_data['tts_lang_open']));
				}
				$default_value = array ('id'=>'','name'=>lang('Open'));
				array_unshift ($values_combo_box[3],$default_value);

				if(!$this->simple)
				{
					$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => true,'use_acl' => $this->_category_acl));
					$default_value = array ('cat_id'=>'','name'=> lang('no category'));
					array_unshift ($values_combo_box[0]['cat_list'],$default_value);

					$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
					$default_value = array ('id'=>'','name'=>lang('no district'));
					array_unshift ($values_combo_box[1],$default_value);

					$values_combo_box[2] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
					$default_value = array ('id'=>'','name'=>lang('no part of town'));
					array_unshift ($values_combo_box[2],$default_value);

					$values_combo_box[4]  = $this->bocommon->get_user_list_right2('filter',PHPGW_ACL_EDIT,$this->user_id,$this->acl_location);
					array_unshift ($values_combo_box[4],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('my assigned tickets')));
					$_my_negative_self = (-1 * $GLOBALS['phpgw_info']['user']['account_id']);
	
					$default_value = array
					(
						'id'		=> $_my_negative_self,
						'name'		=> lang('my submitted tickets'),
						'selected' 	=> $_my_negative_self == $this->user_id
					);
					unset($_my_negative_self);
					array_unshift ($values_combo_box[4],$default_value);

					$default_value = array('id'=>'','name'=>lang('no user'));
					array_unshift ($values_combo_box[4],$default_value);

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
									'vendor_id'        	=> $this->vendor_id,
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
									( //boton 	PART OF TOWN
										'id' => 'btn_part_of_town_id',
										'name' => 'part_of_town_id',
										'value'	=> lang('Part of Town'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 3
									),
									array
									( //boton 	HOUR CATEGORY
										'id' => 'btn_status_id',
										'name' => 'status_id',
										'value'	=> lang('Status'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 4
									),
									array
									( //boton 	USER
										//	'id' => 'btn_user_id',
										'id' => 'sel_user_id', // testing traditional listbox for long list
										'name' => 'user_id',
										'value'	=> lang('User'),
										'type' => 'select',
										'style' => 'filter',
										'values' => $values_combo_box[4],
										'onchange'=> 'onChangeSelect("user_id");',
										'tab_index' => 5
									),
									array
									(//for link "columns", next to Export button
										'type' => 'link',
										'id' => 'btn_columns',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' => 'property.uitts.columns'
										)
									)."','','width=300,height=600,scrollbars=1')",
									'value' => lang('columns'),
									'tab_index' => 11
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 10
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 9
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
										'value'    => $this->query,
										'type' => 'text',
										'onkeypress' => 'return pulsar(event)',
										'size'    => 28,
										'tab_index' => 6
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
									),
									array
									( //div values  combo_box_4
										'id' => 'values_combo_box_4',
										'value'	=> $this->bocommon->select2String($values_combo_box[4])
									)
								)
							)
						)
					);

					if($order_read)
					{
						$datatable['actions']['form'][0]['fields']['field'][] = array
						(
									'id' => 'sel_vendor_id', // testing traditional listbox for long list
									'name' => 'vendor_id',
									'value'	=> lang('vendor'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $this->bo->get_vendors($this->vendor_id),
									'onchange'=> 'onChangeSelect("vendor_id");',
									'tab_index' => 12
						);
						$datatable['actions']['form'][0]['fields']['field'][] = array
						(
									'id' => 'sel_ecodimb', // testing traditional listbox for long list
									'name' => 'ecodimb',
									'value'	=> lang('dimb'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $this->bo->get_ecodimb($this->ecodimb),
									'onchange'=> 'onChangeSelect("ecodimb");',
									'tab_index' => 13
						);
						$datatable['actions']['form'][0]['fields']['field'][] = array
						(
									'id' => 'sel_b_account', // testing traditional listbox for long list
									'name' => 'b_account',
									'value'	=> lang('budget account'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $this->bo->get_b_account($this->b_account),
									'onchange'=> 'onChangeSelect("b_account");',
									'tab_index' => 14
						);


						$_filter_buildingpart = array();
						$filter_buildingpart = isset($this->bo->config->config_data['filter_buildingpart']) ? $this->bo->config->config_data['filter_buildingpart'] : array();
			
						if($filter_key = array_search('.b_account', $filter_buildingpart))
						{
							$_filter_buildingpart = array("filter_{$filter_key}" => 1);
						}

						$datatable['actions']['form'][0]['fields']['field'][] = array
						(
									'id' => 'sel_building_part', // testing traditional listbox for long list
									'name' => 'building_part',
									'value'	=> lang('building part'),
									'type' => 'select',
									'style' => 'filter',
									//'values' => $this->bo->get_building_part($this->building_part),
									'values'	=> $this->bocommon->select_category_list(array('type'=> 'building_part','selected' =>$this->building_part, 'order' => 'id', 'id_in_name' => 'num', 'filter' => $_filter_buildingpart)),
									'onchange'=> 'onChangeSelect("building_part");',
									'tab_index' => 15
						);

						if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']==1)
						{
							$datatable['actions']['form'][0]['fields']['field'][] = array
							(
									'id' => 'sel_branch_id', // testing traditional listbox for long list
									'name' => 'branch_id',
									'value'	=> lang('branch'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $this->bo->get_branch($this->branch_id),
									'onchange'=> 'onChangeSelect("branch_id");',
									'tab_index' => 16
							);
						}

						$datatable['actions']['form'][0]['fields']['field'][] = array
						(
									'id' => 'sel_order_dim1', // testing traditional listbox for long list
									'name' => 'order_dim1',
									'value'	=> lang('order_dim1'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $this->bo->get_order_dim1($this->order_dim1),
									'onchange'=> 'onChangeSelect("order_dim1");',
									'tab_index' => 17
						);
					}
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
										'value'	=> $this->bocommon->select2String($values_combo_box[3])
									)
								)
							)
						)
					);				
				}
				$dry_run = true;
			}

			$ticket_list = $this->bo->read($start_date, $end_date,'',$dry_run);

			$this->bo->get_origin_entity_type();
			$uicols_related = $this->bo->uicols_related;
			//_debug_array($uicols_related);
			$uicols = array();

			$uicols['name'][] = 'priority';
			$uicols['descr'][]	= lang('priority');
			$uicols['name'][] = 'hidden_id';
			$uicols['descr'][]	= 'hidden_id';
			$uicols['name'][] = 'id';
			$uicols['descr'][]	= lang('id');
			$uicols['name'][] = 'bgcolor';
			$uicols['descr'][]	= lang('bgcolor');
			$uicols['name'][] = 'subject';
			$uicols['descr'][]	= lang('subject');
/*
			$uicols['name'][] = 'location_code';
			$uicols['descr'][]	= lang('location code');
*/
			$location_types = execMethod('property.soadmin_location.select_location_type');
			$level_assigned = isset($this->bo->config->config_data['list_location_level']) && $this->bo->config->config_data['list_location_level'] ? $this->bo->config->config_data['list_location_level'] : array();

			static $location_cache = array();

			foreach ( $location_types as $dummy => $level)
			{
				if ( in_array($level['id'], $level_assigned))
				{
					$uicols['name'][] = "loc{$level['id']}_name";
					$uicols['descr'][]	= $level['name'];
					if($level['id'] > 1)
					{
						foreach ($ticket_list as & $_ticket)
						{
							$_location_code_arr = explode('-', $_ticket['location_code']);
							$__location_code_arr = array();
							for ($k=0; $k<$level['id']; $k++)
							{
								if(isset($_location_code_arr[$k]))
								{
									$__location_code_arr[] = $_location_code_arr[$k];
								}
							}
							$_location_code = implode('-', $__location_code_arr);

							if(!$_ticket["loc{$level['id']}_name"] = $location_cache[$_location_code])
							{
								$location_data = execMethod('property.solocation.read_single', $_location_code);
								$_ticket["loc{$level['id']}_name"] = $location_data["loc{$level['id']}_name"];
								$location_cache[$_location_code] = $location_data["loc{$level['id']}_name"];
							}
						}
						unset($_ticket);
					}
				}
			}

			$uicols['name'][] = 'entry_date';
			$uicols['descr'][]	= lang('entry date');

			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : array();
			$columns = $this->bo->get_columns();

//_debug_array($custom_cols);die();
			foreach ($custom_cols as $col)
			{
				$uicols['name'][]		= $col;
				$uicols['descr'][]		= $columns[$col]['name'];
			}


			$uicols['name'][] = 'child_date';
			$uicols['descr'][]	= lang('child date');
			$uicols['name'][] = 'link_view';
			$uicols['descr'][]	= lang('link view');
			$uicols['name'][] = 'lang_view_statustext';
			$uicols['descr'][]	= lang('lang view statustext');
			$uicols['name'][] = 'text_view';
			$uicols['descr'][]	= lang('text view');

			$count_uicols_name = count($uicols['name']);


			$j = 0;
			$k = 0;
			if(is_array($ticket_list))
			{
				$status['X'] = array
					(
						'status'			=> lang('closed'),
					);
				$status['O'] = array
					(
						'status'			=> isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open'),
					);
				$status['C'] = array
					(
						'status'			=> lang('closed'),
					);

				$custom_status	= $this->bo->get_custom_status();

				foreach($custom_status as $custom)
				{
					$status["C{$custom['id']}"] = array
						(
							'status'			=> $custom['name'],
						);
				}

				$view_action = $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uitts.view','id'=> $ticket['id']));
				foreach($ticket_list as $ticket)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
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
						if($uicols['name'][$k] == 'id' || $uicols['name'][$k] == 'entry_date')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 	= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['link']		= "{$view_action}&id={$ticket['id']}";
							$datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket[$uicols['name'][$k]] . $ticket['new_ticket'];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
						}

						if($uicols['name'][$k] == 'hidden_id')//hidden
						{
							$datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket['id'];
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
							'source'	=> 'hidden_id'
						),
					)
				);

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'view',
						'statustext' 	=> lang('view the ticket'),
						'text'			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitts.view'
						)),
						'parameters'	=> $parameters
					);

				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'print',
						'statustext' 	=> lang('print the ticket'),
						'text'			=> lang('print view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitts._print',
							'target'		=> '_blank'
						)),
						'parameters'	=> $parameters
					);


				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'edit',
							'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uijasper.view',
								'jasper_id'			=> $report['id'],
								'target'		=> '_blank'
							)),
							'parameters'			=> $parameters
						);
				}
			}
			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'delete',
						'statustext' 	=> lang('delete the ticket'),
						'text'			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this ticket'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitts.delete'
						)),
						'parameters'	=> $parameters
					);
			}

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'] == 'yes'
				&& $this->acl_edit)
			{

				unset($status['C']);
				foreach ($status as $status_code => $status_info)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 		=> 'status',
							'statustext' 	=> $status_info['status'],
							'text' 			=> lang('change to') . ' status:  ' .$status_info['status'],
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

				$_priorities = $this->bo->get_priority_list();
				foreach ($_priorities as $_priority => $_priority_info)
				{
					$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'priority',
						'statustext' 	=> $_priority_info['name'],
						'text' 			=> lang('change to') . ' ' . lang('priority') .':  ' .$_priority_info['name'],
						'confirm_msg'	=> lang('do you really want to change the priority to %1',$_priority_info['name']),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'property.uitts.edit_priority',
							'edit_status'		=> true,
							'new_priority'		=> $_priority,
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
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('Add new ticket'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitts.add'
						))
					);
			}


			$uicols_formatter = array
			(
				'estimate'		=> 'FormatterRight',
				'actual_cost'	=> 'FormatterRight',
				'difference'	=> 'FormatterRight',
			);
			
			unset($parameters);
			for ($i=0;$i<$count_uicols_name;$i++)
			{
	//		if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= isset($uicols_formatter[$uicols['name'][$i]]) && $uicols_formatter[$uicols['name'][$i]] ? $uicols_formatter[$uicols['name'][$i]] : '""';
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
//					$datatable['headers']['header'][$i]['width']			= (int)$uicols['width'][$i];
					if($uicols['name'][$i]=='priority' || $uicols['name'][$i]=='id' || $uicols['name'][$i]=='assignedto'
					 || $uicols['name'][$i]=='finnish_date'|| $uicols['name'][$i]=='user'|| $uicols['name'][$i]=='entry_date'
					 || $uicols['name'][$i]=='order_id'|| $uicols['name'][$i]=='modified_date')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
					}
					if($uicols['name'][$i]=='text_view' || $uicols['name'][$i]=='bgcolor' || $uicols['name'][$i]=='child_date' || $uicols['name'][$i]== 'link_view' || $uicols['name'][$i]=='lang_view_statustext' || $uicols['name'][$i]=='hidden_id')
					{
						$datatable['headers']['header'][$i]['visible'] 		= false;
						$datatable['headers']['header'][$i]['format'] 		= 'hidden';
					}
				}
			}

			//path for property.js

			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($ticket_list);
			}
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

			//-- BEGIN----------------------------- JSON CODE ------------------------------
			//values for Pagination
			$json = array
				(
					'recordsReturned' 		=> $datatable['pagination']['records_returned'],
					'totalRecords' 			=> (int)$datatable['pagination']['records_total'],
					'startIndex' 			=> $datatable['pagination']['records_start'],
					'sort'					=> $datatable['sorting']['order'],
					'dir'					=> $datatable['sorting']['sort'],
					'records'				=> array(),
					'show_sum_estimate'		=> in_array('estimate', $custom_cols),
					'show_sum_actual_cost'	=> in_array('actual_cost', $custom_cols),
					'show_sum_difference'	=> in_array('difference', $custom_cols),
					'sum_budget'			=> $this->bo->sum_budget,
					'sum_actual_cost'		=> $this->bo->sum_actual_cost,
					'sum_difference'		=> $this->bo->sum_difference,
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

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', $this->simple ? 'tts.index.simple' : 'tts.index' , 'property' );

		}

		function index2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','nextmatchs'));


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
					$status = isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open');
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
						'extra'	=> array
						(
							'menuaction' => 'property.uitts.index',
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
						'extra'	=> array
						(
							'menuaction' => 'property.uitts.index',
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
						'extra'	=> array
						(
							'menuaction' => 'property.uitts.index',
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
						'extra'	=> 
						array(
							'menuaction'	=> 'property.uitts.index',
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
						'extra' => array
						(
							'menuaction'	=> 'property.uitts.index',
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
						'extra'	=> array
						(
							'menuaction'	=> 'property.uitts.index',
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
					'user_id'			=> $this->user_id,
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
					'lang_download_help'			=> lang('Download table to your browser'),

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
					'lang_show_all'					=> isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open'),
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
			if($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.add2'));
			}

			$bolocation		= CreateObject('property.bolocation');

			$values		= phpgw::get_var('values');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			if ((isset($values['cancel']) && $values['cancel']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index'));
			}

			$values_attribute			= phpgw::get_var('values_attribute');

			//------------------- start ticket from other location
			$bypass 		= phpgw::get_var('bypass', 'bool');
//			if(isset($_POST) && $_POST && isset($bypass) && $bypass)
			if($bypass)
			{
				$boadmin_entity		= CreateObject('property.boadmin_entity');
				$location_code 		= phpgw::get_var('location_code');
				$values['descr']	= phpgw::get_var('descr');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id			= phpgw::get_var('p_cat_id', 'int');
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
					$values['street_name'] = $values['location_data']['street_name'];
					$values['street_number'] = $values['location_data']['street_number'];
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
				$values['origin'][0]['data'][]= array
					(
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

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as &$attribute )
					{
						if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if(isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && ! ctype_digit($attribute['value']))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter integer for attribute %1', $attribute['input_text']));						
						}

						if(isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'V' && strlen($attribute['value']) > $attribute['precision'])
						{
							$receipt['error'][]=array('msg'=>lang('Max length for attribute %1 is: %2', "\"{$attribute['input_text']}\"",$attribute['precision']));
							$attribute['value'] = substr($attribute['value'],0,$attribute['precision']);
						}
					}
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

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute )
					{
						if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}
					}
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->add($values, $values_attribute);

					//------------ files
					$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);

					if($values['file_name'] && $receipt['id'])
					{
						$bofiles	= CreateObject('property.bofiles');
						$to_file = $bofiles->fakebase . '/fmticket/' . $receipt['id'] . '/' . $values['file_name'];

						if($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => array(RELATIVE_NONE)
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
				//	$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');

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

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
			}

			$values	= $this->bo->get_attributes($values);

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
							(
								'menuaction'	=> 'property.uiproject.attrib_history',
								'attrib_id'	=> $attribute['id'],
								'id'		=> $id,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array(
				'values'	=> isset($values['location_data'])?$values['location_data']:'',
				'type_id'	=> -1, // calculated from location_types
				'no_link'	=> false, // disable lookup links for location type less than type_id
				'tenant'	=> true,
				'lookup_type'	=> 'form',
				'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
				'entity_data'	=> (isset($values['p'])?$values['p']:'')
			));

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_me_as_contact']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_me_as_contact']==1)
			{
				$ticket['contact_id'] = $GLOBALS['phpgw']->accounts->get($this->account)->person_id;
			}
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


			if(!$this->simple && $this->show_finnish_date)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('values_finnish_date');
			}

			$data = array
			(
					'custom_attributes'				=> array('attributes' => $values['attributes']),
					'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
					'contact_data'					=> $contact_data,
					'simple'						=> $this->simple,
					'show_finnish_date'				=> $this->show_finnish_date,
					'value_origin'					=> isset($values['origin']) ? $values['origin'] : '',
					'value_origin_type'				=> (isset($origin)?$origin:''),
					'value_origin_id'				=> (isset($origin_id)?$origin_id:''),

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'location_data'					=> $location_data,
					'lang_no_user'					=> lang('Select user'),
					'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
					'select_user_name'				=> 'values[assignedto]',
					'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$values['assignedto'],$this->acl_location),
					'disable_userassign_on_add'		=> isset($this->bo->config->config_data['tts_disable_userassign_on_add'])?$this->bo->config->config_data['tts_disable_userassign_on_add']:'',

					'lang_no_group'					=> lang('No group'),
					'group_list'					=> $this->bo->get_group_list($values['group_id']),
					'select_group_name'				=> 'values[group_id]',

					'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
					'select_priority_name'			=> 'values[priority]',
					'priority_list'					=> array('options' => $this->bo->get_priority_list((isset($values['priority'])?$values['priority']:''))),

					'status_list'					=> array('options' => $this->bo->get_status_list('O')),

					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

					'lang_details'					=> lang('Details'),
					'lang_category'					=> lang('category'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_send'						=> lang('send'),
					'value_details'					=> (isset($values['details'])?$values['details']:''),
					'value_subject'					=> (isset($values['subject'])?$values['subject']:''),

					'value_finnish_date'			=> (isset($values['finnish_date'])?$values['finnish_date']:''),
					'lang_finnish_date_statustext'	=> lang('Select the estimated date for closing the task'),

					'lang_cancel_statustext'		=> lang('Back to the ticket list'),
					'lang_send_statustext'			=> lang('Save the entry and return to list'),
					'lang_save_statustext'			=> lang('Save the ticket'),
					'lang_no_cat'					=> lang('no category'),
					'lang_town_statustext'			=> lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
					'lang_part_of_town'				=> lang('Part of town'),
					'lang_no_part_of_town'			=> lang('No part of town'),
					'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id,'use_acl' => $this->_category_acl)),
					'pref_send_mail'				=> (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:''),
					'fileupload'					=> (isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:''),
				);

			//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('add ticket');

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tts.add', 'property' );
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','files','attributes_form'));
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

			$values['location_data'] = $bolocation->read_single($values['location_code'],array('tenant_id'=>$this->tenant_id, 'view' => true));

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
				//	$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index'));
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

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');

			$data = array
				(
					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'location_data'						=> $location_data,

					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index')),
					'lang_subject'						=> lang('Subject'),
					'lang_subject_statustext'			=> lang('Enter the subject of this ticket'),

					'lang_details'						=> lang('Details'),
					'lang_details_statustext'			=> lang('Enter the details of this ticket'),

					'lang_save'							=> lang('save'),
					'lang_done'							=> lang('done'),
					'value_details'						=> (isset($values['details'])?$values['details']:''),
					'value_subject'						=> (isset($values['subject'])?$values['subject']:''),

					'lang_done_statustext'				=> lang('Back to the ticket list'),
					'lang_save_statustext'				=> lang('Save the ticket'),
					'lang_contact_phone'				=> lang('contact phone'),
					'lang_contact_phone_statustext'		=> lang('contact phone'),
					'value_contact_phone'				=> isset($values['contact_phone'])?$values['contact_phone']:'',

					'lang_contact_email'				=> lang('contact email'),
					'lang_contact_email_statustext'		=> lang('contact email'),
					'value_contact_email'				=> isset($values['contact_email'])?$values['contact_email']:'',
				);

			$appname					= lang('helpdesk');
			$function_msg				= lang('add ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add2' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function update_data()
		{
			$action = phpgw::get_var('action', 'string', 'GET');
			switch($action)
			{
				case 'get_vendor':
					return $this->bocommon->get_vendor_email();
					break;
				case 'get_files':
					return $this->get_files();
					break;
				default:
			}
		}

		function get_files()
		{
			$id 	= phpgw::get_var('id', 'int');

			if( !$this->acl_read)
			{
				return;
			}

			$link_file_data = array
			(
				'menuaction'	=> 'property.uitts.view_file',
				'id'			=> $id
			);

			$link_to_files = isset($this->bo->config->config_data['files_url']) ? $this->bo->config->config_data['files_url']:'';

			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);
			$values	= $this->bo->read_single($id);

			$content_files = array();

			foreach($values['files'] as $_entry )
			{
				$content_files[] = array
				(
					'file_name' => '<a href="'.$link_view_file.'&amp;file_name='.$_entry['name'].'" target="_blank" title="'.lang('click to view file').'">'.$_entry['name'].'</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="'.$_entry['name'].'" title="'.lang('Check to delete file').'">',
					'attach_file' => '<input type="checkbox" name="values[file_attach][]" value="'.$_entry['name'].'" title="'.lang('Check to attach file').'">'
				);
			}							

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{

				if(count($content_files))
				{
					return json_encode($content_files);
				}
				else
				{
					return "";
				}
			}
			return $content_files;
		}



		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$id = phpgw::get_var('id', 'int', 'GET');

			if($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.view2', 'id' => $id ));
			}

			$bolocation	= CreateObject('property.bolocation');

			$values = phpgw::get_var('values');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			$values['ecodimb']			= phpgw::get_var('ecodimb');
			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

			$values_attribute			= phpgw::get_var('values_attribute');

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');
			if(!$receipt)
			{
				$receipt = array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts', 'files', 'attributes_form'));

			$historylog	= CreateObject('property.historylog','tts');

			$order_read 			= $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property');
			$order_add 				= $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property');
			$order_edit 			= $this->acl->check('.ticket.order', PHPGW_ACL_EDIT, 'property');

			$access_order = false;
			if($order_add || $order_edit)
			{
				$access_order = true;
			}

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

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as &$attribute )
					{
						if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if(isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && ! ctype_digit($attribute['value']))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter integer for attribute %1', $attribute['input_text']));						
						}

						if(isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'V' && strlen($attribute['value']) > $attribute['precision'])
						{
							$receipt['error'][]=array('msg'=>lang('Max length for attribute %1 is: %2', $attribute['input_text'],$attribute['precision']));
							$attribute['value'] = substr($attribute['value'],0,$attribute['precision']);
						}
					}
				}


				if($access_order)
				{
					if((isset($values['order_id']) && $values['order_id']) && (!isset($values['budget']) || !$values['budget']) )
					{
						$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Missing value'));
					}

					$sogeneric		= CreateObject('property.sogeneric');
					$sogeneric->get_location_info('ticket_status',false);
					$status_data	= $sogeneric->read_single(array('id' => (int)ltrim($values['status'],'C')),array());

					if(isset($status_data['actual_cost']) && $status_data['actual_cost'])
					{
						if(!$values['actual_cost'] || !abs($values['actual_cost']) > 0)
						{
							$receipt['error'][]=array('msg'=>lang('actual cost') . ': ' . lang('Missing value'));
						}
						else if(!is_numeric($values['actual_cost']))
						{
							$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter a numeric value'));					
						}
					}
				}
				
				if(isset($values['takeover']) && $values['takeover'])
				{
					$values['assignedto'] = $this->account;
				}

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute )
					{
						if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}
					}
				}

				$receipt = $this->bo->update_ticket($values,$id, $receipt, $values_attribute);

				if ( (isset($values['send_mail']) && $values['send_mail']) 
					|| (isset($this->bo->config->config_data['mailnotification'])
						&& $this->bo->config->config_data['mailnotification']
						&& $this->bo->fields_updated
						)
					|| (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']==1
						&& $this->bo->fields_updated
						)
				)
				{
					$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt,'', false, isset($values['send_mail']) && $values['send_mail'] ? true : false);
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

			//---------end files
				if( phpgw::get_var('notify_client_by_sms', 'bool') 
					&& isset($values['response_text'])
					&& $values['response_text']
					&& phpgw::get_var('to_sms_phone'))
				{
					$to_sms_phone = phpgw::get_var('to_sms_phone');
		//			$ticket['contact_phone'] = $to_sms_phone;
					
					$sms	= CreateObject('sms.sms');
					$sms->websend2pv($this->account,$to_sms_phone,$values['response_text']);
					$historylog->add('MS',$id,"{$to_sms_phone}::{$values['response_text']}");
				}


			}

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
			}

			$ticket = $this->bo->read_single($id, $values);

			if (isset($ticket['attributes']) && is_array($ticket['attributes']))
			{
				foreach ($ticket['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
							(
								'menuaction'	=> 'property.uiproject.attrib_history',
								'attrib_id'	=> $attribute['id'],
								'id'		=> $id,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}
			}

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

			$link_file_data = array
				(
					'menuaction'	=> 'property.uitts.view_file',
					'id'		=> $id
				);

			if(!$this->simple && $this->show_finnish_date)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('values_finnish_date');
			}

			// -------- start order section

			if($order_read || $access_order)
			{
				$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
					'vendor_id'			=> $ticket['vendor_id'],
					'vendor_name'		=> $ticket['vendor_name'],
					'type'				=> $order_read && !$access_order ? 'view' : 'form'
					));
	
			}
			
			if($access_order)
			{

				$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array
					(
						'b_account_id'		=> $ticket['b_account_id'] ? $ticket['b_account_id'] : $ticket['b_account_id'],
						'b_account_name'	=> $ticket['b_account_name'],
						'disabled'			=> false
					)
				);

				$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array
					(
						'ecodimb'			=> $ticket['ecodimb'] ? $ticket['ecodimb'] : $ticket['ecodimb'],
						'ecodimb_descr'		=> $ticket['ecodimb_descr'],
						'disabled'			=> false
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
					if(isset($prefs['email']) && $prefs['email'])
					{
						$supervisor_email[] = array
						(
							'id'	  => $supervisor_id,
							'address' => $prefs['email'],
						);
					}

					if ( isset($prefs['approval_from'])  && $prefs['approval_from'])
					{
						$prefs2 = $this->bocommon->create_preferences('property', $prefs['approval_from']);

						if(isset($prefs2['email']) && $prefs2['email'])
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

			$vendor_email = array();
			$validator = CreateObject('phpgwapi.EmailAddressValidator');			
			if(isset($values['vendor_email']) && is_array($values['vendor_email']))
			{
				foreach ($values['vendor_email'] as $_temp)
				{
					if($_temp)
					{
						if($validator->check_email_address($_temp))
						{
							$vendor_email[] = $_temp;
						}
						else
						{
							$receipt['error'][]=array('msg'=>lang('%1 is not a valid address',$_temp));				
						}
					}
				}
			}
			unset($_temp);

			$preview_html = phpgw::get_var('preview_html', 'bool');
			$preview_pdf = phpgw::get_var('preview_pdf', 'bool');

			if($preview_pdf)
			{
				$this->_pdf_order($id, true);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if($vendor_email || $preview_html)
			{
				$subject = lang('workorder').": {$ticket['order_id']}";

				$organisation = '';
				$contact_name = '';
				$contact_email = '';
				$contact_phone = '';

				if(isset($this->bo->config->config_data['org_name']))
				{
					$organisation = $this->bo->config->config_data['org_name'];
				}

				$on_behalf_of_assigned = phpgw::get_var('on_behalf_of_assigned', 'bool');
				if($on_behalf_of_assigned && isset($ticket['assignedto_name']))
				{
					$user_name = $ticket['assignedto_name'];
					$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
					$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->data;
					if(!$preview_html && !$preview_pdf)
					{
						$_behalf_alert = lang('this order is sent by %1 on behalf of %2',$GLOBALS['phpgw_info']['user']['fullname'], $user_name);
						$historylog->add('C',$id,$_behalf_alert);
						unset($_behalf_alert);
					}
				}
				else
				{
					$user_name = $GLOBALS['phpgw_info']['user']['fullname'];				
				}
				$ressursnr = $GLOBALS['phpgw_info']['user']['preferences']['property']['ressursnr'];
				$location = lang('Address'). ": {$ticket['address']}<br>";

				$address_element = $this->bo->get_address_element($ticket['location_code']);

				foreach($address_element as $address_entry)
				{
					$location .= "{$address_entry['text']}: {$address_entry['value']} <br>";
				}

				$location = rtrim($location, '<br>');

				$order_description = $ticket['order_descr'];

				if(isset($contact_data['value_contact_name']) && $contact_data['value_contact_name'])
				{
					$contact_name = $contact_data['value_contact_name'];
				}
				if(isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
				{
					$contact_email = "<a href='mailto:{$contact_data['value_contact_email']}'>{$contact_data['value_contact_email']}</a>";
				}
				if(isset($contact_data['value_contact_tel']) && $contact_data['value_contact_tel'])
				{
					$contact_phone = $contact_data['value_contact_tel'];
				}

				$order_id = $ticket['order_id'];

				$user_phone = $GLOBALS['phpgw_info']['user']['preferences']['property']['cellphone'];
				$user_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
				$order_email_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_email_template'];

				$body = nl2br(str_replace(array
					(
						'__vendor_name__',
						'__organisation__',
						'__user_name__',
						'__user_phone__',
						'__user_email__',
						'__ressursnr__',
						'__location__',
						'__order_description__',
						'__contact_name__',
						'__contact_email__',
						'__contact_phone__',
						'__order_id__',
						'[b]',
						'[/b]'
					),array
					(
						$vendor_data['value_vendor_name'],
						$organisation,
						$user_name,
						$user_phone,
						$user_email,
						$ressursnr,
						$location,
						$order_description,
						$contact_name,
						$contact_email,
						$contact_phone,
						$order_id,
						'<b>',
						'</b>'
					),$order_email_template));

					$html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><title>{$subject}</title></head>";

					$body .='</br>';
					$body .='</br>';
					$body .= '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $id),false,true).'">' . lang('Ticket').' #' .$id .'</a>';
					$html .= "<body>{$body}</body></html>";


				if($preview_html)
				{
				
					$GLOBALS['phpgw_info']['flags']['noheader'] = true;
					$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
					$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
					echo $html;
					$GLOBALS['phpgw']->common->phpgw_exit();
				}


				if(isset($values['file_attach']) && is_array($values['file_attach']))
				{
					$bofiles	= CreateObject('property.bofiles');
					$attachments = $bofiles->get_attachments("/fmticket/{$id}/", $values['file_attach']);
					$attachment_log = ' ' . lang('attachments') . ' : ' . implode(', ',$values['file_attach']);
				}

				if(isset($values['send_order_format']) && $values['send_order_format'] == 'pdf')
				{
					$pdfcode = $this->_pdf_order($id);
					if($pdfcode)
					{							
						$dir =  "{$GLOBALS['phpgw_info']['server']['temp_dir']}/pdf_files";

						//save the file
						if (!file_exists($dir))
						{
							mkdir ($dir,0777);
						}
						$fname = tempnam($dir.'/','PDF_').'.pdf';
						$fp = fopen($fname,'w');
						fwrite($fp,$pdfcode);
						fclose($fp);

						$attachments[] = array
						(
								'file' => $fname,
								'name' => "order_{$id}.pdf",
								'type' => 'application/pdf'
						);						
					}
					$body = lang('order') . '.</br></br>' . lang('see attachment');
				}

				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
					$coordinator_email = "{$coordinator_name}<{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}>";
					$cc = '';
					$bcc = $coordinator_email;
					if(isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
					{
						$cc = $contact_data['value_contact_email'];
					}

					$_to = implode(';',$vendor_email);

					$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($body), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html', '', $attachments , true);
					if($rcpt)
					{
						$receipt['message'][]=array('msg'=>lang('%1 is notified',$_address));
						$historylog->add('M',$id,"{$_to}{$attachment_log}");
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

			// start approval
			if (isset($values['approval']) && $values['approval']  && $this->bo->config->config_data['workorder_approval'])
			{
				$coordinator_name=$GLOBALS['phpgw_info']['user']['fullname'];
				$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

				$subject = lang(Approval).": ".$ticket['order_id'];
				$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view', 'id'=> $id),false,true).'">' . lang('Workorder %1 needs approval',$ticket['order_id']) .'</a>';

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
					foreach ($values['approval'] as $_account_id => $_address)
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
				else
				{
					$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
				}
			}

			// end approval

			// -------- end order section



			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$notes = array
			(
				array
				(
					'value_id'		=>'', //not from historytable
					'value_count'	=> 1,
					'value_date'	=> $GLOBALS['phpgw']->common->show_date($ticket['timestamp']),
					'value_user'	=> $ticket['user_name'],
					'value_note'	=> $ticket['details'],
					'value_publish'	=> $ticket['publish_note']
				)
			);

			$additional_notes = array_merge($notes,$additional_notes);

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap'])
			{
				foreach ($additional_notes as &$_note)
				{
					$_note['value_note'] = wordwrap($_note['value_note'],40);
				}
			}
			unset($_note);

			if(isset($values['order_text']) && $ticket['order_id'])
			{
				foreach($values['order_text'] as $_text)
				{
					$ticket['order_descr'] .= "\n$_text";
				}
			}

			$note_def = array
			(
					array('key' => 'value_count',	'label'=>'#',		'sortable'=>true,'resizeable'=>true),
					array('key' => 'value_date',	'label'=>lang('Date'),'sortable'=>true,'resizeable'=>true),
					array('key' => 'value_user',	'label'=>lang('User'),'sortable'=>true,'resizeable'=>true),
					array('key' => 'value_note',	'label'=>lang('Note'),'sortable'=>true,'resizeable'=>true)
			);

			if($access_order)
			{
				$note_def[] = array('key' => 'order_text','label'=>lang('order text'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter');
				foreach($additional_notes as &$note)
				{
					$note['order_text'] = '<input type="checkbox" name="values[order_text][]" value="'.$note['value_note'].'" title="'.lang('Check to add text to order').'">';
				}
			}

			if($GLOBALS['phpgw_info']['apps']['frontend']['enabled'])
			{
				$note_def[] = array('key' => 'publish_note','label'=>lang('publish text'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter');			
				foreach($additional_notes as &$note)
				{
					$_checked = $note['value_publish'] ? 'checked' : '';
					$note['publish_note'] = "<input type='checkbox' {$_checked}  name='values[publish_note][]' value='{$id}_{$note['value_id']}' title='".lang('Check to publish text at frontend')."'>";
				}
			}

			foreach($additional_notes as &$note)
			{
				$note['value_note'] = nl2br($note['value_note']);
			}


			//_debug_Array($additional_notes);die();
			//---datatable settings---------------------------------------------------	
			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($additional_notes),
					'total_records'			=> count($additional_notes),
					'is_paginator'			=> 0,
					'edit_action'			=> "''",
					'footer'				=> 0
				);					
			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode($note_def)
				);		
			$datavalues[1] = array
				(
					'name'					=> "1",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'is_paginator'			=> 0,
					'edit_action'			=> "''",
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
					$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$ticket['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'">'.$ticket['files'][$z]['name'].'</a>';
				}				
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$ticket['files'][$z]['name'].'" title="'.lang('Check to delete file').'">';
				$content_files[$z]['attach_file'] = '<input type="checkbox" name="values[file_attach][]" value="'.$ticket['files'][$z]['name'].'" title="'.lang('Check to attach file').'">';
			}							

			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'edit_action'			=> "''",
					'footer'				=> 0
				);

			$attach_file_def = array
				(
					array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter'),
				);

			if(isset($ticket['order_id']) && $ticket['order_id'])
			{
				$attach_file_def[] = array('key' => 'attach_file','label'=>lang('attach file'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter');
			}

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode($attach_file_def)
				);


			$myColumnDefs[3] = array
				(
					'name'		=> "3",
					'values'	=>	json_encode(array(	array('key' => 'value_email',	'label'=>lang('email'),	'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_select','label'=>lang('select'),'sortable'=>false,'resizeable'=>true)))
				);	


			$content_email = $this->bocommon->get_vendor_email(isset($ticket['vendor_id'])?$ticket['vendor_id']:0);

			$datavalues[3] = array
				(
					'name'					=> "3",
					'values' 				=> json_encode($content_email),
					'total_records'			=> count($content_email),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'edit_action'			=> "''",
					'footer'				=> 0
				);


			$location_id	= $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$notify_info = execMethod('property.notify.get_yui_table_def',array
								(
									'location_id'		=> $location_id,
									'location_item_id'	=> $id,
									'count'				=> count($myColumnDefs)
								)
							);
			
			$datavalues[] 	= $notify_info['datavalues'];
			$myColumnDefs[]	= $notify_info['column_defs'];
			$myButtons		= array();
			$myButtons[]	= $notify_info['buttons'];

			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->bo->config->config_data['filter_buildingpart']) ? $this->bo->config->config_data['filter_buildingpart'] : array();
			
			if($filter_key = array_search('.b_account', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}
			
			//----------------------------------------------datatable settings--------			
//_debug_array($supervisor_email);die();
			$msgbox_data = $this->bocommon->msgbox_data($receipt);
			$cat_select	= $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id,'use_acl' => $this->_category_acl));
			$this->cats->set_appname('property','.project');
			$order_catetory	= $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $ticket['order_cat_id']));

			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
				(
					'custom_attributes'				=> array('attributes' => $ticket['attributes']),
					'lookup_functions'				=> isset($ticket['lookup_functions'])?$ticket['lookup_functions']:'',
					'send_response'					=> isset($this->bo->config->config_data['tts_send_response']) ? $this->bo->config->config_data['tts_send_response'] : '',
					'value_sms_phone'				=> $ticket['contact_phone'],
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
	//				'vendor_email'					=> $vendor_email,

					'contact_data'					=> $contact_data,
					'lookup_type'					=> $lookup_type,
					'simple'						=> $this->simple,
					'show_finnish_date'				=> $this->show_finnish_date,
					'tabs'							=> self::_generate_tabs(true),
					'td_count'						=> '""',
					'base_java_url'					=> "{menuaction:'property.uitts.update_data',id:{$id}}",
					'base_java_notify_url'			=> "{menuaction:'property.notify.update_data',location_id:{$location_id},location_item_id:'{$id}'}",
					'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
					'datatable'						=> $datavalues,
					'myColumnDefs'					=> $myColumnDefs,
					'myButtons'						=> $myButtons,
					'value_origin'					=> $ticket['origin'],
					'value_target'					=> $ticket['target'],
					'value_finnish_date'			=> $ticket['finnish_date'],

					'link_entity'					=> $link_entity,
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

					'location_data'					=> $location_data,

					'value_status'					=> $ticket['status'],
					'status_list'					=> array('options' => $this->bo->get_status_list($ticket['status'])),

					'lang_no_user'					=> lang('Select user'),
					'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
					'select_user_name'				=> 'values[assignedto]',
					'value_assignedto_id'			=> $ticket['assignedto'],
					'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$ticket['assignedto'],$this->acl_location),

					'lang_no_group'					=> lang('No group'),
//					'group_list'					=> $this->bocommon->get_group_list('select',$ticket['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
					'group_list'					=> $this->bo->get_group_list($ticket['group_id']),

					'select_group_name'				=> 'values[group_id]',
					'value_group_id'				=> $ticket['group_id'],

					'lang_takeover'					=> (isset($values['assignedto']) && $values['assignedto'] != $this->account)  || (!isset($values['assignedto']) || !$values['assignedto']) ? lang('take over') : '',

					'value_priority'				=> $ticket['priority'],
					'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
					'select_priority_name'			=> 'values[priority]',
					'priority_list'					=> array('options' => $this->bo->get_priority_list($ticket['priority'])),

					'lang_no_cat'					=> lang('no category'),
					'value_cat_id'					=> $this->cat_id,
					'cat_select'					=> $cat_select,

					'value_category_name'			=> $ticket['category_name'],

					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$form_link),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uitts.index')),
					'value_subject'					=> $ticket['subject'],

					'value_id'						=> '[ #'. $id . ' ] - ',

					'value_details'					=> $ticket['details'],

					'value_opendate'				=> $ticket['entry_date'],
					'value_assignedfrom'			=> $ticket['user_name'],
					'value_assignedto_name'			=> isset($ticket['assignedto_name'])?$ticket['assignedto_name']:'',
					'show_billable_hours'			=> isset($this->bo->config->config_data['show_billable_hours']) ? $this->bo->config->config_data['show_billable_hours'] : '',
					'value_billable_hours'			=> $ticket['billable_hours'],

					'additional_notes'				=> $additional_notes,
					'record_history'				=> $record_history,
					'request_link'					=> $request_link,
					'order_link'					=> $order_link,
					'add_to_project_link'			=> $add_to_project_link,

		//			'lang_name'						=> lang('name'),
					'contact_phone'					=> $ticket['contact_phone'],
					'pref_send_mail'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:'',
					'fileupload'					=> isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:'',
					'multiple_uploader'				=> true,
					'fileuploader_action'			=> "{menuaction:'property.fileuploader.add',"
															."upload_target:'property.botts.addfiles',"
															."id:'{$id}'}",
					'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
					'link_to_files'					=> isset($this->bo->config->config_data['files_url'])?$this->bo->config->config_data['files_url']:'',
					'files'							=> isset($ticket['files'])?$ticket['files']:'',
					'lang_filename'					=> lang('Filename'),
					'lang_file_action'				=> lang('Delete file'),
					'lang_view_file_statustext'		=> lang('click to view file'),
					'lang_file_action_statustext'	=> lang('Check to delete file'),
					'lang_upload_file'				=> lang('Upload file'),
					'lang_file_statustext'			=> lang('Select file to upload'),
					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 60,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
					'order_cat_list'				=> $order_catetory,
					'building_part_list'			=> array('options' => $this->bocommon->select_category_list(array('type'=> 'building_part','selected' =>$ticket['building_part'], 'order' => 'id', 'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),
					'order_dim1_list'				=> array('options' => $this->bocommon->select_category_list(array('type'=> 'order_dim1','selected' =>$ticket['order_dim1'], 'order' => 'id', 'id_in_name' => 'num' ))),
					'branch_list'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']==1 ? array('options' => execMethod('property.boproject.select_branch_list', $values['branch_id'])) :'',
					'preview_html'					=> "javascript:preview_html($id)",
					'preview_pdf'					=> "javascript:preview_pdf($id)",

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

			//_debug_array($data);die();

			$appname		= lang('helpdesk');
			$function_msg	= lang('view ticket detail');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
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
				if ((isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
					|| (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']==1
						&& $this->bo->fields_updated
						)
				)
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
					'status_list'					=> array('options' => $this->bo->get_status_list($ticket['status'])),
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
					'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id,'use_acl' => $this->_category_acl)),
					'lang_category'					=> lang('category'),
					'value_category_name'			=> $ticket['category_name'],

					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$form_link),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uitts.index')),
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
				'general'		=> array('label' => lang('general'), 'link' => '#general'),
				'notify'		=> array('label' => lang('notify'), 'link' => '#notify')
			);

			if($history)
			{
				$tabs['history']	= array('label' => lang('history'), 'link' => '#history');
			}

			phpgwapi_yui::tabview_setup('ticket_tabview');

			return  phpgwapi_yui::tabview_generate($tabs, $tab);
		}


		private function _pdf_order($id = 0, $preview = false , $show_cost = false)
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if(!$id)
			{
				$id = phpgw::get_var('id'); // in case of bigint
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}

			if(!$show_cost)
			{
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}

			$ticket = $this->bo->read_single($id, $values);

			if(isset($this->bo->config->config_data['invoice_acl']) && $this->bo->config->config_data['invoice_acl'] == 'dimb')
			{
				$approve_role = execMethod('property.boinvoice.check_role', $ticket['ecodimb']);

				$_ok = false;
				if($approve_role['is_supervisor'])
				{
					$_ok = true;
				}
				else if( $approve_role['is_budget_responsible'] )
				{
					$_ok = true;					
				}

				//FIXME
			/*
				else if( $common_data['workorder']['approved'] )
				{
					$_ok = true;					
				}
			*/
				if(!$_ok)
				{
					phpgwapi_cache::message_set( lang('order is not approved'), 'error' );
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.view', 'id'=> $id));
				}
				unset($_ok);
			}

			//FIXME
			$content = array(); //$this->_get_order_details($common_data['content'],	$show_cost);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date(time(),$dateformat);

			set_time_limit(1800);
			$pdf= CreateObject('phpgwapi.pdf');

			$pdf ->ezSetMargins(50,70,50,50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();

			if(isset($this->bo->config->config_data['order_logo']) && $this->bo->config->config_data['order_logo'])
			{
				$pdf->addJpegFromFile($this->bo->config->config_data['order_logo'],
					40,
					800,
					isset($this->bo->config->config_data['order_logo_width']) && $this->bo->config->config_data['order_logo_width'] ? $this->bo->config->config_data['order_logo_width'] : 80
				);
			}
			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,40,578,40);
		//	$pdf->line(20,820,578,820);
		//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50,28,6,$this->bo->config->config_data['org_name']);
			$pdf->addText(300,28,6,$date);

			if($preview)
			{
				$pdf->setColor(1,0,0);
				$pdf->addText(200,400,40,lang('DRAFT'),-10);
				$pdf->setColor(1,0,0);
			}

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');

//			$pdf->ezSetDy(-100);

			$pdf->ezStartPageNumbers(500,28,6,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$data = array
			(
				array('col1'=>"{$this->bo->config->config_data['org_name']}\n\nOrg.nr: {$this->bo->config->config_data['org_unit_id']}",'col2'=>lang('Order'),'col3'=>lang('order id') . "\n\n{$ticket['order_id']}")
			);		

			$pdf->ezTable($data,array('col1'=>'','col2'=>'','col3'=>''),''
				,array('showHeadings'=>0,'shaded'=>0,'xPos'=>0
				,'xOrientation'=>'right','width'=>500
				,'cols'=>array
				(
					'col1'=>array('justification'=>'right','width'=>200, 'justification'=>'left'),
					'col2'=>array('justification'=>'right','width'=>100, 'justification'=>'center'),
					'col3'=>array('justification'=>'right','width'=>200),
				)

			));


			$delivery_address = lang('delivery address'). ':';
			if(isset($this->bo->config->config_data['delivery_address']) && $this->bo->config->config_data['delivery_address'])
			{
				$delivery_address .= "\n{$this->bo->config->config_data['delivery_address']}";
			}
			else
			{
				$location_code = $ticket['location_data']['location_code'];
				$address_element = execMethod('property.botts.get_address_element', $location_code);
				foreach($address_element as $entry)
				{
					$delivery_address .= "\n{$entry['text']}: {$entry['value']}";
				}
			}

			$invoice_address = lang('invoice address') . ":\n{$this->bo->config->config_data['invoice_address']}";

			$GLOBALS['phpgw']->preferences->set_account_id($common_data['workorder']['user_id'], true);


			$on_behalf_of_assigned = phpgw::get_var('on_behalf_of_assigned', 'bool');
			if($on_behalf_of_assigned && isset($ticket['assignedto_name']))
			{
				$from_name = $ticket['assignedto_name'];
				$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
				$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->data;
			}
			else
			{
				$from_name = $GLOBALS['phpgw_info']['user']['fullname'];
			}

			$from = lang('date') . ": {$date}\n";
			$from .= lang('dimb') .": {$ticket['ecodimb']}\n";
			$from .= lang('from') . ":\n   {$from_name}";
			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['email']}";
			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['cellphone']}";



			if(isset($ticket['vendor_id']) && $ticket['vendor_id'])
			{
				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				$custom 		= createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data	= $contacts->read_single(array('id' => $ticket['vendor_id']),$vendor_data);
				if(is_array($vendor_data))
				{
					foreach($vendor_data['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$ticket['vendor_name']=$attribute['value'];
							break;
						}
					}
				}
				unset($contacts);
			}

			$data = array
			(
				array('col1'=>lang('vendor') . ":\n{$ticket['vendor_name']}",'col2' => $delivery_address),
				array('col1'=>$from,'col2'=>$invoice_address)
			);		

			$pdf->ezTable($data,array('col1'=>'','col2'=>''),''
				,array('showHeadings'=>0,'shaded'=>0,'xPos'=>0
				,'xOrientation'=>'right','width'=>500,'showLines'=> 2
				,'cols'=>array
				(
					'col1'=>array('justification'=>'right','width'=>250, 'justification'=>'left'),
					'col2'=>array('justification'=>'right','width'=>250, 'justification'=>'left'),
				)

			));

			$pdf->ezSetDy(-10);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
			$pdf->ezText(lang('descr').':',20);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
			$ressursnr = $GLOBALS['phpgw_info']['user']['preferences']['property']['ressursnr'];

			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id'		=> $ticket['contact_id'],
				'field'				=> 'contact',
				'type'				=> 'form'));


			if(isset($contact_data['value_contact_name']) && $contact_data['value_contact_name'])
			{
				$contact_name = ltrim($contact_data['value_contact_name']);
			}
			if(isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
			{
				$contact_email =$contact_data['value_contact_email'];
			}
			if(isset($contact_data['value_contact_tel']) && $contact_data['value_contact_tel'])
			{
				$contact_phone = $contact_data['value_contact_tel'];
			}

			$pdf->ezText($ticket['order_descr'],14);
			$pdf->ezSetDy(-20);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
			$pdf->ezText('Kontakt på bygget:',14);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
			$pdf->ezText($contact_name,14);
			$pdf->ezText($contact_email,14);
			$pdf->ezText($contact_phone,14);
			$pdf->ezSetDy(-20);

			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
			$pdf->ezText("Faktura må merkes med ordrenummer: {$ticket['order_id']} og ressursnr.:{$ressursnr}",14);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
			if($content)
			{
				$pdf->ezSetDy(-20);
				$pdf->ezTable($content,'',lang('details'),
					array('xPos'=>0,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 8,'showLines'=> 2,'titleFontSize' => 12,'outerLineThickness'=>2
					,'cols'=>array(
						lang('bill per unit')=>array('justification'=>'right','width'=>50)
						,lang('quantity')=>array('justification'=>'right','width'=>50)
						,lang('cost')=>array('justification'=>'right','width'=>50)
						,lang('unit')=>array('width'=>40)
						,lang('descr')=>array('width'=>120))
					));
			}

			if(isset($this->bo->config->config_data['order_footer_header']) && $this->bo->config->config_data['order_footer_header'])
			{
				if(!$content)
				{
					$pdf->ezSetDy(-100);
				}
				$pdf->ezText($this->bo->config->config_data['order_footer_header'],12);
				$pdf->ezText($this->bo->config->config_data['order_footer'],10);
			}

			$document= $pdf->ezOutput();

			if($preview)
			{
				$pdf->print_pdf($document,"order_{$ticket['order_id']}");
			}
			else
			{
				return $document;
			}
		}

	}
