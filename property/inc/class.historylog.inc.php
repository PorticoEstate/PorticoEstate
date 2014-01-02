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
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_historylog
	{
		var $db;
		var $appname;
		var $table;
		var $attrib_id_field = '';
		var $detail_id_field = '';
		var $types = array(
			'C' => 'Created',
			'D' => 'Deleted',
			'E' => 'Edited'
		);
		var $alternate_handlers = array();
		var $account;

		function property_historylog($appname)
		{
			if (! $this->account)
			{
				$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			}

			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			if(substr($appname,0,6)=='entity')
			{
				$selector = 'entity';
			}
			else if(substr($appname,0,5)=='catch')
			{
				$selector = 'catch';
			}
			else
			{
				$selector = $appname;
			}

			switch($selector)
			{
				case 'request':
					$this->table='fm_request_history';
					break;
				case 'workorder':
					$this->table='fm_workorder_history';
					break;
				case 'project':
					$this->table='fm_project_history';
					break;
				case 'tts':
					$this->table='fm_tts_history';
					break;
				case 'document':
					$this->table='fm_document_history';
					break;
				case 'entity':
				case 'catch':
					$this->table ="fm_{$selector}_history";
					$this->attrib_id_field = 'history_attrib_id';
					break;
				case 's_agreement':
					$this->table='fm_s_agreement_history';
					$this->attrib_id_field = 'history_attrib_id';
					$this->detail_id_field = 'history_detail_id';
				break;
				case 'tenant_claim':
					$this->table='fm_tenant_claim_history';
					break;
				default:
					throw new Exception(lang('Unknown history register for acl_location: %1', $selector));
				
			}

			$this->appname = $appname;

			$this->db      = & $GLOBALS['phpgw']->db;
		}

		function delete($record_id,$attrib_id='')
		{
			if($attrib_id)
			{
				$attrib_id_condition = "and history_attrib_id = $attrib_id";
			}

			$this->db->query("delete from $this->table where history_record_id='$record_id' and "
				. "history_appname='" . $this->appname . "' $attrib_id_condition",__LINE__,__FILE__);
		}

		function delete_single_record($history_id)
		{
			$this->db->query("delete from $this->table where history_id='$history_id'",__LINE__,__FILE__);
		}


		function add($status,$record_id,$new_value,$old_value ='',$attrib_id='', $date=0,$detail_id='')
		{
			if($date)
			{
				$timestamp = date($this->db->date_format(),$date);
			}
			else
			{
				$timestamp = date($this->db->datetime_format());
			}

			$value_set = array
			(
				'history_record_id'		=> $record_id,
				'history_appname'		=> "'{$this->appname}'",
				'history_owner'			=> (int)$this->account,
				'history_status'		=> "'{$status}'",
				'history_new_value'		=> "'" . $this->db->db_addslashes($new_value) . "'",
				'history_old_value'		=> "'" . $this->db->db_addslashes($old_value) . "'",
				'history_timestamp'		=> "'" . $timestamp."'",
			);

			if($this->attrib_id_field && $attrib_id)
			{
				$value_set[$this->attrib_id_field]	= (int)$attrib_id;
			}
			if($this->detail_id_field && $detail_id)
			{
				$value_set[$this->detail_id_field]	= (int)$detail_id;
			}

			$cols = implode(',', array_keys($value_set));
			$values = implode(',', array_values($value_set));
			$sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$values})";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		// array $filter_out
		function return_array($filter_out, $only_show, $_orderby = '', $sort = '', $record_id, $attrib_id=0, $detail_id=0)
		{
			$record_id = (int) $record_id;
			$attrib_id = (int) $attrib_id;
			$detail_id = (int) $detail_id;
			
			if (! $sort || ! $_orderby)
			{
				$orderby = 'order by history_timestamp,history_id';
			}
			else
			{
				$orderby = "order by $_orderby $sort";
			}

			while (is_array($filter_out) && list(,$_filter) = each($filter_out))
			{
				$filtered[] = "history_status != '$_filter'";
			}

			$filter = '';
			if (isset($filtered))
			{
				$filter = ' AND ' . implode(' AND ',$filtered);
			}

			if($attrib_id)
			{
				$filter .= " AND history_attrib_id = $attrib_id";
			}

			if($detail_id)
			{
				$filter .= " AND history_detail_id = $detail_id";
			}

			while (is_array($only_show) && list(,$_filter) = each($only_show))
			{
				$_only_show[] = "history_status='$_filter'";
			}

			$only_show_filter = '';
			if (isset($_only_show))
			{
				$only_show_filter = ' AND (' . implode(' OR ',$_only_show) . ')';
			}

			$this->db->query("SELECT * FROM {$this->table} WHERE history_appname='"
				. $this->appname . "' AND history_record_id=$record_id $filter $only_show_filter "
				. "$orderby",__LINE__,__FILE__);

			$return_values = array();
			while ($this->db->next_record())
			{
				$return_values[] = array
				(
					'id'			=> $this->db->f('history_id'),
					'record_id'		=> $this->db->f('history_record_id'),
					'owner'			=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('history_owner')),
	//				'status'		=> lang($this->types[$this->db->f('history_status')]),
					'status'		=> preg_replace('/ /','',$this->db->f('history_status')),
					'new_value'		=> $this->db->f('history_new_value',true),
					'old_value'		=> $this->db->f('history_old_value',true),
					'datetime'		=> strtotime($this->db->f('history_timestamp')),
					'publish'		=> $this->db->f('publish')
				);
			}
			return $return_values;
		}
	}
