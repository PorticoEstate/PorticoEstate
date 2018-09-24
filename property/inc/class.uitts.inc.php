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
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uitts extends phpgwapi_uicommon_jquery
	{

		var $public_functions = array
			(
			'report' => true,
			'get_data_report' => true,
			'index' => true,
			'view' => true,
			'add' => true,
			'delete' => true,
			'download' => true,
			'download2' => true,
			'view_file' => true,
			'edit_status' => true,
			'edit_priority' => true,
			'update_data' => true,
			'_print' => true,
			'columns' => true,
			'get_vendor_contract'=> true,
			'get_eco_service'=> true,
			'get_ecodimb'	=> true,
			'get_b_account'	=> true,
			'get_external_project'=> true,
			'get_unspsc_code'=> true,
			'receive_order'	=> true,
			'check_purchase_right'=> true,
			'show_attachment'	=> true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true,
			'query2'	=> true
		);

		/**
		 * @var boolean $_simple use simplified interface
		 */
		protected $simple = false;
		protected $group_candidates = array(-1);
		protected $_show_finnish_date = false;
		protected $_category_acl = false;
		var $part_of_town_id;
		var $status;
		var $filter;
		var $user_filter;

		public function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::helpdesk';
			if ($this->tenant_id = $GLOBALS['phpgw']->session->appsession('tenant_id', 'property'))
			{
				//			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->bo = CreateObject('property.botts', true);
			$this->bocommon = & $this->bo->bocommon;
			$this->cats = & $this->bo->cats;
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = $this->bo->acl_location;
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, PHPGW_ACL_PRIVATE, 'property'); // manage

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->status_id = $this->bo->status_id;
			$this->user_id = $this->bo->user_id;
			$this->reported_by = $this->bo->reported_by;
			$this->cat_id = $this->bo->cat_id;
			$this->vendor_id = $this->bo->vendor_id;
			$this->district_id = $this->bo->district_id;
			$this->part_of_town_id = $this->bo->part_of_town_id;
			$this->allrows = $this->bo->allrows;
			$this->start_date = $this->bo->start_date;
			$this->end_date = $this->bo->end_date;
			$this->location_code = $this->bo->location_code;
			$this->p_num = $this->bo->p_num;
			$this->simple = $this->bo->simple;
			$this->group_candidates = $this->bo->group_candidates;
			$this->show_finnish_date = $this->bo->show_finnish_date;

			$this->_category_acl = isset($this->bo->config->config_data['acl_at_tts_category']) ? $this->bo->config->config_data['acl_at_tts_category'] : false;
		}

		function get_params()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$sort = phpgw::get_var('sort');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => is_array($order) ? $columns[$order[0]['column']]['data'] : $order,
				'sort' => is_array($order) ? $order[0]['dir'] : $sort,
				'dir' => is_array($order) ? $order[0]['dir'] : $sort,
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'status_id' => $this->bo->status_id,
				'user_id' => $this->bo->user_id,
				'reported_by' => $this->bo->reported_by,
				'cat_id' => $this->bo->cat_id,
				'vendor_id' => $this->bo->vendor_id,
				'district_id' => $this->bo->district_id,
				'part_of_town_id' => $this->bo->part_of_town_id,
				//'allrows' => $this->bo->allrows,
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
				'menuaction' => 'property.uitts.view',
			);

			array_walk($result_data['results'], array($this, '_add_links'), $link_data);
//			_debug_array($result_data);
			return $this->jquery_results($result_data);
		}


		function query2(  )
		{
			$num_rows = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) ? (int)$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;
			$_REQUEST['start'] = phpgw::get_var('startIndex');
			$_REQUEST['results'] = $num_rows;

			$values = $this->query();

			return array(
				'ResultSet' => array(
					"totalResultsAvailable" => $values['recordsTotal'],
					"totalRecords" => $values['recordsTotal'],
					"Result" => $values['data'],
					'recordsReturned' => count( $values['data']),
					'pageSize' => $num_rows,
					'startIndex' => $this->start,
					'sortKey' => $this->order,
					'sortDir' => $this->sort,
				)
			);
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

			$ticket = $this->bo->mail_ticket($id, $fields_updated = true, $receipt = array(), $location_code = '', $get_message = true);
			$lang_print = lang('print');

			$html = <<<HTML

			<!DOCTYPE html>
			<html>
				<head>
					<title>{$ticket['subject']}</title>
					<link href="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/pure/css/pure-min.css" type="text/css" rel="StyleSheet">
				</head>
				<body>
					<script type="text/javascript">
							document.write("<form><input type=button "
							+"value=\"{$lang_print}\" onClick=\"window.print();\"></form>");
					</script>
					<H2>{$ticket['subject']}</H2>
					{$ticket['body']}
				</body>
			</html>
