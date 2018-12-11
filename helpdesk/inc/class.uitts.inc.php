<?php
	/**
	* phpGroupWare - helpdesk: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2017 Free Software Foundation, Inc. http://www.fsf.org/
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

	phpgw::import_class('phpgwapi.uicommon_jquery');

	class helpdesk_uitts extends phpgwapi_uicommon_jquery
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
				'edit_priority' => true,
				'get_vendor_email'	=> true,
				'_print'			=> true,
				'columns'			=> true,
				'update_data'		=> true,
				'upload_clip'		=> true,
				'view_image'		=> true,
				'get_reverse_assignee'=>true,
				'handle_multi_upload_file' => true,
				'build_multi_upload_file' => true,
				'custom_ajax'			=> true
			);

		/**
		 * @var boolean $_simple use simplified interface
		 */
		protected $_simple = false;
		protected $_group_candidates = array();
		protected $_show_finnish_date = false;
		protected $_category_acl = false;
		protected $lang_app_name;
		var $part_of_town_id;
		var $status;
		var $filter;
		var $user_filter;
		var $parent_cat_id;

		public function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'helpdesk::helpdesk';
			if($this->tenant_id	= $GLOBALS['phpgw']->session->appsession('tenant_id','helpdesk'))
			{
				//			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
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
			$this->parent_cat_id		= $this->bo->parent_cat_id;
			$this->district_id			= $this->bo->district_id;
			$this->allrows				= $this->bo->allrows;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->location_code		= $this->bo->location_code;
			$this->p_num				= $this->bo->p_num;

			if($this->parent_cat_id)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "helpdesk::helpdesk_$this->parent_cat_id";
			}

			$default_interface = isset($this->bo->config->config_data['tts_default_interface']) ? $this->bo->config->config_data['tts_default_interface'] : '';

			/*
			 * Inverted logic
			 */
			if($default_interface == 'simplified')
			{
				$this->_simple = true;
			}

			$user_groups =  $GLOBALS['phpgw']->accounts->membership($this->account);
			$simple_group = isset($this->bo->config->config_data['fmttssimple_group']) ? $this->bo->config->config_data['fmttssimple_group'] : array();
			foreach ($user_groups as $group => $dummy)
			{
				if (in_array($group, $simple_group))
				{
					if($default_interface == 'simplified')
					{
						$this->_simple = false;
					}
					else
					{
						$this->_simple = true;
					}
					break;
				}
			}
			if (isset($this->bo->config->config_data['fmtts_assign_group_candidates']) && is_array($this->bo->config->config_data['fmtts_assign_group_candidates']))
			{
				foreach ($this->bo->config->config_data['fmtts_assign_group_candidates'] as $group_candidate)
				{
					if ($group_candidate)
					{
						$this->_group_candidates[] = $group_candidate;
					}
				}
			}

			reset($user_groups);

			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $this->_group_candidates))
				{
					$this->_simple = false;
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
			if (!empty($this->bo->config->config_data['app_name']))
			{
				$this->lang_app_name = $this->bo->config->config_data['app_name'];
			}
			else
			{
				$this->lang_app_name = lang('helpdesk');
			}
		}

		/**
		 * called as ajax from edit form
		 *
		 * @param string  $query
		 *
		 * @return array
		 */
		public function get_reverse_assignee()
		{
			$query = phpgw::get_var('query');

			$filter = array('active' => 1);

			$account_list = $GLOBALS['phpgw']->accounts->get_list('accounts', -1,'ASC', 'account_lastname',  $query, false, $filter);

			$values = array();

			foreach ($account_list as $account)
			{
				$values[] = array(
					'id' => $account->id,
					'name' => $account->__toString()
				);
			}
			return array('ResultSet' => array('Result' => $values));
		}

		function get_params()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => $export ? -1 : phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'status_id' => $this->bo->status_id,
				'user_id' => $this->bo->user_id,
				'reported_by' => $this->bo->reported_by,
				'cat_id' => $this->bo->cat_id,
				'vendor_id' => $this->bo->vendor_id,
				'district_id' => $this->bo->district_id,
				'part_of_town_id' => $this->bo->part_of_town_id,
				'start_date' => $this->bo->start_date,
				'end_date' => $this->bo->end_date,
				'location_code' => $this->bo->location_code,
				'p_num' => $this->bo->p_num,
				'building_part' => $this->bo->building_part,
				'b_account' => $this->bo->b_account,
				'ecodimb' => $this->bo->ecodimb,
				'branch_id' => phpgw::get_var('branch_id'),
				'order_dim1' => phpgw::get_var('order_dim1'),
				'check_date_type' => phpgw::get_var('check_date_type', 'int'),
				'parent_cat_id'	=> $this->parent_cat_id
			);

			return $params;
		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$params = $this->get_params();

			$values = $this->bo->read($params);

			if ($values)
			{
				$status = array();
				$status['X'] = lang('closed');
				$status['O'] = isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open');
				$status['C'] = lang('closed');

				$custom_status = $this->bo->get_custom_status();

				foreach ($custom_status as $custom)
				{
					$status["C{$custom['id']}"] = $custom['name'];
				}

				foreach ($values as &$entry)
				{
					$entry['status'] = $status[$entry['status']];
				}
			}

			if ($export)
			{
				return $values;
			}
//_debug_array($values);
			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['sum_budget'] = $this->bo->sum_budget;
			$result_data['sum_actual_cost'] = $this->bo->sum_actual_cost;
			$result_data['sum_difference'] = $this->bo->sum_difference;
			$result_data['draw'] = phpgw::get_var('draw', 'int');

			$link_data = array
				(
				'menuaction' => 'helpdesk.uitts.view', 'parent_cat_id' => $this->parent_cat_id
			);

			array_walk($result_data['results'], array($this, '_add_links'), $link_data);
//			_debug_array($result_data);
			return $this->jquery_results($result_data);
		}

		function _print()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$id = phpgw::get_var('id', 'int');

			$ticket_html = $this->bo->mail_ticket($id, $fields_updated = true, $receipt = array(), $get_message = true);

			$ticket = $this->bo->read_single($id);

			$content_files = array();

			$lang_files = lang('files');

			$files = '';
			if($ticket['files'])
			{
				$files = <<<HTML

				<br/>
				<table class='pure-table pure-table-bordered pure-table-striped'>
					<thead>
							<tr>
								<th>
									#
								</th>
								<th>
									{$lang_files}
								</th>
							</tr>
						</thead>
						<tbody>
HTML;

				$i=1;
				foreach ($ticket['files'] as $_entry)
				{
					$files .= <<<HTML

					<tr>
						<td>
							{$i}
						</td>
						<td>
							{$_entry['name']}
						</td>
					</tr>
HTML;
					$i++;
				}
				$files .= <<<HTML

					</tbody>
				</table>

HTML;

			}

			$lang_print = lang('print');

			$html = <<<HTML

			<!DOCTYPE html>
			<html>
				<head>
					<title>{$ticket_html['subject']}</title>
					<link href="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/pure/css/pure-min.css" type="text/css" rel="StyleSheet">
				</head>
					<script type="text/javascript">
					document.onload = window.print();
					</script>
				<body>
					<H2>{$ticket_html['subject']}</H2>
					{$ticket_html['body']}
					{$files}
				</body>
			</html>
