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
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.yui');
	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');


	class property_uidimb_role_user
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
	
		private $config;

		var $public_functions = array
		(
			'index'								=> true,
			'query'								=> true,
			'edit'						 		=> true,
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account_id 			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bodimb_role_user');
			$this->bocommon				= CreateObject('property.bocommon');
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->status_id			= $this->bo->status_id;
			$this->allrows				= $this->bo->allrows;
		
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::accounting::dimb_role_user2';
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->config->read();

		}

		public function add_javascript($app, $pkg, $name)
		{
  			return $GLOBALS['phpgw']->js->validate_file($pkg, str_replace('.js', '', $name), $app);
		}
		/**
		* A more flexible version of xslttemplate.add_file
		*/
		public function add_template_file($tmpl)
		{
			if(is_array($tmpl))
			{
				foreach($tmpl as $t)
				{
					$this->add_template_file($t);
				}
				return;
			}
			foreach(array_reverse($this->tmpl_search_path) as $path)
			{
				$filename = $path . '/' . $tmpl . '.xsl';
				if (file_exists($filename))
				{
					$GLOBALS['phpgw']->xslttpl->xslfiles[$tmpl] = $filename;
					return;
				}
			}
			echo "Template $tmpl not found in search path: ";
			print_r($this->tmpl_search_path);
			die;
		}

		public function link($data)
		{
			return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		public function redirect($link_data)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}




		function index()
		{
			$receipt = array();

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$msgbox_data = array();
			if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$myColumnDefs = array();
			$datavalues = array();
			$myButtons	= array();

			$datavalues[] = array
			(
				'name'				=> "0",
				'values' 			=> $this->query(),//json_encode(array()),
				'total_records'		=> 0,
				'permission'   		=> "''",
				'is_paginator'		=> 0,
				'edit_action'		=> "''",
				'footer'			=> 0
			);

			$datatable = array
			(
				array
				(
				'key' => 'id',
				'hidden' => true
				),
				array
				(
					'key' => 'user',
					'label' => lang('user'),
					'sortable' => false
				),
				array
				(
					'key' => 'ecodimb',
					'label' => lang('dim b'),
					'sortable' => false,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key'	=>	'role',
					'label'	=>	lang('role'),
					'formatter' => 'FormatterRight',
					'sortable'	=>	true
				),
				array
				(
					'key' => 'default_user',
					'label' => lang('default'),
					'sortable'	=> false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'active_from',
					'label' => lang('date from'),
					'sortable'	=> true,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key' => 'active_to',
					'label' => lang('date to'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'add',
					'label' => lang('add'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'delete',
					'label' => lang('delete'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'alter_date',
					'label' => lang('alter date'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
			);

			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode($datatable)
			);	



			$user_list = $this->bocommon->get_user_list_right2('select', PHPGW_ACL_READ, $this->filter, '.invoice', array(), $this->account_id);
			$role_list = execMethod('property.bogeneric.get_list', array('type'=>'dimb_role', 'selected' => $role ));
			$dimb_list = execMethod('property.bogeneric.get_list', array('type'=>'dimb', 'selected' => $dimb ));

			array_unshift ($user_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift ($role_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift ($dimb_list ,array ('id'=>'','name'=>lang('select')));

			$data = array
			(
				'td_count'						=> '""',
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
				'myButtons'						=> $myButtons,

				'msgbox_data'					=> $msgbox_data,
				'filter_form' 					=> array
													(
														'user_list' 	=> array('options' => $user_list),
														'role_list' 	=> array('options' => $role_list),
														'dimb_list' 	=> array('options' => $dimb_list),
													),
				'update_action'					=> self::link(array('menuaction' => 'property.uidimb_role_user.edit'))
			);

			$GLOBALS['phpgw']->jqcal->add_listener('query_start');
			$GLOBALS['phpgw']->jqcal->add_listener('query_end');
			$GLOBALS['phpgw']->jqcal->add_listener('active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('active_to');

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('property', 'portico', 'ajax_dimb_role_user.js');
			self::add_javascript('property', 'yahoo', 'dimb_role_user.index.js');

			$GLOBALS['phpgw']->xslttpl->add_file(array('dimb_role_user'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('data' => $data));
		}
	

		public function query()
		{
			$user_id =	phpgw::get_var('user_id', 'int');
			$dimb_id =	phpgw::get_var('dimb_id', 'int');
			$role_id =	phpgw::get_var('role_id', 'int');
			$query_start =	phpgw::get_var('query_start');
			$query_end =	phpgw::get_var('query_end');

//			$this->bo->allrows = true;
			$values = $this->bo->read(array('user_id' => $user_id, 'dimb_id' => $dimb_id, 'role_id' => $role_id, 'query_start' => $query_start, 'query_end' => $query_end));

			foreach($values as &$entry)
			{
				if($entry['active_from'])
				{
					$default_user_checked = $entry['default_user'] == 1 ? 'checked = "checked"' : '';
					$entry['default_user'] = "<input id=\"default_user\" type =\"checkbox\" $default_user_checked name=\"values[default_user][]\" value=\"{$entry['id']}\">";
					$entry['delete'] = "<input id=\"delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$entry['id']}\">";
					$entry['alter_date'] = "<input id=\"alter_date\" type =\"checkbox\" name=\"values[alter_date][]\" value=\"{$entry['id']}\">";
					$entry['add'] = '';
				}
				else
				{
					$entry['default_user'] = '';
					$entry['delete'] = '';
					$entry['alter_date'] = '';
					$entry['add'] = "<input id=\"add\" type =\"checkbox\" name=\"values[add][]\" value=\"{$entry['ecodimb']}_{$entry['role_id']}_{$entry['user_id']}\">";				
				}
				$results['results'][]= $entry;
			}

			return json_encode($values);
		}

		public function edit()
		{
			$user_id =	phpgw::get_var('user_id', 'int');
			$dimb_id =	phpgw::get_var('dimb_id', 'int');
			$role_id =	phpgw::get_var('role_id', 'int');
			$query =	phpgw::get_var('query');

			if($values = phpgw::get_var('values'))
			{
				if(!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][]=true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}
				if(!$receipt['error'])
				{
					if($this->bo->edit($values))
					{
						$result =  array
						(
							'status'	=> 'updated'
						);
					}
					else
					{
						$result =  array
						(
							'status'	=> 'error'
						);
					}
				}
			}

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if( $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				else
				{
					$result['receipt'] = array();
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uidimb_role_user.index', 'user_id' => $user_id, 'dimb_id' => $dimb_id, 'role_id' => $role_id, 'query' => $query));
			}
		}
	}