HTML;

			echo $html;
		}

		function show_attachment(  )
		{
			if (!$this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property') && !$this->acl->check('.project', PHPGW_ACL_ADD, 'property'))
			{
				phpgw::no_access();
			}
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$file_name = urldecode(phpgw::get_var('file_name'));
			$key = phpgw::get_var('key');
			$invoice_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$directory_attachment = rtrim($invoice_config->config_data['import']['local_path'], '/') . '/attachment/' . $key;

			$file = "$directory_attachment/$file_name";

			if (file_exists($file))
			{
				$size = filesize($file);
				$content = file_get_contents($file);

				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($file_name, '', $size);
				echo $content;
			}

		}

		function download2()
		{
			if (!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => '.ticket.external'));
			}

			$this->download($external = true);
		}

		function download( $external = '' )
		{
			$params = $this->get_params();
			$params['start'] = 0;
			$params['results'] = -1;
			$params['start_date'] = urldecode($this->start_date);
			$params['end_date'] = urldecode($this->end_date);
			$params['download'] = true;
			$params['allrows'] = true;
			$params['external'] = $external;
//			_debug_array($params); die();
			$list = $this->bo->read($params);

			$custom_status = $this->bo->get_custom_status();

			$status = array();
			$status['O'] = isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'] ? $this->bo->config->config_data['tts_lang_open'] : lang('Open');
			$status['X'] = lang('Closed');
			foreach ($custom_status as $custom)
			{
				$status["C{$custom['id']}"] = $custom['name'];
			}

			foreach ($list as &$entry)
			{
				$entry['status'] = $status[$entry['status']];

				if (isset($entry['child_date']) AND is_array($entry['child_date']))
				{
					$j = 0;
					foreach ($entry['child_date'] as $date)
					{
						if ($date['date_info'][0]['descr'])
						{
							$entry["date_{$j}"] = $date['date_info'][0]['entry_date'];
							$name_temp["date_{$j}"] = true;
							$descr_temp["date_{$j}"] = $date['date_info'][0]['descr'];
						}
						$j++;
					}
					unset($entry['child_date']);
				}
			}

			$name = array();
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

			if ($this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property'))
			{
				$name[] = 'order_id';
				$name[] = 'vendor';
			}

			if ($this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property'))
			{
				$name[] = 'estimate';
				$name[] = 'actual_cost';
				$name[] = 'difference';
			}

			$uicols_related = $this->bo->uicols_related;

			foreach ($uicols_related as $related)
			{
				//					$name[] = $related;
			}

			$descr = array();
			foreach ($name as $_entry)
			{
				//				$descr[] = str_replace('_', ' ', $_entry);
				$descr[] = lang(str_replace('_', ' ', $_entry));
			}

			foreach ($name_temp as $_key => $_name)
			{
				array_push($name, $_key);
			}


			foreach ($descr_temp as $_key => $_name)
			{
				array_push($descr, $_name);
			}

			$name[] = 'finnish_date';
			$name[] = 'delay';

			array_push($descr, lang('finnish date'), lang('delay'));


			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : array();

			foreach ($custom_cols as $col)
			{
				if(!in_array($col, $name))
				{
					$name[] = $col;
					$descr[] = lang(str_replace('_', ' ', $col));
				}
			}

			$this->bocommon->download($list, $name, $descr);
		}

		function edit_status()
		{
			if (!$this->acl_edit)
			{
				return lang('sorry - insufficient rights');
			}

			$new_status = phpgw::get_var('new_status', 'string', 'GET');
			$id = phpgw::get_var('id', 'int');

			$ticket = $this->bo->read_single($id);

			if ($ticket['order_id'] && abs($ticket['actual_cost']) == 0)
			{
				$sogeneric = CreateObject('property.sogeneric');
				$sogeneric->get_location_info('ticket_status', false);
				$status_data = $sogeneric->read_single(array('id' => (int)ltrim($new_status, 'C')), array());

				if ($status_data['actual_cost'])
				{
					return "id " . $id . " " . lang('actual cost') . ': ' . lang('Missing value');
				}
			}

			$this->bo->update_status(array('status' => $new_status), $id);
			if ((isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification']) || (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'] == 1 && $this->bo->fields_updated
				)
			)
			{
				$this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
			}

			if ($this->bo->fields_updated)
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
			if (!$this->acl_edit)
			{
				return lang('sorry - insufficient rights');
			}

			$new_priority = phpgw::get_var('new_priority', 'int');
			$id = phpgw::get_var('id', 'int');

//			$ticket = $this->bo->read_single($id);

			$receipt = $this->bo->update_priority(array('priority' => $new_priority), $id);
			if ((isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification']) || (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'] == 1 && $this->bo->fields_updated
				)
			)
			{
				$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt);
			}
			return "id {$id} " . lang('priority has been changed');
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				return lang('sorry - insufficient rights');
			}

			$id = phpgw::get_var('id', 'int');
			if ($this->bo->delete($id))
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
			
			$options['base_dir'] = 'fmticket/'.$id;
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/property/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => 'property.uitts.handle_multi_upload_file', 'id' => $id)));
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
			
			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.handle_multi_upload_file', 'id' => $id));

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

			$values = phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property', 'ticket_columns', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uitts.columns',
			);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list' => $this->bo->column_list($selected),
				'function_msg' => $function_msg,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_columns' => lang('columns'),
				'lang_none' => lang('None'),
				'lang_save' => lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('columns' => $data));
		}

		private function _get_fields()
		{
			$this->bo->get_origin_entity_type();
			$uicols_related = $this->bo->uicols_related;

			$uicols = array();

			$uicols['name'][] = 'id';
			$uicols['descr'][] = lang('id');
			$uicols['name'][] = 'priority';
			$uicols['descr'][] = lang('priority');
			$uicols['name'][] = 'subject';
			$uicols['descr'][] = lang('subject');

			$location_types = execMethod('property.soadmin_location.select_location_type');
//			$level_assigned = isset($this->bo->config->config_data['list_location_level']) && $this->bo->config->config_data['list_location_level'] ? $this->bo->config->config_data['list_location_level'] : array();

			foreach ($location_types as $dummy => $level)
			{
		//		if (in_array($level['id'], $level_assigned))
				{
					$uicols['name'][] = "loc{$level['id']}_name";
					$uicols['descr'][] = $level['name'];
				}
				break;//first one only...
			}


			$uicols['name'][] = 'entry_date';
			$uicols['descr'][] = lang('entry date');

			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : array();
			$columns = $this->bo->get_columns();

			foreach ($custom_cols as $col)
			{
				$uicols['name'][] = $col;
				$uicols['descr'][] = $columns[$col]['name'];
			}

			$uicols['name'][] = 'link_view';
			$uicols['descr'][] = lang('link view');
			$uicols['name'][] = 'lang_view_statustext';
			$uicols['descr'][] = lang('lang view statustext');
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
			$order_read = $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property');

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

			$values_combo_box[1] = $this->bocommon->select_district_list('filter', $this->district_id);
			$default_value = array('id' => '', 'name' => lang('no district'));
			array_unshift($values_combo_box[1], $default_value);
			$link = self::link(array(
					'menuaction' => 'property.uilocation.get_part_of_town',
					'district_id' => $this->district_id,
					'part_of_town_id' => $this->part_of_town_id,
					'phpgw_return_as' => 'json'
			));

			$code = '
				var link = "' . $link . '";
				var data = {"district_id": $(this).val()};
				execute_ajax(link,
					function(result){
						var $el = $("#part_of_town_id");
						$el.empty();
						$.each(result, function(key, value) {
						  $el.append($("<option></option>").attr("value", value.id).text(value.name));
						});
					}, data, "GET", "json"
				);
				';

			$combos[] = array('type' => 'filter',
				'name' => 'district_id',
				'extra' => $code,
				'text' => lang('district'),
				'list' => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bocommon->select_part_of_town('filter', $this->part_of_town_id, $this->district_id);
			$default_value = array('id' => '', 'name' => lang('no part of town'));
			array_unshift($values_combo_box[2], $default_value);
			$combos[] = array('type' => 'filter',
				'name' => 'part_of_town_id',
				'extra' => '',
				'text' => lang('part of town'),
				'list' => $values_combo_box[2]
			);

			$values_combo_box[5] = array(); //reported by

			if(!$this->simple)
			{
				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format' => 'filter',
					'selected' => $this->cat_id, 'globals' => true, 'use_acl' => $this->_category_acl));
				$default_value = array('cat_id' => '', 'name' => lang('no category'));
				array_unshift($values_combo_box[0]['cat_list'], $default_value);

				$_categories = array();
				foreach ($values_combo_box[0]['cat_list'] as $_category)
				{
					$_categories[] = array('id' => $_category['cat_id'], 'name' => $_category['name']);
				}

				$combos[] = array('type' => 'filter',
					'name' => 'cat_id',
					'extra' => '',
					'text' => lang('category'),
					'list' => $_categories
				);

				$values_combo_box[4] = $this->_get_user_list($this->user_id);

				$filter_tts_assigned_to_me = $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_assigned_to_me'];

				array_unshift($values_combo_box[4], array(
					'id' => -1 * $GLOBALS['phpgw_info']['user']['account_id'],
					'name' => lang('my assigned tickets'),
					'selected'	=> ((int)$this->user_id < 0  || (int)$filter_tts_assigned_to_me == 1) ? 1 : 0));

				array_unshift($values_combo_box[4], array('id' => '', 'name' => lang('assigned to')));
				$combos[] = array('type' => 'filter',
					'name' => 'user_id',
					'extra' => '',
					'text' => lang('assigned to'),
					'list' => $values_combo_box[4]
				);

			}

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

			if($order_read)
			{
				$combos[] = array('type' => 'filter',
					'name' => 'vendor_id',
					'extra' => '',
					'text' => lang('vendor'),
					'list' => $this->bo->get_vendors($this->vendor_id)
				);
				$combos[] = array('type' => 'filter',
					'name' => 'ecodimb',
					'extra' => '',
					'text' => lang('dimb'),
					'list' => $this->bo->get_ecodimb($this->ecodimb)
				);

				$combos[] = array('type' => 'filter',
					'name' => 'b_account',
					'extra' => '',
					'text' => lang('budget account'),
					'list' => $this->bo->get_b_account($this->b_account)
				);

				$_filter_buildingpart = array();
				$filter_buildingpart = isset($this->bo->config->config_data['filter_buildingpart']) ? $this->bo->config->config_data['filter_buildingpart'] : array();

				if ($filter_key = array_search('.b_account', $filter_buildingpart))
				{
					$_filter_buildingpart = array("filter_{$filter_key}" => 1);
				}

				$buildingpart_list = $this->bocommon->select_category_list(array('type' => 'building_part',
					'selected' => $this->building_part, 'order' => 'id', 'id_in_name' => 'num',
					'filter' => $_filter_buildingpart));

				array_unshift($buildingpart_list, array('id' => '', 'name' => lang('select')));

				$combos[] = array('type' => 'filter',
					'name' => 'building_part',
					'extra' => '',
					'text' => lang('building part'),
					'list' => $buildingpart_list
				);

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list'] == 1)
				{
					$combos[] = array('type' => 'filter',
						'name' => 'branch_id',
						'extra' => '',
						'text' => lang('branch'),
						'list' => $this->bo->get_branch($this->branch_id)
					);
				}

				$combos[] = array('type' => 'filter',
					'name' => 'order_dim1',
					'extra' => '',
					'text' => lang('order_dim1'),
					'list' => $this->bo->get_order_dim1($this->order_dim1)
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

			$check_date_type =	array('type' => 'filter',
				'name' => 'check_date_type',
				'extra' => '',
				'text' => lang('check date type'),
				'list' => array(
					array(
						'id'	=> 1,
						'name'	=> lang('modified date')
					),
					array(
						'id'	=> 2,
						'name'	=> lang('entry date')
					)
				)
			);

			if($order_read)
			{
				$check_date_type['list'][] = array(
					'id'	=> 3,
					'name'	=> lang('no date')
				);
			}

			$combos[] = $check_date_type;

			return $combos;
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			self::add_javascript('property', 'portico', 'tts.index.js');

			$start_date = urldecode($this->start_date);
			$end_date = urldecode($this->end_date);

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');

			$appname = lang('helpdesk');
			$function_msg = lang('list ticket');

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uitts.index',
					//	'start_date' => $start_date,
					//	'end_date' => $end_date,
						'phpgw_return_as' => 'json')),
					'download' => self::link(array('menuaction' => 'property.uitts.download',
						'export' => true, 'allrows' => true)),
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uitts.columns'}, {closeAction:'reload', height: 500})"),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'property.uitts.add')),
					'editor_action' => self::link(array('menuaction' => 'property.uitts.edit_survey_title')),
					'field' => $this->_get_fields(),
					'query' => phpgw::get_var('query')
				)
			);

			$filters = $this->_get_filters();

			foreach ($filters as $filter)
			{
				$data['form']['toolbar']['item'][] = $filter;
			}

			$data['form']['toolbar']['item'][] = array(
								'type' => 'date-picker',
								'id' => 'start_date',
								'name' => 'start_date',
								'value' => $start_date,
								'text' => lang('from')
							);
			$data['form']['toolbar']['item'][] = array(
								'type' => 'date-picker',
								'id' => 'end_date',
								'name' => 'end_date',
								'value' => $end_date,
								'text' => lang('to')
							);

			$parameters = array
				(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);
			$parameters_location = array(
				'parameter' => array(
					array(
						'name' => 'location_code',
						'source' => 'location_code'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view_survey',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uitts.view'
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
					'menuaction' => 'property.uitts._print',
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

			foreach ($jasper as $report)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('open JasperReport %1 in new window', $report['title']),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.view',
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
						'menuaction' => 'property.uitts.delete'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if(!$this->simple)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'docs',
					'statustext' => lang('documents'),
					'text' => lang('documents'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uidocument.list_doc',
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters_location)
				);
			}

			if (!$this->simple && isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'] == 'yes' && $this->acl_edit)
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
							'menuaction' => 'property.uitts.edit_status',
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
							'menuaction' => 'property.uitts.edit_priority',
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

			if (count($data['datatable']['actions']) < 10)
			{
				$data['datatable']['group_buttons'] = false;
			}
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}
		
		function report()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('chart');
			phpgwapi_jquery::load_widget('print');
				
			$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), '01', date("Y")), $this->dateFormat);
			$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $this->dateFormat);
			
			$appname = lang('helpdesk');
			$function_msg = lang('Report');

			self::add_javascript('property', 'portico', 'tts.report.js');

			$data = array(
				'start_date' => $start_date,
				'end_date' => $end_date,
				'image_loader' => $GLOBALS['phpgw']->common->image('property', 'ajax-loader', '.gif', false)
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			self::render_template_xsl(array('tts_report'), $data);
		}
		
		function get_data_report()
		{
			$start_date = phpgw::get_var('start_date', 'date');
			$end_date = phpgw::get_var('end_date', 'date');
			$type = phpgw::get_var('type');
			
			$params['start_date'] = $start_date;
			$params['end_date'] = $end_date;
			$params['results'] = -1;
			$params['type'] = $type;

			$values = $this->bo->get_data_report($params);
			
			$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
	
			$list_categories = $this->cats->formatted_xslt_list(array('format' => 'filter',
				'selected' => $this->cat_id, 'globals' => true, 'use_acl' => $this->_category_acl));
			
			if ($type == 1)
			{
				$_categories = array();
				foreach ($list_categories['cat_list'] as $_category)
				{
					$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
					$_categories[$_category['cat_id']] = array('label'=>$_category['name'], 'count' => 0, 
						'backgroundColor' => $color, 'hoverBackgroundColor' => $color);
				}

				foreach ($values as $item) 
				{
					if ($_categories[$item['cat_id']]) {
						$_categories[$item['cat_id']]['count'] = (int)$item['count_category'];
					}
				}

				return $_categories;
			} 
			else {
				
				$list_status = $this->bo->filter(array('format' => '', 'filter' => $this->status_id, 'default' => 'O'));
				if (isset($this->bo->config->config_data['tts_lang_open']) && $this->bo->config->config_data['tts_lang_open'])
				{
					array_unshift($list_status, array('id' => 'O2', 'name' => $this->bo->config->config_data['tts_lang_open']));
				}

				$_status = array();
				foreach ($list_status as $_item)
				{
					if ($_item['id'] == 'all' || $_item['id'] == 'X')
					{
						continue;
					}
					$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
					$_status[$_item['id']] = array('label'=>$_item['name'], 'count' => 0, 
						'backgroundColor' => $color, 'hoverBackgroundColor' => $color);					
				}

				foreach ($values as $item) 
				{
					if ($_status[$item['status']]) {
						$_status[$item['status']]['count']  = (int)$item['count_status'];
					}
				}

				return $_status;
			}
		}
		
		function add()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}
			if ($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uitts.add2'));
			}

			$bolocation = CreateObject('property.bolocation');

			$values = phpgw::get_var('values');
			$values['contact_id'] = phpgw::get_var('contact', 'int', 'POST');
			if ((isset($values['cancel']) && $values['cancel']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uitts.index'));
			}

			$values_attribute = phpgw::get_var('values_attribute');

			//------------------- start ticket from other location
			$bypass = phpgw::get_var('bypass', 'bool');
//			if(isset($_POST) && $_POST && isset($bypass) && $bypass)
			if ($bypass)
			{
				$boadmin_entity = CreateObject('property.boadmin_entity');
				$location_code = phpgw::get_var('location_code');
				$values['descr'] = phpgw::get_var('descr');
				$p_entity_id = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id = phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
				$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');

				$origin = phpgw::get_var('origin');
				$origin_id = phpgw::get_var('origin_id', 'int');

				if ($p_entity_id && $p_cat_id)
				{
					$entity_category = $boadmin_entity->read_single_category($p_entity_id, $p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if ($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code, array('tenant_id' => $tenant_id,
						'p_num' => $p_num, 'view' => true));
					$values['street_name'] = $values['location_data']['street_name'];
					$values['street_number'] = $values['location_data']['street_number'];
				}
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
			if (!empty($values['save']) || !empty($values['apply']))
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');

				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_values' . $this->acl_location, 'property');

				if (isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j = 0; $j < count($insert_record_entity); $j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);

				if (!$values['subject'] && isset($this->bo->config->config_data['tts_mandatory_title']) && $this->bo->config->config_data['tts_mandatory_title'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a title !'));
				}

				if (!$values['cat_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a category !'));
				}

				if (!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][] = array('msg' => lang('Please give som details !'));
				}

				if ((!isset($values['location']['loc1']) || !$values['location']['loc1']) && (!isset($values['extra']['p_num']) || !$values['extra']['p_num']))
				{
					$receipt['error'][] = array('msg' => lang('Please select a location - or an entity!'));
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

				if(empty($values['group_id']))
				{
					$values['group_id'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'] : '');
				}

				if (!$values['assignedto'] && !$values['group_id'])
				{
					$_responsible = execMethod('property.boresponsible.get_responsible', $values);
					if (!$_responsible)
					{
						if(!$values['assignedto'] = $GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'])
						{

							$receipt['error'][] = array('msg' => lang('Please select a person or a group to handle the ticket !'));
						}
					}
					else
					{
						if ($GLOBALS['phpgw']->accounts->get($_responsible)->type == phpgwapi_account::TYPE_USER)
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
					$values['file_name'] = @str_replace(array(' ', '..'), array('_', '.'), $_FILES['file']['name']);

					if ($values['file_name'] && $receipt['id'])
					{
						$bofiles = CreateObject('property.bofiles');
						$to_file = $bofiles->fakebase . '/fmticket/' . $receipt['id'] . '/' . $values['file_name'];

						if ($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][] = array('msg' => lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("fmticket/{$receipt['id']}");
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
					//--------------end files
					$GLOBALS['phpgw']->session->appsession('receipt', 'property', $receipt);
					//	$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');

					if ((isset($values['save']) && $values['save']))
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uitts.index'));
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uitts.view',
							'id' => $receipt['id'], 'tab' => 'general'));
					}
				}
				else
				{
					if (isset($values['location']) && $values['location'])
					{
						$location_code = implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
					}
					if (isset($values['extra']['p_num']) && $values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num'] = $values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id'] = $values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id'] = $values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name'] = phpgw::get_var('entity_cat_name_' . $values['extra']['p_entity_id'], 'string', 'POST');
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

			$location_data = $bolocation->initiate_ui_location(array(
				'values' => isset($values['location_data']) ? $values['location_data'] : '',
				'type_id' => -1, // calculated from location_types
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => true,
				'required_level' => 1,
				'lookup_type' => 'form2',
				'lookup_entity' => $this->bocommon->get_lookup_entity('ticket'),
				'entity_data' => (isset($values['p']) ? $values['p'] : '')
			));

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_me_as_contact']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_me_as_contact'] == 1)
			{
				$ticket['contact_id'] = $GLOBALS['phpgw']->accounts->get($this->account)->person_id;
			}
			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));

			$link_data = array
				(
				'menuaction' => 'property.uitts.add'
			);

			if (!isset($values['assignedto']))
			{
				$values['assignedto'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'] : '');
			}
			if (!isset($values['group_id']))
			{
				$values['group_id'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'] : '');
			}

			if (!isset($values['cat_id']))
			{
				$this->cat_id = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category'] : '');
			}
			else
			{
				$this->cat_id = $values['cat_id'];
			}

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');


			if (!$this->simple && $this->show_finnish_date)
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

			$fmttssimple_categories = isset($this->bo->config->config_data['fmttssimple_categories']) ? $this->bo->config->config_data['fmttssimple_categories'] : array();



			$cat_select = $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]',	'use_acl' => $this->_category_acl, 'required' => true, 'class' => 'pure-input-1-2'));

			$_cat_list = array();
			if($this->simple && isset($fmttssimple_categories) && $fmttssimple_categories[1])
			{
				foreach ($cat_select['cat_list'] as $entry)
				{
					if(in_array($entry['cat_id'], array_values($fmttssimple_categories)))
					{
						$_cat_list[] = $entry;
					}
				}
				$cat_select['cat_list'] = $_cat_list;
			}

			$data = array
				(
				'my_groups' => json_encode($my_groups),
				'custom_attributes' => array('attributes' => $values['attributes']),
				'lookup_functions' => isset($values['lookup_functions']) ? $values['lookup_functions'] : '',
				'contact_data' => $contact_data,
				'simple' => $this->simple,
				'show_finnish_date' => $this->show_finnish_date,
				'value_origin' => isset($values['origin_data']) ? $values['origin_data'] : '',
				'value_origin_type' => (isset($origin) ? $origin : ''),
				'value_origin_id' => (isset($origin_id) ? $origin_id : ''),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data2' => $location_data,
				'lang_no_user' => lang('Select user'),
				'lang_user_statustext' => lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name' => 'values[assignedto]',
//				'user_list' => $this->bocommon->get_user_list_right2('select', 4, $values['assignedto'], $this->acl_location),
				'user_list' => $this->_get_user_list($values['assignedto']),
				'disable_userassign_on_add' => isset($this->bo->config->config_data['tts_disable_userassign_on_add']) ? $this->bo->config->config_data['tts_disable_userassign_on_add'] : '',
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
				'pref_send_mail' => (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'] : ''),
				'fileupload' => (isset($this->bo->config->config_data['fmttsfileupload']) ? $this->bo->config->config_data['fmttsfileupload'] : ''),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			//_debug_array($data);
			$appname = lang('helpdesk');
			$function_msg = lang('add ticket');

			self::add_javascript('property', 'portico', 'tts.add.js');
			phpgwapi_jquery::formvalidator_generate(array('date', 'security','file'));
			$this->_insert_custom_js();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			self::render_template_xsl( array('tts', 'files', 'attributes_form'), $data, $xsl_rootdir = '' , 'add');
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

		function get_files()
		{
			$id = phpgw::get_var('id', 'int');

			if (!$this->acl_read)
			{
				return;
			}

			$link_file_data = array
				(
				'menuaction' => 'property.uitts.view_file',
			);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);
			$values = $this->bo->read_single($id);

			$file_attachments = isset($values['file_attachments']) && is_array($values['file_attachments']) ? $values['file_attachments'] : array();

			$content_files = array();

			foreach ($values['files'] as $_entry)
			{
				$_checked = '';
				if (in_array($_entry['file_id'], $file_attachments))
				{
					$_checked = 'checked="checked"';
				}

				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
					'attach_file' => '<input type="checkbox"' .$_checked . ' name="values[file_attach][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to attach file') . '">'
				);
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
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$id = phpgw::get_var('id', 'int');

			$add_external_communication = phpgw::get_var('external_communication', 'int');

			if ($add_external_communication)
			{
				self::redirect(array('menuaction' => 'property.uiexternal_communication.edit','ticket_id' => $id,
					'type_id' => $add_external_communication ));
			}

			if ($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uitts.view2',
					'id' => $id));
			}

			$add_relation = phpgw::get_var('add_request');
			if($add_relation)
			{
				$receipt = $this->bo->add_relation($add_relation, $id);
			}

			$bolocation = CreateObject('property.bolocation');

			$values = phpgw::get_var('values');
			$values['contact_id'] = phpgw::get_var('contact', 'int', 'POST');
//			$values['ecodimb'] = phpgw::get_var('ecodimb');
			$values['vendor_id'] = phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name'] = phpgw::get_var('vendor_name', 'string', 'POST');
//			$values['b_account_id'] = phpgw::get_var('b_account_id', 'int', 'POST');
//			$values['b_account_name'] = phpgw::get_var('b_account_name', 'string', 'POST');

			$values_attribute = phpgw::get_var('values_attribute');

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt', 'property');
			$GLOBALS['phpgw']->session->appsession('receipt', 'property', '');
			if (!$receipt)
			{
				$receipt = array();
			}

			$historylog = CreateObject('property.historylog', 'tts');

			$order_read = $this->acl->check('.ticket.order', PHPGW_ACL_READ, 'property');
			$order_add = $this->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property');
			$order_edit = $this->acl->check('.ticket.order', PHPGW_ACL_EDIT, 'property');

			$access_order = false;
			if ($order_add || $order_edit)
			{
				$access_order = true;
			}

			if (!empty($values['save']) || !empty($values['send_order']))
			{
				if (!$this->acl_edit)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
						'perm' => 4, 'acl_location' => $this->acl_location));
				}

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_values' . $this->acl_location, 'property');

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


				if ($access_order)
				{
					//test for budget
					$_ticket = $this->bo->read_single($id);
					if (!$_ticket['budget'] && ((isset($values['order_id']) && $values['order_id']) && (!isset($values['budget']) || !$values['budget'])))
					{
						$receipt['error'][] = array('msg' => lang('budget') . ': ' . lang('Missing value'));
					}
					unset($_ticket);

					$sogeneric = CreateObject('property.sogeneric');
					$sogeneric->get_location_info('ticket_status', false);
					$status_data = $sogeneric->read_single(array('id' => (int)ltrim($values['status'], 'C')), array());

					if (isset($status_data['actual_cost']) && $status_data['actual_cost'])
					{
						if (!$values['actual_cost'] || !abs($values['actual_cost']) > 0)
						{
							$receipt['error'][] = array('msg' => lang('actual cost') . ': ' . lang('Missing value'));
						}
						else if (!is_numeric($values['actual_cost']))
						{
							$receipt['error'][] = array('msg' => lang('budget') . ': ' . lang('Please enter a numeric value'));
						}
					}
				}

				if (isset($values['takeover']) && $values['takeover'])
				{
					$values['assignedto'] = $this->account;
				}

				if (!empty($values['approval']) && !empty($this->bo->config->config_data['ticket_approval_status']))
				{
					$values['status'] = $this->bo->config->config_data['ticket_approval_status'];
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
				$receipt = $this->bo->update_ticket($values, $id, $receipt, $values_attribute);

				if ((isset($values['send_mail']) && $values['send_mail']) || (isset($this->bo->config->config_data['mailnotification']) && $this->bo->config->config_data['mailnotification'] && $this->bo->fields_updated
					) || (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_notify_me'] == 1 && $this->bo->fields_updated
					)
				)
				{
					$receipt = $this->bo->mail_ticket($id, $this->bo->fields_updated, $receipt, '', false, isset($values['send_mail']) && $values['send_mail'] ? true : false);
				}

				//--------- files
				$bofiles = CreateObject('property.bofiles');
				if (isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/fmticket/{$id}/", $values);
				}

//				$values['file_name'] = str_replace(' ', '_', $_FILES['file']['name']);
				$values['file_name'] = str_replace(array(' ', '..'), array('_', '.'), $_FILES['file']['name']);

				if ($values['file_name'])
				{
					$to_file = $bofiles->fakebase . '/fmticket/' . $id . '/' . $values['file_name'];

					if ($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][] = array('msg' => lang('This file already exists !'));
					}
					else
					{
						$bofiles->create_document_dir("fmticket/{$id}");
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

			$order_link = '';
			$add_to_project_link = '';
			$request_link = '';

			if ($GLOBALS['phpgw']->acl->check('.project.request', PHPGW_ACL_ADD, 'property'))
			{
				$request_link_data = array
					(
					'menuaction' => 'property.uirequest.edit',
					'bypass' => true,
					'location_code' => $ticket['location_code'],
					'p_num' => $ticket['p_num'],
					'p_entity_id' => $ticket['p_entity_id'],
					'p_cat_id' => $ticket['p_cat_id'],
					'tenant_id' => $ticket['tenant_id'],
					'origin' => '.ticket',
					'origin_id' => $id
				);

				$request_link = $GLOBALS['phpgw']->link('/index.php', $request_link_data);
			}

			if ($GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'property'))
			{
				$order_link_data = array
					(
					'menuaction' => 'property.uiproject.edit',
					'bypass' => true,
					'location_code' => $ticket['location_code'],
					'p_num' => $ticket['p_num'],
					'p_entity_id' => $ticket['p_entity_id'],
					'p_cat_id' => $ticket['p_cat_id'],
					'tenant_id' => $ticket['tenant_id'],
					'origin' => '.ticket',
					'origin_id' => $id
				);

				$add_to_project_link_data = array
					(
					'menuaction' => 'property.uiproject.index',
					'from' => 'workorder',
					'lookup' => true,
					'query' => isset($ticket['location_data']['loc1']) ? $ticket['location_data']['loc1'] : '',
					//			'p_num'				=> $ticket['p_num'],
					//			'p_entity_id'		=> $ticket['p_entity_id'],
					//			'p_cat_id'			=> $ticket['p_cat_id'],
					'tenant_id' => $ticket['tenant_id'],
					'origin' => '.ticket',
					'origin_id' => $id
				);

				$order_link = $GLOBALS['phpgw']->link('/index.php', $order_link_data);
				$add_to_project_link = $GLOBALS['phpgw']->link('/index.php', $add_to_project_link_data);
			}

			$form_link = array
				(
				'menuaction' => 'property.uitts.view',
				'id' => $id
			);


			if ($ticket['origin'] || $ticket['target'] || $this->simple)
			{
				$lookup_type = 'view2';
				$type_id = count(explode('-', $ticket['location_data']['location_code']));
			}
			else
			{
				$lookup_type = 'form2';
				$type_id = -1;
			}

			$location_data = $bolocation->initiate_ui_location(array(
				'values' => $ticket['location_data'],
				'type_id' => $type_id,
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => (isset($ticket['location_data']['tenant_id']) ? $ticket['location_data']['tenant_id'] : ''),
				'lookup_type' => $lookup_type,
				'lookup_entity' => $this->bocommon->get_lookup_entity('ticket'),
				'entity_data' => (isset($ticket['p']) ? $ticket['p'] : '')
			));
			unset($type_id);

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));


			if ($ticket['contact_phone'])
			{
				for ($i = 0; $i < count($location_data['location']); $i++)
				{
					if ($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if ($ticket['cat_id'])
			{
				$this->cat_id = $ticket['cat_id'];
			}

			$start_entity = $this->bocommon->get_start_entity('ticket');

			$link_entity = array();
			if (isset($start_entity) AND is_array($start_entity))
			{
				$i = 0;
				foreach ($start_entity as $entry)
				{
					if ($GLOBALS['phpgw']->acl->check(".entity.{$entry['id']}", PHPGW_ACL_ADD, 'property'))
					{
						$link_entity[$i]['link'] = $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uientity.edit',
							'bypass' => true,
							'location_code' => $ticket['location_code'],
							'entity_id' => $entry['id'],
							'p_num' => $ticket['p_num'],
							'p_entity_id' => $ticket['p_entity_id'],
							'p_cat_id' => $ticket['p_cat_id'],
							'tenant_id' => $ticket['tenant_id'],
							'origin' => '.ticket',
							'origin_id' => $id
						));
						$link_entity[$i]['name'] = $entry['name'];
						$i++;
					}
				}
			}

			//_debug_array($link_entity);

			$link_file_data = array
				(
				'menuaction' => 'property.uitts.view_file',
				'id' => $id
			);

			if (!$this->simple && $this->show_finnish_date)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('values_finnish_date');
			}

			// -------- start order section

			if ($order_read || $access_order)
			{
				$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
					'vendor_id' => $ticket['vendor_id'],
					'vendor_name' => $ticket['vendor_name'],
					'type' => $order_read && !$access_order ? 'view' : 'form'
				));
			}

			if ($access_order)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('order_deadline');
				$GLOBALS['phpgw']->jqcal->add_listener('order_deadline2');

				$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array
					(
					'b_account_id' => $ticket['b_account_id'] ? $ticket['b_account_id'] : $ticket['b_account_id'],
					'b_account_name' => $ticket['b_account_name'],
					'disabled' => false
					)
				);

				$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
					(
					'ecodimb' => $ticket['ecodimb'] ? $ticket['ecodimb'] : $ticket['ecodimb'],
					'ecodimb_descr' => $ticket['ecodimb_descr'],
					'disabled' => false
					)
				);

				// approval
				$supervisor_id = 0;

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
				{
					$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
				}

				$need_approval = isset($this->bo->config->config_data['workorder_approval']) ? $this->bo->config->config_data['workorder_approval'] : '';


				//temporary
		//		$test_user = $this->acl->check('.ticket.order', 16, 'property');
		//		$need_approval = $need_approval && $test_user ? true : false;

				// approval
			}

			$preview_html = phpgw::get_var('preview_html', 'bool');
			$preview_pdf = phpgw::get_var('preview_pdf', 'bool');

			if ($preview_pdf)
			{
				$this->_pdf_order($id, true);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if ($preview_html)
			{
				$this->_html_order($id, true);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$_budget_amount = $this->_get_budget_amount($id);
			$sosubstitute = CreateObject('property.sosubstitute');

			if (isset($values['approval']) && $values['approval'] && $this->bo->config->config_data['workorder_approval'])
			{
				$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
				$coordinator_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

				$subject = lang(Approval) . ": " . $ticket['order_id'];
				$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view',
						'id' => $id), false, true) . '">' . lang('Workorder %1 needs approval', $ticket['order_id']) . '</a>';

				if (empty($GLOBALS['phpgw_info']['server']['smtp_server']))
				{
					$receipt['error'][] = array('msg' => lang('SMTP server is not set! (admin section)'));
				}

				if (!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}

				$action_params = array(
					'appname' => 'property',
					'location' => '.ticket',
					'id' => $id,
					'responsible' => '',
					'responsible_type' => 'user',
					'action' => 'approval',
					'remark' => '',
					'deadline' => ''
				);
				$bcc = '';//$coordinator_email;

				foreach ($values['approval'] as $_account_id => $_address)
				{
					$substitute = $sosubstitute->get_substitute($_account_id);

					if($substitute)
					{
						$_account_id = $substitute;
					}

					$prefs = $this->bocommon->create_preferences('property', $_account_id);
					if (!empty($prefs['email']))
					{
						$_address = $prefs['email'];
					}
					else
					{
						$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
						$_address = $GLOBALS['phpgw']->accounts->id2lid($_account_id) . "@{$email_domain}";
					}

					$action_params['responsible'] = $_account_id;
					try
					{
						$historylog->add('AR', $id, $GLOBALS['phpgw']->accounts->get($_account_id)->__toString() . "::{$_budget_amount}");
						execMethod('property.sopending_action.set_pending_action', $action_params);
						$rcpt = $GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html');
						if ($rcpt)
						{
							phpgwapi_cache::message_set(lang('%1 is notified', $_address),'message');
						}
					}
					catch (Exception $exc)
					{
						phpgwapi_cache::message_set($exc->getMessage(),'error');
					}
				}
			
			}
	
			if (!empty($values['do_approve']) && is_array($values['do_approve']))
			{
				$action_params = array(
					'appname' => 'property',
					'location' => '.ticket',
					'id' => $id,
					'responsible' => '',
					'responsible_type' => 'user',
					'action' => 'approval',
					'remark' => '',
					'deadline' => ''
				);

//				foreach ($values['do_approve'] as $_account_id => $_dummy)
//				{
//					$action_params['responsible'] = $_account_id;
//					if(!execMethod('property.sopending_action.get_pending_action', $action_params))
//					{
//						execMethod('property.sopending_action.set_pending_action', $action_params);
//					}
//					execMethod('property.sopending_action.close_pending_action', $action_params);
//					$historylog->add('AA', $id, $GLOBALS['phpgw']->accounts->get($_account_id)->__toString() . "::{$_budget_amount}");
//				}

////
				foreach ($values['do_approve'] as $_account_id => $_dummy)
				{
					$users_for_substitute = $sosubstitute->get_users_for_substitute($_account_id);

					$approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

					$take_responsibility_for = array($_account_id);
					foreach ($approvals as $approval)
					{
						if(in_array($approval['responsible'],$users_for_substitute))
						{
							$take_responsibility_for[] = $approval['responsible'];
						}
					}
					foreach ($take_responsibility_for as $__account_id)
					{
						$action_params['responsible'] = $__account_id;
						if(!execMethod('property.sopending_action.get_pending_action', $action_params))
						{
							execMethod('property.sopending_action.set_pending_action', $action_params);
						}
						execMethod('property.sopending_action.close_pending_action', $action_params);
						$historylog->add('AA', $id, $GLOBALS['phpgw']->accounts->get($__account_id)->__toString() . "::{$_budget_amount}");
					}
					unset($action_params['responsible']);
				}
			}

			// end approval
			// -------- end order section

			if(!empty($values['send_order']))
			{
				$send_order_format = !empty($values['send_order_format']) ? $values['send_order_format'] : 'html';
				$purchase_grant_checked = !empty($values['purchase_grant_checked']) ? true : false;
				$purchase_grant_error = !empty($values['purchase_grant_error']) ? true : false;

				$this->_send_order($ticket, $send_order_format, $purchase_grant_checked, $purchase_grant_error);
			}

			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$notes = array
				(
				array
					(
					'value_id' => '', //not from historytable
					'value_count' => 1,
					'value_date' => $GLOBALS['phpgw']->common->show_date($ticket['timestamp']),
					'value_user' => $ticket['user_name'],
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

			if (isset($values['order_text']) && $ticket['order_id'])
			{
				foreach ($values['order_text'] as $_text)
				{
					$ticket['order_descr'] .= "\n" . $GLOBALS['phpgw']->db->stripslashes($_text);
				}
			}

			$note_def = array
				(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_note', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);

			if ($access_order)
			{
				$note_def[] = array('key' => 'order_text', 'label' => lang('order text'), 'sortable' => false,
					'resizeable' => true, 'formatter' => 'FormatterCenter');
				foreach ($additional_notes as &$note)
				{
					$note['order_text'] = '<input type="checkbox" name="values[order_text][]" value="' . str_replace('"', "'", $note['value_note']) . '" title="' . lang('Check to add text to order') . '">';
				}
			}

			if ($GLOBALS['phpgw_info']['apps']['frontend']['enabled'])
			{
				$note_def[] = array('key' => 'publish_note', 'label' => lang('publish text'),
					'sortable' => false, 'resizeable' => true, 'formatter' => 'FormatterCenter');
				foreach ($additional_notes as &$note)
				{
					$_checked = $note['value_publish'] ? 'checked' : '';
					$note['publish_note'] = "<input type='checkbox' {$_checked}  name='values[publish_note][]' value='{$id}_{$note['value_id']}' title='" . lang('Check to publish text at frontend') . "'>";
				}
			}

			foreach ($additional_notes as &$note)
			{
				$note['value_note'] = preg_replace("/[[:alpha:]]+:\/\/[^<>[:space:]]+[[:alnum:]\/]/","<a href=\"\\0\">\\0</a>", $note['value_note']);
				$note['value_note'] = nl2br($note['value_note']);
			}

			$datatable_def = array();

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $note_def,
				'data' => json_encode($additional_notes),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(0,'asc'))),
				)
			);

			//_debug_Array($additional_notes);die();
			//---datatable settings---------------------------------------------------

			$z = 1;
			foreach ($record_history as &$history_entry)
			{
				$history_entry['sort_key'] = $z++;
				
			}
			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => array(
					array('key' => 'sort_key', 'label' => '#', 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => false,
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


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);


			$file_attachments = isset($ticket['file_attachments']) && is_array($ticket['file_attachments']) ? $ticket['file_attachments'] : array();

			$content_files = array();

			foreach ($ticket['files'] as $_entry)
			{
				$_checked = '';
				if (in_array($_entry['file_id'], $file_attachments))
				{
					$_checked = 'checked="checked"';
				}

				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
					'attach_file' => '<input type="checkbox"' .$_checked . ' name="values[file_attach][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to attach file') . '">'
				);
			}

			$attach_file_def = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
					'resizeable' => true, 'formatter' => 'FormatterCenter'),
			);

			if (isset($ticket['order_id']) && $ticket['order_id'])
			{
				$attach_file_def[] = array('key' => 'attach_file', 'label' => lang('attach file'),
					'sortable' => false, 'resizeable' => true, 'formatter' => 'FormatterCenter');
			}

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


			$content_email = $this->bocommon->get_vendor_email(isset($ticket['vendor_id']) ? $ticket['vendor_id'] : 0);

			if (isset($ticket['mail_recipients']) && is_array($ticket['mail_recipients']))
			{
				$_recipients_found = array();
				foreach ($content_email as &$vendor_email)
				{
					if (in_array($vendor_email['value_email'], $ticket['mail_recipients']))
					{
						$vendor_email['value_select'] = str_replace("type='checkbox'", "type='checkbox' checked='checked'", $vendor_email['value_select']);
						$_recipients_found[] = $vendor_email['value_email'];
					}
				}
				$value_extra_mail_address = implode(',', array_diff($ticket['mail_recipients'], $_recipients_found));
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_3',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'value_email', 'label' => lang('email'),
						'sortable' => true, 'resizeable' => true),
					array('key' => 'value_select', 'label' => lang('select'), 'sortable' => false,
						'resizeable' => true)),
				'data' => json_encode($content_email),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$budgets = $this->bo->get_budgets($id);
			$datatable_def[] = array
				(
				'container' => 'datatable-container_4',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'period', 'label' => lang('period'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'amount', 'label' => lang('amount'), 'sortable' => true, 'resizeable' => true,
						'formatter' => 'JqueryPortico.FormatterAmount2'),
					array('key' => 'remark', 'label' => lang('remark'), 'sortable' => false, 'resizeable' => true)),
				'data' => json_encode($budgets),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			$payments = $this->bo->get_payments($id);
			$datatable_def[] = array
				(
				'container' => 'datatable-container_5',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'period', 'label' => lang('period'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'amount', 'label' => lang('amount'), 'sortable' => true, 'resizeable' => true,
						'formatter' => 'JqueryPortico.FormatterAmount2'),
					array('key' => 'remark', 'label' => lang('remark'), 'sortable' => false, 'resizeable' => true)),
				'data' => json_encode($payments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$notify_info = execMethod('property.notify.get_jquery_table_def', array
				(
				'location_id' => $location_id,
				'location_item_id' => $id,
				'count' => count($datatable_def),
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


			// start invoice
			$invoices = array();
			if(!empty($ticket['order_id']))
			{
				$active_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array(
					'order_id' => $ticket['order_id']));
				$historical_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array(
					'order_id' => $ticket['order_id'],
					'paid' => true));
				$invoices = array_merge($active_invoices, $historical_invoices);
			}

			$link_data_invoice1 = array
				(
				'menuaction' => 'property.uiinvoice.index',
				'user_lid' => 'all'
			);
			$link_data_invoice2 = array
				(
				'menuaction' => 'property.uiinvoice2.index'
			);

			$content_invoice = array();
			$amount = 0;
			$approved_amount = 0;
			foreach ($invoices as $entry)
			{
				$entry['voucher_id'] = $entry['transfer_time'] ? -1 * $entry['voucher_id'] : $entry['voucher_id'];
				if ($entry['voucher_out_id'])
				{
					$voucher_out_id = $entry['voucher_out_id'];
				}
				else
				{
					$voucher_out_id = abs($entry['voucher_id']);
				}

				if ($this->bo->config->config_data['invoicehandler'] == 2)
				{
					if ($entry['voucher_id'] > 0)
					{
						$link_data_invoice2['voucher_id'] = $entry['voucher_id'];
						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice2);
					}
					else
					{
						$link_data_invoice1['voucher_id'] = abs($entry['voucher_id']);
						$link_data_invoice1['paid'] = 'true';
						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice1);
					}
				}
				else
				{
					if ($entry['voucher_id'] > 0)
					{
						$link_data_invoice1['voucher_id'] = $entry['voucher_id'];
						$link_data_invoice1['query'] = $entry['voucher_id'];
						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice1);
					}
					else
					{
						$link_data_invoice1['voucher_id'] = abs($entry['voucher_id']);
						$link_data_invoice1['paid'] = 'true';
						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice1);
					}
				}
				$link_voucher_id = "<a href='" . $url . "'>" . $voucher_out_id . "</a>";

				$content_invoice[] = array
					(
					'external_voucher_id'	=> $entry['external_voucher_id'],
					'voucher_id' => ($_lean) ? $entry['voucher_id'] : $link_voucher_id,
					'voucher_out_id' => $entry['voucher_out_id'],
					'status' => $entry['status'],
					'period' => $entry['period'],
					'periodization' => $entry['periodization'],
					'periodization_start' => $entry['periodization_start'],
					'invoice_id' => $entry['invoice_id'],
					'budget_account' => $entry['budget_account'],
					'dima' => $entry['dima'],
					'dimb' => $entry['dimb'],
					'dimd' => $entry['dimd'],
					'type' => $entry['type'],
					'amount' => $entry['amount'],
					'approved_amount' => $entry['approved_amount'],
					'vendor' => $entry['vendor'],
					'external_project_id' => $entry['project_id'],
					'currency' => $entry['currency'],
					'budget_responsible' => $entry['budget_responsible'],
					'budsjettsigndato' => $entry['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['budsjettsigndato']), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
					'transfer_time' => $entry['transfer_time'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['transfer_time']), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				);

				$amount += $entry['amount'];
				$approved_amount += $entry['approved_amount'];
			}


			if($invoices)
			{
				$invoice_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			}

			foreach ($invoices as $entry)
			{
				$directory_attachment = rtrim($invoice_config->config_data['import']['local_path'], '/') . '/attachment/' .$entry['external_voucher_id'];
				$attachmen_list = array();
				try
				{
					$dir = new DirectoryIterator("$directory_attachment/");
					if (is_object($dir))
					{
						foreach ($dir as $file)
						{
							if ($file->isDot() || !$file->isFile() || !$file->isReadable())
							{
								continue;
							}

							$url = self::link(array(
								'menuaction'=> 'property.uitts.show_attachment',
								'file_name' => urlencode((string)$file),
								'key'=> $entry['external_voucher_id']
								));

							$attachmen_list[] = array(
								'voucher_id'	=> $entry['external_voucher_id'],
								'file_name'		=> "<a href='{$url}' target='_blank'>" . (string)$file . "</a>"
							);
						}
					}
				}
				catch (Exception $e)
				{

				}
			}
			$attachmen_def = array(
				array(
					'key' => 'voucher_id',
					'label' => 'key',
					'hidden' => false
					),
				array(
					'key' => 'file_name',
					'label' => lang('attachments'),
					'hidden' => false,
					'sortable' => true,
					)
				);

			$invoice_def = array
				(
				array(
					'key' => 'external_voucher_id',
					'label' => 'key',
					'sortable' => false,
					'value_footer' => lang('Sum')),
				array(
					'key' => 'voucher_id',
					'label' => lang('bilagsnr'),
					'sortable' => false),
				array(
					'key' => 'voucher_out_id',
					'hidden' => true),
				array(
					'key' => 'invoice_id',
					'label' => lang('invoice number'),
					'sortable' => false),
				array(
					'key' => 'vendor',
					'label' => lang('vendor'),
					'sortable' => false),
				array(
					'key' => 'amount',
					'label' => lang('amount'),
					'sortable' => false,
					'className' => 'right',
					'value_footer' => number_format($amount, 2, $this->decimal_separator, '.')),
				array(
					'key' => 'approved_amount',
					'label' => lang('approved amount'),
					'sortable' => false,
					'className' => 'right',
					'value_footer' => number_format($approved_amount, 2, $this->decimal_separator, '.')),
/*				array(
					'key' => 'period',
					'label' => lang('period'),
					'sortable' => false),
				array(
					'key' => 'periodization',
					'label' => lang('periodization'),
					'sortable' => false),
				array(
					'key' => 'periodization_start',
					'label' => lang('periodization start'),
					'sortable' => false),*/
				array(
					'key' => 'currency',
					'label' => lang('currency'),
					'sortable' => false),
				array(
					'key' => 'type',
					'label' => lang('type'),
					'sortable' => false),
				array(
					'key' => 'budget_responsible',
					'label' => lang('budget responsible'),
					'sortable' => false),
				array(
					'key' => 'budsjettsigndato',
					'label' => lang('budsjettsigndato'),
					'sortable' => false),
				array(
					'key' => 'transfer_time',
					'label' => lang('transfer time'),
					'sortable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_7',
				'requestUrl' => "''",
				'data' => json_encode($content_invoice),
				'ColumnDefs' => $invoice_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_8',
				'requestUrl' => "''",
				'data' => json_encode($attachmen_list),
				'ColumnDefs' => $attachmen_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
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

			$external_messages = createObject('property.soexternal_communication')->read($id);

			foreach ($external_messages as &$external_message)
			{
				$external_message['modified_date'] = $GLOBALS['phpgw']->common->show_date($external_message['modified_date']);
				$external_message['mail_recipients'] = implode(', ', $external_message['mail_recipients']);
				$external_message['subject_link'] = "<a href=\"" . self::link(array('menuaction' => 'property.uiexternal_communication.edit',
						'id' => $external_message['id'], 'ticket_id' => $id)) . "\">{$external_message['subject']}</a>";
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_9',
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


			// end invoice table

			//----------------------------------------------datatable settings--------


			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->bo->config->config_data['filter_buildingpart']) ? $this->bo->config->config_data['filter_buildingpart'] : array();

			if ($filter_key = array_search('.b_account', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

//_debug_array($supervisor_email);die();
			$msgbox_data = $this->bocommon->msgbox_data($receipt);
			$cat_select = $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]',
				'selected' => $this->cat_id, 'use_acl' => $this->_category_acl, 'required' => true, 'class' => 'pure-input-1-2'));

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

				array_unshift($cat_select['cat_list'], array
					(
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

			$cat_select['disabled'] = !!$this->simple;

//			$this->cats->set_appname('property','.project');
//			$order_catetory	= $this->cats->formatted_xslt_list(array('select_name' => 'values[order_cat_id]','selected' => $ticket['order_cat_id']));

			$year = date('Y') - 1;
			$limit = $year + 4;

			$year_list = array();
			while ($year < $limit)
			{
				$year_list[] = array
					(
					'id' => $year,
					'name' => $year
				);
				$year++;
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

			$unspsc_code = $ticket['unspsc_code'] ? $ticket['unspsc_code'] : $GLOBALS['phpgw_info']['user']['preferences']['property']['unspsc_code'];
			$enable_order_service_id = !empty($this->bo->config->config_data['enable_order_service_id']) ? true : false;
			$enable_unspsc = !empty($this->bo->config->config_data['enable_unspsc']) ? true : false;
			$relation_type_list = array(
				array(
					'id'	=> 'property.uirequest.index',
					'name'	=> lang('request')
				),
//				array(
//					'id'	=> 'property.uiproject.index',
//					'name'	=> lang('project')
//				),
//				array(
//					'id'	=> 'property.uilookup.entity',
//					'name'	=> 'Everything else'
//				),
			);

			$data = array(
				'datatable_def' => $datatable_def,
				'relation_type_list' => array('options' => $relation_type_list),
				'my_groups' => json_encode($my_groups),
				'custom_attributes' => array('attributes' => $ticket['attributes']),
				'lookup_functions' => isset($ticket['lookup_functions']) ? $ticket['lookup_functions'] : '',
				'send_response' => isset($this->bo->config->config_data['tts_send_response']) ? $this->bo->config->config_data['tts_send_response'] : '',
				'value_sms_phone' => $ticket['contact_phone'],
				'access_order' => $access_order,
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'enable_unspsc' => $enable_unspsc,
				'enable_order_service_id' => $enable_order_service_id,
				'value_order_id' => $ticket['order_id'],
				'value_order_descr' => $ticket['order_descr'],
				'value_invoice_remark' => $ticket['invoice_remark'],
				'vendor_data' => $vendor_data,
				'b_account_data' => $b_account_data,
				'ecodimb_data' => $ecodimb_data,
				'value_service_id' => $ticket['service_id'],
				'value_service_name' => $this->_get_eco_service_name($ticket['service_id']),
				'value_external_project_id' => $ticket['external_project_id'],
				'value_external_project_name' => $this->_get_external_project_name($ticket['external_project_id']),
				'value_unspsc_code' => $unspsc_code,
				'value_unspsc_code_name' => $this->_get_unspsc_code_name($unspsc_code),
				'value_budget' => $ticket['budget'],
				'value_actual_cost' => $ticket['actual_cost'],
				'year_list' => array('options' => $this->bocommon->select_list((int)$ticket['actual_cost_year'] ? $ticket['actual_cost_year'] : (int)date('Y'), $year_list)),
				'period_list' => array('options' => execMethod('property.boinvoice.period_list', date('Ym'))),
				'need_approval' => $need_approval,
				'contact_data' => $contact_data,
				'lookup_type' => $lookup_type,
				'simple' => $this->simple,
				'show_finnish_date' => $this->show_finnish_date,
				'tabs' => self::_generate_tabs(true),
				'td_count' => '""',
				'base_java_url' => "{menuaction:'property.uitts.update_data',id:{$id}}",
				'location_item_id' => $id,
				'value_location_code'	=> $ticket['location_code'],
				'value_origin' => $ticket['origin'],
				'value_target' => $ticket['target'],
				'value_finnish_date' => $ticket['finnish_date'],
				'value_order_deadline' => $ticket['order_deadline'],
				'value_order_deadline2' => $ticket['order_deadline2'],
				'link_entity' => $link_entity,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data2' => $location_data,
				'value_status' => $ticket['status'],
				'status_list' => array('options' => $this->bo->get_status_list($ticket['status'])),
				'lang_no_user' => lang('Select user'),
				'lang_user_statustext' => lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name' => 'values[assignedto]',
				'value_assignedto_id' => $ticket['assignedto'],
//				'user_list' => $this->bocommon->get_user_list_right2('select', 4, $ticket['assignedto'], $this->acl_location),
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
				'contract_list' => array('options' => $this->get_vendor_contract($ticket['vendor_id'], $ticket['contract_id']) ),
				'lang_no_cat' => lang('no category'),
				'value_cat_id' => $this->cat_id,
				'cat_select' => $cat_select,
				'value_category_name' => $ticket['category_name'],
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $form_link),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.index')),
				'value_subject' => $ticket['subject'],
				'value_id' => '[ #' . $id . ' ] - ',
				'value_details' => $ticket['details'],
				'value_opendate' => $ticket['entry_date'],
				'value_assignedfrom' => $ticket['user_name'],
				'value_assignedto_name' => isset($ticket['assignedto_name']) ? $ticket['assignedto_name'] : '',
				'show_billable_hours' => isset($this->bo->config->config_data['show_billable_hours']) ? $this->bo->config->config_data['show_billable_hours'] : '',
				'value_billable_hours' => $ticket['billable_hours'],
				'additional_notes' => $additional_notes,
				'record_history' => $record_history,
				'request_link' => $request_link,
				'order_link' => $order_link,
				'add_to_project_link' => $add_to_project_link,
				//			'lang_name'						=> lang('name'),
				'contact_phone' => $ticket['contact_phone'],
				'pref_send_mail' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'] : '',
				'fileupload' => isset($this->bo->config->config_data['fmttsfileupload']) ? $this->bo->config->config_data['fmttsfileupload'] : '',
				'multiple_uploader' => true,
				'multi_upload_parans' => "{menuaction:'property.uitts.build_multi_upload_file', id:'{$id}'}",
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'link_to_files' => isset($this->bo->config->config_data['files_url']) ? $this->bo->config->config_data['files_url'] : '',
				'files' => isset($ticket['files']) ? $ticket['files'] : '',
				'lang_filename' => lang('Filename'),
				'lang_file_action' => lang('Delete file'),
				'lang_view_file_statustext' => lang('click to view file'),
				'lang_file_action_statustext' => lang('Check to delete file'),
				'lang_upload_file' => lang('Upload file'),
				'lang_file_statustext' => lang('Select file to upload'),
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 60,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
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
				'branch_list' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list'] == 1 ? array(
					'options' => execMethod('property.boproject.select_branch_list', $values['branch_id'])) : '',
				'preview_html' => "javascript:preview_html($id)",
				'preview_pdf' => "javascript:preview_pdf($id)",
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_order_sent'	=> !!$ticket['order_sent'],
				'value_order_received'	=> $ticket['order_received'] ? $GLOBALS['phpgw']->common->show_date($ticket['order_received']) : '[ DD/MM/YYYY - H:i ]',
				'value_order_received_amount' => (int) $ticket['order_received_amount'],
				'value_extra_mail_address' => $value_extra_mail_address,
				'value_continuous'	=> $ticket['continuous']
			);

			phpgwapi_jquery::load_widget('numberformat');
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('property', 'portico', 'tts.view.js');

			$this->_insert_custom_js();
			//-----------------------datatable settings---
			//_debug_array($data);die();

			$appname = lang('helpdesk');
			$function_msg = lang('view ticket detail');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			self::render_template_xsl( array('tts', 'files', 'attributes_form',
				'datatable_inline'), $data, $xsl_rootdir = '' , 'view');
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		public function get_vendor_contract($vendor_id = 0, $selected = 0)
		{
			$contract_list = $this->bocommon->get_vendor_contract($vendor_id, $selected);
			if($contract_list)
			{
				array_unshift($contract_list, array('id' => -1, 'name' => lang('outside contract')));
			}
			if($selected)
			{
				foreach ($contract_list as &$contract)
				{
					$contract['selected'] = $selected == $contract['id'] ? 1 : 0;
				}
			}
			return $contract_list;
		}


		public function check_purchase_right()
		{
			$ecodimb	= phpgw::get_var('ecodimb');
			$amount		= phpgw::get_var('amount', 'int');
			$order_id	=  phpgw::get_var('order_id', 'int');

			return $this->bo->check_purchase_right($ecodimb, $amount, $order_id);
		}

		/**
		 * Gets vendor canidated to be used as vendor - called as ajax from edit form
		 *
		 * @param string  $query
		 *
		 * @return array
		 */
		public function get_eco_service()
		{
			if (!$this->acl_read)
			{
				return;
			}

			return $this->bocommon->get_eco_service();
		}

		public function get_external_project()
		{
			if (!$this->acl_read)
			{
				return;
			}

			return $this->bocommon->get_external_project();
		}

		public function get_unspsc_code()
		{
			if (!$this->acl_read)
			{
				return;
			}
			return $this->bocommon->get_unspsc_code();
		}

		public function get_ecodimb()
		{
			if (!$this->acl_read)
			{
				return;
			}

			return $this->bocommon->get_ecodimb();
		}

		public function get_b_account()
		{
			if (!$this->acl_read)
			{
				return;
			}

			return $this->bocommon->get_b_account();
		}

		public function receive_order( )
		{
			if (!$GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'property'))
			{
				return;
			}

			$id = phpgw::get_var('id', 'int');
			$received_amount = phpgw::get_var('received_amount', 'float');
			return $this->bo->receive_order($id, $received_amount);
		}

		private function _get_eco_service_name( $id )
		{
			return $this->bocommon->get_eco_service_name($id);
		}

		private function _get_unspsc_code_name( $id )
		{
			return $this->bocommon->get_unspsc_code_name($id);
		}

		private function _get_external_project_name( $id )
		{
			return $this->bocommon->get_external_project_name($id);
		}

		protected function _generate_tabs( $history = '' )
		{
			if (!$tab = phpgw::get_var('tab'))
			{
				$tab = 'general';
			}


			$tabs = array
				(
				'general' => array('label' => lang('general'), 'link' => '#general'),
				'notify' => array('label' => lang('notify'), 'link' => '#notify')
			);

			if ($history)
			{
				$tabs['history'] = array('label' => lang('history'), 'link' => '#history');
			}

			return phpgwapi_jquery::tabview_generate($tabs, $tab, 'ticket_tabview');
		}

		private function _pdf_order( $id = 0, $preview = false, $show_cost = false )
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if (!$id)
			{
				$id = phpgw::get_var('id'); // in case of bigint
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}

			if (!$show_cost)
			{
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}

			$ticket = $this->bo->read_single($id);


			$content = array(); //$this->_get_order_details($common_data['content'],	$show_cost);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);

			set_time_limit(1800);
			$pdf = CreateObject('phpgwapi.pdf');

			$pdf->ezSetMargins(50, 70, 50, 50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();

			if (isset($this->bo->config->config_data['order_logo']) && $this->bo->config->config_data['order_logo'])
			{
				$pdf->addJpegFromFile($this->bo->config->config_data['order_logo'], 40, 800, isset($this->bo->config->config_data['order_logo_width']) && $this->bo->config->config_data['order_logo_width'] ? $this->bo->config->config_data['order_logo_width'] : 80
				);
			}
			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 40, 578, 40);
			//	$pdf->line(20,820,578,820);
			//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50, 28, 6, $this->bo->config->config_data['org_name']);
			$pdf->addText(300, 28, 6, $date);

			if ($preview)
			{
				$pdf->setColor(1, 0, 0);
				$pdf->addText(200, 400, 40, lang('DRAFT'), -10);
				$pdf->setColor(1, 0, 0);
			}

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');

//			$pdf->ezSetDy(-100);

			$pdf->ezStartPageNumbers(500, 28, 6, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);

			$organisation = '';
			$contact_name = '';
			$contact_email = '';
			$contact_phone = '';
			$contact_name2  = '';
			$contact_email2 = '';
			$contact_phone3 = '';

			if (isset($this->bo->config->config_data['org_name']))
			{
				$organisation = $this->bo->config->config_data['org_name'];
			}
			if (isset($this->bo->config->config_data['department']))
			{
				$department = $this->bo->config->config_data['department'];
			}

			$data = array(
				array(
					'col1' => lang('order id') . " <b>{$ticket['order_id']}</b>",
					'col2' => lang('date') . ": {$date}"
			));

			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array('showHeadings' => 0,
				'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500, 'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)
			));

			$GLOBALS['phpgw']->preferences->set_account_id($common_data['workorder']['user_id'], true);


			$on_behalf_of_assigned = phpgw::get_var('on_behalf_of_assigned', 'bool');
			if ($on_behalf_of_assigned && isset($ticket['assignedto_name']))
			{
				$from_name = $ticket['assignedto_name'];
				$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
				$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->data;
			}
			else
			{
				$from_name = $GLOBALS['phpgw_info']['user']['fullname'];
			}

			$ressursnr = $GLOBALS['phpgw_info']['user']['preferences']['property']['ressursnr'];
			$data = array(
				array(
					'col1' => "{$organisation}\n{$department}\nOrg.nr: {$this->bo->config->config_data['org_unit_id']}",
					'col2' => "Saksbehandler: {$from_name}\nRessursnr.: {$ressursnr}"
				),
			);

			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array(
				'showHeadings' => 0, 'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500, 'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)
			));


			$delivery_address = lang('delivery address') . ':';
			if (isset($this->bo->config->config_data['delivery_address']) && $this->bo->config->config_data['delivery_address'])
			{
				$delivery_address .= "\n{$this->bo->config->config_data['delivery_address']}";
			}
			else
			{
				$delivery_address .= "\n" . createObject('property.solocation')->get_location_address($ticket['location_code'])."\n";
				$location_code = $ticket['location_data']['location_code'];
				$address_element = execMethod('property.botts.get_address_element', $location_code);
				foreach ($address_element as $entry)
				{
					$delivery_address .= "\n{$entry['text']}: {$entry['value']}";
				}
			}

			$data = array
				(
				array('col1' => $delivery_address)
			);

			$pdf->ezTable($data, array('col1' => ''), '', array('showHeadings' => 0,
				'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500, 'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('justification' => 'right', 'width' => 500, 'justification' => 'left'),
				)
			));

			$invoice_address = lang('invoice address') . ":\n{$this->bo->config->config_data['invoice_address']}";