HTML;

			echo $html;
		}

		function download()
		{
			$params = $this->get_params();

			$list = $this->bo->read($params);

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

			if($this->_show_finnish_date)
			{
				$name[] = 'finnish_date';
				$name[] = 'delay';
				array_push($descr,lang('finnish date'),lang('delay'));
			}


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

		function edit_priority()
		{
			if (!$this->acl_edit)
			{
				return lang('sorry - insufficient rights');
			}

			$new_priority = phpgw::get_var('new_priority', 'int');
			$id = phpgw::get_var('id', 'int');

//			$ticket = $this->bo->read_single($id);

			$receipt = $this->bo->update_priority(array('priority' => $new_priority), $id);
			if ((isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification']) || (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_notify_me']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_notify_me'] == 1 && $this->bo->fields_updated
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

		public function handle_multi_upload_file()
		{
			$id = phpgw::get_var('id');

			phpgw::import_class('property.multiuploader');

			$options['fakebase'] = "/helpdesk";
			$options['base_dir'] = $id;
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/helpdesk/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => 'helpdesk.uitts.handle_multi_upload_file', 'id' => $id)));
			$upload_handler = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'OPTIONS':
				case 'HEAD':
					$upload_handler->head();
					break;
				case 'GET':
					$upload_handler->get();
					break;
				case 'PATCH':
				case 'PUT':
				case 'POST':
					$upload_handler->add_file();
					break;
				case 'DELETE':
					$upload_handler->delete_file();
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		public function build_multi_upload_file()
		{
			phpgwapi_jquery::init_multi_upload_file();
			$id = phpgw::get_var('id', 'int');

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.handle_multi_upload_file', 'id' => $id));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
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


		private function _get_fields()
		{
			$this->bo->get_origin_entity_type();
			$uicols_related = $this->bo->uicols_related;

			$uicols = array();

			$uicols['name'][] = 'id';
			$uicols['descr'][] = lang('id');

			 if(empty($this->bo->config->config_data['disable_priority']))
			 {
				$uicols['name'][] = 'priority';
				$uicols['descr'][] = lang('priority');
			}

			$uicols['name'][] = 'subject';
			$uicols['descr'][] = lang('subject');
			$uicols['name'][] = 'entry_date';
			$uicols['descr'][] = lang('entry date');

			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] : array();
			$columns = $this->bo->get_columns();

			foreach ($custom_cols as $col)
			{
				$uicols['name'][] = $col;
				$uicols['descr'][] = $columns[$col]['name'];
			}

			$uicols['name'][] = 'link_view';
			$uicols['descr'][] = lang('link view');
			$uicols['name'][] = 'lang_view_statustext';
			$uicols['descr'][] = lang('view statustext');
			$uicols['name'][] = 'text_view';
			$uicols['descr'][] = lang('text view');

			$count_uicols_name = count($uicols['name']);

			$fields = array();
			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if (isset($uicols_related) && in_array($uicols['name'][$k], $uicols_related))
				{
					$params['formatter'] = 'JqueryPortico.formatLinkRelated';
				}

				if ($uicols['datatype'][$k] == 'link')
				{
					$params['formatter'] = 'JqueryPortico.formatLinkGeneric';
				}

				if ($uicols['name'][$k] == 'id')
				{
					$params['formatter'] = 'JqueryPortico.formatTtsIdLink';
				}

				if ($uicols['name'][$k] == 'entry_date')
				{
					$params['formatter'] = 'JqueryPortico.formatLink';
				}

				if ($uicols['name'][$k] == 'location_code')
				{
					$params['formatter'] = 'JqueryPortico.searchLinkTts';
				}
				if ($uicols['name'][$k] == 'estimate')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'actual_cost')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'difference')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'address' || $uicols['name'][$k] == 'id' || $uicols['name'][$k] == 'priority')
				{
					$params['sortable'] = true;
				}
				if ($uicols['name'][$k] == 'priority' || $uicols['name'][$k] == 'id' || $uicols['name'][$k] == 'assignedto' || $uicols['name'][$k] == 'finnish_date' || $uicols['name'][$k] == 'user' || $uicols['name'][$k] == 'entry_date' || $uicols['name'][$k] == 'order_id' || $uicols['name'][$k] == 'modified_date')
				{
					$params['sortable'] = true;
				}
				if ($uicols['name'][$k] == 'text_view' || $uicols['name'][$k] == 'bgcolor' || $uicols['name'][$k] == 'link_view' || $uicols['name'][$k] == 'lang_view_statustext' || $uicols['name'][$k] == 'hidden_id')
				{
					$params['hidden'] = true;
				}

				$fields[] = $params;
			}
			return $fields;
		}

		private function _get_filters()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[3] = $this->bo->filter(array('format' => $group_filters, 'filter' => $this->status_id,
				'default' => 'O'));

			if (isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'])
			{
				array_unshift($values_combo_box[3], array('id' => 'O2', 'name' => $this->bo->config->config_data['tts_lang_open']));
			}
			$default_value = array('id' => '', 'name' => lang('Open'));
			array_unshift($values_combo_box[3], $default_value);

			$combos[] = array('type' => 'filter',
				'name' => 'status_id',
				'extra' => $code,
				'text' => lang('status'),
				'list' => $values_combo_box[3]
			);

			if(!$this->_simple)
			{
				if($this->parent_cat_id)
				{
					$_cats = $this->cats->return_sorted_array(0, false, '', '', '', false, $this->parent_cat_id);
					$_categories = array();
					foreach ($_cats as $_cat)
					{
						if ($_cat['active'] != 2)
						{
							$_cat['name'] =  str_repeat(' . ' , (int)($_cat['level'] -1) ) . $GLOBALS['phpgw']->strip_html($_cat['name']);
							$_categories[] = $_cat;
						}
					}
				}
				else
				{
					$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format' => 'filter',
						'selected' => $this->cat_id, 'globals' => true, 'use_acl' => $this->_category_acl));

					$_categories = array();
					foreach ($values_combo_box[0]['cat_list'] as $_category)
					{
						$_categories[] = array('id' => $_category['cat_id'], 'name' => $_category['name']);
					}

				}

				array_unshift($_categories, array('id' => '', 'name' => lang('no category')));

				$combos[] = array('type' => 'filter',
					'name' => 'cat_id',
					'extra' => '',
					'text' => lang('category'),
					'list' => $_categories
				);

				$filter_tts_assigned_to_me = $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_assigned_to_me'];

				$values_combo_box[4] = $this->_get_user_list($this->user_id);

				array_unshift($values_combo_box[4], array(
					'id' => -1 * $GLOBALS['phpgw_info']['user']['account_id'],
					'name' => lang('my assigned tickets'),
					'selected'	=> ((int)$this->user_id < 0  || (int)$filter_tts_assigned_to_me == 1) ? 1 : 0));

