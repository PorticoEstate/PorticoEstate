<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
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

	class property_boexternal_communication
	{

		var $so, $historylog;

		public function __construct()
		{
			$this->so = createObject('property.soexternal_communication');
			$this->historylog = & $this->so->historylog;
		}

		function read_additional_notes( $id = 0)
		{
			$additional_notes = array();
			$history_array = $this->historylog->return_array(array(), array('C'), '', '', $id);

			$i = 2;
			foreach ($history_array as $value)
			{
				$additional_notes[] = array
					(
					'value_id' => $value['id'],
					'value_count' => $i,
					'value_date' => $GLOBALS['phpgw']->common->show_date($value['datetime']),
					'value_user' => $value['owner'],
					'value_note' => stripslashes($value['new_value']),
					'value_publish' => $value['publish'],
				);
				$i++;
			}
			return $additional_notes;
		}

		function get_fields()
		{
			return $this->so->get_fields();
		}

		function read_single( $id )
		{
			return $this->so->read_single($id);
		}

		function save( $values )
		{

			if (empty($values['id']))
			{
				$action = 'add';
			}
			else
			{
				$action = 'edit';
			}

			$criteria = array
				(
				'appname' => 'property',
				'location' => ".ticket.external_communication",
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require_once $file;
				}
			}

			if ($action == 'edit')
			{
				try
				{
					$receipt = $this->so->edit($values);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				try
				{
					$receipt = $this->so->add($values);
					$values['id'] = $receipt['id'];
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}

			reset($custom_functions);
			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require_once $file;
				}
			}

			return $receipt;

		}
		function read_record_history( $id )
		{
			$history_array = $this->historylog->return_array(array('C', 'O'), array(), '', '', $id);

			$record_history = array();
			$i = 0;

			foreach ($history_array as $value)
			{
				$record_history[$i]['value_date'] = $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user'] = $value['owner'];

				switch ($value['status'])
				{
					case 'S': $type = lang('Subject changed');
						break;
					case 'M':
						$type = lang('Sent by email to');
						$this->order_sent_adress = $value['new_value']; // in case we want to resend the order as an reminder
						break;
					default:
					// nothing
				}

				$record_history[$i]['value_action'] = $type ? $type : '';
				unset($type);
				if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value'] = $value['new_value'];
					$record_history[$i]['value_old_value'] = $value['old_value'];
				}
				else
				{
					$record_history[$i]['value_new_value'] = '';
				}

				$i++;
			}

			return $record_history;
		}

	}