//			$from = lang('date') . ": {$date}\n";
//			$from .= lang('dimb') . ": {$ticket['ecodimb']}\n";
//			$from .= lang('from') . ":\n   {$from_name}";
//			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['email']}";
//			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['cellphone']}";
//


			if (isset($ticket['vendor_id']) && $ticket['vendor_id'])
			{
				$ticket['vendor_name'] = $this->_get_vendor_name($ticket['vendor_id']);
			}

			$data = array
				(
				array('col1' => lang('to') . ":\n{$ticket['vendor_name']}", 'col2' => $invoice_address),
			);

			if($ticket['order_deadline'])
			{
				$data[] = array('col1' => lang('deadline for start'), 'col2' =>"<b>{$ticket['order_deadline']}</b>");
			}
			if($ticket['order_deadline2'])
			{
				$data[] = array('col1' => lang('deadline for execution'), 'col2' =>"<b>{$ticket['order_deadline2']}</b>");
			}

			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array('showHeadings' => 0,
				'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500, 'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)
			));

			$pdf->ezSetDy(-10);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
			$pdf->ezText(lang('descr') . ':', 20);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));


			if (isset($contact_data['value_contact_name']) && $contact_data['value_contact_name'])
			{
				$contact_name = ltrim($contact_data['value_contact_name']);
			}
			if (isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
			{
				$contact_email = $contact_data['value_contact_email'];
			}
			if (isset($contact_data['value_contact_tel']) && $contact_data['value_contact_tel'])
			{
				$contact_phone = $contact_data['value_contact_tel'];
			}

			$pdf->ezText($ticket['order_descr'], 14);
			$pdf->ezSetDy(-20);

			$user_phone = $GLOBALS['phpgw_info']['user']['preferences']['property']['cellphone'];
			$user_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
			$order_email_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_email_template'];
			$order_contact_block_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_contact_block_1'];

			if (!empty($this->bo->config->config_data['contact_at_location']))
			{
				$contact_at_location = $this->bo->config->config_data['contact_at_location'];

				$_responsible = execMethod('property.boresponsible.get_responsible', array('location'=> explode('-', $ticket['location_code']),
					'cat_id' => $ticket['cat_id'],
					'role_id' => $contact_at_location
					));

				if($_responsible)
				{
					$prefs					= $this->bocommon->create_preferences('property', $_responsible);
					$GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'] = 'firstname';
					$_responsible_name		= $GLOBALS['phpgw']->accounts->get($_responsible)->__toString();
					$_responsible_email		= $prefs['email'];
					$_responsible_cellphone	= $prefs['cellphone'];
					if($contact_email  && ($contact_data['value_contact_email'] != $_responsible_email))
					{
						$contact_name2 = $_responsible_name;
						$contact_email2 = $_responsible_email;
						$contact_phone2 = $_responsible_cellphone;
						$order_contact_block_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_contact_block_2'];
					}
					else
					{
						$contact_name = $_responsible_name;
						$contact_email = $_responsible_email;
						$contact_phone = $_responsible_cellphone;
					}
				}
			}

			$user_phone = str_replace(' ', '', $user_phone);
			$contact_phone = str_replace(' ', '', $contact_phone);
			$contact_phone2 = str_replace(' ', '', $contact_phone2);

			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $user_phone,  $matches ) )
			{
				$user_phone = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}
			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $contact_phone,  $matches ) )
			{
				$contact_phone = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}
			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $contact_phone2,  $matches ) )
			{
				$contact_phone2 = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}

			if($contact_name)
			{
				$contact_block = str_replace(array
					(
					'__user_name__',
					'__user_phone__',
					'__user_email__',
					'__contact_name__',
					'__contact_email__',
					'__contact_phone__',
					'__contact_name2__',
					'__contact_email2__',
					'__contact_phone2__',
					'__order_id__',
					'[b]',
					'[/b]'
						), array
					(
					$user_name,
					$user_phone,
					$user_email,
					$contact_name,
					$contact_email,
					$contact_phone,
					$contact_name2,
					$contact_email2,
					$contact_phone2,
					$order_id,
					'<b>',
					'</b>'
						), $order_contact_block_template);
			}
			else
			{
				$contact_block = '';
			}