//				array_unshift($values_combo_box[4], array('id' => '', 'name' => lang('assigned to')));
				$combos[] = array(
					'type' => 'filter',
					'multiple'	=> true,
					'name' => 'user_id',
					'extra' => '',
					'text' => lang('assigned to'),
					'list' => $values_combo_box[4]
				);

				$values_combo_box[5] = $this->bo->get_reported_by($this->reported_by);
				array_unshift($values_combo_box[5], array('id' => $GLOBALS['phpgw_info']['user']['account_id'],
					'name' => lang('my submitted tickets')));
				array_unshift($values_combo_box[5], array('id' => '', 'name' => lang('reported by')));
				$combos[] = array('type' => 'filter',
					'name' => 'reported_by',
					'extra' => '',
					'text' => lang('reported by'),
					'list' => $values_combo_box[5]
				);
			}

			$attrib_data = $this->bo->get_custom_cols();
			if ($attrib_data)
			{
				foreach ($attrib_data as $attrib)
				{
					$_filter_data = array();
					if (($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R') && $attrib['choice'])
					{

						$_filter_data[] = array
							(
							'id' => '',
							'name' => lang('select') . " {$attrib['input_text']}"
						);

						$_selected = phpgw::get_var($attrib['column_name']);
						foreach ($attrib['choice'] as $choice)
						{
							$_filter_data[] = array
								(
								'id' => $choice['id'],
								'name' => htmlspecialchars($choice['value'], ENT_QUOTES, 'UTF-8'),
								'selected' => $choice['id'] == $_selected ? 1 : 0
							);
						}

						$combos[] = array('type' => 'filter',
							'name' => $attrib['column_name'],
							'extra' => '',
							'text' => $attrib['input_text'],
							'list' => $_filter_data
						);
					}
				}
			}

			return $combos;
		}

		function index()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$_cats = $this->cats->return_sorted_array(0, false, '', '', '', false, false);

			$subs = false;
			foreach ($_cats as $_cat)
			{
				if ($_cat['level'] > 0 && $_cat['active'] != 2)
				{
					$subs = true;
				}
			}

			if($subs && (int)$this->parent_cat_id <= 0)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "helpdesk::helpdesk_{$_cat['main']}"; // to expand the menu
				$GLOBALS['phpgw_info']['flags']['app_header'] = $this->lang_app_name . ': ' . lang('choose a section from the menu');
				self::render_template_xsl('datatable_jquery', array());
				return;
			}

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			self::add_javascript('helpdesk', 'portico', 'tts.index.js');
			self::add_javascript('phpgwapi', 'materialize', 'js/materialize.min.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/materialize/css/materialize.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/materialize/css/materialize_override.css');

			$start_date = urldecode($this->start_date);
			$end_date = urldecode($this->end_date);

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');

			$appname = $this->lang_app_name;

			if($this->parent_cat_id)
			{
				$parent_category =  CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket')->return_single($this->parent_cat_id);
				$appname = !empty($parent_category[0]['name']) ? $parent_category[0]['name'] : $this->lang_app_name;
			}

			$function_msg = lang('list ticket');

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'show_filter_group' => true,
						'item' => array(
							array
								(
								'type' => 'date-picker',
								'id' => 'start_date',
								'name' => 'start_date',
								'value' => $start_date,
								'text' => lang('from')
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'end_date',
								'name' => 'end_date',
								'value' => $end_date,
								'text' => lang('to')
							)
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'helpdesk.uitts.index', 'parent_cat_id' => $this->parent_cat_id,
						'phpgw_return_as' => 'json')),
					'download' => self::link(array('menuaction' => 'helpdesk.uitts.download',
						'export' => true, 'allrows' => true)),
					'allrows' => true,
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'helpdesk.uitts.columns'}, {closeAction:'reload'})"),
					'new_item' => self::link(array('menuaction' => 'helpdesk.uitts.add', 'parent_cat_id' => $this->parent_cat_id)),
					'editor_action' => self::link(array('menuaction' => 'helpdesk.uitts.edit_survey_title')),
					'field' => $this->_get_fields(),
					'query' => phpgw::get_var('query')
				)
			);

			$filters = $this->_get_filters();

			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$js ='';

			if (!empty($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['refreshinterval']) && (int)$GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['refreshinterval'] > 0)
			{
				$refreshinterval = (int) $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['refreshinterval'] * 1000;

				$js =<<<JS
					setInterval( function () {
						var api = oTable.api();
						api.ajax.reload();
				}, {$refreshinterval} );
JS;
			}

			foreach ($filters as $filter)
			{
				if($filter['type'] == 'filter')
				{
					$js .=<<<JS

					$("#{$filter['name']}").hide();
					$("#{$filter['name']}").material_select();
JS;

				}
			}

			if($js)
			{
				$GLOBALS['phpgw']->js->add_code('', $js, true);
			}

			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view_survey',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'helpdesk.uitts.view',
					'parent_cat_id' => $this->parent_cat_id
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'print',
				'statustext' => lang('print the ticket'),
				'text' => lang('print view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'helpdesk.uitts._print',
					'parent_cat_id' => $this->parent_cat_id
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			if(!$this->_simple)
			{
				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('helpdesk', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'edit',
						'text' => lang('open JasperReport %1 in new window', $report['title']),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'helpdesk.uijasper.view',
							'parent_cat_id' => $this->parent_cat_id,
							'jasper_id' => $report['id'],
						)),
						'target' => '_blank',
						'parameters' => json_encode($parameters)
					);
				}

				if ($this->acl_delete)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'delete',
						'statustext' => lang('delete the ticket'),
						'text' => lang('delete'),
						'confirm_msg' => lang('do you really want to delete this ticket'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'helpdesk.uitts.delete'
						)),
						'parameters' => json_encode($parameters)
					);
				}

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_status_link']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_status_link'] == 'yes' && $this->acl_edit)
				{
					$status['X'] = array
						(
						'status' => lang('closed'),
					);
					$status['O'] = array
						(
						'status' => isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open'),
					);

					$custom_status = $this->bo->get_custom_status();

					foreach ($custom_status as $custom)
					{
						$status["C{$custom['id']}"] = array
							(
							'status' => $custom['name'],
						);
					}

					foreach ($status as $status_code => $status_info)
					{
						$data['datatable']['actions'][] = array
							(
							'my_name' => 'status',
							'statustext' => $status_info['status'],
							'text' => lang('change to') . ' status:  ' . $status_info['status'],
							'confirm_msg' => lang('do you really want to change the status to %1', $status_info['status']),
							'action' => $GLOBALS['phpgw']->link('/index.php', array
								(
								'menuaction' => 'helpdesk.uitts.edit_status',
								'edit_status' => true,
								'new_status' => $status_code,
								'second_display' => true,
								'sort' => $this->sort,
								'order' => $this->order,
								'cat_id' => $this->cat_id,
								'filter' => $this->filter,
								'user_filter' => $this->user_filter,
								'query' => $this->query,
								'district_id' => $this->district_id,
								'allrows' => $this->allrows,
								'delete' => 'dummy'// FIXME to trigger the json in property.js.
							)),
							'parameters' => json_encode($parameters)
						);
					}

					$_priorities = $this->bo->get_priority_list();

					foreach ($_priorities as $_priority_info)
					{
						$data['datatable']['actions'][] = array
							(
							'my_name' => 'priority',
							'statustext' => $_priority_info['name'],
							'text' => lang('change to') . ' ' . lang('priority') . ':  ' . $_priority_info['name'],
							'confirm_msg' => lang('do you really want to change the priority to %1', $_priority_info['name']),
							'action' => $GLOBALS['phpgw']->link('/index.php', array
								(
								'menuaction' => 'helpdesk.uitts.edit_priority',
								'edit_status' => true,
								'new_priority' => $_priority_info['id'],
								'second_display' => true,
								'sort' => $this->sort,
								'order' => $this->order,
								'cat_id' => $this->cat_id,
								'filter' => $this->filter,
								'user_filter' => $this->user_filter,
								'query' => $this->query,
								'district_id' => $this->district_id,
								'allrows' => $this->allrows,
								'delete' => 'dummy'// FIXME to trigger the json in property.js.
							)),
							'parameters' => json_encode($parameters)
						);
					}
				}
			}

			if (count($data['datatable']['actions']) < 10)
			{
				$data['datatable']['group_buttons'] = false;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = $this->lang_app_name . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function upload_clip()
		{
			$id = phpgw::get_var('id', 'POST', 'int');
			$ret = array(
				'status' => 'error',
				'message'=> lang('No data')
			);

			if($_POST['pasted_image'])
			{
				$_ticket = $this->bo->read_single($id);
				$bofiles = CreateObject('property.bofiles','/helpdesk');
				$img = $_POST['pasted_image'];
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				$file = '/tmp/' . uniqid() . '.png';
				if (file_put_contents($file, $data))
				{
					$to_file = "{$bofiles->fakebase}/{$id}/" .  str_replace(array(' ', '/', '?'), array('_', '_', ''), $_ticket['subject']) . '_' . ( (int)count($_ticket['files']) +1 ) . '.png';
					$bofiles->create_document_dir("{$id}");
					$bofiles->vfs->override_acl = 1;

					$ret = array(
						'status' => 'ok',
						'message'=> 'Ok'
					);
					if (!$bofiles->vfs->cp(array(
							'from' => $file,
							'to' => $to_file,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
					{
						$ret = array(
							'status' => 'error',
							'message'=> lang('Failed to upload file !')
						);

					}
					$bofiles->vfs->override_acl = 0;
				}
			}
			return $ret;
		}

		function add()
		{
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}

			$values = phpgw::get_var('values');
			$values['contact_id'] = phpgw::get_var('contact', 'int', 'POST');
			if ((isset($values['cancel']) && $values['cancel']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'helpdesk.uitts.index','parent_cat_id' => $this->parent_cat_id));
			}

			$values_attribute = phpgw::get_var('values_attribute');

			//------------------- start ticket from other location
			$bypass = phpgw::get_var('bypass', 'bool');
//			if(isset($_POST) && $_POST && isset($bypass) && $bypass)
			if ($bypass)
			{
				$values['descr'] = phpgw::get_var('descr');
				$p_entity_id = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id = phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
				$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');

				$origin = phpgw::get_var('origin');
				$origin_id = phpgw::get_var('origin_id', 'int');
			}

			if (isset($values['origin']) && $values['origin'])
			{
				$origin = $values['origin'];
				$origin_id = $values['origin_id'];
			}

			$interlink = CreateObject('property.interlink');

			if (isset($origin) && $origin)
			{
				$values['origin_data'][0]['location'] = $origin;
				$values['origin_data'][0]['descr'] = $interlink->get_location_name($origin);
				$values['origin_data'][0]['data'][] = array
					(
					'id' => $origin_id,
					'link' => $interlink->get_relation_link(array('location' => $origin), $origin_id),
				);
			}
			//_debug_array($insert_record);
			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'helpdesk');

				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_values' . $this->acl_location, 'helpdesk');

				if (isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j = 0; $j < count($insert_record_entity); $j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);

				if (!$values['subject'])
				{
					if(isset($this->bo->config->config_data['tts_mandatory_title']) && $this->bo->config->config_data['tts_mandatory_title'])
					{
						$receipt['error'][] = array('msg' => lang('Please enter a title !'));
					}
					else
					{
						$_cat = $this->cats->return_single($values['cat_id']);
						$values['subject'] = $_cat[0]['name'];
					}
				}

				if (!$values['cat_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a category !'));
				}

				if (!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][] = array('msg' => lang('Please give som details !'));
				}

				if (isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as &$attribute)
					{
						if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && !ctype_digit($attribute['value']))
						{
							$receipt['error'][] = array('msg' => lang('Please enter integer for attribute %1', $attribute['input_text']));
						}

						if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'V' && strlen($attribute['value']) > $attribute['precision'])
						{
							$receipt['error'][] = array('msg' => lang('Max length for attribute %1 is: %2', "\"{$attribute['input_text']}\"", $attribute['precision']));
							$attribute['value'] = substr($attribute['value'], 0, $attribute['precision']);
						}
					}
				}

				$disable_userassign_on_add = isset($this->bo->config->config_data['tts_disable_userassign_on_add']) ? $this->bo->config->config_data['tts_disable_userassign_on_add'] : false;
				$disable_groupassign_on_add = isset($this->bo->config->config_data['tts_disable_groupassign_on_add']) ? $this->bo->config->config_data['tts_disable_groupassign_on_add'] : false;

				if (!isset($values['assignedto']) || !$values['assignedto'])
				{
					$values['assignedto'] = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault'] : '';
				}

				if (!$values['assignedto'] && !$values['group_id'] && !$disable_userassign_on_add && !$disable_groupassign_on_add)
				{
					$receipt['error'][] = array('msg' => lang('Please select a person or a group to handle the ticket !'));
				}

				if (!isset($values['status']) || !$values['status'])
				{
					$values['status'] = "O";
				}
				if (!isset($values['priority']) || !$values['priority'])
				{
					$_priority = $this->bo->get_priority_list();
					$values['priority'] = count($_priority);
					unset($_priority);
				}

				if (isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute)
					{
						if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
						}
					}
				}

				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->add($values, $values_attribute);

					//------------ files
					$values['file_name'] = @str_replace(' ', '_', $_FILES['file']['name']);

					$bofiles = CreateObject('property.bofiles','/helpdesk');
					if ($values['file_name'] && $receipt['id'])
					{
						$to_file = "{$bofiles->fakebase}/{$receipt['id']}/{$values['file_name']}";

						if ($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][] = array('msg' => lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("{$receipt['id']}");
							$bofiles->vfs->override_acl = 1;

							if (!$bofiles->vfs->cp(array(
									'from' => $_FILES['file']['tmp_name'],
									'to' => $to_file,
									'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][] = array('msg' => lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}
					}
					if(!empty($_POST['pasted_image']) && empty($_POST['pasted_image_is_blank'])	)
					{
						$img = $_POST['pasted_image'];
						$img = str_replace('data:image/png;base64,', '', $img);
						$img = str_replace(' ', '+', $img);
						$data = base64_decode($img);
						$file = '/tmp/' . uniqid() . '.png';
						if (file_put_contents($file, $data))
						{
							$to_file = "{$bofiles->fakebase}/{$receipt['id']}/" .  str_replace(array(' ', '/', '?'), array('_', '_', ''), $values['subject']) . '.png';
							$bofiles->create_document_dir("{$receipt['id']}");
							$bofiles->vfs->override_acl = 1;

							if (!$bofiles->vfs->cp(array(
									'from' => $file,
									'to' => $to_file,
									'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][] = array('msg' => lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}
					}

					//--------------end files
					$GLOBALS['phpgw']->session->appsession('receipt', 'helpdesk', $receipt);
					//	$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');

					if ((isset($values['save']) && $values['save']))
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'helpdesk.uitts.index', 'parent_cat_id' => $this->parent_cat_id));
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'helpdesk.uitts.view', 'parent_cat_id' => $this->parent_cat_id,
							'id' => $receipt['id'], 'tab' => 'general'));
					}
				}
			}

			/* Preserve attribute values from post */
			if (isset($receipt['error']) && (isset($values_attribute) && is_array($values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values, $values_attribute);
			}

			$values = $this->bo->get_attributes($values);

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction' => 'property.uiproject.attrib_history',
							'attrib_id' => $attribute['id'],
							'id' => $id,
							'edit' => true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}
				}
			}

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_me_as_contact']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_me_as_contact'] == 1)
			{
				$ticket['contact_id'] = $GLOBALS['phpgw']->accounts->get($this->account)->person_id;
			}
			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));

			$link_data = array(
				'menuaction' => 'helpdesk.uitts.add', 'parent_cat_id' => $this->parent_cat_id
			);

			if (!isset($values['assignedto']))
			{
				$values['assignedto'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault'] : '');
			}
			if (!isset($values['group_id']))
			{
				$values['group_id'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['groupdefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['groupdefault'] : '');
			}

			if (!isset($values['cat_id']))
			{
				$this->cat_id = (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_category']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_category'] : '');
			}
			else
			{
				$this->cat_id = $values['cat_id'];
			}

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');


			if (!$this->_simple && $this->_show_finnish_date)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('values_finnish_date');
			}

			$membership = $GLOBALS['phpgw']->accounts->membership($this->account);
			$my_groups = array();
			foreach ($membership as $group_id => $group)
			{
				$my_groups[$group_id] = $group->firstname;
			}

			$tabs = array();
			$tabs['add'] = array('label' => lang('Add'), 'link' => '#add');
			$active_tab = 'add';

			$cat_select = $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]',
					'selected' => $this->cat_id, 'use_acl' => $this->_category_acl, 'required' => true,'class'=>'pure-input-1-2'));

			/**overide*/
			if($this->parent_cat_id)
			{
				$cat_select['cat_list'] = array();

				$_cats = $this->cats->return_sorted_array(0, false, '', '', '', false, $this->parent_cat_id);
				foreach ($_cats as $_cat)
				{
					if ($_cat['active'] != 2)
					{
						if($_cat['level'] > 1)
						{
							$cat_name_arr = array();
							$cat_path = $this->cats->get_path($_cat['id']);

							foreach ($cat_path as $cat_path_entry)
							{
								if($this->parent_cat_id == $cat_path_entry['id'])
								{
									continue;
								}
								$cat_name_arr[] = $cat_path_entry['name'];

							}
							$cat_name = implode(' -> ', $cat_name_arr);

						}
						else
						{
							$cat_name	= str_repeat(' . ' , (int)($_cat['level'] -1) ) . $GLOBALS['phpgw']->strip_html($_cat['name']);
						}
//						$cat_name	= str_repeat(' . ' , (int)($_cat['level'] -1) ) . $GLOBALS['phpgw']->strip_html($_cat['name']);
						$cat_select['cat_list'][] = array
						(
							'cat_id'	=> $_cat['id'],
							'name'		=> $cat_name,
							'selected'	=> $_cat['id'] == $this->cat_id ? 'selected' : '',
							'description' => $_cat['description']
						);
					}
				}
			}

			$data = array(
				'my_groups' => json_encode($my_groups),
				'custom_attributes' => array('attributes' => $values['attributes']),
				'lookup_functions' => isset($values['lookup_functions']) ? $values['lookup_functions'] : '',
				'contact_data' => $contact_data,
				'simple' => $this->_simple,
				'show_finnish_date' => $this->_show_finnish_date,
				'value_origin' => isset($values['origin_data']) ? $values['origin_data'] : '',
				'value_origin_type' => (isset($origin) ? $origin : ''),
				'value_origin_id' => (isset($origin_id) ? $origin_id : ''),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_no_user' => lang('Select user'),
				'lang_user_statustext' => lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name' => 'values[assignedto]',
				'user_list' => $this->_get_user_list($values['assignedto']),
				'disable_userassign_on_add' => isset($this->bo->config->config_data['tts_disable_userassign_on_add']) ? $this->bo->config->config_data['tts_disable_userassign_on_add'] : '',
				'disable_groupassign_on_add' => isset($this->bo->config->config_data['tts_disable_groupassign_on_add']) ? $this->bo->config->config_data['tts_disable_groupassign_on_add'] : '',
				'disable_priority'			=> isset($this->bo->config->config_data['disable_priority']) ? $this->bo->config->config_data['disable_priority'] : '',
				'lang_no_group' => lang('No group'),
				'group_list' => $this->bo->get_group_list($values['group_id']),
				'select_group_name' => 'values[group_id]',
				'lang_priority_statustext' => lang('Select the priority the selection belongs to.'),
				'select_priority_name' => 'values[priority]',
				'priority_list' => array('options' => $this->bo->get_priority_list((isset($values['priority']) ? $values['priority'] : ''))),
				'status_list' => array('options' => $this->bo->get_status_list('O')),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_details' => lang('Details'),
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_send' => lang('send'),
				'value_details' => (isset($values['details']) ? $values['details'] : ''),
				'value_subject' => (isset($values['subject']) ? $values['subject'] : ''),
				'tts_mandatory_title' => $this->bo->config->config_data['tts_mandatory_title'],
				'value_finnish_date' => (isset($values['finnish_date']) ? $values['finnish_date'] : ''),
				'lang_finnish_date_statustext' => lang('Select the estimated date for closing the task'),
				'lang_no_cat' => lang('no category'),
				'lang_town_statustext' => lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_part_of_town' => lang('Part of town'),
				'lang_no_part_of_town' => lang('No part of town'),
				'cat_select' => $cat_select,
				'pref_send_mail' => (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification'] : ''),
				'fileupload' => true,//(isset($this->bo->config->config_data['fmttsfileupload']) ? $this->bo->config->config_data['fmttsfileupload'] : ''),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			//_debug_array($data);
			$function_msg = lang('add ticket');

			self::add_javascript('helpdesk', 'portico', 'tts.add.js');
			phpgwapi_jquery::formvalidator_generate(array('date', 'security','file'));
			phpgwapi_jquery::load_widget('autocomplete');
			$this->_insert_custom_js();
			$GLOBALS['phpgw_info']['flags']['app_header'] = $this->lang_app_name . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('tts', 'files', 'attributes_form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add' => $data));
		}

		function update_data()
		{
			$action = phpgw::get_var('action', 'string', 'GET');
			switch ($action)
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

		function view_image()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$thumb = phpgw::get_var('thumb', 'bool');
			$img_id = phpgw::get_var('img_id', 'int');

			$bofiles = CreateObject('property.bofiles');

			if($img_id)
			{
				$file_info = $bofiles->vfs->get_info($img_id);
				$file = "{$file_info['directory']}/{$file_info['name']}";
			}
			else
			{
				$file = urldecode(phpgw::get_var('file'));
			}

			$source = "{$bofiles->rootdir}{$file}";
			$thumbfile = "$source.thumb";

			// prevent path traversal
			if (preg_match('/\.\./', $source))
			{
				return false;
			}

			$uigallery = CreateObject('property.uigallery');

			$re_create = false;
			if ($uigallery->is_image($source) && $thumb && $re_create)
			{
				$uigallery->create_thumb($source, $thumbfile, $thumb_size = 50);
				readfile($thumbfile);
			}
			else if ($thumb && is_file($thumbfile))
			{
				readfile($thumbfile);
			}
			else if ($uigallery->is_image($source) && $thumb)
			{
				$uigallery->create_thumb($source, $thumbfile, $thumb_size = 50);
				readfile($thumbfile);
			}
			else if ($img_id)
			{
				$bofiles->get_file($img_id);
			}
			else
			{
				$bofiles->view_file('', $file);
			}
		}

		function get_files()
		{
			$id = phpgw::get_var('id', 'int');

			if (!$this->acl_read)
			{
				return;
			}

			$link_file_data = array
				(
				'menuaction' => 'helpdesk.uitts.view_file',
			);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);
			$values = $this->bo->read_single($id);

			$content_files = array();
			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$z = 0;
			foreach ($values['files'] as $_entry)
			{
				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
					'attach_file' => '<input type="checkbox" name="values[file_attach][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to attach file') . '">'
				);
				if ( in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name'] = $_entry['name'];
					$content_files[$z]['img_id'] = $_entry['file_id'];
					$content_files[$z]['img_url'] = self::link(array(
							'menuaction' => 'helpdesk.uitts.view_image',
							'img_id'	=>  $_entry['file_id'],
							'file' => $_entry['directory'] . '/' . $_entry['file_name']
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z ++;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$total_records = count($content_files);

				return array
					(
					'data' => $content_files,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);
			}
			return $content_files;
		}

		function view()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$id = phpgw::get_var('id', 'int', 'GET');
			$add_external_communication = phpgw::get_var('external_communication', 'int');

			if ($add_external_communication)
			{
				self::redirect(array('menuaction' => 'helpdesk.uiexternal_communication.edit','ticket_id' => $id,
					'type_id' => $add_external_communication ));
			}

			$values = phpgw::get_var('values');
			$values['contact_id'] = phpgw::get_var('contact', 'int', 'POST');
			$values['vendor_id'] = phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name'] = phpgw::get_var('vendor_name', 'string', 'POST');
			$values_attribute = phpgw::get_var('values_attribute');

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt', 'helpdesk');
			$GLOBALS['phpgw']->session->appsession('receipt', 'helpdesk', '');
			if (!$receipt)
			{
				$receipt = array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts', 'files', 'attributes_form',
				'datatable_inline'));

			$historylog	= & $this->bo->historylog;

			if (isset($values['save']))
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');

				$notified = createObject('property.notify')->read(array('location_id' => $location_id, 'location_item_id' => $id));

				$additional_users = array();
				foreach ($notified as $entry)
				{
					if($entry['account_id'])
					{
						$additional_users[] = $entry['account_id'];
					}
				}

				if(in_array($this->account, $additional_users));
				{
					$this->acl_edit = true;
				}

				if (!$this->acl_edit)
				{
					phpgw::no_access();
				}

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'helpdesk');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_values' . $this->acl_location, 'helpdesk');

				if (isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j = 0; $j < count($insert_record_entity); $j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);

				if (isset($values['budget']) && $values['budget'] && !ctype_digit($values['budget']))
				{
					$values['budget'] = (int)$values['budget'];
					$receipt['error'][] = array('msg' => lang('budget') . ': ' . lang('Please enter an integer !'));
				}

				if (isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as &$attribute)
					{
						if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && !ctype_digit($attribute['value']))
						{
							$receipt['error'][] = array('msg' => lang('Please enter integer for attribute %1', $attribute['input_text']));
						}

						if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'V' && strlen($attribute['value']) > $attribute['precision'])
						{
							$receipt['error'][] = array('msg' => lang('Max length for attribute %1 is: %2', $attribute['input_text'], $attribute['precision']));
							$attribute['value'] = substr($attribute['value'], 0, $attribute['precision']);
						}
					}
				}



				if (isset($values['takeover']) && $values['takeover'])
				{
					$values['assignedto'] = $this->account;
				}
				/*
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
				 */
				$receipt = $this->bo->update_ticket($values, $id, $receipt, $values_attribute, $this->_simple);

				if ((isset($values['send_mail']) && $values['send_mail']) || (isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'] && $this->bo->fields_updated
					) || (isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_notify_me']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_notify_me'] == 1 && $this->bo->fields_updated
					)
				)
				{
					$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt, false, isset($values['send_mail']) && $values['send_mail'] ? true : false);
				}

				//--------- files
				$bofiles = CreateObject('property.bofiles','/helpdesk');
				if (isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/{$id}", $values);
				}

				$values['file_name'] = str_replace(' ', '_', $_FILES['file']['name']);

				if ($values['file_name'])
				{
					$to_file = "{$bofiles->fakebase}/{$id}/{$values['file_name']}";

					if ($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][] = array('msg' => lang('This file already exists !'));
					}
					else
					{
						$bofiles->create_document_dir("{$id}");
						$bofiles->vfs->override_acl = 1;

						if (!$bofiles->vfs->cp(array(
								'from' => $_FILES['file']['tmp_name'],
								'to' => $to_file,
								'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][] = array('msg' => lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}
				}

				//---------end files
				if (phpgw::get_var('notify_client_by_sms', 'bool') && isset($values['response_text']) && $values['response_text'] && phpgw::get_var('to_sms_phone'))
				{
					$to_sms_phone = phpgw::get_var('to_sms_phone');
					//			$ticket['contact_phone'] = $to_sms_phone;

					$sms = CreateObject('sms.sms');
					$sms->websend2pv($this->account, $to_sms_phone, $values['response_text']);
					$historylog->add('MS', $id, "{$to_sms_phone}::{$values['response_text']}");
				}
			}

			/* Preserve attribute values from post */
			if (isset($receipt['error']) && (isset($values_attribute) && is_array($values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values, $values_attribute);
			}

			$ticket = $this->bo->read_single($id, $values);

			if(!$ticket)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'helpdesk.uitts.index', 'parent_cat_id' => $this->parent_cat_id));
			}
			if (isset($ticket['attributes']) && is_array($ticket['attributes']))
			{
				foreach ($ticket['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction' => 'property.uiproject.attrib_history',
							'attrib_id' => $attribute['id'],
							'id' => $id,
							'edit' => true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}
				}
			}


			$form_link = array(
				'menuaction' => 'helpdesk.uitts.view',
				'parent_cat_id' => $this->parent_cat_id,
				'id' => $id
			);

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));


			if ($ticket['cat_id'])
			{
				$this->cat_id = $ticket['cat_id'];
			}


			if ($this->_show_finnish_date)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('values_finnish_date');
			}

			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$notes = array(
				array(
					'value_id' => '', //not from historytable
					'value_count' => 1,
					'value_date' => $GLOBALS['phpgw']->common->show_date($ticket['timestamp']),
					'value_user' => $ticket['reverse_id']? $ticket['reverse_name'] : $ticket['user_name'],
					'value_note' => $ticket['details'],
					'value_publish' => $ticket['publish_note']
				)
			);

			$additional_notes = array_merge($notes, $additional_notes);

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap'])
			{
				foreach ($additional_notes as &$_note)
				{
					$_note['value_note'] = wordwrap($_note['value_note'], 40);
				}
			}
			unset($_note);

			$note_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_note', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);

			foreach ($additional_notes as &$note)
			{
				$note['value_note'] = preg_replace("/[[:alpha:]]+:\/\/[^<>[:space:]]+[[:alnum:]\/]/","<a href=\"\\0\">\\0</a>", $note['value_note']);
				$note['value_note'] = nl2br($note['value_note']);
			}

			$datatable_def = array();

			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $note_def,
				'data' => json_encode($additional_notes),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(0,'asc')))
				)
			);

