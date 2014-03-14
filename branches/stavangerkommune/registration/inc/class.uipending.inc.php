<?php
	/**
	* phpGroupWare - registration
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package registration
 	* @version $Id: class.uicheck_list.inc.php 8628 2012-01-21 10:42:05Z vator $
	*/

	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon');

	class registration_uipending extends phpgwapi_uicommon
	{
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;
		var $config;
	
		private $so_control_area;
		private $so_control;
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;

		var $public_functions = array
		(
			'index'								=> true,
			'index2'								=> true,
			'query'								=> true,
			'edit'						 		=> true
		);

		function __construct()
		{
			parent::__construct();
		
			$this->bo					= CreateObject('registration.bopending',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$c = createobject('phpgwapi.config','registration');
			$c->read();
			$this->config = $c->config_data;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->status_id			= $this->bo->status_id;
			$this->allrows				= $this->bo->allrows;
		
			self::set_active_menu('registration::pending');
		}

		function index()
		{

			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			if($values = phpgw::get_var('values'))
			{
				$values['pending_users'] = isset($values['pending_users']) && $values['pending_users'] ? array_unique($values['pending_users']) : array();
				$values['pending_users_orig'] = isset($values['pending_users_orig']) && $values['pending_users_orig'] ? array_unique($values['pending_users_orig']) : array();
				
				$this->bo->approve_users($values);
				if(isset($values['process_user']) && $values['process_user'])
				{
					$this->bo->process_users($values);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'registration.uipending.index'));
			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json')
				{
					return $this->query();
				}

				$status_list = array
				(
					array
					(
						'id'	=> 0,
						'name'	=> lang('Select status')
					),
					array
					(
						'id'	=> 1,
						'name'	=> lang('approved')
					),
					array
					(
						'id'	=> 2,
						'name'	=> lang('pending')
					),
				);
		
				$data = array(
					'filter_form' 				=> array(
						'status_list' 			=> array('options' => $status_list)
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'registration.uipending.query', 'phpgw_return_as' => 'json')),
						'field' => array(
							array(
								'key' => 'id',
								'hidden' => true
							),
							array(
								'key' => 'reg_id',
								'label' => lang('id'),
								'sortable'	=> true,
								'formatter' => 'formatLinkPending'
							),
							array(
								'key'	=>	'reg_lid',
								'label'	=>	lang('user'),
								'sortable'	=>	true
							),
							array(
								'key' => 'reg_dla',
								'label' => lang('time'),
								'sortable'	=> true
							),
							array(
								'key' => 'reg_approved',
								'label' => lang('approved'),
								'sortable'	=> true,
								'formatter' => 'FormatterCenter'
							),
							array(
								'key' => 'location_code',
								'label' => lang('location'),
								'sortable'	=> false
							),

							array(
									'key' => 'checked',
									'label' => lang('approve'),
									'sortable' => false,
									'formatter' => 'formatterCheckPending',
									'className' => 'mychecks'
							),
							array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'labels',
								'hidden' => true
							),
							array(
								'key' => 'ajax',
								'hidden' => true
							),array(
								'key' => 'parameters',
								'hidden' => true
							)					
						)
					)
				);
			
				phpgwapi_yui::load_widget('paginator');

				self::add_javascript('registration', 'yahoo', 'pending.index.js');
				self::render_template_xsl(array('pending_users'), $data);
			}	
		}
	

		function index2()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			self::add_javascript('registration', 'yahoo', 'pending.index2.js');

			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$status_list = array
			(
				array
				(
					'id'	=> 0,
					'name'	=> lang('Select status')
				),
				array
				(
					'id'	=> 1,
					'name'	=> lang('approved')
				),
				array
				(
					'id'	=> 2,
					'name'	=> lang('pending')
				),
			);


			$data = array(
				'datatable_name' => lang('Pending for approval'),
				'js_lang'	=>js_lang('edit', 'add'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('invert checkboxes'),
								'href' => "javascript:checkAll('mychecks')"
							),
							array(
								'type' => 'link',
								'value' => lang('save'),
								'href' => "javascript:onSave()"
							),
							
							array('type' => 'filter', 
								'name' => 'reg_dla',
                                'text' => lang('status').':',
                                'list' => $status_list,
							),
							array('type' => 'text', 
                                'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_showall'))
							//	'href' => self::link(array('menuaction' => 'registration.uipending.index2', 'phpgw_return_as' => 'json', 'all'))
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'registration.uipending.index2', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'reg_id',
							'label' => lang('id'),
							'sortable'	=> true,
							'formatter' => 'formatLinkPending'
						),
						array(
							'key'	=>	'reg_lid',
							'label'	=>	lang('user'),
							'sortable'	=>	true
						),
						array(
							'key' => 'reg_dla',
							'label' => lang('time'),
							'sortable'	=> true
						),
						array(
							'key' => 'reg_approved',
							'label' => lang('approved'),
							'sortable'	=> true,
							'formatter' => "''"
						),
						array(
							'key' => 'location_code',
							'label' => lang('location'),
							'sortable'	=> false,
	//						'editor' => 'new YAHOO.widget.CheckboxCellEditor({checkboxOptions:[{label:"ja", value:true},{label:"nei", value:false}],disableBtns:true})'
						),
						array(
								'key' => 'checked',
								'label' => lang('approve'),
								'sortable' => false,
								'formatter' => 'formatterCheckPending',
								'className' => 'mychecks',
						),