//			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
//			$pdf->ezText('Kontakt på bygget:', 14);
//			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
//			$pdf->ezText($contact_name, 14);
//			$pdf->ezText($contact_email, 14);
//			$pdf->ezText($contact_phone, 14);
//			$pdf->ezSetDy(-20);
			$pdf->ezText($contact_block, 14);

			$location_exceptions = createObject('property.solocation')->get_location_exception($ticket['location_code'], $alert_vendor = true);

			if($location_exceptions)
			{
				$pdf->ezSetDy(-20);
				$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
				$pdf->ezText(lang('important information'), 14);
				$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
			}

			foreach ($location_exceptions as $location_exception)
			{
				$pdf->ezText($location_exception['category_text'], 14);

				if($location_exception['location_descr'])
				{
					$pdf->ezText($location_exception['location_descr'], 14);
				}
			}
			$pdf->ezSetDy(-20);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
			$pdf->ezText("Faktura må merkes med ordrenummer: {$ticket['order_id']} og ressursnr.:{$ressursnr}", 14);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
			if ($content)
			{
				$pdf->ezSetDy(-20);
				$pdf->ezTable($content, '', lang('details'), array('xPos' => 0, 'xOrientation' => 'right',
					'width' => 500, 0, 'shaded' => 0, 'fontSize' => 8, 'gridlines' => EZ_GRIDLINE_ALL,
					'titleFontSize' => 12, 'outerLineThickness' => 2
					, 'cols' => array(
						lang('bill per unit') => array('justification' => 'right', 'width' => 50)
						, lang('quantity') => array('justification' => 'right', 'width' => 50)
						, lang('cost') => array('justification' => 'right', 'width' => 50)
						, lang('unit') => array('width' => 40)
						, lang('descr') => array('width' => 120))
				));
			}
