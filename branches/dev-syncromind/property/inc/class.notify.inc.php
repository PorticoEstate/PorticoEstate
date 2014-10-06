<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2011 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Notify - handles notification to contacts related to items across locations.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_notify
	{
		/**
		* @var object $_db Database connection
		*/
		protected $_db;
		
		protected $account;

		var $public_functions = array
		(
			'update_data'	=> true,
		);
		
		/**
		 * Constructor
		 *
		 */

		function __construct()
		{
			$this->_db		= & $GLOBALS['phpgw']->db;
			$this->_join	= & $this->_db->join;
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		/**
		 * Get list of contacts to notify at location item
		 *
		 * @param array $data location_id and location_item_id
		 * @return array content.
		 */

		public function read($data = array())
		{
			if(!isset($data['location_id']) || !isset($data['location_item_id']) || !$data['location_item_id'])
			{
				return array();
			}	

			$location_id = (int) $data['location_id'];
			$location_item_id = $data['location_item_id']; // in case of bigint
			
			$sql = "SELECT phpgw_notification.id, phpgw_notification.contact_id,phpgw_notification.user_id,"
			. " phpgw_notification.is_active,phpgw_notification.entry_date,phpgw_notification.notification_method,"
			. " first_name, last_name"
			. " FROM phpgw_notification"
			. " {$this->_join} phpgw_contact_person ON phpgw_notification.contact_id = phpgw_contact_person.person_id"
			. " WHERE location_id = {$location_id} AND location_item_id = '{$location_item_id}'";
			$this->_db->query($sql,__LINE__,__FILE__);

			$values		= array();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$lang_yes	= lang('yes');
			$lang_no	= lang('no');

			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'id'					=> $this->_db->f('id'),
					'location_id'			=> $location_id,
					'location_item_id'		=> $location_item_id,
					'contact_id'			=> $this->_db->f('contact_id'),
					'is_active'				=> $this->_db->f('is_active'),
					'notification_method'	=> $this->_db->f('notification_method',true),
					'user_id'				=> $this->_db->f('user_id'),
					'entry_date'			=> $GLOBALS['phpgw']->common->show_date($this->_db->f('entry_date'),$dateformat),
					'first_name'			=> $this->_db->f('first_name',true),
					'last_name'				=> $this->_db->f('last_name', true)
				);
			}

			$contacts = CreateObject('phpgwapi.contacts');

			$socommon			= CreateObject('property.socommon');


			foreach ($values as &$entry)
			{
				$comms = execMethod('addressbook.boaddressbook.get_comm_contact_data',$entry['contact_id']);

				$entry['email'] = $comms[$entry['contact_id']]['work email'];
				$entry['sms'] = $comms[$entry['contact_id']]['mobile (cell) phone'];
				$entry['is_active_text'] = $entry['is_active'] ? $lang_yes : $lang_no;

				$sql = "SELECT account_id FROM phpgw_accounts WHERE person_id = " . (int) $entry['contact_id'];
				$this->_db->query($sql,__LINE__,__FILE__);
				if($this->_db->next_record())
				{
					$account_id		= $this->_db->f('account_id');
					$prefs = $socommon->create_preferences('property',$account_id);		

					$entry['email'] = isset($entry['email']) && $entry['email'] ? $entry['email'] : $prefs['email'];
					$entry['sms'] = isset($entry['sms']) && $entry['sms'] ?  $entry['sms'] : $prefs['cellphone'];
				}
			}

			return $values;
		}

		/**
		 * Get definition for an inline YUI table
		 *
		 * @param array $data location and the number of preceding tables in the same page
		 * @return array table def data and prepared content.
		 */

		public function get_yui_table_def($data = array())
		{
			if(!isset($data['count']))
			{
				throw new Exception("property_notify::get_yui_table_def() - Missing count in input");
			}	

			$content = array();

			if(isset($data['location_id']) && isset($data['location_item_id']))
			{
				$content = $this->read($data);
			}	

			$count = (int)$data['count'];
			$datavalues = array
			(
				'name'					=> "{$count}",
				'values' 				=> json_encode($content),
				'total_records'			=> count($content),
				'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'addressbook.uiaddressbook.view_person'))),
				'is_paginator'			=> 1,
				'footer'				=> 0
			);

			$column_defs = array
			(
				'name'		=> "{$count}",
				'values'	=>	json_encode(array(	array('key' => 'id','hidden' => true),
											//		array('key' => 'email','hidden' => true),
											//		array('key' => 'sms','hidden' => true),
													array('key' => 'contact_id','label'=>lang('id'),'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink_notify'),
													array('key' => 'first_name','label'=>lang('first name'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'last_name','label'=>lang('last name'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'email','label'=>lang('email'),'sortable'=>false,'resizeable'=>true),
													array('key' => 'sms','label'=>'SMS','sortable'=>false,'resizeable'=>true),
													array('key' => 'notification_method','label'=>lang('method'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'is_active_text','label'=>lang('active'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'entry_date','label'=>lang('entry_date'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'select','label'=> lang('select'), 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck_notify','width'=>30)
													))
			);

			$buttons = array
			(
				'name'   => "{$count}",
				'values'  => json_encode(array(
					array('id' =>'check_all','type'=>'buttons', 'value'=>'', 'label'=> lang('check all'), 'funct'=> 'check_all_notify' , 'classname'=> 'actionButton', 'value_hidden'=>""),
					array('id' =>'notify[email]','type'=>'buttons', 'value'=>'email', 'label'=> lang('email'), 'funct'=> 'onActionsClick_notify' , 'classname'=> 'actionButton', 'value_hidden'=>""),
					array('id' =>'notify[sms]','type'=>'buttons', 'value'=>'sms', 'label'=> 'SMS', 'funct'=> 'onActionsClick_notify' , 'classname'=> 'actionButton', 'value_hidden'=>""),
					array('id' =>'notify[enable]','type'=>'buttons', 'value'=>'enable', 'label'=> lang('enable'), 'funct'=> 'onActionsClick_notify' , 'classname'=> 'actionButton', 'value_hidden'=>""),
					array('id' =>'notify[disable]','type'=>'buttons', 'value'=>'disable', 'label'=>lang('disable'), 'funct'=> 'onActionsClick_notify' , 'classname'=> 'actionButton', 'value_hidden'=>""),
					array('id' =>'notify[delete]','type'=>'buttons', 'value'=>'delete', 'label'=> lang('Delete'), 'funct'=> 'onActionsClick_notify' , 'classname'=> 'actionButton', 'value_hidden'=>""),
				))
			);

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'notify', 'property' );

			$lang_view = lang('view');
			$code = <<<JS
	var  myPaginator_{$count}, myDataTable_{$count};
	var Button_{$count}_0, Button_{$count}_1, Button_{$count}_2;
	var notify_table_count = {$count};
	var notify_lang_view = "{$lang_view}";
	var notify_lang_alert = "Posten må lagres før kontakter kan tilordnes";

	this.refresh_notify_contact=function()
	{
		if(document.getElementById('notify_contact').value)
		{
			base_java_notify_url['contact_id'] = document.getElementById('notify_contact').value;
		}

		if(document.getElementById('notify_contact').value != notify_contact)
		{
			base_java_notify_url['action'] = 'refresh_notify_contact';
			execute_async(myDataTable_{$count}, base_java_notify_url);
			notify_contact = document.getElementById('notify_contact').value;
		}
	}

	this.onActionsClick_notify=function()
	{
		flag = false;
		//clean hidden buttons actions
		cleanValuesHiddenActionsButtons();

		//validate ckecks true
		array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks');
		for ( var i in array_checks )
		{
			if(array_checks[i].checked)
			{
				flag = true;
				break;
			}
		}

		if(flag)
		{
			//asign value to hidden
			YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

			formObject = document.body.getElementsByTagName('form');
			YAHOO.util.Connect.setForm(formObject[0]);//First form
			base_java_notify_url['action'] = 'refresh_notify_contact';
			execute_async(myDataTable_{$count},base_java_notify_url);
		}
	}
JS;
			$GLOBALS['phpgw']->js->add_code($namespace, $code);

			return array('datavalues' => $datavalues, 'column_defs' => $column_defs, 'buttons' => $buttons);
		}

		public function update_data()
		{
			$action = phpgw::get_var('action', 'string', 'GET');
			switch($action)
			{
				case 'refresh_notify_contact':
					return $this->refresh_notify_contact();
					break;
				default:
			}
		}

		protected function refresh_notify_contact()
		{
			$location_id		= (int)phpgw::get_var('location_id', 'int');
			$location_item_id	= (int)phpgw::get_var('location_item_id', 'int');
			$contact_id			= (int)phpgw::get_var('contact_id', 'int');

			$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

			if( !$GLOBALS['phpgw']->acl->check($location_info['location'], PHPGW_ACL_EDIT, $location_info['appname']))
			{
				return;
			}

			$update = false;
			if($notify = phpgw::get_var('notify'))
			{
				$ids = $notify['ids'];
				if($ids)
				{
					$value_set = array();

					if($notify['email'])
					{
						$value_set['notification_method'] = 'email';
					}	
					else if($notify['sms'])
					{
						$value_set['notification_method'] = 'sms';
					}	
					else if($notify['enable'])
					{
						$value_set['is_active'] = 1;
					}	
					else if($notify['disable'])
					{
						$value_set['is_active'] = '';
					}	
					else if($notify['delete'])
					{
						$sql = "DELETE FROM phpgw_notification WHERE id IN (". implode(',', $ids) . ')';
					}	
					
					if($value_set)
					{
						$value_set	= $this->_db->validate_update($value_set);
						$sql = "UPDATE phpgw_notification SET {$value_set} WHERE id IN (". implode(',', $ids) . ')';
					}
					$this->_db->query($sql,__LINE__,__FILE__);			
				}
				$update = true;
			}

			if($location_id && $location_item_id && $contact_id && !$update)
			{
				$sql = "SELECT id FROM phpgw_notification WHERE location_id = {$location_id} AND location_item_id = {$location_item_id} AND contact_id = {$contact_id}";
				$this->_db->query($sql,__LINE__,__FILE__);
				if(!$this->_db->next_record())
				{
					$values_insert = array
					(
						'location_id'			=> $location_id,
						'location_item_id'		=> $location_item_id,
						'contact_id'			=> $contact_id,
						'is_active'				=> 1,
						'entry_date'			=> time(),
						'user_id'				=> $this->account,
						'notification_method'	=> 'email'
					);
					
					$this->_db->query("INSERT INTO phpgw_notification (" . implode(',',array_keys($values_insert)) . ') VALUES ('
					 . $this->_db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);
				}
			}

			$content = $this->read(array('location_id'=> $location_id,'location_item_id'=> $location_item_id));

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{

				if(count($content))
				{
					return json_encode($content);
				}
				else
				{
					return "";
				}
			}
			return $content;
		}
	}
