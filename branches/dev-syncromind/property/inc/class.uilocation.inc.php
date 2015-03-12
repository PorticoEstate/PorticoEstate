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
	* @subpackage location
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	//phpgw::import_class('phpgwapi.yui');

	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uilocation extends phpgwapi_uicommon_jquery
	{
		protected $appname = 'property';
		private $receipt = array();
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $lookup;
		var $location_code;

		var $public_functions = array
			(
				'query'				    => true,
				'responsiblility_role_save' => true,
				'get_part_of_town'      => true,
				'download'  			=> true,
				'index'  				=> true,
				'view'   				=> true,
				'edit'   				=> true,
				'delete' 				=> true,
				'update_cat'			=> true,
				'stop'					=> true,
				'summary'				=> true,
				'columns'				=> true,
				'update_location'		=> true,
				'responsiblility_role'	=> true
			);

		function __construct()
		{
//			parent::__construct();
//
//		//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
//			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
//			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location';
//			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
//			$this->bo					= CreateObject('property.bolocation',true);
//			$this->bocommon				= & $this->bo->bocommon;
			$this->soadmin_location		= CreateObject('property.soadmin_location');
//			$this->acl 					= & $GLOBALS['phpgw']->acl;
//
//			$this->type_id				= $this->bo->type_id;
//
//			$this->acl_location			= $this->bo->acl_location;
//			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
//			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
//			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
//			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
//
//			$this->start				= $this->bo->start;
//			$this->query				= $this->bo->query;
//			$this->sort					= $this->bo->sort;
//			$this->order				= $this->bo->order;
//			$this->filter				= $this->bo->filter;
//			$this->cat_id				= $this->bo->cat_id;
//			$this->part_of_town_id		= $this->bo->part_of_town_id;
//			$this->district_id			= $this->bo->district_id;
//			$this->status				= $this->bo->status;
//			$this->allrows				= $this->bo->allrows;
//			$this->lookup				= $this->bo->lookup;
//			$this->location_code		= $this->bo->location_code;
			parent::__construct();

			//$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			/*$this->acl_location			= $this->location_info['acl_location'];
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->location_info['acl_app']);
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->location_info['acl_app']);
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->location_info['acl_app']);
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->location_info['acl_app']);
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, $this->location_info['acl_app']);*/
			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->type_id				= $this->bo->type_id;
			$this->lookup				= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location';
			//$GLOBALS['phpgw_info']['flags']['menu_selection'] = $this->location_info['menu_selection'];