//start SMS::QRCODE
			$sms_location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');
			$config_sms = CreateObject('admin.soconfig', $sms_location_id);
			$gateway_number = $config_sms->config_data['common']['gateway_number'];
			$gateway_codeword = $config_sms->config_data['common']['gateway_codeword'];
			phpgw::import_class('phpgwapi.phpqrcode');
			$code_text = "SMSTO:{$gateway_number}: {$gateway_codeword} STATUS {$ticket['order_id']} ";

			$filename = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . md5($code_text) . '.png';
			QRcode::png($code_text, $filename);
			$pdf->ezSetDy(-20);

			$lang_status_code = lang('status code');
			$lang_to = lang('to');
			$code_help = "Send: {$gateway_codeword} STATUS {$ticket['order_id']} <{$lang_status_code}> {$lang_to} {$gateway_number}\n\n"
				. $lang_status_code
				. ":\n\n 1 => " . lang('performed')
				. "\n 2 => " . lang('No access')
				. "\n 3 => I arbeid";
			$data = array(
				array('col1' => "<C:showimage:{$filename} 90>", 'col2' => $code_help)
			);

			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array('showHeadings' => 0,
				'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500,
				'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('width' => 150, 'justification' => 'left'),
					'col2' => array('width' => 350, 'justification' => 'left'),
				)
			));

