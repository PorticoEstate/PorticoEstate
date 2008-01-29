<?php
	/**
	* Trouble Ticket System - business object
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id: class.botts.inc.php 17868 2007-01-09 01:21:53Z skwashd $
	*/


	/**
	* Business object
	* 
	* @package tts
	*/	
	class tts_botts
	{
		/**
		* @var object $cats reference to the categories object
		*/
		var $cats;
		
		/**
		* @var object $custom reference to the custom fields object
		*/
		var $custom;

		/**
		* @var object $db reference to the global database object
		*/
		var $db;

		/**
		* @var array $errors errors which have occured
		*/
		var $errors;
		
		/**
		* @var object $historylog reference to the history log object
		*/
		var $historylog;

		
		/**
		* @var object $so does nothing as bo and so is combined
		*/
		var $so;

		/**
		* @var int $total records - not used
		*/
		var $total_records;

		/**
		* @var array $public_methods the publicly available methods of this class
		*/
		var $public_functions = array
		(
			'get_search_fields'	=> true,
			'list_methods'		=> true,
			'search'			=> true
		);

		/**
		* @constructor
		*/
		function tts_botts()
		{
			$this->db =& $GLOBALS['phpgw']->db;

			$this->cats = createObject('phpgwapi.categories');
			
			$this->custom = createObject('phpgwapi.custom_fields');

			$this->historylog = createobject('phpgwapi.historylog','tts');
			$this->historylog->types = array
			(				
				'A' => 'Re-assigned',
				'B' => 'Billing rate',
				'C'	=> 'Commant',
				'D' => 'Deadline',
				'E' => 'Effort',
				'F' => 'Custom field value',
				'G' => 'Group ownership changed',
				'H' => 'Billing hours',
				'L' => 'Platform',
				'O' => 'Opened',
				'P' => 'Priority changed',
				'R' => 'Re-opened',
				'S' => 'Subject changed',
				'T' => 'Category changed',
				'X' => 'Closed',
				'Y' => 'Type'
			);
		}

		function list_methods($_type)
		{
			if (is_array($_type))
			{
				$_type = $_type['type'];
			}

			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Read this list of methods.')
						),
						'save' => array(
							'function'  => 'save',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Creates a new ticket, returns ticket_id')
						),
						'list' => array(
							'function'  => '_list',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Creates a struct of tickets')
						),
						'read' => array(
							'function'  => '_read',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Returns a struct of values of a single ticket')
						),
						'read_notes' => array(
							'function'  => 'read_notes',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Returns the additional notes attached to a ticket')
						),
						'history' => array(
							'function'  => 'history',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Returns a struct of a tickets history')
						),
						'update' => array(
							'function'  => 'update',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Updates ticket')
						),
						'test' => array(
							'function'  => 'test',
							'signature' => array(array(xmlrpcString)),
							'docstring' => lang('TEST')
						)
						
					);
					return $xml_functions;

				case 'soap':
					return $this->soap_functions;

				default:
					return array();
			}
		}

		function cached_accounts($account_id)
		{
			$this->accounts = CreateObject('phpgwapi.accounts', $account_id);
			$this->accounts->read_repository();

			$cached_data[$this->accounts->data['account_id']]['account_lid'] = $this->accounts->data['account_lid'];
			$cached_data[$this->accounts->data['account_id']]['firstname']   = $this->accounts->data['firstname'];
			$cached_data[$this->accounts->data['account_id']]['lastname']    = $this->accounts->data['lastname'];

			return $cached_data;
		}
		
		function delete($id)
		{
			$id = (int)$id;
			$ret = true;
			$ret &= (! is_null($this->db->query(
				"DELETE FROM phpgw_tts_tickets WHERE ticket_id=$id",
				__LINE__,
				__FILE__
			)));
			$ret &= (! is_null($this->db->query(
				"DELETE FROM phpgw_tts_views WHERE view_id=$id",
				__LINE__,
				__FILE__
			)));
			return $ret;
		}

		/**
		* Read a ticket from the database
		*
		* @param int $id the database id for the ticket
		* @param bool $bypass_check bypass ACL check
		* @return array the ticket values, count(0) == invalid ticket
		*/
		function get_ticket($id, $bypass_check = false)
		{
			$id = (int)$id;
			if ( $id === 0 )
			{
				return array();
			}
			
			$this->_record_view($id);
			
			$ttype = (int)$this->db->adodb->getOne('SELECT ticket_type FROM phpgw_tts_tickets WHERE ticket_id = ' . $id);

			$cats = $this->get_user_cat_list(PHPGW_ACL_READ);
			
			if ( !$bypass_check && ($ttype === 0 || !isset($cats[$ttype]) ) ) //not found, invalid type or no rights
			{
				return array();
			}

			$ticket = $this->db->adodb->getAssoc("SELECT * FROM phpgw_tts_tickets, phpgw_tts_c{$ttype}" .
					" WHERE phpgw_tts_tickets.ticket_id = $id" .
					" AND phpgw_tts_c{$ttype}.ticket_id = phpgw_tts_tickets.ticket_id");
					
			if ( !is_array($ticket) || !count($ticket) )
			{
				return array();
			}
			$ticket = $ticket[$id];//it keys the array with the primary key
			
			$ticket['type'] = $this->cats->id2name($ttype);
			
			$ticket['deadline'] = $ticket['ticket_deadline'];
			if ( !$ticket['ticket_deadline'] )
			{
				$ticket['deadline'] = '';
			}
				
			$ticket['attachment'] = array();

			$ticket['history'] = $this->get_history($id);
			$ticket['notes'] = $this->get_notes($id);
			
			$cached_data = $this->cached_accounts($ticket['ticket_group']);
			$ticket['ticket_group_name'] = isset($cached_data[$ticket['ticket_group']]) ? lang('%1 group', $cached_data[$ticket['ticket_group']]['firstname']) : lang('unknown');
			
			$cached_data = $this->cached_accounts($ticket['ticket_owner']);
			$ticket['ticket_owner_name'] = isset($cached_data[$ticket['ticket_owner']]) ? $GLOBALS['phpgw']->common->display_fullname($cached_data[$ticket['ticket_owner']]['account_lid'],
				$cached_data[$ticket['ticket_owner']]['firstname'], $cached_data[$ticket['ticket_owner']]['lastname']) : lang('unkown');

			$cached_data = $this->cached_accounts($ticket['ticket_assignedto']);
			$ticket['ticket_assignedto_name'] = isset($cached_data[$ticket['ticket_assignedto']]) ? $GLOBALS['phpgw']->common->display_fullname($cached_data[$ticket['ticket_assignedto']]['account_lid'],
				$cached_data[$ticket['ticket_assignedto']]['firstname'],$cached_data[$ticket['ticket_assignedto']]['lastname']) : lang('unassigned');

			return $ticket;
		}

		/**
		* List tickets
		*
		* @param array $param the search criteria
		* @return array the tickets found - empty array for none found
		*/
		function list_tickets($params)
		{
			$order  = isset($param['order']) ? $this->db->db_addslashes($params['order']) : '';
			$sort   = ( isset($param['sort']) && strtolower($params['sort']) == 'asc') ? 'ASC' : 'DESC';
			$sortmethod = strlen($order) > 0 ? "ORDER BY $order $sort" : 'ORDER BY ticket_priority DESC';
					
			$filter = array();
			if( isset($params['filter_prio']) && $params['filter_prio'] != '')
			{
				$filter[] = 'ticket_priority = ' . $this->db->db_addslashes($_POST['ticket']['filter_prio']);
			}
		
			if( isset($params['filter_owner']) && $params['filter_owner'] != '')
			{
				$filter[] = 'ticket_owner = '.(int)$params['filter_owner'];
			}
		
			if( isset($params['filter_assignedto']) && $params['filter_assignedto'] != '')
			{
				$filter[] = 'ticket_assignedto = ' . (int)$params['filter_assignedto'];
			}
		
			if( isset($params['filter_category']) && $params['filter_category'] > 0 )
			{
				$filter[] = 'ticket_category =  ' . (int)$params['filter_category'];
			}
			
			if( isset($params['filter_status']) && $params['filter_status']  != 'none')
			{
				if ( $params['filter_status'] == 'open' )
				{
					$filter[] = "ticket_status='O'";
				}
				else
				{
					$filter[] = "ticket_status='X'";
				}
			}
			if ( isset($params['searchfilter']) && $params['searchfilter'] != '') 
			{		
				$filter[] .= "(ticket_details LIKE '%" . $this->db->db_addslashes($params['searchfilter']) ."%' OR " .
				                 " ticket_subject LIKE '%" . $this->db->db_addslashes($params['searchfilter']) . "'%)";

			}
			
			if ( isset($params['filter_due_before']) && $params['filter_due_before'] > 0 )
			{
				$filter[] = 'ticket_deadline <= ' . (int)$params['filter_due_before'] . ' AND ticket_deadline > 0';
			}

			$cats = $this->get_user_cat_list(PHPGW_ACL_READ);
			$filter[] = ' ticket_category IN (0' . ( count($cats) ? ', ' . implode(', ', $cats) : '') . ')';
			
			$filter[] = '1 = 1';
			
			$filterstring = implode(' AND ', $filter);
			unset($filter);
			
			$sql = 'SELECT * FROM phpgw_tts_tickets'
				. " WHERE {$filterstring} {$sortmethod}";

			$rs = $this->db->adodb->Execute($sql);
			$tickets = $rs->getAssoc(true);

			$this->total_records = count($tickets);
			
			foreach ( $tickets as $tid => $ticket )
			{

				$history_values = $this->historylog->return_array(array(), array('O'), '', '', $tid);

				$cached_data = $this->cached_accounts($ticket['ticket_group']);
				$tickets[$tid]['ticket_group_name'] = isset($cached_data[$ticket['ticket_group']]) ? lang('%1 group', $cached_data[$ticket['ticket_group']]) : lang('unkown');
				
				$cached_data = $this->cached_accounts($ticket['ticket_owner']);
				$tickets[$tid]['ticket_owner_name'] = isset($cached_data[$ticket['ticket_owner']]) ? $GLOBALS['phpgw']->common->display_fullname($cached_data[$ticket['ticket_owner']]['account_lid'],
					$cached_data[$ticket['ticket_owner']]['firstname'], $cached_data[$ticket['ticket_owner']]['lastname']) : lang('unkown');

				$cached_data = $this->cached_accounts($ticket['ticket_assignedto']);
				$tickets[$tid]['ticket_assignedto_name'] = isset($cached_data[$ticket['ticket_assignedto']]) ? $GLOBALS['phpgw']->common->display_fullname($cached_data[$ticket['ticket_assignedto']]['account_lid'],
					$cached_data[$ticket['ticket_assignedto']]['firstname'],$cached_data[$ticket['ticket_assignedto']]['lastname']) : lang('unassigned');

				//$tickets[$tid]['odate'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				//$tickets[$tid]['odate_epoch'] = (int)$history_values[0]['datetime'];
				$tickets[$tid]['status_name'] = $ticket['ticket_status'] == 0 ? lang('open') : lang('closed');
			}
			return $tickets;
		}

		/**
		* Does a ticket ID already exist?
		*
		* @param int $id the ticket ID
		* @return bool does the ticket ID exist?
		*/
		function exists($id)
		{
			$myid = (int)$id;
			$this->db->query(
				"SELECT ticket_id FROM phpgw_tts_tickets WHERE ticket_id=$myid",
				__LINE__,
				__FILE__
			);
			return $this->db->next_record();
		}
		
		/**
		* Get a list of category IDs for the current user
		*
		* @param int $level the requited security level
		* @return array the categories available to the user
		*/
		function get_user_cat_list($level)
		{
			$ret_cats = array();
			$cat_list = $this->cats->return_array('all', 0, false, '', 'ASC', 'cat_name', false);
			foreach ( $cat_list as $cat )
			{
				if ( $GLOBALS['phpgw']->acl->check('C' . $cat['id'], $level, 'tts') )
				{
					$ret_cats[$cat['id']] = $cat['id']; // this way we can use $val[$key] lookups as well as using implode :)`
				}
			}
			return $ret_cats;
		}

		function getIDList($lastmod = -1)
		{
			$mylastmod = (int)$lastmod;
			$sel = 'SELECT ticket_id FROM phpgw_tts_tickets';
			if ($mylastmod >= 0)
			{
				$sel .= " WHERE ticket_lastmod >= $mylastmod";
			}
			$this->db->query($sel, __LINE__, __FILE__);
			$ret = array();
			
			while ($this->db->next_record())
			{
				$ret[] = $this->db->f("ticket_id");
			}
			
			error_log("bo_tts:getIDList: ".print_r($ret, true));
			
			return $ret;
		}

		function get_search_fields($cat_id = -1)
		{
			if ( $cat_id == -1 ) //being called directly by JSON
			{
				$cat_id = isset($_GET['cat_id']) ? (int) $_GET['cat_id'] : 0;
			}
			$fields = $this->get_fields($cat_id, ($cat_id > 0) );

			//echo "<pre>fields == " . print_r($fields, true) . '</pre>';

			$values = array();
			if ( count($fields) )
			{
				foreach ( $fields as $id => $field )
				{
					if ( !$field['search'] )
					{
						continue;
					}

					$values[$id] = array
					(
						'field_name'	=> $field['id'],
						'descr'			=> $field['label'],
						'type'			=> strtoupper($field['type']),
						'is_custom'		=> ($cat_id > 0)
					);

					if ( $field['type'] == 'select' )
					{
						$values[$id]['type'] = 'LOOKUP';
						$values[$id]['lookup_values'] = $field['options'];
					}
				}
			}
			//echo "<pre>values == " . print_r($values, true) . '</pre>';
			return $values;
		}

		function get_field()
		{
			
		}
		
		function get_fields($cat_id, $custom_only = false)
		{
			$fields = array();
			
			if ( !$custom_only )
			{
				$fields[] = array
				(
					'id'		=> 'ticket_id',
					'label'		=> lang('ticket id'),
					'search'	=> true,
					'list'		=> true,
					'type'		=> 'hidden',
					'datatype'	=> 'I',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_type',
					'label'		=> lang('ticket type'),
					'type'		=> 'hidden',
					'search'	=> true,
					'list'		=> false,
					'datatype'	=> 'I',
					'change_key'=> 'T',
					'nullable'	=> False
				);
				
				$fields[] = array
				(
					'id'		=> 'ticket_group',
					'label'		=> lang('user group'),
					'options'	=> $this->_get_groups(),
					'search'	=> true,
					'list'		=> false,
					'type'		=> 'select',
					'datatype'	=> 'I',
					'nullable'	=> True
				);
				
				$fields[] = array
				(
					'id'		=> 'ticket_priority',
					'label'		=> lang('priority'),
					'options'	=> $this->_get_priorities(),
					'type'		=> 'select',
					'search'	=> true,
					'list'		=> false,
					'change_key'=> 'P',
					'datatype'	=> 'I',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_owner',
					'label'		=> lang('reported by'),
					'options'	=> $this->_get_users($cat_id),
					'type'		=> 'hidden',
					'search'	=> true,
					'list'		=> true,
					'datatype'	=> 'I',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_assignedto',
					'label'		=> lang('assigned to'),
					'options'	=> $this->_get_users($cat_id),
					'type'		=> 'select',
					'change_key'=> 'A',
					'search'	=> true,
					'list'		=> true,
					'datatype'	=> 'I',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_subject',
					'label'		=> lang('subject'),
					'type'		=> 'textbox',
					'change_key'=> 'S',
					'search'	=> true,
					'list'		=> true,
					'datatype'	=> 'V',
					'nullable'	=> False
				);

				$fields[] = array
				(
					'id'		=> 'ticket_billable_hours',
					'label'		=> lang('billable hours'),
					'type'		=> 'textbox',
					'change_key'=> 'H',
					'search'	=> true,
					'list'		=> false,
					'datatype'	=> 'N',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_billable_rate',
					'label'		=> lang('hourly rate'),
					'type'		=> 'textbox',
					'change_key'=> 'B',
					'search'	=> true,
					'list'		=> false,
					'datatype'	=> 'N',
					'nullable'	=> True
				);
				
				$fields[] = array
				(
					'id'		=> 'ticket_status',
					'label'		=> lang('status'),
					'options'	=> $this->_get_stati(),
					'type'		=> 'select',
					'search'	=> true,
					'list'		=> true,
					'datatype'	=> 'C',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_deadline',
					'label'		=> lang('deadline'),
					'type'		=> 'date',
					'change_key'=> 'D',
					'search'	=> true,
					'list'		=> true,
					'datatype'	=> 'D',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'	=> 'ticket_effort',
					'label'	=> lang('effort'),
					'type'	=> 'textbox',
					'change_key'=> 'E',
					'search'	=> true,
					'list'		=> false,
					'datatype'	=> 'V',
					'nullable'	=> True
				);
	
				$fields[] = array
				(
					'id'		=> 'ticket_details',
					'label'		=> lang('details'),
					'type'		=> 'memo',
					'change_key'=> 'C',
					'search'	=> true,
					'list'		=> false,
					'datatype'	=> 'M',
					'nullable'	=> False
				);
			}

			$cust_fields = $this->custom->get_attribs('tts', "C{$cat_id}", 0, '', 'ASC', 'attrib_sort', true, true);
			if ( is_array($cust_fields) && count($cust_fields) )
			{
				$i = count($fields);
				foreach ( $cust_fields as $cust_field )
				{
					$fields[$i] = array
					(
						'id'		=> $cust_field['column_name'],
						//'help'	=> lang($cust_field['status_text']),
						'label'		=> lang($cust_field['name']),
						'datatype'	=> $cust_field['datatype'],
						'change_key'=> 'F',
						'search'	=> $cust_field['search'],
						'list'		=> $cust_field['list'],
						'type'		=> $this->_translate_cust_type($cust_field['datatype']),
						'nullable'	=> $cust_field['nullable']
					);
					if ( isset($cust_field['choice']) )
					{
						$fields[$i]['options'] = $cust_field['choice'];
					}
					++$i;
				}
			}
			return $fields;
		}

		// created getter without view-creation or reading it
		function retrieve($id) {
			$myid = (int) $id;
			$this->db->query("select * from phpgw_tts_tickets where ticket_id='" . $myid . "'",__LINE__,__FILE__);
			if (! $this->db->next_record()) {
				return false;
			}
			$ret = array(
				'id'             => (int) $id,
				'group'          => $this->db->f('ticket_group'),
				'priority'       => $this->db->f('ticket_priority'),
				'owner'          => $this->db->f('ticket_owner'),
				'assignedto'     => $this->db->f('ticket_assignedto'),
				'subject'        => $this->db->f('ticket_subject'),
				'category'       => $this->db->f('ticket_category'),
				'billable_hours' => $this->db->f('ticket_billable_hours'),
				'billable_rate'  => $this->db->f('ticket_billable_rate'),
				'status'         => $this->db->f('ticket_status'),
				'details'        => $this->db->f('ticket_details')
			);
			return $ret;
		}

		/**
		* Validate a ticket before it is submitted
		*
		* @param array $values the ticket values as an associative array
		* @return bool any errors?
		*/
		function validate($values, $ignore_custom)
		{
			$ttype = $values['ticket_type'];
			if ( $ignore_custom )
			{
				$ttype = 0;
			}
			$ticket_fields = $this->get_fields($ttype);
			foreach ( $ticket_fields as $field )
			{
				if ( !$field['nullable'] && !(isset($values[$field['id']]) && $values[$field['id']]) )
				{
					echo "{$field['id']} == {$values[$field['id']]}\n";
					$this->errors[$field['id']] = lang('%1 must not be empty', $field['label']);
				}
			}
			print_r($this->errors);
			return !count($this->errors);
		}
		
		function _read($params = '')
		{
			$cat = createobject('phpgwapi.categories');

			// Have they viewed this ticket before ?
			$this->db->query("select count(*) from phpgw_tts_views where view_id='" . $params['id']
					. "' and view_account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
			$this->db->next_record();

			if (! $this->db->f(0))
			{
				$this->db->query("insert into phpgw_tts_views values ('" . $params['id'] . "','"
					. $GLOBALS['phpgw_info']['user']['account_id'] . "','" . time() . "')",__LINE__,__FILE__);
			}

			$this->db->query("select * from phpgw_tts_tickets where ticket_id='" . $params['id'] . "'",__LINE__,__FILE__);
			$this->db->next_record();

			$cached_data = $this->cached_accounts($this->db->f('ticket_owner'));
			$owner = $GLOBALS['phpgw']->common->display_fullname($cached_data[$this->db->f('ticket_owner')]['account_lid'],
				$cached_data[$this->db->f('ticket_owner')]['firstname'],$cached_data[$this->db->f('ticket_owner')]['lastname']);

			$cached_data = $this->cached_accounts($this->db->f('ticket_assignedto'));
			$assignedto = $GLOBALS['phpgw']->common->display_fullname($cached_data[$this->db->f('ticket_assignedto')]['account_lid'],
				$cached_data[$this->db->f('ticket_assignedto')]['firstname'],$cached_data[$this->db->f('ticket_assignedto')]['lastname']);

			$r = array
			(
				'id'             => (int)$this->db->f('ticket_id'),
				'group'          => $this->db->f('ticket_group'),
				'priority'       => $this->db->f('ticket_priority'),
				'owner'          => $owner,
				'assignedto'     => $assignedto,
				'subject'        => $this->db->f('ticket_subject', true),
				'category'       => $cat->id2name($this->db->f('ticket_category')),
				'billable_hours' => $this->db->f('ticket_billable_hours'),
				'billable_rate'  => $this->db->f('ticket_billable_rate'),
				'status'         => $this->db->f('ticket_status'),
				'details'        => $this->db->f('ticket_details', true),
				'odate'          => $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
				'odate_epoch'    => (int)$history_values[0]['datetime'],
				'view'           => $this->db->f('ticket_view'),
				'history_size'   => count($this->historylog->return_array(array('C','O'),array(),'','',$params['id']))
			);
			return $r;			
		}

		function get_notes($ticket_id)
		{
			$r = array();
			$history_array = $this->historylog->return_array(array(),array('C'),'','',$ticket_id);
			foreach ( $history_array as $value )
			{
				$r[] = array
				(
					'note_date'		=> $GLOBALS['phpgw']->common->show_date($value['datetime']),
					'note_user'		=> $value['owner'],
					'note_contents'	=> nl2br($value['new_value'])//i know this really belongs in the UI layer but I am doing it here
				);
			}
			return $r;
		}

		function get_history($ticket_id)
		{
			$r = array();
			// This function needs to make use of the alternate handle option (jengo)
			$history_array = $this->historylog->return_array(array('C'), array(), '', '', $ticket_id);
			foreach ( $history_array as $value )
			{
				$datetime = $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$owner    = $value['owner'];

				$action = lang('unknown');
				if ( isset($this->historylog->types[$value['status']]) )
				{
					$action = $this->historylog->types[$value['status']];
				}

				$new_value = '';
				if ($value['status'] == 'A')
				{
					if (! $value['new_value'])
					{
						$new_value = lang('None');
					}
					else
					{
						$new_value = $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					}
				}
				else if ( $value['status'] == 'X' || $value['status'] == 'O' )
				{
					$value['old_value'] = $GLOBALS['phpgw']->common->show_date($value['old_value']);
					if ( (int) $value['new_value'] > 0 )
					{
						$new_value = $GLOBALS['phpgw']->common->show_date($value['new_value']);
					}
				}
				else if ($value['status'] == 'T')
				{
 					$new_value = $cat->id2name($value['new_value']);
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$new_value = $value['new_value'];
				}
	
				$r[] = array
				(
					'owner'     => $owner,
					'action'    => $action,
					'new_value' => $new_value,
					'old_value' => $value['old_value'],
					'datetime'  => $datetime
				);
			}

			return $r;
		}
		
		/**
		 * Process the data provided by the mailpipe script
		 * 
		 * @todo implement attachment handling
		 * @param array $ticket the ticket data
		 * @param int $handler_id the mail handler id, used for lookups
		 * @return int the new ticket id, 0 = failure
		 */
		function process_mail($msg)
		{
			$sql = 'SELECT tts_cat_id FROM phpgw_tts_email_map WHERE is_active = 1 AND api_handler_id = ' . (int) $msg->handler_id;
			$this->db->query($sql, __LINE__, __FILE__);
			if ( !$this->db->next_record() )
			{
				return false; //invalid or inactive
			}
			$cat_id = $this->db->f('tts_cat_id');

			$matches = array();
			if ( !preg_match('/(\[ticket #(\d+)\])(.*)/', $msg->subject, $matches) ) //is it new?
			{
				if ( !$GLOBALS['phpgw']->acl->check('C' . $cat_id, PHPGW_ACL_ADD, 'tts') )
				{
					return false;
				}

				$ticket = array
				(
					'ticket_group'		=> 0,
					'ticket_priority'	=> 0,
					'ticket_assignedto'	=> 0,
					'ticket_subject'	=> trim($msg->subject),
					'ticket_billable_hours'	=> 0.00,
					'ticket_billable_rate'	=> 0.00,
					'ticket_deadline'	=> time() + 86400, // (60 * 60 * 24) or 1 day
					'ticket_effort'		=> '',
					'ticket_type'		=> $cat_id,
					'ticket_details'	=> $msg->body,
				);
				return !!$this->save($ticket, true, true);
			}
			else
			{
				$ticket = $this->get_ticket($matches[2], true);
				if ( $ticket['ticket_type'] != $cat_id // can't reassign cat via email - for now anyway
					|| (!$GLOBALS['phpgw']->acl->check('C' . $cat_id, PHPGW_ACL_EDIT, 'tts') && $ticket['ticket_owner'] != $GLOBALS['phpgw-info']['user']['account_id'] ) )
				{
					return false;
				}

				$ticket['ticket_subject'] = trim($matches[3]); // trust me :)
				$ticket['ticket_detail'] = trim($msg->body);
				$ticket['ticket_status'] = 'O';//reopen it as someone has posted to it
				return !!$this->edit($ticket['ticket_id'], $ticket);
			}
		}

		/**
		 * Save a ticket to the database
		 */
		function save($ticket, $dohistorylog = true, $ignore_custom = false)
		{
			if ( !$this->validate($ticket, $ignore_custom) )
			{
				return 0;
			}

			$ticket_id = 0;
			$lastmod = isset($ticket['ticket_lastmod']) ? (int) $ticket['ticket_lastmod'] : time();
			$sql = $this->db->adodb->Prepare('INSERT INTO phpgw_tts_tickets('
					. ' ticket_group,'
					. ' ticket_priority,'
					. ' ticket_owner,'
					. ' ticket_assignedto,'
					. ' ticket_subject,'
					. ' ticket_billable_hours,'
					. ' ticket_billable_rate,'
					. ' ticket_status,'
					. ' ticket_deadline,'
					. ' ticket_effort,'
					. ' ticket_type,'
					. ' ticket_details,'
					. ' ticket_lastmod,'
					. ' ticket_lastmod_user)'
				. ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			
			$params = array
			(
				(int) $ticket['ticket_group'],
				(int) $ticket['ticket_priority'],
				(int) $GLOBALS['phpgw_info']['user']['account_id'],
				(int) $ticket['ticket_assignedto'],
				$this->db->db_addslashes($ticket['ticket_subject']),
				(float) $ticket['ticket_billable_hours'],
				(float) $ticket['ticket_billable_rate'],
				'O',
				(int) $ticket['ticket_deadline'],
				$this->db->db_addslashes($ticket['ticket_effort']),
				(isset($ticket['ticket_type']) ? (int) $ticket['ticket_type'] : 0),
				 $this->db->db_addslashes($ticket['ticket_details']),
				$lastmod,
				(int) $GLOBALS['phpgw_info']['user']['account_id']
			);
				
			if ( $GLOBALS['phpgw']->db->adodb->Execute($sql, $params) !== false )
			{
				$ticket_id = $GLOBALS['phpgw']->db->adodb->Insert_ID();
				$ticket['ticket_id'] = $ticket_id;

				$this->_save_custom($ticket['ticket_type'], $ticket);

				//error_log("bo_tts::save - saved $ticket_id");
				//added optional parameter to avoid historylog-entries due sync-addition
				if ($dohistorylog)
				{
					$this->historylog->add('O', $ticket_id, '', time());
				}
			}
			else
			{
				echo "ERROR: sql == $sql<br />\nmessage: " . $GLOBALS['phpgw']->db->adodb->ErrorMsg();
			}

			return $ticket_id;
		}

		/**
		* Search for tickets
		*
		* @param array $args the search arguments
		* @return array the search results, empty array for nothing found
		*/
		function search($args = array() )
		{
			$from_tbls = 'phpgw_tts_tickets';
			if ( $ttype_key = array_search('ttype', $args) )
			{
				$ttype = (int) $args['value'][$ttype_key];
				$from_tbls .= " phpgw_tts_c{$ttype}";
				$ttype_filter = $this->acl->check("C{$ttype}", PHPGW_ACL_READ, 'tts') ? "phpgw_tts_c{$ttype}.ticket_id = phpgw_tts_tickets.ticket_id" : '1 = 0';
				unset($args['value'][$ttype_key]);
			}
			else
			{
				$ttype = 0;
				$ttype_filter = 'phpgw_tts_tickets.ticket_type IN(';
				foreach ( $GLOBALS['phpgw']->acl->get_location_list('tts', PHPGW_ACL_READ) as $loc )
				{
					$ttype_filter .= (int)substr($loc, 1) . ', ';
				}
				$ttype_filter .= "0)";
				//$ttype_filter = 'phpgw_tts_tickets.ticket_type IN(\'' . implode('\',\'', $GLOBALS['phpgw']->acl->get_location_list('tts', PHPGW_ACL_READ)) . '\')';
				
			}

			$fields = $this->get_fields($ttype);

			$show_fields = array();
			$headings = array();
			$search_fields = array();
			foreach ( $fields as $id => $field )
			{
				if ( $field['list'] )
				{
					$show_fields[$id] = $field['id'];
					$headings[$id] = $field['label'];
				}
				if ( $field['search'] )
				{
					$search_fields[$field['id']] = true;
				}
				// this is a needed hack
				if ( $field['id'] == 'ticket_owner' )
				{
					$fields[$id]['type'] = 'select';
				}
			}

			$where = array();
			foreach ( $args['value'] as $key => $val )
			{
				if ( isset($search_fields[$args['field'][$key]]) 
					&& $search_fields[$args['field'][$key]] )
				$where[] = $this->_criteria2sql($args['field'][$key], $args['stype'][$key], $val, $fields);
			}

			$defaults = array('date' => lang('not specified'), 'lookup' => lang('invalid') );

			$concat = ($args['search_type'] == 'AND' ? ' AND ' : ' OR ');

			$sql = 'SELECT ' . implode(',', $show_fields) . " FROM {$from_tbls} WHERE (" . implode($concat, $where) . ") AND {$ttype_filter}";
			//trigger_error("SQL: $sql", E_USER_NOTICE);
			$this->db->query($sql, __LINE__, __FILE__);


			$results = array();
			while ( $this->db->next_record() )
			{
				$id = (int) $this->db->f('ticket_id');
				$results[$id]['view_action'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.view', 'ticket_id' => $id));
				foreach ( $show_fields as $key => $field )
				{
					$results[$id][$field] = $this->db->f($field, true);
					if ( isset($fields[$key]['options']) 
						&& is_array($fields[$key]['options']) )
					{
						$found = false;
						foreach ( $fields[$key]['options'] as $option )
						{
							if ( $option['id'] == $results[$id][$field] )
							{
								$results[$id][$field] = $option['value'];
								$found = true;
							}
						}
						if ( !$found )
						{
							$results[$id][$field] = $defaults['lookup'];
						}
					}
					else if ( $fields[$key]['datatype'] == 'D' )
					{
						if ( $results[$id][$field] == 0 )
						{
							$results[$id][$field] = $defaults['date'];
						}
						else
						{
							$results[$id][$field] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $results[$id][$field]);
						}
					}
				}
			}
			return array('results' => $results, 'headings' => $headings);
		}

		/**
		* Update an existing ticket
		*
		* @param int $ticket_id the ticket id
		* @param array $params the values of the ticket
		* @return int the ticket number, 0 == failure
		*/
		function update($ticket_id, $params)
		{
			if ( !$this->validate($ticket) )
			{
				return 0;
			}
			
			$ticket_id = (int) $ticket_id;
			if ( $ticket_id <= 0 )
			{
				return 0; //invalid ticket
			}
			
			$ticket = $this->get_ticket($ticket_id);

			if ( !count($ticket) )
			{
				return 0;//ticket not found/available
			}
			
			$ticket_fields = $this->get_fields($ticket['ticket_type']);
			if ( !count($ticket_fields) )
			{
				return 0; //no fields - invalid type
			}
			
			//die('<pre>fields:' . print_r($ticket_fields, true) . '</pre>current values<pre>' . print_r($ticket, true) . '</pre>new values<pre>' . print_r($params, true) . '</pre>');

			// Make sure it is a single transaction
			$this->historylog->db = &$this->db;
			$this->db->transaction_begin();

			//stop empty notes being added	
			if ( trim($params['ticket_details']) == '' )
			{
				unset($params['ticket_details']);
			}

			$vals = array();
			foreach ( $ticket_fields as $field )
			{
				if ( isset($params[$field['id']]) 
					&& $ticket[$field['id']] != $params[$field['id']] 
					&& $field['id'] != 'ticket_status' )
				{
					$this->historylog->add($field['change_key'], $ticket_id, $params[$field['id']], $ticket[$field['id']]);
					$vals[$field['id']] = $params[$field['id']];
				}
			}

			if ( isset($_FILES['attachment']) && $_FILES['attachment']['name'] != '')
			{
				$fields_updated = true;
				$attdir = "/tts/$ticket_id";
				$basedir = $GLOBALS['basedir'] . "/tts";
				
				if (!file_exists($basedir . "/" . $attdir))
				{
					$GLOBALS['phpgw']->vfs->override_acl = 1;
					$GLOBALS['phpgw']->vfs->mkdir (array (
							'string' => $attdir,
							'relatives' => array (RELATIVE_ALL)));
					$GLOBALS['phpgw']->vfs->override_acl = 0;
				}
				//FIXME do this properly
				$vfs->override_acl = 1;
				$vfs->cp(array
				(
					'from'		=> $_FILES['attachment']['tmp_name'],
					'to'		=> $attdir . '/' . $_FILES['attachment']['name'],
					'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))
				);
				$vfs->override_acl = 0;
				
				$this->historylog->add('M',$ticket_id,$_FILES['attachment']['name'],'');
			}

			if($params['ticket_status'] != $ticket['ticket_status'])
			{
				//only allow assigned-to or admin members to close tickets
				if ( $GLOBALS['phpgw_info']['user']['account_id'] == $ticket['ticket_assignedto']
					|| isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
				{
					$GLOBALS['phpgw']->historylog->add($param['ticket_status'], $ticket_id, $ticket['ticket_status'], $param['ticket_status']);
					$vals['ticket_status'] = $param['ticket_status'];
				}
				else
				{
					$messages .= '<br>You can only close a ticket if it is assigned to you.';
					$GLOBALS['phpgw']->session->appsession('messages','tts',lang($messages));
				}
			}

	
			if ( count($vals) )
			{
				$vals['lastmod'] = time();
				$vals['lastmod_user'] = $GLOBALS['phpgw_info']['user']['account_id'];

				$this->db->adodb->AutoExecute('phpgw_tts_tickets', $vals, 'UPDATE', 'ticket_id = ' . (int)$ticket_id, true, get_magic_quotes_gpc());
				$this->_save_custom($ticket['ticket_type'], $params);

				// Do this before we go into mail_ticket()
				$GLOBALS['phpgw']->db->transaction_commit();
	
				$GLOBALS['phpgw']->session->appsession('messages','tts',lang('Ticket has been updated'));
	
				$GLOBALS['phpgw']->config = createObject('phpgwapi.config', 'tts');
				if($GLOBALS['phpgw']->config->config_data['mailnotification'])
				{
					mail_ticket($ticket_id);
				} 
			}

		}

		/**
		* Convert a search criteria entry to a snippet of SQL
		*/
		function _criteria2sql($field_name, $stype, $val, $field_list)
		{
			$sql = '1 = 1';
			foreach ( $field_list as $entry )
			{
				if ( $entry['id'] == $field_name 
					&& $entry['search'] )
				{
					$is_string = '';
					$sql = $this->db->db_addslashes($field_name) . ' ';
					switch ( strtolower($entry['datatype']) )
					{
						case 'i': //int
						case 'r': //radio boxes
						case 'lb': //select
						case 'ch': //checkboxes
						case 'ab': //addressbook
						case 'vendor':
							$val = (int) $val;
							break;
						case 'f': //float
							$val = (float) $val;
							break;
						case 'c': //char
						case 'v': //varchar
						case 'email':
						case 'link':
						default: //anything else
							$val = $this->db->db_addslashes($val);
							$is_string = true;
					}

					switch ( $stype )
					{
						case 'is_not':
						case 'not_equals':
							$sql = $is_string ? "$field_name != '$val'" : "$field_name != $val";
							break;
						case 'before':
						case 'less_than':
							$sql = "$field_name < $val";
							break;
						case 'after':
						case 'greater_than':
							$sql = "$field_name > $val";
							break;
						case 'contains':
							$sql = "$field_name LIKE '%$val%'";
							break;
						case 'not_contains':
							$sql = "$field_name NOT LIKE '%$val%'";
							break;
						case 'starts':
							$sql = "$field_name LIKE '$val%'";
							break;
						case 'not_starts':
							$sql = "$field_name NOT LIKE '$val%'";
							break;
						case 'ends':
							$sql = "$field_name LIKE '%$val'";
							break;
						case 'not_ends':
							$sql = "$field_name NOT LIKE '%$val'";
							break;
						case 'is':
						case 'equals':
						default:
							$sql = $is_string ? "$field_name = '$val'" : "$field_name = $val";
					}
				}
			}
			return $sql;
		}

		/**
		* Get a list of groups
		*
		* @return array list of groups
		*/
		function _get_groups()
		{
			$groups = array();
			$accts = createObject('phpgwapi.accounts');
			foreach ( $accts->get_list('groups') as $group )
			{
				$groups[] = array
				(
					'id' => $group['account_id'],
					'value' => $GLOBALS['phpgw']->common->display_fullname($group['account_id'], $group['account_firstname'], $group['account_lastname'])
				);
			}
			return $groups;
		}

		/**
		* Get a list of available priorities
		*
		* @return array the priorities
		*/
		function _get_priorities()
		{
			return array
			(
				array('id' => '0', 'value' => lang('none')),
				array('id' => '1', 'value' => '1 - ' . lang('lowest')),
				array('id' => '2', 'value' => '2'),
				array('id' => '3', 'value' => '3'),
				array('id' => '4', 'value' => '4'),
				array('id' => '5', 'value' => '5 - ' . lang('medium')),
				array('id' => '6', 'value' => '6'),
				array('id' => '7', 'value' => '7'),
				array('id' => '8', 'value' => '8'),
				array('id' => '9', 'value' => '9'),
				array('id' => '10', 'value' => '10 - ' . lang('highest'))
			); 
		}
		
		/**
		* Get a list of available stati
		*
		* @return array the stati
		*/
		function _get_stati()
		{
			return array
			(
				array('id' => 'O', 'value' => lang('open')),
				array('id' => 'X', 'value' => lang('closed')),
			);
		}
		
		/**
		 * Get a list of users who have edit access to a ticket type
		 * 
		 * @param int $cat_id the category to check
		 * @return array list of users 
		 */
		function _get_users($cat_id = -1)
		{
			if ( $cat_id = -1 )
			{
				$accounts = array();
				foreach ( $GLOBALS['phpgw']->accounts->get_list() as $acct )
				{
					$accounts[] = array('id' => $acct['account_id'], 'value' => $GLOBALS['phpgw']->common->display_fullname($acct['account_lid'], $acct['account_firstname'], $acct['account_lastname']) );
				}
				return $accounts;
			}
			
			if ( $cat_id == 0 )
			{
				$loc = '.';
			}
			else
			{
				$loc = "C{$cat_id}";
			}

			$users = array();
			$grants = $GLOBALS['phpgw']->acl->get_ids_for_location($loc, PHPGW_ACL_EDIT, 'tts');
			if ( is_array($grants ) && count($grants) )
			{
				foreach ( $grants as $uid )
				{
					if ( $GLOBALS['phpgw']->accounts->get_type($uid) == 'g' )
					{
						$GLOBALS['phpgw']->accounts->account_id = $uid;
						$accts = $GLOBALS['phpgw']->accounts->get_members();
						foreach ( $accts as $acct )
						{
							$users[] = array('id' => $acct, 'value' => $GLOBALS['phpgw']->accounts->id2name($acct));
						}
					}
					else
					{
						$users[] = array('id' => $uid, 'value' => $GLOBALS['phpgw']->accounts->id2name($uid));
					}
				}
			}
			return $users;
		}

		//FIXME make this work again and use phpmailer
		function _mail_ticket($ticket_id)
		{
			return ;
			// $GLOBALS['phpgw']->preferences->read_repository();
			// $GLOBALS['phpgw_info']['user']['preferences']['tts']['mailnotification']

			$GLOBALS['phpgw']->config->read_repository();

			if ($GLOBALS['phpgw']->config->config_data['mailnotification'])
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
	
				$this->db->query('select t_id,t_category,t_detail,t_priority,t_user,t_assignedto,'
					. "t_timestamp_opened, t_timestamp_closed, t_subject from phpgw_tts_tickets where t_id='".$ticket_id."'",__LINE__,__FILE__);
				$this->db->next_record();
    
				$group = $this->db->f('t_category');
			
				// build subject
				$subject = "[TTS {$group} #{$ticket_id}] "
						. (!$this->db->f('t_timestamp_closed')? lang('updated') : lang('closed') )
						. ': ' . $this->db->f('t_subject', true);

				// build body
				$body  = '';
				$body .= 'TTS #'.$ticket_id."\n\n";
				$body .= 'Subject: '.$this->db->f('t_subject')."\n\n";
				$body .= 'Assigned To: '.$this->db->f('t_assignedto')."\n\n";
				$body .= 'Priority: ' . $this->db->f('t_priority') . "\n\n";
				$body .= 'Group: ' . $group."\n\n";
				$body .= 'Opened By: ' . $this->db->f('t_user')."\n";
				$body .= 'Date Opened: '.$GLOBALS['phpgw']->common->show_date($this->db->f('t_timestamp_opened'))."\n\n";
				if($this->db->f('t_timestamp_closed'))
				{
					$body .= 'Date Closed: '.$GLOBALS['phpgw']->common->show_date($this->db->f('t_timestamp_closed'))."\n\n";
				}
				$body .= stripslashes(strip_tags($this->db->f('t_detail')))."\n\n.";
			
				$members = array();
				if ($GLOBALS['phpgw']->config->config_data['groupnotification']) 
				{
					// select group recipients
					$group_id = $GLOBALS['phpgw']->accounts->name2id($group);
					$members  = $GLOBALS['phpgw']->accounts->members($group_id);
				}

				if ($GLOBALS['phpgw']->config->config_data['ownernotification'])
				{
					// add owner to recipients
					$members[] = array('account_id' => $GLOBALS['phpgw']->accounts->name2id($this->db->f('t_user')), 'account_name' => $this->db->f('t_user'));
				}

				if ($GLOBALS['phpgw']->config->config_data['assignednotification'])
				{
					// add assigned to recipients
					$members[] = array('account_id' => $GLOBALS['phpgw']->accounts->name2id($this->db->f('t_assignedto')), 'account_name' => $this->db->f('t_assignedto'));
				}

				$toarray = Array();
				$i=0;
				for ($i=0;$i<count($members);$i++)
				{
					if ($members[$i]['account_id'])
					{
						$prefs = $GLOBALS['phpgw']->preferences->create_email_preferences($members[$i]['account_id']);
						$toarray[$prefs['email']['address']] = $prefs['email']['address'];
					}
				}
				if(count($toarray) > 1)
				{
					@reset($toarray);
					$to = implode(',',$toarray);
				}
				else
				{
					$to = current($toarray);
				}

				$rc = $GLOBALS['phpgw']->send->msg('email', $to, $subject, stripslashes($body), '', $cc, $bcc);
				if (!$rc)
				{
					echo  'Your message could <B>not</B> be sent!<BR>'."\n"
						. 'The mail server returned:<BR>'
						. "err_code: '".$GLOBALS['phpgw']->send->err['code']."';<BR>"
						. "err_msg: '".htmlspecialchars($GLOBALS['phpgw']->send->err['msg'])."';<BR>\n"
						. "err_desc: '".$GLOBALS['phpgw']->err['desc']."'.<P>\n"
						. 'To go back to the msg list, click <a href="'.$GLOBALS['phpgw']->link('/tts/index.php','cd=13').'">here</a>';
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}
		}
		
		function _record_view($id)
		{
			// Have they viewed this ticket before ?
			$this->db->query('SELECT COUNT(view_id) AS cnt_views FROM phpgw_tts_views'
					. " WHERE view_id=$id"
					. " AND view_account_id={$GLOBALS['phpgw_info']['user']['account_id']}", __LINE__, __FILE__);
			$this->db->next_record();
	
			if(!$this->db->f('cnt_views'))
			{
				$this->db->query('INSERT INTO phpgw_tts_views'
					. " VALUES ({$id}, {$GLOBALS['phpgw_info']['user']['account_id']}, " . time() . ')', __LINE__, __FILE__);
			}
		}
		
		/**
		 * Save the custom field values for a ticket
		 */
		function _save_custom($ttype, $values)
		{
			$tbl_name = 'phpgw_tts_c' . $ttype;
			if ( $this->db->adodb->getOne("SELECT COUNT(ticket_id) FROM $tbl_name WHERE ticket_id = {$values['ticket_id']}") > 0 )
			{
				$this->db->adodb->autoExecute($tbl_name, $values, 'UPDATE', 'ticket_id = ' . (int)$values['ticket_id'], false, get_magic_quotes_gpc() );
			}
			else //is new
			{
				$this->db->adodb->Execute($this->db->adodb->GetInsertSQL($tbl_name, $values, get_magic_quotes_gpc() ) );
				// auto execute is broken :(
				//$this->db->adodb->autoExecute('phpgw_tts_c' . $ttype, $values, 'INSERT', null, true, get_magic_quotes_gpc() );
			}
		}
		
		/**
		 * Translates custom field types into html form element types
		 */
		function _translate_cust_type($dt)
		{
			switch (strtolower($dt) )
			{
				case 'lb':
				case 'r':
					return 'select';

				case 'd':
					return 'date';

				case 't':
					return 'memo';

				case 'v':
				case 'i':
				case 'c':
				case 'f':
				case 'email':
				case 'link':
				default:
					return 'textbox';
			}
		}
	}
?>
