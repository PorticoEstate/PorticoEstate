<?php
	/**
	* phpGroupWare - helpdesk: a Facilities Management System.
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
	* @package helpdesk
	* @subpackage helpdesk
 	* @version $Id: class.uitts.inc.php 6705 2010-12-26 23:10:55Z sigurdne $
	*/

	/**
	 * Description
	 * @package helpdesk
	 */

	phpgw::import_class('phpgwapi.yui');

	class helpdesk_uitts
	{
		var $public_functions = array
			(
				'index'				=> true,
				'view'				=> true,
				'add'				=> true,
				'delete'			=> true,
				'download'			=> true,
				'view_file'			=> true,
				'edit_status'		=> true,
				'get_vendor_email'	=> true,
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
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'helpdesk::helpdesk';
			if($this->tenant_id	= $GLOBALS['phpgw']->session->appsession('tenant_id','helpdesk'))
			{
				//			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->bo					= CreateObject('helpdesk.botts',true);
			$this->bocommon 			= & $this->bo->bocommon;
			$this->cats					= & $this->bo->cats;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'helpdesk');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'helpdesk');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'helpdesk');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'helpdesk');
			$this->acl_manage 			= $this->acl->check($this->acl_location, PHPGW_ACL_PRIVATE, 'helpdesk'); // manage
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
			$this->location_code		= $this->bo->location_code;
			$this->p_num				= $this->bo->p_num;
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
					'district_id'	=> $this->district_id,
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
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
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

		function download()
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$this->bo->allrows = true;
			$list = $this->bo->read($start_date,$end_date,'', $download = true);

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
			$name[] = 'user';
			$name[] = 'assignedto';
			$name[] = 'entry_date';
			$name[] = 'status';

			if( $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'helpdesk') )
			{
				$name[] = 'order_id';
				$name[] = 'vendor';
			}

			if( $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'helpdesk') )
			{
				$name[] = 'estimate';
				$name[] = 'actual_cost';
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


			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] : array();

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
			$receipt 	= $this->bo->update_status(array('status'=>$new_status),$id);
			if (isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'])
			{
				$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
			}
			//	$GLOBALS['phpgw']->session->appsession('receipt','helpdesk',$receipt);
			return "id ".$id." ".lang('Status has been changed');
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
				$GLOBALS['phpgw']->preferences->add('helpdesk','ticket_columns', $values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'helpdesk.uitts.columns',
				);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'		=> $this->bo->column_list($selected , $this->type_id, $allrows=true),
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
				//				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.index2'));
			}

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$this->save_sessiondata();
			$dry_run = false;
			$second_display = phpgw::get_var('second_display', 'bool');

			$default_category 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['default_district']:'');
			//FIXME: differentiate mainsreen and helpdesk if this should be used.
			$default_status 	= '';//isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_status'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_status']:'';
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

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'		=> 'helpdesk.uitts.index',
						'query'				=> $this->query,
						'district_id'		=> $this->district_id,
						'part_of_town_id'	=> $this->part_of_town_id,
						'cat_id'			=> $this->cat_id,
						'status_id'			=> $this->status_id,
						'p_num'				=> $this->p_num
					)
				);

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'helpdesk.uitts.index',"
					."second_display:1,"
					."sort: '{$this->sort}',"
					."order: '{$this->order}',"
					."cat_id:'{$this->cat_id}',"
					."status_id: '{$this->status_id}',"
					."user_id: '{$this->user_id}',"
					."query: '{$this->query}',"
					."p_num: '{$this->p_num}',"
					."district_id: '{$this->district_id}',"
					."start_date: '{$start_date}',"
					."end_date: '{$end_date}',"
					."location_code: '{$this->location_code}',"
					."allrows:'{$this->allrows}'";

				$link_data = array
					(
						'menuaction'	=> 'helpdesk.uitts.index',
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
						'location_code'	=> $this->location_code,
						'allrows'		=> $this->allrows
					);

				$group_filters = 'select';

				$values_combo_box = array();

				$values_combo_box[1]  = $this->bo->filter(array('format' => $group_filters, 'filter'=> $this->status_id,'default' => 'O'));

				if(isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'])
				{
					array_unshift ($values_combo_box[1],array ('id'=>'O2','name'=>$this->bo->config->config_data['tts_lang_open']));
				}
				$default_value = array ('id'=>'','name'=>lang('Open'));
				array_unshift ($values_combo_box[1],$default_value);

				if(!$this->_simple)
				{
					$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => true,'use_acl' => $this->_category_acl));
					$default_value = array ('cat_id'=>'','name'=> lang('no category'));
					array_unshift ($values_combo_box[0]['cat_list'],$default_value);

					$values_combo_box[2]  = $this->bocommon->get_user_list_right2('filter_',PHPGW_ACL_EDIT,$this->user_id,$this->acl_location);
					array_unshift ($values_combo_box[2],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine tickets')));
					$default_value = array('id'=>'','name'=>lang('no user'));
					array_unshift ($values_combo_box[2],$default_value);

					$datatable['actions']['form'] = array
						(
							array
							(
								'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction' 		=> 'helpdesk.uitts.index',
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
										//	'id' => 'btn_user_id',
										'id' => 'sel_user_id', // testing traditional listbox for long list
										'name' => 'user_id',
										'value'	=> lang('User'),
										'type' => 'select',
										'style' => 'filter',
										'values' => $values_combo_box[2],
										'onchange'=> 'onChangeSelect();',
										'tab_index' => 4
									),
									array
									(//for link "columns", next to Export button
										'type' => 'link',
										'id' => 'btn_columns',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' => 'helpdesk.uitts.columns'
										)
									)."','','width=300,height=600,scrollbars=1')",
									'value' => lang('columns'),
									'tab_index' => 10
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
									'menuaction' 		=> 'helpdesk.uitts.index',
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

			$ticket_list = array();
			//			if(!$dry_run)
				{
					$ticket_list = $this->bo->read($start_date, $end_date,'',$dry_run);
				}

			$this->bo->get_origin_entity_type();
			$uicols_related = $this->bo->uicols_related;
			//_debug_array($uicols_related);
			$uicols = array();

			$uicols['name'][] = 'priority';
			$uicols['name'][] = 'id';
			$uicols['name'][] = 'bgcolor';
			$uicols['name'][] = 'subject';
			$uicols['name'][] = 'assignedto';
			$uicols['name'][] = 'entry_date';
			$uicols['name'][] = 'status';

			if( $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'helpdesk') )
			{
				$uicols['name'][] = 'order_id';
				$uicols['name'][] = 'vendor';
			}

			if( $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'helpdesk') )
			{
				$uicols['name'][] = 'estimate';
				$uicols['name'][] = 'actual_cost';
			}

