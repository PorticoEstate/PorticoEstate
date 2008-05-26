<?php
	/**
	* Trouble Ticket System
	*
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id$
	*/
	
	class tts_uitts
	{
		/**
		 * @var object $bo reference to the tts business logic class
		 */
		 var $bo;

		/**
		 * var object $historylog reference the history log obejct
		 */
		 var $historylog;

		/**
		 * @var array $public_functions the publicly accessible methods of the class
		 */
		var $public_functions = array
		(
			'add_ticket'	=> true,
			'edit_ticket'	=> true,
			'get_cats'		=> true,
			'get_users'		=> true,
			'index'			=> true,
			'search'		=> true,
			'view'			=> true
		);
		
		/**
		 * @var object $t refernce to global template object
		 */
		 var $t;
		
		/**
		 * @constructor
		 */
		function tts_uitts()
		{
			$this->bo = createObject('tts.botts');

			$this->historylog =& $this->bo->historylog;

			$this->t =& $GLOBALS['phpgw']->template;
			$this->t->set_root(PHPGW_APP_TPL);
		}
		
		/**
		* Add a new trouble ticket
		*/
		function add_ticket()
		{
			if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$GLOBALS['phpgw']->xslttpl->add_file('select_ticket');
				
				$elements = array('lang' => array(), 'ticket_type' => array() );
				
				$cats = createObject('phpgwapi.categories', $GLOBALS['phpgw_info']['user']['account_id'], 'tts');
				$cat_list = $cats->return_array('all', 0, false, '', 'ASC', 'cat_name', false);
				foreach ( $cat_list as $cat )
				{
					if ( $GLOBALS['phpgw']->acl->check('C' . $cat['id'], PHPGW_ACL_ADD, 'tts') )
					{
						$elements['ticket_type'][] = array
						(
							'id'	=> $cat['id'],
							'value'	=> $cat['name']
						);
					}
				}

				$elements['lang'] = array
				(
					'cancel'		=> lang('cancel'),
					'next'			=> lang('next >'),
					'ticket_type'	=> lang('ticket_type')
				);

				$elements['url_form_action'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.add_ticket', 'id' => 0));

				$GLOBALS['phpgw']->xslttpl->set_var('select_ticket', $elements);

				$GLOBALS['phpgw']->common->phpgw_exit(true);
			}
			elseif ( $_SERVER['REQUEST_METHOD'] == 'POST' )
			{
				if ( !isset($_POST['ticket_type'])
					|| !$GLOBALS['phpgw']->acl->check('C' . (int) $_POST['ticket_type'], PHPGW_ACL_ADD, 'tts') )
				{
					$this->_access_denied();
				}


				$ticket = array
				(
					'ticket_id'		=> 0,
					'ticket_type'	=> (int) $_REQUEST['ticket_type']
				);
				
				if ( isset($_POST['submit']) && $_POST['submit'] )
				{
					$id = $this->bo->save($_POST);
					if ($id > 0)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'tts.uitts.view', 'ticket_id' => $id));
						exit;
					}
				}

				if ( isset($_POST['cancel']) && $_POST['cancel'] )
				{
					$GLOBALS['phpgw']->redirect_link('/tts/index.php');
					exit;
				}
				
				$this->_render_add_form($ticket);
				$GLOBALS['phpgw']->common->phpgw_footer();
				exit;
			}
			die('invalid request');
		}
		
		function edit_ticket()
		{
		}
		
		
		function get_users()
		{
			Header('Content-Type: text/javascript; charset=utf-8');
			$group_id = (int) $_GET['group_id'];
			$accounts = createObject('phpgwapi.accounts');
			$members = $accounts->member($group_id);
			echo json_encode($members);
			exit;
		}
		
		/**
		 * Display the index listing
		 */
		function index()
		{
			$js =& $GLOBALS['phpgw']->js;
			$js->validate_file('core', 'base');
			$js->validate_file('sortabletable', 'sortabletable');
			$js->validate_file('tabs', 'tabs');
			$js->validate_file('yahoo', 'yahoo');
			$js->validate_file('yahoo', 'dom');
			$js->validate_file('yahoo', 'event');
			$js->validate_file('yahoo', 'dragdrop');
			$js->validate_file('yahoo', 'connection');
			$js->validate_file('yahoo', 'container');
			$js->validate_file('base', 'index', 'tts');
			
			$js->add_event('load', 'ttsIndexOnLoad()');

			if ( !isset($GLBOALS['phpgw_info']['flags']['css']) )
			{
				$GLOBALS['phpgw_info']['flags']['css'] = '';
			}
			$GLOBALS['phpgw_info']['flags']['css'] = "@import url('rostering/templates/base/css/base.css');\n";

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw']->xslttpl->add_file('index');
		
			$values = array
			(
				'img' => array
				(
					'goto'		=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_jump_to', '.png', false),
					'new'		=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_new', '.png', false),
					'prefs'		=> $GLOBALS['phpgw']->common->image('phpgwapi', 'settings', '.png', false),
					'search'	=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_search', '.png', false),
				),
				'lang' => array
				(
					'clear'			=> lang('clear'),
					'close_ticket'	=> lang('close ticket'),	
					'filter'		=> lang('filter'),
					'find'			=> lang('find'),
					'goto'			=> lang('go to'),
					'invalid'		=> lang('invalid ticket id, please try again'),
					'new'			=> lang('new'),
					'open'			=> lang('open'),
					'overdue'		=> lang('overdue'),
					'preferences'	=> lang('preferences'),
					'search'		=> lang('search'),
					'ticket_no'		=> lang('ticket number')
				),
				'url' => array
				(
					'new_ticket'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.add_ticket')),
					'prefs'			=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'tts')),
					'search'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.search')),
					'view'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.view') ),
					'goto_action'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.view') )
				),
				'open_tickets' => $this->bo->search(array
				(
					'field'			=> array('ticket_status'),
					'stype'			=> array('is'),
					'value'			=> array('O'),
					'search_type'	=> 'AND'
				)),
				'overdue_tickets' => $this->bo->search(array
				(
					'field'			=> array('ticket_status', 'ticket_deadline', 'ticket_deadline'),
					'stype'			=> array('is', 'before', 'is_not'),
					'value'			=> array('O', time(), 0),
					'search_type'	=> 'AND'
				))
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('index' => $values));
			$GLOBALS['phpgw']->common->phpgw_exit(true);



		
		
				//$this->t->set_var('tts_searchfilter', isset($_REQUEST['searchfilter']) ? $_REQUEST['searchfilter'] : '');
				//$this->t->set_var('tts_numfound',lang('Tickets found %1',$numfound));
			
				//$this->t->set_var('tts_ticketstotal', lang('Tickets total %1',$numtotal));
				//$this->t->set_var('tts_ticketsopen', lang('Tickets open %1',$numopen));
				
				$tickets = $this->bo->list_tickets(array('filter_due_before' => time(), 'filter_status' => 'open'));
				$this->_render_list($tickets, 'tickets_overdue', 'overdue', 'overdue', 'overdue_list');
				
				$tickets = $this->bo->list_tickets(array('filter_status' => 'open'));
				$this->_render_list($tickets, 'tickets_open', 'status', 'ticket', 'open_list');
				
				$GLOBALS['phpgw']->common->phpgw_header(true);
	
				$this->t->pfp('out','index');
			
				$GLOBALS['phpgw']->common->phpgw_footer();
			}

			function view()
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				if ( !isset($_REQUEST['ticket_id'])
					|| (isset($_POST['cancel']) && $_POST['cancel']) ) 
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'tts.uitts.index'));
				}

				if ( isset($_POST['submit'])  && $_POST['submit'] )
				{
					$this->bo->update($_POST['ticket_id'], $_POST);
				}

				$js =& $GLOBALS['phpgw']->js;
				$js->validate_file('tabs', 'tabs');
				$js->validate_file('base', 'view', 'tts');
				
				$values = array
				(
					'edit'		=> false,
					'history'	=> array(),
					'notes'		=> array(),
					'ticket'	=> $this->bo->get_ticket($_REQUEST['ticket_id']),
					'view'		=> array()
				);

				if ( count($values['ticket']['history']) )
				{
					$values['history'] = $values['ticket']['history'];
				}
				unset($values['ticket']['history']);

				if ( count($values['ticket']['notes']) )
				{
					$values['notes'] = $values['ticket']['notes'];
				}
				unset($values['ticket']['notes']);

				//echo '<pre>' . print_r($values['ticket'], true) . '</pre>';

				if ( !$GLOBALS['phpgw']->acl->check('C' . (int) $values['ticket']['ticket_type'], PHPGW_ACL_READ, 'tts') )
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'tts.uitts.index'));
				}

				if ( $GLOBALS['phpgw']->acl->check('C' . (int) $values['ticket']['ticket_type'], PHPGW_ACL_EDIT, 'tts') )
				{
					$values['edit'] = true;
				}
				$js->add_event('load', "oTabs = new Tabs(" . ($values['edit'] ? 4 : 3) .", 'activetab', 'inactivetab', 'tab', 'tabcontent');\n\toTabs.display(1);");

				$GLOBALS['phpgw']->xslttpl->add_file('viewticket_details');
				$ui = createObject('phpgwapi.ui_tools');

				$fields = $this->bo->get_fields($values['ticket']['ticket_type']);
				foreach ( $fields as $key => $field )
				{
					switch ( $field['type'] )
					{
						case 'hidden':
							$fields[$key]['value'] = $values['ticket'][$field['id']];
							continue; //ignore it

						case 'select':
							$values['view'][] = array
							(
								'label'	=> $field['label'],
								'value'	=> $this->_get_selection($values['ticket'][$field['id']], $field['options'])
							);
							$fields[$key]['selected'] = $values['ticket'][$field['id']];
							break;

						case 'date':
							$values['view'][] = array
							(
								'label'	=> $field['label'],
								'value'	=> date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $values['ticket'][$field['id']])
							);
							$fields[$key] = $ui->date($field['id'], $field['label'], $values['ticket'][$field['id']]);
							break;

						default:
							$values['view'][] = array
							(
								'label'	=> $field['label'],
								'value'	=> $values['ticket'][$field['id']]
							);
							$fields[$key]['value'] = $values['ticket'][$field['id']];
					}
				}

				if ( $values['edit'] )
				{
					$values['form_elements'] = array('form_elm' => $fields);
				}
				unset($fields);

				$values['form_action'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.view'));
				$values['done_url'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.index'));
				$values['lang'] = array
				(
					'action'	=> lang('action'),
					'done'		=> lang('done'),
					'date'		=> lang('date'),
					'history'	=> lang('history'),
					'new_value'	=> lang('new value'),
					'no_history'=> lang('no history available'),
					'no_rights'	=> lang('you do not have the required rights to edit this ticket'),
					'notes'		=> lang('notes'),
					'old_value'	=> lang('old value'),
					'save'		=> lang('save'),
					'status'	=> lang('status'),
					'summary'	=> lang('summary'),
					'update'	=> lang('update'),
					'user'		=> lang('user')
				);

				unset($values['ticket']);

				$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('view_ticket' => $values));
				$GLOBALS['phpgw']->common->phpgw_exit(true);
			}

		/**
		* Search for a ticket
		*/
		function search()
		{
			if ( isset($_GET['search_mode']) )
			{
				if ( $_GET['search_mode'] == 'adv' )
				{
					$results = $this->bo->search((array) $_GET);
				}
				else
				{
					$results = array();
				}

				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$GLOBALS['phpgw']->xslttpl->add_file('search_results');

				$results['lang'] = array
				(
					'search_results'	=> lang('search results')
				);

				$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('search_results' => $results) );
				$GLOBALS['phpgw']->common->phpgw_exit(true);
			}

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw']->xslttpl->add_file('search');

			$js =& $GLOBALS['phpgw']->js;
			$js->validate_file('base', 'search', 'tts');
			$js->validate_file('json', 'json');
			$js->add_event('load', "loadFields(0, true);");

			$values = array();

			$values['lang'] = array
			(
				'add'				=> lang('add'),
				'advanced'			=> lang('advanced'),
				'edit'				=> lang('edit'),
				'find_all'			=> lang('find all items that meet the following criteria'),
				'find_items'		=> lang('find items'),
				'go'				=> lang('go'),
				'if_all'			=> lang('if all criteria are met'),
				'if_any'			=> lang('if any criteria are met'),
				'saved_searches'	=> lang('saved searches'),
				'search'			=> lang('search'),
				'search_criteria'	=> lang('Search criteria'),
				'search_name'		=> lang('search name'),
			);

			$values['url'] = array
			(
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('search' => $values) );
			$GLOBALS['phpgw']->common->phpgw_exit(true);
		}

		/**
		* Inform users that they have been denied access
		*/
		function _access_denied()
		{
			trigger_error(lang('You do not have access to that record type! This attempted access has been reported'), E_USER_ERROR);
		}
		
		function _edit_fields($fields, $values)
		{
			foreach ( $fields as $key => $val )
			{
				if ( isset($values[$val['id']]) )
				{
					$fields[$key]['value'] = $values[$val['id']];
				}

				if ( $val['type'] == 'date' )
				{
					if ( !isset($values[$val['id']]) )
					{
						$values[$val['id']] = time();
					}

					$ui = createObject('phpgwapi.ui_tools');
					$fields[$key] = $ui->date($val['id'], $val['label'], $values[$val['id']]);
				}
			}
			
		
			$fields[] = array
			(
				'id'	=> 'br',
				'type'	=> 'break'
			);
			
			$fields[] = array
			(
				'id'	=> 'cancel',
				'type'	=> 'button',
				'value'	=> lang('cancel')
			);

			$fields[] = array
			(
				'id'	=> 'submit',
				'type'	=> 'button',
				'value'	=> lang('save')
			);

			
			$fields[] = array
			(
				'id'	=> 'br',
				'type'	=> 'break'
			);

			//echo '<pre>' . print_r($fields, true) . '</pre>';

			$invalids =& $this->bo->errors;
			foreach ( $fields as $key => $field )
			{
				if ( isset($invalids[$field['id']]) )
				{
					$fields[$key]['class'] = 'error';
				}
			}
			
			return array('form_elm' => $fields);
		}

		function _get_selection($selected, $options)
		{
			if ( !is_array($options) || !count($options) )
			{
				return '';
			}

			foreach ( $options as $option )
			{
				if ( $option['id'] == $selected )
				{
					return $option['value'];
				}
			}

			return '';
		}

		function _invalid_request()
		{
			die('<pre>' . print_r($_REQUEST, true) . '</pre>');
		}
	
		
		function _render_add_form($ticket, $invalids = array() )
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$ui = createObject('phpgwapi.ui_tools');
			
			if ( isset($_REQUEST['ticket_type']) && $_REQUEST['ticket_type'] > 0  )
			{
				$ticket['cat_id'] = (int) $_REQUEST['ticket_type'];
			}
			else
			{
				$this->_invalid_request();
			}

			$fields = $this->bo->get_fields($ticket['cat_id']);
			$form = array
			(
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.add_ticket')),
				'form_elements'	=> $this->_edit_fields($fields, $ticket)
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $form));
		}
		
		function _render_list($records, $tbl_id, $status_prefix, $col_prefix, $target)
		{
			$cats = createObject('phpgwapi.categories');
			$this->t->set_var(
				array
				(
					'col_prefix'		=> $col_prefix,
					'img_close'			=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_close', '.png', false),
					'lang_id'			=> lang('ID'),
					'lang_subject'		=> lang('subject'),
					'lang_opened'		=> lang('opened'),
					'lang_category'		=> lang('category'),
					'lang_assignedto'	=> lang('assigned to'),
					'lang_openedby'		=> lang('opened by'),
					'lang_status'		=> lang('status'),
					'status_prefix'		=> $status_prefix,
					'table_id'			=> $tbl_id
				));

			if ( !count($records) )
			{
				$this->t->set_var($target, '', true);
				return false;
			}

			$first_pass = true;
			foreach ( $records as $record )
			{
				$status_class = "{$status_prefix}_{$record['ticket_priority']}";
				$this->t->set_var(
				array
				(
					'assignedto_name'	=> $record['ticket_assignedto_name'],
					'cat_name'			=> $cats->id2name($record['ticket_category']),
					'group_name'		=> $record['ticket_group_name'],
					'owner_name'		=> $record['ticket_owner_name'],
					'subject'			=> htmlspecialchars($record['ticket_subject']),
					'status'			=> $record['status_name'],
					'status_id'			=> $record['ticket_status'],
					'ticket_id'			=> $record['ticket_id'],
					'url_ticket'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.view', 'ticket_id' => $record['ticket_id']) ),
					'url_close'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.uitts.view', 'ticket_id' => $record['ticket_id'], 'status' => 'X') )
				));
	
				$history_values = $this->historylog->return_array(array(), array('O'), 'history_timestamp', 'ASC', $record['ticket_id']);
				$this->t->set_var('opened', $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']),
																												$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
																												));
				$this->t->parse('tts_rows', 'tts_row', !$first_pass);
				$first_pass = false;
 			}
			$this->t->parse($target, 'list', true);
			return true;
		}
	}
?>