//			$this->start				= $this->bo->start;
//			$this->query				= $this->bo->query;
//			$this->sort					= $this->bo->sort;
//			$this->order				= $this->bo->order;
//			$this->allrows				= $this->bo->allrows;
//
//			$this->type 		= $this->bo->type;
//			$this->type_id 		= $this->bo->type_id;


		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$type_id	= $this->type_id;
			$lookup 	= $this->lookup;
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1,

				'type_id' => $type_id,
				'lookup_tenant' => $lookup_tenant,
				'lookup' => $lookup,
				'district_id' => phpgw::get_var('district_id', 'int'),
				'status' => phpgw::get_var('status'),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int'),
				'location_code' => phpgw::get_var('location_code'),
				'filter'		=> phpgw::get_var('filter', 'int')
			);

			$values = $this->bo->read($params);
			if ( phpgw::get_var('export', 'bool'))
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}


		public function query_role()
		{
			$type_id	= $this->type_id;
			$lookup 	= $this->lookup;
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');
			$user_id = phpgw::get_var('user_id', 'int', 'request', $this->account);
			$role_id = phpgw::get_var('role_id', 'int');

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1,

				'type_id' => $type_id,
				'lookup_tenant' => $lookup_tenant,
				'lookup' => $lookup,
				'district_id' => phpgw::get_var('district_id', 'int'),
				'status' => phpgw::get_var('status'),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int'),
				'location_code' => phpgw::get_var('location_code'),
				'filter'		=> phpgw::get_var('filter', 'int'),
				'user_id' => $user_id,
				'role_id' => $role_id
			);

			$values = $this->bo->get_responsible($params);

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function query_summary()
		{
			$values = $this->bo->read_summary();

			$result_data = array('results' => $values);

			$result_data['total_records'] = count($values);
			$result_data['draw'] = 0;

			return $this->jquery_results($result_data);
		}


		public function responsiblility_role_save()
		{
			$values = phpgw::get_var('values');
			//$values_assign = $_POST['values_assign'];
			$assign_orig = phpgw::get_var('assign_orig');
			$assign = phpgw::get_var('assign');

			$role_id = phpgw::get_var('role_id', 'int');
			//$receipt = array();
			$_role = CreateObject('property.sogeneric');
			$_role->get_location_info('responsibility_role','');

			//$this->save_sessiondata();

			$user_id = phpgw::get_var('user_id', 'int', 'request', $this->account);

			if(($assign || $assign_orig) && $this->acl_edit)
			{
				//$values_assign = phpgw::clean_value(json_decode(stripslashes($values_assign),true)); //json_decode has issues with magic_quotes_gpc
				$user_id = abs($user_id);
				$account = $GLOBALS['phpgw']->accounts->get($user_id);
				$contact_id = $account->person_id;
				if(empty($role_id))
				{
					$result = lang('missing role');
				}
				else
				{
					$role = $_role->read_single($data=array('id' => $role_id));
					$values['contact_id']			= $contact_id;
					$values['responsibility_id']	= $role['responsibility_id'];
					$values['assign']				= $assign;
					$values['assign_orig']			= $assign_orig;
					$boresponsible = CreateObject('property.boresponsible');
					$result = $boresponsible->update_role_assignment($values);
				}
			}

			return $result;
		}


		function save_sessiondata()
		{
			$data = array
				(
					'start'				=> $this->start,
					'query'				=> $this->query,
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'filter'			=> $this->filter,
					'cat_id'			=> $this->cat_id,
					'part_of_town_id'	=> $this->part_of_town_id,
					'district_id'		=> $this->district_id,
					'status'			=> $this->status,
					'type_id'			=> $this->type_id,
				//	'allrows'			=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$summary		= phpgw::get_var('summary', 'bool', 'GET');
			$type_id		= phpgw::get_var('type_id', 'int', 'GET');
			$lookup 		= phpgw::get_var('lookup', 'bool');
			//$lookup_name 	= phpgw::get_var('lookup_name');
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');

			if(!$summary)
			{
				$list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>true));
			}
			else
			{
				$list= $this->bo->read_summary();
			}

			$uicols	= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function columns()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values 		= phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'] && $this->type_id)
			{
				$GLOBALS['phpgw']->preferences->add('property','location_columns_' . $this->type_id . !!$this->lookup,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uilocation.columns',
					'type_id'		=> $this->type_id,
					'lookup'		=> $this->lookup
				);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

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

		private function _get_categories()
		{
			$values_combo_box = array();
			$combos = array();

			$link = self::link(array(
					'menuaction' => 'property.uilocation.get_part_of_town',
					'type_id' =>  $this->type_id,
					'phpgw_return_as' => 'json'
					));

			$code = '
				var link = "'.$link.'";
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

			$values_combo_box[0]  = $this->bocommon->select_category_list(array
				('format' => 'filter',
				'selected' => $this->cat_id,
				'type' => 'location',
				'type_id' => $this->type_id,
				'order' => 'descr')
			);
			array_unshift ($values_combo_box[0], array('id'=>'', 'name'=>lang('no category')));
			$combos[] = array('type' => 'filter',
						'name' => 'cat_id',
						'extra' => '',
						'text' => lang('category'),
						'list' => $values_combo_box[0]
					);

			$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
			array_unshift ($values_combo_box[1], array('id'=>'', 'name'=>lang('no district')));
			$combos[] = array('type' => 'filter',
						'name' => 'district_id',
						'extra' => $code,
						'text' => lang('district'),
						'list' => $values_combo_box[1]
					);

			$values_combo_box[2] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
			array_unshift ($values_combo_box[2], array('id'=>'', 'name'=>lang('no part of town')));
			$combos[] = array('type' => 'filter',
						'name' => 'part_of_town_id',
						'extra' => '',
						'text' => lang('part of town'),
						'list' => $values_combo_box[2]
					);

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
			{
				$values_combo_box[3] = $this->bo->get_owner_list('filter', $this->filter);
			}
			else
			{
				$values_combo_box[3] = $this->bo->get_owner_type_list('filter', $this->filter);
			}
			array_unshift ($values_combo_box[3], array('id'=>'', 'name'=>lang('show all')));
			$combos[] = array('type' => 'filter',
						'name' => 'filter',
						'extra' => '',
						'text' => lang('filter'),
						'list' => $values_combo_box[3]
					);

			return $combos;
		}

		private function _get_categories_role()
		{
			$type_id = $this->type_id;

			$values_combo_box = array();
			$combos = array();

			$link = self::link(array(
					'menuaction' => 'property.uilocation.get_part_of_town',
					'type_id' =>  $this->type_id,
					'phpgw_return_as' => 'json'
					));

			$code = '
				var link = "'.$link.'";
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

			$values_combo_box[0]  = execMethod('property.soadmin_location.read',array());
			$combos[] = array('type' => 'filter',
						'name' => 'type_id',
						'extra' => '',
						'text' => lang('Type'),
						'list' => $values_combo_box[0]
					);

			$values_combo_box[1]  = $this->bocommon->select_category_list(array
				('format'=>'filter',
				'selected' => $this->cat_id,
				'type' =>'location',
				'type_id' =>$type_id,
				'order'=>'descr')
			);
			array_unshift ($values_combo_box[1], array('id'=>'', 'name'=>lang('no category')));
			$combos[] = array('type' => 'filter',
						'name' => 'cat_id',
						'extra' => '',
						'text' => lang('category'),
						'list' => $values_combo_box[1]
					);


			$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
			array_unshift ($values_combo_box[2], array('id'=>'', 'name'=>lang('no district')));
			$combos[] = array('type' => 'filter',
						'name' => 'district_id',
						'extra' => $code,
						'text' => lang('district'),
						'list' => $values_combo_box[2]
					);


			$values_combo_box[3] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
			array_unshift ($values_combo_box[3], array('id'=>'', 'name'=>lang('no part of town')));
			$combos[] = array('type' => 'filter',
						'name' => 'part_of_town_id',
						'extra' => '',
						'text' => lang('part of town'),
						'list' => $values_combo_box[3]
					);


			$_role_criteria = array
				(
					'type'			=> 'responsibility_role',
					'filter'		=> array('location_level' => $type_id),
					'filter_method'	=> 'like',
					'order'			=> 'name'
				);
			$values_combo_box[4] =   execMethod('property.sogeneric.get_list',$_role_criteria);
			array_unshift ($values_combo_box[4], array('id'=>'', 'name'=>lang('no role')));
			$combos[] = array('type' => 'filter',
						'name' => 'role_id',
						'extra' => '',
						'text' => lang('role'),
						'list' => $values_combo_box[4]
					);

//				$values_combo_box[5]  = $this->bocommon->get_user_list_right2('filter',PHPGW_ACL_READ,$this->user_id,".location.{$type_id}");
			$_users = $GLOBALS['phpgw']->accounts->get_list('accounts', -1, 'ASC',	'account_lastname', '', -1);
			$values_combo_box[5]  = array();
			foreach($_users as $_user)
			{
				$values_combo_box[5][] = array
				(
					'id'	=> $_user->id,
					'name'	=> $_user->__toString(),

				);
			}
			unset($_users);
			unset($_user);

			array_unshift ($values_combo_box[5],array('id'=> (-1*$GLOBALS['phpgw_info']['user']['account_id']),'name'=>lang('mine roles')));
			array_unshift ($values_combo_box[5], array('id'=>'', 'name'=>lang('no user')));
			$combos[] = array('type' => 'filter',
						'name' => 'user_id',
						'extra' => '',
						'text' => lang('user'),
						'list' => $values_combo_box[5]
					);

			return $combos;
		}


		private function _get_categories_summary()
		{
			$values_combo_box = array();
			$combos = array();

			$link = self::link(array(
					'menuaction' => 'property.uilocation.get_part_of_town',
					'type_id' =>  $this->type_id,
					'phpgw_return_as' => 'json'
					));

			$code = '
				var link = "'.$link.'";
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

			$values_combo_box[0]  = $this->bocommon->select_district_list('filter',$this->district_id);
			array_unshift ($values_combo_box[0], array('id'=>'', 'name'=>lang('no district')));
			$combos[] = array('type' => 'filter',
						'name' => 'district_id',
						'extra' => $code,
						'text' => lang('district'),
						'list' => $values_combo_box[0]
					);

			$values_combo_box[1] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
			array_unshift ($values_combo_box[1], array('id'=>'', 'name'=>lang('no part of town')));
			$combos[] = array('type' => 'filter',
						'name' => 'part_of_town_id',
						'extra' => '',
						'text' => lang('part of town'),
						'list' => $values_combo_box[1]
					);

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
			{
				$values_combo_box[2] = $this->bo->get_owner_list('filter', $this->filter);
			}
			else
			{
				$values_combo_box[2] = $this->bo->get_owner_type_list('filter', $this->filter);
			}
			array_unshift ($values_combo_box[2], array('id'=>'', 'name'=>lang('show all')));
			$combos[] = array('type' => 'filter',
						'name' => 'filter',
						'extra' => '',
						'text' => lang('filter'),
						'list' => $values_combo_box[2]
					);

			return $combos;
		}


		function get_part_of_town()
		{
			$district_id	= phpgw::get_var('district_id', 'int');
			$values =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$district_id);
			array_unshift ($values, array('id'=>'', 'name'=>lang('no part of town')));

			return $values;
		}

		function index()
		{
			$type_id	= $this->type_id;
			// $lookup use for pop-up
			$lookup 	= $this->lookup;
			// $lookup_name use in pop-up option "project"
			$lookup_name 	= phpgw::get_var('lookup_name');
			// use in option menu TENANT
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');
			$block_query	= phpgw::get_var('block_query', 'bool');

			if($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}

			if ( $type_id && !$lookup_tenant )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::tenant';
			}

			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$custom_config	= CreateObject('admin.soconfig',$location_id);
			$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$_integration_set = array();
			foreach ($_config as $_config_section => $_config_section_data)
			{
				$integrationurl = '';
				if(isset($_config_section_data['url']) && !isset($_config_section_data['tab']))
				{
					if(isset($_config_section_data['auth_hash_name']) && $_config_section_data['auth_hash_name'] && isset($_config_section_data['auth_url']) && $_config_section_data['auth_url'])
					{
						//get session key from remote system

						$arguments = array($_config_section_data['auth_hash_name'] => $_config_section_data['auth_hash_value']);
						$query = http_build_query($arguments);
						$auth_url = $_config_section_data['auth_url'];
						$request = "{$auth_url}?{$query}";

						$aContext = array
							(
								'http' => array
								(
									'request_fulluri' => true,
								),
							);

						if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
						{
							$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
						}

						$cxContext = stream_context_create($aContext);
						$response = trim(file_get_contents($request, False, $cxContext));
					}


					$_config_section_data['url'] = htmlspecialchars_decode($_config_section_data['url']);
					$_config_section_data['parametres'] = htmlspecialchars_decode($_config_section_data['parametres']);
					$integration_name = isset($_config_section_data['name']) && $_config_section_data['name'] ? $_config_section_data['name'] : lang('integration');

					parse_str($_config_section_data['parametres'], $output);

					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;
						$__substitute = trim($_substitute, '_');
						$_values[] = $this->$__substitute;
					}
					unset($output);

					$_sep = '?';
					if (stripos($_config_section_data['url'],'?'))
					{
						$_sep = '&';
					}
					$_param = str_replace($_keys, $_values, $_config_section_data['parametres']);

					$integrationurl = "{$_config_section_data['url']}{$_sep}{$_param}";
					$integrationurl .= "&{$_config_section_data['auth_key_name']}={$response}";


					//in the form: sakstittel=__loc1__.__loc4__

					$_config_section_data['location_data']= htmlspecialchars_decode($_config_section_data['location_data']);

					$parameters_integration = array();
					if($_config_section_data['location_data'])
					{
						parse_str($_config_section_data['location_data'], $output);

						foreach ($output as $_name => $_substitute)
						{
							if($_substitute == '__loc1__') // This one is a link...
							{
								$_substitute = '__location_code__';
							}

							$parameters_integration['parameter'][] = array
							(
								'name'		=> $_name,
								'source'	=> trim($_substitute, '_'),
							);
						}
					}

					$_integration_set[] = array
					(
						'name'			=> $integration_name,
						'parameters'	=> $parameters_integration,
						'url'			=> $integrationurl
					);
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$this->bo->read(array('type_id'=> $type_id, 'lookup_tenant' => $lookup_tenant,'lookup' => $lookup,'dry_run' => true));
			$uicols = $this->bo->uicols;

			$appname = lang('location');

			if($lookup)
			{
				$lookup_list	= $GLOBALS['phpgw']->session->appsession('lookup_name','property');
				$function_msg	= $lookup_list[$lookup_name];
			// for POP-UPs
				$input_name		= phpgwapi_cache::session_get('property', 'lookup_fields');
				$function_exchange_values = <<<JS

				$(document).ready(function() {
					$("#datatable-container").on("click", "tr", function() {
					var iPos = oTable.fnGetPosition( this );
					var aData = oTable.fnGetData( iPos ); //complete dataset from json returned from server

JS;

				if(is_array($input_name))
				{
					for ($k=0;$k<count($input_name);$k++)
					{
						$function_exchange_values .= <<<JS

						parent.document.getElementsByName("{$input_name[$k]}")[0].value = "";
JS;
					}
				}

				for ($i=0;$i<count($uicols['name']);$i++)
				{
					if(isset($uicols['exchange'][$i]) && $uicols['exchange'][$i])
					{
						$function_exchange_values .=  <<<JS

						parent.document.getElementsByName("{$uicols['name'][$i]}")[0].value = "";
						parent.document.getElementsByName("{$uicols['name'][$i]}")[0].value = aData["{$uicols['name'][$i]}"];
JS;
					}
				}

				$function_exchange_values .=<<<JS

				parent.TINY.box.hide();

			});

	});
JS;

				$GLOBALS['phpgw']->js->add_code('', $function_exchange_values);
			}
			else
			{
				if($lookup_tenant)
				{
					$function_msg = lang('Tenant');
				}
				else
				{
					$function_msg = $uicols['descr'][($type_id)];
				}
			}

			$data = array(
				'datatable_name'	=> $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('new'),
								'href' => self::link(array(
									'menuaction' => 'property.uilocation.add',
									'type_id' =>  $type_id,
									'parent' =>  $this->location_code
									)),
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => lang('columns'),
								'href' => '#',
								'class' => '',
								'onclick'=> "JqueryPortico.openPopup({menuaction:'property.uilocation.columns', type_id:'{$type_id}',parent:'{$this->location_code}'}, {closeAction:'reload'})"
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
								'menuaction' 		=> 'property.uilocation.index',
								'type_id' 			=> $type_id,
								'district_id'       => $this->district_id,
								'part_of_town_id'   => $this->part_of_town_id,
								'lookup'        	=> $lookup,
								'lookup_tenant'     => $lookup_tenant,
								'lookup_name'       => $lookup_name,
								'cat_id'        	=> $this->cat_id,
								'location_code'		=> $this->location_code,
								'block_query'		=> $block_query,
								'phpgw_return_as' => 'json'
					)),
					'download'	=> self::link(array('menuaction' => 'property.uilocation.download',
									'type_id'		=> $type_id,
									'lookup'		=> $lookup,
									'lookup_tenant' => $lookup_tenant,
									'export'		=> true,
									'allrows'		=> true)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			if(!$lookup)
			{
				$data['actions']['form']['toolbar']['item'][] =  array
					(
						'type'	=> 'button',
						'id'	=> 'btn_new',
						'value'	=> lang('add'),
						'tab_index' => 7
					);
			}

			$filters = $this->_get_categories();

			foreach ($filters as $filter)
			{
				array_unshift ($data['form']['toolbar']['item'], $filter);
			}

			$count_uicols_name = count($uicols['name']);

			$searc_levels = array();
			for($i=1; $i<$type_id; $i++)
			{
				$searc_levels[] = "loc{$i}";
			}

			for($k=0;$k<$count_uicols_name;$k++)
			{
					$params = array(
									'key' => $uicols['name'][$k],
									'label' => $uicols['descr'][$k],
									'sortable' => false,
									'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
								);

					if ($uicols['datatype'][$k] == 'link') {
						$params['formatter'] = 'JqueryPortico.formatLinkGeneric';
					}

					if(in_array($uicols['name'][$k], $searc_levels))
					{
						$params['formatter'] = 'JqueryPortico.searchLink';
					}
					if($uicols['name'][$k]=='loc1')
					{
						$params['formatter'] = 'JqueryPortico.searchLink';
						$params['sortable']	= true;
					}
					else if($uicols['name'][$k]=='street_name')
					{
						$params['sortable']	= true;
					}
					else if(isset($uicols['cols_return_extra'][$k]) && ($uicols['cols_return_extra'][$k]!='T' || $uicols['cols_return_extra'][$k]!='CH'))
					{
						$params['sortable']	= true;
					}

					array_push ($data['datatable']['field'], $params);
			}


			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'location_code',
								'source'	=> 'location_code'
							),
						)
					);

				$parameters2 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'sibling',
								'source'	=> 'location_code'
							),
						)
					);

				$parameters3 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'search_for',
								'source'	=> 'location_code'
							),
						)
					);

				if($this->acl->check('run', PHPGW_ACL_READ, 'rental'))
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'			=> 'view',
							'text' 			=> lang('contracts'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	  => 'rental.uicontract.index',
								'search_type'	  => 'location_id',
								'contract_status' => 'all',
								'populate_form'   => 'yes'
							)),
							'parameters'	=> json_encode($parameters3)
						);

					$data['datatable']['actions'][] = array
						(
							'my_name'			=> 'view',
							'text' 			=> lang('composites'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	  => 'rental.uicomposite.index',
								'search_type'	  => 'location_id',
								'populate_form'   => 'yes'
							)),
							'parameters'	=> json_encode($parameters3)
						);
				}

				if($this->acl_read)
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'		=> 'view',
							'text' 			=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.view',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> json_encode($parameters)
						);

					$data['datatable']['actions'][] = array
						(
							'my_name'		=> 'view',
							'text' 			=> lang('open view in new window'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.view',
								'lookup_tenant'	=> $lookup_tenant,
								'target'		=> '_blank'
							)),
							'parameters'	=> json_encode($parameters)
						);
				}

				if($this->acl_add)
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> json_encode($parameters2)
						);
				}

				if($this->acl_edit)
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> json_encode($parameters)
						);

					$data['datatable']['actions'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('open edit in new window'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'lookup_tenant'	=> $lookup_tenant,
								'target'		=> '_blank'
							)),
							'parameters'	=> json_encode($parameters)
						);

				}

				foreach ($_integration_set as $_integration )
				{
					$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'integration',
						'text'	 		=> $_integration['name'],
						'action'		=> $_integration['url'].'&target=_blank',
						'parameters'	=> $_integration['parameters']
					);
				}

				if($this->acl_delete)
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'		=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.delete',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> json_encode($parameters)
						);
				}

				unset($parameters);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);

		}


		function responsiblility_role()
		{
			$user_id = phpgw::get_var('user_id', 'int', 'request', $this->account);
			$role_id = phpgw::get_var('role_id', 'int');
			$type_id = phpgw::get_var('type_id', 'int');

			if(!$type_id)
			{
				$type_id = 1;
			}

			if($_menu_selection = phpgw::get_var('menu_selection'))
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = $_menu_selection;
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::responsibility_role';
			}

			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_role();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			self::add_javascript('property', 'portico', 'location.responsiblility_role.js');

			$this->bo->get_responsible(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id, 'allrows'=>$this->allrows));

			$uicols = $this->bo->uicols;

			$appname = lang('location');
			$function_msg = lang('role');

			$data = array(
				'datatable_name'	=> $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
								'menuaction' 		=> 'property.uilocation.responsiblility_role',
								'type_id' 			=> $type_id,
								'cat_id'        	=> $this->cat_id,
								'district_id'       => $this->district_id,
								'part_of_town_id'   => $this->part_of_town_id,
								'second_display'    => 1,
								'status'            => $this->status,
								'location_code'     => $this->location_code,
								'entity_id'			=> $this->entity_id,
								'phpgw_return_as' => 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$filters = $this->_get_categories_role();

			foreach ($filters as $filter)
			{
				array_unshift ($data['form']['toolbar']['item'], $filter);
			}

			$uicols['name'][]		= 'responsible_contact';
			$uicols['descr'][]		= lang('responsible');
			$uicols['sortable'][]	= false;
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'responsible_contact_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'responsible_item';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'select';
			$uicols['descr'][]		= lang('select');
			$uicols['sortable'][]	= false;
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= $this->acl_edit ? 'myFormatterCheck' : '';
			$uicols['input_type'][]	= '';

			$count_uicols_name = count($uicols['name']);

			$searc_levels = array();
			for($i=1; $i<$type_id; $i++)
			{
				$searc_levels[] = "loc{$i}";
			}

			for($k=0;$k<$count_uicols_name;$k++)
			{
					$params = array(
									'key' => $uicols['name'][$k],
									'label' => $uicols['descr'][$k],
									'sortable' => false,
									'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
								);

					if(!empty($uicols['formatter'][$k]))
					{
						$params['formatter'] = $uicols['formatter'][$k];
					}
					if(in_array($uicols['name'][$k], $searc_levels))
					{
						$params['formatter'] = 'JqueryPortico.searchLink';
					}
					if($uicols['name'][$k]=='loc1')
					{
						$params['formatter'] = 'JqueryPortico.searchLink';
						$params['sortable']	= true;
					}
					else if(isset($uicols['cols_return_extra'][$k]) && ($uicols['cols_return_extra'][$k]!='T' || $uicols['cols_return_extra'][$k]!='CH'))
					{
						$params['sortable']	= true;
					}

					array_push ($data['datatable']['field'], $params);
			}

			if($this->acl_edit)
			{
				$code = '
					var assign = [];
					var assign_orig = [];

					$(".mychecks:checked").each(function () {
							assign.push($(this).val());
					});

					$(".orig_check").each(function () {
							assign_orig.push($(this).val());
					});

					var data = {"assign": assign, "assign_orig": assign_orig, "user_id": $("#user_id").val(), "role_id": $("#role_id").val()};
					execute_ajax(action, function(result){
						document.getElementById("message").innerHTML += "<br/>" + result;
						oTable.fnDraw();
					}, data, "POST");
					';

				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'save',
						'type'			=> 'custom',
						'custom_code' => $code,
						'text' 			=> lang('save'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uilocation.responsiblility_role_save',
							'type_id' 			=> $type_id,
							'cat_id'        	=> $this->cat_id,
							'district_id'       => $this->district_id,
							'part_of_town_id'   => $this->part_of_town_id,
							'second_display'    => 1,
							'status'            => $this->status,
							'location_code'     => $this->location_code,
							'entity_id'			=> $this->entity_id,
							'phpgw_return_as' => 'json'
						))
					);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			self::render_template_xsl('datatable_jquery', $data);
		}


		function edit($view = '')
		{

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$get_history 		= phpgw::get_var('get_history', 'bool', 'POST');
			$change_type 		= phpgw::get_var('change_type', 'int', 'POST');
			$lookup_tenant 		= phpgw::get_var('lookup_tenant', 'bool');
			$location_code		= phpgw::get_var('location_code');
			$sibling			= phpgw::get_var('sibling');
			$parent				= phpgw::get_var('parent');
			$values_attribute	= phpgw::get_var('values_attribute');
			$location 			= explode('-',$location_code);
			$error_id			= false;

			if($sibling)
			{
				$parent = array();
				$sibling = explode('-',$sibling);
				$this->type_id = count($sibling);
				for ($i=0;$i<(count($sibling)-1);$i++)
				{
					$parent[] = $sibling[$i];
				}
				$parent = implode('-', $parent);
			}

			$type_id	 	= $this->type_id;

			if($location_code)
			{
				$type_id = count($location);
			}

			if ( $type_id && !$lookup_tenant )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::tenant';
			}

			if($view)
			{
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
				$mode = 'view';
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
				$mode = 'edit';
			}

			$values = array();
			if(isset($_POST['save']) && !$view)
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$GLOBALS['phpgw']->session->appsession('insert_record','property','');

				if(isset($insert_record['location']) && is_array($insert_record['location']))
				{
					for ($i=0; $i<count($insert_record['location']); $i++)
					{
						$values[$insert_record['location'][$i]]= phpgw::get_var($insert_record['location'][$i], 'string', 'POST');
					}
				}

				$insert_record_attributes	= $GLOBALS['phpgw']->session->appsession('insert_record_values' . '.location.' . $this->type_id,'property');

				if(is_array($insert_record_attributes))
				{
					foreach ($insert_record_attributes as $attribute)
					{
						foreach ($values_attribute as &$attr)
						{
							if($attr['name'] ==  $attribute)
							{
								$attr['value'] = phpgw::get_var($attribute, 'string', 'POST');
							}
						}
					}
				}

				if(isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					for ($i=0; $i<count($insert_record['extra']); $i++)
					{
						$values[$insert_record['extra'][$i]]= phpgw::get_var($insert_record['extra'][$i], 'string', 'POST');
					}
				}
			}

			//$GLOBALS['phpgw']->xslttpl->add_file(array('location','attributes_form'));

			if ($values)
			{
				for ($i=1; $i<($type_id+1); $i++)
				{
					if((!$values["loc{$i}"]  && (!isset($location[($i-1)])  || !$location[($i-1)])  ) || !$values["loc{$i}"])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a location %1 ID !',$i));
						$error_id = true;
					}

					$values['location_code'][]= $values["loc{$i}"];

					if($i<$type_id)
					{
						$location_parent[]= $values["loc{$i}"];
					}
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category'));
				}

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute )
					{
						if($attribute['nullable'] != 1 && !$attribute['value'])
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if($attribute['datatype'] == 'I' && isset($attribute['value']) && $attribute['value'] && !ctype_digit($attribute['value']))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter integer for attribute %1', $attribute['input_text']));
						}
					}
				}

				if (isset($insert_record['extra']) && array_search('street_id',$insert_record['extra']) && (!isset($values['street_id']) || !$values['street_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a street'));
				}
				if (isset($insert_record['extra']) && array_search('part_of_town_id',$insert_record['extra']) && (!isset($values['part_of_town_id']) || !$values['part_of_town_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a part of town'));
				}
				if (isset($insert_record['extra']) && array_search('owner_id',$insert_record['extra']) && (!isset($values['owner_id']) || !$values['owner_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select an owner'));
				}

				$values['location_code']=implode("-", $values['location_code']);

				if($values['location_code'] && !$location_code)
				{
					if($this->bo->check_location($values['location_code'],$type_id))
					{
						$receipt['error'][]=array('msg'=>lang('This location is already registered!') . '[ '.$values['location_code'].' ]');
						$error_location_id=true;
						$error_id = true;
					}
				}

				if($location_code)
				{
					$action='edit';
					$values['change_type'] = $change_type;


					if(!$values['change_type'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select change type'));
					}
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->save($values,$values_attribute,$action,$type_id,isset($location_parent)?$location_parent:'');
					$error_id = isset($receipt['location_code']) && $receipt['location_code'] ? false : true;
					$location_code = $receipt['location_code'];
				}
				else
				{
					if(isset($location_parent) && $location_parent)
					{
						$location_code_parent=implode('-', $location_parent);
						$values = $this->bo->read_single($location_code_parent);

						$values['attributes']	= $this->bo->find_attribute(".location.{$this->type_id}");
						$values					= $this->bo->prepare_attribute($values, ".location.{$this->type_id}");

						/* restore date from posting */
						if(isset($insert_record['extra']) && is_array($insert_record['extra']))
						{
							for ($i=0; $i<count($insert_record['extra']); $i++)
							{
								$values[$insert_record['extra'][$i]]= phpgw::get_var($insert_record['extra'][$i], 'string', 'POST');
							}
						}
					}
				}
			}

			if(!$error_id && $location_code)
			{
				$values = $this->bo->read_single($location_code,array('tenant_id'=>'lookup'));

				$check_history = $this->bo->check_history($location_code);
				if($get_history)
				{
					$history = $this->bo->get_history($location_code);
					$uicols = $this->bo->uicols;

					$j=0;
					if (isSet($history) && is_array($history))
					{
						foreach($history as $entry)
						{
							$k=0;
							for ($i=0;$i<count($uicols['name']);$i++)
							{
								if($uicols['input_type'][$i]!='hidden')
								{
									$content[$j]['row'][$k]['value'] 	= $entry[$uicols['name'][$i]];
									$content[$j]['row'][$k]['name'] 	= $uicols['name'][$i];
								}

								$content[$j]['hidden'][$k]['value'] 	= $entry[$uicols['name'][$i]];
								$content[$j]['hidden'][$k]['name'] 		= $uicols['name'][$i];
								$k++;
							}
							$j++;
						}
					}

					$uicols_count	= count($uicols['descr']);
					for ($i=0;$i<$uicols_count;$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$table_header[$i]['header'] 	= $uicols['descr'][$i];
							$table_header[$i]['width']		= '5%';
							$table_header[$i]['align']		= 'center';
						}
					}
				}
			}
			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
				unset($values['location_code']);
			}

			if(!$values)
			{
				$values['attributes']	= $this->bo->find_attribute(".location.{$this->type_id}");
				$values					= $this->bo->prepare_attribute($values, ".location.{$this->type_id}");
			}

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$link_data = array
				(
					'menuaction'	=> $view ? 'property.uilocation.view' : 'property.uilocation.edit',
					'location_code'	=> $location_code,
					'type_id'	=> $type_id,
					'lookup_tenant'	=> $lookup_tenant
				);


			$lookup_type = $view ? 'view' : 'form';

			if(!$location_code && $parent)
			{
				$_values = $this->bo->read_single($parent,array('noattrib' => true));
				$_values['attributes'] = $values['attributes'];
			}
			else
			{
				$_values = $values;
			}

			$location_data=$this->bo->initiate_ui_location(array
				(
					'values'		=> $_values,
					'type_id'		=> ($type_id-1),
					'no_link'		=> ($type_id), // disable lookup links for location type less than type_id
					'tenant'		=> false,
					'lookup_type'	=> $lookup_type
				)
			);

			unset($_values);

			$location_types	= $this->bo->location_types;
			$config			= $this->bo->config;

			if ($location_code)
			{
				$function_msg = lang('edit');
			}
			else
			{
				$function_msg = lang('add');
			}

			$function_msg .= ' ' .$location_types[($type_id-1)]['name'];

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');


			if(!is_array($insert_record))
			{
				$insert_record = array();
			}

			$j=0;
			$additional_fields[$j]['input_text']	= $location_types[($type_id-1)]['name'];
			$additional_fields[$j]['statustext']	= $location_types[($type_id-1)]['descr'];
			$additional_fields[$j]['datatype']		= 'varchar';
			$additional_fields[$j]['input_name']	= 'loc' . $type_id;
			$additional_fields[$j]['name']			= 'loc' . $type_id;
			$additional_fields[$j]['value']			= isset($values[$additional_fields[$j]['input_name']])?$values[$additional_fields[$j]['input_name']]:'';
			$additional_fields[$j]['class']			= 'th_text';
			$insert_record['extra'][]				= $additional_fields[$j]['input_name'];

			$j++;
			$additional_fields[$j]['input_text']	= lang('name');
			$additional_fields[$j]['statustext']	= lang('enter the name for this location');
			$additional_fields[$j]['datatype']		= 'varchar';
			$additional_fields[$j]['input_name']	= 'loc' . $type_id . '_name';
			$additional_fields[$j]['name']			= 'loc' . $type_id . '_name';
			$additional_fields[$j]['value']			= isset($values[$additional_fields[$j]['input_name']])?$values[$additional_fields[$j]['input_name']]:'';
			$additional_fields[$j]['size']			= $additional_fields[$j]['value'] ? strlen($additional_fields[$j]['value']) + 5 : 30;
			$insert_record['extra'][]				= $additional_fields[$j]['input_name'];
			$j++;

			//_debug_array($attributes_values);

			$_config		= CreateObject('phpgwapi.config','property');
			$_config->read();

			$insert_record['extra'][]						= 'cat_id';

			$config_count=count($config);
			for ($j=0;$j<$config_count;$j++)
			{
				if($config[$j]['location_type'] == $type_id)
				{

					if($config[$j]['column_name']=='street_id')
					{
						$edit_street=true;
						$insert_record['extra'][]	= 'street_id';
						$insert_record['extra'][]	= 'street_number';
					}

					if($config[$j]['column_name']=='tenant_id')
					{
						if(!isset($_config->config_data['suppress_tenant']) || !$_config->config_data['suppress_tenant'])
						{
							$edit_tenant=true;
							$insert_record['extra'][]	= 'tenant_id';
						}
					}

					if($config[$j]['column_name']=='part_of_town_id')
					{
						$edit_part_of_town		= true;
						$select_name_part_of_town	= 'part_of_town_id';
						$part_of_town_list		= $this->bocommon->select_part_of_town('select',$values['part_of_town_id']);
						$lang_town_statustext		= lang('Select the part of town the property belongs to. To do not use a part of town -  select NO PART OF TOWN');
						$insert_record['extra'][]	= 'part_of_town_id';
					}
					if($config[$j]['column_name']=='owner_id')
					{
						$edit_owner			= true;
						$lang_owner			= lang('Owner');
						$owner_list			= $this->bo->get_owner_list('',$values['owner_id']);
						$lang_select_owner		= lang('Select owner');
						$lang_owner_statustext		= lang('Select the owner');
						$insert_record['extra'][]	= 'owner_id';
					}
				}
			}

			$GLOBALS['phpgw']->session->appsession('insert_record','property',$insert_record);

			if(isset($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}


			if($location_code)
			{
				$change_type_list = $this->bo->select_change_type($values['change_type']);

				$location_types = $this->soadmin_location->read(array('order'=>'id','sort'=>'ASC'));
				foreach ($location_types as $location_type)
				{
					if($type_id != $location_type['id'])
					{
						if($type_id > $location_type['id'])
						{
							$entities_link[] = array
								(
									'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'=> "property.uilocation.{$mode}",
										'location_code'=>implode('-',array_slice($location, 0, $location_type['id']))
									)
								),
								'lang_entity_statustext'	=> $location_type['descr'],
								'text_entity'			=> '<- '. $location_type['name'],
							);
						}
						else
						{
							$_location_code = implode('-',array_slice($location, 0, $location_type['id']));
							$marker = str_repeat('-', ($location_type['id'] - $type_id));
							$entities_link[] = array
								(
									'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uilocation.index',
										'type_id'		=> $location_type['id'],
										'query'			=> $_location_code,
										'location_code' => $_location_code
									)
								),
								'lang_entity_statustext'	=> $location_type['descr'],
								'text_entity'			=> "{$marker}> " . $location_type['name'],
							);
							unset($_location_code);
						}
					}
				}
			}

			//phpgwapi_yui::tabview_setup('location_edit_tabview');
			$tabs = array();
			$tabs['general']	= array('label' => $location_types[($type_id-1)]['name'], 'link' => '#general');

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array // FIXME
							(
								'menuaction'	=> 'property.uilocation.attrib_history',
								'entity_id'	=> $this->entity_id,
								'cat_id'	=> $this->cat_id,
								'attrib_id'	=> $attribute['id'],
								'id'		=> $id,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				$location = ".location.{$type_id}";
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);
//	_debug_array($attributes_groups);die();

				$attributes_general = array();
				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']) && isset($group['group_sort']))
					{
						$tabs[str_replace(' ', '_', $group['name'])] = array('label' => $group['name'], 'link' => '#' . str_replace(' ', '_', $group['name']));
						$group['link'] = str_replace(' ', '_', $group['name']);
						$attributes[] = $group;
					}
					else if(isset($group['attributes']) && !isset($group['group_sort']))
					{
						$attributes_general = array_merge($attributes_general,$group['attributes']);
					}

				}
				unset($attributes_groups);
			}

			$documents = array();
			$file_tree = array();
			$integration = array();
			if($location_code)
			{
				$_role_criteria = array
				(
					'type'		=> 'responsibility_role',
					'filter'	=> array('location_level' => $type_id),
					'order'		=> 'name'
				);

				$roles = execMethod('property.sogeneric.get_list',$_role_criteria);

				$soresponsible		= CreateObject('property.soresponsible');
				$contacts = createObject('phpgwapi.contacts');
				foreach ($roles as & $role)
				{
					$responsible_item = $soresponsible->get_active_responsible_at_location($location_code, $role['id']);
					$role['responsibility_contact'] = $contacts->get_name_of_person_id($responsible_item['contact_id']);
					$responsibility = $soresponsible->read_single_contact($responsible_item['id']);
					$role['responsibility_name'] = $responsibility['responsibility_name'];
				}

				if($roles)
				{
					$tabs['roles']	= array('label' => lang('contacts'), 'link' => '#roles');
				}

//_debug_array($roles);die();
				$location_arr = explode('-', $location_code);
//_debug_array($location_arr);die();

				$related = array();
				$_location_level_arr = array();
				foreach($location_arr as $_location_level)
				{
					$_exact = $location_code == $_location_level ? false : true;
					$_location_level_arr[] = $_location_level;
					$location_level = implode('-', $_location_level_arr);
					$related[$location_level] = $this->bo->read_entity_to_link($location_level, $_exact);
				}
//_debug_array($related);die();

				$location_type_info =  $this->soadmin_location->read_single($type_id);
				$documents = array();
				if($location_type_info['list_documents'])
				{
					$document = CreateObject('property.sodocument');
					$documents = $document->get_files_at_location( array('location_code' => $location_code) );
				}

				if($documents)
				{
					$tabs['document']	= array('label' => lang('document'), 'link' => '#document');
					$documents = json_encode($documents);
				}

				$_dirname = '';

				$_files_maxlevel = 0;
				if (isset($_config->config_data['external_files_maxlevel']) &&  $_config->config_data['external_files_maxlevel'])
				{
					$_files_maxlevel = $_config->config_data['external_files_maxlevel'];
				}
				$_files_filterlevel = 0;
				if (isset($_config->config_data['external_files_filterlevel']) &&  $_config->config_data['external_files_filterlevel'])
				{
					$_files_filterlevel = $_config->config_data['external_files_filterlevel'];
				}
				$_filter_info = explode('-',$location_code);

				if (isset($_config->config_data['external_files']) &&  $_config->config_data['external_files'])
				{
					$_dirname = $_config->config_data['external_files'];
					$file_tree = $document->read_file_tree($_dirname,$_files_maxlevel,$_files_filterlevel, $_filter_info[0]);
				}

				unset($_config);
				if($file_tree)
				{
					$tabs['file_tree']	= array('label' => lang('Files'), 'link' => '#file_tree');
					$file_tree = json_encode($file_tree);
				}

				$_related = array();
				foreach($related as $_location_level => $related_info)
				{
					if(isset($related_info['related']))
					{
						foreach($related_info as $related_key => $related_data)
						{
							if( $related_key == 'gab')
							{
								foreach($related_data as $entry)
								{
									$entities_link[] = array
										(
											'entity_link'				=> $entry['entity_link'],
											'lang_entity_statustext'	=> $entry['descr'],
											'text_entity'				=> $entry['name'],
										);
								}
							}
							else
							{
								foreach($related_data as $entry)
								{
									$_related[] = array
									(
										'where'		=> $_location_level,
										'url'		=> "<a href=\"{$entry['entity_link']}\" > {$entry['name']}</a>",
									);
								}
							}
						}
					}
				}

				$related_link = $_related ? true : false;

				if($_related)
				{
					$tabs['related']	= array('label' => lang('related'), 'link' => '#related');
				}


				/*$datavalues = array();
				$myColumnDefs = array();
				$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($_related),
					'total_records'			=> count($_related),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

				$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(
						array('key' => 'where','label'=>lang('where'),'sortable'=>false,'resizeable'=>true),
						array('key' => 'url','label'=>lang('what'),'sortable'=>false,'resizeable'=>true),
						)
					)
				);*/

				$related_def = array
				(
					array('key' => 'where','label'=>lang('where'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'url','label'=>lang('what'),'sortable'=>false,'resizeable'=>true)
				);

				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_0',
					'requestUrl'	=> "''",
					'data'			=> json_encode($_related),
					'ColumnDefs'	=> $related_def,
					'config'		=> array(
						array('disableFilter'	=> true),
						array('disablePagination'	=> true)
					)
				);

// ---- START INTEGRATION -------------------------

				$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
				$custom_config	= CreateObject('admin.soconfig',$location_id);
				$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();
//_debug_array($custom_config->config_data);die();
			// required settings:
/*
				integration_tab
				integration_height
				integration_url
				integration_parametres
				integration_action
				integration_action_view
				integration_action_edit
				integration_auth_key_name
				integration_auth_url
				integration_auth_hash_name
				integration_auth_hash_value
				integration_location_data
 */
				foreach ($_config as $_config_section => $_config_section_data)
				{
					if(isset($_config_section_data['tab']))
					{
						if(!isset($_config_section_data['url']))
						{
							phpgwapi_cache::message_set("'url' is a required setting for integrations, '{$_config_section}' is disabled", 'error');
							break;
						}

						//get session key from remote system
						$arguments = array($_config_section_data['auth_hash_name'] => $_config_section_data['auth_hash_value']);
						$query = http_build_query($arguments);
						$auth_url = $_config_section_data['auth_url'];
						$request = "{$auth_url}?{$query}";

						$aContext = array
						(
							'http' => array
							(
								'request_fulluri' => true,
							),
						);

						if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
						{
							$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
						}

						$cxContext = stream_context_create($aContext);
						$response = trim(file_get_contents($request, False, $cxContext));


						$_config_section_name = str_replace(' ', '_',$_config_section);
						$integration[]	= array
						(
							'section' => $_config_section_name,
							'height' => isset($_config_section_data['height']) && $_config_section_data['height'] ? $_config_section_data['height'] : 500
						);
						$_config_section_data['url']		= htmlspecialchars_decode($_config_section_data['url']);
						$_config_section_data['parametres']	= htmlspecialchars_decode($_config_section_data['parametres']);

						/*
						* 'parametres' In the form:
						* <targetparameter1>=__<attrbute_name1>__&<targetparameter2>=__<attrbute_name2>__&
						* Example: objId=__id__&lon=__posisjon_lengde__&lat=__posisjon_bredde__
						*/

						parse_str($_config_section_data['parametres'], $output);

						$_values = array();
						foreach ($output as $_dummy => $_substitute)
						{
							$_keys[] = $_substitute;

							$__value = false;
							if(!$__value = urlencode($values[trim($_substitute, '_')]))
							{
								foreach ($values['attributes'] as $_attribute)
								{
									if(trim($_substitute, '_') == $_attribute['name'])
									{
										$__value = urlencode($_attribute['value']);
										break;
									}
								}
							}

							if($__value)
							{
								$_values[] = $__value;
							}
						}

						//_debug_array($_config_section_data['parametres']);
						//_debug_array($_values);
						unset($output);
						unset($__value);
						$_sep = '?';
						if (stripos($_config_section_data['url'],'?'))
						{
							$_sep = '&';
						}
						$_param = $_config_section_data['parametres'] ? $_sep . str_replace($_keys, $_values, $_config_section_data['parametres']) : '';
						unset($_keys);
						unset($_values);
		//				$integration_src = phpgw::safe_redirect("{$_config_section_data['url']}{$_sep}{$_param}");
						$integration_src = "{$_config_section_data['url']}{$_param}";
						if($_config_section_data['action'])
						{
							$_sep = '?';
							if (stripos($integration_src,'?'))
							{
								$_sep = '&';
							}
							//$integration_src .= "{$_sep}{$_config_section_data['action']}=" . $_config_section_data["action_{$mode}"];
						}

						$arguments = array($_config_section_data['auth_key_name'] => $response);

						//in the form: sakstittel=__loc1__.__loc4__

						if(isset($_config_section_data['location_data']) && $_config_section_data['location_data'])
						{
							$_config_section_data['location_data']	= htmlspecialchars_decode($_config_section_data['location_data']);
							parse_str($_config_section_data['location_data'], $output);
							foreach ($output as $_dummy => $_substitute)
							{
								//$_substitute = '__loc1__.__loc4__%';
								$regex = "/__([\w]+)__/";
								preg_match_all($regex, $_substitute, $matches);

								foreach($matches[1] as $__substitute)
								{
									$_values[] = urlencode($values[$__substitute]);
								}
							}
							//FIXME
							$integration_src .= $_config_section_data['url_separator'] . str_replace($matches[0], $_values, $_config_section_data['location_data']);
						}

						if(isset($_config_section_data['auth_key_name']) && $_config_section_data['auth_key_name'])
						{
							$integration_src .= "&{$_config_section_data['auth_key_name']}={$response}";
						}

						//FIXME NOT WORKING!! test for webservice, auth...
						if(isset($_config_section_data['method']) && $_config_section_data['method'] == 'POST')
						{
							$aContext = array
							(
								'http' => array
								(
									'method'			=> 'POST',
									'request_fulluri'	=> true,
								),
							);

							if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
							{
								$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
							}

							$cxContext = stream_context_create($aContext);
							$response = trim(file_get_contents($integration_src, False, $cxContext));
						}
						//_debug_array($values);
						//_debug_array($integration_src);die();

						$tabs[$_config_section]	= array('label' => $_config_section_data['tab'], 'link' => "#{$_config_section_name}", 'function' => "document.getElementById('{$_config_section_name}_content').src = '{$integration_src}';");
					}
				}
// ---- END INTEGRATION -------------------------
			}

			unset($values['attributes']);

			/*$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}*/


			$data = array
			(
				/*'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,*/
				'datatable_def'					=> $datatable_def,
				'integration'					=> $integration,
				'roles'							=> $roles,
				'edit'							=> $view ? '' : true,
				'lang_change_type'				=> lang('Change type'),
				'lang_no_change_type'			=> lang('No Change type'),
				'lang_change_type_statustext'	=> lang('Type of changes'),
				'change_type_list'				=> (isset($change_type_list)?$change_type_list:''),
				'check_history'					=> (isset($check_history)?$check_history:''),
				'lang_history'					=> lang('History'),
				'lang_history_statustext'		=> lang('Fetch the history for this item'),
				'table_header'					=> (isset($table_header)?$table_header:''),
				'values'						=> (isset($content)?$content:''),

				'lang_related_info'				=> lang('related info'),
				'entities_link'					=> (isset($entities_link)?$entities_link:''),
				'related_link'					=> $related_link,
				'edit_street'					=> (isset($edit_street)?$edit_street:''),
				'edit_tenant'					=> (isset($edit_tenant)?$edit_tenant:''),
				'edit_part_of_town'				=> (isset($edit_part_of_town)?$edit_part_of_town:''),
				'edit_owner'					=> (isset($edit_owner)?$edit_owner:''),
				'select_name_part_of_town'		=> (isset($select_name_part_of_town)?$select_name_part_of_town:''),
				'part_of_town_list'				=> (isset($part_of_town_list)?$part_of_town_list:''),
				'lang_town_statustext'			=> (isset($lang_town_statustext)?$lang_town_statustext:''),
				'lang_part_of_town'				=> lang('Part of town'),
				'lang_no_part_of_town'			=> lang('No part of town'),
				'lang_owner'					=> (isset($lang_owner)?$lang_owner:''),
				'owner_list'					=> (isset($owner_list)?$owner_list:''),
				'lang_select_owner'				=> (isset($lang_select_owner)?$lang_select_owner:''),
				'lang_owner_statustext'			=> (isset($lang_owner_statustext)?$lang_owner_statustext:''),
				'additional_fields'				=> $additional_fields,
				'attributes_group'				=> $attributes,
				'attributes_general'			=> array('attributes' => $attributes_general),
//				'attributes_values'				=> $values['attributes'],
				'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'lang_none'						=> lang('None'),
				'msgbox_data'					=> (isset($msgbox_data)?$GLOBALS['phpgw']->common->msgbox($msgbox_data):''),
				'street_link'					=> "menuaction:'" . 'property'.".uilookup.street'",
				'lang_street'					=> lang('Address'),
				'lang_select_street_help'		=> lang('Select the street name'),
				'lang_street_num_statustext'	=> lang('Enter the street number'),
				'value_street_id'				=> (isset($values['street_id'])?$values['street_id']:''),
				'value_street_name'				=> (isset($values['street_name'])?$values['street_name']:''),
				'value_street_number'			=> (isset($values['street_number'])?$values['street_number']:''),
				'tenant_link'					=> "menuaction:'" . 'property'.".uilookup.tenant'",
				'lang_tenant'					=> lang('tenant'),
				'value_tenant_id'				=> (isset($values['tenant_id'])?$values['tenant_id']:''),
				'value_last_name'				=> (isset($values['last_name'])?$values['last_name']:''),
				'value_first_name'				=> (isset($values['first_name'])?$values['first_name']:''),
				'lang_tenant_statustext'		=> lang('Select a tenant'),
				'size_last_name'				=> (isset($values['last_name'])?strlen($values['last_name']):''),
				'size_first_name'				=> (isset($values['first_name'])?strlen($values['first_name']):''),
				'lookup_type'					=> $lookup_type,
				'location_data'					=> $location_data,
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index','type_id'=> $type_id, 'lookup_tenant'=> $lookup_tenant)),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the location'),
				'lang_category'					=> lang('category'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the location belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'location','type_id' =>$type_id,'order'=>'descr')),
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, 'general'),
				'documents'						=> $documents,
				'file_tree'						=> $file_tree,
				'lang_expand_all'				=> lang('expand all'),
				'lang_collapse_all'				=> lang('collapse all')
			);

			//$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/examples/treeview/assets/css/folders/tree.css');
			//phpgwapi_yui::load_widget('treeview');
			phpgwapi_jquery::load_widget('treeview');

			//$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'location.edit', 'property' );
			$appname	= lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			self::render_template_xsl(array('location', 'datatable_inline', 'attributes_form'), array('edit' => $data));
			//$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function delete()
		{

			$location_code	 	= phpgw::get_var('location_code', 'string', 'GET');
			$type_id	 	= $this->type_id;

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($location_code);
				return "location_code ".$location_code." ".lang("has been deleted");
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";

			if(!$this->acl_delete)
			{
				$this->bocommon->no_access();
				return;
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uilocation.index',
					'type_id'	=>$type_id
				);

			if (phpgw::get_var('confirm', 'bool', 'GET'))
			{
				$this->bo->delete($location_code);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.delete', 'location_code'=> $location_code, 'type_id'=> $type_id)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname			= lang('location');
			$function_msg		= lang('delete location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit($view = true);
		}

		/**
		 * Traverse the location hierarchy and set the parent to not active - where all children are not active.
		 *
		 * @return void
		 */

		function update_cat()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::inactive_cats';

			if(!$this->acl->check('.admin.location', PHPGW_ACL_EDIT, 'property'))
			{
				$this->bocommon->no_access();
				return;
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uilocation.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$receipt= $this->bo->update_cat();
				$lang_confirm_msg = lang('Do you really want to update the categories again');
				$lang_yes			= lang('again');
			}
			else
			{
				$lang_confirm_msg 	= lang('Do you really want to update the categories');
				$lang_yes			= lang('yes');
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
					'update_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.update_cat')),
					'message'				=> $receipt['message'],
					'lang_confirm_msg'		=> $lang_confirm_msg,
					'lang_yes'				=> $lang_yes,
					'lang_yes_statustext'	=> lang('Update the category to not active based on if there is only nonactive apartments'),
					'lang_no_statustext'	=> lang('Back to Admin'),
					'lang_no'				=> lang('no')
				);

			$appname		= lang('location');
			$function_msg	= lang('Update the not active category for locations');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('update_cat' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		/**
		 * Perform an update on all location_codes on all levels to make sure they are consistent and unique
		 *
		 * @return void
		 */

		function update_location()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::location::update_location';

			if(!$this->acl->check('.admin.location', PHPGW_ACL_EDIT, 'property'))
			{
				$this->bocommon->no_access();
				return;
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$receipt= $this->bo->update_location();
				$lang_confirm_msg = lang('Do you really want to update the locations again');
				$lang_yes			= lang('again');
			}
			else
			{
				$lang_confirm_msg 	= lang('Do you really want to update the locations');
				$lang_yes			= lang('yes');
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'done_action'				=> $GLOBALS['phpgw']->link('/admin/index.php'),
					'update_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.update_location')),
					'message'					=> $receipt['message'],
					'lang_confirm_msg'			=> $lang_confirm_msg,
					'lang_yes'					=> $lang_yes,
					'lang_yes_statustext'		=> lang('perform an update on all location_codes on all levels to make sure they are consistent and unique'),
					'lang_no_statustext'		=> lang('Back to Admin'),
					'lang_no'					=> lang('no')
				);

			$appname		= lang('location');
			$function_msg	= lang('Update the locations');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('update_cat' => $data));
		}

		function stop()
		{
			$perm	 		= phpgw::get_var('perm', 'int');
			$location	 	= phpgw::get_var('acl_location');

			$right = array
				(
					PHPGW_ACL_READ		=> 'read',
					PHPGW_ACL_ADD		=> 'add',
					PHPGW_ACL_EDIT		=> 'edit',
					PHPGW_ACL_DELETE	=> 'delete',
					PHPGW_ACL_PRIVATE	=> 'manage'
				);

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$receipt['error'][] = array('msg' => lang('You need the right "%1" for this application at "%2" to access this function', lang($right[$perm]), $location));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				);

			$appname		= lang('Access error');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' : ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('stop' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function summary()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::summary';

			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_summary();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$this->bo->read_summary();

			$uicols = $this->bo->uicols;

			$appname = lang('Summary');
			$function_msg = lang('List') . ' ' . lang($this->role);

			$data = array(
				'datatable_name'	=> $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
								'menuaction' 		=> 'property.uilocation.summary',
								'district_id'		=> $this->district_id,
								'part_of_town_id'	=> $this->part_of_town_id,
								'filter'			=> $this->filter,
								'summary'			=> true,
								'phpgw_return_as' => 'json'
					)),
					'download'	=> self::link(array('menuaction' => 'property.uilocation.download',
									'district_id'		=> $this->district_id,
									'part_of_town_id'	=> $this->part_of_town_id,
									'filter'			=> $this->filter,
									'summary'			=> true,
									'export'     => true,
									'allrows'    => true)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$filters = $this->_get_categories_summary();

			foreach ($filters as $filter)
			{
				array_unshift ($data['form']['toolbar']['item'], $filter);
			}

			$this->bo->read_summary();

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
					$params = array(
									'key' => $uicols['name'][$k],
									'label' => $uicols['descr'][$k],
									'sortable' => false,
									'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
								);

					array_push ($data['datatable']['field'], $params);
			}

			$data['datatable']['actions'][] = array();

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			//print_r($data); die;
			self::render_template_xsl('datatable_jquery', $data);
		}
	}