/*
			foreach($uicols_related as $related)
			{
				$uicols['name'][] = $related;			
			}
*/
			if( $this->_show_finnish_date )
			{
				$uicols['name'][] = 'finnish_date';
				$uicols['name'][] = 'delay';
			}

			$uicols['name'][] = 'child_date';
			$uicols['name'][] = 'link_view';
			$uicols['name'][] = 'lang_view_statustext';
			$uicols['name'][] = 'text_view';

			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] : array();

			foreach ($custom_cols as $col)
			{
				//			$uicols['input_type'][]	= 'text';
				$uicols['name'][]			= $col;
				//			$uicols['descr'][]		= lang(str_replace('_', ' ', $col));
				//			$uicols['statustext'][]	= $col;
			}


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
							$datatable['rows']['row'][$j]['column'][$k]['link']		=	$GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'helpdesk.uitts.view',
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
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'view',
						'statustext' 	=> lang('view the ticket'),
						'text'			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'helpdesk.uitts.view'
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
							'menuaction'	=> 'helpdesk.uitts._print',
							'target'		=> '_blank'
						)),
						'parameters'	=> $parameters
					);


				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('helpdesk', $this->acl_location)));

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
							'menuaction'	=> 'helpdesk.uitts.delete'
						)),
						'parameters'	=> $parameters
					);
			}

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_status_link'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_status_link'] == 'yes'
				&& $this->acl_edit)
			{

				unset($status['C']);
				foreach ($status as $status_code => $status_info)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 		=> 'status',
							'statustext' 	=> $status_info['status'],
							'text' 			=> lang('change to') . ':  ' .$status_info['status'],
							'confirm_msg'	=> lang('do you really want to change the status to %1',$status_info['status']),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'		=> 'helpdesk.uitts.edit_status',
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
								'delete'			=> 'dummy'// FIXME to trigger the json in helpdesk.js.
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
							'menuaction'	=> 'helpdesk.uitts.add'
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
				$datatable['headers']['header'][$i]['text'] 			= lang(str_replace('_', ' ', $uicols['name'][$i]));
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

			//path for helpdesk.js
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
			$opt_cb_depend =  $this->bocommon->select_part_of_town('filter_',$this->part_of_town_id,$this->district_id);
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
			$GLOBALS['phpgw']->css->validate_file('helpdesk');
			$GLOBALS['phpgw']->css->add_external_file('helpdesk/templates/base/css/helpdesk.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('helpdesk') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tts.index' , 'helpdesk' );
		}


		function add()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}
			if($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.add2'));
			}