//			_debug_Array($datatable_def);die();
			//---datatable settings---------------------------------------------------

			$datatable_def[] = array(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'value_action', 'label' => lang('Action'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_old_value', 'label' => lang('old value'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_new_value', 'label' => lang('New value'), 'sortable' => true,
						'resizeable' => true)),
				'data' => json_encode($record_history),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$link_file_data = array('menuaction' => 'helpdesk.uitts.view_file');

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.view_file'));

			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$content_files = array();

			$z = 0;
			foreach ($ticket['files'] as $_entry)
			{
				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
					'attach_file' => '<input type="checkbox" name="values[file_attach][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to attach file') . '">'
				);
				if ( in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name'] = $_entry['name'];
					$content_files[$z]['img_id'] = $_entry['file_id'];
					$content_files[$z]['img_url'] = self::link(array(
							'menuaction' => 'helpdesk.uitts.view_image',
							'img_id'	=>  $_entry['file_id'],
							'file' => $_entry['directory'] . '/' . $_entry['file_name']
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z ++;
			}

			$attach_file_def = array(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'picture', 'label' => lang('picture'), 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.showPicture'),
				array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
					'resizeable' => true, 'formatter' => 'FormatterCenter'),
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'ColumnDefs' => $attach_file_def,
				'data' => json_encode($content_files),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', $this->acl_location);
			$notify_info = execMethod('property.notify.get_jquery_table_def', array(
				'location_id' => $location_id,
				'location_item_id' => $id,
				'count' => 6,//count($datatable_def),
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.notify.update_data',
						'location_id' => $location_id, 'location_item_id' => $id, 'action' => 'refresh_notify_contact',
						'phpgw_return_as' => 'json'))),
				)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_6',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.notify.update_data',
						'location_id' => $location_id, 'location_item_id' => $id, 'action' => 'refresh_notify_contact',
						'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $notify_info['column_defs']['values'],
				'data' => json_encode(array()),
				'tabletools' => $notify_info['tabletools'],
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$external_messages_def = array(
				array(
					'key' => 'id',
					'label' => lang('id'),
					'hidden' => false
					),
				array(
					'key' => 'subject_link',
					'label' => lang('subject'),
					'hidden' => false,
					'sortable' => true,
					),
				array(
					'key' => 'mail_recipients',
					'label' => lang('email'),
					'hidden' => false,
					'sortable' => true,
					),
				array(
					'key' => 'modified_date',
					'label' => lang('modified date'),
					'hidden' => false,
					'sortable' => true,
					)
				);

			$external_messages = createObject('helpdesk.soexternal_communication')->read($id);

			foreach ($external_messages as &$external_message)
			{
				$external_message['modified_date'] = $GLOBALS['phpgw']->common->show_date($external_message['modified_date']);
				$external_message['mail_recipients'] = implode(', ', $external_message['mail_recipients']);
				$external_message['subject_link'] = "<a href=\"" . self::link(array('menuaction' => 'helpdesk.uiexternal_communication.edit',
						'id' => $external_message['id'], 'ticket_id' => $id)) . "\">{$external_message['subject']}</a>";
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_7',
				'requestUrl' => "''",
				'data' => json_encode($external_messages),
				'ColumnDefs' => $external_messages_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			//----------------------------------------------datatable settings--------

//_debug_array($supervisor_email);die();
			$msgbox_data = $this->bocommon->msgbox_data($receipt);
			$cat_select = $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]',
				'selected' => $this->cat_id, 'use_acl' => $this->_category_acl, 'required' => true,'class'=>'pure-input-1-2'));

			/**overide*/
			if($this->parent_cat_id)
			{
				$cat_select['cat_list'] = array();

				$_cats = $this->cats->return_sorted_array(0, false, '', '', '', false, $this->parent_cat_id);
				foreach ($_cats as $_cat)
				{
					if ($_cat['active'] != 2)
					{
						if($_cat['level'] > 1)
						{
							$cat_name_arr = array();
							$cat_path = $this->cats->get_path($_cat['id']);

							foreach ($cat_path as $cat_path_entry)
							{
								if($this->parent_cat_id == $cat_path_entry['id'])
								{
									continue;
								}
								$cat_name_arr[] = $cat_path_entry['name'];

							}
							$cat_name = implode(' -> ', $cat_name_arr);

						}
						else
						{
							$cat_name	= str_repeat(' . ' , (int)($_cat['level'] -1) ) . $GLOBALS['phpgw']->strip_html($_cat['name']);
						}
//						$cat_name	= str_repeat(' . ' , (int)($_cat['level'] -1) ) . $GLOBALS['phpgw']->strip_html($_cat['name']);
						$cat_select['cat_list'][] = array
						(
							'cat_id'	=> $_cat['id'],
							'name'		=> $cat_name,
							'selected'	=> $_cat['id'] == $this->cat_id ? 'selected' : '',
							'description' => $_cat['description']
						);
					}
				}
			}


			$_ticket_cat_found = false;
			if (isset($cat_select['cat_list']) && is_array($cat_select['cat_list']))
			{
				foreach ($cat_select['cat_list'] as $cat_list_entry)
				{
					if ($cat_list_entry['cat_id'] == $ticket['cat_id'])
					{
						$_ticket_cat_found = true;
						break;
					}
				}
			}

			if (!$_ticket_cat_found)
			{
				$category = $this->cats->return_single($ticket['cat_id']);
//_debug_array($category);

				array_unshift($cat_select['cat_list'], array(
					'cat_id' => $category[0]['id'],
					'name' => $category[0]['name'],
					'description' => $category[0]['description'],
					'selected' => true,
					)
				);
				$cat_select['disabled'] = true;
				$cat_select['hidden_value'] = $ticket['cat_id'];
//_debug_array($cat_select);die();
			}


			$membership = $GLOBALS['phpgw']->accounts->membership($this->account);
			$my_groups = array();
			foreach ($membership as $group_id => $group)
			{
				$my_groups[$group_id] = $group->firstname;
			}

			phpgwapi_jquery::formvalidator_generate(array('date', 'security',
				'file'));

			$tabs = array();
			$tabs['general'] = array('label' => lang('General'), 'link' => '#general');
			$tabs['notify'] = array('label' => lang('Notify'), 'link' => '#notify');
			$tabs['history'] = array('label' => lang('History'), 'link' => '#history');
			$active_tab = 'general';

			$data = array(
				'datatable_def' => $datatable_def,
				'my_groups' => json_encode($my_groups),
				'custom_attributes' => array('attributes' => $ticket['attributes']),
				'lookup_functions' => isset($ticket['lookup_functions']) ? $ticket['lookup_functions'] : '',
				'simple' => $this->_simple,
				'send_response' => isset($this->bo->config->config_data['tts_send_response']) ? $this->bo->config->config_data['tts_send_response'] : '',
				'disable_priority'	=> isset($this->bo->config->config_data['disable_priority']) ? $this->bo->config->config_data['disable_priority'] : '',
				'value_sms_phone' => $ticket['contact_phone'],
				'value_budget' => $ticket['budget'],
				'value_actual_cost' => $ticket['actual_cost'],
				'contact_data' => $contact_data,
				'show_finnish_date' => $this->_show_finnish_date,
				'tabs' => self::_generate_tabs(true),
				'base_java_url' => "{menuaction:'helpdesk.uitts.update_data',id:{$id}}",
				'location_item_id' => $id,
				'value_origin' => $ticket['origin'],
				'value_target' => $ticket['target'],
				'value_finnish_date' => $ticket['finnish_date'],
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_status' => $ticket['status'],
				'status_list' => array('options' => $this->bo->get_status_list($ticket['status'])),
				'lang_no_user' => lang('Select user'),
				'lang_user_statustext' => lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name' => 'values[assignedto]',
				'value_assignedto_id' => $ticket['assignedto'],
				'value_owned_by'		=> $ticket['user_name'],
				'user_list' => $this->_get_user_list($ticket['assignedto']),
				'lang_no_group' => lang('No group'),
				'group_list' => $this->bo->get_group_list($ticket['group_id']),
				'select_group_name' => 'values[group_id]',
				'value_group_id' => $ticket['group_id'],
				'lang_takeover' => (isset($values['assignedto']) && $values['assignedto'] != $this->account) || (!isset($values['assignedto']) || !$values['assignedto']) ? lang('take over') : '',
				'value_priority' => $ticket['priority'],
				'lang_priority_statustext' => lang('Select the priority the selection belongs to.'),
				'select_priority_name' => 'values[priority]',
				'priority_list' => array('options' => $this->bo->get_priority_list($ticket['priority'])),
				'lang_no_cat' => lang('no category'),
				'value_cat_id' => $this->cat_id,
				'cat_select' => $cat_select,
				'value_category_name' => $ticket['category_name'],
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $form_link),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.index','parent_cat_id' => $this->parent_cat_id)),
				'value_subject' => $ticket['subject'],
				'value_id' => '[ #' . $id . ' ] - ',
				'id'		=> $id,
				'value_details' => $ticket['details'],
				'value_opendate' => $ticket['entry_date'],
				'value_assignedfrom' => $ticket['user_name'],
				'value_assignedto_name' => isset($ticket['assignedto_name']) ? $ticket['assignedto_name'] : '',
				'show_billable_hours' => isset($this->bo->config->config_data['show_billable_hours']) ? $this->bo->config->config_data['show_billable_hours'] : '',
				'value_billable_hours' => $ticket['billable_hours'],
				'additional_notes' => $additional_notes,
				'record_history' => $record_history,
				'contact_phone' => $ticket['contact_phone'],
				'pref_send_mail' => isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['tts_user_mailnotification'] : '',
				'fileupload' => true,//isset($this->bo->config->config_data['fmttsfileupload']) ? $this->bo->config->config_data['fmttsfileupload'] : '',
				'multiple_uploader' => true,
				'multi_upload_parans' => "{menuaction:'helpdesk.uitts.build_multi_upload_file', id:'{$id}'}",