/*
						array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'labels',
								'hidden' => true
							),
							array(
								'key' => 'ajax',
								'hidden' => true
							),array(
								'key' => 'parameters',
								'hidden' => true
							)					
*/
					),
				),
			);

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

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'registration.uipending.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);
					
	//		$data['datatable']['editor_action'] = 'rental.uiprice_item.set_value';

//_debug_array($data);die();

			self::render_template_xsl(array('datatable_common'), $data);
		}


		public function edit()
		{
			$id = phpgw::get_var('id', 'string');
			$bo = createobject('registration.boreg');

			if(isset($_POST['save']) && $id)
			{
				$values = phpgw::get_var('values');

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}


				$values['account_permissions']			= phpgw::get_var('account_permissions');
				$values['account_permissions_admin']	= phpgw::get_var('account_permissions_admin');
				$values['account_groups']				= phpgw::get_var('account_groups');

				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				$values['id'] = $id;

//_debug_array($account_permissions);
//_debug_array($account_permissions_admin);
//_debug_array($values);die();
				if($this->bo->update_pending_user($values))
				{
					$this->bo->process_users($values);
					$message = lang('messages_saved_form');
					phpgwapi_cache::message_set($message, 'message');
				}
				else
				{
					$error = lang('messages_form_error');
					phpgwapi_cache::message_set($message, 'error');
				}

			}

			if (isset($_POST['cancel'])) // The user has pressed the cancel button
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'registration.uipending.index'));
			}

			if (isset($_POST['delete']) && $id) // The user has pressed the delete button
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'registration.uipending.index'));
			}

			if($id)
			{
				$user = $bo->get_pending_user($id);
			}
				
			$fields = $bo->fields;
				
			$user_data = array();
			$user_data[] = array
			(
				'text'	=> 'username',
				'value' => $user['reg_lid']
			);
				
			foreach ($fields as $key => $field_info)
			{
				if($user['reg_info'][$field_info['field_name']])
				{
					$user_data[] = array
					(
						'text'	=> $field_info['field_text'],
						'value' => $user['reg_info'][$field_info['field_name']]
					);
				}
			}

			$bolocation	= CreateObject('property.bolocation');
			$user['location_data'] = isset($user['reg_info']['location_code']) && $user['reg_info']['location_code'] ? $bolocation->read_single($user['reg_info']['location_code'],array('view' => true)) : '';
				
			$location_data=$bolocation->initiate_ui_location(array(
				'values'	=> $user['location_data'],
				'type_id'	=> -1,
				'no_link'	=> false, // disable lookup links for location type less than type_id
				'tenant'	=> false,
				'lookup_type'	=> 'form',
				'lookup_entity'	=> false,
				'entity_data'	=> false
				));

			/* groups */
			$group_list = array();

			$all_groups =$GLOBALS['phpgw']->accounts->get_list('groups');

			//FIXME!!
			/*
			if(!$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$available_apps = $GLOBALS['phpgw_info']['apps'];
				$valid_groups = array();
				foreach($available_apps as $_app => $dummy)
				{
					if($GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, $_app))
					{
						$valid_groups	= array_merge($valid_groups,$GLOBALS['phpgw']->acl->get_ids_for_location('run', phpgwapi_acl::READ, $_app));
					}
				}

				$valid_groups = array_unique($valid_groups);
			}
			else
			{
				$valid_groups = array_keys($all_groups);
			}
			*/

			$valid_groups = array_keys($all_groups);

			$user['reg_info']['account_groups'] = isset($user['reg_info']['account_groups']) && $user['reg_info']['account_groups'] ? $user['reg_info']['account_groups'] : array();
			if($this->config['default_group_id'] && !in_array($this->config['default_group_id'] , $user['reg_info']['account_groups']))
			{
				$user['reg_info']['account_groups'] = array_merge ($user['reg_info']['account_groups'], array($this->config['default_group_id']));
			}

			foreach ( $all_groups as $group )
			{
				$group_list[] = array
				(
					'account_id'	=> $group->id,
					'account_lid'	=> $group->__toString(),
					'i_am_admin'	=> in_array($group->id, $valid_groups) ? 1 : 0,
					'checked'	 	=> in_array($group->id, $user['reg_info']['account_groups']) ? 1 : 0
				);
			}


			/* create list of available apps */

			$available_apps = $GLOBALS['phpgw_info']['apps'];
			asort($available_apps);

			if(!$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$valid_apps = $GLOBALS['phpgw']->acl->get_app_list_for_id('admin', phpgwapi_acl::ADD, $GLOBALS['phpgw_info']['user']['account_id']);
			}
			else
			{
				$valid_apps = array_keys($available_apps);
			}

			foreach ( $available_apps as $key => $application )
			{
				if ($application['enabled'] && $application['status'] != 3)
				{
					$perm_display[] = array
					(
						'app_name'			=> $key,
						'translated_name'	=> lang($key)
					);
				}
			}
			asort($perm_display);

			$app_list = array();
			foreach ( $perm_display as $perm )
			{
				$app_list[] = array
				(
					'app_title'				=> $perm['translated_name'],
					'checkbox_name'			=> "account_permissions[{$perm['app_name']}]",
					'checked'				=> in_array($perm['app_name'], $user['reg_info']['account_permissions']) ? 1 : 0,
					'checkbox_name_admin'	=> "account_permissions_admin[{$perm['app_name']}]",
					'checked_admin'			=> in_array($perm['app_name'], $user['reg_info']['account_permissions_admin']) ? 1 : 0,
					'i_am_admin'			=> in_array($perm['app_name'], $valid_apps) ? 1 : 0,
				);
			}