//end SMS::QRCODE

			if (isset($this->bo->config->config_data['order_footer_header']) && $this->bo->config->config_data['order_footer_header'])
			{
				if (!$content)
				{
					$pdf->ezSetDy(-100);
				}
				$pdf->ezText($this->bo->config->config_data['order_footer_header'], 12);
				$pdf->ezText($this->bo->config->config_data['order_footer'], 10);
			}

			$document = $pdf->ezOutput();

			if ($preview)
			{
				$pdf->print_pdf($document, "order_{$ticket['order_id']}");
			}
			else
			{
				return $document;
			}
		}

		private function _html_order( $id = 0, $preview = false, $show_cost = false )
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			if (!$id)
			{
				$id = phpgw::get_var('id'); // in case of bigint
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}
			$GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'] = 'firstname';

			if (!$show_cost)
			{
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}
			$historylog = CreateObject('property.historylog', 'tts');

			$ticket = $this->bo->read_single($id);
			$subject = lang('workorder') . ": {$ticket['order_id']}";

			$organisation = '';
			$contact_name = '';
			$contact_email = '';
			$contact_phone = '';
			$contact_name2  = '';
			$contact_email2 = '';
			$contact_phone3 = '';

			if (isset($this->bo->config->config_data['org_name']))
			{
				$organisation = $this->bo->config->config_data['org_name'];
			}
			if (isset($this->bo->config->config_data['department']))
			{
				$department = $this->bo->config->config_data['department'];
			}

			$on_behalf_of_assigned = phpgw::get_var('on_behalf_of_assigned', 'bool');
			if ($on_behalf_of_assigned && isset($ticket['assignedto_name']))
			{
				$user_name = $ticket['assignedto_name'];
				$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
				$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->data;
				if (!$preview)
				{
					$_behalf_alert = lang('this order is sent by %1 on behalf of %2', $GLOBALS['phpgw_info']['user']['fullname'], $user_name);
					$historylog->add('C', $id, $_behalf_alert);
					unset($_behalf_alert);
				}
			}
			else
			{
				$user_name = $GLOBALS['phpgw_info']['user']['fullname'];
			}
			$ressursnr = $GLOBALS['phpgw_info']['user']['preferences']['property']['ressursnr'];