//				'fileuploader_action' => "{menuaction:'property.fileuploader.add',upload_target:'helpdesk.botts.addfiles',id:'{$id}'}",
				'link_to_files' => isset($this->bo->config->config_data['files_url']) ? $this->bo->config->config_data['files_url'] : '',
				'files' => isset($ticket['files']) ? $ticket['files'] : '',
				'lang_filename' => lang('Filename'),
				'lang_file_action' => lang('Delete file'),
				'lang_view_file_statustext' => lang('click to view file'),
				'lang_file_action_statustext' => lang('Check to delete file'),
				'lang_upload_file' => lang('Upload file'),
				'lang_file_statustext' => lang('Select file to upload'),
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textareacols'] : 60,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['textarearows'] : 6,
//					'order_cat_list'				=> $order_catetory,
				'building_part_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'building_part', 'selected' => $ticket['building_part'], 'order' => 'id',
						'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),
				'order_dim1_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'order_dim1', 'selected' => $ticket['order_dim1'], 'order' => 'id',
						'id_in_name' => 'num'))),
				'tax_code_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'tax', 'selected' => $ticket['tax_code'], 'order' => 'id',
						'id_in_name' => 'num'))),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'set_user' => ($ticket['user_id'] != $ticket['reverse_id'] && $ticket['assignedto'] ==  $this->account) ? true : false,
				'parent_cat_id' => $this->parent_cat_id,
			);

			phpgwapi_jquery::load_widget('numberformat');
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('helpdesk', 'portico', 'tts.view.js');

			$this->_insert_custom_js();
			//-----------------------datatable settings---
			//_debug_array($data);die();

			$function_msg = lang('view ticket detail');
			$GLOBALS['phpgw_info']['flags']['app_header'] = $this->lang_app_name . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('view' => $data));
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access();
			}

			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		protected function _generate_tabs( $history = '' )
		{
			if (!$tab = phpgw::get_var('tab'))
			{
				$tab = 'general';
			}

			$tabs = array(
				'general' => array('label' => lang('general'), 'link' => '#general'),
				'notify' => array('label' => lang('notify'), 'link' => '#notify')
			);

			if ($history)
			{
				$tabs['history'] = array('label' => lang('history'), 'link' => '#history');
			}

			return phpgwapi_jquery::tabview_generate($tabs, $tab, 'ticket_tabview');
		}

		public function custom_ajax()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access();
			}

			$acl_location = phpgw::get_var('acl_location');

			if (!$acl_location)
			{
				return false;
			}

			$criteria = array
			(
				'appname'	=> 'helpdesk',
				'location'	=> $acl_location,
				'allrows'	=> true
			);

			if (!$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria))
			{
				return false;
			}

			$ajax_result = array();

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['ajax'])
				{
					require $file;
				}
			}

			return $ajax_result;
		}
		/**
		 *
		 */
		private function _insert_custom_js()
		{
			$criteria = array
				(
				'appname' => 'helpdesk',
				'location' => $this->acl_location,
				'allrows' => true
			);

			if (!$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria))
			{
				return false;
			}

			$js_found = false;

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && $entry['client_side'] && is_file($file))
				{
					$GLOBALS['phpgw']->js->add_external_file("/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}");
					$js_found = true;
				}
			}

			if ($js_found)
			{
				phpgw::import_class('phpgwapi.jquery');
				phpgwapi_jquery::load_widget('core');
			}
		}

		private function _get_user_list($selected)
		{
			$xsl_rootdir = PHPGW_SERVER_ROOT . "/property/templates/{$GLOBALS['phpgw_info']['server']['template_set']}";

			$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_select'), $xsl_rootdir);

			if($this->parent_cat_id)
			{
				$acl_location = ".ticket.category.{$this->parent_cat_id}";
			}
			else
			{
				$acl_location = $this->acl_location;
			}

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, $acl_location, 'helpdesk', $this->_group_candidates);
			$user_list = array();
			$selected_found = false;
			foreach ($users as $user)
			{
				$name = (isset($user['account_lastname']) ? $user['account_lastname'] . ' ' : '') . $user['account_firstname'];
				$user_list[] = array(
					'id' => $user['account_id'],
					'name' => $name,
					'selected' => $user['account_id'] == $selected ? 1 : 0
				);

				if (!$selected_found)
				{
					$selected_found = $user['account_id'] == $selected ? true : false;
				}
			}
			if ($selected && !$selected_found)
			{
				$user_list[] = array
					(
					'id' => $selected,
					'name' => $GLOBALS['phpgw']->accounts->get($selected)->__toString(),
					'selected' => 1
				);
			}
			return $user_list;
		}
	}