//_debug_array($app_list);die();

			$tabs = array
			(
				'main'		=> array('label' => lang('user'), 'link' => '#main'),
				'groups'	=> array('label' => lang('groups'), 'link' => '#groups'),
				'apps'		=> array('label' => lang('applications'), 'link' => '#apps'),
			);
			$active_tab = 'main';

			phpgwapi_yui::tabview_setup('edit_user_tabview');

			$data = array
			(
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
				'value_id'				=> $id,
				'user_data'				=> $user_data,
				'location_data'			=> $location_data,
				'value_approved'		=> $user['reg_approved'],
				'app_list'				=> $app_list,
				'group_list'			=> $group_list,
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('registration') . '::' . lang('edit user');

			self::render_template_xsl('user_edit', $data);
		}
	
		public function query()
		{
			$status_id = phpgw::get_var('status_id');

			$this->bo->start = phpgw::get_var('startIndex');
		
			$user_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));
			
			foreach($user_list as &$user)
			{
				$reg_info = unserialize(base64_decode($user['reg_info']));
				$user['location_code'] = $reg_info['location_code'];
				$results['results'][]= $user;
			}
			$results['total_records'] = $this->bo->total_records;
			$results['start'] = $this->start;
			$results['sort'] = 'reg_lid';
			$results['dir'] = $this->bo->sort ? $this->bo->sort : 'ASC';
					
			array_walk($results['results'], array($this, 'add_actions'), array($type));
//_debug_array($results);						
			return $this->yui_results($results);
		}

		public function add_actions(&$value, $key, $params)
		{
			//Defining new columns
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
	
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental._uibilling.delete', 'id' => $value['id'])));
			$value['labels'][] = lang('delete');
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental._uibilling.commit', 'id' => $value['id'])));
			$value['labels'][] = lang('commit');
	    }

	}
