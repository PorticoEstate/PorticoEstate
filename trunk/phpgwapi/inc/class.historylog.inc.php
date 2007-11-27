<?php
	/**
	* Record history logging
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.historylog.inc.php 17814 2006-12-28 10:28:55Z skwashd $
	*/

	/**
	* Record history logging
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class historylog
	{
		var $db;
		var $appname;
		var $template;
		var $nextmatchs;
		var $types = array(
			'C' => 'Created',
			'D' => 'Deleted',
			'E' => 'Edited'
		);
		var $alternate_handlers = array();

		function historylog($appname)
		{
			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$this->appname = $appname;
			$this->db      =& $GLOBALS['phpgw']->db;
		}

		function delete($record_id)
		{
			$this->db->query("DELETE FROM phpgw_history_log WHERE history_record_id='".(int)$record_id."' and "
				. "history_appname='" . $this->appname . "'",__LINE__,__FILE__);
		}

		function add($status,$record_id,$new_value,$old_value)
		{
			if ($new_value != $old_value)
			{
				$this->db->query("INSERT INTO phpgw_history_log (history_record_id,"
					. "history_appname,history_owner,history_status,history_new_value,history_old_value,history_timestamp) "
					. "VALUES ('".(int)$record_id."','" . $this->appname . "','"
					. $GLOBALS['phpgw_info']['user']['account_id'] . "','$status','"
					. $this->db->db_addslashes($new_value) . "','" . $this->db->db_addslashes($old_value) . "','" . $this->db->to_timestamp(time())
					. "')",__LINE__,__FILE__);
			}
		}

		function return_array($filter_out = array(),$only_show = array(), $_orderby = '',$sort = '', $record_id)
		{
			$record_id = (int) $record_id;

			if ( !$record_id )
			{
				return array();
			}
			
			if (! $sort || ! $_orderby)
			{
				$orderby = 'ORDER BY history_timestamp, history_id';
			}
			else
			{
				$orderby = "ORDER BY $_orderby $sort";
			}

			$filter = '';
			if ( count($filter_out) )
			{
				$filtered = array();
				foreach ( $filter_out as $_filter )
				{
					$filtered[] = "history_status != '" . $this->db->db_addslashes($_filter) . "'";
				}
				$filter = ' AND ' . implode(' AND ',$filtered);
			}

			$only_show_filter = '';
			if ( count($only_show) )
			{
				$_only_show = array();
				foreach ( $only_show as $_filter )
				{
					$_only_show[] = "history_status='$_filter'";
				}
				$only_show_filter = ' AND (' . implode(' OR ',$_only_show). ')';
			}

			$this->db->query('SELECT * FROM phpgw_history_log'
				. " WHERE history_appname='{$this->appname}' AND history_record_id = {$record_id} $filter $only_show_filter "
				. $orderby, __LINE__, __FILE__);

			$return_values = array();
			while ( $this->db->next_record() )
			{
				$return_values[] = array
				(
					'id'         => $this->db->f('history_id'),
					'record_id'  => $this->db->f('history_record_id'),
					'owner'      => $GLOBALS['phpgw']->accounts->id2name($this->db->f('history_owner')),
//					'status'     => lang($this->types[$this->db->f('history_status')]),
					'status'     => ereg_replace(' ','',$this->db->f('history_status')),
					'new_value'  => $this->db->f('history_new_value'),
					'old_value'  => $this->db->f('history_old_value'),
					'datetime'   => $this->db->from_timestamp($this->db->f('history_timestamp'))
				);
			}
			return $return_values;
		}

		function return_html($filter_out,$orderby = '',$sort = '', $record_id)
		{
			$this->template   = createObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
			$this->nextmatchs = createObject('phpgwapi.nextmatchs');

			$this->template->set_file('_history','history_list.tpl');

			$this->template->set_block('_history','row_no_history');
			$this->template->set_block('_history','list');
			$this->template->set_block('_history','row');

			$this->template->set_var('lang_user',lang('User'));
			$this->template->set_var('lang_date',lang('Date'));
			$this->template->set_var('lang_action',lang('Action'));
			$this->template->set_var('lang_new_value',lang('New Value'));

			$this->template->set_var('sort_date',lang('Date'));
			$this->template->set_var('sort_owner',lang('User'));
			$this->template->set_var('sort_status',lang('Status'));
			$this->template->set_var('sort_new_value',lang('New value'));
			$this->template->set_var('sort_old_value',lang('Old value'));

			$values = $this->return_array($filter_out,array(),$orderby,$sort,$record_id);

			if (! is_array($values))
			{
				$this->template->set_var('lang_no_history',lang('No history for this record'));
				$this->template->fp('rows','row_no_history');
				return $this->template->fp('out','list');
			}

			$i = 0;
			foreach ( $values as $value )
			{
				$this->template->set_var('tr_class', $this->nextmatchs->alternate_row_class($i));

				$this->template->set_var('row_date',$GLOBALS['phpgw']->common->show_date($value['datetime']));
				$this->template->set_var('row_owner',$value['owner']);

				if ($this->alternate_handlers[$value['status']])
				{
					eval('\$s = ' . $this->alternate_handlers[$value['status']] . '(' . $value['new_value'] . ');');
					$this->template->set_var('row_new_value',$s);
					unset($s);

					eval('\$s = ' . $this->alternate_handlers[$value['status']] . '(' . $value['old_value'] . ');');
					$this->template->set_var('row_old_value',$s);
					unset($s);
				}
				else
				{
					$this->template->set_var('row_new_value',stripslashes($value['new_value']));
					$this->template->set_var('row_old_value',stripslashes($value['old_value']));
				}

				$this->template->set_var('row_status',$this->types[$value['status']]);

				$this->template->fp('rows','row',True);
			}
			return $this->template->fp('out','list');
		}

	}