//			$location = $ticket['address'];

			$location = createObject('property.solocation')->get_location_address($ticket['location_code'])  . '<br/>';

			$address_element = $this->bo->get_address_element($ticket['location_code']);

			foreach ($address_element as $address_entry)
			{
				$location .= " <br/>{$address_entry['text']}: {$address_entry['value']}";
			}

	//		$location = rtrim($location, '<br/>');

			$order_description = $ticket['order_descr'];

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));

			if (isset($contact_data['value_contact_name']) && $contact_data['value_contact_name'])
			{
				$contact_name = $contact_data['value_contact_name'];
			}
			if (isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
			{
				$contact_email = "<a href='mailto:{$contact_data['value_contact_email']}'>{$contact_data['value_contact_email']}</a>";
			}
			if (isset($contact_data['value_contact_tel']) && $contact_data['value_contact_tel'])
			{
				$contact_phone = $contact_data['value_contact_tel'];
			}

			$order_id = $ticket['order_id'];
//account_display
			$user_phone = $GLOBALS['phpgw_info']['user']['preferences']['property']['cellphone'];
			$user_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
			$order_email_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_email_template'];
			$order_contact_block_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_contact_block_1'];

			if (!empty($this->bo->config->config_data['contact_at_location']))
			{
				$contact_at_location = $this->bo->config->config_data['contact_at_location'];

				$_responsible = execMethod('property.boresponsible.get_responsible', array('location'=> explode('-', $ticket['location_code']),
					'cat_id' => $ticket['cat_id'],
					'role_id' => $contact_at_location)
					);

				if($_responsible)
				{
					$prefs					= $this->bocommon->create_preferences('property', $_responsible);
					$GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'] = 'firstname';
					$_responsible_name		= $GLOBALS['phpgw']->accounts->get($_responsible)->__toString();
					$_responsible_email		= $prefs['email'];
					$_responsible_cellphone	= $prefs['cellphone'];
					if($contact_email  && ($contact_data['value_contact_email'] != $_responsible_email))
					{
						$contact_name2 = $_responsible_name;
						$contact_email2 = "<a href='mailto:{$_responsible_email}'>{$_responsible_email}</a>";
						$contact_phone2 = $_responsible_cellphone;
						$order_contact_block_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_contact_block_2'];
					}
					else
					{
						$contact_name = $_responsible_name;
						$contact_email = "<a href='mailto:{$_responsible_email}'>{$_responsible_email}</a>";
						$contact_phone = $_responsible_cellphone;
					}
				}
			}

			$user_phone = str_replace(' ', '', $user_phone);
			$contact_phone = str_replace(' ', '', $contact_phone);
			$contact_phone2 = str_replace(' ', '', $contact_phone2);

			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $user_phone,  $matches ) )
			{
				$user_phone = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}
			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $contact_phone,  $matches ) )
			{
				$contact_phone = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}
			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $contact_phone2,  $matches ) )
			{
				$contact_phone2 = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}

			function nl2br2($string)
			{
				$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
				return $string;
			}
			
			if($contact_name)
			{
				$contact_block = '<br/>' . nl2br2(str_replace(array
					(
					'__user_name__',
					'__user_phone__',
					'__user_email__',
					'__contact_name__',
					'__contact_email__',
					'__contact_phone__',
					'__contact_name2__',
					'__contact_email2__',
					'__contact_phone2__',
					'__order_id__',
					'[b]',
					'[/b]'
						), array
					(
					$user_name,
					$user_phone,
					$user_email,
					$contact_name,
					$contact_email,
					$contact_phone,
					$contact_name2,
					$contact_email2,
					$contact_phone2,
					$order_id,
					'<b>',
					'</b>'
						), $order_contact_block_template));
				$contact_block .= '<br/>';
			}
			else
			{
				$contact_block = '';
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);


			$lang_order = lang('order');
			$lang_from = lang('from');
			$body = "<table style='width: 800px;'><tr>";
			$body .= "<td valign='top'>{$lang_order}: <b>{$order_id}</b><br/>&nbsp;</td>";
			$body .= "<td valign='top'>" . lang('date') . ":{$date}</td>";
			$body .= "</tr>";
			$body .= "<tr>";
			$body .= "<td valign='top'>{$lang_from}: {$organisation}<br/>"
					. "{$department}<br/>"
					. "Org.nr: {$this->bo->config->config_data['org_unit_id']}"
					. "</td>";
			$body .= "<td valign='top'>Saksbehandler: {$user_name}<br/>"
					. "Ressursnr.: {$ressursnr}<br/>"
					. "</td>";
			$body .= "</tr>";
			$body .= "<tr>";
			$body .= "<td colspan=2>" .  lang('delivery address') . ":<br/>{$location}</td>";
			$body .= "</tr>";
			$body .= "<tr>";
			$body .= "<td valign='top'>" .  lang('to') . ":<br/>" . $this->_get_vendor_name($ticket['vendor_id']) . "</td>";
			$body .= "<td valign='top'>" .  lang('invoice address') . ":<br/>{$this->bo->config->config_data['invoice_address']}</td>";

			$body .= "</tr></table>";


			$deadline_block = '';

			if($ticket['order_deadline'] || $ticket['order_deadline2'])
			{
				$deadline_block .= "<br/><table id='order_deadline'><tr>";
			}

			if($ticket['order_deadline'])
			{
				$deadline_block .= "<td><b>" . lang('deadline for start') . '</b></td>';
			}
			if($ticket['order_deadline2'])
			{
				$deadline_block .= "<td><b>" . lang('deadline for execution') . '</b></td></tr>';
			}
			else
			{
				$deadline_block .= '</tr>';
			}
			if($ticket['order_deadline'])
			{
				$deadline_block .= "<tr><td>" . $ticket['order_deadline'] . "</td>";
			}
			if($ticket['order_deadline2'])
			{
				$deadline_block .= "<td>" . $ticket['order_deadline2'] . "</td>";
			}
			else
			{
				$deadline_block .= '</tr>';
			}
			if($deadline_block)
			{
				$deadline_block .= "</tr></table>";
			}

			$location_exceptions = createObject('property.solocation')->get_location_exception($ticket['location_code'], $alert_vendor = true);

			$important_imformation = '';
			if($location_exceptions)
			{
				$important_imformation .= "<b>" . lang('important information') . '</b>';
				$important_imformation_arr = array();
				foreach ($location_exceptions as $location_exception)
				{
					$important_imformation_arr[] = $location_exception['category_text'];

					if($location_exception['location_descr'])
					{
						$important_imformation_arr[] = $location_exception['location_descr'];
					}
				}
				$important_imformation .= "\n" . implode("\n", $important_imformation_arr);
			}

			$body .= '<br/>'. nl2br(str_replace(array
				(
				'__vendor_name__',
				'__organisation__',
				'__user_name__',
				'__user_phone__',
				'__user_email__',
				'__ressursnr__',
				'__location__',
				'__order_description__',
				'__deadline_block__',
				'__important_imformation__',
				'__contact_block__',
				'__contact_name__',
				'__contact_email__',
				'__contact_phone__',
				'__order_id__',
				'[b]',
				'[/b]'
					), array
				(
				$this->_get_vendor_name($ticket['vendor_id']),
				$organisation,
				$user_name,
				$user_phone,
				$user_email,
				$ressursnr,
				$location,
				$order_description,
				$deadline_block,
				$important_imformation,
				$contact_block,
				$contact_name,
				$contact_email,
				$contact_phone,
				$order_id,
				'<b>',
				'</b>'
					), $order_email_template));



			$html = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><title>{$subject}</title>";
			$html .= '	<style>

			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
			}
		html {
		font-family: arial;
		}
		@page {
		size: A4;
		}

		#order_deadline{
			width: 800px;
			border:0px solid transparent;
		}

		#order_deadline td{
			border:0px solid transparent;
		}
		@media print {
		li {page-break-inside: avoid;}
		h1, h2, h3, h4, h5 {
		page-break-after: avoid;
		}

		table, figure {
		page-break-inside: avoid;
		}
		}


		@page:left{
		@bottom-left {
		content: "Page " counter(page) " of " counter(pages);
		}
		}
		@media print
		{
		.btn
		{
		display: none !important;
		}
		}

		.btn{
		background: none repeat scroll 0 0 #2647A0;
		color: #FFFFFF;
		display: inline-block;
		margin-right: 5px;
		padding: 5px 10px;
		text-decoration: none;
		border: 1px solid #173073;
		cursor: pointer;
		}

		ul{
		list-style: none outside none;
		}

		li{
		list-style: none outside none;
		}

		li.list_item ol li{
		list-style: decimal;
		}

		ul.groups li {
		padding: 3px 0;
		}

		ul.groups li.odd{
		background: none repeat scroll 0 0 #DBE7F5;
		}

		ul.groups h3 {
		font-size: 18px;
		margin: 0 0 5px;
		}

	</style></head>';
			$body .='</br>';
			$body .='</br>';
			$body .= '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view',
					'id' => $id), false, true) . '">' . lang('Ticket') . ' #' . $id . '</a>';
			$html .= "<body><div style='width: 800px;'>{$body}</div></body></html>";


			if ($preview)
			{

				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
				echo $html;
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			return $html;
		}



		private function _get_vendor_name($vendor_id = 0)
		{
			$vendor_name = '';
			if (!empty($vendor_id))
			{
				$contacts = CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor', false);

				$custom = createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property', '.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data = $contacts->read_single(array('id' => $vendor_id), $vendor_data);
				if (is_array($vendor_data))
				{
					foreach ($vendor_data['attributes'] as $attribute)
					{
						if ($attribute['name'] == 'org_name')
						{
							$vendor_name = $attribute['value'];
							break;
						}
					}
				}
				unset($contacts);
			}
			return $vendor_name;
		}

		/**
		 *
		 */
		private function _insert_custom_js()
		{
			$criteria = array
				(
				'appname' => 'property',
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

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && $entry['client_side'] && is_file($file))
				{
					$GLOBALS['phpgw']->js->add_external_file("/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}");
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

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, $this->acl_location, 'property', $this->group_candidates);
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

		private function _send_order( $ticket, $send_order_format, $purchase_grant_checked = false, $purchase_grant_error = false )
		{
			$_to = !empty($ticket['mail_recipients'][0]) ? implode(';', $ticket['mail_recipients']) : '';

			$subject = lang('workorder') . ": {$ticket['order_id']}";

			if (!$_to)
			{
				phpgwapi_cache::message_set(lang('missing recipient for order %1', $ticket['order_id']),'error' );
				return false;
			}
			$historylog = CreateObject('property.historylog', 'tts');

			$id = $ticket['id'];
			$order_id = $ticket['order_id'];

			if (isset($ticket['file_attachments']) && is_array($ticket['file_attachments']))
			{
				$attachments = CreateObject('property.bofiles')->get_attachments($ticket['file_attachments']);
				$_attachment_log = array();
				foreach ($attachments as $_attachment)
				{
					$_attachment_log[] = $_attachment['name'];
				}
				$attachment_log = ' ' . lang('attachments') . ' : ' . implode(', ', $_attachment_log);
			}

			if ($send_order_format == 'pdf')
			{
				$pdfcode = $this->_pdf_order($id);
				if ($pdfcode)
				{
					$dir = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/pdf_files";

					//save the file
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);
					}
					$fname = tempnam($dir . '/', 'PDF_') . '.pdf';
					$fp = fopen($fname, 'w');
					fwrite($fp, $pdfcode);
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
			else
			{
				$body = $this->_html_order($id);
			}

			if (empty($GLOBALS['phpgw_info']['server']['smtp_server']))
			{
				phpgwapi_cache::message_set(lang('SMTP server is not set! (admin section)'),'error' );
			}
			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$on_behalf_of_assigned = phpgw::get_var('on_behalf_of_assigned', 'bool');
			if ($on_behalf_of_assigned && isset($ticket['assignedto_name']))
			{
				$coordinator_name = $ticket['assignedto_name'];
			}
			else
			{
				$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
			}

			$coordinator_email = "{$coordinator_name}<{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}>";

			$validator = CreateObject('phpgwapi.EmailAddressValidator');

			if (!$validator->check_email_address($GLOBALS['phpgw_info']['user']['preferences']['property']['email']))
			{
				$bcc = '';
				phpgwapi_cache::message_set(lang('please update <a href="%1">your email address here</a>', $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'=>'property','type'=> 'user') )),'error' );
			}
			else
			{
				$bcc = $coordinator_email;
			}

			$cc = '';
			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));
			if (isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
			{
				$cc = $contact_data['value_contact_email'];
			}

			if (empty($purchase_grant_checked))
			{
				$budget_amount = $this->_get_budget_amount($id);

				try
				{
					$purchase_grant_ok = $this->bo->validate_purchase_grant( $ecodimb, $budget_amount, $order_id );
				}
				catch (Exception $ex)
				{
					throw $ex;
				}

				$purchase_grant_error = $purchase_grant_ok ? false : true;

			}

//				_debug_array($check_purchase); die();

			if(!$purchase_grant_error)
			{
				try
				{
					$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($body), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html', '', $attachments, true);
					phpgwapi_cache::message_set(lang('%1 is notified', $_to),'message' );
					$historylog->add('M', $id, "{$_to}{$attachment_log}");
					phpgwapi_cache::message_set(lang('Workorder %1 is sent by email to %2', $order_id, $_to),'message' );
				}
				catch (Exception $exc)
				{
					phpgwapi_cache::message_set($exc->getMessage(),'error' );
				}
			}
		}

		private function _get_budget_amount($id)
		{
			static $_budget_amount = 0;
			if(!$_budget_amount)
			{
				$budgets = $this->bo->get_budgets($id);
				foreach ($budgets as $budget)
				{
					$_budget_amount += $budget['amount'];
				}
			}
			return $_budget_amount;
		}
	}