//			$bolocation		= CreateObject('helpdesk.bolocation');

			$values		= phpgw::get_var('values');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			if ((isset($values['cancel']) && $values['cancel']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.index'));
			}

			//------------------- start ticket from other location
			$bypass 		= phpgw::get_var('bypass', 'bool');
			if(isset($_POST) && $_POST && isset($bypass) && $bypass)
			{
				$boadmin_entity		= CreateObject('helpdesk.boadmin_entity');
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
//					$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num, 'view' => true));
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
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','helpdesk');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','helpdesk');

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
//					$receipt['error'][]=array('msg'=>lang('Please select a location - or an entity!'));
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
						$bofiles	= CreateObject('property.bofiles','/helpdesk');
						$to_file = $bofiles->fakebase . "/{$receipt['id']}/{$values['file_name']}";

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
					$GLOBALS['phpgw']->session->appsession('receipt','helpdesk',$receipt);
					$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');

					if ((isset($values['save']) && $values['save']))
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.index'));
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.view', 'id' => $receipt['id'], 'tab' =>'general'));					
					}
				}
				else
				{
					if(isset($values['location']) && $values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
//						$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
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

/*
			$location_data=$bolocation->initiate_ui_location(array(
				'values'	=> (isset($values['location_data'])?$values['location_data']:''),
				'type_id'	=> -1, // calculated from location_types
				'no_link'	=> false, // disable lookup links for location type less than type_id
				'tenant'	=> true,
				'lookup_type'	=> 'form',
				'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
				'entity_data'	=> (isset($values['p'])?$values['p']:'')
			));
*/

			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id'		=> $ticket['contact_id'],
				'field'				=> 'contact',
				'type'				=> 'form'));

			$link_data = array
				(
					'menuaction'	=> 'helpdesk.uitts.add'
				);

			if(!isset($values['assignedto']))
			{
				$values['assignedto']= (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault']:'');
			}
			if(!isset($values['group_id']))
			{
				$values['group_id']= (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['groupdefault'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['groupdefault']:'');
			}

			if(!isset($values['cat_id']))
			{
				$this->cat_id = (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_category'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_category']:'');
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

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'location_data'					=> $location_data,
					'lang_no_user'					=> lang('Select user'),
					'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
					'select_user_name'				=> 'values[assignedto]',
					'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$values['assignedto'],$this->acl_location),

					'lang_no_group'					=> lang('No group'),
					'group_list'					=> $this->bocommon->get_group_list('select',$values['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
					'select_group_name'				=> 'values[group_id]',

					'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
					'select_priority_name'			=> 'values[priority]',
					'priority_list'					=> $this->bo->get_priority_list((isset($values['priority'])?$values['priority']:'')),

					'status_list'					=> $this->bo->get_status_list('O'),

					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

					'lang_details'					=> lang('Details'),
					'lang_category'					=> lang('category'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_send'						=> lang('send'),
					'value_details'					=> (isset($values['details'])?$values['details']:''),
					'value_subject'					=> (isset($values['subject'])?$values['subject']:''),

					'value_finnish_date'			=> (isset($values['finnish_date'])?$values['finnish_date']:''),
					'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
					'lang_datetitle'				=> lang('Select date'),
					'lang_finnish_date_statustext'	=> lang('Select the estimated date for closing the task'),

					'lang_cancel_statustext'		=> lang('Back to the ticket list'),
					'lang_send_statustext'			=> lang('Save the entry and return to list'),
					'lang_save_statustext'			=> lang('Save the ticket'),
					'lang_no_cat'					=> lang('no category'),
					'lang_town_statustext'			=> lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
					'lang_part_of_town'				=> lang('Part of town'),
					'lang_no_part_of_town'			=> lang('No part of town'),
					'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id,'use_acl' => $this->_category_acl)),
					'pref_send_mail'				=> (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification']:''),
					'fileupload'					=> (isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:''),
				);

			//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('add ticket');

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tts.add', 'helpdesk' );
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('helpdesk') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','files'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
		}


		function get_vendor_email($vendor_id = 0)
		{
			if(!$vendor_id)
			{
				$vendor_id = phpgw::get_var('vendor_id', 'int', 'GET', 0);
			}
			$vendor_email = execMethod('property.sowo_hour.get_email', $vendor_id);

			$content_email = array();
			foreach($vendor_email as $_entry )
			{				
				$content_email[] = array
					(

						'value_email'		=> $_entry['email'],
						'value_select'		=> '<input type="checkbox" name="values[vendor_email][]" value="'.$_entry['email'].'" title="'.lang('The address to which this order will be sendt').'">'
					);
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{

				if(count($content_email))
				{
					return json_encode($content_email);
				}
				else
				{
					return "";
				}
			}
			return $content_email;
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$id = phpgw::get_var('id', 'int', 'GET');

			if($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.view2', 'id' => $id ));
			}

//			$bolocation	= CreateObject('helpdesk.bolocation');

			$values = phpgw::get_var('values');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			$values['ecodimb']			= phpgw::get_var('ecodimb');
			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','helpdesk');
			$GLOBALS['phpgw']->session->appsession('receipt','helpdesk','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts', 'files'));

			if(isset($values['save']))
			{
				if(!$this->acl_edit)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uilocation.stop', 'perm'=>4, 'acl_location'=> $this->acl_location));
				}

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','helpdesk');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','helpdesk');

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

				if ( (isset($values['send_mail']) && $values['send_mail']) 
					|| (isset($this->bo->config->config_data['mailnotification'])
					&& $this->bo->config->config_data['mailnotification']
					&& $this->bo->fields_updated
				))
				{
					$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
				}

				//--------- files
				$bofiles	= CreateObject('property.bofiles', '/helpdesk');
				if(isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/{$id}/", $values);
				}

				$values['file_name']=str_replace(' ','_',$_FILES['file']['name']);

				if($values['file_name'])
				{
					$to_file = $bofiles->fakebase . "/{$id}/{$values['file_name']}";

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
				//			$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uitts.index'));
			}
			//---------end files
			$ticket = $this->bo->read_single($id);

			$order_link				= '';
			$add_to_project_link	= '';
			$request_link			='';

			if($GLOBALS['phpgw']->acl->check('.project.request', PHPGW_ACL_ADD, 'helpdesk'))
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

			if($GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'helpdesk'))
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
					'menuaction'	=> 'helpdesk.uitts.view',
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

/*
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
*/
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
					if($GLOBALS['phpgw']->acl->check(".entity.{$entry['id']}", PHPGW_ACL_ADD, 'helpdesk'))
					{
						$link_entity[$i]['link'] = $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'		=> 'helpdesk.uientity.edit',
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
					'menuaction'	=> 'helpdesk.uitts.view_file',
					'id'		=> $id
				);

			if(!$this->_simple && $this->_show_finnish_date)
			{
				$jscal = CreateObject('phpgwapi.jscalendar');
				$jscal->add_listener('values_finnish_date');
			}

			// -------- start order section
			$order_read 			= $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'helpdesk');
			$order_add 				= $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'helpdesk');
			$order_edit 			= $this->acl->check('.ticket.order', PHPGW_ACL_EDIT, 'helpdesk');

			$access_order = false;
			if($order_add || $order_edit)
			{
				$access_order = true;
			}

			if($access_order)
			{
				$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
					'vendor_id'			=> $ticket['vendor_id'],
					'vendor_name'		=> $ticket['vendor_name']));

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

				if ( isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['approval_from'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['approval_from'] )
				{
					$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['approval_from'];
				}

				$need_approval = isset($this->bo->config->config_data['workorder_approval']) ? $this->bo->config->config_data['workorder_approval'] : '';

				$supervisor_email = array();
				if ($supervisor_id && $need_approval)
				{
					$prefs = $this->bocommon->create_preferences('helpdesk',$supervisor_id);
					$supervisor_email[] = array
						(
							'id'	  => $supervisor_id,
							'address' => $prefs['email'],
						);
					if ( isset($prefs['approval_from']) )
					{
						$prefs2 = $this->bocommon->create_preferences('helpdesk', $prefs['approval_from']);

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

			if($vendor_email)
			{
				$historylog	= CreateObject('helpdesk.historylog','tts');

				$subject = lang(workorder).": {$ticket['order_id']}";

				$organisation = '';
				$contact_name = '';
				$contact_email = '';
				$contact_phone = '';

				if(isset($this->bo->config->config_data['org_name']))
				{
					$organisation = $this->bo->config->config_data['org_name'];
				}

				if(isset($values['on_behalf_of_assigned']) && $values['on_behalf_of_assigned'] && isset($ticket['assignedto_name']))
				{
					$user_name = $ticket['assignedto_name'];
					$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
					$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->data;
					$_behalf_alert = lang('this order is sent by %1 on behalf of %2',$GLOBALS['phpgw_info']['user']['fullname'], $user_name);
					$historylog->add('C',$id,$_behalf_alert);
					unset($_behalf_alert);
				}
				else
				{
					$user_name = $GLOBALS['phpgw_info']['user']['fullname'];				
				}
				$ressursnr = $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ressursnr'];
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

				$user_phone = $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['cellphone'];
				$user_email = $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['email'];
				$order_email_template = $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['order_email_template'];

				$body = nl2br(str_replace(array
					(
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

				if(isset($values['file_attach']) && is_array($values['file_attach']))
				{
//					$bofiles	= CreateObject('helpdesk.bofiles' '/helpdesk');
					$attachments = $bofiles->get_attachments("/{$id}/", $values['file_attach']);
					$attachment_log = ' ' . lang('attachments') . ' : ' . implode(', ',$values['file_attach']);
				}
				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
					$coordinator_email = "{$coordinator_name}<{$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['email']}>";
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
								'appname'			=> 'helpdesk',
								'location'			=> '.ticket',
								'id'				=> $id,
								'responsible'		=> $values['vendor_id'],
								'responsible_type'  => 'vendor',
								'action'			=> 'remind',
								'remark'			=> '',
								'deadline'			=> ''
							);

						$reminds = execMethod('helpdesk.sopending_action.set_pending_action', $action_params);
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
				$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['email'];

				$subject = lang(Approval).": ".$ticket['order_id'];
				$message = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'helpdesk.uitts.view', 'id'=> $id)).'">' . lang('Workorder %1 needs approval',$ticket['order_id']) .'</a>';

				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					$action_params = array
						(
							'appname'			=> 'helpdesk',
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

						execMethod('helpdesk.sopending_action.set_pending_action', $action_params);
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

			$note_def[] = array('key' => 'publish_note','label'=>lang('publish text'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter');
			foreach($additional_notes as &$note)
			{
				$note['value_note'] = nl2br($note['value_note']);
				$_checked = $note['value_publish'] ? 'checked' : '';
				$note['publish_note'] = "<input type='checkbox' {$_checked}  name='values[publish_note][]' value='{$id}_{$note['value_id']}' title='".lang('Check to publish text at frontend')."'>";
			}


			//_debug_Array($additional_notes);die();
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
					'values'	=>	json_encode($note_def)
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


			$content_email = $this->get_vendor_email(isset($ticket['vendor_id'])?$ticket['vendor_id']:0);

			$datavalues[3] = array
				(
					'name'					=> "3",
					'values' 				=> json_encode($content_email),
					'total_records'			=> count($content_email),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);


			//----------------------------------------------datatable settings--------			
			$msgbox_data = $this->bocommon->msgbox_data($receipt);
			$cat_select	= $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id,'use_acl' => $this->_category_acl));
			$this->cats->set_appname('helpdesk','.project');
			$order_catetory	= $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $ticket['order_cat_id']));
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
	//				'vendor_email'					=> $vendor_email,

					'contact_data'					=> $contact_data,
					'lookup_type'					=> $lookup_type,
					'simple'						=> $this->_simple,
					'show_finnish_date'				=> $this->_show_finnish_date,
					'tabs'							=> self::_generate_tabs(true),
					'td_count'						=> '""',
					'base_java_url'					=> "{menuaction:'helpdesk.uitts.get_vendor_email'}",
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
					'cat_select'					=> $cat_select,

					'value_category_name'			=> $ticket['category_name'],

					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$form_link),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'helpdesk.uitts.index')),
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
					'pref_send_mail'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification']:'',
					'fileupload'					=> isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:'',
					'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
					'link_to_files'					=> isset($this->bo->config->config_data['files_url'])?$this->bo->config->config_data['files_url']:'',
					'files'							=> isset($ticket['files'])?$ticket['files']:'',
					'lang_filename'					=> lang('Filename'),
					'lang_file_action'				=> lang('Delete file'),
					'lang_view_file_statustext'		=> lang('click to view file'),
					'lang_file_action_statustext'	=> lang('Check to delete file'),
					'lang_upload_file'				=> lang('Upload file'),
					'lang_file_statustext'			=> lang('Select file to upload'),
					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textareacols'] : 60,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textarearows'] : 6,
					'order_cat_list'				=> $order_catetory,
					'building_part_list'			=> array('status_list' => $this->bocommon->select_category_list(array('type'=> 'building_part','selected' =>$ticket['building_part'], 'order' => 'id', 'id_in_name' => 'num' ))),
					'order_dim1_list'				=> array('status_list' => $this->bocommon->select_category_list(array('type'=> 'order_dim1','selected' =>$ticket['order_dim1'], 'order' => 'id', 'id_in_name' => 'num' ))),
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
			$GLOBALS['phpgw']->css->validate_file('helpdesk');
			$GLOBALS['phpgw']->css->add_external_file('helpdesk/templates/base/css/helpdesk.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tts.view', 'helpdesk' );
			//-----------------------datatable settings---

			//_debug_array($data);die();

			$appname		= lang('helpdesk');
			$function_msg	= lang('view ticket detail');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('helpdesk') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'helpdesk.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bofiles	= CreateObject('property.bofiles','/helpdesk');
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
